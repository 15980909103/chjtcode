const app = getApp();
var id = 0, agent_id=0;
Page({
  /**
   * 页面的初始数据
   */
  data: {
    domain_name:app.data.domain_name,
    domain_img: app.data.domain_img,
    is_collection:false,  //是否收藏
    buildingInfo:{}, //楼盘信息
    agentInfo:{},   //经纪人信息
    doorInfo:[],  //主力户型信息
    agentBuildingInfo:{}, //经纪人与楼盘对应信息 开盘/降价
    scroll_top: 0,
    message:{is_message: false, tag:""}, //开盘或降价通知
    img:'https://wx.qlogo.cn/mmopen/vi_32/PiajxSqBRaELmLRnB2FBsKKZq8nLVq2ITWeyJGKgicud6Tf0F4naf1811yzYMgpzU1qsJPBRNqJ0xibY4fMR7V8gw/132',
    current: 0, //当前轮播图索引
    shuffleInfo:[],  //轮播图数据
    mapData: {  //地图数据
      longitude: 0,
      latitude: 0,
      markers: [],
      userinfoshow:false
    },
    floorInfo: [],  //楼栋信息
    lpList: [], //楼盘
    locationIndex: 0,  //选择的索引
    mapInfo: [],  //周边位置数据
    optionsss:'',
    itemIconList:[
      '/image/l-bus-icon.png',
      '/image/l-study-icon.png',
      '/image/l-hospital-icon.png',
      '/image/l-shop-icon.png',
      '/image/l-food-icon.png',
    ],
    buildIcon:'/image/l-bulid-icon.png',
    mainIcon:'/image/icon-map-sign2.png'
  },
  onLoad: function (options) {
    this.setData({
      optionsss: options
    });
  },
  onShow() {
    var that = this
    if (!app.data.userInfo.id) {
      app.checkLogin().then(function(){
        that.onShow()
      });
      return
    }
    that.gethousesinfo()
  },
  onUnload() {
    if (app.data.userInfo.id !== undefined) {
      app.browsingHistory(3, false, agent_id, 0, id);
    }
  },
  onShareAppMessage() {
    if (app.data.userInfo.id !== undefined) {
      app.shareHistory(3, agent_id, 0, id);
    }
  },
  // 获取楼盘信息
  gethousesinfo(){
    var _this = this;
    if (_this.data.optionsss.scene != undefined) {
      // app.checkLogin(function () {
      //   scene = decodeURIComponent(_this.data.optionsss.scene);
      //   scene = scene.split(",");
      //   id = scene[0];
      //   agent_id = scene[1];
      //   //添加到经纪人客户对应表
      //   app.ajax("agentAjax/addCustomer", { agent_id: id, user_id: app.data.userInfo.id }, function (res) {
      //     var data = res.data;
      //   })
      // })

      let scene = decodeURIComponent(_this.data.optionsss.scene);
      scene = scene.split(",");
      id = scene[0];
      agent_id = scene[1];
      //添加到经纪人客户对应表
      app.ajax("agentAjax/addCustomer", { agent_id: agent_id, user_id: app.data.userInfo.id, source:3 }, function (res) { })
    } else {
      id = _this.data.optionsss.id;
      agent_id = _this.data.optionsss.agent_id;
    }
    app.browsingHistory(3, true, agent_id, 0, id);
    //获取楼盘详情数据
    app.ajax("buildingAjax/getBuildingDetail", { id: id, agent_id: agent_id, user_id: app.data.userInfo.id }, function (res) {
      var data = res.data;
      var coordinate = data.buildingInfo.coordinate.split(",");
      var mapData = _this.data.mapData;
      mapData.latitude = coordinate[0];
      mapData.longitude = coordinate[1];
      mapData.markers = [{ id: 0, latitude: coordinate[0], longitude: coordinate[1], iconPath: _this.data.buildIcon, width: 40, height: 40 }]
      _this.setData({ buildingInfo: data.buildingInfo, shuffleInfo: data.shuffleInfo, agentInfo: data.agentInfo, doorInfo: data.doorInfo, mapInfo: data.mapInfo, mapData: mapData, lpList: data.lpList, floorInfo: data.floorInfo, agentBuildingInfo: data.agentBuildingInfo, is_collection: data.is_collection })
      _this.setMapMarkers();
      wx.setNavigationBarTitle({ title: data.buildingInfo.name })
    })
  },
  //轮播图改变事件
  onSwiperChange(e){
    this.setData({ current: e.detail.current})
  },
  //开盘降价通知
  onMessage(event){
    var message=this.data.message;
    message.is_message = true;
    message.tag = event.currentTarget.dataset.tag;
    this.setData({ message: message });
  },
  onClose() { //关闭遮罩层
    this.setData({ message:{is_message: false,tag:""}});
  },
  // 周边位置切换nav
  onLocation(event){
    var index = event.currentTarget.dataset.index;
    this.setData({ locationIndex: index });
    this.setMapMarkers();
  },
  //开盘/降价提醒
  onNotice(){
    var _this=this;
    var tag = this.data.message.tag;
    if (tag =='kp')
      var notice = this.data.agentBuildingInfo.kaipan_notice == '0' ? 1 : 0;
    else
      var notice = this.data.agentBuildingInfo.jianjia_notice == '0' ? 1 : 0;
    //修改开盘/降价提醒
    app.ajax("buildingAjax/updateNotice", { id: id, agent_id: agent_id, user_id: app.data.userInfo.id, tag: tag, notice: notice }, function (res) {
      var data = res.data;
      if(data.success){
        _this.onClose();
        var agentBuildingInfo = _this.data.agentBuildingInfo;
        if (tag == 'kp')
          agentBuildingInfo.kaipan_notice = notice;
        else
          agentBuildingInfo.jianjia_notice = notice;
        _this.setData({ agentBuildingInfo: agentBuildingInfo})
        wx.showToast({title: '修改成功！',icon: 'none'})
      }else{
        wx.showToast({ title: '修改失败！', icon: 'none' })
      }
    })
  },
  //跳转楼盘信息
  onHouseInfo(event){
    var index = event.currentTarget.dataset.lindex;
    wx.navigateTo({url: '/pages/store/house_info/house_info?index=' + index + "&id=" + id})
  },
  //跳转主力户型
  onModelList(){
    wx.navigateTo({ url: '/pages/store/model_list/model_list?id='+id})
  },
  //跳转楼栋详情
  onBuildingList(){
    wx.navigateTo({ url: '/pages/store/building_list/building_list?id=' + id + "&agent_id=" + agent_id })
  },
  //跳转房价计算器
  onCalculator(){
    wx.navigateTo({ url: '/pages/store/calculator/calculator' })
  },
  //跳转户型详情
  onModelDetail(event) {
    wx.navigateTo({ url: '/pages/store/model_detail/model_detail?id=' + event.currentTarget.dataset.id })
  },
  //楼盘推荐跳转
  onHouseDetail(event){
    app.browsingHistory(3, false, agent_id, 0, id);
    var _id = event.currentTarget.dataset.id;
    this.onLoad({ id: _id, agent_id: agent_id});
    this.setData({ scroll_top:0});
  },
  onCall() { //拨打电话
    var phone = this.data.agentInfo.phone;
    if (phone == "") {
      wx.showToast({ title: '该经纪人未设置号码', icon: 'none' })
    } else {
      wx.makePhoneCall({ phoneNumber: phone })
    }
  },
  //收藏/未收藏事件
  onCollection(){
    var _this = this;
    var _status = _this.data.is_collection?'0':'1';
    app.ajax("buildingAjax/updateCollection", { id: id, agent_id: agent_id,user_id: app.data.userInfo.id, status: _status }, function (res) {
      var data = res.data;
      if(data.success){
        _this.setData({ is_collection: !_this.data.is_collection})
      }else{
        wx.showToast({ title: '修改失败！', icon: 'none' })
      }
    })
  },
  setMapMarkers() {
    const locationData = this.data.mapInfo[this.data.locationIndex].data;
    const mapData = this.data.mapData;
    var that = this
    mapData.markers = mapData.markers.slice(0, 1).concat(locationData.map((item, index) => ({
          id: index + 1,
          latitude: item.lat,
          longitude: item.lng,
          iconPath: that.data.itemIconList[0],//
          width: 28,
          height: 28
    })))
    this.setData({
      mapData
    });
  },
})
