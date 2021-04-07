<!--
  图片预览操作
-->
<template>
  <div class="imgpreview-box">
     <el-dialog title="图片预览" :visible.sync="pageShow">
       <div class="mydialog-cotent" :style="styleCss" >
         <slot name="header"></slot>
         <div><el-image class="img-preview" :src="getRealImgUrl(src)" fit="fill"></el-image></div>
         <slot name="footer"></slot>
       </div>
      </el-dialog>
  </div>
</template>

<script>
var util = require("@/utils/util.js");
/**
 * <img-preview :src='preview_src' :show.sync='preview_show'='false'></img-preview>
 */
export default {
  name: 'img-preview',
  props: {
    src:{//图片地址
      type: String,
      default () {
        return '';
      }
    },
    show: { //父组件需要show.sync
      type: Boolean,
      default () {
        return false;
      }
    },
    width:{
      default () {
        return '';
      }
    }
    
  },
  watch:{
    pageShow(newVal){
      this.$emit('update:show', newVal)
    },
    show(newVal){
      this.pageShow = newVal
    }
  },
  computed: {
    styleCss(){
      if(this.width!='auto'){
        return 'width:'+this.width+'px'
      }
      return 
    }
  },
  data () {
    return {
      pageShow: false
    };
  },
  created(){

  },
  methods: {
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    }
  },
   
};
</script>

<style scoped>
  .imgpreview-box .mydialog-cotent{
    margin: 0 auto;
  }
  .imgpreview-box .el-dialog .img-preview{
    width: 100%;
    height: 100%;
  }
</style>
