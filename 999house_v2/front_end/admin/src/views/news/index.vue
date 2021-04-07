<template>

  <div class="mapmange_container">
    <el-card>
      <div slot="header" class="clearfix">
        <span style="display: inline-block;vertical-align: middle">资讯创作</span>
        <el-select style="float: right;"  v-model="searchData.region_no" placeholder="请选择城市">
          <el-option
            v-for="item in myCitys"
            :key="item.id"
            :label="item.cname"
            :value="item.id">
          </el-option>
        </el-select>
      </div>
      <div class="mapmange_content">
        <el-form :inline="true" :model="searchData" class="form-serch">
          <div style="text-align: center">
            <el-form-item>
              <el-input v-model="searchData.name" placeholder="请输入文章名称" prefix-icon="el-icon-search"></el-input>
              <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
            </el-form-item>
          </div>
<!--          <el-form-item>-->
<!--            <mycity-select :city_no.sync='searchData.region_no' :unlimitedCity='true' :isMy='true' model='3' siteAreasUrl='city/siteAreas'></mycity-select>-->
<!--          </el-form-item>-->
        <div>
          <el-form-item label="标签">
            <el-cascader
              :options="categoryList"
              v-model="searchData.cate_id"
              :props="{
                     value:'id',
                     label:'name'
                    }"
              :show-all-levels="false"
            ></el-cascader>
          </el-form-item>
          <el-form-item label="楼盘" prop="forid">
            <el-autocomplete
              class="inline-input"
              v-model="searchData.forid"
              :fetch-suggestions="querySearch"
              placeholder="请输入内容"
              :trigger-on-focus="false"
              @select="handleSelect"
            ></el-autocomplete>
<!--            <el-col>-->
<!--                <span @click="changeInnerShow">-->
<!--                  <el-input style="width:100%;display:none;"  v-model="searchData.forid" placeholder="请选择新房"></el-input>-->
<!--                  <el-input style="width:100%" :disabled='true'  v-model="searchData.forname" placeholder="请选择新房"></el-input>-->
<!--                </span>-->
<!--            </el-col>-->
<!--            <el-col :span="4" style="text-align: right;">-->
<!--              <el-button  @click="clearInner">清空</el-button>-->
<!--            </el-col>-->
          </el-form-item>
          <estates-new :region_no='searchData.region_no' :show.sync='innerVisible' @innerFormData='innerFormData'></estates-new>


          <el-form-item label="状态">
            <el-select v-model="searchData.status" placeholder="请选择">
              <el-option label="全部" value="-1"></el-option>
              <el-option label="显示" value="1"></el-option><!--显示等于发布-->
              <el-option label="隐藏" value="0"></el-option><!--隐藏等于禁用-->
              <el-option label="保存" value="2"></el-option> <!--保存等于草稿-->
            </el-select>
          </el-form-item>
          <el-form-item label="时间">
            <el-date-picker
              style="width:100%"
              v-model="searchTime"
              value-format="yyyy-MM-dd" format="yyyy-MM-dd"
              type="daterange"
              range-separator="-"
              start-placeholder="开始日期"
              end-placeholder="结束日期">
            </el-date-picker>
          </el-form-item>
        </div>
        </el-form>

        <!-- ========================================================= -->
        <div class="fun_box">
          <div class="left_btn">
            <div class="left_item">置顶</div>
            <div class="left_item">显示</div>
            <div class="left_item">隐藏</div>
            <div class="left_item">发布</div>
            <div class="left_item">
              <i class="el-icon-menu"></i>
            </div>
            <svg-icon class="left_item" icon-class="list" />
          </div>

          <el-button type="primary" plain icon="el-icon-plus" @click="openPage({url:'/news/edit' })">新增</el-button>
        </div>
        <el-table :data="tableData" style="width: 100%">
          <el-table-column
            type="selection"
            width="55">
          </el-table-column>
<!--          <el-table-column prop="id" label="ID" width="80" align="center"></el-table-column>-->
          <el-table-column prop="title" label="内容" align="center">
