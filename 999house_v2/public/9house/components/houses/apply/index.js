var housesApply = (function() {
	const html = `
			<div class="apply_alert">
				<van-popup v-model="showThat" closeable>
					<h4>立即报名，领取优惠</h4>
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
					<div class="apply_alert_project" @click="sureState = !sureState">
						<i class="iconfont" :class="sureState ? 'iconnewxuanzhongduoxuan' : 'iconweixuanzhong'"></i>
						<p>
							我已阅读并同意<i @click.stop="goProtocol(0)">《九房隐私协议》</i>、<i @click.stop="goProtocol(1)">《九房使用协议》</i>
						</p>
					</div>
					<van-button 
						size="small" 
						color="rgba(219, 30, 30, 1)" 
						round 
						class="apply_alert_submit"
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
				
				// 手机号
				phoneNum: '',
				// 验证码
				msg: '',
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
		},
		watch: {
			show(newV) {
				const phone = this.$api.localGet('phone');
				
				if( phone && newV ){
					this.showWin(phone);
				} else {
					this.showThat = newV;
				}
			},
			showThat( newV ){
				this.$emit('close',newV);
			}
		},
		created() {

		},
		mounted() {
			
		},
		methods: {
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
					const timeOut = setInterval(()=>{
						if( this.msgText != 1 ){
							this.msgText -= 1;
						} else {
							this.msgDisabled = false;
							this.msgText = '获取验证码';
							clearInterval(timeOut);
						}
					},1000)
					
					this.$toast(res.data);
					
					console.log(res);
				}).catch( res=>{
					 
					// }
					
					// console.log(res);
					this.$toast(res.msg);
				})
			},
			submitClick() {
				if( this.$api.mobile(this.phoneNum) ){
					if( this.msg.length == 6 ){
						if( this.sureState != true ){
							this.$toast('请勾选相关协议');
						} else {
							this.submitInfo(this.phoneNum,this.msg);
						}
					} else {
						this.$toast('请输入正确验证码')
					}
				} else {
					this.$toast('请输入正确手机号')
				}
			},
			submitInfo(phone,code) {
				const obj = {
					mobile: phone,
					sign_id: this.id,
				}
				
				if( code ){
					obj.code = code;
				} else {
					obj.type = 1;
				}

				let active = this.$api.funcUrlDel().option.active_id;
				if(active){
					obj.active_id =active
				}
				console.log('9232')
				this.$http.ajax({
					method: 'POST',
					url: '/index/signUp/add',
					data: obj,
				}).then( res=>{
					let data = res.data;
					this.$api.localSet('phone',this.phoneNum)
					this.$toast('报名成功');
					this.$emit('state');
					this.$emit('close',false);
				}).catch( res=>{
					this.$toast(res.msg);
					
					if( res.code == 2 ){
						this.$api.localDel('phone');
					}
					this.$emit('close',false);
				})
			},
			// 有手机号
			showWin( phone ) {
				this.$dialog.confirm({
					title: '报名活动',
				  message: `报名活动后将有专人联系您</br><span style="margin-top: 0.1rem">手机号码${phone}</span>`,
				})
				.then(() => {
					this.submitInfo(phone);
				})
				.catch(() => {
					this.$emit('close',false);
				});
			},
			goProtocol( type ) {
				this.$api.goPage('protocol/index.html',{ type: type });
			}
		}
	}
}());