<template>
	<view>
		<div>
			<div class="template-house" v-for="(house,key) in list" :key="key">
				<img :src="$api.imgDirtoUrl(house.img)" @click="$http.showImg(house.img)">
				<div class="template-house-box">
					<div class="template-house-title">
						<h4>{{ house.title }}</h4>
						<van-tag 
							type="primary" 
							color="rgba(246, 247, 248, 1)"
							text-color="rgba(90, 96, 102, 1)"
							v-for="(tip,num) in house.tip" 
							:key="num"
						>
							{{ tip }}
						</van-tag>
					</div>
					<div>
						建面{{ house.area }}m² 朝向{{ house.way }}
					</div>
					<div v-if="house.price" class="template-house-price">
						约{{ house.price }}万/套 
					</div>
					<div v-else-if="house.price_ave" class="template-house-price">
						{{ house.price_ave }}元/m²
					</div>
					<div v-else class="template-house-price">价格待定</div>
				</div>
			</div>
		</div>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				
			}
		},
		props: {
			list: {
				type: Array,
				default() {
					return []
				}
			}
		},
		methods: {
			
		}
	}
</script>

<style>
	.template-house{
		display: flex;
		font-size: 24rpx;
		color: rgba(90, 96, 102, 1);
		box-sizing: border-box;
		padding: 30rpx 0;
		border-bottom: 1rpx solid rgba(224, 224, 224, 1);
	}
	
	.template-house:last-child{
		border-bottom: none;
	}
	
	.template-house img{
		width: 190rpx;
		height: 138rpx;
		border: 1px solid #E6E6E6;
		margin-right: 30rpx;
	}
	
	.template-house-title{
		display: flex;
		align-items: center;
		margin-bottom: 10rpx;
	}
	
	.template-house-title h4{
		font-size: 30rpx;
		color: rgba(11, 15, 18, 1);
	}
	
	.template-house-title .van-tag{
		font-size: 22rpx;
		margin-left: 26rpx;
	}
	
	.template-house-box{
		display: flex;
		flex-direction: column;
		justify-content: space-between;
	}
	
	.template-house-price{
		font-size: 32rpx;
		color: rgba(252, 77, 57, 1);
		font-weight: bold;
		margin-top: 10rpx;
	}
</style>
