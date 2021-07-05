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

use OCP\IDBConnection;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCA\RiotChat\Db\CustomQBMapper;
use OCP\AppFramework\Db\DoesNotExistException;

class RoomStateMapper extends CustomQBMapper {
	protected $uniqueColums = ['user_id', 'room_id', 'type', 'state_key'];

	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'matrix_int_room_state');
	}

	public function get(Room $room, $type, $stateKey = '') {
		if (!$stateKey) {
			$stateKey = '';
		}
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->tableName)
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($room->getUserId(), IQueryBuilder::PARAM_STR))
			)
			->andWhere(
				$qb->expr()->eq('room_id', $qb->createNamedParameter($room->getRoomId(), IQueryBuilder::PARAM_STR))
			)
			->andWhere(
				$qb->expr()->eq('type', $qb->createNamedParameter($type, IQueryBuilder::PARAM_STR))
			)
			->andWhere(
				$qb->expr()->eq('state_key', $qb->createNamedParameter($stateKey, IQueryBuilder::PARAM_STR))
			);
		try {
			return $this->findEntity($qb);
		} catch (DoesNotExistException $ex) {
			return NULL;
		}
	}

	public function getContent(Room $room, $type, $stateKey = '') {
		$event = $this->get($room, $type, $stateKey);
		if (!$event) {
			return NULL;
		}
		return $event->jsonGetContent();
	}
}
