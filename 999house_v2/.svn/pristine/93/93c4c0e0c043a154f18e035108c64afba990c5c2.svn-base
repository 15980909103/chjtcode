<template>
  <div class="mapmange_container">
    <div class="mapmange_content">
      <el-form :inline="true" :model="searchData" class="form-serch">
        <el-form-item>
          <el-input v-model="searchData.name" placeholder="请输入商品名称" prefix-icon="el-icon-search"></el-input>
        </el-form-item>
        <el-form-item label="状态">
          <el-select v-model="searchData.status" placeholder="请选择">
            <el-option label="全部" value="-1"></el-option>
            <el-option label="上架" value="1"></el-option>
            <el-option label="下架" value="0"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
        </el-form-item>
        <!-- <el-form-item>
          <el-button type="danger" icon="el-icon-circle-plus-outline" @click="openPage({url:'/mall/goodsEdit'})">新增</el-button>
        </el-form-item> -->
      </el-form>

      <!-- ========================================================= -->
      <el-table :data="tableData" style="width: 100%">
        <el-table-column prop="id" label="ID" width="80" align="center"></el-table-column>
        <el-table-column prop="cover" label="封面图" width="85" align="center">
          <template slot-scope="scope">
             <img style="width:62px; height:62px;" :src="getRealImgUrl(scope.row.cover)"/>
          </template> 
        </el-table-column>
        <el-table-column prop="name" width="180" label="商品名称" align="center"></el-table-column>
        <el-table-column prop="price" label="价格/佣金" width="100" align="center">
          <template slot-scope="scope">
             <div>价格：{{scope.row.price}}</div>
             <div>佣金：{{scope.row.commission}}</div>
          </template> 
        </el-table-column>

        <el-table-column label="规格设置" align="center">
          <template slot-scope="scope">
            <div v-for="(item,index) in scope.row.attr" :key="index">名称：{{item.attr_name}} | 价格：{{item.attr_price}} | 佣金：{{item.attr_commission}}</div> 
          </template> 
        </el-table-column>

        <!-- <el-table-column label="状态" width="80" align="center">
          <template slot-scope="scope">
            <el-switch @change='(val)=>{switchChange(scope.row.id,val)}' v-model="scope.row.status" :active-value="1" :inactive-value="0" ></el-switch>
          </template> 
        </el-table-column> -->

        <el-table-column label="操作"  align="center">
          <template slot-scope="scope">
            <el-button type="primary" size="mini" v-iscan='"mall/goodsEdit"' @click="openPage({url:'/mall/goodsEdit',data:{id:scope.row.id} })">编辑</el-button>
            <!-- <el-button type="danger" size="mini" v-iscan='"mall/goodsDel"' @click="doDel(scope.row.id)">删除</el-button> -->
          </template>
        </el-table-column>
      </el-table>
      <!-- ========================================================= -->

      <!-- ============分页=============== -->
      <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>
    </div>
  </div>
</template>
<script>
var util = require("@/utils/util.js");
import paginationBox from '@/components/common/pagination.vue';
export default {
  components: {
			'pagination-box': paginationBox,
	},
  data() {
    return {
      searchData: {//  搜索数据
        name: "",
        status: "-1",//全部
      },
      
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据
    };
  },
  created: function(){
    console.log(this.$pageShowBtns)
    this.getList()
  },
  methods: {
    getList(searchdata={}){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "mall/getGoodsList",
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
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
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
            url: "mall/goodsDel",
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

    switchChange(id,val){
      //console.log(id,val)
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "mall/goodsEnable",
          data: {id: id,status: val}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.onSerch()
            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
      });
    },

    openPage: util.openPage
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