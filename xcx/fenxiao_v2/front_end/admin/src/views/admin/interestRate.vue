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
        <el-form-item label="类型">
          <el-select v-model="searchData.type" placeholder="请选择">
            <el-option label="全部" value="-1"></el-option>
            <el-option label="LPR" value="1"></el-option>
            <el-option label="个人贷款旧版基准利率" value="2"></el-option>
            <el-option label="公积金贷款基准利率" value="3"></el-option>
          </el-select>
        </el-form-item>

        <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="type" label="类型" width="180" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.type=='1'">LPR</el-tag>
          <el-tag v-if="scope.row.type=='2'">个人贷款旧版基准利率</el-tag>
          <el-tag v-if="scope.row.type=='3'">公积金贷款基准利率</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="status" label="状态" align="center">
        <template slot-scope="scope">
            <el-switch @change="(val)=>{switchChange(scope.row.id,val)}" v-model="scope.row.status" :active-value="1" :inactive-value="0" ></el-switch>
        </template>
      </el-table-column>
      <el-table-column prop="release_time" label="发布时间" width="150" align="center"></el-table-column>

      <el-table-column  label="操作" align="center">
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
      width="800px"
      :close-on-click-modal="false"
      @close="doEditCancel('formData')"
    >
      <el-form style="padding-right:50px;" :model="formData" ref="formData" :rules="rules">
        <el-row>
          <el-form-item label="类型" :label-width="formLabelWidth">
            <el-select @change='changeType' v-model="formData.type" placeholder="请选择">
              <el-option label="LPR" value="1"></el-option>
              <el-option label="个人贷款旧版基准利率" value="2"></el-option>
              <el-option label="公积金贷款基准利率" value="3"></el-option>
            </el-select>
          </el-form-item>
        </el-row>
        <el-row>
          <el-form-item label="状态" :label-width="formLabelWidth">
            <el-radio-group v-model="formData.status">
              <el-radio  label="1">启用</el-radio>
              <el-radio  label="0">禁用</el-radio>
            </el-radio-group>
          </el-form-item>
        </el-row> 
        <el-row>
          <el-form-item label="发布时间" :label-width="formLabelWidth"  :v-cloak="formLabelWidth">
            <el-date-picker style="width:100%" v-model="formData.release_time" type="date" value-format="yyyy-MM-dd" format="yyyy-MM-dd" placeholder="选择日期"></el-date-picker>
          </el-form-item>
        </el-row>
        <el-row>
            <el-row :span="24">
              <el-col>
                <el-button type="primary" @click="onAdd('content')">添加利率</el-button>
              </el-col>
            </el-row>
            <el-row v-if="formData.type!=1">
              <el-row v-for="(itemC, i) in contentItem" :key="'content'+i">
                <el-col :span="6">
                  <el-form-item prop="year_start" label="年限" label-width="70px">
                    <el-input type="text" width="2px" v-model="itemC.start"></el-input>
                  </el-form-item> 
                </el-col>
                <el-col :span="6">
                  <el-form-item prop="year_end" label="至" label-width="40px">
                    <el-input type="text" width="2px" v-model="itemC.end"></el-input>
                  </el-form-item> 
                </el-col>
                <el-col :span="6">
                  <el-form-item prop="rate" label="利率(百分比)" label-width="100px">
                    <el-input-number :precision="2" :step="0.1"  v-model="itemC.rate"></el-input-number>
                  </el-form-item>
                </el-col>
                <el-col :span="6">
                  <el-form-item prop="del" label="" label-width="120px">
                    <el-button  @click="onDelete(i, 'content')">删除</el-button>
                  </el-form-item>
                </el-col>
              </el-row>
            </el-row>
            <el-row v-if="formData.type==1">
              <el-row v-for="(itemC, i) in contentItem" :key="'content'+i">
                <el-col :span="8">
                  <el-form-item prop="year_start" label="年期" label-width="70px">
                    <el-input-number v-model="itemC.year"></el-input-number>
                  </el-form-item> 
                </el-col>
                <el-col :span="8">
                  <el-form-item prop="rate" label="利率(百分比)" label-width="120px">
                    <el-input-number :precision="2" :step="0.1"  v-model="itemC.rate"></el-input-number>
                  </el-form-item>
                </el-col>
                <el-col :span="8">
                  <el-form-item prop="del" label="" label-width="120px">
                    <el-button  @click="onDelete(i, 'content')">删除</el-button>
                  </el-form-item>
                </el-col>
              </el-row>
            </el-row>
        </el-row>
        <el-row>
            <el-row :span="24">
              <el-col>
                <el-button type="primary" @click="onAdd('basic_point')" v-if="formData.type==1">添加基点</el-button>
                <el-button type="primary" @click="onAdd('basic_point')" v-else>添加上浮百分比</el-button>
              </el-col>
            </el-row>
            <el-row v-for="(itemB, i) in basicPointItem" :key="'basic'+i">
              <el-col :span="8">
                <el-form-item prop="rate" label="数值(百分比)" label-width="120px">
                  <el-input-number :precision="2" :step="0.1"  v-model="itemB.rate"></el-input-number>
                </el-form-item>
              </el-col>
              <el-col :span="8">
                <el-form-item prop="del" label="" label-width="120px">
                  <el-button  @click="onDelete(i, 'basic_point')">删除</el-button>
                </el-form-item>
              </el-col>
            </el-row>
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
import baseMixin from  '@/mixin/baseMixin';

