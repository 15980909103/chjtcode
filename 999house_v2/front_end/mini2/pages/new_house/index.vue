<template>
	<view class="content">
		<!-- <web-view :webview-styles="webviewStyles" :src='h5Host+"/new_house/index.html?"+t_version'></web-view> -->
		<div class="search" v-if="flag">
				<top-bar type="1" :icon-style="{
					width: '1rem',
					fontSize: '.5rem',
					color: 'rgba(173, 173, 173, 1)',
					marginLeft: '-.3rem'
				}">
				</top-bar>
				<u-search class="search-box" disabled placeholder="请输入搜索关键词" @click="goPages('index/search',{ type: 3 })"></u-search>
				<!-- <span class="search-map" @click="goPage('map/index.html')">地图</span> -->
				<!-- <div class="search-tip" :data-badge="badge">
					<span class="iconfont iconxiaoxi-4"></span>
				</div> -->
			</div>
		
			<div class="box" v-if="boxList.length > 0">
				<div class="box-wrap" v-for="(item,index) in boxList" :key="index">
					<div class="box-item" @click="goActive(item.href)">
						<span class="box-title">{{ item.title }}</span>
						<div class="box-img-wrap">
							<template v-for="(img,key) in item.img">
								<img :src="$http.imgDirtoUrl(img)" :key="key" v-if="key<1">
							</template>
						</div>
					</div>
				</div>
			</div>
			<div :class="flag?'drop':'drops'" style="width: 100%;">
				<common-sizer  @result="chooseResult" :default_data='default_data' :more-data="moreData"></common-sizer>
		
				<common-tag :list="tipList" margin-right=".3rem" @change="chooseTip" v-if="flag" start="1"></common-tag>
			</div>
		
			<van-list style="width: 100%;" v-model="loading" :finished="finished" finished-text="没有更多了" @load="onLoads">
				<common-newhouse :list="list" @del="(e)=>{ list = e  }"></common-newhouse>
			</van-list>
			<div id="container-user-site"></div>
		</div>
		
	</view>
</template>

