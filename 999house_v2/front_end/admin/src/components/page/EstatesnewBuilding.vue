<template>
  <div >
    <div class="tb-top" style="float:right;">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item>
          <el-input v-model="searchData.name" placeholder="请输入楼栋名称" prefix-icon="el-icon-search"></el-input>
        </el-form-item>

        <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
        <el-button type="info" icon="el-icon-circle-close" @click="openPage({url: '/estates/estatesnew'})">返回</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="name" label="楼栋名称" width="180" align="center"></el-table-column>

      <el-table-column  label="操作" align="center">
        <template slot-scope="scope">
          <!-- <el-button type="primary" size="mini" @click="openPage({url: '/estates/estatesnew_house', data:{building_id:scope.row.id,estate_id:estate_id}})">户型</el-button> -->
          <!-- <el-button type="info" size="mini" @click="openPage({url: '/estates/estatesnew_opentime', data:{building_id:scope.row.id,estate_id:estate_id}})">开盘时间</el-button> -->
          <el-button type="success" size="mini" @click="doEdit(scope.row)">编辑</el-button>
          <el-button type="danger" size="mini" @click="del(scope.row.id,scope.$index)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <!-- ============分页=============== -->
    <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>


    <!-- 新增弹窗部分 -->
    <el-dialog
        :title="formData.id?'编辑':'新增'"
        :visible.sync="dialogVisibleEdit"
        width="800px"
        :close-on-click-modal="false"
        @close="doEditCancel('formData')"
      >
        <el-form style="padding-right:50px;" :model="formData" ref="formData" :rules="rules">
          <el-row>
            <el-col :span="12">
              <el-form-item label="楼栋名称" prop="name" :label-width="formLabelWidth">
                <el-input style="width:100%" v-model="formData.name" placeholder="请输入内容"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="楼型" prop="building_type" :label-width="formLabelWidth">
                <el-input style="width:100%" v-model="formData.building_type" placeholder="请输入内容"></el-input>
              </el-form-item>
            </el-col>
          </el-row>

          <el-row>
            <el-col :span="24">
              <el-form-item prop="sale_status" label="销售状态" :label-width="formLabelWidth">
                <el-radio v-for="(item,index) in const_estates_new_sale_status" :key="index" v-model="formData.sale_status" :label="String(index)">{{item}}</el-radio>
              </el-form-item>
            </el-col>
          </el-row>


          <el-row>
            <el-col :span="24">
              <el-form-item label="层级" prop="floor_number" :label-width="formLabelWidth">
                <el-input-number label="层级" v-model="formData.floor_number"></el-input-number> 层
              </el-form-item>
            </el-col>
          </el-row>

          <el-row >
            <el-col :span="24">
              <el-form-item prop="unit" label="每层几单元" :label-width="formLabelWidth">
                <el-input-number label="每层几单元" v-model="formData.unit"></el-input-number> 单元
              </el-form-item>
            </el-col>
          </el-row>

          <el-row >
            <el-col :span="24">
              <el-form-item prop="house_number" label="户数" :label-width="formLabelWidth">
                <el-input-number label="户数" v-model="formData.house_number"></el-input-number> 户
              </el-form-item>
            </el-col>
          </el-row>

          <el-row >
            <el-col :span="24">
              <el-form-item prop="delivery_time" label="交房时间" label-width="110px">
                <el-date-picker
                  v-model="formData.delivery_time"
                  type="date"
                  value-format="yyyy-MM-dd"
                  placeholder="选择日期">
                </el-date-picker>
              </el-form-item>
            </el-col>
          </el-row>

          <el-row >
            <el-col :span="24">
              <el-form-item  label="预售许可证" :label-width="formLabelWidth">
                <span v-if="sales_license&&sales_license.length">
                  <el-checkbox-group v-model="formData.sales_license">
                    <el-checkbox v-for="(item,index) in sales_license" :key="index" :label="String(item.license)">{{item.license}}</el-checkbox>
                  </el-checkbox-group>
                </span>
                <span v-else>暂未设置，请先设置预售许可证</span>
              </el-form-item>
            </el-col>
          </el-row>

        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button type="primary" @click="doSubmit('formData')">确 定</el-button>
          <el-button type="danger" @click="doEditCancel('formData')">取 消</el-button>
        </div>
      </el-dialog>

  </div>
