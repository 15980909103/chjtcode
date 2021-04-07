<template>
  <el-form style="padding-right:50px;" :model="formData" ref="formData" :rules="rules">

    <div class="mapeditor-title">图片上传</div>

    <el-row >
      <el-col :lg="8" :sm="8" :xs="8">
        <el-form-item label="开发商logo（150x150）" :label-width="formLabelWidth" ref="logo" prop="logo">
          <img-upload2 ref="imgUpload" url="upload/imgUpload" :thumb='{ isthumb:1, width:150, height:150 }' :show-file-list="false" :fileList.sync="formData.logo" :imgIds.sync="formData.logo_id" :uploadedImg="onUploadedImg"></img-upload2>
          <!-- <img-upload ref="imgUpload" url="upload/imgUpload" :thumb='{isthumb:1}' :default_src.sync='default_logo_src' :uploadedImg="(e)=>onUploadedImg(e, 'logo')" ></img-upload> -->
        </el-form-item>
      </el-col>
      <el-col :lg="8" :sm="8" :xs="8">
        <el-form-item label="详情封面（750x1210）" :label-width="formLabelWidth" ref="detail_cover" prop="detail_cover">
          <img-upload2 ref="imgUpload" url="upload/imgUpload" :show-file-list="false" :thumb='{ isthumb:1, width:750, height:1210 }' :fileList.sync="formData.detail_cover" :imgIds.sync="formData.detail_cover_id" :uploadedImg="onUploadedImg"></img-upload2>
          <!-- <img-upload ref="imgUpload" url="upload/imgUpload" :thumb='{isthumb:1}' :default_src.sync='default_detailcover_src' :uploadedImg="(e)=>onUploadedImg(e, 'detail')" ></img-upload> -->
        </el-form-item>
      </el-col>
      <el-col :lg="8" :sm="8" :xs="8">
        <el-form-item label="列表封面" :label-width="formLabelWidth" ref="list_cover" prop="list_cover">
          <img-upload2 ref="imgUpload" url="upload/imgUpload" :show-file-list="false" :thumb='{ isthumb:1, width:450, height:450 }' :fileList.sync="formData.list_cover" :imgIds.sync="formData.list_cover_id" :uploadedImg="onUploadedImg"></img-upload2>
          <!-- <img-upload ref="imgUpload" url="upload/imgUpload" :thumb='{isthumb:1}' :default_src.sync='default_listcover_src' :uploadedImg="(e)=>onUploadedImg(e, 'list')" ></img-upload> -->
        </el-form-item>
      </el-col>
    </el-row>

    <div class="mapeditor-title">沙盘图</div>
    <el-row >
      <el-col :lg="24" :sm="24" :xs="24">
        <el-form-item label-width="110px"  label="上传图片"  ref="sand_table" prop="upload">
          <img-upload2
            ref="imgUpload"
            url="upload/imgUpload"
            :thumb='{ isthumb:1, width:750, height:750 }'
            :show-file-list="true"
            :limit=10
            :fileList.sync="formData.sand_img_url"
            :imgIds.sync="formData.sand_img_ids"
            :uploadedImg="onUploadedImg">
          </img-upload2>
        </el-form-item>
      </el-col>
    </el-row>


    <div class="mapeditor-title">基本信息</div>
    <el-row >
      <el-col :lg="11" :sm="12" :xs="24">
        <el-form-item prop="name" label="楼盘名称" label-width="110px">
          <el-input placeholder="请输入楼盘名称" v-model="formData.name"></el-input>
        </el-form-item>
      </el-col>
      <el-col :lg="11" :sm="24" :xs="24">
        <el-form-item prop="coordinate" label="坐标地址" label-width="110px">
            <span @click="dialogVisibleMap = true">
              <el-input placeholder="点击搜索地图" v-model="formData.coordinate" :disabled="true" ></el-input>
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

    <el-row >
      <el-col :lg="10" :sm="12" :xs="24">
        <el-form-item prop="title" label="副标题" label-width="110px">
          <el-input placeholder="请输入副标题" v-model="formData.title"></el-input>
        </el-form-item>
      </el-col>
      <el-col :lg="10" :sm="12" :xs="24">
        <el-form-item prop="price_str" label="价格描述" label-width="110px">
          <el-input placeholder="请输入参考价格描述" v-model="formData.price_str"></el-input>
        </el-form-item>
      </el-col>
      <el-col :lg="7" :sm="12" :xs="24">
        <el-form-item prop="price" label="参考均价" label-width="110px">
          <el-input-number :precision="2" :step="0.1"  v-model="formData.price"></el-input-number> 元/㎡
        </el-form-item>
      </el-col>
      <el-col :lg="7" :sm="12" :xs="24">
        <el-form-item prop="price_total" label="参考总价" label-width="110px">
          <el-input-number :precision="2" :step="0.1"  v-model="formData.price_total"></el-input-number> 万元/套
        </el-form-item>
      </el-col>
    </el-row>

    <el-row >
      <el-col :lg="11" :sm="12" :xs="24">
        <el-form-item prop="house_type" label="户型" label-width="110px">
          <el-input placeholder="请输入户型(如：5/7居)" v-model="formData.house_type"></el-input>
        </el-form-item>
      </el-col>
      <el-col :lg="11" :sm="12" :xs="24">
        <el-form-item prop="built_area" label="户型建面" label-width="110px">
          <el-input placeholder="请输入户型建面范围(如：78-127㎡)" v-model="formData.built_area"></el-input>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row >
      <el-col :lg="24" :sm="12" :xs="24">
        <mycity-select  label-width="110px" @getMoreInfo="getMoreInfo" :province_no.sync='formData.province' :city_no.sync='formData.city' :area_no.sync='formData.area' :business_area_no.sync='formData.business_area' :street_no.sync='formData.street' :isMy='true' model='5' siteAreasUrl='city/siteAreas'></mycity-select>
      </el-col>
    </el-row>

    <el-row >
        <el-form-item required prop="address" label="详细地址" label-width="110px">
          <el-input placeholder="请输入详细地址(上面的省市区等信息不用再填)" v-model="formData.address"></el-input>
        </el-form-item>
    </el-row >

    <el-row>
      <el-col :lg="11" :sm="12" :xs="24">
        <el-form-item prop="subway" label="地铁" label-width="110px">
          <el-select clearable v-model="formData.subway_sites" filterable placeholder="输入搜索">
            <el-option v-for="item in subway_sites" :key="item.id" :label="item.name" :value="item.id"></el-option>
          </el-select>
        </el-form-item>
      </el-col>

      <el-col :lg="11" :sm="12" :xs="24">
        <el-form-item prop="sort" label="排序" label-width="110px">
          <el-input-number label="排序" v-model="formData.sort"></el-input-number>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row >
      <el-col :lg="11" :sm="12" :xs="24">
        <el-form-item prop="developers" label="开发商" label-width="110px">
          <el-input placeholder="请输入开发商" v-model="formData.developers"></el-input>
        </el-form-item>
      </el-col>
      <el-col :lg="11" :sm="12" :xs="24">
        <el-form-item prop="sales_telephone" label="售楼电话" label-width="110px">
          <el-input placeholder="请输入售楼电话" v-model="formData.sales_telephone"></el-input>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row >
      <el-col :lg="24" :sm="24" :xs="24">
        <el-form-item prop="sale_status" label="销售状态" label-width="110px">
          <el-radio v-for="(item,index) in const_estates_new_sale_status" :key="index" v-model="formData.sale_status" :label="String(index)">{{item}}</el-radio>
          <!-- <el-radio v-model="formData.sale_status" label="1">待售</el-radio>
          <el-radio v-model="formData.sale_status" label="2">在售</el-radio>
          <el-radio v-model="formData.sale_status" label="3">售罄</el-radio>
          <el-radio v-model="formData.sale_status" label="4">尾盘</el-radio> -->
        </el-form-item>
      </el-col>
    </el-row>

    <el-row >
      <el-form-item prop="subway" label="标签" label-width="110px">
        <el-popover
          placement="bottom"
          width="800"
          trigger="click">
          
          <el-row >
            <el-col :lg="24" :sm="24" :xs="24">
              <el-form-item  label="系统标签" label-width="110px">
                <el-checkbox v-model="formData.recommend" true-label="1" false-label="0">推荐</el-checkbox>
                <el-checkbox v-model="formData.is_cheap" true-label="1" false-label="0">低价盘</el-checkbox>
              </el-form-item>
            </el-col>
          </el-row>

          <el-row >
            <el-col :lg="24" :sm="24" :xs="24">
              <el-form-item prop="house_purpose" label="建筑用途" label-width="110px">
                <el-checkbox-group v-model="formData.house_purpose">
                  <el-checkbox  v-for="(item,index) in const_house_purpose" :key='index' :label="String(index)">{{item}}</el-checkbox>
                </el-checkbox-group>
                <!-- <el-radio v-for="(item,index) in const_house_purpose" :key="index" v-model="formData.house_purpose" :label="String(index)">{{item}}</el-radio> -->
              </el-form-item>
            </el-col>
          </el-row>

          <el-row >
            <el-col :lg="24" :sm="24" :xs="24">
              <el-form-item prop="feature_tag" label="特色标签" label-width="110px">
                <el-checkbox-group v-model="formData.feature_tag">
                  <el-checkbox v-for="item in feature_tag" :key="item.id" :label="item.id">{{item.name}}</el-checkbox>
                </el-checkbox-group>
              </el-form-item>
            </el-col>
          </el-row>

          <div slot="reference" class="select-tags-box">
            <el-tag
              class="item-tag"
              v-for="(tag,idx) in selected_tags"
              :key="idx"
              @close="tagClose(tag)"
              closable>
              {{tag.name}}
            </el-tag>
          </div>
        </el-popover>
      </el-form-item>
   </el-row> 


    <div class="mapeditor-title">
      销售许可证
      <el-button style="margin-left:50px;" type="primary" @click="onAdd('license')">添加</el-button>
    </div>

    <el-row  v-for="(item,i) in licenseItems"  :key="'license'+i">
      <el-col :lg="7" :md="7" :xs="24">
        <el-form-item prop="sales_license" label="销售许可证" label-width="120px">
          <el-input type="text" width="2px" v-model="item.license"></el-input>
        </el-form-item>
      </el-col>
      <el-col :lg="7" :md="7" :xs="24">
        <el-form-item prop="open_time" label="发证时间" label-width="120px">
          <el-date-picker
            v-model="item.time"
            type="date"
            value-format="yyyy-MM-dd"
            placeholder="选择日期">
          </el-date-picker>
        </el-form-item>
      </el-col>
      <!-- <el-col :lg="7" :md="7" :xs="24">
        <el-form-item prop="sales_license_building" label="登记楼栋" label-width="120px">
          <el-input type="text" width="2px" v-model="item.building"></el-input>
        </el-form-item>
      </el-col> -->
      <el-col :lg="3" :md="3" :xs="24">
        <el-form-item label-width="50px">
          <el-button  @click="onDelete(i, 'license')">删除</el-button>
        </el-form-item>
      </el-col>
    </el-row>


    <div class="mapeditor-title">分享设置</div>
    <el-row>
      <el-col :span="24">
        <el-form-item label="分享详情" :label-width="formLabelWidth">
          <el-input type="textarea" :role="5" v-model="formData.share_desc" placeholder="分享详情"></el-input>
        </el-form-item>
      </el-col>
    </el-row>


    <div class="mapeditor-title">统计信息设置</div>

    <el-form-item>
      <el-col :lg="6" :sm="12" :xs="24">
        <el-form-item prop="num_read" label="阅读数" label-width="110px">
          <el-input-number label="阅读数" v-model="formData.num_read"></el-input-number>
        </el-form-item>
      </el-col>
      <el-col :lg="6" :sm="12" :xs="24">
        <el-form-item prop="num_collect" label="收藏数" label-width="110px">
          <el-input-number label="收藏数" v-model="formData.num_collect"></el-input-number>
        </el-form-item>
      </el-col>
      <el-col :lg="6" :sm="12" :xs="24">
        <el-form-item prop="num_share" label="转发数" label-width="110px">
          <el-input-number label="转发数" v-model="formData.num_share"></el-input-number>
        </el-form-item>
      </el-col>
      <el-col :lg="6" :sm="12" :xs="24">
        <el-form-item prop="num_share" label="搜索阅读数" label-width="110px">
          <el-input-number label="搜索阅读数" v-model="formData.num_read_search"></el-input-number>
        </el-form-item>
      </el-col>
    </el-form-item>

    <el-form-item>
      <el-col :lg="6" :sm="12" :xs="24">
        <el-form-item prop="num_read_real" label="真实阅读数" label-width="110px">
          <el-input-number label="真实阅读数" v-model="formData.num_read_real" :disabled="true"></el-input-number>
        </el-form-item>
      </el-col>
      <el-col :lg="6" :sm="12" :xs="24">
        <el-form-item prop="num_collect_real" label="真实收藏数" label-width="110px">
          <el-input-number label="真实收藏数" v-model="formData.num_collect_real" :disabled="true"></el-input-number>
        </el-form-item>
      </el-col>
      <el-col :lg="6" :sm="12" :xs="24">
        <el-form-item prop="num_share_real" label="真实转发数" label-width="110px">
          <el-input-number label="真实转发数" v-model="formData.num_share_real" :disabled="true"></el-input-number>
        </el-form-item>
      </el-col>
      <el-col :lg="6" :sm="12" :xs="24">
        <el-form-item prop="num_read_search_real" label="真实搜索阅读数" label-width="110px">
          <el-input-number label="真实搜索阅读数" v-model="formData.num_read_search_real" :disabled="true"></el-input-number>
        </el-form-item>
      </el-col>
    </el-form-item>


    <div v-if="release_status!=2" class="mapeditor-title">上下架</div>

    <el-row v-if="release_status!=2" >
      <el-col :lg="10" :sm="12" :xs="24">
        <el-form-item prop="status" label="上下架" label-width="110px">
          <el-radio v-model="formData.status" label="1">上架</el-radio>
          <el-radio v-model="formData.status" label="0">下架</el-radio>
        </el-form-item>
      </el-col>
    </el-row>


    <el-form-item style="text-align:center">
      <el-button type="primary" icon="el-icon-success" @click="doSubmit('formData', 'release')">保存</el-button>
      <el-button v-if="release_status==2" type="info" icon="el-icon-success" @click="doSubmit('formData', 'draft')">草稿保存</el-button>
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
      var validateImg= (rule, value, callback) => {
        var that = this
        that.$nextTick(function(){
          var field=rule.field
          if(!that.formData[field]){
            callback(new Error(rule.message));
            return false;
          }
          callback();
        })
      };

      return {
        dialogVisibleMap:false,//地图显示
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
        rules: {
          name: [
            { required: true, message: '请输入标签名称', trigger: 'change' },
          ],
          address: [
            { required: true, message: '请输入地址', trigger: 'change' },
          ],
        },
        formLabelWidth: "110px",
        default_logo_src: "",
        default_detailcover_src: "",
        default_listcover_src: "",
        mapMarkers:[],
        map_center: [118.125313, 24.499929],
        my_zoom: 13,
        addressKeyword: "",
        feature_tag: [],
        selling_point_tag: [],
        base_tag: [],
        sysTag:[],
        subways:[],
        licenseItems:[],
        // openTimeItems:[],
        subway_sites:[],
        release_status:2,
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
        selected_tags(){
          let arr= [];

          if(this.formData.recommend==1){
            arr.push({
              name: '推荐',
              type: 'recommend',
            })
          }
          if(this.formData.is_cheap==1){
            arr.push({
              name: '低价盘',
              type: 'is_cheap',
            })
          }

          for(let i in this.const_house_purpose){
            let item = this.const_house_purpose[i];
            if(this.formData.house_purpose.includes(i)){
              arr.push({
                id: i,
                name: item,
                type: 'house_purpose',
              })
            }
          }
         
          for(let i in this.feature_tag){
            let item = this.feature_tag[i];
            if(this.formData.feature_tag.includes(item.id)){
              arr.push({
                id: item.id,
                name: item.name,
                type: 'feature_tag',
              })
            }
          }
          
          console.log(arr,789)
          return arr;
        },
    },
    watch:{
      'formData.city':function(val,old){

        //初始化可配置地铁
        if(typeof(val)=='string'){
          if(val!=old||this.subway_sites.length==0){
            this.subway_sites = [];
            this.getSubwaySites(val);
          }

          if(val==old){
            return
          }

          this.formData.subway_sites = '';
        }
      },
    },

    created: function(){
      this.resetData({
        formData: this.formData,
        feature_tag: this.feature_tag,
        selling_point_tag: this.selling_point_tag,
        base_tag: this.base_tag,
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
      // 判断是添加还是编辑
      if (this.$urlData && this.$urlData.id) {
        var isMiss = this.doMissId(this.$urlData.id);
        if (!isMiss) {
          return;
        }
        //修改操作
        this.formData.id = this.$urlData.id;
        this.getInfo(this.formData.id);
      } else {
        //添加操作
        this.resetForm("formData");
      }

      this.getSysTag();// 初始化可配置标签
      this.getHousePurpose();// 初始化建筑用途列
      this.getEstatesNewSaleStatus(); // 初始化新房销售状态


      /* new AMap.Map("map_container", {
        zoomEnable:true, //是否禁止缩放
        zoom: 12, //缩放比例
        dragEnable: false,//禁止拖动
        cursor: 'hand' // 鼠标在地图上的表现形式，小手
      }); */

    },

    mounted() {

    },

    methods: {
      handleClick(tab, event) {
        console.log(tab, event);
      },

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
              // 处理坐标
              if(formData.coordinate != "") {
                  var coordinate = formData.coordinate.split(",");
                  if(coordinate['0']&&coordinate['1']){
                    //this.map_center = [coordinate['0'],coordinate['1']];
                    that.onMapSearchResult([{lng: coordinate['0'], lat: coordinate['1']}])
                  }
              }

              formData.subways = formData.subways?formData.subways:[]
              that.setDataArr({
                formData: formData,
                default_logo_src: that.$getRealImgUrl(formData.logo),
                default_listcover_src: that.$getRealImgUrl(formData.list_cover),
                default_detailcover_src: that.$getRealImgUrl(formData.detail_cover),
                licenseItems: formData.sales_license,
                release_status: Number(formData.status),
                
              });
              that.formData.activity_type = String(formData.activity_type)
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
            // console.log(that.licenseItems)
            // return ;

            that.formData.sales_license = that.licenseItems;// 许可证
            // that.formData.open_time = that.openTimeItems;// 开盘时间

            // 地铁
            if(that.formData.subway_sites) {
              var arr_subway = [];
              for(var tiss of that.subway_sites) {// 所有站点
                if(tiss.id == that.formData.subway_sites) {// 获取所选站点附属地铁线信息
                  arr_subway = arr_subway.concat(tiss.subway);// 合并附属地铁线
                  break;
                }
              }
              arr_subway = Array.from(new Set(arr_subway));// 去重
              that.formData.subways = arr_subway;
            }

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

      // 获取可配置标签
      getSysTag() {
        var that = this
        util.requests("post",{
          url:"estates/getTagList",
          data: {status:"1", name:"", type:"-1"}
        }).then(res=>{
          that.sysTag = res.data.list
          that.sysTag.forEach((item, index) => {
            switch(item.type) {
                case 1:
                  that.feature_tag.push(item)
                  break;
                case 2:
                  that.selling_point_tag.push(item)
                  break;
                case 3:
                  that.base_tag.push(item)
                  break;
            }
          });
        })
      },

      // 获取地铁线
      getSubways(region_no){
        var that = this
        util.requests("post",{
          url:"city/siteInfo",
          data: {key:"subway",region_no:region_no}
        }).then(function(res){
            that.subways = res.data.info&&res.data.info.val?res.data.info.val:[]
        })
      },

      // 获取地铁站点
      getSubwaySites(region_no) {
        var that = this
        util.requests("post",{
          url:"city/getSubwaySites",
          data: {region_no:region_no}
        }).then(function(res){
            that.subway_sites = res.data;
        })
      },

      // 省市区中文赋值
      getMoreInfo(e) {
          // console.log(e)
          switch(e.type) {
            case 'city':
              this.formData.province_str = e.pcname;
              this.formData.city_str = e.cname;
              break;
            case 'area':
              this.formData.area_str = e.cname;
              break;
            case 'streets':
              this.formData.street_str = e.cname;
              break;
            case 'businessAreas':
              this.formData.business_area_str = e.cname;
              break;
          }
          // console.log(this.formData)
      },

      // 添加元素
      onAdd(type){
        // console.log(type);
        switch(type) {
          case 'license':
            this.licenseItems.push({license:"", time:"", building:""});
            break;
          case 'openTime':
            this.openTimeItems.push({opening_time:"", building:""});
            break;
        }
      },
      // 删除元素
      onDelete(index, type){
        switch(type) {
          case 'license':
            this.licenseItems.splice(index, 1);
            break;
          case 'openTime':
            this.openTimeItems.splice(index, 1);
            break;
        }
      },

      tagClose(e){
        switch (e.type) {
          case 'recommend':
            this.formData.recommend = 0;
            break;
          case 'is_cheap':
            this.formData.is_cheap = 0;
            break;
          case 'house_purpose':
            var idx = this.formData.house_purpose.findIndex(item => item === e.id);
            idx > -1 && this.formData.house_purpose.splice(idx,1);
            break;
          case 'feature_tag':
            var idx = this.formData.feature_tag.findIndex(item => item === e.id);
            console.log(idx, this.formData.feature_tag,e)
            idx > -1 && this.formData.feature_tag.splice(idx,1);
            break;
        
          default:
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
  .select-tags-box{
    width: 100%;
    min-height: 40px;
    line-height: 40px;
    border: 1px solid #DCDFE6;
    padding: 5px 10px;
  }
  .item-tag{
    margin-right: 10px;
  }
  .select-tags-box:hover{
    border-color: #C0C4CC;
  }
}
</style>
