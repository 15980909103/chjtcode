<template>
	<view>
		<view v-if="list.length > 0" style="overflow: scroll;">
			<template v-for="( item, index ) in list">
				<view class="u_talk" v-if="showItem(index)" :key="index">
					<view class="u_talk_top">
						<img v-if="item.head" :src="$api.imgDirtoUrl(item.head,2)">
						<img v-else :src="$api.imgDirtoUrl('/my/touxiang.png')">
						<span>{{ item.name }}</span>
					</view>
					<view class="u_talk_content">{{ item.content }}</view>
					<view class="u_talk_bottom">
						<view class="u_talk_box">
							<span class="u_talk_time">{{ item.time }}</span>
							<u-tag v-if="user_id&&user_id!=item.user_id" color="#666666" bg-color="#F4F8FF" border-color="#fff" size="mini" @click="$emit('reply',item)" :text="item.reply.length+'回复'"/>
						</view>
						<common-like v-if="item.id" :itemid="item.id" :state=" (( replyState && replyState[item.id] && replyState[item.id].state == true) ? 1 : 0) " :num="item.lik" padding=".2rpx 0 .2rpx .3rpx" @like="likeSome"></common-like>
						<!-- <common-like v-if="item.id"  :state=" (( replyState && replyState[item.id] && replyState[item.id].state == true) ? 1 : 0) " idss="23" num="2000" padding=".2rpx 0 .2rpx .3rpx" @like="likeSome"></common-like> -->
					</view>
					<view class="u_talk_wrap" v-if="item.reply.length > 0 && reply">
						<view class="u_talk_item" v-for="( myList, key ) in item.reply" :key="key">
							<view class="u_talk_item_top">
								<img v-if="myList.head" :src="$api.imgDirtoUrl(myList.head,2)">
								<img v-else :src="$api.imgDirtoUrl('/my/touxiang.png')">
								<span>{{ myList.name }}</span>
							</view>
							<view class="u_talk_item_content">{{ myList.content }}</view>
						</view>
					</view>
				</view>
			</template>
		</view>
	</view>
</template>

