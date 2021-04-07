<template>
  <div class="_container">
    <div class="tb-top" style="float:right;margin-bottom: 30px;">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item label="状态">
          <el-select v-model="searchData.status" placeholder="请选择">
             <el-option label="全部" value="-1"></el-option>
            <el-option label="禁用" value="0"></el-option>
            <el-option label="启用" value="1"></el-option>
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
      <el-table-column prop="name" label="热搜词" width="180" align="center"></el-table-column>
      <el-table-column prop="type" label="类型" width="150" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.type=='1'">楼盘</el-tag>
          <el-tag v-if="scope.row.type=='2'">标签</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="status" width="80" label="状态" align="center">
        <template slot-scope="scope">
          <el-switch @change="(val)=>{switchChange(scope.row.id,val)}" v-model="scope.row.status" :active-value="1" :inactive-value="0" ></el-switch>
        </template>
      </el-table-column>
      <el-table-column label="排序" width="80" align="center">
        <template slot-scope="scope">
            <el-input v-model="scope.row.sort" @change="(val)=>{sortChange(scope.row.id,val)}" placeholder="越大越靠前" size="medium"></el-input>
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
            <el-form-item label="热搜词" prop="name" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.name" placeholder="请输入内容"></el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="12">
            <el-form-item label="类型" :label-width="formLabelWidth">
              <el-select @change="changeType" v-model="formData.type" placeholder="请选择">
                <el-option label="楼盘" value="1"></el-option>
                <el-option label="标签" value="2"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="12">
            <mycity-select @changeCity='changeCity' :label-width="formLabelWidth" :city_no.sync='formData.city' :isMy='true' model='3' siteAreasUrl='city/siteAreas'></mycity-select>
          </el-col>
        </el-row>

        <el-row>
          <el-col v-if="formData.type==1" :span="12">
            <el-form-item label="选择新房" prop="forid" :label-width="formLabelWidth">
              <el-row>
                <el-col :span="18">
                  <span @click="changeInnerShow(1)">
                    <el-input style="width:100%;display:none;"  v-model="formData.bind_id" placeholder="请选择新房"></el-input>
                    <el-input style="width:100%" :disabled='true'  v-model="formData.bind_name" placeholder="请选择新房"></el-input>
                  </span>
                </el-col>
                <el-col :span="4" style="text-align: right;">
                  <el-button  @click="clearInner">清空</el-button>
                </el-col>
              </el-row>
            </el-form-item>

            <estates-new :region_no='formData.region_no' :show.sync='innerVisible' @innerFormData='innerFormData'></estates-new>
          </el-col>
          <el-col v-if="formData.type==2" :span="12">
            <el-form-item label="请选择标签" prop="forid_tag" :label-width="formLabelWidth">
              <el-row>
                <el-col :span="18">
                  <span @click="changeInnerShow(2)">
                    <el-input style="width:100%;display:none;"  v-model="formData.bind_id" placeholder="请选择标签"></el-input>
                    <el-input style="width:100%" :disabled='true'  v-model="formData.bind_name" placeholder="请选择标签"></el-input>
                  </span>
                </el-col>
                <el-col :span="4" style="text-align: right;">
                  <el-button  @click="clearInner">清空</el-button>
                </el-col>
              </el-row>
            </el-form-item>

            <estates-new-tag :show.sync='innerVisibleTag' @innerFormData='innerFormData'></estates-new-tag>
          </el-col>
        </el-row>

        
        <el-row>
          <el-col :span="12">
            <el-form-item label="状态" :label-width="formLabelWidth">
              <el-select v-model="formData.status" placeholder="请选择">
                <el-option label="禁用" value="0"></el-option>
                <el-option label="启用" value="1"></el-option> 
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="12">
            <el-form-item label="排序" :label-width="formLabelWidth">
              <el-input-number style="width:100%" v-model="formData.sort" placeholder="请输入内容"></el-input-number>
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
import EstatesNew from '@/components/InnerTable/EstatesNew.vue';
import MycitySelect from '@/components/common/MycitySelect.vue';
import baseMixin from  '@/mixin/baseMixin';
import EstatesNewTag from '@/components/InnerTable/EstatesNewTag.vue';

export default {
  components: {
    'img-upload': ImgUpload,
    'estates-new': EstatesNew,
    'estates-new-tag': EstatesNewTag,
    'mycity-select': MycitySelect,
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
      page_loading:'',
      rules: { 

      },
      searchTime:[],  
      searchData:{   
          status : '-1',
          region_no: '',
      },
      tableData: [],   
      formData:{
        id: 0, 
        status:'0',   
        type:'1',
        name:"",
        sort:"",
        city:"",
        bind_id:"",
        purpose_type:1,
      },

      innerVisible: false,
      innerVisibleTag: false,
    }
  },
  watch: {
    
  },
  created: function(){
    let that = this
    that.resetData({
      formData: that.formData,
    },function(){
      
    })

    that.getList(that.searchData)
  },

  methods:{
    // 类型变动
    changeType(){
      this.clearInner()
    },
    // 城市变动
    changeCity(){
      this.clearInner()
    },
    // 获取列表
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"SearchWord/getList",
        data: searchdata
      }).then(res=>{
        //console.log(res.data.list)
        that.tableData = res.data.list
      })
    },
    // 新增/编辑弹框
    doEdit(e={}){
      if(Object.keys(e).length>0){
        this.formData = Object.assign({},e);
        this.formData.status = String(this.formData.status)
        this.formData.type = String(this.formData.type)
      }
      this.dialogVisibleEdit = true;
    },
    // 取消弹框
    doEditCancel(formName){
      var that=this
      that.$refs[formName].resetFields()
      that.resetData()
      if(that.dialogVisibleEdit == true){
        that.dialogVisibleEdit = false
      }
    },
    // 排序
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
          url: "searchWord/changeSort",
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
    // 状态变更
    switchChange(id,val){
      var that = this
      util.requests("post", {
        url: "searchWord/changeStatus",
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
    // 删除
    del(id,val){   //确定删除
        this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
          }).then(() => {
            util.requests("post",{
              url: "searchWord/delete",
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

    // 提交
    doSubmit(formName){
      var that = this
      that.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.page_loading){ 
            return; 
          }
          that.page_loading = true;
          util.requests("post",{
            url:"searchWord/edit",
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
    // 搜索
    onSearch(){
      this.getList(this.searchData);
    },

    // 选择弹窗
    changeInnerShow(type=1){
      console.log(type)
      if(type == 1) {
        if(!this.formData.city){
          this.$message({
            type: 'error',
            message: '请先选择城市'
          });
          return
        }
        this.innerVisible = true
      } else if(type == 2) {
        this.innerVisibleTag = true
      }
    },
    // 内嵌表选择
    innerFormData(e){
      console.log(e)
      this.formData.bind_id = e.id
      this.formData.bind_name = e.name
      console.log(this.formData)
    },
    // 清除选择
    clearInner(){
      this.formData.bind_name = '';
      this.formData.bind_id = 0;
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


