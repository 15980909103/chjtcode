<template>
  <div>
    <div class="tb-top" style="float:right;">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item label="更新时间">
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
        <el-button icon="el-icon-circle-close" @click="openPage({url: '/estates/estatesnew'})">返回</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="change_time" label="月份"  align="center"></el-table-column>
      <el-table-column prop="update_time" label="更新时间"  align="center"></el-table-column>
       <el-table-column prop="new_price" label="变动价格" align="center"></el-table-column>
      
      <el-table-column prop="opt" label="操作"  align="center">
        <template slot-scope="scope">
          <el-button type="success" size="mini" @click="doEdit(scope.row)">编辑</el-button>
          <!-- <el-button type="danger" size="mini" @click="del(scope.row.id,scope.$index)">删除</el-button> -->
        </template>
      </el-table-column>
    </el-table>
     <!-- ============分页=============== -->
    <div style="text-align:right">
      <pagination-box :pagination="pagination" @pageChange="pageChange" style="display:inline-block;" ></pagination-box>
      <el-link style="margin-top: 10px;pointer-events:none;">共有 <el-link style="pointer-events:none;margin-top:-2px" type="primary">{{total}}</el-link> 价格</el-link>
    </div>

    <!-- 新增弹窗部分 -->
   <el-dialog
      :title="formData.id?'编辑':'新增'"
      :visible.sync="dialogVisibleEdit"
      width="800px"
      :close-on-click-modal="false"
      @close="doEditCancel('formData')"
    >
      <el-form style="padding-right:50px;" :model="formData" ref="formData" >
        <el-form-item label="价格变动时间" label-width="110px">
              <el-date-picker
                v-model="formData.change_time"
                value-format="yyyy-MM-dd" format="yyyy-MM-dd"
                type="date"
                placeholder="选择日期"
                >
              </el-date-picker>
            </el-form-item>
        
        <el-form-item label="变动价格" label-width="110px">
          <el-input style="width:200px" v-model="formData.new_price" placeholder="请输入内容"></el-input>
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
import paginationBox from '@/components/common/pagination.vue';
import baseMixin from  '@/mixin/baseMixin';

export default {
  components: {
    'pagination-box': paginationBox,
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
       page_loading : false,
      searchTime:[],
      searchData:{   
          status : '-1',
          is_hot:'-1',
          id : '',
      },
      tableData: [],   
      value3:'',
      formData:{
        estate_id:'',
        city_no:'',
        change_time:'',   
        new_price:''
      },
      pagination: {}, //分页数据
     
       total:0
    }
  },
  watch:{
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
     this.resetData({
      formData: this.formData
    })
    console.log(this.$urlData.id)
    this.searchData.id = this.$urlData.id 
    this.getList(this.searchData)
    this.getSysCitys(0,'provinces') //省份列表
  },

  methods:{
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
    var that = this
        util.requests("get", {
          url: "buildingPriceLog/list",
          data: searchdata
      }).then(res => {
      
          that.page_loading = false
          if(res.code==1){
            that.setDataArr({
              tableData : res.data.list,
              pagination : {
                page : res.data.current_page,
                pagecount : res.data.last_page,
                pagesize : Math.ceil(res.data.total / res.data.last_page)
              },
              total:res.data.total
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },
      //分页操作
    pageChange: function(page) {
      let post_data = Object.assign({},this.searchData);
      post_data.page = page;
      this.getList(post_data)
    },
    getSysCitys(pid, key){   //获取所有数据，或按条件查找数据
      let that = this
      util.requests("post", {
          url: "city/sysCitys",
          data: {pid: pid}
        }).then(res => {
          if(res.code==1){
            let arr = [{id:0,cname:'不限制'}]
            that[key] = arr.concat(res.data)
            console.log(that[key])
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    openPage: util.openPage,

    doEdit(e={}){
      if(Object.keys(e).length>0){
        e.status = String(e.status)
        e.is_hot = String(e.is_hot)
        this.formData = Object.assign({},e);
        this.formData.dotype = 'edit'
    
        this.provinceData = e.pid+'-'+e.pcname

        var m = setInterval(() => {
          if(this.cityData){
            clearInterval(m)
            return;
          }
          if(this.formData.dotype == 'edit'){
            this.cityData = e.id+'-'+e.cname
          }else{
            this.cityData = ''
          }
        }, 800);
      }else{
        this.formData.dotype = 'add'
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
              url: "city/siteCitysDel",
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
      that.formData.estate_id = that.$urlData.id
      that.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.page_loading){ 
            return; 
          }
          that.page_loading = true;
          util.requests("post",{
            url:"buildingPriceLog/cityPriceLogEdit",
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


