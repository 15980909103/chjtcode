<template>
  <div class="_container">
    <el-tabs v-model="activeTab" type="card" >
      <el-tab-pane label="基本设置" name="1">
            <estates-edit v-if="activeTab=='1'"/> 
      </el-tab-pane>
      <el-tab-pane label="公众号菜单配置" name="2">
         <wxmemu-edit v-if="activeTab=='2'"/> 
      </el-tab-pane>
    </el-tabs>
  </div>
</template>
<script>
  var util = require("@/utils/util.js");
  import ImgUpload from '@/components/common/ImgUpload.vue';
  import EstatesEdit from  '@/components/city/cityset.vue';
  import WxMenu from  '@/components/city/wxmenu.vue';
  import baseMixin from  '@/mixin/baseMixin';
  
  export default {
    components: {
      'img-upload': ImgUpload,
      'estates-edit': EstatesEdit,
      'wxmemu-edit': WxMenu,
    },
    mixins: [baseMixin],
    data() {
      return {
        activeTab: '1',
      };
    },
    methods: {
      
    }
  };
</script>
<style lang="scss" scoped>
._container {
  margin-top: 20px;
  padding: 20px;
  background: #fff;
  min-height: calc(100vh - 90px);
  .mapeditor-title {
    line-height: 40px;
    padding-left: 10px;
    background-color: rgba($color: #f0f2f5, $alpha: 0.5);
    border-left: 4px solid;
    border-color: #409eff;
    font-size: 14px;
    margin-bottom: 10px;
  }
  .mapeditor-content {
    padding-left: 16px;
    .el-col {
      margin: 10px 0;
    }
  }
  
}
</style>