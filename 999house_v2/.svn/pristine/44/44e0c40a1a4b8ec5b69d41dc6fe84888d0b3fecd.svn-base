import { login, logout, getUserInfo, getMenuArr } from '@/api/login'
import { getToken, setToken, setSid, getSid, removeToken, removeSid, initMenu,setFileHost } from '@/utils/auth'
const user = {
  state: {
    token: getToken(),
    sid  : getSid(),
    userinfo:{
    },
    routerInfo:{//路由菜单
      menuArr: [],//合并了rsMenuArr总的menu数据
      rsMenuArr:[],//只是返回的menu数据
      constantRouterMap:[]//初始路由设置
    },
    name: '',
    avatar: '',
    roles: [],
    activeCity:{}
  },

  mutations: {
    SET_TOKEN: (state, val) => {
      state.token = val
    },
    SET_SID: (state, val) => {
      state.sid = val
    },

    SET_USERINFO:(state, val)=>{
      state.userinfo=val
    },
    SET_MENU:(state, e)=>{
      state.routerInfo.menuArr=e.menuArr;
      state.routerInfo.rsMenuArr=e.rsMenuArr;
      state.routerInfo.constantRouterMap=e.constantRouterMap;
    },

    SET_NAME: (state, val) => {
      state.name = val
    },
    SET_AVATAR: (state, val) => {
      state.avatar = val
    },
    SET_ROLES: (state, val) => {
      state.roles = val
    },
    SET_ACTIVECITY: (state, val) => {
      state.activeCity = val
    },
  },

  actions: {
    SetActiveCity: ({ commit },val) => {
      commit('SET_ACTIVECITY',val)
    },
    // 登录
    Login({ commit }, userInfo) {
      const username = userInfo.username.trim()
      const password = userInfo.password.trim()
      return new Promise((resolve, reject) => {
        login(username, password).then(response => {
          //console.log(response)
          if(response.code=='1'){
            response.data.token
            setToken(response.data.token)
            setSid(response.data.sid)
            setFileHost(response.data.host)
            commit('SET_TOKEN', response.data.token)
            commit('SET_SID', response.data.sid)
            //commit('SET_USERINFO', response.data.user)
            resolve()
          }else{
            reject(response.msg)
          }
        }).catch(error => {
          reject(error)
        })
      })
    },
    // 获取动态菜单
    GetMenuArr({ commit, state }) {
      return new Promise((resolve, reject) => {
        getMenuArr().then(response => {
          //console.log(response)
          var rs_menus = []
          if( response.code==1 && response.data && Object.keys(response.data.list).length>0 ){
            rs_menus = response.data.list
            for(var i in rs_menus){
              if(rs_menus[i]['url']){
                  rs_menus[i]['url']=rs_menus[i]['url'].replace('admin/','')
              }
            }
          }

          let rs=initMenu.setInit(rs_menus)//对返回的menu格式处理
          commit('SET_MENU',{rsMenuArr:rs.rsMenuArr,menuArr:rs.menuArr,constantRouterMap:rs.constantRouterMap})
          resolve(rs)
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 获取用户信息
     GetUserInfo({ commit, state }) {
      return new Promise((resolve, reject) => {
        getUserInfo(state.token).then(response => {
          //console.log(response)
          if (response.code==1 && Object.keys(response.data).length > 0) { // 验证返回的roles是否是一个非空数组
            //格式化可操作的应用列表
            var scenics=response.data.scenics
            var newscenics={}
            for(var i in scenics){
              var item=scenics[i]
              newscenics[item.id]=item
            }
            response.data.scenics=newscenics

            commit('SET_USERINFO', response.data)
          } else {
            reject('getInfo: 用户数据获取失败!')
          }

          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    },

    /**
     * 登出的公共操作
     * @param {*} param0
     */
    clearForLogOut({ commit, state }){
      return new Promise((resolve, reject) => {
        removeToken()
        commit('SET_TOKEN', '')
        removeSid()
        commit('SET_SID', '')

        commit('SET_USERINFO',{})
        commit('SET_ROLES', [])
        resolve()
      })
    },

    // 登出
    LogOut({ commit, state, dispatch }) {
      return new Promise((resolve, reject) => {
        logout(state.token).then(() => {
          dispatch('clearForLogOut')

          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 前端 登出
    FedLoginOut({ commit, state, dispatch }) {
      return new Promise(resolve => {
        dispatch('clearForLogOut')
        resolve()
      })
    }
  }
}

export default user
