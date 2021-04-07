<template>
	<view class="content">
		<web-view :webview-styles="webviewStyles" :src='h5Host+"/houses/hot_news.html?"+t_version+"&id="+id+"&typeId="+typeId'></web-view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				webviewStyles: 'false',//禁用进度条
				id: 0,
				typeId: 0,
			}
		},
		onLoad(options) {
			this.id = options.id;
			this.typeId = options.typeId;
		},
		methods: {

		}
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
