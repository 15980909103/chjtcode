<template>
	<view  v-if="Object.keys(site).length > 0" class="map-wrap">
		<map-tabs 
			:list="siteName" 
			:current="current" 
			duration="0.3" 
			active-color="rgba(254, 130, 30, 1)"
			inactive-color="rgba(117, 117, 117, 1)"
			gutter="32"
			:bold="false"
			:show-bar="false"
			@change="change"
		>
		</map-tabs>
		<map 
			id="mapId"
			class="map-class"
			:longitude="site.lng"
			:latitude="site.lat"
			
			:subkey="mapKey"
			:layer-style="2"
			
			:enable-zoom="false"
			:enable-scroll="false"
			:scale="mapScale"
			:markers="markers"
			@click="showMapTitle"
		>
			<view class="tip" @click.stop="goMore" v-if="showTitle">
				<view class="tip-title">
					{{ site.title }}
				</view>
				<view class="tip-text">
					{{ site.site }}
				</view>
				<view class="tip-arr"></view>
			</view>
			
		</map>
		<view class="map-cover" @click="goMore"></view>
		<view class="list">
			<view class="list-wrap" v-if="siteInfo[current] && siteInfo[current].length > 0">
				<template v-for="(item, index) in siteInfo[current]">
					<view 
						class="list-box" 
						:class="[ activeId == ('site-'+index) ? 'border-active' : '' ]"
						:key="index" 
						v-if="index < 3"
						@click="chooseItem(index)"
					>
					
						<view class="list-box-left">
							<view class="list-box-title van-ellipsis">
								{{ item.title }}
							</view>
							<view class="list-box-text van-ellipsis" v-if="siteName[current] == '公交'">
								{{ item.address }}
							</view>
						</view>
						<view class="list-box-right">
							{{ item._distance }}m
						</view>
					</view>
				</template>
			</view>
			<view class="list-none" v-else>
				暂无相关数据喔~
			</view>
			<view class="list-more" @click="goMore">
				<span>查看更多</span><i class="iconfont iconjiantou1"></i>
			</view>
		</view>
	</view>
</template>

<script>
	import mapTabs from '@/components/map/mapTabs';
	import txmap from '@/utils/module/map/qqmap-wx-jssdk.min.js';
	
	export default {
		data() {
			return {
				current: 0,
				siteName: ['公交','教育','医院','购物','美食'],
				siteIcon: [
					'/map/transit-icon.png', 
					'/map/school-icon.png', 
					'/map/hospital-icon.png', 
					'/map/market-icon.png', 
					'/map/food1-icon.png',
				],
				siteInfo: [],
				
				mapKey: '',
				mapEl: null,
				// mapCtx: null,
				mapScale: 16.5,	// 缩放级别，取值范围为5-18
				markers: [],
				includePoints: [],
				showTitle: true,
				activeId: null,
				arrTop: 0,
			};
		},
		components: {
			mapTabs
		},
		props: {
			site: {
				type: Object,
				default() {
					return {}
				}
			},
		},
		watch: {
			site( newV ){
				console.log(newV,'--------------new')
				this.initSiteInfo();
			}
		},
		created() {
			this.mapKey = getApp().globalData.map_key;
			
			this.mapEl = new txmap({
				key: this.mapKey
			});
			
			this.initSiteInfo();
		},
		mounted() {
			// this.mapCtx = uni.createMapContext('mapId',this);
		},
		methods: {
			async initSiteInfo() {
				let pois =[];
				
				this.siteName.map( (item,index)=>{
					pois.push(this.getSiteInfo(item))
				})

				this.siteInfo = await Promise.all(pois);

				this.initMap();
			},
			getSiteInfo(key) {
				let that = this;
				
				switch(key){
					case '教育':
						key = '学校';
						break;
					case '医院':
						key = '三级医院';
						break;
					case '购物':
						key = '购物中心';
						break;
				}
	
				return new Promise((resolve)=>{
					const obj = {
						keyword: key,
						page_size: 20,
						location: {
						  latitude: Number(that.site.lat),
						  longitude: Number(that.site.lng)
						},
						success: function (res) {
							const arr = [];
									
							res.data.map( data=>{
								if( data._distance <= 3000 ){
									arr.push(data)
								}
							})
							resolve(arr);
						},
						fail: function (res) {
							console.log(res,'-------fail')
							resolve()
						}
					};
							
					that.mapEl.search(obj);
				})
			},
			change(e) {
				this.current = e;
				this.activeId = null;
				this.getPoi();
			},
			initMap() {
				this.getSiteMarker();
				this.getPoi();
			},
			getSiteMarker() {
				const obj = {
					id: 'site',
					latitude: this.site.lat,
					longitude: this.site.lng,
					iconPath: this.$api.imgDirtoUrl('/map/site.png'),
					width: 14,
					height: 20,
					zIndex: 999
				}
				
				this.markers.push(obj)
			},
			getPoi() {
				const data = this.siteInfo[this.current];
				
				if( !data ) return;
				
				const arr = [];
				let distance = 0;
	
				data.map( (item,index)=>{
					
					const obj = {
						id: 'site-'+index,
						latitude: item.location.lat,
						longitude: item.location.lng,
						iconPath: this.$api.imgDirtoUrl(this.siteIcon[this.current]),
						width: 20,
						height: 20,
						zIndex: 1
					}
					
					arr.push(obj);
				})

				if( data.length == 0 ){
					this.mapScale = 16.5;
				} else {
					
					if( data.length > 3 ){
						 distance = data[2]._distance;
					} else {
						distance = data[data.length-1]._distance;
					}
					
					if( distance <= 50 ){
						this.mapScale = 18;
					} else if( distance <= 100 ){
						this.mapScale = 16;
					} else if( distance <= 200 ){
						this.mapScale = 15;
					} else if( distance <= 500 ){
						this.mapScale = 14;
					} else if( distance <= 1000 ){
						this.mapScale = 13;
					} else if( distance <= 2000 ){
						this.mapScale = 12;
					} else if( distance <= 3000 ){
						this.mapScale = 11.8;
					}
				}
				
				// console.log(this.mapScale)
				// console.log(arr)
				// console.log(data)
				this.markers.splice(1, this.markers.length);
				this.markers = this.markers.concat(arr);
			},
			chooseItem( index ) {
				const id = 'site-'+ (index);
				let idState = true;
				// console.log(id)
				
				this.markers.map( item=>{
					if( item.id == id ){
						if( item.iconPath.indexOf('-check-') != -1 ){
							item.iconPath = item.iconPath.replace('-check-', '-');
							idState = false;
							item.zIndex = 1;
						} else {
							item.iconPath = item.iconPath.replace('-icon', '-check-icon');
							item.zIndex = 99;
						}
					} else if( item.id == this.activeId ){
						item.iconPath = item.iconPath.replace('-check-', '-');
						item.zIndex = 1;
					}
				})
				
				this.activeId = idState == true ? id : 0;
				
				if( this.showTitle || !idState ){
					this.showMapTitle();
				}
			},
			showMapTitle(){
				this.showTitle = !this.showTitle; 
			},
			goMore() {
				this.$emit('more')
			}
		},
	}
