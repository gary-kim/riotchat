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

namespace OCA\RiotChat\Db;

use OCP\AppFramework\Db\Entity;

class Room extends Entity {
	protected $userId;
	protected $roomId;
	protected $membership;
	protected $highlightCount;
	protected $notificationCount;
	protected $joinedMemberCount;
	protected $invitedMemberCount;
	protected $heroes;
	protected $effectiveName;
	protected $effectiveAvatar;
	protected $effectiveTopic;
	protected $nameOutdated;
	protected $avatarOutdated;
	protected $topicOutdated;

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('room_id', 'string');
		$this->addType('membership', 'string');
		$this->addType('highlight_count', 'int');
		$this->addType('notification_count', 'int');
		$this->addType('joined_member_count', 'int');
		$this->addType('invited_member_count', 'int');
		$this->addType('heroes', 'string');
		$this->addType('effective_name', 'string');
		$this->addType('effective_topic', 'string');
		$this->addType('effective_topic', 'string');
		$this->addType('name_outdated', 'boolean');
		$this->addType('avatar_outdated', 'boolean');
		$this->addType('topic_outdated', 'boolean');
	}

	public function jsonSetHeroes($h) {
		$this->setHeroes(json_encode($h));
	}

	public function jsonGetHeroes() {
		return json_decode($this->getHeroes(), true);
	}
}
