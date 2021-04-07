var topBar = (function() {
	const html = `
					<div v-if="pageShow" class="top_title_box">
						<template v-if="type == 0">
							<div class="top_title" :style="{ marginBottom: marginB + 'rem' }" v-if="type == 0">
								<i class="iconfont iconjiantou1-copy-copy" @click="goBack"></i>
								<span>{{ title }}</span>
							</div>
							<div class="top_title_1"></div>
						</template>
						<div class="top_title_2" v-else>
							<i class="iconfont" :class="[icon]" :style="iconStyle" @click="goBack"></i>
						</div>
					</div>
				`;
	
	return {
		data: function(){
			return {
				pageShow:false,
			}
		},
		template: html,
		props: {
			type: {
				type: [String,Number],
				default() {
					return 0
				}
			},
			title: {
				type: String,
				default() {
					return ''
				}
			},
			marginB: {
				type: [String,Number],
				default() {
					return 0
				}
			},
			icon: {
				type: String,
				default() {
					return 'iconjiantou1-copy-copy'
				}
			},
			iconStyle: {
				type: Object,
				default() {
					return {}
				}
			},
			customBack: {
				type: Boolean,
				default() {
					return false
				}
			}
		},
		created(){
			if( this.$http.isWechat().isMini!=true || this.customBack ){
				this.pageShow = true;
			}
		},
		methods: {
			goBack(){
				if( this.customBack ){
					this.$emit('back');
					return;
				};
				
				let tabBack = this.$api.sessionGet('tabBack');
				// console.log(tabBack)
				// return
				if( tabBack && tabBack.length > 0 ){
					const url = tabBack[tabBack.length-1];
					tabBack.splice(tabBack.length-1,1);
					this.$api.sessionSet('tabBack', tabBack);
					
					window.history.go(-1);
					// this.$api.goPage( url, {}, '/9house/pages/', 1);
				} else {
					this.$api.goPage('index/index.html');
				}
			}
		},
	}
}());