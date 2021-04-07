const app = getApp()
Page({
  /**
   * 页面的初始数据
   */
  data: {
    userInfo: {}
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

  },
  onShow() {
    this.setData({
      userInfo: app.data.userInfo
    });
  },
  getUserInfo() {  //点击头像
    if (app.data.userInfo.id == undefined) {
      app.checkLogin();
    }
  },
  //楼盘收藏跳转
  onCollection(){
    app.checkLogin(()=>{
      wx.navigateTo({ url: '/pages/me/collection/collection' })
    })
  },
  onRecord(){
    app.checkLogin(() => {
      wx.navigateTo({ url: '/pages/me/record/record' })
    })
  },
  onFeedback(){
    app.checkLogin(() => {
      wx.navigateTo({ url: '/pages/me/feedback/feedback' })
    })
  }

})
