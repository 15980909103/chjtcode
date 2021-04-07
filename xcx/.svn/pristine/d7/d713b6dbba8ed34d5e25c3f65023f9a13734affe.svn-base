<template>
  <div class="mapeditor_container">
    <el-form :model="formData" :rules="rules" ref="formData">

 <el-form-item>
        <div class="mapeditor-title">
          视频上传
          <span>(建议文件小于100M)</span>
        </div>
        <el-row>
          <el-col :lg="8" :sm="12" :xs="24">
              <el-form-item prop="name" label="标题" label-width="80px">
                <el-input placeholder="请输入标题" v-model="formData.name"></el-input>
              </el-form-item>
            </el-col>
        </el-row>
        <p></p>
        <el-row>
          <el-col :lg="12" :sm="12" :xs="24">
            <el-form-item label="视频封面图" :label-width="formLabelWidth" ref="cover" prop="cover">
              <img-upload ref="imgUpload" url="upload/imgUpload" :default_src.sync='formData.cover_url' :uploadedImg="onUploadedImg" ></img-upload>
            </el-form-item>
          </el-col>

          <el-col :lg="12" :sm="12" :xs="24">
            <el-form-item prop="num_thumbup" label="视频上传" required label-width="80px">
              <el-row class="mapeditor-content" >

                <div class="mater-upload-container" v-if="formData.video_path == '' || isedit ">
                  <simple
                    ref="upload"
                    @success="success"
                    @geturl="geturl"
                  >
                    <div slot="tip" class="upload-tip">
                      <i class="el-icon-info"></i>:
                      只能上传 mp4文件
                    </div>
                  </simple>

                </div>
                <div style="position: relative" v-if="formData.video_path != ''  && !isedit">
                  <video  :src="getRealImgUrl(formData.video_path)"  controls="controls" width="200px"> </video>
                  <div style="position: absolute;top: 0px;margin: 0px;left: 120px;z-index: 99">
                    <el-button  type="success" icon="el-icon-success" size="mini"  @click="seteidtflag()">修改</el-button>
                  </div>
                </div>

              </el-row>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row :gutter="60" class="mapeditor-content">
          <el-col :lg="16" :sm="20" :xs="24">
            <el-form-item prop="description" label="分享摘要" label-width="80px">
              <el-input
                type="textarea"
                :rows="6"
                placeholder="请输入内容"
                v-model="formData.description">
              </el-input>
            </el-form-item>
          </el-col>
        </el-row>

      </el-form-item>

      
      <el-form-item>
        <div class="mapeditor-title"> 发布城市</div>
      </el-form-item>

      <el-row>
        <el-form-item label="发布城市" v-if="citylist.length" label-width="100px">
          <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
          <div style="margin: 15px 0;"></div>
          <el-checkbox-group v-model="selected_city" size="small" @change="handleCheckedCitiesChange">
            <el-checkbox  prop="lable" v-for="item1 in citylist" :label="Number(item1.id)" :key="item1.id">
              {{item1.cname}}
            </el-checkbox>
          </el-checkbox-group>
        </el-form-item>

      </el-row>

      <el-form-item>
        <div class="mapeditor-title">
          附加信息设置
        </div>
        <el-col :lg="24" :sm="24" :xs="24">
          <el-form-item label=" 标签选择" label-width="100px">
              <el-checkbox-group v-model="selected_tags" size="small">
                <el-checkbox  prop="lable"
                  v-for="item in tagList"
                              :label="item.id"
                              :key="item.id">
                  {{item.name}}
                </el-checkbox>
              </el-checkbox-group>
          </el-form-item>
        </el-col>

        <el-form-item label="推荐显示" label-width="100px">
          <el-checkbox-group v-model="formData.recommend_place" size="small">
            <el-checkbox  label="1" >置顶</el-checkbox>
            <el-checkbox  label="2" >首页</el-checkbox>
          </el-checkbox-group>
        </el-form-item>

        <el-form-item v-if="formData.recommend_place.includes('1')" prop="top_time" required label="置顶到期时间" label-width="120px">
          <el-date-picker
            v-model="formData.top_time"
            type="datetime"
            value-format="yyyy-MM-dd HH:mm:ss"
            placeholder="选择日期时间">
          </el-date-picker>
        </el-form-item>


