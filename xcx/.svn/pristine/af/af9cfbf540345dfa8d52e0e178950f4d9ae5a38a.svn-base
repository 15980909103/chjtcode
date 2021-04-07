var WxParse = require('../../../wxParse/wxParse.js')
var _id = 0,agent_id=0;
const app = getApp()
Page({
  /**
   * 页面的初始数据
   */
  data: {
    domain_name: app.data.domain_name,
    domain_img: app.data.domain_img,
    agentInfo:{}, //经纪人信息
    articleInfo: {}, //文章详情数据
    focus:false,  //评论栏是否获取焦点
    comments:[],    //评论数组
    commentsText:"",  //评论内容
    advertisingImg:"", //广告图片
    zxList: [], //资讯
    lpList: [{}, {}, {}, {}, {}, {}, {}, {}, {}, {}], //楼盘
    zx_width: 100,  //资讯头条对应的宽度
    scroll_top:0,
    is_agent:false,  //是否经纪人
    page:1,
    replyshow:false,
    indexss:0,
    pageDetail:1,
    commentsDetailInfo:[],
    popoPlaceholder:'',
    popoReplyid:'',
    commentsTextss:'',
    root_id:'',
    userinfoshow:false,
    isLoad: false,
    isInitcomment: true,
    userInfo:'',

    timeoutId: null,
    isRecording: false,
  },
  onUnload: function () {
    this.clearCountDownHistory();
    // app.browsingHistory(2, false, agent_id, _id, 0);
  },
  onHide() {
    this.clearCountDownHistory();
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({
      optionsss: options
    });
    this.getnewsinfo()
    // this.pullupRefresh2()
  },
  onShow(){
    this.setData({
      page: 1
    });

    this.countDownRecordingHistory();
  },
  // 获取新闻详情
  getnewsinfo(){
    var _this = this;
    if (_this.data.optionsss.scene != undefined) {
      app.checkLogin(function () {
        scene = decodeURIComponent(_this.data.optionsss.scene);
        scene = scene.split(",");
        _id = scene[0];
        agent_id = scene[1];
        if (agent_id != 0) { //获取分享的经纪人信息
          app.ajax("articleAjax/getAgentData", { agent_id: agent_id }, function (res) {
            var data = res.data;
            _this.setData({ 
              agentInfo: data.agentInfo,
              userInfo: app.data.userInfo
             })
          })
        }
      })
    } else {
      _id = _this.data.optionsss.id
    }
    app.ajax("articleAjax/getArticleDetail", { id: _id }, function (res) {
      var data = res.data;
      _this.setData({
        userInfo: app.data.userInfo,
        articleInfo: data.articleInfo,
        // comments: data.commentsInfo,
        advertisingImg: data.advertisingInfo&&data.advertisingInfo.img?data.advertisingInfo.img:'',
        // zxList: data.zxInfo
      })
      WxParse.wxParse('spaceData', 'html', data.articleInfo.content, _this, 0);
      // _this.setZxWidth();
    })
    this.pullupRefresh2(1)
  },
  countDownRecordingHistory() {
    if (this.data.isRecording || this.data.timeoutId) {
      return ;
    }
    const id = setTimeout(() => {
      if (this.data.timeoutId) {
        app.browsingHistory(2, true, agent_id, _id, 0);
        this.setData({
          timeoutId: null,
          isRecording: true,
        })
      }
    }, 5000);
    this.setData({
      timeoutId: id
    })
  },
  clearCountDownHistory() {
    if (this.data.timeoutId) {
      clearTimeout(this.data.timeoutId);
      this.setData({
        timeoutId: null
      })
    }
  },
  setZxWidth() {  //资讯头条对应的宽度
    var myLength = this.data.zxList.length;
    this.setData({
      zx_width: parseInt(myLength) * 40
    })
  },
  // 评论列表
  pullupRefresh2(index) {
    var _this = this;
    app.ajax('articleAjax/getReply', {
      page: _this.data.page,
      aid: _id,
      uid: app.data.userInfo.id
    }, function (res) {
      var data = res.data.data;
      var page = _this.data.page++;
      wx.hideLoading();
      if (!res.data.success){
        wx.showToast({
          title: '没有更多数据了！',
          icon: 'none',
          duration: 1000
        })
        return ;
      }
      if(index==1){
        _this.setData({
          // comments: _this.data.comments.concat(res.data.data),
          comments: res.data.data.data,
          page: page
        })
      }else{
        _this.setData({
          comments: _this.data.comments.concat(res.data.data.data),
          // comments: res.data.data,
          page: page
        })
      }
      if (!res.success && _this.data.page==1){
        _this.setData({
          page: 1
        })
      }
    });
  },
  // 上拉加载更多
  onReachBottom: function () {
    var _this = this;
    // 显示加载图标
    wx.showLoading({
      title: '玩命加载中',
    })
    _this.setData({
      page: _this.data.page+1
    })
    _this.pullupRefresh2()
  },
  // 评论详情
  pullupRefresh(root_id) {
    var _this = this;
    _this.setData({
      root_id: root_id
    })
    var pageDetail = this.data.pageDetail;
    app.ajax('articleAjax/getDetailReply', { page: pageDetail, root_id: root_id, uid: app.data.userInfo.id }, function (res) {
      if (res.data.success) {
        var list = null;
        if (pageDetail == 1) {
          list = res.data.data.data;
        } else {
          list = _this.data.commentsDetailInfo.concat(res.data.data.data);
        }
        _this.setData({
          pageDetail: pageDetail+=1,
          commentsDetailInfo: list
        });
      } else {
        if(pageDetail == 1) {
            _this.setData({
              commentsDetailInfo : []
            })
        }
        // if (_this.data.pageDetail == 1) {
        //   _this.data.commentsDetailInfo = [];
        // }
        //                        _self.endPullUpToRefresh(true);   //内容为空了
      }
      wx.hideLoading({
        title: '加载评论中',
        success: () => {
          _this.setData({
            isLoad: false
          });
        }
      });
    });
  },
  // 点赞
  like_btn(event) {
    var id = event.currentTarget.dataset.id
    var status = event.currentTarget.dataset.status
    var _this = this;
    app.ajax('articleAjax/setPraise', {
      cid: id,
      status: status,
      uid: app.data.userInfo.id
    }, function (res) {
      if (res.data.success) {
        _this.data.page = 1
        _this.data.comments = []
        _this.pullupRefresh2();
        // _this.pullupRefresh(_this.pullToRefresh, _this.commentsInfo[_this.popoIndex].id);
        if (status == 1) {
          wx.showToast({
            title: '点赞成功!',
            icon: 'none',
          })
        } else if (status == 0) {
          wx.showToast({
            title: '取消点赞成功!',
            icon: 'none',
          })
        }
      } else {
        wx.showToast({
          title: '点赞失败',
          icon: 'none',
        })
      }
    });
  },

  // 评论列表回复列表
  onReply(event){
    var _this = this;
    var index = event.currentTarget.dataset.index
    var id = event.currentTarget.dataset.id
    var nickName = event.currentTarget.dataset.nickname
    _this.setData({
      pageDetail: 1,
      commentsDetailInfo: [],
      popoPlaceholder: nickName,
      replyshow: !_this.data.replyshow,
      indexss: index,
      popoReplyid: id
    });
    this.pullupRefresh(id)
  },
  // 评论详情回复列表
  onReplyEdetail(event){
    var _this = this;
    var popoplaceholder = event.currentTarget.dataset.popoplaceholder
    var poporeplyid = event.currentTarget.dataset.poporeplyid
    console.log(event)
    _this.setData({
      popoPlaceholder: popoplaceholder,
      popoReplyid: poporeplyid,
      focus: true
    })
  },
  onSendMessage() {
    app.checkLogin(this.onSendDetail);
  },
  //详情回复
  onSendDetail() {
    var _this = this;
    var commentsTextss = _this.data.commentsTextss;
    if (commentsTextss == "") {
      wx.showToast({
        title: '请输入评论内容!',
        icon: 'none',
      })
      return false;
    }
    app.ajax('articleAjax/addDetailComments', {
        parent_id: _this.data.popoReplyid,//二级评论对象id
        root_id: _this.data.root_id, // 一级评论对象id
        content: commentsTextss, //评论内容
        aid: _id,  //文章id
        uid: app.data.userInfo.id //用户id
      }, function (res) {
        console.log(res)
      if (res.data.success) {
        wx.showToast({
          title: '评论成功！',
          icon: 'none',
        })
        
        // _this.commentsDetailInfo = res.commentsInfo.concat(_this.commentsDetailInfo);
        console.log('>>>>>>', this);
        _this.setData({
          commentsTextss : "",
          commentsDetailInfo : _this.data.commentsDetailInfo.concat(res.data.data.commentsInfo)
        })
      } else {
        wx.showToast({ title: res.data.message?res.data.message:'评论失败！', icon: 'none'})
      }
    });
  },
  noshow(e){
    var _this = this;
    _this.setData({
      replyshow: false
    })
  },
  onComments() {   //查看全部评论
    wx.navigateTo({ url: '/pages/index/comments/comments?id=' + _id })
  },
  //经纪人楼盘
  onBuilding() {
    wx.navigateTo({ url: '/pages/store/building/building' })
  },
  onArticleDetail(event){ //新闻推荐跳转
    var id = event.currentTarget.dataset.id;
    wx.navigateTo({
      url: `/pages/index/article_detail/article_detail?id=${id}`
    })
  },
  setFocus() {  //设置焦点
    this.setData({ focus:true})
  },
  setInputs(event) {
    this.setData({ commentsTextss: event.detail.value })
  },
  setInput(event){
    this.setData({ commentsText: event.detail.value})
  },
  //转发
  onShareAppMessage() {
    app.shareHistory(2, agent_id, _id, 0);
  },

  onCall() { //拨打电话
    var phone = this.data.agentInfo.phone;
    if (phone == "") {
      wx.showToast({ title: '该经纪人未设置号码', icon: 'none' })
    } else {
      wx.makePhoneCall({ phoneNumber: phone })
    }
  },
  onSend(){
    var _this=this;
    app.checkLogin(function(){
      var content = _this.data.commentsText;
      if(content==""){
        wx.showToast({title: '请输入评论内容！',icon: 'none'})
        return false;
      }
      var uid = app.data.userInfo.id;
      app.ajax("articleAjax/addComments", { aid: _id, uid: uid, content: content}, function (res) {
        var data = res.data;
        if(data.success){
          wx.showToast({
            title: '评论成功!',
            icon: 'none',
            success() {
              _this.setData({
                 commentsText: "",
              // comments: data.commentsInfo
                comments: _this.data.comments.concat(data.data.commentsInfo),
            })
            }
          })
        }else{
          wx.showToast({ title: data.message?data.message:'评论失败！', icon: 'none' })
        }
      })
    })
  },
  fetchMoreReplay() {
    if (!this.data.isLoad) {
      wx.showLoading({
        title: '加载评论中',
        mask: true,
        success: () => {
          this.setData({
            isLoad: true
          });
        }
      });
      this.pullupRefresh(this.data.popoReplyid);
    }
  }
})
