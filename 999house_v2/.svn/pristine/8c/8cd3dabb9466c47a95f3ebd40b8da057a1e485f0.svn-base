
var discoverReplyBox = (function() {
	const html = `<div class="reply_wrap">
				<van-popup v-model="showThat" position="bottom">
					<div class="reply_box">
						<div class="reply_top">
							<h4>相关留言</h4>
							<span class="reply_close" @click="close">取消</span>
						</div>
						<discover-reply v-if="list.length" :list="list" @like="likeSome" @reply="replySome"></discover-reply>
						<div class="reply_box-no-text" v-else>暂无数据</div>
					</div>
				</van-popup>
				<transition name="van-slide-up">
					<div class="reply_input" v-show="navShow || showThat">
						<van-form @submit="onSubmit" ref ="tesxfrom">
							<van-field
								name="val"
								class="reply_input_item" 
								v-model="text" 
								maxlength="300"
								:placeholder="pltesxt" 
								ref="field"
								@input="inputClick"
								@blur="inputBlur"
								@click='inputFocus'
								:readonly='read_only'
							>
							</van-field>
						</van-form>
						<div class="reply_icon" v-if="!sendBtn">
							<span @click="addFollow" class="reply_icon_wrap"><i class="iconfont" :class=" like == 0 ? 'iconbianzu1' : 'iconguanzhu  text-active' "></i></span>
							<span class="reply_icon_wrap" @click="goShare"><i class="iconfont iconfenxiang2"></i></span>
						</div>
						<div class="reply_icon" v-if="sendBtn">
							<van-button class='reply_send' @click='onSubmit'>发送</van-button>
						</div>
					</div>
				</transition>
				
			</div>`;
	
	return {
		data: function(){
			return {
				showThat: false,
				text: '',
				replyId: 0,
				pltesxt:'我来说两句',
				like: 0,
				sendBtn: false,
				read_only: false,
				user_id: 0,
			}
		},
		template: html,
		props: {
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
			id: {
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
					if(!this.$http.isLogin()){
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
		created() {
			let userInfo = this.$api.localGet('user_info');
			if(userInfo&&userInfo.user_id){
				this.user_id = userInfo.user_id; 
			}
			this.like = this.favorite;
			this.sendBtn = this.showSend;
		},
		methods: {
			close() {
				this.$emit('close');
			},
			inputClick() {
				if(!this.$http.isLogin()){
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
				this.$refs.field.focus();
				this.pltesxt  = '@'+item.name
			},
			inputBlur() {
				this.pltesxt  = '我来说两句';
			},
			inputFocus(){
				if( !this.$http.isLogin()){
					this.read_only = true;
					this.$http.hrefMobileLogin();
					return;
				}else{
					this.read_only = false;
				}
			},
			addFollow(){
				if( !this.$http.isLogin()){
					this.$http.hrefMobileLogin();
					return;
				}
				let _this = this;

				_this.$http.ajax({
					data:{
						pid: this.$http.getUrlParamValue('pid'),
						cate_id: this.$http.getUrlParamValue('cate_id'),
						id: this.id,
					},
					url:'/index/news/addFollow'
				}).then(res=>{
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
					id: this.id,
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
				if( !this.$http.isLogin()){
					this.$http.hrefMobileLogin();
					return;
				}
				
				option.cate_id = this.$http.getUrlParamValue('cate_id');
				
				// console.log(option);

				let _this = this;
							
				_this.$http.ajax({
					data: option,
					url:'/index/comment/newsComment'
				}).then(res=>{
					if(res.code==1){
						this.$toast('评论成功');
						this.text = '';
						this.replyId = 0;
						option.new_id = res.data.id;
						option.user_id = this.user_id;
						
						this.initSendInfo(option);
					}else{
						this.$toast('评论失败');
					}
				}).catch(()=>{
					this.$toast('评论失败');
				});
			},
			initSendInfo(option) {
				const info = this.$api.localGet('user_info');
				const time = this.$api.timeFormat(Date.parse(new Date()),'mm-dd hh:MM');
				const id = Number(option.new_id);
				// console.log(info);
				// return
				const obj = {
					id: id,
					content: option.text,
					head: (info && info.user_avatar) ? info.user_avatar : '../../static/my/touxiang.png',
					name: (info &&  info.user_name) ? info.user_name : '手机用户',
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
				console.log(e,'like');
				this.$emit('like', e);
			},
			goShare() {
				if(this.id== -1 || this.cate_id == -1 || this.pid== -1){
					this.$toast('参数缺失');
					return ;
				}

				this.$api.goPage('discover/share.html', { id: this.id,pid:this.pid,cate_id:this.cate_id });
			}
		}
	}
}());