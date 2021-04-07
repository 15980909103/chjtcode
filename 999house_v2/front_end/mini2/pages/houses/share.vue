<template>
	<view>
		<image :src="imgUrl" v-if="imgUrl" class="poster" :class="imgUrl ? 'van-slide-down-enter-active' : ''" show-menu-by-longpress>
		<l-painter :board="base" isRenderImage @success="success" style="position: absolute; top: -999px;"/>
		<u-popup
			v-model="load" 
			mode="center" 
			border-radius="6" 
			width="200"
			height ="200"
			:mask-close-able="false"
		>
			<view class="box">
				<u-loading mode="flower" size="60"></u-loading>
				<view>
					海报生成中~
				</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	// 插件文档地址 https://ext.dcloud.net.cn/plugin?id=2389
	import lPainter from '@/components/lime-painter/index';
	
	export default {
		data() {
			return {
				base: '',
				option: '',
				load: true,
				imgUrl: ''
			}
		},
		components: {
			lPainter
		},
		onLoad(option) {
			this.option = option;

			this.init();
		},
		methods: {
			init() {
				let that = this;
				this.$http.post('estates/shareEstatesInfo', {
					id: that.option.id
				}).then( res=>{
					
					const data = res.data;
				
					if(!data || Object.keys(data).length==0){
						this.$toast('无分享数据');
						that.load = false;
						return
					}
					// console.log(9999)
					// 背景
					const bg = this.$api.imgDirtoUrl('/houses/bg.png');
					// logo
					const logo = this.$api.imgDirtoUrl('/houses/logo.png');
					let page = getCurrentPages();
					page = getApp().globalData.host+'/9house'+page[page.length-1].$page.fullPath;
					
					const base = {
						width: '750rpx',
						height: '1206rpx',
						views: [
							{
								type: 'image',
								src: bg,
								css: {
									left: '0rpx',
									top: '0rpx',
									width: '100%',
									height: '100%'
								}
							},
							{
								type: 'image',
								src: logo,
								css: {
									left: '32rpx',
									top: '41rpx',
									width: '200rpx',
									height: '60rpx'
								}
							},
							{
								type: 'view',
								css: {
									left: '32rpx',
									top: '150rpx',
									background: '#fff',
									width: '686rpx',
									height: '754rpx',
									radius: '4rpx'
								}
							},
							{
								type: 'text',
								text: '长按图片保存至手机',
								css: {
									color: 'rgba(255, 255, 255, .5)',
									left: '267rpx',
									top: '1141rpx',
									fontSize: '24rpx',
								}
							},
							{
							    type: 'qrcode',
							    text: page,
							    css: {
							        left: '578rpx',
							        top: '952rpx',
							        width: '134rpx',
							        height: '134rpx',
							        color: '#000',
							        backgroundColor: '#fff',
							        border: '6rpx solid #fff',
							    }
							}
						]
					};
					
					const cover = that.$api.imgDirtoUrl(data.list_cover);
					const price = ( data.price && data.price != 0 ) ? data.price : '价格待定';
					const area = data.built_area ? data.built_area : '--';
					const type = data.house_type ? data.house_type : '--';
					let user = [];
					
					const el = [
						{
							type: 'image',
							src: cover,
							css: {
								left: '32rpx',
								top: '150rpx',
								width: '686rpx',
								height: '410rpx'
							}
						},
						{
							type: 'text',
							text: data.name,
							css: {
								color: '#000',
								left: '75rpx',
								top: '598rpx',
								fontSize: '48rpx',
								fontWeight: 'bold'
							}
						},
						{
							type: 'text',
							text: price,
							css: {
								width: '230rpx',
								color: 'rgba(252, 77, 57, 1)',
								left: '40rpx',
								top: '751rpx',
								fontSize: '44rpx',
								fontWeight: 'bold',
								textAlign: 'center'
							}
						},
						{
							type: 'text',
							text: '单价 (元/平)',
							css: {
								width: '230rpx',
								color: 'rgba(117, 117, 117, 1)',
								left: '40rpx',
								top: '814rpx',
								fontSize: '22rpx',
								textAlign: 'center'
							}
						},
						{
							type: 'text',
							text: area,
							css: {
								width: '230rpx',
								color: 'rgba(33, 33, 33, 1)',
								left: '270rpx',
								top: '751rpx',
								fontSize: '44rpx',
								fontWeight: 'bold',
								textAlign: 'center'
							}
						},
						{
							type: 'text',
							text: '面积',
							css: {
								width: '230rpx',
								color: 'rgba(117, 117, 117, 1)',
								left: '270rpx',
								top: '814rpx',
								fontSize: '22rpx',
								textAlign: 'center'
							}
						},
						{
							type: 'text',
							text: type,
							css: {
								width: '230rpx',
								color: 'rgba(33, 33, 33, 1)',
								left: '500rpx',
								top: '751rpx',
								fontSize: '44rpx',
								fontWeight: 'bold',
								textAlign: 'center'
							}
						},
						{
							type: 'text',
							text: '居室',
							css: {
								width: '230rpx',
								color: 'rgba(117, 117, 117, 1)',
								left: '500rpx',
								top: '814rpx',
								fontSize: '22rpx',
								textAlign: 'center'
							}
						},
					];
					
					if( data.discount.length > 0 ){
						let textLen = 0;
						
						data.discount.map( (item, index)=>{
							if( index > 1 ){
								return;
							};
							
							let coverNew = '';
							let tagEl = [];
							let left = 75;
							
							coverNew = item.type == 'discount' ? '/index/hot.png' : '/index/sale.png';
							coverNew = that.$api.imgDirtoUrl(coverNew);
							
							if( index == 0 ){
								textLen = item.title.length;
							} else {
								left = left + textLen*30 + 30;
							}
							
							tagEl = [
								{
									type: 'image',
									src: coverNew,
									css: {
										left: left + 'rpx',
										top: '686rpx',
										width: '30rpx',
										height: '30rpx'
									}
								},
								{
									type: 'text',
									text: item.title,
									css: {
										color: '#000',
										left: left + 40 + 'rpx',
										top: '686rpx',
										fontSize: '24rpx'
									}
								},
							];
							
							el.push(...tagEl);
						})
					}
					
					
					
					if( data.house_purpose_user ){
						user = [
							{
								type: 'image',
								src: that.$api.imgDirtoUrl(data.house_purpose_user.head_img),
								css: {
									left: '32rpx',
									top: '993rpx',
									width: '74rpx',
									height: '74rpx',
									radius: '50%'
								}
							},
							{
								type: 'text',
								text: data.house_purpose_user.name,
								css: {
									color: 'rgba(255, 255, 255, 1)',
									left: '120rpx',
									top: '986rpx',
									fontSize: '30rpx',
									fontWeight: 'bold'
								}
							},
							{
								type: 'text',
								text: '联系方式：' + data.house_purpose_user.phone,
								css: {
									color: 'rgba(255, 255, 255, .7)',
									left: '120rpx',
									top: '1036rpx',
									fontSize: '24rpx'
								}
							}
						];
						
						el.push(...user);
					}
					
					base.views.push(...el);
					
					this.base = base;
					
					console.log(base)
					// console.log(res)
					// console.log(res.data)
					
				}).catch(res=>{
					that.load = false;
					if(typeof(res)=='object'){
						that.$toast(res.msg);
					};
				});
			},
			success( e ){
				this.load = false;
				this.imgUrl = e;
			}
		}
	}

</script>

<style lang="scss" scoped>
	.poster{
		width: 750rpx;
		height: 1206rpx;
	}
	
	.box{
		width: 100%;
		height: 100%;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		
		view{
			margin-top: 20rpx;
			color: rgba(0,0,0,.6);
		}
	}
</style>
