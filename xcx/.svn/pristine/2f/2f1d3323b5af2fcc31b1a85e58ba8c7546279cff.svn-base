const app = getApp();
Page({
  /**
   * 页面的初始数据
   */
  data: {
    domain_name: app.data.domain_name,
    agentInfo:{}, //经纪人信息
    unitInfo:[]
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var title = options.title;  //楼栋名称
    var id = options.id;  //楼栋id
    var agent_id = options.agent_id;  //经纪人id
    var _this = this;
    wx.setNavigationBarTitle({ title: title})
    //获取楼栋单元户型数据
    app.ajax("buildingAjax/getBuildingUnit", { id: id, agent_id: agent_id }, function (res) {
      var data = res.data;
      _this.setData({ agentInfo: data.agentInfo, unitInfo: data.unitInfo })
    })
  },
  onCall() { //拨打电话
    var phone = this.data.agentInfo.phone;
    if (phone == "") {
      wx.showToast({ title: '该经纪人未设置号码', icon: 'none' })
    } else {
      wx.makePhoneCall({ phoneNumber: phone })
    }
  },
  //跳转户型详情
  onModelDetail(event) {
    wx.navigateTo({ url: '/pages/store/model_detail/model_detail?id=' + event.currentTarget.dataset.id })
  },
})