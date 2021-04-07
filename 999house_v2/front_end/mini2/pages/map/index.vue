<template>
	<view class="content">
		<web-view :webview-styles="webviewStyles" :src='h5Host+`/map/index.html?id=${ id }&city=${ city }&`+t_version'></web-view>
	</view>
</template>

<script>
	const app = getApp()
	export default {
		data() {
			return {
				id: null,
				city: null,
				webviewStyles: 'false',//禁用进度条
			}
		},
		onLoad(options) {
			this.id = options.id;
			
			let obj = {
				city_no: app.globalData.city_no,
				city_name: app.globalData.city_name,
			};
			
			obj = JSON.stringify(obj);
			this.city = encodeURI(obj);
			// console.log(getApp(),3333)
			// console.log(options,111)
			// console.log(this.$data,222)
			// console.log(app)
			// this.$http.post('news/getColumnList',{id:1}).then(res=>{
				
			// })
		},
		onShow(){
			//console.log(app.globalData.city_no)
		},
		methods: {
			
		}
	}
</script>

<style>
	
</style>
