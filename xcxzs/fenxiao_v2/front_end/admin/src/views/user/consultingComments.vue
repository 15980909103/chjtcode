<template>
  <div class="_container">
     <div class="tb-top" style="float:right;">
      
      <el-form :inline="true" :model="searchData" class="form-serch" >
          <el-form-item label="类别选择">
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
        <el-form-item label="昵称">
          <el-input placeholder="请输入用户昵称" v-model="searchData.user_name"></el-input>
        </el-form-item>
         <mycity-select :province_no.sync='searchData.province' :city_no.sync='searchData.city' :area_no.sync='searchData.area' :isMy='true' model='3' siteAreasUrl='city/siteAreas'></mycity-select>
        <el-form-item label="文章名称" >
          <el-input placeholder="请输入文章名称" v-model="searchData.name"></el-input>
        </el-form-item>
        <el-form-item label="创建时间">
           <el-date-picker
            style="width:250px"
            v-model="searchTime"
            value-format="yyyy-MM-dd" format="yyyy-MM-dd"
            type="daterange"
            range-separator="-"
            start-placeholder="开始日期"
            end-placeholder="结束日期">
          </el-date-picker>
        </el-form-item>
        <el-form-item label="审核状态">
            <el-select v-model="searchData.status" placeholder="请选择">
              <el-option label="全部" value="-1"></el-option>
              <el-option label="已启用" value="1"></el-option>
              <el-option label="已禁用" value="0"></el-option>
            </el-select>
          </el-form-item>
         
        <el-button type="primary" icon="el-icon-search"  @click="onSerch">查询</el-button>
      
      </el-form>
    </div>

    <el-table :data="tableData" style="width:100%" row-key="id" :tree-props="{children: 'children', hasChildren: 'hasChildren'}" >
      <el-table-column prop="id" label="ID"  width="100"  align="center"></el-table-column>
      <el-table-column prop="name_title" label="文章名称"  width="180" align="center"></el-table-column>
      <el-table-column prop="user_name" label="用户昵称" width="180" align="center"></el-table-column>
      <el-table-column prop="content" label="评论内容"  align="center"></el-table-column>
    
      <el-table-column prop="create_time" label="创建时间" width="180"  align="center"></el-table-column>

      <el-table-column label="审核状态" width="180"  align="center">
        <template slot-scope="scope">
          <el-switch @change='(val)=>{switchChange(scope.row.id,val,"status")}' v-model="scope.row.status" :active-value="1" :inactive-value="0"></el-switch>
        </template>
      </el-table-column>
    </el-table>
    <!-- ============分页=============== -->
    <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>

    <!-- 编辑用户弹窗 -->
    <el-dialog v-if="dialogFormVisible"
      :title="!form.id?'新增类别':'编辑类别'"
      :visible.sync="dialogFormVisible"
      width="800px"

      :close-on-click-modal="false"
      @close="doEditCancel"
    >
      <el-form style="padding-right:50px;" :model="form" ref="form" :rules="rules">
        <el-form-item label="所属栏目" :label-width="formLabelWidth" prop="pid">
          <el-select v-model="form.pid" placeholder="请选择">
            <el-option
              v-for="item in catelist"
              :key="item.id"
              :label="item.title"
              :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="名称" :label-width="formLabelWidth" prop="name">
          <el-input v-model="form.name"></el-input>
        </el-form-item>

        <el-form-item label="排序" :label-width="formLabelWidth" prop="order">
          <el-input v-model="form.order"></el-input>
        </el-form-item>

        <el-form-item label="状态" :label-width="formLabelWidth">
           <el-radio-group v-model="form.status">
             <el-radio  label="1">启用</el-radio>
             <el-radio  label="0">禁用</el-radio>
           </el-radio-group>
        </el-form-item>

        <el-form-item label="评论功能" :label-width="formLabelWidth">
           <el-radio-group v-model="form.has_comment">
             <el-radio  label="1">启用</el-radio>
             <el-radio  label="0">禁用</el-radio>
           </el-radio-group>
        </el-form-item>

        <el-form-item label="分类图标" :label-width="formLabelWidth" prop="icon_category">
          <img-upload ref="imgUpload" url="upload/imgUpload" :thumb="{isthumb:1}" :default_src.sync='default_icon_category'  :uploadedImg="(e)=>onUploadedImg(e,'icon_category')"></img-upload>
        </el-form-item>

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="doEditSubmit">确 定</el-button>
        <el-button type="danger" @click="doEditCancel">取 消</el-button>
      </div>
    </el-dialog>

  </div>
