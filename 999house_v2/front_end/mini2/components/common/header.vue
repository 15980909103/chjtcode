<template>
	<view>
		<header class="header" :style="{ background: backClass }">
			<div class="header-box">
				<div class="header-options">
					<span class="header-location" @click="chooseLocation">{{location}}<span class="iconfont iconjiantou"></span></span>
					<div class="header-skill">
						<!--<span class="header-map" @click="goPage('map/index.html')"><span class="iconfont iconzhoubian"></span>地图</span>
					<span class="header-scan"><span class="iconfont iconsaoma"></span>扫一扫</span>	-->
					</div>
				</div>
				<div class="header-search" @click="goSearch">
					<span class="iconfont icon901"><span class="header-tip">搜索楼盘、资讯</span></span>
				</div>
			</div>
			<div class="map" :style="{ color: mapColor }" @click="goPage('map/index.html')"><i class="iconfont iconzhoubian"></i><span>地图</span></div>
		</header>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				scrollFlag:false,
				backcolor:false,
				backClass: '',
				mapColor: ''
			};
		},
		props: [
			'location',
			'isfixed'
		],
		watch: {
			isfixed(newV){
				if( newV ){
					this.backcolor = true;
					// this.backClass = 'rgb(245, 245, 245)';
					this.backClass = '#F8F8F8';
					this.mapColor = '#000';
				} else {
					this.backcolor = false;
					this.backClass = this.mapColor = '';
				}
			}
		},
		created() {
			this.$api.localStore.localDel('pre-page');
		},
		methods: {
			chooseLocation() {
				this.goPage('index/location');
			},
			goSearch() {
				console.log(window)
				if(window){
					this.$api.localStore.localSet('pre-page', window.location.href)
				}
				this.goPage('index/search');
			},
		}
	}
</script>

<style>
	.header{
		width: 100%;
		/* height: 1.5rpx; */
		display: flex;
		align-items: center;
		box-sizing: border-box;
		padding: 30rpx 30rpx 20rpx;
		position: absolute;
		top: 0;
		left: 0;
		z-index: 90;
	}
	
	.header .map{
		width: 100rpx;
		height: 80rpx;
		display: flex;
		justify-content: flex-end;
		align-items: center;
		font-weight: 600;
		font-size: 24rpx;
		color: #fff;
	}
	
	.abouse{
		position: fixed;
		background: #F8F8F8;
	}
	.header-box{
		flex: 1;
		/* background: rgba(243, 243, 243, 0.7); */
		background-color: #fff;
		/* margin: 0.3rpx 0.3rpx 0.2rpx; */
		/* padding: 0 0.32rpx; */
		border-radius: 34rpx;
		overflow: hidden;
		/* box-sizing: border-box; */
		font-size: 0;
		/* position: fixed;
		top: 0;
		z-index: 90; */
	}
	
	.header-options{
		width: 20%;
		font-size: 26rpx;
		color: rgba(117, 117, 117, 1);
		/* padding: 0.3rpx 0; */
		box-sizing: border-box;
		display: flex;
		justify-content: space-between;
		align-items: center;
		display: inline-block;
		vertical-align: middle;
		box-sizing: border-box;
		padding-left: 10rpx;
	}
	
	.header-location{
		width: 100%;
		overflow:hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
		-o-text-overflow:ellipsis;
		font-size: 30rpx;
		color: #212121;
		font-weight: 500;
		text-align: center;
		position: relative;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	.header-location::after{
		content: '';
		position: absolute;
		right: 0;
		top: 100px;
		width: 100px;
		height: 36rpx;
		background-color: #D8D8D8;
	}
	.iconjiantou{
		font-size: 26rpx;
		margin-left: 2rpx;
	}
	
	.header-scan{
		margin-left: 40rpx;
	}
	
	.iconzhoubian,
	.iconsaoma{
		/* font-weight: 600;
		font-size: 0.24rpx;
		margin-right: 0.12rpx; */
	}
	
	.header-search{
		
		width: 80%;
		height: 80rpx;
		font-size: 26rpx;
		color: rgba(173, 173, 173, 1);
		background: #FFFFFF;
		/* box-shadow: 0px 3px 20px 0px #F5F5F5; */
		border-radius: 6px;
		/* border: 1px solid #E0E0E0; */
		/* margin-left: .35rpx; */
		/* display: flex;
		align-items: center; */
		display: inline-block;
		vertical-align: middle;
		line-height: 80rpx;
		box-sizing: border-box;
		padding-left: 16rpx;
	}
	
	.icon901{
		font-size: 30rpx;
		display: flex;
		align-items: center;
		
	}
	
	.icon901:before{
		position: relative;
		top: 4rpx;
	}
	
	.header-tip{
		margin-left: 15rpx;
		font-size: 26rpx;
	}
</style>
