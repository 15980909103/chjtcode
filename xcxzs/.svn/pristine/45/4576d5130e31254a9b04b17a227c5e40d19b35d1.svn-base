<template>
  <div class="_container">
    <el-form :model="searchData">
      <el-row :gutter="30">
        <el-col :xl="6" :md="8" :xs="24">
          <el-form-item label="账号" label-width="60px">
            <el-input v-model="searchData.account"></el-input>
          </el-form-item>
        </el-col>

        <el-col :xl="6" :md="6" :xs="24" style="float:right;text-align:right;">
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
          <el-button
            type="danger"
            icon="el-icon-circle-plus-outline"
            @click="doEdit('add')"
          >新增</el-button>
        </el-col>
      </el-row>
    </el-form>

    <el-table :data="tableData" style="width:100%" v-loading="page_loading">
      <el-table-column prop="id" label="ID" width="100" align="center"></el-table-column>
      <el-table-column prop="account" label="账号" align="center"></el-table-column>
      <el-table-column prop="mobile" label="手机号" align="center"></el-table-column>
      <el-table-column prop="email" label="邮箱" align="center"></el-table-column>
      <el-table-column prop="role_name" label="用户角色" align="center"></el-table-column>
      <el-table-column label="状态" align="center">
        <template slot-scope="scope">
          <el-switch @change='(val)=>{switchChange(scope.row.id,val)}' v-model="scope.row.status" :active-value="1" :inactive-value="0" :disabled="scope.row.id==1?true:false"></el-switch>
        </template>
      </el-table-column>
      <el-table-column prop="last_login_ip" label="最后登录IP" align="center"></el-table-column>
      <el-table-column label="最后登录时间" align="center">
        <template slot-scope="scope">
          <span>{{getFormatDate(scope.row.last_login_time,2)}}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作"  align="center" width="200">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="doEdit('edit',{ id:scope.row.id, mobile:scope.row.mobile, email:scope.row.email, role_id:scope.row.role_id, status:scope.row.status, region_nos_info:scope.row.region_nos_info,head_ico_path:scope.row.head_ico_path,head_ico_id:scope.row.head_ico_id })">编辑</el-button>
          <el-button type="danger" size="mini" @click="scope.row.id!=1?doDel(scope.row.id):''" :disabled="scope.row.id==1">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <!-- ============分页=============== -->
    <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>

    <!-- 编辑用户弹窗 -->
    <el-dialog v-if="dialogFormVisible"
      :title="form_type=='add'?'新增用户':'编辑用户'"
      :visible.sync="dialogFormVisible"
      width="800px"
      :close-on-click-modal="false"
      @close="doEditCancel"
    >
      <el-form style="padding-right:50px;" :model="form" ref="form" :rules="rules">
        <el-form-item v-if="form_type=='add'" label="账号" :label-width="formLabelWidth" prop="account" >
          <el-input v-model="form.account"></el-input>
        </el-form-item>
        <el-form-item label="手机号" :label-width="formLabelWidth"  prop="mobile">
          <el-input v-model="form.mobile"></el-input>
        </el-form-item>
        <el-form-item label="邮箱" :label-width="formLabelWidth"  prop="email">
          <el-input v-model="form.email"></el-input>
        </el-form-item>
        <el-form-item label="密码"  :label-width="formLabelWidth"  prop="newpassword">
          <el-input v-model="form.newpassword"></el-input>
        </el-form-item>
        <el-form-item label="确认密码"  :label-width="formLabelWidth"  prop="newpassword2">
          <el-input v-model="form.newpassword2"></el-input>
        </el-form-item>
