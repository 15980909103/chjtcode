<template>
  <div class="_container">

        <div  style="display: inline;width: 100%; position: relative">
          <span class="admin_m20 admin_font1">用户增长</span>
          <el-select style="width:250px"
                     v-model="searchData.city_lsit"
                     filterable
                     allow-create
                     default-first-option
                     placeholder="区域">
            <el-option
              v-for="item in city_lsit"
              :key="item.id"
              :label="item.cname"
              :value="item.id">
            </el-option>
          </el-select>
        </div>
         <el-divider></el-divider>

     <div style="display: flex;height: 304px"></div>

  </div>
</template>

<script>
var util = require("@/utils/util");
import paginationBox from '../../components/common/pagination.vue';
import ImgUpload from '@/components/common/ImgUpload.vue';
export default {
  components: {
			'pagination-box': paginationBox,
    'img-upload': ImgUpload,
	},
  data() {
    return {
      searchData:[],
    };
  },
  watch:{

  },
  created: function() {
    this.city_lsit = this.$store.getters.userinfo.region_nos_info;
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
}
.admin_font1{
  height: 20px;
  font-size: 14px;
  font-family: PingFangSC-Medium, PingFang SC;
  font-weight: 500;
  color: #212121;
  line-height: 20px;
}
.admin_m20{
  display: inline-block;
  margin: 5px;
}
.el-divider--horizontal{
  margin: 5px;
}
.el-radio{
  margin-right: 5px;
}
</style>


