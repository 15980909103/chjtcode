<template>
  <el-dialog
    width="55%"
    title="新房"
    :visible.sync="innerDialogVisible"
    append-to-body>
      <el-form :inline="true" :model="searchData" class="form-serch">
        <el-form-item label="类别">
          <el-cascader
            :options="categoryList"
            v-model="searchData.cate_id"
            :props="{
                     value:'id',
                     label:'title'
                    }"
            :show-all-levels="false"
          ></el-cascader>
        </el-form-item>

        <el-form-item>
          <el-input v-model="searchData.name" placeholder="请输入文章名称" prefix-icon="el-icon-search"></el-input>
        </el-form-item>
        <el-form-item label="状态">
          <el-select v-model="searchData.status" placeholder="请选择">
            <el-option label="全部" value="-1"></el-option>
            <el-option label="已启用" value="1"></el-option>
            <el-option label="已禁用" value="0"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
        </el-form-item>
      </el-form>

      <!-- ========================================================= -->
      <el-table :data="tableData" style="width: 100%" @row-click = "innerSelcetRow" >
        <el-table-column prop="id" label="ID"  align="center"></el-table-column>
        <el-table-column prop="name" label="名称"  align="center"></el-table-column>

      </el-table>
      <!-- ========================================================= -->

      <!-- ============分页=============== -->
      <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>
  </el-dialog>
</template>
<script>
var util = require("@/utils/util.js");
import paginationBox from '@/components/common/pagination.vue';
export default {
  components: {
			'pagination-box': paginationBox,
	},
  props:{
    show: { //父组件需要show.sync
      type: Boolean,
      default () {
        return false;
      }
    },
    city:{
      type:String,
      default:''
    },
  },
  data() {
    return {
      searchData: {//  搜索数据
        cate_id:[9,'all'],
        name: "",
        status: "-1",//全部
        city:'',
      },
      categoryList:[],
      page_loading : false,
      tableData: [],
      innerDialogVisible: false,
      // cate_id:['all','全部'],
      pagination: {}, //分页数据
    };
  },
  watch:{
    innerDialogVisible(newVal){
      this.$emit('update:show', newVal)
    },
    show(newVal){
      this.innerDialogVisible = newVal
    },
  },
  created: function(){
    this.getCategoryList()
    this.getList(this.searchData)
  },
  methods: {
    getList(searchdata={}){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true
      searchdata.city = this.city;
      util.requests("post", {
          url: "news/getList",
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
    innerSelcetRow(row){
      //console.log(row)
      this.innerDialogVisible = false
      this.$emit('innerFormData',row)
    },

    getCategoryList(){
      var that = this

      util.requests("post", {
          url: "news/getCategoryListAll",
          data:{pid:9}
      }).then(res => {
          if(res.code==1){
            that.setDataArr({
              categoryList : res.data,
            })
            let arr = new Array()
            arr['id']   = 'all'
            arr['title'] = "全部"
            that.categoryList[0].children.unshift(arr);
          }else{
            util.Message.error(res.msg);
          }
      });
    },
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
  .count{
    width: 50px;
    margin-bottom: 5px;
  }
  .pagination {
    margin-top: 20px;
    text-align: right;
  }
}
</style>