</template>
<script>
  // import { log } from 'util';
  var util = require("@/utils/util.js");
  import paginationBox from '@/components/common/pagination.vue';
  import ImgUpload from '@/components/common/ImgUpload.vue';
  import baseMixin from  '@/mixin/baseMixin';
   import constMixin from  '@/mixin/constMixin';

  export default {
    components: {
        'pagination-box': paginationBox,
    },
    mixins: [baseMixin,constMixin],
    data() {
      return {
        dialogVisibleEdit: false,
        formLabelWidth: "123px",
        page_loading:'',
        estate_id:0,
        searchData:{

        },
        tableData: [],
        formData:{
          id: '',
          name:'',
          sale_status: '1',
          building_type: '',
          floor_number: '',
          unit: '',
          house_number: '',
          sales_license: [],
          delivery_time: '',
        },
        sales_license: [],
        pagination: {}, //分页数据
        rules: {
          name: [
            { required: true, message: '请输入楼栋名称', trigger: 'change' },
          ],
        },
      }
    },
    watch:{
      'formData.id':function(val) {
        this.formData.sales_license = []
        if(val){
          val = String(val)
          let arr=[]
          for(var i in this.sales_license){
            var item = this.sales_license[i]
            if(item.building&&item.building.includes(val)){
              arr.push(item.license);
            }
          }
          this.formData.sales_license = arr
        }
      }
    },
    created: function(){
      let that = this
      if (this.$urlData && this.$urlData.id) {
        that.estate_id = this.$urlData.id
      }
      that.resetData({
        formData: this.formData,
      },function(){

      })

      that.getSalesLicense();
      that.getList(that.searchData)
      this.getEstatesNewSaleStatus(); // 初始化新房销售状态
    },

    methods:{
      getList(searchdata={}){   //获取所有数据，或按条件查找数据
        if (this.$urlData && this.$urlData.id) {
          var that = this
          searchdata['estate_id'] = this.$urlData.id;
          util.requests("post",{
            url:"estates/getEstatesnewBuildingList",
            data: searchdata
          }).then(res=>{
            //console.log(res.data.list)
            that.tableData = res.data.list
            that.setDataArr({
              pagination : {
                page : res.data.current_page,
                pagecount : res.data.last_page,
                pagesize : Math.ceil(res.data.total / res.data.last_page)
              }
            })
          })
        } else {
          return;
        }
      },

      onSearch(){
        this.getList(this.searchData);
      },

      del(id,val){   //确定删除
        this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          util.requests("post",{
            url: "estates/delEstateBuilding",
            data: {id:id}
          }).then(res => {
            // console.log(res); return;
            if(res.data.code==0){ alert("删除失败："+res.data.msg);return; }
            this.tableData.splice(val,1)
            this.$message({
              type: 'success',
              message: '删除成功!'
            });
          })
        })
      },

      //分页操作
      pageChange: function(page) {
        let post_data = Object.assign({},this.searchData);
        post_data.page = page;
        this.getList(post_data)
      },

      doEdit(e={}){
        if(Object.keys(e).length>0){
          e.sales_license = e.sales_license?e.sales_license:[]
          this.formData = Object.assign({},e);
          this.formData.sale_status = String(this.formData.sale_status);
        }

        this.dialogVisibleEdit = true;
      },

      doEditCancel(formName){
        var that=this
        that.$refs[formName].resetFields()
        that.resetData()

        if(that.dialogVisibleEdit == true){
          that.dialogVisibleEdit = false
        }
      },

      doSubmit(formName){
        var that = this
        that.$refs[formName].validate((valid) => {
          if (valid) {
            if(this.page_loading){
              return;
            }
            that.page_loading = true;

            // 楼盘ID
            if(this.$urlData && this.$urlData.id) {
                that.formData.estate_id = this.$urlData.id
            } else {
                that.formData.estate_id = 0
            }

            util.requests("post",{
              url:"estates/editEstatesnewBuilding",
              data:that.formData
            }).then(res=>{
              that.page_loading = false
              //console.log(res)
              if(res.code==1){
                that.$message({ type: 'success', message: '操作成功!' });
                that.dialogVisibleEdit = false;
                that.onSearch()
                that.getSalesLicense();
              }else{
                that.$message({
                  type: 'error',
                  message: res.msg
                });
              }
            });
          }else{
            console.log('error submit!!');
            return false;
          }
        })
      },

      // 获取预售许可证列表
      getSalesLicense() {
        var id = 0;
        var that = this;
        if(that.formData.id) {
          id = that.formData.id;
        }
        util.requests("post",{
          url:"estates/getSalesLicense",
          data: {estate_id: that.estate_id, building_id: id}
        }).then(res=>{
          that.sales_license = res.data.sales_license
        })
      },
      
      openPage: util.openPage
    }

  };
</script>
<style lang="scss" scoped>
  .tb-title{
    margin-top: 40px;
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


