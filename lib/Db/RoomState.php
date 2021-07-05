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

class RoomState extends Entity {
	protected $userId;
	protected $roomId;
	protected $eventId;
	protected $originServerTs;
	protected $sender;
	protected $type;
	protected $unsigned;
	protected $content;
	protected $stateKey;

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('room_id', 'string');
		$this->addType('event_id', 'string');
		$this->addType('origin_server_ts', 'int');
		$this->addType('sender', 'string');
		$this->addType('type', 'string');
		$this->addType('unsigned', 'string');
		$this->addType('content', 'string');
		$this->addType('state_key', 'string');
	}

	public function jsonSetContent($c) {
		$this->setContent(json_encode($c));
	}

	public function jsonGetContent() {
		return json_decode($this->getContent(), true);
	}

	public function jsonSetUnsigned($c) {
		$this->setUnsigned(json_encode($c));
	}

	public function jsonGetUnsigned() {
		return json_decode($this->getUnsigned(), true);
	}
}