<!--          <el-form-item label="用户头像" :label-width="formLabelWidth" ref="cover" prop="cover">-->
<!--            <img-upload ref="imgUpload" url="upload/imgUpload" :default_src.sync='form.head_ico_path' :uploadedImg="onUploadedImg" ></img-upload>-->
<!--          </el-form-item>-->

        <el-form-item v-if="form.id != 1" class="city-box" label="区域"  :label-width="formLabelWidth"  >
            <el-select v-model="form.region_nos_info2" multiple placeholder="请选择">
              <el-option
                v-for="(item) in siteCitys"
                :key="item.id"
                :label="item.cname"
                :value="item.id">
              </el-option>
            </el-select>
        </el-form-item>

        <el-form-item v-if="form.id != 1" label="勾选角色"  :label-width="formLabelWidth" >
          <div >
            <el-radio v-model="form.role_id" :label="-1" border >超级管理员</el-radio>
            <el-radio v-model="form.role_id" v-for="item in roleList" :key="item.id" :label="item.id" border >{{item.name}}</el-radio>
          </div>
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
      siteCitys:{},
      dialogFormVisible: false,
      formLabelWidth: "123px",
      form_type:'',
      form: {
        account: "",
        mobile: "",
        email: "",
        head_ico_id:'',
        head_ico_path:'',
        newpassword: "",
        newpassword2: "",
        role_id:'',
        status:'',
        region_nos_info:{},
        region_nos_info2:[]
      },
      rules: { },

      searchData: {
        account: ""
      },
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据

      roleList:[{id:-1,name:'超级管理员'}],
    };
  },
  watch:{
    form_type(val){
      var validatePass = (rule, value, callback) => {
        value=util.trim(value)
        if (value.length <6 ) {
          callback(new Error('请输入至少6位密码'));
        } else {
          if (this.form.newpassword2 !== '') {
            this.$refs.form.validateField('newpassword2');
          }
          callback();
        }
      };
      var validatePass2 = (rule, value, callback) => {
        value=util.trim(value)
        if (value === '') {
          callback(new Error('请再次输入密码'));
        } else if (value !== this.form.newpassword) {
          callback(new Error('两次输入的新密码不一致'));
        } else {
          callback();
        }
      };

      if(val=='add'){
        this.rules={
          account: [
              { required: true, message: '请输入账号', trigger: 'change' },
            ],
          newpassword: [
            { required: true, message: '请输入新密码', trigger: 'change' },
            { validator: validatePass, trigger: 'change' }
          ],

          newpassword2: [
            { required: true, message: '请再次输入新密码', trigger: 'change' },
            { validator: validatePass2, trigger: 'change' }
          ]
        }
      }else{
        this.rules={ }
      }
    }
  },
  created: function() {
    this.getList()
    this.getRoleList()
    this.getSiteCitys()
  },
  methods: {
    getSiteCitys(){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"city/siteCitys",
        data:{status:1}
      }).then(res=>{
        let obj = {}
        for(var i in res.data){
          let item = res.data[i]
          obj[item.id] = item
        }
        that.siteCitys = obj
      })
    },

    onUploadedImg(e){
      this.form.head_ico_id      = e.res.info.id;
      this.form.head_ico_path  = e.res.info.url;
      this.$refs.cover.clearValidate()
    },
    getList(searchdata={}) {
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "account/index",
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
    getRoleList() {
      var that = this
      util.requests("post", {
          url: "role/index",
      }).then(res => {
          //console.log(res.data)
          if(res.code==1){
            that.setDataArr({
              roleList : res.data.list
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    //分页操作
    pageChange: function(page) {
      let post_data = Object.assign({},this.searchData);
      post_data.page = page;
      this.getList(post_data)
    },
    onSerch() {
      //console.log("查询",this.searchData);
      this.getList(this.searchData)
    },


    resetData(){
      this.setDataArr({
        form: {
          account: "",
          mobile: "",
          email: "",
          newpassword: "",
          newpassword2: "",
          role_id:'',
          status:'',
          region_nos_info:{},
          region_nos_info2:[]
        }
      })
    },
    doEditCancel(){
      var that=this
      that.resetData()
      if(that.dialogFormVisible == true){
        that.dialogFormVisible = false
      }
    },
    doEdit(dotype,e={}){
      let that = this
      that.dialogFormVisible = true
      that.form_type = dotype
      if(Object.keys(e).length>0){
        e.region_nos_info2=[]
        that.form = Object.assign({},e)
        that.form.role_id = Number(that.form.role_id)
        that.form.status = String(that.form.status)

        if(that.form.region_nos_info){
          that.form.region_nos_info = JSON.parse(that.form.region_nos_info)

          for(var i in that.form.region_nos_info){
            that.form.region_nos_info2[i] = Number(that.form.region_nos_info[i].id)
          }
        }
      }
    },
    doEditSubmit(){
      let that=this
      that.form.region_nos_info ={}

      for(var i in that.form.region_nos_info2){
        let item = that.siteCitys[that.form.region_nos_info2[i]]
        that.form.region_nos_info[i] = {id:item.id,cname:item.cname,pid:item.pid,pcname:item.pcname}
      }

      that.$refs['form'].validate((valid) => {
        if (valid) {
          util.requests("post", {
            url: "account/edit",
            data: that.form
          }).then(res => {
              that.page_loading = false
              if(res.code==1){
                util.Message.success('操作成功');
                that.onSerch()
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
    doDel(id){
      var that = this
      if(id==1 || that.page_loading){
          return
      }
      that.$confirm('该操作将永久删除记录，是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        that.page_loading = true

        util.requests("post", {
            url: "account/del",
            data: {id: id}
          }).then(res => {
            that.page_loading = false
            if(res.code==1){
              that.onSerch()
              util.Message.success('操作成功');
            }else{
              util.Message.error(res.msg);
            }
        });
      }).catch(() => {
      });
    },
    switchChange(id,val){
      //console.log(id,val)
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "account/enable",
          data: {id: id,status: val}
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
    getFormatDate:util.DataFun.getFormatDate
  }
};
</script>
<style lang="scss" scoped>
._container {
  margin-top: 20px;
  padding: 20px;
  background: #fff;
}
.el-radio{
  margin-right: 5px;
}
</style>


