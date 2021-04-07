<template>
  <div class="_container">
    <div class="tb-top" style="float:right;margin-bottom: 30px;">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item label="状态">
          <el-select v-model="searchData.status" placeholder="请选择">
             <el-option label="全部" value="-1"></el-option>
            <el-option label="下架" value="0"></el-option>
            <el-option label="直播中" value="1"></el-option> 
            <el-option label="未开始" value="2"></el-option>
            <el-option label="已结束" value="3"></el-option>
          </el-select>
        </el-form-item>

        <el-form-item>
          <mycity-select :city_no.sync='searchData.region_no' :unlimitedCity='true' :isMy='true' model='3' siteAreasUrl='city/siteAreas'></mycity-select>
        </el-form-item>

        <el-form-item label="创建时间">
           <el-date-picker
            style="width:100%"
            v-model="searchTime"
            value-format="yyyy-MM-dd" format="yyyy-MM-dd"
            type="daterange"
            range-separator="-"
            start-placeholder="开始日期"
            end-placeholder="结束日期">
          </el-date-picker>
        </el-form-item>

        <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="room_name" label="直播间名称" width="180" align="center"></el-table-column>
      <el-table-column prop="cover" label="封面图" width="180" align="center">
        <template slot-scope="scope">
          <el-image v-if="scope.row.cover" style="width: 90px;" :src="getRealImgUrl(scope.row.cover)"></el-image>
        </template>
      </el-table-column>
       <el-table-column prop="forname" label="绑定的楼盘" width="180" align="center"></el-table-column>
      <el-table-column prop="status" label="状态" width="80" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.status=='0'">下架</el-tag>
          <el-tag v-if="scope.row.status=='1'">直播中</el-tag>
          <el-tag v-if="scope.row.status=='2'">未开始</el-tag>
          <el-tag v-if="scope.row.status=='3'">已结束</el-tag>
        </template>
      </el-table-column>

      <el-table-column prop="sort" label="排序" width="90" align="center">
        <template slot-scope="scope">
          <el-input v-model="scope.row.sort" @change="(val)=>{sortChange(scope.row.id,val)}"  placeholder="请输入内容"></el-input>
        </template>
      </el-table-column>

      <el-table-column label="有效时间" width="260" align="center">
        <template slot-scope="scope">
        {{getFormatDate(scope.row.start_time,3)}} - {{getFormatDate(scope.row.end_time,3)}}
        </template>
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
      width="860px"
      :close-on-click-modal="false"
      @close="doEditCancel('formData')"
    >
      <el-form style="padding-right:50px;" :model="formData" ref="formData" :rules="rules">
        <el-row>
          <el-col :span="12">
            <el-form-item label="直播间名称" prop="room_name" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.room_name" placeholder="请输入内容"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="跳转地址" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.room_url" placeholder="请输入内容"></el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="12">
            <mycity-select @changeCity='changeCity' :label-width="formLabelWidth" :city_no.sync='formData.region_no' :isMy='true' model='3' siteAreasUrl='city/siteAreas'></mycity-select>
          </el-col>
          <el-col :span="12">
            <el-form-item label="选择新房" prop="forid" :label-width="formLabelWidth">
              <el-row>
                <el-col :span="18">
                  <span @click="changeInnerShow">
                    <el-input style="width:100%;display:none;"  v-model="formData.forid" placeholder="请选择新房"></el-input>
                    <el-input style="width:100%" :disabled='true'  v-model="formData.forname" placeholder="请选择新房"></el-input>
                  </span>
                </el-col>
                <el-col :span="4" style="text-align: right;">
                  <el-button  @click="clearInner">清空</el-button>
                </el-col>
              </el-row>
            </el-form-item>

            <estates-new :region_no='formData.region_no' :show.sync='innerVisible' @innerFormData='innerFormData'></estates-new>
          </el-col>  
        </el-row>


        <el-row>
          <el-col :span="12">
            <el-form-item label="开始时间"  :label-width="formLabelWidth">
              <el-date-picker style="width:100%" v-model="formData.start_time" type="datetime" value-format="yyyy-MM-dd HH:mm"
                    format="yyyy-MM-dd HH:mm" placeholder="选择日期"></el-date-picker>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="结束时间" :label-width="formLabelWidth">
              <el-date-picker style="width:100%" v-model="formData.end_time" type="datetime" value-format="yyyy-MM-dd HH:mm"
                    format="yyyy-MM-dd HH:mm" placeholder="选择日期"></el-date-picker>
            </el-form-item>
          </el-col>
        </el-row>
        
        <el-row>
          <el-col :span="12">
            <el-form-item label="状态" :label-width="formLabelWidth">
              <el-select v-model="formData.status" placeholder="请选择">
                <el-option label="下架" value="0"></el-option>
                <el-option label="直播中" value="1"></el-option> 
                <el-option label="未开始" value="2"></el-option>
                <el-option label="已结束" value="3"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="排序" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.sort" placeholder="请输入内容"></el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-form-item label="直播间描述" prop="desc" :label-width="formLabelWidth">
          <el-input style="width:100%" v-model="formData.desc" placeholder="请输入内容"></el-input>
        </el-form-item>

        <el-form-item label="上传封面" :label-width="formLabelWidth" ref="cover" prop="cover">
          <img-upload2 ref="imgUpload" url="upload/imgUpload" :show-file-list="false" :fileList.sync="formData.cover_url" :imgIds.sync="formData.cover_id" :uploadedImg="onUploadedImg"></img-upload2>
          <!-- <img-upload ref="imgUpload" url="upload/imgUpload" :default_src.sync='default_src' :uploadedImg="onUploadedImg" ></img-upload> -->
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
import EstatesNew from '@/components/InnerTable/EstatesNew.vue';
import MycitySelect from '@/components/common/MycitySelect.vue';
import baseMixin from  '@/mixin/baseMixin';
import ImgUpload2 from '@/components/common/ImgUpload2.vue';

