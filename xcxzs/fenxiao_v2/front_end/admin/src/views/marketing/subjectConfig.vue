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
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="forid" label="ID" width="70" align="center"></el-table-column>

      <el-table-column v-if="activeTabs=='banner'" prop="forhref" label="跳转地址" align="center"></el-table-column>
      
      <el-table-column v-else prop="forname" label="名称" align="center"></el-table-column>
      
      
      

      <el-table-column prop="module" label="模块类型" align="center">
        <template slot-scope="scope">
          <el-tag >{{moduleList[scope.row.module]}}</el-tag>
        </template>
      </el-table-column>

      <el-table-column v-if="activeTabs!='label'" prop="forsort" width="100" label="序号" align="center">
        <template slot-scope="scope">
            <el-input v-model="scope.row.forsort" @change="(val)=>{sortChange({forid:scope.row.forid, module:scope.row.module,ftype:0},val)}"  placeholder="请输入内容"></el-input>
        </template>
      </el-table-column>
      <el-table-column v-if="activeTabs =='estates_new'" prop="look_number" width="80" label="浏览量" align="center"></el-table-column>

       <el-table-column v-if="activeTabs =='label'" prop="status" label="状态" align="center" width="120">
        <template slot-scope="scope">
            <el-switch @change="(val)=>{sortChange({forid:scope.row.forid, module:scope.row.module,ftype:1},val)}" v-model="scope.row.status" :active-value="'1'" :inactive-value="'2'" ></el-switch>
        </template>
      </el-table-column>

      <el-table-column prop="opt" label="操作" align="center">
        <template slot-scope="scope">
          <el-button type="success" size="mini" @click="doEdit({...scope.row,doforid:scope.row.forid})">编辑</el-button>
          <el-button type="danger" size="mini" @click="del({forid:scope.row.forid, module:scope.row.module},scope.$index)">删除</el-button>
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
        <span v-if="['estates_new','liveRoom'].includes(activeTabs)">
          <el-form-item :label="'选择'+activeTabsName" prop="forid" :label-width="formLabelWidth">
            <el-row>
              <el-col :span="20">
                <span @click="changeInnerShow()">
                  <el-input style="width:100%;display:none;"  v-model="formData.forid" :placeholder="'请选择'+activeTabsName"></el-input>
                  <el-input style="width:100%" :disabled='true'  v-model="formData.forname" :placeholder="'请选择'+activeTabsName"></el-input>
                </span>
              </el-col>
              <el-col :span="4" style="text-align: right;">
                <el-button  @click="clearInner">重置</el-button>
              </el-col>
            </el-row>
          </el-form-item>

          <el-form-item label="序号" :label-width="formLabelWidth">
            <el-input  v-model="formData.forsort" placeholder="请输入内容"></el-input>
          </el-form-item>

          <span v-if="activeTabs=='estates_new'">
            <estates-new :region_no='formData.region_no' :show.sync='innerVisible.estates_new' @innerFormData='innerFormData'></estates-new>
            <!-- <el-form-item :label="'选择封面图片'" prop="forimgUrls" :label-width="formLabelWidth">
              <el-row>
                <el-col :span="20">
                  <span @click="changeInnerShow('imgs')">
                    <el-input style="width:100%;display:none;" :placeholder="'选择封面图片'"></el-input>
                    <el-button type="primary" plain>选择</el-button>
                  </span>
                </el-col>
              </el-row>
            </el-form-item> -->
            <estates-imgs v-if="formData.forid" :estate_id='formData.forid' :show.sync='innerVisibleImgs' :selectData.sync='formData.cover_imgs' ></estates-imgs>

            <el-form-item prop="subway" label="标签" label-width="110px">
              <el-select clearable v-model="formData.label_id" filterable placeholder="输入搜索">
                <el-option v-for="item in subway_sites" :key="item.id" :label="item.name" :value="item.id"></el-option>
              </el-select>
            </el-form-item>

            <el-form-item label=" " :label-width="formLabelWidth">
              <span v-for="(itemIU,idxIU) in formData.cover_imgs"  :key="idxIU">
              <el-image style="width:80px;height:80px;float:left;margin-right:10px;" @click="preview_show = true" :src="getRealImgUrl(itemIU)"></el-image>
              <img-preview :src='getRealImgUrl(itemIU)' :show.sync='preview_show'></img-preview>
              </span>
            </el-form-item>
          </span>

          <span v-if="activeTabs=='liveRoom'"><live-room :region_no='formData.region_no' :show.sync='innerVisible.liveRoom' @innerFormData='innerFormData'></live-room></span>
        </span>

        <span v-if="activeTabs=='banner'">
          <el-form-item label="封面图" :label-width="formLabelWidth" ref="cover_imgs" prop="cover_imgs">
            <img-upload2 ref="imgUpload" url="upload/imgUpload" :limit='1'  :thumb='{ isthumb:0, width:750, height:750 }' :show-file-list="false" :fileList.sync="formData.cover_imgs"  ></img-upload2>
            <!-- <img-upload ref="imgUpload" url="upload/imgUpload" :thumb='{isthumb:1,width:750,height:750}' :default_src.sync='default_src' :uploadedImg="onUploadedImg" ></img-upload> -->
          </el-form-item>

          <el-form-item label="序号" :label-width="formLabelWidth">
            <el-input  v-model="formData.forsort" placeholder="请输入内容"></el-input>
          </el-form-item>

          <el-form-item label="跳转地址" :label-width="formLabelWidth">
            <el-input  v-model="formData.forhref" placeholder="请输入内容"></el-input>
          </el-form-item>
        </span>

        <span v-if="activeTabs=='label'">
          <el-form-item label="标签名称" :label-width="formLabelWidth">
            <el-input  v-model="formData.forname" placeholder="请输入内容"></el-input>
          </el-form-item>

        <el-form-item prop="is_cheap" label="状态" label-width="110px">
          <el-radio v-model="formData.status" label="1">开启</el-radio>
          <el-radio v-model="formData.status" label="2">关闭</el-radio>
        </el-form-item>

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
import EstatesImgs from '@/components/InnerTable/EstatesImgs.vue';
import ImgPreview from '@/components/common/ImgPreview.vue';
import ImgUpload2 from '@/components/common/ImgUpload2.vue';

