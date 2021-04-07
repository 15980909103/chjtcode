var is_allchecked=false;
const app = getApp();
var page=1;
var _buildingName = ""; //搜索内容
Page({
  /**
   * 页面的初始数据
   */
  data: {
    domain_name: app.data.domain_name,
    flag: 0,
    scrollPosition: {
      scrollY: true
    },
    buildingInfo: [], //楼盘
    is_detail:false   //是否点击编辑
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    page=1;
    this.onGetDataInfo();
  },
  onGetData(){
    this.onGetDataInfo();
  },
  onGetDataInfo(is_init=false) {
    var _this = this;
    app.ajax("surroundingAjax/getBuildingCollection", { page: page, user_id: app.data.userInfo.id, buildingName: _buildingName }, function (res) {
      var data = res.data;
      if (data.success){
        page++;
        if (is_init) {
          _this.setData({ buildingInfo: _this.addCustomAttr(data.buildingInfo) })
        } else {
          var buildingInfo = _this.data.buildingInfo.concat(data.buildingInfo);
          _this.setData({ buildingInfo: _this.addCustomAttr(buildingInfo) })
        }
      }
    })
  },
  /**
   * 搜索框确认事件
   */
  onSearchInput: function (e) {
    _buildingName = e.detail.value;
    page = 1;
    this.onGetDataInfo(true);
  },
  onDel(event) {  //删除事件
    var index = event.currentTarget.dataset.index;
    var id = event.currentTarget.dataset.id;
    var _this=this;
    var buildingInfo = _this.data.buildingInfo;
    buildingInfo.splice(index, 1);
    //楼盘收藏全部删除事件
    app.ajax("surroundingAjax/collectionAllDel", { collection_ids: id, user_id: app.data.userInfo.id }, function (res) {
      var data = res.data;
      if (data.success) {
        _this.setData({ buildingInfo: buildingInfo})
      } else {
        wx.showToast({ title: '删除失败！', icon: 'none' })
      }
    })
  },
  //编辑事件
  onEditBtn(){
    this.setData({ is_detail: !this.data.is_detail})
  },
  //编辑点击选项事件
  onEditChange(event){
    var index = event.currentTarget.dataset.index;
    var buildingInfo = this.data.buildingInfo;
    buildingInfo[index].is_checked = !buildingInfo[index].is_checked;
    this.setData({ buildingInfo: buildingInfo})
  },
  //全选事件
  onAllChecked(){
    var buildingInfo = this.data.buildingInfo;
    for (var i in buildingInfo){
      if (is_allchecked)
        buildingInfo[i].is_checked = false;
      else
        buildingInfo[i].is_checked = true;
    }
    if (is_allchecked)
      is_allchecked = false;
    else
      is_allchecked = true;
    this.setData({ buildingInfo: buildingInfo })
  },
  //删除事件
  onDelete(){
    var _this=this;
    wx.showModal({
      title: '提示',
      content: '确认删除',
      success(res) {
        if (res.confirm) {
          var _ids = "";
          var buildingInfo = _this.data.buildingInfo;
          var newBuildingInfo = [];
          for (var i in buildingInfo) {
            if (buildingInfo[i].is_checked) {
              _ids += "," + buildingInfo[i].collection_id;
            } else {
              newBuildingInfo.push(buildingInfo[i]);
            }
          }
          if (_ids == "") {
            wx.showToast({ title: '请选择要删除的楼盘', icon: 'none' })
            return false;
          }
          //楼盘收藏全部删除事件
          app.ajax("surroundingAjax/collectionAllDel", { collection_ids: _ids, user_id: app.data.userInfo.id }, function (res) {
            var data = res.data;
            if (data.success) {
              _this.setData({ buildingInfo: newBuildingInfo,is_detail: !_this.data.is_detail })
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
          var buildingInfo = that.data.buildingInfo;
          for (var i = 0; i < buildingInfo.length; i++) {
            buildingInfo[i].show_options = false;
          }
          buildingInfo[index].show_options = true;
          that.setData({
            buildingInfo: buildingInfo
          });
        }
        this.setScrollY(true);
      } else if (tx > 70 && (ty <= 40 && ty >= -40)) {
        //"向右滑动";
        that.data.flag = 2;
        if (that.data.swiper_index != undefined) {
          var index = that.data.swiper_index;
          var buildingInfo = that.data.buildingInfo;
          buildingInfo[index].show_options = false;
          that.setData({
            buildingInfo: buildingInfo
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
          var buildingInfo = that.data.buildingInfo;
          buildingInfo[index].show_options = false;
          that.setData({ buildingInfo: buildingInfo });
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
    var buildingInfo = this.data.buildingInfo;
    for (var i in buildingInfo) {
      buildingInfo[i].show_options = false;
    }
    this.setData({ buildingInfo: buildingInfo });
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
    var buildingInfo = this.data.buildingInfo;
    if (buildingInfo[index].show_options == true) {
      buildingInfo[index].show_options = false;
      this.setData({ buildingInfo: buildingInfo });
    } else {
      this.setShowDeletF();
      wx.navigateTo({
        url: '/pages/store/house_detail/house_detail?id='+id+"&agent_id="+agent_id
      })
    }
  }
})