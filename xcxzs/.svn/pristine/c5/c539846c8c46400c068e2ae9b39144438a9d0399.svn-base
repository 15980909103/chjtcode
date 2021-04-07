var util = require("@/utils/util.js");

//选项的数据
const data=[
  {key:'scenic', name: '景区管理'},
  {key:'interest', name: '好玩'},
  {key:'discover', name: '发现'},
  {key:'service', name: '服务'},
  {key:'news', name: '资讯'},
]
//根据key获取对应名称
function getCheckedsName(key){
  var arr = data
  var keys = util.ArrayFun.column(arr,'key')
  
  if(key=='all'){
    return '全部'
  }else if(key){
    var i= util.ArrayFun.indexOfArr(keys,key)
    return data[i]? data[i].name:''
  }
}


export default {
  data,
  getCheckedsName : getCheckedsName
}