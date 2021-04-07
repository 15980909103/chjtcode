const app = getApp(), loadingTime = 500;
var isFirst = [true];
Page({
  data: {
    domain_name: app.data.domain_name,
    domain_img: app.data.domain_img,
    imgUrls: [],
    channel: [], //频道列表
    current: 0, //当前频道
    scrollInto: 'current0', //频道栏切换
    isLoadmore: [], //是否上拉加载
    scrollY: false,
    list:[]
  },
  onLoad: function () {
    var _this=this;
    this.fetchPageHeader();
  },
  fetchPageHeader() {
      app.ajax('articleAjax/getArticleHome',{},res => {
          const data = res.data;
          if (data.success){
            this.setData({
              imgUrls: data.data.figure,
              channel: data.data.column,
              list: data.data.column.map(() => ([]))
            });
            this.fetchArticleList();
          }
      });
  },
  fetchArticleList() {
    const target = this.data.list[this.data.current] || [];
    const page = parseInt(target? (target.length / 10): 0) + 1;
    //获取首页数据
    app.ajax("articleAjax/getDataInfo", {
      column: this.data.channel[this.data.current].id || null,
      page,
    }, res => {
      var data=res.data;
      if (data.success){
        this.setData({
          list: this.data.list.map((articleList, index) => {
              if (index == this.data.current) {
                return page == 1? data.data.article : target.concat(data.data.article)
              }
              return [];
          }),
          channel: this.data.channel.map((item, index) => {
              return {
                ...item,
                isEmpty: this.data.current == index && page == 1 && data.data.article.length == 0
              };
          }),
        });
      }
    })
  },
  /**
   * 最外层scroll滚动到顶部
   */
  scrollTop: function () {
    this.setData({
      scrollY: false
    });
  },

  /**
   * 最外层scroll滚动到底部
   */
  scrollBottom: function () {
    this.setData({
      scrollY: true
    });
  },
  /**
 * 频道切换
 */
  currentChange: function (e) {
    var current;
    if (e.type == 'change'){
      current = e.detail.current;
    }else{
      current = e.currentTarget.dataset.current;
    }
    var self = this;
    if (typeof (isFirst[current]) == "undefined") {
      isFirst[current] = true;
      wx.showLoading({
        title: '加载中',
      });
      setTimeout(function () {
        //首次渲染
        self.firstRendering(current, self.data.channel[current].id);
      }, loadingTime);
    }
    self.setData({
      current: current,
      scrollInto: 'current' + current
    });
    if (e.type == 'change') {
      this.fetchArticleList();
    }
  },
  //上拉加载数据
  getArticleData(event){
    var _this=this;
    var index = event.currentTarget.dataset.index;
    var cid = event.currentTarget.dataset.cid;
    var page = event.currentTarget.dataset.page;
    app.ajax("articleAjax/getArticle", { cid: cid, page: page},function(res){
      var data=res.data;
      if(data.success){
        //更新类目页数
        var channel = _this.data.channel;
        channel[index].page = data.page;
        //更新文章数据
        var list = _this.data.list;
        list[index] = list[index].concat(data.data);
        _this.setData({ channel: channel, list: list })
      }
    })
  },
  //首次渲染
  firstRendering: function (current, tid) {
    var self = this;
    wx.hideLoading();
  },
  //跳转文章详情
  onArticleDetail(event){
    var id = event.currentTarget.dataset.id;
    wx.navigateTo({ url: '/pages/index/article_detail/article_detail?id=' + id})
  }
})
