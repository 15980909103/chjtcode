<template>
  <div class="_container">
    <div class="tb-top" style="float:right;margin-bottom: 30px;">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item label="状态：">
          <el-select v-model="searchData.status" placeholder="请选择">
            <el-option v-for="item in searchStatus" :key="item.status" :label="item.label" :value="item.status"></el-option>
          </el-select>
        </el-form-item>

        <el-form-item label="状态：">
          <el-select v-model="searchData.type" placeholder="请选择">
            <el-option v-for="item in searchType" :key="item.status" :label="item.label" :value="item.id"></el-option>
          </el-select>
        </el-form-item>

        <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="name" label="广告标题" width="200" align="center"></el-table-column>
        <el-table-column prop="type" label="类型" width="200" align="center"></el-table-column>
      <el-table-column label="通知时间" align="center">
        <template slot-scope="scope">
        {{getFormatDate(scope.row.create_time,3)}}
        </template>
      </el-table-column>
  
      <el-table-column prop="status" label="已读状态" align="center">
        <template slot-scope="scope">
            <el-switch @change="(val)=>{switchChange(scope.row.id,val)}" v-model="scope.row.status" :active-value="2" :inactive-value="1" ></el-switch>
        </template>
      </el-table-column>
     
    </el-table>

<!-- ============分页=============== -->
    <div style="text-align:right">
      <pagination-box :pagination="pagination" @pageChange="pageChange" style="display:inline-block;" ></pagination-box>
      <el-link style="margin-top: 10px;pointer-events:none;">共有 <el-link style="pointer-events:none;margin-top:-2px" type="primary">{{total}}</el-link> 数据</el-link>
    </div>


  </div>
</template>
<script>
// import { log } from 'util';
var util = require("@/utils/util.js");

import DyTags from '@/components/common/DyTags.vue';
import paginationBox from '@/components/common/pagination.vue';

