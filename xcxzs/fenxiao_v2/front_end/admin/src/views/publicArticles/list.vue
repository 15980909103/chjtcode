<template>
  <div class="_container">
    <div class="tb-top" style="float:right;margin-bottom: 30px;">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item label="状态">
          <el-select v-model="searchData.status" placeholder="请选择">
            <el-option label="全部" value="0"></el-option>
            <el-option label="启用" value="1"></el-option>
            <el-option label="禁用" value="2"></el-option>
          </el-select>
        </el-form-item>


        <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="title" label="标题名称" width="180" align="center"></el-table-column>
      <el-table-column prop="status" label="状态" align="center">
        <template slot-scope="scope">
            <el-switch @change="(val)=>{switchChange(scope.row.id,val)}" v-model="scope.row.status" :active-value="1" :inactive-value="2" ></el-switch>
        </template>
      </el-table-column>
       <el-table-column prop="create_time" label="创建时间" width="180" align="center"></el-table-column>



      <el-table-column  label="操作" align="center">
        <template slot-scope="scope">
          <el-button type="success" size="mini" @click="doEdit(scope.row)">编辑</el-button>
          <el-button type="danger" size="mini" @click="del(scope.row.id,scope.$index)">删除</el-button>
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
        <el-row>
          <el-col :span="24">
            <el-form-item label="标题名称" prop="title" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.title" placeholder="请输入名称" ></el-input>
            </el-form-item>
          </el-col>
          
        </el-row>
              <el-row :gutter="60" class="mapeditor-content">
            
                  <el-col :span="24">
                    <el-form-item prop="context" label="内容详情" required :label-width="formLabelWidth">
                      <Tinymce v-if="dialogVisibleEdit" ref="editor" v-model="formData.context" />
                    </el-form-item>
                  </el-col>
      
            </el-row>


        <el-form-item label="状态" :label-width="formLabelWidth">
           <el-radio-group v-model="formData.status">
             <el-radio  label="1">启用</el-radio>
             <el-radio  label="2">禁用</el-radio>
           </el-radio-group>
        </el-form-item>

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
import paginationBox from '@/components/common/pagination.vue';
import Tinymce from '@/components/Tinymce';
export default {
  components: {
    'img-upload': ImgUpload,
    'pagination-box': paginationBox,
     'Tinymce' : Tinymce
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
          { required: true, message: '请输入标签名称', trigger: 'change' },
        ],

        //cover: [{ validator: validateImg,  message: "请上传图片" }],
      },
      searchData:{
          status : '0',
          searchData:'-1'
      },
      pagination: {}, //分页数据
      catelist:[],
      tableData: [],
      formData:{
        name:'',
       context:'',
       title:'',
        status: '1',
        context:'',
      }
    }
  },
  created: function(){
    this.getCateList();
    let that = this
    that.resetData({
      formData: this.formData
    })

    that.getList(that.searchData)
  },

  methods:{
     pageChange: function(page) {
      let post_data = Object.assign({},this.searchData);
      post_data.page = page;
      this.getList(post_data)
    },
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"PublicArticles/list",
        data: searchdata
      }).then(res=>{
        //console.log(res.data.list)
      
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
    getCateList(){
      let that = this;
      util.requests("get", {
        url: "news/getCateList",
        data:{pid:0,flag:'fx'}
      }).then(res => {
        that.page_loading = false
        if(res.code==1){
          that.setDataArr({
            catelist : res.data,
          });
        }else{
          util.Message.error(res.msg);
        }
      });
    },
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },

    doEdit(e={}){
      console.log(888,this.formData)
      if(Object.keys(e).length>0){
        this.formData = Object.assign({},e);
        this.formData.status = String(this.formData.status)
        this.formData.type = String(this.formData.type)
      
      }
       console.log(999,this.formData)
      this.dialogVisibleEdit = true;
    },
    doEditCancel(formName){
      var that=this
      if(that.dialogVisibleEdit == true){
        that.dialogVisibleEdit = false
      }
      
      setTimeout(() => {
        that.$refs[formName].resetFields()
        that.resetData()
      }, 20);
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
          url: "PublicArticles/edit",
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
            url: "PublicArticles/delete",
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

    doSubmit(formName){
  
      var that = this
      that.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.page_loading){
            return;
          }
          that.page_loading = true;
          util.requests("post",{
            url:"PublicArticles/edit",
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
.pagination {
    margin-top: 20px;
    text-align: right;
  }
</style>