<!--            <template slot-scope="scope">-->
<!--              <img style="width:110px; height:110px;" :src="getRealImgUrl(scope.row.cover)"/>-->
<!--            </template>-->
          </el-table-column>
          <el-table-column prop="label_tag" label="标签" align="center"></el-table-column>
          <el-table-column prop="city_list" label="地区" align="center"></el-table-column>
          <el-table-column prop="num_read_real" label="阅读数" align="center"></el-table-column>
          <el-table-column prop="num_read_real" label="评论数" align="center"></el-table-column>
          <el-table-column prop="status" label="状态"  align="center"></el-table-column>
          <el-table-column prop="create_time" label="链接地址"  align="center">
            <template slot-scope="scope">
              <a :href="scope.row.href">PC端</a>
              <a :href="scope.row.href">移动端</a>
            </template>
          </el-table-column>
<!--          <el-table-column label="统计信息" align="center">-->
<!--            <template slot-scope="scope">-->
<!--              <el-popover trigger="hover" placement="top">-->
<!--              <span>-->
<!--                <div>真实阅读数：<el-tag class="count" effect="plain" size="mini">{{(scope.row && scope.row.num_read_real)? format9999Num(scope.row.num_read_real): 0}}</el-tag></div>-->
<!--                <div>真实收藏数：<el-tag class="count" effect="plain" size="mini">{{(scope.row && scope.row.num_collect_real)? format9999Num(scope.row.num_collect_real): 0}}</el-tag></div>-->
<!--                <div>真实转发数：<el-tag class="count" effect="plain" size="mini">{{(scope.row && scope.row.num_share_real)? format9999Num(scope.row.num_share_real): 0}}</el-tag></div>-->
<!--                <div>真实点赞数：<el-tag class="count" effect="plain" size="mini">{{(scope.row && scope.row.num_thumbup_real)? format9999Num(scope.row.num_thumbup_real): 0}}</el-tag></div>-->
<!--              </span>-->
<!--                <div slot="reference" class="name-wrapper">-->
<!--                <span>-->
<!--                  <div>阅读数：<el-tag class="count" effect="plain" size="mini">{{(scope.row && scope.row.num_read)? format9999Num(scope.row.num_read): 0}}</el-tag></div>-->
<!--                  <div>收藏数：<el-tag class="count" effect="plain" size="mini">{{(scope.row && scope.row.num_collect)? format9999Num(scope.row.num_collect): 0}}</el-tag></div>-->
<!--                  <div>转发数：<el-tag class="count" effect="plain" size="mini">{{(scope.row && scope.row.num_share)? format9999Num(scope.row.num_share): 0}}</el-tag></div>-->
<!--                  <div>点赞数：<el-tag class="count" effect="plain" size="mini">{{(scope.row && scope.row.num_thumbup)? format9999Num(scope.row.num_thumbup): 0}}</el-tag></div>-->
<!--                </span>-->
<!--                </div>-->
<!--              </el-popover>-->
<!--            </template>-->
<!--          </el-table-column>-->

          <el-table-column prop="create_time" label="发布时间" width="150" align="center">
            <template slot-scope="scope">
              {{formatDate(scope.row.release_time)}}
            </template>
          </el-table-column>

<!--          <el-table-column label="发布状态" width="80" align="center">-->
<!--            <template slot-scope="scope">-->
<!--              <el-tag v-if= "scope.row.status1 == 2" type="info" >保存 </el-tag>-->
<!--              <el-switch v-else @change='(val)=>{switchChange(scope.row.id,val,scope.row.p_cate_id)}' v-model="scope.row.status" :active-value="1" :inactive-value="0" ></el-switch>-->
<!--            </template>-->
<!--          </el-table-column>-->

<!--          <el-table-column label="排序" width="120" align="center">-->
<!--            <template slot-scope="scope">-->
<!--              <el-input v-model="scope.row.sort" @change="(val)=>{sortChange(scope.row.id,val,scope.row.p_cate_id)}" placeholder="越大越靠前" size="medium"></el-input>-->
<!--            </template>-->
<!--          </el-table-column>-->

          <el-table-column label="操作"  align="center">
            <template slot-scope="scope">
              <el-button type="primary" size="mini" @click="openPage({url:'/news/edit',data:{p_cate_id:scope.row.p_cate_id,id:scope.row.id}})">编辑</el-button>
