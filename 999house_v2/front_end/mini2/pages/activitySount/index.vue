<template>
	<view style="background-color: #F5F5F5;">
		<tabs name="活动首页"></tabs>
		<index-activity v-if="show" :id='activity_id'></index-activity>
		<!-- <index-activity></index-activity> -->
	</view>
</template>

<script>
	// const app = getApp()
	import indexActivity from '@/components/activityItem/indexActivity.vue'
	import tabs from '@/components/activityItem/tab.vue'
	// import { http, loginMixin } from '@/utils/http.js';
	export default {
		data() {
			return {
				show:false,
			}
		},
		// mixins: [loginMixin],
		components:{
			indexActivity,
			tabs
		},
		onLoad(e) {
			this.activity_id = e.activity_id
			let that = this
			//this.$api.wxLogin()
			this.show = true
		}
		// methods: {
			
		// }
	}
</script>

<style>

</style>
