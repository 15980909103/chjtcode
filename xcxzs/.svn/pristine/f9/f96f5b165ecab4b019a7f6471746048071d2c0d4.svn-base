<template>
  <el-form style="padding-right:50px;" :model="formData" ref="formData" :rules="rules">
    <div class="mapeditor-title">建筑信息</div>

    <el-row >
      <el-col :lg="8" :sm="12" :xs="24">
        <el-form-item prop="planning_number" label="规划户数" label-width="110px">
          <el-input-number label="规划户数" v-model="formData.planning_number"></el-input-number> 户
        </el-form-item>
      </el-col>
      <el-col :lg="8" :sm="12" :xs="24">
        <el-form-item prop="total_area" label="占地总面积" label-width="110px">
          <el-input-number :precision="2" :step="0.1"  v-model="formData.total_area"></el-input-number> ㎡
        </el-form-item>
      </el-col>
      <el-col :lg="8" :sm="12" :xs="24">
        <el-form-item prop="total_construction_area" label="建筑总面积" label-width="110px">
          <el-input-number :precision="2" :step="0.1"  v-model="formData.total_construction_area"></el-input-number> ㎡
        </el-form-item>
      </el-col>
    </el-row>

    <el-row >
      <el-col :lg="11" :sm="12" :xs="24">
        <el-form-item prop="sizelayout" label="大小户型" label-width="110px">
          <el-input placeholder="请输入户型描述(如：86-120m高层复式)" v-model="formData.sizelayout"></el-input>
        </el-form-item>
      </el-col>
      <el-col :lg="11" :sm="12" :xs="24">
        <el-form-item prop="building_type" label="建筑类型" label-width="110px">
          <el-input placeholder="请输入建筑类型" v-model="formData.building_type"></el-input>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row >
      <el-col :lg="11" :sm="12" :xs="24">
        <el-form-item prop="floor_height" label="层高" label-width="110px">
          <el-input placeholder="请输入层高描述(如：叠拼约5.8-6m、高层约2.9m)" v-model="formData.floor_height"></el-input>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row >
      <el-col :lg="11" :sm="12" :xs="24">
        <el-form-item prop="floor_condition" label="楼层状况" label-width="110px">
          <el-input placeholder="请输入楼层状况描述(如：高层19-34F/叠拼6-8F)" v-model="formData.floor_condition"></el-input>
        </el-form-item>
      </el-col>

      <el-col :lg="10" :sm="12" :xs="24">
        <el-form-item prop="decoration" label="装修情况" label-width="110px">
          <el-input placeholder="请输入装修情况" v-model="formData.decoration"></el-input>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row >
      <el-col :lg="11" :sm="12" :xs="24">
        <el-form-item prop="progress_project" label="项目进度" label-width="110px">
          <el-input placeholder="请输入项目进度" v-model="formData.progress_project"></el-input>
        </el-form-item>
      </el-col>
      <el-col :lg="11" :sm="12" :xs="24">
        <el-form-item prop="public_bear" label="公摊" label-width="110px">
          <el-input placeholder="请输入公摊" v-model="formData.public_bear"></el-input>
        </el-form-item>
      </el-col>
    </el-row>

    <el-form-item style="text-align:center">
      <el-button type="primary" icon="el-icon-success" @click="doSubmit('formData', 'release')">保存</el-button>
      <el-button
        icon="el-icon-refresh"
        v-if="!formData.id"
        @click="resetForm('formData')"
      >重置</el-button>
      <el-button
        type="danger"
        icon="el-icon-circle-close"
        @click="openPage({url: '/estates/estatesnew'})"
      >返回</el-button>
    </el-form-item>

  </el-form>

