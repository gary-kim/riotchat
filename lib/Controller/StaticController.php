<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2020 Gary Kim <gary@garykim.dev>
 * @copyright Copyright (c) 2019 Robin Appelman <robin@icewind.nl>
 *
 * @author 2020 Gary Kim <gary@garykim.dev>
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

use OCA\RiotChat\AppInfo\Application;

use OC\ForbiddenException;
use OC\Security\CSP\ContentSecurityPolicy;
use OC\Security\CSP\ContentSecurityPolicyNonceManager;
use OCA\RiotChat\FileResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\NotFoundResponse;
use OCP\Files\IMimeTypeDetector;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IRequest;

class StaticController extends Controller {

	/** @var IMimeTypeDetector */
	private $mimeTypeHelper;

	/** @var ContentSecurityPolicyNonceManager */
	private $nonceManager;

	/** @var IL10N */
	private $l10n;

	/** @var IConfig */
	private $config;

	/**
	 * StaticController constructor.
	 *
	 * @param $appName
	 * @param IRequest $request
	 * @param IMimeTypeDetector $mimeTypeHelper
	 * @param ContentSecurityPolicyNonceManager $nonceManager
	 * @param IL10N $l10n
	 * @param IConfig $config
	 */
	public function __construct(
		$appName,
		IRequest $request,
		IMimeTypeDetector $mimeTypeHelper,
		ContentSecurityPolicyNonceManager $nonceManager,
		IL10N $l10n,
		IConfig $config
	) {
		parent::__construct($appName, $request);

		$this->mimeTypeHelper = $mimeTypeHelper;
		$this->nonceManager = $nonceManager;
		$this->l10n = $l10n;
		$this->config = $config;
	}

	/**
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function index() {
		return $this->riot("index.html");
	}

	/**
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 *
	 * @param string $path
	 * @throws ForbiddenException
	 * @return FileResponse
	 */
	public function riot(string $path) {
		if (strpos($path, '..') !== false) {
			throw new ForbiddenException();
		}

		$localPath = __DIR__ . '/../../3rdparty/riot/' . $path;

		return $this->createFileResponse($localPath);
	}

	/**
	 * @param $path
	 * @return FileResponse|NotFoundResponse
	 */
	private function createFileResponse($path) {
		if (!file_exists($path)) {
			return new NotFoundResponse();
		}
		$content = file_get_contents($path);
		return $this->createFileResponseWithContent($path, $content);
	}

	/**
	 * @param string $path
	 * @param string $content
	 * @param bool $cache
	 * @return FileResponse
	 */
	private function createFileResponseWithContent(string $path, string $content, $cache = true) {
		$isHTML = pathinfo($path, PATHINFO_EXTENSION) === 'html';
		if ($isHTML) {
			$content = $this->addScriptNonce($content, $this->nonceManager->getNonce());
		}

		$mime = $this->mimeTypeHelper->detectPath($path);
		switch (pathinfo($path, PATHINFO_EXTENSION)) {
			case 'wasm':
				$mime = 'application/wasm';
				break;
		}

		$response = new FileResponse(
			$content,
			strlen($content),
			filemtime($path),
			$mime,
			basename($path)
		);

		// we can't cache the html since the nonce might need to get updated
		if ($cache && !$isHTML) {
			$response->cacheFor(3600);
		}

		$default_server_domain = $this->config->getAppValue(Application::APP_ID, 'base_url', Application::AvailableSettings['base_url']);

		$csp = new ContentSecurityPolicy();
		$csp->addAllowedScriptDomain($this->request->getServerHost());
		$csp->addAllowedScriptDomain('\'unsafe-eval\'');
		$csp->addAllowedScriptDomain('\'unsafe-inline\'');
		if ($this->config->getAppValue(Application::APP_ID, 'disable_custom_urls', Application::AvailableSettings['disable_custom_urls']) === 'true') {
			$csp->addAllowedConnectDomain($default_server_domain);
			$csp->addAllowedImageDomain($default_server_domain);
		} else {
			$csp->addAllowedConnectDomain('*');
			$csp->addAllowedImageDomain('*');
		}
		$csp->addAllowedFrameDomain($this->request->getServerHost());
		$response->setContentSecurityPolicy($csp);

		return $response;
	}

	private function addScriptNonce(string $content, string $nonce): string {
		return str_replace('<script', "<script nonce=\"$nonce\"", $content);
	}
}
