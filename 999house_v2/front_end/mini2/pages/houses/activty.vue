<template>
	<div>
		<div class="box">
			<div class="nav-apply">
				<div class="nav-apply-title van-ellipsis">
					{{ apply.title }}
				</div>
				<div class="nav-apply-box">
					<div class="nav-apply-left">
						<span class="nav-apply-tip">
							{{ apply.tip }}
						</span>
						<div>
							<span class="nav-apply-day">
								距离结束剩余{{ apply.day }}天
							</span>
							<span class="nav-apply-people">
								{{ apply.people }}人已报名
							</span>
						</div>
					</div>
				</div>
			</div>
			<u-button
				:custom-style="{
					width: '100%',
					height: '88rpx',
					borderRadius: '4rpx',
					color: '#fff',
					background: 'linear-gradient(90deg, #FFA640 0%, #FE8D35 100%)',
				}"
				@click.stop="applyShow"
			>
				立即报名
			</u-button>
			
			<div class="rule">
				<span class="rule-title" v-if="apply.desc">
					报名须知
				</span>
				<div class="rule-text" v-html="apply.desc"></div>
			</div>
		</div>
		
		<u-modal
			v-model="showApply" 
			title="报名活动" 
			:content="applyContent" 
			show-cancel-button 
			confirm-color="rgb(254, 130, 30)"
			@confirm="sureApply"
		>
		</u-modal>
	</div>
</template>

<script>
	
	export default {
		data() {
			return {
				option: '',
				showApply: false,
				apply: {
					id: -1,
					title: '',
					tip: '',
					day: '',
					people: '',
					desc: '',
					share_title: '',
					share_desc: '',
					share_img: '',
					is_sign: false
				},
				applyContent: '',
			}
		},
		onLoad(option) {
			this.option = option;
			this.getInfo();
		},
		methods: {
			getInfo() {
				const data = {
					id: this.option.id
				};
				
				this.$http.post( 'estates/getInfo', data ).then( res=>{
					let data = res.data;
					const apply = {};
						
					apply.id = data.sign_up.id;
					apply.title = data.sign_up.name;
					apply.tip = data.sign_up.subname;
					apply.day = data.sign_up.left_day;
					apply.desc = data.sign_up.desc;
					apply.people = data.sign_up.join_num;
					apply.share_title = data.sign_up.share_title;
					apply.share_desc = data.sign_up.share_desc;
					apply.share_img = this.$api.imgDirtoUrl(data.sign_up.share_img);
					apply.is_sign = data.is_sign ? data.is_sign:false;
					
					this.apply = apply;
				})
			},
			// 报名
			applyShow() {
				if(this.apply.is_sign == true){
					this.$toast('您已报名成功')
					return;
				}
				
				let phone = '';
				let that = this;
				
				// #ifdef MP-WEIXIN
					if( !this.isLogin() ){
						uni.showModal({
							title: '提示',
							content: '抱歉,请先授权登录',
							confirmColor: 'rgb(254, 130, 30)',
							success: function(res){
								that.goPage('authorize/index');
							}
						})
						
						return;
					}

					phone = getApp().globalData.userInfo.phone;
					this.applyContent = `报名活动后将有专人联系您 手机号码${phone}`;
					this.showApply = true;
				// #endif
				// #ifdef H5
					this.$toast('h5版本未迁移')
				// #endif
			},
			sureApply() {
				const obj = {
					mobile: getApp().globalData.userInfo.phone,
					sign_id: this.apply.id,
					type: 1
				}
							
				let active = this.option.active_id;
				
				if(active){
					obj.active_id =active
				}
				// console.log('9232')
				this.$http.post( 'signUp/add', obj ).then( res=>{
					if( res.code == 0 ){
						this.$toast(res.msg);
						return;
					}
					
					let data = res.data;
					
					this.$toast('报名成功');
					this.is_sign = true;
				})
			},
		}
	}

</script>

<style lang="scss" scoped>
	.box{
		width: 100%;
		box-sizing: border-box;
		padding: 0 32rpx;
	}
	
	.nav-apply{
		width: 100%;
		height: 178rpx;
		box-sizing: border-box;
		padding: 24rpx 30rpx 15rpx;
		margin: 40rpx 0 48rpx;
		background-color: rgba(255, 249, 242, 1);
	}
	
	.nav-apply-title{
		font-size: 26rpx;
		font-weight: 800;;
	}
	
	.nav-apply-box{
		display: flex;
		justify-content: space-between;
		align-items: flex-end;
	}
	
	/deep/ .u-hairline-border{
		border: none !important;
	}
	
	.nav-apply-tip{
		font-size: 26rpx;
		color: rgba(173, 173, 173, 1);
		margin: 16rpx 0;
	}
	
	.nav-apply-box{
		font-size: 22rpx;
		color: rgba(173, 173, 173, 1);
	}
	
	.nav-apply-day{
		color: rgba(252, 77, 57, 1);
		margin-right: 20rpx;
	}
	
	.rule-title{
		font-size: 38rpx;
		font-weight: 800;;
		margin: 100rpx 0 20rpx;
	}
	
	.rule-text{
		font-size: 30rpx;
		line-height: 56rpx;
	}
</style>
