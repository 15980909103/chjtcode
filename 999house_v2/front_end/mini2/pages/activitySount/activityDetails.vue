<template>
	<view style="background-color: #F5F5F5;" class="content">
		<tabs name="优惠券" :id='activity_id'></tabs>
		<view class="discount_box" >
			<view class="discount_conter">
				<view class="shop_selet">
					{{infos.info&&infos.info.shop_name}}
				</view>
				<view class="coupon_name">{{infos.activity_info&&infos.activity_info.name}}</view>
				<view class="coupon_number">{{infos.info&&infos.info.coupon_describe}}</view>
				<view class="progress" v-if="infos.info">
					<view class="discounts_Percentage" >剩余：{{ (infos.info.coupon_num/(infos.info.sum||1)||0)*100}}%</view>
					<view class="line_progress">
						<view class="progress" :style="{width: (infos.info.coupon_num/(infos.info.sum)||0)*100+'%'}"></view>
					</view>
				</view>
				<view class="qrcode" >
					<view class="noshowimg" v-if="infos.is_follw==0||infos.is_receive_coupon==0">
						<tki-qrcode cid="qrcode2" ref="qrcode2" val="暂无数据" :size="size" :onval="onval" :loadMake="loadMake" :usingComponents="true" @result="qrR" />
						<view class="texts">无资格用户</view>
					</view>
					<view class="aaa" v-else-if="ifShow" style="position: relative;">
						<tki-qrcode  cid="qrcode2" ref="qrcode2" :val="infos.url" :size="size" :onval="onval" :loadMake="loadMake" :usingComponents="true" @result="qrR" />
						<view style="position: absolute;z-index: 100;top: 0;left: 0;width: 100%;height: 100%;opacity: 0;"></view>
					</view>
					
					<!-- <image src="../../static/activity.png"></image> -->
				</view>
				<u-cell-group :border="false" v-if="infos.info">
					<u-cell-item style="padding: 2px 17px" icon="clock" :border-bottom="false" :border-top="false" :arrow="false" :title="`有效期：`+ infos.info.start_time+` - `+infos.info.end_time"></u-cell-item>
					<u-cell-item style="padding: 2px 17px 15px" icon="error-circle-fill" :arrow="false" title="单次微信用户活动期间内限参与1次"></u-cell-item>
					<u-cell-item style="padding: 15px 17px" title="活动咨询" :arrow="true" @click="showKeFu=true"></u-cell-item>
				</u-cell-group>
				
				<!-- <view class="advertising" v-if="infos.adv_list&&infos.adv_list.length>0">
					<image :src="$api.imgDirtoUrl(infos.adv_list)"></image>
				</view> -->
			</view>
			<view style="margin: 30rpx 0;">
				<swiper class="advertising" v-if="infos.adv_list&&infos.adv_list.length>0">
					<swiper-item v-for="item in infos.adv_list">
						<image :src="$api.imgDirtoUrl(item.imghurl[0].url)"></image>
					</swiper-item>
				</swiper>
			</view>
			<view class="rule">
				<view class="rule_title">活动规则</view>
				<view class="rule_item" v-html="infos.activity_info&&infos.activity_info.context_rule"></view>
				<!-- <view class="rule_item">
					<span>1、活动时间：</span>
					3月1日-3月31日
				</view>
				<view class="rule_item">
					<span>2、优惠时间：</span>
					3月1日-3月31日
				</view>
				<view class="rule_item">
					<span>3、活动对象：</span>
					新华都消费的顾客（具体以店
				</view>
				<view class="rule_item">
					<span>4、活动范围：</span>
					新华都前埔门店、集美门店、
				</view>
				<view class="rule_item">
					<span>5、活动内容：</span>
					关注厦门地产情报站的新用户
				</view><view class="rule_item">
					<span>6、活动期间：</span>
					超市总优惠名额20000个，此活动，同一用户，同一微信号，每人仅限一次机会，每人活动名额有限，先到先得，送完为止。所有参加此次粉丝节主题活动的商户优惠次数、优惠名额共享
				</view>
				<view class="rule_item">
					<span>7、优惠失效：</span>
					享受优惠的交易如发生退款、撤销，则视为放弃优惠，退款金额扣除享受优惠的金额后退回至用户原支付账号；
				</view>
				<view class="rule_item">
					<span>8、</span>
					用户不允许拆单，在收银台排队一次仅可进行一次支付
				</view> -->
				<view class="qrcode_box">
					<image v-if="infos.activity_info" :src="$api.imgDirtoUrl(infos.activity_info.gzh_qrcode)"></image>
					<view class="qrcode_p">厦门地产情报站</view>
				</view>
			</view>
		</view>
		<view class="kefu-box">
			<u-modal v-model="showKeFu" title='活动提示' :mask-close-able="true" close-on-click-overlay :show-cancel-button="false" confirm-color="#3EB88B">
				<view class="h4_title">关注客服咨询活动</view>
				<image v-if="infos.activity_info" show-menu-by-longpress :src="$api.imgDirtoUrl(infos.activity_info.kf_qrcode)" />
				<!-- <view class="activity_title">活动资格</view>
				<view class="kefu-tips">① 需要关注公众号</view>
				<view class="kefu-tips">② 活动期间只能使用一次</view> -->
			</u-modal>
		</view>
	</view>
</template>

<script>
	import tkiQrcode from "@/components/tki-qrcode/tki-qrcode.vue"
	var jweixin = require('utils/jweixin-module.js')
	import tabs from '@/components/activityItem/tab.vue'
	export default {
		data() {
			return {
				index: 0,
				activity_id:'',
				coupon_id:'',
				infos:{},
				qrcodesrc:'',
				ifShow: false,
				size: 300, // 二维码大小
				onval: false, // val值变化时自动重新生成二维码
				loadMake: true, // 组件加载完成后自动生成二维码
				showKeFu:false,
			};
		},
		components: {tkiQrcode,tabs},
		onLoad(e) {
			this.coupon_id = e.coupon_id
			this.activity_id = e.activity_id
		},
		onShow() {
			//#ifdef H5
			this.$api.wxLogin();
			//#endif
			
			if(this.getToken()){
				this.couponinfo();
			}else{
				this.couponinfo();
			}
			
			// this.infos = {}
			// this.couponinfo()
		},
		// onPullDownRefresh(){
		// 	this.couponinfo()
		// },
		mounted() {
			// this.couponinfo()
		},
		methods:{
			qrR(res) {
				this.qrcodesrc = res
			},
			// 获取微信sdk参数
			wxsdk(){
				this.$http.post(
					'/public/getWebInfo',
					{
						city_no:this.infos.activity_info.region_no,
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
				console.log('分享')
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
						activity_id:this.activity_id
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
			couponinfo(){
				this.$http.post(
					'/ActivityCoupon/getCouponInfo',
					{
						activity_id: this.activity_id,
						cop_id: this.coupon_id
					}
				).then( res=>{
					if(res.code==1){
						this.infos = res.data
						this.infos.adv_list&&this.infos.adv_list.forEach((item,index)=>{
							item['imghurl'] = JSON.parse(item.cover_path)
						})
						this.ifShow = true
						this.wxsdk()
						// uni.stopPullDownRefresh()
					}else{
						this.$toast(res.msg);
						if(res.code == 50008){
							this.$api.wxLogin(1)
						}
					}
				})
			},
		}
	}
</script>

<style lang="scss"> 
	@import './activityDetails.scss';
</style>
