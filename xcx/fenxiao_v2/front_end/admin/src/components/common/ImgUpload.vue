<!--
  图片上传操作
-->
<template>
  <div>
      <!-- 图片上传文档查询 -->
      <!-- https://element.eleme.cn/2.0/#/zh-CN/component/upload  http-request进行自定义函数上传-->
      <el-upload
        class="avatar-uploader"
        action="string"
        :http-request="uploadImage" 
        :show-file-list="false"
        :before-upload="_beforeUploadImage"
        :on-change="changeUploadImage"
        :auto-upload="auto_upload"
        accept="image/jpeg,image/png,image/gif"
      >
      
        <img v-if="default_src" :src="default_realsrc" class="avatar" :style="styleSet">
        <i v-else class="el-icon-plus avatar-uploader-icon" :style="styleSet"></i> 
        <div class="avatar-uploader-loading" :style="styleSet" v-loading="uploadLoading" element-loading-background="rgba(0, 0, 0, 0.8)"></div>
      </el-upload>
  </div>
</template>

<script>
var util = require("@/utils/util.js");
export default {
  name: 'img-upload',
  props: {
    url:{//上传地址
      type: String,
      default () {
        return '';
      }
    },
    thumb:{
      type:Object,
      default(){
        return {
          isthumb:0,
          width:90,
          height:90
        }
      },
    },

     //必填项，父组件使用.sync做同步关联，在更新数据时子组件调用this.$emit('update:default_src', newValue) 字段显示更新，使父子组件数据同步更新
    default_src: { //默认图片加载的地址
      type: String,
      default () {
        return '';
      }
    },
    auto_upload: { //是否自动上传，手动上传时得在父组件调用该组件的uploadImage方法
      type: Boolean,
      default () {
        return true;
      }
    },
    uploadedImg:{ //上传返回调用，接在现有逻辑业务后面
      type: Function,
    },
    beforeUploadImage:{
      type: Function,
      default(){return true;}
    },
    uploadedImgForNew:{ //上传返回调用，完全新逻辑业务的使用
      type: Function,
    },
    limitM:{
      type: Number,
      default () {
        return 10.5;
      }
    },
    width:{
      type: Number,
      default () {
        return 178;
      }
    },
    height:{ }
  },
  computed:{
    styleSet(){
      if(this.height==0||this.height==null){
        var height= this.width
      }else{
        var height= this.height
      }
  
      return 'width:'+this.width+'px; height:'+height+'px; line-height:'+height+'px;'
    },
    default_realsrc(){
      return this.getRealImgUrl(this.default_src)
    }
  },
  data () {
    return {
      uploadLoading :false,
      current_fileHandle:'',//当前文件切换的句柄
    };
  },
  created(){
    
  },
  methods: {
    resetData(){//重置数据
      this.setDataArr({
        current_fileHandle: null,
      })
      this.$emit('update:default_src', '')//显示更新到父组件
    },
    //图片上传前操作
    _beforeUploadImage(file) {
      const that= this
      var iscanmsg = this.beforeUploadImage()
      if(iscanmsg!=true){
        that.$message.error(iscanmsg);
        return false
      }
      
      const isFormat = (file.type === "image/jpeg"||file.type === "image/png"||file.type === "image/gif");
      const limitM = this.limitM
      const isLt2KB = file.size / 1024 / 1024 < limitM;

      /* if (!isFormat) {
        that.$message.error("上传图片只能是 JPG,PNG,GIF 格式!");
      }
      if (!isLt2KB) {
        that.$message.error("上传图片大小不能超过 "+limitM+"M!");
      }

      return isFormat && isLt2KB; */
      return true
    },
    changeUploadImage(file, fileList){
      var that=this
     // console.log(file)
      var fileHandle =file.raw 

      var iscan = that._beforeUploadImage(fileHandle) //手动上传时候只能手动触发验证
      if(iscan!=true){
        return false
      }

      if(!that.auto_upload){
        //手动上传时候//生成base64预览
        if ( window.FileReader) {
          // 看支持不支持FileReader
          let reader = new FileReader()
          reader.readAsDataURL(fileHandle) // 进行转换
          reader.onloadend = function (e) {
            //console.log(e,'--',e.currentTarget.result)
            var src= e.currentTarget.result
            that.$emit('update:default_src', src)//显示更新到父组件
          } 
        }  
      }

      that.current_fileHandle = fileHandle //变更后此次打开的文件句柄
    },
    //上传操作
    uploadImage(item){
      let that = this;
      if(that.uploadLoading){
        return
      }
      that.uploadLoading = true;
      //设置上传子目录
      var arr= this.$route.path.split('/')
      var subpath = arr[1]? arr[1] :''
      if(!subpath){
        console.log('页面路径设置错误')
        return
      }

      if(!that.auto_upload){
        //手动上传时候无法自动获取item值 从current_fileHandle中拿
        var file = that.current_fileHandle
      }else{
        var file = item.file
      }
      //console.log(file)

      var post_url =that.url
      var post_data =  { image: file, subpath: subpath }
      if(that.thumb.isthumb==1){
        post_data['isthumb'] = that.thumb.isthumb
        post_data['width'] = that.thumb.width?that.thumb.width:90
        post_data['height'] = that.thumb.height?that.thumb.height:90
      }
      util.requests("post", {
          url: post_url,
          data: post_data,
          headers: { "Content-Type": "multipart/form-data" }
      }).then(res => {
          that.uploadLoading = false

          if(!that.uploadedImgForNew){
            /////
            if(res.code==1){
              let src_info = res.data.info;
              //var new_default_src = that.getRealImgUrl(src_info.url) //显示图片
              var new_default_src = src_info.url //显示图片
              that.$emit('update:default_src', new_default_src)//显示更新到父组件
              
              if(that.uploadedImg){
                that.uploadedImg({ sub_that:that,res:res.data })
              }
              //that.$emit('onUploadedImg', res.data);//上传完成后传回表单进行赋值
            }else{
              util.Message.error(res.msg);
            }
            ///////
          }else{
            that.uploadedImgForNew({ sub_that:that,res:res })
          }
          
      });
    },

    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },


  },

};
</script>

<style scoped>

.avatar-uploader .el-upload {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
.avatar-uploader .el-upload:hover {
  border-color: #409eff;
}
.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  /* width: 178px;
  height: 178px;
  line-height: 178px; */
  text-align: center;
  border: dashed 1px #ccc;
}
.avatar-uploader-loading {
  /* width: 178px;
  height: 178px; */
  text-align: center;
  position: absolute !important;
  top: 0;
}
.avatar {
  /* width: 178px;
  height: 178px; */
  display: block;
}
</style>
