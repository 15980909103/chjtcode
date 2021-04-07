<template>
  <div class="_container">
    <div class="tb-top" >
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item label="状态">
          <el-select v-model="searchData.status" placeholder="请选择">
            <el-option label="全部" value="-1"></el-option>
            <el-option label="启用" value="1"></el-option>
            <el-option label="禁用" value="0"></el-option>
          </el-select>
        </el-form-item>

        <el-form-item>
          <mycity-select :city_no.sync='searchData.region_no' :unlimitedCity='true' :isMy='true' model='3' siteAreasUrl='city/siteAreas'></mycity-select>
        </el-form-item>

        <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="name" label="活动名称"  align="center"></el-table-column>
      <el-table-column prop="type" label="活动时间"  align="center" width="280">
        <template slot-scope="scope">
            {{getFormatDate(scope.row.start_time)}} -- {{getFormatDate(scope.row.end_time)}}
        </template>
      </el-table-column>

      <el-table-column prop="status" label="状态" align="center" width="120">
        <template slot-scope="scope">
            <el-switch @change="(val)=>{switchChange(scope.row.id,val)}" v-model="scope.row.status" :active-value="1" :inactive-value="0" ></el-switch>
        </template>
      </el-table-column>

      <el-table-column prop="opt" label="操作" align="center">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="openPage({url:'/marketing/voteDetail',data:{vote_id: scope.row.id, region_no:scope.row.region_no }})">模块配置</el-button>
          <el-button type="primary" size="mini" @click="showThat(scope)" >复制链接</el-button>
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
          <el-col :span="12">
            <el-form-item label="活动名称" prop="name" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.name" placeholder="请输入内容"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <mycity-select @changeCity='changeCity' :label-width="formLabelWidth" :city_no.sync='formData.region_no' :isMy='true' model='3' siteAreasUrl='city/siteAreas'></mycity-select>
          </el-col>
        </el-row>     

        <el-row>
          <el-col :span="12">
            <el-form-item label="页面标题" :label-width="formLabelWidth">
              <el-input  v-model="formData.page_title" placeholder="请输入内容"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="页面关键字" :label-width="formLabelWidth">
              <el-input  v-model="formData.page_keywords" placeholder="请输入内容"></el-input>
            </el-form-item>
          </el-col>
        </el-row>  
        
        <el-row>
          <el-form-item label="页面描述" :label-width="formLabelWidth">
            <el-input type="textarea" :autosize="{ minRows: 2}" v-model="formData.page_desc" placeholder="请输入内容"></el-input>
          </el-form-item>
        </el-row>  
        
        <el-row>
          <el-col :span="12">
            <el-form-item label="开始时间"  :label-width="formLabelWidth">
              <el-date-picker style="width:100%" v-model="formData.start_time" clearable type="datetime" value-format="yyyy-MM-dd HH:mm"
                    format="yyyy-MM-dd HH:mm" placeholder="选择日期"></el-date-picker>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="结束时间" :label-width="formLabelWidth">
              <el-date-picker style="width:100%" v-model="formData.end_time" clearable type="datetime" value-format="yyyy-MM-dd HH:mm"
                    format="yyyy-MM-dd HH:mm" placeholder="选择日期"></el-date-picker>
            </el-form-item>
          </el-col>
        </el-row>    

        <el-row>
          <el-col :span="24">
            <el-form-item label="每天限制投票" :label-width="formLabelWidth">
              <el-input  v-model="formData.more_set.day_limit" placeholder="请输入内容"></el-input>
            </el-form-item>
          </el-col>
        </el-row>    

        <el-form-item label="状态" :label-width="formLabelWidth">
           <el-radio-group v-model="formData.status">
             <el-radio  label="1">启用</el-radio>
             <el-radio  label="0">禁用</el-radio>
           </el-radio-group>
        </el-form-item>


        <el-form-item label="封面图" :label-width="formLabelWidth" ref="cover_url" prop="cover_url">
          <img-upload2 ref="imgUpload" url="upload/imgUpload" :show-file-list="false" :fileList.sync="formData.cover_url" :thumb='{ isthumb:1, width:1200, height:750 }' :imgIds.sync="formData.cover_id" :uploadedImg="onUploadedImg"></img-upload2>
          <!-- <img-upload ref="imgUpload" url="upload/imgUpload" :thumb='{isthumb:1,width:750,height:750}' :default_src.sync='default_src' :uploadedImg="onUploadedImg" ></img-upload> -->
        </el-form-item>
        <el-form-item prop="context_rule" label="投票规则" required label-width="80px">
                <Tinymce ref="editor" :height="400"   v-model="formData.context_rule" />
        </el-form-item>
        <el-form-item v-if="formData.type=='0'" label="底色选择" :label-width="formLabelWidth" ref="bgcolor" prop="bgcolor">
          <el-color-picker el-color-picker v-model="formData.bgcolor"></el-color-picker>
        </el-form-item>

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="doSubmit('formData')">确 定</el-button>
        <el-button type="danger" @click="doEditCancel('formData')">取 消</el-button>
      </div>
    </el-dialog>

    <img-preview :show.sync='previewShow' :src='previewSrc'></img-preview>

  </div>
