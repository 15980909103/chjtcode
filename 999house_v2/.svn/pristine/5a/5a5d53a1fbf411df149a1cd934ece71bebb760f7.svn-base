import router from './router'
import store from './store'
import NProgress from 'nprogress' // progress bar
import 'nprogress/nprogress.css' // progress bar style
import { Message } from 'element-ui'
import { getToken } from '@/utils/auth' // getToken from cookie

NProgress.configure({ showSpinner: false })// NProgress configuration

const whiteList = ['/login'] // 不重定向白名单
router.beforeEach((to, from, next) => {
  NProgress.start()
  if (getToken()) {
    if (to.path === '/login') {
      next({ path: '/' })
      NProgress.done() // if current page is dashboard will not trigger	afterEach hook, so manually handle it
    } else {
      //获取用户数据
      if(Object.keys(store.getters.userinfo).length==0){
        store.dispatch('GetUserInfo').then(res => { // 拉取用户信息
          //下发路由菜单
          if(store.getters.routerInfo.rsMenuArr.length === 0){
            store.dispatch('GetMenuArr').then(res => {
              next({ ...to, replace: true }) // 在动态添加可访问路由表时 确保addRoutes已完成
            }).catch((err) => {
              /* store.dispatch('FedLoginOut').then(() => {
                Message.error(err || '身份检测有误，请重新登陆')
                next({ path: '/' })
              }) */
            })
          }
        }).catch((err) => {
          /* store.dispatch('FedLoginOut').then(() => {
            Message.error(err || '身份检测有误，请重新登陆')
            next({ path: '/' })
          }) */
        })
      }
      /* if (store.getters.roles.length === 0) {
        store.dispatch('GetUserInfo').then(res => { // 拉取用户信息
          next()
        }).catch((err) => {
          store.dispatch('FedLoginOut').then(() => {
            Message.error(err || 'Verification failed, please login again')
            next({ path: '/' })
          })
        })
      } else {
        next()
      } */
      next()
    }
  } else {
    if (whiteList.indexOf(to.path) !== -1) {
      next()
    } else {
      next(`/login?redirect=${to.path}`) // 否则全部重定向到登录页
      NProgress.done()
    }
  }
})

router.afterEach(() => {
  NProgress.done() // 结束Progress
})
