<!--
  图片上传操作
-->
<template>
  <div class="voice-upload-content">
      <!-- 图片上传文档查询 -->
      <!-- https://element.eleme.cn/2.0/#/zh-CN/component/upload  http-request进行自定义函数上传-->
      <el-upload
        class="voice-uploader"
        action="string"
        :http-request="uploadVoice"
        :before-upload="beforeUploadVoice"

        :on-preview="handlePreview"
        :on-remove="handleRemove"
        :before-remove="beforeRemove"
        :limit="1"
        :on-exceed="handleExceed"
        :show-file-list="true"
        :file-list="fileList"
        accept="audio/mp3"
      >
        <el-button size="small" type="primary">选择文件</el-button>
      </el-upload>
      <audio ref="open_voicesrc" :src="open_voicesrc" type="audio/*"></audio>
  </div>
</template>

<script>
var util = require("@/utils/util.js");
export default {
  name: 'voice-upload',
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
        return 5;
      }
    }
  },
  data () {
    return {
      uploadLoading :false,
      open_voicesrc: '',
      isPlaying :false,//是否正在播放音频
    };
  },
  created(){

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
    //上传前操作
    beforeUploadVoice(file) {
      const that= this
      const isFormat = (file.type === "audio/mp3");
      const limitM = this.limitM
      const isLt2KB = file.size / 1024 / 1024 < limitM;

      if (!isFormat) {
        that.$message.error("上传文件只能是 mp3 格式!");
      }
      if (!isLt2KB) {
        that.$message.error("上传文件大小不能超过 "+limitM+"M!");
      }

      return isFormat && isLt2KB;
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
                  console.log(res)
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
                  let src_info = res.data.info;
                  that.fileList.push({name: src_info.url, url: src_info.url})//其中name为fileList中的必填属性
                  that.$emit('update:fileList', that.fileList)

                  if(that.uploadedVoice){
                    that.uploadedVoice({ sub_that:that,res:res.data })
                  }
                  //that.$emit('onUploadedImg', res.data);//上传完成后传回表单进行赋值
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
    /**
     * 删除前 操作
     * @param file //删除的文件
     * @param fileList //文件列表
     */
    beforeRemove(file, fileList) {
      //console.log(file, fileList)
      return this.$confirm(`确定移除 ${file.name}？`);
    },
    /**
     * 删除文件 操作
     * @param file //file删除的文件
     * @param remain_fileList //文件列表(删除后还剩下的)
     */
    handleRemove(file, remain_fileList) {
      //this.fileList = remain_fileList
      this.stop()
        if(this.uploadedVoice){
            this.uploadedVoice({ sub_that:this,res:remain_fileList })
        }
      //父组件传入的fileList使用.sync做同步关联，子组件调用this.$emit('update:fileList', newValue) 字段显示更新，使父子组件数据同步更新
      this.$emit('update:fileList', remain_fileList)
    },

    /**
     * 点击文件列表中已上传的文件的操作
     */
    handlePreview(file) {
      //console.log(file);
      var that = this
      that.open_voicesrc =''
      that.open_voicesrc = this.getRealVoiceUrl(file.url)
      setTimeout(() => { //延迟使dom加载
        that.play()
      }, 300);
    },
    /**
     * 音频播放
     */
    play(){
      if(this.$refs.open_voicesrc.ended){//判断是否播放完
        //将进度置0重新开始,状态为非播放中
        this.$refs.open_voicesrc.currentTime = 0;
        this.isPlaying = false
      }

      if(!this.isPlaying){
        this.$refs.open_voicesrc.play()
        this.isPlaying = true;
      }else{
        this.pause()
      }
    },
    /**
     * 音频播放暂停
     */
    pause(){
      if(this.isPlaying){
        this.$refs.open_voicesrc.pause();
        this.isPlaying = false
      }
    },
    /**
     * 音频播放停止
     */
    stop(){
      if(this.isPlaying){
        this.$refs.open_voicesrc.pause();
        this.isPlaying = false
        this.$refs.open_voicesrc.currentTime = 0; //将进度置0下次重新开始
      }
    },

    getRealVoiceUrl(url){
      return this.$getRealVoiceUrl(url)
    },
  },

};
</script>

<style scoped>

</style>