<!--              <el-button type="danger" size="mini" @click="doDel(scope.row.id,scope.row.p_cate_id)">删除</el-button>-->
            </template>
          </el-table-column>
        </el-table>
        <!-- ========================================================= -->

        <div class="grid_box">
<!--          <el-checkbox-group>-->
            <div class="grid_item" v-for="(item,index) in tableData" :key="index">
<!--              <el-checkbox v-model="item.id"></el-checkbox>-->
              <el-image ></el-image>
              <p>2020上半年中国房地产市场总结&下半年</p>
              <div>
                <span>10评论</span>
                <span>5000阅读</span>
                <span>发布</span>
              </div>
              <div>
                <a href="">PC端</a>
                <a href="">移动端</a>
                <div>编辑</div>
              </div>
            </div>
<!--          </el-checkbox-group>-->
        </div>
        <!-- ============分页=============== -->
        <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>
      </div>
    </el-card>


  </div>
</template>
<script>
var util = require("@/utils/util.js");
import paginationBox from '@/components/common/pagination.vue';
import MycitySelect from '@/components/common/MycitySelect.vue';
import EstatesNew from '@/components/InnerTable/EstatesNew.vue';
export default {
  components: {
      'pagination-box': paginationBox,
      'mycity-select': MycitySelect,
      'estates-new': EstatesNew,
	},
  data() {
    return {
      searchData: {//  搜索数据
        cate_id:[9,'all'],
        name: "",
        status: "-1",//全部
        forname:'',
        forid:0
      },
      categoryList:[],
      searchTime:[],
      page_loading : false,
      innerVisible: false,
      tableData: [],
      // cate_id:['all','全部'],
      pagination: {}, //分页数据
      myCitys:[],
    };
  },
     watch:{
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
    if(this.$urlData['status']){
      this.searchData.status = String(this.$urlData['status']);
    }

    this.myCitys = this.$store.getters.userinfo&&this.$store.getters.userinfo.region_nos_info?this.$store.getters.userinfo.region_nos_info:[]
    this.getCategoryList()
    this.getList(this.searchData)
  },
  methods: {
    handleSelect(item) {
      console.log(item);
    },
    querySearch(queryString, cb) {
      var restaurants = this.restaurants;
      var results = queryString ? restaurants.filter(this.createFilter(queryString)) : restaurants;
      // 调用 callback 返回建议列表的数据
      cb(results);
    },
    createFilter(queryString) {
      return (restaurant) => {
        return (restaurant.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
      };
    },

     innerFormData(e){
      console.log(e)
      this.searchData.forid = e.id
      this.searchData.forname = e.name
      console.log(this.searchData)
    },
    clearInner(){

      this.searchData.forname = '';
      this.searchData.forid = 0;
    },
    changeInnerShow(){
      if(!this.searchData.region_no){
        this.$message({
          type: 'error',
          message: '请先选择城市'
        });
        return
      }
      this.innerVisible = true
    },
    getList(searchdata={}){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

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
            arr['name'] = "全部"
            that.categoryList[0].children.unshift(arr);
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
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
    formatDate(val){
     return util.DataFun.getFormatDate(val,2)
    },

    doDel(id,p_cate_id){
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
            url: "news/del",
            data: {id: id,p_cate_id:p_cate_id}
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
          url: "news/changeSort",
          data: {id: id,sort: val,p_cate_id:p_cate_id}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){

            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
        that.onSerch()
      });
    },
    switchChange(id,val,p_cate_id){
      //console.log(id,val)
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "news/enable",
          data: {id: id,status: val,p_cate_id:p_cate_id}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){

            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
        that.onSerch()
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
  .count{
    width: 50px;
    margin-bottom: 5px;
  }
  .pagination {
    margin-top: 20px;
    text-align: right;
  }
}
.fun_box{
  display: flex;
  justify-content: space-between;
  .left_btn{
    .left_item{
      display: inline-block;
      vertical-align: middle;
      padding: 0 12px;
      margin: 0 5px;
      cursor: pointer;
    }
  }
}
.grid_box{

  .grid_item{
    width: 25%;
    min-width: 220px;
    image{
      width: 220px;
      height: 124px;
    }
    .title{
      font-size: 14px;
      color: #212121;
    }

  }
}
</style>
