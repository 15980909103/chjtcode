var applyActive = (function() {
	const html = `
			<div class="apply_alert">
				<van-popup v-model="showThat" closeable>
					<h4>立即报名，领取优惠</h4>
					<p v-if="name" class="apply_name">报名楼盘：{{ name }}</p>
					<van-field
						v-model="username"
						clearable
						placeholder="请输入用户名"
						maxlength="15"
					>
					</van-field>
					<van-field
						v-model="phoneNum"
						type="tel"
						center
						class="apply_alert_phone"
						clearable
						placeholder="请输入手机号码"
						maxlength="11"
					>
						<template #button>
							<van-button 
								size="small" 
								type="primary" 
								class="apply_alert_msg"
								:disabled="msgDisabled"
								@click="getMsg"
							>
								{{ msgText }}
							</van-button>
						</template>
					</van-field>
					<van-field 
						v-model="msg" 
						type="digit" 
						center
						clearable
						placeholder="输入验证码"
						maxlength="6"
					>
					</van-field>
					<!-- <div class="apply_alert_project" @click="sureState = !sureState">
						<i class="iconfont" :class="sureState ? 'iconnewxuanzhongduoxuan' : 'iconweixuanzhong'"></i>
						<p>
							我已阅读并同意<i @click.stop="goProtocol(0)">《九房使用协议》</i>、<i @click.stop="goProtocol(1)">《九房双12活动说明》</i>
						</p>
					</div> -->
					<van-button 
						size="small" 
						color="rgba(219, 30, 30, 1)" 
						round 
						class="apply_alert_submit"
						:loading="loading"
						loading-text="提交中..."
						@click="submitClick"
					>
						提交
					</van-button>
				</van-popup>
			</div>
	`;
				
	
	return {
		template: html,
		data: function(){
			return {
				showThat: false,
				msgDisabled: false,
				msgText: '获取验证码',
				sureState: true,
				loading: false,
				username: '',
				// 手机号
				phoneNum: '',
				// 验证码
				msg: '',
				timeOut: 0,
				source: ''
			}
		},
		props: {
			show: {
				type: Boolean,
				default() {
					return false
				}
			},
			id: {
				type: [ Number, String ],
				default() {
					return -1
				}
			},
			name: {
				type: [ String ],
				default() {
					return ''
				}
			},
			state: {
				type: [ String ],
				default() {
					return 'only'
				}
			}
		},
		watch: {
			show(newV) {
				const phone = this.$api.localGet('phone');
				
				if( phone && newV ){
					this.showWin(phone);
				} else {
					this.init();
					this.showThat = newV;
				}
			},
			showThat( newV ){
				this.$emit('close',newV);
			}
		},
		created() {
			this.source = this.$api.funcUrlDel().option.source;
		},
		methods: {
			init() {
				this.msgDisabled = false;
				clearInterval(this.timeOut);
				this.msgText = '获取验证码';
				this.sureState = true;
				this.loading = false;
				this.username = '';
				this.msg = '';
			},
			getMsg() {
				if( this.$api.mobile(this.phoneNum) ){
					this.sendCode()
				} else {
					this.$toast('请输入正确手机号')
				}
			},
			// 发送验证码
			sendCode(){
				this.$http.ajax({
					method: 'POST',
					url: '/index/public/sendMsg',
					data: {
						phone: this.phoneNum,
						sence: 'sign_up'
					},
				}).then( res=>{
					let data = res.data;
					
					this.msgDisabled = true;
					this.msgText = 60;
					this.timeOut = setInterval(()=>{
						if( this.msgText != 1 ){
							this.msgText -= 1;
						} else {
							this.msgDisabled = false;
							clearInterval(this.timeOut);
							this.msgText = '获取验证码';
						}
					},1000)
					
					this.$toast(res.data);
					
					console.log(res);
				}).catch( res=>{
					this.$toast(res.msg);
				})
			},
			submitClick() {
				if( this.$api.mobile(this.phoneNum) ){
					if( this.msg.length == 6 ){
						// if( this.sureState != true ){
						// 	this.$toast('请勾选相关协议');
						// } else {
						// 	this.submitInfo(this.phoneNum,this.msg);
						// }
						if( this.username ){
							this.$api.localSet('userName', this.username);
						} 
						
						this.loading = true;
						this.submitInfo(this.phoneNum,this.msg);
					} else {
						this.$toast('请输入正确验证码')
					}
				} else {
					this.$toast('请输入正确手机号')
				}
			},
			submitInfo(phone,code) {
				let url;
				const obj = {
					mobile: phone,
					sign_id: this.id,
					source: this.source
				}

				if( code ){
					obj.code = code;
				} else {
					obj.type = 1;
				}
				
				if( this.state == 'all' ){
					this.username = this.$api.localGet('userName');
					
					if( this.username ){
						obj.name = this.username;
					}
					
					url = '/index/SignUp/discountRegistration';
					
				} else {
					let active = this.$api.funcUrlDel().option.active_id;
					if(active){
						obj.active_id =active
					}
					console.log('777767',obj)
					url = '/index/SignUp/add';
				}
				
				this.$http.ajax({
					url: url,
					data: obj,
				}).then( res=>{
					let data = res.data;
					this.loading = false;
					this.$api.localSet('phone',obj.mobile);
					meteor.track("form", {convert_id: "1685019733755916"});
					if( url == '/index/SignUp/add' ){
						console.log('889')
						this.$toast('您已成功领取优惠');
					} else {
						this.$toast(res.msg);
					}
					// this.$emit('state');
					this.$emit('close',false);
				}).catch( res=>{
					this.loading = false;
					
					if( res.code == 2 ){
						this.$toast(res.msg);
						this.$api.localDel('phone');
						
						setTimeout(()=>{
							this.showThat = true;
						},1000);
					} else {
						if( res.code == 3 ) {
							
							
							this.$api.localSet('phone',obj.mobile);
							if( url == '/index/SignUp/add' ){
								console.log('889')
								this.$toast('您已成功领取优惠');
							} else {
								this.$toast(res.msg);
							}
							
							this.$emit('close',false);
						} else {
							this.$toast(res.msg);
						}
					}
				})
			},
			// 有手机号
			showWin( phone ) {
				// let title;
				
				// if( this.name ){
				// 	title = `报名活动-${this.name}`;
				// } else {
				// 	title = `报名活动`;
				// }
				
				// this.$dialog.confirm({
				// 	title: title,
				// 	message: `报名活动后将有专人联系您</br><span style="margin-top: 0.1rem">手机号码${phone}</span>`,
				// })
				// .then(() => {
				// 	this.submitInfo(phone);
				// })
				// .catch(() => {
				// 	this.$emit('close',false);
				// });
				this.submitInfo(phone);
			},
			goProtocol( type ) {
				this.$api.goPage('protocol/index.html',{ type: type });
			}
		}
	}
}());

