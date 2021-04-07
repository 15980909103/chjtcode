import axios from 'axios'
import { Message, MessageBox } from 'element-ui'
import store from '../store'
import { getToken, getSid } from '@/utils/auth'
import Qs from 'qs'//post data转换提交

var baseconfig = require('../../baseconfig.js')
var util=require('@/utils/util.js')

//请求时合并公共请求参数
function mergeCommonRequestData(data){
  var newdata= Object.assign({},data)
 
  newdata['nonce']=util.randomString(8)
  newdata['timestamp']=Date.parse(new Date())/1000
  newdata['sign']=util.makeSign(newdata)
  return newdata
}

axios.defaults.withCredentials=true//设置axios请求允许跨域
// 创建axios实例
const service = axios.create({
  //baseURL:'api/admin',
  baseURL: baseconfig.api_baseURL, // api 的 base_url //baseconfig.api_baseURL
  timeout: 5000 ,// 请求超时时间
  method: 'post',
  // `headers` 是即将被发送的自定义请求头
  headers: {"Content-Type": "application/x-www-form-urlencoded"},
  transformRequest: [function (data,headers) {
    //变更头部信息
    headers['XX-Api-Version']=1.0
    headers['XX-Device-Type']='pc'
    var token =store.getters.token
    if (!token) {
       token = getToken() // 让每个请求携带自定义token 请根据实际情况自行修改
    }
    if(token){
      headers['XX-Token'] =token
    }
    var sid=store.getters.sid
    if (!sid) {
       sid = getSid() 
    }
    if(sid){
      headers['XX-Sid'] =sid
    }
    //变更请求数据
    if(headers['Content-Type']=='multipart/form-data'){//为二进制文件时
        //针对文件上传时通过FormData转换数据
        var data2 = new FormData()
        data2['nonce']=util.randomString(8)
        data2['timestamp']=Date.parse(new Date())/1000
        for (const key of Object.keys(data)) {
          data2.append(key, data[key])
        }
  
    }else{
        if(!data){
          data={}
        }
        data = mergeCommonRequestData(data)//合并公共的请求数据 
        //console.log(data)
        //对data进行格式转换处理
        var data2 = Qs.stringify(data)
    }

    return data2
  }],
  withCredentials: true, //允许跨域请求
})

// request拦截器
service.interceptors.request.use(
  config => {

    //console.log(config)
    return config
  },
  error => {
    // Do something with request error
    console.log(error) // for debug
    Promise.reject(error)
  }
)

// response 拦截器
service.interceptors.response.use(
  response => {
    /**
     * code是状态代码号 可结合自己业务进行修改
     */
    const res = response.data

    // 50008:非法的token; 50012:其他客户端登录了;  50014:Token 过期了;
    if (res.code === 50008 || res.code === 50012 || res.code === 50014) {
      MessageBox.confirm(
        '你已被登出，可以取消继续留在该页面，或者重新登录',
        '确定登出',
        {
          confirmButtonText: '重新登录',
          type: 'warning',
          showCancelButton: false,
        }
      ).then(() => {
        store.dispatch('FedLoginOut').then(() => {
          location.reload() // 为了重新实例化vue-router对象 避免bug
        })
      })

      return Promise.reject('error')
    }else{
      return res
    }
    

    /* if (res.code !== 20000) {
      Message({
        message: res.message,
        type: 'error',
        duration: 5 * 1000
      })
    } else {
      return response.data
    } */
  },
  error => {
    console.log('err' + error) // for debug
    Message({
      message: error.message,
      type: 'error',
      duration: 5 * 1000
    })
    return Promise.reject(error)
  }
)

export default service
