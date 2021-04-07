const app = getApp()
var page = 1;
Page({
  /**
   * 页面的初始数据
   */
  data: {
    domain_name: app.data.domain_name,
    systemInfo: {},  //系统透与昵称
    systemList: []  //聊天消息记录
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var _this=this;
    page = 1;
    app.ajax("agentAjax/getSystemInfo", {}, function (res) {
      var data=res.data;
      _this.setData({ systemInfo: data.systemInfo})
    })
    app.ajax("agentAjax/updateSystenRead", { user_id: app.data.userInfo.id }, function (res) {})
    this.pullupRefresh()
  },
  pullupRefresh() {    //下拉加载
    var _this = this;
    app.ajax("agentAjax/getSystenInforms", { page: page, user_id: app.data.userInfo.id }, function (res) {
      var data = res.data;
      if (data.success) {
        var systemList = _this.data.systemList;
        _this.setData({ systemList: data.data.concat(systemList) })
        if (page == 1) { _this.onscrollBottom(); }
        page++;
      }
    })
  },
  onBindscroll(event) {  //监听滚动事件
    var scroH = event.detail.scrollTop;
    if (scroH < 10 && page > 1) {
      this.pullupRefresh()
    }
  },
  onscrollBottom() {   //滚动到底部
    var _this = this;
    var query = wx.createSelectorQuery();
    query.select('.my-both2').boundingClientRect(function (rect) {
      _this.setData({ scrollTop: rect.top })
    }).exec();
  }
})