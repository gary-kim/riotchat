import { generateUrl } from '@nextcloud/router';

document.addEventListener('DOMContentLoaded', main);

function main () {
    document.getElementById('riot-iframe').src = generateUrl('/apps/riotchat/riot/');
}
