# Contributing to Riot Chat for Nextcloud

Thank you for getting involved in the development of Riot Chat for Nextcloud. 

To get started, first set up a Nextcloud development environment. Refer to the [Nextcloud Developer Documentation](https://docs.nextcloud.com/server/latest/developer_manual/general/devenv.html) for information on how to do that.

Once you get your Nextcloud development environment set up, clone this repo into `apps/riotchat` in your Nextcloud directory then go in to the directory and run the following commands:
```bash
git submodule update --init --recursive
make
```
This will fetch and build all the files required to use the app.

To start the Javascript build in watch mode, you can use `npm run watch`.

### Sign-off your commit

When you commit your change, please sign off on your work to certify that you have read and agreed to the [Developer Certificate of Origin](#developer-certificate-of-origin). By signing off on your commit, you certify that the changes are yours or you have the right to use it in an open source contribution.

You can easily sign off on your work by adding the `-s` flag when you commit your change.
```
git commit -s
```
Otherwise, you can add the following line to your commit message to certify the DCO.
```
Signed-off-by: Random J Developer <random@developer.example.org>
```

### License Header

If you modify an existing file, please keep the existing license header as
it is and just add your copyright notice:

````
@copyright Copyright (c) <year>, <your name> (<your email address>)
````

If you create a new file please use this license header:

````
/**
 * @copyright Copyright (c) <year>, <your name> (<your email address>)
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
 *
 */
````

### Developer Certificate of Origin
```
Developer Certificate of Origin
Version 1.1

Copyright (C) 2004, 2006 The Linux Foundation and its contributors.
1 Letterman Drive
Suite D4700
San Francisco, CA, 94129

Everyone is permitted to copy and distribute verbatim copies of this
license document, but changing it is not allowed.


Developer's Certificate of Origin 1.1

By making a contribution to this project, I certify that:

(a) The contribution was created in whole or in part by me and I
    have the right to submit it under the open source license
    indicated in the file; or

(b) The contribution is based upon previous work that, to the best
    of my knowledge, is covered under an appropriate open source
    license and I have the right under that license to submit that
    work with modifications, whether created in whole or in part
    by me, under the same open source license (unless I am
    permitted to submit under a different license), as indicated
    in the file; or

(c) The contribution was provided directly to me by some other
    person who certified (a), (b) or (c) and I have not modified
    it.

(d) I understand and agree that this project and the contribution
    are public and that a record of the contribution (including all
    personal information I submit with it, including my sign-off) is
    maintained indefinitely and may be redistributed consistent with
    this project or the open source license(s) involved.

```
