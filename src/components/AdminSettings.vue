<!--
  - @copyright Copyright (c) 2020 Gary Kim <gary@garykim.dev>
  - @copyright Copyright (c) 2020 Samuel Llamzon
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
        <template v-if="!customConfigEnabled">
            <SettingsSection
                :title="t('riotchat', 'Element common configuration')"
                :description="t('riotchat', 'Configure Element here.')"
            >
                <p
                    class="settings-hint"
                    v-html="riotWebDocumentation"
                />
                <label
                    ref="base_url"
                    for="base_url"
                >{{ t('riotchat', 'Default server URL:') }}</label>
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
                >{{ t('riotchat', 'Default server name:') }}</label>
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
                >{{ t('riotchat', 'Disable custom URLs') }}</label>
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
            <SettingsSection
                :title="t('riotchat', 'Jitsi settings')"
                :description="t('riotchat', 'Configure the Jitsi instance that Element will connect to.')"
            >
                <label
                    ref="jitsi_preferred_domain"
                    for="jitsi_preferred_domain"
                >{{ t('riotchat', 'Custom Jitsi instance (leave blank to use default Element Jitsi server):') }}</label>
                <input
                    id="jitsi_preferred_domain"
                    v-model="jitsi_preferred_domain"
                    type="text"
                    @change="updateSetting('jitsi_preferred_domain')"
                >
            </SettingsSection>
            <SettingsSection
                :title="t('riotchat', 'Custom integration server')"
                :description="t('riotchat', 'Configure a custom integration server for Element (leave empty to use Scalar).')"
            >
                <label
                    ref="integrations_ui_url"
                    for="integrations_ui_url"
                >{{ t('riotchat', 'Integration UI URL:') }}</label>
                <input
                    id="integrations_ui_url"
                    v-model="integrations_ui_url"
                    type="text"
                    @change="updateSetting('integrations_ui_url')"
                >
                <br>
                <label
                    ref="integrations_rest_url"
                    for="integrations_rest_url"
                >{{ t('riotchat', 'Integration REST URL:') }}</label>
                <input
                    id="integrations_rest_url"
                    v-model="integrations_rest_url"
                    type="text"
                    @change="updateSetting('integrations_rest_url')"
                >
                <br>
                <label
                    ref="integrations_widgets_urls"
                    for="integrations_widgets_urls"
                >{{ t('riotchat', 'Integration widgets URL:') }}</label>
                <input
                    id="integrations_widgets_urls"
                    v-model="integrations_widgets_urls"
                    type="text"
                    @change="updateSetting('integrations_widgets_urls')"
                >
            </SettingsSection>
        </template>
        <SettingsSection
            :title="t('riotchat', 'Custom Element config')"
            :description="t('riotchat', 'Specify a custom configuration for Element.')"
        >
            <input
                id="enable_custom_json"
                type="checkbox"
                class="checkbox"
                :checked="customConfigEnabled"
                @change="(e) => { toggleCustomConfig(e.currentTarget.checked) }"
            >
            <label
                ref="enable_custom_json"
                for="enable_custom_json"
            >{{ t('riotchat', 'Use a custom configuration') }}</label><br>
            <textarea
                v-if="customConfigEnabled"
                id="riotchat-custom"
                ref="custom_json"
                v-model="custom_json"
                :class="{ 'riotchat-error': !customConfigValid }"
                @change="() => { customConfigValid ? updateSetting('custom_json', 'custom_json') : '' }"
            />
            <div
                v-else-if="custom_json_loading"
                class="icon-loading loading-div"
            />
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
            "jitsi_preferred_domain": loadState('riotchat', 'jitsi_preferred_domain'),
            "custom_json": loadState('riotchat', 'custom_json'),
            "integrations_ui_url": loadState('riotchat', 'integrations_ui_url'),
            "integrations_rest_url": loadState('riotchat', 'integrations_rest_url'),
            "integrations_widgets_urls": loadState('riotchat', 'integrations_widgets_urls'),
            "custom_json_loading": false,
        };
    },
    computed: {
        featureDocumentation () {
            return t('riotchat', 'These are experimental features in Element that you can enable. For information on what each feature is, check out the documentation for it {linkstart}here{linkend}.')
                .replace('{linkstart}', `<a href="https://github.com/vector-im/riot-web/blob/${RIOT_WEB_HASH}/docs/labs.md" target="_blank" rel="noopener noreferrer">`)
                .replace('{linkend}', `</a>`);
        },
        riotWebDocumentation () {
            return t('riotchat', 'This version of Element for Nextcloud is based on Element Web {riotWebVersion}. Check out the source code for Element Web {linkstart}here{linkend}.', { riotWebVersion: RIOT_WEB_VERSION })
                .replace('{linkstart}', `<a href="https://github.com/vector-im/riot-web/tree/${RIOT_WEB_HASH}" target="_blank" rel="noopener noreferrer">`)
                .replace('{linkend}', `</a>`);
        },
        customConfigEnabled () {
            return this.custom_json !== '';
        },
        customConfigValid () {
            try {
                JSON.parse(this.custom_json);
            } catch {
                return false;
            }
            return true;
        },
    },
    methods: {
        updateSetting (setting, settingName) {
            const value = this[setting].toString();
            if (!settingName) {
                settingName = this.$refs[setting].innerText.split("(")[0].split(":")[0].trim();
            }
            this.sendUpdate(setting, settingName, value);
        },
        updateLabSetting (setting) {
            const value = this.labs[setting].toString();
            this.sendUpdate(setting, t('riotchat', 'Experimental feature {feature}', { feature: setting }), value);
        },
        sendUpdate (setting, settingName, value) {
            Axios.put(generateUrl(`apps/riotchat/settings/${setting}`), {
                value,
            }).then(() => {
                if (settingName === 'custom_json') {
                    showSuccess(t('riotchat', 'Custom config has been set'));
                } else {
                    showSuccess(t('riotchat', '{settingName} has been set to {value}', { settingName, value }));
                }
            }).catch(() => {
                if (settingName === 'custom_json') {
                    showSuccess(t('riotchat', 'Custom config could not be set. Try reloading the page.'));
                } else {
                    showError(t('riotchat', '{settingName} could not be set. Try reloading the page.', { settingName }));
                }
            });
        },
        toggleCustomConfig (setting) {
            if (setting) {
                this.custom_json_loading = true;
                Axios.get(generateUrl('apps/riotchat/riot/config.json')).then(res => {
                    this.custom_json_loading = false;
                    this.custom_json = JSON.stringify(res.data);
                    this.updateSetting('custom_json', 'custom_json');
                });
            } else {
                this.custom_json = "";
                this.updateSetting('custom_json', 'custom_json');
            }
        },
    },
};
</script>

<style lang="scss" scoped>
#riotchat-custom {
    width: 100%;
    height: 10em;

    &.riotchat-error {
        border-color: var(--color-error) !important;
    }
}
.loading-div {
    height: 3em;
}
</style>
