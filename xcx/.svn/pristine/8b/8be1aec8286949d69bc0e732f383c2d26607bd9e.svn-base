<template>
  <div class="_container">
    <el-form :model="searchData">
      <el-row :gutter="30">
        <el-col :xl="6" :md="8" :xs="24">
          <el-form-item label="账号" label-width="60px">
            <el-input v-model="searchData.search_word"></el-input>
          </el-form-item>
        </el-col>

        <el-col :xl="6" :md="6" :xs="24" style="float:right;text-align:right;">
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
          <el-button
            type="danger"
            icon="el-icon-circle-plus-outline"
            @click="doEdit('add')"
          >新增</el-button>
        </el-col>
      </el-row>
    </el-form>

    <el-table :data="tableData" style="width:100%" v-loading="page_loading">
      <el-table-column prop="id" label="ID" width="100" align="center"></el-table-column>
      <el-table-column prop="name" label="姓名"  align="center"></el-table-column>
      <el-table-column prop="head_img" label="头像" width="120" align="center">
        <template slot-scope="scope">
          <el-image v-if="scope.row.head_img" style="width: 90px;" :src="getRealImgUrl(scope.row.head_img)"></el-image>
        </template>
      </el-table-column>
      <el-table-column prop="phone" label="手机号" align="center"></el-table-column>
      <el-table-column label="状态" width="85" align="center">
        <template slot-scope="scope">
          <el-switch @change='(val)=>{switchChange(scope.row.id,val)}' v-model="scope.row.status" :active-value="1" :inactive-value="0" ></el-switch>
        </template>
      </el-table-column>
      <el-table-column prop="create_time" label="创建时间" width="150" align="center"></el-table-column>
      <el-table-column label="操作"  align="center" >
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="openPage({url: '/user/AgentEstates', data:{agent_id:scope.row.id}})">绑定楼盘</el-button>
          <el-button type="primary" size="mini" @click="doEdit('edit', scope.row)">编辑</el-button>
          <el-button type="danger" size="mini" @click="doDel(scope.row.id)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <!-- ============分页=============== -->
    <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>

    <!-- 编辑用户弹窗 -->
    <el-dialog v-if="dialogFormVisible"
      :title="form_type=='add'?'新增淘房师':'编辑淘房师'"
      :visible.sync="dialogFormVisible"
      width="800px"
      :close-on-click-modal="false"
      @close="doEditCancel"
    >
      <el-form style="padding-right:50px;" :model="form" ref="form" :rules="rules">
        <el-form-item label="姓名" :label-width="formLabelWidth"  prop="name">
          <el-input v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item label="手机号" :label-width="formLabelWidth"  prop="phone">
          <el-input v-model="form.phone"></el-input>
        </el-form-item>
        <el-form-item label="头像" :label-width="formLabelWidth" ref="head_img" prop="head_img">
          <img-upload2 ref="imgUpload" url="upload/imgUpload" :show-file-list="false" :fileList.sync="form.head_img_url" :imgIds.sync="form.head_img_id" :uploadedImg="onUploadedImg"></img-upload2>
        </el-form-item>
        <el-form-item label="状态" :label-width="formLabelWidth"  prop="status">
          <el-radio v-model="form.status" label="1">是</el-radio>
          <el-radio v-model="form.status" label="0">否</el-radio>
        </el-form-item>
        <mycity-select :label-width="formLabelWidth" :city_no.sync='form.city_no' :area_no.sync='form.area_no' :isMy='true' model='2' siteAreasUrl='city/siteAreas'></mycity-select>
        <el-form-item label="区域默认淘房师" :label-width="formLabelWidth"  prop="is_default">
          <el-radio v-model="form.is_default" :label="1">是</el-radio>
          <el-radio v-model="form.is_default" :label="0">否</el-radio>
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
import baseMixin from  '@/mixin/baseMixin';
import paginationBox from '../../components/common/pagination.vue';
import ImgUpload from '@/components/common/ImgUpload.vue';
import ImgUpload2 from '@/components/common/ImgUpload2.vue';
import MycitySelect from '@/components/common/MycitySelect.vue';

export default {
  components: {
			'pagination-box': paginationBox,
      'img-upload': ImgUpload,
      'img-upload2': ImgUpload2,
      'mycity-select': MycitySelect,
  },
  mixins: [baseMixin],
  data() {
    return {
      siteCitys:{},
      dialogFormVisible: false,
      formLabelWidth: "123px",
      form_type:'',
      form: {
        id:"",
        name: "",
        phone: "",
        status:'1',
        head_img_id:[],
        head_img_url:[],
        is_default:0,
        city_no:'',
        area_no:'',
      },
      rules: { },

      searchData: {
        search_word: ""
      },
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据

    };
  },
  watch:{

  },
  created: function() {
    this.resetData({
      form: this.form
    })
    this.getList()
  },
  methods: {
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },
    onUploadedImg(e){
      this.form.head_img_id      = e.res.id;
      this.form.head_img_url  = e.res.url;
    },
    getList(searchdata={}) {
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "agent/getList",
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
    //分页操作
    pageChange: function(page) {
      let post_data = Object.assign({},this.searchData);
      post_data.page = page;
      this.getList(post_data)
    },
    onSerch() {
      //console.log("查询",this.searchData);
      this.getList(this.searchData)
    },

    doEditCancel(){
      var that=this
      that.resetData()
      if(that.dialogFormVisible == true){
        that.dialogFormVisible = false
      }
    },
    doEdit(dotype,e={}){
      let that = this
      that.dialogFormVisible = true
      that.form_type = dotype
      if(Object.keys(e).length>0){
        e.region_nos_info2=[]
        that.form = Object.assign({},e)
        that.form.status = String(that.form.status)

      }
    },
    doEditSubmit(){
      let that=this

      if(this.form.is_default==1 && !this.form.city_no){
        this.$message({
          type: 'error',
          message: '未选地区不得设为默认'
        });
        return
      }

      that.$refs['form'].validate((valid) => {
        if (valid) {
          util.requests("post", {
            url: "agent/editAgent",
            data: that.form
          }).then(res => {
              that.page_loading = false
              if(res.code==1){
                util.Message.success('操作成功');
                that.onSerch()
                setTimeout(() => {
                  that.doEditCancel()
                }, 1000);
              }else{
                util.Message.error(res.msg);
              }
          });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    doDel(id){
      var that = this
      that.$confirm('该操作将永久删除记录，是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        that.page_loading = true

        util.requests("post", {
            url: "agent/delete",
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
          url: "agent/setStatus",
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
    getFormatDate:util.DataFun.getFormatDate,
    openPage: util.openPage
  }
};
</script>
<style lang="scss" scoped>
._container {
  margin-top: 20px;
  padding: 20px;
  background: #fff;
}
.el-radio{
  margin-right: 5px;
}
</style>


