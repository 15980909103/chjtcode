<template>
	<view>
		<view v-if="null_show" class="list_null" style="background: #fff;height: 100vh;">
			<u-empty image="error" description="暂无数据"/>
			<!-- <img src="../../static/null.png">
			<p>暂无数据</p> -->
		</view>

		<view v-if="info_id" :style="{background: activeList_type!=11? bgColor: '#fff',minHeight: '100%'}">
			<img :src="$api.imgDirtoUrl(adTop.cover_imgs)" class="ad_top" v-if="adTop">

			<!-- 直播 -->
			<view class="live-box" v-show="activeList.live && activeList.live.length > 0">
				<vant-swipe class="live-swipe" :autoplay="3000" indicator-color="white">
					<vant-swipe-item class="live-swipe-wrap" v-for="(live,num) in activeList.live" :key="num">
						<view 
							class="live-swipe-item" 
							:style="{backgroundImage: 'url('+ liveItem.img +')'}"
							v-for="(liveItem,key) in live.list" 
							:key="key">
							<template v-if="liveItem.type == 0">
								<view class="live-swipe-tip live-swipe-tip1">
									<img :src="$api.imgDirtoUrl('/my/logo.png')">
									<span>直播中</span>
								</view>
							</template>
							<template v-else>
								<view class="live-swipe-tip live-swipe-tip2">
									<view class="live-swipe-tip2-title">
										<span class="iconfont icon17shijian"></span>预约
									</view>
									<view class="live-swipe-tip2-time">
										{{liveItem.time}}
									</view>
								</view>
							</template>
							
						
							<span class="live-swipe-title u-multi-ellipsis--l2">
								{{liveItem.title}}
							</span>
						</view>
						
					</vant-swipe-item>
				</vant-swipe>
			</view>

			<!-- 列表 -->
			<view :class="[activeList_type==11?' list-box11':'list-box']" v-if="has_tip==1">
				<view class="list-tip">
					<view class="list-tip-item" v-for="(v,key) in activeList.tip" :key="key">
						<view @click="chooseTip(key)" :class="[key == currentChoosTip ? 'tip-active' : '']">
							{{v.name}}
						</view>
					</view>
				</view>
				<view v-if="activeList.tip&&activeList.tip[currentChoosTip]&&activeList.tip[currentChoosTip].list.length">
					<common-information :list="activeList.tip[currentChoosTip].list"></common-information>
					<view class="list-end-tips">
						我是有底线的哦
					</view>
				</view>
				<view class="list_null" style="background: #fff;" v-else >
					<view v-if="list_null_show">
						<u-empty image="error" description="暂无数据"/>
						<!-- <img src="../../static/null.png">
						<p>暂无数据</p> -->
					</view>
				</view>
			</view>
			<view :class="[activeList_type==11?'list-box list-box11':'list-box']" v-if="has_tip==2">
				<view v-if="activeList.not_tip.length">
					<common-information :list="activeList.not_tip" ></common-information>
					<!-- <common-template 
						:list="activeList.not_tip" 
					> 
					</common-template>-->
					<view class="list-end-tips">
						我是有底线的哦
					</view>
				</view>
				<view class="list_null" style="background: #fff;" v-else >
					<view v-if="list_null_show">
						<u-empty image="error" description="暂无数据"/>
						<!-- <img src="../../static/null.png">
						<p>暂无数据</p> -->
					</view>
				</view>
			</view>
			
			<view id="container-user-site"></view>
		</view>
	</view>
</template>

