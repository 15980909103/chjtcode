<template>
	<div>
		<template v-if="list.length > 0">
			<template v-for="(comment,key) in list" >
				<template v-if="key < showMore">
				<!-- v-if="comment.type.indexOf(type) != -1" -->
					<template >
						<div class="nav-comment" :key="key" >
							<div class="nav-comment-top">
								<img v-if='comment.head' :src="$api.imgDirtoUrl(comment.head)">
								<img v-else :src="$api.imgDirtoUrl('my/touxiang.png')">
								<span>{{ comment.name }}</span>
							</div> 
							<span class="nav-comment-content van-multi-ellipsis--l2">{{ comment.say }}</span>
							<div class="nav-comment-bottom" v-if="comment.img.length > 0">
								<template v-for="(img,num) in comment.img">
									<img :src="$api.imgDirtoUrl(img)" :key="num" v-if="num < 3" @click="showImg(comment.img,num)">
								</template>
								<u-tag
									:text="`共${ comment.img.length }张`" 
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
									:show="comment.img.length > 3"
								/>
								<!-- <van-tag color="rgba(0, 0, 0, .6)" v-if="comment.img.length > 3">共{{ comment.img.length }}张</van-tag> -->
							</div>
							<span class="nav-comment-time" v-if="time">{{ comment.time }}</span>
						</div>
					</template>
				</template>
			</template>
		</template>
		<template v-else>
			<div class="list_null">
				<img :src="$api.imgDirtoUrl('null.png')">
				<p>暂无数据</p>
			</div>
		</template>
	</div>
</template>

<script>
	export default {
		computed: {
			// 显示多少条
			showMore() {
				if( this.showAll ){
					return 9999;
				} else {
					return this.num;
				}
			},
		},
		props: {
			list: {
				type: Array,
				default() {
					return []
				}
			},
			showAll: {
				type: Boolean,
				default() {
					return true
				}
			},
			num: {
				type: [String, Number],
				default() {
					return 3
				}
			},
			// type: 0-所有 1-有图，2-实看
			type: {
				type: [String, Number],
				default() {
					return 0
				}
			},
			time: {
				type: Boolean,
				default() {
					return false
				}
			}
		},
		methods: {
			showImg(item, index=0){
				return this.$api.showImg(item, index=0)
				
			// 	let obj = {};
			// 	const arr = [];
			// 	console.log(item)
			// 	if( typeof(item) != 'object' ){
			// 		item = [item];
			// 	}
			// 	console.log(item);
			// 	item.map( newItem=>{
			// 		const img = this.imgDirtoUrl(newItem);
					
			// 		arr.push(img);
			// 	})
			
			// 	obj = {
			// 		urls: arr,
			// 		current: 0,
			// 	};
				
			// 	if( index ){
			// 		obj.current = index;
			// 	}
			
			// 	uni.previewImage(obj)
			}
		},
	}
</script>

<style lang="scss" scoped>
/* 用户点评 */
.nav-comment{
	margin: 24rpx 0 36rpx;
}

.nav-comment:last-child{
	margin: 24rpx 0 0;
}

.nav-comment-top{
	display: flex;
	align-items: center;
}

.nav-comment-top img{
	width: 60rpx;
	height: 60rpx;
	border-radius: 50%;
}

.nav-comment-top span{
	font-size: 26rpx;
	color: rgba(117, 117, 117, 1);
	margin-left: 18rpx;
}

.nav-comment-content{
	font-size: 26rpx;
	margin: 18rpx 0 0;
}

.nav-comment-time{
	color: rgba(173, 173, 173, 1);
	font-size: 22rpx;
	margin-top: 18rpx;
}

.nav-comment-bottom{
	display: flex;
	position: relative;
	margin-top: 18rpx;
}

.nav-comment-bottom .van-tag{
	position: absolute;
	right: 10rpx;
	bottom: 10rpx;
	font-size: 22rpx;
}

.nav-comment-bottom img{
	width: 218rpx;
	height: 218rpx;
	margin-right: 16rpx;
}

.nav-comment-bottom img:nth-child(3){
	margin-right: 0;
}
</style>