<script>
	import commonNewhouse from '@/components/common/template_newHouse';
	import commonSizer from '@/components/common/sizer.vue';
	import commonTag from '@/components/common/tag.vue';
	export default {
		data() {
			return {
				webviewStyles: 'false',//禁用进度条
				badge: 20,
				boxList: [],
				city_no: 0,
				option: [
					{ text: '全部商品', value: 0 },
					{ text: '新款商品', value: 1 },
					{ text: '活动商品', value: 2 },
				],
				tabChoose: {},
				// 外部筛选
				tipChoose: -1,
				tipList: [
					{
						id: 0,
						name: '在售'
					},
					{
						id: 1,
						name: '本月开盘'
					},
					{
						id: 3,
						name: '近期开盘'
					},
				],
				// 数据
				list: [],
				loading: true,
				finished: false,
				page: 0,
				maxPage: 1,
				flag:true,
				// 筛选数据
				default_data:{},
				moreData: {},
			}
		},
		provide () {
		    return {
		      default_data: this.default_data
		    }
		  },
		components:{
			commonNewhouse,
			commonSizer,
			commonTag
		},
		onLoad(options) {
			this.$api.localStore.localDel('pre-page');
			// console.log(option)
			// if( this.$api.funcUrlDel().option && this.$api.funcUrlDel().option.more_type && this.$api.funcUrlDel().option.more_tag){
			// 	this.moreData = {
			// 		type: this.$api.funcUrlDel().option.more_type,
			// 		id: this.$api.funcUrlDel().option.more_tag
			// 	}
			// }
			if(options&&options.more_type&&options.more_tag){
				this.moreData = {
					type: options.more_type,
					id: options.more_tag
				}
			}
			this.city_no = getApp().getCurrentCity().city_no
			this.getHouseActivty();
			// this.$http.getCurrentCity().then( data=>{
			// 	this.city_no = data.city_no;
			// 	this.getHouseActivty();
			// })
			if(options&&options.id){
				this.flag = false
				this.tabChoose.area_no = options.id
				this.default_data.site_center = {
					pid:options.id,
					type:'area',
					district: options.district
				}
			}
			// if( this.$api.funcUrlDel().option && this.$api.funcUrlDel().option.id){
			// 	this.flag = false
			// 	this.tabChoose.area_no = this.$api.funcUrlDel().option.id
			// 	this.default_data.site_center = {
			// 		pid:this.$api.funcUrlDel().option.id,
			// 		type:'area',
			// 		district: this.$api.funcUrlDel().option.district
			// 	}
			// }
			
			// this.$nextTick(()=>{
			// 	this.$refs.DOM.scrollTo(0,3500)
			// })
		},
		methods:{
			init() {
				this.list = [];
				this.loading = true;
				this.finished = false;
				this.page = 0;
				this.maxPage = 1;
			},
			getHouseActivty() {
				this.$http.post(
					'/adv/getAdvByFlag',
					{
						falg: 'h5_home_estates',
						city_no: this.city_no,
						limit: 999
					}
				).then( res=>{
					const data = res.data;
					
					// console.log(res)
					// console.log(data)
					this.boxList = data;
					
					// if( ( !this.$api.funcUrlDel().option ) || (this.$api.funcUrlDel().option && !this.$api.funcUrlDel().option.more_type && !this.$api.funcUrlDel().option.more_tag) ){
						this.onLoads();
					// }
				}).catch(res=>{
					console.log(res)
					if( typeof res == 'object' ){
						this.$toast(res.msg);
					}
					this.onLoads();
				})
			},
			onLoads() {
				if( this.page >= this.maxPage ) {
					this.loading = false;
					this.finished = true;
					return;
				}
				
				let page = this.page;
				
				page++;
				
				let data = {
					city_no: this.city_no,
					page_size: 6,
					page: page
				};
				
				data = { ...data, ...this.tabChoose };
				
				if( this.tipChoose != -1 ){
					data.cond = Number(this.tipChoose) + 1;
				}
				
				// console.log(data)
				
				this.$http.post(
					'/estates/getEstatesList',
					data
				).then( res=>{
					const data = res.data;
					let arr = [];
					
					this.maxPage = data.last_page;
					this.page = data.current_page;
		
					arr = this.$api.createHouseList( data, 1 ); 
					
					this.list = [...this.list, ...arr];
					
					if( this.page >= this.maxPage ) {
						this.finished = true;
					}
					
					this.loading = false;
				})
			},
			chooseResult(e) {
				const result = e;
				const obj = {};
			
				for( let i in result ){
					switch( i ){
						case 'site':
							if( result[i] && result[i].length > 0 && result[i] != 0 ){
								if( result.siteCenter.type != 'subway' ){
									if( typeof result[i] != 'object' ){
										obj.area_no = result[i];
									} else {
										obj.business_no = result[i];
									}
								} else {
									if( typeof result[i] != 'object' ){
										obj.subway = Number(result[i]);
									} else {
										obj.subway = result.siteCenter.pid;
										obj.sites = result[i];
									}
								}
							};
							break;
							
						case 'price':
							if( !Array.isArray(result[i]) ){
								obj.price_type = result[i].type;
								obj.price = result[i].val;
							}
							break;
						case 'type':
							if( result[i] != 0 ){
								obj.rooms = result[i];
							}
							break;
						case 'more':
							const more = result[i];
							for( let k in more ) {
								if( more[k] && more[k].length > 0 ){
									obj[k] = more[k];
								}
							}
							break;
					}
				}
				// console.log(obj)
				// return;
				this.tabChoose = obj;
				
				this.init();
				this.onLoads();
			},
			chooseTip(index) {
				if( index == this.tipChoose ){
					this.tipChoose = -1;
				} else {
					this.tipChoose = index;
				}
				// console.log(this.tipChoose)
				this.init();
				this.onLoads();
			},
			goPages( url, data ) {
				if( url == 'index/search'){
					if(window){
						this.$api.localSet('pre-page', window.location.href)
					}
				}
				
				this.goPage(url,data);
			},
			goActive(href) {
				if(!$api.trim(href)){
					return
				}
				this.goPage(href);
			},
		},
	}
</script>

<style>
	.content {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}

	.logo {
		height: 200rpx;
		width: 200rpx;
		margin-top: 200rpx;
		margin-left: auto;
		margin-right: auto;
		margin-bottom: 50rpx;
	}

	.text-area {
		display: flex;
		justify-content: center;
	}

	.title {
		font-size: 36rpx;
		color: #8f8f94;
	}
</style>
