import request from '@/utils/request'

export function login(username, password) {
  return request({
    url: '/public/login',
    method: 'post',
    data: {
      username,
      password
    }
  })
}
export function logout() {
  return request({
    url: '/public/logout',
    method: 'post'
  })
}
export function getMenuArr(){
  return request({
    url: '/index/menu',
    method: 'post'
  })
}

export function getUserInfo(token) {
  return request({
    url: '/index/userInfo',
    method: 'post',
    //params: { token }
  })
}


