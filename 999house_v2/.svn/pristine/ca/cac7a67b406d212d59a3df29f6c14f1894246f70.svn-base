<template>
	<view>
		<div class="info" id="info" ref="contentBox" style="background-color: #FFFFFF;">
			<u-tabs
				:list="tabList" 
				:is-scroll="false"
				:current="active" 
				active-color="rgba(254, 130, 30, 1)" 
				:active-item-style="{
					fontSize: '38rpx'
				}"
				bar-trans="0"
				@change="tabsChange"
			>
			</u-tabs>
			<van-tabs
				active="active"
				color="rgba(254, 130, 30, 1)"
				title-active-color="rgba(254, 130, 30, 1)"
				:sticky="true"
				@change="tabsChange"
				style="background-color: #FFFFFF;"
			>
				<van-tab v-for="(items,index) in tabList"  :title="items.name" :key="index" v-show="active == items.type" style="background-color: #FFFFFF;">
					<template v-if="items.type == 0">
						<template>
							<van-list
							  v-model="loading"
							  :finished="finished"
							  finished-text="没有更多了"
							  @load="onLoad"
							>
								<common-information  :list="activeList" @del="(e)=>{ activeList = e }"></common-information>
							</van-list>
						</template>
						<!-- <template v-else>
							<van-empty image="search" description="暂无数据" v-show="no_data"/>
						</template> -->
					</template>
					<template v-else>
						<div class="house_wrap" @touchstart="start" @touchmove="move" @touchend="end"  v-if="activeList.length > 0">
							<div class="new-house-activity" v-if="items.activityList && items.activityList.length > 0">
								<div 
									class="new-house-activity-item" 
									v-for="(item,index) in items.activityList" 
									:key="index"
									@click="goActive(item)"
								>
									<div>{{item.title}}</div>
									<img v-if="item.img&&item.img[0]" :src="$http.imgDirtoUrl(item.img[0])">
								</div>
							</div>
							
							<div 
								class="house_wrap_content" 
								:style="{ transform: 'translateY(' + (moveY + 'px') + ')'}"
							>
								<common-newhouse :list="activeList" @del="(e)=>{ activeList = e }"></common-newhouse>
								<!-- <common-template :list="activeList"  @del="(e)=>{ activeList = e }"></common-template> -->
							</div>
							<div class="house_wrap_bottom" :style="{ transform: 'translateY(' + (moveY + 'px') + ')'}">{{ moreText }}</div>
						</div>
						<template v-else>
							<van-empty image="search" description="暂无数据" v-show="no_data"/>
						</template>
					</template>
				</van-tab>
			</van-tabs>
		</div>
	</view>
</template>

