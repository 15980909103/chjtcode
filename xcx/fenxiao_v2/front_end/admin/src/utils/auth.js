/*token使用cookie存储
import Cookies from 'js-cookie'
const TokenKey = 'vue_admin_template_token'
 export function getToken() {
  return Cookies.get(TokenKey)
}
export function setToken(token) {
  return Cookies.set(TokenKey, token)
}
export function removeToken() {
  return Cookies.remove(TokenKey)
} */

import myStorage from '../utils/modules/storage'
const TokenKey = 'footpath_token'
export function getToken() {
  return myStorage.local.get(TokenKey)
}
export function setToken(token) {
  return myStorage.local.set(TokenKey,token,60*60*24)
}
export function removeToken() {
  return myStorage.local.del(TokenKey)
}

const SidKey = 'footpath_sid' //sid设置
export function setSid(sid){
  return myStorage.local.set(SidKey,sid,60*60*24)
}
export function getSid() {
  return myStorage.local.get(SidKey)
}
export function removeSid() {
  return myStorage.local.del(SidKey)
}

const fileHost = 'fileHost' //图片和音频Host
export function setFileHost(val){
  val = JSON.stringify(val);
  return myStorage.local.set(fileHost,val,60*60*24)
}
export function getFileHost(type='') {
  let val = myStorage.local.get(fileHost);
  let hostList = val?JSON.parse(val):[];
  return type ? hostList[type] : hostList;
}
export function removeFileHost() {
  return myStorage.local.del(fileHost)
}



