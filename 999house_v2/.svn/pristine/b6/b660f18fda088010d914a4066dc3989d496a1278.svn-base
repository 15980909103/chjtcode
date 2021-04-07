<template>
  <div class="_container">
    <el-form :inline="true" :model="searchData" class="form-serch">
        
        <el-form-item label="昵称" label-width="120px">
          <el-input placeholder="请输入用户昵称" v-model="searchData.user_nickname"></el-input>
        </el-form-item>

        <el-form-item label="手机号" label-width="120px">
          <el-input placeholder="请输入手机号" v-model="searchData.phone"></el-input>
        </el-form-item>

         <el-form-item label="用户状态">
            <el-select v-model="searchData.status" placeholder="请选择">
              <el-option label="全部" value="-1"></el-option>
              <el-option label="启用" value="1"></el-option>
              <el-option label="冻结" value="0"></el-option>
            </el-select>
          </el-form-item>
        <el-form-item>
          <el-form-item label="注册时间">
           <el-date-picker
            style="width:250px"
            v-model="searchTime"
            value-format="yyyy-MM-dd" format="yyyy-MM-dd"
            type="daterange"
            range-separator="-"
            start-placeholder="开始日期"
            end-placeholder="结束日期">
          </el-date-picker>
        </el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
        </el-form-item>
    </el-form>

    <el-table :data="tableData" style="width:100%">
      <el-table-column prop="id" label="ID" width="80" align="center"></el-table-column>
      <el-table-column label="用户头像" align="center">
        <template slot-scope="scope">
           <el-image style="width:80px;height:80px;" :src="getRealImgUrl(scope.row.headimgurl)"></el-image>
        </template>
      </el-table-column>
       <el-table-column prop="nickname" label="昵称" align="center"></el-table-column>
       <el-table-column prop="cname" label="地址" align="center"></el-table-column>
       <el-table-column prop="phone" label="手机号码" align="center"></el-table-column>
        <el-table-column prop="type" label="用户类型" align="center"></el-table-column>
      <!-- <el-table-column label="是否是工作人员" align="center">
        <template slot-scope="scope">
          <el-switch @change='(val)=>{switchChange(scope.row.id,val)}' v-model="scope.row.user_type" :active-value="1" :inactive-value="0" :disabled="scope.row.id==1?true:false"></el-switch>
        </template> 
      </el-table-column> -->
      <el-table-column label="是否冻结" align="center">
        <template slot-scope="scope">
          <el-switch @change='(val)=>{switchChange1(scope.row.id,val)}' v-model="scope.row.is_disable" :active-value="0" :inactive-value="1" :disabled="scope.row.id==1?true:false"></el-switch>
        </template> 
      </el-table-column>
     
      <el-table-column label="注册时间" align="center">
        <template slot-scope="scope">
           {{getFormatDate(scope.row.create_time)}}
        </template>
      </el-table-column>

      <el-table-column prop="opt" label="操作" align="center">
        <template slot-scope="scope">
          <el-button type="success" size="mini" @click="openPage({url:'/estates/selectLog',data:{user_id: scope.row.id}})">买房方案</el-button>
        </template>
      </el-table-column>
 
    </el-table>

    <!-- ============分页=============== -->
    <div style="text-align:right">
      <pagination-box :pagination="pagination" @pageChange="pageChange" style="display:inline-block;" ></pagination-box>
      <el-link style="margin-top: 10px;pointer-events:none;">共有 <el-link style="pointer-events:none;margin-top:-2px" type="primary">{{total}}</el-link> 用户</el-link>
    </div>
      
    
    <!-- 编辑弹窗 -->
    <el-dialog class="user-dialog"
      title="用户信息"
      :visible.sync="dialogFormVisible"
      width="700px"
      :close-on-click-modal="false"
    >
      <el-form :model="form" ref="form" >
        <el-row :gutter="15">
          <el-col :lg="7" :sm="12" :xs="24">
            <el-form-item label="用户头像" :label-width="formLabelWidth">
              <el-image :src="form.avatar" style="width:95px"></el-image>
            </el-form-item>
          </el-col>  
          <el-col :lg="17" :sm="12" :xs="24">
            <el-form-item label="用户昵称" :label-width="formLabelWidth" >
              <div>{{form.user_nickname}}</div>
            </el-form-item>
            <el-form-item label="地址信息" :label-width="formLabelWidth" >
              <div>{{form.province+' - '+form.city}}</div>
            </el-form-item>
            <el-form-item label="注册时间" :label-width="formLabelWidth" >
              <div>{{getFormatDate(form.create_time)}}</div>
            </el-form-item>
          </el-col> 
        </el-row>  
         <el-divider content-position="left">{{form.user_nickname}}</el-divider>
        <el-row>
          <el-col :lg="12" :sm="12" :xs="24">
            <el-form-item label="最后登录IP" :label-width="formLabelWidth" >
              <div>{{form.last_login_ip}}</div>
            </el-form-item>
            <el-form-item label="最后登录时间" :label-width="formLabelWidth" >
              <div>{{getFormatDate(form.last_login_time)}}</div>
            </el-form-item>  
          </el-col>
        </el-row>  
        
        
      </el-form>
    </el-dialog>
    
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

      form: {
        user_nickname: "",
        editscore: ''
      },
      searchData: {
        sort:"id",
        user_nickname: "",
        city_no:'',
      },
      searchTime:[],
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据
      caneditSource:false,
      total:0
    };
  },
  created: function(){
    console.log(8888)
    this.getList()
  },
   watch:{
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
  methods: {
 switchChange(id,val){
      //console.log(id,val)
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "user/userChange",
          data: {id: id,user_type: val}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.onSerch()
            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
      });
    },
openPage: util.openPage,
    switchChange1(id,val){
      //console.log(id,val)
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "user/userDisableChange",
          data: {id: id,is_disable: val}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.onSerch()
            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
      });
    },

    showEditSource(){
      this.caneditSource = !this.caneditSource
      if(this.caneditSource === false){
        this.form.editscore = ''
      }
    },
    doEditSource(){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "user/addSource",
          data: {editscore: that.form.editscore, user_id: that.form.id}
      }).then(res => {
          that.page_loading = false
          if(res.code==1){
              util.Message.success('操作成功');
            }else{
              util.Message.error(res.msg);
            }
      });
    },

    getList(searchdata={}){
     
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true
      util.requests("post", {
          url: "user/list",
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


