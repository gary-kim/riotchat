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
return [
    'routes' => [
    	['name' => 'app#index', 'url' => '/', 'verb' => 'GET'],
	    ['name' => 'static#index', 'url' => '/riot/', 'verb' => 'GET'],
		['name' => 'config#config', 'url' => '/riot/config.json', 'verb' => 'GET'],
        ['name' => 'static#riot', 'url' => '/riot/{path}', 'verb' => 'GET', 'requirements' => ['path' => '.+']],
        ['name' => 'settings#setSetting', 'url' => '/settings/{key}', 'verb' => 'PUT'],
    ]
];
