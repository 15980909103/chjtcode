<template>
  <div class="_container">
    <div class="tb-top" style="float:right;margin-bottom: 30px;">
      <span>状态：</span>
      <el-select v-model="searchData.status" placeholder="请选择">
        <el-option label="全部" value="-1"></el-option>
        <el-option label="启用" value="1"></el-option>
        <el-option label="禁用" value="0"></el-option>
      </el-select>
      <span>是否热门</span>
      <el-select v-model="searchData.is_hot" placeholder="请选择">
        <el-option label="全部" value="-1"></el-option>
        <el-option label="是" value="1"></el-option>
        <el-option label="否" value="0"></el-option>
      </el-select>

      <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
      <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="cname" label="城市名称" width="180" align="center"></el-table-column>
      <el-table-column prop="status" label="状态" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.status==1" type='success' effect="dark">启用</el-tag>
          <el-tag v-else type='danger' effect="dark">禁用</el-tag>
        </template>
      </el-table-column>

      <el-table-column prop="is_hot" label="是否热门" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.is_hot==1" type='success' effect="dark">是</el-tag>
          <el-tag v-else type='danger' effect="dark">否</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="sort" label="排序" align="center"></el-table-column>
      <el-table-column prop="is_hot" label="经度" align="center">
        <template slot-scope="scope">
          {{scope.row.lng}},{{scope.row.lat}}
        </template>
      </el-table-column>

      <el-table-column prop="opt" label="操作" width="380" align="center">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="openPage({url:'/admin/citytoptag',data:{region_no: scope.row.id}})">配置</el-button>
          <el-button type="primary" size="mini" @click="openPage({url:'/admin/areaList',data:{pid: scope.row.id}})">区设置</el-button>
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
      <el-form style="padding-right:50px;" :model="formData" ref="formData" >
        <el-form-item class="city-box" label="城市"  :label-width="formLabelWidth"  >
            <el-select :disabled='formData.dotype=="edit"?true:false' v-model="provinceData" placeholder="请选择">
              <el-option
                v-for="item in provinces"
                :key="item.id"
                :label="item.cname"
                :value="item.id+'-'+item.cname">
              </el-option>
            </el-select>
            <el-select :disabled='formData.dotype=="edit"?true:false' v-model="cityData" placeholder="请选择">
              <el-option
                v-for="item in citys"
                :key="item.id"
                :label="item.cname"
                :value="item.id+'-'+item.cname">
              </el-option>
            </el-select>
        </el-form-item>

        <el-form-item label="状态" :label-width="formLabelWidth">
           <el-radio-group v-model="formData.status">
             <el-radio  label="1">启用</el-radio>
             <el-radio  label="0">禁用</el-radio>
           </el-radio-group>
        </el-form-item>
        <el-form-item label="是否热门" :label-width="formLabelWidth">
           <el-radio-group v-model="formData.is_hot">
             <el-radio  label="1">是</el-radio>
             <el-radio  label="0">否</el-radio>
           </el-radio-group>
        </el-form-item>

        <el-form-item label="排序" :label-width="formLabelWidth">
          <el-input style="width:110px" v-model="formData.sort" placeholder="请输入内容"></el-input>
        </el-form-item>

        <el-row v-if="formData.id">
          <el-col :span="12">
            <el-form-item label="经度" :label-width="formLabelWidth">
              <el-input  v-model="formData.lng" placeholder="请输入内容"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="纬度" :label-width="formLabelWidth">
              <el-input  v-model="formData.lat" placeholder="请输入内容"></el-input>
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
          is_hot:'-1',
          id : '',
      },
      tableData: [],   
      formData:{
        status:'0',   
        is_hot:'0',
        province_no:'',   
        province_name:'',
        city_no:'',   
        city_name:'',
        sort:'',
        lng:'',
        lat:'',
      },

      provinces: [],
      citys: [],

      provinceData: '',//选中省
      cityData: '', //选中市区
    }
  },
  watch:{
    'provinceData': function(val){
      val = val.split('-')
      this.formData.province_no = val[0]
      this.formData.province_name = val[1]

      this.cityData = ''
      this.citys = [];
      this.formData.city_no = ''
      this.formData.city_name = ''
      
      if(val==0){
        return;
      }
      this.getSysCitys(val[0],'citys') //城市列表
    },
    'cityData': function(val){
      val = val.split('-')
      this.formData.city_no = val[0]
      this.formData.city_name = val[1]

      if(val==0){
        return;
      }
    },
  },
  created: function(){
     this.resetData({
      formData: this.formData,
      provinceData: this.provinceData,
      cityData: this.cityData
    })
    this.getList(this.searchData)
    this.getSysCitys(0,'provinces') //省份列表
  },

  methods:{
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"city/siteCitys",
        data: searchdata
      }).then(res=>{
        that.tableData = res.data
      })
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
      that.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.page_loading){ 
            return; 
          }
          that.page_loading = true;
          util.requests("post",{
            url:"city/siteCitysEdit",
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


