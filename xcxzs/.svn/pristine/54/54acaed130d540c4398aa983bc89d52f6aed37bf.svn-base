const app = getApp();
var WxParse = require('../../../wxParse/wxParse.js')
var id=0;
Page({
  /**
   * 页面的初始数据
   */
  data: {
    domain_name: app.data.domain_name,
    scroll_top: 0,
    current: 0, //当前轮播图索引
    doorImgInfo: [],  //轮播图数据
    buildingInfo: {},  //楼盘信息
    doorInfo: {},  //户型信息
    remainingDoorInfo:[]  //本楼盘其他户型
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var _this = this;
    id=options.id;
    //获取户型详情数据
    app.ajax("buildingAjax/getBuildingDoorDetail", { id: id }, function (res) {
      var data = res.data;
      WxParse.wxParse('spaceData', 'html', data.doorInfo.spatial_information, _this, 0);
      _this.setData({ doorInfo: data.doorInfo, buildingInfo: data.buildingInfo, doorImgInfo: data.doorImgInfo, remainingDoorInfo: data.remainingDoorInfo})
    })
  },
  //轮播图改变事件
  onSwiperChange(e) {
    this.setData({ current: e.detail.current })
  },
  //户型跳转
  onModelDetail(event){
    this.onLoad({ id: event.currentTarget.dataset.id})
    this.setData({ scroll_top:0})
  },
  //跳转房价计算器
  onCalculator() {
    wx.navigateTo({ url: '/pages/store/calculator/calculator' })
  }
})