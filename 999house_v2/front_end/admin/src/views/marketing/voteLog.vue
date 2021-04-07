<template>
  <div class="_container">
    <el-form :inline="true" :model="searchData" class="form-serch">
        <el-form-item label="昵称" label-width="120px">
          <el-input placeholder="请输入用户昵称" v-model="searchData.nickname"></el-input>
        </el-form-item>
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
        
        <el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
        </el-form-item>
        <el-form-item :inline="true" :model="searchData" class="form-serch" >
          <el-button icon="el-icon-back" @click="openPage({url:-1,hreftype:'navigateBack'})">返回</el-button>
        </el-form-item>
    </el-form>

    <el-table :data="tableData" style="width:100%">
      <el-table-column prop="id" label="ID" width="60" align="center"></el-table-column>
      <el-table-column label="用户头像" align="center">
        <template slot-scope="scope">
           <el-image style="width:60px;height:60px;" :src="getRealImgUrl(scope.row.headimgurl)"></el-image>
        </template>
      </el-table-column>
       <el-table-column prop="nickname" label="昵称" align="center"></el-table-column>
       <el-table-column prop="phone" label="手机号码" align="center"></el-table-column>
     
      <el-table-column label="创建时间" align="center">
        <template slot-scope="scope">
           {{getFormatDate(scope.row.create_time)}}
        </template>
      </el-table-column>
 
    </el-table>

    <!-- ============分页=============== -->
    <div style="text-align:right">
      <pagination-box :pagination="pagination" @pageChange="pageChange" style="display:inline-block;" ></pagination-box>
      <el-link style="margin-top: 10px;pointer-events:none;">共有 <el-link style="pointer-events:none;margin-top:-2px" type="primary">{{total}}</el-link> 数据</el-link>
    </div>
      
  
  </div>
</template>
<script>
var util = require("@/utils/util.js");
import paginationBox from '@/components/common/pagination.vue';
import MycitySelect from '@/components/common/MycitySelect.vue';
import { isNumber } from 'util';

export default {
  components: {
    'pagination-box': paginationBox,
    'mycity-select': MycitySelect,
  },
  
  data() {
    return {
      dialogFormVisible: false,
      formLabelWidth: "123px",
      searchTime:[],  
      searchData: {
        vote_detail_id: '',
        region_no: '',
        startdate: '',
        enddate: '',
        nickname: "",
      },
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据
      caneditSource:false,
      total:0,

      vote_detail_id:'',
      region_no:'',
    };
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
    let that = this
    that.setDataArr({
      vote_detail_id: that.$urlData.vote_detail_id,
      region_no: that.$urlData.region_no,
      searchData:{
        vote_detail_id: that.$urlData.vote_detail_id,
        region_no: that.$urlData.region_no,
      },
    })

    if(!that.vote_detail_id||!that.region_no){
      this.$alert('缺失参数', '提示',{
        confirmButtonText: '确定',
        callback: action => {
          util.openPage({url:'/marketing/vote'});
        }
      });
      return;
    }
    
    this.getList(that.searchData)
  },
  methods: {

    getList(searchdata={}){
     
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true
      util.requests("post", {
          url: "vote/getLogList",
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
    
    openPage:util.openPage,
    getFormatDate(time){
     return util.DataFun.getFormatDate(time,2)
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


