<template>
  <div>
    <div class="tb-top" style="float:right;">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item label="类型">
          <el-select v-model="searchData.category_id" placeholder="请选择">
            <el-option label="全部" value="-1"></el-option>
            <el-option :label="item" v-for="(item,index) in categorys" :key="index" :value="index"></el-option>
          </el-select>
        </el-form-item>

        <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
        <el-button  icon="el-icon-circle-close" @click="openPage({url: '/estates/estatesnew'})">返回</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="cover" label="图" align="center">
        <template slot-scope="scope">
          <el-image v-if="scope.row.cover" style="width: 90px;" :src="getRealImgUrl(scope.row.cover)"></el-image>
        </template>
      </el-table-column>

      <el-table-column prop="type" label="类型" width="150" align="center">
        <template slot-scope="scope">
          <el-tag >{{categorys[scope.row.category_id]}}</el-tag>
        </template>
      </el-table-column>

      <el-table-column  label="操作" align="center">
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
  
        <el-form-item label="类型" prop="category_id" :label-width="formLabelWidth">
          <el-select v-model="formData.category_id" placeholder="请选择">
            <el-option :label="item" v-for="(item,index) in categorys" :key="index" :value="index"></el-option>
          </el-select>
        </el-form-item>

        <el-form-item label="上传封面" :label-width="formLabelWidth" ref="cover" prop="cover">
          <img-upload2 ref="imgUpload" url="upload/imgUpload" :thumb='{ isthumb:1, width:750, height:750 }' :show-file-list="false" :fileList.sync="formData.cover_url" :imgIds.sync="formData.cover_id" :uploadedImg="onUploadedImg"></img-upload2>
          <!-- <img-upload ref="imgUpload" url="upload/imgUpload" :thumb='{isthumb:1,width:750,height:750}' :default_src.sync='default_src' :uploadedImg="onUploadedImg" ></img-upload> -->
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
import ImgUpload2 from '@/components/common/ImgUpload2.vue';

export default {
  components: {
    'img-upload': ImgUpload,
    'img-upload2': ImgUpload2,
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
        category_id: [
          { required: true, message: '请选择类型', trigger: 'change' },
        ],
        // cover: [{ validator: validateImg,  message: "请上传图片" }],
      },
      searchData:{
          estate_id:'',
          category_id:'-1'
      },
      tableData: [],
      formData:{
        category_id: '',
        cover:'',
        cover_url:[],
        cover_id:[],
        estate_id: '',
      },
      categorys:[],
      estate_id:'',
    }
  },
  created: function(){
    let that = this
    that.resetData({
      formData: this.formData,
      default_src: ''
    },function(){
      that.$nextTick(()=>{
        that.$refs.imgUpload&&that.$refs.imgUpload.resetData() //重置图片信息
      })
    })
    that.estate_id = that.$urlData.id;
    that.searchData.estate_id = that.$urlData.id;

    that.getBuildingPhotosCategory()
    that.getList(that.searchData)
  },

  methods:{
    getBuildingPhotosCategory(){
      var that = this
      util.requests("post",{
        url:"estates/getBuildingPhotosCategory",
      }).then(res=>{
        that.categorys = res.data
      })
    },
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"estates/getBuildingPhotosList",
        data: searchdata
      }).then(res=>{
        //console.log(res.data.list)
        that.tableData = res.data.list
      })
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
        this.formData.category_id = String(this.formData.category_id)
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
    },

    //图片上传后操作
    onUploadedImg(e){
      this.formData.cover = e.res.info.url;
      this.$refs.cover.clearValidate()
    },
    onSearch(){
      this.getList(this.searchData);
    },
 

  del(id,val){   //确定删除
      this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          util.requests("post",{
            url: "estates/delBuildingPhotos",
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

      that.formData.estate_id = that.estate_id
      that.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.page_loading){
            return;
          }
          that.page_loading = true;
          util.requests("post",{
            url:"estates/editBuildingPhotos",
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

    openPage: util.openPage

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


