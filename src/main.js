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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

import { generateUrl } from '@nextcloud/router';
import { loadState } from '@nextcloud/initial-state';

document.addEventListener('DOMContentLoaded', main);

let originalTitle = "";
let iframe;
let ignoreNextIframeHashChange = false;
let ignoreNextWindowHashChange = false;

function main () {
    originalTitle = document.title;

    iframe = document.getElementById('riot-iframe');
    if (!window.location.hash && loadState('riotchat', 'disable_custom_urls') === 'true' && !window.localStorage.getItem('mx_user_id')) {
        iframe.src = generateUrl('/apps/riotchat/riot/') + '#/login';
        window.location.hash = '#/login';
    } else {
        iframe.src = generateUrl('/apps/riotchat/riot/') + window.location.hash;
    }
    iframe.onload = onIframeLoad;
}

function onIframeLoad () {
    const titleObserver = new MutationObserver(setTitle);
    titleObserver.observe(iframe.contentWindow.document.querySelector('title'), {
        childList: true,
        attributes: true,
        characterData: true,
    });

    iframe.contentWindow.onhashchange = iframeHashChanged;
    window.onhashchange = () => {
        if (ignoreNextWindowHashChange) {
            ignoreNextWindowHashChange = false;
            return;
        }
        ignoreNextIframeHashChange = true;
        iframe.contentWindow.location.hash = window.location.hash;
    };
}

function iframeHashChanged () {
    if (ignoreNextIframeHashChange) {
        ignoreNextIframeHashChange = false;
        return;
    }
    ignoreNextWindowHashChange = true;
    window.location.hash = iframe.contentWindow.location.hash;
}

function setTitle () {
    document.title = iframe.contentWindow.document.title + " - " + originalTitle;
}
