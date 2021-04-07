const app = getApp();
Page({
  /**
   * 页面的初始数据
   */
  data: {
    domain_name: app.data.domain_name,
    doorInfo:[]
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var _this=this;
    //获取楼盘的所有户型
    app.ajax("buildingAjax/getBuildingDoor", { id: options.id }, function (res) {
      var data = res.data;
      _this.setData({ doorInfo: data.doorInfo})
    })
  },
  //跳转户型详情
  onModelDetail(event){
    wx.navigateTo({ url: '/pages/store/model_detail/model_detail?id=' + event.currentTarget.dataset.id })
  }
})