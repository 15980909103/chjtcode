<template>
	<view >
		<!-- <view class="set">
			<span class="iconfont iconsaoma"></span>
			<span class="iconfont iconkefu" @click='showKeFu=true'></span>
			<span class="iconfont iconshezhi"></span>
		</view> -->
		<view class="my">
			<view class="info" @click="changeInfoShow">
				<view class="info-left">
					<image v-if='uInfo&&uInfo.headimgurl' :src="uInfo.headimgurl">
					<image v-else :src="$api.imgDirtoUrl('/my/touxiang.png')">
					<view class="info-content-box">
						<span class="info-name">{{ uInfo.nickname }}</span>
						<span class="info-phone">{{ $api.encryptPhone(uInfo.phone) }}</span>
					</view>
				</view>
				<view class="info-right">
					<!-- <i class="iconfont iconerweima"></i> -->
					<!-- <u-icon name="arrow" /> -->
					<u-icon name="arrow-right" />
				</view>
			</view> 
		
			<view class="tab">
				<view class="tab-item" @click="goPage('/pages/my/my_history')">
					<view>
						<image mode="widthFix" :src="$api.imgDirtoUrl('/my/history.png')"></image>
					</view>
					<span>浏览记录</span>
				</view>
				<view class="tab-item" @click="goPage('/pages/my/my_focus')">
					<view>
						<image mode="widthFix" :src="$api.imgDirtoUrl('/my/like.png')">
					</view>
					<span>我的关注</span>
				</view>	
				<!-- <view class="tab-item" @click='showKeFu=true'>
					<view>
						<image mode="widthFix" :src="$api.imgDirtoUrl('/my/service.png')">
					</view>
					<span>客服中心</span>
				</view>	 -->
				<view class="tab-item" @click="goPage('/pages/houses/loan')">
					<view>
						<image mode="widthFix" :src="$api.imgDirtoUrl('/my/calculator.png')">
					</view>
					<span>房贷计算</span>
				</view>	
				<!-- <view class="tab-item">
					<view>
						<image src="../../static/my/deal.png">
					</view>
					<span>交易报告</span>
				</view>
				<view class="tab-item">
					<view>
						<image src="../../static/my/sale.png">
					</view>
					<span>优惠券</span>
				</view> -->
			</view>
		
			<image v-if="ad.img" :src="$api.imgDirtoUrl(ad.img)" @click='goAd(ad)' class="ad">
		
			<view class="common">
				<span class="common-title">常用模块</span>
				<view class="common-box">
					<view class="common-item" :style="{ backgroundImage: 'url(https://act.999house.com/9house/static/my/common.png)' }" @click="goFindHouse">
						<span class="common-item-title">买房方案</span>
						<span class="common-item-text">智能匹配，精准推送</span>
					</view>
				</view>
			</view>
			
			<!-- <view class="tool">
				<span class="common-title">我的工具</span>
				<view class="tool-box">
					<view class="tool-item" @click="goPage('discover/index.html',{active:'研究院'})">
						<i class="iconfont iconfangjia"></i>
						<span>查房价</span>
					</view>
					<view class="tool-item" @click="goPage('/pages/houses/loan')">
						<i class="iconfont iconbianzu141"></i>
						<span>房贷计算</span>
					</view>
					<view class="tool-item">
						<i class="iconfont iconhuida-2"></i>
						<span>我的回答</span>
					</view>
					<view class="tool-item">
						<i class="iconfont iconbianzu162"></i>
						<span>我的点评</span>
					</view>
					<view class="tool-item">
						<i class="iconfont icontiezi-copy"></i>
						<span>我的帖子</span>
					</view>
				</view>
			</view> -->
		</view>
		<u-cell-group style="padding-left: -32rpx;padding-right: -32rpx;margin-bottom: 100rpx;color: #000000;font-size: 30rpx;">
			<u-cell-item title="客服中心" @click='showKeFu=true'></u-cell-item>
			<u-cell-item title="九房公众号" @click='tencentIsux=true'></u-cell-item>
		</u-cell-group>
		<!-- 底部栏 -->
		<!-- <common-tabbar active="我的"></common-tabbar> -->
		
		<!-- 个人信息 -->
		<u-popup v-model="infoShow" class="info-wrap" mode="bottom" custom-style=" height: '100%'" :overlay="false"
		 :closeable="true" close-icon-position="top-left">
		 <view style="height: 100vh;">
			<view class="info-title">
			 	个人信息
			 </view>
			 <view class="info-content">
			 	<u-cell-group>
			 		<u-cell-item size="large" title="头像" is-link @click="changeInfo(1)">
			 			<image class="uesr_image" v-if='uInfo&&uInfo.headimgurl' :src="uInfo.headimgurl">
			 			<image class="uesr_image" v-else :src="$api.imgDirtoUrl('/my/touxiang.png')">
			 		</u-cell-item>
			 		<u-cell-item size="large" title="昵称" :value="uInfo.nickname" is-link @click="changeInfo(2)"></u-cell-item>
			 		<u-cell-item size="large" title="手机号" :value="$api.encryptPhone(uInfo.phone)" :arrow="false"></u-cell-item>
			 	</u-cell-group>
			 </view>
		 </view>

		</u-popup>
		
		<!-- 修改个人信息 -->
		<u-popup 
			v-model="changeShow" 
			mode="bottom" 
			:style="{ height: '100%', width: '100vw',}" 
			:overlay="false"
			:closeable="true"
			close-icon-position="top-left"
		>
			<view style="height: 100vh;background: #F7F7F7">
				<template v-if="changeShowType == 1">
					<view class="info-title">
						修改信息
					</view>
					<view class="change-box">
						<view class="change-head">
							<image v-if='uChange&&uChange.headimgurl' :src="uChange.headimgurl" ref="userHead">
							<image v-else src="../../static/my/touxiang.png" ref="userHead">
							<p>点击图片修改头像</p>
						</view>
						<view class="change-name">
							<view class="change-name-box">
								
								<!-- <span>昵称</span> -->
								<u-field class="change_field" focus="true" input-align='left' label-width="0" v-model="uChange.nickname" placeholder="请输入昵称"></u-field>
							</view>
							<!-- <p>请输入要修改的昵称</p> -->
						</view>
					</view>
					<u-upload ref="uUpload" :auto-upload="false" class="img-up" @on-list-change="headChange"></u-upload>
					<!-- <input type="file" accept="img/*" class="img-up" @change="headChange" ref="imgChange"> -->
					
					<view class="botton-logout" >
						<u-button type="warning"  size="medium" @click="sureChange">保存</u-button>
					</view>
				</template>
				<!-- <template v-if="changeShowType == 2">
					<view class="change-phone">
						<h4>{{ changePhoneText }}</h4>
						<u-field
							v-model="phoneNum"
							type="tel"
							center
							label-width="0"
							class="apply_alert_phone"
							clearable
							placeholder="请输入手机号码"
							maxlength="11"
						>
							<template>
								<u-button 
									size="small" 
									slot="right"
									class="apply_alert_msg"
									:disabled="msgDisabled"
									@click="getMsg"
								>
									{{ msgText }}
								</u-button>
							</template>
						</u-field>
						<u-field
							v-model="msg" 
							type="digit" 
							label-width="0"
							center
							clearable
							placeholder="请输入验证码"
							maxlength="6"
						>
						</u-field>
						<u-button
							class="apply_phone"
							:disabled="applyDisabled"
							@click="apply"
						>
							{{ upBtn }}
						</u-button>
					</view>
				</template> -->
			</view>
		</u-popup>
		<view class="kefu-box">
			<u-modal v-model="showKeFu" :mask-close-able="true" close-on-click-overlay :show-confirm-button='false'>
				<image show-menu-by-longpress :src="$api.imgDirtoUrl(serverCode)" />
				<view class="kefu-tips">联系客服咨询</view>
			</u-modal>
		</view>
		<view class="kefu-box">
			<u-modal v-model="tencentIsux" title="关注公众号" confirm-color="#FE821E">
				<text class="modal_text">每日及时推送厦门人居楼市、市场动态，为购房者提供服务和参考</text>
				<image show-menu-by-longpress :src="$api.imgDirtoUrl('/my/tencentIsux.jpg')" />
			</u-modal>
		</view>
		<view id="container-user-site"></view>
	</view>
