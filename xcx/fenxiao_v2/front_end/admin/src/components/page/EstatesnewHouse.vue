<template>
  <div class="_container">
    <div class="tb-top" style="float:right;margin-bottom: 30px;">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item>
          <el-input v-model="searchData.name" placeholder="请输入户型名称" prefix-icon="el-icon-search"></el-input>
        </el-form-item>

        <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
        <el-button icon="el-icon-back" @click="openPage({url:-1,hreftype:'navigateBack'})">返回</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="name" label="户型名称" width="180" align="center"></el-table-column>

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
            <el-col :span="24">
              <el-form-item label="户型名称" prop="name" :label-width="formLabelWidth">
                <el-input style="width:100%" v-model="formData.name" placeholder="请输入内容"></el-input>
              </el-form-item>
            </el-col>
          </el-row>

          <el-form-item label="上传户型图" :label-width="formLabelWidth" ref="img" prop="cover">
            <img-upload2 ref="imgUpload" url="upload/imgUpload" :show-file-list="false" :fileList.sync="formData.cover_url" :imgIds.sync="formData.cover_id" :uploadedImg="onUploadedImg"></img-upload2>
            <!-- <img-upload ref="imgUpload" url="upload/imgUpload" :thumb='{isthumb:1}' :default_src.sync='default_src' :uploadedImg="onUploadedImg" ></img-upload> -->
          </el-form-item>

          <el-row>
            <el-col :span="24">
              <el-form-item prop="sale_status" label="销售状态" :label-width="formLabelWidth">
                <el-radio v-for="(item,index) in const_estates_new_sale_status" :key="index" v-model="formData.sale_status" :label="String(index)">{{item}}</el-radio>
                <!-- <el-radio v-model="formData.sale_status" label="1">待售</el-radio>
                <el-radio v-model="formData.sale_status" label="2">在售</el-radio>
                <el-radio v-model="formData.sale_status" label="3">售罄</el-radio>
                <el-radio v-model="formData.sale_status" label="4">尾盘</el-radio> -->
              </el-form-item>
            </el-col>
          </el-row>

          <el-row>
            <el-col :span="24">
              <el-form-item label="参考价格-均价" prop="price" :label-width="formLabelWidth">
                <el-input-number :precision="2" :step="0.1"  v-model="formData.price"></el-input-number>元/㎡
              </el-form-item>
            </el-col>
          </el-row>

          <el-row>
            <el-col :span="24">
              <el-form-item label="参考价格-总价" prop="price_total" :label-width="formLabelWidth">
                <el-input-number :precision="2" :step="0.1"  v-model="formData.price_total"></el-input-number>万元/套
              </el-form-item>
            </el-col>
          </el-row>

          <el-row>
            <el-col :span="24">
              <el-form-item label="参考价格" prop="price_str" :label-width="formLabelWidth">
                <el-input style="width:100%" v-model="formData.price_str" placeholder="请输入价格描述"></el-input>
              </el-form-item>
            </el-col>
          </el-row>

          <el-row >
            <el-col :span="24">
              <el-form-item prop="built_area" label="建面" :label-width="formLabelWidth">
                <el-input-number :precision="2" :step="0.1"  v-model="formData.built_area"></el-input-number>㎡
              </el-form-item>
            </el-col>
          </el-row>

          <el-row>
            <el-col :span="24">
              <el-form-item prop="rooms" label="几居室" :label-width="formLabelWidth">
                <el-radio v-for="(item,index) in const_rooms" :key="index" v-model="formData.rooms" :label="String(index)">{{item}}</el-radio>
                <!-- <el-radio v-model="formData.rooms" label="1">一室</el-radio>
                <el-radio v-model="formData.rooms" label="2">两室</el-radio>
                <el-radio v-model="formData.rooms" label="3">三室</el-radio>
                <el-radio v-model="formData.rooms" label="4">四室</el-radio>
                <el-radio v-model="formData.rooms" label="5">五室</el-radio>
                <el-radio v-model="formData.rooms" label="6">五室+</el-radio> -->
              </el-form-item>
            </el-col>
          </el-row>

          <el-row>
            <el-col :span="24">
              <el-form-item prop="rooms_str" label="居室描述" :label-width="formLabelWidth">
                <el-input style="width:100%" v-model="formData.rooms_str" placeholder="请输入居室描述"></el-input>
              </el-form-item>
            </el-col>
          </el-row>

          <el-row>
            <el-col :span="24">
              <el-form-item prop="orientation" label="朝向" :label-width="formLabelWidth">
                <el-radio v-for="(item,index) in const_orientation" :key="index" v-model="formData.orientation" :label="String(index)">{{item}}</el-radio>
                <!-- <el-radio v-model="formData.orientation" label="1">东</el-radio>
                <el-radio v-model="formData.orientation" label="2">南</el-radio>
                <el-radio v-model="formData.orientation" label="3">西</el-radio>
                <el-radio v-model="formData.orientation" label="4">北</el-radio>
                <el-radio v-model="formData.orientation" label="5">南北</el-radio>
                <el-radio v-model="formData.orientation" label="6">东西</el-radio>
                <el-radio v-model="formData.orientation" label="7">东南</el-radio>
                <el-radio v-model="formData.orientation" label="8">东北</el-radio>
                <el-radio v-model="formData.orientation" label="9">西南</el-radio>
                <el-radio v-model="formData.orientation" label="10">西北</el-radio> -->
              </el-form-item>
            </el-col>
          </el-row>

          <el-row>
            <el-col :span="24">
              <el-form-item prop="house_purpose" label="建筑用途" :label-width="formLabelWidth">
                <el-checkbox-group :max="1" v-model="formData.house_purpose">
                  <el-checkbox  v-for="(item,index) in const_house_purpose" :key='index' :label="String(index)">{{item}}</el-checkbox>
                </el-checkbox-group>
                <!-- <el-radio v-for="(item,index) in const_house_purpose" :key="index" v-model="formData.house_purpose" :label="String(index)">{{item}}</el-radio> -->
              </el-form-item>
            </el-col>
          </el-row>

          <el-row>
            <el-col :span="24">
              <el-form-item label="所属楼栋" prop="building_id" :label-width="formLabelWidth">
                <el-select clearable v-model="formData.building_id">
                  <el-option v-for="itemBuild in building" :key="itemBuild.id" :label="itemBuild.name" :value="itemBuild.id"></el-option>
                </el-select>
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
  import ImgUpload2 from '@/components/common/ImgUpload2.vue';

  export default {
    components: {
        'pagination-box': paginationBox,
        'img-upload': ImgUpload,
        'img-upload2': ImgUpload2,
    },
    mixins: [baseMixin,constMixin],
    data() {
      return {
        dialogVisibleEdit: false,
        formLabelWidth: "123px",
        page_loading:'',
        searchData:{
          
        },
        tableData: [],
        formData:{
          name:'',
          img: '',
          cover_url:[],
          cover_id:[],
          price: 0.00,
          price_total: 0.00,
          price_str: '',
          built_area: 0.00,
          rooms: '1',
          rooms_str:'',
          sale_status: '1',
          orientation: '1',
          house_purpose: [],
          building_id:''
        },
        default_src:'',
        pagination: {}, //分页数据
        rules: {
          name: [
            { required: true, message: '请输入楼栋名称', trigger: 'change' },
          ],
        },
        estate_id:0,
        building:[],
      }
    },
    created: function(){
      let that = this
      that.resetData({
        formData: that.formData,
        default_src: ''
      },function(){
        that.$nextTick(()=>{
          that.$refs.imgUpload&&that.$refs.imgUpload.resetData() //重置图片信息
        })
      })

      if(this.$urlData && this.$urlData.id) {
        this.estate_id = this.$urlData.id;
      }

      that.getList(that.searchData)

      this.getHousePurpose();// 初始化建筑用途列
      this.getOrientation();// 初始化朝向列
      this.getRooms();// 初始化几居室列
      this.getEstatesNewSaleStatus(); // 初始化新房销售状态
      this.getBuildingList();// 楼栋列表
    },
 
    methods:{
      
      getList(searchdata={}){   //获取所有数据，或按条件查找数据
        var that = this
        // if(this.$urlData && this.$urlData.building_id) {
        //   searchdata['building_id'] = this.$urlData.building_id;
        // }
        // if(this.$urlData && this.$urlData.estate_id) {
        //   searchdata['estate_id'] = this.$urlData.estate_id;
        // }
        searchdata['estate_id'] = this.estate_id
        util.requests("post",{
          url:"estates/getEstatesnewHouseList",
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
      },

      getRealImgUrl(url){
        return this.$getRealImgUrl(url)
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
            url: "estates/delEstateHouse",
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
          this.formData.sale_status = String(this.formData.sale_status)
          this.formData.orientation = String(this.formData.orientation)
          this.formData.rooms = String(this.formData.rooms)
          this.default_src = this.$getRealImgUrl(this.formData.img)
        }
        this.dialogVisibleEdit = true;
        console.log(this.formData)
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
            that.formData.estate_id = that.estate_id
            // if(this.$urlData && this.$urlData.estate_id) {
            //     that.formData.estate_id = this.$urlData.estate_id
            // }
            // // 楼栋ID
            // that.formData.building_id = 0
            // if(this.$urlData && this.$urlData.building_id) {
            //     that.formData.building_id = this.$urlData.building_id
            // }

            util.requests("post",{
              url:"estates/editEstatesnewHouse",
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

      // 获取楼栋信息
      getBuildingList() {
        var id = 0;
        var that = this;
        
        var estate_id = that.estate_id
        util.requests("post",{
          url:"estates/getBuildingList",
          data: {estate_id: estate_id}
        }).then(res=>{
          that.building = res.data.list
        })
      },

      //图片上传后操作
      onUploadedImg(e){
        // this.formData.img = e.res.info.url;
        // this.$refs.cover.clearValidate()
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


