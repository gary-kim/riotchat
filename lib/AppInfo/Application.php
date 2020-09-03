<?php

declare(strict_types=1);

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

namespace OCA\RiotChat\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'riotchat';

	public const AvailableSettings = [
		'base_url' => 'https://matrix-client.matrix.org',
		'server_name' => 'Matrix Homeserver',
		'disable_custom_urls' => 'false',
		'disable_login_language_selector' => 'false',
		'jitsi_preferred_domain' => '',
		'integrations_ui_url' => '',
		'integrations_rest_url' => '',
		'integrations_widgets_urls' => '',
		'custom_json' => '',
	];

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);
	}

	public static function AvailableLabs() {
		// Element Web has removed the current Labs system. https://github.com/gary-kim/riotchat/issues/139
		// TODO: Remove the labs feature fully
		return [];
	}
}
