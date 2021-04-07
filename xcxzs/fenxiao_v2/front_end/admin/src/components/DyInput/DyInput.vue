<!--
  动态输入框（动态添加/删除）
-->
<template>
  <div >
    <el-col :lg="8" :sm="12" :xs="24" v-for="item in dataListOrderByName" :key="item.name">
      <el-form-item :label="item.name" label-width="80px">
        <el-input placeholder="请输入数据点名称" @change="(val)=>{return onInputAttrs(val,item.name)}" v-model="attrsValue[item.name]"></el-input>
      </el-form-item>
    </el-col>
  </div>
</template>

<script>
var util = require("@/utils/util.js");
export default {
  name: 'dy-tags',
  props: {
     //父组件传入的data使用.sync做同步关联，在更新数据时子组件调用this.$emit('update:data', newValue) 字段显示更新，使父子组件数据同步更新
     //data对应父组件为form的属性时，该属性在初始(或者编辑请求返回为空数据)时，其值要赋值为[]
    dataList: { 
      type: Array,
      default () {
        return [];
      }
    },
    form_attrs: { //用于表单提交
      type: Object,
      default () {
        return {};
      }
    },
  },
  computed:{
    dataListOrderByName(){
      var dataListOrderByName= {}
      var attrsObj = {} //用于表单提交，存储给现有类别下的属性列表，去除变更过的附加属性
      var attrsValue={}//输入框的值
      if(this.dataList){
        //转换以sort排序 
        dataListOrderByName= util.ArrayFun.ArraytoKeyObj(this.dataList,'name')
        //转换以sort排序 针对表单的附加属性赋值
        var formData_attrsOrderByName= util.ArrayFun.ArraytoKeyObj(this.form_attrs,'name')  
        for(var i in dataListOrderByName){
          var item= dataListOrderByName[i]
          //去除变更删除的属性
          if(formData_attrsOrderByName[i]){
            //this.attrsValue[i] = formData_attrsOrderByName[i].val
            attrsObj[i] = formData_attrsOrderByName[i] 
            attrsValue[i] = formData_attrsOrderByName[i].val
          }
        }
      }
      
      this.attrsValue = Object.assign({},attrsValue)
      this.$emit('form_attrs:update',Object.assign({},attrsObj))
      return dataListOrderByName
    }
  },
  data() {
    return {
      attrsValue: {},//输入框的值
    };
  },
  methods: {
    //显示输入框
    onInputAttrs(val,key){
      var form_attrs= this.form_attrs
      if(!form_attrs){
        console.log('重新赋值需form_attrs设定为{}',form_attrs)
      }
  
      form_attrs[key]={
        sort: this.dataListOrderByName[key].sort,
        name: this.dataListOrderByName[key].name,
        val: val
      }
      console.log(form_attrs)
      this.$emit('form_attrs:update',form_attrs)
    },
  }  

  
};
</script>

<style lang="scss" scoped>

</style>
