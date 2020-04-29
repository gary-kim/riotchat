import { generateUrl } from '@nextcloud/router';

document.addEventListener('DOMContentLoaded', main);

let originalTitle = "";
let iframe;
let ignoreNextIframeHashChange = false;
let ignoreNextWindowHashChange = false;

function main () {
    originalTitle = document.title;

    iframe = document.getElementById('riot-iframe');
    iframe.src = generateUrl('/apps/riotchat/riot/') + window.location.hash;
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
