const app = getApp()
var id=0,page=1;
Page({
  /**
   * 页面的初始数据
   */
  data: {
    domain_name: app.data.domain_name,
    domain_img: app.data.domain_img,
    show: false,
    userInfo:{},  //经纪人信息
    zxList: [], //资讯
    lpList: [], //楼盘
    fyNum: 0,  //房源数
    khNum: 0,  //客户数
    lllNum:0,  //浏览量数
    zx_width:100,  //资讯头条对应的宽度
    lengshow:true,
    userinfoshow:false,
    optionsss:''

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
    that.getbrokerinfo()
  },
  onUnload() {
    if (app.data.userInfo.id !== undefined) {
      app.browsingHistory(1, false, id, 0, 0);
    }
  },
  onShareAppMessage(res) {
    var _this=this;
    app.shareHistory(1, id, 0, 0);
    return {
      title: _this.data.userInfo.name,
      path: '/pages/store/store_detail/store_detail?id=' + id,
      imageUrl: _this.data.userInfo.headimgurl
    }
  },
  // 获取经纪人信息
  getbrokerinfo(){
    var _this = this;
    _this.setData({
      zxList:[],
      lpList: [],
    })
    if (_this.data.optionsss.id == undefined) {
        id = decodeURIComponent(_this.data.optionsss.scene);
        //添加到经纪人客户对应表
        app.ajax("agentAjax/addCustomer", { agent_id: id, user_id: app.data.userInfo.id, source:1 }, function (res) { })
    } else {
      id = _this.data.optionsss.id;
    }
    app.browsingHistory(1, true, id, 0, 0);
    //获取经纪人详情页面信息
    app.ajax("agentAjax/getAgentDetailData", { agent_id: id, user_id: app.data.userInfo.id }, function (res) {
      var data = res.data;
      if (data.success) {
        _this.setData({ dataInfo: data.data, storeInfo: data.data.storeInfo, zxList: data.data.zxInfo, fyNum: data.data.fyNum, khNum: data.data.khNum })
      }
      _this.setZxWidth();
      wx.setStorageSync('9h_localStorage', JSON.stringify({ type: '1', agent_id: id, user_id: data.data.user_id, time: new Date().getTime() }))
    })
    page = 1;
    _this.onGetDataInfo();
  },
  onClose() {
    this.setData({ show: false });
  },
  //资讯头条对应的宽度
  setZxWidth(){
    var myLength = this.data.zxList.length;
    this.setData({
      zx_width: parseInt(myLength)*40
    })
  },
  onArticleDetail(event){ //文章详情跳转
    var id = event.currentTarget.dataset.id;
    wx.navigateTo({ url: '/pages/index/article_detail/article_detail?id=' + id })
  },
  //返回首页
  onGoHome(){
    wx.switchTab({
      url: '/pages/index/index/index'
    })
  },
  //二维码
  onQrcode(){
    this.setData({ show: true });
  },
  onCall(){ //拨打电话
    var phone = this.data.dataInfo.phone;
    if (phone==""){
      wx.showToast({title: '该经纪人未设置号码',icon: 'none'})
    }else{
      wx.makePhoneCall({phoneNumber: phone})
    }
  },
  onTalkChat(event){ //微聊跳转
    wx.navigateTo({ url: '/pages/talk/chat/chat?title=' + event.currentTarget.dataset.title})
  },
  //名片海报
  onPosters(){
    wx.navigateTo({ url: '/pages/store/posters/posters?id=' + id})
  },
  //经纪人楼盘
  onBuilding(){
    wx.navigateTo({ url: '/pages/store/building/building?id='+id })
  },
  //资讯头条
  onHeadlines(){
    wx.navigateTo({ url: '/pages/store/headlines/headlines' })
  },
  //楼盘详情
  onHouseDetail(event){
    var building_id = event.currentTarget.dataset.id;
    wx.navigateTo({ url: '/pages/store/house_detail/house_detail?id=' + building_id+"&agent_id="+id})
  },
  //ajax获取数据
  onGetDataInfo() {
    var _this = this;
    //获取经纪人对应的楼盘数据
    app.ajax("agentAjax/getAgentBuildingData", { agent_id: id,page:page }, function (res) {
      var data = res.data;
      if (data.success){
        if (data.data.length > 0 || _this.data.lpList.length>0){
          _this.setData({
            lengshow: false
          });
        }else{
          _this.setData({
            lengshow: true
          });
        }
        _this.setData({ lpList: _this.data.lpList.concat(data.data)});
        page++;
      }
    })
  }
})
