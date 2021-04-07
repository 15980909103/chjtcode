<template>
	<view class="content" v-cloak ref="DOM" style="background: #F5F5F5;">
		<div class="header_box">
			<!-- 头部 -->
			<van-sticky @scroll="(e)=>{ topIsFixed = e.isFixed }">
				<headerindex :location="city_name" :isFixed="topIsFixed"></headerindex>
			</van-sticky>
			<!-- banner -->
			<bannerindex v-if='bannerList.length' :list='bannerList'></bannerindex>
		</div>
		<!-- 导航 -->
		<navindex v-if='columns.length' :nav='columns'></navindex>
		<!-- 便捷模块 -->
		<speedyindex></speedyindex>
		<!-- 活动专栏 -->
		<activityindex v-if='activityList.length' :activitylist='activityList'></activityindex> 
		<!-- 资讯/新房 -->
		<news></news>
		<!-- <web-view :webview-styles="webviewStyles" :src='h5Host+"/index/index.html?"+t_version'></web-view> -->
	</view>
</template>

<script>
	const app = getApp()
	import headerindex from '@/components/common/header.vue'
	import bannerindex from '@/components/common/banner.vue'
	import navindex from '@/components/common/nav.vue'
	import speedyindex from '@/components/common/speedy.vue'
	import activityindex from '@/components/common/activity.vue'
	import news from '@/components/common/news.vue'
	export default {
		components:{
			headerindex,
			bannerindex,
			navindex,
			speedyindex,
			activityindex,
			news,
		},
		data() {
			return {
				webviewStyles: 'false',//禁用进度条
				topIsFixed: false,
				locationShow: false,
				columns:[],
				bannerList: [],
				activityList: [],
				city_no: '',
				city_name: ''
			}
		},
		onLoad() {
			this.getUserLocation();
		},
		onShow(){
			//console.log(app.globalData.city_no)
			this.city_no = app.globalData.city_no;
		},
		methods:{
			handleScroll() {
			  let scrollY = document.documentElement.scrollTop
			  if (scrollY > 600) {
				  // do something...
				   }
				else {
					}
				// do something...      }
			},
			// 获取位置
			getUserLocation() {
				this.city_no = getApp().getCurrentCity().city_no
				this.city_name = getApp().getCurrentCity().city_name
				this.getColumnList();
				this.getAdvs();
				// getApp().getCurrentCity().then( data=>{
				// 	this.city_no = data.city_no;
				// 	this.city_name = data.city_name;
					
				// 	this.getColumnList();
				// 	this.getAdvs();
				// })
			},
			getAdvs(){
				const data = {
					falg: ['h5_home_top1','h5_home_top2'],
					city_no: this.city_no
				};
				
				this.$http.post('/adv/getAdvByFlag',
					data,
				).then( res=>{
					let data = res.data;
					let banners = [];
					for(let i in data.h5_home_top1[0].img){
						banners.push({
							img: data.h5_home_top1[0].img[i],
							href: data.h5_home_top1[0].href,
							info: data.h5_home_top1[0].info,
							cover: data.h5_home_top1[0].cover,
						});
					}
					this.bannerList = banners;
		
					let activityList = [];
					for(let i in data.h5_home_top2){
						data.h5_home_top2[i]&&data.h5_home_top2[i].img&&activityList.push({
							img: data.h5_home_top2[i].img[0],
							href: data.h5_home_top2[i].href,
							info: data.h5_home_top2[i].info,
							cover: data.h5_home_top2[i].cover,
							title:data.h5_home_top2[i].title,
							sub_title: data.h5_home_top2[i].sub_title
						});
					}
					this.activityList = activityList;
					
					// this.wxShare(res);
				})
			},
			getColumnList(){
				const data = {
					group_flag: 'h5_home_icons'
				};
				
				this.$http.post(
					'/news/getColumnList',
					data,
				).then( res=>{
					let data = res.data;
					this.columns = data;
				})
			}
		}
	}
</script>

<style>
	
</style>