</template>
<script>
var util = require("@/utils/util");
import ImgUpload from '@/components/common/ImgUpload.vue';
import MycitySelect from '@/components/common/MycitySelect.vue';
import paginationBox from '@/components/common/pagination.vue';

export default {
  components: {
    'pagination-box': paginationBox,
    'img-upload':ImgUpload,
     'mycity-select': MycitySelect,
	},
  data() {
    return {
      dialogFormVisible: false,
      formLabelWidth: "123px",
      form: {
        name: "",
        status: '0',
        has_comment: '0',
        order: 50,
        icon_category:'',
        pid:'',
      },
      categoryList:[],
      searchTime:[],
      searchData:{
        status:'-1',
        has_comment:'-1',
        name:''
      },
      catelist:[],
      default_icon_category:'',
      rules: {
        name: [
          { required: true, message: '请输入名称', trigger: 'change' },
        ],
      },

      tableData: [],
      pagination: {}, //分页数据
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
  created: function() {
    this.getCategoryList()
    this.getList()
  },
  methods: {

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
    getList(searchData = {}) {
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "commentList/consultingComments",
          data:searchData
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
    getCateList(){
        let that = this;
        util.requests("get", {
          url: "news/getCateList"
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.setDataArr({
              catelist : res.data,
            });
          }else{
            util.Message.error(res.msg);
          }
        });
    },
    resetData(){
      this.setDataArr({
        form: {
          name: "",
          status : "0",
          has_comment: '0',
          order: 0,
          pid:'',
          icon_category:'',
        },
        default_icon_category:'',
      })

      if(this.$refs.imgUpload){//重置图片信息
        this.$refs.imgUpload.resetData()
      }
    },
    doEditCancel(){
      var that=this
      that.resetData()
      if(that.dialogFormVisible == true){
        that.dialogFormVisible = false
      }
    },
    doEdit(e={}){
      let that = this
      that.dialogFormVisible = true
      if(Object.keys(e).length>0){
        that.form = Object.assign({},e)
        that.default_icon_category= that.$getRealImgUrl(that.form.icon_category)
      }

      that.form.status=String(that.form.status)
      that.form.has_comment=String(that.form.has_comment)
      that.form.pid=String(that.form.pid)
    },
    onUploadedImg(e,key){
      var info = e.res.info
      this.form[key]= info.url
    },
    doEditSubmit(){
      let that=this
      that.$refs['form'].validate((valid) => {
        if (valid) {
          util.requests("post", {
            url: "news/categoryEdit",
            data: that.form
          }).then(res => {
              that.page_loading = false
              if(res.code==1){
                util.Message.success('操作成功');
                that.getList()
                setTimeout(() => {
                  that.doEditCancel()
                }, 1000);
              }else{
                util.Message.error(res.msg);
              }
          });
        } else {

          return false;
        }
      });
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
            url: "news/categoryDel",
            data: {id: id}
          }).then(res => {
            that.page_loading = false
            if(res.code==1){
              that.getList()
              util.Message.success('操作成功');
            }else{
              util.Message.error(res.msg);
            }
        });
      }).catch(() => {
         //console.log('取消')
      });
    },
    orderChange(id,val){
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
          url: "news/categoryChangeorder",
          data: {id: id,order: val}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.getList()
            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    onSerch(){
      this.getList(this.searchData)
    },
    switchChange(id,val,type){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      if(type=='status'){
        var posturl = "commentList/consultingCommentsReview"
        var postdata = {id: id,status: val}
      }

      util.requests("post", {
          url: posturl,
          data: postdata
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.getList()
            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    //分页操作
    pageChange: function(page) {
      let post_data = Object.assign({},this.searchData);
      post_data.page = page;
      this.getList(post_data)
    },


    openPage: function(e){
      return util.openPage(e);
    }
  }
};
</script>
<style lang="scss" scoped>
._container {
  margin-top: 20px;
  padding: 20px;
  background: #fff;
}
</style>


