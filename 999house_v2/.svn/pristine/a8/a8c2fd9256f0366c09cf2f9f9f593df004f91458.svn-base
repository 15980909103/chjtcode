<template>
  <div class="_container">
    <estates-open-time />
  </div>
</template>
<script>
  var util = require("@/utils/util.js");
  import ImgUpload from '@/components/common/ImgUpload.vue';
  import EstatesOpenTime from  '@/components/page/EstatesnewOpenTime.vue';
  import baseMixin from  '@/mixin/baseMixin';

  export default {
    components: {
      'img-upload': ImgUpload,
      'estates-open-time': EstatesOpenTime,
    },
    mixins: [baseMixin],

    watch:{
      
    },
    data() {
      return {
        estate_id:0,
        building_id:0,
      };

    },

    created: function(){
        if(this.$urlData) {
            if(this.$urlData.estate_id) {
              this.estate_id = this.$urlData.estate_id
            }
            if(this.$urlData.building_id) {
              this.building_id = this.$urlData.building_id
            }
        }
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
