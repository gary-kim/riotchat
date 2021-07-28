<!--
  - @copyright Copyright (c) 2021 Sorunome <mail@sorunome.de>
  - @copyright Copyright (c) 2020-2021 Gary Kim <gary@garykim.dev>
  - @copyright Copyright (c) 2020 Samuel Llamzon
  -
  - @author Sorunome <mail@sorunome.de>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program. If not, see <http://www.gnu.org/licenses/>.
  -
  -->

<template>
	<div class="section">
        <SettingsSection
            :title="t('riotchat', 'Matrix Login')"
            :description="t('riotchat', 'Log in to your Matrix Account')"
        >
            <form @submit="login">
                <label
                    ref="matrix_username"
                    for="matrix_username"
                >{{ t('riotchat', 'Matrix Username:') }}</label>
                <input
                    id="matrix_username"
                    v-model="matrix_username"
                    type="text"
                >
                <br>
                <label
                    ref="matrix_password"
                    for="matrix_password"
                >{{ t('riotchat', 'Matrix Password:') }}</label>
                <input
                    id="matrix_password"
                    v-model="matrix_password"
                    type="text"
                >
                <input type="submit" value="login">
                <br>
            </form>
            <template v-if="loggedIn">
                <p>{{ currentlyLoggedInMessage }}</p>
                <input type="button" @submit="logout">
            </template>
        </SettingsSection>
	</div>
</template>

<script>
import Axios from '@nextcloud/axios';
import { showError, showSuccess } from '@nextcloud/dialogs';
import { generateUrl } from '@nextcloud/router';
import { loadState } from '@nextcloud/initial-state';
import { SettingsSection, Tooltip } from '@nextcloud/vue';

export default {
	name: "PersonalSettings",
	components: {
		SettingsSection,
	},
	directives: {
		Tooltip,
	},
	data() {
		return {
		    // Form inputs
            "matrix_username": "",
            "matrix_password": "",

            // Data
            "loggedIn": false,
            "currentUsername": "",
		};
	},
    computed: {
	    currentlyLoggedInMessage() {
	        return this.t('riotchat', 'Currently logged in as {username}', {
	            username: this.currentUsername,
            });
        }
    },
	methods: {
		login () {
            const url = generateUrl('apps/riotchat/share/login');
            Axios.post(
                url,
                {
                    username: this.username,
                    password: this.password,
                }
            ).then(() => {
                showSuccess(t('riotchat', 'Successfully logged into Matrix account'));
                this.whoAmI();
            }).catch(() => {
                showSuccess(t('riotchat', 'Failed to login to Matrix account. Are your account details correct?'));
            });
        },
        logout () {
		    const url = generateUrl('apps/riotchat/share/logout');
		    Axios.post(url).then(this.whoAmI).catch(() => {
		       showError(t('riotchat', 'Failed to logout of Matrix account.'));
            });
        },
        whoAmI () {
		    const url = generateUrl('apps/riotchat/share/whoami');
		    Axios.get(url).then((res) => {
		        this.loggedIn = res.data.logged_in;
		        this.currentUsername = res.data.user_id;
            }).catch(() => {
                showError(t('riotchat', 'Could not load user info. Please reload the page.'));
            });
        }
	},
    mounted () {
	    this.whoAmI();
    },
}
</script>
