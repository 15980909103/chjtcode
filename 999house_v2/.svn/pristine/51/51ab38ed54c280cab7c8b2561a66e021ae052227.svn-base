<template>
	<view>
		<div class="nav">
			<div class="nav-wrap" v-for="(items,key) in newNav" :key="key">
				<div class="nav-box" >
					<div class="nav-item" v-for="(item,index) in items" :key="index" @click="goPages(item.href)">
						<image :src="$api.imgDirtoUrl(item.cover)" class="nav-img" />
						<span class="nav-tip">{{item.name}}</span>
					</div>
				</div>
			</div>
		</div>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				newNav: []
			}
		},
		props: ['nav'],
		created() {
			//console.log(this.nav,666)
			this.newNav = this.divideNav();
		},
		mounted() {
			
		},
		methods: {
			divideNav() {
				let that = this;
				let newArr = [];
				
				if( that.nav.length > 0 ){
					let cloneNav = this.$api.deepClone(that.nav);
					_deepAdd(cloneNav);
					
					// console.log(newArr)
					return newArr;
				}
				
				function _deepAdd(arr){
					if( arr.length > 0 ){
						newArr.push(arr.slice(0,8));
						arr.splice(0,8);
						
						if( arr.length > 0 ) {
							_deepAdd(arr);
						}
					}
				}
			},
			goPages(href) {
				let url = ''
				
				if(href.indexOf('.html')!=-1){
					url = href.replace('.html','')
					console.log('url', url)
					if( this.$api.trim(url) ){
						this.goPage(url);
					}
				}
			}
		}
	}
</script>

<style>
	.nav{
		/* height: 3.72rpx; */
		margin: 30rpx 0;
		width: 100vw;
		display: flex;
		overflow-x: scroll;
		-webkit-overflow-scrolling: touch;
		font-size: 0;
	}

	.nav-wrap,
	.nav-box{
		width: 100vw;
		height: 100%;
	}

	.nav-box{
		display: flex;
		flex-wrap: wrap;
	}

	.nav-item{
		width: 20%;
		/* width: 1.875rpx; */
		height: 160rpx;
		font-size: 28rpx;
		font-weight: 800;
		color: #212121;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}

	.nav-item image{
		width: 90rpx;
		height: 90rpx;
		margin-bottom: 15rpx;
	}
</style>