<script>
	import commonLike from '../common/like'
	let app = getApp();
	let that = null;
	export default {
		data() {
			return {
				replyState: {},
				replyList: [],
				user_id: 0,
			};
		},
		props: {
			list: {
				type: [Array],
				default() {
					return []
				}
			},
			reply: {
				type: Boolean,
				default() {
					return true
				}
			}
		},
		watch: {
			list: {
				handler( val ) {
					if(!this.reply){
						this.replyList = this.$api.deepClone(val);
						for(let i in this.replyList){
							let item = this.replyList[i]
							item.head = item.head? item.head : '../../static/my/touxiang.png'
							item.name = item.name? item.name : '手机用户'
						}

						let length = this.replyList.length;
					
						this.replyList = this.replyList.slice(0,3);
					}else{
						this.replyList = this.$api.deepClone(val);
						
					}
					this.initLike();
				},
				deep: true
			}
		},
		components:{
			commonLike
		},
		// onLoad() {
		// 	let userInfo =''
		// 	console.log('replu onshow')
		// 	if(app.globalData.userInfo&&app.globalData.userInfo.headimgurl&&refresh==0){
		// 		return userInfo = app.globalData.userInfo
		// 	}
		// 	console.log('userInfo',userInfo)
		// 	if(userInfo&&userInfo.user_id){
		// 		this.user_id = userInfo.user_id; 
		// 	}
			
		// 	for(let i in this.list){
		// 		let item = this.list[i]
		// 		item.head = item.head? item.head : '../../static/my/touxiang.png';
		// 		item.name = item.name? item.name : '手机用户';
		// 	}
			
		// 	this.replyList = this.$api.deepClone(this.list);
		// 	this.initLike();
		// },
		created() {
			let userInfo = ''
			if(app.globalData.userInfo&&app.globalData.userInfo.headimgurl){
				let userInfo = app.globalData.userInfo
				this.user_id = userInfo.id; 
			}
			// if(userInfo&&userInfo.user_id){
			// 	this.user_id = userInfo.user_id; 
			// }
			for(let i in this.list){
				let item = this.list[i]
				item.head = item.head? item.head : '../../static/my/touxiang.png';
				item.name = item.name? item.name : '手机用户';
			}
			this.replyList = this.$api.deepClone(this.list);
			this.initLike();
		},
		methods: {
			// 初始化点赞
			initLike( ) {
				let likeReply = this.$api.localStore.localGet('u-like-reply');
				const timestamp = Date.parse(new Date());
				
				for( let i in likeReply ){
					if( likeReply[i].state == true && likeReply[i].data - timestamp >= 86400000 ){
						likeReply[i].state = false;
						likeReply[i].data = timestamp;
					}
				}
				
				this.replyState = likeReply;
				this.$api.localStore.localSet('u-like-reply',JSON.stringify(likeReply));
			},
			// 点赞
			likeSome( id ) {
				this.alertLogin();
				
				if( Number(id) < 0 ){
					return;
				}
				
				let likeReply = this.$api.localStore.localGet('u-like-reply');
				
				const timestamp = Date.parse(new Date());
				
				if( likeReply ){
					if( likeReply[id] && likeReply[id].state == true ){
						return;
					}
				} else {
					likeReply = {};
				}
				
				likeReply[id] = {
					data: timestamp,
					state: true
				}
				
				this.$http.post(
					'/Comment/newsCommentLike',
					{
						id: id
					},
				).then( res=>{
					this.replyList.map((item,index)=>{
						if( item.id == id ){
							let newLike = Number(item.lik) + 1;
							this.replyState = likeReply;
							this.$api.localStore.localSet('u-like-reply',JSON.stringify(likeReply));
							this.$emit('like',{
								index: index,
								num: newLike
							});
							// this.$set(this.replyList[index],'lik',newLike);
						}
					});
				})
			},
			showItem(index){
				if( this.reply == false && index > 2 ){
					return false
				} else {
					return true
				}
			}
		}
	}
</script>

<style lang="scss">
	/* 留言 */
	.u_talk{
		width: 100%;
		display: flex;
		flex-direction: column;
	}
	
	.u_talk_top,
	.u_talk_item_top{
		display: flex;
		align-items: center;
	}
	
	.u_talk_top img{
		width: 68rpx;
		height: 68rpx;
		border-radius: 50%;
		margin-right: 20rpx;
	}
	
	.u_talk_top text,
	.u_talk_item_top text{
		font-size: 30rpx;
		color: rgba(117, 117, 117, 1);
	}
	
	.u_talk_content{
		width: 100%;
		font-size: 30rpx;
		margin-top: 24rpx;
	}
	
	.u_talk_bottom{
		font-size: 24rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}
	
	.u_talk_box{
		display: flex;
		align-items: center;
	}
	
	.u_talk_box .u-tag{
		color: rgba(117, 117, 117, 1);
	}
	.u_talk_time{
		color: rgba(173, 173, 173, 1);
		vertical-align: bottom;
		margin-right: 38rpx;
		margin-bottom: 14rpx;
	}
	
	.u_talk_wrap{
		width: 100%;
		box-sizing: border-box;
		padding-left: 88rpx;
		margin-bottom: 34rpx;
	}
	
	.u_talk_item{
		margin-bottom: 28rpx;
	}
	
	.u_talk_item:last-child{
		margin-bottom: 0;
	}
	
	.u_talk_item_top img{
		width: 44rpx;
		height: 44rpx;
		border-radius: 50%;
		margin-right: 20rpx;
	}
	
	.u_talk_item_content{
		width: 100%;
		font-size: 30rpx;
		margin-top: 20rpx;
		padding-bottom: 20rpx;
		border-bottom: 1px solid rgba(224, 224, 224, 1);
	}
	
</style>
