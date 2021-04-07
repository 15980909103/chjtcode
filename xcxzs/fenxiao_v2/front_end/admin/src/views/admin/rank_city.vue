<template>
  <div class="_container">
    
    <el-table class="tb-title" :data="tableData" style="width: 100%" row-key="id" :tree-props="{children: 'children', hasChildren: 'hasChildren'}">
      <el-table-column prop="id" label="ID" width="150" align="center"></el-table-column>
      <el-table-column prop="cname" label="城市名称"  align="center"></el-table-column>

      <el-table-column prop="opt" label="操作"  align="center">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="openPage({url:'/admin/rank',data:{id: scope.row.id, type: scope.row.type}})">查看榜单</el-button>
        </template>
      </el-table-column>
    </el-table>


  </div>
</template>
<script>
// import { log } from 'util';
var util = require("@/utils/util.js");

import baseMixin from  '@/mixin/baseMixin';

export default {
  components: { },
  mixins: [baseMixin],
  data() {
    return {
      dialogVisibleEdit: false,
      formLabelWidth: "123px",

      searchData:{   
          name : '',
      },
      
      tableData: [],   

      formData:{
        
      }
    }
  },
  watch:{
    
  },
  created: function(){
     this.resetData({
      formData: this.formData,
    })
    this.getList(this.searchData)
  },

  methods:{
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"estates/getRankCity",
        data: searchdata
      }).then(res=>{
        that.tableData = res.data
      })
    },

    openPage: util.openPage,
    
    onSearch(){
      this.getList(this.searchData);
    },

  }

};
</script>
<style lang="scss" scoped>
  .tb-title{
    margin-top: 40px;
  }
  .type{
    float: right;
    position: relative;
    top: -164px;
    left: -100px;
  }
  .editimg{
    float: left;
  }
  .infoEdit{
    float: right;
  }

</style>


