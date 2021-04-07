<template>
  <div class="_container">
    <el-form :inline="true" :model="searchData" class="form-serch">
        <el-form-item label="商户名称" label-width="70px">
          <el-input placeholder="请输入商户名称" v-model="searchData.name"></el-input>
        </el-form-item>
        <el-form-item label="账号" label-width="70px">
          <el-input placeholder="请输入账号" v-model="searchData.account"></el-input>
        </el-form-item>
        <el-form-item label="联系电话" label-width="70px">
          <el-input placeholder="请输入联系电话" v-model="searchData.mobile"></el-input>
        </el-form-item>
        
        <el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
        </el-form-item>

        <el-form-item>
        <el-button
            type="danger"
            icon="el-icon-circle-plus-outline"
            @click="doEdit()"
          >新增</el-button>
        </el-form-item>
        
    </el-form>

    <el-table :data="tableData" style="width:100%">
      <el-table-column prop="id" label="ID" width="80" align="center"></el-table-column>
      <el-table-column prop="account" label="账号" align="center"></el-table-column>
      <el-table-column prop="name" label="商户名称" align="center"></el-table-column>
      <el-table-column prop="mobile" label="联系电话" align="center"></el-table-column>
      <el-table-column label="微信公众号配置信息" align="center">
        <template slot-scope="scope">
          <div v-if="scope.row.wx_setting&&scope.row.wx_setting.h5">
            <div>Appid: {{scope.row.wx_setting.h5.appid}}</div>
            <div>Secret: {{scope.row.wx_setting.h5.secret}}</div>
            <div>Token: {{scope.row.wx_setting.h5.token}}</div>
          </div>
        </template>
      </el-table-column>

      <el-table-column label="状态" width="80" align="center">
        <template slot-scope="scope">
          <el-switch @change='(val)=>{switchChange(scope.row.id,val)}' v-model="scope.row.status" :active-value="1" :inactive-value="0" ></el-switch>
        </template> 
      </el-table-column>

  
      <el-table-column prop="code" label="操作"  align="center">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="doEdit({...scope.row})">编辑</el-button>
          <el-button type="danger" size="mini" v-iscan='"merchant/del"' @click="doDel(scope.row.id)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <!-- ============分页=============== -->
    <div style="text-align:right">
      <pagination-box :pagination="pagination" @pageChange="pageChange" style="display:inline-block;" ></pagination-box>
      <el-link style="margin-top: 10px;pointer-events:none;">共有 <el-link style="pointer-events:none;margin-top:-2px" type="primary">{{total}}</el-link> </el-link>
    </div>

    <!-- 编辑弹窗 -->
    <el-dialog v-if="dialogFormVisible"
      :title="!form.id?'新增':'编辑'"
      :visible.sync="dialogFormVisible"
      width="800px"
      :close-on-click-modal="false"
      @close="doEditCancel"
    >
      <el-form style="padding-right:50px;" :model="form" ref="form" >
        <el-form-item  label="商户名称" :label-width="formLabelWidth">
          <el-input v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item label="联系电话" :label-width="formLabelWidth" prop="mobile">
          <el-input v-model="form.mobile"></el-input>
        </el-form-item>
        <el-form-item label="邮箱" :label-width="formLabelWidth" prop="mobile">
          <el-input v-model="form.email"></el-input>
        </el-form-item>

        <el-form-item label="账号" :label-width="formLabelWidth">
          <el-input :disabled="form.id?true:false" v-model="form.account"></el-input>
        </el-form-item>
        <el-form-item label="密码" :label-width="formLabelWidth">
          <el-input v-model="form.newpassword"></el-input>
        </el-form-item>
        <el-form-item label="确认密码" :label-width="formLabelWidth">
          <el-input v-model="form.newpassword2"></el-input>
        </el-form-item>

        <el-form-item label="微信公众号Appid" :label-width="formLabelWidth">
          <el-input v-model="form.wx_setting.h5.appid"></el-input>
        </el-form-item>
        <el-form-item label="微信公众号Secret" :label-width="formLabelWidth">
          <el-input v-model="form.wx_setting.h5.secret"></el-input>
        </el-form-item>
        <el-form-item label="微信公众号Token" :label-width="formLabelWidth">
          <el-input v-model="form.wx_setting.h5.token"></el-input>
        </el-form-item>

        <el-form-item label="状态" :label-width="formLabelWidth">
          <el-radio v-model="form.status" label="1">开启</el-radio>
          <el-radio v-model="form.status" label="0">禁用</el-radio>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="doEditSubmit">确 定</el-button>
        <el-button type="danger" @click="doEditCancel">取 消</el-button>
      </div>
    </el-dialog>


  </div>
</template>
<script>
var util = require("@/utils/util.js");
import paginationBox from '@/components/common/pagination.vue';
import exportexcelBox from '@/components/common/exportexcel.vue';
import { isNumber } from 'util';
import baseMixin from  '@/mixin/baseMixin';

export default {
  components: {
    'pagination-box': paginationBox,
    'exportexcel-box': exportexcelBox,
  },
  mixins: [baseMixin],
  data() {
    return {
      attrs:[],
      dialogFormVisible: false,
      formLabelWidth: "130px",

      exportExcelInt:{
        progressShow:false,
        percentage:0,
        loading:false,
        tocancel: false,
      },
      host:this.$baseconfig.host,

      form: {
        id:0,
        name:'',
        mobile: '',
        wx_setting:{
          h5:{}
        },
        account:'',
        newpassword:'',
        newpassword:'',
        //start_time: util.DataFun.getFormatDate(),
        status: '1',
        role_id: -1,
      },
      searchData: {
        sort:"id",
        name: "",
        account:'',
        mobile:'',
      },
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据
      total:0
    };
  },
  computed:{
      
  },
  created: function(){
    this.getList()
    this.resetData({
      form:this.form,
    })

  },
  methods: {
    getList(searchdata={}){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "merchant/merchantList",
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
    
  
    doEditCancel(){
      var that=this
      that.resetData()
      if(that.dialogFormVisible == true){
        that.dialogFormVisible = false
      }
      console.log(that.form)
    },
    doEdit(e={}){
      let that = this
      that.dialogFormVisible = true
      if(Object.keys(e).length>0){
        if(!e.wx_setting){
          e.wx_setting = {}
        }
        if(!e.wx_setting.h5){
          e.wx_setting.h5 ={
            appid:'',
            secret:'',
            token:'',
          }
        }

        that.form = Object.assign({},e)
        that.form.status = String(that.form.status)
      }
    },
    doEditSubmit(){
      let that=this
      that.$refs['form'].validate((valid) => {
        if (valid) {
          util.requests("post", {
            url: "merchant/merchantEdit",
            data: that.form
          }).then(res => {
              that.page_loading = false
              if(res.code==1){
                util.Message.success('操作成功');
                that.getList()
                setTimeout(() => {
                  that.doEditCancel()
                }, 1000);
              }else{
                util.Message.error(res.msg);
              }
          });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },

    switchChange(id,val){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "merchant/merchantEnable",
          data: {id: id,status: val}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.getList()
            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
      });
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
    .el-input{
      width: 170px;
    }
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


