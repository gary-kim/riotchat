<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2020-2021 Gary Kim <gary@garykim.dev>
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


namespace OCA\RiotChat\Controller;

use OC\Security\CSP\ContentSecurityPolicy;

use OCA\RiotChat\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\FeaturePolicy;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IInitialStateService;
use OCP\IRequest;

class AppController extends Controller {

	/** @var IInitialStateService */
	private $initialStateService;

	/** @var IConfig */
	private $config;

	public function __construct($AppName, IRequest $request, IInitialStateService $initialStateService, IConfig $config) {
		parent::__construct($AppName, $request);
		$this->initialStateService = $initialStateService;
		$this->config = $config;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {
		$response = new TemplateResponse('riotchat', 'index');

		$this->initialStateService->provideInitialState(Application::APP_ID, 'disable_custom_urls',
			$this->config->getAppValue(Application::APP_ID, 'disable_custom_urls', Application::AvailableSettings['disable_custom_urls']));
		$this->initialStateService->provideInitialState(Application::APP_ID, 'sso_force_iframe',
			$this->config->getAppValue(Application::APP_ID, 'sso_force_iframe', Application::AvailableSettings['sso_force_iframe']));

		$default_server_domain = $this->config->getAppValue(Application::APP_ID, 'base_url', Application::AvailableSettings['base_url']);
		$custom_sso_iframe_domain = $this->config->getAppValue(Application::APP_ID, 'sso_iframe_domain', Application::AvailableSettings['sso_iframe_domain']);
		$csp = new ContentSecurityPolicy();
		$csp->addAllowedFrameDomain($this->request->getServerHost());
		$csp->addAllowedFrameDomain($default_server_domain);

		if ($custom_sso_iframe_domain !== '') {
			$custom_domain_arr = preg_split('/\s+/', $custom_sso_iframe_domain, PREG_SPLIT_NO_EMPTY);
			foreach ($custom_domain_arr as $tmp_domain) {
				$csp->addAllowedFrameDomain($tmp_domain);
			}
		}

		$csp->addAllowedFrameDomain('blob:');
		$response->setContentSecurityPolicy($csp);

		$featurePolicy = new FeaturePolicy();
		$featurePolicy->addAllowedCameraDomain('*');
		$featurePolicy->addAllowedMicrophoneDomain('*');

		$response->setFeaturePolicy($featurePolicy);

		return $response;
	}
}
