<template>
	<view class="content">
		<view class="top" :style="[menuTop ? { paddingTop: menuTop.top + menuTop.height + 10 + 'px' } : '']">
			<view class="search">
				<u-search
					v-model="searchVal"
					placeholder="搜索联系人" 
					shape="square"
					bg-color="rgba(245, 248, 254, 1)"
					search-icon-color="rgba(173, 173, 173, 1)"
					placeholder-color="rgba(173, 173, 173, 1)"
					color="#606266"
					:show-action="false"
					height="74"
					:input-style="{
						fontSize: '26rpx'
					}"
				>
				</u-search>
			</view>
			<view class="cancel" @click="cancel">取消</view>
		</view>
		<template v-if="$u.trim(searchVal)">
			
			<view class="wrap">
				<view class="title">联系人</view>
				<template v-for="(item,index) in data">
					<view 
						class="chat"  
						@click="goPage('chat/chat', { id: item.dialogue_id, name: item.friend_name, to_uid: item.friend_user_id })"
						v-if="$u.trim(searchVal)&&item.friend_name.indexOf($u.trim(searchVal)) != -1" 
						:key="index"
					>
						<view class="chat-head">
							<img :src="$api.imgDirtoUrl(item.friend_head)">
						</view>
						<view class="chat-info van-ellipsis">
							<view class="chat-info-top">
								<span v-html="showResult(item.friend_name)"></span>
							</view>
						</view>
					</view>
				</template>
				
				<!-- <view class="chat" >
					<view class="chat-head">
						<img src="../../static/logo.png">
					</view>
					<view class="chat-info van-ellipsis">
						<view class="chat-info-top">
							<text>陈佳</text>
						</view>
						<view class="chat-info-say van-ellipsis" v-if="item != 1">
							房子是哪一年的？房子是哪一年的？房子是哪一年的？房子是哪一年的？房子是哪一年的？房子是哪一年的？房子是哪一年的？
						</view>
					</view>
				</view> -->
			</view>
		</template>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				// 小程序按钮位置
				menuTop: 0,
				searchVal: ''
			}
		},
		props: {
			data: {
				type: Array,
				default: []
			},
		},
		created() {
			this.menuTop = getApp().globalData.menuInfo;
		},
		methods: {
			cancel() {
				const that = this;
				
				that.$emit('close');
				
				setTimeout(()=>{
					that.searchVal = '';
				}, 300);
			},
			showResult(name) {
				let item = '';
				
				for( let i in name ){
					if( this.searchVal.indexOf(name[i]) != -1 ){
						item += `<span class="text-active">${ name[i] }</span>`;
					} else {
						item += `<span>${ name[i] }</span>`;
					}
				}
			
				return item;
			}
		}
	}

</script>

<style lang="scss" scoped>
	.content{
		
	}
	
	.top{
		box-sizing: border-box;
		padding: 0 32rpx 28rpx;
		display: flex;
		align-items: center;
		background-color: #fff;
		position: sticky;
		top: 0;
		z-index: 99;
		
		
		.search{
			flex: 1;
		}
		
		.cancel{
			font-size: 28rpx;
			color: rgba(117, 117, 117, 1);
			padding-left: 32rpx;
		}
	}
	
	.wrap{
		.title{
			font-size: 24rpx;
			color: rgba(173, 173, 173, 1);
			box-sizing: border-box;
			padding: 10rpx 32rpx 14rpx;
			border-bottom: 1px solid rgba(240, 240, 240, 1);
		}
		
		.chat{
			width: 100%;
			display: flex;
			box-sizing: border-box;
			padding: 34rpx 32rpx;
			border-bottom: 1px solid rgba(230, 230, 230, 1);
			
			&-head{
				margin-right: 30rpx;
				position: relative;
				
				img{
					width: 92rpx;
					height: 92rpx;
					border-radius: 50%;
				}
			}
			
			&-info{
				flex: 1;
				display: flex;
				flex-direction: column;
				justify-content: center;
				
				&-top{
					width: 100%;
					display: flex;
					justify-content: space-between;
					font-size: 30rpx;
					font-weight: 800;
				}
				
				&-say{
					font-size: 26rpx;
					color: rgba(117, 117, 117, 1);
					margin-top: 14rpx;
				}
			}
		}
		
		.chat:last-child{
			border-bottom: none;
		}
	}
</style>