<script>
	let app = getApp();
	import commonInformation from '@/components/common/template_information'
	import commonNewHouse from '@/components/common/template_newHouse.vue'
	export default{
		data(){
			return {
				has_tip : 0,
				info_id: 0,
				null_show: false,
				list_null_show: false,
				bgColor: 'rgba(254, 114, 71, 1)',
				adTop: {
					cover_imgs:"",
					forhref:"",
					forid:1,
					forsort:"0"
				},
				active: 0,
				currentChoosTip: 0,
				activeList_type: -1,
				activeList: {
				  live:[],
				  tip:[], //有分栏时候的列表
				  not_tip:[]//无分栏时候的列表
				},
				tipChoose: [],
			}
		},
		components:{
			commonNewHouse,
			commonInformation
		},
		onLoad(option) {
			this.id = option.id;
			this.getList();
		},
		created() {
			/* if( this.$http.publicFun() ){
				  this.tagList = $api.localGet('u-tag');
			}else{
				this.$http.publicFun(1).then(res=>{
					this.tagList = res;
				})
			} */
			// this.tabsChange();
			// const option = this.$api.funcUrlDel().option;

			// if( Object.keys(option).length > 0 ){
			// 	this.id = option.id;
			// }

			
			// this.getList();
			// this.getList();
		},
		methods:{
			getList() {
				if(!this.id){
					return
				}
				let sale_status = '';
				let opening_time = '';
				let is_cheap = '';
				let recommend = '';

				this.$http.get(
					'/subject/index', //获取专题列表
					{
						id:this.id,
						sale_status: sale_status, //销售状态
						opening_time:opening_time, // 开盘时间
						is_cheap: is_cheap,       // 是否低价盘
						recommend: recommend,
					},
				).then(res=>{
					if(res.code ==1){
						const allTag = this.$api.localStore.localGet('u-tag');
						let that = this;
						if(!res.data||!res.data.hasOwnProperty('id')){
							this.null_show = true;
							return
						}
						this.info_id = res.data.id

						// 其他信息赋值
						this.adTop = res.data.banner?res.data.banner[0]:'';// banner图
						this.bgColor = res.data.bgcolor;

						// 直播列表处理
						if(!res.data.type) {
							var count = 0;
							var newLive = [];
							let arrLive = res.data.live_room;
							arrLive.forEach(function (val,key) {
								if(!newLive[count]){
									newLive[count] = {
										list: []
									}
								}
								if(newLive[count].list.length<2){
									let tmpLive = {
										type: val.status,
										img: val.cover,
										title: val.room_name,
										time: val.start_time
									}
									newLive[count].list.push(tmpLive)

									if(newLive[count].list.length==2){
										count++;
									}
								}
							})
						} else {
							newLive = [];
						}
						this.activeList.live = newLive;

						// 楼盘列表处理
						let arr =  res.data.estates;
						arr.forEach( (val,key)=> {
							// 版面类型判断
							let type = '';
							if(res.data.type) {// 优惠活动样式的版面-1
								type = 11;
							} else if(res.data.type==0){// 好房推荐样式的版面-0
								if(val.video_url) {// 有无视频
									type = 9;
								} else {
									type = 10;
								}
							}
							this.activeList_type = type;

							// 标签转换
							//console.log('sale_status',this.$api.getTagsText({estatesnew_sale_status: val.sale_status}))
							let tagarr = [];
							tagarr = tagarr.concat(this.$api.getTagsText({estatesnew_sale_status: val.sale_status}));
							tagarr = tagarr.concat(this.$api.getTagsText({house_purpose: val.house_purpose}));
							let lipStr = val.feature_tag;
							if(lipStr instanceof  Array && lipStr.length >0){
								tagarr = tagarr.concat(this.$api.getTagsText({feature_tag:lipStr}));
							}
							
							//console.log('tagarr',tagarr, type)
							// 报名信息
							let img;
							let apply = {}
							if(!res.data.type) {
								img = val.imgs&&val.imgs.length?val.imgs: [val.list_cover];
							} else {
								apply = {
									title:val.sign_name,
									people:val.sign_num,
									state:0,
								}
								img = [val.list_cover];
							}
							
							arr[key]= {
								id:val.id,
								type: type,
								info: {
									name: val.name,
									tip: tagarr,
									price: val.price,
									site: val.area_str + ' ' + val.business_area_str,
									area: val.built_area,
									is_sale_status: val.sale_status?val.sale_status:0,
									is_cheap: val.is_cheap?val.is_cheap:0,
									is_recommend: val.recommend?val.recommend:0,
									is_opening_time: val.opening_time?val.opening_time:0,
									coloum_id: val.label_id
								},
								cover: val.has_cover,
								title: val.title,
								img:  img,
								url:  val.video_url,
								apply: apply
							}
						})
						//console.log(arr,111)

						//进行栏目切分
						let coloums = {};
						let currentChoosTip = '';
						if(res.data.label){
							for(let i in res.data.label){
								let item = res.data.label[i];
								coloums[item.id] = { 
									name: item.name,
									list:[]
								}
								this.has_tip = 1;////有栏目
								if(!currentChoosTip){
									currentChoosTip = item.id
								}
							}
						}
						
						if(this.has_tip==1){
							this.activeList.tip = coloums;
							this.currentChoosTip = currentChoosTip;		
						}
		
						arr.map((item)=>{
							if(!item.info){
								return;
							}
							if(this.has_tip ==1 ){
								let coloum_id = item.info.coloum_id;
								if(coloum_id&&this.activeList.tip[coloum_id]){
									this.activeList.tip[coloum_id].list.push(item)
								}
							}else{
								this.activeList.not_tip.push(item)
							}
						});
						console.log(this.activeList)
						if(this.has_tip !=1){
							this.has_tip = 2;//无栏目
						}
						
						this.list_null_show = true
					}else{
						// this.$toast(res.msg);
					}
				}).catch(res=>{
					// this.$toast(res.msg);
				});
			},

			// 切换资讯/新房
			tabsChange() {
				// this.activeList = this.tabList[this.active];
			},
			chooseTip(val) {
				this.currentChoosTip = val
			},
		},
	}
</script>

<style>
	@import url("./good_house.css");
</style>
