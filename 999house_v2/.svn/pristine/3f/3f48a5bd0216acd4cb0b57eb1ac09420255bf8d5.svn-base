<template>
  <div class="_container">
    <div class="tb-top">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-button icon="el-icon-back" @click="openPage({url:-1,hreftype:'navigateBack'})">返回</el-button>
      </el-form>
    </div>
    <el-tabs v-model="activeTabs" @tab-click="tabsClick">
      <el-tab-pane v-for="(item,key) in moduleList" :key="key" :label="item" :name="String(key)">
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
      </el-tab-pane>
    </el-tabs>

    <!-- 表格 -->
    <el-table class="tb-title" v-if="activeTabs=='banner'" :data="banner_tableData" style="width: 100%">
      <el-table-column prop="forid" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="forhref" label="跳转地址" align="center"></el-table-column>

      <el-table-column prop="module" label="模块类型" align="center">
        <template slot-scope="scope">
          <el-tag >{{moduleList[scope.row.module]}}</el-tag>
        </template>
      </el-table-column>

      <el-table-column prop="forsort" width="100" label="序号" align="center">
        <template slot-scope="scope">
            <el-input v-model="scope.row.forsort" @change="(val)=>{sortChange({id:scope.row.id},val)}"  placeholder="请输入内容，越大越靠前"></el-input>
        </template>
      </el-table-column>

      <el-table-column prop="opt" label="操作" align="center">
        <template slot-scope="scope">
          <el-button type="success" size="mini" @click="doEdit({...scope.row})">编辑</el-button>
          <el-button type="danger" size="mini" @click="del({forid:scope.row.forid},scope.$index)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <!-- 表格2 -->
    <el-table class="tb-title" v-else :data="tableData" style="width: 100%">
      <el-table-column prop="forid" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="forname" label="名称" align="center"></el-table-column>

      <el-table-column prop="module" label="模块类型" align="center">
        <template slot-scope="scope">
          <el-tag >{{moduleList[scope.row.module]}}</el-tag>
        </template>
      </el-table-column>

      <el-table-column prop="forsort" width="100" label="序号" align="center">
        <template slot-scope="scope">
            <el-input v-model="scope.row.forsort" @change="(val)=>{sortChange({id:scope.row.id},val)}"  placeholder="请输入内容，越大越靠前"></el-input>
        </template>
      </el-table-column>

      <el-table-column prop="vote_num" width="180" label="当前票数" align="center">
        <template slot-scope="scope">
            <div>显示的：{{scope.row.vote_num}}</div>
            <div>真实的：{{scope.row.real_vote_num}}</div>
        </template>
      </el-table-column>

      <el-table-column prop="module" width="100" label="参与人数" align="center">
        <template slot-scope="scope">
          <span>{{scope.row.join_num}}</span>
        </template>
      </el-table-column>

      <el-table-column prop="opt" label="操作" align="center">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="openPage({url:'/marketing/voteLog',data:{vote_detail_id: scope.row.id, region_no:scope.row.region_no }})">投票记录</el-button>
          <el-button type="success" size="mini" @click="doEdit({...scope.row})">编辑</el-button>
          <el-button type="danger" size="mini" @click="del({id:scope.row.id},scope.$index)">删除</el-button>
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
          <el-form-item label="图片" :label-width="formLabelWidth" ref="cover_imgs" prop="cover_imgs">
            <img-upload2 ref="imgUpload" url="upload/imgUpload" :limit='1' :thumb='{isthumb:1,width:1200,height:850}' :show-file-list="false" :fileList.sync="formData.cover_imgs"  ></img-upload2>
            <!-- <img-upload ref="imgUpload" url="upload/imgUpload" :thumb='{isthumb:1,width:750,height:750}' :default_src.sync='default_src' :uploadedImg="onUploadedImg" ></img-upload> -->
          </el-form-item>
        <span v-if="activeTabs=='banner'">
          <el-form-item label="序号" :label-width="formLabelWidth">
            <el-input  v-model="formData.forsort" placeholder="请输入内容"></el-input>
          </el-form-item>

          <el-form-item label="跳转地址" :label-width="formLabelWidth">
            <el-input  v-model="formData.forhref" placeholder="请输入内容"></el-input>
          </el-form-item>
        </span>

        <span v-if="activeTabs=='estates_new'">
          <el-form-item :label="'选择'+activeTabsName" prop="forid" :label-width="formLabelWidth">
            <el-row>
              <el-col :span="20">
                <span @click="changeInnerShow">
                  <el-input style="width:100%;display:none;"  v-model="formData.forid" :placeholder="'请选择'+activeTabsName"></el-input>
                  <el-input style="width:100%" :disabled='true'  v-model="formData.forname" :placeholder="'请选择'+activeTabsName"></el-input>
                </span>
              </el-col>
              <el-col :span="4" style="text-align: right;">
                <el-button  @click="clearInner">重置</el-button>
              </el-col>
            </el-row>
          </el-form-item>

          <el-row>
            <el-col :span="12">
              <el-form-item label="序号" :label-width="formLabelWidth">
                <el-input  v-model="formData.forsort" placeholder="请输入内容，越大越靠前"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="真实投票数" :label-width="formLabelWidth">
                <el-input  v-model="formData.real_vote_num" placeholder="请输入内容，越大越靠前"></el-input>
              </el-form-item>
            </el-col>
            <el-row>
               <el-col :span="24">
                <el-form-item label="当前票数" :label-width="formLabelWidth">
                  <el-input  v-model="formData.vote_num" placeholder="请输入内容，越大越靠前"></el-input>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
               <el-col :span="24">
                <el-form-item label="分享详情" :label-width="formLabelWidth">
                  <el-input type="textarea" v-model="formData.share_desc" placeholder="分享详情"></el-input>
                </el-form-item>
              </el-col>
            </el-row>
            <el-col>
              <el-form-item prop="introduction" label="详情介绍" required label-width="80px">
                <Tinymce ref="editor" :height="400"   v-model="formData.introduction" />
              </el-form-item>
            </el-col>
          </el-row>

          <estates-new :region_no='formData.region_no' :show.sync='innerVisible.estates_new' @innerFormData='innerFormData'></estates-new>
          <live-room :region_no='formData.region_no' :show.sync='innerVisible.liveRoom' @innerFormData='innerFormData'></live-room>
        </span>
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
import EstatesNew from '@/components/InnerTable/EstatesNew.vue';
import LiveRoom from '@/components/InnerTable/LiveRoom.vue';
import ImgUpload2 from '@/components/common/ImgUpload2.vue';
import Tinymce from '@/components/Tinymce';

