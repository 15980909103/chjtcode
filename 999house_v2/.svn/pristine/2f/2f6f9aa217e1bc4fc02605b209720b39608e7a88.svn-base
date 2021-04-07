<!--
  图片上传操作
-->
<template>
    <div class="img-upload-content" >
        <!-- 图片上传文档查询 -->
        <!-- https://element.eleme.cn/2.0/#/zh-CN/component/upload  http-request进行自定义函数上传-->
        <ul class="el-upload-list el-upload-list--picture-card">
            <li tabindex="0" class="el-upload-list__item is-success" v-if="fileList.length>0" v-for="(item, idx) in fileList" :key="idx">
                <img :src="getRealImgUrl(item.url)" class="el-upload-list__item-thumbnail">
                <span class="el-upload-list__item-actions">
                    <span class="el-upload-list__item-preview" @click="handlePreview(item.url)">
                        <i class="el-icon-zoom-in"></i>
                    </span>
                    <span class="el-upload-list__item-delete" @click="handleRemove(idx)">
                        <i class="el-icon-delete"></i>
                    </span>
                </span>

                <el-form-item v-if="doSort" prop="integral" label="排序" label-width="50px">
                  <el-input maxlength="3" placeholder="越大越靠前" v-model="fileList[idx].sort"></el-input>
                </el-form-item>
            </li>
        </ul>

        <el-upload
                class="img-upload"
                :class="{hide:hideUpload}"
                ref="imgUpload"
                action="string"
                :on-change="changeHide"
                :http-request="uploadImg"
                :before-upload="beforeUploadImg"
                :limit="limit"
                :on-exceed="handleExceed"
                :show-file-list="false"
                list-type="picture-card"
                :file-list="fileList"
                :img-ids ="imgIds"
                accept="image/jpeg,image/png,image/gif"
        >

            <div class="itemfile-box" :style="styleSet"><i class="el-icon-plus"></i></div>
        </el-upload>
        <el-dialog :visible="dialogVisible"  :append-to-body='true' @close='closeDialog'>
            <img width="100%" :src="dialogImageUrl" alt="">
        </el-dialog>
    </div>
</template>

<script>
    var util = require("@/utils/util.js");
    import { getFileHost } from '@/utils/auth'
