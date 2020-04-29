import { generateUrl } from '@nextcloud/router';

document.addEventListener('DOMContentLoaded', main);

let originalTitle = "";
let iframe;

function main () {
    originalTitle = document.title;

    iframe = document.getElementById('riot-iframe');
    iframe.src = generateUrl('/apps/riotchat/riot/');
    iframe.onload = onIframeLoad;
}

function onIframeLoad () {
    const titleObserver = new MutationObserver(setTitle);
    titleObserver.observe(iframe.contentWindow.document.querySelector('title'), {
        childList: true,
        attributes: true,
        characterData: true,
    });
}

function setTitle () {
    document.title = iframe.contentWindow.document.title + " - " + originalTitle;
}
