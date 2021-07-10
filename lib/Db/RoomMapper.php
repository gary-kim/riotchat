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

class RoomMapper extends CustomQBMapper {
	protected $uniqueColums = ['user_id', 'room_id'];

	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'matrix_int_room');
	}

	public function getAllNeedUpdate($userId) : array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->tableName)
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->andWhere(
				$qb->expr()->orX(
					$qb->expr()->eq('name_outdated', $qb->createNamedParameter(true, IQueryBuilder::PARAM_BOOL)),
					$qb->expr()->eq('avatar_outdated', $qb->createNamedParameter(true, IQueryBuilder::PARAM_BOOL)),
					$qb->expr()->eq('topic_outdated', $qb->createNamedParameter(true, IQueryBuilder::PARAM_BOOL))
				)
			);
		return $this->findEntities($qb);
	}
}
