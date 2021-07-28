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
	<div>
        <SettingsSection
            :title="t('riotchat', 'Matrix Sharing')"
            :description="t('riotchat', 'Configure how to share things into matrix.')"
        >
            <label
                ref="share_domain"
                for="share_domain"
            >{{ t('riotchat', 'Domain:') }}</label>
            <input
                id="share_domain"
                v-model="share_domain"
                type="text"
                @change="updateSetting('share_domain')"
            >
            <br>
            <label
                ref="share_prefix"
                for="share_prefix"
            >{{ t('riotchat', 'Prefix:') }}</label>
            <input
                id="share_prefix"
                v-model="share_prefix"
                type="text"
                @change="updateSetting('share_prefix')"
            >
            <br>
            <label
                ref="share_suffix"
                for="share_suffix"
            >{{ t('riotchat', 'Suffix:') }}</label>
            <input
                id="share_suffix"
                v-model="share_suffix"
                type="text"
                @change="updateSetting('share_suffix')"
            >
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
	name: "ShareAdminSettings",
	components: {
		SettingsSection,
	},
	directives: {
		Tooltip,
	},
	data() {
		return {
            "share_domain": loadState('riotchat', 'share_domain'),
            "share_prefix": loadState('riotchat', 'share_prefix'),
            "share_suffix": loadState('riotchat', 'share_suffix'),
		};
	},
	methods: {
		updateSetting (setting, settingName) {
            const value = this[setting].toString();
            if (!settingName) {
                settingName = this.$refs[setting].innerText.split("(")[0].split(":")[0].trim();
            }
            this.sendUpdate(setting, settingName, value);
        },
		sendUpdate (setting, settingName, value) {
            Axios.put(generateUrl(`apps/riotchat/settings/${setting}`), {
                value,
            }).then(() => {
                showSuccess(t('riotchat', '{settingName} has been set to {value}', { settingName, value }));
            }).catch(() => {
                showError(t('riotchat', '{settingName} could not be set. Try reloading the page.', { settingName }));
            });
        },

	},
}
</script>
