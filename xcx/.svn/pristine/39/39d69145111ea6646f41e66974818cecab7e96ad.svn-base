<template>
  <div class="_container">
    <el-form :inline="true" :model="searchData" class="form-serch">
        <el-form-item label="下单时间">
           <el-date-picker
             style="width:100%"
            v-model="searchTime"
             value-format="yyyy-MM-dd HH:mm" format="yyyy-MM-dd HH:mm"
             type="datetimerange"
            range-separator="-"
            start-placeholder="开始日期"
            end-placeholder="结束日期">
          </el-date-picker>
        </el-form-item>
        <el-form-item label="订单号">
          <el-input style="width:100%" v-model="searchData.orderno" placeholder="请输入订单号" prefix-icon="el-icon-search"></el-input>
        </el-form-item>
        <el-form-item label="分销商昵称">
          <el-input style="width:100%" v-model="searchData.distributor_user_name" placeholder="请输入分销商昵称" prefix-icon="el-icon-search"></el-input>
        </el-form-item>
        <el-form-item label="状态">
          <el-select v-model="searchData.status" placeholder="请选择">
            <el-option label="全部" value="all"></el-option>
            <el-option label="已支付" value="1"></el-option>
            <el-option label="未支付" value="0"></el-option>
          </el-select>
        </el-form-item>  
        
        <el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
          <el-button type="success"  @click="onReset">重置</el-button>
        </el-form-item>
    </el-form>

    <el-table :data="tableData" style="width:100%">
      <el-table-column prop="user_nickname" label="购买者昵称" width="80" align="center">
        <template slot-scope="scope">
           {{scope.row.user_nickname}}
        </template>
      </el-table-column>
      <el-table-column prop="order_no" label="订单号" align="center"></el-table-column>
      <el-table-column label="商品名称" align="center">
        <template slot-scope="scope">
           {{scope.row.goods_name}}<span v-if="scope.row.attr"> | {{scope.row.attr.attr_name}}</span>
        </template>
      </el-table-column>
      <el-table-column prop="isfirst" label="类型" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.isfirst==1">开户</span>
          <span v-else-if="scope.row.isfirst==2">续费</span>
          <span v-else>-</span>
        </template>
      </el-table-column>
      <el-table-column prop="real_price" label="支付金额" align="center"></el-table-column>
      <el-table-column prop="isfirst" label="状态" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.status==1">已支付</span>
          <span v-else-if="scope.row.status==0">待支付</span>
        </template>
      </el-table-column>
      <el-table-column label="下单时间" align="center">
        <template slot-scope="scope">
           {{getFormatDate(scope.row.create_time)}}
        </template>
      </el-table-column>
      <el-table-column prop="distributor_user_name" label="上级分销商" align="center"></el-table-column>
      <el-table-column prop="commission" label="佣金" align="center"></el-table-column>
    </el-table>

    <!-- ============分页=============== -->
    <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>


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
      searchTime:[],
      searchData: {
        goods_id:'',
        orderno:'',
        distributor_user_name:'',
        startdate: '',
        enddate: '',
        status: 'all',
        isfirst: 'all',
        dotype: 'all',
      },
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据
    };
  },
  watch: {
    searchTime(newVal){
      if(newVal){
        this.searchData.startdate = newVal[0]
        this.searchData.enddate = newVal[1]
      }else{
        this.searchData.startdate = ''
        this.searchData.enddate = ''
      }
    }
  },
  created: function(){
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
          url: "commission/getDistributorOrderList",
          data: searchdata
      }).then(res => {
          that.page_loading = false
          if(res.code==1){
            console.log(res);
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
    onReset(){
      var searchData = {
        goods_id:'',
        orderno:'',
        distributor_user_name:'',
        startdate: '',
        enddate: '',
        status: 'all',
        isfirst: 'all',
        dotype: 'all',
      };
      this.setDataArr({
        searchData:searchData,
        searchTime:[],
      })
      this.getList()
    },
    onSerch() {
      this.getList(this.searchData)
    },
    //分页操作
    pageChange: function(page) {
      let post_data = Object.assign({},this.searchData);
      post_data.page = page;
      this.getList(post_data)
    },


    getFormatDate(time){
     return util.DataFun.getFormatDate(time,2)
    },
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },
    
  }
};
</script>
<style lang="scss" scoped>
._container {
  margin-top: 20px;
  padding: 20px;
  background: #fff;
  .pagination {
    text-align: right;
    margin-top: 20px;
  }
  .form-serch {
    text-align: left;
  }
  .user-dialog {
    .el-form{
      padding:0 10px;
    }
    .el-form-item{
      margin-bottom: 0;
    }
    /deep/.el-form-item__label,/deep/.el-form-item__content{
      line-height: 32px;
    }
  }

}
</style>


