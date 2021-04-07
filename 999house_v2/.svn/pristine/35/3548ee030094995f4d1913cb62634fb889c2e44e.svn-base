<template>
	<view class="content">
		<!-- <web-view :webview-styles="webviewStyles" :src='h5Host+"/houses/house_type.html?"+t_version+"&id="+id'></web-view> -->
		<template v-if="list.all.list.length > 0">
			<u-tabs :list="list" :is-scroll="false" :current="active" name="title" @change="change" active-color="rgba(254, 130, 30, 1)"></u-tabs>
			<van-tabs class="tabs" v-model="active" sticky>
				<van-tab v-for="(item,index) in list" :name='index' :title="item.rooms_name + '('+ item.list.length +')'" :key="index">
					<div class="wrap">
						<houses-template :list="item.list"/>
					</div>
				</van-tab>
			</van-tabs>
		</template>
		<template v-else>
			<div class="list_null">
				<img src="../../static/null.png">
				<p>暂无数据</p>
			</div>
		</template>
	</view>
</template>

<script>
	import housesTemplate from '@/components/houses/template_house'
	export default {
		data() {
			return {
				webviewStyles: 'false',//禁用进度条
				id: 0,
				active: 0,
				pageShow: false,
				list:{
					all:{
						rooms_name:'全部',
						list:[],
					}
				},
			}
		},
		components: {
			housesTemplate
		},
		onLoad(options) {
			this.id = options.id;
			this.getList();
		},
		methods:{
			change(e){
				this.active = e
			},
			// 历史价图表数据
			getList() {
				const data = {
					estate_id: this.id,
					is_group: 1
				};
				
				this.$http.post(
					'/estates/getEstatesnewHouseList',
					data,
				).then( res=>{
					let data = res.data;
					let allTag = this.$api.localStore.localGet('u-tag');
					const rooms = allTag.rooms;
					const tip = allTag.house_purpose;
					const way = allTag.orientation;
		
					let arr = this.formatData(data.all,{
						rooms: rooms,
						tip: tip,
						way: way
					});
					
					this.list.all = {
						rooms_name:'全部',
						list: arr
					}
				
					if( data.group&&Object.keys(data.group).length > 0 ){
						for( let i in data.group ){
							let newObj = {
								rooms_name: rooms[i],
								list: []
							};
		
							newObj.list = this.formatData(data.group[i],{
								rooms: rooms,
								tip: tip,
								way: way
							});
							this.list[newObj.rooms_name] = newObj;
						}
					}
		
					this.pageShow = true
				})
			},
			formatData(data, e){
				let arr = [];
				let tip = e.tip;
				let way = e.way;
				let rooms = e.rooms;
		
				data&&data.map( item=>{
					const obj = {};
					
					obj.img = item.img;
					obj.title = item.name;
					obj.tip = tip&&tip[item.house_purpose]?[tip[item.house_purpose]]:[];
					obj.area = item.built_area;
					obj.way = way&&way[item.orientation]?way[item.orientation]:'';
					obj.price = item.price;
					obj.price_ave = item.price_ave;
					
					arr.push(obj);
				});
				return arr;
			}
		},
	}
</script>

<style>
	.content {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}

	.logo {
		height: 200rpx;
		width: 200rpx;
		margin-top: 200rpx;
		margin-left: auto;
		margin-right: auto;
		margin-bottom: 50rpx;
	}

	.text-area {
		display: flex;
		justify-content: center;
	}

	.title {
		font-size: 36rpx;
		color: #8f8f94;
	}
</style>
