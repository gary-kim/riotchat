<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2020 Gary Kim <gary@garykim.dev>
 * @copyright Copyright (c) 2019 Robin Appelman <robin@icewind.nl>
 *
 * @author 2020 Gary Kim <gary@garykim.dev>
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

namespace OCA\RiotChat\Controller;

use OCA\RiotChat\AppInfo\Application;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IRequest;

class ConfigController extends Controller {

	/** @var IL10N */
	private $l10n;

	/** @var IConfig */
	private $config;

	/**
	 * ConfigController constructor.
	 *
	 * @param $appName
	 * @param IL10N $l10n
	 */
	public function __construct(IRequest $request, IL10N $l10n, IConfig $config) {
		parent::__construct(Application::APP_ID, $request);
		$this->l10n = $l10n;
		$this->config = $config;
	}

	/**
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function config() {
		// TODO: fill in branding from theming
		$lang = $this->l10n->getLanguageCode();
		$config = [
			'disable_guests' => true,
			'piwik' => false,
			'settingDefaults' => [
				// TODO: Check if this actually works :)
				'language' => $lang,
			],
			'disable_custom_urls' => $this->config->getAppValue(Application::APP_ID, 'disable_custom_urls', Application::AvailableSettings['disable_custom_urls']) === 'true',
			'disable_login_language_selector' => $this->config->getAppValue(Application::APP_ID, 'disable_login_language_selector', Application::AvailableSettings['disable_login_language_selector']) === 'true',
			'default_server_config' => [
				'm.homeserver' => [
					'base_url' => $this->config->getAppValue(Application::APP_ID, 'base_url', Application::AvailableSettings['base_url']),
					'server_name' => $this->config->getAppValue(Application::APP_ID, 'server_name', Application::AvailableSettings['server_name']),
				],
			],
		];
		return new JSONResponse($config);
	}
}