</template>
<script>
  var util = require("@/utils/util.js");
  import ImgUpload from '@/components/common/ImgUpload.vue';
  import baseMixin from  '@/mixin/baseMixin';
  import constMixin from  '@/mixin/constMixin';
  import MycitySelect from '@/components/common/MycitySelect.vue';
  import ImgUpload2 from '@/components/common/ImgUpload2.vue';
  import Tinymce from '@/components/Tinymce';


  export default {
    components: {
      'img-upload': ImgUpload,
      'mycity-select': MycitySelect,
      'img-upload2': ImgUpload2,
      'Tinymce' : Tinymce
    },
    mixins: [baseMixin,constMixin],
    data() {
      return {
        formData:{
          name:'',
          title:'',
          price:0.00,
          price_total:0.00,
          price_str:'',
          built_area:'',
          house_type:'',
          sale_status:'1',
          activity_type:'1',
          house_purpose: [],
          bind_building: '',
          sizelayout: '',
          planning_number: 0,
          total_area: 0.00,
          total_construction_area: 0.00,
          building_type: '',
          floor_condition: '',
          developers: '',
          floor_height: '',
          share_desc:'',
          decoration: '',
          progress_project: '',
          public_bear: '',
          sales_telephone: '',
          property_company: '',
          property_type: '',
          property_charges: '',
          volume_rate: '',
          greening_rate: '',
          parking_space_number: '',
          parking_space_proportion: '',
          status: '1',
          logo : [],
          logo_id : [],
          detail_cover: [],
          detail_cover_id: [],
          list_cover: [],
          list_cover_id: [],
          coordinate:'',
          feature_tag:[],
          selling_point_tag:[],
          base_tag:[],
          province:'',
          city:'',
          area:'',
          province_str:'',
          city_str:'',
          area_str:'',
          street:'',
          street_str:'',
          business_area:'',
          business_area_str:'',
          subways:[],
          subway_sites:"",
          delivery_time: '',
          address:'',
          num_collect:0,
          num_read:0,
          num_read_search:0,
          num_share:0,
          sort:0,
          recommend: '0',
          num_collect_real:0,
          num_read_real:0,
          num_read_search_real:0,
          num_share_real:0,
          is_cheap:'0',
          activity_img:'',
          sand_img_url:[],
          sand_img_ids:[],
          activity_img_ids:[]
        },
        rules:{},
        formLabelWidth: "110px",
      };
    },


    created: function(){
      this.resetData({
        formData: this.formData,
      });

      var isMiss = this.doMissId(this.$urlData.id);
      if (!isMiss) {
        return;
      }
      //修改操作
      this.formData.id = this.$urlData.id;
      this.getInfo(this.formData.id);
    },

    methods: {

      // 获取详情
      getInfo(id) {
        var that = this;
        if (that.page_loading) {
          return;
        }
        that.page_loading = true;

        util.requests("get", {
            url: "Estates/getEstatesnewInfo",
            data: { id: id },
          })
          .then((res) => {
            that.page_loading = false;
            if (res.code == 1) {
              var formData = res.data;
             
              that.setDataArr({
                formData: formData,
              });
            } else {
              util.Message.error(res.msg);
            }
          });
      },

      // 表单提交
      doSubmit(formName, type){
        var that = this
        that.$refs[formName].validate((valid) => {
          if (valid) {
            if(this.page_loading){
              return;
            }
            that.page_loading = true;
   
            // 发布状态
            switch(type) {
              case 'release':
                if(that.formData.status == 2) {
                  that.formData.status = 1;
                }
                break;
              case 'draft':
              default:
                that.formData.status = 2;
                break;
            }

            util.requests("post",{
              url:"estates/editEstate",
              data:that.formData
            }).then(res=>{
              that.page_loading = false
              //console.log(res)
              if(res.code==1){
                that.$message({ type: 'success', message: '操作成功!' });
                that.dialogVisibleEdit = false;
                // 返回页面
                setTimeout(function () {
                  util.openPage({url:-1})
                },600)
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

      //数据重置
      resetForm(formName) {
        var that = this;
        if (that.$refs[formName] && that.$refs[formName].resetFields) {
          that.$refs[formName].resetFields();
        }

        that.resetData();
      },

      // id判断
      doMissId(id) {
        if (!id) {
          var that = this;
          this.$alert("缺少必要的id参数，请重新打开", "提示", {
            confirmButtonText: "确定",
            callback: (action) => {
              that.openPage({ hreftype: "navigateBack", url: -1 });
            },
          });
          return false;
        }
        return true;
      },

      // 跳转页面
      openPage: util.openPage,
    }
  };
</script>
<style lang="scss" scoped>
._container {
  .amap-wrapper {
    width: 500px;
    height: 500px;
  }
  margin-top: 20px;
  padding: 20px;
  background: #fff;
  min-height: calc(100vh - 90px);
  .mapeditor-title {
    line-height: 40px;
    padding-left: 10px;
    background-color: rgba($color: #f0f2f5, $alpha: 0.5);
    border-left: 4px solid;
    border-color: #409eff;
    font-size: 14px;
    margin-bottom: 10px;
  }
  .mapeditor-content {
    padding-left: 16px;
    .el-col {
      margin: 10px 0;
    }
  }
  #map {
    width:400px;
    height:300px;
    margin: 20px 0;
  }
}
</style>
