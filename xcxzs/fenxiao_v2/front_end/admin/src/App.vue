<template>
  <div id="app">
    <router-view  v-if="isRouterAlive"/>
  </div>
</template>

<script>
export default {
  name: 'App',
  provide(){//定义子组件可以直接调用的属性或者方法
    return{
      reload:this.reload //页面重刷
    }
  },
  data(){
    return{
      isRouterAlive:true
    }
  },
  created:function(){
    document.title =this.$baseconfig.sitename
  },
  mounted:function(){
    var that = this
    
  },
  methods:{
    reload(){//定义页面重刷方法，原理让组件重新加载
      this.isRouterAlive = false;
      this.$nextTick(function(){//$nextTick里面的代码会在DOM更新后执行
        this.isRouterAlive = true;
      })
    }
  }
}
</script>

