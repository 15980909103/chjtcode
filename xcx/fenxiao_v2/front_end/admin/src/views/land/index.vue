<template>
  <div class="_container">
    <div style="margin-bottom: 19px;background-color: #FFFFFFFF">
      <el-form :inline="true" :model="searchData" class="form-serch"  style="margin: 0px 15px">
            <el-row>
              <el-col :lg="17">
                <el-form-item label="地块">
                </el-form-item>
              </el-col>
                    <el-form-item label="区域选择" >
                      <el-select style="width:250px"
                        v-model="searchData.city_lsit"
                                 multiple
                        filterable
                        allow-create
                        default-first-option
                        placeholder="区域">
                        <el-option
                          v-for="item in city_lsit"
                          :key="item.id"
                          :label="item.cname"
                          :value="item.id">
                        </el-option>
                      </el-select>
                    </el-form-item>
              </el-row>

              <el-divider class="admin_huang"></el-divider>

              <el-row>
                   <div style="width: 600px;margin:15px auto">
                     <el-input style="width: 500px" v-model="searchData.name"></el-input>
                     <el-button type="success" @click="search()" >搜索</el-button>
                   </div>
              </el-row>


            <el-row style="margin-top: 15px">
              <el-col :lg="3">
                <el-form-item label="土地用途">
                    <el-select style="width:120px"
                               v-model="searchData.type"
                               filterable
                               allow-create
                               default-first-option
                               placeholder="用途">
                      <el-option :value="-1" label="全部">   </el-option>

                      <el-option
                        v-for="item in type_list"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value">
                      </el-option>
                    </el-select>
                  </el-form-item>
              </el-col>

              <el-col :lg="3">
                <el-form-item label="交易状态">
                  <el-select style="width:101px"
                             v-model="searchData.status"
                             filterable
                             allow-create
                             default-first-option
                             placeholder="状态">
                    <el-option :value="-1" label="全部">   </el-option>
                    <el-option
                      v-for="item in status_lsit"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value">
                    </el-option>
                  </el-select>
                </el-form-item>
              </el-col>

              <el-col :lg="6">
                <el-form-item label="成交楼面价">
                  <el-select
                             v-model="searchData.transaction_price"
                             filterable
                             allow-create
                             default-first-option
                             placeholder="楼面价">
                    <el-option :value="-1" label="全部">   </el-option>
                    <el-option
                      v-for="item in money_list"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value">
                    </el-option>
                  </el-select>
                </el-form-item>
              </el-col>

              <el-col :lg="6">
                <el-form-item label="竞拍时间">
                  <el-date-picker
                    style="width:250px"
                    v-model="searchData.auction_time"
                    value-format="yyyy-MM-dd" format="yyyy-MM-dd"
                    type="datetimerange"
                    align="center"
                    range-separator="-"
                    start-placeholder="开始日期"
                    end-placeholder="结束日期">
                  </el-date-picker>
                </el-form-item>
              </el-col>


              <el-col :lg="6">
                <el-form-item label="成交时间">
                  <el-date-picker
                    style="width:250px"
                    v-model="searchData.transaction_time"
                    value-format="yyyy-MM-dd" format="yyyy-MM-dd"
                    type="datetimerange"
                    range-separator="-"
                    align="center"
                    start-placeholder="开始日期"
                    end-placeholder="结束日期">
                  </el-date-picker>
                </el-form-item>
              </el-col>
            </el-row>
      </el-form>
    </div>
    <div style="background-color: #FFFFFFFF">
      <div style="padding-top: 35px;margin-left: 40px;vertical-align: center">
        <el-row >

          <el-col :lg="1" style="margin-right: 20px;line-height: 38px">
            <el-button  size="mini" @click="setLandStatus(1)"> 显示 </el-button>
          </el-col>
          <el-col  :lg="19" style="margin-right: 20px;line-height: 38px">
            <el-button  size="mini" @click="setLandStatus(0)" > 隐藏 </el-button>
          </el-col>
          <el-col :lg="1">
           <el-button icon="el-icon-plus" @click="openPage({url:'/land/edit'})"> 新增地块 </el-button>
          </el-col>
        </el-row>
      </div>

      <el-table :data="tableData" style="width:100%" v-loading="page_loading"  @selection-change="handleSelectionChange">>
        <el-table-column type="selection"></el-table-column>
        <el-table-column prop="id" label="ID" width="100" align="center"></el-table-column>
        <el-table-column prop="index_img" label="封面图" width="150" align="center">
          <template slot-scope="scope">
            <img style="width:110px; height:110px;" :src="getRealImgUrl(scope.row.index_img)"/>
          </template>
        </el-table-column>
        <el-table-column prop="title" label="地块名称" align="center"></el-table-column>
        <el-table-column prop="city_no"  label="地区" align="center">
          <template slot-scope="scope">
              {{getCityName(scope.row.city_no)}}
          </template>
        </el-table-column>

        <el-table-column prop="area" label="面积" align="center">
          <template slot-scope="scope">
            {{scope.row.area +'㎡'}}
          </template>
        </el-table-column>

        <el-table-column prop="status" label="交易状态" align="center">
          <template slot-scope="scope">
            {{scope.row.status ==1 ? '公告' :'已成交'}}
          </template>
        </el-table-column>

        <el-table-column label="竞得方" prop="recipients" align="center">
        </el-table-column>

        <el-table-column prop="starting_price" label="起拍楼面价" align="center">
          <template slot-scope="scope">
            {{scope.row.starting_price +'元/㎡'}}
          </template>
        </el-table-column>
        <el-table-column prop="transaction_price" label="成交楼面价" align="center">
          <template slot-scope="scope">
            {{scope.row.transaction_price +'元/㎡'}}
          </template>
        </el-table-column>

        <el-table-column label="成交时间" align="center">
          <template slot-scope="scope">
            <span>{{getFormatDate(scope.row.transaction_time,2)}}</span>
          </template>
        </el-table-column>
        <el-table-column label="起拍时间" align="center">
          <template slot-scope="scope">
            <span>{{getFormatDate(scope.row.auction_time,2)}}</span>
          </template>
        </el-table-column>
        <el-table-column label="操作"  align="center" width="200">
          <template slot-scope="scope">
            <el-button type="primary" size="mini" @click="openPage({url:'/land/edit',data:{id:scope.row.id}})">编辑</el-button>
