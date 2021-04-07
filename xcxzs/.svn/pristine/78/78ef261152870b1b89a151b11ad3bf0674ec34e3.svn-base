<template>
  <div class="_container">
    <div class="tb-top" style="float:right;margin-bottom: 30px;">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item label="状态">
          <el-select v-model="searchData.status" placeholder="请选择">
            <el-option label="全部" value="-1"></el-option>
            <el-option label="启用" value="1"></el-option>
            <el-option label="禁用" value="0"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="父及栏目" prop="pid">
          <select-tree
              style='width="100%"'
              :clearable='true'
              :data="catelist"
              :defaultProps="defaultProps"
              :checkedKeys="defaultCheckedKeys1"
              @change='popoverHide1'
          >
          </select-tree>
        </el-form-item>
        <el-form-item label="类型">
          <el-select v-model="searchData.type" placeholder="请选择">
            <el-option label="全部" value="-1"></el-option>
            <el-option label="H5端" value="0"></el-option>
          </el-select>
        </el-form-item>

        <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%"  row-key="id" :tree-props="{children: 'children', hasChildren: 'hasChildren'}">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="title" label="title" width="180" align="center"></el-table-column>
      <el-table-column prop="cover" label="icon图" width="180" align="center">
        <template slot-scope="scope">
          <el-image v-if="scope.row.cover" style="width: 90px;" :src="getRealImgUrl(scope.row.cover)"></el-image>
        </template>
      </el-table-column>
      <el-table-column prop="status" label="状态" align="center">
        <template slot-scope="scope">
            <el-switch @change="(val)=>{switchChange(scope.row.id,val)}" v-model="scope.row.status" :active-value="1" :inactive-value="0" ></el-switch>
        </template>
      </el-table-column>

      <el-table-column prop="sort" label="排序" width="100" align="center"></el-table-column>

      <el-table-column prop="type" label="类型" width="150" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.type==0">H5端</el-tag>
        </template>
      </el-table-column>

      <el-table-column prop="place" label="使用位置"  align="center"></el-table-column>
      <!-- <el-table-column prop="p_title"  label="父模块"  align="p_title" > -->

      </el-table-column>
      <el-table-column prop="opt" label="操作" align="center">
        <template slot-scope="scope">
          <el-button type="success" size="mini" @click="doEdit(scope.row)">编辑</el-button>
          <el-button type="danger" size="mini" @click="del(scope.row.id,scope.$index)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

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
          <el-col>
            <el-form-item label="父及栏目" :label-width="formLabelWidth" prop="pid">
              <select-tree

                           style='width="100%"'
                           :data="catelist"
                           :defaultProps="defaultProps"
                           :checkedKeys="defaultCheckedKeys"
                           @popoverHide='popoverHide'
              >
              </select-tree>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="栏目名称" prop="title" :label-width="formLabelWidth">
              <el-input style="width:100%"  v-model="formData.title" placeholder="请输入内容"></el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="24" v-if="formData.pid ==0">
            <el-form-item label="跳转地址" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.href" placeholder="请输入内容"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="排序" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.sort" placeholder="请输入内容"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
         <div v-if="(formData.place =='h5_fx_home' && formData.pid != '0') || (formData.place =='h5_fx_home' && formData.id == '13') "  v-for="(tag,index) in tagList">

            <el-row v-if="tag.children">
              <el-divider><span style="color: #5A6066;font-size: 20px">{{tag.name}}</span></el-divider>
              <el-col :span="24">
                <el-form-item :label="tag.name +':'" :label-width="formLabelWidth">
                  <el-checkbox-group v-model="selected_tags" size="small">
                    <el-checkbox  prop="lable" v-for="item in tag.children" :label="item.id" :key="item.id">
                                 {{item.name}}
                    </el-checkbox>
                  </el-checkbox-group>
                </el-form-item>
              </el-col>
            </el-row>
        </div>

        <el-row>

          <el-col :span="24">
            <el-form-item label="分组标识" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.place" placeholder="请输入内容"></el-input>
            </el-form-item>
          </el-col>
        </el-row>


        <el-form-item label="页面标题" :label-width="formLabelWidth">
          <el-input  v-model="formData.page_title" placeholder="请输入内容"></el-input>
        </el-form-item>
        <el-form-item label="页面关键字" :label-width="formLabelWidth">
          <el-input  v-model="formData.page_keywords" placeholder="请输入内容"></el-input>
        </el-form-item>
        <el-form-item label="页面描述" :label-width="formLabelWidth">
          <el-input type="textarea" :autosize="{ minRows: 2}" v-model="formData.page_desc" placeholder="请输入内容"></el-input>
        </el-form-item>

        <el-form-item label="上传图片" :label-width="formLabelWidth" ref="cover" prop="cover">
          <img-upload ref="imgUpload" url="upload/imgUpload" :default_src.sync='default_src' :uploadedImg="onUploadedImg" ></img-upload>
        </el-form-item>

        <el-form-item label="类型" :label-width="formLabelWidth">
          <el-radio v-model="formData.type" label="0">H5端</el-radio>
        </el-form-item>
        <el-form-item label="状态" :label-width="formLabelWidth">
           <el-radio-group v-model="formData.status">
             <el-radio  label="1">启用</el-radio>
             <el-radio  label="0">禁用</el-radio>
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
import SelectTree from '@/components/common/SelectTree.vue'

