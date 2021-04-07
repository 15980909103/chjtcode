//时间日期操作函数
const DataFun={
  /**
   * //php返回的时间戳转时间格式
   * @param {*} str 
   * @param {*} timetype 要转换的形式
   * @param {*} seperator1 间隔符
   */
  getFormatDate : function(str = '' ,timetype=1 , seperator1='-') {
    str=str*1000
    if (str) {
      var date = new Date(str);
    } else {
      var date = new Date();
    }
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    month = DataFun.checkAddZone(month)
    strDate = DataFun.checkAddZone(strDate)

    var currentdate = year + seperator1 + month + seperator1 + strDate;
    if(timetype == 2){
      var hour = date.getHours()
      hour = DataFun.checkAddZone(hour)
      var min = date.getMinutes()
      min = DataFun.checkAddZone(min)
      var se = date.getSeconds()
      se = DataFun.checkAddZone(se)
      currentdate=currentdate+' '+ hour+':'+min+':'+se
    }
    if(timetype == 3){
      var hour = date.getHours()
      hour = DataFun.checkAddZone(hour)
      var min = date.getMinutes()
      min = DataFun.checkAddZone(min)
      currentdate=currentdate+' '+hour+ ':'+min
    }
    return currentdate;
  },

  //格式化小于10的数字前面加0
  checkAddZone:function(num){
    return num< 10 ? '0' + num.toString() : num
  },
  
  //指定日期加减day天或者month
  dateAdd: function (startDate, day,month=0) {
    startDate = new Date(startDate);
    
    startDate = +startDate + 1000 * 86400 * day;
    startDate = new Date(startDate);
    if(month){
      startDate.setMonth(startDate.getMonth()+month);
    }
    
    var year = startDate.getFullYear();
    var month = startDate.getMonth() + 1;
    var strDate = startDate.getDate();
    if (month >= 1 && month <= 9) {
      month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
      strDate = "0" + strDate;
    }
    var nextStartDate = year + "-" + month + "-" + strDate;
    return nextStartDate;
  },
  
  //比较两个日期的大小
  firstLarge: function (date1,date2){
      var oDate1 = new Date(date1);
      var oDate2 = new Date(date2);
      if(oDate1.getTime() >= oDate2.getTime()){
        return true;
      } else {
        return false;
      }
  }

}

export default DataFun