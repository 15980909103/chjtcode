<template>
	<view>
		<div class="reply_wrap">
			<u-popup v-model="showThat" mode="bottom">
				<div class="reply_box">
					<div class="reply_top">
						<h4>相关留言</h4>
						<span class="reply_close" @click="close">取消</span>
					</div>
					<scroll-view scroll-y="true" style="height: 828rpx;">
						<discover-reply v-if="list.length" :list="list" @like="likeSome" @reply="replySome"></discover-reply>
						<div class="reply_box-no-text" v-else>暂无数据</div>
					</scroll-view>
				</div>
			</u-popup>
			<transition name="u-slide-up">
				<div class="reply_input" v-show="navShow || showThat">
					<u-form @submit="onSubmit" ref ="tesxfrom">
						<u-input class="reply_input_item" 
							v-model="text" 
							:focus="focus"
							@click="loginfalse"
							maxlength="300"
							:placeholder="pltesxt" ></u-input>
						<!-- <u-field
							name="val"
							class="reply_input_item" 
							v-model="text" 
							maxlength="300"
							:placeholder="pltesxt" 
							ref="field"
							@input="inputClick"
							@blur="inputBlur"
							@click='inputFocus'
							:readonly='read_only'>
						</u-field> -->
					</u-form>
					<!-- <common-like
						v-if="!sendBtn"
						:itemid="ids" 
						:num="content.like" 
						type="0" 
						:state="content.likeStatus"
						padding=".2rem 0 0 .5rem"
						@like="likeSomess"
					>
					</common-like> -->
					<view
						class="like_state" 
						:class="[ content.likeStatus == 0 ? '' : 'text-active' ]"
						style="padding:.2rem 0 0 1rem"
						@click="likeSomess"
					>
						<i class="iconfont icondianzan" :data-like="content.like | brief"></i>
						<!-- <text class="num_text">{{ content.like | brief }}</text> -->
						<!-- <text class="num_text" v-if="content.like>999">{{ content.like.substring(0,1) +'K+' }}</text> -->
					</view>
					<div class="reply_icon" v-if="!sendBtn">
						<span @click="addFollow" class="reply_icon_wrap"><i class="iconfont" :class=" like == 0 ? 'iconbianzu1' : 'iconguanzhu  text-active' "></i></span>
						<span class="reply_icon_wrap" @click="goShare"><i class="iconfont iconfenxiang2"></i></span>
					</div>
					<div class="reply_icon" v-if="sendBtn">
						<button class='reply_send' @click='onSubmit'>发送</button>
					</div>
				</div>
			</transition>
			
		</div>
	</view>
</template>

