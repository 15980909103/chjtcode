onUploadedImg(e){
this.formData.cover = e.res.info.url;
this.$refs.cover.clearValidate()
},<template>
  <div class="_container">
    <div class="tb-top" style="float:right;margin-bottom: 30px;">



      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item label="消息标题" :label-width="formLabelWidth" prop="title">
            <el-input style="width:100%" v-model="searchData.title" placeholder="请输入消息标题" ></el-input>
        </el-form-item>

        <el-form-item label="所属分类" :label-width="formLabelWidth" prop="chatType">
          <el-select v-model="searchData.chatType" placeholder="请选择">
            <el-option value="-1" label="全部" key="-1"></el-option>
            <el-option
              v-for="item in chatType"
              :key="item.id"
              :label="item.name"
              :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>

        <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%" row-key="id" :tree-props="{children: 'children', hasChildren: 'hasChildren'}">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="title" label="消息标题" width="180" align="center"></el-table-column>

      <el-table-column prop="sub_context" label="消息概要" width="550" align="center"></el-table-column>

      <el-table-column prop="fordo" label="消息分类" width="150" align="center">
        <template slot-scope="scope">
          <el-tag v-for="item in chatType" v-if="item.id == scope.row.chat_type" :key="item.id">{{item.name}}</el-tag>
        </template>
      </el-table-column>

      <el-table-column  label="操作" align="center">
        <template slot-scope="scope">
          <el-button type="success" size="mini" @click="doEdit(scope.row)">编辑</el-button>
<!--          <el-button type="danger" size="mini" @click="del(scope.row.id,scope.$index)">删除</el-button>-->
        </template>
      </el-table-column>
    </el-table>
    <!-- ============分页=============== -->
    <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>
    <!-- 新增弹窗部分 -->
   <el-dialog
      :title="formData.id?'编辑':'新增'"
      :visible.sync="dialogVisibleEdit"
      width="800px"
      :close-on-click-modal="false"
      @close="doEditCancel('formData')"
    >
      <el-form style="padding-right:50px;" :model="formData" ref="formData" :rules="rules">
        <el-form-item label="消息类型" :label-width="formLabelWidth">
          <el-select v-model="formData.chat_type" placeholder="请选择消息类型">
            <el-option
              v-if="item.id ==1 || item.id==2"
              v-for="item in chatType"
              :key="item.id"
              :label="item.name"
              :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>
        <el-row>
          <el-col :span="24">
            <el-form-item label="消息标题" prop="title" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.title" placeholder="消息标题" ></el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row v-if="formData.id?false:true">
          <el-col :span="24">
              <el-form-item label="发送账号" :label-width="formLabelWidth"  prop="user_ids">
                <el-select
                  style="width: 100%"
                  v-model="formData.user_ids"
                  filterable
                  remote
                  multiple
                  reserve-keyword
                  placeholder="请输入昵称搜索,不选人默认发全部人"
                  :remote-method="remoteMethod"
                  :loading="loading">
                  <el-option
                    v-for="item in user_list"
                    :key="item.id"
                    :label="item.nickname"
                    :value="item.id">
                  </el-option>
                </el-select>
              </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-form-item label="消息图片" :label-width="formLabelWidth" ref="cover" prop="cover">
              <img-upload ref="imgUpload" url="upload/imgUpload" :default_src.sync='default_src'  :uploadedImg="onUploadedImg"></img-upload>
          </el-form-item>
        </el-row>

        <el-row>
          <el-col :lg="24" :sm="24" :xs="24">
            <el-form-item prop="status"  label="状态" :label-width="formLabelWidth">
              <el-select  v-model="formData.status" placeholder="请选择" >
                <el-option label="发布" value="1"></el-option>
                <el-option label="停用" value="2"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="消息内容概要" prop="sub_context" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.sub_context" placeholder="消息标题" ></el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :lg="24" :sm="24" :xs="24">
            <el-form-item prop="context" label="内容详情" required :label-width="formLabelWidth">
              <Tinymce ref="editor" :height="400"   v-model="formData.context" />
            </el-form-item>
          </el-col>
        </el-row>

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="doSubmit('formData')">确 定</el-button>
        <el-button type="danger" @click="doEditCancel('formData')">取 消</el-button>
      </div>
    </el-dialog>


  </div>
</template>
<script>
// import { log } from 'util';
var util = require("@/utils/util.js");
import ImgUpload from '@/components/common/ImgUpload.vue';
import DyTags from '@/components/common/DyTags.vue';
import baseMixin from  '@/mixin/baseMixin';
import Tinymce from '@/components/Tinymce';
import paginationBox from '../../components/common/pagination.vue';

