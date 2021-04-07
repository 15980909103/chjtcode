<template>
	<view>
		<div class="tag_box" :class="[ start == 1 ? 'tag_box_1' : '' ]">
			<div 
				class="tag_item" 
				:class="[ Number(myTag) == index ? ( bg == true ? 'tag_item_active2' : 'tag_item_active') : '' ]"
				:style="{ marginRight: marginRight }"
				v-for="(item,index) in list" 
				:key="index" 
				@click="chooseTip(index)"
				
			>
				{{ item.name }}
			</div>
		</div>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				myTag: -1
			}
		},
		props: {
			list: {
				type: [Array],
				default() {
					return []
				}
			},
			marginRight: {
				type: [String,Number],
				default() {
					return 0
				}
			},
			tag: {
				type: [String,Number],
				default() {
					return -1
				}
			},
			cancel: {
				type: Boolean,
				default() {
					return true
				}
			},
			bg: {
				type: Boolean,
				default() {
					return false
				}
			},
			start: {
				type: [String,Number],
				default() {
					return 0
				}
			}
		},
		watch: {
			tag(val) {
				this.myTag = val;
			}
		},
		created() {
			this.myTag = this.tag;
		},
		methods: {
			chooseTip(index) {
				if( index == this.myTag ){
					if( !this.cancel ) {
						return;
					}
					
					this.myTag = -1;
				} else {
					this.myTag = index;
				}
				
				this.$emit('change',this.myTag);
			}
		}
	}
</script>

<style>
	.tag_box{
		width: 100%;
		display: flex;
		box-sizing: border-box;
		padding: 10rpx 32rpx 30rpx;
		justify-content: center;
	}
	
	.tag_box_1{
		justify-content: flex-start !important;
	}
	
	.tag_item{
		width: 150rpx;
		height: 54rpx;
		font-size: 26rpx;
		background-color: rgba(240, 240, 240, 1);
		border-radius: 2rpx;
		box-sizing: border-box;
		/* margin-right: .29rpx; */
		display: flex;
		justify-content: center;
		align-items: center;
	}
	
	.tag_item:last-child{
		margin-right: 0 !important;
	}
	
	.tag_item_active{
		background-color: rgba(255, 238, 224, 1);
		color: rgba(254, 130, 30, 1);
		border: 1px solid #FE821E;
	}
	
	.tag_item_active2{
		background-color: rgba(254, 130, 30, 1);
		color: #fff;
		border: 1px solid #FE821E;
	}
</style>
