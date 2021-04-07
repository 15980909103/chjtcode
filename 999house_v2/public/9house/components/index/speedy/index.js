var indexSpeedy = (function() {
	const html = `<div class="speedy">
				<div class="speedy-card" v-for="(item, index) in speedyList" :key="index">
					<div class="speedy-wrap">
					  <div class="speedy-item">
						  <div class="speedy-title text-omit">{{item.title}}</div>
						  <div v-if='index==0&&find_house_total>0' class="speedy-text text-omit">新楼盘<span class="speedy-text-active"> {{find_house_total}} </span>个</div>
						  <div v-else class="speedy-text text-omit">{{item.text}}</div>
					  </div>
					  <img :src="item.img" class="speedy-img">
					</div>
					<div class="speedy-btn" @click="$api.goPage(item.url)">{{item.btn}}</div>
				</div>
			</div>`;
	// <van-tabs
	// 	class="speedy-card"
	// 	v-model="active" 
	// 	animated
	// >
	//   <van-tab v-for="(item, index) in speedyList" :key="index" :title="item.tip" >
	// 	  <div class="speedy-wrap">
	// 		  <div class="speedy-item">
	// 			  <div class="speedy-title text-omit">{{item.title}}</div>
	// 			  <div v-if='index==0&&find_house_total>0' class="speedy-text text-omit">新楼盘<span class="speedy-text-active"> {{find_house_total}} </span>个</div>
	// 			  <div v-else class="speedy-text text-omit">{{item.text}}</div>
	// 		  </div>
	// 		  <img :src="item.img" class="speedy-img">
	// 	  </div>
	// 	  <div class="speedy-btn" @click="$api.goPage(item.url)">{{item.btn}}</div>
	//   </van-tab>
	// </van-tabs>
	return {
		template: html,
		data: function(){
			return {
				active: 0,
				speedyList: [
					// {
					// 	tip: '快速找房',
					// 	title: '为您线上快速找房',
					// 	text: '智能AI匹配，定制推荐，动态提醒，专项服务',
					// 	img: '/9house/static/logo.png',
					// 	btn: '立即找房',
					// 	url: 'index/find_house.html'
					// },
					// {
					// 	tip: '我的房子',
					// 	title: '为您线上快速找房，为您线上快速找房，为您线上快速找房，为您线上快速找房',
					// 	text: '智能AI匹配，定制推荐，动态提醒，专项服务，智能AI匹配，定制推荐，动态提醒，专项服务',
					// 	img: '/9house/static/logo.png',
					// 	btn: '立即查看'
					// }
				],
				find_house_total: '',
				city_no: 0
			}
		},
		watch:{
			find_house_total(val){
				this.speedyList[0] = {
					tip: '快速找房',
					img: '/9house/static/find-img.png',
				}
				
				if(val>0){
					this.speedyList[0] = {
						...this.speedyList[0] ,
						title: '已为您智能匹配房源',
						btn: '立即查看',
						url: 'index/find_result.html'
					}
				}else{
					this.speedyList[0] = {
						...this.speedyList[0] ,
						title: '为您线上快速找房',
						text: '智能AI匹配，定制推荐，动态提醒，专项服务',
						btn: '立即找房',
						url: 'index/find_house.html'
					}
				}
				this.$forceUpdate();
			}
		},
		created(){
			this.$http.getCurrentCity().then( data=>{
				this.city_no = data.city_no;
				//快速找房
				this.getEstatesList();
			})
		},
		methods: {
			intLocalUChoose(){
				let uChoose = $api.localGet('user_find_house');
				let new_uChoose = {};
				if(!uChoose||Object.keys(uChoose).length==0){
					return new_uChoose
				}
				if(uChoose.like){
					uChoose.like.map((item,index)=>{
						new_uChoose['tags['+index+']'] = item;
					})
				}
				if(uChoose.area){
					new_uChoose.built_area = uChoose.area;
				}
				if(!uChoose.site_id){
					new_uChoose.city_no = this.city_no;
				}else{
					if(typeof(uChoose.site_id)=='object'){
						uChoose.site_id.map((item,index)=>{
							if(uChoose.site_center.type=='area'){
								if(item){
									new_uChoose['business_no['+index+']'] = item;
								}
							}else if(uChoose.site_center.type=='subway'){//地铁站点
								if(item){
									new_uChoose['sites['+index+']'] = item;
									new_uChoose.city_no = this.city_no;
								}
							}
						})
					}else{
						if(String(uChoose.site_id).indexOf('p_')!=-1){//去除父级标识
							uChoose.site_id = uChoose.site_id.replace('p_','')
						}

						if(uChoose.site_center.type=='area'){
							new_uChoose.area_no = uChoose.site_id;
						}else if(uChoose.site_center.type=='subway'){//地铁
							new_uChoose.subway = uChoose.site_id;
							new_uChoose.city_no = this.city_no;
						}
					}
				}
				if(uChoose.budget_str&&uChoose.budget&&Number(uChoose.budget)<1000){
					new_uChoose.price_type = 'total' 
					new_uChoose.price = uChoose.budget_str
				}
				new_uChoose.no_adv = 1;
				new_uChoose.page_size = 20;
				new_uChoose.purpose = uChoose.aim;
				new_uChoose.has_num = uChoose.manyHouse;

				return new_uChoose
			},
			getEstatesList(){
				let uChoose = this.intLocalUChoose();
				
				if(Object.keys(uChoose).length==0){
					this.find_house_total = 0;
					return
				}
				
				this.$http.ajax({
					url: '/index/estates/getEstatesList',
					data: {
						...uChoose,
						page: 1,
					},
				}).then( res =>{
					res = res.data
					this.find_house_total = res.total?res.total:0;
				})
			}
		},
		
	}
}());