<!--
  图片上传操作
-->
<template>
  <el-form-item class="city-box" label="城市"  :label-width="labelWidth"  >
    <el-select class="select-box" :clearable='clearable' @change='(val)=>{changeRegion(val,"changeProvince")}' :style="showSelect.province==true?'display:inline-block;':'display:none;'" v-model="provinceData2" placeholder="请选择省份">
      <span v-for="item in provinces" :key="item.id">
        <el-option
          v-if="myProvinces[item.id]"
          :label="item.cname"
          :value="String(item.id)">
        </el-option>
      </span>
    </el-select>
    <el-select class="select-box" :clearable='clearable' @change='(val)=>{changeRegion(val,"changeCity")}' :style="showSelect.city==true?'display:inline-block;':'display:none;'" v-model="cityData2" placeholder="请选择城市">
      <el-option v-if="unlimitedCity==true" label="全部" value="-1"></el-option>
      <span v-for="item in citys" :key="item.id">
        <el-option
          v-if="myCitys[item.id]"
          :label="item.cname"
          :value="String(item.id)">
        </el-option>
      </span>
    </el-select>
    <el-select class="select-box" :clearable='clearable' @change='(val)=>{changeRegion(val,"changeArea")}' :style="showSelect.area==true?'display:inline-block;':'display:none;'" v-model="areaData2" placeholder="请选择区级">
      <el-option
        v-for="item in areas"
        :key="item.id"
        :label="item.cname"
        :value="String(item.id)">
      </el-option>
    </el-select>
    <el-select class="select-box" :clearable='clearable' @change='(val)=>{changeRegion(val,"changeBusinessArea")}' :style="showSelect.businessArea==true?'display:inline-block;':'display:none;'" v-model="businessAreaData2" placeholder="请选择商圈">
      <el-option
        v-for="item in businessAreas"
        :key="item.id"
        :label="item.cname"
        :value="String(item.id)">
      </el-option>
    </el-select>
    <el-select class="select-box" :clearable='clearable' @change='(val)=>{changeRegion(val,"changeStreet")}' :style="showSelect.street==true?'display:inline-block;':'display:none;'" v-model="streetData2" placeholder="请选择街道">
      <el-option
        v-for="item in streets"
        :key="item.id"
        :label="item.cname"
        :value="String(item.id)">
      </el-option>
    </el-select>
  </el-form-item>
</template>

<script>
var util = require("@/utils/util.js");
/**
 * <mycity-select :provinceData.sync='formData.province' :cityData.sync='formData.city' :areaData.sync='formData.area' :isMy='true'></mycity-select>
 */
