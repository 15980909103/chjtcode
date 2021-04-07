<template>
	<view class="content" v-if="Object.keys(data).length > 0">
		<text class="title">{{ data.title }}</text>
		<view class="text" v-html="data.contxt"></view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				data: {}
				
			}
		},
		components: {
		
		},
		onLoad(option) {
			this.init(option);
		},
		onShow() {
		
		},
		methods: {
			init(option) {
				this.$http.post( 'chat/getSystemMsgInfo', { id: option.id }).then( res=>{
					this.data = res.data;
				})
			}
		}
	}

</script>

<style lang="scss" scoped>
	.content{
		width: 100%;
		box-sizing: border-box;
		padding: 50rpx 32rpx;
		
		.title{
			font-size: 40rpx;
			font-weight: 600;
			color: #212121;
			line-height: 61rpx;
			
		}
		.text{
			width: 100%;
			font-size: 30rpx;
			color: #333333;
			margin-top: 44rpx;
			
			img,
			image{
				width: 100%;
			}
		}
	}
</style>
