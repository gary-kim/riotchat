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

namespace OCA\RiotChat\Service;

use OCA\RiotChat\MatrixClient;
use OCA\RiotChat\Db\AccountData;
use OCA\RiotChat\Db\AccountDataMapper;
use OCA\RiotChat\Db\RoomAccountData;
use OCA\RiotChat\Db\RoomAccountDataMapper;
use OCA\RiotChat\Db\Room;
use OCA\RiotChat\Db\RoomMapper;
use OCA\RiotChat\Db\RoomState;
use OCA\RiotChat\Db\RoomStateMapper;
use OCP\IConfig;

function is_key_type(&$arr, $key, $type) {
	return is_array($arr) && array_key_exists($key, $arr) && ('is_' . $type)($arr[$key]);
}

class RoomSyncService {
	private $appName;
	private $config;
	private $accountDataMapper;
	private $roomAccountDataMapper;
	private $roomMapper;
	private $roomStateMapper;
	private $syncFilter = [
		'presence' => [
			'limit' => 0,
			'types' => ['none'],
		],
		'room' => [
			'ephemeral' => [
				'limit' => 0,
				'types' => ['none'],
			],
			'timeline' => [
				'limit' => 0,
				'types' => ['none'],
				'lazy_load_members' => true,
			],
			'state' => [
				'lazy_load_members' => true,
			],
		],
	];

	public function __construct(
		string $appName,
		IConfig $config,
		AccountDataMapper $accountDataMapper,
		RoomAccountDataMapper $roomAccountDataMapper,
		RoomMapper $roomMapper,
		RoomStateMapper $roomStateMapper
	) {
		$this->appName = $appName;
		$this->config = $config;
		$this->accountDataMapper = $accountDataMapper;
		$this->roomAccountDataMapper = $roomAccountDataMapper;
		$this->roomMapper = $roomMapper;
		$this->roomStateMapper = $roomStateMapper;
	}

	private function getUserValue($userId, $key, $default = '') {
		return $this->config->getUserValue($userId, $this->appName, $key, $default);
	}

	private function setUserValue($userId, $key, $value) {
		return $this->config->setUserValue($userId, $this->appName, $key, $value ?? '');
	}

	public function sync($userId) {
		$accessToken = $this->getUserValue($userId, 'access_token');
		$homeserverUrl = $this->getUserValue($userId, 'homeserver_url');
		if (!$accessToken || !$homeserverUrl) {
			return;
		}
		$cl = new MatrixClient($homeserverUrl, $accessToken);
		$filterId = $cl->uploadFilter($this->syncFilter);
		$since = $this->getUserValue($userId, 'room_sync_since');
		$query = [
			'set_presence' => 'offline',
			'full_state' => 'false',
			'filter' => $filterId,
		];
		if ($since) {
			$query['since'] = $since;
		}
		$response = $cl->doRequest('/_matrix/client/r0/sync?' . http_build_query($query));
		if (!is_array($response)) {
			return;
		}
		if (is_array($response['account_data']) && is_array($response['account_data']['events'])) {
			foreach ($response['account_data']['events'] as $event) {
				if (
					!is_key_type($event, 'type', 'string') ||
					!is_key_type($event, 'content', 'array')
				) {
					continue;
				}
				$accountData = new AccountData();
				$accountData->setUserId($userId);
				$accountData->setType($event['type']);
				$accountData->jsonSetContent($event['content']);
				$this->accountDataMapper->insertOrUpdate($accountData);
			}
		}
		if (is_array($response['rooms'])) {
			foreach (['join', 'leave', 'invite', 'knock'] as $membership) {
				if (!is_key_type($response['rooms'], $membership, 'array')) {
					continue;
				}
				foreach ($response['rooms'][$membership] as $roomId => $room) {
					if (!is_string($roomId) || !is_array($room)) {
						continue;
					}
					$roomObj = new Room();
					$roomObj->setUserId($userId);
					$roomObj->setRoomId($roomId);
					$roomObj = $this->roomMapper->getExisting($roomObj);
					$roomObj->setMembership($membership);
					if (is_key_type($room, 'summary', 'array')) {
						$summary = $room['summary'];
						if (is_key_type($summary, 'm.heroes', 'array')) {
							$roomObj->jsonSetHeroes($summary['m.heroes']);
							$roomObj->setNameOutdated(true);
						}
						if (is_key_type($summary, 'm.joined_member_count', 'int')) {
							$roomObj->setJoinedMemberCount($summary['m.joined_member_count']);
							$roomObj->setNameOutdated(true);
						}
						if (is_key_type($summary, 'm.invited_member_count', 'int')) {
							$roomObj->setInvitedMemberCount($summary['m.invited_member_count']);
						}
					}
					if (is_key_type($room, 'unread_notifications', 'array')) {
						$unread = $room['unread_notifications'];
						if (is_key_type($unread, 'highlight_count', 'int')) {
							$roomObj->setHighlightCount($unread['highlight_count']);
						}
						if (is_key_type($unread, 'notification_count', 'int')) {
							$roomObj->setNotificationCount($unread['notification_count']);
						}
					}
					if (is_key_type($room, 'account_data', 'array') && is_key_type($room['account_data'], 'events', 'array')) {
						foreach ($room['account_data']['events'] as $event) {
							if (
								!is_array($event) ||
								!is_key_type($event, 'type', 'string') ||
								!is_key_type($event, 'content', 'array')
							) {
								continue;
							}
							$roomAccountData = new RoomAccountData();
							$roomAccountData->setUserId($userId);
							$roomAccountData->setRoomId($roomId);
							$roomAccountData->setType($event['type']);
							$roomAccountData->jsonSetContent($event['content']);
							$this->roomAccountDataMapper->insertOrUpdate($roomAccountData);
						}
					}
					foreach (['invite_state', 'state', 'timeline'] as $stateSource) {
						if (!is_key_type($room, $stateSource, 'array') || !is_key_type($room[$stateSource], 'events', 'array')) {
							continue;
						}
						foreach ($room[$stateSource]['events'] as $event) {
							if (
								!is_key_type($event, 'type', 'string') ||
								!is_key_type($event, 'state_key', 'string') ||
								!is_key_type($event, 'sender', 'string') ||
								!is_key_type($event, 'content', 'array') ||
								!is_key_type($event, 'event_id', 'string')
							) {
								continue;
							}
							if (in_array($event['type'], ['m.room.name', 'm.room.member', 'm.room.canonical_alias'])) {
								$roomObj->setNameOutdated(true);
							}
							if (in_array($event['type'], ['m.room.avatar', 'm.room.member'])) {
								$roomObj->setAvatarOutdated(true);
							}
							if (in_array($event['type'], ['m.room.topic'])) {
								$roomObj->setTopicOutdated(true);
							}
							$roomState = new RoomState();
							$roomState->setUserId($userId);
							$roomState->setRoomId($roomId);
							$roomState->setEventId($event['event_id']);
							if (is_key_type($event, 'origin_server_ts', 'int')) {
								$roomState->setOriginServerTs($event['origin_server_ts']);
							}
							$roomState->setSender($event['sender']);
							$roomState->setType($event['type']);
							if (is_key_type($event, 'unsigned', 'array')) {
								$roomState->jsonSetUnsigned($event['unsigned']);
							}
							$roomState->jsonSetContent($event['content']);
							$roomState->setStateKey($event['state_key']);
							$this->roomStateMapper->insertOrUpdate($roomState);
						}
					}
					$this->roomMapper->insertOrUpdate($roomObj);
				}
			}
		}
		if (is_key_type($response, 'next_batch', 'string')) {
			$this->setUserValue($userId, 'room_sync_since', $response['next_batch']);
		}
	}

