<template>
	<view class="content">
		<img src="../../static/broker.png" class="bg">
		
		<view class="box">
			<view class="card">
				<view class="info">
					<img :src="$api.imgDirtoUrl('/my/logo.png')">
					<view class="info-item">
						<view class="title">
							<text>陈佳佳</text>
							<i class="iconfont iconerweima1"></i>
						</view>
						<view class="describe">
							厦门九房网 | 湖里万达店
						</view>
					</view>
				</view>
				<view class="tab">
					<view class="tab-box">
						<view class="tab-num">
							14
						</view>
						<view class="tab-text">
							精选房源
						</view>
					</view>
					<view class="tab-box">
						<view class="tab-num">
							36
						</view>
						<view class="tab-text">
							服务人数
						</view>
					</view>
					<view class="tab-box">
						<view class="tab-num">
							2
						</view>
						<view class="tab-text">
							新增消息
						</view>
					</view>
				</view>
			</view>
			
			<text class="house-title">TA的楼盘</text>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				
				
			}
		},
		components: {
		
		},
		onLoad(option) {
			
		},
		onShow() {
		
		},
		methods: {
			
		}
	}

</script>

<style lang="scss" scoped>
	.content{
		width: 100%;
	}
	
	.bg{
		width: 100%;
		height: 264rpx;
		position: absolute;
		z-index: -1;
	}
	
	.box{
		width: 100%;
		box-sizing: border-box;
		padding: 74rpx 34rpx 0;
	}
	
	.card{
		width: 100%;
		height: 318rpx;
		background-color: #fff;
		border-radius: 8rpx;
		box-shadow: 0 0 10rpx rgba(0,0,0,.2);
		margin-bottom: 50rpx;
	}
	
	.info{
		display: flex;
		box-sizing: border-box;
		padding: 40rpx;
		
		img{
			width: 94rpx;
			height: 94rpx;
			border-radius: 50%;
			margin-right: 28rpx;
		}
		
		&-item{
			flex: 1;
		}
		
		.title{
			width: 100%;
			font-size: 38rpx;
			font-weight: 800;
			color: rgba(33, 33, 33, 1);
			display: flex;
			align-items: center;
			justify-content: space-between;
			
			.iconfont{
				font-size: 50rpx;
				font-weight: 500;
			}
		}
		
		.describe{
			font-size: 24rpx;
			color: rgba(117, 117, 117, 1);
			margin-top: 8rpx;
		}
	}
	
	.tab{
		flex: 1;
		display: flex;
		justify-content: space-between;
		
		&-box{
			flex: 1;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
		}
		
		&-num{
			font-size: 50rpx;
			color: rgba(254, 130, 30, 1);
			margin-bottom: 14rpx;
		}
		
		&-text{
			font-size: 24rpx;
			color: rgba(173, 173, 173, 1);
		}
	}
	
	.house-title{
		font-size: 30rpx;
		font-weight: bold;
	}
</style>
