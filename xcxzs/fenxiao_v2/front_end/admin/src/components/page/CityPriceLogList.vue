<template>
  <el-dialog
    width="55%"
    title="区域"
    :visible.sync="innerDialogVisible"
    append-to-body>

    <el-form :inline="true" :model="innerSearch" class="form-serch">
     <!-- <el-form-item label="变更时间">
           <el-date-picker
            style="width:100%"
            v-model="searchTime"
            value-format="yyyy-MM" format="yyyy-MM"
            type="monthrange"
            range-separator="-"
            start-placeholder="开始日期"
            end-placeholder="结束日期">
          </el-date-picker>
        </el-form-item> -->

      <!-- <el-form-item>
        <el-button type="primary" icon="el-icon-search" @click="onInnerSerch">查询</el-button>
      </el-form-item> -->
    </el-form>

    <el-table
        ref="innerTable"
        :data="innerTableData"
        height="350px"
        highlight-current-row
        style="width: 100%"
        border
    >
      
      <!-- <el-table-column property="id" label="ID" width="80" align="center"></el-table-column> -->
      <el-table-column property="cname" label="城市" align="center"></el-table-column>
      <el-table-column property="new_price" width="180" label="变动价格" align="center"></el-table-column>
      <el-table-column property="change_time" width="180" label="时间" align="center"></el-table-column>
    </el-table>

  </el-dialog>
</template>
<script>
// import { log } from 'util';
var util = require("@/utils/util.js");
import baseMixin from  '@/mixin/baseMixin';
import constMixin from  '@/mixin/constMixin';

export default {
  name: 'estates-new',
  mixins: [baseMixin,constMixin],
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
    startTime:{//是否是自己权限的城市列表
      default () {
        return '';
      }
    },
     endTime:{//是否是自己权限的城市列表
      default () {
        return '';
      }
    },
    cityId:{//是否是自己权限的城市列表
      default () {
        return '';
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

      if(newVal==true){//城市数据未发生变更时保留上次搜索数据
        if(this.innerSearch.region_no == this.region_no && this.innerTableData.length){
          return
        }
        console.log(this.cityId)
        this.innerSearch.city_id  = this.cityId
        this.innerSearch.region_no = this.region_no
        //  this.innerSearch.start_time = this.startTime
        //   this.innerSearch.end_time = this.endTime
        this.onInnerSerch()
      }
    },
    show(newVal){
      this.innerDialogVisible = newVal
    },

    searchTime(newVal){
              if(newVal){
                this.innerSearch.start_time = newVal[0]
                this.innerSearch.end_time = newVal[1]
              }else{
                this.innerSearch.start_time = ''
                this.innerSearch.end_time = ''
              }
        }
    
  },
  data() {
    return {
      url: 'cityPriceLog/cityAreaPriceList',
      innerTableData:[],
      searchTime:[],
      innerDialogVisible: false,
      innerSearch:{
        name:'',
        region_no:'',
        status:'',
        sale_status:'',
        recommend:'',
        start_time:'',
        end_time:''
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
   

    //that.onInnerSerch()
  },

  methods:{
    openPage: util.openPage,

    innerSelcetRow(row){
      //console.log(row)
      this.innerDialogVisible = false
      this.$emit('innerFormData',row)
    },
   
    onInnerSerch(){
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
    

  }

};
</script>
<style lang="scss" scoped>
  .form-serch {
    text-align: left;
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


