<template>
	<DashboardWidget :items="items">
		<template v-slot:default="{ item }">
			{{ item.title }}
		</template>
	</DashboardWidget>
</template>

<script>
import { DashboardWidget } from '@nextcloud/vue-dashboard';
import * as matrixsdk from 'matrix-js-sdk';
import StorageManager from 'matrix-react-sdk/src/utils/StorageManager';
export default {
	name: 'Dashboard',
	components: {
		DashboardWidget,
	},
	data () {
		return {
			items: [],
		};
	},
	async mounted() {
		console.log('loading this')
		console.log(await StorageManager.idbLoad("account", "mx_access_token"));
		const db = indexedDB.open('matrix-react-sdk');
		db.onsuccess = () => {

			// const m = matrixsdk.createClient({
			// 	baseUrl: localStorage.getItem("mx_hs_url"),
			// 	idBaseUrl: localStorage.getItem("mx_is_url"),
			// 	accessToken: loclaStorage.getItem()
			// })
			// const rooms = await matrixsdk.getJoinedRooms()
		}
	}
}
</script>

<style scoped>

</style>
