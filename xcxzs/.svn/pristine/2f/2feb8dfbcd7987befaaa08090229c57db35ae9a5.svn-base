<template>
  <div class="mapeditor_container">
    <el-form :model="formData" :rules="rules" ref="formData">
        <div class="mapeditor-title">
          基本信息
        </div>

          <el-row class="mapeditor-content" >
            <el-form-item  label="上传图片(大小超过3M)"  ref="img_url" prop="upload" >
              <br>
              <img-upload2
                ref="imgUpload"
                url="upload/imgUpload"
                :thumb='{ isthumb:1, width:1200}'
                :show-file-list="true"
                :limit='1'
                :fileList.sync="formData.img_url"
                :uploadedImg="onUploadedImg">
              </img-upload2>
            </el-form-item>
          </el-row>
          <el-row>
            <el-col :lg="5" :sm="12" :xs="24">
              <el-form-item prop="title" label="地块名" label-width="90px">
                <el-input placeholder="请输入地块名" v-model="formData.title"></el-input>
              </el-form-item>
            </el-col>
            <el-col :lg="5" :sm="12" :xs="24">
              <el-form-item prop="landaddr" label="地块所在地" label-width="90px">
                <el-input placeholder="请输入地块所在地" v-model="formData.landaddr"></el-input>
              </el-form-item>
            </el-col>
            <el-col :lg="5" :sm="12" :xs="24" >
              <el-form-item prop="land_status" label="发布状态" label-width="90px">
                <el-select

                  v-model="formData.land_status"
                  filterable
                  allow-create
                  default-first-option
                  placeholder="用途">
                  <el-option
                    v-for="item in land_status"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
                  </el-option>
                </el-select>
              </el-form-item>
            </el-col>

            <el-col :lg="8" :sm="12" :xs="24">
              <el-form-item prop="keyword" label="坐标" label-width="90px">
                  <span @click="()=>{dialogVisibleMap = true}">
                      <el-input placeholder="请选择坐标" v-model="formData.coordinate" :disabled="true" ></el-input>
                  </span>
              </el-form-item>
            </el-col>
          </el-row>
      <el-dialog
        title="地图"
        :visible.sync="dialogVisibleMap"
        width="800px"
        :close-on-click-modal="false"
        @close="dialogVisibleMap = false"
      >
        <el-row >
          <el-col :lg="18" :sm="24" :xs="24">
            <el-form-item prop="coordinate" label="地图搜索" label-width="110px">
              <!-- <el-input v-model="addressKeyword" placeholder="请输入地址来查找和点选坐标(地址中请包含城市名称，否则会影响解析效果)"></el-input> -->
              <el-amap-search-box :search-option="{ city: formData.city_str, citylimit: true }" :on-search-result="onMapSearchResult"></el-amap-search-box>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row >
          <el-col :lg="18" :sm="24" :xs="24">
            <el-form-item label="" label-width="110px">
              <div class="amap-wrapper">

                <el-amap vid="map_container"  :zoom="my_zoom" :center="map_center" :events="cEvents">
                  <el-amap-marker v-for="(marker,index) in mapMarkers" :key='index' :position="marker.position" :events="mapMarkerEvents"></el-amap-marker>
                </el-amap>
              </div>
            </el-form-item>
          </el-col>
        </el-row>
      </el-dialog>

      <el-row>
        <el-col :lg="5" :sm="12" :xs="24">
          <el-form-item label="地区" label-width="90px" prop="city_no" >
            <el-select
                       v-model="formData.city_no"
                       filterable
                       allow-create
                       default-first-option
                       placeholder="地区">
              <el-option
                v-for="item in citylist"
                :key="item.id"
                :label="item.cname"
                :value="item.id">
              </el-option>
            </el-select>
          </el-form-item>
        </el-col>
        <el-col :lg="5" :sm="12" :xs="24">
          <el-form-item prop="type" label="土地用途" label-width="90px">

              <el-select

                         v-model="formData.type"
                         filterable
                         allow-create
                         default-first-option
                         placeholder="用途">
                <el-option
                  v-for="item in type_list"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value">
                </el-option>
              </el-select>
          </el-form-item>
        </el-col>
        <el-col :lg="5" :sm="12" :xs="24">
          <el-form-item prop="use_year" label="使用年限" label-width="90px">
            <el-input placeholder="请输入使用年限" v-model="formData.use_year">
              <template slot="append">年</template>
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :lg="5" :sm="12" :xs="24">
          <el-form-item prop="area" label="面积" label-width="90px">
            <el-input placeholder="请输入面积" v-model="formData.area">
              <template slot="append">㎡</template>
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :lg="4" :sm="12" :xs="24">
          <el-form-item prop="plot_ratio" label="容积率" label-width="90px">
            <el-input placeholder="请输入容积率" v-model="formData.plot_ratio">
              <template slot="append">%</template>
            </el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row>
        <el-form-item prop="explain" label="说明" label-width="90px">
            <el-input
              type="textarea"
              style="width: 900px"
              placeholder="请输入说明"
              rows=5
              v-model="formData.explain"
              maxlength="255"
              show-word-limit
            >
            </el-input>
        </el-form-item>
      </el-row>

      <el-form-item>
        <div class="mapeditor-title">
          土地交易信息

        </div>
      </el-form-item>
      <el-row>
          <el-col :lg="4" :sm="12" :xs="24">
            <el-form-item prop="status" label="交易状态" label-width="90px">

              <el-select
                v-model="formData.status"
                filterable
                allow-create
                default-first-option
                placeholder="用途">
                <el-option
                  v-for="item in status_lsit"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value">
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :lg="6" :sm="12" :xs="24">
            <el-form-item prop="recipients" label="竞得方" label-width="90px">
              <el-input placeholder="请输入竞得方" v-model="formData.recipients">
              </el-input>
            </el-form-item>
          </el-col>
          <el-col :lg="6" :sm="12" :xs="24">
            <el-form-item prop="transaction_time" label="成交时间" label-width="90px">
              <el-date-picker
                v-model="formData.transaction_time"
                type="datetime"
                value-format="yyyy-MM-dd HH:mm:ss"
                placeholder="选择日期时间">
              </el-date-picker>
            </el-form-item>
          </el-col>
      </el-row>
      <el-row>
        <el-col :lg="4" :sm="12" :xs="24">
          <el-form-item prop="starting_price" label="起拍楼面价" label-width="90px">
            <el-input placeholder="请输入价格" v-model="formData.starting_price">
              <template slot="append">元/㎡</template>
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :lg="6" :sm="12" :xs="24">
          <el-form-item prop="total_starting_price" label="起拍总价" label-width="90px">
            <el-input placeholder="请输入价格" v-model="formData.total_starting_price">
              <template slot="append">万元</template>
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :lg="6" :sm="12" :xs="24">
          <el-form-item prop="transaction_price" label="成交楼面价" label-width="90px">
            <el-input placeholder="请输入价格" v-model="formData.transaction_price">
              <template slot="append">元/㎡</template>
            </el-input>
          </el-form-item>
        </el-col>
      </el-row>

      <el-row>
        <el-col :lg="4" :sm="12" :xs="24">
          <el-form-item prop="total_transaction_price" label="成交总价" label-width="90px">
            <el-input placeholder="请输入使用年限" v-model="formData.total_transaction_price">
              <template slot="append">年</template>
            </el-input>
          </el-form-item>
        </el-col>

        <el-col :lg="6" :sm="12" :xs="24">
          <el-form-item prop="premium_rate" label="溢价率" label-width="90px">
            <el-input placeholder="请输入容积率" v-model="formData.premium_rate">
              <template slot="append">%</template>
            </el-input>
          </el-form-item>
        </el-col>

        <el-col :lg="6" :sm="12" :xs="24">
          <el-form-item prop="auction_time" label="起拍时间" label-width="90px">
            <el-date-picker
              v-model="formData.auction_time"
              type="datetime"
              value-format="yyyy-MM-dd HH:mm:ss"
              placeholder="选择日期时间">
            </el-date-picker>
          </el-form-item>
        </el-col>
      </el-row>
      <el-form-item style="text-align:center;margin-top: 100px" >
        <el-button type="primary" icon="el-icon-success" @click="submitForm('formData')">保存</el-button>
        <el-button  icon="el-icon-refresh" v-if="!formData.id" @click="resetForm('formData')">重置</el-button>
        <el-button type="danger" icon="el-icon-circle-close" @click="openPage({url: -1, hreftype: 'navigateBack'})">返回</el-button>
