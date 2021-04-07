<template>
  <div class="_container">
    <el-tabs v-model="activeTab" type="card" @tab-click="handleClick">
      <el-tab-pane label="楼盘人气榜" name="1">
            <estates-rank v-if="activeTab=='1'" :type="1" />
      </el-tab-pane>
      <el-tab-pane label="楼盘热搜榜" name="2">
            <estates-rank v-if="activeTab=='2'" :type="2" />
      </el-tab-pane>
      <el-tab-pane label="热讯榜" name="3" v-if="zx_flag">
            <article-rank v-if="activeTab=='3'" :type="3" />
      </el-tab-pane>
    </el-tabs>
  </div>
</template>
<script>
  var util = require("@/utils/util.js");
  import EstatesRank from  '@/components/page/EstatesRank.vue';
  import ArticleRank from  '@/components/page/ArticleRank.vue';
  import baseMixin from  '@/mixin/baseMixin';

  export default {
    components: {
      'estates-rank': EstatesRank,
      'article-rank': ArticleRank,
    },
    mixins: [baseMixin],

    watch:{
      activeTab(val){
        this.$changeUrlData({activeTab:val})
      }
    },
    data() {
      return {
        activeTab: '1',
        zx_flag:false
      };

    },

    created: function(){
       let id = this.$urlData.id;
       let citylist  = this.$store.getters.userinfo.region_nos_info;
       let cityarr = citylist.map(item =>item.id);

       if(cityarr.includes(Number(id)) ){
         this.zx_flag  = true;
       }
      //  this.activeTab = this.$urlData.activeTab;
    },

    methods: {
      handleClick(tab, event) {
        console.log(tab, event);
      },
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
