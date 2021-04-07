const app = getApp();
Page({
  data: {
    code: null,
    isLogin: false,
  },
  onShow() {
    app.getWxCode();
  },
  setUserInfo(userInfo) {
    if (userInfo.detail.errMsg == "getUserInfo:ok") {
      const that = this;
      app.login(userInfo, () => {
        that.setData({
          isLogin: true
        });

        wx.showToast({
          title: '授权登录成功',
          icon: 'success',
          success: setTimeout(that.backToPage, 1500)
        })
      });
    }
  },
  backToPage() {
    // 如果不是tabbar页面,没有授权直接跳回首页
    const history = getCurrentPages();
    const routePath = history[history.length -2].route;
    const pageName = routePath.split('/').reverse()[0];
    const tabbarPath = ['store', 'talk', 'me'];
    const index = tabbarPath.findIndex(item => item == pageName);
    if (getCurrentPages().length <= 2 && index == -1 && !this.data.isLogin) {
      wx.switchTab({
        url: '/pages/index/index/index'
      });
    } else {
      let backPageObj = history[history.length -2]
      let options = wx.getLaunchOptionsSync().query
      backPageObj.onLoad(options);
     // backPageObj.onShow();
      wx.navigateBack();
    }
  }
})
