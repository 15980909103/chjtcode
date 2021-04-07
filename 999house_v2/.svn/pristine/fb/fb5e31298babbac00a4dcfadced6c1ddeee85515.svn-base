const commonTabbar = (function() {
	const html = `<div class="page-nav">
						<van-tabbar v-model="myActive" active-color="rgba(254, 130, 30, 1)">
						  <van-tabbar-item>
							<span>首页</span>
							<template #icon="props">
							  <span class="iconfont iconzhuye"></span>
							</template>
						  </van-tabbar-item>
						  <van-tabbar-item>
							<span>发现</span>
							<template #icon="props">
							  <span class="iconfont iconfaxian"></span>
							</template>
						  </van-tabbar-item>
						  <van-tabbar-item>
							<span>微聊</span>
							<template #icon="props">
							  <span class="iconfont iconweiliao"></span>
							</template>
						  </van-tabbar-item>
						<!--  <van-tabbar-item>
							<span>商城</span>
							<template #icon="props">
							  <span class="iconfont iconshangcheng"></span>
							</template>
						  </van-tabbar-item> -->
						  <van-tabbar-item>
							<span>我的</span>
							<template #icon="props">
							  <span class="iconfont iconwode"></span>
							</template>
						  </van-tabbar-item>
						</van-tabbar>
					</div>`;
	
	return {
		data: function(){
			return {
				myActive: 0,
			}
		},
		template: html,
		props: {
			active: {
				type: [Number,String],
				default() {
					return 0
				}
			}
		},
		created() {
			this.myActive = this.active;
		},
		methods: {
	
		},
	}
}());