<!--          <el-col :lg="2" :sm="12" :xs="24">-->
<!--            <el-form-item prop="has_comment" required label="是否置顶" label-width="80px">-->
<!--              <el-switch-->
<!--                v-model="formData.is_top"-->
<!--                active-color="#13ce66"-->
<!--                inactive-color="#ff4949">-->
<!--              </el-switch>-->
<!--            </el-form-item>-->
<!--          </el-col>-->

<!--          <el-col :lg="6" :sm="12" :xs="24" v-if="formData.is_top">-->
<!--            <el-form-item prop="has_comment" required label="置顶到期时间" label-width="120px">-->
<!--              <el-date-picker-->
<!--                v-model="formData.top_time"-->
<!--                type="datetime"-->
<!--                value-format="yyyy-MM-dd HH:mm:ss"-->
<!--                placeholder="选择日期时间">-->
<!--              </el-date-picker>-->
<!--            </el-form-item>-->
<!--          </el-col>-->

<!--        <el-col :lg="6" :sm="12" :xs="24">-->
<!--          <el-form-item prop="is_index" label="推送首页" label-width="80px">-->
<!--            <el-switch-->
<!--              v-model="formData.is_index"-->
<!--              active-color="#13ce66"-->
<!--              inactive-color="#ff4949">-->
<!--            </el-switch>-->
<!--          </el-form-item>-->
<!--        </el-col>-->
<!--      </el-form-item>-->

<!--      <el-form-item>-->

        <el-form-item  v-if="formData.is_propert_news">
          <div class="mapeditor-title">
            房源信息
          </div>
          <el-row>
            <el-col :span="12">
              <mycity-select @changeCity='changeCity'  :city_no.sync='formData.region_no' :isMy='true' model='3' siteAreasUrl='city/siteAreas'></mycity-select>
            </el-col>
            <el-col :span="12">
              <el-form-item label="选择新房" prop="forid" :label-width="formLabelWidth">
                <el-row>
                  <el-col :span="12">
                  <span @click="changeInnerShow">
                    <el-input style="width:100%;display:none;"  v-model="formData.forid" placeholder="请选择新房"></el-input>
                    <el-input style="width:100%" :disabled='true'  v-model="formData.forname" placeholder="请选择新房"></el-input>
                  </span>
                  </el-col>
                  <el-col :span="4" style="text-align: right;">
                    <el-button  @click="clearInner">清空</el-button>
                  </el-col>
                </el-row>
              </el-form-item>

              <estates-new :region_no='formData.region_no' :show.sync='innerVisible' @innerFormData='innerFormData'></estates-new>
            </el-col>
          </el-row>
        </el-form-item>

        <el-form-item>
        <div class="mapeditor-title">基础信息</div>
        <el-row class="mapeditor-content">
            
            <el-col :lg="8" :sm="12" :xs="24">
              <el-form-item prop="status" required label="状态" label-width="80px">
                <el-select  v-model="formData.status" style="width:100%;" placeholder="请选择" >
                  <el-option label="未启用" value="0"></el-option>
                  <el-option label="启用" value="1"></el-option>
                  <el-option label="草稿" value="2"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :lg="8" :sm="12" :xs="24">
              <el-form-item prop="sort" label="排序"  label-width="80px">
                <el-input placeholder="排序越大越靠前" v-model="formData.sort"></el-input>
              </el-form-item>
            </el-col>

            <el-col :lg="8" :sm="12" :xs="24">
              <el-form-item prop="release_time" label="发布时间" label-width="80px">
                <el-date-picker style="width:100%;"
                  v-model="formData.release_time"
                  value-format="yyyy-MM-dd HH:mm:ss"
                  type="datetime"
                  placeholder="选择日期时间">
                </el-date-picker>
              </el-form-item>
            </el-col>
            <el-col :lg="8" :sm="12" :xs="24">
              <el-form-item prop="title" label="副标题" label-width="80px">
                <el-input placeholder="请输入副标题" v-model="formData.title"></el-input>
              </el-form-item>
            </el-col>

            <el-col :lg="8" :sm="12" :xs="24">
              <el-form-item prop="cate_id" label="类别选择" label-width="80px" >
                  <el-cascader
                    :options="categoryList"
                    v-model="formData.cate_id"
                    :props="{
                     value:'id',
                     label:'name',
                      multiple:true
                    }"
                    :show-all-levels="false"
                  ></el-cascader>
              </el-form-item>
            </el-col>
            <el-col :lg="8" :sm="12" :xs="24">
              <el-form-item prop="keyword" label="关键词" label-width="80px">
                <el-input placeholder="关键词用于seo优化 多个关键词用,号隔开" v-model="formData.keyword"></el-input>
              </el-form-item>
            </el-col>
             <el-col :lg="8" :sm="12" :xs="24">
              <el-form-item prop="has_comment" required label="评论功能" label-width="80px">
                <el-select  v-model="formData.has_comment" style="width:100%;" placeholder="请选择" >
                  <el-option label="启用" value="1"></el-option>
                  <el-option label="禁用" value="0"></el-option>
                </el-select>
              </el-form-item>
            </el-col>



          <el-col :lg="8" :sm="12" :xs="24">
            <el-form-item prop="has_comment" required label="资讯类型" label-width="80px">
              <el-select  v-model="formData.resource_type" style="width:100%;" placeholder="请选择" >
                <el-option label="长视频" value="2"></el-option>
                <el-option label="短视频" value="3"></el-option>

              </el-select>
            </el-form-item>
          </el-col>

        </el-row>

        <el-row>