<!--        <el-button v-if="formData.status !=1 " type="info" icon="el-icon-success" @click="dosubmit('formData')">草稿保存</el-button>-->
      </el-form-item>
    </el-form>



  </div>
</template>
<script>
import cityList from "../admin/cityList";

var util = require("@/utils/util.js");
import ImgUpload from '@/components/common/ImgUpload.vue';
import Tinymce from '@/components/Tinymce';
import ImgUpload2 from '@/components/common/ImgUpload2.vue';
import VoiceUpload from '@/components/common/VoiceUpload.vue';
import VoiceUpload1 from '@/components/common/VoiceUpload1.vue';
import MycitySelect from '@/components/common/MycitySelect.vue';
import EstatesNew from '@/components/InnerTable/EstatesNew.vue';
import extra from '@/views/extra/DataSet.js'
import baseMixin from  '@/mixin/baseMixin';

export default {
  components: {
    'img-upload': ImgUpload,
    'img-upload2': ImgUpload2,
    'voice-upload': VoiceUpload,
    'voice-upload1': VoiceUpload1,
    'mycity-select': MycitySelect,
    'estates-new': EstatesNew,
     'Tinymce' : Tinymce
  },
  mixins: [baseMixin],
  data() {
    var validateIsNumber = (rule, value, callback) => {
      if (parseFloat(value).toString() == 'NaN') {
  　　　　callback(new Error(rule.message));
  　　　　return false;
  　　}
      callback();
    };


    return {
      dialogVisibleMap:false,//地图显示
      formData: {
        id: '',
        city_no:350200,
        landaddr:'',
        transaction_time:'',
        type:'',
        title: '',
        status:'',
        land_status:'',
        explain:'',
        auction_time:'',
        coordinate:'',
        transaction_price:'',
        total_transaction_price:'',
        img_url:[],
        use_year:'',
        area:'',
        plot_ratio:'',
        recipients:'',
        total_starting_price:'',
        starting_price:'',
        premium_rate:''
      },
      rules: {
        title: [{ required: true, message: "请输入地块名" }],
        recipients: [{ required: true, message: "竞得方法必须填写" }],
        img_url: [{ required: true, message: "请上传图片" }],
        coordinate: [{ required: true, message: "请选择位置我" }],
        city_no: [{ required: true, message: "请选择位置我" }],
        status: [{ required: true, message: "请选择交易状态" }],
        type: [{ required: true, message: "请选择类型" }],
        landaddr: [{ required: true, message: "请选择地块所在地" }],
        transaction_time: [{ required: true, message: "请设置发布时间" }],
        auction_time: [{ required: true, message: "请设置起拍时间" }],
        premium_rate: [{ validator: validateIsNumber, trigger: 'blur', message: "溢价率必须是数字" }],
        total_starting_price: [{ validator: validateIsNumber, trigger: 'blur', message: "起拍总价必须是数字"}],
        starting_price: [{ validator: validateIsNumber, trigger: 'blur', message: "起拍楼面价必须是数字"}],
        plot_ratio: [{ validator: validateIsNumber, trigger: 'blur', message: "容积率必须是数字"}],
        use_year: [{ validator: validateIsNumber, trigger: 'blur', message: "使用年限必须是数字"}],
        area: [{ validator: validateIsNumber, trigger: 'blur', message: "面积必须是数字"}],
      },
      formLabelWidth: "123px",
      categoryList:[],
      type_list:[
        {
          value: 1,
          label: '住宅用地'
        },{
          value: 2,
          label: '商服用地'
        }
      ],
      status_lsit:[
        {
          value: 1,
          label: '公告'
        },{
          value: 2,
          label: '已成交'
        }
      ],

      land_status:[
        {
          value: 1,
          label: '显示'
        },{
          value: 0,
          label: '隐藏'
        }
      ],
      citylist:[],
      default_src:'',//上传图片浏览全地址
      innerVisible: false,
      info: [],
      page_loading: false,
      mapMarkers:[],
      map_center: [118.125313, 24.499929],
      my_zoom: 13,

    };
  },
  computed:{
    mapMarkerEvents(){
      return {
        click: (e) => {
          console.log(e)
          if(e.lnglat){
            this.getLocationPoint({lng: e.lnglat.lng, lat:e.lnglat.lat})
          }
        }
      }
    },
    cEvents(){
      return {
        click:(e)=>{
          if(e.lnglat){
            this.getLocationPoint({lng: e.lnglat.lng, lat:e.lnglat.lat})
          }
        }
      }
    },
  },
  created: function(){


    if(this.$urlData && this.$urlData.id){
      var isMiss= this.doMissId(this.$urlData.id)
      if(!isMiss){
        return
      }
      this.formData.id = this.$urlData.id;
      this.getInfo(this.formData.id);
    }else{
      //添加操作
      this.resetForm('formData')
    }
    this.citylist  = this.$store.getters.userinfo.region_nos_info;
    this.resetData({
      formData: this.formData,
      default_src: '',
      //重置附加信息
      selected_tags: [],
    },()=>{
      this.$nextTick(()=>{
        if(this.$refs.imgUpload){//重置图片信息
          this.$refs.imgUpload.resetData()
        }

        if(this.$refs.editor){//重置富文本信息
          this.$refs.editor.setContent('')
        }
      })
    });



  },
  methods: {
    //搜索结果与展示
    onMapSearchResult(e){
      console.log(e)
      let mapMarkers = [];
      e&&e.map((item)=>{
        mapMarkers.push({position:[item.lng,item.lat]})
      })

      this.my_zoom = 16
      if(e[0]&&e[0].lng){
        this.map_center = [e[0].lng, e[0].lat]
      }
      this.mapMarkers = mapMarkers
    },

    // 地图最终的点选坐标给表单
    getLocationPoint(e) {
      this.lng = e.lng;
      this.lat = e.lat;
      this.formData.coordinate = this.lng + ',' + this.lat;
      this.dialogVisibleMap = false;
      this.my_zoom = 13
    },
    handleCheckAllChange(val){
      let result = this.citylist.map(function (obj) {
            return obj.id
      });
      this.selected_city = val ? result :[];
      this.isIndeterminate = false;
    },
    handleCheckedCitiesChange(value) {

      let result = this.citylist.map(function (obj) {
        return Number(obj.id)
      });
      console.log(99,result)
      let checkedCount = value.length;
      this.checkAll = checkedCount === result.length;
      this.isIndeterminate = checkedCount > 0 && checkedCount <  result.length;
    },
    getInfo(id,p_cate_id){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "land/getInfo",
          data: {id: id}
      }).then(res => {

          that.page_loading = false

          if(res.code==1){
            var formData = res.data
            that.formData.type = String(formData.type)
            console.log(that.formData);
            that.setDataArr({
              formData: formData,
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },

   //数据重置
    resetForm(formName) {
      var that= this
      if(that.$refs[formName]&&that.$refs[formName].resetFields){
        that.$refs[formName].resetFields();
      }

      that.resetData();
      that.setDataArr({
        formData: {
          id: '',
          city:350200,
          landaddr:'',
          transaction_time:'',
          type:'',
          name: '',
          status:'',
          land_status:'',
          explain:'',
          coordinate:'',
          img_url:[],
          use_year:'',
          area:'',
          plot_ratio:'',
          recipients:'',
          total_starting_price:'',
          premium_rate:''
        },
        default_src: '',
        citylist:[],
      })

      if(that.$refs.img_url){//重置图片信息
        that.$refs.img_url.resetData()
      }

    },


    onUploadedImg(e){
      console.log(this.formData.img_ids);
    },

    submitForm(formName) {
      console.log(this.formData)
      let that = this;
      that.$refs[formName].validate((valid) => {
        if (valid) {
          if(that.page_loading){
              return
          }
          that.page_loading = true
          util.requests("post",{
            url: '/land/edit',
            data: that.formData
          }).then(function(res){
            console.log(res);
            that.page_loading = false

            if(res.code==1){
              util.Message.success('操作成功');
              setTimeout(function () {
                util.openPage({url:-1});
              },500)

            }else{
                that.page_loading = false
                util.Message.error(res.msg);
            }
          })
        } else {
          that.page_loading = false
          console.log('error submit!!');
          return false;
        }
      });
    },



   doMissId(id) {
    if(!id){
      var that = this
      this.$alert('缺少必要的id参数，请重新打开', '提示', {
        confirmButtonText: '确定',
        callback: action => {
          that.openPage({hreftype:'navigateBack',url:-1})
        }
      });
      return false
    }
    return true
   },

    openPage: util.openPage
  }
};
</script>
<style lang="scss" scoped>
.mapeditor_container {
  .amap-wrapper {
    width: 500px;
    height: 500px;
  }
  margin-top: 20px;
  background: #fff;
  min-height: calc(100vh - 90px);
  padding: 20px;
  .mapeditor-title {
    padding-left: 10px;
    background-color: rgba($color: #f0f2f5, $alpha: 0.5);
    border-left: 4px solid;
    border-color: #409eff;
    font-size: 14px;
    margin-bottom: 10px;
    span {
      font-size: 12px;
      margin-left: 16px;
      color: #f56c6c;
    }
  }
  .mapeditor-content {
    padding-left: 16px;
    .el-col {
      margin: 10px 0;
    }
  }
}

</style>


