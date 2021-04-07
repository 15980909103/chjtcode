<template>
	<view class="content" v-if="option.type">
		<template v-if="option.type == 1">
			<view 
				class="box" 
				:class="[item.is_read == 2 ? 'box_read' : '']"
				v-for="(item,index) in data" 
				:key="index" 
				@click="goPage('chat/detail', { id: item.id })"
			>
				<view class="title van-ellipsis">
					{{ item.title }}
				</view>
				<view class="text van-multi-ellipsis--l2" :class="[item.cover ? '' : 'text-lineheight']">
					<img :src="$api.imgDirtoUrl(item.cover)" v-if="item.cover">
					<span v-else>{{ item.sub_context }}</span>
				</view>
				<view class="detail"><span>查看详情<i class="iconfont iconjiantou1-copy"></i></span></view>
			</view>
		</template>
		<template v-else-if="option.type == 4">
			<view class="box" 
				v-for="(item,index) in data" 
				:key="index"
				:class="[item.is_read == 2 ? 'box_read' : '']"
				 @click="goPage('houses/index', { id: item.estate_id, cover: item.is_cover })"
			 >
				<view class="title van-ellipsis">
					{{ item.title }}
				</view>
				<view class="text van-multi-ellipsis--l2">
					<img :src="$api.imgDirtoUrl(item.cover)">
				</view>
				<view class="name"><span>楼盘：{{ item.name }}</span></view>
			</view>
		</template>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				data: [],
				option: '',
				firstLoad: true,
			}
		},
		components: {
		
		},
		onLoad(option) {
			this.option = option;
			this.init(option);
			this.listen();
		},
		onShow() {
			if( this.firstLoad ) return;
			this.init(this.option);
		},
		methods: {
			init( option ) {
				uni.setNavigationBarTitle({
					title: option.name
				});
				
				this.$http.post( 'chat/getSystemMsgByType',{
					type: option.type
				}).then( res=>{
					this.data = res.data.list;
					
					if( this.firstLoad ){
						this.firstLoad = false;
					}
				})
			},
			listen() {
				uni.$on('system_msg', (e)=>{
					if( this.option.type == e.type ){
						this.data.unshift(e);
					}
				})
			}
		}
	}

</script>

<style lang="scss" scoped>
	.content{
		width: 100%;
		min-height: 100vh;
		box-sizing: border-box;
		padding: 30rpx 32rpx;
		background: #F7F7F7;
		
		.box{
			width: 100%;
			box-sizing: border-box;
			padding: 30rpx 34rpx;
			background-color: #fff;
			border-radius: 10rpx;
			margin-bottom: 20rpx;
			position: relative;
			
			.title{
				font-size: 30rpx;
				color: rgba(0, 0, 0, 1);
				margin-bottom: 25rpx;
			}
			
			.text{
				font-size: 26rpx;
				color: #757575;
				
				img{
					width: 100%;
					max-height: 300rpx;
					object-fit: cover
				}
				
				&-lineheight{
					line-height: 44rpx;
				}
			}
			
			.detail,
			.name{
				height: 44rpx;
				
				span{
					position: absolute;
					bottom: 20rpx;
					display: flex;
					align-items: center;
					
					.iconfont{
						font-size: 36rpx;
						margin-left: 14rpx;
						box-sizing: border-box;
						padding-top: 6rpx;
						color: rgba(117, 117, 117, 1);
					}
				}
			}
			
			.detail{
				margin-top: 20rpx;
				
				span{
					font-size: 26rpx;
					right: 34rpx;
					color: rgba(57, 95, 171, 1);
				}
			}
			
			.name{
				span{
					font-size: 28rpx;
					left: 34rpx;
					color: rgba(117, 117, 117, 1);
				}
			}
		}
		
		.box_read::after{
			content: '';
			width: 14rpx;
			height: 14rpx;
			background-color: red;
			border-radius: 50%;
			position: absolute;
			top: 10rpx;
			right: 10rpx;
		}
	}
</style>
