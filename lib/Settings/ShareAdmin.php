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

namespace OCA\RiotChat\Settings;

use OCA\RiotChat\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\Settings\ISettings;
use OCP\IInitialStateService;

class ShareAdmin implements ISettings {
	private $config;
	private $initialStateService;

	public function __construct(
		IConfig $config,
		IInitialStateService $initialStateService
	) {
		$this->config = $config;
		$this->initialStateService = $initialStateService;
	}

	private function getAppValue($key, $default = '') {
		return $this->config->getAppValue(Application::APP_ID, $key, $default);
	}

	private function setAppValue($key, $value) {
		$this->config->setAppValue(Application::APP_ID, $key, $value);
	}

	public function getForm() {
		$this->initialStateService->provideInitialState(Application::APP_ID, 'share_domain', $this->getAppValue('share_domain', $this->config->getSystemValue('trusted_domains')[0]));
		$this->initialStateService->provideInitialState(Application::APP_ID, 'share_prefix', $this->getAppValue('share_prefix'));
		$this->initialStateService->provideInitialState(Application::APP_ID, 'share_suffix', $this->getAppValue('share_suffix'));

		return new TemplateResponse(Application::APP_ID, 'settings/share-admin');
	}

	public function getSection() {
		return 'sharing';
	}

	public function getPriority() {
		return 70;
	}
}