<!--            <el-button type="danger" size="mini" @click="doDel(scope.row.id)">删除</el-button>-->
          </template>
        </el-table-column>
      </el-table>
    </div>


    <!-- ============分页=============== -->
    <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>

  </div>
</template>

<script>
var util = require("@/utils/util");
import paginationBox from '../../components/common/pagination.vue';
import ImgUpload from '@/components/common/ImgUpload.vue';
export default {
  components: {
			'pagination-box': paginationBox,
    'img-upload': ImgUpload,
	},
  data() {
    return {
      siteCitys:{},
      dialogFormVisible: false,
      formLabelWidth: "123px",
      form_type:'',
      form: {
        account: "",
        mobile: "",
        email: "",
        head_ico_id:'',
        head_ico_path:'',
        newpassword: "",
        newpassword2: "",
        role_id:'',
        status:'',
        region_nos_info:{},
        region_nos_info2:[],

      },
      rules: { },

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
      money_list:[
        {
          value: '0-5000',
          label: '0-5000'
        },{
          value: '5000-10000',
          label: '5000-10000'
        },{
          value: '10000-30000',
          label: '10000-30000'
        },{
          value: '30000-60000',
          label: '30000-60000'
        },{
          value: '60000',
          label: '60000以上'
        }
      ],
      status_lsit:[
        {
          value: '1',
          label: '公告'
        },{
          value: '2',
          label: '已成交'
        }
      ],
      type_list:[
        {
          value: '1',
          label: '住宅用地'
        },{
          value: '2',
          label: '商服用地'
        }
      ],
      city_lsit:[],
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据
      select_ids :[],
      roleList:[{id:-1,name:'超级管理员'}],
    };
  },
  created: function() {
    this.city_lsit = this.$store.getters.userinfo.region_nos_info;
    this.getList(this.searchData)
    // this.getRoleList()
    this.getSiteCitys()
  },
  methods: {
    getSiteCitys(){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"city/siteCitys",
        data:{status:1}
      }).then(res=>{
        let obj = {}
        for(var i in res.data){
          let item = res.data[i]
          obj[item.id] = item
        }
        that.siteCitys = obj
      })
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
  padding: 20px;
  background: #F9FBFF;
}
.el-radio{
  margin-right: 5px;
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
  width: 28px;
  height: 20px;
  font-size: 14px;
  font-family: PingFangSC-Regular, PingFang SC;
  font-weight: 400;
  color: #212121;
  line-height: 20px;

}
.admin_huang{
   margin-top: 0px;
   margin-bottom: 14px;
   border: 0;
}
.mt_15{
  margin-top: 35px;
}
</style>


