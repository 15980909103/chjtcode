<template>
	<view class="content">
		<!-- <web-view :webview-styles="webviewStyles" :src='h5Host+"/houses/info.html?"+t_version+"&id="+id'></web-view> -->
		<u-tabs :list="list" :is-scroll="false" style="width: 100%;" :current="active" name="title" @change="change" active-color="rgba(254, 130, 30, 1)"></u-tabs>
		<van-tabs class="tabs" v-model="active" scrollspy sticky color="rgba(254, 130, 30, 1)" @change="tabChange" style="width: 100%;">
			<van-tab v-for="(item,index) in list" :title="item.name" :key="index" v-show="active == index">
				<div class="wrap" v-if="index < 4">
					
					<h4 class="title" :class="[ index == 0 ? 'title-first' : '' ]">
						{{ item.name }}
					</h4>
					
					<div class="box">
						<template  v-for="(list,key) in item.list">
							<template v-if="list.type == 0">
								<div class="box-info" :key="key" v-if="list.list">
									<div>{{ list.name }}</div>
									<span>：</span>
									<p class="info-text">
										{{ list.list }}
									</p>
								</div>
							</template>
							<template v-else>
								<div class="box-time" v-if="list.list.length > 0" :key="key">
									<div class="box-time-title">{{ list.name }}</div>
									<div class="open">
										<div class="open-title">
											<div v-for="(name,keys) in list.nameList" :key="keys">
												{{ name }}
											</div>
										</div>
										<div class="open-text" v-for="(myItem,keys) in list.list" :key="keys">
											<template v-if=" list.type == 1 ">
												<div>{{ myItem.time }}</div>
												<div class="open-item"><p v-for="(tower,num) in myItem.tower" :key="num">{{ tower }}</p></div>
											</template>
											<template v-else>
												<div>{{ myItem.num }}</div>
												<div>{{ myItem.time }}</div>
												<div>{{ myItem.house }}</div>
											</template>
										</div>
									</div>
								</div>
							</template>
						</template>
					</div>
				</div>
			</van-tab>
		</van-tabs>
		<div class="talk">
			<div class="talk-top">
				<span class="iconfont iconicon_anquan"></span>免责声明
			</div>
			<div class="talk-text">
				楼盘信息由开发商提供，最终以政府部门登记备案为准，请谨慎核查。
			</div>
		</div>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				webviewStyles: 'false',//禁用进度条
				id: 0,
				active: 0,
				list: [
					{
						name: '基本信息',
						list: [
							{
								type: 0,
								name: '楼盘名称',
								list: ''
							},
							{
								// 暂无
								type: 0,
								name: '区域',
								list: ''
							},
							{
								type: 0,
								name: '楼盘地址',
								list: '湖里区观日西路127号'
							},
							{
								type: 0,
								name: '销售状态',
								list: ''
							},
							{
								type: 0,
								name: '开发商',
								list: ''
							},
							{
								type: 0,
								name: '交房时间',
								list: ''
							},
							{
								type: 0,
								name: '售楼电话',
								list: ''
							},
							{
								type: 1,
								name: '开盘时间',
								nameList: ['开盘时间','开盘楼栋'],
								list: []
							}
						]
					},
					{
						name: '预售许可证',
						list: [
							{
								type: 2,
								name: '',
								nameList: ['预售许可证','发证时间','登记楼栋'],
								list: []
							}
						]
					},
					{
						name: '物业参数',
						list: [
							{
								type: 0,
								name: '物业公司',
								list: ''
							},
							{
								type: 0,
								name: '物业类型',
								list: ''
							},
							{
								type: 0,
								name: '物业费',
								list: ''
							},
							{
								type: 0,
								name: '容积率',
								list: ''
							},
							{
								type: 0,
								name: '绿化率',
								list: ''
							},
							{
								type: 0,
								name: '车位数',
								list: ''
							},
							{
								type: 0,
								name: '车位比',
								list: ''
							}
						]
					},
					{
						name: '建筑信息',
						list: [
							{
								type: 0,
								name: '大小户型',
								list: ''
							},
							{
								type: 0,
								name: '规划户数',
								list: ''
							},
							{
								type: 0,
								name: '项目类型',
								list: ''
							},
							{
								type: 0,
								name: '建筑类型',
								list: ''
							},
							{
								type: 0,
								name: '总占地面积',
								list: ''
							},
							{
								type: 0,
								name: '总建筑面积',
								list: ''
							},
							{
								type: 0,
								name: '楼层状况',
								list: ''
							},
							{
								type: 0,
								name: '层 高',
								list: ''
							},
							{
								type: 0,
								name: '装修情况',
								list: ''
							},
							{
								type: 0,
								name: '项目进度',
								list: ''
							},
							{
								type: 0,
								name: '公 摊',
								list: ''
							}
						]
					},
				]
			}
		},
		onLoad(options) {
			this.id = options.id;
			this.getList();
		},
		methods: {
			change(index){
				this.active = index
			},
			tabChange(e) {
				if( e == 4 ){
					console.log('周边位置')
				}
			},
			getList() {
				const data = {
					estate_id: this.id,
				};
				
				this.$http.post(
					'/estates/getMoreInfo',
					data,
				).then( res=>{
					let data = res.data;
					console.log('res.data',res.data)
					//const sale = ['待售', '在售', '售罄', '尾盘'];
					const saleState = [];
					const proveState = [];
					let houseType = [];
					const tag = this.$api.localStore.localGet('u-tag');
					let mytime = '';
					
					// 基本信息
					this.list[0].list[0].list = data.name;
					this.list[0].list[1].list = data.areaInfo;
					this.list[0].list[2].list = data.address;
					this.list[0].list[3].list = tag.estatesnew_sale_status[data.sale_status];
					this.list[0].list[4].list = data.developers;
					this.list[0].list[5].list = data.delivery_time;
					
					this.list[0].list[6].list = data.sales_telephone;
					
					data.start_opens&&data.start_opens.map( item=>{
						const obj = {};
						obj.time =  this.$api.timeFormat(item.opening_time);
						obj.tower = [...item.building];
						
						saleState.push(obj);
					})
					
					this.list[0].list[7].list = saleState;
					
					// 预售许可证
					data.sales_license&&data.sales_license.map( item=>{
						const obj = {};
						
						
						obj.num =  item.license;
						obj.time = this.$api.timeFormat(item.opening_time);
						obj.house = item.building;
						
						proveState.push(obj);
					})
					this.list[1].list[0].list = proveState;
					
					// 建筑信息
					this.list[3].list[0].list = data.sizelayout + 'm²';
					this.list[3].list[1].list = data.planning_number;
					
					
					data.house_purpose&&data.house_purpose.map( item=>{
						houseType.push(tag['house_purpose'][item]);
					});
						
					houseType = houseType.join('、');
					
					this.list[3].list[2].list = houseType;
					
					this.list[3].list[3].list = data.building_type;
					
					this.list[3].list[4].list = data.total_area + 'm²';
					this.list[3].list[5].list = data.total_construction_area + 'm²';
					this.list[3].list[6].list = data.floor_condition;
					this.list[3].list[7].list = data.floor_height;
					this.list[3].list[8].list = data.decoration;
					this.list[3].list[9].list = data.progress_project;
					this.list[3].list[10].list = data.public_bear + 'm²';
					
					// 物业参数
					this.list[2].list[0].list = data.property_company;
					this.list[2].list[1].list = data.property_type;
					this.list[2].list[2].list = data.property_charges;
					this.list[2].list[3].list = data.volume_rate;
					this.list[2].list[4].list = data.greening_rate;
					this.list[2].list[5].list = data.parking_space_number;
					this.list[2].list[6].list = data.parking_space_proportion;
					
					console.log(this.list,8888888)
					// 配套信息
					
				})
			},
		}
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
	
	
	
	.wrap{
		box-sizing: border-box;
		padding: 0 32rpx;
	}
	
	.title{
		font-size: 36rpx;
		box-sizing: border-box;
		padding: 0 0 26rpx;
		padding-top: 30rpx;
	}
	
	.title-first{
		padding-top: 42rpx;
	}
	
	.box{
		box-sizing: border-box;
		border-bottom: 1rpx solid rgba(224, 224, 224, 1);
	}
	
	.box:last-child{
		border-bottom: none;
	}
	
	.box-info{
		display: flex;
		margin-bottom: 30rpx;
		font-size: 30rpx;
		color: rgba(117, 117, 117, 1);
	}
	
	.box-time{
		display: flex;
		font-size: 30rpx;
		color: rgba(117, 117, 117, 1);
		flex-direction: column;
	}
	
	.box-time-title{
		margin-bottom: 30rpx;
	}
	
	.box-info div{
		width: 121rpx;
		height: 42rpx;
		text-align: justify;
		overflow: hidden;
	}
	
	.box-info div:after{
	  content: '';
	  display: inline-block; 
	  padding-left: 100%;
	}
	
	.info-text{
		width: 72%;
		margin-left: 38rpx;
		color: rgba(11, 15, 18, 1);
		display: flex;
		align-items: flex-end;
	}
	
	.open{
		width: 100%;
		font-size: 26rpx;
		margin-bottom: 30rpx;
	}
	
	.open-title{
		height: 74rpx;
		font-size: 30rpx;
		color: rgba(33, 33, 33, 1);
		background-color: rgba(245, 248, 254, 1);
		display: flex;
		border: 1rpx solid rgba(235, 235, 235, 1);
	}
	
	.open-text{
		display: flex;
		box-sizing: border-box;
		font-size: 26rpx;
		border-width: 0 1rpx 1rpx;
		color: rgba(117, 117, 117, 1);
		border-color: rgba(235, 235, 235, 1);
		border-style: solid;
	}
	
	.open-text div{
		box-sizing: border-box;
		padding: 20rpx;
	}
	
	.open-title div,
	.open-text div{
		width: 50%;
		display: flex;
		justify-content: center;
		align-items: center;
		border-right: 0.01rpx solid rgba(235, 235, 235, 1);
	}
	
	.open-title div:last-child,
	.open-text div:last-child{
		border-right: none;
	}
	
	.open-item{
		display: flex;
		flex-direction: column;
	}
	
	.open-item p{
		margin-bottom: 0.08rpx;
	}
	
	.talk{
		font-size: 0.24rpx;
		color: rgba(189, 189, 189, 1);
		box-sizing: border-box;
		padding: 0.5rpx 0.32rpx 4rpx;
		background-color: rgba(246, 246, 246, 1);
	}
	
	.talk-top{
		font-weight: 500;
		color: rgba(117, 117, 117, 1);
		display: flex;
		align-items: center;
		margin-bottom: 0.1rpx;
	}
	
	.talk-top .iconfont{
		color: rgba(70, 70, 70, 1);
		font-size: 0.36rpx;
		margin-right: 0.06rpx;
	}
</style>
