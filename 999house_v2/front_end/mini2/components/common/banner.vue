<template>
	<view>
		<div class="banner">
			<swiper class="my-swipe" :autoplay="5000" indicator-color="white" @change="change">
				<swiper-item v-for="(item,index) in list" :key="index" @click="goPage(item)">
					<image mode="widthFix" :src="$api.imgDirtoUrl(item.img)" class="banner-img">
				</swiper-item>
			</swiper>
			<!-- <u-swiper :list="list" mode="none" indicator-pos="bottomRight" >
				<image mode="widthFix" :src="$api.imgDirtoUrl(item.img)" class="banner-img">
			</u-swiper> -->
			<!-- <van-swipe class="my-swipe" :autoplay="5000" indicator-color="white" @change="change">
				<van-swipe-item v-for="(item,index) in list" :key="index" @click="goPage(item)">
					<image mode="widthFix" :src="$api.imgDirtoUrl(item.img)" class="banner-img">
				</van-swipe-item>
			</van-swipe> -->
		</div>
	</view>
</template>

<script>
	export default{
		data() {
			return {
				skip: true
			};
		},
		props: ['list'], 
		methods: {
			goPage (item){
				if( !this.skip ){
					this.skip = !this.skip;
					return;
				}
				
				if(!this.trim(item.href)&&item.info){
					item.href = 'houses/index.html?id='+item.info.estate_id+'&cover='+item.cover;
				}
		
				if(!item.href){
					return
				}
				this.goPage(item.href)
			},
			change() {
				this.skip = false;
			}
		},
	}
</script>

<style>
	.banner{
		height: 360rpx;
		/* height: 1.38rpx; */
		/* padding: 0 0.32rpx; */
		box-sizing: border-box;
		touch-action: pan-y;
		/* margin-bottom: 0.52rpx; */
	}
	.my-swipe{
		width: 100%;
	}
	.my-swipe swipe-item {
		/* height: 1.38rpx; */
		height: 360rpx;
		color: #fff;
		text-align: center;
	}
	
	.my-swipe .banner-img{
		width: 100%;
		height: 100%;
		object-fit: cover;
	}
	
	.my-swipe .van-swipe__indicators{
		/* left: auto;
		right: 0.18rpx; */
		bottom: 12rpx;
		transform: translate(-50%,0);
	}
	.my-swipe .van-swipe__indicator{
		width: 24rpx;
		height: 10rpx;
		background-color: #FFFFFF;
		opacity: 0.43;
		border-radius: 5px;
	}
	.my-swipe .van-swipe__indicator--active{
		width: 24rpx;
		opacity: 1;
	}
</style>
