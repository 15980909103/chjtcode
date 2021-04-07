const app = getApp()
var id = 0;
Page({
  /**
   * 页面的初始数据
   */
  data: {
    domain_name: app.data.domain_name,
    buildingInfo: {}, //楼盘信息
    selectedIndex:0,  //当前选中的标签页索引
    mapData: {  //地图数据
      longitude: 0,
      latitude: 0,
      markers: []
    },
    locationIndex: 0,  //选择的索引
    mapInfo: [],  //周边位置数据
    mapIndex: 20,  //所选详细列索引
    mapIndex2: 20,  //所选详细列索引2
    location_show:false,  //是否展开显示具体详情

    selectLocationIndex: 0,
    selectLocationItemIndex: -1,
    
    mainIcon: '/image/icon-map-sign2.png',
    otherIcon: '/image/icon-artical-map.png',
    itemIconList:[
      '/image/l-bus-icon.png',
      '/image/l-study-icon.png',
      '/image/l-hospital-icon.png',
      '/image/l-shop-icon.png',
      '/image/l-food-icon.png',
    ],
    buildIcon:'/image/l-bulid-icon.png',
    build_coordinate:[]
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var _this=this;
    id=options.id;
    _this.setData({ selectedIndex: options.index })
    //获取楼盘详情数据
    app.ajax("buildingAjax/getBuildingDetail2", { id: id}, function (res) {
      var data = res.data;
      var coordinate = data.buildingInfo.coordinate.split(",");
      var mapData = _this.data.mapData;
      mapData.latitude = coordinate[0];
      mapData.longitude = coordinate[1];
      //mapData.markers = [{ id: 0, latitude: coordinate[0], longitude: coordinate[1], iconPath: _this.data.mainIcon, width: 45, height: 45 }]
      mapData.markers = [{ id: 0, latitude: coordinate[0], longitude: coordinate[1], iconPath: _this.data.buildIcon, width: 45, height: 45 }]

      _this.setData({ buildingInfo: data.buildingInfo,mapInfo: data.mapInfo, mapData: mapData,build_coordinate:coordinate});
      _this.setMapMarkers();
    })
  },
  //标签页切换
  onTabsChange(e){
    this.setData({ selectedIndex: e.detail.index })
  },
  // 周边位置切换nav
  onLocation(event) {
    var index = event.currentTarget.dataset.index;
    this.setData({
      locationIndex: index,
      selectLocationIndex: index,
      selectLocationItemIndex: -1
    });

    const mapData = this.data.mapData;
    mapData.markers[0] = { id: 0, latitude: this.data.build_coordinate[0], longitude: this.data.build_coordinate[1], iconPath: this.data.buildIcon, width: 45, height: 45 }
    this.setMapMarkers();
  },
  //展开地图详情事件
  onLocationShow(){
    this.setData({ location_show: !this.data.location_show})
  },
  //反馈纠错
  onFeedback(){
    wx.navigateTo({ url: '/pages/store/feedback/feedback?id=' + id})
  },
  //地图坐标事件
  selectPoint(event){
    var index = event.currentTarget.dataset.index;
    var lat = event.currentTarget.dataset.lat;
    var lng = event.currentTarget.dataset.lng;
    const target = this.data.mapData.markers[index + 1];
    const currentSelect = this.data.mapData.markers[this.data.selectLocationItemIndex + 1];

    if (currentSelect) {
      //currentSelect.iconPath = this.data.otherIcon;
      currentSelect.iconPath = this.data.itemIconList[this.data.locationIndex];
      currentSelect.width = 28
      currentSelect.height = 28
    }
    target.iconPath = this.data.mainIcon;
    target.width = 40
    target.height = 40

    const mapData = this.data.mapData;
    mapData.markers[0] = { id: 0, latitude: this.data.build_coordinate[0], longitude: this.data.build_coordinate[1], iconPath: this.data.buildIcon, width: 45, height: 45 }
    mapData.latitude = lat;
    mapData.longitude = lng;

    this.setData({
      selectLocationItemIndex: index,
      mapData,
      location_show:false
    });
  },
  setMapMarkers() {
    var that = this
    var locationIndex = that.data.locationIndex
    const locationData = this.data.mapInfo[locationIndex].data;
    const mapData = this.data.mapData;
    mapData.markers = mapData.markers.slice(0, 1).concat(locationData.map((item, index) => ({
          id: index + 1,
          latitude: item.lat,
          longitude: item.lng,
          iconPath: that.data.itemIconList[locationIndex],////this.data.otherIcon,
          width: 28,
          height: 28,
          title: item.title
    })));
    /* if (this.data.selectLocationItemIndex == -1) {
      mapData.markers[0].iconPath = this.data.mainIcon;
    } */
    this.setData({
      mapData
    });
  },
})
