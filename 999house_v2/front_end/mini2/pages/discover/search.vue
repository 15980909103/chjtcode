<template>
	<view class="content">
		<div class="tabs">
			<div class="tabs-wrap">
				<u-tabs 
					:list="tabList" 
					:is-scroll="false"
					:current="active" 
					active-color="rgba(254, 130, 30, 1)" 
					:active-item-style="{
						fontSize: '38rpx'
					}"
					bar-trans="0"
					@change="tabChange"
				>
				</u-tabs>
			</div>
		</div>

		<block v-for="(item,index) in tabList" :key="index" :title="item.name">
			<block v-if="active==index">
				<div class="search">
					<form action="/">
						<u-search 
						v-model="value"
						:show-action="showResultBox"
						bg-color="#fff"
						placeholder-color="#ADADAD"
						:placeholder="item.placeholder"
						maxlength="16"
						shape="square"
						action-text="取消"
						@search="(val)=>{onSearch(val,1)}"
						@change="onInput"
						@custom="onClear"
						@clear="onClear"
						>	
						</u-search>
					</form>
				</div>
				
				<div class="box" v-show="!showResultBox">
					
					<div class="history" v-if="searchHistory[item.historyKey] && searchHistory[item.historyKey].length > 0">
						<view class="h4-title">历史搜索</view>
						<span class="iconfont iconlajitong" @click="delHistory"></span>
					</div>
					
					<div class="history-tip" v-if="searchHistory[item.historyKey] && searchHistory[item.historyKey].length > 0">
						<template v-for="(tip,num) in searchHistory[item.historyKey]" >
							<span :key="num" @click="tipClick(tip)" v-if="num < 11">
								{{tip}}
							</span>
						</template>
					</div>
					
					<div class="history" >
						<view class="h4-title">热门搜索</view>
					</div>
					
					<div class="history-tip">
						<span @click="goPage('index/find_house')" >
							快捷找房
						</span>
						<!-- <span @click="goPage('map/index')" >
							地图找房
						</span> -->
						<block v-if="item.id==2 && hotTip.length > 0" >
							<template v-for="(tip,num) in hotTip" >
								<span :key="num" @click="tipClick(tip)" v-if="num < 11">
									{{tip.name}}
								</span>
							</template>
						</block>
					</div>
				</div>
				<div class="result" v-show="showResultBox">
					<!-- 资讯/有料/新房 -->
					<div class="detail" v-show="showResult">
						<template v-if="resultDetail.length == 0">
							<u-empty text="暂无相关搜索" mode="search"></u-empty>
						</template>
						<template v-else>
							<common-information v-if="do_type==0" :list="resultDetail" @del="(e)=>{ resultDetail = e }"></common-information>
							<common-newhouse v-if="do_type==2" :list="resultDetail" @del="(e)=>{ resultDetail = e }"></common-newhouse>
							<u-loadmore
								:status="loadState" 
								margin-top="60"
								:load-text="{
									loadmore: '上拉加载',
									loading: '努力加载中~',
									nomore: '我是有底线的'
								}"
							/>
						</template>
					</div>
				</div>
			</block>
		</block>
	</view>
</template>

