<template>
	<view class="content">
		<view class="wrap">
			<view class="top" :style="[menuTop ? { paddingTop: menuTop.top + 'px' } : '']">
				<u-tabs 
					:list="list" 
					:is-scroll="false" 
					font-size="34"
					:current="current" 
					active-color="rgba(254, 130, 30, 1)"
					bar-trans="-60%"
					bar-width="30"
					:is-dot="true"
					:offset="[15,35]"
					@change="tabChange"
				>
				</u-tabs>
			</view>
			<view class="search">
				<view class="search-box">
					<u-search
						v-if="current == 0"
						placeholder="搜索联系人" 
						shape="square"
						bg-color="rgba(245, 248, 254, 1)"
						search-icon-color="rgba(173, 173, 173, 1)"
						placeholder-color="rgba(173, 173, 173, 1)"
						color="#606266"
						:disabled="true"
						:show-action="false"
						height="74"
						:input-style="{
							fontSize: '26rpx'
						}"
						@click="()=>{ searchShow = true }"
					>
					</u-search>
					<view class="search-box-item" v-else>
						<text>关注公众号，不错过任何聊天记录</text>
						<text @click="sub = true">去绑定</text>
					</view>
				</view>
				<i class="iconfont iconlianxiren" @click.stop="goPage('chat/address')"></i>
			</view>
		</view>
		
		<view class="box" v-if="current == 0">
			<!-- <view class="group">
				<view class="group-top">
					<view class="group-top-left">
						<i class="iconfont iconlianxiren-3"></i>
						<text>群聊广场</text>
					</view>
					<view class="group-top-right">
						<img 
							src="../../static/logo.png" 
							v-for="item in 6" 
							:key="item"
							:style="{
								zIndex: item,
								transform: `translate(-${60*(5-item)}%,0)`
							}"
						>
					</view>
				</view>
				<view class="group-bottom">
					<view class="group-bottom-left">
						<text>海沧区置业找房群</text>
						<text>124人正在热聊</text>
					</view>
					<i class="iconfont iconjiantou1-copy"></i>
				</view>
			</view> -->
			<template v-if="data.length > 0">
				<view class="chat" v-for="(item,index) in data" :key="index" @tap="goChat(item.id, item.nickname, item.user_id)">
					<view class="chat-head">
						<img :src="$api.imgDirtoUrl(item.headimgurl)">
						<u-badge :count="item.not_read" :offset="[-5,-5]"></u-badge>
					</view>
					<view class="chat-info van-ellipsis">
						<view class="chat-info-top">
							<text>{{ item.nickname }}</text>
							<text>{{ $u.timeFormat(item.update_time, 'mm-dd') }}</text>
						</view>
						<view class="chat-info-say van-ellipsis">
							{{ !item.msg_type || item.msg_type == 1 ? item.last_msg : '[图片]' }}
						</view>
					</view>
				</view>
			</template>
		</view>
		<view v-else>
			<!-- <view class="info">
				<view class="info-head">
					<img src="../../static/system.png">
				</view>
				<view class="info-info van-ellipsis">
					<view class="info-info-top">
						<text>系统消息</text>
					</view>
					<view class="info-info-say van-ellipsis">
						2020.12.18日版本1.2.3更新提示
					</view>
				</view>
			</view>
			<view class="info">
				<view class="info-head">
					<img src="../../static/house.png">
				</view>
				<view class="info-info van-ellipsis">
					<view class="info-info-top">
						<text>楼盘消息</text>
					</view>
					<view class="info-info-say van-ellipsis">
						恭喜您！您的房子升值17万
					</view>
				</view>
			</view>
			<view class="info">
				<view class="info-head">
					<img src="../../static/activity.png">
				</view>
				<view class="info-info van-ellipsis">
					<view class="info-info-top">
						<text>活动消息</text>
					</view>
					<view class="info-info-say van-ellipsis">
						今日特惠活动来袭，赶快来领取啦！
					</view>
				</view>
			</view> -->
			<template v-if="Object.keys(informList).length > 0">
				<view class="info" v-for="( item, index ) in informList" :key="index" @click="goPage('chat/list',{ type: index, name: item.name })">
					<view class="info-head">
						<template v-if="index == 1">
							<img src="../../static/system.png">
							<u-badge :count="item.not_read_num" :offset="[-5,-5]"></u-badge>
						</template>
						<template v-else-if="index == 4">
							<img src="../../static/house.png">
							<u-badge :count="item.not_read_num" :offset="[-5,-5]"></u-badge>
						</template>
					</view>
					<view class="info-info van-ellipsis">
						<view class="info-info-top">
							<text>{{ item.name }}</text>
						</view>
						<view class="info-info-say van-ellipsis">
							{{ item.lastMsg.title }}
						</view>
					</view>
				</view>
			</template>
			<view class="info" @click="sub = true">
				<view class="info-head">
					<img src="../../static/add.png">
				</view>
				<view class="info-info van-ellipsis">
					<view class="info-info-top">
						<text>订阅号</text>
					</view>
					<view class="info-info-say van-ellipsis">
						每日推送人居楼市、新闻资讯、市场动态
					</view>
				</view>
			</view>
		</view>
		
		<!-- 搜索弹窗 -->
		<u-popup 
			v-model="searchShow" 
			mode="bottom" 
			height="100%" 
			:closeable="true" 
			close-icon-pos="top-left"
			close-icon-size="40"
			:close-top="menuTop ? menuTop.top + 10 + 'px' : ''"
			close-icon="arrow-down"
			@close="searchClose"
		>
			<search :data="searchList" @close="()=>{ searchShow = false }" ref="search"></search>
	
			
		</u-popup>
		
		<!-- 公众号 -->
		<u-mask :show="sub">
			<view class="poster" @click.stop>
				<text class="title">
					厦门九房网
				</text>
				<view class="QRcode">
					<image :src="$api.imgDirtoUrl(QRcode)" show-menu-by-longpress @click.stop>
				</view>
				<text class="tip">
					长按识别二维码，添加订阅号
				</text>
			</view>
			
			<image src="../../static/close.png" class="close" @click.stop="sub = false">
			
		</u-mask>
	</view>
