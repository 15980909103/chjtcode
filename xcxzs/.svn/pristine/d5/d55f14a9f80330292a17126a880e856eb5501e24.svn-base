<template>
  <div class="navbar">
    <hamburger :toggle-click="toggleSideBar" :is-active="sidebar.opened" class="hamburger-container"/>
    <breadcrumb />

    <el-dropdown v-if="activeCity&&activeCity.cname" class="platform-switching" trigger="click">
      <div class="avatar-wrapper">
        <span>{{activeCity.cname}}</span>
        <i class="el-icon-caret-bottom"/>
      </div>
      <el-dropdown-menu slot="dropdown" class="user-dropdown">
        <div v-for="item in userinfo.region_nos_info" :key="item.id" @click="doAcitveCity(item)">
          <el-dropdown-item>
            {{item.cname}}
          </el-dropdown-item>
        </div>
      </el-dropdown-menu>
    </el-dropdown>

    <el-dropdown class="avatar-container" trigger="click">
      <div class="avatar-wrapper">
        <span>{{userinfo.username}}</span>
        <i class="el-icon-caret-bottom"/>
      </div>
      <el-dropdown-menu slot="dropdown" class="user-dropdown">
        <div @click="openPage({url:'/changePassword/index'})">
          <el-dropdown-item>
            修改登录密码
          </el-dropdown-item>
        </div>
        <div @click="logout">
          <el-dropdown-item divided>
            退出
          </el-dropdown-item>
        </div>
      </el-dropdown-menu>
    </el-dropdown>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Breadcrumb from '@/components/Breadcrumb'
import Hamburger from '@/components/Hamburger'
import { debounce } from '@/utils'


var util=require('@/utils/util')

export default {
  components: {
    Breadcrumb,
    Hamburger
  },
  inject:['reload'],//调取App.vue里面注册的reload()方法
  data(){
    return{
      citys:[],
      activeCity:{},
    }
  },
  computed: {
    ...mapGetters([
      'sidebar',
      'avatar',
      'userinfo',
    ])
  },
  watch:{
    userinfo(e){
      //console.log(e)
    }
  },
  created:function(){
    /* let active = util.myStorage.local.get('$activeCity')
    if(!active||!this.inMyCity(active.id,this.userinfo.region_nos_info)){
      active = this.userinfo.region_nos_info&&this.userinfo.region_nos_info[0]?this.userinfo.region_nos_info[0]:''
      util.myStorage.local.set('$activeCity',active)
    }

    this.activeCity = active
    this.$urlData.$activeCity = active
    this.$store.dispatch('SetActiveCity',active) */
  },
  methods: {
    inMyCity(id,citys){
      for(var i in citys){
        if(id==citys[i].id){
          return true;
        }
      }
      return false;
    },
    doAcitveCity(active){
      this.activeCity = active
      util.myStorage.local.set('$activeCity',active)
      this.$urlData.$activeCity = active
      this.$store.dispatch('SetActiveCity',active)
      this.reload()
    },
    openPage:function(e){
      util.openPage(e)
    },
    
    toggleSideBar() {
      this.$store.dispatch('ToggleSideBar')
    },
    logout() {
      this.$store.dispatch('LogOut').then(() => {
        location.reload() // 为了重新实例化vue-router对象 避免bug
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.navbar {
  height: 50px;
  line-height: 50px;
  box-shadow: 0 1px 3px 0 rgba(0,0,0,.12), 0 0 3px 0 rgba(0,0,0,.04);
  .hamburger-container {
    line-height: 58px;
    height: 50px;
    float: left;
    padding: 0 10px;
  }
  .screenfull {
    position: absolute;
    right: 90px;
    top: 16px;
    color: red;
  }
  .platform-switching{
    position: absolute;
    right: 120px;
    cursor: pointer;
    /* &::after{
      content: "";
      display: inline-block;
      width: 1px;
      height: 20px;
      border-right: 1px solid gray;
      vertical-align: middle;
      margin-left: 10px;
    } */
  }
  .avatar-container {
    height: 50px;
    display: inline-block;
    position: absolute;
    line-height: 50px;
    right: 35px;
    .avatar-wrapper {
      cursor: pointer;
      position: relative;
      line-height: 50px;
      .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
      }
      .el-icon-caret-bottom {
        position: absolute;
        right: -20px;
        top: 19px;
        font-size: 12px;
      }
    }
  }
}
</style>

