const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    canvasHidden:true,
    shareImgPath:"",
    bjImg:"/image/haibao.png",
    userInfo: {},
    qrCode: ""
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var _this=this;
    //获取经纪人名片信息
    //app.ajax("agentapi/userAjax/getCardData", { id:options.id }, function (res) {
      app.ajax("xcxapi/userAjax/getCardData", { id:options.id }, function (res) {
      var data = res.data;
      _this.setData({ userInfo: data.data.userInfo, qrCode: data.data.qrCode})
    }, null, false)
  },
  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  },
  //长按事件
  onLongtap(){
    var _this=this;
    wx.showModal({
      title: '',
      content: '保存至相册?',
      success(res) {
        if (res.confirm) {
          _this.drawImg();
        }
      }
    })
  },
  drawImg() {
    var windowWidth = 375;
    var windowHeight=603;
    // wx.getSystemInfo({
    //   success(res) {
    //     windowWidth = res.windowWidth;
    //     windowHeight = res.windowHeight;
    //   }
    // })
    var _this = this;
    var context = wx.createCanvasContext('share')  //这里的“share”是“canvas-id”

    context.setFillStyle('#fff')    //这里是绘制白底，让图片有白色的背景
    context.fillRect(0, 0, windowWidth, windowHeight)
    context.drawImage(_this.data.bjImg, 0, 0, windowWidth, windowHeight) //绘制背景图片
    //缓存头像
    wx.downloadFile({
      url: _this.data.userInfo.headimgurl, // 仅为示例，并非真实的资源
      success(res) {
        // 只要服务器有响应数据，就会把响应内容写入文件并进入 success 回调，业务需要自行判断是否下载到了想要的内容
        if (res.statusCode === 200) {
          //绘制头像图片
          context.drawImage(res.tempFilePath, 63, 123, 250, 215)
          //绘制姓名
          var nameLength = _this.data.userInfo.name.length;
          context.setFontSize(22)
          context.setFillStyle("#000000")
          if (nameLength==2)
            context.fillText(_this.data.userInfo.name, 165, 110)
          else if (nameLength == 3)
            context.fillText(_this.data.userInfo.name, 153, 110)
          else if (nameLength == 4)
            context.fillText(_this.data.userInfo.name, 144, 110)
          //绘制信息
          context.setFontSize(16)
          context.setFillStyle("#3D3D3D")
          context.fillText('Tel：'+_this.data.userInfo.phone, 63, 370)
          context.fillText('Col：' + _this.data.userInfo.storename, 63, 392)
          context.fillText('Add：' + _this.data.userInfo.city + _this.data.userInfo.area, 63, 415)
          //绘制二维码
          context.drawImage(_this.data.qrCode, 232, 308, 81, 81)
          //绘制提示信息
          context.setFontSize(11)
          context.setFillStyle("#999999")
          context.fillText("长按识别更多", 235, 415)
          // //绘制签名底色
          // context.setFillStyle('#8C74E6')
          // context.fillRect(70, 540, 235, 25)
          //绘制签名
          context.setFontSize(20)
          context.setFillStyle("#ffffff")
          context.fillText(_this.data.userInfo.signature, 80, 540)
          //绘画到canvas
          context.draw(true, function (res) {
            wx.canvasToTempFilePath({
              x: 0,
              y: 0,
              width: windowWidth,
              height: windowHeight,
              destWidth: windowWidth,
              destHeight: windowHeight,
              canvasId: 'share',
              success: function (a) {
                _this.setData({shareImgPath: a.tempFilePath})
                wx.saveImageToPhotosAlbum({
                  filePath: a.tempFilePath,
                  success(res) {
                    wx.showToast({title: '保存成功',icon: 'none'})
                  },
                  fail(){
                    wx.showToast({ title: '保存失败', icon: 'none' })
                  }
                })
              },
              fail: function (e) { console.log('失败') }
            })
          })
        }
      },
      fail() {
        wx.showToast({ title: '头像缓存失败', icon: 'none' })
      }
    })
  }
})
