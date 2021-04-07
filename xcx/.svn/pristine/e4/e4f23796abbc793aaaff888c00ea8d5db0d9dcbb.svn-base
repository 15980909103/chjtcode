<template>
  <div class="_container">
    <el-tabs v-model="activeTab" type="card" @tab-click="handleClick">
      <el-tab-pane label="楼盘信息" name="1">
            <estates-edit v-if="activeTab=='1'" />
      </el-tab-pane>
      <el-tab-pane v-if="url_id>0" label="建筑信息" name="9">
            <estatesnew-binfo v-if="activeTab=='9'"/>
      </el-tab-pane>
       <el-tab-pane v-if="url_id>0" label="物业信息" name="10">
            <estatesnew-wuye v-if="activeTab=='10'"/>
      </el-tab-pane>
      <el-tab-pane v-if="url_id>0" label="开盘时间管理" name="5">
            <estates-open-time v-if="activeTab=='5'"/>
      </el-tab-pane>
      <el-tab-pane v-if="url_id>0" label="楼栋管理" name="2">
            <estates-building  v-if="activeTab=='2'"/>
      </el-tab-pane>
      <el-tab-pane v-if="url_id>0" label="户型管理" name="7">
            <estatesnew-house  v-if="activeTab=='7'"/>
      </el-tab-pane>
      <el-tab-pane v-if="url_id>0" label="楼盘相册" name="3">
            <buildingphotos-list v-if="activeTab=='3'"/>
      </el-tab-pane>
      <el-tab-pane v-if="url_id>0" label="历史价格变化列表" name="4">
            <buildingpricelog-list v-if="activeTab=='4'"/>
      </el-tab-pane>
       <el-tab-pane v-if="url_id>0" label="关联资讯" name="6">
            <estatesnew-news v-if="activeTab=='6'"/>
      </el-tab-pane>
      <el-tab-pane v-if="url_id>0" label="优惠信息" name="8">
            <estatesnew-discount v-if="activeTab=='8'"/>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>
<script>
  var util = require("@/utils/util.js");
  import ImgUpload from '@/components/common/ImgUpload.vue';
  import EstatesEdit from  '@/components/page/EstatesnewEdit.vue';
  import EstatesBuilding from  '@/components/page/EstatesnewBuilding.vue';
  import BuildingPriceLog from  '@/components/page/buildingpricelog.vue';
  import BuildingPhotos from  '@/components/page/BuildingPhotos.vue';
  import EstatesOpenTime from  '@/components/page/EstatesnewOpenTime.vue';
  import EstatesNewNews from  '@/components/page/EstatesNewNews.vue';
  import EstatesNewHouse from  '@/components/page/EstatesnewHouse.vue';
  import EstatesNewDiscount from  '@/components/page/EstatesnewDiscount.vue';
  import EstatesNewBinfo from  '@/components/page/EstatesNewBinfo.vue';
  import EstatesNewWuye from  '@/components/page/EstatesnewWuye.vue';
  import baseMixin from  '@/mixin/baseMixin';

  export default {
    components: {
      'img-upload': ImgUpload,
      'estates-edit': EstatesEdit,
      'estates-building': EstatesBuilding,
      'buildingpricelog-list': BuildingPriceLog,
      'buildingphotos-list': BuildingPhotos,
      'estates-open-time': EstatesOpenTime,
      'estatesnew-news': EstatesNewNews,
      'estatesnew-house': EstatesNewHouse,
      'estatesnew-discount': EstatesNewDiscount,
      'estatesnew-binfo': EstatesNewBinfo,
      'estatesnew-wuye': EstatesNewWuye,
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
        url_id:0,
      };

    },

    created: function(){
        this.url_id = this.$urlData && this.$urlData.id?this.$urlData.id:0
        this.activeTab = this.$urlData&&this.$urlData.activeTab?String(this.$urlData.activeTab):'1'
    },

    methods: {
      handleClick(tab, event) {
       // console.log(tab, event);
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
