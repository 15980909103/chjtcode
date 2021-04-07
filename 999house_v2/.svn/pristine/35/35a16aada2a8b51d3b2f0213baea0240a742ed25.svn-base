var housesTemplate = (function() {
	const html = `<div>
						<div class="template-house" v-for="(house,key) in list" :key="key">
							<img :src="$http.testUrl(house.img)" @click="$http.showImg(house.img)">
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
								<div v-if="house.price" class="template-house-price">
									约{{ house.price }}万/套 
								</div>
								<div v-else-if="house.price_ave" class="template-house-price">
									{{ house.price_ave }}元/m²
								</div>
								<div v-else class="template-house-price">价格待定</div>
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

		}
	}
}());