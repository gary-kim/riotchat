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

namespace OCA\RiotChat\Cron;

use OC\BackgroundJob\TimedJob;
use OCP\IUserManager;
use OCA\RiotChat\Service\RoomSyncService;

class RoomSyncTask extends TimedJob {
	private $userManager;
	private $roomSyncService;

	public function __construct(
		IUserManager $userManager,
		RoomSyncService $roomSyncService
	) {
		$this->userManager = $userManager;
		$this->roomSyncService = $roomSyncService;
		$this->setInterval(10); // once every 10 seconds
	}

	protected function run($arguments) {
		$this->userManager->callForAllUsers(function ($user) {
			$this->roomSyncService->sync($user->getUID());
			$this->roomSyncService->updateCache($user->getUID());
		});
	}
}
