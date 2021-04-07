const indexEstate = (function() {
	const html = `<div class="estate">
					<h4 class="estate-title">
						地产研究院
					</h4>
					<div class="estate-wrap">
						<div class="estate-left" :style="{ backgroundImage: 'url('+estateList.left.bg+')' }">
							<span class="estate-left-title">
								{{estateList.left.title}}
							</span>
							<span class="estate-left-text">
								{{estateList.left.text}}
							</span>
						</div>
						<div class="estate-right">
							<div class="estate-item" v-for="(item,index) in estateList.right.top" :key="index">
								<span class="estate-item-title">
									{{item.price}}<span>&nbsp;元/m²</span>
								</span>
								<div class="estate-item-text">
									<span>
										{{item.text}}
									</span>
									<span class="estate-percent">
										<span 
											class="iconfont" 
											:class="[item.tip == 0 ? 'iconFill1beifen12 color-icon-orange' : 'iconFill1beifen11 color-icon-blue']"
										></span>
										{{item.percent}}
									</span>
								</div>
							</div>
							<div class="estate-item" v-for="(item,index) in estateList.right.bottom" :key="index+2">
								<span class="estate-item-title">
									{{item.title}}
								</span>
								<span class="estate-item-text">
									{{item.text}}
								</span>
							</div>
						</div>
					</div>
				</div>`;
				
	
	return {
		template: html,
		data: function(){
			return {
				estateList: {
					left: {
						title: '厦门行情',
						text: '九月均价',
						bg: 'static/logo.png'
					},
					right: {
						top: [
							{ 
								price: 45900,
								text: '新房',
								tip: 0,
								percent: '0.23%'
							},
							{
								price: 35700,
								text: '二手房',
								tip: 1,
								percent: '0.18%'
							}
						],
						bottom: [
							{ 
								title: '6盘',
								text: '近期开盘楼盘'
							},
							{
								title: '4条',
								text: '楼盘动态&资讯'
							}
						]
					}
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