</script>

<style lang="scss" scoped>
	.map-wrap{
		position: relative;
		margin-top: 30rpx;
	}
	
	.map-cover{
		position: absolute;
		width: 750rpx;
		height: 358rpx;
		top: 90rpx;
		background-color: rgba(0,0,0,0);
	}
	
	.map-class{
		width: 750rpx;
		height: 358rpx;
		touch-action: auto;
	}
	
	map{
		    touch-action: auto;
	}

	.tip {
		background-color: #fff; 
		// border: 1px solid #ccc;
		border-radius: 10rpx;
		display: inline-flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		padding: 10rpx 24rpx;
		position: absolute;
		top: 30rpx;
		left: 50%;
		transform: translate(-50%,0);
		overflow: visible;
		box-shadow: 0 0 10px rgba(0,0,0,.2);
		
		&-title {
			font-size: 28rpx;
			color: rgba(71, 75, 78, 1);
			margin-bottom: 10rpx;
		}
		
		&-text {
			font-size: 22rpx;
			color: rgba(117, 117, 117, 1);
			max-width: 500rpx;
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;
		}
		
		&-arr {
			width: 0;
			height: 0;
			border: 10rpx solid;
			border-color: #fff transparent transparent;
			position: absolute;
			left: 50%;
			top: 100%;
			transform: translate(-50%,0);
		}
	}
	
	
	
	.list {
		width: 100%;
		padding: 20rpx 32rpx 0;
		box-sizing: border-box;
		
		&-wrap {
			width: 100%;
		}
		
		&-box {
			width: 100%;
			height: 120rpx;
			display: flex;
			border-bottom: 1px solid rgba(224, 224, 224, 1);
			
			&-left {
				width: 75%;
				display: flex;
				flex-direction: column;
				justify-content: center;
			}
			
			&-title {
				font-size: 30rpx;
				color: rgba(33, 33, 33, 1);
			}
			
			&-text {
				font-size: 24rpx;
				color: rgba(117, 117, 117, 1);
				margin-top: 10rpx;
				
			}
			
			&-right {
				flex: 1;
				font-size: 24rpx;
				color: rgba(117, 117, 117, 1);
				display: flex;
				justify-content: flex-end;
				align-items: center;
			}
		}
		
		&-more {
			width: 100%;
			height: 90rpx;
			display: flex;
			justify-content: center;
			align-items: center;
			
			span {
				font-size: 24rpx;
				color: rgba(117, 117, 117, 1);
			}
			
			.iconfont {
				margin-left: 8rpx;
				color: rgba(204, 204, 204, 1);
			}
		}
		
		&-none{
			width: 100%;
			height: 120rpx;
			font-size: 30rpx;
			color: rgba(153, 153, 153, 1);
			display: flex;
			align-items: center;
			border-bottom: 1px solid rgba(224, 224, 224, 1);
		}
	}
	
	.border-active{
		border-color: rgba(254, 130, 30, 1);
	}
</style>
