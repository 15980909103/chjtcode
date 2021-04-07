const housesTemplate = (function() {
	const html = `<div>
						<div class="template-house" v-for="(house,key) in list" :key="key">
							<img :src="house.img" @click="lookImg(house.img)">
							<div class="template-house-box">
								<div class="template-house-title">
									<h4>{{ house.title }}</h4>
									<van-tag 
										type="primary" 
										color="rgba(246, 247, 248, 1)"
										text-color="rgba(90, 96, 102, 1)"
										v-for="(tip,num) in house.tip" 
										:key="num"
									>
										{{ tip }}
									</van-tag>
								</div>
								<div>
									建面{{ house.area }}m² 朝向{{ house.way }}
								</div>
								<div class="template-house-price">
									约{{ house.price }}万/套 
								</div>
							</div>
						</div>
					</div>`;
				
	
	return {
		template: html,
		data: function(){
			return {
				
			}
		},
		props: {
			list: {
				type: Array,
				default() {
					return []
				}
			}
		},
		created() {
			
		},
		mounted() {
			
		},
		methods: {
			lookImg(img) {
				
			}
		}
	}
}());