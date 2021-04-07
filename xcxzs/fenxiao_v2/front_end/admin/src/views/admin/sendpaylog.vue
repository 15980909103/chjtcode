<template>
  <div class="mapmange_container">
    <div class="mapmange_content">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item label="创建时间">
           <el-date-picker
            style="width:100%"
            v-model="searchTime"
            value-format="yyyy-MM-dd" format="yyyy-MM-dd"
            type="daterange"
            range-separator="-"
            start-placeholder="开始日期"
            end-placeholder="结束日期">
          </el-date-picker>
        </el-form-item>
        <el-form-item label="申请单号">
          <el-input style="width:100%" v-model="searchData.out_trade_no" placeholder="请输入申请单号" prefix-icon="el-icon-search"></el-input>
        </el-form-item>

        <el-form-item style="text-align: right;">
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
          <el-button type="success"  @click="onReset">重置</el-button>
        </el-form-item>
      </el-form>
        <!-- ========================================================= -->
        <el-table :data="tableData" style="width: 100%">
          <el-table-column prop="id" label="ID" width="80" align="center"></el-table-column>
          <el-table-column prop="out_trade_no" label="申请单号" width="210" align="center"></el-table-column>
          <el-table-column prop="url" label="请求地址" align="center"></el-table-column>
          <el-table-column prop="postdata" label="请求数据"  align="center">
            <template slot-scope="scope">
             <el-popover trigger="click" width="600" placement="top">
              <p>{{scope.row.postdata}}</p>
              <div slot="reference" class="name-wrapper">
                <div class="my-textoverflow">{{ scope.row.postdata }}</div>
              </div>
            </el-popover>
            </template>
          </el-table-column>
          <el-table-column prop="response" label="返回数据"  align="center">
            <template slot-scope="scope">
              <el-popover trigger="click" width="600" placement="top">
                <p>{{scope.row.response}}</p>
                <div slot="reference" class="name-wrapper">
                  <div class="my-textoverflow">{{ scope.row.response }}</div>
                </div>
              </el-popover>
            </template>
          </el-table-column>
          <el-table-column prop="create_time" label="创建时间" width="160" align="center">
            <template slot-scope="scope">
             <span>{{getFormatDate(scope.row.create_time,2)}}</span>
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
import ImgPreview from '@/components/common/ImgPreview.vue';
export default {
  components: {
      'pagination-box': paginationBox,
      'img-preview': ImgPreview,
	},
  data() {
    var validateStatus = (rule, value, callback) => {
      if (value!='1') {
  　　　　callback(new Error('请选择审核状态'));
  　　　　return false;
  　　}
      callback();
    };

    return {
      searchTime:[],
      searchData: {
        //  搜索数据
        startdate: '',
        enddate: '',
        url: '',
        out_trade_no: '',
      },
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据
    };
  },
  computed:{

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
      console.log(searchdata);
      util.requests("post", {
          url: "log/sendpayLog",
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
    onReset(){
      var searchData = {
        //  搜索数据
        startdate: '',
        enddate: '',
        url: '',
        out_trade_no: '',
      };
      this.setDataArr({
        searchData:searchData,
        searchTime:[],
      })
      this.getList()
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


 
    getFormatDate:util.DataFun.getFormatDate,
    openPage: util.openPage
  }
};
</script>
<style lang="scss" scoped>
.mapmange_container {
  min-height: calc(100vh - 50px);
  background: #f0f2f5;
  padding-top: 20px;
  /deep/.el-tabs__header{
    margin-left: 42px;
  }
  .mapmange_content {
    background: #fff;
    min-height: calc(100vh - 90px);
    border-radius: 2px;
    padding: 20px 10px;
    .form-serch {
      text-align: left;
      .el-input {
        width: 300px;
      }
      .el-select {
        width: 150px;
      }
      /deep/.el-input-group__prepend{
        background: #fff!important;
        .el-select {
          width: 100px;
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
  .my-textoverflow{
    cursor: pointer;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis; 
  }
  .pagination {
    margin-top: 20px;
    text-align: right;
  }
}
</style>
