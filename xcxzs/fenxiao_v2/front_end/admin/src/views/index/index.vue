<template>
  <div class="_container">
    <div class="admin_huang admin_heard">
      <div class="admin_huang" style=" width: 65% ;height: 397px;display: flex;flex-direction:column">
          <div class="admin_huang" style="margin-bottom: 15px ; height: 210px">
            <el-row>
              <span class="admin_m20 admin_font1">待办事项</span>
            </el-row>
            <el-divider class="admin_lr20"></el-divider>
            <div style="display: flex;margin: 20px 24px">
                <div class="admin_ico admin_ico1" @click="openPage({url:'/news/index',data:{status:2}})">
                  <span class=" admin_p_32 admin_font2">{{newsStatusBy2}}</span>
                  <span class="admin_p_59 admin_font3" >待办资讯</span>
                  <span class="admin_p_11 el-icon-edit-outline"> </span>
                </div>

              <div class="admin_ico admin_ico2" @click="openPage({url:'/marketing/subject'})">
                <span class=" admin_p_32 admin_font2">{{subjectCount}}</span>
                <span class="admin_p_59 admin_font3" >激活活动</span>
                <span class="admin_p_11 el-icon-present"> </span>
              </div>
              <div class="admin_ico admin_ico3" @click="openPage({url:'/user/material'})">
                <span class=" admin_p_32 admin_font2">{{comentCount}}</span>
                <span class="admin_p_59 admin_font3" >审批评论</span>
                <span class="admin_p_11 el-icon-edit-outline"> </span>
              </div>
              <div class="admin_ico admin_ico4">
                <span class=" admin_p_32 admin_font2">{{htCount}}</span>
                <span class="admin_p_59 admin_font3" >待审话题</span>
                <span class="admin_p_11 el-icon-chat-line-round"> </span>
              </div>
            </div>
          </div>
          <div class="admin_huang " style="height: 177px">
            <el-tabs v-model="activeName_cz" @tab-click="handleClick_cz" style="margin: 0 20px">
              <el-tab-pane label="创作" name="first">
                <div style="display: flex">
                  <div v-for="item in topagelist" >
                    <div style="display: flex ;flex-direction: column;justify-items: center;margin: 20px"  @click="openPage({url:item.url})" class="hoverchangeclor" >
                      <span style="text-align: center;margin: 10px 0;display: block;font-size: 32px" :class="item.ico"></span>
                      <span class="admin_font8">{{item.name}}</span>
                    </div>
                  </div>

                </div>
              </el-tab-pane>
              <el-tab-pane label="工具" name="second">
                <div>
                </div>
              </el-tab-pane>
            </el-tabs>
          </div>
      </div>
      <div class="admin_huang" style="width: 33%  ; margin-left: 15px; height: 397px;display: inline-block;position: relative" v-if="institute['xf']">
        <el-row>
          <span class="admin_m20 admin_font1">大数据看板</span>
        </el-row>

        <el-divider class="admin_lr20"></el-divider>
        <el-row class="admin_m20 w59h22">
          <el-date-picker
            v-model="academyTime"
            type="month"
            placeholder="选择月份">
          </el-date-picker>
        </el-row>
        <el-row style="display: flex; flex-direction: column;">
          <span style="margin: 0 auto " class="admin_font4" > {{institute['xf'].recent_opening }}</span>
          <span style="margin: 0 auto" class="admin_font5"> 近期开盘</span>
        </el-row>

        <el-row style="display: flex;flex-direction: row">
          <el-col  style="margin: auto 48px;text-align: center" class="admin_font4">
              <div style="display: flex;flex-direction: column">
                <span style="margin-top: 5px;">{{institute['xf'].price}}</span>

                <span class="admin_font6 el-icon-top" style="margin-top: 5px;" v-if="institute['xf'].last_month_type == 1" >{{institute['xf'].last_month_rate}}</span>
                <span class="admin_font7 el-icon-bottom" style="margin-top: 5px;" v-else>{{institute['xf'].last_month_rate}}</span>
                <span class="admin_font5" style="margin-top: 5px;">新房价格(元/m²)</span>
              </div>
          </el-col>
          <el-col  style="margin:  auto 48px ;text-align: center" class="admin_font4">
            <div>
              <div style="display: flex;flex-direction: column">
                <span style="margin-top: 5px;">{{institute['esf'].price}}</span>

                <span class="admin_font5" style="margin-top: 5px;">二手房价格(元/m²)</span>
              </div>

            </div>
          </el-col>
        </el-row>
        <div style='position:absolute;bottom: 20px;display: flex;justify-content: center;width:100%;'>
            <span class="admin_font5" style="margin-left: 14px" v-for="itme in city_lsit"><a>{{itme.cname}}</a></span>
        </div>
     </div>
  </div>

    <div class="admin_huang admin_heard h580">
      <div class="admin_huang" style=" width: 50% ;height: 580px;display: flex;flex-direction:column;">
        <el-row>
          <span class="admin_m20 admin_font1">最新创作</span>
        </el-row>
        <el-divider class="admin_lr20"></el-divider>
        <div style="margin: 0px 20px" class="my_dome">
          <swipe style="width: 100%;height:500px" :auto="500000000000">
            <swipe-item v-for="(item,index) in updateData" :key="index"  style="height: 500px" >
              <el-table :data="item" v-loading="page_loading" :show-header="false" :cell-style= "()=>{return 'padding:9px 0;'}" :border="false">
                <el-table-column prop="ico" width="62px;" >
                  <template slot-scope="scope">
                <span class="pd9">
                    <img :src="scope.row.index_ico" style="width: 42px;height: 42px;">
                </span>
                  </template>
                </el-table-column>

                <el-table-column style="margin-left: 20px " >
                  <template slot-scope="scope">
                    <div style="display: flex;flex-direction: column;height: 53px;" @click="opindexPage(scope.row.id)">
                      <div style="display: flex;flex-direction: row;">
                        <span style="width: 90%;display: inline-block;margin-bottom: 5px;margin-right: 24px" class="amdin_outtitle admin_font9">{{scope.row.name}}</span>
                        <span  class="admin_font5">{{scope.row.update_time}}</span>
                      </div>
                      <div>
                        <div style="display: flex;flex-direction: row;">
                          <span style="width: 90%;" class="amdin_outtitle admin_font5">{{scope.row.commentNum}}评论&nbsp;&nbsp;.&nbsp;&nbsp;{{scope.row.num_read}}阅读</span>
                        </div>
                      </div>
                    </div>
                  </template>
                </el-table-column >
              </el-table>
            </swipe-item>
          </swipe>
        </div >


      </div>
      <div class="admin_huang" style="width: 48%  ; margin-left: 15px; height: 580px;display: inline-block;position: relative">
        <el-row>
          <span class="admin_m20 admin_font1">热门排行</span>
        </el-row>
        <el-divider class="admin_lr20"></el-divider>
        <div class="admin_m20 ">
          <el-tabs  type="card" v-model="activeName_zx"  @tab-click="handleClick_zx" >
            <el-tab-pane label="资讯" name="newsHos" class="my_table1">
              <el-table :data="hosData" v-loading="page_loading" :show-header="false" :cell-style="()=>{return 'padding:9px 0; border-bottom-width: 0px;'}">
                <el-table-column style="margin-left: 20px " >
                  <template slot-scope="scope">
                    <div style="display: flex;flex-direction: column;height: 20px;"  @click="opindexPage(scope.row.id)">
                      <div style="display: flex;flex-direction: row;">
                        <span style="width: 5% "  v-if="scope.$index <3">
                          <img :src="'../../../static/img/' + (scope.$index+1) +'.png' " style="width: 18px;height: 19px">
                        </span>
                        <span v-else style="width: 5%"></span>
                        <span style="width: 85%;display: inline-block;margin-bottom: 5px;margin-right: 24px" class="amdin_outtitle admin_font10">{{scope.row.name}}</span>
                        <span style="width: 15%;display: inline-block;margin-bottom: 5px;margin-right: 24px" class="amdin_outtitle admin_font10">阅读{{scope.row.num_read}}</span>
                      </div>
                    </div>
                  </template>
                </el-table-column >
              </el-table>
              <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>
            </el-tab-pane>
            <el-tab-pane label="话题" name="htHos" >
              <span>暂无数据</span>
            </el-tab-pane>
            <el-tab-pane label="视频" name="voideHos" >
              <el-table :data="hosData" v-loading="page_loading" :show-header="false" :cell-style="()=>{return 'padding:6px 0'}">
                <el-table-column style="margin-left: 20px " >
                  <template slot-scope="scope" >
                    <div style="display: flex;flex-direction: row;height: 70px" @click="opindexPage(scope.row.id)">
                      <div style="width: 10%; margin: auto 0;">
                        <!-- <img src="../../../static/img/1609396979(1).jpg" style="width: 65px;height: 55px;"> -->
                      </div>
                      <div style="display: flex;flex-direction: column;width: 90% ;margin: auto 0;">
                        <span style="display: inline-block;margin-bottom: 5px;margin-right: 24px" class="amdin_outtitle admin_font11">{{scope.row.name}}</span>
                        <div style="display: flex;position: relative">
                          <!-- <img src="../../../static/img/1609396979(1).jpg" style="width: 26px;height: 26px;border-radius: 50% ;margin-right: 10px"> -->
                          <span style="display: inline-block;margin-right: 24px" class="admin_font12">淘房师啊读</span>
                          <span style="display: inline-block;margin-right: 24px;position: absolute;right: 0px" class=" admin_font12">阅读5000</span>
                        </div>

                      </div>
                    </div>
                  </template>
                </el-table-column >
              </el-table>
              <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>
            </el-tab-pane>
          </el-tabs>
        </div>
      </div>

    </div>
    <div class="admin_huang admin_heard h580" style="margin-right: 20px;display: flex;flex-direction: column">
      <div style="display: flex;flex-direction: row">
        <div style="width: 77%">
          <span class="admin_m20 admin_font1">流量趋势</span>
        </div>
        <div style="margin: 20px auto">
          <el-date-picker
            style="width:250px;height: 40px"
            v-model="searchData.auction_time"
            value-format="yyyy-MM-dd" format="yyyy-MM-dd"
            type="datetimerange"
            align="center"
            range-separator="-"
            start-placeholder="开始日期"
            end-placeholder="结束日期">
          </el-date-picker>
        </div>

      </div>

      <el-divider class="admin_lr20"></el-divider>

      <div style="margin: 20px">
        <el-tabs v-model="activeName_tb" type="card" @tab-click="handleClick_tb">
          <el-tab-pane label="总体" name="first">
            <div ref="charall" style="width: 100%;height: 400px">
            </div>
          </el-tab-pane>
          <el-tab-pane label="pc端" name="second">
            <div ref="charpc">

            </div>
          </el-tab-pane>
          <el-tab-pane label="移动端" name="third">
            <div ref="charyd">
            </div>
          </el-tab-pane>
          <el-tab-pane label="app端" name="fourth">
            <div  ref="charapp">
            </div>
          </el-tab-pane>
        </el-tabs>
      </div>

    </div>
  </div>