</template>

<script>
	import search from '@/components/chat/search'
	
	export default {
		data() {
			return {
				firstLoad: true,
				// 小程序按钮位置
				menuTop: 0,
				// tab栏目
				list: [
					{
						name: '微聊',
						count: 0
					}, {
						name: '通知',
						count: 0
					}
				],
				current: 0,
				// 搜索
				searchShow: false,
				searchList: [],			//	搜索联系人数据
				
				// 会话列表数据
				data: [],
				
				// 订阅号二维码
				QRcode: '',
				informList: {},
				
				// 是否已监听
				isListen: false,
				
				// 二维码窗口
				sub: false
			}
		},
		components: {
			search
		},
		watch: {
			searchShow( newV ) {
				if( !newV ) return;
				
				this.$http.post( 'chat/getFriendList').then( res=>{
					this.searchList = res.data.list;
				})
			},
			sub( newV ) {
				if( newV ){
					uni.hideTabBar({
						animation: true
					})
				} else {
					uni.showTabBar({
						animation: true
					})
				}
			}
		},
		onLoad(option) {
			this.init();
			uni.$once('loginOk', this.init);
		},
		onShow() {
			if( this.firstLoad ){
				this.firstLoad = false;
				return;
			}
			
			this.init();
			// this.initTabCount();
		},
		methods: {
			init() {
				this.menuTop = getApp().globalData.menuInfo;
				
				
				
				this.getList();
			},
			tabChange(e) {
				this.current = e;
			},
			getList() {
				// console.log(getApp().globalData.userInfo)
				
				this.$http.post( 'chat/getDialogueList' ).then( res=>{
					let data = res.data.dialoguel;
					console.table(data)
					console.log(res)
					
					this.data = data;
					this.QRcode = res.data.gzh_qrcode;
					this.informList = res.data.system_list;
					
					this.initTabCount();
					this.listenNewTalk();
				})
			},
			listenNewTalk() {
				if( this.isListen ) return;
				this.isListen = true;
				
				uni.$on('some_talk', (e)=>{
					this.data.map( item=>{
						if( item.id == e.id ){
							item.last_msg = e.type == 1 ? e.val : '[图片]';
							
							const page = getCurrentPages();
							
							if( page[page.length - 1].route == 'pages/chat/chat' && page[page.length - 1].options.id == e.id){
								item.not_read = 0;
							} else {
								item.not_read += 1;
								this.initTabCount();
							}

							// console.log(e,'------some_talk')
						} 
					})
				})
				
				uni.$on('system_msg', (e)=>{
					this.informList[e.type].not_read_num = Number(this.informList[e.type].not_read_num) + 1;
					this.informList[e.type].lastMsg.title = e.title;
					this.initTabCount();
				})
			},
			goChat( id, name, to_uid ) {
				this.goPage('chat/chat', { id: id, name: name, to_uid: to_uid });
				
				// 消除未读
				setTimeout(()=>{
					this.data.map( item=>{
						if( item.id == id ){
							item.not_read = 0;
						}
					})
					
					this.initTabCount();
				}, 500);
			},
			initTabCount() {
				let index = 0;
				
				this.data.map( item=>{
					index += item.not_read;
				})
				
				this.list[0].count = index == 0 ? 0 : 1;
	
				if( Object.keys(this.informList).length > 0 ){
					let num = 0;
					
					for( let i in this.informList ){
						index += Number(this.informList[i].not_read_num);
						num += Number(this.informList[i].not_read_num);
					}
					
					this.list[1].count = num == 0 ? 0 : 1;
				}
				
				
				// console.log(index,'----666')
				if( index == 0 ){
					uni.removeTabBarBadge({
						 index: 2
					})
				} else {
					uni.setTabBarBadge({
					  index: 2,
					  text: String(index)
					})
				}
				
			},
			searchClose() {
				this.$refs.search.cancel();
			},
			
		}
	}

