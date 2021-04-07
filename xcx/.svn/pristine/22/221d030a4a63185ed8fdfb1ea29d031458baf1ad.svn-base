<template>
	<div class="pagination-box">
		<el-pagination  layout="total, prev, pager, next, jumper"  :page-size="parseInt(pagination.pagesize)" :current-page="parseInt(pagination.page)" :page-count="parseInt(pagination.pagecount)" @current-change="pageChange" ></el-pagination>
	</div>  
</template>

<script>
  //数据格式pagination:{pagesize //请求数量,pagecount//总页数,page//当前页}
  //调用方式<pagination-box :pagination="pagination" v-on:pageChange="pageChange"></pagination-box>
  
	export default {
		name: "pagination-box",
		props: ['pagination'],
		data() {
			return {
				
			}
		},
		methods: {
      //页面点击跳转
      pageChange(page){
        window.scrollTo(0, 0);//跳转时页面置顶
        //val为跳转的页数
        this.$emit('pageChange',page)
      }
		}
	};
</script>
<style >
	
.pagination-box{
  text-align: right;
  margin-top: 15px
}

</style>
