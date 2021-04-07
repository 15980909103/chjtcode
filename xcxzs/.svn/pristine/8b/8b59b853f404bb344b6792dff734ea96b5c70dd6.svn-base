var is_allchecked = false;
var is_allchecked2 = false;
const app = getApp();
var page1=1,page2=1;
var _searchName1 = ""; //楼盘搜索内容
var _searchName2 = ""; //文章搜索内容
Page({
  /**
   * 页面的初始数据
   */
  data: {
    domain_name: app.data.domain_name,
    active:0, //选中的下表
    flag: 0,
    scrollPosition: {
      scrollY: true
    },
    list: [], //楼盘
    is_detail: false,   //是否点击编辑
    flag2: 0,
    scrollPosition2: {
      scrollY: true
    },
    list2: [], //文章
    is_detail2: false   //是否点击编辑文章
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    page1 = 1, page2 = 1;
    this.onGetBuildingInfo();
    this.onGetArticleInfo();
  },
  //楼盘数据获取
  onGetBuilding(){
    this.onGetBuildingInfo();
  },
  onGetBuildingInfo(is_init = false) {
    var _this = this;
    app.ajax("surroundingAjax/getBuildingRecord", { page: page1, user_id: app.data.userInfo.id, searchName: _searchName1 }, function (res) {
      var data = res.data;
      if (data.success) {
        page1++;
        if (is_init) {
          _this.setData({ list: _this.addCustomAttr(data.buildingInfo) })
        } else {
          var list = _this.data.list.concat(data.buildingInfo);
          _this.setData({ list: _this.addCustomAttr(list) })
        }
      }
    })
  },
  //文章数据获取
  onGetArticle() {
    this.onGetArticleInfo();
  },
  onGetArticleInfo(is_init = false) {
    var _this = this;
    app.ajax("surroundingAjax/getArticleRecord", { page: page2, user_id: app.data.userInfo.id, searchName: _searchName2 }, function (res) {
      var data = res.data;
      if (data.success) {
        page2++;
        if (is_init) {
          _this.setData({ list2: _this.addCustomAttr(data.articleInfo) })
        } else {
          var list2 = _this.data.list2.concat(data.articleInfo);
          _this.setData({ list2: _this.addCustomAttr(list2) })
        }
      }
    })
  },
  /**
   * 搜索框输入事件
   */
  onSearchInput: function (e) {
    if (this.data.active==0){
      _searchName1 = e.detail.value;
      page1 = 1;
      this.onGetBuildingInfo(true);
    }else{
      _searchName2 = e.detail.value;
      page2 = 1;
      this.onGetArticleInfo(true);
    }
  },
  //导航页切换事件
  onChange(e){
    this.setData({ active: e.detail.index })
  },
  onDel(event) {  //删除事件
    var index = event.currentTarget.dataset.index;
    var id = event.currentTarget.dataset.id;
    var _this = this;
    var list = _this.data.list;
    list.splice(index, 1);
    //楼盘收藏全部删除事件
    app.ajax("surroundingAjax/historyAllDel", { history_ids: id, user_id: app.data.userInfo.id }, function (res) {
      var data = res.data;
      if (data.success) {
        _this.setData({ list: list })
      } else {
        wx.showToast({ title: '删除失败！', icon: 'none' })
      }
    })
  },
  //编辑事件
  onEditBtn() {
    this.setData({ is_detail: !this.data.is_detail })
  },
  //编辑点击选项事件
  onEditChange(event) {
    var index = event.currentTarget.dataset.index;
    var list = this.data.list;
    list[index].is_checked = !list[index].is_checked;
    this.setData({ list: list })
  },
  //全选事件
  onAllChecked() {
    var list = this.data.list;
    for (var i in list) {
      if (is_allchecked)
        list[i].is_checked = false;
      else
        list[i].is_checked = true;
    }
    if (is_allchecked)
      is_allchecked = false;
    else
      is_allchecked = true;
    this.setData({ list: list })
  },
  //删除事件
  onDelete() {
    var _this = this;
    wx.showModal({
      title: '提示',
      content: '确认删除',
      success(res) {
        if (res.confirm) {
          var _ids = "";
          var list = _this.data.list;
          var newlist = [];
          for (var i in list) {
            if (list[i].is_checked) {
              _ids += "," + list[i].history_id;
            } else {
              newlist.push(list[i]);
            }
          }
          if (_ids == "") {
            wx.showToast({ title: '请选择要删除的楼盘', icon: 'none' })
            return false;
          }
          //楼盘收藏全部删除事件
          app.ajax("surroundingAjax/historyAllDel", { history_ids: _ids, user_id: app.data.userInfo.id }, function (res) {
            var data = res.data;
            if (data.success) {
              _this.setData({ list: newlist, is_detail: !_this.data.is_detail })
            } else {
              wx.showToast({ title: '删除失败！', icon: 'none' })
            }
          })
        }
      }
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
      items[i].is_checked = false;
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
  onDetailCard(event) {  //跳转名片详情
    var index = event.currentTarget.dataset.index;
    var id = event.currentTarget.dataset.id;
    var agent_id = event.currentTarget.dataset.agent_id;
    var list = this.data.list;
    if (list[index].show_options == true) {
      list[index].show_options = false;
      this.setData({ list: list });
    } else {
      this.setShowDeletF();
      wx.navigateTo({
        url: '/pages/store/house_detail/house_detail?id=' + id + "&agent_id=" + agent_id
      })
    }
  },
  /* ================= 文章 ================ */
  onDel2(event) {  //删除事件
    var index = event.currentTarget.dataset.index;
    var id = event.currentTarget.dataset.id;
    var _this = this;
    var list2 = _this.data.list2;
    list2.splice(index, 1);
    //楼盘收藏全部删除事件
    app.ajax("surroundingAjax/historyAllDel", { history_ids: id, user_id: app.data.userInfo.id }, function (res) {
      var data = res.data;
      if (data.success) {
        _this.setData({ list2: list2 })
      } else {
        wx.showToast({ title: '删除失败！', icon: 'none' })
      }
    })
  },
  //编辑事件
  onEditBtn2() {
    this.setData({ is_detail2: !this.data.is_detail2 })
  },
  //编辑点击选项事件
  onEditChange2(event) {
    var index = event.currentTarget.dataset.index;
    var list = this.data.list2;
    list[index].is_checked = !list[index].is_checked;
    this.setData({ list2: list })
  },
  //全选事件
  onAllChecked2() {
    var list = this.data.list2;
    for (var i in list) {
      if (is_allchecked2)
        list[i].is_checked = false;
      else
        list[i].is_checked = true;
    }
    if (is_allchecked2)
      is_allchecked2 = false;
    else
      is_allchecked2 = true;
    this.setData({ list2: list })
  },
  //删除事件
  onDelete2() {
    var _this = this;
    wx.showModal({
      title: '提示',
      content: '确认删除',
      success(res) {
        if (res.confirm) {
          var _ids = "";
          var list2 = _this.data.list2;
          var newlist2 = [];
          for (var i in list2) {
            if (list2[i].is_checked) {
              _ids += "," + list2[i].history_id;
            } else {
              newlist2.push(list2[i]);
            }
          }
          if (_ids == "") {
            wx.showToast({ title: '请选择要删除的楼盘', icon: 'none' })
            return false;
          }
          //楼盘收藏全部删除事件
          app.ajax("surroundingAjax/historyAllDel", { history_ids: _ids, user_id: app.data.userInfo.id }, function (res) {
            var data = res.data;
            if (data.success) {
              _this.setData({ list2: newlist2, is_detail: !_this.data.is_detail })
            } else {
              wx.showToast({ title: '删除失败！', icon: 'none' })
            }
          })
        }
      }
    })
  },
  //处理左滑显示事件
  handletouchstart2: function (event) {
    this.data.lastX = event.touches[0].pageX;
    this.data.lastY = event.touches[0].pageY;
    if (event.currentTarget.dataset.index != undefined) {
      this.data.swiper_index = event.currentTarget.dataset.index;
    }
  },
  handletouchmove2: function (event) {
    var that = this;
    if (that.data.flag2 !== 0) {
      return false;
    }
    var currentX = event.touches[0].pageX;
    var currentY = event.touches[0].pageY;
    var tx = currentX - that.data.lastX;
    var ty = currentY - that.data.lastY;
    if (Math.abs(tx) > Math.abs(ty)) {
      that.setScrollY2(false);
      if (tx < -70 && (ty <= 40 && ty >= -40)) {
        //"向左滑动";
        that.data.flag2 = 1;
        if (that.data.swiper_index != undefined) {
          var index = that.data.swiper_index;
          var list = that.data.list2;
          for (var i = 0; i < list.length; i++) {
            list[i].show_options = false;
          }
          list[index].show_options = true;
          that.setData({
            list2: list
          });
        }
        this.setScrollY2(true);
      } else if (tx > 70 && (ty <= 40 && ty >= -40)) {
        //"向右滑动";
        that.data.flag2 = 2;
        if (that.data.swiper_index != undefined) {
          var index = that.data.swiper_index;
          var list = that.data.list2;
          list[index].show_options = false;
          that.setData({
            list2: list
          });
        }
        this.setScrollY2(true);
      }
    }
  },
  handletouchend2: function (event) {
    this.data.flag2 = 0;
    this.setScrollY2(true);
  },
  //处理右滑关闭选项
  opstart2(event) {
    this.data.lastX = event.touches[0].pageX;
    this.data.lastY = event.touches[0].pageY;
    if (event.currentTarget.dataset.index != undefined) {
      this.data.swiper_index = event.currentTarget.dataset.index;
    }
  },
  opmove2(event) {
    var that = this;
    var currentX = event.touches[0].pageX;
    var currentY = event.touches[0].pageY;
    var tx = currentX - that.data.lastX;
    var ty = currentY - that.data.lastY;
    if (Math.abs(tx) > Math.abs(ty)) {
      that.setScrollY2(false);
      if (tx > 70 && (ty <= 40 && ty >= -40)) {
        //"向右滑动";
        if (that.data.swiper_index != undefined) {
          var index = that.data.swiper_index;
          var list = that.data.list2;
          list[index].show_options = false;
          that.setData({ list2: list });
        }
        this.setScrollY2(true);
      }
      this.setScrollY2(true);
    }
  },
  opend2(event) {
    this.setScrollY2(true);
  },
  //关闭所有选项信息
  setShowDeletF2() {
    var list = this.data.list2;
    for (var i in list) {
      list[i].show_options = false;
    }
    this.setData({ list2: list });
  },
  setScrollY2(isRes) {
    var scrollPosition = this.data.scrollPosition2;
    scrollPosition.scrollY = isRes;
    this.setData({
      scrollPosition2: scrollPosition
    })
  },
  onDetailCard2(event) {  //跳转名片详情
    var index = event.currentTarget.dataset.index;
    var id = event.currentTarget.dataset.id;
    var agent_id = event.currentTarget.dataset.agent_id;
    var list = this.data.list2;
    if (list[index].show_options == true) {
      list[index].show_options = false;
      this.setData({ list2: list });
    } else {
      this.setShowDeletF2();
      if (agent_id=="0")
        wx.navigateTo({ url: '/pages/index/article_detail/article_detail?id=' + id })
      else
        wx.navigateTo({ url: '/pages/index/article_detail/article_detail?id=' + id + '&agent_id=' + agent_id })
    }
  }
})