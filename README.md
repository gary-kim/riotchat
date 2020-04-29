# Riot Chat for Nextcloud

Riot Chat for Nextcloud allows you to install Riot easily through Nextcloud and join the Matrix decentralized communication network.

This app does not include a Matrix server, only the client. You will have to either set up your own Matrix homeserver, rent one, or use a public one.

This project is not affiliated, associated, authorized, endorsed by, or in any way officially connected with Riot.im, or any of its subsidiaries or its affiliates.

The name Riot.im as well as related names, marks, emblems and images are registered trademarks of their respective owners.

The upstream project can be found at [https://github.com/vector-im/riot-web](https://github.com/vector-im/riot-web).

### Installation

This Nextcloud app is not yet available on the Nextcloud App Store. Until then, follow these steps to install this app:

You will need to have **Node**, **npm**, **yarn** (for compiling Riot.im), and **Make** installed.

Clone the repository into `apps/riotchat` on your server then run
```bash
make
```

### License

Copyright © 2020 Gary Kim &lt;<gary@garykim.dev>&gt;

Using code from the Riot Web project. <https://github.com/vector-im/riot-web>. Huge thanks to the Riot Web developers. They're doing the actual heavy lifting here.
