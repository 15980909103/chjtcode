<template>
  <div class="_container">
    <el-form :inline="true" :model="searchData" class="form-serch">
        <!-- <el-form-item label="排序" label-width="120px">
          <el-select v-model="searchData.sort" placeholder="请选择">
            <el-option label="ID" value="id"></el-option>
            <el-option label="最近消费时间" value="last_custtime"></el-option>
            <el-option label="消费总金额" value="total_custmoney"></el-option>
          </el-select>
        </el-form-item> -->
        <el-form-item label="客户名字" label-width="120px">
          <el-input placeholder="请输入客户名字" v-model="searchData.customer_name"></el-input>
        </el-form-item>
        <el-form-item label="客户手机号" label-width="120px">
          <el-input placeholder="请输入客户手机号" v-model="searchData.customer_mobile"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
        </el-form-item>
    </el-form>

    <el-table :data="tableData" style="width:100%">
      <el-table-column prop="id" label="ID" width="80" align="center"></el-table-column>
      <el-table-column prop="customer_name" label="客户信息" align="center">
        <template slot-scope="scope">
           {{scope.row.customer_name}} | {{scope.row.customer_mobile}}
        </template>
      </el-table-column>
      <el-table-column prop="user_nickname" label="归属人员" align="center"></el-table-column>
      <el-table-column prop="table_no" label="桌号" align="center"></el-table-column>
      <el-table-column label="花" align="center">
        <template slot-scope="scope">
           {{scope.row.flower_price}}
        </template>
      </el-table-column>
      <el-table-column label="酒" align="center">
        <template slot-scope="scope">
           {{scope.row.wine_price}}
        </template>
      </el-table-column>
     
      <el-table-column prop="create_date" label="创建时间" align="center"></el-table-column>
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
        sort:"id",
        customer_name: "",
        customer_mobile: "",
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
          url: "user/getBookList",
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
    text-align: right;
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


