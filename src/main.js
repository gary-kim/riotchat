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
        if (iframe.contentWindow.location.hash !== window.location.hash) {
            iframe.contentWindow.location.hash = window.location.hash;
        }
    };

    // Setting sso_force_iframe (in config) to true forces iframe even if using SSO or CAS login
    if (loadState('riotchat', 'sso_force_iframe') !== false) {
        // Watch for the localStorage change that indicates that an SSO sign in is being attempted
        // eslint-disable-next-line no-proto
        iframe.contentWindow.localStorage.__proto__.setItem = function (...params) {
            // It looks like an SSO or CAS login is being attempted
            if (params[0] === "mx_sso_hs_url" && iframe.contentWindow.location.hash === "#/login") {
                // Kick them to the non-iframed version. A bit jarring but SSO login most likely won't work in the iframe.
                window.location.href = generateUrl('/apps/riotchat/riot/#/login');
            }
            window.localStorage.setItem.apply(this, params);
        };
    }
}

function iframeHashChanged () {
    if (window.location.hash !== iframe.contentWindow.location.hash) {
        window.location.hash = iframe.contentWindow.location.hash;
    }
}

function setTitle () {
    document.title = iframe.contentWindow.document.title + " - " + originalTitle;
}
