var commonTabbar = (function() {
	const html = `<div class="page-nav" v-show='show'>
						<van-tabbar v-model="myActive" active-color="rgba(254, 130, 30, 1)" @change="change">
						  
						  <van-tabbar-item v-for="(item,index) in nav" :key="index" :name="item.name">
							<span>{{ item.name }}</span>
							<template #icon="props">
							  <span class="iconfont" :class="item.icon"></span>
							</template>
						  </van-tabbar-item>
						</van-tabbar>
					</div>`;
	
	return {
		data: function(){
			return {
				show: true,
				myActive: 0,
				nav: [
					{
						name: '首页',
						icon: 'iconzhuye'
					},
					{
						name: '发现',
						icon: 'iconfaxian',
						
					},
					// {
					// 	name: '微聊',
					// 	icon: 'iconweiliao'
					// },
					// {
					// 	name: '商城',
					// 	icon: 'iconshangcheng'
					// },
					{
						name: '我的',
						icon: 'iconwode'
					}
				]
			}
		},
		template: html,
		props: {
			active: {
				type: [Number,String],
				default() {
					return '首页'
				}
			}
		},
		created() {
			this.myActive = this.active;

			this.show = !this.$http.isWechat().isMini;
		},
		methods: {
			change(active) {
				let url = '';
				
				switch( active ){
					case '首页':
						url = 'index/index.html';
						break;
					case '发现':
						url = 'discover/index.html';
						break;
					case '我的':
						url = 'my/index.html';
						break;
				}
				
				this.$api.goPage(url);
				
				this.myActive = this.active;
			}
		},
	}
}());