export default {
  components: {
    'img-upload': ImgUpload,
    'Tinymce' : Tinymce,
    'pagination-box': paginationBox,
  },
  mixins: [baseMixin],
  data() {
    var validateImg= (rule, value, callback) => {
      var that = this
      that.$nextTick(function(){
        var field=rule.field
        if(!that.formData[field]){
      　　callback(new Error(rule.message));
  　　　　return false;
        }
        callback();

      })
    };
    return {
      dialogVisibleEdit: false,
      formLabelWidth: "123px",
      src:'',
      default_src:'',
      page_loading:'',
      thumb:{},
      rules: {
        name: [
          { required: true, message: '请输入消息标题名称', trigger: 'change' },
        ],

        //cover: [{ validator: validateImg,  message: "请上传图片" }],
      },

      searchData:{
          chatType :'-1',
          title   :'',
      },
      chatType:[],
      catelist:[],
      tableData: [],
      user_list:[],
      pagination: {}, //分页数据
      loading:false,
      formData:{
        chat_type:'',
        title:'',
        context: '',
        sub_context:'',
        status:'',
        user_ids:[],
        cover:'',
      },

    }
  },
  created: function(){
    this.getSystemType();
    let that = this
    // that.resetData({
    //   formData: this.formData,
    //   default_src: ''
    // },function(){
    //   that.$nextTick(()=>{
    //     that.$refs.imgUpload&&that.$refs.imgUpload.resetData() //重置图片信息
    //     if(that.$refs.editor){//重置富文本信息
    //       that.$refs.editor.setContent('')
    //     }
    //   })
    // })

    that.getList(that.searchData)
  },

  methods:{

    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"Chat/getSystemList",
        data: searchdata
      }).then(res=>{
        that.setDataArr({
          tableData : res.data.list,
          pagination : {
            page : res.data.current_page,
            pagecount : res.data.last_page,
            pagesize : Math.ceil(res.data.total / res.data.last_page)
          }
        })
      })
    },
    remoteMethod(query) {

      let that  = this;
      if (query !== '') {
        let searchData= {
          user_nickname:query
        }
        this.loading = true;
        util.requests("post", {
          url: "user/list",
          data:searchData,
        }).then(res => {
          if(res.code==1){
            that.setDataArr({
              user_list : res.data.list,
            })
            this.loading = false;
          }else{
            this.user_list =[];
            util.Message.error(res.msg);
          }
        });
      } else {
        this.user_list = [];
      }
    },
    getSystemType(){
        let that  = this;
        util.requests("post",{
          url:"chat/getSystemType",
          data: {}
        }).then(res=>{
          that.chatType =res.data;
        })
    },
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },
     resetData(){
      this.setDataArr({
        formData:{
          chat_type:'',
          title:'',
          context: '',
          sub_context:'',
          status:'',
          user_ids:[],
        },
      })

      if(this.$refs.editor){//重置图片信息
        this.$refs.editor.setContent('');
      }
    },
    doEdit(e={}){
      let that = this;
      if(Object.keys(e).length>0){
        console.log(e,234234)
        this.formData = Object.assign({},e);
        this.formData.status = String(this.formData.status)
        if(that.$refs.editor){//重置富文本信息
          that.$refs.editor.setContent(e.context);
        }

        // this.formData.type = String(this.formData.type)
        this.default_src = this.$getRealImgUrl(this.formData.cover)
      }
      this.dialogVisibleEdit = true;
    },
    doEditCancel(formName){
      var that=this
      that.$refs[formName].resetFields()
      that.resetData()
      if(that.dialogVisibleEdit == true){
        that.dialogVisibleEdit = false
      }
      that.$refs.imgUpload&&that.$refs.imgUpload.resetData() //重置图片信息
    },

    //图片上传后操作
    onUploadedImg(e){
      this.formData.cover = e.res.info.url;
      this.$refs.cover.clearValidate()
    },
    onSearch(){
      this.getList(this.searchData);
    },
    switchChange(id,val){
      var that = this
      util.requests("post", {
          url: "news/enableTag",
          data: {id: id,status: val}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
      });
    },

  del(id,val){   //确定删除
      this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          util.requests("post",{
            url: "news/delTag",
            data: {id:id}
          }).then(res => {
              // console.log(res); return;
              if(res.data.code==0){ alert("删除失败："+res.data.msg);return; }
              this.tableData.splice(val,1)
              this.$message({
                type: 'success',
                message: '删除成功!'
              });
          })
        })
  },
    //分页操作
    pageChange: function(page) {
      let post_data = Object.assign({},this.searchData);
      post_data.page = page;
      this.getList(post_data)
    },
    doSubmit(formName){
      var that = this
      that.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.page_loading){
            return;
          }
          that.page_loading = true;
          util.requests("post",{
            url:"chat/sendSystemMsg",
            data:that.formData
          }).then(res=>{
            that.page_loading = false
            //console.log(res)
            if(res.code==1){
               that.$message({ type: 'success', message: '操作成功!' });
               that.dialogVisibleEdit = false;
               that.onSearch()
            }else{
              that.$message({
                type: 'error',
                message: res.msg
              });
            }
          });
        }else{
          console.log('error submit!!');
          return false;
        }
      })
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