</script>

<style lang="scss" scoped>
	.poster{
		width: 500rpx;
		height: 565rpx;
		overflow: hidden;
		position: absolute;
		background-color: #fff;
		top: 300rpx;
		left: 125rpx;
		border-radius: 8rpx;
		
		.title{
			font-size: 34rpx;
			color: rgba(33, 33, 33, 1);
			position: absolute;
			left: 50%;
			top: 38rpx;
			transform: translate(-50%,0);
		}
		
		.QRcode{
			width: 360rpx;
			height: 360rpx;
			border: 1px solid #E0E0E0;
			position: absolute;
			left: 50%;
			top: 114rpx;
			transform: translate(-50%,0);
			
			image{
				width: 100%;
				height: 100%;
			}
		}
		
		.tip{
			width: 100%;
			font-size: 26rpx;
			color: rgba(153, 153, 153, 1);
			position: absolute;
			bottom: 30rpx;
			text-align: center;
		}
	}
	
	.close{
		width: 54rpx;
		height: 54rpx;
		position: absolute;
		left: 348rpx;
		top: 950rpx;
	}
	
	.content{
		width: 100%;
		box-sizing: border-box;
		padding: 0 32rpx;
		
	}
	
	.wrap{
		background-color: #fff;
		position: sticky;
		top: 0;
		z-index: 99;
		padding-bottom: 28rpx;
	}
	
	.top{
		width: 100%;
		padding: 0 170rpx;
		background-color: #fff;
	}

	.search{
		display: flex;
		margin: 16rpx 0 0 0;
		
		&-box{
			flex: 1;
			height: 74rpx;
		}
		
		&-box-item{
			font-size: 26rpx;
			height: 74rpx;
			background-color: rgba(245, 248, 254, 1);
			box-sizing: border-box;
			padding: 0 36rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;
			
			text:first-child{
				color: rgba(117, 117, 117, 1);
			}
			
			text:last-child{
				color: rgba(254, 130, 30, 1);
			}
		}
		
		.iconfont{
			display: flex;
			align-items: center;
			padding-left: 32rpx;
			font-weight: bold;
		}
	}
	
	.group{
		width: 100%;
		height: 145rpx;
		background-color: #fff;
		box-sizing: border-box;
		padding: 14rpx 18rpx;
		border: 10rpx solid rgba(245, 248, 254, 1);
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		margin-bottom: 20rpx;
		
		&-top,
		&-bottom{
			display: flex;
			justify-content: space-between;
		}
		
		&-top{
			&-left{
				display: flex;
				align-items: center;
				font-size: 30rpx;
				font-weight: bold;
				
				.iconfont{
					font-size: 28rpx;
					margin-right: 16rpx;
				}
			}
			
			&-right{
				display: flex;
				justify-content: flex-end;
				position: relative;
				
				img{
					width: 40rpx;
					height: 40rpx;
					border-radius: 50%;
					box-sizing: border-box;
					border: 2rpx solid #FFFFFF;
					position: absolute;
				}
			}
		}
		
		&-bottom{
			align-items: center;
			
			&-left{
				font-size: 26rpx;
				
				text:first-child{
					font-weight: 800;
				}
				
				text:last-child{
					color: rgba(117, 117, 117, 1);
					margin-left: 24rpx;
				}
			}
			
			.iconfont{
				font-size: 36rpx;
				color: rgba(173, 173, 173, 1);
			}
		}
	}
	
	.chat,
	.info{
		width: 100%;
		display: flex;
		align-items: center;
		padding: 34rpx 0;
		border-bottom: 1px solid rgba(230, 230, 230, 1);
		
		&-head{
			display: flex;
			align-items: center;
			margin-right: 30rpx;
			position: relative;
			
			img{
				width: 92rpx;
				height: 92rpx;
				border-radius: 50%;
			}
		}
		
		&-info{
			flex: 1;
			
			&-top{
				width: 100%;
				display: flex;
				justify-content: space-between;
				
				text:first-child{
					font-size: 30rpx;
					font-weight: 800;
				}
				
				text:nth-child(2){
					font-size: 24rpx;
					color: rgba(173, 173, 173, 1);
				}
			}
			
			&-say{
				font-size: 26rpx;
				color: rgba(117, 117, 117, 1);
				margin-top: 14rpx;
			}
		}
	}
	
	.info{
		img{
			width: 100rpx;
			height: 100rpx;
		}
		
		&-info{
			display: flex;
			flex-direction: column;
			justify-content: center;
			
			&-say{
				margin-top: 16rpx;
			}
		}
	}
</style>
