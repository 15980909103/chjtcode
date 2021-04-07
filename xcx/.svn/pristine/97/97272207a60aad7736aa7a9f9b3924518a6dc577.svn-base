<template>
  <el-dialog
    width="55%"
    title="新房"
    :visible.sync="innerDialogVisible"
    append-to-body>

    <el-form :inline="true" :model="innerSearch" class="form-serch">
      <el-form-item>
        <el-input style="width:180px;" v-model="innerSearch.name" placeholder="标题" prefix-icon="el-icon-search"></el-input>
      </el-form-item>

      <el-form-item >
        <el-select style="width:150px;" v-model="innerSearch.sale_status" placeholder="销售状态选择">
          <el-option label="全部" value="-1"></el-option>
          <el-option v-for="(item, i) in const_estates_new_sale_status" :key="i" :label="item" :value="i"></el-option>
        </el-select>
      </el-form-item>

      <el-form-item >
        <el-select style="width:150px;" v-model="innerSearch.status" placeholder="启用状态选择">
          <el-option label="全部" value="-1"></el-option>
          <el-option label="启用" value="1"></el-option>
          <el-option label="禁用" value="0"></el-option>
        </el-select>
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
      
      <el-table-column property="id" label="ID" width="80" align="center"></el-table-column>
      <el-table-column property="name" label="标题" align="center"></el-table-column>
      <el-table-column property="recommend" width="80" label="是否推荐" align="center">
        <template slot-scope="scope">
          {{scope.row.recommend==1?'是':'否'}}
        </template>
      </el-table-column>
      <el-table-column property="status" width="80" label="状态" align="center">
        <template slot-scope="scope">
          {{scope.row.status==1?'启用':'禁用'}}
        </template>
      </el-table-column>
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
    // innerSelcetRow:{
    //   type: Function,
    // },
    region_no:{
      default () {
        return '';
      }
    },
    area_no:{
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
        this.innerSearch.region_no = this.region_no
        this.onInnerSerch()
      }
    },
    show(newVal){
      this.innerDialogVisible = newVal
    },
    
  },
  data() {
    return {
      url: 'news/getList',
      innerTableData:[],
      innerDialogVisible: false,
      innerSearch:{
        name:'',
        region_no:'',
        status:'1',
        sale_status:'',
        recommend:'',
        area_no:'',
        from:'innerTable',
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
    that.innerSearch.region_no = that.region_no
    that.innerSearch.area = that.area_no

    that.getEstatesNewSaleStatus();
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


