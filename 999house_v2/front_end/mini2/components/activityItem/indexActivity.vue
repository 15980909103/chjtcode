<template>
	<view>
		<view class="active_conten">
			<view class="user_box">
				<view class="user_info">
					<image class="user_img" mode="widthFix" v-if="infos.user_info&&infos.user_info.user_avatar" :src="infos.user_info.user_avatar"></image>
					<view class="user_name" >
						<view class="name" style="margin-bottom: 15rpx;" v-if="infos.user_info">{{infos.user_info.user_name}}</view>
						<view class="identity" v-if="infos.is_receive_coupon==0" style="color:#666666 ;"><icon type="success" size="13" color="#666666"/>已核销</view>
						<view class="identity" v-else-if="infos.is_follw==1&&infos.is_receive_coupon==1"><icon type="success" size="13" color="#03B272"/>资格用户</view>
						<view class="identity" v-else-if="infos.is_follw==0" style="color:#666666 ;"><icon type="cancel" size="13" color="#666666"/>未获得资格</view>
					</view>
				</view>
				<view class="tab_box">
					<view class="tab_item" @click="goPages('/pages/activitySount/record?activity_id='+id)">
						<image src="../../static/activity/bill.png"></image>
						优惠账单
					</view>
					<view class="tab_item" @click="showKeFu = true">
						<image src="../../static/activity/gain.png"></image>
						获取资格
					</view>
				</view>
			</view>
			<view class="explain">
				<image src="../../static/activity/explain.png"></image>
				优惠资格说明：关注公众号且活动期间未使用
			</view>
			<view>
				<swiper class="advertising" v-if="infos.adv_list&&infos.adv_list.length>0">
					<swiper-item v-for="item in infos.adv_list">
						<image :src="$api.imgDirtoUrl(item.imghurl[0].url)"></image>
					</swiper-item>
				</swiper>
			</view>
			<!-- <view class="advertising" v-if="infos.adv_list&&infos.adv_list.length>0" >
				<image :src="$api.imgDirtoUrl(infos.adv_list)"></image>
			</view> -->
			<view class="coupon_box">
				<view class="coupon"  v-for="(item,index) in infos.coupon_list">
					<view class="time">优惠时间：{{index}}</view>
					<view class="coupon_item" v-for="coponitem in item" @click="goto(coponitem)">
						<image class="shop_img" :src="$api.imgDirtoUrl(coponitem.shop_img)"></image>
						<view class="coupon_shop">
							<view class="shop_title">{{coponitem.shop_name}}</view>
							<view class="shop_type">{{coponitem.shop_type_string}}</view>
							<view class="coupon_type">{{coponitem.shop_lable_string}}</view>
						</view>
						<view class="discounts">
							<view class="discounts_number">{{coponitem.coupon_describe}}</view>
							<view class="discounts_Percentage">剩余：{{ (coponitem.coupon_num/(coponitem.sum||1)||0)*100}}%</view>
							<view class="line_progress">
								<view class="progress" :style="{width: coponitem.coupon_num/(coponitem.sum||1)*100+'%'}"></view>
							</view>
							<!-- <view class="discounts_amount">剩余500位</view> -->
						</view>
					</view>
				</view>
				<u-empty style="min-height: 100vh;" v-if="infos.coupon_list&&infos.coupon_list.length<=0" text="暂无数据" mode="list"></u-empty>
			</view>
		</view>
		<view class="kefu-box">
			<u-modal v-model="showKeFu" @confirm="confirmTips" title='活动提示' :mask-close-able="true" close-on-click-overlay :show-cancel-button="false" confirm-color="#3EB88B">
				<view class="h4_title">长安二维码，关注公众号参与活动</view>
				<image show-menu-by-longpress v-if="infos.act_inof" :src="$api.imgDirtoUrl(infos.act_inof.gzh_qrcode)" />
				<view class="activity_title">活动资格说明：</view>
				<view class="kefu-tips">① 需要关注公众号 <span style="font-size: 0;width: 80rpx;"></span></view>
				<view class="kefu-tips" style="padding-bottom: 44rpx;">② 活动期间只能使用一次</view>
			</u-modal>
		</view>
		
	</view>
</template>

