# Element for Nextcloud

[![Build Status](https://ghdrone.garykim.dev/api/badges/gary-kim/riotchat/status.svg)](https://ghdrone.garykim.dev/gary-kim/riotchat)
[![Matrix Room: #riotchatfornextcloud-general:garykim.dev](https://img.shields.io/matrix/riotchatfornextcloud-general:garykim.dev?label=%23riotchatfornextcloud-general%3Agarykim.dev&logo=matrix&server_fqdn=matrix.org)](https://matrix.to/#/#riotchatfornextcloud-general:garykim.dev)
[![GitHub Releases](https://img.shields.io/github/downloads/gary-kim/riotchat/latest/total?label=downloads)](https://apps.nextcloud.com/apps/riotchat)


Element for Nextcloud allows you to install Element easily through Nextcloud and join the Matrix decentralized communication network.

This app does not include a Matrix server, only the client. You will have to either set up your own Matrix homeserver, rent one, or use a public one.

This project is not affiliated, associated, authorized, endorsed by, or in any way officially connected with Element or the Element project, or any of its subsidiaries or its affiliates.

The name Element as well as related names, marks, emblems and images are registered trademarks of their respective owners.

The upstream project can be found at [https://github.com/vector-im/riot-web](https://github.com/vector-im/riot-web).

![Screenshot 2](https://garykim.dev/res/large/riotchat-for-nextcloud/screenshot-2.png)
![Screenshot 3](https://garykim.dev/res/large/riotchat-for-nextcloud/screenshot-3.png)

If you want to join the discussion, join the Matrix community chat room here: <https://matrix.to/#/#riotchatfornextcloud-general:garykim.dev>. There is work going on to get this bridged with Nextcloud Talk.

### Installation

You can install this app from the [Nextcloud App Store](https://apps.nextcloud.com/apps/riotchat).

If you don't want to download the app from the Nextcloud App Store, follow these steps to install this app:

You will need to have **Node**, **npm**, **yarn** (for compiling Element), and **Make** installed.

Clone the repository into `apps/riotchat` on your server then run
```bash
git submodule update --init --recursive
make
```

### License

Copyright © 2020-2024 Gary Kim &lt;<gary@garykim.dev>&gt;

Licensed under [AGPL-3.0-or-later](LICENSE).

Using code from the Element Web project. <https://github.com/vector-im/element-web>. Huge thanks to the Element Web developers. They're doing the actual heavy lifting here.
