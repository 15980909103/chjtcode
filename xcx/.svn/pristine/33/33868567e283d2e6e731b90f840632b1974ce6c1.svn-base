<template>
  <div class="_container">
    <div class="tb-top">
      <el-form :inline="true"  class="form-serch" >
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="forname" label="楼盘名称" width="100" align="center"></el-table-column>

      <el-table-column prop="opt" label="操作" align="center">
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
import MycitySelect from '@/components/common/MycitySelect.vue';
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
      page_loading:'',
      rules: { 
        
      },
      tableData: [],   
      formData:{
        agent_id:0,
        region_no: '',
        forid:'',//关联id
        forname:'',
      },
      agent_id:0,
      searchdata:{},

     innerVisible: false,
    }
  },
  watch: {
    
  },
  created: function(){
    var that = this 

    that.resetData({
      formData: that.formData,
    },function(){
      that.$nextTick(()=>{

      })
    })

    if(this.$urlData && this.$urlData.agent_id) {
      this.agent_id = this.$urlData.agent_id;
    }
    
    that.getList(this.searchdata)
  },

  methods:{
    changeCity(val){
      this.clearInner()
    },
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      searchdata.agent_id = this.agent_id;
      util.requests("post",{
        url:"agent/getEstatesList",
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
          url: "agent/delEstate",
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
          that.formData.agent_id = that.agent_id
          util.requests("post",{
            url:"agent/addEstate",
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


