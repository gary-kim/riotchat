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

namespace OCA\RiotChat\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000000Date20210726173400 extends SimpleMigrationStep {
	/**
	* @param IOutput $output
	* @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	* @param array $options
	* @return null|ISchemaWrapper
	*/
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();
		if (!$schema->hasTable('matrix_int_room')) {
			$table = $schema->createTable('matrix_int_room');
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('room_id', 'text', [
				'notnull' => true,
			]);
			$table->addColumn('membership', 'text', [
				'notnull' => true,
				'default' => 'join',
			]);
			$table->addColumn('highlight_count', 'integer', [
				'notnull' => true,
				'default' => 0,
			]);
			$table->addColumn('notification_count', 'integer', [
				'notnull' => true,
				'default' => 0,
			]);
			$table->addColumn('joined_member_count', 'integer', [
				'notnull' => true,
				'default' => 0,
			]);
			$table->addColumn('invited_member_count', 'integer', [
				'notnull' => true,
				'default' => 0,
			]);
			$table->addColumn('heroes', 'text', [
				'notnull' => true,
				'default' => '[]',
			]);
			$table->addColumn('effective_name', 'text', [
				'notnull' => false,
			]);
			$table->addColumn('effective_avatar', 'text', [
				'notnull' => false,
			]);
			$table->addColumn('effective_topic', 'text', [
				'notnull' => false,
			]);
			$table->addColumn('name_outdated', 'boolean', [
				'notnull' => true,
				'default' => true,
			]);
			$table->addColumn('avatar_outdated', 'boolean', [
				'notnull' => true,
				'default' => true,
			]);
			$table->addColumn('topic_outdated', 'boolean', [
				'notnull' => true,
				'default' => true,
			]);
			$table->setPrimaryKey(['id']);
			$table->addUniqueIndex(['user_id', 'room_id'], 'matrix_int_r_ur_index');
		}
		if (!$schema->hasTable('matrix_int_room_state')) {
			$table = $schema->createTable('matrix_int_room_state');
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('room_id', 'text', [
				'notnull' => true,
			]);
			$table->addColumn('event_id', 'text', [
				'notnull' => true,
			]);
			$table->addColumn('origin_server_ts', 'bigint', [
				'notnull' => true,
				'default' => 0,
			]);
			$table->addColumn('sender', 'text', [
				'notnull' => true,
			]);
			$table->addColumn('type', 'text', [
				'notnull' => true,
			]);
			$table->addColumn('unsigned', 'text', [
				'notnull' => true,
				'default' => '{}',
			]);
			$table->addColumn('content', 'text', [
				'notnull' => true,
				'default' => '{}',
			]);
			$table->addColumn('state_key', 'text', [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
			$table->addUniqueIndex(['user_id', 'room_id', 'type', 'state_key'], 'matrix_int_rs_urts_index');
		}
		if (!$schema->hasTable('matrix_int_acc_data')) {
			$table = $schema->createTable('matrix_int_acc_data');
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('type', 'text', [
				'notnull' => true,
			]);
			$table->addColumn('content', 'text', [
				'notnull' => true,
				'default' => '{}',
			]);
			$table->setPrimaryKey(['id']);
			$table->addUniqueIndex(['user_id', 'type'], 'matrix_int_ad_ut_index');
		}
		if (!$schema->hasTable('matrix_int_room_acc_data')) {
			$table = $schema->createTable('matrix_int_room_acc_data');
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('room_id', 'text', [
				'notnull' => true,
			]);
			$table->addColumn('type', 'text', [
				'notnull' => true,
			]);
			$table->addColumn('content', 'text', [
				'notnull' => true,
				'default' => '{}',
			]);
			$table->setPrimaryKey(['id']);
			$table->addUniqueIndex(['user_id', 'room_id', 'type'], 'matrix_int_rad_urt_index');
		}
		return $schema;
	}
}
