var housesComment = (function() {
	const html = `<div>
					<template v-if="list.length > 0">
						<template v-for="(comment,key) in list" >
							<template v-if="key < showMore">
							<!-- v-if="comment.type.indexOf(type) != -1" -->
								<template >
									<div class="nav-comment" :key="key" >
										<div class="nav-comment-top">
											<img v-if='comment.head' :src="$http.testUrl(comment.head)">
											<img v-else src="../../static/my/touxiang.png">
											<span>{{ comment.name }}</span>
										</div> 
										<span class="nav-comment-content van-multi-ellipsis--l2">{{ comment.say }}</span>
										<div class="nav-comment-bottom" v-if="comment.img.length > 0">
											<template v-for="(img,num) in comment.img">
												<img :src="$http.testUrl(img)" :key="num" v-if="num < 3" @click="$http.showImg(comment.img,num)">
											</template>
											<van-tag color="rgba(0, 0, 0, .6)" v-if="comment.img.length > 3">共{{ comment.img.length }}张</van-tag>
										</div>
										<span class="nav-comment-time" v-if="time">{{ comment.time }}</span>
									</div>
								</template>
							</template>
						</template>
					</template>
					<template v-else>
						<div class="list_null">
							<img src="../../static/null.png">
							<p>暂无数据</p>
						</div>
					</template>
					
				
				</div>`;
				
	
	return {
		template: html,
		data: function(){
			return {
				
			}
		},
		computed: {
			// 显示多少条
			showMore() {
				if( this.showAll ){
					return 9999;
				} else {
					return this.num;
				}
			},
		},
		props: {
			list: {
				type: Array,
				default() {
					return []
				}
			},
			showAll: {
				type: Boolean,
				default() {
					return true
				}
			},
			num: {
				type: [String, Number],
				default() {
					return 3
				}
			},
			// type: 0-所有 1-有图，2-实看
			type: {
				type: [String, Number],
				default() {
					return 0
				}
			},
			time: {
				type: Boolean,
				default() {
					return false
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