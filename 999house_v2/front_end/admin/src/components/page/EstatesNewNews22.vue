<template>
  <div >
    <div class="tb-top">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="openPage({url:'/news/edit',data:{estate_id: estate_id}})">新增</el-button>
        <el-button icon="el-icon-circle-close" @click="openPage({url: '/estates/estatesnew'})">返回</el-button>
      </el-form>
    </div>
    <!-- 时间线 -->
    <div class="tb-title">
      <el-timeline class="timeline-box" v-if="tableData.length">
          <el-timeline-item v-for="(item,index) in tableData" :key="index" :timestamp="getFormatDate(item.create_time)" placement="top">
            <el-card>
              <h4>{{item.title}}</h4>
              <h5>{{item.describe}}</h5>
              <el-row>
                <el-col :span="20">
                  <span v-for="(item2,idx) in item.img_real_url" :key="idx">
                    <el-image 
                      style="width: 100px; height: 100px;margin-right:10px;"
                      :src="item2" 
                      :preview-src-list="item.img_real_url">
                    </el-image>
                  </span>
                </el-col>
                <el-col :span="4" style="text-align: right;">
                  <div><el-button type="success" size="mini" @click="openPage({url:'/news/edit',data:{estate_id: item.estate_id, id: item.id}})">编辑</el-button></div>
                  <div style="margin-top: 20px;"><el-button type="danger" size="mini" @click="del(item.id,index)">删除</el-button></div>
                </el-col>
              </el-row>
            </el-card>
          </el-timeline-item>
      </el-timeline>
      <div v-else>暂无数据</div>
    </div>

  </div>
</template>
<script>
  // import { log } from 'util';
  var util = require("@/utils/util.js");
  import paginationBox from '@/components/common/pagination.vue';
  import ImgUpload from '@/components/common/ImgUpload.vue';
  import ImgUpload2 from '@/components/common/ImgUpload2.vue';
  import baseMixin from  '@/mixin/baseMixin';
  import Tinymce from '@/components/Tinymce';

  export default {
    components: {
        'pagination-box': paginationBox,
        'img-upload2': ImgUpload2,
        'Tinymce' : Tinymce,
    },
    mixins: [baseMixin],
    data() {
      return {
        dialogVisibleEdit: false,
        formLabelWidth: "123px",
        page_loading:'',
        estate_id:0,
        searchData:{

        },
        tableData: [],
       
        pagination: {}, //分页数据
        rules: { },
      }
    },
    created: function(){
      let that = this
      if (this.$urlData && this.$urlData.id) {
        that.estate_id = this.$urlData.id;
      } else {
        that.estate_id = 0;
      }
   
      that.getList(that.searchData)
    },

    methods:{
      getList(searchdata={}){   //获取所有数据，或按条件查找数据
        var that = this
        if (that.estate_id) {
          searchdata['estate_id'] = that.estate_id;
          util.requests("post",{
            url:"estates/getEstatesnewNews",
            data: searchdata
          }).then(res=>{
            for(var i in res.data.list){
              let item = res.data.list[i]
              for(var j in item.img_url){
                if(!item.img_real_url){
                  item.img_real_url = []
                }
                if(item.img_url[j]&&item.img_url[j].url){
                  item.img_real_url[j] = that.getRealImgUrl(item.img_url[j].url)
                }
              }
            }
            that.tableData = res.data.list
          })
        } else {
          return;
        }
      },

      onSearch(){
        this.getList(this.searchData);
      },

      del(id,val){   //确定删除
        this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          util.requests("post",{
            url: "estates/delEstatesnewNews",
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

      
      getFormatDate(t){
        return util.DataFun.getFormatDate(t,3);
      },
      getRealImgUrl(src){
        return this.$getRealImgUrl(src);
      },

      openPage: util.openPage
    }

  };
</script>
<style lang="scss" scoped>
  .form-serch{text-align: right;}
  .timeline-box{
    padding: 0;
    margin: 0 40px;
  }
  .tb-title{
    margin-top: 30px;
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


