<template>
	<view class="content">
		<div class="choose-location" v-show="show">
			<div class="location-search">
				<u-search 
				 v-model="searchValue"
				 show-action
				 bg-color="#fff"
				 placeholder-color="#ADADAD"
				 placeholder="搜索城市"
				 maxlength="16"
				 shape="square"
				 action-text="取消"
				 @change="onSearch"
				 @custom="onCancel"
				>	
				</u-search>
			</div>
			<div class="location-info">
				<div class="location-info-left">
					<span>{{city_name}}</span>GPS定位
				</div>
				<div class="location-info-right" @click="getUserSite">
					<span class="iconfont iconmubiao"></span>重新定位
				</div>
			</div>
			<div class="location-tip" v-show="!isSearch">
				<span>热门城市</span>
				<div class="city">
					<span class="city-tip" v-for="(item,index) in cityList.hot" :key="index" @click="choose(item)">
						{{item.cname}}
					</span>
				</div>
			</div>
			<div class="location-tip" v-show="!isSearch">
				<span>所有城市</span>
				<div class="city">
						<view 
							class="city-tip" 
							v-for="(item,index) in cityList.all"
							:key="index"
							@click="choose(item)"
						>
							{{item.cname}}
						</view>
				</div>
			</div>
			<div class="location-tip" v-show="isSearch">
				<span>搜索城市</span>
				<div class="city">
					<block v-for="(item,index) in cityList.all">
						<view 
							class="city-tip" 
							:key="index"
							@click="choose(item)"
							v-show="srarchShow.indexOf(index) != -1"
						>
							{{item.cname}}
						</view>
					</block>
					<div v-if='srarchShow.length==0' class="nosearch-tip">暂无数据</div>
				</div>
			</div>
		</div>
		<div id="container-user-site"></div>
	</view>
</template>


<script >
	let app = getApp();
	export default {
		data(){
			return {
				city_name: '',
				show: true,
				searchValue: '',
				cityList: {
					hot: [],
					all: []
				},
				isSearch: false,
				srarchShow: [],
				map: 0
			}
		},
		onLoad() {
			this.getSite();
		},
		methods:{
			init() {
				this.searchValue = '';
				this.srarchShow = [];
				this.isSearch = false;
			},
			onSearch(val) {
				if( !this.$api.isEmpty(val) ) {
					this.cityList.all.map((item,index)=>{
						if( item.cname&&item.cname.indexOf(val) != -1 ){
							console.log(item.cname,789)
							this.srarchShow.push(index);
						}
					});
					
					this.isSearch = true;
				} else {
					this.init();
				}
			},
			onCancel() {
				this.init();
				this.goPage(-1);
			},
			choose(val, type) {
				//console.log(val)
				app.getCurrentCity({
					city_name: val.cname,
					city_no: val.id,
				})
				
				if( !type ){
					this.onCancel();
				}
			},
			getSite() {
				app.getAllCitys().then((res)=>{
					//console.log(res)
					let hotList = [];
					let allCity =[];
					let obj;
					
					res&&res.map((item)=>{
						item.cname = item.cname.replace('市','');
						item.is_hot&&hotList.push(item);
						allCity.push(item);
					});
					
					obj = {
						hot: hotList,
						all: allCity,
					};
					
					this.cityList = obj;
				});
			},
			getUserSite() {
				app.getUserLocationCity().then( data=>{
					console.log(data)
					if( this.city_name&&this.city_name.indexOf(data.city_name) != -1 ){
						this.choose({
							cname: data.city_name,
							id: data.city_no
						});
					} else {
						
						uni.showModal({
						    title: '提示',
						    content: '确定将定位切换到'+data.city_name+'吗?',
						    success: (res)=>{
								if (res.confirm) {
								    this.choose({
								    	cname: data.city_name,
								    	id: data.city_no
								    });
								    
								    this.city_name = data.city_name;
								} else if (res.cancel) { }
							}
						});
						
					}
				} )
			}
		}
	}
	
	
	
</script>

<style>
	 @import './location.css';
</style>