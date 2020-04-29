<template>
    <div>
        <SettingsSection
            :title="t('riotchat', 'Riot.im configuration')"
            :description="t('riotchat', 'Configure Riot chat here')"
        >
            <label for="default_server_url">{{ t('riotchat', 'Default server url') }}</label>
            <input
                id="default_server_url"
                v-model="base_url"
                type="text"
                @change="updateSetting('base_url')"
            >
            <br>
            <label for="default_server_url">{{ t('riotchat', 'Default server name') }}</label>
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
            <label for="disable_custom_urls">{{ t('riotchat', 'Disable custom urls') }}</label>
            <br>
            <input
                id="disable_login_language_selector"
                v-model="disable_login_language_selector"
                type="checkbox"
                class="checkbox"
                @change="updateSetting('disable_login_language_selector')"
            >
            <label for="disable_login_language_selector">{{ t('riotchat', 'Disable login language selector') }}</label>
        </SettingsSection>
    </div>
</template>

<script>
import Axios from '@nextcloud/axios';
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
            Axios.put(generateUrl(`apps/riotchat/settings/${setting}`), {
                value: this[setting].toString(),
            });
        },
    },
};
</script>

<style lang="scss" scoped>

</style>
