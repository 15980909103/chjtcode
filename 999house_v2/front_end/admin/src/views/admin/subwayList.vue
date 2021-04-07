<template>
  <div class="_container">
    <div class="tb-top" style="float:right;margin-bottom: 30px;">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item>
          <mycity-select :label-width="formLabelWidth" :unlimitedCity='true' :city_no.sync='searchData.region_no' :isMy='true' model='3' siteAreasUrl='city/siteAreas'></mycity-select>
        </el-form-item>

        <el-form-item label="地铁线">
          <el-select v-model="searchData.subway" placeholder="请选择">
            <el-option label="全部" value="-1"></el-option>
            <el-option v-for="(item) in subways" :key="item" :label="item+'号线'" :value="item"></el-option>
          </el-select>
        </el-form-item>

        <el-form-item label="状态">
          <el-select v-model="searchData.status" placeholder="请选择">
            <el-option label="全部" value="-1"></el-option>
            <el-option label="启用" value="1"></el-option>
            <el-option label="禁用" value="0"></el-option>
          </el-select>
        </el-form-item>

        <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
        <!-- <el-button icon="el-icon-back" @click="openPage({url:-1,hreftype:'navigateBack'})">返回</el-button> -->
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="80" align="center"></el-table-column>
      <el-table-column prop="name" label="站点"  align="center"></el-table-column>
      <el-table-column prop="region_name" label="城市" width="180" align="center"></el-table-column>
      <el-table-column prop="subway_name" label="所属地铁" width="180" align="center"></el-table-column>
      <el-table-column prop="status" label="状态" width="80" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.status==1" type='success' effect="dark">启用</el-tag>
          <el-tag v-else type='danger' effect="dark">禁用</el-tag>
        </template>
      </el-table-column>

      <el-table-column prop="opt" label="操作" align="center">
        <template slot-scope="scope">
          <el-button type="success"  size="mini" @click="doEdit(scope.row)">编辑</el-button>
          <el-button type="danger"  size="mini" @click="del(scope.row.id,scope.$index)">删除</el-button>
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
        <el-form-item class="city-box" label="站点名称"  :label-width="formLabelWidth"  >
            <el-input  v-model="formData.name" placeholder="请输入内容"></el-input>
        </el-form-item>

        <mycity-select :label-width="formLabelWidth" @getMoreInfo="getMoreInfo" :city_no.sync='formData.region_no' :isMy='true' model='3' siteAreasUrl='city/siteAreas'></mycity-select>

        <el-form-item prop="subway" label="所属地铁线" :label-width="formLabelWidth">
          <el-checkbox-group v-model="formData.subway">
            <el-checkbox  v-for="(item) in subways" :key='item' :label="String(item)">{{item}}号线</el-checkbox>
          </el-checkbox-group>
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

import DyTags from '@/components/common/DyTags.vue';
import baseMixin from  '@/mixin/baseMixin';
import MycitySelect from '@/components/common/MycitySelect.vue';

export default {
  components: { 
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

      searchData:{   
        status : '-1',
        region_no : '-1',
        subway : '-1',
      },
      tableData: [],   
      formData:{
        status:'0',   
        name:'',
        region_no:'',
        region_name:'',
        subway:[],
        subway_name:''
      },
      subways:[],
      pagination: {}, //分页数据
    }
  },

  created: function(){

    this.resetData({
      formData: this.formData,
    })
    this.getList(this.searchData)
  },

  watch:{
    'formData.region_no':function(val){
      //初始化可配置地铁
      this.getSubways(val);
    },
    'searchData.region_no':function(val){
      //初始化可配置地铁
      this.getSubways(val);
      this.searchData.subway = '-1';
    },
  },

  methods:{
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"city/getSubwayList",
        data: searchdata
      }).then(res=>{
        that.tableData = res.data.list
        that.setDataArr({
          pagination : {
            page : res.data.current_page,
            pagecount : res.data.last_page,
            pagesize : Math.ceil(res.data.total / res.data.last_page)
          }
        })
      })
    },
    
    openPage: util.openPage,

    doEdit(e={}){
      if(Object.keys(e).length>0){
        e.status = String(e.status)
        
        this.formData = Object.assign({},e);
        this.formData.dotype = 'edit'
        // this.formData.subway = String(this.formData.subway);
    
      }else{
        this.formData.dotype = 'add'
      }
      // console.log(this.formData)
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
              url: "city/delSubway",
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
            url:"city/editSubway",
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

    // 获取地铁信息
    getSubways(region_no){
      var that = this
      util.requests("post",{
        url:"city/siteInfo",
        data: {key:"subway",region_no:region_no}
      }).then(function(res){
          that.subways = res.data.info&&res.data.info.val?res.data.info.val:[]
      })
    },

    // 省市区中文赋值
    getMoreInfo(e) {
        // console.log(e)
        switch(e.type) {
          case 'city':
            this.formData.region_name = e.cname;
            break;
          case 'area':
            break;
          case 'streets':
            break;
          case 'businessAreas':
            break;
        }
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


