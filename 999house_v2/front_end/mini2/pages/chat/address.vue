<template>
	<view class="content">
		<view class="top">
			<view class="search">
				<u-search
					v-model="val"
					placeholder="搜索联系人" 
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
			</view>
			<i class="iconfont iconsaoma" @click="qrCode"></i>
		</view>
		<view class="top-item">
			<view class="list-cell" @click="myQRcode">
				<view class="item">
					<view class="info">
						<img src="../../static/addF.png"><text>我的二维码</text>
					</view>
				</view>
				<i class="iconfont iconjiantou1-copy"></i>
			</view>
		</view>
		<template v-for="(item, index) in indexList">
			<view :key="index" v-if="!showSearch || (showSearch && item.friend_name.indexOf(val) != -1)">
				<view class="title" >{{ item.py_group }}</view>
				<view class="list-cell" @click="goPage('chat/chat', { id: item.dialogue_id, name: item.friend_name, to_uid: item.friend_user_id })">
					<view class="item">
						<view class="info">
							<img :src="$api.imgDirtoUrl(item.friend_head)">
							<span v-if="!showSearch">{{ item.friend_name }}</span>
							<span v-html="showResult(item.friend_name)" v-else></span>
						</view>
					</view>
				</view>
			</view>
		</template>
		
		<l-painter :board="base" isRenderImage @success="success" style="position: absolute; top: -999px;"/>
		
		<u-mask :show="load" @click="load = false">
			<!-- <view class="box">
				<u-loading mode="flower" size="60"></u-loading>
				<view>
					海报生成中~
				</view>
			</view> -->
			<image :src="userQRcode" class="poster" v-if="userQRcode" :class="userQRcode ? 'van-slide-down-enter-active' : ''" show-menu-by-longpress @click.stop>
			<image src="../../static/close.png" class="close" v-if="userQRcode" :class="userQRcode ? 'van-slide-down-enter-active' : ''" @click.stop="load = false">
			
		</u-mask>
	</view>
</template>

<script>
	import lPainter from '@/components/lime-painter/index';
	
	export default {
		data() {
			return {
				scrollTop: 0,
				val: '',
				showSearch: false,
				indexList: [],
				
				base: '',
				userQRcode: '',
				load: false
			}
		},
		components: {
			lPainter
		},
		watch: {
			val( newV ){
				const val = this.$u.trim(newV);
				
				this.showSearch = val ? true : false;
			}
		},
		computed: {
			haveVal() {
				const val = this.$u.trim(this.val);
				
				return val == '' ? false : true;
			}
		},
		onLoad(option) {
			this.getList();

			// 存在参数 添加好友二维码
			if(option.scene){
				let code = decodeURIComponent(option.scene);
			 	code = code.slice(code.indexOf('code=')+5);
				this.addFriend( code );
			}
			
			uni.$on('refFriends', this.getList);
		},
		onShow() {
			
		},
		methods: {
			getList() {
				this.$http.post( 'chat/getFriendList').then( res=>{
					this.indexList = res.data.list;
				})
			},
			qrCode() {
				uni.scanCode({
					success: (res)=>{
						let code = res.path.slice(res.path.indexOf('scene=')+6);
						code = decodeURIComponent(code);
						code = code.slice(code.indexOf('code=')+5);
						
						this.addFriend( code );
					}
				})
			},
			addFriend(code) {
				this.$http.post( 'chat/addFriend', { code: code }).then( res=>{
					if( res.code == 1 ){
						this.getList();
						this.$toast('添加成功');
					} else {
						this.$toast(res.msg)
					}
				})
			},
			showResult(name) {
				let item = '';
				
				for( let i in name ){
					if( this.val.indexOf(name[i]) != -1 ){
						console.log(name[i])
						item += `<span class="text-active">${ name[i] }</span>`;
					} else {
						item += `<span>${ name[i] }</span>`;
					}
				}
			
				return item;
			},
			myQRcode() {
				this.load = true;
				
				if( this.userQRcode ) return;
				
				this.$http.post( 'chat/getChatUserInfo').then( res=>{
					console.log(res)
					const QRcode = this.$api.imgDirtoUrl(res.data.code);
					
					this.base = {
						width: '500rpx',
						height: '660rpx',
						views: [
							{
								type: 'view',
								css: {
									left: '0',
									top: '0',
									background: '#fff',
									width: '500rpx',
									height: '660rpx',
									radius: '8rpx'
								}
							},
							{
								type: 'text',
								text: '联系人小程序码',
								css: {
									color: 'rgba(33, 33, 33, 1)',
									left: '0',
									top: '38rpx',
									fontSize: '34rpx',
									textAlign: 'center',
								}
							},
							{
								type: 'image',
								src: QRcode,
								css: {
									left: '50rpx',
									top: '114rpx',
									width: '400rpx',
									height: '400rpx'
								}
							},
							{
								type: 'text',
								text: '姓名：'+res.data.info.nickname,
								css: {
									color: 'rgba(117, 117, 117, 1)',
									left: '50rpx',
									top: '546rpx',
									fontSize: '26rpx',
								}
							},
							{
								type: 'text',
								text: '电话：'+res.data.info.phone,
								css: {
									color: 'rgba(117, 117, 117, 1)',
									left: '50rpx',
									top: '590rpx',
									fontSize: '26rpx',
								}
							},
						]
					}
				})
			},
			success( e ){
				this.userQRcode = e;
			}
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
		}
	}

</script>

<style lang="scss" scoped>
	
	.poster{
		width: 500rpx;
		height: 600rpx;
		overflow: hidden;
		position: absolute;
		top: 200rpx;
		left: 125rpx;
		border-radius: 8rpx;
	}
	
	.close{
		width: 54rpx;
		height: 54rpx;
		position: absolute;
		left: 348rpx;
		top: 894rpx;
	}
	
	.content{
		width: 100%;
		
		/deep/.u-index-anchor{
			padding: 14rpx 34rpx;
		}
		
		/deep/.u-index-anchor--active{
			background-color: whitesmoke;
		}
	}
	
	.title{
		width: 100%;
		height: 58rpx;
		background-color: rgba(247, 247, 247, 1);
		box-sizing: border-box;
		padding: 0 32rpx;
		display: flex;
		align-items: center;
		font-size: 30rpx;
		color: #212121;
		margin-bottom: 6rpx;
	}
	
	.top{
		width: 100%;
		display: flex;
		align-items: center;
		box-sizing: border-box;
		padding: 34rpx 0 34rpx 32rpx;
		
		.search{
			flex: 1;
		}
		
		.iconfont{
			padding: 0 32rpx;
			font-size: 44rpx;
		}
	}
	
	.list-cell {
		display: flex;
		box-sizing: border-box;
		width: 100%;
		padding: 24rpx 34rpx;
		overflow: hidden;
		color: #323233;
		font-size: 14px;
		line-height: 24px;
		background-color: #fff;
		align-items: center;
		justify-content: space-between;
		
		.info{
			display: flex;
			align-items: center;
			
			img{
				width: 74rpx;
				height: 74rpx;
				border-radius: 50%;
				margin-right: 30rpx;
			}
			
			span{
				font-size: 30rpx;
				color: #212121;
			}
		}
		
	}
</style>