<script>
	let app = getApp();
	import commonLike from '../common/like'
	import discoverReply from './reply.vue'
	export default {
		data() {
			return {
				showThat: false,
				text: '',
				replyId: 0,
				pltesxt:'我来说两句',
				like: 0,
				sendBtn: false,
				read_only: false,
				user_id: 0,
			};
		},
		props: {
			focus: {
				type: [Number,String],
				default() {
					return false
				}
			},
			show: {
				type: [Boolean],
				default() {
					return false
				}
			},
			list: {
				type: [Array],
				default() {
					return []
				}
			},
			ids: {
				type: [Number,String],
				default() {
					return -1
				}
			},
			content: {
				type: [Object],
				default() {
					return {}
				}
			},
			userid: {
				type: [Number,String],
				default() {
					return -1
				}
			},
			pid:{
				type: [Number,String],
				default() {
					return -1
				}
			},
			cate_id:{
				type: [Number,String],
				default() {
					return -1
				}
			},
			favorite: {
				type: [Number,String],
				default() {
					return 0
				}
			},
			// 资讯 9 视频 13
			column:{
				type: [Number,String],
				default() {
					return 9
				}
			},
			navShow: {
				type: Boolean,
				default() {
					return true
				}
			},
			showSend: {
				type: Boolean,
				default() {
					return false
				}
			}
		},
		watch: {
			show(val){
				this.showThat = val;
			},
			showThat(val){
				if( val == false ){
					this.close();
				}
			},
			favorite( val ){
				this.like = val;
			},
			showSend( val ){
				this.sendBtn = val;
			},
			text(val){
				if(val){
					if(!this.isLogin()){
						return this.sendBtn = false;
					}
					this.sendBtn = true
				}else{
					this.replyId = 0;
					this.sendBtn = false
					this.pltesxt  = '我来说两句';
				}
			}
		},
		filters:{
			brief:function(value){
				if(parseInt(value)>999){
					let nums = value+''
					return nums.substring(0,1)+'k+'
				}else{
					return value
				}
			}
		},
		components:{
			commonLike,
			discoverReply
		},
		created() {
			let userInfo = this.$api.localStore.localGet('user_info');
			if(userInfo&&userInfo.user_id){
				this.user_id = userInfo.user_id; 
			}
			this.like = this.favorite;
			this.sendBtn = this.showSend;
			
		},
		methods: {
			loginfalse(){
				if(!this.isLogin()){
					this.alertLogin();
				}
			},
			close() {
				this.$emit('close');
			},
			inputClick() {
				if(!this.isLogin()){
					return this.sendBtn = false;
				}
				if( this.showSend == false && this.text.length != 0 ){
					this.sendBtn = true;
				}
			},
			// 回复某条
			replySome( item ) {
				this.text = '';
				this.replyId = item.id;
				// this.$refs.field.focus();
				this.pltesxt  = '@'+item.name
			},
			inputBlur() {
				this.pltesxt  = '我来说两句';
			},
			inputFocus(){
				if( !this.isLogin()){
					this.read_only = true;
					this.goPage('authorize/index')
					// this.$http.hrefMobileLogin();
					return;
				}else{
					this.read_only = false;
				}
			},
			addFollow(){
				this.alertLogin();
				// if( !this.isLogin()){
				// 	this.goPage('authorize/index')
				// 	return;
				// }
				let _this = this;

				this.$http.post(
					'/news/addFollow',
					{
						pid: this.pid,
						cate_id: this.cate_id,
						id: this.userid,
					},
				).then(res=>{
					if(res.code == 1){
						if(res.code == 1){
							this.like = Number(res.data.is_follow);
							
							this.$toast(this.like==1?'关注成功':'取消关注');
						}else{
							this.$toast(res.msg);
						}
					}
				});
			},
			onSubmit() {
				let text = this.text;
				text = this.$api.trim(text);
				text = this.$api.htmlEscape(text);
				const obj = {
					id: this.userid,
					text: text,
					column_id: this.column
				};
				
				if( this.$api.isEmpty(text) ){
					this.$toast('输入不能为空');
				} else {
					if( this.replyId != 0 ){
						obj.pid = this.replyId;
					} else {
						obj.pid = 0;
					}

					this.sendSome(obj);
				}
			},
			sendSome( option ) {
				this.alertLogin();
				// if( !this.isLogin()){
				// 	this.goPage('authorize/index')
				// 	// this.$http.hrefMobileLogin();
				// 	return;
				// }
				// option.cate_id = this.$http.getUrlParamValue('cate_id');
				option.cate_id =this.cate_id
				let _this = this;
							
				this.$http.post(
					'/comment/newsComment',
					option,
				).then(res=>{
					console.log(res)
					if(res.code==1){
						this.$toast('评论成功');
						this.text = '';
						this.replyId = 0;
						option.new_id = res.data.id;
						option.user_id = this.user_id;
						
						this.initSendInfo(option);
					}else{
						this.$toast(res.msg);
					}
				}).catch(()=>{
					this.$toast('评论失败');
				});
			},
			initSendInfo(option) {
				// app.globalData.userInfo
				const info = app.globalData.userInfo;
				const time = this.$api.timeFormat(Date.parse(new Date()),'mm-dd hh:MM');
				const id = Number(option.new_id);
				const obj = {
					id: id,
					content: option.text,
					head: (info && info.headimgurl) ? info.headimgurl : this.$api.imgDirtoUrl('/my/touxiang.png'),
					name: (info &&  info.nickname) ? info.nickname : '手机用户',
					lik: 0,
					num: 0,
					pid: 0,
					reply: [],
					time: time,
					user_id: option.user_id ? option.user_id : 0
				};
				const newOption = {
					option: option,
					obj: obj
				}
				this.$emit('send', newOption);
			},
			likeSome( e ) {
				this.$emit('like', e);
			},
			likeSomess(e) {
				 this.$emit('likess', e);
				// if( !this.isLogin()){
				// 	this.$http.hrefMobileLogin();
				// 	return;
				// }
				
				// let that = this;
				// this.$http.post(
				// 	'/news/addFabulous',
				// 	{
				// 		pid:this.pid,
				// 		cate_id:this.cate_id,
				// 		id:this.ids,
				// 	}
				// ).then(res=>{
				// 	console.log(res);
				// 	if(res.code == 1){
				// 		if(res.code == 1){
				// 			uni.showToast({
				// 				title: res.msg,
				// 				duration: 2000
				// 			});
				// 			// this.$toast(res.msg);
				// 			console.log(this.content)
				// 			console.log(Number(res.data.is_fabulous))
				// 			this.content.likeStatus = Number(res.data.is_fabulous);
				// 			console.log(this.content.likeStatus)
				// 			if(this.content.likeStatus  ==1){
				// 				this.content.like ++;
				// 			}else{
				// 				this.content.like --;
				// 			}
							
				// 		}
				// 	}
				// }).catch(res=>{
				// 	this.$toast(res.msg);
				// });
			
			
			},
				// this.$emit('likess', e);
			goShare() {
				// if(this.userid== -1 || this.cate_id == -1 || this.pid== -1){
				// 	this.$toast('参数缺失');
				// 	return ;
				// }

				this.goPage('discover/share', { id: this.userid,pid:this.pid,cate_id:this.cate_id });
			}
		}
	}
