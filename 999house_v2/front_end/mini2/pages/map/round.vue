<template>
	<view class="content">
		<web-view :webview-styles="webviewStyles" :src='h5Host+`/map/round.html?id=${ id }&`+t_version'></web-view>
	</view>
</template>

<script>
	const app = getApp()
	export default {
		data() {
			return {
				webviewStyles: 'false',//禁用进度条
				id: -1,
			}
		},
		onLoad(options) {
			this.id = options.id;
	
			uni.setNavigationBarTitle({
				title: decodeURIComponent(options.name)
			});
			// console.log(getApp(),3333)
			// console.log(options,111)
			// console.log(this.$data,222)
			// console.log(app)
			// this.$http.post('news/getColumnList',{id:1}).then(res=>{
				
			// })
		},
		onShow(){
			//console.log(app.globalData.city_no)
			this.city_no = app.globalData.city_no;
		},
		methods: {
			
		}
	}
</script>

<style>
	
</style>
