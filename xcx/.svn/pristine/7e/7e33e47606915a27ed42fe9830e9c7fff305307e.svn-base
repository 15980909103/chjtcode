<template>
  <div class="upload-mycontainer">
    <el-button :style="{background:color,borderColor:color}" icon="el-icon-upload" size="mini" type="primary" @click=" dialogVisible=true">
      上传
    </el-button>
    <el-dialog :visible.sync="dialogVisible">
      <el-upload
        drag
        :multiple="true"
        :file-list="fileList"
        :show-file-list="true"
        :on-remove="handleRemove"
        :on-success="handleSuccess"
        :before-upload="beforeUpload"
        class="upload-dialog"
        list-type="picture-card"

        :action="uploadUrl"
        :http-request="uploadImage"   
      >
        <i class="el-icon-upload">
          <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
        </i>
        
        <!-- <el-button size="small" type="primary">
          点击上传
        </el-button> -->
      </el-upload>
      <el-button @click="dialogVisible = false">
        取消
      </el-button>
      <el-button type="primary" @click="handleSubmit">
        确认
      </el-button>
    </el-dialog>
  </div>
</template>

<script>
// import { getToken } from 'api/qiniu'
var util = require("@/utils/util.js");

export default {
  name: 'EditorSlideUpload',
  props: {
    color: {
      type: String,
      default: '#1890ff'
    },
    url:{
      type: String,
      default: 'Upload/imgUpload'
    },
  },
  data() {
    return {
      dialogVisible: false,
      listObj: {},
      fileList: [],
      //uploadUrl: 'https://httpbin.org/post'
      uploadUrl: 'string'
    }
  },

  methods: {
    //自定义上传操作
    uploadImage(item){
      let that = this;
      //设置上传子目录
      var arr= this.$route.path.split('/')
      var subpath = arr[1]? arr[1] :''
      if(!subpath){
        console.log('页面路径设置错误')
        return
      }

      var file = item.file
      var post_url =that.url
      util.requests("post", {
          url: post_url,
          data: { image: file, subpath: subpath },
          headers: { "Content-Type": "multipart/form-data" }
      }).then(res => {
          if(res.code==1){
            let src_info = res.data.info;
            
            that.handleSuccess(src_info, file)
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },


    resetData(){//重置数据
      this.setDataArr({
        listObj: {},
        fileList :[]
      })
    },
    checkAllSuccess() {
      return Object.keys(this.listObj).every(item => this.listObj[item].hasSuccess)
    },
    handleSubmit() {
      const arr = Object.keys(this.listObj).map(v => this.listObj[v])
      if (!this.checkAllSuccess()) {
        this.$message('请耐心等待文件上传完成!')
        return
      }
      //console.log(arr)
      this.$emit('successCBK', arr)
      this.listObj = {}
      this.fileList = []
      this.dialogVisible = false
    },
    handleSuccess(response, file) {
      // var new_img_src = this.getRealImgUrl(response.url) //显示服务器图片
      var new_img_src = response.url
      const uid = file.uid
      const objKeyArr = Object.keys(this.listObj)
      for (let i = 0, len = objKeyArr.length; i < len; i++) {
        if (this.listObj[objKeyArr[i]].uid === uid) {
          this.listObj[objKeyArr[i]].url = new_img_src
          this.listObj[objKeyArr[i]].hasSuccess = true
          return
        }
      }
    },
    /* handleSuccess(response, file) {
      const uid = file.uid
      const objKeyArr = Object.keys(this.listObj)
      for (let i = 0, len = objKeyArr.length; i < len; i++) {
        if (this.listObj[objKeyArr[i]].uid === uid) {
          this.listObj[objKeyArr[i]].url = response.files.file
          this.listObj[objKeyArr[i]].hasSuccess = true
          return
        }
      }
    }, */
    handleRemove(file) {
      const uid = file.uid
      const objKeyArr = Object.keys(this.listObj)
      for (let i = 0, len = objKeyArr.length; i < len; i++) {
        if (this.listObj[objKeyArr[i]].uid === uid) {
          delete this.listObj[objKeyArr[i]]
          return
        }
      }
    },
    beforeUpload(file) {
      const _self = this
      const _URL = window.URL || window.webkitURL
      const fileName = file.uid
      this.listObj[fileName] = {}
      return new Promise((resolve, reject) => {
        const img = new Image()
        img.src = _URL.createObjectURL(file)
        img.onload = function() {
          _self.listObj[fileName] = { hasSuccess: false, uid: file.uid, width: this.width, height: this.height }
        }
        resolve(true)
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.upload-mycontainer .upload-dialog{
  margin-bottom: 20px;
   /deep/ .el-upload--picture-card {
    width: 232px;
    height: 162px;
  }
   /deep/ .el-upload-list--picture-card .el-upload-list__item{
    width: 160px;
    height: 160px;
  }
   /deep/ .el-upload .el-upload-dragger {
    width: 230px;
    height: 160px;
  } 
  .el-upload__text{
    text-align: center;
  }
}

</style>
