<template>
	<view>
		<scroll-view scroll-y='true' @scrolltolower="onLoads" class="scrollbox">
			<div class="title-box">
				<h4 class="title-text">为您智能匹配{{total}}个房源</h4>
				<div class="title-tip">
					<div class="iconfont icon000" @click="goMap"><span>地图</span></div>
					<div class="iconfont icon009" @click='goEdit'><span>修改</span></div>
				</div>
			</div>
			<common-new-house :list="list.get" @del="(e)=>{ $set(list, 'get', e) }"></common-new-house>
		</scroll-view>
		
		
		<h4 class="title-like">猜你喜欢</h4>
		<common-new-house :list="list.recommend" @del="(e)=>{ $set(list, 'get', e) }"></common-new-house>
		
	</view>
</template>

<script>
	import commonNewHouse from '@/components/common/template_newHouse.vue'
	export default{
		data(){
			return {
				pageShow:false,
				total:0,
				page:0,
				last_page:1,
				loading: false,
				finished: false,
				list: {
					get: [],
					recommend: []
				},
				uChoose:{},
				city_no: 0,
			}
		},
		onLoad() {
			this.city_no = getApp().getCurrentCity().city_no;
			this.intLocalUChoose()
			this.getEstatesLike();
			this.onLoads();
		},
		components: {
			commonNewHouse
		},
		methods:{
			intLocalUChoose(){
				let uChoose =this.$api.localStore.localGet('user_find_house');
				//console.log(uChoose)
				let new_uChoose = {};
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
				new_uChoose.is_log = 1;
				new_uChoose.purpose = uChoose.aim;
				new_uChoose.has_num = uChoose.manyHouse;

				this.uChoose = new_uChoose;
			},
			onLoads() {
				let page = Number(this.page) + 1
				if(page>this.last_page){
					this.loading = false;
					this.finished = true;
					return
				}
				if(page>1){
					delete this.uChoose.is_log
					delete this.uChoose.purpose
					delete this.uChoose.has_num
				}
				this.$http.get('/estates/getEstatesList',
					{
						...this.uChoose,
						page,
						page_size: 6
					},
				).then( res =>{
					res = res.data
					this.pageShow = true;
					this.loading = false;
					this.page = page;
					this.last_page = res.last_page?res.last_page:1;
					this.total = res.total?res.total:0;
					
					let list = this.$api.createHouseList(res);
					console.log(list)
					this.list.get = this.list.get.concat(list);

					if(page>=this.last_page){
						this.finished = true;
					}
				})
			},
			//猜你喜欢楼盘
			getEstatesLike() {
				const data = {
					city_no: this.city_no,
				};
					
				this.$http.get(
					'/estates/getGuessList',
					data,
			).then( res=>{
					let data = res.data;
					//console.log('猜你喜欢',data);
					
					this.list.recommend = this.$api.createHouseList({list:data});
				})
			},

			goMap() {
				let id = '';
				this.list.get&&this.list.get.map((item)=>{
					id += item.id + '-';
				})
				
				this.goPage('map/index',{id: id});
			},
			goEdit(){
				this.goPage('index/find_house');
			},
		},
	}
</script>

<style>
	@import "./find_result.css";
</style>
