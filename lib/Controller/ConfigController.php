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
use OCP\Defaults;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IURLGenerator;

class ConfigController extends Controller {

	/** @var IL10N */
	private $l10n;

	/** @var IConfig */
	private $config;

	/** @var Defaults */
	private $defaults;

	/** @var IURLGenerator */
	private $urlGenerator;

	/**
	 * ConfigController constructor.
	 *
	 * @param IRequest $request
	 * @param IL10N $l10n
	 * @param IConfig $config
	 * @param Defaults $defaults
	 * @param IURLGenerator $urlGenerator
	 */
	public function __construct(IRequest $request, IL10N $l10n, IConfig $config, Defaults $defaults, IURLGenerator $urlGenerator) {
		parent::__construct(Application::APP_ID, $request);
		$this->l10n = $l10n;
		$this->config = $config;
		$this->defaults = $defaults;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function config() {
		$custom_json = $this->config->getAppValue(Application::APP_ID, 'custom_json', '');
		if ($custom_json !== '') {
			return new JSONResponse(json_decode(($custom_json)));
		}


		// TODO: fill in branding from theming
		$lang = $this->l10n->getLanguageCode();
		$config = [
			'disable_guests' => true,
			'piwik' => false,
			'settingDefaults' => [
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
			'brand' => $this->defaults->getName(),
			'branding' => [
				'authHeaderLogoUrl' => $this->defaults->getLogo(),
			],
			'showLabsSettings' => $this->config->getAppValue(Application::APP_ID, 'show_labs_settings', Application::AvailableSettings['show_labs_settings']) === 'true',
			'sso_immediate_redirect' => $this->config->getAppValue(Application::APP_ID, 'sso_immediate_redirect', Application::AvailableSettings['sso_immediate_redirect']) === 'true',
		];

		if ($this->config->getAppValue(Application::APP_ID, 'set_custom_permalink', Application::AvailableSettings['set_custom_permalink']) === 'true') {
			$config['permalinkPrefix'] = rtrim($this->urlGenerator->linkToRouteAbsolute("riotchat.app.index"), "/");
		}

		$jitsi_domain = $this->config->getAppValue(Application::APP_ID, 'jitsi_preferred_domain', Application::AvailableSettings['jitsi_preferred_domain']);
		if ($jitsi_domain !== "") {
			$config['jitsi'] = [
				'preferredDomain' => $jitsi_domain,
			];
		}

		// Integration UI
		$integrations_ui_url = $this->config->getAppValue(Application::APP_ID, 'integrations_ui_url', Application::AvailableSettings['integrations_ui_url']);
		if ($integrations_ui_url !== Application::AvailableSettings['integrations_ui_url']) {
			$config['integrations_ui_url'] = $integrations_ui_url;
		}

		$integrations_rest_url = $this->config->getAppValue(Application::APP_ID, 'integrations_rest_url', Application::AvailableSettings['integrations_rest_url']);
		if ($integrations_rest_url !== Application::AvailableSettings['integrations_rest_url']) {
			$config['integrations_rest_url'] = $integrations_rest_url;
		}

		$integrations_widgets_urls = $this->config->getAppValue(Application::APP_ID, 'integrations_widgets_urls', Application::AvailableSettings['integrations_widgets_urls']);
		if ($integrations_widgets_urls !== Application::AvailableSettings['integrations_widgets_urls']) {
			$config['integrations_widgets_urls'] = [];
			$config['integrations_widgets_urls'][0] = $integrations_widgets_urls;
		}

		return new JSONResponse($config);
	}

	/**
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function rootConfig(): JSONResponse {
		return $this->config();
	}
}
