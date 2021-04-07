<template>
	<view class="site">
		<view class="content" :style="{ height: height*100 + 'rpx' }">
			<template v-if="list.length > 0">
				<view class="content-left">
					<view 
						class="content-left-item" 
						:class="choosing.left == index ? 'content-active' : ''"
						v-for="(item,index) in list" 
						:key="index"
						@click="choosingSite('left',index)"
					>
						<view class="content-left-item van-ellipsis" >
							{{ item.title }}
						</view>
					</view>
				</view>
				<view class="content-center">
					<template v-if="list[choosing.left].list && list[choosing.left].list.length > 0">
						<template v-for="(item,index) in list[choosing.left].list" >
							<view
								class="content-left-item" 
								:class="[
									choosing.center == index ? 'content-active' : '',
									(Object.keys(choosing.right).indexOf(String(index)) != -1 && choosing.right[index] && choosing.right[index].length > 0) ? 'content-active' : ''
								]"
								:key="index"
								@click="choosingSite('center',index)"
							>
								<view class="content-left-item van-ellipsis" >
									{{ item.name }}
								</view>
							</view>
						</template>
					</template>
				</view>
				<view class="content-right">
					<template v-if="list[choosing.left].list[choosing.center].list && list[choosing.left].list[choosing.center].list.length > 0">
						<template v-for="(item,index) in list[choosing.left].list[choosing.center].list" >
							<view
								class="content-left-item" 
								:class="(choosing.right[choosing.center] && choosing.right[choosing.center].indexOf(index) != -1) ? 'content-active' : ''"
								:key="index"
								@click="choosingSite('right',index)"
							>
								<view class="content-left-item-right">
									<view class="content-left-item-right van-ellipsis" >
										{{ item.name }}
									</view>
									<span 
										class="iconfont"
										:class="(choosing.right[choosing.center] && choosing.right[choosing.center].indexOf(index) != -1) ? 'iconnewxuanzhongduoxuan' : 'iconweixuanzhong'"
									>
									</span>
								</view>
								
							</view>
						</template>
					</template>
				</view>
			</template>
		</view>
		<view class="location-btn">
			<template v-if="!clear">
				<u-button
					class="location-btn-close" 
					:custom-style="{
						border: '1px solid rgba(134, 186, 122, 1)',
						color: 'rgba(134, 186, 122, 1)',
						height: '88rpx',
						lineHeight: '88rpx'
					}"
					plain 
					@click="close"
				>
					{{ clear == true ? '重置' : '取消' }}
				</u-button>
			</template>
			<template v-else>
				<div class="location-btn-icon" @click="close">
					<i class="iconfont iconqingchu"></i>
					<span>重置</span>
				</div>
			</template>
			
			<u-button
				class="location-btn-sure" 
				:custom-style="{
					color: '#fff',
					backgroundColor: 'rgba(254, 130, 30, 1)',
					height: '88rpx',
					lineHeight: '88rpx'
				}"

				@click="sure"
			>
				确认
			</u-button>
		</view>
	</view>
</template>