export default {
  components: {
    'img-upload': ImgUpload,
    'estates-new': EstatesNew,
    'mycity-select': MycitySelect,
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
        // cover: [{ validator: validateImg,  message: "请上传图片" }],
      },
      searchTime:[],  
      searchData:{   
          status : '-1',
          startdate: '',
          enddate: '',
          region_no: '',
      },
      tableData: [],   
      formData:{ 
        status:'0',   
        sort:'0',   
        cover:'',
        cover_id:[],
        cover_url:[],
        start_time:'',
        end_time:'',
        forid:'',//关联id
      },

      innerVisible: false,
    }
  },
  watch: {
    searchTime(newVal){
      if(newVal){
        this.searchData.startdate = newVal[0]
        this.searchData.enddate = newVal[1]
      }else{
        this.searchData.startdate = ''
        this.searchData.enddate = ''
      }
    }
  },
  created: function(){
    let that = this
    that.resetData({
      formData: that.formData,
      default_src: ''
    },function(){
      that.$nextTick(()=>{
        that.$refs.imgUpload&&that.$refs.imgUpload.resetData() //重置图片信息
      })
    })

    that.getList(that.searchData)
  },

  methods:{
    changeCity(val){
      this.clearInner()
    },
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"LiveRoom/getList",
        data: searchdata
      }).then(res=>{
        //console.log(res.data.list)
        that.tableData = res.data.list
      })
    },
    getFormatDate:util.DataFun.getFormatDate,
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },
    
    doEdit(e={}){
      if(Object.keys(e).length>0){
        this.formData = Object.assign({},e);
        this.formData.status = String(this.formData.status)
        this.formData.type = String(this.formData.type)

        this.default_src = this.$getRealImgUrl(this.formData.cover)
        this.formData.start_time = this.getFormatDate(this.formData.start_time,2)
        this.formData.end_time = this.getFormatDate(this.formData.end_time,2)
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
    sortChange(id,val){
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
          url: "LiveRoom/changeSort",
          data: {id: id,sort: val}
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
 
    //图片上传后操作
    onUploadedImg(e){
      // this.formData.cover = e.res.info.url;
      // this.$refs.cover.clearValidate()
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
            url: "LiveRoom/del",
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
            url:"LiveRoom/edit",
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

    changeInnerShow(){
      if(!this.formData.region_no){
        this.$message({
          type: 'error',
          message: '请先选择城市'
        });
        return
      }
      this.innerVisible = true
    },
    innerFormData(e){
      console.log(e)
      this.formData.forid = e.id
      this.formData.forname = e.name
      console.log(this.formData)
    },
    clearInner(){
      this.formData.forname = '';
      this.formData.forid = 0;
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


