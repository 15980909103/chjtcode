const app = getApp()
var autoClose = true;
var page=1;
var isNotError=true;
var myLocalStorage=[];
Page({
  /**
   * 页面的初始数据
   */
  data: {
    agentInfo:{},
    userInfo:{},
    scrollTop:0,
    textContent:"",
    messageList: []  //聊天消息记录
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var _this=this;
    if (!app.data.userInfo.id) {
      app.checkLogin().then(function(){
        _this.onLoad(options)
      })
      return;
    }
    if(options&&options.title){
      wx.setNavigationBarTitle({title: options.title})
    }
    let parms =''
    if(options.scene){//扫码进的
      parms = app.getScanParms(options)
    }
    
    page = 1;
    autoClose = true;
    isNotError=true;
    this.onscrollBottom();

    //连接WebSocket
    myLocalStorage = wx.getStorageSync("9h_localStorage");
    if(parms.agent_id){//扫码进入的
      myLocalStorage = { type: '1', agent_id: parms.agent_id, user_id: app.data.userInfo.user_id, time: new Date().getTime() }
      wx.setStorageSync('9h_localStorage', JSON.stringify(myLocalStorage))
    }else if(myLocalStorage){
      myLocalStorage = JSON.parse(myLocalStorage);
    }else{
      myLocalStorage = ''
    }
    if (myLocalStorage != "") {
      if((new Date().getTime() - myLocalStorage.time) <= 86400000) {
        var token = wx.getStorageSync("9h_token");
        if (token !=""){
          wx.connectSocket({ url: app.data.domain_websocket })
          wx.onSocketOpen(function () { //初始化聊天信息
            wx.sendSocketMessage({
              data: JSON.stringify({ type: 'user', user: myLocalStorage.type + "-" + myLocalStorage.agent_id + "-" + myLocalStorage.user_id + "?" + token })
            })
            setTimeout(function(){
              if (isNotError){
                app.ajax("agentAjax/getPortraitData", { agent_id: myLocalStorage.agent_id, user_id: app.data.userInfo.id }, function (res) {
                  var data = res.data;
                  _this.setData({ agentInfo: data.agentInfo, userInfo: data.userInfo })
                })
                _this.pullupRefresh();
              }
            },600)
          })
          wx.onSocketMessage(function (event) {  //接受监听
            var result = JSON.parse(event.data);
            var messageList = _this.data.messageList;
            messageList.push({ sender: result.sender, success: result.success, content: result.data });
            _this.setData({ messageList: messageList })
            _this.onscrollBottom();
          })
          wx.onSocketError(function(e){
            wx.showModal({
              title: '连接错误',
              content: e.errMsg,
              showCancel: false,
              success(res) {
                if (res.confirm) {
                  wx.navigateBack({ delta: 1 })
                }
              }
            })
          })
          wx.onSocketClose(function (e) {
            if (autoClose) {
              isNotError=false;
              var reason = e.reason == "" ? '服务器连接异常' : e.reason;
              wx.showModal({
                title: '提示',
                content: reason,
                showCancel: false,
                success(res) {
                  if (res.confirm) {
                    wx.navigateBack({ delta: 1 })
                  }
                }
              })
            }
          })
        }else{
          wx.showToast({ title: 'token不正确！', icon: 'none' })
        }
      }else{
        wx.showToast({ title: 'WebSocket连接失败！', icon: 'none' })
      }
    }else{
      wx.showToast({title: 'WebSocket连接失败！',icon: 'none'})
    }
  },
  onUnload: function () {
    autoClose = false;
    wx.closeSocket()
  },
  setTextContent(event){
    this.setData({ textContent: event.detail.value})
  },
  onSend() {
    var textContent = this.data.textContent;
    if (textContent == "") {wx.showToast({ title: '发送内容不能为空！', icon: 'none' }); return false; }
    wx.sendSocketMessage({
      data: JSON.stringify({ type:'message', message_type: '1', content: textContent })
    })
    this.setData({ textContent:""})
    this.onscrollBottom();
  },
  pullupRefresh() {    //下拉加载
    var _this = this;
    app.ajax("agentAjax/getChatMessage", { page: page,agent_id: myLocalStorage.agent_id, user_id: app.data.userInfo.id }, function (res) {
      var data = res.data;
      if (data.success) {
        var messageList = _this.data.messageList;
        _this.setData({ messageList: data.data.concat(messageList)})
        if (page == 1) {_this.onscrollBottom();}
        page++;
      }
    })
  },
  onBindscroll(event){  //监听滚动事件
    var scroH = event.detail.scrollTop;
    if (scroH < 10 && page > 1) {
      this.pullupRefresh()
    }
  },
  onscrollBottom() {   //滚动到底部
    var _this=this;
    var query = wx.createSelectorQuery();
    query.select('.my-both').boundingClientRect(function (rect) {
      if(rect){
        _this.setData({scrollTop: rect.top})
      }
    }).exec();
  }
})