import { setTimeout } from 'timers';
    export default {
        name: 'img-upload2',
        props: {
            url:{//上传地址
                type: String,
                default () {
                    return '';
                }
            },
            doSort:{
              type: Boolean,
               default(){
                 return false;
               },
            },

            //父组件传入的fileList使用.sync做同步关联，在更新数据时该组件调用this.$emit('update:fileList', newValue) 字段显示更新，使父子组件数据同步更新
            fileList: { //上传的文件列表,//show-file-list使用上传文件列表模式
                type: Array,
                default () {
                    return [];
                }
            },
            //压缩图片
            thumb:{
              type:Object,
              default(){
                return {
                  isthumb:1,
                  width:90,
                  height:90
                }
              },
            },
             //上传方式 oss,local
            upload_type: {
                type: String,
                default () {
                    return 'local';
                }
            },
            imgIds:{
              type: Array,
              default () {
                return [];
              }
            },

            uploadedImg:{ //上传返回调用，接在现有逻辑业务后面
                type: Function,
            },
            uploadedImgForNew:{ //上传返回调用，完全新逻辑业务的使用
                type: Function,
            },
            beforeRemoveForOpenAlert:{//是否在删除前弹窗提醒
                type: Boolean,
                default () {
                    return true;
                }
            },
            width:{
                type: Number,
                default () {
                    return 164;
                }
            },
            limit:{//限制文件数量
                type: Number,
                default () {
                    return 1;
                }
            },
            limitM:{//限制文件大小/M
                type: Number,
                default () {
                    return 5;
                }
            }
        },
        computed:{
            styleSet(){
                if(this.fileList.length>2){
                    this.hideUpload = this.fileList.length >= this.limit-1
                }else{
                    return 'width:'+this.width+'px; height:'+this.width+'px; line-height:'+this.width+'px;'
                }

            },
            preview_fileList(){
                return this.fileList;
            },
        },
        data () {
            return {
                uploadLoading :false,
                isPlaying :false,//是否正在播放音频
                dialogVisible:false,//点击图片弹出遮罩
                dialogImageUrl:'',//显示在遮罩中的图片url地址
                hideUpload:false,//上传按钮隐藏
                imgHost:'',//云地址+本地图片地址

            };
        },
        created(){
            // this.imgHost = getFileHost("imageHost");
        },
        updated(){
            /* var el=document.getElementsByClassName('el-upload-list__item')
            for(var i in el){
              console.log(el[i].style)
            } */
        },
        methods: {
            resetData(){//重置数据
                this.setDataArr({
                    uploadLoading :false,
                    hideUpload:false,//上传按钮隐藏
                })
                // this.fileList = [];
                this.$emit('update:fileList', [])
                this.$emit('update:imgIds', [])
            },
            /**
             * 超出文件限制数量时候的操作
             */
            handleExceed(files, audioFail) {
                this.$message.warning("只允许上传"+this.limit+"个文件,若要更改请先移除");
            },
            //上传前操作
            beforeUploadImg(file) {
                const isFormat = (file.type === "image/jpeg"||file.type === "image/png"||file.type === "image/gif");
                const limitM = this.limitM
                const isLt2KB = file.size / 1024 / 1024 < limitM;

                if (!isFormat) {
                    that.$message.error("上传图片只能是 JPG,PNG,GIF 格式!");
                }
                if (!isLt2KB) {
                    that.$message.error("上传文件大小不能超过 "+limitM+"M!");
                }

                return isFormat && isLt2KB;
            },

            //上传操作
            uploadImg(item){
                let that = this;
                if(that.uploadLoading){
                    return
                }
                that.uploadLoading = true;
                var file = item.file
                if(!that.uploadedImgForNew){
                  if(that.upload_type=='oss'){
                    util.uploadFileToOss({
                      file: file,
                      type:'image',
                      success: function(res) {
                        that.uploadLoading = false

                        that.fileList.push({name: res.name, url: res.url, sort:0 })//其中name为fileList中的必填属性

                        if(that.uploadedImg){
                          that.uploadedImg({ sub_that:that, res:res,fileList:that.fileList })
                        }
                        that.$emit('update:fileList', that.fileList)
                      },
                      fail: function(err) {
                        that.uploadLoading = false
                        util.Message.error(err);
                      }
                    })
                  }else if(that.upload_type=='local'){
                    //设置上传子目录
                    var arr= this.$route.path.split('/')
                    var subpath = arr[1]? arr[1] :''
                    if(!subpath){
                      console.log('页面路径设置错误')
                      return
                    }
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
                        if(res.code==1){
                          res = res.data&&res.data.info?res.data.info:''
                          that.fileList.push({name: res.name, url: res.url, sort:0 })//其中name为fileList中的必填属性
                          that.imgIds.push(Number(res.id));
                          if(that.uploadedImg){
                            that.uploadedImg({ sub_that:that, res:res,fileList:that.fileList })
                          }
                          that.$emit('update:fileList', that.fileList)//上传完成后传回表单进行赋
                          that.$emit('update:imgIds',that.imgIds);
                        }else{
                          util.Message.error(res.msg);
                        }
                    });
                  }
                }else{
                  that.uploadedImgForNew({ sub_that:that,res:res })
                }
            },
            closeDialog(){
                this.dialogVisible = false;
            },
            /**
             * 删除文件 操作
             * @param file //file删除的文件
             * @param remain_fileList //文件列表(删除后还剩下的)
             */
            handleRemove(idx) {
                this.fileList.splice(idx, 1);
                if(this.fileList.length < 3){
                    this.hideUpload = false
                }
                this.imgIds.splice(idx,1);
                this.$emit('update:imgIds',this.imgIds);
                // if(this.uploadedImg){
                //     this.uploadedImg({ fileList:this.fileList })
                // }
                //父组件传入的fileList使用.sync做同步关联，子组件调用this.$emit('update:fileList', newValue) 字段显示更新，使父子组件数据同步更新
                this.$emit('update:fileList', this.fileList)
            },

            /**
             * 查看图片
             */
            handlePreview(image) {
                this.dialogImageUrl = this.getRealImgUrl(image);
                this.dialogVisible = true;

            },

            /**
             * 文件状态改变时的钩子，添加文件、上传成功和上传失败时都会被调用
             * 当上传的图片超过3张时隐藏上传ui
             */
            changeHide(){
                this.hideUpload = this.fileList.length >= this.limit-1
            },

            getRealImgUrl(url){
              return this.$getRealImgUrl(url)
            },
        },

    };
</script>

<style lang="scss">
    .img-card{
        width: 148px;
        height: 148px;
        display: inline-block;
        margin: 0px 8px 8px 0px;
    }
    .img-card .el-image{
        width: 148px;
        height: 148px;
    }
    .img-tool{
        position: absolute;
        z-index: 99;
        width: 148px;
        height: 148px;
        text-align: center;
        color: #fff;
        font-size: 20px;
        background-color: rgba(0,0,0,.5);
        transition: opacity .3s;
        -webkit-transition: opacity .3s;
    }
    .img-tool span{
        display: inline-block;
        margin-top: 60px;
        cursor: pointer;
    }
    .img-upload{display: inline;}
    .img-tool span:first-child{
        margin-right: 10px;
    }
    .hide .el-upload--picture-card {
        display: none;
    }
    .img-upload{
        /deep/.el-upload--picture-card{
            width: auto;
            height: auto;
        }
        .itemfile-box .el-icon-plus{
            line-height:inherit
        }

    }
    .img-upload-content{
      /deep/.el-upload-list__item {
        overflow: unset;
        width: 166px;
        height: 166px;
        margin-right: 8px;
        margin-bottom: 8px;
        margin-top: 0;
      }
    }
</style>
