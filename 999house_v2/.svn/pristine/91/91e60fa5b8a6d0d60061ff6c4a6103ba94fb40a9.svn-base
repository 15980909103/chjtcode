<template>
	<view class="content">
		<img :src="$api.imgDirtoUrl('/my/logo.png')" class="ad">
		<u-search
			v-model="searchVal"
			placeholder="搜索群名称" 
			shape="square"
			bg-color="rgba(245, 248, 254, 1)"
			search-icon-color="rgba(173, 173, 173, 1)"
			placeholder-color="rgba(173, 173, 173, 1)"
			color="#606266"
			:show-action="false"
			height="74"
			:input-style="{
				fontSize: '26rpx'
			}"
		>
		</u-search>
		<view class="group">
			<view class="item" v-for="item in 6" :key="item">
				<view class="item-box">
					<view class="item-info van-ellipsis">
						<img :src="$api.imgDirtoUrl('/my/logo.png')">
						<view class="item-title van-ellipsis">
							<text>思明区置业找房群</text>
							<text class="van-ellipsis">思明买房，专家咨询，专属顾思明买房，专家咨询，专属顾</text>
						</view>
					</view>
					<view class="item-btn">
						<u-button 
							:plain="true"
							hover-class="none"
							:custom-style="{
								width: '140rpx',
								height: '50rpx',
								fontSize: '26rpx',
								borderRadius: '2rpx',
								border: '1px solid rgba(254, 130, 30, 1)',
								color: 'rgba(254, 130, 30, 1)'
							}"
						>
							立即加入
						</u-button>
					</view>
				</view>
				<view class="NNT">
					<template v-for="item in 6">
						<img
							:src="$api.imgDirtoUrl('/my/logo.png')"
							v-if="item<4"
							:key="item"
							:style="{
								zIndex: item,
								transform: `translate(${60*item}%,0)`
							}"
						>
						
					</template>
					<span>128人正在热聊</span>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				searchVal: ''
				
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
		box-sizing: border-box;
		padding: 20rpx 32rpx 0;
	}
	
	.ad{
		width: 100%;
		height: 160rpx;
		margin-bottom: 36rpx;
	}
	
	.group{
		margin-top: 40rpx;
	}
	
	.item{
		display: flex;
		flex-direction: column;
		padding: 30rpx 0;
		border-bottom: 1px solid rgba(230, 230, 230, 1);
		
		&-box{
			display: flex;
			align-items: center;
			justify-content: space-between;
		}
		
		&-info{
			flex: 1;
			display: flex;
			align-items: center;
			
			img{
				width: 92rpx;
				height: 92rpx;
				border-radius: 50%;
				margin-right: 30rpx;
			}
		}
		
		&-title{
			flex: 1;
			font-size: 26rpx;
			color: rgba(117, 117, 117, 1);
			display: flex;
			flex-direction: column;
			
			text:first-child{
				font-size: 30rpx;
				font-weight: 800;
				color: rgba(33, 33, 33, 1);
			}
			
			text:nth-child(2){
				margin-top: 12rpx;
			}
		}
		
		&-btn{
			margin-left: 60rpx;
		}
	}
	
	.NNT{
		height: 40rpx;
		margin-top: 14rpx;
		box-sizing: border-box;
		padding-left: 122rpx;
		position: relative;
		
		img{
			width: 40rpx;
			height: 40rpx;
			border-radius: 50%;
			box-sizing: border-box;
			border: 2rpx solid #FFFFFF;
			position: absolute;
		}
		
		span{
			font-size: 26rpx;
			color: rgba(117, 117, 117, 1);
			position: absolute;
			left: 250rpx;
			top: 50%;
			transform: translate(0,-50%);
		}
	}
</style>
