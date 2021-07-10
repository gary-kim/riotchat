<?php
/**
 * @copyright Copyright (c) 2020 Gary Kim <gary@garykim.dev>
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

use OC\Route\Router;
use OCA\RiotChat\RouteConfig;
use OCA\RiotChat\AppInfo\Application;

$application = \OC::$server->get(Application::class);
$router = $this;


// by using a modified RouteConfig to register the routes we can bypass the root url restrictions placed on most apps
$routeConfig = new RouteConfig($application->getContainer(), $router,  [
	'routes' => [
		// elementweb routes
		['name' => 'element#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'static#index', 'url' => '/riot/', 'verb' => 'GET'],
		['name' => 'element#config', 'url' => '/riot/config.json', 'verb' => 'GET'],
		['name' => 'static#usercontent', 'url' => '/riot/bundles/{version}/usercontent.js', 'verb' => 'GET'],
		['name' => 'static#riot', 'url' => '/riot/{path}', 'verb' => 'GET', 'requirements' => ['path' => '.+']],
		['name' => 'settings#setSetting', 'url' => '/settings/{key}', 'verb' => 'PUT'],
		// general personal matrix routes
		[
			'name' => 'matrix#whoami',
			'url' => '/whoami',
			'verb' => 'GET',
		],
		[
			'name' => 'matrix#login',
			'url' => '/login',
			'verb' => 'POST',
		],
		[
			'name' => 'matrix#logout',
			'url' => '/logout',
			'verb' => 'POST',
		],
		[
			'name' => 'matrix#roomSummary',
			'url' => '/room_summary',
			'verb' => 'GET',
		],
		// file sharing routes
		[
			'name' => 'fileShare#matrixDownload',
			'url' => '/_matrix/media/r0/download/{mxc}',
			'verb' => 'GET',
			'requirements' => ['mxc' => '.+'],
			'root' => '',
		],
		[
			'name' => 'fileShare#matrixThumbnail',
			'url' => '/_matrix/media/r0/thumbnail/{mxc}',
			'verb' => 'GET',
			'requirements' => ['mxc' => '.+'],
			'root' => '',
		],
		[
			'name' => 'fileShare#matrixEvent',
			'url' => '/_matrix/media/r0/event/{mxc}',
			'verb' => 'GET',
			'requirements' => ['mxc' => '.+'],
			'root' => '',
		],

	]
]);
$routeConfig->register();
