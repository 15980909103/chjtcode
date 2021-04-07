const app = getApp()
Page({
  /**
   * 页面的初始数据
   */
  data: {
    systemNum: 0,
    list: [],
    pageShow: false,
    domain_name: app.globalData.domain_name,
    isFirstIn: true
  },
  onShow:function(){
    var _this = this;
    if (this.data.isFirstIn || app.data.userInfo.id) {
      app.checkLogin(this.getChatList)
    }
  },
  onHide() {
    this.setData({
      isFirstIn: !this.data.isFirstIn,
    })
  },
  /**
   * 标记为已读
   */
  read: function (e) {
    var index = e.currentTarget.dataset.index;
    var agent_id = e.currentTarget.dataset.agent_id;
    var _this=this;
    var list = _this.data.list;
    app.ajax("agentAjax/updateYd", { agent_id:agent_id,user_id: app.data.userInfo.id }, function (res) {
      var data = res.data;
      if (data.success) {
        list[index].unread_num = '0';
        _this.setData({ list: list})
      }
    })
  },
  /**
   * 删除
   */
  del: function (e) {
    var id = e.currentTarget.dataset.id;
    var index = e.currentTarget.dataset.index;
    var agent_id = e.currentTarget.dataset.agent_id;
    var _this = this;
    var list = _this.data.list;
    app.ajax("agentAjax/deleteMessageList", { id:id,agent_id: agent_id, user_id: app.data.userInfo.id }, function (res) {
      var data = res.data;
      if (data.success) {
        list.splice(index, 1);
        _this.setData({ list: list })
      }
    })
  },
  /**
   * 跳转聊天记录
   */
  linkChat: function (event) {
    var agent_id = event.currentTarget.dataset.agent_id;
    var user_id = event.currentTarget.dataset.user_id;
    var title = event.currentTarget.dataset.title;
    wx.setStorageSync('9h_localStorage', JSON.stringify({ type: '1', agent_id: agent_id, user_id: user_id, time: new Date().getTime() }))
    wx.navigateTo({
      url: '/pages/talk/chat/chat?title=' + title
    })
  },
  //系统通知跳转
  onSystem(){
    wx.navigateTo({
      url: '/pages/talk/system/system'
    })
  },
  getChatList() {
    var _this = this;
    app.ajax("agentAjax/getChatList", { user_id: app.data.userInfo.id }, function (res) {
      var data = res.data;
      _this.setData({ systemNum:data.systemNum})
      if (data.success) {
        _this.list = res.data;
        _this.setData({ list: data.data,pageShow:true  })
      }
    })
  }
})
