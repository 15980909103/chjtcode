<template>
	<view class="content">
		<div class="box" :style="{ paddingBottom: 200 + safeBottom*2 +'rpx' }">
			<div class="tip">
				<block v-for="(item,index) in list.tip" :key="index">
					<u-tag :text="item.name" :class="[type==item.type?'tag-active':'tag']" mode="light" @click="changeTag(index)"/>
				</block>
			</div>
			<houses-comment v-if='showComment' :list="list.list" :type="type" :time="true"></houses-comment>
		</div>
		
		<div class="btn" :style="{ paddingBottom: safeBottom+'px' }">
			<u-button type="default"  @click="goComment">点评楼盘 分享心得</u-button>
		</div>
		<div id="container-user-site"></div>
	</view>
</template>		


<script >
	import housesComment from '../../components/houses/comment.vue';
	export default{
		data(){
			return {
				// 安全区
				safeBottom: 0,
				page: 0,
				total_page: 1,
				loading: false,
				showComment: false,
				estate_id: 0,
				color: [],
				type: 0,
				list: {
					tip: [
						{
							type: 0,
							name: '全部评论'
						},
						{
							type: 2,
							name: '有图'
						},
						// {
						// 	type: 3,
						// 	name: '实看用户'
						// }
					],
					// type: 0-所有 1-有图，2-实看
					list: [
						// {
						// 	type: [0],
						// 	head: '../../static/logo.png',
						// 	name: '海的声音',
						// 	say: '去看了样板房，89平的4房户型很好，简直了！',
						// 	img: [],
						// 	time: '2020年8月12日'
						// },
					]
				}
			}
		},
		components: {
			housesComment
		},
		onLoad(options) {
			this.safeBottom = Math.abs(uni.getSystemInfoSync().safeAreaInsets.bottom);
			console.log(uni.getSystemInfoSync().safeAreaInsets)
			
			this.estate_id = options.id;
		},
		onShow(){
			this.getTalk(1);
		},
		onReachBottom() { //滚动到底翻页
		  this.getTalk()
		},
		methods:{
			changeTag(index) {
				this.type = this.list.tip[index].type;
				this.getTalk(1)
			},
			// 楼盘评论
			getTalk(reset = 0) {
				let page = this.page+1
				if(reset==1){
					page = 1;
					this.showComment = false;
					this.$set(this.list, 'list',  []);
				}
				if(page>this.total_page){
					return
				}
				if(this.loading==true){
					return
				}
				this.loading = true;

				let id = this.estate_id;
				let is_img = this.type //1没有图片 2有图片的评论 如果不传或者传 0就是说明全部的
				let data = {
					id: id,
					is_img: is_img,
					page: page,
					pageSize: 100,
				};
		
				this.$http.post('/comment/propertyReviewsList',data).then( res=>{
					let data = res.data.list?res.data.list:[];
					const newData = [];
					// console.log(data);
					
					data.map(item=>{
						const obj = {};
						obj.id = item.id;
						obj.head = this.imgDirtoUrl(item.user_avatar);
						obj.name = item.user_name;
						obj.say = item.content;
						obj.time = this.$api.timeFormat(item.create_time,'yyyy年mm月dd日');
						obj.img = item.img;
						
						newData.push(obj);
					})
					
					this.$set(this.list, 'list',  newData);
					this.showComment = true;

					this.page = page;
					this.total_page = res.data.last_page? res.data.last_page : 1;
					this.loading = false;
				}).catch(()=>{
					this.showComment = true;
					this.loading = false;
				})
			},
			goComment() {
				if( !this.isLogin()){
					return;
				}
				this.goPage('houses/send_comment',{ id: this.estate_id })
			}
		},
	}
	
</script>

<style>
	 @import './comment.css';
</style>