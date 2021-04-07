var commonLike = (function() {
	const html = `<div 
						class="like_state" 
						:class="[ state == 0 ? '' : 'text-active' ]"
						:style="{ padding: padding }"
						@click="like"
					>
						{{ num }}<i class="iconfont icondianzan"></i>
					</div>`;
	
	return {
		data: function(){
			return {
		
			}
		},
		template: html,
		props: {
			id: {
				type: [Number,String],
				default() {
					return -1
				}
			},
			num: {
				type: [Number,String],
				default() {
					return 0
				}
			},
			/**
			 * 0-文章
			 * 文章点赞状态走接口
			 * 
			 * 1-评论
			 * 评论点赞状态前端记录
			 * 1天为时限
			 */
			type: {
				type: [Number,String],
				default() {
					return 1
				}
			},
			state: {
				type: [Number,String],
				default() {
					return 0
				}
			},
			padding: {
				type: [String],
				default() {
					return ''
				}
			}
		},
		created() {
			
		},
		methods: {
			like() {
				this.$emit('like',this.id);
			}
		},
	}
}());