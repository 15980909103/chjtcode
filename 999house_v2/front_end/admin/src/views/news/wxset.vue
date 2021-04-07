<template>
  <div class="_container">
    <div class="tb-top" style="float:right;margin-bottom: 30px;">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item label="状态">
          <el-input v-model="searchData.name" placeholder="请输入文章名称" prefix-icon="el-icon-search"></el-input>
        </el-form-item>
<!--        <el-form-item label="模块类型" :label-width="formLabelWidth" prop="p_cate_id">-->
<!--          <el-select v-model="searchData.p_cate_id" placeholder="请选择">-->
<!--            <el-option value="-1" label="全部" key="-1"></el-option>-->
<!--            <el-option-->
<!--              v-for="item in catelist"-->
<!--              :key="item.id"-->
<!--              :label="item.title"-->
<!--              :value="item.id">-->
<!--            </el-option>-->
<!--          </el-select>-->
<!--        </el-form-item>-->


        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增素材</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="name" label="名称" width="180" align="center"></el-table-column>
      <el-table-column prop="media_id" label="微信资源标识" width="360" align="center"></el-table-column>

      <el-table-column prop="type" label="类型" width="150" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.type == 1" >图片</el-tag>
          <el-tag v-else-if="scope.row.type == 2" >图文</el-tag>
          <el-tag  v-else-if="scope.row.type == 3" >视频</el-tag>
        </template>
      </el-table-column>

      <el-table-column  label="操作" align="center">
        <template slot-scope="scope">
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
         <el-col :span="24" >
           <span @click="changeInnerShow">
               <el-form-item label="请选择需上传文章" prop="name" :label-width="formLabelWidth">
                 <el-input style="width:100% ;display: none" :disabled="true"  v-model="formData.id"  placeholder="请输入内容" ></el-input>
                 <el-input style="width:100%"   v-model="formData.name"  placeholder="请输入内容" ></el-input>
               </el-form-item>
         </span>
         </el-col>
       </el-row>
       <Article :show.sync='innerVisible' @innerFormData="innerFormData"> </Article>
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
import Article from '@/components/page/Article.vue';
import baseMixin from  '@/mixin/baseMixin';

export default {
  components: {
    'img-upload': ImgUpload,
    'Article'   : Article
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
      formLabelWidth: "150px",
      src:'',
      default_src:'',
      page_loading:'',
      innerVisible:false,
      thumb:{},
      rules: {

      },
      searchData:{
          status : '-1',
          searchData:'-1'
      },

      catelist:[],
      tableData: [],
      formData:{
        name:'',
        id:'',
      }
    }
  },
  created: function(){
    this.getCateList();
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
        url:"Wxset/getMaterialData",
        data: searchdata
      }).then(res=>{
        that.tableData = res.data.list
      })
    },
    innerFormData(e){
      this.formData.id = e.id;
      this.formData.name= e.name
    },
    changeInnerShow(){
      this.innerVisible = true
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
        //this.default_src = this.$getRealImgUrl(this.formData.cover)
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
            url: "Wxset/deleteWxFile",
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
            url:"Wxset/uploadWxFile",
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


