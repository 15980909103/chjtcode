<template>
  <el-form style="padding-right:50px;" :model="formData" ref="formData" :rules="rules">
    <div class="mapeditor-title">
      优惠信息
      <el-button style="margin-left:50px;" type="primary" @click="onAdd('discount')">添加</el-button>
    </div>

    <el-row v-for="(itemDis,idx) in discountItems"  :key="'discount'+idx">
    <!-- <el-row>
      <el-col :span="6">
         <el-form-item label="小按钮" :label-width="formLabelWidth" ref="list_cover" prop="list_cover"> 
          <img-upload2 ref="imgUpload" url="upload/imgUpload" :show-file-list="false" :thumb='{ isthumb:1, width:450, height:450 }' :fileList.sync="itemDis.btn" :imgIds.sync="itemDis.list_cover_id" :uploadedImg="onUploadedImg"></img-upload2>
        </el-form-item>
      </el-col>
    </el-row> -->
      <el-col :lg="10" :md="18" :xs="24">
        <el-form-item prop="title" label="标题" label-width="120px">
          <el-input type="text" width="2px" v-model="itemDis.title"></el-input>
        </el-form-item>
      </el-col>
      <el-col :span="6">
        <el-form-item label="开始时间"  label-width="120px">
          <el-date-picker style="width:100%" v-model="itemDis.start_time" type="date" value-format="yyyy-MM-dd"
                format="yyyy-MM-dd" placeholder="选择日期"></el-date-picker>
        </el-form-item>
      </el-col>
      <el-col :span="6">
        <el-form-item label="结束时间" label-width="120px">
          <el-date-picker style="width:100%" v-model="itemDis.end_time" type="date" value-format="yyyy-MM-dd"
                format="yyyy-MM-dd" placeholder="选择日期"></el-date-picker>
        </el-form-item>
      </el-col>
      <!--小按钮-->
     
      <el-col :lg="16" :md="18" :xs="24">
        <el-form-item prop="content" label="详情" label-width="120px">
          <el-input type="textarea" width="2px" v-model="itemDis.content"></el-input>
        </el-form-item>
      </el-col>
      <el-col :lg="16" :md="18" :xs="24">
        <el-form-item prop="rule" label="规则" label-width="120px">
          <el-input type="textarea" width="2px" v-model="itemDis.rule"></el-input>
        </el-form-item>
      </el-col>

      <el-col :lg="8" :md="18" :xs="24">
        <el-form-item label-width="50px">
          <el-button  @click="onDelete(idx, 'discount')">删除</el-button>
        </el-form-item>
      </el-col>
      <el-col :lg="24" :md="24" :xs="24">
          <el-divider><i class="el-icon-thumb"></i></el-divider>
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
  import ImgUpload2 from '@/components/common/ImgUpload2.vue';
  import Tinymce from '@/components/Tinymce';


  export default {
    components: {
      'img-upload': ImgUpload,
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
      
        discountItems:[],
      };
    },
    

    created: function(){
      this.resetData({
        formData: this.formData,
        discountItems: [],
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
                discountItems: formData.discount,
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

            that.formData.discount = that.discountItems;// 优惠信息

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

      //图片上传后操作
      onUploadedImg(e, type){
        // console.log(type)
        switch(type) {
          case 'logo':
            this.formData.logo = e.res.info.url;
            this.$refs.logo.clearValidate()
            break;
          case 'detail':
            this.formData.detail_cover = e.res.info.url;
            this.$refs.detail_cover.clearValidate()
            break;
          case 'list':
            this.formData.list_cover = e.res.info.url;
            this.$refs.list_cover.clearValidate()
            break;
        }
      },

   



      // 添加元素
      onAdd(type){
        // console.log(type);
        switch(type) {
          case 'discount':
            if(this.discountItems.length < 5) {
              this.discountItems.push({title:"", content:"", rule:"", start_time:"", end_time:"",btn:[]});
            } else {
              this.$alert("超出限制", "提示", {
                confirmButtonText: "确定",
                callback: (action) => {
                    return ;
                },
              });
            }
            break;
        }
      },
      // 删除元素
      onDelete(index, type){
        switch(type) {
          case 'discount':
            this.discountItems.splice(index, 1);
            break;
        }
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
