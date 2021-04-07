<template>
  <div class="mapmange_container">
    <div class="mapmange_content">
      <el-form :inline="true" :model="searchData" class="form-serch">
        <el-form-item>
          <el-input v-model="searchData.name" placeholder="请输入名称" prefix-icon="el-icon-search"></el-input>
        </el-form-item>

        <el-form-item>
          <el-select style="width:100%;" v-model="searchData.fordo" :clearable='true' placeholder="模块应用范围" >
            <el-option label="全部" value="all"></el-option>
            <el-option
              v-for="item in check_dataAll"
              :key="item.key"
              :label="item.name"
              :value="item.key">
            </el-option>
          </el-select>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
        </el-form-item>
        <el-form-item>
          <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
        </el-form-item>
      </el-form>

      <!-- ========================================================= -->
      <el-table :data="tableData" style="width: 100%">
        <el-table-column prop="id" label="ID" width="80" align="center"></el-table-column>
        <el-table-column prop="name" label="名称"  align="center"></el-table-column>
        <el-table-column  label="模块应用"  align="center">
          <template slot-scope="scope">  
            {{getCheckedsName(scope.row.fordo)}}
          </template>
        </el-table-column>
       
        <el-table-column label="操作"  align="center">
          <template slot-scope="scope">
            <el-button type="primary" size="mini" @click="doEdit({ id:scope.row.id, name:scope.row.name,type:scope.row.type,fordo:scope.row.fordo })">编辑</el-button>
            <el-button  type="danger" size="mini" @click="doDel(scope.row.id)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <!-- ========================================================= -->

      <!-- ============分页=============== -->
      <!-- <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box> -->

       <!-- 编辑弹窗 -->
      <el-dialog v-if="dialogFormVisible"
        :title="!form.id?'新增标签':'编辑标签'"
        :visible.sync="dialogFormVisible"
        width="800px"
        :close-on-click-modal="false"
        @close="doEditCancel"
      >
        <el-form style="padding-right:50px;" :model="form" ref="form" :rules="rules">
          <el-form-item label="名称" :label-width="formLabelWidth" prop="name" >
            <el-input v-model="form.name"></el-input>
          </el-form-item>
          <el-form-item style="display:none;" label="作用标识" :label-width="formLabelWidth" prop="type" >
            <el-input v-model="form.type"></el-input>
          </el-form-item>

          <el-form-item label="模块应用" :label-width="formLabelWidth" prop="fordo">
            <extra-checkbox ref="extra_checkbox" :dataAll='check_dataAll' @onChecked='onChecked'></extra-checkbox>
          </el-form-item>

        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button type="primary" @click="doEditSubmit">确 定</el-button>
          <el-button type="danger" @click="doEditCancel">取 消</el-button>
        </div>
      </el-dialog>

    </div>
  </div>
</template>
<script>
var util = require("@/utils/util.js");
import paginationBox from '@/components/common/pagination.vue';
import ExtraCheckbox from './components/ExtraCheckbox.vue';
import DataSet from './DataSet.js';

export default {
  components: {
      'pagination-box': paginationBox,
      'extra-checkbox': ExtraCheckbox,
	},
  data() {
    var validateFordo = (rule, value, callback) => {
      this.$nextTick(() => {//由于是子组件传值，得等dom渲染才能拿到
        if (this.form.fordo == '') { 
    　　　　callback(new Error('请点选要应用的模块'));
    　　　　return false; 
    　　} 
        callback();
      })
    };

    return {
      dialogFormVisible: false,
      formLabelWidth: "123px",
      form: {
        id: '',
        name: '',
        type: 0,
        fordo: ''
      },

      rules: { 
        name: [
          { required: true, message: '请输入名称', trigger: 'change' },
        ],
        type: [
          { required: true, message: '缺少作用标识'},
        ],
        fordo: [
          { required: true, validator: validateFordo },
        ],
      },
      check_dataAll: DataSet.data,

      searchData: {
        //  搜索数据
        name: "",
        type: 0,//获取标签
        fordo:''
      },
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据
    };
  },
  
  created: function(){
    this.getList(this.searchData)
  },
  methods: {
    getList(searchdata={}){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "extra/getExtraList",
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
              }
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    onSerch() {
      //console.log("查询",this.searchData);
      this.getList(this.searchData)
    },
    //分页操作
    pageChange: function(page) {
      let post_data = Object.assign({},this.searchData);
      post_data.page = page;
      this.getList(post_data)
    },
    onChecked(e,isCheckedAll){
      console.log(e)
      if(isCheckedAll){
        this.form.fordo= 'all'
      }else{
        this.form.fordo= e.join(',')//转换,格式
      }
    },
    getCheckedsName(key){
      var key_arr=key.split(',')
      var str_arr =[]
      for(var i in key_arr){
        str_arr[i]=DataSet.getCheckedsName(key_arr[i])
      } 
      return str_arr=str_arr.join(' / ')
    },

    resetData(){
      this.setDataArr({
        form: {
          id: '',
          name: '',
          type: 0,
          fordo: ''
        },
      })
    },
    doEditCancel(){
      var that=this
      that.resetData()
      if(that.dialogFormVisible == true){
        that.dialogFormVisible = false
      }
    },
    doEdit(e={}){
      let that = this
      that.dialogFormVisible = true
      //进行表单赋值
      if(Object.keys(e).length>0){
        that.form = Object.assign({},e)
        if(that.form.fordo){
          that.$nextTick(() => {
            that.$refs.extra_checkbox.setCheckeds(that.form.fordo)
          })
        }
      }
    },
    doEditSubmit(){
      let that=this
      that.$refs['form'].validate((valid) => {
        if (valid) {
          if(that.page_loading){
              return
          }
          that.page_loading = true

          util.requests("post", {
            url: "extra/extraEdit",
            data: that.form
          }).then(res => {
              that.page_loading = false
              //console.log(res.data)
              if(res.code==1){
                util.Message.success('操作成功');
                that.onSerch()
                setTimeout(() => {
                  that.doEditCancel()
                }, 1000);
              }else{
                util.Message.error(res.msg);
              }
          });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    doDel(id){
      var that = this
      that.$confirm('是否删除此条记录?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        if(that.page_loading){
            return
        }
        that.page_loading = true
        util.requests("post", {
            url: "extra/extraDel",
            data: {id: id}
          }).then(res => {
            that.page_loading = false
            if(res.code==1){
              that.onSerch()
              util.Message.success('操作成功');
            }else{
              util.Message.error(res.msg);
            }
        });
      }).catch(() => {
         //console.log('取消')     
      });        
    },
    

    openPage: function(e){
      return util.openPage(e);
    }

    
  }
};
</script>
<style lang="scss" scoped>
.mapmange_container {
  min-height: calc(100vh - 50px);
  background: #f0f2f5;
  padding-top: 20px;
  .mapmange_content {
    background: #fff;
    min-height: calc(100vh - 90px);
    border-radius: 2px;
    padding: 20px 10px;
    .form-serch {
      text-align: right;
      .el-input {
        width: 300px;
      }
      .el-select {
        width: 150px;
      }
    }
  }
  .pagination {
    margin-top: 20px;
    text-align: right;
  }
}
</style>