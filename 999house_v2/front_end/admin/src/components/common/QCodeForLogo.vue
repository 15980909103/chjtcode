<template>
  <div class="qcode-box" @click="qrcodeImgClick">
    <slot name="header"></slot>
    <div class="qcode-item" :id='id_name'>
      <vue-qr :margin='margin' :correctLevel='correctLevel' :bgSrc='bgSrc' :logoSrc="logoSrc" :text="value" :logoScale="logoScale" :logoMargin='logoMargin' :logoBackgroundColor='logoBackgroundColor' :size="width" :dotScale='dotScale' ></vue-qr>
    </div>
    <slot name="footer"></slot>

    <div v-if="need_big" class="qcode-bigitem" style="display:none" :id='id_name_big'>
      <vue-qr :ref="id_name_big" :margin='margin' :correctLevel='correctLevel' :bgSrc='bgSrc' :logoSrc="logoSrc" :text="value" :logoScale="logoScale" :logoMargin='logoMargin' :logoBackgroundColor='logoBackgroundColor' :size="big_width" :dotScale='dotScale'></vue-qr>
    </div>
  </div>
</template>

<script>
//=====https://www.npmjs.com/package/vue-qr  ===//
import VueQr from 'vue-qr'
/**
 * 列表渲染时父组件的每个div的item需加v-if="qrcode_show"，在请求时必须通过v-if重载组件，才可刷新二维码数据
 */
export default {
  components: {
    VueQr
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
    logoScale:{
      type:Number,
      default:0.25
    },
    logoSrc:{//中间logo图片
      type:String,
      default:''
    },
    bgSrc:{//背景图片
      type:String,
      default:''
    },
    width:{
      type:Number,
      default:132
    },
    need_big:{
      type:Boolean,
      default: false
    },
    big_width:{
      type:Number,
      default:600
    },
    
  },
  data() {
    return {
      margin: 8, //二维码图像的外边距
      correctLevel: 3,//容错级别 0-3，3为最高
      dotScale: 0.8, //数据区域点缩小比例
      logoMargin: 3, //LOGO 标识周围的空白边框
      logoBackgroundColor: '#fff' //Logo 背景色,需要设置logoMargin
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
    
  },

  methods: {
    qrcodeImgClick(e){
      if(this.$refs[this.id_name_big]){
        var bigimgel= this.$refs[this.id_name_big].$el
        this.$emit('onQrcodeImgClick',{bigImgSrc: bigimgel.currentSrc, })
      }
    },
  }
};
</script>
<style lang="scss" scoped>
.qcode-bigitem /deep/img{
 display: none!important;
}
</style>