</template>
<script>
// import { log } from 'util';
var util = require("@/utils/util.js");
import ImgUpload from '@/components/common/ImgUpload.vue';
import DyTags from '@/components/common/DyTags.vue';
import baseMixin from  '@/mixin/baseMixin';
import ImgPreview from '@/components/common/ImgPreview.vue';
import MycitySelect from '@/components/common/MycitySelect.vue';
import ImgUpload2 from '@/components/common/ImgUpload2.vue';
import Tinymce from '@/components/Tinymce';
import VueClipboards from 'vue-clipboard2';
import Vue from 'vue';
Vue.use(VueClipboards);


export default {
  components: {
    'img-upload': ImgUpload,
    'img-preview':ImgPreview,
    'mycity-select': MycitySelect,
    'img-upload2': ImgUpload2,
     'Tinymce' : Tinymce
  },
  mixins: [baseMixin,VueClipboards],
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
          { required: true, message: '请输入名称', trigger: 'change' },
        ],
      },
      searchData:{   
          status : '-1',
          type:'-1',
          region_no: '',
      },
      tableData: [],   
      formData:{
        name:'',   
        status: '0',   
        page_title:'',
        page_keywords:'',
        page_desc:'',
        region_no: '',
        type: '0',
        bgcolor: '',
        cover_url:[],
        cover_id:[],
        start_time:'',
        end_time:'',
        more_set:{
          day_limit:''
        },
        context_rule:''
      },
      old_region_no:'',
      
      type_list:['0','1'],
      previewShow:false,
      previewSrc:'',
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

    that.getList(that.searchData)
  },

  methods:{

 showThat(e){
     let url = this.$baseconfig.host + e.row.wx_h5
        this.$copyText(url).then(
          res => {
            this.$message({
              message: '复制成功！',
              type: 'success'
            })
            // this.$toast("已成功复制，可直接去粘贴");
          },
          err => {
            this.$message.error(this.$t('prompt.copyFail'))
            // this.$toast("复制失败");
          }
        )
      
    },

    changeCity(){
      
    },
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"Vote/getList",
        data: searchdata
      }).then(res=>{
        //console.log(res.data.list)
        that.tableData = res.data.list
      })
    },
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },

    doEdit(e={}){
      this.old_region_no = ''
      if(Object.keys(e).length>0){
        this.formData = Object.assign({},e);
        this.formData.status = String(this.formData.status)
        this.formData.type = String(this.formData.type)
        this.formData.bgcolor = this.formData.bgcolor?this.formData.bgcolor:''
        this.formData.start_time = this.getFormatDate(this.formData.start_time)
        this.formData.end_time = this.getFormatDate(this.formData.end_time)
        this.formData.more_set = this.formData.more_set? this.formData.more_set:{ day_limit:'' }

        this.old_region_no = e.region_no
        this.default_src = this.$getRealImgUrl(this.formData.cover_url)
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
      // this.formData.cover_url = e.res.info.url;
      // this.$refs.cover_url.clearValidate()
    },
    onSearch(){
      this.getList(this.searchData);
    },
    switchChange(id,val){
      var that = this
      util.requests("post", {
          url: "Vote/enable",
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
            url: "Vote/del",
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
      if(this.old_region_no!=this.formData.region_no&&this.formData.id){
        that.$confirm('检测到变更新的城市将会重置配置信息, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(res => {
          that.formSubmit(formName)
        })
      }else{
        that.formSubmit(formName)
      }
    },
    formSubmit(formName){
      var that = this
      
      that.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.page_loading){ 
            return; 
          }
          that.page_loading = true;
          util.requests("post",{
            url:"Vote/edit",
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

    openPage:util.openPage,
    getFormatDate(val){
     return util.DataFun.getFormatDate(val,2)
    },
    
  }

};
</script>
<style lang="scss" scoped>
  .radio-item{
    display: inline-block;
    margin-right: 15px;
    margin-bottom: 15px;
    .radio-img{
      width: 90px;
      height: 180px;
      overflow: hidden;
      cursor: pointer;
    }
    .radio-el{
      margin-top: 15px;
    }
  }

  .form-serch{
    text-align: right;
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


