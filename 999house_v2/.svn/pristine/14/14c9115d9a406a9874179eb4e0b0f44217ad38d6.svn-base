<!--
  角色权限菜单弹层设置
-->
<template>
  <div>
    <el-dialog
      :title="'权限设置( ' +roleinfo.name+' )'"
      :visible.sync="show"
      width="800px"
      :close-on-click-modal="false"
      @close='onClose'
    >
      <el-tree
        :data="data"
        show-checkbox
        default-expand-all
        :expand-on-click-node='false'
        check-on-click-node
        ref="tree"
        node-key="id"
        :default-checked-keys='checkedKeys'
        :props="defaultProps">
      </el-tree>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="onSubmit">确 定</el-button>
        <el-button type="danger" @click=" show=false ">取 消</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
var util = require("@/utils/util");
export default {
  name: 'auth-tree',
  props: {
    roleinfo:{
      type: Object,
      default () {
        return {};
      }
    },
    // 全部的树结构数据
    data: {
      type: Array,
      default () {
        return [];
      }
    },
    defaultProps: { //树形结构中对应的childeren_key与选项名称
      type: Object,
      default () {
        return {
          children: 'children',//children_key
          label: 'name'       //name_key 选项名称
        }
      }
    },
    // 默认选中的节点key数组
    default_checkedKeys: {
      type: Array,
      default () {
        return [];
      }
    }
  },
  data () {
    return {
      show: true
    };
  },
  computed:{
    checkedKeys(){
      var listData= util.Tree.treeTransArray(this.data) //将全部数据的树形结构转成数组
      return this.filterParentMenusId(this.default_checkedKeys,listData)
    }
  },
  methods: {
    filterParentMenusId(roleMenus,listData){  //过滤不要的父级
      if(roleMenus.length==0){
        return []
      }

      var new_roleMenus =[]
      var roleMenus2 =[]
      for(var i in listData){
        var item = listData[i]
        if(item['parent_id']){
          roleMenus2[item['parent_id']]=item['id']
        }
      }
      roleMenus2=Object.keys(roleMenus2)
      for(var i in roleMenus){
        var item = roleMenus[i]
        var has=util.ArrayFun.indexOfArr(roleMenus2,item)
        if(has==-1){
          new_roleMenus.push(roleMenus[i])
        }
      }

      return new_roleMenus
    },

    onClose(){
      this.$emit("onClose",false);  //函数参数为组合的keys
    },
    onSubmit(){
      var checkedkeys = this.$refs.tree.getCheckedKeys() //获取菜单选中的
      var checkedfather_halfkeys = this.$refs.tree.getHalfCheckedKeys() //获取父级半选状态（父级含有一个子元素时）
      var keys =[]
      if(checkedfather_halfkeys.length>0){
        keys = checkedkeys.concat(checkedfather_halfkeys)
      }else{
        keys = checkedkeys
      }
      
      this.$emit("onSubmit",{ role_id: this.roleinfo.id, checked_keys:keys });  //函数参数为组合的keys
    }
  },
 
};
</script>

<style scoped>

</style>