<script>
	import commonInformation from '@/components/common/template_information';
	import commonNewhouse from '@/components/common/template_newHouse';
	export default {
		data() {
			return {
				active: 0,
				activeList: [],
				loading: true,
				finished: false,
				page: 0,
				maxPage: 1,
				city_no: 0,
				no_data: false,
				tabList: [
					{ 
						type: 0,
						name: '资讯',
					},
					{
						type: 1,
						name: '新房',
						activityList: []
					}
				],
				startY: 0,
				moveY: 0,
				moreText: '再往上拉查看更多~'
			}
		},
		components:{
			commonInformation,
			commonNewhouse
		},
		created() {
			this.city_no = getApp().getCurrentCity().city_no
			this.getNewsList();
			// this.$http.getCurrentCity().then( data=>{
			// 	this.city_no = data.city_no;
			// 	this.getNewsList();
			// })
		},
		methods: {
			// 切换资讯/新房
			tabsChange(e) {
				console.log(e)
				this.page = 0;
				this.maxPage = 1;
				this.loading = true;
				this.finished = false;
				this.activeList = [];
				this.no_data = false;
				this.active = e
				this.onLoads();
			},
			onLoads() {
				if( this.active == 0 ) {
					this.getNewsList();
				} else {
					this.getHouseActivty();
					this.getHouseList();
				}
			},
			getNewsList() {
				if( this.page >= this.maxPage ) {
					this.loading = false;
					this.finished = true;
					return;
				}
				
				let page = this.page;
				const haveVideo = ( page == 1 || page%3 == 0 ) ? 1 : 0;
				
				page++;
				
				const data = {
					pid: 9,
					is_index: 1,
					city_no: this.city_no,
					page: page,
					is_get_small_video: haveVideo
				};
				
				// console.log(data)
				
				this.$http.post(
					'/news/getNewsList',
					data
				).then( res=>{
					const data = res.data;
					const arr = [];
					let adIndex = 0;
					
					this.maxPage = data.last_page;
					this.page = data.current_page;
					
					if( page == 1 && (!data.list || data.list.length == 0)){
						this.no_data = true;
					}
					
					data.list.map( (item,index)=>{
						arr.push(item);
						
						if( (index+1)%6 == 3 ){
							let ad = data.ad_lsit[adIndex];
							ad = this.formatAdv(ad);
							if( ad ){
								arr.push( data.ad_lsit[adIndex] );
								adIndex++;
							}
						}
					})
					
					if( haveVideo == 1 ) {
						( data.small_voide && Object.keys(data.small_voide).length > 0 ) && arr.push( data.small_voide );
					}
					
					// console.log(res)
					// console.log(data)
					// console.log(arr)
					this.activeList = [...this.activeList,...arr];
					
					if( this.page >= this.maxPage ) {
						this.finished = true;
					}
					
					this.loading = false;
				})
			},
			getHouseActivty() {
				this.$http.post(
					'/adv/getAdvByFlag',
					{
						falg: 'h5_home_estates',
						city_no: this.city_no,
						limit: 999
					}
				).then( res=>{
					const data = res.data;
					
					// console.log(res)
					// console.log(data)
					this.$set( this.tabList[1], 'activityList', data );
				})
			},
			getHouseList() {
				const data = {
					city_no: this.city_no,
					recommend: 1
				};
				
				// console.log(data)
				
				this.$http.post(
					'/estates/getEstatesList',
					data
				).then( res=>{
					const data = res.data;
					
					this.activeList = this.$api.createHouseList( data, 1 );
					// console.log(res)
					// console.log(data)
					if( !this.activeList || this.activeList.length == 0 ) {
						this.no_data = true;
					}
				})
			},
			goActive(e) {
				if(!$api.trim(e.href)&&e.info){
					e.href = 'houses/index.html?id='+e.info.estate_id+'&cover='+e.cover;
				}
				if(!e.href){
					return
				}
				$api.goPage(e.href)
			},
			// 查看更多跳转
			start( e ) {
				console.log(this.$refs)
				// let el = this.$refs.contentBox.parentElement;
				// console.log(el.scrollHeight,'el.scrollHeight---',this.$refs.contentBox.scrollHeight)
				// console.log(el.scrollTop,'el.scrollTop---',this.$refs.contentBox.scrollTop)
				// console.log(el.clientHeight,'el.clientHeight---',this.$refs.contentBox.clientHeight)
				// console.log(el.scrollHeight - el.scrollTop - el.clientHeight)
				// if( el.scrollHeight - el.scrollTop - el.clientHeight < 1){
				// 	this.startY = Number(e.changedTouches[0].clientY.toFixed(2));
				// 	console.log('到底了',this.startY)
				// }
			},
			move( e ) {
				if( this.startY == 0 ){
					return;
				}
				
				let move = Number(e.changedTouches[0].clientY.toFixed(2));
				
				if( move < this.startY ) {
					const num = Number((this.startY - move).toFixed(2));
					if( num < 60 ){
						this.moveY = -num;
					}
					
					if(  Math.abs(num) < 40 ){
						this.moreText = '再往上拉查看更多~'
					} else {
						this.moreText = '跳转新房~'
					}
				}
			},
			end( e ) {
				if( this.startY == 0 ){
					return;
				}
			
				if(  Math.abs(this.moveY) > 40 ){
					this.$api.goPage('new_house/index.html');
				}
				
				this.startY = 0;
				this.moveY = 0;
				this.moreText = '再往上拉查看更多~';
			},
		
		
			formatAdv(advlist){
				if(advlist &&  !advlist.href&&advlist.info){
					advlist.href = 'houses/index.html?id='+advlist.info.estate_id+'&cover='+advlist.cover;
				}
				
				let tips = [];	
				let new_lab = [];	
				if(advlist && advlist.info){
					let adv_info = advlist.info															
					tips = tips.concat($api.getTagsText('estatesnew_sale_status',advlist.info.sale_status));
					tips = tips.concat($api.getTagsText('house_purpose',advlist.info.house_purpose));
					if(advlist.info.feature_tag){
						tips = tips.concat($api.getTagsText('feature_tag',advlist.info.feature_tag));
					}
					advlist.info.tip = tips;
		
					if(advlist.info.lab){
						let lab = advlist.info.lab
						
						for(let i in lab){
							let item = lab[i]
							if(item.type == 'discount'){
								item.type = 1;
								new_lab.push({
									name: item.title,
									type: item.type,
								})
							}
							if(item.type == 'hot'){
								item.type = 0;
								new_lab.push({
									name: item.title,
									type: item.type,
								})
							}
						}
					}
					advlist.info.lab = new_lab;
				}
				
				
				return advlist;
			},
		}
	}
