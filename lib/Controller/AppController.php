<?php

declare(strict_types=1);
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


namespace OCA\RiotChat\Controller;

use OCA\RiotChat\AppInfo\Application;

use OC\Security\CSP\ContentSecurityPolicy;
use OCP\IConfig;
use OCP\IInitialStateService;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;

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

		$csp = new ContentSecurityPolicy();
		$csp->addAllowedFrameDomain($this->request->getServerHost());
		$response->setContentSecurityPolicy($csp);

		return $response;
	}
}