export default {
  name: 'mycity-select',
  props: {
    model:{
      default () {
        return '1';
      }
    },
    province_no:{
      default () {
        return '';
      }
    },
    city_no: {
      default () {
        return '';
      }
    },
    area_no:{
      default () {
        return '';
      }
    },
    business_area_no:{
      default () {
        return '';
      }
    },
    street_no:{
      default () {
        return '';
      }
    },
    'label-width':{
      default () {
        return '';
      }
    },
    isMy:{//是否是自己权限的城市列表
      default () {
        return false;
      }
    },
    url:{
      default () {
        return "city/sysCitys";
      }
    },
    siteAreasUrl:{//自定义区级的请求
      default () {
        return "";
      }
    },
    unlimitedCity:{ //显示城市不限制
      default () {
        return false;
      }
    }
  },
  watch:{
    'provinceData2': function(val){
      this.$emit('update:province_no', val)

      if(this.showSelect.province==true){
        this.cityData2 = '';
        this.citys = [];

        this.getSysCitys('citys',{
          data:{pid: val}
        }) //市级列表
      }
    },
    'cityData2': function(val){
      this.$emit('update:city_no', val)

      if(['2','3','4','5'].includes(this.model)){//直接操作市级反找省级
        let c = this.myCitys[val]
        this.provinceData2 = c&&c.pid?String(c.pid):''
      }

      this.areaData2 = '';
      this.areas = [];

      if(this.citys&&this.citys[val]){
        // 返回省市信息
        let moreInfo = this.citys[val];
        moreInfo.type = 'city';
        this.$emit('getMoreInfo', moreInfo);
      }

      if(this.showSelect.area==true){
        if(this.siteAreasUrl){//自定义区级列表
          //'city/customUrl'
          this.getSysCitys('areas',{
            url: this.siteAreasUrl,
            data: {pid: val, status:1}
          }) //区级列表
        }else{
          this.getSysCitys('areas',{
            data:{pid: val}
          }) //区级列表
        }
      }

    },
    'areaData2': function(val){
      this.$emit('update:area_no', val)

      if(this.areas&&this.areas[val]){
        // 返回区信息
        let moreInfo = this.areas[val];
        moreInfo.type = 'area';
        this.$emit('getMoreInfo', moreInfo);
      }

      this.businessAreaData2 = ''
      this.businessAreas = [];
      if(this.showSelect.businessArea==true){//显示商圈
        this.getSysCitys('businessAreas',{url: 'city/siteBusinessAreas',data:{ area_no: val}})
      }

      this.streetData2 = ''
      this.streets = [];
      if(this.showSelect.street==true){//显示街道
        this.getSysCitys('streets',{url: 'city/siteStreet',data:{ area_no: val}})
      }
    },
    'businessAreaData2': function(val){
      if(this.businessAreas&&this.businessAreas[val]){
        // 返回商圈信息
        let moreInfo = this.businessAreas[val];
        moreInfo.type = 'businessAreas';
        this.$emit('getMoreInfo', moreInfo);
      }

      this.$emit('update:business_area_no', val)
    },
    'streetData2': function(val){
      if(this.streets&&this.streets[val]){
        // 返回街道信息
        let moreInfo = this.streets[val];
        moreInfo.type = 'streets';
        this.$emit('getMoreInfo', moreInfo);
      }

      this.$emit('update:street_no', val)
    },
    province_no(newVal){
      this.provinceData2 = newVal?String(newVal):''
    },
    city_no(newVal){
      this.cityData2 = newVal?String(newVal):''
    },
    area_no(newVal){
      this.areaData2 = newVal?String(newVal):''
    },
    business_area_no(newVal){
      this.businessAreaData2 = newVal?String(newVal):''
    },
    street_no(newVal){
      this.streetData2 = newVal?String(newVal):''
    }
  },
  computed: {

  },
  data () {
    return {
      provinceData2:'',
      cityData2:'',
      areaData2:'',
      businessAreaData2:'',
      streetData2:'',

      provinces: '',
      citys: '',
      areas:'',
      businessAreas:'',
      streets:'',

      showSelect:{
        province: true,
        city: true,
        area: true,
        businessArea: false,
        street: false,
      },
      clearable:true,

      //该账号拥有的省市区
      myProvinces:{},
      myCitys:{},
    };
  },
  created(){
    if(this.isMy == true){
      this.getMyCitys();
    }
    if(this.unlimitedCity == true){
      this.clearable = false;
    }

    switch (this.model) {
      case '2'://显示市级区级
        this.showSelect = {
          province: false,
          city: true,
          area: true,
          businessArea: false,
          street: false,
        }
        break;
      case '3'://显示市级
        this.showSelect = {
          province: false,
          city: true,
          area: false,
          businessArea: false,
          street: false,
        }
        break;
      case '4'://显示市级区级商圈
        this.showSelect = {
          province: false,
          city: true,
          area: true,
          businessArea: true,
          street: false,
        }
        break;
      case '5'://显示市级区级商圈街道
        this.showSelect = {
          province: false,
          city: true,
          area: true,
          businessArea: true,
          street: true,
        }
        break;
      default://显示省级市级区级
        this.showSelect = {
          province: true,
          city: true,
          area: true,
          businessArea: false,
          street: false,
        }
        break;
    }

    //初始化时赋值
    if(this.province_no){
      this.provinceData2 = String(this.province_no)
    }
    if(this.city_no){
      this.cityData2 = String(this.city_no)
    }
    if(this.area_no){
      setTimeout(() => {
        this.areaData2 = String(this.area_no)
      }, 10);
    }

    if(this.showSelect.province==true){
      this.getSysCitys('provinces',{data:{pid: 0}}) //省份列表
    }else{
      //省份隐藏时直接走城市操作
      /* let activeCity = this.$store.getters.activeCity
      this.provinceData2 = activeCity.pid
      setTimeout(() => {
        this.cityData2 = activeCity.id
      }, 50); */
      if(['2','3','4','5'].includes(this.model)){
        //市级列表
        this.citys = this.myCitys
      }
    }

  },
  methods: {
    getSysCitys(key, e={}){   //获取所有数据，或按条件查找数据
      let that = this
      if(!e.url){
        e.url = that.url
      }
      if(!e.data){
        e.data = {}
      }

      util.requests("post", {
          url: e.url,
          data: e.data
        }).then(res => {
          if(res.code==1){
            //let arr = [{id:0,cname:'不限制'}]
            let arr = {}
            for(let i in res.data){
              let item = res.data[i]
              arr[item.id] = {...item}
            }
            that[key] = arr
            //console.log(that[key])
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    getMyCitys(){
      let myCitys = this.$store.getters.userinfo&&this.$store.getters.userinfo.region_nos_info?this.$store.getters.userinfo.region_nos_info:[]
      this.myProvinces = {}
      this.myCitys = {}
      for (let i in myCitys) {
        let item = myCitys[i];
        this.myProvinces[item.pid] = item.pcname
        this.myCitys[item.id] = {...item}
      }
    },
    changeRegion(val,key){
      this.$emit(key,val)
    },

  },
};
</script>

<style lang="scss" scoped>
.city-box{
  .select-box{
    /* margin-right:20px; */
  }
}

</style>