export default {
  components: {
    'img-upload': ImgUpload,
    'select-tree': SelectTree,
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
        title: [
          { required: true, message: '请输入栏目名称', trigger: 'change' },
        ],

        //cover: [{ validator: validateImg,  message: "请上传图片" }],
      },
      searchData:{
          status : '-1',
          type:'-1',
          pid:''
      },
      tableData: [],
      catelist:[],
      defaultProps: {
        children: 'children',
        label: 'title'
      },
      tagList:[],
      formData:{
        type: '0',
        pid:'0',
        title:'',
        place: '',
        status: '0',
        sort: '0',
        cover: '',
        tags:'',
        href: '',
        page_title:'',
        page_keywords:'',
        page_desc:''
      },
      selected_tags:[]
    }
  },
  watch: {
    selected_tags(val){
      this.formData.tags = val.join(',')
      // console.log(this.selected_tags);
    },
  },
  computed:{
    defaultCheckedKeys(){
      return [this.formData.pid]
    },
    defaultCheckedKeys1(){
      return [this.searchData.pid]
    }
  },
  created: function(){
    this.getCateList();
    this.getTagList();
    let that = this
    that.resetData({
      formData: this.formData,
      default_src: ''
    },function(){
      that.$nextTick(()=>{
        that.$refs.imgUpload&&that.$refs.imgUpload.resetData() //重置图片信息
      })
    })

    that.getList(that.searchData)
  },

  methods:{
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"column/getList",
        data: searchdata
      }).then(res=>{
        //console.log(res.data.list)
        that.tableData = res.data.list
      })
    },

    popoverHide(val){
      console.log(val)
      this.formData.pid = val
    },


    popoverHide1(val){
      this.searchData.pid = val
    },

    getCateList(){
      let that = this;
      util.requests("get", {
        url: "column/getCategoryListAll",
        // data:{pid:0}
      }).then(res => {
        that.page_loading = false
        if(res.code==1){
          that.setDataArr({
            catelist : res.data,
          });
          console.log(this.catelist);
        }else{
          util.Message.error(res.msg);
        }
      }).catch(res=>{
        that.page_loading = false
      });
    },
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },
    /* resetData(){
      this.setDataArr({
        formData:{
          type:'0',
          place:'',
          status:'0',
          sort:'0',
          cover:'',
        },
        default_src:''
      })

      if(this.$refs.imgUpload){//重置图片信息
        this.$refs.imgUpload.resetData()
      }
    }, */
    doEdit(e={}){
      if(Object.keys(e).length>0){
        this.formData = Object.assign({},e);
        this.formData.status = String(this.formData.status)
        this.formData.type = String(this.formData.type)
        this.formData.pid  = Number(this.formData.pid)
        this.default_src = this.$getRealImgUrl(this.formData.cover)
        var selected_tags= []
        if(this.formData.tags){//标签数据转换
          selected_tags = this.formData.tags.split(',')
        }
        for (let i=0;i<selected_tags.length;i++){
          selected_tags[i]  = Number(selected_tags[i])
        }
        this.selected_tags = selected_tags;
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
    },
    getTagList(){
      var that = this
      util.requests("post",{
        url:"news/getTagList",
        data: {pid:-1,name:'',status:-1}
      }).then(res=>{
        console.log(res.data)
        that.tagList = res.data
      });
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
          url: "column/enable",
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
            url: "column/del",
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
            url:"column/edit",
            data:that.formData
          }).then(res=>{
            that.page_loading = false
            //console.log(res)
            if(res.code==1){
               that.$message({ type: 'success', message: '操作成功!' });
               that.dialogVisibleEdit = false;
               that.onSearch()
               that.getCateList()
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


