/**
 * 前置引入area组件
 */
var PoiMap = (function() {
	const html =
		`	
		<div class="main">
			<div class="optionBox">
				<div :class="poiChecked=='transit'?'checked option':'option'" @click='Checked("transit")'>
					<i class="iconfont iconjiaotong"></i>
					公交
				</div>
				<div :class="poiChecked=='school'?'checked option':'option'" @click='Checked("school")'>
					<i class="iconfont iconjiaoyubeifen"></i>
					教育
				</div>
				<div :class="poiChecked=='hospital'?'checked option':'option'" @click='Checked("hospital")'>
					<i class="iconfont iconyiyuan"></i>
					医院
				</div>
				<div :class="poiChecked=='market'?'checked option':'option'" @click='Checked("market")'>
					<i class="iconfont icongouwu"></i>
					购物
				</div>
			</div>
		<div id="container">
	<div class="detail transitBox">
			<div v-if="poiChecked == 'transit'"
				class="contentBox">
				
				<template v-if="piolist.transit && piolist.transit.length > 0">
					<div class="detail_item" v-for='(item, index) in piolist.transit' :key='index'>
						<div>{{item.name}}</div>
						<div>{{item.distance}}m</div>
					</div>
				</template>
				<template v-else>
					<div class="detail_no">
						暂无数据
					</div>
				</template>
			</div>
			
			<div v-if="poiChecked == 'school'" class="contentBox" >
				<template v-if="piolist.school && piolist.school.length > 0">
					<div class="detail_item" v-for='(item, index) in piolist.school' :key='index'>
						<div>{{item.name}}</div>
						<div>{{item.distance}}m</div>
					</div>
				</template>
				<template v-else>
					<div class="detail_no">
						暂无数据
					</div>
				</template>
			</div>
			
			<div v-if="poiChecked == 'hospital'" class="contentBox" >
				<template v-if="piolist.hospital && piolist.hospital.length > 0">
					<div class="detail_item" v-for='(item, index) in piolist.hospital' :key='index'>
						<div>{{item.name}}</div>
						<div>{{item.distance}}m</div>
					</div>
				</template>
				<template v-else>
					<div class="detail_no">
						暂无数据
					</div>
				</template>
			</div>
			<div v-if="poiChecked == 'market'" class="contentBox">
				<template v-if="piolist.market && piolist.market.length > 0">
					<div class="detail_item" v-for='(item, index) in piolist.market' :key='index'>
						<div>{{item.name}}</div>
						<div>{{item.distance}}m</div>
					</div>
				</template>
				<template v-else>
					<div class="detail_no">
						暂无数据
					</div>
				</template>
			</div>
		</div>
	</div>
	<div class='more' @click='goPath()'>查看更多</div>
</div>`

	return {
		data: function() {
			return {
				map: null, //地图对象
				cluster: null,
				markerList: [],
				points: [],
				markVal: null, //当前点击点的信息
				flag: false, //楼盘详情弹窗是否显示
				piolist: {}, //当前楼盘的附近poi信息 hospital三甲医院信息 market商场信息 school学校信息 transit附近公交站台信息
				centerValue: null, //当前地图中心点的行政信息
				mapCenter: [118.072793, 24.457917], //当前地图中心点
				mapZoomType: 'area', //地图展示级别信息 city市级 area区级 business商圈 floor楼盘
				zoom: 13, //地图缩放级别
				markers: [], //地图标点对象
				marketVal: null, //当前商圈信息
				poiChecked: 'school', //当前点击的pio信息分类
				poiFlag: false, //pio信息列表状态
				pioMarker: [], //pio信息点的实例对象
				cityValue: {}, //当前城市信息
				cityList: null,
				arr: null,
				floorList: null,
				floorDetail: null,
				pathDate: null
			}
		},
		template: html,
		props: {
			// list: {
			// 	type: [Array],
			// 	default() {
			// 		return []
			// 	}
			// }
			// zoom: {
			// 	type: Number,
			// 	default: '10'
			// }
			id: {
				type: [Number, String],
				default () {
					return 0
				}
			}
		},
		created() {

		},
		mounted() {
			this.init();

		},
		methods: {
			// 返回筛选id列表
			chooseResult(e) {
				console.log(e)
			},
			//初始化地图
			init() {
				//创建地图
				let that = this
				let map = new AMap.Map("container", {
					zoom: that.zoom,
					center: that.mapCenter,
					resizeEnable: true,
					dragEnable: false, // 地图是否可通过鼠标拖拽平移，默认为true
					keyboardEnable: false, //地图是否可通过键盘控制，默认为true
					doubleClickZoom: false, // 地图是否可通过双击鼠标放大地图，默认为true
					zoomEnable: false, //地图是否可缩放，默认值为true
				});
				that.getMarker()
				map.plugin('AMap.Geolocation', function() {
					geolocation = new AMap.Geolocation({
						enableHighAccuracy: true, //是否使用高精度定位，默认:true
						timeout: 100, //超过10秒后停止定位，默认：无穷大
						maximumAge: 0, //定位结果缓存0毫秒，默认：0
						convert: true, //自动偏移坐标，偏移后的坐标为高德坐标，默认：true
						showButton: true, //显示定位按钮，默认：true
						buttonPosition: 'LB', //定位按钮停靠位置，默认：'LB'，左下角
						buttonOffset: new AMap.Pixel(10,
							20), //定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
						showMarker: true, //定位成功后在定位到的位置显示点标记，默认：true
						showCircle: true, //定位成功后用圆圈表示定位精度范围，默认：true
						panToLocation: false, //定位成功后将定位到的位置作为地图中心点，默认：true
						zoomToAccuracy: true //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
					});
					map.addControl(geolocation);
					geolocation.getCurrentPosition();
					AMap.Event.addListener(geolocation, 'complete', that.onComplete); //返回定位信息
					AMap.Event.addListener(geolocation, 'error', that.onError); //返回定位出错信息
					// this.mapMoveend()
				});
				this.map = map;
			},
			// 计算距离中心点的距离单位m
			calculateLineDistance(end) {
				var d1 = 0.01745329251994329;
				var d2 = this.mapCenter[0];
				var d3 = this.mapCenter[1];
				var d4 = end[0];
				var d5 = end[1];
				d2 *= d1;
				d3 *= d1;
				d4 *= d1;
				d5 *= d1;
				var d6 = Math.sin(d2);
				var d7 = Math.sin(d3);
				var d8 = Math.cos(d2);
				var d9 = Math.cos(d3);
				var d10 = Math.sin(d4);
				var d11 = Math.sin(d5);
				var d12 = Math.cos(d4);
				var d13 = Math.cos(d5);
				var arrayOfDouble1 = [];
				var arrayOfDouble2 = [];
				arrayOfDouble1.push(d9 * d8);
				arrayOfDouble1.push(d9 * d6);
				arrayOfDouble1.push(d7);
				arrayOfDouble2.push(d13 * d12);
				arrayOfDouble2.push(d13 * d10);
				arrayOfDouble2.push(d11);
				var d14 = Math.sqrt((arrayOfDouble1[0] - arrayOfDouble2[0]) * (arrayOfDouble1[0] -
						arrayOfDouble2[0]) +
					(arrayOfDouble1[1] - arrayOfDouble2[1]) * (arrayOfDouble1[1] - arrayOfDouble2[
						1]) +
					(arrayOfDouble1[2] - arrayOfDouble2[2]) * (arrayOfDouble1[2] - arrayOfDouble2[
						2]));
				let distance = (Math.asin(d14 / 2.0) * 12742001.579854401).toFixed(0)
				return distance;
			},
			// 查询当前楼盘附近的poi信息
			getPeriphery() {
				let data = this.floorDetail
				let location = [data.lng - 0, data
					.lat - 0
				]
				console.log(data)
				this.mapCenter = location
				let that = this
				// 查询公交信息
				AMap.plugin(["AMap.PlaceSearch"], function() {
					//构造地点查询类
					var placeSearch = new AMap.PlaceSearch({
						type: '购物中心|三级甲等医院|小学|中学|高中|高等院校|公交车站', // 兴趣点类别
						pageSize: 1000,
						pageIndex: 1,
						citylimit: true //是否强制限制在设置的城市内搜索
					});
					var cpoint = location; //中心点坐标
					placeSearch.searchNearBy('', cpoint, 3000, function(status, result) {
						let school = []
						let hospital = []
						let market = []
						let transit = []
						if (!result.poiList) {
							that.piolist.hospital = hospital
							that.piolist.school = school
							that.piolist.market = market
							that.piolist.transit = transit
							return
						}
						let data = result.poiList.pois
						for (let i = 0; i < data.length; i++) {
							if (data[i].type.indexOf('医院') != -1) {
								hospital.push(data[i])
							}
							if (data[i].type.indexOf('学校') != -1) {
								school.push(data[i])
							}
							if (data[i].type.indexOf('购物中心') != -1) {
								// console.log(data[i])
								market.push(data[i])
							}
							if (data[i].type.indexOf('公交车站') != -1) {
								transit.push(data[i])
							}
							transit.sort(function(a, b) {
								return a.distance - b.distance
							})
							that.piolist.transit = transit
							that.piolist.hospital = hospital
							that.piolist.school = school
							that.piolist.market = market
							that.Checked('transit')
						}
					});
				});
			},
			setfoorMarker(data) {
				let location = [data.lng, data.lat];
				location[0] = location[0] - 0
				location[1] = location[1] - 0
				let width = 1.22
				let style = `width:${width}rem;padding: .25rem 0px;border-radius: 50%;font-size:14px' `
				let extra = ''
				let text = data.name
				width = 2.2
				let price = data.price
				let bgColor = 'background-color: #fff;border:1px solid #fff'
				style = `width:${width}rem;padding: .10rem .3rem;border-radius:.5rem;font-size:14px' `
				if (this.markVal) {
					// 判断是否为当前点击点
					if (this.markVal.id == data.id) {
						bgColor = 'background-color: #fff;border:1px solid #fff'
						style =
							`width:${width}rem;padding: .10rem .3rem;border-radius:.5rem;background:#fff;border:1px solid #fff;box-shadow: 0px 0px 1px #fff;font-size:14px;color:#000 `
					}
				}
				extra += `<div class="custom-content-marker" style='${style}'>` +
					'<span>' + text + '</span>' + ' ' +
					`<div class="positionIcon" style='${bgColor}'>` + '</div>' +
					'</div>';
				let marker = new AMap.Marker({
					position: location,
					// 将 html 传给 content
					content: extra,
					// 以 icon 的 [center bottom] 为原点
					offset: new AMap.Pixel(-13, -30),
					clickable: true,
					extData: data
				});
				this.map.add(marker);
			},
			// 点的点击事件
			getMarker(e) {
				let id = this.id
				//console.log(id)
				let that = this
				this.$http.ajax({
					method: 'post',
					data: {
						'id': id
					},
					url: '/index/estates/getInfo',
				}).then(res => {
					//console.log(res)
					if (!res.data || !res.data.lng || !res.data.lat) {
						return;
					}
					res.data.logo = that.$http.imgDirtoUrl(res.data.logo)
					that.floorDetail = res.data
					that.markVal = res.data
					that.map.setCenter([res.data.lng - 0, res.data.lat - 0])
					that.getPeriphery()
					that.setfoorMarker(res.data)
					that.getpio()
				})
			},

			// 渲染公交、医院、商场、学校等到地图
			Checked(val) {
				this.map.remove(this.pioMarker);
				this.pioMarker = []
				this.poiChecked = val
				let data = this.piolist[val] ? this.piolist[val] : []
				let icon = ''
				switch (val) {
					case 'school':
						// /static/map/school-icon.png
						icon = '../../static/map/school-icon.png'
						break;
					case 'hospital':
						// /static/map/hospital-icon.png
						icon = '../../static/map/hospital-icon.png'
						break;
					case 'market':
						// icon = '/static/map/market-icon.png'
						icon = '../../static/map/market-icon.png'
						break;
					case 'transit':
						// icon = '/static/map/transit-icon.png'
						icon = '../../static/map/transit-icon.png'
						break;
					default:
				}
				for (let i = 0; i < data.length; i++) {
					// 将 icon 传入 marker
					var startMarker = new AMap.Marker({
						position: new AMap.LngLat(data[i].location.lng, data[i].location.lat),
						icon: icon,
						offset: new AMap.Pixel(-16, -32)
					});
					this.pioMarker.push(startMarker)
					// 将 markers 添加到地图
				}
				this.map.add(this.pioMarker);

				// console.log(val)
			},
			// 点击对应的poi信息移动地图
			goConter(val) {
				this.map.setCenter([val.location.lng, val.location.lat])
			},
			onComplete(success) {
				console.log(success)
			},
			onError(error) {
				console.log(error)
			},
			getpio() {
				this.flag = false
				this.poiFlag = true
			},
			goPath() {
				let obj = {
					id: this.id,
					type: 'floor'
				}
				this.$api.goPage('map/index.html', obj);
			}
		},
	}
}());
