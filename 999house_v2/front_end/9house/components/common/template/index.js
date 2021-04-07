const commonTemplate = (function() {
	const html = `<span class="all-template">
						<div
							:class="[item.type != 6 ? 'template' : 'template2']" 
							v-for="(item,key) in list" 
							:key="key"
						>
							<!-- 资讯 -->
							<template v-if="item.type != 6 && item.type != 8">
								<!-- 楼盘广告顶部 -->
								<template v-if="item.type == 4 || item.type == 7 || item.type == 9 || item.type == 10">
									<div class="template-top-houses">
										<div class="template-top-houses-name">
											{{item.info.name}}
											<template v-if="item.info.tip && item.info.tip.length > 0">
												<template v-for="(houseTip,houseKey) in item.info.tip" >
													<span 
														:key="houseKey" 
														v-if="houseKey < 4"
														:class="[
															houseTip == '在售' ? 'houses-bg-blue' : '',
															houseTip == '待售' ? 'houses-bg-orange' : '',
															houseTip == '售完' ? 'houses-bg-purple' : '',
															houseTip == '尾盘' ? 'houses-bg-blue2' : '',
														]"
													>
														{{houseTip}}
													</span>
												</template>
											</template>
											
										</div>
										<div class="template-top-houses-price">
											<div>
												<span>{{item.info.price}}</span>元/m²
											</div>
											<span class="template-top-houses-site">{{item.info.site}}</span>
											<span>建面{{item.info.area}}m²</span>
										</div>
									</div>
								</template>
								<div 
									class="template-top" 
									:class="[ 
										item.type == 1 && item.img && item.img.length == 1 ? 'template-news-1' : '',
										(item.type == 1 || item.type == 2 || item.type == 4 || item.type == 10) && item.img && item.img.length == 2 ? 'template-news-2' : '',
										(item.type == 1 || item.type == 2 || item.type == 4 || item.type == 10) && item.img && item.img.length > 2 ? 'template-news-3' : '',
										(item.type == 2 || item.type == 4 || item.type == 10) && item.img && item.img.length == 1 ? 'template-ad-1' : '',
									]"
								>
									<h4 class="template-title">
										<span v-if="(item.type == 0 || item.type == 1)">
											<span>
												<img src="/9house/static/index/fire.png" v-if="item.hot == 1">
												<span 
													class="template-title-write" 
													:class="[item.hot == 1 && item.write == 1 ? 'margin-left' : '']"
													v-if="item.write == 1"
												>
													原创
												</span>
											</span>
										</span>
										{{item.title}}
									</h4>
									
									<!-- 资讯/广告有图 -->
									<span 
										:class="[ 
											item.type == 1 && item.img && item.img.length == 1 ? 'template-top-1-pic' : '',
										]"
										v-if="item.type != 0 && item.type != 3 && item.type != 5 && item.type != 7 && item.type != 9"
									>
										<template v-for="(url, newKey) in item.img">
											<img :src="url" v-if="newKey < 3" :key="newKey">
										</template>
									</span>
									
									<!-- 广告视频占位 -->
									<template v-if="item.type == 3 || item.type == 5 || item.type == 7 || item.type == 9">
										<div class="template-ad-video">
											广告视频占位
										</div>
									</template>
								</div>
								<div class="template-bottom" v-if="item.type != 9 && item.type != 10">
									<!-- 资讯 / 单视频 -->
									<template v-if="item.type == 0 || item.type == 1 || item.type == 5">
										<div class="template-bottom-left">
											<img :src="item.author.head">
											<span>
												{{item.author.name}} · {{item.readNum}}次阅读 · {{item.commentNum}}评论
											</span>
										</div>
										<div class="template-bottom-news">
											<template v-for="(newsTip, newsTipKey) in item.tip">
												<span class="template-bottom-tip" :key="newsTipKey" v-if="newsTipKey < 3">
													{{newsTip}}
												</span>
											</template>
										</div>
										
									</template>
									<!-- 资讯/视频广告 -->
									<template v-if="item.type == 2 || item.type == 3">
										<div class="template-bottom-tip">
											广告
										</div>
										<span class="template-bottom-ad-del">
											<span class="iconfont iconlujing"></span>
										</span>
									</template>
									<!-- 楼盘广告 -->
									<template v-if="item.type == 4">
										<div class="template-bottom-left">
											<template v-for="(house,houseIndex) in item.info.lab">
												<span class="template-bottom-houses-tip" :key="houseIndex" v-if="houseIndex < 2">
													<img :src="house.img">
													<span>{{house.name}}</span>
												</span>
											</template>
										</div>
										<div class="template-bottom-right">
											<span class="template-bottom-tip">
												广告
											</span>
											<span class="template-bottom-ad-del">
												<span class="iconfont iconlujing"></span>
											</span>
										</div>
									</template>
								</div>
							</template>
							<template v-else-if="item.type == 6">
								<h4 class="template-title template2-title" v-if="titleShow">精彩小视频</h4>
								<div class="template2-video">
									<div class="template2-video-item" v-for="(list,key) in item.list" :key="key">
										<div class="template2-video-bg">
											小视频占位
										</div>
										<div class="template2-video-top">
											<span>{{list.tip}}</span>
											<span>{{list.view}}人</span>
										</div>
										<div class="template2-video-bottom">
											{{list.title}}
										</div>
									</div>
									<!-- 占位 -->
									<div class="template2-video-place">
										<div>
											<span class="template2-video-place-box">
												<img src="/9house/static/index/more.png">
												<span>查看更多</span>
											</span>
										</div>
									</div>
								</div>
							</template>
							
							<!-- 新房 -->
							<template v-else>
								<div class="template3">
									<div class="template3-top">
										<img :src="item.img[0]" class="template3-top-img">
										<div class="template3-top-right">
											<div class="template3-title">
												<span 
													:class="[
														item.info.tip[0] == '在售' ? 'houses-bg-blue' : '',
														item.info.tip[0] == '待售' ? 'houses-bg-orange' : '',
														item.info.tip[0] == '售完' ? 'houses-bg-purple' : '',
														item.info.tip[0] == '尾盘' ? 'houses-bg-blue2' : '',
													]"
												>
													{{item.info.tip[0]}}
												</span>
												{{item.info.name}}
											</div>
											<div class="template3-tip">
												<template v-for="(tip,num) in item.info.tip">
													<div v-if="num != 0" :key="num">
														{{tip}}
													</div>
												</template>
											</div>
											<div class="template3-site">
												<span class="template-top-houses-site">{{item.info.site}}</span>
												<span>建面{{item.info.area}}m²</span>
											</div>
											<div class="template-top-houses-price">
												<div>
													<span>{{item.info.price}}</span>元/m²
												</div>
											</div>
											
										</div>
									</div>
									<div class="template3-bottom">
										<template v-for="(house,houseIndex) in item.info.lab">
											<span class="template3-bottom-tip" :key="houseIndex" v-if="houseIndex < 2 && house.type == 0">
												<img :src="house.img">
												<span class="van-ellipsis">{{house.name}}</span>
											</span>
											<span class="template3-bottom-tip template3-bottom-vr" :key="houseIndex" v-if="houseIndex < 2 && house.type == 1">
												<span class="van-ellipsis">{{house.name}}</span>
											</span>
										</template>
									</div>
								</div>
							</template>
						</div>
					</span>`;
	
	return {
		data: function(){
			return {
				
			}
		},
		template: html,
		props: {
			list: {
				type: Array,
				default() {
					return []
				}
			},
			titleShow: {
				type: Boolean,
				default() {
					return true
				}
			},
		},
		methods: {
	
		},
	}
}());