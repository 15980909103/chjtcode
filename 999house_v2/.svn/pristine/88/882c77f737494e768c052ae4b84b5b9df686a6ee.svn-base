<!--
  动态标签组件（动态添加/删除）
-->
<template>
  <div class="tag-content">
      <el-tag
      :key="index"
      v-for="(item,index) in data"
      closable
      :disable-transitions="false"
      @close="handleDel(item,index)">
      {{item.name}}
    </el-tag>
    <el-input
      class="input-new-tag"
      v-if="inputVisible"
      v-model="inputValue"
      ref="saveTagInput"
      size="small"
      @keyup.enter.native="oninputTagVal"
      @blur="oninputTagVal"
    >
    </el-input>
    <el-button v-else class="button-new-tag" size="small" @click="showInput">+ {{button_name}}</el-button>
    <div class="el-form-item__error">{{inputErrorMsg}}</div>
  </div>
</template>

<script>
var util = require("@/utils/util.js");
export default {
  name: 'dy-tags',
  props: {
     //父组件传入的data使用.sync做同步关联，在更新数据时子组件调用this.$emit('update:data', newValue) 字段显示更新，使父子组件数据同步更新
     //data对应父组件为form的属性时，该属性在初始(或者编辑请求返回为空数据)时，其值要赋值为[]
    data: { 
      type: Array,
      default () {
        return [];
      }
    },
    button_name:{ 
      type: String,
      default () {
        return '添加标签';
      }
    },
    limitNum: { //选项设置数量上限 0为数量无上限
      type: Number,
      default () {
        return 0;
      }
    },
  },

  data() {
    return {
      inputVisible: false,
      inputValue: '',
      inputErrorMsg: '' //显示选项的错误信息
    };
  },
  methods: {
    isEmpty(msg){//用于父组件提交时手动检验触发,检查是否为空
      if(this.data.length>0){
        return false    
      }
      this.inputErrorMsg = msg
      return true
    },
    //点击删除
    handleDel(item,index) {
      this.data.splice(index, 1);
      this.inputErrorMsg= ''//清除错误信息
    },
    //显示输入框
    showInput() {
      var dataArr = this.data
      //判断是否达到上限个数
			if(this.limitNum!=0 && dataArr.length >= this.limitNum){
				this.inputErrorMsg= '选项设置已达到上限个数 '+this.limitNum+' 个'
				return
      }
      
      this.inputVisible = true;
      this.$nextTick(_ => {
        this.$refs.saveTagInput.$refs.input.focus();
      });
    },
    //监听tag输入设置
    oninputTagVal() {
      let inputValue = this.inputValue;
      var dataArr= this.data
      if(inputValue) {
        //获取值的列表
        var valList=util.ArrayFun.column(dataArr,'name')
        //判断选项是否存在
				let idx= util.ArrayFun.indexOfArr(valList,inputValue)
				if(idx!=-1){
					this.inputErrorMsg= '该选项名称已经存在，所以无添加该选项'
				}else{
          var max= util.ArrayFun.getMaxOrMin(this.data, 'sort', 'max') //获取目前最大的key以设置新的key
          dataArr.push({sort: Number(max)+1, name: inputValue});//添加选项
					this.inputErrorMsg= ''
				} 
      }

      this.inputVisible = false;
      this.inputValue = '';
    }
}
  
};
</script>

<style lang="scss" scoped>

.tag-content{
	/* margin-bottom: 34px; */
	/deep/ .el-tag{
		margin-right: 10px;
	}
	.el-form-item__error{
		z-index: 99;
	}
}	
</style>