</template>

<script>
import { Swipe, SwipeItem } from 'vue-swipe'
var util = require("@/utils/util");
import paginationBox from '../../components/common/pagination.vue';
import ImgUpload from '@/components/common/ImgUpload.vue';
import cityList from "../admin/cityList";
export default {
  components: {
			'pagination-box': paginationBox,
    'img-upload': ImgUpload,
    'swipe': Swipe,
    'swipe-item': SwipeItem,
	},
  data() {
    return {
      siteCitys:{},
      activeName_cz:'first',
      activeName_tb:'first',
      activeName_zx:'newsHos',
      formLabelWidth: "123px",
      rank_click_city_no:'350200',
      newsStatusBy2: 0,
      academyTime:'',
      htCount: 0,
      hosData:[],
      subjectCount: 0,
      comentCount: 0,
      rules: { },
      institute:[],
      value3:'',
      city_list:[350200,350500],
      searchData: {
        name: "",
        status:-1,
        type:-1,
        auction_time:'',
        transaction_time:'',
        transaction_price:-1,
        city_lsit:[350200]
      },
      radio:false,
      updateData:[],
      topagelist:[
        {
          url:'/news/edit',
          ico:'el-icon-s-order',
          name:'资讯',
        },{
          url:'',
          ico:'el-icon-chat-line-round',
          name:'话题',
        },{
          url:'/news/videoedit',
          ico:'el-icon-video-camera',
          name:'视频',
        },{
          url:'/marketing/liveRoom',
          ico:'el-icon-video-camera-solid',
          name:'直播',
        },{
          url:'/marketing/subject',
          ico:'el-icon-menu',
          name:'专题',
        }
      ],
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据
      select_ids :[],
    };
  },
  mounted:function(){
    var charall = this.$refs.charall;

    if(charall){
      var myChart = this.$echarts.init(charall);
      var option = {
        legend: {
          bottom:'1%'
        },
        tooltip: {},
        dataset: {
          source: [
            ['product', '预览量', '访客数', 'ip数'],
            ['7/12', 43.3, 85.8, 52.55],
            ['7/13', 83.1, 73.4, 55.1],
            ['7/14', 86.4, 65.2, 82.5],
            ['7/15', 72.4, 53.9, 39.1],
            ['7/16', 72.4, 53.9, 39.1]
          ]
        },
        xAxis: {type: 'category'},
        yAxis: {},
        // Declare several bar series, each will be mapped
        // to a column of dataset.source by default.
        series: [
          {type: 'bar',
            itemStyle:{
              normal:{
                color:'rgba(117, 164, 254, 1)'
              }
            },
          },
          {type: 'bar',
            itemStyle:{
              normal:{
                color:'rgba(121, 209, 142, 1)'
              }
            },},
          {type: 'bar',itemStyle:{
              normal:{
                color:'rgba(255, 170, 84, 1)'
              }
            },}
        ]
      };
      // console.log(12312);
      myChart.setOption(option);
    }



  },
  created: function() {
    this.city_lsit    = this.$store.getters.userinfo.region_nos_info;
    this.academyTime = this.getFormatYm(); //获取当前月
    // let city_list  =  [];
    // for (this.city_lsit in item){
    //   city_list.push(item.id);
    // }

    this.getIndexData({city_list:this.city_list});
    this.getInstituteList();
    this.getRankList();//获取榜单数据
    this.tableData_1 =[
      {
        id:1,
        title:'测试数据，测试数据，测试数据，测试数据，测试数据，测试数据，测试数据，测试数据测试数据',
        ico:'../../static/img/1609396979(1).jpg',
        create_time:'10月31',
        pl:1000,
        yd:1000,
        city_no:'厦门'
      },
      {
        id:1,
        title:'测试数据，测试数据，测试数据，测试数据，测试数据，测试数据，测试数据，测试数据测试数据',
        ico:'../../static/img/1609396979(1).jpg',
        create_time:'10月31',
        pl:1000,
        yd:1000,
        city_no:'厦门'
      },
      {
        id:1,
        title:'测试数据，测试数据，测试数据，测试数据，测试数据，测试数据，测试数据，测试数据测试数据',
        ico:'../../static/img/1609396979(1).jpg',
        create_time:'10月31',
        pl:1000,
        yd:1000,
        city_no:'厦门'
      },
      {
        id:1,
        title:'测试数据，测试数据，测试数据，测试数据，测试数据，测试数据，测试数据，测试数据测试数据',
        ico:'../../static/img/1609396979(1).jpg',
        create_time:'10月31',
        pl:1000,
        yd:1000,
        city_no:'厦门'
      },

    ]
    // this.getList(this.searchData)
    // this.getRoleList()
    // this.getSiteCitys()
  },
  methods: {
    getInstituteList(){
      let that = this;
      util.requests("post", {
        url: "index/getInstituteList",
        data: {city_no:this.city_list[0],date:this.academyTime}
      }).then(res => {
        if(res.code==1){
          that.setDataArr({
            institute: res.data
          })
        }else{
          util.Message.error(res.msg);
        }
      });
    },
    getRankList(){
      let that = this;
      util.requests("post", {
        url: "index/getRank",
        data: {type:this.activeName_zx,city_no:this.rank_click_city_no}
      }).then(res => {
        if(res.code==1){
          that.setDataArr({
            hosData:res.data,
          })
        }else{
          util.Message.error(res.msg);
        }
      });
    },
    getIndexData(city_lsit){
      let that = this;
      util.requests("post", {
        url: "index/index",
        data: city_lsit
      }).then(res => {
        if(res.code==1){
          that.setDataArr({
            updateData:res.data.newList['update'],
            newsStatusBy2:res.data.newsStatusBy2,
            htCount:res.data.htCount,
            subjectCount:res.data.subjectCount ,
            comentCount:res.data.comentCount,
          })
        }else{
          util.Message.error(res.msg);
        }
      });
    },
    opindexPage(id){
      // console.log(id,234234);
      let url ='http://mo.999house.com/9house/pages/discover/news_detail.html?id='+id+'&pid=9&cate_id=10';
      window.open(url,'_blank');
    },
    getFormatYm(){
      let date = new Date();
      let Y  = date.getFullYear();
      let m  = date.getMonth();
      return Y +'-' + (m+1);
    },
    handleClick_cz(e){
      console.log(e)
    },
    handleClick_tb(e){
      console.log(e)
    },
    handleClick_zx(e){
      this.hosData =[];
      this.getRankList();
    },
    search(){
      this.getList(this.searchData);
    },
    setLandStatus(land_status){
      var that = this
      if(that.select_ids.length < 1){
        that.$alert('请选择要操作的行');
        return ;
      }
      that.$confirm('是否修改选中行状态?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        if(that.page_loading){
          return
        }
        that.page_loading = true
        util.requests("post", {
          url: "land/setLandStatus",
          data: {ids:this.select_ids,status:land_status}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.onSerch()
            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
        });
      }).catch(() => {
        //console.log('取消')
      });
    },
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },
    onUploadedImg(e){
      this.form.head_ico_id      = e.res.info.id;
      this.form.head_ico_path  = e.res.info.url;
      this.$refs.cover.clearValidate()
    },
    getCityName($city_no){
      let city_name = '';
      this.city_lsit.map((item)=>{
          if(item.id == $city_no){
            city_name = item.cname;
          }
      });

      return city_name;

    },
    getList(searchdata={}) {
      var that = this
      if(that.page_loading){
          return
      }
      if(searchdata.city_lsit && searchdata.city_lsit.length<1){
        that.$alert('请选择城市');
      }
      that.page_loading = true

      util.requests("post", {
          url: "land/index",
          data: searchdata
      }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.setDataArr({
              tableData : res.data.list,
              pagination : {
                page : res.data.current_page,
                pagecount : res.data.last_page,
                pagesize : Math.ceil(res.data.total / res.data.last_page)
              }
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    //分页操作
    pageChange: function(page) {
      let post_data = Object.assign({},this.searchData);
      post_data.page = page;
      this.getList(post_data)
    },
    onSerch() {
      //console.log("查询",this.searchData);
      this.getList(this.searchData)
    },


    resetData(){
      this.setDataArr({
        form: {
          account: "",
          mobile: "",
          email: "",
          newpassword: "",
          newpassword2: "",
          role_id:'',
          status:'',
          region_nos_info:{},
          region_nos_info2:[]
        }
      })
    },
    doEditCancel(){
      var that=this
      that.resetData()
      if(that.dialogFormVisible == true){
        that.dialogFormVisible = false
      }
    },
    doEdit(dotype,e={}){
      let that = this
      that.dialogFormVisible = true
      that.form_type = dotype
      if(Object.keys(e).length>0){
        e.region_nos_info2=[]
        that.form = Object.assign({},e)
        that.form.role_id = Number(that.form.role_id)
        that.form.status = String(that.form.status)

        if(that.form.region_nos_info){
          that.form.region_nos_info = JSON.parse(that.form.region_nos_info)

          for(var i in that.form.region_nos_info){
            that.form.region_nos_info2[i] = Number(that.form.region_nos_info[i].id)
          }
        }
      }
    },
    doEditSubmit(){
      let that=this
      that.form.region_nos_info ={}

      for(var i in that.form.region_nos_info2){
        let item = that.siteCitys[that.form.region_nos_info2[i]]
        that.form.region_nos_info[i] = {id:item.id,cname:item.cname,pid:item.pid,pcname:item.pcname}
      }

      that.$refs['form'].validate((valid) => {
        if (valid) {
          util.requests("post", {
            url: "account/edit",
            data: that.form
          }).then(res => {
              that.page_loading = false
              if(res.code==1){
                util.Message.success('操作成功');
                that.onSerch()
                setTimeout(() => {
                  that.doEditCancel()
                }, 1000);
              }else{
                util.Message.error(res.msg);
              }
          });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    doDel(id){
      var that = this
      if(that.page_loading){
          return
      }
      that.$confirm('该操作将永久删除记录，是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        that.page_loading = true

        util.requests("post", {
            url: "land/delLand",
            data: {id: id}
          }).then(res => {
            that.page_loading = false
            if(res.code==1){
              that.onSerch()
              util.Message.success('操作成功');
            }else{
              util.Message.error(res.msg);
            }
        });
      }).catch(() => {
      });
    },
    handleSelectionChange(e){
      this.select_ids =[];
      for (let i=0;i < e.length;i++){

        this.select_ids[i] = e[i].id;
      }
    },
    switchChange(id,val){
      //console.log(id,val)
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "account/enable",
          data: {id: id,status: val}
        }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.onSerch()
            util.Message.success('操作成功');
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    getFormatDate:util.DataFun.getFormatDate,
    openPage: util.openPage
  }
};
</script>
<style lang="scss" scoped>
._container {
  margin-top: 20px;
  padding: 0px;
  background: #F9FBFF;
}
.admin_font4{
  height: 43px;
  font-size: 34px;
  font-family: DINPro-Medium, DINPro;
  font-weight: 500;
  color: #212121;
  line-height: 43px;

}
.w54_h26{
  width: 54px;
  height: 26px;
}
.admin_font5{
  height: 20px;
  font-size: 14px;
  font-family: PingFangSC-Regular, PingFang SC;
  font-weight: 400;
  color: #ADADAD;
  line-height: 20px;

}

.pd9{
  padding: 9px 0;
}
.admin_heard{
   height: 397px;
   background: #FFFFFF;
   display: flex;
   margin: 0 0 15px 0;
   padding: 0;
}

.h580{
  height: 580px;
}
.admin_font{
  width: 28px;
  height: 20px;
  font-size: 14px;
  font-family: PingFangSC-Medium, PingFang SC;
  font-weight: 500;
  color: #212121;
  line-height: 20px;

}
.admin_font1{
  height: 22px;
  font-size: 16px;
  font-family: PingFangSC-Regular, PingFang SC;
  font-weight: 400;
  color: #212121;
  line-height: 22px;

}
.admin_m20{
  display: block;
  margin: 24px;
}
.w59h22{
  width: 59px;
  height: 22px;
}
.el-date-editor.el-input, .el-date-editor.el-input__inner{
  width: 118px;
  height: 22px;
}
.admin_lr20{
  margin: 0px 20px;
  width: 95%;
}

.admin1{
   margin: 20px auto;
}
.admin_ico{
  flex-grow: 1;
  height: 100px;
  position: relative;
}
.admin_p_32{
  position: absolute;
  left: 32px;
  top: 3px;
}
.admin_font6{
  height: 21px;
  font-size: 16px;
  font-family: DINPro-Medium, DINPro;
  font-weight: 500;
  color: #ED3335;
  line-height: 21px;
  text-align: center;

}
.admin_font7{
  height: 21px;
  font-size: 16px;
  font-family: DINPro-Medium, DINPro;
  font-weight: 500;
  color: #079DD6;
  line-height: 21px;
  text-align: center;

}
.admin_p_11{
  position: absolute;
  right: 11px;
  bottom: 11px;
}
.admin_p_59{
  position: absolute;
  left: 36px;
  top: 59px;
}
.admin_ico1{
  width: 154px;
  height: 100px;
  background: linear-gradient(90deg, #8990FF 0%, #793DF4 100%);
  box-shadow: 0px 2px 10px 0px #C3B6FF;
  border-radius: 4px;
}
.admin_ico2{
  width: 154px;
  height: 100px;
  background: linear-gradient(90deg, #6CBCF7 0%, #1F8FE7 100%);
  box-shadow: 0px 2px 10px 0px #B4DEFF;
  border-radius: 4px;

}

.admin_ico3{
  width: 154px;
  height: 100px;
  background: linear-gradient(90deg, #5BDDD3 0%, #0ABDA2 100%);
  box-shadow: 0px 2px 10px 0px #A9FFF5;
  border-radius: 4px;


}

.admin_ico4{
  width: 154px;
  height: 100px;
  background: linear-gradient(90deg, #38DFFF 0%, #1FACE7 100%);
  box-shadow: 0px 2px 10px 0px #B4DEFF;
  border-radius: 4px;


}
.admin_font2{
  width: 45px;
  height: 54px;
  font-size: 42px;
  font-family: DINPro-Medium, DINPro;
  font-weight: 500;
  color: #FFFFFF;
  line-height: 54px;

}
.admin_font3{
  width: 56px;
  height: 20px;
  font-size: 14px;
  font-family: PingFangSC-Medium, PingFang SC;
  font-weight: 500;
  color: #FFFFFF;
  line-height: 20px;


}
.admin_ico:not(:last-child){
  margin-right: 35px;
}
.admin_font8{
  width: 28px;
  height: 20px;
  font-size: 14px;
  font-family: PingFangSC-Regular, PingFang SC;
  font-weight: 400;
  color: #666666;
  line-height: 20px;

}
.admin_huang{
  box-shadow: 0px 2px 12px 0px #E4EDFF;
  border-radius: 6px;
}
.mt_15{
  margin-top: 35px;
}
.amdin_outtitle{
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
  .admin_font9{
    height: 20px;
    font-size: 14px;
    font-family: PingFangSC-Regular, PingFang SC;
    font-weight: 400;
    color: #212121;
    line-height: 20px;

  }
  .admin_font10{
    height: 20px;
    font-size: 14px;
    font-family: PingFangSC-Regular, PingFang SC;
    font-weight: 400;
    color: #666666;
    line-height: 20px;

  }
  .admin_font11{
    height: 20px;
    font-size: 14px;
    font-family: PingFangSC-Regular, PingFang SC;
    font-weight: 400;
    color: #212121;
    line-height: 20px;

  }
  .admin_font12{
    height: 20px;
    font-size: 14px;
    font-family: PingFangSC-Regular, PingFang SC;
    font-weight: 400;
    color: #666666;
    line-height: 20px;


  }
 .hoverchangeclor:hover{
   background: #e5e3e3;
   cursor:pointer;

 }
  .my_table1 > .el-table::before {
    /* 去除下边框 */
    height: 0;
  }


</style>


<style>
  @import 'vue-swipe/dist/vue-swipe.css';
  .mint-swipe-indicator.is-active{
    background-color:#1890FF;
  }
</style>
