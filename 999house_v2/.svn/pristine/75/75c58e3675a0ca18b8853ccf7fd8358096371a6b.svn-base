/* 前置引入topBar组件 */

var mapRound = (function() {
	const html = `
		<div class="round-box">
			<top-bar
				:title="roundInfo.name" 
				:custom-back="roundBack" 
				@back="roundHide"
			>
			</top-bar>
			<div id="round-map" @click.stop></div>
			
			<div class="round" ref="round">
				<div class="round-btn" @click="hidePoiMenu"><i class="iconfont iconbianzu21"></i></div>
				<div class="round-tab">
					<div 
						class="round-tab-item"
						:class="[ activePoi == index ? 'text-active' : '' ]"
						v-for="(item,index) in roundName" 
						:key="index"
						@click="()=>{
							activePoi = index
						}"
					>
						<i 
							class="iconfont" 
							:class="[
								item == '公交' ? 'iconjiaotong' : '',
								item == '教育' ? 'iconjiaoyubeifen' : '',
								item == '医院' ? 'iconyiyuan' : '',
								item == '购物' ? 'icongouwu' : '',
								item == '美食' ? 'iconweibiaoti--beifen' : ''
							]"
						>
						</i>
						<span>{{ item }}</span>
					</div>
				</div>
				
				<div class="round-content" ref="poi">
					<template v-if="roundPois[activePoi]&&roundPois[activePoi].data&&roundPois[activePoi].data.length > 0">
						<div class="round-content-item" v-for="(item,index) in roundPois[activePoi].data" :key="index" @click="poiClick(item,1)">
							<div class="round-content-item-left van-ellipsis">
								<div class="round-content-title" :class="[ poiId == item.id ? 'text-active' : '' ]">
									{{ item.title }}
								</div>
								<div class="round-content-subhead van-ellipsis" v-if="activePoi == 0">
									{{ item.address }}
								</div>
							</div>
							<div class="round-content-item-right">
								{{ item._distance }}m
							</div>
						</div>
					</template>
					<template v-else>
						<div class="not_list" v-if="roundPois.length > 0">
							<van-empty image="search" description="暂无相关数据喔~" />
						</div>
					</template>
				</div>
			</div>
		</div>
	`;
	
	return {
		data: function(){
			return {
				// 地图
				roundMap: null,
				zoom: 13.5,			//	初始地图缩放级别，支持3～20。
				
				// 周边配套
				roundInfo: {},
				roundCover: '',		//	周边配套定位点图层
				roundText: '',		//	周边配套定位文本
				activePoi: 0,
				roundName: ['公交','教育','医院','购物','美食'],
				roundPois: [],
				poiId: 0,			//	点击某poi相应文字高亮
			}
		},
		template: html,
		props: {
			id: {
				type: [Number,String],
				default() {
					return -1
				}
			},
			roundBack: {
				type: [Boolean],
				default() {
					return false
				}
			},
		},
		watch: {
			// 切换周边配套POI
			activePoi( newV, oldV ) {
				const id = [];
				this.roundCover.geometries.map( item=>{
					if( item.id != 'center' ){
						id.push(item.id);
					}
				})
				
				this.poiId = 0;
				this.$refs.poi.scrollTo(0,0);
				this.roundCover.remove(id);
				this.setRoundPoi();
			},
			id( newV ) {
				this.init();
			}
		},
		mounted() {
			this.init();
		},
		methods: {
			init() {
				if( this.id == -1 ) return;
				// id = 148;
				// [148,689,918]
				this.initRound( this.id );
			},
			// 获取城市楼盘数据
			getMapList(data,call) {
				this.$http.ajax({
					method: 'GET',
					data: data,
					url: '/index/estates/getMapList',
				}).then(res => {
					call(res);
				})
			},
			mapPoi( data ) {
				return new Promise((resove,rej)=>{
					this.$http.ajax({
						method: 'GET',
						data: data,
						url: '/index/estates/searchMap',
					}).then(res => {
						resove(res);
					})
				})
			},
			initRound( id ) {
				this.getMapList({
					estate_id: id
				}, res=>{
					if( res.data && res.data.length > 0 ){
						
						const data = res.data;
						
						data.map( (item,index)=>{
							item.lat = Number(item.lnglat.lat);
							item.lng = Number(item.lnglat.lng);
						})
				
						this.roundPoi(data[0]);
					}
				})
			},
			// 选中楼盘周边配套
			roundPoi( item ) {
				this.roundInfo = item;
				
				this.$nextTick(()=>{
					this.initRoundMap(item);
				})
			},
			// 周边配套隐藏
			roundHide() {
				this.$emit('close');
				setTimeout(()=>{
					this.activePoi = 0;
					this.$refs.poi.scrollTo(0,0);
					this.$refs.round.style.transform = this.$refs.round.childNodes[0].childNodes[0].style.transform = '';
				},300)
			},
			initRoundMap(item) {
				const center = new TMap.LatLng(item.lat, item.lng);
				
				// this.exchangeCenter(this.roundMap,center,{x:0,y:100})
				if( !this.roundMap ){
					this.roundMap = new TMap.Map(document.getElementById('round-map'), {
						center: center,//设置地图中心点坐标
						viewMode: '2D',
						zoom: 17,   //设置地图缩放级别
						showControl: false
					});
				} else {
					this.roundMap.easeTo({
						zoom: 17,
						center: center
					});
				}
				
				this.roundMarker({
					center: center,
					lnglat: item
				});
			},  
			roundMarker( data, type='center' ) {
				if( !this.roundCover ){
					this.roundCover = new TMap.MultiMarker({
					    map: this.roundMap,
					    //样式定义
					    styles: {
					        centerStyle: new TMap.MarkerStyle({ 
					            width: 25,
					            height: 35,
					            src: '../../static/map/site.png',
					            //焦点在图片中的像素位置，一般大头针类似形式的图片以针尖位置做为焦点，圆形点以圆心位置为焦点
					            anchor: { x: 16, y: 32 }  
					        }),
							busStyle: new TMap.MarkerStyle({
							    width: 26,
							    height: 30,
							    src: '../../static/map/transit-icon.png',
							    anchor: { x: 16, y: 32 }  
							}),
							busActiveStyle: new TMap.MarkerStyle({
							    width: 26,
							    height: 30,
							    src: '../../static/map/transit-check-icon.png',
							    anchor: { x: 16, y: 32 }  
							}),
							schoolStyle: new TMap.MarkerStyle({
							    width: 26,
							    height: 30,
							    src: '../../static/map/school-icon.png',
							    anchor: { x: 16, y: 32 }  
							}),
							schoolActiveStyle: new TMap.MarkerStyle({
							    width: 26,
							    height: 30,
							    src: '../../static/map/school-check-icon.png',
							    anchor: { x: 16, y: 32 }  
							}),
							hospitalStyle: new TMap.MarkerStyle({
							    width: 26,
							    height: 30,
							    src: '../../static/map/hospital-icon.png',
							    anchor: { x: 16, y: 32 }  
							}),
							hospitalActiveStyle: new TMap.MarkerStyle({
							    width: 26,
							    height: 30,
							    src: '../../static/map/hospital-check-icon.png',
							    anchor: { x: 16, y: 32 }  
							}),
							buyStyle: new TMap.MarkerStyle({
							    width: 26,
							    height: 30,
							    src: '../../static/map/market-icon.png',
							    anchor: { x: 16, y: 32 }  
							}),
							buyActiveStyle: new TMap.MarkerStyle({
							    width: 26,
							    height: 30,
							    src: '../../static/map/market-check-icon.png',
							    anchor: { x: 16, y: 32 }  
							}),
							foodStyle: new TMap.MarkerStyle({
							    width: 26,
							    height: 30,
							    src: '../../static/map/food1-icon.png',
							    anchor: { x: 16, y: 32 }  
							}),
							foodActiveStyle: new TMap.MarkerStyle({
							    width: 26,
							    height: 30,
							    src: '../../static/map/food1-check-icon.png',
							    anchor: { x: 16, y: 32 }  
							}),
					    },
					    //点标记数据数组
					    geometries: [
							{
								id: 'center',
								styleId: 'centerStyle',
								position: data.center
							}
					    ]
					});
						
					
				} else {
					this.roundCover.updateGeometries([
						{
							id: 'center',   			//点标记唯一标识，后续如果有删除、修改位置等操作，都需要此id
							styleId: 'centerStyle',
							position: data.center,  			//点标记坐标位置
						}
					])
				}
				
				const obj = {
					map: this.roundMap,
					position: data.center,
					title: data.lnglat.name,
					site: data.lnglat.address
				}
				
				if( this.roundText ){
					this.roundText.destroy();
				}
				
				this.roundText = new Site(obj);
			
				this.getRoundPoi(data.lnglat);
			},
			async getRoundPoi(lnglat) {
				let pois =[];
				
				this.roundName.map( item=>{
					let key;
					let filter;
					
					switch(item){
						case '教育':
							key = '学校';
							filter = 'category=大学,中学,小学,幼儿园';
							break;
						case '医院':
							key = '医院';
							filter = 'category=综合医院,专科医院,医疗保健附属';
							break;
						case '购物':
							key = '购物中心';
							break;
						default: 
							key = item;
							break;
					}
					
					pois.push(this.mapPoi({
						keyword: key,
						lat: lnglat.lat,
						lng: lnglat.lng,
						filter: filter
					}));
				})
				
				this.roundPois = await Promise.all(pois);
				console.table(this.roundPois[2].data)
				this.setRoundPoi();
			},
			setRoundPoi() {
				const arr = [];
				const data = this.roundPois[this.activePoi].data;
				let style,
					distance,
					mapScale;
				
				switch(this.activePoi){
					case 0:
						style = 'busStyle';
						break;
					case 1:
						style = 'schoolStyle';
						break;
					case 2:
						style = 'hospitalStyle';
						break;
					case 3:
						style = 'buyStyle';
						break;
					case 4:
						style = 'foodStyle';
						break;
				}
				this.roundCover.off('click', this.poiClick);
				
				data.map( item=>{
					let obj = {
						id: item.id,
						styleId: style,
						position: new TMap.LatLng(item.location.lat, item.location.lng),
					}
					
					arr.push(obj);
				})
				
				this.roundCover.add(arr);
				
				this.roundCover.on('click', this.poiClick);
				
				if( data.length == 0 ){
					mapScale = 16.5;
				} else {
					
					if( data.length > 3 ){
						 distance = data[2]._distance;
					} else {
						distance = data[data.length]._distance;
					}
					
					if( distance <= 200 ){
						mapScale = 17.5;
					} else if( distance <= 500 ){
						mapScale = 16;
					} else if( distance <= 1000 ){
						mapScale = 14;
					} else if( distance <= 2000 ){
						mapScale = 13.5;
					} else if( distance <= 3000 ){
						mapScale = 12;
					} else {
						mapScale = 11.5;
					}
				}
				
				let center = new TMap.LatLng(this.roundCover.geometries[0].position.lat, this.roundCover.geometries[0].position.lng)
				
				this.roundMap.setCenter(center);
				
				this.roundMap.zoomTo(mapScale,250);
				setTimeout(()=>{
					center = this.exchangePosition(this.roundMap,center,{x:0,y:-100});
					this.roundMap.panTo(center,250);
				},250)
			},
			exchangePosition( el, center, anchor ){
				const site = el.projectToContainer(center);
				
				site.x = site.x - anchor.x;
				site.y = site.y - anchor.y;
				
				return el.unprojectFromContainer(site);
			},
			poiClick(e, type) {
				let id,
					style,
					position;
						
				if( type == 1 ){
					id = e.id;
					position = new TMap.LatLng(e.location.lat, e.location.lng);
				} else {
					id = e.geometry.id;
					position = e.geometry.position;
				}
				
				if( id == 'center' ) return;
				
				this.roundCover.geometries.map( item=>{
					
					if( item.id == id ){
						style = item.styleId;
					}else if( item.styleId.indexOf('Active') != -1 ){
						this.roundCover.updateGeometries([{
							id: item.id,
							styleId: item.styleId.replace('Active',''),
							position: new TMap.LatLng(item.position.lat, item.position.lng)
						}])
					}
				})
				
				if( style.indexOf('Active') != -1 ){
					style = style.replace('Active','');
					this.poiId = 0;
				} else {
					style = style.replace('Sty','ActiveSty');
					this.poiId = id;
				}
							
				this.roundCover.updateGeometries([{
					id: id,
					styleId: style,
					position: position
				}])
				
				this.roundMap.panTo(this.exchangePosition(this.roundMap,position,{x:0,y:-100}));
			},
			// 隐藏poi菜单
			hidePoiMenu(e) {
				if( !this.$refs.round.style.transform ){
					this.$refs.round.style.transform = 'translate(0,100%)';
					this.$refs.round.childNodes[0].childNodes[0].style.transform = 'rotate(-180deg)';
				} else {
					this.$refs.round.style.transform = this.$refs.round.childNodes[0].childNodes[0].style.transform = '';
				}
			},
		},
	}
}());