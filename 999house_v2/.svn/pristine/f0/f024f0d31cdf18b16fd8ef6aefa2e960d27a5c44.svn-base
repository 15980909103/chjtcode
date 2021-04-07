<template>
	<span class="wrap">
		<template v-for="(news,key) in list">
			<view class="nav-news" :key="key" v-if="key < showNum" @click="goInfo(news.type, news.id)">
				<view class="nav-news-top" :class="[ news.type == 1 ? 'van-multi-ellipsis--l2' : '' ]">
					<u-tag 
						:text="news.tip" 
						mode="plain" 
						size="mini" 
						:color="news.tip == '测评' ? 'rgba(134, 186, 122, 1)' : 'rgba(55, 141, 219, 1)'"
						:border-color="news.tip == '测评' ? 'rgba(134, 186, 122, 1)' : 'rgba(55, 141, 219, 1)'"
						:style="{
							fontSize: '20rpx !important',
							marginRight: '10rpx'
						}"
					/>
					{{ news.title }}
				</view>
				
				<template v-if="news.img">
					<view class="nav-news-bottom" v-if="news.img.length > 0">
						<template  v-for="(img,num) in news.img" >
							<!-- @click.stop="$http.showImg(news.img,num)" -->
							<img :src="$api.imgDirtoUrl(img)" :key="num" v-if="num < 3" >
						</template>
						<u-tag
							:text="`共${ news.img.length }张`" 
							mode="plain" 
							size="mini" 
							color="rgba(0, 0, 0, .6)"
							border-color="rgba(0, 0, 0, .6)"
							:style="{
								position: 'absolute',
								right: '10rpx',
								bottom: '10rpx',
								fontSize: '22rpx'
							}"
							:show="news.img.length > 3"
						/>
						<!-- <van-tag color="rgba(0, 0, 0, .6)" v-if="news.img.length > 3">共{{ news.img.length }}张</van-tag> -->
					</view>
				</template>
				<template v-else-if="news.video">
					<view class="nav-news-bottom" v-if="news.video.length > 0">
						<template  v-for="(video,num) in news.video" >
							<view class="nav-news-video" :key="num" v-if="num < 2">
								视频预留位
							</view>
						</template>
					</view>
				</template>
			</view>
		</template>
	</span>
</template>

<script>
	export default {
		props: {
			list: {
				type: Array,
				default() {
					return []
				}
			},
			showNum: {
				type: Number,
				default() {
					return 99999
				}
			}
		},
		methods: {
			goInfo( type, id ){
				this.goPage('discover/news_detail',{ id: id, pid: 9, cate_id: 10 });
			}
		},
	}
</script>

<style lang="scss" scoped>
/* 实时资讯 */	
.wrap{
	width: 100%;
}

.nav-news{
	width: 100%;
	box-sizing: border-box;
	margin: 24rpx 0 28rpx;
	border-bottom: 1px solid rgba(235, 235, 235, 1);
	padding-bottom: 30rpx;
}

.nav-news:last-child{
	border-bottom: none;
}

.nav-news-top{
	font-size: 26rpx;
	line-height: 40rpx;
}

.nav-news-bottom{
	display: flex;
	align-items: center;
	position: relative;
	margin-top: 24rpx;
	width: fit-content;
}

.nav-news-bottom img{
	width: 218rpx;
	height: 218rpx;
	margin-right: 16rpx;
}

.nav-news-bottom img:nth-child(3){
	margin-right: 0;
}

.nav-news-video{
	width: 250rpx;
	height: 330rpx;
	margin-right: 36rpx;
	background-color: pink;
}

</style>
