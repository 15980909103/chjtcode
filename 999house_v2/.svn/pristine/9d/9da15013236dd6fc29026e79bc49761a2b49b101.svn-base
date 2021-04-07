<template>
  <el-button type="primary" size="small" @click="doDownLoadImg">下载</el-button>
</template>

<script>
import md5 from 'js-md5';//引入md5

export default {
  components: {
    
  },
  name: "download-base64img",
  props:{
    fileName:{
      type:String,
      default(){
        var timestamp =Date.parse(new Date());
        return md5('img'+timestamp).toLowerCase()
      }
    },
    content:{ }
  },
  data() {
    return {
      
    };
  },

  methods: {

    doDownLoadImg(){
      let fileName = this.fileName
      let content = this.content
      
      let aLink = document.createElement('a');
      let blob = this.base64ToBlob(content); //new Blob([content]);
      let evt = document.createEvent("HTMLEvents");
      evt.initEvent("click", true, true);//initEvent 不加后两个参数在FF下会报错  事件类型，是否冒泡，是否阻止浏览器的默认行为
      aLink.download = fileName;
      aLink.href = URL.createObjectURL(blob);

      // aLink.dispatchEvent(evt);
      aLink.click()
    },

     //base64转blob
    base64ToBlob(code) {
      let parts = code.split(';base64,');
      let contentType = parts[0].split(':')[1];
      let raw = window.atob(parts[1]);
      let rawLength = raw.length;

      let uInt8Array = new Uint8Array(rawLength);

      for (let i = 0; i < rawLength; ++i) {
        uInt8Array[i] = raw.charCodeAt(i);
      }
      return new Blob([uInt8Array], {type: contentType});
    }
  }
};
</script>
<style scoped>
.van-radio {
  display: inline-block;
}
</style>