</template>

<script>
	let app = getApp();
	export default {
		data() {
			return {
				showKeFu: false,
				tencentIsux:false,
				uInfo: {
					headimgurl:'',
					nickname: '登录/注册',
					phone: ''
				},
				ad: '',
				find_house_total: 0,
				// 个人信息
				infoShow: false,
				serverCode:'',
				// 修改信息
				changeShow: false,
				changeShowType: 1,
				uChange: {
					nickname: '',
					headimgurl: ''
				},
				changePhoneText: '验证当前手机号',
				msgDisabled: false,
				applyDisabled: false,
				phoneNum: '',
				msg: '',
				timeOut: 0,
				msgText: '获取验证码',
				upBtn: '验证',
			}
		},
		onLoad() {
		},
		onShow() {
			// this.myInfo()
			// this.serverCodeInfo()
			// this.getUserLocation()
			// this.isWechat();
			
			this.myInfo(1)
			this.serverCodeInfo()
			this.getAdvs();
			this.getEstatesList();
		},
		methods:{
			goAd(e){
				if(!this.$api.trim(e.href)&&e.info){
					e.href = 'houses/index?id='+e.info.estate_id+'&cover='+e.cover;
				}
				if(!e.href){
					return
				}
				this.goPage(e.href)
			},
		
			getAdvs(){
				const data = {
					falg: ['h5_my_ad'],
					city_no: this.city_no
				};
				
				this.$http.post('/adv/getAdvByFlag',
					data,
				).then( res=>{
					if(res.code==1){
						let data = res.data;
						let banners = [];
						for(let i in data.h5_my_ad){
							data.h5_my_ad[i]&&data.h5_my_ad[i].img&&banners.push({
								img: data.h5_my_ad[i].img[0],
								href: data.h5_my_ad[i].href,
								info: data.h5_my_ad[i].info,
								cover: data.h5_my_ad[i].cover,
							});
						}
						
						if(banners[0]){
							this.ad = {
								img: banners[0].img,
								href: banners[0].href,
								info: banners[0].info,
								cover: banners[0].cover,
							};
						}
					}
				})
			},
			goFindHouse(){
				if(this.find_house_total > 0){
					this.goPage('/pages/index/find_result')
				}else{
					this.goPage('/pages/index/find_house')
				}
			},
			changeInfoShow(e){
				// this.infoShow = true
				// return false
				console.log(this.isLogin())
				if(this.isLogin()){
					this.infoShow = true
				}else{
					this.goPage('authorize/index')
				} 
			},
			myInfo(refresh=0){
				if(app.globalData.userInfo&&app.globalData.userInfo.headimgurl&&refresh==0){
					return this.uInfo = app.globalData.userInfo
					this.uChange.nickname = this.uInfo.nickname
				}
				
				if(!app.globalData.token){
					return
				}
				this.$http.get(
					'/user/getInfo',
				).then( res=>{
					if(res.code==1){
						let obj = res.data;
						obj.headimgurl = this.imgDirtoUrl(obj.headimgurl);
						this.uInfo = obj;
						this.uChange.nickname = this.uInfo.nickname
						app.globalData.userInfo = obj
						
						this.$api.getUserInfoByCache(obj);
					}else{
						this.$toast(res.msg);
					}
				})
				
			},
			serverCodeInfo(){
				this.$http.get(
					'/public/serverCode'
				).then( res=>{
					if(res.code==1){
						this.serverCode = res.data
					}else{
						this.$toast(res.msg);
					}
				})
			},
			logOut(){
				let token = this.$http.getLocal('token');
				console.log(token)
				this.$http.post(
					'/public/logout',
					{token}
				).then( res=>{
					localStorage.removeItem('token');
					localStorage.removeItem('is_login');
					localStorage.removeItem('info');
					this.goPage('my/login.html');
				}).catch( res=>{
					this.$toast(res.msg);
				})
		
			},
			changeInfo( type ) {
				if( type == 1 || type == 2 ){
					this.changeShowType = 1;
					this.$set(this.uChange, 'headimgurl', this.uInfo.headimgurl);
				} else {
					this.changeShowType = 2;
					this.upBtn = '验证'
					this.changePhoneText = '验证当前手机号';
					this.msgDisabled = false;
					this.applyDisabled = false;
					this.phoneNum = '';
					this.msg = '';
				}
				
				this.changeShow = true;
			},
			headChange(e) {
				console.log(111)
				let that = this;
				for(let i in e){
					uni.getFileSystemManager().readFile({
					filePath: e[i].file.path, //选择图片返回的相对路径
						encoding: 'base64', //编码格式
						success: res => { //成功的回调
							let base64 = 'data:image/jpeg;base64,' + res.data //不加上这串字符，在页面无法显示的哦
							//console.log(base64)
							that.uChange.headimgurl = base64;
						}
					});
				}
				// var img = e.path[0].files[0]
				// var reader = new FileReader();
				
				// reader.addEventListener("load", function (){
				// 	that.$set(that.uChange, 'headimgurl', reader.result);
				// }, false);
				
				// if (img) {
				// 	reader.readAsDataURL(img);
				// }
			},
			
			sureChange() {
				const img = this.uChange.headimgurl;
				const name = this.uChange.nickname;
				const data = {};
				const dispose = (option)=>{
					this.$http.get('/user/editUserInfo',
						{...option}
					).then( res=>{
						let info = uni.getStorageSync('user_info')
						// let info = this.$api.localGet('user_info');
						
						if( !info ){
							info = {};
						}
						
						for( let i in option ){
							this.$set( this.uInfo, i, option[i] );
							
							if( i == 'nickname' ){
								info.user_name = option[i];
							} else {
								info.user_avatar = option[i];
							}
						}
						this.$api.localStore.localSet('user_info',info);
						
						
						this.changeShow = false;
						this.$toast('修改成功');
					}).catch( res=>{
						this.changeShow = false;
						this.$toast(res.msg);
					})
				}
			
				if( name.length > 0 || img ){
					if( name.length > 0 ){
						data.nickname = name;
					}
					
					if( img ){
						data.headimgurl = img;
						dispose(data);
						
						return;
					}
					
					dispose(data);
				}
			},
			urlToBase64(el) {
				console.log(el)
			    return new Promise ((resolve,reject) => {
					let image = new Image();
					image.src = el.src;
					image.setAttribute('width',el.width);
					image.setAttribute('height',el.height);
					image.onload = function() {
						let canvas = document.createElement('canvas');
						canvas.width = el.width*2;
						canvas.height = el.height*2;
						// 将图片插入画布并开始绘制
						canvas.getContext('2d').drawImage(image, 0, 0, el.width*2, el.height*2);
						// result
						let result = canvas.toDataURL('image/png',1.0)
						resolve(result);
					};
				
					// 图片加载失败的错误处理 
					image.onerror = () => {
						reject(new Error('图片流异常'));
			        };
			    })  
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
				this.$http.get('/public/sendMsg',
					{
						phone: this.phoneNum,
						sence: 'sign_up',
						is_limit: this.changeShowType-1
					},
				).then( res=>{
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
			// 验证
			apply() {
				if( this.phoneNum.length > 0 && this.msg.length == 6 ){
					let type = 0;
					
					if( this.changePhoneText == '验证当前手机号' ){
						type = 1;
					} else {
						type = 2;
					}
					
					this.$http.get(
						'/user/editUserPhone',
						{
							mobile: this.phoneNum,
							code: this.msg,
							type: type
						},
					).then( res=>{
						if( type == 1 ){
							clearInterval(this.timeOut);
							this.changePhoneText = '绑定新手机号';
							this.upBtn = '绑定'
							this.msgDisabled = false;
							this.msgText = '获取验证码';
							this.phoneNum = '';
							this.msg = '';
						} else {
							// const info = this.$api.localGet('user_info');
							// info.phone = this.phoneNum;
							// this.$api.localSet('user_info',info);
							this.$set( this.uInfo, 'phone', this.phoneNum );
							this.changeShow = false;
							this.$toast('绑定成功');
						}
						
					}).catch( res=>{
						this.$toast(res.msg);
					})
					
				} else {
					this.$toast('请正确填写信息');
				}
			},
		
		
			getEstatesList(){
				let uChoose = this.intLocalUChoose();
				
				if(Object.keys(uChoose).length==0){
					this.find_house_total = 0;
					return
				}
				
				this.$http.get(
					'/estates/getEstatesList',
					{
						...uChoose,
						page: 1,
					},
				).then( res =>{
					res = res.data
					this.find_house_total = res.total?res.total:0;
				}).catch( res=>{
					this.$toast(res.msg);
				})
			},
			intLocalUChoose(){
				
				let uChoose = this.$api.localStore.localGet('user_find_house');
				let new_uChoose = {};
				if(!uChoose||Object.keys(uChoose).length==0){
					return new_uChoose
				}
				if(uChoose.like){
					uChoose.like.map((item,index)=>{
						new_uChoose['tags['+index+']'] = item;
					})
				}
				if(uChoose.area){
					new_uChoose.built_area = uChoose.area;
				}
				if(!uChoose.site_id){
					new_uChoose.city_no = this.city_no;
				}else{
					if(typeof(uChoose.site_id)=='object'){
						uChoose.site_id.map((item,index)=>{
							if(uChoose.site_center.type=='area'){
								if(item){
									new_uChoose['business_no['+index+']'] = item;
								}
							}else if(uChoose.site_center.type=='subway'){//地铁站点
								if(item){
									new_uChoose['sites['+index+']'] = item;
									new_uChoose.city_no = this.city_no;
								}
							}
						})
					}else{
						if(String(uChoose.site_id).indexOf('p_')!=-1){//去除父级标识
							uChoose.site_id = uChoose.site_id.replace('p_','')
						}
		
						if(uChoose.site_center.type=='area'){
							new_uChoose.area_no = uChoose.site_id;
						}else if(uChoose.site_center.type=='subway'){//地铁
							new_uChoose.subway = uChoose.site_id;
							new_uChoose.city_no = this.city_no;
						}
					}
				}
				if(uChoose.budget_str&&uChoose.budget&&Number(uChoose.budget)<1000){
					new_uChoose.price_type = 'total' 
					new_uChoose.price = uChoose.budget_str
				}
				new_uChoose.no_adv = 1;
				new_uChoose.page_size = 20;
				new_uChoose.purpose = uChoose.aim;
				new_uChoose.has_num = uChoose.manyHouse;
		
				return new_uChoose
			},
		},
	}
</script>
<style>
	 @import './index.css';
</style>
