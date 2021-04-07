<template>
  <el-dialog
    width="55%"
    title="直播"
    :visible.sync="innerDialogVisible"
    append-to-body>

    <el-form :inline="true" :model="innerSearch" class="form-serch">
      <el-form-item>
        <el-input v-model="innerSearch.room_name" placeholder="标题" prefix-icon="el-icon-search"></el-input>
      </el-form-item>

      <el-form-item>
        <el-button type="primary" icon="el-icon-search" @click="onInnerSerch">查询</el-button>
      </el-form-item>
    </el-form>

    <el-table
        ref="innerTable"
        :data="innerTableData"
        v-loadmore="innerLoadMore"
        height="350px"
        highlight-current-row
        @row-click = "innerSelcetRow"
        style="width: 100%"
        border
    >
      
      <el-table-column property="id" width="80" label="ID" align="center"></el-table-column>
      <el-table-column property="room_name" label="标题" align="center"></el-table-column>
      <el-table-column prop="forname" label="绑定的楼盘"  align="center"></el-table-column>
      <el-table-column property="status" width="70" label="状态" align="center">
        <template slot-scope="scope">
          {{scope.row.status==1?'启用':'禁用'}}
        </template>
      </el-table-column>
      <el-table-column label="有效时间" width="255" align="center">
        <template slot-scope="scope">
        {{getFormatDate(scope.row.start_time,3)}} - {{getFormatDate(scope.row.end_time,3)}}
        </template>
      </el-table-column>
    </el-table>

  </el-dialog>
</template>
<script>
// import { log } from 'util';
var util = require("@/utils/util.js");
import baseMixin from  '@/mixin/baseMixin';

export default {
  name: 'live-room',
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
    region_no:{
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
    // innerSelcetRow:{
    //   type: Function,
    // },
    region_no:{
      default () {
        return '';
      }
    },
  },

  watch:{
    innerDialogVisible(newVal){
      this.$emit('update:show', newVal)
    },
    show(newVal){
      this.innerDialogVisible = newVal
    }
  },
  data() {
    return {
      url: 'LiveRoom/getList',
      innerTableData:[],
      innerDialogVisible: false,
      innerSearch:{
        name:'',
        region_no:'',
      },
      innerLoads:{
        loadSign: false,
        page: 0,
        last_page:1,
      },
    }
  },

  created: function(){
    var that = this 
   
  },

  methods:{
    openPage: util.openPage,

    clearEstates(){
      this.formData.estates_name = '';
      this.formData.forid = 0;
    },
    innerSelcetRow(row){
      //console.log(row)
      this.innerDialogVisible = false
      this.$emit('innerFormData',{id:row.id, name:row.room_name})
    },
   
    onInnerSerch(){
      this.innerSearch.region_no = this.region_no
      this.getList({page: 1, ...this.innerSearch});
    },
    innerLoadMore () {
      this.getList({page: this.innerLoads.page+1, ...this.innerSearch});
    },
    getList(searchdata={}){
      var that=this;
      if(that.innerLoads.loadSign==true){
        return
      }

      let page = searchdata.page
      searchdata.field = 'en.name,en.id'
      if(page==1){//重置page
        that.innerLoads.last_page = 1
        that.innerTableData = []
      }
      if (page > that.innerLoads.last_page) {
        return
      }
      that.innerLoads.loadSign = true; 

      util.requests("post", {
        url: that.url,
        data: searchdata
      }).then(res => {
        {
          that.innerLoads.loadSign = false;
          if (res.code == 1) {
            for(let key in res.data.list){
              that.innerTableData.push(res.data.list[key]);
            }
            
            that.innerLoads.last_page = res.data.last_page 
            that.innerLoads.page = res.data.current_page 
          } else {
            util.Message.error(res.msg);
          }
        }
      });
    },
    getFormatDate:util.DataFun.getFormatDate,

  }

};
</script>
<style lang="scss" scoped>
  .form-serch {
    text-align: right;
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


