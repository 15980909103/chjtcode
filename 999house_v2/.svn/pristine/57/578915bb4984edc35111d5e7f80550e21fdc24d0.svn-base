var commonTag = (function() {
	const html = `<div class="tag_box" :class="[ start == 1 ? 'tag_box_1' : '' ]">
						<div 
							class="tag_item" 
							:class="[ Number(myTag) == index ? ( bg == true ? 'tag_item_active2' : 'tag_item_active') : '' ]"
							:style="{ marginRight: marginRight }"
							v-for="(item,index) in list" 
							:key="index" 
							@click="chooseTip(index)"
							
						>
							{{ item.name }}
						</div>
					</div>`;
	
	return {
		data: function(){
			return {
				myTag: -1
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
			marginRight: {
				type: [String,Number],
				default() {
					return 0
				}
			},
			tag: {
				type: [String,Number],
				default() {
					return -1
				}
			},
			cancel: {
				type: Boolean,
				default() {
					return true
				}
			},
			bg: {
				type: Boolean,
				default() {
					return false
				}
			},
			start: {
				type: [String,Number],
				default() {
					return 0
				}
			}
		},
		watch: {
			tag(val) {
				this.myTag = val;
			}
		},
		created() {
			this.myTag = this.tag;
		},
		methods: {
			chooseTip(index) {
				if( index == this.myTag ){
					if( !this.cancel ) {
						return;
					}
					
					this.myTag = -1;
				} else {
					this.myTag = index;
				}
				
				this.$emit('change',this.myTag);
			}
		},
	}
}());