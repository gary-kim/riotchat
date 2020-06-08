# Riot Chat for Nextcloud

[![Build Status](https://ghdrone.garykim.dev/api/badges/gary-kim/riotchat/status.svg)](https://ghdrone.garykim.dev/gary-kim/riotchat)

Riot Chat for Nextcloud allows you to install Riot easily through Nextcloud and join the Matrix decentralized communication network.

This app does not include a Matrix server, only the client. You will have to either set up your own Matrix homeserver, rent one, or use a public one.

This project is not affiliated, associated, authorized, endorsed by, or in any way officially connected with New Vector or the Riot.im project, or any of its subsidiaries or its affiliates.

The name Riot.im as well as related names, marks, emblems and images are registered trademarks of their respective owners.

The upstream project can be found at [https://github.com/vector-im/riot-web](https://github.com/vector-im/riot-web).

![Screenshot 1](https://garykim.dev/res/large/riotchat-for-nextcloud/main-screenshot.png)

If you want to join the discussion, join the Matrix community chat room here: <https://matrix.to/#/#riotchatfornextcloud-general:garykim.dev>. There is work going on to get this bridged with Nextcloud Talk.

### Installation

You can install this app from the [Nextcloud App Store](https://apps.nextcloud.com/apps/riotchat).

If you don't want to download the app from the Nextcloud App Store, follow these steps to install this app:

You will need to have **Node**, **npm**, **yarn** (for compiling Riot.im), and **Make** installed.

Clone the repository into `apps/riotchat` on your server then run
```bash
git submodule update --init --recursive
make
```

### Logo

The logo is the logo of the Riot.im project. The file used is from [Fork Awesome](https://github.com/ForkAwesome/Fork-Awesome/pull/232/).

### License

Copyright © 2020 Gary Kim &lt;<gary@garykim.dev>&gt;

Licensed under [AGPL-3.0-or-later](LICENSE).

Using code from the Riot Web project. <https://github.com/vector-im/riot-web>. Huge thanks to the Riot Web developers. They're doing the actual heavy lifting here.
