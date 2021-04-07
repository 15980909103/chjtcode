<template>
  <div class="_container">
    <el-form :inline="true" :model="searchData" class="form-serch">
        <el-form-item label="分销商昵称" label-width="120px">
          <el-input placeholder="请输入分销商昵称" v-model="searchData.user_nickname"></el-input>
        </el-form-item>
        <el-form-item label="分销商手机号" label-width="120px">
          <el-input placeholder="请输入分销商手机号" v-model="searchData.mobile"></el-input>
        </el-form-item>
        <el-form-item label="开户姓名" label-width="120px">
          <el-input placeholder="请输入开户姓名" v-model="searchData.real_name"></el-input>
        </el-form-item>
        <el-form-item label="银行卡号" label-width="120px">
          <el-input placeholder="请输入银行卡号" v-model="searchData.band_card"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
        </el-form-item>
    </el-form>

    <el-table :data="tableData" style="width:100%">
      <el-table-column prop="id" label="ID" width="80" align="center"></el-table-column>
      <el-table-column prop="user_nickname" label="分销商昵称" align="center"></el-table-column>
      <el-table-column prop="mobile" label="分销商手机号" align="center"></el-table-column>
      <el-table-column label="佣金" align="center">
        <template slot-scope="scope">
           {{scope.row.commission}}
        </template>
      </el-table-column>
      <el-table-column label="累计佣金" align="center">
        <template slot-scope="scope">
           {{scope.row.total_commission}}
        </template>
      </el-table-column>

      <el-table-column prop="real_name" label="开户姓名" align="center"></el-table-column>
      <el-table-column prop="band_card" label="银行卡" align="center"></el-table-column>
    </el-table>

    <!-- ============分页=============== -->
    <div style="text-align:right">
      <pagination-box :pagination="pagination" @pageChange="pageChange" style="display:inline-block;" ></pagination-box>
      <el-link style="margin-top: 10px;pointer-events:none;">共有 <el-link style="pointer-events:none;margin-top:-2px" type="primary">{{total}}</el-link> 用户</el-link>
    </div>
      
 
    
  </div>
</template>
<script>
var util = require("@/utils/util.js");
import paginationBox from '@/components/common/pagination.vue';
import { isNumber } from 'util';

export default {
  components: {
    'pagination-box': paginationBox,
  },
  
  data() {
    return {
      dialogFormVisible: false,
      formLabelWidth: "123px",

      form: { },
      searchData: {
        user_nickname: "",
        mobile: "",
        real_name:"",
        band_card:"",
      },
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据
      total:0
    };
  },
  created: function(){
    this.getList()
  },
  methods: {

    getList(searchdata={}){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "commission/getDistributors",
          data: searchdata
      }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.setDataArr({
              tableData : res.data.list,
              pagination : {
                page : res.data.current_page,
                pagecount : res.data.last_page,
                pagesize : Math.ceil(res.data.total / res.data.last_page)
              },
              total:res.data.total
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    onSerch() {
      this.getList(this.searchData)
    },
    //分页操作
    pageChange: function(page) {
      let post_data = Object.assign({},this.searchData);
      post_data.page = page;
      this.getList(post_data)
    },
    doEdit(e={}){
      let that = this
      that.dialogFormVisible = true
      if(Object.keys(e).length>0){
        that.form = Object.assign({},e)
        //that.form.avatar = that.$getRealImgUrl(that.form.avatar)
      }
    },
    
    getFormatDate(time){
     return util.DataFun.getFormatDate(time,1)
    },
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },
  }
};
</script>
<style lang="scss" scoped>
._container {
  margin-top: 20px;
  padding: 20px;
  background: #fff;
  .pagination {
    text-align: right;
    margin-top: 20px;
  }
  .form-serch {
    text-align: left;
  }
  .editsource-btn{
    position: absolute;top: 0;right: -175px;
  }
  .editsource-cancelbtn{
    position: absolute;top: 0;right: -260px;
  }
  .user-dialog {
    .el-form{
      padding:0 10px;
    }
    .el-form-item{
      margin-bottom: 0;
    }
    /deep/.el-form-item__label,/deep/.el-form-item__content{
      line-height: 32px;
    }
  }
  
}
</style>


