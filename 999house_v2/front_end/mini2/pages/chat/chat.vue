<template>
	<view class="content" @click="closeMore">
		
		<view class="bar" id="bar" :style="{ transform: `translateY(${ moreShow ? '0' : '196rpx' })` }" @click.stop>
			<view class="action">
				<view class="input">
					<textarea
						class="u-input__input u-input__textarea"
						:value="val"
						:style="{
							width: '100%',
							background: '#fff',
							fontSize: '30rpx',
							boxSizing: 'border-box',
							padding: '0 16rpx',
							margin: '10rpx 0'
						}"
						placeholder=" "
						:fixed="true"
						:focus="myfocus"
						:hold-keyboard="true"
						:autoHeight="true"
						:show-confirm-bar="false"
						cursor-spacing="22"
						:adjust-position="adjust"
						@input="(e)=>{ val = e.detail.value }"
						@focus="onFocus"
					/>
				<!-- 	
					@focus="onFocus"
					@confirm="onConfirm" -->
				</view>
				<view class="more">
					<i 
						class="iconfont iconjia" 
						:style="[ 
							!haveVal ? { width: '82rpx', padding: '0 14rpx'} : { width: 0, padding: 0}
						]" 
						@click="showMore"
					>
					</i>
					<view class="send" :style="{ width: haveVal ? '140rpx' : 0 }">
						<u-button type="warning" @click.stop="send(0)">发送</u-button>
					</view>
				</view>
			</view>
			<view class="choose">
				<view class="choose-box">
					<view class="choose-item" @tap="choosePic(1)">
						<i class="iconfont icontupian"></i>
					</view>
					<text>照片</text>
				</view>
				<view class="choose-box">
					<view class="choose-item" @tap="choosePic(2)">
						<i class="iconfont iconpaizhao"></i>
					</view>
					<text>拍照</text>
				</view> 
			</view>
		</view>
		<view 
			class="wrap" 
			:style="[ 
				 { transform: `translateY(-${wrapTransform}rpx) rotateX(180deg)` },
				 { opacity: pageShow ? 1 : 0 },
				 { paddingTop: wrapPadding + 'rpx' }
			]"
		>
			<template v-for="(item,index) in list">
				<view class="item" :class="item.send_user_id != to_uid ? 'item-right' : ''" :key="index">
					<view class="item-head">
						<u-image
							width="60rpx" 
							height="60rpx" 
							shape="circle"
							mode="heightFix" 
							border-radius="12"
							duration="300"
							bg-color="red"
							show-menu-by-longpress
							:src="$api.imgDirtoUrl(item.send_head)"
							@click.stop="lookPic(item.send_head)"
						>
						</u-image>
					</view>
					<view class="item-wrap">
						<template v-if="item.msg_type == 1">
							<view class="item-content">
								{{ item.msg }}
							</view>
						</template>
						<template v-else>
							<view class="item-img">
								<u-image 
									width="100%" 
									height="300rpx" 
									mode="heightFix" 
									border-radius="12"
									show-menu-by-longpress
									duration="300"
									:src="$api.imgDirtoUrl(item.msg_url)"
									@click.stop="lookPic(item.msg_url)"
								>
								</u-image>
								{{ $api.imgDirtoUrl(item.msg_url) }}
							</view>
						</template>
					</view>
				</view>
			</template>
			<u-loadmore class="loading" status="loading"  v-if="loadShow"/>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				id: -1,				//	会话id
				to_uid: -1,
				otherName: '',		//	对方名称
				
				screenHeight: 0,	//	可视高度
				pageShow: false,	//	页面显示 防止闪动
				loadShow: false,
				
				val: '',			//	输入值
				moreShow: false,	//	显示照相相册
				wrapTransform: 0,	//	显示照相相册时偏移值
				wrapPadding: 100,
				myfocus: false,		//	获取焦点
				
				screenMove: false,
				
				// 上推页面
				adjust: true,
				
				// 数据
				list: [],
				
				page: 0,
				maxPage: 1,
			}
		},
		components: {
			
		},
		watch: {
			list( newV ){
				
			},
		},
		computed: {
			haveVal() {
				let val = this.$u.trim(this.val);
				
				val = val == '' ? false : true;
				
			
				this.pageShow && this.$u.getRect('.action').then(res => {
					let val = res.height * 2;
					
					if( this.wrapPadding == val ) return;
					
					this.wrapPadding = val;
					
					if( this.wrapPadding > val ) return;

					setTimeout(()=>{
						this.scrollNewText();
					},50)
				})
				
				return val;
			}
		},
		onLoad(option) {
			this.init(option);
		},
		onShow() {
			
		},
		methods: {
			init(option) {
				this.id = option.id;
				this.otherName = option.name;
				this.to_uid = option.to_uid;

				uni.setNavigationBarTitle({
				    title: option.name
				});
				
				uni.getSystemInfo({
				    success: (res)=>{
						if( !this.screenHeight ){
							this.screenHeight = res.windowHeight;
						}
				    }
				});
				
				this.getData();
			},
			// 获取数据
			getData(call) {
				let page = this.page;
				
				page++;
				
				if(call && page != 1 && this.loadShow) return;
				
				this.$http.post( 'chat/getChatListBYDialogueId', {
					dialogue_id: this.id,
					type: 'up',
					page: page,
					pageSize: 20
				}).then( res=>{
					let data = res.data.list;
					
					this.page = res.data.current_page;
					this.maxPage = res.data.last_page;
					
					call && call();
					
					// console.log(data)
					this.list.push(...data);
				
					if(page == 1){
						setTimeout(()=>{
							this.scrollNewText();
						}, 300)
						
						this.listenNewTalk();
					}                     
					
					setTimeout(()=>{
						// 判断是否有历史记录
						if( this.page < this.maxPage ){
							this.loadShow = true;
						}
					}, 300);
				})
			},
			listenNewTalk() {
				const that = this;
				
				uni.$on('some_talk_detail', (e)=>{
					
					console.log(e,1111);
					
					if( that.id == 0 ){
						that.id = e.chat_dialogue_id;
						
						// 刷新联系人页面
						uni.$emit('refFriends'); 
					}
					
					if( !that.id || (that.id != e.chat_dialogue_id) ) return;
					
					that.list.unshift(e);
					
					uni.sendSocketMessage({
						data: JSON.stringify({
							url: 'wliao/Index/setMsgStatus',
							dialogue_id: that.id
						})
					});
				})
			},
			scrollNewText() {
				this.$u.getRect('.wrap').then(res => {
					let height = res.height;

					uni.pageScrollTo({
					    scrollTop: height,
					    duration: 0
					});
					
					this.$nextTick(()=>{
						this.pageShow = true;
					})
				})
			},
			send( url ) {
				if( !url && !this.haveVal ) return;
				
				const that = this;
				
				const obj = {
					url: 'wliao/Index/usertouser',
					msg_type: url ? 2 : 1,
					to_user_id: that.to_uid,
					send_msg: url ? '' : that.val,
					msg_url: url ? url : '',
					group_id: 0
				};
				
				// const myMsg = {
				// 	chat_dialogue_id: that.id,
				// 	msg: url ? '' : that.val,
				// 	msg_type: url ? 2 : 1,
				// 	msg_url: url ? url : '',
				// 	to_user_id: that.to_uid,
				// 	send_user_id: getApp().globalData.userInfo.id,
				// 	send_head: getApp().globalData.userInfo.headimgurl,
				// 	send_name: getApp().globalData.userInfo.nickname,
				// 	to_name: that.otherName,
				// 	tu_head: ""
				// }

				uni.sendSocketMessage({
					data: JSON.stringify(obj),
					success: (res)=>{
						console.log(res)
						// that.list.unshift(myMsg);
						uni.$emit('some_talk', { id: that.id, val: url ? '' : that.val, type: url ? 2 : 1});
					},
					fail: (res)=>{
						uni.$emit('socketErr');
						console.log(JSON.stringify(res),777)
						that.$toast('服务器异常，请检查网络后重试',JSON.stringify(res))
					}
				});
	
				this.val = '';
				this.closeMore();
				
				this.scrollNewText();
			},
			showMore() {
				this.$u.getRect('.wrap').then(res => {
					let height = res.height;
					this.moreShow = !this.moreShow;
					if( height >= this.screenHeight - 148 ){
						this.wrapTransform = !this.moreShow ? 0 : 196;
					}
				})
			},
			closeMore( e ) {
				this.myfocus = this.moreShow = false;
				this.wrapTransform = 0;
			},
			onFocus() {
				this.myfocus = true;
				this.scrollNewText();
			},
			choosePic(type) {
				const sourceType = type == 1 ? 'album' : 'camera';
				
				uni.chooseImage({
					sourceType: sourceType,
					success: (e)=>{
						console.log(e)
						
						e.tempFiles.map( item=>{
							uni.uploadFile({
								url: getApp().globalData.host_api+'/upload/imgUpload',
								name: 'image',
								filePath: item.path,
								complete: (e)=>{
									const data = JSON.parse(e.data);
									
									// console.log(data,777)
									
									if( data && data.code == 1){
										const url = data.data.info.url;
										this.send(url);
									} else {
										this.$toast('图片上传失败');
									}
								}
							})
						})
					}
				})
			},
			lookPic( pic ){
				pic = this.$api.imgDirtoUrl(pic);
				
				uni.previewImage({
					urls: [pic]
				})
			}
		},
		onPageScroll(e) {
			if( !this.loadShow || e.scrollTop > 400 ) return;
			
			// 加载历史
			this.$u.debounce(()=>{
				this.loadShow = false;
				
				this.getData(()=>{
					console.log(e.scrollTop)
					if( e.scrollTop < 10 ){
						uni.pageScrollTo({
							scrollTop: 10,
							duration: 0
						}); 
					}
				})
			},300)
		}
	}

