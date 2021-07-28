<?php
/**
 * @copyright Copyright (c) 2020 Gary Kim <gary@garykim.dev>
 *
 * @author Gary Kim <gary@garykim.dev>
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
 *
 */

namespace OCA\RiotChat\Settings;

use OCA\RiotChat\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IInitialStateService;
use OCP\IUserSession;
use OCP\Settings\ISettings;

class Admin implements ISettings {

	/** @var IConfig */
	private $config;

	/** @var IInitialStateService */
	private $initialStateService;

	/** @var IUserSession */
	private $user;

	/**
	 * Personal constructor
	 *
	 * @param IConfig $config
	 * @param IUserSession $user
	 */
	public function __construct(IConfig $config, IUserSession $user, IInitialStateService $initialStateService) {
		$this->config = $config;
		$this->user = $user;
		$this->initialStateService = $initialStateService;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm() {
		foreach (Application::AvailableSettings as $key => $default) {
			// TODO: Don't send non-Element related settings here
			$data = $this->config->getAppValue(Application::APP_ID, $key, $default);
			$this->initialStateService->provideInitialState(Application::APP_ID, $key, $data);
		}

		$labstr = [];
		foreach (Application::AvailableLabs() as $k) {
			$labstr['lab_' . $k] = $this->config->getAppValue(Application::APP_ID, 'lab_' . $k, 'disable');
		}
		$this->initialStateService->provideInitialState(Application::APP_ID, 'labs', json_encode($labstr));
		return new TemplateResponse(Application::APP_ID, 'settings/element-admin');
	}

	/**
	 * @return string section ID. 'riotchat' in this case
	 */
	public function getSection() {
		return 'riotchat';
	}

	/**
	 * @return int priority of settings
	 */
	public function getPriority() {
		return 80;
	}
}
