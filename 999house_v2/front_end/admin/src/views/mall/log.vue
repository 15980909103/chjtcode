<template>
  <div class="mapmange_container">
    <div class="mapmange_content">
      <el-form :inline="true" :model="searchData" class="form-serch">
        <el-form-item>
           <el-date-picker
            style="width:380px;"
            v-model="searchTime"
            value-format="yyyy-MM-dd"
            type="daterange"
            range-separator="至"
            start-placeholder="申请的开始日期"
            end-placeholder="申请的结束日期">
          </el-date-picker>
        </el-form-item>

        <!-- <el-form-item>
          <el-input v-model="searchData.selectVal" :placeholder="select_placeholder">
            <el-select v-model="searchData.selectKey" slot="prepend" placeholder="请选择">
              <el-option label="手机号" value="mobile"></el-option>
              <el-option label="姓名" value="contact_name"></el-option>
              <el-option label="商品名称" value="goods_name"></el-option>  
            </el-select>
          </el-input>
        </el-form-item> -->

        <el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
        </el-form-item>
      </el-form>

      <!-- ========================================================= -->
      <el-table :data="tableData" style="width: 100%">
        <el-table-column prop="id" label="ID" width="80" align="center"></el-table-column>

        <el-table-column prop="goods_name" label="商品名称" width="180" align="center"></el-table-column>
        <el-table-column contact_name label="用户信息" width="150"  align="center">
          <template slot-scope="scope">
            <div style="text-align: left;">
              <div>姓名:{{scope.row.contact_name}}</div>
              <div>手机号:{{scope.row.mobile}}</div>
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="create_time" label="申请时间" align="center">
          <template slot-scope="scope">
              <div>{{getFormatDate(scope.row.create_time)}}</div>
          </template>
        </el-table-column>

        <el-table-column prop="receive_time" label="领取时间" align="center">
          <template slot-scope="scope">
              <div>{{getFormatDate(scope.row.receive_time)}}</div>
          </template>
        </el-table-column>


        <el-table-column label="操作"  align="center">
          <template slot-scope="scope">
            <el-button  type="primary" size="mini" @click="doEdit({id: scope.row.id, contact_name: scope.row.contact_name, mobile: scope.row.mobile})">查看</el-button>
          </template>
        </el-table-column>
      </el-table>
      <!-- ========================================================= -->

      <!-- ============分页=============== -->
      <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>

    </div>
  </div>
</template>
<script>
var util = require("@/utils/util.js");
import paginationBox from '@/components/common/pagination.vue';

export default {
  components: {
      'pagination-box': paginationBox,
	},
  data() {
    return {
      searchTime:[],
      searchData: {
        //  搜索数据
        selectKey:'mobile',
        selectVal:'',
        startdate: '',
        enddate: '',
      },
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据
    };
  },
  computed:{
    select_placeholder(){
      if(this.searchData.selectKey == 'mobile'){
        return '请输入手机号'
      } 
      if(this.searchData.selectKey == 'contact_name'){
        return '请输入姓名'
      } 
      if(this.searchData.selectKey == 'goods_name'){
        return '请输入商品名称'
      } 
    }
  },
  watch: {
    searchTime(newVal){
      if(newVal){
        this.searchData.startdate = newVal[0]
        this.searchData.enddate = newVal[1]
      }else{
        this.searchData.startdate = ''
        this.searchData.enddate = ''
      }
    }
  },
  created: function(){
    this.getList(this.searchData)
  },
  methods: {
    getList(searchdata={}){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "exchange/log",
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
              }
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    onSerch() {
      //console.log("查询",this.searchData);
      this.getList(this.searchData)
    },
    //分页操作
    pageChange: function(page) {
      let post_data = Object.assign({},this.searchData);
      post_data.page = page;
      this.getList(post_data)
    },
   

    doEdit(e){
      let that = this
      let id = e.id
    
    },
    

    getFormatDate: function(val){
      if(val){
        return util.DataFun.getFormatDate(val,2)
      }
      return ''
    },

    openPage: util.openPage
  }
};
</script>
<style lang="scss" scoped>
.mapmange_container {
  min-height: calc(100vh - 50px);
  background: #f0f2f5;
  padding-top: 20px;
  .mapmange_content {
    background: #fff;
    min-height: calc(100vh - 90px);
    border-radius: 2px;
    padding: 20px 10px;
    .form-serch {
      text-align: right;
      .el-input {
        width: 300px;
      }
      .el-select {
        width: 150px;
      }
      /deep/.el-input-group__prepend{
        background: #fff!important;
        .el-select {
          width: 110px;
        }
      }
    }
  }
  /deep/.dialog-examine .el-input.is-disabled .el-input__inner,/deep/.dialog-examine .el-textarea.is-disabled .el-textarea__inner{
    cursor: auto;
    color: #606266;
  }
  .dialog-examine .avatar{
      width: 35px;
      height: 35px;
      border-radius: 50%;
  }
  .dialog-examine .thumbnail{
    width: 110px;
    height:110px;
    margin-right: 9px;
    display: inline-block;
    position: relative;
  }
  .dialog-examine .thumbnail:nth-child(5n){
     margin-right: 0;
   }
  .dialog-examine .thumbnail {
    .el-image{
      width: 100%;
      height: 100%;
    }
    .el-upload-list__item-actions {
      position: absolute;
      width: 100%;
      height: 100%;
      left: 0;
      top: 0;
      cursor: default;
      text-align: center;
      color: #fff;
      opacity: 0;
      font-size: 20px;
      background-color: rgba(0,0,0,.5);
      transition: opacity .3s;
    }
    .el-upload-list__item-actions:hover {
      opacity: 1;
      cursor: pointer;
    }
    .el-upload-list__item-actions span {
      line-height: 110px;
      display: none;
    }
    .el-upload-list__item-actions:hover span {
      display: inline-block;
    }
  }
  

  .pagination {
    margin-top: 20px;
    text-align: right;
  }
}
</style>