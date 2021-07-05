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

namespace OCA\RiotChat;

class MatrixClient {
	private $accessToken;
	private $homeserverUrl;
	private $userId;

	public function __construct(
		string $homeserverUrl = '',
		string $accessToken = ''
	) {
		$this->homeserverUrl = $homeserverUrl;
		$this->accessToken = $accessToken;
	}

	public function getAccessToken() {
		return $this->accessToken;
	}

	public function getHomeserverUrl() {
		return $this->homeserverUrl;
	}

	public function login($username, $password) {
		// make sure we don't have old access tokens / user ids cached
		$this->accessToken = NULL;
		$this->userId = NULL;
		// if our username is an mxid, we need to do a well-known lookup or thelike
		if ($username[0] === '@') {
			$domain = explode(':', $username, 2)[1];
			$this->homeserverUrl = 'https://' . $domain;
			try {
				$wk = $this->doRequest('/.well-known/matrix/client');
				$this->homeserverUrl = $wk['m.homeserver']['base_url'];
			} catch (Exception $e) {
				$this->homeserverUrl = NULL;
			}
			if (!$this->homeserverUrl) {
				$this->homeserverUrl = 'https://' . $domain;
			}
		}
		// ok, now we should have a working homeserver url, if we have right input
		$res = $this->doRequest('/_matrix/client/r0/login', 'POST', [
			'type' => 'm.login.password',
			'identifier' => [
				'type' => 'm.id.user',
				'user' => $username,
			],
			'password' => $password,
			'initial_device_display_name' => 'Nextcloud Integration',
		]);
		if (!$res || !$res['access_token']) {
			return false;
		}
		$this->userId = $res['user_id'];
		$this->accessToken = $res['access_token'];
		return true;
	}

	public function logout() {
		$this->doRequest('/_matrix/client/r0/logout', 'POST', []);
		$this->accessToken = NULL;
		$this->userId = NULL;
	}

	public function getUserId() {
		if (!$this->userId) {
			$this->userId = $this->whoami();
		}
		return $this->userId;
	}

	public function whoami() {
		try {
			return $this->doRequest('/_matrix/client/r0/account/whoami')['user_id'];
		} catch (Exception $e) {
			return NULL;
		}
	}

	public function uploadFilter($filter) {
		return $this->doRequest('/_matrix/client/r0/user/' . urlencode($this->getUserId()) . '/filter', 'POST', $filter)['filter_id'];
	}

	public function doRequest($path, $method = 'GET', $body = NULL) {
		$url = $this->homeserverUrl . $path;
		$ch = curl_init($url);
		$headers = [];
		if ($body) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
			array_push($headers, 'Content-type: application/json');
		}
		if ($this->accessToken) {
			array_push($headers, 'Authorization: Bearer ' . $this->accessToken);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, true);
	}
}
