
var discoverReply = (function() {
	const html = `<div v-if="list.length > 0">
					<template v-for="( item, index ) in replyList">
						<div class="u_talk" v-if="showItem(index)" :key="index">
							<div class="u_talk_top">
								<img v-if="item.head" :src="item.head">
								<img v-else src="../../static/my/touxiang.png">
								<span>{{ item.name }}</span>
							</div>
							<div class="u_talk_content">{{ item.content }}</div>
							<div class="u_talk_bottom">
								<div class="u_talk_box">
									<span class="u_talk_time">{{ item.time }}</span>
									<van-tag v-if="user_id&&user_id!=item.user_id" size="medium" color="rgba(244, 248, 255, 1)" @click="$emit('reply',item)">{{ item.reply.length }}回复</van-tag>
								</div>
								<common-like :id="item.id" :state=" (( replyState && replyState[item.id] && replyState[item.id].state == true) ? 1 : 0) " :num="item.lik" padding=".2rem 0 .2rem .3rem" @like="likeSome"></common-like>
							</div>
							<div class="u_talk_wrap" v-if="item.reply.length > 0 && reply">
								<div class="u_talk_item" v-for="( myList, key ) in item.reply" :key="key">
									<div class="u_talk_item_top">
										<img v-if="myList.head" :src="myList.head">
										<img v-else src="../../static/my/touxiang.png">
										<span>{{ myList.name }}</span>
									</div>
									<div class="u_talk_item_content">{{ myList.content }}</div>
								</div>
							</div>
						</div>
					</template>
				</div>`;
	
	return {
		data: function(){
			return {
				replyState: {},
				replyList: [],
				user_id: 0,
			}
		},
		template: html,
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
						// console.log(this.replyList,3435345);
					}else{
						this.replyList = this.$api.deepClone(val);
						
					}
					this.initLike();
				},
				deep: true
			}
		},
		created() {
			let userInfo = this.$api.localGet('user_info');
			if(userInfo&&userInfo.user_id){
				this.user_id = userInfo.user_id; 
			}

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
				let likeReply = this.$api.localGet('u-like-reply');
				const timestamp = Date.parse(new Date());
				
				for( let i in likeReply ){
					if( likeReply[i].state == true && likeReply[i].data - timestamp >= 86400000 ){
						likeReply[i].state = false;
						likeReply[i].data = timestamp;
					}
				}
				
				this.replyState = likeReply;
				this.$api.localSet('u-like-reply',JSON.stringify(likeReply));
			},
			// 点赞
			likeSome( id ) {
				if( Number(id) < 0 ){
					return;
				}
				
				let likeReply = this.$api.localGet('u-like-reply');
				
				// console.log(likeReply)
				
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
				
				this.$http.ajax({
					data:{
						id: id
					},
					url:'/index/Comment/newsCommentLike'
				}).then( res=>{
					this.replyList.map((item,index)=>{
						if( item.id == id ){
							let newLike = Number(item.lik) + 1;
							this.replyState = likeReply;
							this.$api.localSet('u-like-reply',JSON.stringify(likeReply));
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
}());