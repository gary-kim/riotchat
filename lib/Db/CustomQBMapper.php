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
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\AppFramework\Db\DoesNotExistException;

class CustomQBMapper extends QBMapper {
	private function getEntityId(Entity $entity) {
		$qb = $this->db->getQueryBuilder();
		$qb->select('id')
			->from($this->tableName);
		foreach ($this->uniqueColums as $col) {
			$getter = 'get' . ucfirst($entity->columnToProperty($col));
			$qb->andWhere(
				$qb->expr()->eq($col, $qb->createNamedParameter($entity->$getter()), $this->getParameterTypeForProperty($entity, $col))
			);
		}
		$res = $this->findOneQuery($qb);
		return $res['id'];
	}

	public function insertOrUpdate(Entity $entity): Entity {
		try {
			$properties = $entity->getUpdatedFields();
			if (sizeof($properties) === 0) {
				// nothing to do
				return $entity;
			}

			if ($entity->getId() === NULL) {
				return $this->insert($entity);
			} else {
				return $this->update($entity);
			}
		} catch (\OCP\DB\Exception $ex) {
			if ($ex->getReason() === Exception::REASON_UNIQUE_CONSTRAINT_VIOLATION) {
				if (!$entity->getId()) {
					// we need to fetch the id first
					$entity->setId($this->getEntityId($entity));
				}
				return $this->update($entity);
			}
			throw $ex;
		} catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
			if (!$entity->getId()) {
				// we need to fetch the id first
				$entity->setId($this->getEntityId($entity));
			}
			return $this->update($entity);
		}
	}

	public function getExisting(Entity $entity): Entity {
		try {
			$qb = $this->db->getQueryBuilder();
			$qb->select('*')
				->from($this->tableName);
			foreach ($this->uniqueColums as $col) {
				$getter = 'get' . ucfirst($entity->columnToProperty($col));
				$qb->andWhere(
					$qb->expr()->eq($col, $qb->createNamedParameter($entity->$getter()), $this->getParameterTypeForProperty($entity, $col))
				);
			}
			return $this->findEntity($qb);
		} catch (DoesNotExistException $ex) {
			return $entity;
		}
	}

	public function getAll($userId) : array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->tableName)
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);
		return $this->findEntities($qb);
	}

	public function deleteAll($userId) {
		$qb = $this->db->getQueryBuilder();
		$qb->delete($this->tableName)
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->execute();
	}
}
