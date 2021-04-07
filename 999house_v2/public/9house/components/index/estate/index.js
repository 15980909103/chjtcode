var indexEstate = (function() {
	const html = `<div class="estate">
					<h4 class="estate-title">
						地产研究院
					</h4>
					<div class="estate-wrap">
						<div class="estate-left" :style="{ backgroundImage: 'url(../../static/index/bg.png)' }">
							<span class="estate-left-title">
								{{estateList.left.title}}
							</span>
							<span class="estate-left-text">
								{{estateList.left.text}}
							</span>
						</div>
						<div class="estate-right">
							<div class='estate-box clearfix'>
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
												:class="[item.percent.slice(0,-1) > 0 ? 'iconFill1beifen12 color-icon-orange' : 'iconFill1beifen11 color-icon-blue']"
											></span>
											{{item.percent.indexOf('-') == -1 ? item.percent : item.percent.slice(1,item.percent.length)}}
										</span>
									</div>
								</div>
							</div>
							
							<div class="estate-item" v-for="(item,index) in estateList.right.bottom" :key="index+2">
								<div class="estate-item-title">
									{{item.title.slice(0,-1)}}<span>&nbsp;{{item.title.slice(-1,item.title.length)}}</span>
								</div>
								<div class="estate-item-text">
									{{item.text}}
								</div>
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
						text: '九月均价'
					},
					right: {
						top: [
							{ 
								price: '暂无数据',
								text: '新房',
								tip: 0,
								percent: '0%'
							},
							{
								price: '暂无数据',
								text: '二手房',
								tip: 1,
								percent: '0%'
							}
						],
						bottom: [
							{ 
								title: '0盘',
								text: '近期开盘楼盘'
							},
							{
								title: '0条',
								text: '楼盘动态&资讯'
							}
						]
					}
				},
				city_no: 0
			}
		},
		created() {
			this.$http.getCurrentCity().then( data=>{
				this.city_no = data.city_no;
				this.getInstituteList();
			})
		},
		mounted() {
			
		},
		methods: {
			getInstituteList(){
				const data = {
					city: this.city_no,
					date: $api.timeFormat(),
				};
				
				this.$http.ajax({
					url: 'index/news/getInstituteList',
					data: data,
				}).then( res=>{
					let data = res.data;
					let city_name = data.city_no_name? data.city_no_name.replace('市','') : '';
					
					this.estateList = {
						left: {
							title: city_name+'行情',
							text: data.show_time+'月均价'
						},
						right: {
							top: [
								{ 
									price: data.price,
									text: '新房',
									tip: 0,
									percent: data.last_month_rate
								},
								/* {
									price: 35700,
									text: '二手房',
									tip: 1,
									percent: '0.18%'
								} */
							],
							bottom: [
								{ 
									title: data.recent_opening+'盘',
									text: '近期开盘楼盘'
								},
								{
									title: data.on_sale+'盘',
									//text: '楼盘动态&资讯'
									text: '在售楼盘'
								}
							]
						}
					}
				})
			},
		}
	}
}());