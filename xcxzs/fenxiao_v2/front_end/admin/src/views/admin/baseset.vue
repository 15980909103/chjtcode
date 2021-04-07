<template>
  <div class="_container">
    <el-form ref="form" :model="form">
      <div class="mapeditor-title">基础设置</div>
      <el-row class="mapeditor-content">
        <el-col :lg="18" :md="18" :xs="24">
          <el-form-item prop="kfmobile" label="客服电话" label-width="120px">
            <el-input type="text"  v-model="form.kfmobile"></el-input>
          </el-form-item>
        </el-col>
      <el-col :lg="18" :md="18" :xs="24">
          <el-form-item prop="kfmobilecode" label="客服二维码" label-width="120px">
            <img-upload2 ref="imgUpload" url="upload/imgUpload" :thumb='{ isthumb:1, width:750, height:750 }' :limit='1' :show-file-list="false" :fileList.sync="form.kfmobilecode" :imgIds.sync="form.cover_id" :uploadedImg="onUploadedImg"></img-upload2>
        
          </el-form-item>
        </el-col>

      </el-row>

      <div class="mapeditor-title">微信公众号配置-服务号</div>
      <el-row class="mapeditor-content">
      <el-col :lg="18" :md="18" :xs="24">
          <el-form-item label="微信公众号appid" label-width="120px">
            <el-input type="text"  v-model="form.wxh5.appid"></el-input>
          </el-form-item>
        </el-col>
          <el-col :lg="18" :md="18" :xs="24">
          <el-form-item label="微信公众号密钥" label-width="120px">
            <el-input type="text"  v-model="form.wxh5.secret"></el-input>
          </el-form-item>
        </el-col>
        <el-col :lg="18" :md="18" :xs="24">
          <el-form-item label="微信公众号token" label-width="120px">
            <el-input type="text"  v-model="form.wxh5.token"></el-input>
          </el-form-item>
        </el-col>
         <el-col :lg="18" :md="18" :xs="24">
          <el-form-item label="回调地址" label-width="120px">
            <el-input type="text"  v-model="form.wxh5.url"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <div class="mapeditor-title">浏览记录配置</div>
      <el-row class="mapeditor-content">
        <el-col :lg="18" :md="18" :xs="24">
          <el-form-item prop="value" label="选择类型" label-width="120px">
          <el-select v-model="value" placeholder="请选择">
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
          </el-form-item>
        </el-col>
        <el-col :lg="18" :md="18" :xs="24">
          <el-form-item prop="table_number" label="间隔月份/天数" label-width="120px">
            <el-input type="text" style="width: 200px" v-model="table_number"></el-input>
          </el-form-item>
        </el-col>
      </el-row>

      <el-form-item label-width="136px">
        <el-button type="primary" @click="onSubmit">保 存</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script>
var util=require('@/utils/util')
import ImgUpload from '@/components/common/ImgUpload.vue';
import ImgUpload2 from '@/components/common/ImgUpload2.vue';
import Tinymce from '@/components/Tinymce'
export default {
  components: {
    Tinymce,
    'img-upload': ImgUpload,
    'img-upload2': ImgUpload2,
	},
  data() {
    return {
      value: '',
      table_number:'',
      options:[
        {
          value: 'month',
          label: '按月分表'
        },
        {
          value: 'day',
          label: '按天分表'
        },
      ],
      form:{
        estates_browse:{
          type:'',
          number:''
        },
        kfmobile:'',
        kfmobilecode:[],
        cover_id:[],
         wxh5:{
          appid:'',
          secret:'',
          token:'',
          url:''
        },


      }
    };
  },
  created(){
    this.getInfo()
  },
  methods:{
    onUploadedImg(e,key){
      var info = e.res.info
      this.form[key] = info.url;//表单赋值
    },
    getInfo(){
      var that = this
      util.requests("post", {
          url: "sys/sysInfo",
          data: {key: ['kfmobile','kfmobilecode','wxh5','poster_img','estates_browse']}
      }).then(res => {
          if(res.code==1){
            var form = {}
            for(var i in res.data.list){
              var item = res.data.list[i]

              if(!item.val){
                if(item.key=='wxh5'){
                  item.val = {
                    appid:'',
                    secret:'',
                    token:'',
                  }
                }
              }
              form[item.key] = item.val

            }
            console.log(888,form.kfmobilecode)
            console.log(77,form.wxh5)
            that.setDataArr({
              form : form,
              value: form.estates_browse.type,
              table_number :form.estates_browse.number
            })

          }else{
            util.Message.error(res.msg);
          }
      });
    },
    onSubmit(){
      var that = this
      // if(!that.form.book_probation_days||isNaN(that.form.book_probation_days)){
      //   util.Message.error('请输入大于0的整数试用期');
      //   return
      // }
      // if(!that.form.manage_probation_days||isNaN(that.form.manage_probation_days)){
      //   util.Message.error('请输入大于0的整数试用期');
      //   return
      // }
      /* if(!that.form.reach_rate||isNaN(that.form.reach_rate)){
        util.Message.error('请输入大于0的数字类型两位小数的成员比例');
        return
      } */
      // if(!that.form.book_instruction){
      //   util.Message.error('请填写使用说明');
      //   return
      // }
      console.log(that.form)
      that.form.estates_browse.type = that.value;
      that.form.estates_browse.number = that.table_number;
      console.log(that.form)
      util.requests('post',{
        url:'sys/sysEdit',
        data:{key:{...that.form}}

      }).then(res=>{
        {
          if(res.code==1){
            that.getInfo()
            util.Message.success(res.msg)
          }else{
            util.Message.error(res.msg);
          }
        }
      });
    },
  }
};
</script>
<style lang="scss" scoped>
._container {
  margin-top: 20px;
  padding: 20px;
  background: #fff;
  min-height: calc(100vh - 90px);
  .mapeditor-title {
    line-height: 40px;
    padding-left: 10px;
    background-color: rgba($color: #f0f2f5, $alpha: 0.5);
    border-left: 4px solid;
    border-color: #409eff;
    font-size: 14px;
    margin-bottom: 10px;
  }
  .mapeditor-content {
    padding-left: 16px;
    .el-col {
      margin: 10px 0;
    }
  }

}
</style>


