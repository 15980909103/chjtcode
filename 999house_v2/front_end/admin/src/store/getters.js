const getters = {
  sidebar: state => state.app.sidebar,
  device: state => state.app.device,
  visitedViews: state => state.tagsView.visitedViews,
  cachedViews: state => state.tagsView.cachedViews,
  token: state => state.user.token,
  sid: state => state.user.sid,
  userinfo: state => state.user.userinfo,
  routerInfo: state => state.user.routerInfo,

  avatar: state => state.user.avatar,
  name: state => state.user.name,
  roles: state => state.user.roles,
  permission_routes: state => state.permission.routes,
  activeCity: state => state.user.activeCity
}
export default getters
