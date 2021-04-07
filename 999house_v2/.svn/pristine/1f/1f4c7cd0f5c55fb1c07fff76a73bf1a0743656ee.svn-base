<template>
  <div class="_container">
    <el-form >
      <el-row :gutter="30">
        <el-col :xl="6" :md="6" :xs="24" style="float:right;text-align:right;">
          <el-button
            type="danger"
            icon="el-icon-circle-plus-outline"
            @click="doEdit('add')"
          >新增</el-button>
        </el-col>
      </el-row>
    </el-form>

    <el-table :data="tableData" style="width:100%" v-loading="page_loading">
      <el-table-column prop="id" label="ID" width="100" align="center">
        <template slot-scope="scope">
          {{scope.row.id==-1?0:scope.row.id}}
        </template>
      </el-table-column>
      <el-table-column prop="name" label="角色名称" align="center"></el-table-column>
      <el-table-column prop="remark" label="角色描述" align="center"></el-table-column>
      <el-table-column label="状态" align="center">
        <template slot-scope="scope">
          <el-switch :disabled="scope.row.id==-1" @change='(val)=>{switchChange(scope.row.id,val)}' v-model="scope.row.status" :active-value="1" :inactive-value="0"></el-switch>
        </template> 
      </el-table-column>
      <el-table-column prop="code" label="操作"  align="center">
        <template slot-scope="scope">
          <el-button :disabled="scope.row.id==-1" type="primary" size="mini" @click="openRoleMenus({ id:scope.row.id,name:scope.row.name })">设置权限</el-button>
          <el-button :disabled="scope.row.id==-1" type="primary" size="mini" @click="doEdit('edit',{ id:scope.row.id, name:scope.row.name, remark:scope.row.remark, status:scope.row.status })">编辑</el-button>
          <el-button :disabled="scope.row.id==-1" type="danger" size="mini" @click="doDel(scope.row.id)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>


    <!-- 编辑用户弹窗 -->
    <el-dialog v-if="dialogFormVisible"
      :title="form_type=='add'?'新增角色':'编辑角色'"
      :visible.sync="dialogFormVisible"
      width="800px"
      :close-on-click-modal="false"
      @close="doEditCancel"
    >
      <el-form style="padding-right:50px;" :model="form" ref="form" :rules="rules">
        <el-form-item label="角色名称" :label-width="formLabelWidth" prop="name">
          <el-input v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item label="角色描述" :label-width="formLabelWidth">
          <el-input v-model="form.remark"></el-input>
        </el-form-item>

        <el-form-item label="状态" :label-width="formLabelWidth">
          <el-radio v-model="form.status" label="1">开启</el-radio>
          <el-radio v-model="form.status" label="0">禁用</el-radio>
        </el-form-item>
        
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="doEditSubmit">确 定</el-button>
        <el-button type="danger" @click="doEditCancel">取 消</el-button>
      </div>
    </el-dialog>
    
    <!-- 编辑角色菜单弹窗 -->
    <auth-tree  ref='authtree' v-if="menuList.length>0&&roleMenusPageShow" :roleinfo='menu_roleinfo' :data="menuList" :pageShow='roleMenusPageShow'  :default_checkedKeys='roleMenus' @onClose='closeroleMenus' @onSubmit='doMenuSubmit' ></auth-tree>

  </div>
</template>
<script>
var util = require("@/utils/util");
import AuthTree from '@/components/common/AuthTree.vue';
import baseMixin from  '@/mixin/baseMixin';
 
export default {
  components: {
			'auth-tree': AuthTree,
  },
   mixins: [baseMixin],
  data() {
    return {
      dialogFormVisible: false,
      formLabelWidth: "123px",
      
      form: {
        id: 0,
        name: "",
        remark: "",
        status : 0
      },
      rules: { 
        name: [
          { required: true, message: '请输入角色名称', trigger: 'change' },
        ],
      },

      tableData: [],
      roleMenusPageShow:false,
      menu_roleinfo:{},
      tree_defultcheckedkeys:[], //初始化菜单有选中的
      roleMenus:[],//某个角色的授权菜单
      menuList: [],//获取后台的菜单列表
    };
  },
  created: function() {
    this.resetData({
      form: this.form,
    })
    this.getList()
    this.getMenuList()
  },
  methods: {
    getList() {
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "role/index"
      }).then(res => {
          that.page_loading = false
          if(res.code==1){
            let arr =[{id: -1, name: '超级管理员', remark:'超级管理员', status:1}]
            that.setDataArr({
              tableData : arr.concat(res.data.list), 
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },

   
    doEditCancel(){
      var that=this
      that.resetData()
      if(that.dialogFormVisible == true){
        that.dialogFormVisible = false
      }
    },
    doEdit(dotype,e={}){
      let that = this
      that.dialogFormVisible = true
      that.form_type = dotype
      if(Object.keys(e).length>0){
          that.form = Object.assign({},e)
      }

      that.form.status=String(that.form.status)
    },
    doEditSubmit(){
      let that=this
      if(that.form.id==-1){
        return
      }
      that.$refs['form'].validate((valid) => {
        if (valid) {
          util.requests("post", {
            url: "role/edit",
            data: that.form
          }).then(res => {
              that.page_loading = false
              if(res.code==1){
                util.Message.success('操作成功');
                that.getList()
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
      if(id==-1){
        return
      }
      var that = this
      if(that.page_loading){
          return
      }
      that.$confirm('该操作将永久删除记录，是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        that.page_loading = true

        util.requests("post", {
            url: "role/del",
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
      });
    },
    switchChange(id,val){
      if(id==-1){
        return
      }
      //console.log(id,val)
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "role/enable",
          data: {id: id,status: val}
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

    /**
     * //菜单操作
     */
    doMenuSubmit(e){
      var that = this
      if(e.role_id==-1){
        return
      }
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "role/editRoleMenus",
          data: {id: e.role_id,mymenu_ids: e.checked_keys}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            if(that.list){
              that.list[id]['status']= val
              that.setDataArr({
                tableData : that.list,
              })
            } 
            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    openRoleMenus(e){
      this.menu_roleinfo = { id:e.id ,name:e.name }
      this.getRoleMenusId(e.id)
    },
    closeroleMenus(){
      this.roleMenusPageShow =false
    },
    //获取某个用户的菜单列表id集合
    getRoleMenusId(id){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "role/getRoleMenusId",
          data: {role_id: id}
      }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.setDataArr({
              roleMenus : res.data.list,
            })
            that.roleMenusPageShow = true
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    //获取全部菜单的树形结构数据
    getMenuList() {
      var that = this
      util.requests("post", {
          url: "menu/index",
      }).then(res => {
          if(res.code==1){
            that.setDataArr({
              menuList : res.data.list
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    
  }
};
</script>
<style lang="scss" scoped>
._container {
  margin-top: 20px;
  padding: 20px;
  background: #fff;
}
</style>


