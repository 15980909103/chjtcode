const housesHotNews = (function() {
	const html = `<span>
						<template v-for="(news,key) in list">
							<div class="nav-news" :key="key" v-if="key < showNum">
								<div class="nav-news-top" :class="[ news.type == 1 ? 'van-multi-ellipsis--l2' : '' ]">
									<van-tag 
										:color="news.tip == '测评' ? 'rgba(134, 186, 122, 1)' : 'rgba(55, 141, 219, 1)'" plain
									>
										{{ news.tip }}
									</van-tag>
									{{ news.title }}
								</div>
								
								<template v-if="news.img">
									<div class="nav-news-bottom" v-if="news.img.length > 0">
										<template  v-for="(img,num) in news.img" >
											<img :src="img" :key="num" v-if="num < 3">
										</template>
										<van-tag color="rgba(0, 0, 0, .6)" v-if="news.img.length > 3">共{{ news.img.length }}张</van-tag>
									</div>
								</template>
								<template v-else-if="news.video">
									<div class="nav-news-bottom" v-if="news.video.length > 0">
										<template  v-for="(video,num) in news.video" >
											<div class="nav-news-video" :key="num" v-if="num < 2">
												视频预留位
											</div>
										</template>
									</div>
								</template>
							</div>
						</template>
					</span>`;
				
	
	return {
		template: html,
		data: function(){
			return {
				
			}
		},
		computed: {

		},
		props: {
			list: {
				type: Array,
				default() {
					return []
				}
			},
			showNum: {
				type: Number,
				default() {
					return 99999
				}
			}
		},
		created() {
			
		},
		mounted() {
			
		},
		methods: {
			
		}
	}
}());