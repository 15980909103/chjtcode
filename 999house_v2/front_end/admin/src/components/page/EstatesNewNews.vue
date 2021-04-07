<template>
  <div >
    <div class="tb-top">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
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
                  <div><el-button type="success" size="mini" @click="doEdit(item)">编辑</el-button></div>
                  <div style="margin-top: 20px;"><el-button type="danger" size="mini" @click="del(item.id,index)">删除</el-button></div>
                </el-col>
              </el-row>
            </el-card>
          </el-timeline-item>
      </el-timeline>
      <div v-else>暂无数据</div>
    </div>

    <!-- 新增弹窗部分 -->
    <el-dialog
        :title="formData.id?'编辑':'新增'"
        :visible.sync="dialogVisibleEdit"
        width="800px"
        :close-on-click-modal="false"
        @close="doEditCancel('formData')"
      >
        <el-form style="padding-right:50px;" :model="formData" ref="formData" :rules="rules">
          <el-row>
            <el-form-item prop="create_time" label="发布时间" label-width="120px">
              <el-date-picker
                style="width:100%;"
                v-model="formData.create_time"
                type="datetime"
                value-format="yyyy-MM-dd HH:mm"
                format="yyyy-MM-dd HH:mm"
                placeholder="选择日期">
              </el-date-picker>
            </el-form-item>
          </el-row>

          <el-row>
            <el-col :span="24">
              <el-form-item prop="title" label="标题" label-width="120px">
                <el-input type="text" width="2px" v-model="formData.title"></el-input>
              </el-form-item>
            </el-col>
          </el-row>

          <el-row>
            <el-col :span="24">
              <el-form-item prop="describe" label="描述" label-width="120px">
                <el-input type="textarea" width="2px" v-model="formData.describe"></el-input>
              </el-form-item>
            </el-col>
          </el-row>

          <el-row>
            <el-col :span="24">
              <el-form-item prop="building" label="资讯内容" label-width="120px">
                <!-- <el-input type="textarea" width="2px" v-model="formData.content"></el-input> -->
                <Tinymce v-if="dialogVisibleEdit" ref="editor" v-model="formData.content" />
              </el-form-item>
            </el-col>
          </el-row>

          <el-form-item label-width="120px"  label="上传图片"  ref="sand_table" prop="upload">
            <img-upload2
              ref="imgUpload"
              url="upload/imgUpload"
              :thumb='{ isthumb:1, width:750, height:750 }'
              :show-file-list="true"
              :limit=3
              :fileList.sync="formData.img_url"
              :imgIds.sync="formData.img_ids"
              :uploadedImg="onUploadedImg">
            </img-upload2>
          </el-form-item>

        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button type="primary" @click="doSubmit('formData')">确 定</el-button>
          <el-button type="danger" @click="doEditCancel('formData')">取 消</el-button>
        </div>
      </el-dialog>

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
        formData:{
          id: '',
          create_time:'',
          estate_id: '',
          cover: '',
          content: '',
        },
        pagination: {}, //分页数据
        rules: {
          content: [
            { required: true, message: '请输入内容', trigger: 'change' },
          ],
        },
      }
    },
    created: function(){
      let that = this
      if (this.$urlData && this.$urlData.id) {
        that.estate_id = this.$urlData.id;
      } else {
        that.estate_id = 0;
      }
      that.resetData({
        formData: this.formData,
      },function(){

      })
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

      doEdit(e={}){
        if(Object.keys(e).length>0){
          this.formData = Object.assign({},e);
          this.formData.create_time = this.getFormatDate(this.formData.create_time)
        }
        this.dialogVisibleEdit = true;
      },
      getFormatDate(t){
        return util.DataFun.getFormatDate(t,3);
      },
      getRealImgUrl(src){
        return this.$getRealImgUrl(src);
      },

      doEditCancel(formName){
        var that=this
        // that.$refs[formName].resetFields()
        // that.resetData()

        if(that.dialogVisibleEdit == true){
          that.dialogVisibleEdit = false
        }

        setTimeout(() => {
          that.$refs[formName].resetFields()
          that.resetData()
        }, 20);
      },

      doSubmit(formName){
        var that = this
        that.$refs[formName].validate((valid) => {
          if (valid) {
            if(this.page_loading){
              return;
            }
            that.page_loading = true;

            // 楼盘ID
            that.formData.estate_id = that.estate_id;

            util.requests("post",{
              url:"estates/editEstatesnewNews",
              data:that.formData
            }).then(res=>{
              that.page_loading = false
              //console.log(res)
              if(res.code==1){
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

      //图片上传后操作
      onUploadedImg(e){
        //this.formData.img_url = e.res.info.url;
        //this.$refs.imgUpload.clearValidate()
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


