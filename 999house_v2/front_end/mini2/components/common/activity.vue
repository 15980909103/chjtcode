<template>
	<view>
		<div class="activity">
						
			<div class="activity-vr" v-if="vrlist.length > 0">
				<div 
					class="activity-vr-item" 
					v-for="(item,index) in vrlist" 
					:key="index" 
					:style="{ background: item.bg }"
					@click="goPage(item)"
				>
					<div class="vr-left">
						<span class="vr-title">{{item.name}}</span>
						<span class="vr-text">{{item.text}}</span>
						<span class="vr-tip">{{item.tip}} ></span>
					</div>
					<img :src="item.img">
				</div>
			</div>
			<div class="activity-box" v-if="activitylist.length > 0">
				
				<swiper display-multiple-items="2">
					<swiper-item class="activity-item" v-for="(item,index) in activitylist" :key="index" @click="goPage(item)">
						<p class="activity-title">{{item.title}}</p>
						<span>{{item.sub_title}}</span>
						<img :src="$api.imgDirtoUrl(item.img)">
					</swiper-item>
					<swiper-item class="activity-item" v-for="(item,index) in activitylist" :key="index" @click="goPage(item)">
						<p class="activity-title">{{item.title}}</p>
						<span>{{item.sub_title}}</span>
						<img :src="$api.imgDirtoUrl(item.img)">
					</swiper-item>
				</swiper>
				<!-- <div class="swiper-container">
					<div class="swiper-wrapper">
						<div class="swiper-slide activity-item" v-for="(item,index) in activitylist" :key="index" @click="goPage(item)">
							<p class="activity-title">{{item.title}}</p>
							<span>{{item.sub_title}}</span>
							
							<img :src="$api.imgDirtoUrl(item.img)">
						</div>
					</div>
					<div class="swiper-pagination "></div>
				</div> -->
			</div>
		</div>
	</view>
</template>

<script>
	export default {
		props: {
			activitylist:{
				default(){
					return []
				}
			},
			vrlist:{
				default(){
					return []
				}
			},
		}, 
		data() {
			return {
				
			}
		},
		created() {
			// this.$nextTick(()=>{
			// 	this.mySwiper = new Swiper('.swiper-container', {
			// 		slidesPerView: 'auto',
			// 		loop: true,
			// 		pagination: {
			// 		  el: '.swiper-pagination',
			// 		  clickable: true,
			// 		},
			// 	})    
			// })
		},
		mounted() {
			console.log('activitylist',this.activitylist)
		},
		methods: {
			goPage: (item)=>{
				console.log(1)
				if(!$api.trim(item.href)&&item.info){
					item.href = 'houses/index?id='+item.info.estate_id+'&cover='+item.cover;
				}
				if(!item.href){
					return
				}
				$api.goPage(item.href)
			},
		}
	}
</script>

<style>
	.activity{
		font-size: 34rpx;
		font-weight: 600;
		color: #212121;
		margin-bottom: 20rpx;
	}
	
	.activity-title{
		margin-bottom: 10rpx;
		/* margin-left: 0.32rpx; */
	}
	
	.activity-vr{
		width: 100%;
		height: 152rpx;
		box-sizing: border-box;
		padding: 0 32rpx;
		margin: 34rpx 0 40rpx;
		display: flex;
		justify-content: space-between;
	}
	
	.activity-vr-item{
		height: 100%;
		width: 328rpx;
		box-shadow: 0 2rpx 16rpx 0 rgba(166, 206, 217, 0.52);
		border-radius: 6rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
		box-sizing: border-box;
		padding: 16rpx;
	}
	
	.activity-vr-item img{
		width: 110rpx;
		height: 110rpx;
	}
	
	.vr-left{
		height: 100%;
		display: flex;
		flex-direction: column;
		justify-content: space-between;
	}
	
	.vr-left span{
		text-align: justify;
		text-justify: newspaper;
		word-break: break-all;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 1;
		overflow: hidden;
	}
	
	.vr-title{
		font-size: 32rpx;
		font-weight: bold;
	}
	
	.vr-text{
		font-size: 22rpx;
		color: #ADADAD;
	}
	
	.vr-tip{
		font-size: 22rpx;
		color: #757575;
	}
	
	.activity-box{
		/* width: 100%; */
		/* height: 2.19rpx; */
		background-color: #FFFFFF;
		margin: 0 20rpx;
		padding: 20rpx 0 0;
		/* box-sizing: border-box; */
		/* padding: 0 0.2rpx; */
		/* display: flex;
		overflow: scroll hidden;
		overflow-scrolling:touch; */
	}
	.activity-box .van-swipe{
		padding-bottom: 64rpx;
	}
	
	.activity-item{
		box-sizing: border-box;
		padding: 0 30rpx;
		width: 50%;
	}
	
	.activity-item:not(:last-child){
		border-right: 1px solid rgba(240, 240, 240, 1);
	}
	
	.activity-box .swiper-pagination-bullet{
		width: 24rpx;
		height: 10rpx;
		background-color: #F0F0F0;
		border-radius: 5px;
		opacity: 1;
	}
	.activity-box  .swiper-pagination-bullet-active{
		background-color: #FE821E;
	}
	.activity-box img{
		display: block;
		width: 240rpx;
		height: 158rpx;
		/* height: 100%; */
		border-radius: 6rpx;
		margin-top: 22rpx;
		/* margin-left: 0.34rpx; */
	}
	.activity-item p{
		font-size: 30rpx;
		font-weight: 800;
		color: #212121;
		margin-bottom: 10rpx;
	}
	.activity-item span{
		display: inline-block;
		font-size: 24rpx;
		color: #666666;
		margin-bottom: 22rpx;
	}
	.swiper-container{
		padding-bottom: 1rpx;
		touch-action: auto;
		/* height: 3.48rpx; */
	}
	/* body{
		touch-action: none;
	} */
</style>
