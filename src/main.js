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
    if (loadState('riotchat', 'sso_force_iframe') !== 'true') {
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
    const interval = setInterval(() => {
        const titles = iframe.contentWindow.document.querySelector(".mx_RoomSublist_tiles")
        const actions = iframe.contentWindow.document.querySelector(".mx_MessageComposer_actions")
    
        if (titles) {
          setupClickHandler(iframe, titles)
          clearTimeout(interval)
        }
    
        if (actions) {
          addButton(iframe.contentWindow.document)
        }
    }, 1000)
    // a nonsense if {} just to be sure closePickerIframe function is kept when building the app
    if (iframe.contentWindow.location.hash === "aaaaaaaaaaaaa") {
        closePickerIframe()
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

function setupClickHandler(iframe, titles) {
    const iframeDocument = iframe.contentWindow.document
    titles.addEventListener('click', function(event) {
        const roomTile = event.target.closest('.mx_RoomTile')
  
        if (roomTile) {
            addButton(iframeDocument)
        }
    })
}

function addButton(iframeDocument) {
    const interval = setInterval(() => {
        const button = iframeDocument.querySelector(".mx_MessageComposer_upload");
  
            if (button) {
            const newButton = iframeDocument.createElement('a');
            newButton.setAttribute('aria-label', 'Nextcloud Share Link');
            newButton.style.marginLeft = '0';
            newButton.style.display = 'flex';
            newButton.style.alignItems = 'center';
            newButton.style.justifyContent = 'center';

            const svg = iframeDocument.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('width', '26');
            svg.setAttribute('height', '26');
            svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');

            const path = iframeDocument.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', 'M 2.4 1.2 A 2.4 2.4 0 0 0 0 3.6 L 0 20.4 A 2.4 2.4 0 0 0 2.4 22.8 L 21.6 22.8 A 2.4 2.4 0 0 0 24 20.4 L 24 7.2 A 2.4 2.4 0 0 0 21.6 4.8 L 13.692012 4.8 L 10.8 1.9079883 A 2.4 2.4 0 0 0 9.1079883 1.2 L 2.4 1.2 z M 12.015293 9.1495898 C 14.128085 9.1487404 15.902746 10.587886 16.45834 12.528223 C 16.941727 11.501136 17.973094 10.772227 19.176035 10.772227 A 3.0319929 3.0319929 0 0 1 22.194434 13.790625 A 3.0311433 3.0311433 0 0 1 19.176035 16.809023 C 17.973944 16.809023 16.942602 16.080963 16.459219 15.053027 C 15.902774 16.993365 14.128085 18.43166 12.015293 18.43166 C 9.8914562 18.43166 8.1066027 16.97725 7.5620508 15.019922 C 7.0863114 16.066548 6.0396784 16.809023 4.8231445 16.809023 A 3.0319929 3.0319929 0 0 1 1.8055664 13.790625 A 3.0319929 3.0319929 0 0 1 4.8231445 10.772227 C 6.0396784 10.772227 7.0871316 11.51388 7.5628711 12.560508 C 8.107423 10.604028 9.8914562 9.1495898 12.015293 9.1495898 z M 12.015293 10.920879 C 10.420718 10.920879 9.1464258 12.195199 9.1464258 13.790625 A 2.856139 2.856139 0 0 0 12.015293 16.660371 C 13.610721 16.660371 14.885039 15.38605 14.885039 13.790625 C 14.885039 12.195199 13.610721 10.920879 12.015293 10.920879 z M 4.8231445 12.543516 L 4.8231445 12.544336 C 4.1239774 12.544336 3.5768555 13.091459 3.5768555 13.790625 A 1.2335258 1.2335258 0 0 0 4.8231445 15.037734 C 5.5223118 15.036884 6.069375 14.489792 6.069375 13.790625 C 6.069375 13.091459 5.5214622 12.543516 4.8231445 12.543516 z M 19.176035 12.543516 L 19.176035 12.544336 C 18.476868 12.544336 17.929746 13.091459 17.929746 13.790625 A 1.2335258 1.2335258 0 0 0 19.176035 15.037734 C 19.875202 15.037734 20.423145 14.489792 20.423145 13.790625 C 20.423145 13.091459 19.875202 12.543516 19.176035 12.543516 z ');
            path.setAttribute('fill', '#656c76');

            svg.appendChild(path);
            newButton.appendChild(svg);

            newButton.onclick = () => {
                // Create the iframe element
                var pickerFrame = document.createElement("iframe");
                pickerFrame.id = "pickerFrame";
                pickerFrame.src = generateUrl('/apps/picker/single-link?option=Clipboard'); // Set the source URL
                pickerFrame.style.height = '800px'; // set the height
                pickerFrame.style.width = '800px'; // set the width
                pickerFrame.style.maxHeight = '80vh'; // set the max height for mobile
                pickerFrame.style.maxWidth = '100%'; // set the max width for mobile
                pickerFrame.style.border = 'none'; // Remove the default border
                // Set the iframe styles to position it in the middle and on top
                pickerFrame.style.position = 'fixed';
                pickerFrame.style.top = '50%';
                pickerFrame.style.left = '50%';
                pickerFrame.style.transform = 'translate(-50%, -50%)';
                pickerFrame.style.zIndex = 9999; // Adjust this value as needed
                // Append the iframe to the document body
                document.body.appendChild(pickerFrame);
            };

            newButton.addEventListener('mouseover', () => {
                path.setAttribute('fill', '#ebeef2');
            });

            newButton.addEventListener('mouseout', () => {
                path.setAttribute('fill', '#656c76');
            });
            
            button.insertAdjacentElement('afterend', newButton);
            clearInterval(interval);
        }
    }, 100);
}

function closePickerIframe() {
    const pickerFrame = document.getElementById('pickerFrame');
    document.body.removeChild(pickerFrame); // Remove the iframe
}