</script>

<style>
	/* 资讯tabber */
	.info{
		position: sticky;
		top: 0;
		z-index: 10;
		margin: 0 20rpx;
	}
	
	.info .van-tab{
		flex: none;
		width: 130rpx;
		font-size: 34rpx;
		touch-action: none;
		color: #000000;
		font-weight: 500;
	}
	
	.info .van-tab--active{
		font-size: 38rpx;
	}
	
	.info .van-sticky--fixed{
		top: 120rpx;
		box-shadow: 0px 0px 5px 1px #F8F8F8;
	}
	.info .van-tabs__line{
		width: 24rpx;
		bottom: 18px;
	}
	
	.info .van-tabs__content{
		background: #fff;
		overflow: hidden;
	}
	
	.info .van-tab__pane{
		min-height: calc( 100vh - 44px );
	}
	
	.new-house-activity{
		width: 100%;
		height: 200rpx;
		overflow: scroll hidden;
		-webkit-overflow-scrolling: touch;
		display: flex;
		padding: 50rpx 0 20rpx 0;
		background-color: #fff;
	}
	
	.new-house-activity-item{
		width: 208rpx;
		height: 198rpx;
		padding: 20rpx;
		background: #F0F5F6;
		box-sizing: border-box;
		margin-left: 32rpx;
	}
	
	.new-house-activity-item:last-child{
		margin-right: 32rpx;
	}
	
	.new-house-activity-item:nth-child(2n-1){
		background: rgba(245, 248, 255, 1);
	}
	
	.new-house-activity-item div{
		width: 220rpx;
		font-size: 30rpx;
		margin-bottom: 10rpx;
	}
	
	.new-house-activity-item img{
		height: 110rpx;
		width: 100%;
	}
	
	.van-list__finished-text{
		padding-bottom: 104rpx;
	}
	
	.house_wrap{
		transition: .3s;
		min-height: calc( 100vh - 44px );
	}
	
	.house_wrap_content{
		min-height: calc( 100vh - 44px );
		/* min-height: 2000px; */
		width: 100%;
		/* background-color: green; */
		display: flex;
		justify-content: center;
		/* align-items: center; */
		transition: .3s;
	}
	
	.house_wrap_bottom{
		height: 102rpx;
		text-align: center;
		transition: .3s;
		color: rgba(100,100,100,.8);
		box-sizing: border-box;
		padding-top: 10rpx;
	}
</style>
