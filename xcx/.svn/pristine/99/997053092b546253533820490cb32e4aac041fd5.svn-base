<template>
  <el-dialog
    width="55%"
    title="图片"
    :visible.sync="innerDialogVisible"
    append-to-body>

    <div class="demo-image">
      <div class="block" v-for="(data, category) in imgsData" :key="category">
        <el-row class="demonstration">
          {{ category }}
        </el-row>
        <el-row>
          <div class="img-selectbox" v-for="(img, index) in data" :key="index">
            <span @click="setImgs(img.id, img.cover_url[0].url)">
              <el-image :src="img.cover_url[0].url" :fit="'cover'" ></el-image>
            </span>
            <i v-if="selectImgs[img.id]" class="el-icon-circle-check check_select"></i>
          </div>
        </el-row>
      </div>
    </div>

    <div slot="footer" class="dialog-footer">
      <el-button type="primary" icon="el-icon-success" @click="doSubmit()">保存</el-button>
    </div>
  </el-dialog>
    
</template>
<script>
// import { log } from 'util';
var util = require("@/utils/util.js");
import baseMixin from  '@/mixin/baseMixin';

export default {
  name: 'estates-imgs',
  mixins: [baseMixin],
  directives: {
      loadmore: {
        // 指令的定义
        bind(el, binding, vnode) {
          const selectWrap = el.querySelector('.el-table__body-wrapper')
          selectWrap.addEventListener('scroll', function() {
            const sign = 100
            const scrollDistance = this.scrollHeight - this.scrollTop - this.clientHeight
            if (scrollDistance <= sign) {
              binding.value()
              // console.log('距离底部小于100了')
              // console.log(vnode.context)
              // // 指令中不能用this关键字
              // vnode.context.getNewData()
            }
          })
        }
      },
    },
  props: {
    estate_id:{
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
    selectData:{
      default () {
        return [];
      }
    },
    limiNum:{
      default () {
        return 3;
      }
    }
  },

  watch:{
    innerDialogVisible(newVal){
      this.$emit('update:show', newVal)
    },
    show(newVal){
      this.innerDialogVisible = newVal
    },
    estate_id(newVal){
      this.getList();
    },
  },
  data() {
    return {
      url: 'Estates/getImgsListGroup',
      innerLoads:{
        loadSign: false,
        page: 0,
        last_page:1,
      },
      innerDialogVisible: false,
      imgsData: {},
      selectImgs:{},
    }
  },

  created: function(){
    var that = this
    this.getList();
  },

  methods:{
    openPage: util.openPage,

    clearEstates(){
      this.formData.estates_name = '';
      this.formData.forid = 0;
    },

    getList(searchdata={}){
      var that=this;

      searchdata.estate_id = that.estate_id

      util.requests("post", {
        url: that.url,
        data: searchdata
      }).then(res => {
        {
          if (res.code == 1) {
             this.imgsData = res.data

             for(var i in this.imgsData){
               let item = this.imgsData[i]
               for(var j in item){
                 let item2 = item[j]
                 if(item2.cover_url&&item2.cover_url[0]&&item2.cover_url[0].url&&this.selectData.includes(item2.cover_url[0].url)){
                  this.selectImgs[item2.id] = {url: item2.cover_url[0].url}
                }
               }
             }

             //console.log(this.imgsData, this.selectData, this.selectImgs,88888888)
          } else {
            util.Message.error(res.msg);
          }
        }
      });
    },
    getFormatDate:util.DataFun.getFormatDate,

    setImgs(id, url) {
      if(!url) {
        return
      }
      
      if(this.selectImgs[id]) {// 原先已在数组中，就去掉
          delete this.selectImgs[id]
      } else {// 原先不在数组中，加入
          if(Object.keys(this.selectImgs).length>=Number(this.limiNum)){
            this.$message({
                type: 'error',
                message: '数量不能超过'+this.limiNum
            });
            return;
          }

          this.selectImgs[id] = {url: url}
      }
      
      this.$forceUpdate();
    },

    doSubmit() {
      let finalUrls = []
      for(var img in this.selectImgs) {
        finalUrls.push(this.selectImgs[img].url);
      }
      //console.log(finalUrls)
    
      this.$emit('update:selectData', finalUrls);
      this.innerDialogVisible = false
    }
  },

};
</script>
<style lang="scss" scoped>
  .demonstration{
    margin-bottom: 15px;;
  }
  .img-selectbox{
    position: relative;
    width: 100px;
    height: 100px;
    float: left;
    margin-right: 15px;
    margin-bottom: 15px;
    .el-image{
      width: 100%;
      height: 100%;
    }
    .check_select{
      position: absolute;
      top: 10px;
      right: 10px;
      position: absolute;
      top: 10px;
      right: 10px;
      color: #409EFF;
      font-size: 20px;
    }
  }
  
  .type{
    float: right;
    position: relative;
    top: -164px;
    left: -100px;
  }
  .editimg{
    float: left;
  }
  .infoEdit{
    float: right;
  }

</style>


