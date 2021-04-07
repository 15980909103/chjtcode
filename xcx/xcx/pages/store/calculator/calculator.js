var wxCharts = require('../../../dist/wxcharts.js');
var ringChart = null;
Page({
  /**
   * 页面的初始数据
   */
  data: {
    is_options: false,   //选项弹窗
    optionsIndex: '0',    //选择的选项下标
    optionsTitle:'',    //选项标题
    is_result:false,   //结果弹窗
    custom:'0',  //自定义临时数据
    constData:{'dknx':[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25]}, //固定数据
    results: {1: ['按贷款总额', '0', '25', '3.25'], 2: ['按贷款总额', '0', '25', '4.9'], 3: ['按贷款总额', '0', '3.25', '0','4.9','25']},
    resTitle:"",   //计算结果标题
    resArr: {  //计算结果集
      'dkze': 0, //贷款总额
      'hkze': 0,  //还款总额
      'dkys': 0,  //贷款月数
      'zflx': 0,  //支付利息
      'yjhk': 0  //月平还款
    }
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var _this=this;
    ringChart = new wxCharts({
      animation: true,
      canvasId: 'ringCanvas',
      type: 'ring',
      extra: {
        ringWidth: 30,
        pie: {
          offsetAngle: 180
        }
      },
      series: [{
        name: '贷款总额',
        data: _this.data.resArr.dkze,
        color:'#DD3032'
      }, {
        name: '支付利息',
          data: _this.data.resArr.hkze-_this.data.resArr.dkze,
        color: '#FFA05C'
      }],
      disablePieStroke: false,
      width: 150,
      height: 150,
      dataLabel: false,
      legend: false,
      background: '#f5f5f5',
      padding: 0
    });
  },
  //设置输入框值
  setInput(event){
    var index = event.currentTarget.dataset.index;
    index = index.split("");
    var results = this.data.results;
    results[index[0]][index[1]] = event.detail.value;
    this.setData({ results: results })
  },
  focusInput(event){
    if(event.detail.value == "0" || event.detail.value == 0){
      var index = event.currentTarget.dataset.index;
      index = index.split("");
      var results = this.data.results;
      results[index[0]][index[1]] = "";
      this.setData({ results: results})
    }
  },
  blurInput(event){
    if (event.detail.value == "") {
      var index = event.currentTarget.dataset.index;
      index = index.split("");
      var results = this.data.results;
      results[index[0]][index[1]] = '0';
      this.setData({ results: results })
    }
  },
  //关闭弹出框
  onClose(event) {
    if (event.currentTarget.dataset.tag =='options'){
      this.setData({ is_options: false });
    }else{
      this.setData({ is_result: false });
    }
  },
  //显示弹出框
  showOptions(event){
    this.setData({ optionsIndex: event.currentTarget.dataset.index, optionsTitle: event.currentTarget.dataset.title,is_options:true})
  },
  //设置临时input值
  setCustom(event){
    var value = event.detail.value;
    this.setData({ custom: value})
  },
  //设置选项框值
  setResults(event){
    var index = event.currentTarget.dataset.index;
    index = index.split("");
    var val = event.currentTarget.dataset.val;
    var results = this.data.results;
    results[index[0]][index[1]] = val;
    this.setData({ results: results,is_options: false });
  },
  //计算事件
  onSubmit(event){
    var index = event.currentTarget.dataset.index;
    var resArr={};
    var resTitle="";
    var regstr = /^\d+(\.\d+)?$/;
    if (index=='1'){
      resTitle='公积金贷款结果'
      var result = this.data.results[1];
      var loanAmount = result[1]*10000;
      if (!regstr.test(loanAmount) || loanAmount == 0) {
        wx.showToast({title: '请输入正确的贷款总额',icon: 'none'})
        return false;
      }
      var rate = parseFloat(result[3])/12/100;
      var periods = parseFloat(result[2])*12;
      var hkze = this.bxRepayment(loanAmount, rate, periods);
      var yjhk = this.bxAlgorithm(loanAmount, rate, periods);
      var zflx = (hkze - loanAmount).toFixed(2);
      resArr = { 'dkze': loanAmount, 'hkze': hkze, 'dkys': periods, 'yjhk': yjhk, 'zflx': zflx}
    } else if (index == '2'){
      resTitle = '商业贷款结果'
      var result = this.data.results[2];
      var loanAmount = result[1] * 10000;
      if (!regstr.test(loanAmount) || loanAmount == 0) {
        wx.showToast({ title: '请输入正确的贷款总额', icon: 'none' })
        return false;
      }
      var rate = parseFloat(result[3]) / 12 / 100;
      var periods = parseFloat(result[2]) * 12;
      var hkze = this.bxRepayment(loanAmount, rate, periods);
      var yjhk = this.bxAlgorithm(loanAmount, rate, periods);
      var zflx = (hkze - loanAmount).toFixed(2);
      resArr = { 'dkze': loanAmount, 'hkze': hkze, 'dkys': periods, 'yjhk': yjhk, 'zflx': zflx }
    }else{
      resTitle = '组合贷款结果'
      var result = this.data.results[3];
      var gj = result[1] * 10000;
      if (!regstr.test(gj) || gj == 0) {
        wx.showToast({ title: '请输入正确的公积金贷款金额', icon: 'none' })
        return false;
      }
      var sy = result[3] * 10000;
      if (!regstr.test(sy) || sy == 0) {
        wx.showToast({ title: '请输入正确的商业贷款金额', icon: 'none' })
        return false;
      }
      var loanAmount = gj + sy;
      var gjrate = parseFloat(result[2]) / 12 / 100;
      var syrate = parseFloat(result[4]) / 12 / 100;
      var periods = parseFloat(result[5]) * 12;

      var gjhkze = this.bxRepayment(gj, gjrate, periods);
      var syhkze = this.bxRepayment(sy, syrate, periods);
      var hkze = parseFloat(gjhkze) + parseFloat(syhkze);

      var gjyjhk = this.bxAlgorithm(gj, gjrate, periods);
      var syyjhk = this.bxAlgorithm(sy, syrate, periods);
      var yjhk = parseFloat(gjyjhk) + parseFloat(syyjhk);
      var zflx = (hkze - loanAmount).toFixed(2);
      resArr = { 'dkze': loanAmount, 'hkze': hkze, 'dkys': periods, 'yjhk': yjhk, 'zflx': zflx }
    }
    this.setData({ is_result: true, resTitle: resTitle, resArr: resArr});
    ringChart.updateData({
      series: [{ name: '贷款总额', data: resArr.dkze, color: '#DD3032' }, { name: '支付利息', data: resArr.hkze-resArr.dkze,color: '#FFA05C'}]
    });
  },
  //每期还款额－等额本息算法
  //amount本金,rate月利率,periods还款月数
  bxAlgorithm(amount, rate, periods){
    var temp = Math.pow((1 + rate), periods);
    return (amount * rate * temp / (temp - 1)).toFixed(2);
  },
  //还款总额－等额本息算法
  //amount本金,rate月利率,periods还款月数
  bxRepayment(amount, rate, periods) {
    var agvAmount = this.bxAlgorithm(amount, rate, periods);
    return (agvAmount * periods).toFixed(2);
  }
})