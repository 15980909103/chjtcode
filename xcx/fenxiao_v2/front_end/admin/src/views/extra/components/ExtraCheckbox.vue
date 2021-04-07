
<template>
	<div >
		<el-checkbox :indeterminate="isIndeterminate" v-model="isCheckedAll" @change="handleCheckAllChange">全部</el-checkbox>
		<el-checkbox-group v-model="checkeds" @change="handleCheckeditemChange">
			<el-checkbox v-for="(item,index) in dataAll" :label="item.key" :key="index">{{item.name}}</el-checkbox>
		</el-checkbox-group>
	</div>  
</template>

<script>
  /* 应用模块选择组件 */
  
	export default {
		name: "ExtraCheckbox",
		props: {
			dataAll :{
				type: Array
			}
		},
		data() {
			return {
				isIndeterminate: false,
				isCheckedAll: false,
				checkeds: [],//选中的
			}
		},
		computed: {
			dataAllForKeys(){
				//获取key一列
				var arr = this.dataAll
				var len = arr.length
				var keys = [];
				for (var i = 0; i < len; i++) {
						keys[i] = arr[i]['key']
				};
				return keys
			}
		},
		methods: {
			setCheckeds(arr){//用于父组件调用初始值
				if(arr=='all'){
					var checkeds = this.dataAllForKeys
				}else{
					var checkeds = arr.split(',')
				}
				this.checkeds = checkeds
				this.handleCheckeditemChange(checkeds)
			},
      		//点击全选操作
			handleCheckAllChange(val) {
				var keys = this.dataAllForKeys
				this.checkeds = val ? keys : [];
				this.isIndeterminate = false;
				this.$emit('onChecked', this.checkeds, val)
			},
			//每次点选的操作
			handleCheckeditemChange(value) {
				var total = this.dataAll.length
				let checkedCount = value.length;
				this.isCheckedAll = checkedCount === total;
				this.isIndeterminate = checkedCount > 0 && checkedCount < total;
				this.$emit('onChecked', this.checkeds, this.isCheckedAll)
			},
		}
	};
</script>
<style >
	


</style>
