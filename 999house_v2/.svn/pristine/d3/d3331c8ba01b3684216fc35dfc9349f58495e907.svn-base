<template>
	<view style="background-color: #F5F5F5;">
		<tabs name="优惠账单" :id='activity_id'></tabs>
		<view class="record_center">
			<view v-if="array.length>0" @click="showin=true">{{array[index]}}<u-icon style="margin-left: 16rpx;vertical-align: 10%" name="arrow-down-fill" size="16"></u-icon></view>
			<!-- <picker v-if="array.length>0" @change="bindPickerChange" :value="index" :range="array"> -->
			<u-picker mode="selector" :range="array" v-model="showin" @confirm='getconfirm' :default-selector="[index]"></u-picker>
			<!-- <picker  @change="bindPickerChange" :value="index" :range="array">
				<view class="uni-input">{{array[index]}}</view>
			</picker> -->
			<view class="coupon_box">
				<view class="coupon" v-for="dates in infos" >
					<view class="coupon_item" v-for="item in dates">
						<view class="item_conter">
							<image class="shop_img" :src="$api.imgDirtoUrl(item.shop_img)"></image>
							<view class="coupon_shop">
								<view class="shop_title">{{item.shop_name}}</view>
								<view class="shop_type">{{item.shop_type_string}}</view>
								<view class="coupon_type">{{item.shop_lable_string}}</view>
							</view>
							<view class="discounts">
								<view class="discounts_number">{{item.coupon_describe}}</view>
								<!-- <view class="discounts_amount">剩余500位</view> -->
							</view>
						</view>
						<view class="verification_time">核销时间：{{item.write_off_time | formatDate}}</view>
					</view>
					
					<image class="state" src="../../static/activity/succeed.png"></image>
				</view>
				<u-empty style="min-height: 100vh;" v-if="infos.length<=0" text="暂无数据" mode="list"></u-empty>
			</view>
		</view>
	</view>
</template>

<script>
	import { formatDate } from '@/utils/timedate'
	var jweixin = require('utils/jweixin-module.js')
	import tabs from '@/components/activityItem/tab.vue'
	export default {
		data() {
			return {
				array: [],
				index: 0,
				activity_id:'',
				infos:[],
				showin:false,
				ueseinfo:{}
			};
		},
		filters: {
			formatDate(time) {
				time = time * 1000
			  let date = new Date(time)
			  return formatDate(date, 'yyyy-MM-dd hh:mm:ss')
			}
		  },
		  components:{
		  	tabs
		  },
		onLoad(e) {
			this.activity_id = e.activity_id
			
			//#ifdef H5
			this.$api.wxLogin();
			//#endif
			
			if(this.getToken()){
				this.activitylist()
			}else{
				this.activitylist()
			}
		},
		// onShow() {
		// 	this.activitylist()
		// },
		// mounted() {
		// 	this.activitylist()
		// },
		methods:{
			// 获取微信sdk参数
			wxsdk(){
				console.log('this.ueseinfo',this.ueseinfo)
				this.$http.post(
					'/public/getWebInfo',
					{
						city_no:this.ueseinfo.region_no,
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
				console.log(this.wxsdkinfo)
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
			activitylist(){
				
				this.$http.post(
					'/ActivityCoupon/getQualificationsRecord',
					{
						activity_id:this.activity_id,
						date: this.array[this.index] || ''
					}
				).then( res=>{
					if(res.code==1){
						this.array = []
						
						this.ueseinfo = res.data.activity_info
						this.infos = res.data.list
						
						for( let i in res.data.list){
							console.log(res.data.list[i])
							this.array.push(i)
						}
						this.wxsdk()
						uni.stopPullDownRefresh()
						console.log(this.array)
					}else{
						this.$toast(res.msg);
						if(res.code == 50008){
							this.$api.wxLogin(1)
						}
					}
				})
			},
			 bindPickerChange: function(e) {
				console.log('picker发送选择改变，携带值为', e.target.value)
				this.index = e.target.value
				this.activitylist()
			},
			getconfirm(e){
				this.index = e[0]
				this.activitylist()
			}
		}
	}
</script>

<style lang="scss">
	@import './record.scss';
</style>
