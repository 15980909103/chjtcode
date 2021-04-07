const app = getApp()
var page = 1
Page({
  /**
   * 页面的初始数据
   */
  data: {
    list: [],
    pageShow: false,
    flag: 0,
    scrollPosition: {
      scrollY: true
    },
    isFirstIn: true,
    domain_name: app.globalData.domain_name
  },
  onShow(options) {
    if (this.data.isFirstIn || app.data.userInfo.id) {
      app.checkLogin(this.onGetDataInfo);
    }
  },
  onHide() {
    this.setData({
      isFirstIn: !this.data.isFirstIn,
    })
  },
  //处理左滑显示事件
  handletouchstart: function (event) {
    this.data.lastX = event.touches[0].pageX;
    this.data.lastY = event.touches[0].pageY;
    if (event.currentTarget.dataset.index != undefined) {
      this.data.swiper_index = event.currentTarget.dataset.index;
    }
  },
  handletouchmove: function (event) {
    var that = this;
    if (that.data.flag !== 0) {
      return false;
    }
    var currentX = event.touches[0].pageX;
    var currentY = event.touches[0].pageY;
    var tx = currentX - that.data.lastX;
    var ty = currentY - that.data.lastY;
    if (Math.abs(tx) > Math.abs(ty)) {
      that.setScrollY(false);
      if (tx < -70 && (ty <= 40 && ty >= -40)) {
        //"向左滑动";
        that.data.flag = 1;
        if (that.data.swiper_index != undefined) {
          var index = that.data.swiper_index;
          var list = that.data.list;
          for (var i = 0; i < list.length; i++) {
            list[i].show_options = false;
          }
          list[index].show_options = true;
          that.setData({
            list: list
          });
        }
        this.setScrollY(true);
      } else if (tx > 70 && (ty <= 40 && ty >= -40)) {
        //"向右滑动";
        that.data.flag = 2;
        if (that.data.swiper_index != undefined) {
          var index = that.data.swiper_index;
          var list = that.data.list;
          list[index].show_options = false;
          that.setData({
            list: list
          });
        }
        this.setScrollY(true);
      }
    }
  },
  handletouchend: function (event) {
    this.data.flag = 0;
    this.setScrollY(true);
  },
  //处理右滑关闭选项
  opstart(event) {
    this.data.lastX = event.touches[0].pageX;
    this.data.lastY = event.touches[0].pageY;
    if (event.currentTarget.dataset.index != undefined) {
      this.data.swiper_index = event.currentTarget.dataset.index;
    }
  },
  opmove(event) {
    var that = this;
    var currentX = event.touches[0].pageX;
    var currentY = event.touches[0].pageY;
    var tx = currentX - that.data.lastX;
    var ty = currentY - that.data.lastY;
    if (Math.abs(tx) > Math.abs(ty)) {
      that.setScrollY(false);
      if (tx > 70 && (ty <= 40 && ty >= -40)) {
        //"向右滑动";
        if (that.data.swiper_index != undefined) {
          var index = that.data.swiper_index;
          var list = that.data.list;
          list[index].show_options = false;
          that.setData({ list: list });
        }
        this.setScrollY(true);
      }
      this.setScrollY(true);
    }
  },
  opend(event) {
    this.setScrollY(true);
  },
  //关闭所有选项信息
  setShowDeletF() {
    var list = this.data.list;
    for (var i in list) {
      list[i].show_options = false;
    }
    this.setData({ list: list });
  },
  //为数据源添加自定义数据
  addCustomAttr(items) {
    for (var i in items) {
      items[i].show_options = false;
    }
    return items;
  },
  setScrollY(isRes) {
    var scrollPosition = this.data.scrollPosition;
    scrollPosition.scrollY = isRes;
    this.setData({
      scrollPosition: scrollPosition
    })
  },
  onDetailCard(event){  //跳转名片详情
    var index = event.currentTarget.dataset.index;
    var id = event.currentTarget.dataset.id;
    var list = this.data.list;
    if (list[index].show_options == true) {
      list[index].show_options = false;
      this.setData({ list: list });
    } else {
      this.setShowDeletF();
      wx.navigateTo({
        url: '/pages/store/store_detail/store_detail?id=' + id
      })
    }
  },
  onDel(event) {  //删除事件
    var _this = this;
    var id = event.currentTarget.dataset.id;
    var index = event.currentTarget.dataset.index;
    wx.showModal({
      title: '提示',
      content: '确认删除该经纪人',
      success(res) {
        if (res.confirm) {
          app.ajax("agentAjax/delUserStatus", { id: id,user_id: app.data.userInfo.id }, function (res) {
            var data = res.data;
            if (data.success) {
              var list = _this.data.list;
              _this.setShowDeletF();
              list.splice(index, 1);
              _this.setData({ list: list })
            } else {
              wx.showToast({ title: '删除失败', icon: 'none' })
            }
          })
        } else if (res.cancel){
          _this.setShowDeletF();
        }
      }
    })
  },
  onTop(event) {    //置顶事件
    var _this=this;
    var id = event.currentTarget.dataset.id;
    var index = event.currentTarget.dataset.index;
    app.ajax("agentAjax/setAgentTop", { id: id, user_id: app.data.userInfo.id }, function (res) {
      var data = res.data;
      if(data.success){
        var list=_this.data.list;
        for (var i in list){
          list[i].user_top = 0;
          list[i].show_options=false;
        }
        var _temp = list[index];
        _temp.user_top=1;
        list.splice(index, 1);
        list.unshift(_temp);
        _this.setData({ list: list})
      }else{
        wx.showToast({ title: '修改失败', icon: 'none' })
      }
    })
  },
  //ajax获取数据
  onGetDataInfo() {
    var _this = this;
    //获取客户对应的经纪人
    app.ajax("agentAjax/getAgentData", { page: page, user_id: app.data.userInfo.id }, function (res) {
      var data = res.data;
      page++;
      var list = _this.data.list.concat(data.agentList);
      _this.setData({ list: _this.addCustomAttr(list),pageShow:true })
    })
  }
})
