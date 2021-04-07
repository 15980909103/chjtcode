<template>
  <div class="_container">
    <div class="tb-top" style="float:right;margin-bottom: 30px;">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item>
          <mycity-select :city_no.sync='searchData.region_no' :area_no.sync='searchData.area' :business_area_no.sync='searchData.business_area' :street_no.sync='searchData.street' :isMy='true' model='5' siteAreasUrl='city/siteAreas'></mycity-select>
        </el-form-item>
        <el-form-item label="楼盘">
          <el-input v-model="searchData.name" placeholder="请输入楼盘名称" prefix-icon="el-icon-search"></el-input>
        </el-form-item>
        <el-form-item label="销售状态">
          <el-select v-model="searchData.sale_status" placeholder="请选择">
            <el-option label="全部" value="-1"></el-option>
            <el-option v-for="(item, i) in const_estates_new_sale_status" :key="i" :label="item" :value="i"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="状态">
          <el-select v-model="searchData.status" placeholder="请选择">
            <el-option label="全部" value="-1"></el-option>
            <el-option label="上架" value="1"></el-option>
            <el-option label="下架" value="0"></el-option>
            <el-option label="草稿" value="2"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="推荐">
          <el-select v-model="searchData.recommend" placeholder="请选择">
            <el-option label="全部" value="-1"></el-option>
            <el-option label="是" value="1"></el-option>
            <el-option label="否" value="0"></el-option>
          </el-select>
        </el-form-item>
        <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="openPage({url: '/estates/estatesnew_edit'})">新增</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="name" label="楼盘名称" width="180" align="center">
        <template slot-scope="scope">
          <div>{{scope.row.name}}</div>
          <el-tag size="mini" >{{scope.row.city_str}}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="list_cover" label="封面图" width="120" align="center">
        <template slot-scope="scope">
          <el-image v-if="scope.row.list_cover" style="width: 90px;" :src="getRealImgUrl(scope.row.list_cover)"></el-image>
        </template>
      </el-table-column>
      <el-table-column label="统计信息" align="center">
        <template slot-scope="scope">
            <el-popover trigger="hover" placement="top">
              <span>
                <div>真实统计:</div>
                <div>阅读数：<el-tag class="count" effect="plain" size="mini">{{(scope.row && scope.row.num_read_real)? format9999Num(scope.row.num_read_real): 0}}</el-tag></div>
                <div>收藏数：<el-tag class="count" effect="plain" size="mini">{{(scope.row && scope.row.num_collect_real)? format9999Num(scope.row.num_collect_real): 0}}</el-tag></div>
                <div>转发数：<el-tag class="count" effect="plain" size="mini">{{(scope.row && scope.row.num_share_real)? format9999Num(scope.row.num_share_real): 0}}</el-tag></div>
              </span>
              <div slot="reference" class="name-wrapper">
                <span>
                  <div>阅读数：<el-tag class="count" effect="plain" size="mini">{{(scope.row && scope.row.num_read)? format9999Num(scope.row.num_read): 0}}</el-tag></div>
                  <div>收藏数：<el-tag class="count" effect="plain" size="mini">{{(scope.row && scope.row.num_collect)? format9999Num(scope.row.num_collect): 0}}</el-tag></div>
                  <div>转发数：<el-tag class="count" effect="plain" size="mini">{{(scope.row && scope.row.num_share)? format9999Num(scope.row.num_share): 0}}</el-tag></div>
                </span>
              </div>
            </el-popover>
        </template>
      </el-table-column>
      <el-table-column prop="sale_status" width="80" label="销售状态" align="center">
        <template slot-scope="scope">
          {{const_estates_new_sale_status[scope.row.sale_status]}}
        </template>
      </el-table-column>
      <el-table-column prop="status" width="85" label="发布状态" align="center">
        <template slot-scope="scope">
          <el-switch v-if="scope.row.release_status!=2"  @change="(val)=>{switchChange(scope.row.id,val,'status')}" v-model="scope.row.status" :active-value="1" :inactive-value="0" ></el-switch>
          <el-tag v-else type='info' >草稿</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="recommend" width="85" label="推荐" align="center">
        <template slot-scope="scope">
          <el-switch @change="(val)=>{switchChange(scope.row.id,val,'recommend')}" v-model="scope.row.recommend" :active-value="1" :inactive-value="0" ></el-switch>
        </template>
      </el-table-column>
      <el-table-column label="排序" width="80" align="center">
          <template slot-scope="scope">
             <el-input v-model="scope.row.sort" @change="(val)=>{sortChange(scope.row.id,val)}" placeholder="越大越靠前" size="medium"></el-input>
          </template>
        </el-table-column>

      <el-table-column  label="操作" align="center">
        <template slot-scope="scope">
          <el-button type="success" size="mini" @click="openPage({url: '/estates/estatesnew_edit', data:{id:scope.row.id}})">编辑</el-button>
          <el-button type="danger" size="mini" @click="del(scope.row.id,scope.$index)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <!-- ============分页=============== -->
    <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>

  </div>
