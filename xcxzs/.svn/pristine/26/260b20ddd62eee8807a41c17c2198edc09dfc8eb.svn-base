const app = getApp()
Page({
  /**
   * 页面的初始数据
   */
  data: {
    textareaText: "",  //文本框内容
    feedbackText: "",  //联系方式内容
    feedbackWidth:'25', //图片组宽度
    feedbackimg:[]   //反馈上传的图片路径
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setFeedbackWidth();
  },
  //设置图片组宽度
  setFeedbackWidth(){
    this.setData({ feedbackWidth: (this.data.feedbackimg.length+1)*25})
  },
  //添加照片
  onAddImage(){
    var _this=this;
    var feedbackimg = _this.data.feedbackimg;
    if (feedbackimg.length>=4){
      wx.showToast({
        title: '最多只能上传4张图片',
        icon: 'none'
      })
      return false;
    }
    wx.chooseImage({
      count: 4,
      success(res) {
        var tempFilePaths = res.tempFilePaths
        for (var i in tempFilePaths){
          for(var j=0;j<4;j++){
            if (feedbackimg[j] == undefined){
              feedbackimg.push({ url: tempFilePaths[i]});
              break;
            }
          }
        }
        _this.setData({ feedbackimg: feedbackimg})
        _this.setFeedbackWidth();
      }
    })
  },
  //删除图片事件
  onCloseImage(event){
    var _this=this;
    wx.showModal({
      title: '提示',
      content: '删除图片？',
      success(res) {
        if (res.confirm) {
          var index = event.currentTarget.dataset.index;
          var feedbackimg = _this.data.feedbackimg;
          feedbackimg.splice(index, 1);
          _this.setData({ feedbackimg: feedbackimg })
          _this.setFeedbackWidth();
        }
      }
    })
  },
  onInput(event) {
    this.setData({ textareaText: event.detail.value })
  },
  onFeedback(event){
    this.setData({ feedbackText: event.detail.value })
  },
  onSave(){
    var _this = this;
    var textareaText = _this.data.textareaText;
    var feedbackText = _this.data.feedbackText;
    if (textareaText==""){
      wx.showToast({title: '意见内容不能为空！',icon: 'none'})
      return false;
    }
    if (feedbackText == "") {
      wx.showToast({ title: '联系方式不能为空！', icon: 'none' })
      return false;
    }
    var parameter = { user_id: app.data.userInfo.id, ambitus_suggest: textareaText, contact_way: feedbackText, client_side_type: 1}
    app.ajax("surroundingAjax/addSuggest", parameter, function (res) {
      var data = res.data;
      if (data.success){
        var _id = data.id;
        var feedbackimg = _this.data.feedbackimg;
        //遍历图片上传
        for (var i in feedbackimg){
          wx.uploadFile({
            url: app.data.domain_name + "xcxapi/surroundingAjax/uploadSuggestImg",
            filePath: feedbackimg[i].url,
            name: 'file',
            formData: {
              id: _id
            }
          })
        }
        wx.showToast({ title: "提交成功！", icon: 'none' })
        _this.setData({ feedbackimg: [], textareaText: "", feedbackText:""})
        _this.setFeedbackWidth();
      }else{
        wx.showToast({ title: data.message, icon: 'none' })
      }
    })
  }
})