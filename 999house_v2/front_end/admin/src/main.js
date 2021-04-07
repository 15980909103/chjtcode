import Vue from 'vue'

import 'normalize.css/normalize.css' // A modern alternative to CSS resets

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import locale from 'element-ui/lib/locale/lang/zh-CN' // lang i18n

import '@/styles/index.scss' // global css

//import Chat from "jwChat";
// import 'jwchat/lin/JwcChat.css';
//Vue.use(Chat);

import App from './App'
import store from './store'
import router from './router'

import '@/icons' // icon
import '@/permission' // permission control
import echarts from 'echarts'
/**
 * This project originally used easy-mock to simulate data,
 * but its official service is very unstable,
 * and you can build your own service if you need it.
 * So here I use Mock.js for local emulation,
 * it will intercept your request, so you won't see the request in the network.
 * If you remove `../mock` it will automatically request easy-mock data.
 */
//import '../mock' // simulation data

Vue.prototype.setDataArr = function (obj) {
	Object.assign(this.$data,obj);
}
import baseconfig from '@/../baseconfig.js'
Vue.prototype.$baseconfig = baseconfig //基础配置
Vue.prototype.$echarts   = echarts;
Vue.prototype.webSocket = null;
Vue.prototype.$getRealImgUrl = function(url,k='imghost'){ //图片地址转换
  if(!url){
    return ''
  }
  if(url.indexOf('http')!='-1'){
    return url//显示
  }else if(url.indexOf('data:image')!='-1'){
    return url//本地显示
  }else{
    if(url.indexOf('/')>'0'){
      url+= '/'+url
    }
    return baseconfig[k] + url//显示
  }
}
Vue.prototype.$getRealVoiceUrl = function(url,k='imghost'){ //音频地址转换
  if(!url){
    return ''
  }
  if(url.indexOf('http')!='-1'){
    return url//显示
  }else{
    if(url.indexOf('/')>'0'){
      url= '/'+url
    }
    return baseconfig[k] + url//显示
  }
}


/**
 * 对象深拷贝
 */
function DeepCopy(object) {
  let resultObject = {};
  for (let obj in object) {
      if (typeof (object[obj]) == "object" && !Array.isArray(object[obj])) {
          let x = {}
          x[obj] = DeepCopy(object[obj])
          Object.assign(resultObject, x);
      } else {
          let x = {};
          x[obj] = object[obj];
          Object.assign(resultObject, x);
      }
  }
  return resultObject;
}
Vue.prototype.$DeepCopy= DeepCopy


Vue.directive('iscan', {
  // 按钮是否可以操作
  inserted: function (el,binding) {
    var val = binding.value
    var can = false
    for(var i in Vue.prototype.$pageShowBtns){
      var item = Vue.prototype.$pageShowBtns[i]
      var val2 = item.page

      if(val==val2){
        can =  true
      }
    }
    if(can==false){
      el.parentNode.removeChild(el);
    }
  }
})


let _urlData= {}
router.afterHooks.push( //增加路由变化事件,router.afterHooks
  function (currentRoute, preRoute) {
    if(Object.keys(currentRoute.params).length>0){
      _urlData = currentRoute.params
    }
    else if(Object.keys(currentRoute.query).length>0){
      _urlData = currentRoute.query
    }else{
      _urlData = {}
    }

    Vue.prototype.$pageShowBtns = currentRoute.meta.pageShowBtns
    //_urlData.$activeCity = store.getters.activeCity //设置当前操作城市
    Vue.prototype.$urlData = _urlData //获取页面传参的数据
  }
)
//无刷新修改url参数
import merge from 'webpack-merge';
Vue.prototype.$changeUrlData = function(val={},reset = 0){
  //替换所有参数：
  if(reset==1){
    router.push({ query:merge({},val)})
  }else{
    //修改原有参数
    router.push({ query:merge(Vue.prototype.$urlData,val) })
  }
}

Vue.use(ElementUI, { locale })

Vue.config.productionTip = false


//引入高德地图
import VueAMap from 'vue-amap'
Vue.use(VueAMap);
VueAMap.initAMapApiLoader({
　　　　　　// 高德的key
　　　　　　key: '53da8183ca23918f2f81c79a5b2ede15',
　　　　　　// 插件集合
　　　　　　plugin: ['AMap.Autocomplete', 'AMap.PlaceSearch', 'AMap.Scale', 'AMap.OverView', 'AMap.ToolBar', 'AMap.MapType', 'AMap.PolyEditor', 'AMap.CircleEditor']
　　　})


new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App)
})