</script>

<style lang="scss" scoped>
	.content{
		width: 100%;
		min-height: 100vh;
		background-color: rgba(245, 245, 245, 1);
		overflow: hidden;
	}
	
	.wrap{
		// padding-top: 100rpx;
		transition: .3s;
	}
	
	.loading{
		/deep/.u-load-more-wrap{
			padding: 50rpx 0 20rpx;
			transform: rotateX(180deg);
		}
	}
	
	.item{
		width: 100%;
		display: flex;
		box-sizing: border-box;
		padding: 34rpx;
		transform: rotateX(180deg);
		// background-color: green;
		
		&-head{
			margin-right: 28rpx;
		}
		
		&-img{
			max-width: 100%;
			box-sizing: border-box;
			padding-right: 68rpx;
			
			/deep/image{
				border: 1px solid rgba(200,200,200,1);
			}
			
			/deep/.u-image__error{
				border: 1px solid rgba(200,200,200,1);
			}
		}
		
		&-wrap{
			flex: 1; 
		}
		
		&-content{
			width: fit-content;
			box-sizing: border-box;
			padding: 18rpx;
			background-color: #fff;
			font-size: 30rpx;
			color: rgba(11, 15, 18, 1);
			border-radius: 12rpx;
			word-break: break-all;
			position: relative;
			
			&:after{
				content: '';
				width: 0;
				height: 0;
				border-right: 14rpx solid #fff;
				border-top: 10rpx solid transparent;
				border-bottom: 10rpx solid transparent;
				position: absolute;
				top: 10rpx;
				left: 0;
				transform: translate(-90%,0);
			}
		}
		
		&-right{
			direction:rtl;
			// background-color: pink;
			
			.item-head{
				margin-right: 0;
				margin-left: 28rpx;
			}
			
			.item-img{
				padding-right: 0;
				padding-left: 68rpx;
			}
			
			.item-content{
				text-align: left;
				color: rgba(255, 255, 255, 1);
				background-color: rgba(0, 189, 253, 1);
				
				&:after{
					content: '';
					width: 0;
					height: 0;
					border-right: 0 solid transparent;
					border-left: 14rpx solid rgba(0, 189, 253, 1);
					border-top: 10rpx solid transparent;
					border-bottom: 10rpx solid transparent;
					position: absolute;
					top: 10rpx;
					right: 0;
					transform: translate(90%,0);
				}
			}
		}
	}
	
	.bar{
		width: 100%;
		background-color: rgba(240, 240, 242, 1);
		position: fixed;
		bottom: 0;
		z-index: 9999;
		transition: .3s;
	}
	
	.action{
		width: 100%;
		box-sizing: border-box;
		padding: 14rpx 17rpx 14rpx 34rpx;
		display: flex;
		align-items: flex-end;
	}
	
	.input{
		flex: 1;
		box-sizing: border-box;
		background-color: #fff;
		padding: 8rpx 0;
		display: flex;
		align-items: center;
		
		textarea{
			max-height: 200rpx;
		}
	}
	
	.more{
		height: 72rpx;
		padding: 0 0 0 14rpx;
		display: flex;
		align-items: center;
		
		
		.iconfont{
			overflow: hidden;
			font-size: 54rpx;
			padding: 0 14rpx;
			transition: .3s;
		}
		
		.send{
			overflow: hidden;
			transition: .3s;
			
			/deep/.u-btn{
				height: 60rpx;
				border-radius: 6rpx;
				margin: 0 14rpx;
			}
		}
	}
	
	.choose{
		display: flex;
		flex-wrap: wrap;
		box-sizing: border-box;
		padding: 24rpx 34rpx 30rpx;
		
		&-box{
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			margin-left: 50rpx;
			
			&:first-child{
				margin-left: 0;
			}
			
			text{
				margin-top: 20rpx;
				font-size: 24rpx;
				color: rgba(151, 155, 157, 1);
			}
		}
		
		&-item{
			width: 90rpx;
			height: 90rpx;
			background: #FCFCFC;
			border-radius: 8rpx;
			display: flex;
			justify-content: center;
			align-items: center;
			box-sizing: border-box;
			padding-top: 4rpx;
			
			.iconfont{
				color: rgba(138, 138, 138, 1);
				font-size: 56rpx;
			}
		}
	}
</style>
