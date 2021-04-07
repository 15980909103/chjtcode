<template>
  <div >
    <div class="tb-top" style="float:right;margin-bottom: 30px;">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
        <el-button type="info" icon="el-icon-circle-close" @click="openPage({url: '/estates/estatesnew'})">返回</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="opening_time" label="开盘时间" width="180" align="center"></el-table-column>
      <!-- <el-table-column prop="building" label="开盘楼栋" width="180" align="center"></el-table-column> -->

      <el-table-column  label="操作" align="center">
        <template slot-scope="scope">
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
            <el-form-item prop="opening_time" label="开盘时间" label-width="120px">
              <el-date-picker
                v-model="formData.opening_time"
                type="date"
                value-format="yyyy-MM-dd"
                placeholder="选择日期">
              </el-date-picker>
            </el-form-item>
          </el-row>

          <!-- <el-row>
            <el-col :span="24">
              <el-form-item prop="building" label="开盘楼栋" label-width="120px">
                <el-input type="textarea" width="2px" v-model="formData.building"></el-input>
              </el-form-item>
            </el-col>
          </el-row> -->

          <el-row>
            <el-button type="primary" @click="onAdd('discount')">添加</el-button>
          </el-row>
          <el-row  v-for="(item,i) in buildingItems"  :key="'building'+i">
            <el-col :span="24">
              <el-form-item label="楼栋" label-width="120px">
                <el-select clearable v-model="item.id" filterable placeholder="输入搜索">
                  <el-option v-for="itemB in buildingList" :key="itemB.id" :label="itemB.name" :value="String(itemB.id)"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item prop="floor" label="楼层" label-width="120px">
                <el-input type="text" width="2px" v-model="item.floor"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item label-width="120px">
                <el-button  @click="onDelete(i)">删除</el-button>
              </el-form-item>
            </el-col>
            <el-col :span="24">
                <el-divider><i class="el-icon-thumb"></i></el-divider>
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

  export default {
    components: {
        'pagination-box': paginationBox,
    },
    mixins: [baseMixin],
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
          opening_time:'',
          building: [],
          // buildingIds: [],
        },
        buildingItems:[],
        buildingList:[],
        pagination: {}, //分页数据
        rules: {
          opening_time: [
            { required: true, message: '请输入开盘时间', trigger: 'change' },
          ],
        },
      }
    },
    created: function(){
      let that = this
      if(this.$urlData) {
        if(this.$urlData.id) {
          that.estate_id = this.$urlData.id
        }
      }
      that.resetData({
        formData: this.formData,
      },function(){

      })
      that.getList(that.searchData)
      that.getBuildingList()
    },

    methods:{
      getList(searchdata={}){   //获取所有数据，或按条件查找数据
        var that = this
        if (that.estate_id) {
          searchdata['estate_id'] = that.estate_id;
          // searchdata['building_id'] = that.building_id;
          util.requests("post",{
            url:"estates/getEstatesnewTime",
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
            url: "estates/delEstateTime",
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
          this.formData = Object.assign({},e);
          this.buildingItems = this.formData.building
        }
        this.dialogVisibleEdit = true;
      },

      doEditCancel(formName){
        var that=this
        that.$refs[formName].resetFields()
        that.resetData()
        that.buildingItems = []

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
            that.formData.estate_id = that.estate_id;
            // that.formData.building_id = that.building_id;
            that.formData.building = that.buildingItems

            util.requests("post",{
              url:"estates/editEstatesnewTime",
              data:that.formData
            }).then(res=>{
              that.page_loading = false
              //console.log(res)
              if(res.code==1){
                that.$message({ type: 'success', message: '操作成功!' });
                that.dialogVisibleEdit = false;
                that.onSearch()
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

      // 添加元素
      onAdd(){
        this.buildingItems.push({id:"", floor:""});
      },
      // 删除元素
      onDelete(index){
        this.buildingItems.splice(index, 1);
      },
      // 获取该楼盘楼栋
      getBuildingList() {
        var that = this
        var estateId = that.estate_id
        util.requests("post",{
          url:"estates/getBuildingList",
          data: {estate_id:estateId}
        }).then(function(res){
            that.buildingList = res.data.list;
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