<script>
	import { formatDate } from '@/utils/timedate';
	import httpInterceptor from '@/utils/http.interceptor.js';
	
	var jweixin = require('utils/jweixin-module.js')
	export default {
		data() {
			return {
				infos:{},
				showKeFu:false,
				timer: '',
			}
		},
		props: {
			id: {
				type: [String, Number],
				default() {
					return 0
				}
			},
		},
		onShow() {
			//this.activityinfo()
		},
		mounted() {
			//#ifdef H5
			this.$api.wxLogin();
			//#endif
			
			if(httpInterceptor.getToken()){
				this.activityinfo()
			}else{
				uni.$on('h5login', ()=>{
					this.activityinfo()
				})
			}
			// this.timer = setInterval(this.onshow,500)
			// this.activityinfo()
		},
		onNavigationBarButtonTap(e){
			console.log(e)
		},
		methods:{
			formatDate(time) {
				time = time * 1000
			  let date = new Date(time)
			  return formatDate(date, 'yyyy-MM-dd hh:mm:ss')
			},
			// 获取微信sdk参数
			wxsdk(){
				this.$http.post(
					'/public/getWebInfo',
					{
						city_no:this.infos.act_inof.region_no,
						url:window.location.href
					}
				).then( res=>{
					if(res.code==1){
						this.wxsdkinfo = res.data
						this.shartclick()
					}else{
						this.$toast(res.msg);
					}
				})
			},
			shartclick() {
				var that = this
				jweixin.config({
					debug: false,
					appId: this.wxsdkinfo.appid,
					timestamp: this.wxsdkinfo.timestamp,
					nonceStr: this.wxsdkinfo.noncestr,
					signature: this.wxsdkinfo.signature,
					jsApiList: ['updateAppMessageShareData']
				});
				this.$http.post(
					'/ActivityCoupon/getShareIinfo',
					{
						activity_id:this.id
					}
				).then(res=>{
					if(res.code ==1){
						jweixin.ready(function () {   //需在用户可能点击分享按钮前就先调用
						  jweixin.updateAppMessageShareData({ 
						    title: res.data.share_title||'九房网&新华都超市丨满减优惠来啦，快戳进来！', // 分享标题
						    desc: res.data.share_dec||'2万张优惠券派送中，快来领取啦~', // 分享描述
						    link: window.location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
						    imgUrl: that.$api.imgDirtoUrl(res.data.share_ico), // 分享图标
						    success: function () {
						      // 设置成功
						    }
						  })
						});
					}
				})
			},
			activityinfo(){
				this.$http.post(
					'/ActivityCoupon/index',
					{
						activity_id:this.id
					}
				).then( res=>{
					if(res.code==1){
						this.infos = res.data
						this.infos.adv_list&&this.infos.adv_list.forEach((item,index)=>{
							item['imghurl'] = JSON.parse(item.cover_path)
							console.log(JSON.parse(item.cover_path))
						})
						console.log('infos',this.infos)
						// clearInterval(this.timer)
						this.wxsdk()
						if(this.infos.user_info.user_name == null &&this.infos.user_info.user_avatar == null){
							this.$api.localStore.localDel('token')
							sessionStorage.removeItem("token")
							this.$api.wxLogin();
						}
					}else{
						this.$toast(res.msg);
						console.log('wxLogin')
						if(res.code == 50008){
							this.$api.wxLogin(1)
						}
					}
				})
			},
			goto(coponitem){
				var timestamp=new Date().getTime()
				if(timestamp<coponitem.start_time*1000){
					this.$toast('当前活动未开始，敬请期待！');
					return false
				}
				// if(timestamp>coponitem.end_time*1000){
				// 	this.$toast('当前活动已经结束了哦！');
				// 	return false
				// }
				this.checkQualifications(coponitem)
				// if(this.infos.is_follw == 1){
				// 	if(this.infos.is_receive_coupon == 1){
				// 		this.goPages('/pages/activitySount/activityDetails?coupon_id='+ coponitem.cop_id+'&activity_id='+this.id)
				// 	}else{
				// 		this.$toast('您的活动名额已用完，敬请期待下次活动哦');
				// 	}
				// }else{
				// 	this.$toast('暂无优惠资格，请关注公众号后重试');
				// }
			},
			checkQualifications(coponitem){
				let activity_id = this.id
				this.$http.post(
					'/ActivityCoupon/checkQualifications',
					{
						activity_id:this.id
					}
				).then( res=>{
					if(res.code == 1){
						// if(res.data.is_follw == 1){
						// 	if(res.data.is_receive_coupon == 1){
								// this.checkQualifications()
								this.goPages('/pages/activitySount/activityDetails?coupon_id='+ coponitem.cop_id+'&activity_id='+ activity_id)
						// 	}else{
						// 		this.$toast('您的活动名额已用完，敬请期待下次活动哦');
						// 	}
						// }else{
						// 	this.$toast('暂无优惠资格，请关注公众号后重试');
						// }
					}
				})
			},
			goPages(item){
				// this.goPage(item)
				uni.navigateTo({
					url: item,
					fail: function(res){
						if(res){
							console.log(res);
							uni.switchTab({
								url: item
							})
						}
					}
				});
			},
			confirmTips(e){
				//if(sessionStorage.getItem('token')&&(this.infos&&this.infos.is_follw==0)){
				if(httpInterceptor.getToken()&&(this.infos&&this.infos.is_follw==0)){	
					this.activityinfo()
				}
			}
		}
	}
</script>

<style lang="scss">
	 @import './indexActivity.scss';
</style>
