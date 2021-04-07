<template>
	<view>
		<view class="tabss">
			<u-icon v-if="iconname == 'arrow-left'" :name="iconname" size="48" @click="clickon"></u-icon>
			<view class="name">{{name}}</view>
		</view>
	</view>
</template>

<script>
	export default {
		props: {
			name: {
				type: String,
			},
			id: {
				type: [String, Number],
				default() {
					return 0
				}
			},
		},
		data() {
			return {
				iconname:'',
				page:{},
				pages:{}
			};
		},
		onLoad() {
			
		},
		mounted() {
			this.pages = getCurrentPages();
		},
		created() {
			this.pages = getCurrentPages();
			let pagess = (this.pages[this.pages.length - 1])
			if(pagess.route !== 'pages/activitySount/index' && pagess.route !=='pages/activitySount/verification'){
				this.iconname = 'arrow-left'
			}else{
				this.iconname = ''
			}	
			console.log('this.iconname ',this.iconname)
		},
		methods:{
			clickon(){
				this.pages = getCurrentPages();
				let page = (this.pages[this.pages.length - 2]);  
				let pagess = (this.pages[this.pages.length - 1])
				let that = this
				
				// console.log(pagess)
				// console.log(page)
				// this.goPages('/'+page.route)
				// uni.navigateBack({
				// 	delta:1
				// })
				if(page){
					console.log(1)
					uni.navigateBack({
						delta:1
					})
				}else{
					console.log(2)
					uni.navigateTo({
						url: '/pages/activitySount/index?activity_id='+this.id
					});
				}
			},
		}
	}
</script>

<style lang="scss">
	.tabss{
		height: 80rpx;
		background-color: #FFFFFF;
		line-height: 80rpx;
		padding: 0 8rpx;
		position: relative;
		.name{
			position: absolute;
			left: 50%;
			top: 0;
			transform: translate(-50%,0);
		}
	}
</style>