<script>
	export default {
		// inject: ['default_data'],
		data() {
			return {
				choosing: {
					left: 0,
					center: 0,
					right: {}
				},
				lastChoose: {
					left: 0,
					center: 0,
					right: {}
				}
			};
		},
		props: {
			list: {
				type: [Array],
				default () {
					return []
				}
			},
			height: {
				type: [String, Number],
				default () {
					return '716'
				}
			},
			clear: {
				type: Boolean,
				default () {
					return true
				}
			},
			default_data: {
				type: [Object],
				default () {
					return {}
				}
			}
		},
		created() {
			if (this.default_data && Object.keys(this.default_data).length) {
				let list = [];
				//选择左边列
				if (this.default_data.site_center.type == 'area') {
					list = this.list[0].list
					this.choosingSite('left', 0) //选中区域
				} else if (this.default_data.site_center.type == 'subway') {
			
					list = this.list[1].list
					this.choosingSite('left', 1) //选中地铁
				} else {
					return
				}
				//选择中间列
				this.setColumnById(this.default_data.site_center.pid, list, 'center')
			
				//选择右边列
				if (typeof(this.default_data.id) == 'object') { //多选
					this.default_data.id.map((item) => {
						this.setColumnById(item, list, 'right')
					})
				} else { //单选
					this.setColumnById(this.default_data.id, list, 'right')
				}
				this.lastChoose = this.$api.deepClone(this.choosing);
			}
			
		},
		methods: {
			setColumnById(id, list, type = 0) {
				let index1 = 0;
				let index2 = 0;
				if (type == 'center') {
					id && list && list.map((item, idx) => {
						if (item.id == id) {
							index1 = idx
							return;
						}
					})
					this.choosingSite('center', index1)
				}
				if (type == 'right') {
					id && list && list.map((item) => {
						item.list && item.list.map((item2, idx2) => {
							if (String(id).indexOf('p_') != -1) { //选了父级的
								return
							}
							if (item2.id == id) {
								index2 = idx2
								return;
							}
						})
					})
			
					index2 && this.choosingSite('right', index2)
				}
			},
			choosingSite(key, index) {
				const el = this.choosing[key];
			
				switch (key) {
					case 'left':
			
						this.choosing = {
							left: 0,
							center: 0,
							right: {},
						}
			
						this.choosing[key] = index;
						break;
					case 'center':
						// // 不限
						// if( index == 0 ){
						// 	this.choosing.right = {};
						// }
			
						// 中间只能单选
						this.$set(this.choosing, 'right', {});
						this.choosing[key] = index;
			
						this.$set(this.choosing['right'], this.choosing['center'], [0]);
						const id = this.list[this.choosing['left']].list[this.choosing['center']].id;
						// this.$emit('sure',this.list[this.choosing['left']].list[this.choosing['center']].name,id);
						break;
					case 'right':
			
						let len = 0;
			
						for (let key in el) {
							len += el[key].length;
						}
			
						// 是否有选择
						if (el[this.choosing['center']]) {
			
							const num = el[this.choosing['center']].indexOf(index);
			
							// 是否已选
							if (num == -1) {
			
								// 是否是不限
								if (index != 0) {
			
									// 存在不限删除
									if (el[this.choosing['center']].indexOf(0) != -1) {
										el[this.choosing['center']].splice(el[this.choosing['center']].indexOf(0), 1);
									}
			
									// 长度<6
									if (len < 5) {
										this.choosing[key][this.choosing['center']].push(index);
									} else {
										this.$toast('最多只能选择5个区域哦')
									}
			
								} else {
									if (len < 5) {
										this.$set(this.choosing[key], this.choosing['center'], []);
										this.choosing[key][this.choosing['center']].push(index);
			
									} else {
										this.$toast('最多只能选择5个区域哦')
									}
								}
							} else {
								el[this.choosing['center']].splice(num, 1);
							}
			
						} else {
							if (len < 5) {
								this.$set(this.choosing[key], this.choosing['center'], [index]);
							} else {
								this.$toast('最多只能选择5个区域哦')
							}
							// console.log(this.choosing)
						}
			
						return;
				}
			},
			reset() {
				this.choosing = this.$api.deepClone(this.lastChoose);
			},
			clearSite() {
				this.choosing = {
					left: 0,
					center: 0,
					right: {}
				};
			
				this.lastChoose = this.$api.deepClone(this.choosing);
			
				this.$emit('sure', '不限');
				this.$emit('close');
			},
			close() {
				if (this.clear == true) {
					this.clearSite();
				} else {
					this.reset();
					this.$emit('close');
				}
			},
			sure() {
				let text = '';
				let len = 0;
				let id = [];
				let center = {
					pid: 0,
					type: ''
				}; //中间列
			
				this.lastChoose = this.$api.deepClone(this.choosing);
			
				for (let k in this.choosing.right) {
					len += Number(this.choosing.right[k].length);
				}
			
				if (len == 0) {
					text = '不限';
				} else {
					const list = this.list[this.choosing.left].list;
					for (let key in this.choosing.right) {
						if (this.choosing.right[key].indexOf(0) != -1) { //	不限
							text += list[key].name + ',';
							id = 'p_' + list[key].id; //前缀p标识是父级id
							center.pid = list[key].id;
						} else {
							this.choosing.right[key].map(item => {
								text += list[key].list[item].name + ',';
								id.push(list[key].list[item].id);
								center.pid = list[key].id;
							})
						}
					}
					if (center.pid != 0) {
						if (this.choosing.left == '0') {
							center.type = 'area'
						} else {
							center.type = 'subway'
						}
					} else {
						center.type = ''
					}
				}
				this.$emit('sure', text, id, center);
				this.$emit('close');
			},
		},
	}
</script>

<style lang="scss"  scoped>
	.site{
		position: relative;
	}

	.content{
		display: flex;
		box-sizing: border-box;
		padding-bottom: 128rpx;
	}

	.content-left{
		
		width: 16.6%;
	}

	.content-center{
		width: 33.2%;
		border-left: 1rpx solid rgba(240, 240, 240, 1);
	}

	.content-right{
		width: 50.2%;
		border-left: 1rpx solid rgba(240, 240, 240, 1);
	}

	.content-left,
	.content-center,
	.content-right{
		display: flex;
		flex-direction: column;
		overflow-y: scroll;
	}

	.content-left-item{
		width: 100%;
		height: 100rpx;
		/* display: flex;
		align-items: center; */
		font-size: 32rpx;
		text-indent: 26rpx;
		line-height: 100rpx !important;
	}

	.content-left-item-right{
		width: 100%;
		height: 100rpx;
		display: flex;
		align-items: center;
		font-size: 32rpx;
		text-indent: 26rpx;
		line-height: 100rpx !important;
	}

	.content-left-item-right .iconfont{
		margin-right: 44rpx;
	}

	.content-active{
		color: rgba(254, 130, 30, 1);
	}

	.location-btn{
		width: 100%;
		height: 128rpx;
		position: absolute;
		bottom: 0;
		background-color: #fff;
		border-top: 1rpx solid rgba(240, 240, 240, 1);
		display: flex;
		justify-content: center;
		align-items: center;
		box-sizing: border-box;
		padding: 0 32rpx;
	}

	.location-btn-close,
	.location-btn-sure{
		flex: 1;
		height: 88rpx;
	}

	.location-btn-close{
		margin-right: 20rpx; 
	}
	
	.location-btn-icon{
		display: flex;
		flex-direction: column;
		align-items: center;
		padding: 0 42rpx 0 10rpx;
	}
	
	.location-btn-icon .iconfont{
		font-size: 44rpx;
	}
	
	.location-btn-icon span{
		font-size: 26rpx;
	}
</style>
