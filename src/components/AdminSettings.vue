<!--
  - @copyright Copyright (c) 2020 Gary Kim <gary@garykim.dev>
  -
  - @author Gary Kim <gary@garykim.dev>
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
    <div>
        <SettingsSection
            :title="t('riotchat', 'Riot.im configuration')"
            :description="t('riotchat', 'Configure Riot chat here')"
        >
            <label
                ref="base_url"
                for="base_url"
            >{{ t('riotchat', 'Default server url') }}</label>
            <input
                id="base_url"
                v-model="base_url"
                type="text"
                @change="updateSetting('base_url')"
            >
            <br>
            <label
                ref="server_name"
                for="server_name"
            >{{ t('riotchat', 'Default server name') }}</label>
            <input
                id="default_server_name"
                v-model="server_name"
                type="text"
                @change="updateSetting('server_name')"
            >
            <br>
            <input
                id="disable_custom_urls"
                v-model="disable_custom_urls"
                type="checkbox"
                class="checkbox"
                @change="updateSetting('disable_custom_urls')"
            >
            <label
                ref="disable_custom_urls"
                for="disable_custom_urls"
            >{{ t('riotchat', 'Disable custom urls') }}</label>
            <br>
            <input
                id="disable_login_language_selector"
                v-model="disable_login_language_selector"
                type="checkbox"
                class="checkbox"
                @change="updateSetting('disable_login_language_selector')"
            >
            <label
                ref="disable_login_language_selector"
                for="disable_login_language_selector"
            >{{ t('riotchat', 'Disable login language selector') }}</label>
        </SettingsSection>
    </div>
</template>

<script>
import Axios from '@nextcloud/axios';
import { showError, showSuccess } from '@nextcloud/dialogs';
import { generateUrl } from '@nextcloud/router';
import { loadState } from '@nextcloud/initial-state';
import { SettingsSection } from '@nextcloud/vue';

export default {
    name: "AdminSettings",
    components: {
        SettingsSection,
    },
    data () {
        return {
            "base_url": loadState('riotchat', 'base_url'),
            "server_name": loadState('riotchat', 'server_name'),
            "disable_custom_urls": loadState('riotchat', 'disable_custom_urls') === 'true',
            "disable_login_language_selector": loadState('riotchat', 'disable_login_language_selector') === 'true',
        };
    },
    methods: {
        updateSetting (setting) {
            const value = this[setting].toString();
            const settingName = this.$refs[setting].innerText;
            Axios.put(generateUrl(`apps/riotchat/settings/${setting}`), {
                value,
            }).then(() => {
                showSuccess(t('riotchat', '{settingName} has been set to {value}', { settingName, value }));
            }).catch(() => {
                showError(t('riotchat', '{settingName} could not be set. Try reloading the page.', { settingName }));
            });
        },
    },
};
</script>

<style lang="scss" scoped>

</style>