export default {
  components: {
    'img-upload': ImgUpload,
    'estates-new': EstatesNew,
    'live-room': LiveRoom,
    'estates-imgs': EstatesImgs,
    'img-preview' : ImgPreview,
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
      activeTabs:'',
      activeTabsName:'',
      dialogVisibleEdit: false,
      preview_show: false,
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
      tableData: [], 
      subway_sites:[],  
      formData:{ 
        module:'',
        label_id:'',
        forname:'',
        forid:'',//关联id
        doforid:'',
        status:'1',
        forsort: 0,
        cover_imgs: [],
        forhref:'',//针对banner的跳转地址
      },
      id:'',
      moduleList:[],
      subject_type:'',

      innerVisible:{ },
      listData:{},

      innerVisibleImgs: false,
      forimgUrls: [],
    }
  },
  watch:{
    activeTabs(val){
      console.log(val)
      this.$nextTick(()=>{
        this.activeTabsName = this.moduleList[val]
        if(!this.listData[val]){
          this.setDataArr({
            tableData: []
          })
        }else{
          this.setDataArr({
            tableData: this.listData[val]
          })
        }
      })
    }
  },

  created: function(){
    var that = this 
    that.getLabelData({
      id:that.$urlData.id
    })
    that.setDataArr({
      subject_type: that.$urlData.subject_type,
      id: that.$urlData.id,
      searchData:{
        id: that.$urlData.id,
        subject_type: that.$urlData.subject_type,
      },
    })

    

    that.resetData({
        formData: that.formData,
        default_src: ''
      },function(){
        that.$nextTick(()=>{
          that.$refs.imgUpload&&that.$refs.imgUpload.resetData() //重置图片信息
        })
      })
      that.getList(that.searchData,function(){
        let innerVisible={}
        for(var i in that.moduleList){
          if(that.activeTabs==0){
            that.activeTabs = i
            //console.log(i)
          }
          innerVisible[i] = false
        }
        setTimeout(() => {
          that.setDataArr({
            innerVisible: innerVisible,
            tableData: that.listData[that.activeTabs]
          })
        }, 50);
      })
  },

  methods:{
    tabsClick(){

    },
    getList(searchdata={},callfun=null){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"subject/getConfigList",
        data: searchdata
      }).then(res=>{
        console.log(res.data)
        that.moduleList = res.data.moduleList

        for(var i in res.data.list){
          let item = res.data.list[i]
        
          let arr = []
          for(var j in item){
            item[j]['module'] = i
            arr.push(item[j])
          }
          res.data.list[i] = arr
        }

        that.formData.region_no = res.data.region_no
        that.setDataArr({
          listData: res.data.list,
          tableData: res.data.list[that.activeTabs]
        })
        
        if(callfun){
          callfun()
        }
      })
    },
    getFormatDate:util.DataFun.getFormatDate,
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },

    getLabelData(searchdata = {}){
       var that = this
      util.requests("post",{
        url:"subject/labelData",
        data: searchdata
      }).then(res=>{
        
       this.subway_sites = res.data
         for(var i in this.subway_sites){
           let item  = this.subway_sites[i]
            item.id = String(item.id) 
            console.log(6666,item)
         }
         
      
      })

    },
    
    doEdit(e={}){
      if(Object.keys(e).length>0){
        this.formData = Object.assign({},e);
        this.formData.module = this.formData.module?String(this.formData.module):this.activeTabs
        this.forimgUrls = e.cover_imgs;
      }else{
        this.formData.module = this.activeTabs
      }
      
      //console.log(this.formData,this.forimgUrls)
      this.dialogVisibleEdit = true;
    },
    doEditCancel(formName){
      var that=this
      that.$refs[formName].resetFields()
      that.resetData()
      that.forimgUrls = [];
      if(that.dialogVisibleEdit == true){
        that.dialogVisibleEdit = false
      }
    },
 
   
    onSearch(){
      this.getList(this.searchData);
    },
    

  del(e,val){   //确定删除
      this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          util.requests("post",{
            url: "subject/delConfig",
            data: {forid:e.forid,module: e.module,id: this.id}
          }).then(res => {
              // console.log(res); return;
              if(res.code==0){ 
                this.$message({
                  type: 'error',
                  message: res.msg
                });
                return; 
              }
              this.tableData.splice(val,1)
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
          url: "subject/changeConfigSort",
          data: {forid:e.forid,module: e.module,id: that.id,ftype:e.ftype,forsort: val}
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
      that.formData.id = that.id
 
      that.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.page_loading){ 
            return; 
          }
          that.page_loading = true;
          util.requests("post",{
            url:"subject/editConfig",
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
    openPage: util.openPage,

    changeInnerShow(type='normal'){
      if(type=='normal') {
        this.innerVisible[this.activeTabs] = true
      } else {
        if(this.activeTabs != 'estates_new'){
          this.$message({
            type: 'error',
            message: '不是新房不用选择图片'
          });
          return
        }
        if(!this.formData.forid){
          this.$message({
            type: 'error',
            message: '请先选择楼盘'
          });
          return
        }
        this.innerVisibleImgs = true
      }
    },
    innerFormData(e){
      if(!e.type) {
        e.type = 'normal';
      }
      if(e.type=='normal') {
        this.formData.forid = e.id
        this.formData.forname = e.name
      }
       //console.log(this.formData,e,999999999999)
    },
    clearInner(type='normal'){
      if(type=='normal') {
        this.formData.forname = '';
        this.formData.forid = 0;
        this.forimgUrls = [];
        this.formData.cover_imgs = [];
      }
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


