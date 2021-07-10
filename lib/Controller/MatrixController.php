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

use OCA\RiotChat\MatrixClient;
use OCA\RiotChat\Db\AccountDataMapper;
use OCA\RiotChat\Db\RoomAccountDataMapper;
use OCA\RiotChat\Db\RoomMapper;
use OCA\RiotChat\Db\RoomStateMapper;
use OCA\RiotChat\Service\RoomSyncService;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;
use OCP\IConfig;
use OCP\IRequest;
use OCP\IUserSession;

class MatrixController extends Controller {
	private $config;
	private $userSession;
	private $matrixClient;
	private $accountDataMapper;
	private $roomAccountDataMapper;
	private $roomMapper;
	private $roomStateMapper;
	private $roomSyncService;
	protected $appName;

	public function __construct(
		string $appName,
		IRequest $request,
		IConfig $config,
		IUserSession $userSession,
		AccountDataMapper $accountDataMapper,
		RoomAccountDataMapper $roomAccountDataMapper,
		RoomMapper $roomMapper,
		RoomStateMapper $roomStateMapper,
		RoomSyncService $roomSyncService
	) {
		parent::__construct($appName, $request);
		$this->appName = $appName;
		$this->config = $config;
		$this->userSession = $userSession;
		$this->accountDataMapper = $accountDataMapper;
		$this->roomAccountDataMapper = $roomAccountDataMapper;
		$this->roomMapper = $roomMapper;
		$this->roomStateMapper = $roomStateMapper;
		$this->roomSyncService = $roomSyncService;
	}

	private function getUserValue($key, $default = '') {
		return $this->config->getUserValue($this->userSession->getUser()->getUID(), $this->appName, $key, $default);
	}

	private function setUserValue($key, $value) {
		return $this->config->setUserValue($this->userSession->getUser()->getUID(), $this->appName, $key, $value ?? '');
	}

	private function getMatrixClient() {
		if (!$this->matrixClient) {
			$accessToken = $this->getUserValue('access_token');
			$homeserverUrl = $this->getUserValue('homeserver_url');
			$this->matrixClient = new MatrixClient($homeserverUrl, $accessToken);
		}
		return $this->matrixClient;
	}

	private function clearUser() {
		$this->setUserValue('access_token', NULL);
		$this->setUserValue('homeserver_url', NULL);
		$this->setUserValue('room_sync_since', NULL);
		$userId = $this->userSession->getUser()->getUID();
		$this->accountDataMapper->deleteAll($userId);
		$this->roomAccountDataMapper->deleteAll($userId);
		$this->roomMapper->deleteAll($userId);
		$this->roomStateMapper->deleteAll($userId);
	}

	/**
	 * @param string $username
	 * @param string $password
	 */
	public function login($username, $password) {
		$cl = $this->getMatrixClient();
		$cl->logout();
		if ($cl->login($username, $password)) {
			$this->setUserValue('access_token', $cl->getAccessToken());
			$this->setUserValue('homeserver_url', $cl->getHomeserverUrl());
		} else {
			$this->clearUser();
		}
		return $this->whoami();
	}

	public function logout() {
		$this->getMatrixClient()->logout();
		$this->clearUser();
		return $this->whoami();
	}

	public function whoami() {
		$cl = $this->getMatrixClient();
		if (!$cl->getAccessToken()) {
			return new JSONResponse(['logged_in' => false]);
		}
		return new JSONResponse([
			'logged_in' => true,
			'user_id' => $cl->getUserId(),
		]);
	}

	public function roomSummary() {
		return new JSONResponse(array_map(function ($r) {
			return [
				'room_id' => $r->getRoomId(),
				'display_name' => $r->getEffectiveName(),
				'avatar_url' => $r->getEffectiveAvatar(),
				'topic' => $r->getEffectiveTopic(),
			];
		}, $this->roomMapper->getAll($this->userSession->getUser()->getUID())));
	}
}
