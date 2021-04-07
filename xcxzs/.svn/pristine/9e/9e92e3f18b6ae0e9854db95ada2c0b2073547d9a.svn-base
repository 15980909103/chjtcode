<template>
  <div class="_container">
    <el-form :model="searchData">
      <el-row :gutter="30">
        <el-col :xl="6" :md="8" :xs="24">
          <el-form-item label="类型" label-width="60px">
            <el-select v-model="searchData.type" placeholder="请选择">
              <el-option label="全部" value="-1"></el-option>
              <el-option label="H5" value="0"></el-option>
            </el-select>
          </el-form-item>
        </el-col>

        <el-col :xl="6" :md="6" :xs="24" style="float:right;text-align:right;">
          <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
          <el-button
            type="danger"
            icon="el-icon-circle-plus-outline"
            @click="doEdit()"
          >新增</el-button>
        </el-col>
      </el-row>
    </el-form>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="place" label="使用位置" align="center"></el-table-column>
      <el-table-column prop="desc" label="描述" align="center"></el-table-column>
      <el-table-column prop="type" label="类型" width="100" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.type=='0'">H5</span>
        </template>
      </el-table-column>

      <el-table-column prop="opt" label="操作" align="center">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="openPage({url:'/admin/banner',data:{place_id: scope.row.id,place:scope.row.place}})">管理广告</el-button>
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

        <el-form-item label="所用位置标识" prop="place" :label-width="formLabelWidth">
          <el-input v-model="formData.place" placeholder="请输入内容"></el-input>
        </el-form-item>
        <el-form-item label="描述" prop="desc" :label-width="formLabelWidth">
          <el-input v-model="formData.desc" placeholder="请输入内容"></el-input>
        </el-form-item>
        <el-form-item label="类型" :label-width="formLabelWidth">
            <el-select style="width:100%;" v-model="formData.type" placeholder="请选择">
              <el-option label="H5" value="0"></el-option>
            </el-select>
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
import baseMixin from  '@/mixin/baseMixin';
import DyTags from '@/components/common/DyTags.vue';

export default {
  components: {

  },
  mixins: [baseMixin],
  data() {
    return {
      dialogVisibleEdit: false,
      formLabelWidth: "123px",
      type:'',
      page_loading:'',

      searchData:{

      },
      tableData: [],
      formData:{
        place:'',
        desc:'',
        type:'0',
      }
    }
  },
  created: function(){
    this.resetData({
      formData: this.formData,
    })
    this.getList(this.searchData)
  },

  methods:{
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"banner/getPlaceList",
        data: searchdata
      }).then(res=>{
        //console.log(res.data.list)
        that.tableData = res.data.list
      })
    },
    openPage: util.openPage,
    doEdit(e={}){
      if(Object.keys(e).length>0){
        e.type = String(e.type)
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
              url: "banner/placeDel",
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
            url:"banner/placeEdit",
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