<!--          <el-col :span="8">-->
<!--            <el-form-item prop="order_type"    label-width="60px">-->
<!--              <mycity-select @changeCity='changeCity'  :city_no.sync='formData.region_no' :isMy='true' model='3' siteAreasUrl='city/siteAreas'></mycity-select>-->
<!--            </el-form-item>-->
<!--          </el-col>-->

          <el-col :span="8">
            <el-form-item prop="is_propert_news"    label="是否房源信息" label-width="120px">
              <el-switch
                @change="setPropertNesData"
                v-model="formData.is_propert_news"
                active-color="#13ce66"
                inactive-color="#ff4949">
              </el-switch>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form-item>
        <!-- <div class="mapeditor-title">
          统计信息设置
        </div> -->
        <el-col :lg="6" :sm="12" :xs="24">
          <el-form-item prop="num_read" label="阅读数" required label-width="80px">
            <el-input placeholder="请输入阅读数 " v-model="formData.num_read"></el-input>
          </el-form-item>
        </el-col>
        <el-col :lg="6" :sm="12" :xs="24">
          <el-form-item prop="num_collect" label="收藏数" required label-width="80px">
            <el-input placeholder="请输入收藏数" v-model="formData.num_collect"></el-input>
          </el-form-item>
        </el-col>
        <el-col :lg="6" :sm="12" :xs="24">
          <el-form-item prop="num_share" label="转发数" required label-width="80px">
            <el-input placeholder="请输入转发数" v-model="formData.num_share"></el-input>
          </el-form-item>
        </el-col>
        <el-col :lg="6" :sm="12" :xs="24">
          <el-form-item prop="num_thumbup" label="点赞数" required label-width="80px">
            <el-input placeholder="请输入点赞数" v-model="formData.num_thumbup"></el-input>
          </el-form-item>
        </el-col>
      </el-form-item>

     

      <el-form-item style="text-align:center" >
        <el-button type="primary" icon="el-icon-success" @click="submitForm('formData')">保存</el-button>
        <el-button  icon="el-icon-refresh" v-if="!formData.id" @click="resetForm('formData')">重置</el-button>
        <el-button type="danger" icon="el-icon-circle-close" @click="openPage({url: -1, hreftype: 'navigateBack'})">返回</el-button>
        <el-button v-if="formData.status !=1 " type="info" icon="el-icon-success" @click="dosubmit('formData')">草稿保存</el-button>
      </el-form-item>
    </el-form>

  </div>
</template>
<script>
var util = require("@/utils/util.js");
var baseconfig = require('@/../baseconfig.js')
import ImgUpload from '@/components/common/ImgUpload.vue';
import Tinymce from '@/components/Tinymce';
import ImgUpload2 from '@/components/common/ImgUpload2.vue';
import VoiceUpload from '@/components/common/VoiceUpload.vue';
import VoiceUpload1 from '@/components/common/VoiceUpload1.vue';
import simple from '@/components/common/simple.vue';
import MycitySelect from '@/components/common/MycitySelect.vue';
import EstatesNew from '@/components/InnerTable/EstatesNew.vue';
import extra from '@/views/extra/DataSet.js'

