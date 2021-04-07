const app = getApp()
var _id=0;
var page=1;
Page({

  /**
   * 页面的初始数据
   */
  data: {
    comments: []    //评论数组
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    page = 1;
    _id = options.id;
    this.ajaxData();
  }, 
  //ajax获取数据
  ajaxData(){
    var _this = this;
    app.ajax("articleAjax/getComments", { aid: _id, page: page }, function (res) {
      var data = res.data;
      if (data.success) {
        var comments = _this.data.comments;
        comments = comments.concat(data.data);
        _this.setData({ comments: comments })
        page++;
      }
    })
  }
})