</template>
<script>
  // import { log } from 'util';
  var util = require("@/utils/util.js");
  import paginationBox from '@/components/common/pagination.vue';
  import ImgUpload from '@/components/common/ImgUpload.vue';
  import baseMixin from  '@/mixin/baseMixin';
  import MycitySelect from '@/components/common/MycitySelect.vue';
  import constMixin from  '@/mixin/constMixin';

  export default {
    components: {
        'pagination-box': paginationBox,
        'mycity-select': MycitySelect,
    },
    mixins: [baseMixin,constMixin],
    data() {
      return {
        formLabelWidth: "123px",
        page_loading:'',
        searchData:{
          status : '-1',
          sale_status : '-1',
          recommend : '-1',
          region_no: "",
          area: "",
          business_area:"",
          street:"",
        },
        tableData: [],
        formData:{
          name:'',
          status: '0',
        },
        pagination: {}, //分页数据
      }
    },
    created: function(){
      let that = this
      that.getList(that.searchData)
      that.getEstatesNewSaleStatus();// 初始化销售状态常量
    },

    methods:{
      getList(searchdata={}){   //获取所有数据，或按条件查找数据
        var that = this
        util.requests("post",{
          url:"estates/getEstatesnewList",
          data: searchdata
        }).then(res=>{
          //console.log(res.data.list)
          that.tableData = res.data.list
          that.setDataArr({
            pagination : {
              page : res.data.current_page,
              pagecount : res.data.last_page,
              pagesize : Math.ceil(res.data.total / res.data.last_page)
            }
          })
        })
      },
      getRealImgUrl(url){
        return this.$getRealImgUrl(url)
      },

      onSearch(){
        this.getList(this.searchData);
      },
      switchChange(id,val,type){
        var that = this
        util.requests("post", {
          url: "estates/setEstatesStatus",
          data: {id: id,status: val,type: type}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
        });
      },

      del(id,val){   //确定删除
        this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          util.requests("post",{
            url: "estates/delEstates",
            data: {id:id}
          }).then(res => {
            // console.log(res); return;
            if(res.data.code==0){ alert("删除失败："+res.data.msg);return; }
            this.tableData.splice(val,1)
            this.$message({
              type: 'success',
              message: '删除成功!'
            });
          })
        })
      },

      //分页操作
      pageChange: function(page) {
        let post_data = Object.assign({},this.searchData);
        post_data.page = page;
        this.getList(post_data)
      },

      format9999Num(val){
        if(val){
          if(val>9999){
            val = '9999 +'
          }
          return val
        }else{
          return 0
        }
      },
    
      // 修改排序
      sortChange(id,val,p_cate_id){
        var that = this
        if (parseFloat(val).toString() == 'NaN') {
          util.Message.error('排序必须是数字');
    　　　return false;
    　　}

        if(that.page_loading){
            return
        }
        that.page_loading = true

        util.requests("post", {
            url: "estates/changeEstateSort",
            data: {id: id, sort: val}
          }).then(res => {
            that.page_loading = false
            if(res.code==1){
              that.onSearch()
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
  .tb-title{
    margin-top: 40px;
  }
  .type{
    float: right;
    position: relative;
    top: -164px;
    left: -100px;
  }
  .editimg{
    float: left;
  }
  .infoEdit{
    float: right;
  }

</style>


