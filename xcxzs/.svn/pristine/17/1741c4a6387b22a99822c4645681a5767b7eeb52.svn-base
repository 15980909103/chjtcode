<template>
  <div class="_container">
    <el-form :inline="true" :model="searchData" class="form-serch">
        <el-form-item label="排序" label-width="120px">
          <el-select v-model="searchData.sort" placeholder="请选择">
            <el-option label="ID" value="id"></el-option>
            <el-option label="注册时间" value="create_time"></el-option>
            <el-option label="积分" value="score"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="昵称" label-width="120px">
          <el-input placeholder="请输入用户昵称" v-model="searchData.user_nickname"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
        </el-form-item>
    </el-form>

    <el-table :data="tableData" style="width:100%">
      <el-table-column prop="id" label="ID" width="80" align="center"></el-table-column>
      <el-table-column prop="user_nickname" label="归属用户" width="80" align="center"></el-table-column>
      <el-table-column prop="team_name" label="团队名称" align="center"></el-table-column>
      <el-table-column prop="join_user_count" label="成员数量" align="center">
        <template slot-scope="scope">
           {{scope.row.member_total.member_count}}
        </template>
      </el-table-column>
      <el-table-column label="创建时间" align="center">
        <template slot-scope="scope">
           {{getFormatDate(scope.row.create_time)}}
        </template>
      </el-table-column>
     
      <el-table-column prop="code" label="操作" width="200" align="center">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="openPage({url: '/user/teamInfo',data:{team_id:scope.row.id, user_id:scope.row.user_id}})">查看</el-button>
        </template>
      </el-table-column>
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

      form: {
        user_nickname: "",
        editscore: ''
      },
      searchData: {
        sort:"id",
        user_nickname: "",
      },
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据
      caneditSource:false,
      total:0
    };
  },
  created: function(){
    this.getList()
  },
  methods: {
    showEditSource(){
      this.caneditSource = !this.caneditSource
      if(this.caneditSource === false){
        this.form.editscore = ''
      }
    },

    getList(searchdata={}){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "team/getTeamList",
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
      console.log("查询",this.searchData);
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
        that.form.avatar = that.$getRealImgUrl(that.form.avatar)
      }
    },
    
    getFormatDate(time){
     return util.DataFun.getFormatDate(time,2)
    },
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },
    openPage:util.openPage
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


