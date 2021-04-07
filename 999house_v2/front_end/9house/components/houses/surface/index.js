const housesSurface = (function() {
	const html = `<transition name="van-slide-down">
						<div
							class="surface" 
							:style="[ list && list.bg ? {backgroundImage: 'url('+ list.bg +')'} : {} ]" 
							v-show="show"
							@touchstart="touchstart"
							@touchend="touchend"
						>
							<div class="surface-right">
								<span><img src="../../static/houses/vr.png"></span>
								<span><img src="../../static/houses/play.png"></span>
								<span><img src="../../static/houses/vr.png"></span>
							</div>
							
							<img :src="list && list.logo ? list.logo : ''" class="surface-icon">
							
							<div class="surface-bottom">
								<div class="surface-info">
									<div class="surface-box-title">
										<div class="title">
											{{list && list.title ? list.title : ''}}
											
											<template v-if="list && list.tip">
												<template v-for="(item,index) in list.tip">
													<van-tag type="primary" :key="index" v-if="index < 3">{{item}}</van-tag>
												</template>
											</template>
										</div>
										<div class="price" v-if="list && list.price">
											{{list.price}}
											<span>元/m²</span>
										</div>
									</div>
									<div class="surface-info-box">
										<span class="van-ellipsis">楼盘地址：{{list && list.site ? list.site : ''}}</span>
										<span class="van-ellipsis">开盘时间：{{list && list.time ? list.time : ''}}</span>
										<span class="van-ellipsis">免费咨询：{{list && list.phone ? list.phone : ''}}</span>
										<a :href="'tel:'+list.phone" class="surface-phone" v-if="list && list.phone"><div class="iconfont icondianhua"></div></a>
									</div>
									
								</div>
								<div class="surface-hint">
									<span>向上滑动查看更多</span>
									<span class="iconfont iconshuangjiantouxia"></span>
								</div>
							</div>
							
						</div>
					</transition>`;
				
	
	return {
		template: html,
		data: function(){
			return {
				startY: 0
			}
		},
		props: {
			show: {
				type: Boolean,
				default() {
					return true
				}
			},
			list: {
				type: Object,
				default() {
					return {}
				}
			}
		},
		created() {
			
		},
		mounted() {
			
		},
		methods: {
			touchstart(evt) {
				try{
					const touch = evt.changedTouches[0];
					const y = Number(touch.pageY); 
					//记录触点初始位置
					this.startY = y;
				}catch(e){
					console.log(e.message)
				}
			},
			touchend(evt) {
				 try{
					const touch = evt.changedTouches[0];
					const y = Number(touch.pageY);
					
					if( this.startY - y > 40 ){
						this.$emit('hide')
					}
				}catch(e){
					console.log(e.message)
				}
			},
		}
	}
}());