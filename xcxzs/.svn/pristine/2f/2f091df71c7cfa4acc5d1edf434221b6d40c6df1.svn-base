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
        <el-form-item label="区域定义">
          <el-select v-model="searchData.is_custom" placeholder="请选择">
            <el-option label="全部" value="-1"></el-option>
            <el-option label="自定义" value="1"></el-option>
            <el-option label="默认" value="0"></el-option>
          </el-select>
        </el-form-item>

        <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
        <el-button icon="el-icon-back" @click="openPage({url:-1,hreftype:'navigateBack'})">返回</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="cname" label="区名称" width="180" align="center"></el-table-column>
      
      <el-table-column prop="is_custom" label="区域定义" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.is_custom==1" type='danger' effect="dark">自定义</el-tag>
          <el-tag v-else type='success' effect="dark">默认</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="is_custom" label="经纬度" align="center">
        <template slot-scope="scope">
          {{scope.row.lng}},{{scope.row.lat}}
        </template>
      </el-table-column>

      <el-table-column prop="status" label="状态" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.status==1" type='success' effect="dark">启用</el-tag>
          <el-tag v-else type='danger' effect="dark">禁用</el-tag>
        </template>
      </el-table-column>

      <el-table-column prop="opt" label="操作" align="center">
        <template slot-scope="scope">
          <el-button type="success" size="mini" @click="doEdit(scope.row)">编辑</el-button>
          <el-button type="danger" v-if="scope.row.is_custom==1" size="mini" @click="del(scope.row.id,scope.$index)">删除</el-button>
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
      <el-form style="padding-right:50px;" :model="formData" ref="formData" >
        <span v-if="formData.is_custom==1">
          <el-form-item class="city-box" label="区名称"  :label-width="formLabelWidth"  >
            <el-input  v-model="formData.cname" placeholder="请输入内容"></el-input>
          </el-form-item>
          <el-form-item label="ID编码" :label-width="formLabelWidth">
            <el-input  v-model="formData.id" placeholder="请输入内容"></el-input>
          </el-form-item>

          <el-form-item label="状态" :label-width="formLabelWidth">
            <el-radio-group v-model="formData.status">
              <el-radio  label="1">启用</el-radio>
              <el-radio  label="0">禁用</el-radio>
            </el-radio-group>
          </el-form-item>
        </span>
        <el-form-item label="经度" :label-width="formLabelWidth">
          <el-input  v-model="formData.lng" placeholder="请输入内容"></el-input>
        </el-form-item>
        <el-form-item label="纬度" :label-width="formLabelWidth">
          <el-input  v-model="formData.lat" placeholder="请输入内容"></el-input>
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

import DyTags from '@/components/common/DyTags.vue';
import baseMixin from  '@/mixin/baseMixin';

export default {
  components: { },
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

      searchData:{   
        status : '-1',
        is_custom:'-1',
        pid : '',
      },
      tableData: [],   
      formData:{
        status:'0',   
        cname:'',
        id:'',
        pid:'',
        lng:'',
        lat:'',
      },
    }
  },

  created: function(){
    this.pid = this.$urlData.pid
    this.searchData.pid = this.pid

    this.resetData({
      formData: this.formData,
    })
    this.getList(this.searchData)
  },

  methods:{
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"city/siteAreas",
        data: searchdata
      }).then(res=>{
        that.tableData = res.data
      })
    },
    
    openPage: util.openPage,

    doEdit(e={}){
      if(Object.keys(e).length>0){
        e.status = String(e.status)
        
        this.formData = Object.assign({},e);
        this.formData.dotype = 'edit'
    
      }else{
        this.formData.dotype = 'add'
        this.formData.is_custom = 1;
      }
      this.dialogVisibleEdit = true;
    },
    doEditCancel(formName){
      var that=this
      
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
              url: "city/siteAreasDel",
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
      that.formData.pid = that.pid

      that.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.page_loading){ 
            return; 
          }
          that.page_loading = true;
          util.requests("post",{
            url:"city/siteAreasEdit",
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


