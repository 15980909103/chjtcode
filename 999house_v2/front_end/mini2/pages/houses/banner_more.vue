<template>
	<view class="content" v-if="pageShow">
		<div v-if="list.length">
			<div class="box" v-for="(item,index) in list" :key="index">
				<block v-if="item.list.length">
					<h4>{{ item.name }}</h4>
					<div class="img-wrap">
						<!-- vr -->
						<block v-if="item.type == 0">
							
						</block>
						<!-- 视频 -->
						<block v-else-if="item.type == 1">
							
						</block>
						<!-- 图片 -->
						<block v-else-if="item.type == 2" v-for="(img,key) in item.list" :key="key">
							<image class="img" :src="imgDirtoUrl(img)"  @click="showImg(item.list,key)">
						</block>
					</div>
				</block>
			</div>
			<div id="container-user-site"></div>
		</div>
		<div class="list_null" v-else>
			<img src="../../static/null.png">
			<p>暂无数据</p>
		</div>
	</view>
</template>

<script>
	let app = getApp();
	export default{
		data(){
			return {
				pageShow: false,
				list: [
					// {
					// 	type: 0,
					// 	name: 'VR看房',
					// 	list: ['/9house/static/logo.png','/9house/static/logo.png']
					// },
					// {
					// 	type: 2,
					// 	name: '小区配套',
					// 	list: ['/9house/static/logo.png','/9house/static/logo.png']
					// },
					// {
					// 	type: 1,
					// 	name: '视频看房',
					// 	list: ['/9house/static/logo.png','/9house/static/logo.png']
					// }
				],
				category: {
					1: '效果图',
					2: '实景图',
					3: '样板间',
					4: '区位',
					5: '小区配套',
					6: '项目现场',
					7: '楼栋',
					8: '预售许可证',
					9: '视频看房'
				}
			}
		} ,
		components: {
	
		},
		onLoad(options) {
			this.estate_id = options.id;
			app.getConst().then(res=>{
				console.log(res.buildingphotos_categorys)
				let tag = res['buildingphotos_categorys'];
			
				if( tag&&Object.keys(tag).length > 0 ){
					this.category = tag;
				}
				
				this.getSwiperList();
			});
			
		},
		methods:{
			// 获取楼盘相册
			getSwiperList() {
				this.$http.post('/estates/getBuildingPhotosList',{
					estate_id: this.estate_id,
				}).then( res=>{
					let data = res.data;
					
					const newData = [];
					
					for( let i in data ){
						const obj = {};
						const arr = [];
						
						if( i != 9 ){
							data[i].map( item=>{
								arr.push(item.cover);
							})
							
							obj.type = 2;
							obj.name = this.category[i];
							obj.list = arr;
							
							newData.push(obj);
						} else {
							// data[i].map( item=>{
							// 	arr.push({
							// 		cover: item.cover,
							// 		video: item.video_url
							// 	});
							// })
							
							// obj.type = 1;
							// obj.name = this.category[i];
							// obj.list = arr;
							
							// newData.unshift(obj);
						}
					}
					
					console.log(newData)
					this.list = newData;
					this.pageShow = true;
				}).catch(()=>{
					this.pageShow = true;
				})
			},
			showImg(item, index){
				return this.$api.showImg(item, index);
			}
		},
	}

	
	
</script>

<style>
.box{
	box-sizing: border-box;
	padding: 20rpx 32rpx 0;
}

.box:first-child{
	padding-top: 50rpx;
}

.box:last-child{
	padding-bottom: 54rpx;
}

.box h4{
	font-size: 30rpx;
	margin-bottom: 24rpx;
}

.img-wrap .img{
	width: 210rpx;
	height: 145rpx;
	margin-right: 28rpx;
	margin-bottom: 30rpx;
}

.img-wrap img:nth-child(3n){
	margin-right: 0;
}
</style>