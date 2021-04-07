<!--
  复制粘贴组件
-->
<template>
  <span>
    <el-button @click='handleCopy(data,$event)' :size="size">复制</el-button>
  </span>
</template>

<script>
var util = require("@/utils/util.js");
export default {
  name: 'clip',
  props: {
    // 要复制的内容
    data: {
      type: String,
      default () {
        return '';
      }
    },
    size: {
      type: String,
      default () {
        return '';
      }
    },
  },
  data () {
    return {
     
    };
  },
 
  methods: {
    handleCopy(text, event) {
      console.log(event)
      util.clip(text, event)
    }
  },
 
};
</script>

<style scoped>
  
</style>
