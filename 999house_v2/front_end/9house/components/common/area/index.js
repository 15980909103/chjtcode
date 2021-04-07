const commonArea = (function() {
	const html = `<div class="site">
					<div class="content" :style="{ height: height + 'rem' }">
						<div class="content-left">
							<div 
								class="content-left-item" 
								:class="choosing.left == index ? 'content-active' : ''"
								v-for="(item,index) in list" 
								:key="index"
								@click="choosingSite('left',index)"
							>
								<div class="content-left-item van-ellipsis" >
									{{ item.name }}
								</div>
							</div>
						</div>
						<div class="content-center">
							<template v-if="list[choosing.left].list && list[choosing.left].list.length > 0">
								<template v-for="(item,index) in list[choosing.left].list" >
									<div
										class="content-left-item" 
										:class="[
											choosing.center == index ? 'content-active' : '',
											(Object.keys(choosing.right).indexOf(String(index)) != -1 && choosing.right[index] && choosing.right[index].length > 0) ? 'content-active' : ''
										]"
										:key="index"
										@click="choosingSite('center',index)"
									>
										<div class="content-left-item van-ellipsis" >
											{{ item.name }}
										</div>
										
									</div>
								</template>
							</template>
						</div>
						<div class="content-right">
							<template v-if="list[choosing.left].list[choosing.center].list && list[choosing.left].list[choosing.center].list.length > 0">
								<template v-for="(item,index) in list[choosing.left].list[choosing.center].list" >
									<div
										class="content-left-item" 
										:class="(choosing.right[choosing.center] && choosing.right[choosing.center].indexOf(index) != -1) ? 'content-active' : ''"
										:key="index"
										@click="choosingSite('right',index)"
									>
										<div class="content-left-item van-ellipsis" >
											{{ item.name }}
										</div>
										<span 
											class="iconfont"
											:class="(choosing.right[choosing.center] && choosing.right[choosing.center].indexOf(index) != -1) ? 'iconnewxuanzhongduoxuan' : 'iconweixuanzhong'"
										>
										</span>
									</div>
								</template>
							</template>
						</div>
					</div>
					<div class="location-btn">
						<van-button 
							class="location-btn-close" 
							type="default" 
							plain 
							@click="close"
						>
							重置
						</van-button>
						<van-button 
							class="location-btn-sure" 
							type="default"
							color="rgba(254, 130, 30, 1)"
							@click="sure"
						>
							确认
						</van-button>
					</div>
				</div>`;
	
	return {
		data: function(){
			return {
				choosing: {
					left: 0,
					center: 0,
					right: {}
				}
			}
		},
		template: html,
		props: {
			list: {
				type: [Array],
				default() {
					return []
				}
			},
			height: {
				type: [String,Number],
				default() {
					return '7.16'
				}
			}
		},
		created() {
			
		},
		methods: {
			choosingSite( key, index ){
				const el = this.choosing[key];
				
				switch(key){
					case 'left':
					
						this.choosing = {
							left: 0,
							center: 0,
							right: {},
						}
						
						break;
					case 'center':
						
						// 不限
						if( index == 0 ){
							this.choosing.right = {};
						}
						
						break;
					case 'right':
					
						let len = 0;
						
						for( let key in el ){
							len += el[key].length;
						}
						
						// 是否有选择
						if( el[this.choosing['center']] ){
							
							const num = el[this.choosing['center']].indexOf(index);
							
							// 是否已选
							if( num == -1 ){
								
								// 是否是不限
								if( index != 0 ){
									
									// 存在不限删除
									if( el[this.choosing['center']].indexOf(0) != -1 ){
										el[this.choosing['center']].splice(el[this.choosing['center']].indexOf(0),1);
									}
									
									
									// 长度<6
									if( len < 5 ){
										this.choosing[key][this.choosing['center']].push(index);
									} else {
										this.$toast('最多只能选择5个区域哦')
									}
									
								} else {
									if( len < 5 ){
										this.$set(this.choosing[key],this.choosing['center'],[]);
										this.choosing[key][this.choosing['center']].push(index);
									} else {
										this.$toast('最多只能选择5个区域哦')
									}
								}
							} else {
								el[this.choosing['center']].splice(num,1);
							}
							
						} else {
							if( len < 5 ){
								this.$set(this.choosing[key],this.choosing['center'],[index]);
							} else {
								this.$toast('最多只能选择5个区域哦')
							}
						}
						
						return;
				}
				
				this.choosing[key] = index;
			},
			close() {
				this.choosing = {
					left: 0,
					center: 0,
					right: {}
				};
				
				this.$emit('sure','不限');
			},
			sure() {
				let text = '';
				let len = 0;
				
				for( let k in this.choosing.right ){
					len += Number(this.choosing.right[k].length);
				}
				
				if( len == 0 ){
					text = '不限';
				} else {
					const list = this.list[this.choosing.left].list;
					
					for( let key in this.choosing.right ){
						if( this.choosing.right[key].indexOf(0) != -1 ){	//	不限
							text += list[key].name + ',';
						} else {
							this.choosing.right[key].map( item =>{
								text += list[key].list[item].name + ',';
							})
						}
					}
				}
				
				this.$emit('sure',text);
			},
		},
	}
}());