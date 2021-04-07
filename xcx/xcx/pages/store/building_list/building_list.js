const app = getApp();
var id = 0, agent_id = 0;
Page({
  /**
   * 页面的初始数据
   */
  data: {
    domain_name: app.data.domain_name,
    agentInfo:{}, //经纪人信息
    buildingInfo:{},  //楼盘信息
    floorInfo:[], //楼栋信息
    floorIndex:0, //当前选中的楼栋索引
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    id = options.id;
    agent_id = options.agent_id;
    var _this=this;
    //获取楼栋数据
    app.ajax("buildingAjax/getBuildingFloor", { id: id, agent_id: agent_id }, function (res) {
      var data = res.data;
      _this.setData({ agentInfo: data.agentInfo, buildingInfo: data.buildingInfo, floorInfo: data.floorInfo})
    })
  },
  //楼栋点击事件
  onFloorSet(event){
    this.setData({ floorIndex: event.currentTarget.dataset.index})
  },
  //楼栋滑动事件
  onSwiperSet(event){
    this.setData({ floorIndex: event.detail.current})
  },
  onCall() { //拨打电话
    var phone = this.data.agentInfo.phone;
    if (phone == "") {
      wx.showToast({ title: '该经纪人未设置号码', icon: 'none' })
    } else {
      wx.makePhoneCall({ phoneNumber: phone })
    }
  },
  //跳转楼栋详细信息
  onBuildingDetail(event){
    var id = event.currentTarget.dataset.id;  //楼栋id
    var title = event.currentTarget.dataset.title;  //单元id
    wx.navigateTo({ url: '/pages/store/building_detail/building_detail?id=' + id + '&agent_id=' + agent_id + '&title=' + title })
  }
})