export default {
  components: {
    'img-upload': ImgUpload,
    'estates-new': EstatesNew,
    'live-room': LiveRoom,
    'img-upload2': ImgUpload2,
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
      activeTabs:'',
      activeTabsName:'',
      dialogVisibleEdit: false,
      formLabelWidth: "123px",
      src:'',
      default_src:'',
      page_loading:'',
      thumb:{},
      rules: {
        //cover: [{ validator: validateImg,  message: "请上传图片" }],
      },

      searchData:{
          module:'',
      },
      banner_tableData: [],
      tableData: [],
      formData:{
        id:'',
        vote_id:'',
        module:'',
        forname:'',
        forid:'',//关联id
        forsort: 0,
        vote_num: 0,
        share_desc:'',
        introduction:''
      },
      vote_id:'',
      moduleList:[],

      innerVisible:{ },
      listData:{},
    }
  },
  watch:{
    activeTabs(val){
      this.$nextTick(()=>{
        this.activeTabsName = this.moduleList[val]

        let list = [];
        if(this.listData[val]){
          list = this.listData[val];
        }

        if(val=='banner'){
          this.setDataArr({
            banner_tableData: list
          })
        }else{
          this.setDataArr({
            tableData: list
          })
        }
      })
    }
  },

  created: function(){
    var that = this

    that.formData.region_no = that.$urlData.region_no
    that.setDataArr({
      vote_id: that.$urlData.vote_id,
      searchData:{
        vote_id: that.$urlData.vote_id,
      },
    })
    if(!that.vote_id||!that.formData.region_no){
      this.$alert('缺失参数', '提示',{
        confirmButtonText: '确定',
        callback: action => {
          util.openPage({url:'/marketing/vote'});
        }
      });
      return;
    }

    that.getConstModules()

    that.resetData({
        formData: that.formData,
        default_src: ''
      },function(){
        that.$nextTick(()=>{
          that.$refs.imgUpload&&that.$refs.imgUpload.resetData() //重置图片信息
        })
      })
      that.getList(that.searchData)
      that.getBanner()
  },

  methods:{
    tabsClick(){

    },
    getConstModules(){
      let that = this
      util.requests("post",{
        url:"vote/getConstModules",
      }).then(res=>{
        //console.log(res.data)
        that.moduleList = res.data

        let innerVisible={}
        for(var i in that.moduleList){
          if(that.activeTabs==0){
            that.activeTabs = i
            //console.log(i)
          }
          innerVisible[i] = false
        }
        that.innerVisible = innerVisible
      })
    },
    getBanner(){
      var that = this
      util.requests("post",{
        url:"vote/getBanner",
        data:{vote_id: that.vote_id}
      }).then(res=>{
        let listData = {}
        for(var i in res.data.list){
          let item = res.data.list[i]
          if(!listData['banner']){
            listData['banner'] = []
          }

          listData['banner'].push(item)
        }

        that.setDataArr({
          listData: Object.assign(that.listData,listData),
          banner_tableData: listData['banner']
        })
      })
    },
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"vote/getDetails",
        data: searchdata
      }).then(res=>{
        let listData = {}
        for(var i in res.data.list){
          let item = res.data.list[i]
          if(!listData[item.module]){
            listData[item.module] = []
          }

          listData[item.module].push(item)
        }

        that.setDataArr({
          listData: Object.assign(that.listData,listData),
        })
      })
    },
    getFormatDate:util.DataFun.getFormatDate,
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },

    doEdit(e={}){
      if(Object.keys(e).length>0){
        this.formData = Object.assign({},e);
        this.formData.module = this.formData.module?String(this.formData.module): this.activeTabs
      }else{
        this.formData.module = this.activeTabs
      }
      //console.log(this.formData)
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


  del(e,val){   //确定删除
      let url = "vote/delDetail";
      let post_data = {id: e.id}
      if(this.activeTabs=='banner'){
          url = "vote/delBanner";
          post_data = {forid: e.forid,vote_id: this.vote_id}
      }

      this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          util.requests("post",{
            url: url,
            data: post_data,
          }).then(res => {
              // console.log(res); return;
              if(res.code==0){
                this.$message({
                  type: 'error',
                  message: res.msg
                });
                return;
              }
              if(this.activeTabs=='banner'){
                this.banner_tableData.splice(val,1)
              }else{
                this.tableData.splice(val,1)
              }
              this.$message({
                type: 'success',
                message: '删除成功!'
              });
          })
        })
  },

    sortChange(e,val){
      var that = this
      if (parseFloat(val).toString() == 'NaN') {
        util.Message.error('排序必须是数字');
  　　　return false;
  　　}
      if(that.page_loading){
          return
      }
      that.page_loading = true
      util.requests("post", {
          url: "vote/changeDetailSort",
          data: {id: e.id,forsort: val}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.onSearch()
            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    doSubmit(formName){
      var that = this
      that.formData.vote_id = that.vote_id

      that.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.page_loading){
            return;
          }
          that.page_loading = true;

          let url = "vote/editDetail";
          if(that.activeTabs=='banner'){
              url = "vote/editBanner";
          }

          util.requests("post",{
            url: url,
            data: that.formData
          }).then(res=>{
            that.page_loading = false
            //console.log(res)
            if(res.code==1){
               that.$message({ type: 'success', message: '操作成功!' });
               that.dialogVisibleEdit = false;

              if(that.activeTabs=='banner'){
                that.getBanner()
              }else{
                that.onSearch()
              }
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
    openPage: util.openPage,

    changeInnerShow(){
      this.innerVisible[this.activeTabs] = true
      console.log(this.innerVisible)
    },
    innerFormData(e){
      console.log(e)
      this.formData.forid = e.id
      this.formData.forname = e.name

      //console.log(this.formData,999999999999)
    },
    clearInner(){
      this.formData.forname = '';
      this.formData.forid = 0;
    },

  }
};
</script>
<style lang="scss" scoped>
  .form-serch {
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