export default {
  components: {
    'img-upload': ImgUpload,
    'img-upload2': ImgUpload2,
    'voice-upload': VoiceUpload,
    'voice-upload1': VoiceUpload1,
    'mycity-select': MycitySelect,
    'estates-new': EstatesNew,
    'simple': simple,
    Tinymce
	},
  data() {
    var validateIsNumber = (rule, value, callback) => {
      if (parseFloat(value).toString() == 'NaN') {
  　　　　callback(new Error(rule.message));
  　　　　return false;
  　　}
      callback();
    };


    return {
      formData: {
        id: '',
        cate_id:[],
        name: '',
        sort: 50,
        status:'0',
        context:'',//内容详情
        lable:'',
        keyword:'',
        is_top:false,
        is_index:false,
        title:'',
        order_type:'',
        video_url:'',
        video_path:'',
        city_list:[],
        tags:[],
        resource_type:'',
        top_time:'',
        has_comment:'1',
        release_time:'',
        region_no:'',
        forid:'',//关联id
        forname:'',
        is_propert_news:false,
        num_read: 0,
        num_collect: 0,
        img_url:[],
        num_share: 0,
        num_thumbup: 0,
        recommend_place:[],
      },
      rules: {
        name: [{ required: true, message: "请输入名称" }],
        cate_id: [{ required: true, message: "请选择类别" }],
        sort: [{ validator: validateIsNumber, trigger: 'blur' , message: "排序必须是数字" }],
        context: [{ required: true, message: "请输入内容详情" }],
        release_time: [{ required: true, message: "请设置发布时间" }],
        num_read: [{ validator: validateIsNumber, trigger: 'blur', message: "阅读数必须是数字" }],
        num_collect: [{ validator: validateIsNumber, trigger: 'blur', message: "收藏数必须是数字"}],
        num_share: [{ validator: validateIsNumber, trigger: 'blur', message: "转发数必须是数字"}],
        num_thumbup: [{ validator: validateIsNumber, trigger: 'blur', message: "点赞数必须是数字"}],
        lable:[{ required: true, message: "最少选择一个标签" }],
      },
      innerVisible:false,
      formLabelWidth: "123px",
      categoryList:[],
      selected_city:[],
      isIndeterminate:false,
      checkAll: false,
      citylist:[],
      tagList:[],//获取标签列表
      nearbyList:[],//周边的列表
      facilitieList:[],//设施的列表
      //附加信息中已选中的数据
      selected_tags:[],
      selected_city:[],
      isedit:false,

      default_src:'',//上传图片浏览全地址
      accepts: 'image/png',


      info: [],
      page_loading: false,

    };
  },
  computed: {



  },
  watch: {
    selected_tags(val){
      this.formData.lable = val.join(',')
    },

    selected_city(val){
      console.log(val);
      this.formData.city_list = val ?val.join(','):[];
    }
  },
  created: function(){

    if(this.$urlData&&this.$urlData.id){
      var isMiss= this.doMissId(this.$urlData.id)
      if(!isMiss){
        return
      }
      //修改操作
      this.formData.id        = this.$urlData.id
      this.getTagList([]);
      this.getInfo(this.formData.id);
    }else{
      //添加操作
      this.getTagList([]);
      this.resetForm('formData')
    }
    this.citylist  = this.$store.getters.userinfo.region_nos_info;
    this.getCategoryList()

  },
  methods: {

    handleCheckAllChange(val){
      let result = this.citylist.map(function (obj) {
        return obj.id
      });
      this.selected_city = val ? result :[];
      this.isIndeterminate = false;
    },
    handleCheckedCitiesChange(value) {

      let result = this.citylist.map(function (obj) {
        return obj.id
      });
      let checkedCount = value.length;
      this.checkAll = checkedCount === result.length;
      this.isIndeterminate = checkedCount > 0 && checkedCount <  result.length;
    },
    getInfo(id){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "news/getInfoVideo",
          data: {id: id}
      }).then(res => {

          that.page_loading = false
          if(res.code==1){
            var formData = res.data
            formData.status=String(formData.status)
            formData.has_comment=String(formData.has_comment)
            // formData.is_top     =formData.is_top==0 ? false:true
            // formData.is_index     =formData.is_index==0 ? false:true
            formData.is_propert_news=Boolean(formData.is_propert_news)
            formData.resource_type     = String(formData.resource_type)
            formData.recommend_place = formData.recommend_place?formData.recommend_place:[]
            formData.cover_url        = this.$getRealImgUrl(formData.cover_url);
            var selected_tags= []
            var selected_city=[]
            if(formData.lable){//标签数据转换
              selected_tags = formData.lable.split(',')
            }

            for (let i=0;i<selected_tags.length;i++){
              selected_tags[i]  = Number(selected_tags[i])
            }


            if(formData.city_list){//标签数据转换
              selected_city = formData.city_list.split(',')
            }

            for (let i=0;i<selected_city.length;i++){
              selected_city[i]  = Number(selected_city[i])
            }




            if(res.data.statistic){
              formData.num_read = res.data.statistic.num_read
              formData.num_collect = res.data.statistic.num_collect
              formData.num_share = res.data.statistic.num_share
              formData.num_thumbup = res.data.statistic.num_thumbup
            }
            this.handleCheckedCitiesChange(selected_city);

            that.setDataArr({
              formData: formData,
              selected_tags: selected_tags,
              selected_city:selected_city,
              // default_src: that.$getRealImgUrl(formData.cover_ur)
            })
            console.log(998,this.formData)
          }else{
            util.Message.error(res.msg);
          }
      })
    },

    setPropertNesData(e){
      var taht= this;
      if(!e){
        this.clearInner();
      }
    },

    seteidtflag(){
      this.isedit = true;
    },

    success(e) {
      // this.isedit = false;
    },
    changeCity(val){
      this.clearInner()
    },
    changeInnerShow(){
      if(!this.formData.region_no){
        this.$message({
          type: 'error',
          message: '请先选择城市'
        });
        return
      }
      this.innerVisible = true
    },
    innerFormData(e){
      console.log(e)
      this.formData.forid = e.id
      this.formData.forname = e.name
      console.log(this.formData)
    },
    clearInner(){
      this.formData.forname = '';
      this.formData.forid = 0;
    },

    geturl(e){
      console.log(e,234234234234);
      this.formData.video_url = e.id;
    },

    getRealImgUrl(url){
      return  baseconfig.imghost +'/' + this.formData.video_path;
    },

    getCategoryList(){
      var that = this

      util.requests("post", {
          url: "news/getCategoryListAll",
          data:{pid:[13]}
      }).then(res => {
          if(res.code==1){
            that.setDataArr({
              categoryList : res.data,
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    getTagList(e){
      var that = this
      util.requests("post", {
          url: "news/getExtraList",

      }).then(res => {
          if(res.code==1){
            that.setDataArr({
              tagList : res.data.list,
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
      that.setDataArr({
        formData: {
          id: '',
          cate_id:[],
          name: '',
          sort: 50,
          status:'0',
          context:'',//内容详情
          lable:'',
          keyword:'',
          is_top:false,
          is_index:false,
          title:'',
          order_type:'',
          video_url:'',
          video_path:'',
          tags:[],
          resource_type:'',
          top_time:'',
          has_comment:'1',
          release_time:'',
          region_no:'',
          city_list:[],
          forid:'',//关联id
          forname:'',
          is_propert_news:false,
          num_read: 0,
          num_collect: 0,
          img_url:[],
          cover:'',
          cover_url:'',
          num_share: 0,
          num_thumbup: 0,
          recommend_place:[],
        },
        default_src: '',
        citylist:[],
        //重置附加信息
        selected_tags: [],

      })

      if(that.$refs.imgUpload){//重置图片信息
        that.$refs.imgUpload.resetData()
      }

      if(that.$refs.editor){//重置富文本信息
        that.$refs.editor.setContent('')
      }
    },

      onUploadedImg(e){
        this.formData.cover      = e.res.info.id;
        this.formData.cover_url  = e.res.info.url;
        this.$refs.cover.clearValidate()
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
          that.formData.status  = that.formData.status == 2?0:that.formData.status;
          util.requests("post",{
            url: '/news/videoEdit',
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
    dosubmit(formName) {
      console.log(this.formData)
      let that = this;
      if(that.page_loading){
        return
      }
      if(!that.formData.name){
        util.Message.error('名称不能为空');
        return;
      }
      that.page_loading = true
      that.formData.status  =2;
      util.requests("post",{
        url: '/news/videoEdit',
        data: that.formData
      }).then(function(res){
        console.log(res);
        that.page_loading = false

        if(res.code==1){
          util.Message.success('操作成功');
        }else{

            that.page_loading = false
            util.Message.error(res.msg);


        }
      })

    },
    setImgLimit(e){
      this.$refs.uploadimg.resetData();
      // this.imglimit   = this.order_type;
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