//动态菜单加载
import router from '@/router'
export const initMenu = {
  setMenuArrIdx:function(menuArr,parent_arr){
      for(var i in menuArr){
          var item=menuArr[i]
            var pid=item.parent_id
            if(pid&&parent_arr['_idx']){
              item['_idx']=parent_arr['_idx']+1
            }else{
              item['_idx']=1
            }

          if(item.children){
            this.setMenuArrIdx(item.children,item)
          }
      }
      return menuArr
  },
  setInit: function (rsMenuArr) {
    var constantRouterMap=router.options.routes//本地初始设置的页面
    var menuArr={} //最终合并后的整体router
    if (Object.keys(rsMenuArr).length > 0) { //加载下发的动态菜单
      rsMenuArr = this.setMenuArrIdx(rsMenuArr,rsMenuArr)//标识菜单层级
      rsMenuArr = this.formatRoutes(rsMenuArr,false)
      //console.log('======',rsMenuArr)

    }else{
      rsMenuArr=[]
    }

    if(rsMenuArr.length>0){
      //添加/主页跳转，默认重定向第一个菜单
      var dashboard = { path: '/', redirect: rsMenuArr[0].path,component: (resolve) => {require(['@/views/layout/Layout'],resolve)} , hidden: true }
      rsMenuArr.unshift(dashboard)
    }

    if(rsMenuArr.length==0){
      //未获取任何菜单时重新登陆
      store.dispatch('FedLoginOut')
    }
    // 最后添加404页面
    let unfound = { path: '*', redirect: '/404', hidden: true }
    rsMenuArr.push(unfound)
    var menuArr=constantRouterMap.concat(rsMenuArr);//进行合并
    //addroutes添加新的动态router
    router.addRoutes(rsMenuArr)

    return {
      rsMenuArr:rsMenuArr,//服务器返回的动态router
      constantRouterMap:constantRouterMap,//本地初始初始设置router
      menuArr:menuArr//最终合并后的整体router
    };
  },
  //路由格式化
  formatRoutes: (aMenu,ischild=false) => {
    ///========数据初始化 start===========////
    let aRouter = []
    let child_key ='children'//代表下级的key
    let pageurl_key ='page'//页面路劲的key
    let pagehide={
      hidekey  : 'status',//代表页面隐藏的key
      value: 0 //代表页面隐藏时的val
    }
    let btnurl_key='url'//代表页面中按钮点击请求url的key,该层级为当前页面层级的下级，类型为接口隐藏页

    /**
     * 设置meta选项
     * @param {*} e
     */
    let setMetafun=function(e){
      /*e= item=aMenu[key]
      *return {title: item.title,icon: item.icontag?item.icontag:'',id: item.id?item.id:'',pid:item.pid?item.pid:''}
      */
      //遍历当前页面层级的下级,获取该页面可点击按钮结果集
      function getPageShowBtns(e){
        var rs=[]
        if(e.meta){
          //手动调整为下级时直接返回已设置
          return e.meta.pageShowBtns; 
        }
        var childs = e[child_key]
        if(childs&&Object.keys(childs).length>0){
          for(var i in childs){
            var child=childs[i]
            //存储下级为接口类型的，用于判断内页是否显示相应按钮
            if(child.btn_show==1){
              rs.push({url:child[btnurl_key],page:child[pageurl_key],type:child.type})
            }
          }
        }
        
        return rs
      }
      let active_path = (e.status=='0'&&e.active_page) ? e.active_page : e.page;
      if(active_path.indexOf('/')!='0'){//是否是以/开头
        active_path='/'+active_path;
      }
      return {
        title        : e.name,
        active_path  : active_path,
        icon         : e.icon,
        id           : e.id,
        pid          : e.parent_id,
        pageShowBtns : getPageShowBtns(e),//当前页面可显示的点击按钮
        _idx         : e._idx,
        pending_disposal: e.pending_disposal,
      }
    }
    ///========数据初始化 end===========//////

    for (let key in aMenu) {
          var item=aMenu[key]
          if(ischild==true){//判断从下级进来
            /* if(item[btnurl_key]&&!item[pageurl_key]&&item[pagehide.hidekey]==pagehide.value){//只是单纯点击请求url用的（没有显示没有打开页面）
              continue;
            } */
            //判断为接口类型不进行格式路由操作
            if(item['type']==2){
              continue;
            }
          }

          if(!item[pageurl_key]){
            item[pageurl_key]='';
          }
          let path=''
          if(item[pageurl_key].indexOf('http')!=-1){
            //判断是否是外链
            path=item[pageurl_key];
          }else{
            if(item[pageurl_key].indexOf('/')=='0'){//是否是以/开头
              path=item[pageurl_key];
            }else{
              path='/'+item[pageurl_key];
            }
            if(path=='/'){//针对当前页面item[pageurl_key]没有填写时读取子元素补全
              path = '/menu_'+item['id']
              //console.log(item['name'])
              // var childs_arr=item[child_key]
              // if(childs_arr&&Object.keys(childs_arr).length>0){
              //   var onechild_key=Object.keys(childs_arr)[0]
              //   var onechild=childs_arr[onechild_key]
              //   path=path+onechild[pageurl_key].split('/')[0]
              // }
            }
          }

          item.meta = setMetafun(item)//设置meta

          //为第一层页面的菜单，当有设置page时,将数据移动到子数据//
          if(ischild!=true&&item[pageurl_key]){
            var new_childarr=[];
            var nweitem=Object.assign({},item) 
            
            for(var i in  nweitem[child_key]){
              new_childarr.push(nweitem[child_key][i])
            }
            nweitem._idx = Number(nweitem._idx)+1
            nweitem.meta = nweitem.meta
            delete nweitem[child_key];
            item[child_key]=[nweitem,...new_childarr];
            
            //console.log(newitem_new_childarr,item[child_key])
          }

          //层级2时且2有内页index，将该index页移到下级 代码不可删，用于多层页面时
          /* if(item._idx>=2&&item[child_key]&&Object.keys(item[child_key]).length>0&&!item._isbulid){
            var path_arr=path.split('/')
            var path_arr_length=path_arr.length
            if(path_arr[path_arr_length-1]=='index'){
              path_arr[path_arr_length-1]=path_arr[path_arr_length-2]
              path=path_arr.join('/')//newpath

              var nweitem=Object.assign({},item)
              delete nweitem[child_key]
              nweitem._idx=nweitem._idx+1
              nweitem._isbulid=1
              var obj={}
              obj[nweitem.id]=nweitem
              item[child_key]=Object.assign({},item[child_key],obj)
            }
          } */

          let ishide=false//菜单是否显示
          if(item[pagehide.hidekey]==pagehide.value){
            ishide=true;
          }

          //console.log(path,item.name,item._idx)
          let oRouter ={
            path: path,
            hidden: ishide,
            //meta:{title: item.title,icon: item.icontag?item.icontag:'',id: item.id?item.id:'',pid:item.pid?item.pid:''},
            meta: item.meta,
            //component(resolve){}
            //name: path.replace(/\//g,''),
          }
          //针对外链时不用component
          if(item[pageurl_key].indexOf('http')==-1){
            oRouter.component=(resolve)=> {
              let componentPath='views'+path
              if(ischild!=true){
                componentPath= 'Layout';//针对侧边栏一级菜单时固定调用Layout
              }
              if (componentPath == 'Layout') {
                require(['../views/layout/Layout'], resolve)
              }else{
                require([`../${componentPath}.vue`], resolve)
              }
            }
          }

          //只作为菜单显示时设置默认重定向到第一个子菜单
          if(item.type==0){
            if(item[child_key]&&item[child_key][0]){
              let redirect=item[child_key][0][pageurl_key]
              oRouter.redirect = redirect
            }
          }

          //循环操作子数据
          let childrenArr = []
          let child = item[child_key]
          if(child&&Object.keys(child).length>0){
              childrenArr=initMenu.formatRoutes(child,true)
              oRouter.children= childrenArr
          }

          //console.log('111111',oRouter)
          aRouter.push(oRouter)
    }

    //console.log(aRouter)
    return aRouter
  }
}

