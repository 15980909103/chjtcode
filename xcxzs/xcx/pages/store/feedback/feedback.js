const app = getApp()
var id=0;
Page({

  /**
   * 页面的初始数据
   */
  data: {
    textareaText:"",  //文本框内容
    problemIndex:0, //问题选中索引
    problemData: [{ title: '基本信息', value: '1' }, { title: '建筑信息', value: '2' }, { title: '物业参数', value: '3' }, { title: '配套信息', value: '4' }]   //问题数据
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    id=options.id;
  },
  //问题选择
  onProblem(event){
    this.setData({ problemIndex: event.currentTarget.dataset.index})
  },
  onInput(event){
    this.setData({ textareaText: event.detail.value})
  },
  onSave(){
    var _this = this;
    var textareaText = _this.data.textareaText;
    var matter_type = _this.data.problemData[_this.data.problemIndex].value;
    if (textareaText == "") {
      wx.showToast({title: '请输入意见反馈内容！',icon: 'none'})
      return false;
    }
    app.ajax('buildingAjax/addFeedback', { id: id, user_id: app.data.userInfo.id,matter_type: matter_type, building_correct_info: textareaText }, function (res) {
      if (res.data.success) {
        wx.showToast({ title: '保存成功！', icon: 'none' })
        _this.setData({ problemIndex: 0, textareaText:""})
      } else {
        wx.showToast({ title: '保存失败！', icon: 'none' })
      }
    });
  }
})