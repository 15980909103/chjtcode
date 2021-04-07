const app = getApp();
var page=1;
Page({
  /**
   * 页面的初始数据
   */
  data: {
    domain_name: app.data.domain_name,
    list: []
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    page = 1;
    this.onGetDataInfo();
  },
  //ajax获取数据
  onGetDataInfo(){
    var _this = this;
    //获取头条新闻数据
    app.ajax("articleAjax/getHeadlinesArticle", {page: page }, function (res) {
      var data = res.data;
      if (data.success) {
        _this.setData({ list: _this.data.list.concat(data.data) });
        page++;
      }
    })
  },
  goto(event){
    let id = event.currentTarget.dataset.id
    // console.log(id)
    wx.navigateTo({
      url: '/pages/index/article_detail/article_detail?id=' + id,
    })
  }
})