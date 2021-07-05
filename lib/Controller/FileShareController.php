<?php
/**
 * @copyright Copyright (c) 2021 Sorunome <mail@sorunome.de>
 *
 * @author 2021 Sorunome <mail@sorunome.de>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\RiotChat\Controller;

use OC_Files;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataDisplayResponse;
use OCP\AppFramework\Http\FileDisplayResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;
use OCP\Constants;
use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\IConfig;
use OCP\IPreview;
use OCP\IRequest;
use OCP\ISession;
use OCP\Share\Exceptions\ShareNotFound;
use OCP\Share\IManager as ShareManager;
use OCP\Share\IShare;

class FileShareController extends Controller {

	/** @var IConfig */
	private $config;

	/** @var ShareManager */
	private $shareManager;

	/** @var IPreview */
	private $previewManager;

	/** @var IRootFolder */
	private $rootFolder;

	/** @var string */
	protected $appName;

	public function __construct(
		string $appName,
		IRequest $request,
		IConfig $config,
		ShareManager $shareManger,
		IPreview $previewManager,
		IRootFolder $rootFolder
	) {
		parent::__construct($appName, $request);
		$this->appName = $appName;
		$this->config = $config;
		$this->shareManager = $shareManger;
		$this->previewManager = $previewManager;
		$this->rootFolder = $rootFolder;
	}

	private function getAppValue($key, $default = '') {
		return $this->config->getAppValue($this->appName, $key, $default);
	}

	private function setAppValue($key, $value) {
		$this->config->setAppValue($this->appName, $key, $value);
	}

	// https://cloud.sorunome.de/s/HybqDm977WJyZTM

	private function getFile($mxc, $isToken = false) {
		\OC_User::setIncognitoMode(true);
		$token = $mxc;
		// due to the magic of MXC URIs we don't need to do any escaping here at all
		if (!$isToken) {
			$parts = explode('/', $mxc);
			if ($parts[0] !== $this->getAppValue('share_domain', $this->config->getSystemValue('trusted_domains')[0])) {
				die('nope');
			}
			$prefix = $this->getAppValue('share_prefix');
			$suffix = $this->getAppValue('share_suffix');
			if (!str_starts_with($parts[1], $prefix) || !str_ends_with($parts[1], $suffix)) {
				die('invalid prefix / suffix');
			}
			$token = substr($parts[1], strlen($prefix), strlen($parts[1]) - strlen($prefix) - strlen($suffix));
		}
		$share = $this->shareManager->getShareByToken($token);
		if (($share->getPermissions() & Constants::PERMISSION_READ) === 0) {
			die('no permission');
		}
		\OC_Util::tearDownFS();
		\OC_Util::setupFS($share->getShareOwner());
		$node = $share->getNode();
		if ($node instanceof Folder) {
			die('folders not supported');
		}
		return $node;
	}

	private function getEvent($mxc, $isToken = false) {
		$f = $this->getFile($mxc, $isToken);
		$info = [
			'mimetype' => $f->getMimeType(),
			'size' => $f->getSize(),
		];
		$msgtype = [
			'image' => 'm.image',
			'audio' => 'm.audio',
			'video' => 'm.video',
		][explode('/', $info['mimetype'])[0]] ?? 'm.file';
		$path = $this->config->getSystemValue('datadirectory', \OC::$SERVERROOT . '/data') . $f->getPath();
		if ($msgtype === 'm.image') {
			$exif = exif_read_data($path);
			$flip = in_array($exif['Orientation'], [2, 4, 6, 8]);
			$imgInfo = getimagesize($path);
			if ($imgInfo) {
				if ($flip) {
					$info['w'] = $imgInfo[1];
					$info['h'] = $imgInfo[0];
				} else {
					$info['w'] = $imgInfo[0];
					$info['h'] = $imgInfo[1];
				}
			}
		}
		return [
			'type' => 'm.room.message',
			'content' => [
				'msgtype' => $msgtype,
				'body' => $f->getName(),
				'info' => $info,
				'url' => 'mxc://' . $mxc,
			],
		];
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 * @NoSameSiteCookieRequired
	 * @param string $mxc
	 */
	public function matrixEvent($mxc) {
		return new JSONResponse($this->getEvent($mxc));
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 * @NoSameSiteCookieRequired
	 * @param string $mxc
	 */
	public function matrixDownload($mxc) {
		// $userFolder = $this->rootFolder->getUserFolder($share->getShareOwner());
		// $originalSharePath = $userFolder->getRelativePath($share->getNode()->getPath());

		$f = $this->getFile($mxc);
		$response = new FileDisplayResponse($f, Http::STATUS_OK, ['Content-Type' => $f->getMimeType()]);
		$response->cacheFor(3600 * 24);
		return $response;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 * @NoSameSiteCookieRequired
	 * @param string $mxc
	 * @param string $method
	 * @param int $w
	 * @param int $h
	 */
	public function matrixThumbnail($mxc, $method = 'scale', $w, $h) {
		if ($method == NULL) {
			$method = 'scale';
		}
		if (!in_array($method, ['scale', 'crop'])) {
			die('invalid method');
		}
		$f = $this->previewManager->getPreview($this->getFile($mxc), $w, $h);
		if ($method === 'crop') {
			$content = $f->getContent();
			$im = imagecreatefromstring($content);
			$width = imagesx($im);
			$height = imagesy($im);
			if (($w / $h) > ($width / $height)) {
				$ratio = $h / $w;
				$nh = $height * $ratio;
				$im2 = imagecrop($im, ['x' => 0, 'y' => ($height - $nh) / 2, 'width' => $width, 'height' => $nh]);
			} else {
				$ratio = $w / $h;
				$nw = $width * $ratio;
				$im2 = imagecrop($im, ['x' => ($width - $nw) / 2, 'y' => 0, 'width' => $nw, 'height' => $height]);

			}
			imagedestroy($im);
			ob_start();
			imagejpeg($im2);
			$buffer = ob_get_contents();
			ob_end_clean();
			imagedestroy($im2);
			$resp = new DataDisplayResponse($buffer, Http::STATUS_OK, ['Content-Type' => 'image/jpeg']);
			$resp->cacheFor(3600 * 24);
			return $resp;
		}
		$response = new FileDisplayResponse($f, Http::STATUS_OK, ['Content-Type' => $f->getMimeType()]);
		$response->cacheFor(3600 * 24);
		return $response;
	}
}