<script>
	import commonInformation from '@/components/common/template_information';
	import commonNewhouse from '@/components/common/template_newHouse';
	export default {
		data() {
			return {
				active: 0,
				value: '',
				searchHistory: {},
				sortTab: {
					0: 0,
					2: 0,
					3: 0
				},
				showResult: false,
				showResultBox: false,
				tabList: [
					{
						id: 0,
						name: '资讯',
						historyKey: 'news-search',
						placeholder: '请输入感兴趣的内容'
					},
					// {
					// 	id: 1,
					// 	name: '视频',
					// 	placeholder: '请输入感兴趣的内容',
					//  historyKey: 'video-search'
					// },
					{
						id: 2,
						name: '选好房',
						historyKey: 'house-search',
						placeholder: '请输入楼盘、地址'
					}
				],
				pid:9,
				hotTip: [],
				resultDetail: [],
				// 分页
				page: 0,
				maxPage: 1,
				loading: false,
				do_type: 0,
			}
		},
		components: {
			commonInformation,
			commonNewhouse
		},
		watch:{
			active(val){
				this.do_type = this.tabList[val].id;
			}
		},
		computed: {
			loadState() {
				if( this.loading == false ){
					if( this.page >= this.maxPage ){
						return 'nomore';
					} else {
						return 'loadmore';
					}
				} else {
					return 'loading';
				}
			}
		},
		onLoad() {
			this.searchHistory = this.uHistory('get');
					
			//获取选好房热搜词
			this.getHouseTip();
		},
		methods: {
			init() {
				this.resultDetail = [];
				this.page = 0;
				this.maxPage = 1;
				this.loading = false;
			},
			tabChange(e) {
				this.active = e
				switch (this.tabList[e].id) {
					case 0: this.pid=9;break;
					case 1: this.pid=13;break;
					case 2: this.pid=18;break;
					default:this.pid = 9 ;
				}
				this.value = '';
				this.showResult = this.showResultBox = false;
				this.init();
			},
			//获取选好房热搜词
			getHouseTip() {
				this.$http.post('estates/getSearchWords',{
					city_no: this.city_no
				}).then(res=>{
					const arr = [];
					const data = res.data;
					
					if( data.estates&&data.estates.length > 0 ){
						data.estates.map( item=>{
							const obj = {
								id: item.bind_id,
								name: item.name,
								type: 0
							}
							
							arr.push(obj);
						})
					}
					
					if( data.tags&&data.tags.length > 0 ){
						data.tags.map( item=>{
							const obj = {
								id: item.bind_id,
								name: item.name,
								type: 1
							}
							
							arr.push(obj);
						})
					}
					
					this.hotTip = arr;
				})
			},
			// 存取搜索历史
			uHistory(type) {
				if( type == 'get' ){
					const val = this.$api.localStore.localGet('u-index-search-history');
					
					if( val ){
						return val;
					} else {
						return {};
					}
				} else {
					if( type ) {
						const key = this.tabList[this.active].historyKey;
						
						if( Object.keys(this.searchHistory).indexOf(String(key)) == -1 ){
							this.$set(this.searchHistory,key,[]);
						}
						
						if( this.searchHistory[key].indexOf(type) != -1 ){
							this.searchHistory[key].splice(this.searchHistory[key].indexOf(type),1);
						}
						
						this.searchHistory[key].unshift(type);
						
						if( this.searchHistory[key].length > 11 ){
							this.searchHistory[key].length = 11;
						}
					}
					
					this.$api.localStore.localSet('u-index-search-history',JSON.stringify(this.searchHistory));
				}
			},
			onInput(val) {
				this.init();
				val.length == 0 && this.onClear();
			},
			onClear() {
				this.value = '';
				this.init();
				this.showResult = this.showResultBox = false;
			},
			// 取消按钮
			onCancel() {
				this.goPage(-1);
			},
			delHistory() {
				const key = this.tabList[this.active].historyKey;
				this.$set(this.searchHistory,key,[]);
				this.uHistory();
			},
			// 标签点击
			tipClick(val) {
				if( typeof val == 'object' ){
					if( val.type == 0 ){
						this.goPage('houses/index', { id: val.id, type:1 });
						return;
					} else {
						val = val.name;
					}
				}
				
				this.value = val;
				this.onSearch( val, 1 );
				
			},
			// 热门资讯切换
			// changeSort(e) {
			// 	const sort = this.sortTab[this.active];
			// 	const newSort = e.target.dataset.id;
				
			// 	if( sort != newSort ){
			// 		this.sortTab[this.active] = newSort;
			// 	}
			// },
			onSearch(val, refresh=0) {
				if(this.loading==true){
					return;
				}
				this.loading = true;

				let page = this.page + 1;
				if( page > this.maxPage ){
					this.loading = false;
					return;
				}

				if( val ){
					this.uHistory(val);
				} else {
					val = this.value;
				}
				
				if( this.tabList[this.active].id != 2 ){
					// 显示结果模块
					this.$http.post('/news/newsSearch',{
						pid: this.pid,
						city_no: this.city_no,
						search_value: val,
						page: page
					}).then(res=>{
						let that =this;
						let newList = res.data.list ? res.data.list : [];
						let write = `<span 
										class="template-title-write" 
										style="
										display: inline-block;
										margin-right: 4rpx;
										height: 30rpx;
										font-size: 20rpx;
										color: #fff;
										padding: 3rpx 10rpx !important;
										background: #FD4243;
										border-radius: 2rpx;
										vertical-align: middle;
										line-height: 30rpx;
										margin-top: 0 !important;
										text-align: center;"
									>
										原创
									</span>`
						newList&&newList.forEach(function (item,index) {
							item.type = 1;
							if(item.title){
								item.html_title = item.title.replace(val,'<p style="color:rgb(254, 130, 30);display: inline;">'+val+'</p>');//设置高亮
							}		
							if(item.write == 1){
								item.html_title = write + item.html_title
							}
							if( item.img instanceof  Array ){
								if(item.img.length > 0){
									item.img.forEach(function(img,i) {
										newList[index]['img'][i] = that.$api.imgDirtoUrl(img);
									})
								}
							}else{
								newList[index]['url'] = that.$api.imgDirtoUrl(item.url);
							}
									
						})

						this.resultDetail = this.resultDetail.concat(newList);
						this.showResult = this.showResultBox = true;

						this.page = res.data.current_page;
						this.maxPage = res.data.last_page;
						this.loading = false;
					})
				} else {
					this.$http.post('/estates/getEstatesList',{
						city_no: this.city_no,
						name: val,
						recommend: 1,
						no_adv: 1,
						page: page,
						page_size: 6
					}).then(res=>{
						const data = res.data;
						let arr = [];
						
						if(data.list&&data.list.length){
							arr = this.$api.createHouseList( data );
						}
						
						this.resultDetail = this.resultDetail.concat(arr);
						this.showResult = this.showResultBox = true;

						this.page = data.current_page;
						this.maxPage = data.last_page;
						this.loading = false;
					})
				}
			},
			onReachBottom(){
				if(this.value){//处于搜索状态时
					this.onSearch();
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
	.tabs{
		width: 100%;
		display: flex;
		justify-content: center;
		
		/deep/ .u-tab-item{
			font-size: 30rpx;
			font-weight: 800;
			color: rgba(33, 33, 33, 1);
			
		}
	}

	.tabs-wrap{
		width: 70%;
		margin: 30rpx 0 40rpx;
	}	
		
	.search{
		box-sizing: border-box;
		box-sizing: border-box;
		padding: 0 32rpx;
		margin-bottom: 30rpx;
		
		/deep/ .u-content{
			height: 80rpx !important;
			box-shadow: 0px 3px 20px 0px #F5F5F5;
			border-radius: 6px;
			border: 1px solid #BDBDBD;
		}
		
		/deep/ .u-iconfont{
			color: rgba(153, 153, 153, 1) !important;
			margin-right: 8rpx;
		}
	}

	.box{
		box-sizing: border-box;
		padding: 0 32rpx 100rpx;
	}

	.history{
		height: 100rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
		font-size: 0.34rem;
	}

	.history .iconfont{
		width: 80rpx;
		height: 100rpx;
		color: rgba(173, 173, 173, 1);
		font-size: 28rpx;
		display: flex;
		justify-content: flex-end;
		align-items: center;
	}

	.history-tip{
		display: flex;
		align-items: center;
		flex-wrap: wrap;
		// margin-bottom: 40rpx;
	}

	.history-tip span{
		height: 55rpx;
		line-height: 55rpx;
		padding: 0 15rpx;
		background-color: rgba(247, 247, 247, 1);
		margin: 0 35rpx 20rpx 0;
		font-size: 26rpx;
		border-radius: 4rpx;
	}

	.sort{
		height: 100%;
		display: flex;
		font-size: 26rpx;
	}

	.sort span{
		height: 100%;
		display: flex;
		align-items: center;
		box-sizing: border-box;
		font-weight: bold;
	}

	.sort span:first-child{
		padding-right: 25rpx;
	}

	.sort span:last-child{
		padding-left: 25rpx;
	}

	.sort-item{
		width: 100%;
		height: 140rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
		border-bottom: 1rpx solid rgba(235, 235, 235, 1);
	}

	.sort-left{
		display: flex;
		align-items: center;
	}

	.sort-left .iconfont{
		font-size: 55rpx;
		color: rgba(189, 189, 189, 1);
		position: relative;
	}

	.sort-left .iconfont span{
		font-size: 30rpx;
		font-weight: bold;
		color: #fff;
		position: absolute;
		left: 50%;
		top: 50%;
		transform: translate(-60%, -60%);
	}

	.top-three{
		color: rgba(252, 77, 57, 1) !important;
	}

	.history .h4-title{
		font-size: 30rpx;
		font-weight: bold;
	}
	.sort-wrap{
		margin-left: 32rpx;
	}

	.sort-wrap .h4-title{
		margin-bottom: 10rpx;
		font-size: 30rpx;
	}

	.sort-site{
		color: rgba(117, 117, 117, 1);
		font-size: 26rpx;
	}

	.sort-site span:first-child{
		padding-right: 10rpx;
		position: relative;
	}

	.sort-site-area{
		width: 340rpx;
		flex: 1;
	}
	
	.sort-site-line:after{
		content: '';
		height: 22rpx;
		width: 1px;
		background-color: rgba(117, 117, 117, 1);
		position: absolute;
		right: 0;
		top: 50%;
		transform: translate(0,-45%);
	}

	.sort-site span:last-child{
		padding-left: 10rpx;
	}

	.sort-price{
		font-size: 34rpx;
		color: rgba(252, 77, 57, 1);
		font-weight: bold;
	}

	.sort-price span{
		font-size: 24rpx;
	}

	.more{
		font-size: 26rpx;
		color: rgba(117, 117, 117, 1);
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.more .iconfont{
		font-size: 38rpx;
		width: auto;
		margin-left: 10rpx;
		color: rgba(117, 117, 117, 1);
	}

	.more2{
		padding-top: 20rpx;
	}

	.info{
		width: 100%;
		padding: 15rpx 0;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.info-text{
		display: flex;
		align-items: center;
	}

	.info-sort{
		width: 24rpx;
		height: 28rpx;
		font-size: 28rpx;
		font-weight: bold;
		color: rgba(117, 117, 117, 1);
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.info-title{
		width: 468rpx;
		margin-left: 16rpx;
	}

	.info-read{
		color: rgba(173, 173, 173, 1);
	}

	.result{
		padding-bottom: 100rpx;
	}

	.result-wrap{
		padding: 0 32rpx;
		box-sizing: border-box;
	}

	.result-all{
		height: 90rpx;
		font-size: 26rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
		border-bottom: 1rpx solid rgba(235, 235, 235, 1);
	}

	.result-text{
		width: 75%;
		font-weight: bold;
	}

	.result-all div{
		display: flex;
		justify-content: space-between;
		align-items: center;
		color: rgba(117, 117, 117, 1);
	}
	
	/deep/ .u-empty{
		margin-top: 160rpx !important;
	}
</style>