export default {
  components: {

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
      searchData:{
          status : '-1',
          type:'-1'
      },
      tableData: [],
      formData:{
        id:0,
        content: [],
        basic_point: [],
        release_time: '',
        type: '1',
        status: '1',
      },
      contentItem:[],
      basicPointItem:[],
    }
  },
  created: function(){
    let that = this
    that.resetData({
      formData: this.formData,
    },function(){
      
    })
    that.getList(that.searchData)
  },

  watch:{
    
  },

  methods:{
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"interestRate/getList",
        data: searchdata
      }).then(res=>{
        //console.log(res.data.list)
        that.tableData = res.data.list
      })
    },
    doEdit(e={}){
      if(Object.keys(e).length>0){
        this.formData = Object.assign({},e);
        this.formData.status = String(this.formData.status)
        this.formData.type = String(this.formData.type)
        this.contentItem = this.formData.content
        this.basicPointItem = this.formData.basic_point
      }
      this.dialogVisibleEdit = true;
    },
    doEditCancel(formName){
      var that=this
      that.$refs[formName].resetFields()
      that.resetData()
      this.contentItem = [];
      this.basicPointItem = [];

      if(that.dialogVisibleEdit == true){
        that.dialogVisibleEdit = false
      }
    },
    onSearch(){
      this.getList(this.searchData);
    },
    switchChange(id,val){
      var that = this
      util.requests("post", {
          url: "interestRate/status",
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
              url: "interestRate/delete",
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
          that.formData.content = that.contentItem;
          that.formData.basic_point = that.basicPointItem;
          util.requests("post",{
            url:"interestRate/edit",
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
    // 添加元素
    onAdd(type){
      // console.log(type);
      switch(type) {
        case 'content':
          if(this.formData.type==1) {
            if(this.contentItem.length < 2) {// LPR目前固定两种
              this.contentItem.push({year:"", rate:""});
            } else {
              this.$alert("超出限制", "提示", {
                confirmButtonText: "确定",
                callback: (action) => {
                    return ;
                },
              });
            }
          } else {
            this.contentItem.push({start:"", end:"", rate:""});
          }
          break;
        case 'basic_point':
          this.basicPointItem.push({rate:""});
          break;
      }
    },
    // 删除元素
    onDelete(index, type){
      switch(type) {
        case 'content':
          this.contentItem.splice(index, 1);
          break;
        case 'basic_point':
          this.basicPointItem.splice(index, 1);
          break;
      }
    },
    changeType() {
      this.contentItem = [];
      this.basicPointItem = [];
    }
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