</script>

<style lang="scss">
/* pop */
.reply_wrap{
	width: 100vw;
	// height: 1020rpx;
	position: fixed;
	bottom: 0;
	z-index: 10;
}

.reply_input{
	width: 100%;
	height: 110rpx;
	background-color: rgba(255, 255, 255, 1);
	box-shadow: 0 -2rpx 10rpx 0px #F5F5F5;
	display: flex;
	align-items: center;
	position: absolute;
	left: 0;
	bottom: 0;
	z-index: 10075;
}

.reply_input_item{
	width: 350rpx;
	height: 68rpx;
	margin-left: 18rpx;
	padding: 0 18rpx;
	display: flex;
	align-items: center;
	border-radius: 34rpx;
	border: 1px solid #E6E6E6;
}
.reply_input_item .u-label{
	width: 0 !important;
}
.reply_icon{
	height: 100%;
	flex: 1;
	display: flex;
	justify-content: center;
	align-items: center;
}

.reply_icon_wrap{
	height: 100%;
	flex: 1;
	display: flex;
	justify-content: center;
	align-items: center;
}

.reply_wrap .u-popup.u-popup--bottom{
	border-radius: 20rpx 20rpx 0 0;
}

.reply_box{
	height: 1020rpx;
	box-sizing: border-box;
	padding: 130rpx 32rpx 140rpx;
	border-radius: 20rpx 20rpx 0 0;
	position: relative;
	// position: fixed;
	// overflow-y: scroll;
	// -webkit-overflow-scrolling: touch;
}

.reply_top{
	width: 100%;
	display: flex;
	justify-content: space-between;
	align-items: center;
	position: absolute;
	left: 0;
	top: 0;
	background-color: #fff;
	z-index: 2;
	box-sizing: border-box;
	padding-left: 32rpx;
	border-bottom: 1rpx solid rgba(224, 224, 224, 1);
}

.reply_close{
	padding: 34rpx 32rpx 28rpx;
}

.reply_send{
	height: 68rpx;
	width: 160rpx;
	// padding-left: 15px;
	background-color: rgba(254, 130, 30, 1);
	color: #fff;
	line-height: 68rpx;
	text-align: center;
}
.reply_box-no-text{
	text-align: center;
	width: 100%;
	margin-top: 50px;
}
.like_state{
	width: 100rpx;
	font-size: 24rpx;
	color: rgba(87, 107, 149, 1);
	box-sizing: content-box;
	// display: flex;
	// align-items: center;
	// justify-content: flex-end;
}

.like_state .iconfont{
	display: inline-block;
	vertical-align: bottom;
	// margin-bottom: 10rpx;
	// margin-left: 10rpx;
	position: relative;
}
.num_text{
	display: inline-block;
	vertical-align: top;
	margin-left: 10rpx;
	margin-top: -17rpx;
}

.iconfont.icondianzan:after{
	content:attr(data-like);
	font-size: 22rpx;
	position: absolute;
	top: -16rpx;
	right: 0;
	transform: translate(100%,0);
}
</style>