export default {
  components: {
    'dy-tags': DyTags,
    'pagination-box': paginationBox,
	},
  data() {
    var validateImg= (rule, value, callback) => {
      var that = this
      that.$nextTick(function(){
        var field=rule.field
        if(!that.formData[field]){
      　　callback(new Error(rule.message));
  　　　　return false;
        }
        callback();
      })
    };

    return {
      siteCitys:[],
      dialogVisibleEdit: false,
      formLabelWidth: "123px",
      src:'',
      default_src:'',
      page_loading:'',
      innerVisible:false,
      thumb:{},
      selectLab:false,
      rules: {

      },
      searchStatus: [{
          status: '0',
          label: '全部'
        },
        {
          status: '2',
          label: '已通知'
        },
        {
          status: '1',
          label: '未通知'
        }],

          searchType: [{
          id: '0',
          label: '全部'
        },
      
        {
          id: '1',
          label: '广告'
        }],
      searchTime:[],
      placeList:[],
      searchData:{
          status : '',
          startdate: '',
          enddate: '',
          place:'',
      },
      tableData: [],
      formData:{
        status:'0',
        put_type:'1',
        sort:'0',
        title:'',
        img_ids:[],
        cover:'',
        start_time:'',
        end_time:'',
        region_no:'',
        is_propert_news:false,
        forid:'',
        forname:'',
        article_id:'',
        article_name:'',
        type:'0',
        align:'0',
        urls:[],
        fileUrl:'',
      },
        pagination: {}, //分页数据
      total:0,
      isedit:false,
      place_id: 0,
      place:'',
      imglimit:1,
    }
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
    this.place_id = this.$urlData.place_id
    this.place    = this.$urlData.place;
    if(this.place){
      this.searchData.place  = this.place;
    }
    if(this.place_id){
      this.selectLab = false
    }else{
      this.selectLab = true
    }
    this.getSiteCitys()
    this.getList(this.searchData)
    this.getPlaceList()
  },

  methods:{
    //分页操作
    pageChange: function(page) {
      let post_data = Object.assign({},this.searchData);
      post_data.page = page;
      this.getList(post_data)
    },
    getSiteCitys(){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"city/siteCitys",
      }).then(res=>{
        that.siteCitys = res.data
      })
    },
    
  
  
    seteidtflag(){
      this.isedit = true;
    },
    geturl(e){

      this.formData.cover = e.id;
      this.formData.fileUrl=e.path;
    },
   
  

    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"notification/list",
        data: searchdata
      }).then(res=>{
          that.page_loading = false
          if(res.code==1){
            console.log(888,res.data)
            
            that.setDataArr({
              tableData : res.data.list,
              pagination : {
                page : res.data.current_page,
                pagecount : res.data.last_page,
                pagesize : Math.ceil(res.data.total / res.data.last_page)
              },
              total:res.data.total
            })
          }else{
            util.Message.error(res.msg);
          }
      })
    },
    getFormatDate:util.DataFun.getFormatDate,
    getRealImgUrl(url){
      return this.$getRealVoiceUrl(url)
    },
  
    resetData(){
      this.setDataArr({
        formData:{
          status:'0',
          sort:'0',
          put_type:'1',
          cover:'',
          title:'',
          img_ids:[],
          start_time:'',
          end_time:'',
          forid:'',
          forname:'',
          region_no:'',
          type:'0',
          align:'0'
        },
        default_src:'',
        pagination : {
                page : res.data.current_page,
                pagecount : res.data.last_page,
                pagesize : Math.ceil(res.data.total / res.data.last_page)
              },
      })

      if(this.$refs.imgUpload){//重置图片信息
        this.$refs.imgUpload.resetData()
      }
    },
    doEdit(e={}){
      if(Object.keys(e).length>0){
        this.formData = Object.assign({},e);
       
        this.formData.status = String(this.formData.status)
        this.formData.type = String(this.formData.type)
        this.formData.put_type = String(this.formData.put_type)
        this.formData.align = String(this.formData.align)
        this.formData.is_propert_news=Boolean(this.formData.is_propert_news)
        this.default_src = this.$getRealImgUrl(this.formData.cover)
        this.formData.start_time = this.getFormatDate(this.formData.start_time,2)
        this.formData.end_time = this.getFormatDate(this.formData.end_time,2)
      }
      console.log(this.formData.urls);
      console.log(this.isedit);
      this.dialogVisibleEdit = true;
    },
    doEditCancel(formName){
      var that=this
      that.$refs[formName].resetFields()
      that.resetData()
      if(that.dialogVisibleEdit == true){
        that.dialogVisibleEdit = false
      }
    },
    sortChange(id,val){
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
          url: "banner/bannerChangeSort",
          data: {id: id,sort: val}
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

    //图片上传后操作
    onUploadedImg(e){

    },
    onSearch(){
      this.getList(this.searchData);
    },
    switchChange(id,val){
      var that = this
      util.requests("post", {
          url: "banner/bannerEnable",
          data: {id: id,status: val}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
      });
    },

     getPlaceList(){
      var that = this
      util.requests("get", {
          url: "banner/getPlace",
        }).then(res => {
          that.placeList = res
      });
    },

  del(id,val){   //确定删除
      this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          util.requests("post",{
            url: "banner/bannerDel",
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

    doSubmit(formName){
      var that = this
      that.formData.place_id = that.place_id ;
      that.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.page_loading){
            return;
          }
          that.page_loading = true;
          util.requests("post",{
            url:"banner/bannerEdit",
            data:that.formData
          }).then(res=>{
            that.page_loading = false
            //console.log(res)
            if(res.code==1){
               that.isedit =false;
               that.$message({ type: 'success', message: '操作成功!' });
               that.dialogVisibleEdit = false;
               that.onSearch()
            }else{
              that.$message({
                type: 'error',
                message: res.msg
              });
            }
          });
        }else{
          console.log('error submit!!');
          return false;
        }
      })
    },
    openPage: util.openPage,
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


