<template>
  <div class="_container">
    <el-form >
      <el-row :gutter="30">
        <el-col :lg="6" :sm="12" :xs="24">
          <el-form-item prop="title" label="名称" label-width="80px">
            <el-input placeholder="请输入分类名称" v-model="searchData.title"></el-input>
          </el-form-item>
        </el-col>
        <el-col :lg="4" :sm="12" :xs="24">
          <el-form-item label="状态">
            <el-select v-model="searchData.status" placeholder="请选择">
              <el-option label="全部" value="-1" ></el-option>
              <el-option label="已启用" value="1"></el-option>
              <el-option label="已禁用" value="0"></el-option>
            </el-select>
          </el-form-item>
        </el-col>

        <el-col :lg="6" :sm="12" :xs="24">
          <el-form-item label="评论状态">
            <el-select v-model="searchData.has_comment" placeholder="请选择">
              <el-option label="全部" value="-1"></el-option>
              <el-option label="已启用" value="1"></el-option>
              <el-option label="已禁用" value="0"></el-option>
            </el-select>
          </el-form-item>
        </el-col>

        <el-col :lg="6" :sm="12" :xs="24">
          <el-form-item label="分类" prop="pid">
            <el-select v-model="searchData.pid" placeholder="请选择">
              <el-option
                v-for="item in catelist"
                :key="item.col_id"
                :label="item.name"
                :value="item.col_id">
              </el-option>
            </el-select>
          </el-form-item>
        </el-col>

        <el-col :xl="6" :md="6" :xs="24" style="float:right;text-align:right;">
          <el-button type="primary" icon="el-icon-search"  @click="onSerch">查询</el-button>

          <el-button
            type="danger"
            icon="el-icon-circle-plus-outline"
            @click="doEdit()"
          >新增</el-button>
        </el-col>
      </el-row>
    </el-form>

    <el-table :data="tableData" style="width:100%" row-key="id" :tree-props="{children: 'children', hasChildren: 'hasChildren'}" >
      <el-table-column prop="id" label="ID" width="100" align="center"></el-table-column>
      <el-table-column prop="title" label="名称" width="120"  align="center"></el-table-column>

      <el-table-column label="状态" width="200" align="center">
        <template slot-scope="scope">
          <el-switch @change='(val)=>{switchChange(scope.row.id,val,"status")}' v-model="scope.row.status" :active-value="1" :inactive-value="0"></el-switch>
        </template>
      </el-table-column>

      <el-table-column label="评论功能" width="200" align="center">
        <template slot-scope="scope">
          <el-switch @change='(val)=>{switchChange(scope.row.id,val,"has_comment")}' v-model="scope.row.has_comment" :active-value="1" :inactive-value="0"></el-switch>
        </template>
      </el-table-column>

      <el-table-column label="排序" width="150" align="center">
        <template slot-scope="scope">
            <el-input v-model="scope.row.sort" @change="(val)=>{sortChange(scope.row.id,val)}" placeholder="排序越大越靠前" size="small"></el-input>
        </template>
      </el-table-column>
      <el-table-column prop="p_title" label="父及分类" width="500"  align="center"></el-table-column>

      <el-table-column label="操作"  align="center">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="doEdit({ id:scope.row.id, name:scope.row.name, status:scope.row.status, has_comment:scope.row.has_comment, icon_category:scope.row.icon_category,sort:scope.row.sort,pid:scope.row.pid })">编辑</el-button>
          <el-button type="danger" size="mini" @click="doDel(scope.row.id)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>


    <!-- 编辑用户弹窗 -->
    <el-dialog v-if="dialogFormVisible"
      :title="!form.id?'新增类别':'编辑类别'"
      :visible.sync="dialogFormVisible"
      width="800px"

      :close-on-click-modal="false"
      @close="doEditCancel"
    >
      <el-form style="padding-right:50px;" :model="form" ref="form" :rules="rules">
        <el-form-item label="父级分类" :label-width="formLabelWidth" prop="pid">
          <el-select v-model="form.pid" placeholder="请选择">
            <el-option
              v-for="item in catelist"
              :key="item.col_id"
              :label="item.name"
              :value="item.col_id">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="名称" :label-width="formLabelWidth" prop="name">
          <el-input v-model="form.name"></el-input>
        </el-form-item>

        <el-form-item label="排序" :label-width="formLabelWidth" prop="sort">
          <el-input v-model="form.sort"></el-input>
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

export default {
  components: {
    'img-upload':ImgUpload
	},
  data() {
    return {
      dialogFormVisible: false,
      formLabelWidth: "123px",
      form: {
        name: "",
        status: '0',
        has_comment: '0',
        sort: 50,
        icon_category:'',
        pid:'',
      },
      searchData:{
        status:'-1',
        has_comment:'-1',
        name:'',
        pid:'',
      },
      catelist:[],
      default_icon_category:'',
      rules: {
        name: [
          { required: true, message: '请输入名称', trigger: 'change' },
        ],
      },

      tableData: [],
    };
  },
  created: function() {
    this.getList()
    this.getCateList();
  },
  methods: {
    getList(searchData) {
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "news/getCategoryList",
          data:searchData
      }).then(res => {

          that.page_loading = false
          if(res.code==1){
            that.setDataArr({
              tableData : res.data.list,
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    getCateList(){
        let that = this;
        util.requests("get", {
          url: "news/getCateListCont",
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
          sort: 0,
          icon_category:'',
          pid:'',
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
      that.form.pid=Number (that.form.pid)
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
          url: "news/categoryChangesort",
          data: {id: id,sort: val}
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
        var posturl = "news/categoryEnable"
        var postdata = {id: id,status: val}
      }else{
        var posturl = "news/commentEnable"
        var postdata = {id: id,has_comment: val}
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