	public function updateCache($userId) {
		$rooms = $this->roomMapper->getAllNeedUpdate($userId);
		foreach ($rooms as $room) {
			if ($room->getNameOutdated()) {
				// name outdated
				$event = $this->roomStateMapper->getContent($room, 'm.room.name');
				if (is_key_type($event, 'name', 'string') && $event['name']) {
					$room->setEffectiveName($event['name']);
				} else {
					$event = $this->roomStateMapper->getContent($room, 'm.room.canonical_alias');
					if (is_key_type($event, 'alias', 'string') && $event['alias']) {
						$room->setEffectiveName($event['alias']);
					} else {
						// ok....time to go off of heroes
						$heroes = $room->jsonGetHeroes();
						$heroeNames = [];
						foreach ($heroes as $hero) {
							if (!is_string($hero)) {
								continue;
							}
							$event = $this->roomStateMapper->getContent($room, 'm.room.member', $hero);
							if (is_key_type($event, 'displayname', 'string')) {
								$heroeNames[] = $event['displayname'];
							} else {
								$heroeNames[] = $hero;
							}
						}
						if (sizeof($heroeNames) === 0) {
							$room->setEffectiveName('Empty Room');
						} else {
							$totalMembers = $room->getJoinedMemberCount() + $room->getInvitedMemberCount();
							$totalHeroes = sizeof($heroeNames);
							$nameStr = implode(', ', $heroeNames);
							if ($totalMembers - 1 > $totalHeroes && $totalMembers > 1) {
								$room->setEffectiveName($nameStr . ' and ' . ($totalMembers - $totalHeroes) . ' others');
							} else {
								$room->setEffectiveName($nameStr);
							}
						}
					}
				}
				$room->setNameOutdated('false');
			}
			if ($room->getAvatarOutdated()) {
				// avatar outdated
				$event = $this->roomStateMapper->getContent($room, 'm.room.avatar');
				if (is_key_type($event, 'url', 'string')) {
					$room->setEffectiveAvatar($event['url']);
				} else {
					$heroes = $room->jsonGetHeroes();
					if (sizeof($heroes) === 1) {
						$event = $this->roomStateMapper->getContent($room, 'm.room.member', $heroes[0]);
						if (is_key_type($event, 'avatar_url', 'string')) {
							$room->setEffectiveAvatar($event['avatar_url']);
						} else {
							$room->setEffectiveAvatar('');
						}
					} else {
						$room->setEffectiveAvatar('');
					}
				}
				$room->setAvatarOutdated('false');
			}
			if ($room->getTopicOutdated()) {
				// topic outdated
				$event = $this->roomStateMapper->getContent($room, 'm.room.topic');
				$room->setEffectiveTopic(is_key_type($event, 'topic', 'string') ? $event['topic'] : '');
				$room->setTopicOutdated('false');
			}
			$this->roomMapper->update($room);
		}
	}
}
