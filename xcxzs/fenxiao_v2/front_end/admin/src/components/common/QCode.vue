<template>
  <div class="qcode-box" @click="qrcodeImgClick">
    <slot name="header"></slot>
    <div class="qcode-item" :id='id_name'></div>
    <slot name="footer"></slot>

    <div v-if="need_big" class="qcode-bigitem" style="display:none" :id='id_name_big'></div>
  </div>
</template>

<script>
//=====https://www.runoob.com/w3cnote/javascript-qrcodejs-library.html==//
import QRCode from 'qrcodejs2'  // 引入qrcode
/**
 * 列表渲染时父组件的每个div的item需加v-if="qrcode_show"，在请求时必须通过v-if重载组件，才可刷新二维码数据
 */
export default {
  components: {
    
  },
  name: "QCode",
  props:{
    id_name:{
      type:String,
      default:''
    },
    value:{
      type:String,
      default:''
    },
    width:{
      type:String,
      default:'132'
    },
    height:{
      type:String,
      default:'132'
    },
    need_big:{
      type:Boolean,
      default: false
    },
    big_width:{
      type:String,
      default:'600'
    },
    big_height:{
      type:String,
      default:'600'
    },
  },
  data() {
    return {
      qrcodeObj: null,
      qrcodeBigObj: null
    };
  },
  watch: {
 
  },
  computed:{
    id_name_big(){
      return this.id_name+'_big'
    }
  },
  mounted () {
    this.qrcodeObj=this.qrcode({
      id_name: this.id_name,
      value: this.value,
      width:Number(this.width),
      height:Number(this.height),
    });

    if(this.need_big){
      this.qrcodeBigObj=this.qrcode({
        id_name: this.id_name_big,
        value: this.value,
        width:Number(this.big_width),
        height:Number(this.big_height),
      });
    }
  },

  methods: {
    //二维码
    qrcode(e) {
      if(!e.value||!e.id_name){
        return
      }
      return new QRCode(e.id_name, {
        width: e.width,  
        height: e.height,
        text: e.value, // 二维码内容
        colorDark : "#000",
        colorLight : "#fff",
      })
    },
    //获取二维码对象
    getQrcodeObj(){
      return {
        qrcodeObj: this.qrcodeObj,
        qrcodeBigObj: this.qrcodeBigObj
      }
    },
    qrcodeImgClick(e){
      var imgSrc= {
        imgSrc:e.srcElement.currentSrc,
        bigimgSrc:this.qrcodeBigObj? this.qrcodeBigObj._el.children[1].currentSrc:''
      }
      this.$emit('onQrcodeImgClick',{imgSrcObj:imgSrc, qrcodeObj:this.getQrcodeObj()})
    }
  }
};
</script>
<style lang="scss" scoped>
.qcode-bigitem /deep/img{
 display: none!important;
}
</style>
