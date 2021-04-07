var login_timer = null
App({
  data: {
    userInfo: {avatarUrl: "/image/moren.jpg",nickName: '请登录'},
    domain_img: "https://www.999house.com/",
    domain_name: "https://chfx.999house.com/",  //服务器域名
    domain_websocket: "wss://chat.999house.com:1201/",  //websocket服务器域名
    isSilentGetUserInfo: true,
    code: null
  },
  globalData: {
    userInfo: null,
    domain_name: "https://chfx.999house.com/",  
  },
  onLaunch() {
    var that = this
    wx.getSetting({
			success: function(resp){
				if(resp.authSetting['scope.userInfo']) {
					that.getWxCode(that.getUserInfo); // 静默登录
				}
			}
		})
    
  },
  getWxCode(cb) {
    if(login_timer){clearTimeout(login_timer);login_timer=null;}
    login_timer = setTimeout(() => {
      wx.login({
        success: data => {
          if (data.errMsg == "login:ok") {
            this.data.code = data.code;
            typeof cb == "function" && cb();
          }
        }
      })
    }, 150);
  },
  getUserInfo: function (cb, errCb) {
    var _this = this
    //调用登录接口
      if (_this.data.code) {
        wx.getUserInfo({
          success: userInfo => {
            _this.login(userInfo, cb);
          },
          fail: function () {
            typeof errCb == "function" && errCb()
            if (!_this.data.isSilentGetUserInfo) {
              wx.showToast({title: '获取授权失败',icon: 'none'})
            }
          }
        })
      }
  },
  login(userInfo, cb) {
    const _this = this;
    const {encryptedData, iv} = userInfo.detail? userInfo.detail: userInfo;
    _this.ajax("userAjax/updateuser", {
      js_code: _this.data.code,
      encryptedData: encodeURIComponent(encryptedData),
      iv: iv
    },
    function (res) {
      if (res.data.code == 1){
        if (res.data.token != ''){
          wx.setStorageSync('9h_token', res.data.data.token);
        }
        _this.data.userInfo = res.data.data.userInfo;
        _this.globalData.userInfo = res.data.data.userInfo;
        typeof cb == "function" && cb(res.data.userInfo)
      }else{
        if (_this.data.isSilentGetUserInfo) {
          _this.data.isSilentGetUserInfo = false;
        } else {
          wx.showToast({title: '用户信息获取失败,请稍后重试',icon: 'none'})
        }
      }
    });
  },
  /*
    浏览记录统计
    browse_type:浏览类型 1：名片  2：文章  3：楼盘
    tag:true|false 开始或结束
    agent_id：经纪人id
    article_id：文章id
    building_id：楼盘id
  */
  browsingHistory(browse_type, tag, agent_id = 0, article_id =0, building_id=0){
    var _this=this;
    //自增浏览记录
    if (tag){
      if (browse_type == 3) {  //楼盘浏览数自增
        _this.ajax("buildingAjax/addViewsNumber", { id: building_id }, function (res) { })
      } else if (browse_type == 2) { //文章浏览数自增
        _this.ajax("articleAjax/addViewsNumber", { id: article_id }, function (res) { })
      }
    }
    var parameter={
      user_id: _this.data.userInfo.id,agent_id: agent_id,building_id: building_id,article_id: article_id,browse_type: browse_type,tag: tag?'start':'end'
    }
    _this.ajax("surroundingAjax/browsingHistory", parameter, function (res) {})
  },
  /*
    分享记录统计
    browse_type:分享类型 1：名片  2：文章  3：楼盘
    agent_id：经纪人id
    article_id：文章id
    building_id：楼盘id
  */
 shareHistory(browse_type,agent_id = 0, article_id =0, building_id=0){
  var _this=this;
  var parameter={
    user_id: _this.data.userInfo.id,agent_id: agent_id,building_id: building_id,article_id: article_id,browse_type: browse_type
  }
  _this.ajax("surroundingAjax/shareHistory", parameter, function (res) {})
 },
  //检测是否登陆
  checkLogin: function (fun, isNavigateToLogin = true) {
    var that = this
    return new Promise(function(resolve){
      if (that.data.userInfo.id !== undefined) {
        typeof fun == "function" && fun()
      } else {
        if (isNavigateToLogin) {
          if(login_timer){clearTimeout(login_timer);login_timer=null;}//清掉默认登录
          wx.getSetting({
            success: function(resp){
              if(resp.authSetting['scope.userInfo']) {
                that.getWxCode(function(){
                  that.getUserInfo(resolve)
                }); // 静默登录
              }else{
                wx.navigateTo({
                  url: '/pages/authorization/user_info/user_info'
                });
              }
            }
          }) 
        }
      }
    })
  },
  ajax: function (url, data, success, fail, isPreFix=true) {
    var that = this

    if(that.globalData.userInfo&&that.globalData.userInfo.id){
      data.user_id = that.globalData.userInfo.id
    }

    var header = {
      'content-type': 'application/x-www-form-urlencoded'
    }
    if(data._header){
      header = data._header
    }

    wx.request({
      url: `${this.data.domain_name}${isPreFix? "xcxapi/": ''}${url}`,
      data: data,
      method: "POST",
      header: header,
      success: function (res) {
        if (success) {
          success(res);
        }
      },
      fail: function (res) {
        if (fail) {
          fail(res);
        } else {
          wx.showToast({
            title: '服务器连接失败',
            icon: 'none',
          })
        }
      }
    })
  },
  setUserInfo(userInfo) {
    this.data.userInfo = userInfo;
    this.globalData.userInfo = userInfo;
  },

  //获取扫码的参数
  getScanParms(options){
    if(!options.scene){
      return ''
    }
    options.scene =decodeURIComponent(options.scene)
    if(options.scene.indexOf('=')=='-1'){
      return options.scene
    }
    options.scene = options.scene.split('&')
    let obj = {}
    for(var i in options.scene){
      options.scene[i] = options.scene[i].split('=')
      obj[options.scene[i][0]] = options.scene[i][1]
    }
    return obj
  }
})
