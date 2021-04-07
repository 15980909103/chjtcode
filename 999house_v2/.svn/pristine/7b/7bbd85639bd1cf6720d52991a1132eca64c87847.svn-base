<script>
	const host = "https://act.999house.com";//dev
	const socket_host = "ws://act.999house.com/wbsocket";
	
	// const host = "https://mo.999house.com";//pro
	// const socket_host = "ws://mo.999house.com/wbsocket";
	
	// const host = "http://999house.test.com";
	// const socket_host = "ws://www.work.com/wbsocket";
	
	import {getUserLocationCity,localStore,getAllCitys} from './utils/module/mapLocation.js';
	let that = null;
	export default {
		globalData: {
			// 腾讯地图key
			map_key: '2YQBZ-V22W3-QWE3V-3G2KA-YWVK5-X4BZR',
			city_no: '350200', 
			city_name: '厦门', 
			userInfo: null,
			token: "", //用于访问
			sid: "",//用于访问
			host: host,
			h5Host: host+"/9house/pages",
			host_api: host+"/miniwechat", 
			host_h5_api:host+"/index",
			imgHost: host+"/9house/static",
			constList: {},
			// 小程序胶囊位置
			menuInfo: '',
			// socket重连
			socketCount: 0,
			
			whitePages: [
			  "pages/index/index",
			  "pages/index/location",
			  "pages/authorize/index",
			  "pages/discover/index",
			  "pages/new_house/index",
			  "pages/index/find_house",
			  "pages/houses/index",
			  "pages/houses/info",
			  "pages/houses/sand",
			  "pages/houses/banner_more",
			  "pages/houses/loan",
			  "pages/houses/loan_result",
			  "pages/new_house/good_house",
			  "pages/discover/news_detail",
			  "pages/index/search",
			  "pages/discover/search",
			  "pages/my/index",
			  "pages/map/my_map",
			  "pages/activitySount/index",
			  "pages/activitySount/activityDetails",
			  "pages/activitySount/verification",
			  "pages/activitySount/record",
			]
		},
		onLaunch: function() {
			that = this;//全局赋值，避免其他页全局调用方法时页面this丢失
			Promise.all([
				// #ifdef MP-WEIXIN
				getUserLocationCity(that),
				// #endif
				that.getConst(),
				// #ifdef MP-WEIXIN
				that.wxAuthLogin(0),
				// #endif
			]).then((res)=>{
				//console.log(res,'Launch')
				that.getCurrentCity({
					city_no: res[0].city_no,
					city_name: res[0].city_name,
				})
				if(res[2]&&res[2].info){
					that.globalData.userInfo = res[2].info;
					that.$api.getUserInfoByCache(res[2].info);
					that.creatSocket();
				}
				
				uni.$emit('myLaunched', 'ok');//执行onLaunched事件
			}).catch((err)=>{
				console.log(err)
				//reject(err);
			})
			
			// #ifdef MP-WEIXIN
				that.globalData.menuInfo = uni.getMenuButtonBoundingClientRect();
			// #endif
			
			uni.$once('loginOk', that.creatSocket);
		},
		onShow: function() {
			
		},
		onHide: function() {
			console.log('App Hide')
		},
		methods:{
			connectSocket(call) {
				const that = this;
				
				uni.connectSocket({
				    url: socket_host + `?token=${ this.globalData.token }&type=index`,
				    success: (res)=>{
						console.log(res)
						
						call&&call();
					}
				});
			},
			creatSocket(call) {
				const that = this;
				
				this.connectSocket(call);
				
				uni.onSocketError(function (res) {
					console.log('WebSocket连接打开失败，请检查！');
					
					if( that.globalData.socketCount < 3 ){
						that.globalData.socketCount += 1;
						
						that.connectSocket(call);
					} else {
						console.log('WebSocket连接打开失败，请检查！');
					}
				});
				
				uni.$on('socketErr', ()=>{
					uni.connectSocket({
					    url: socket_host + `?token=${ this.globalData.token }&type=index`
					});
				})
				
				uni.onSocketMessage((res)=>{
					// console.table(res);
					console.log('收到服务器内容：', JSON.parse(res.data));
					
					const data = JSON.parse(res.data);
					
					if( !data.type ) return;
					
					if( data.type == 'ping' ){
						uni.sendSocketMessage({
							data: JSON.stringify({ type: 'ping' })
						});
					}else if( data.type == 'say' ){
						// 更新外部会话
						const obj = {
							id: data.returndata.chat_dialogue_id,
							type: data.returndata.type
						}
						
						if( data.returndata.type == 1 ){
							obj.val = data.returndata.msg;
						} else {
							obj.val = data.returndata.msg_url;
						}
						
						uni.$emit('some_talk', obj);
						
						// 更新会话详情
						const say = {
							chat_dialogue_id: data.returndata.chat_dialogue_id,
							msg: data.returndata.msg,
							msg_type: data.returndata.type,
							msg_url: data.returndata.msg_url,
							send_user_id: data.sendUserInfo.id,
							send_head: that.$api.imgDirtoUrl(data.sendUserInfo.user_avatar),
							send_name: data.sendUserInfo.user_name,
							to_name: data.returndata.user.user_name,
							tu_head: that.$api.imgDirtoUrl(data.returndata.user.user_avatar)
						}
						
						uni.$emit('some_talk_detail', say);
						
						uni.vibrateLong();
					}else if( data.type == 'systemmsg' ){
						
						const info = {
							type: data.returndata.chat_type,
							id: data.returndata.id,
							title: data.returndata.title,
							cover: data.returndata.cover,
						}
						
						if( data.returndata.chat_type == 1 ){
							info.sub_context = data.returndata.sub_context
						}else if( data.returndata.chat_type == 4 ){
							info.is_cover = data.returndata.is_cover;
							info.name = data.returndata.name;
							info.estate_id = data.returndata.estate_id;
						}
						
						uni.$emit('system_msg', info);
						
					}else if( data.type == 'not_read_count' ){	//	初始连接时获取未读
						if( data.data.total_count == 0 ){
							uni.removeTabBarBadge({
								 index: 2
							})
						} else {
							uni.setTabBarBadge({
							  index: 2,
							  text: String(data.data.total_count)
							})
						}
					} else {
						// console.log(data,88888)
						if( code != 1 ){
							uni.showToast({
								title: data.msg,
								icon: 'none'
							})
						}
					}
					// console.log(data,777);
					
				});
			},
			getCurrentCity(val=''){
				if(val){
					that.globalData.city_no = val.city_no;
					that.globalData.city_name = val.city_name;
					return val;
				}else{
					return {
						city_no: that.globalData.city_no,
						city_name: that.globalData.city_name
					}
				}
			},
			getUserLocationCity(){
				return getUserLocationCity(that);
			},
			getAllCitys(){
				return getAllCitys(that);
			},
			logout(){
				that.globalData.userInfo = null;
				that.$api.getToken(null);
				that.$api.getSid(null);
			},
			wxAuthLogin(refresh=1){
				let token = that.$api.getToken();
				let userInfo = that.$api.getUserInfoByCache()
				if(refresh==1||!token||!userInfo){
					return that.$api.wxMiniAuthLogin(that);
				}else{
					//走缓存
					return new Promise((resolve)=>{
						resolve({
							token,
							info: userInfo
						})
					}) 
				}
			},
			getConst(refresh=0){
				let tag = localStore.localGet('u-tag');
				if(!tag||refresh==1){
					return new Promise((resolve)=>{
						// 获取公共常用列表
						that.$http.post('estates/getConst').then((res)=>{
							if(res.code==1&&res.data){
								resolve(res.data)
								localStore.localSet('u-tag',res.data, 3600*2)
								that.globalData.constList = res.data;
							}else{
								resolve('')
								that.globalData.constList = {};
							}
						})
					})
				}else{
					return new Promise((resolve)=>{
						resolve(tag)
						that.globalData.constList = tag;
					})
				}
			},
		},
	}
</script>
 
<style lang="scss">
	/*每个页面公共css */
	@import "uview-ui/index.scss";
	@import '@/common/css/iconfont.css';
</style>
