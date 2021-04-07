<template>
	<view class="content">
		<!-- <web-view :webview-styles="webviewStyles" :src='h5Host+"/houses/sand.html?"+t_version+"&id="+id'></web-view> -->
		<template v-if="list.length > 0">
			<u-tabs :list="list" :is-scroll="false" :current="active" name="title" @change="change" active-color="rgba(254, 130, 30, 1)"></u-tabs>
			<van-tabs class="tabs" v-model="active" sticky>
				<van-tab v-for="(item,index) in list" :title="item.title" :key="index" v-show="active == index">
					<swiper>
						<swiper-item class="swipe-item" v-for="(banner,key) in item.banner" :key="key">
							<img :src="$api.imgDirtoUrl(banner)">
						</swiper-item>
					</swiper>
					<!-- <van-swipe ref="swipe" :loop="false">
						<van-swipe-item class="swipe-item" v-for="(banner,key) in item.banner" :key="key">
							<img :src="$http.testUrl(banner)">
						</van-swipe-item>
					</van-swipe> -->
					
					
					<div class="wrap">
						<div class="info">
							<h4 class="info-title">
								{{ item.title }}
							</h4>
							<div class="info-box">
								<template v-for="(list,key) in item.info">
									<div class="info-content" :key="key">
										<span>{{ list.name }}:</span>{{ list.list }}
									</div>
									<div class="info-line" v-if=" key == 1 " />
								</template>
								
							</div>
						</div>
						
						<div class="house" v-if="item.list.length>0">
							<p class="house-title">
								{{ item.title }}楼栋户型
							</p>
							<houses-template :list="item.list"/>
						</div>
					</div>
				</van-tab>
			</van-tabs>
		</template>
		<template v-else>
			<div class="list_null" v-if='showNull'>
				<img src="../../static/null.png">
				<p>暂无数据</p>
			</div>
		</template>
	</view>
</template>

<script>
	import housesTemplate from '@/components/houses/template_house'
	export default {
		data() {
			return {
				webviewStyles: 'false',//禁用进度条
				id: 0,
				showNull: false,
				estate_id: 0,
				active: 0,
				swiperActive: 0,
				list: [],
			}
		},
		onLoad(options) {
			this.id = options.id;
			this.estate_id = options.id;
			this.getSandBuilding();
		},
		components: {
			housesTemplate
		},
		methods: {
			change(index){
				this.active = index
			},
			onChange(e) {
					this.swiperActive = e;
				},
				changeSwipe(e) {
					// console.log(this.$refs.swipe[0])
					this.$refs.swipe[0].swipeTo(e);
				},
			
				//楼栋户型
				getSandBuilding() {
					const estate_id = this.estate_id;
					const data = {
						estate_id: estate_id,
						getHouses: 1
					};
						
					this.$http.post(
						'/estates/getEstatesnewBuildingList',
						data,
					).then( res=>{
						let data = res.data;
						let newData = [];
						const tag = this.$api.localStore.localGet('u-tag');
						
						console.log('楼盘户型',data.list);
						
						data.list&&data.list.map(item=>{
							const obj = {
								title: item.name,
								banner: data.banner,
								info: [
									{
										name: '开盘',
										list: item.open_time?item.open_time:'暂无数据'
									},
									{
										name: '交房',
										list: item.delivery_time?item.delivery_time:'暂无数据'
									},
									{
										name: '单元',
										list: item.unit
									},
									{
										name: '层级',
										list: item.floor_number
									},
									{
										name: '户数',
										list: item.house_number
									},
									{
										name: '楼型',
										list: item.building_type
									}
								],
								list: []
							};
							
							item.house_list&&item.house_list.map( child=>{
								const newObj = {
									img: child.img,
									title: child.name,
									tip: [],
									area: child.built_area,
									way: tag['orientation'][child.orientation],
									price: child.price || child.price_avg || '价格待定'
								}
								
								if( child.house_purpose != 0 ){
									newObj.tip.push(tag['house_purpose'][child.house_purpose]);
								}
								
								if( child.sale_status != 0 ){
									newObj.tip.push(tag['estatesnew_sale_status'][child.sale_status]);
								}
								
								obj.list.push(newObj);
							})
							
							newData.push(obj);
						})
						
						this.list = newData;
						this.showNull = true;
					})
				},
			},
	}
</script>

<style>
	.swipe-item{
		width: 100%;
		height: 420rpx;
	}
	
	.swipe-item img{
		width: 100%;
		height: 100%;
	}
	
	.custom-indicator{
		position: absolute;
		left: 50%;
		bottom: 21rpx;
		color: #fff;
		font-size: 20rpx;
		display: flex;
		transform: translate(-50%,0);
	}
	
	.custom-indicator div{
		padding: 8rpx 27rpx;
		background-color: rgba(0, 0, 0, .5);
		border-radius: 19rpx;
	}
	
	.custom-indicator div:nth-child(2){
		margin: 0 28rpx;
	}
	
	.wrap{
		padding: 26rpx 32rpx 68rpx;
		box-sizing: border-box;
	}
	
	.info-title{
		font-size: 34rpx;
		margin-bottom: 20rpx;
	}
	
	.info-box{
		font-size: 30rpx;
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
	}
	
	.info-content{
		width: 50%;
		padding: 14rpx 0;
	}
	
	.info-content span{
		margin-right: 20rpx;
		color: rgba(117, 117, 117, 1);
	}
	
	.info-line{
		margin: 20rpx 0;
		height: 1rpx;
		width: 100%;
		/* background-color: rgba(235, 235, 235, 1); */
		border-bottom: 1px solid #EBEBEB;
	}
	
	.house-title{
		font-size: 30rpx;
		margin-top: 70rpx;
	}
</style>
