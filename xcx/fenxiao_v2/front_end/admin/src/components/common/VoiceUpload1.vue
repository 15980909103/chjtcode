<!--
  图片上传操作
-->
<template>
  <div>
    <el-upload
      class="avatar-uploader el-upload--text"
      action="upload/imgUpload"
      :http-request="uploadVoice"
      :show-file-list="false"
      :limit="limitM"
      :on-success="handleVideoSuccess"
      :before-upload="beforeUploadVideo"
      :on-progress="uploadVideoProcess">
      <video v-if="default_src !='' && videoFlag == false" :src="default_realsrc" class="avatar" controls="controls" > 您的浏览器不支持视频播放</video>
      <i v-else-if="default_src =='' && videoFlag == false" class="el-icon-plus avatar-uploader-icon"></i>
      <el-progress v-if="videoFlag == true" type="circle" :percentage="videoUploadPercent" style="margin-top:30px;"></el-progress>
    </el-upload>
  </div>
</template>

<script>
  var util = require("@/utils/util.js");
  export default {
    name: 'voice-upload1',
    props: {
      url:{//上传地址
        type: String,
        default () {
          return '';
        }
      },
      upload_type: { //上传方式 1:oss,2:local
        type: String,
        default () {
          return '';
        }
      },
      default_src:{
        type: String,
        default () {
          return '';
        }
      },
      //父组件传入的fileList使用.sync做同步关联，在更新数据时子组件调用this.$emit('update:fileList', newValue) 字段显示更新，使父子组件数据同步更新
      fileList: { //上传的文件列表,//show-file-list使用上传文件列表模式
        type: Array,
        default () {
          return [];
        }
      },
      uploadedVoice:{ //上传返回调用，接在现有逻辑业务后面
        type: Function,
      },
      uploadedVoiceForNew:{ //上传返回调用，完全新逻辑业务的使用
        type: Function,
      },
      limitM:{
        type: Number,
        default () {
          return 50;
        }
      }
    },
    data () {
      return {
        uploadLoading :false,
        open_voicesrc: '',
        isPlaying :false,//是否正在播放音频
        videoUploadPercent:0,
        videoForm:{
          Video:'',
          videoUploadId:''
        },
        videoFlag:true,

      };
    },
    created(){

    },
    computed:{
      default_realsrc(){
        return this.getRealImgUrl(this.default_src)
      }
    },
    methods: {
      resetData(){//重置数据
        this.setDataArr({
          uploadLoading :false,
          open_voicesrc: '',
          isPlaying :false//是否正在播放音频
        })
        this.$emit('update:fileList', [])
      },
      /**
       * 超出文件限制数量时候的操作
       */
      handleExceed(files, audioFail) {
        this.$message.warning("只允许上传一个文件,若要更改请先移除");
      },
      handleVideoSuccess(res, file) {//获取上传图片地址

      },
      beforeUploadVideo(file){
        const isLt10M = file.size / 1024 / 1024  < this.limitM;
        if (['video/mp4', 'video/ogg', 'video/flv','video/avi','video/wmv','video/rmvb'].indexOf(file.type) == -1) {
          this.$message.error('请上传正确的视频格式');
          return false;
        }
        if (!isLt10M) {
          this.$message.error('上传视频大小不能超过10MB哦!');
          return false;
        }


      },
      uploadVideoProcess(event, file, fileList){

        console.log(event.percent, file, fileList)
        this.videoFlag = true
        // this.videoUploadPercent = file.percentage.toFixed(0)
        // this.videoUploadPercent = event.percent.toFixed(0)
        this.videoUploadPercent = Math.floor(event.percent)
      },


      //上传操作
      uploadVoice(item){
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

        var file = item.file
        var upload_type =that.upload_type
        if(upload_type == 'oss'){
          util.uploadFileToOss({
            file: file,
            type:'audio',
            success: function(res) {
              that.uploadLoading = false
              that.fileList.push({name: res.name, url: res.url})//其中name为fileList中的必填属性
              that.$emit('update:fileList', that.fileList)
              if(that.uploadedVoice){
                that.uploadedVoice({ sub_that:that,res:res })
              }
            },
            fail: function(err) {
              that.uploadLoading = false
              util.Message.error(res.msg);
            }
          })
        }else{
          var post_url =that.url
          var imghost = that.$baseconfig.imghost
          util.requests("post", {
            url: post_url,
            data: { voice: file, subpath: subpath },
            headers: { "Content-Type": "multipart/form-data" }
          }).then(res => {
            that.uploadLoading = false
            if(!that.uploadedVoiceForNew){
              /////
              if(res.code==1){
                this.videoFlag = false;
                this.videoUploadPercent = 0;
                if(res.code == 1){
                  that.$emit('update:default_src', res.data.info.url)
                  that.default_src = res.data.info.url;
                }else{
                  this.$message.error('视频上传失败，请重新上传！');
                }
              }else{
                util.Message.error(res.msg);
              }
              ///////
            }else{
              that.uploadedVoiceForNew({ sub_that:that,res:res })
            }
          })
        };
      },
      getRealImgUrl(url){
        return this.$getRealImgUrl(url)
      },


    },

  };
</script>

<style>
  .avatar {
    width: 178px;
    height: 178px;
    display: block;
  }
</style>
