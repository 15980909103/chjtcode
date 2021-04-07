<template>
  <div class="_container">
    <el-form >
      <el-row :gutter="30">
        <el-col :xl="6" :md="6" :xs="24" style="float:right;text-align:right;">
          <el-button
            type="danger"
            icon="el-icon-circle-plus-outline"
            @click="openPage({url: '/merchant/menuEdit'})"
          >新增</el-button>
        </el-col>
      </el-row>
    </el-form>

    <el-table :data="tableData" style="width:100%"
      row-key="id"
      default-expand-all
      :tree-props="{children: 'children', hasChildren: 'hasChildren'}"
      v-loading="page_loading"
    >
      <el-table-column prop="id" label="ID" width="120" align="center"></el-table-column>
      <el-table-column prop="name" label="菜单名称" align="center"></el-table-column>
      <el-table-column prop="url" label="权限url" align="center"></el-table-column>
      <el-table-column prop="page" label="页面路径" align="center"></el-table-column>
      <el-table-column label="侧栏状态" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.status==1" type='success' effect="dark">显示</el-tag>
          <el-tag v-else type='danger' effect="dark">隐藏</el-tag>   
        </template> 
      </el-table-column>

      <el-table-column label="启用状态" width="80" align="center">
        <template slot-scope="scope">
          <el-switch @change='(val)=>{switchChange(scope.row.id,val)}' v-model="scope.row.enable" :active-value="1" :inactive-value="0" ></el-switch>
        </template> 
      </el-table-column>

      <el-table-column label="操作"  align="center">
        <template slot-scope="scope">
          <el-button  type="primary" size="mini" @click="openPage({ url: '/merchant/menuEdit',data:{ id:scope.row.id } },'get')">编辑</el-button>
          <el-button  type="danger" size="mini" @click="doDel(scope.row.id)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    
  </div>
</template>
<script>
var util = require("@/utils/util");

export default {
  data() {
    return {
      dialogFormVisible: false,
      formLabelWidth: "123px",
      
      form: {
        name: "",
        remark: "",
        status : 0
      },
      rules: { 
        name: [
          { required: true, message: '请输入角色名称', trigger: 'change' },
        ],
      },
      
      tableData: []
    };
  },
  created: function() {
    this.getList()
  },
  methods: {
    getList() {
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "merchant/getMenuList",
      }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.setDataArr({
              tableData : res.data.list,
            })
          }else{
            util.Message.error(res.msg);
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
            url: "merchant/menuDel",
            data: {id: id}
          }).then(res => {
            that.page_loading = false
            if(res.code==1){
              that.getList()
              util.Message.success('操作成功');
            }else{
              util.Message.error(res.msg);
            }
        });
      }).catch(() => {
         //console.log('取消')     
      });
    },
    switchChange(id,val){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "merchant/menuEnable",
          data: {id: id,enable: val}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.getList()
            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    
    openPage:util.openPage
  }
};
</script>
<style lang="scss" scoped>
._container {
  margin-top: 20px;
  padding: 20px;
  background: #fff;
}

.el-tag--success {
    background-color: #67c23a;
    border-color: #67c23a;
    color: #fff;
}
.el-tag--danger {
    background-color: #f56c6c;
    border-color: #f56c6c;
    color: #fff;
}
</style>


