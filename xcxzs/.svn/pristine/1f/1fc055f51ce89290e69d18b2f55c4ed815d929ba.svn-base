<template>
  <div class="mapeditor_container">
    <el-form :model="formData" :rules="rules" ref="formData">
      <!--内容详情 -start-->
      <el-form-item>
        <div class="mapeditor-title">
          内容详情
        </div>
        <el-row>
          <el-col :lg="8" :sm="12" :xs="24">
              <el-form-item prop="name" label="标题" label-width="80px">
                <el-input placeholder="请输入文章标题" style="width:600px" v-model="formData.name"></el-input>
              </el-form-item>
            </el-col>
        </el-row>
        <el-row :gutter="60" class="mapeditor-content">
           
            <el-col :lg="16" :sm="20" :xs="24">
              <el-form-item prop="context" label="内容详情" required label-width="80px">
                <Tinymce ref="editor" :height="400"   v-model="formData.context" />
              </el-form-item>
            </el-col>
        </el-row>
      </el-form-item>

      <!--内容详情 -start-->

     

      <el-form-item>
        <div class="mapeditor-title"> 发布城市</div>
      </el-form-item>

      <el-row>
        <el-form-item label="发布城市" v-if="true" label-width="100px">
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
        <div class="mapeditor-title"> 附加信息设置</div>
      </el-form-item>

      <el-form-item label=" 标签选择" v-if="tagList.length" label-width="100px">
<!--        <el-checkbox-group v-model="selected_tags" size="small">-->
<!--          <el-checkbox  prop="lable" v-for="item in tagList" :label="item.id" :key="item.id">-->
<!--            {{item.name}}-->
<!--          </el-checkbox>-->
<!--        </el-checkbox-group>-->

        <div v-for="(tag,index) in tagList">
          <el-row v-if="tag.children">
            <el-divider><span style="color: #5A6066;font-size: 20px">{{tag.name}}</span></el-divider>
            <el-col :span="24">
              <el-form-item :label="tag.name +':'" :label-width="formLabelWidth">
                <el-checkbox-group v-model="selected_tags" size="small">
                  <el-checkbox  prop="lable" v-for="item in tag.children" :label="item.id" :key="item.id">
                    {{item.name}}
                  </el-checkbox>
                </el-checkbox-group>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
      </el-form-item>
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

<!--      <el-form-item>-->
<!--          <el-col :lg="2" :sm="12" :xs="24">-->
<!--            <el-form-item prop="is_top" required label="是否置顶" label-width="80px">-->
<!--              -->
<!--            </el-form-item>-->
<!--          </el-col>-->

<!--          <el-col :lg="6" :sm="12" :xs="24" v-if="formData.is_top">-->
<!--            <el-form-item prop="top_time" required label="置顶到期时间" label-width="120px">-->
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
      <el-form-item v-if="formData.is_propert_news">
        <div class="mapeditor-title">
          房源信息
        </div>
        <el-row>

          <el-col :span="12">
            <mycity-select @changeCity='changeCity'  :city_no.sync='formData.region_no' :isMy='true' model='3' siteAreasUrl='city/siteAreas'></mycity-select>
          </el-col>
          <el-col :span="12">
            <el-form-item label="选择新房" prop="forid" :label-width="formLabelWidth">
                <el-col :span="12">
                  <span @click="changeInnerShow">
                    <el-input style="width:100%;display:none;"  v-model="formData.forid" placeholder="请选择新房"></el-input>
                    <el-input style="width:100%" :disabled='true'  v-model="formData.forname" placeholder="请选择新房"></el-input>
                  </span>
                </el-col>
                <el-col :span="4" style="text-align: right;">
                  <el-button  @click="clearInner">清空</el-button>
                </el-col>
            </el-form-item>

            <estates-new :region_no='formData.region_no' :show.sync='innerVisible' @innerFormData='innerFormData'></estates-new>
          </el-col>
        </el-row>
      </el-form-item>

<el-row>
           <el-col :lg="8" :sm="12" :xs="24" >
            <el-form-item prop="order_type" required label="排列方式" label-width="80px">
              <el-select    v-model="formData.order_type"  style="width:100%;" placeholder="请选择" @change="setImgLimit" >
                <el-option label="无图" :value='0'></el-option>
                <el-option label="单图" :value='1'></el-option>
                <!-- <el-option label="双图" :value='2'></el-option> -->
                <el-option label="三图" :value='3'></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
      <el-form-item v-if="formData.order_type != 0">
        <div class="mapeditor-title">
          图片上传
          <span>(建议文件小于3M)</span>
        </div>
        
<p></p>
        <el-row class="mapeditor-content" >
         
          <el-form-item  label="上传图片"  ref="cover" prop="upload">
            <img-upload2
              ref="imgUpload"
              url="upload/imgUpload"
              :thumb='{ isthumb:1, width:1200}'
              :show-file-list="true"
              :limit='Number(formData.order_type)'
              :fileList.sync="formData.img_url"
              :imgIds.sync="formData.img_ids"
              :uploadedImg="onUploadedImg">
            </img-upload2>
          </el-form-item>

        </el-row>
      </el-form-item>
      <el-form-item>
        <!-- <div class="mapeditor-title">
          分享设置
        </div> -->
        <el-row :gutter="60" class="mapeditor-content">
          <el-col :lg="16" :sm="20" :xs="24">
            <el-form-item prop="context" label="分享摘要" required label-width="80px">
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

<!--基础信息 -start-->
       <el-form-item>
        <div class="mapeditor-title">基础信息</div>
        <el-row class="mapeditor-content">
          <el-col :lg="8" :sm="12" :xs="24">
            <el-form-item prop="is_original" required label="是否是原创文章" label-width="110px">
              <el-select  v-model="formData.is_original" style="width:100%;" placeholder="请选择" >
                <el-option label="否" value="0"></el-option>
                <el-option label="是"   value="1"></el-option>
              </el-select>
            </el-form-item>
          </el-col>

          <!-- <el-col :span="8">
            <el-form-item prop="is_propert_news"    label="是否房源信息" label-width="120px">
              <el-switch
                @change="setPropertNesData"
                v-model="formData.is_propert_news"
                active-color="#13ce66"
                inactive-color="#ff4949">
              </el-switch>
            </el-form-item>
          </el-col> -->
            <el-col :lg="8" :sm="12" :xs="24">
              <el-form-item prop="keyword" label="原文链接" label-width="90px">
                <el-input placeholder="如果是其他地方文章请输入原文链接" v-model="formData.source_link"></el-input>
              </el-form-item>
            </el-col>

            
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

<!--            <el-col :lg="8" :sm="12" :xs="24">-->
<!--              <el-form-item prop="cate_id" label="栏目选择" label-width="80px" style="width: 460px" >-->
<!--                  <el-cascader-->
<!--                    :options="categoryList"-->
<!--                    v-model="formData.cate_id"-->
<!--                    :props="{-->
<!--                     value:'id',-->
<!--                     label:'title',-->
<!--                     multiple:true-->
<!--                    }"-->

<!--                    :show-all-levels="false"-->
<!--                  ></el-cascader>-->
<!--              </el-form-item>-->
<!--            </el-col>-->
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

<!--          <el-col :lg="8" :sm="12" :xs="24">-->
<!--            <el-form-item prop="has_comment" required label="资讯类型" label-width="80px">-->
<!--              <el-select  v-model="formData.resource_type" style="width:100%;" placeholder="请选择"  @change="clearUpload">-->
<!--                <el-option label="文章" value="1"></el-option>-->
<!--&lt;!&ndash;                <el-option label="长视频" value="2"></el-option>&ndash;&gt;-->
<!--&lt;!&ndash;                <el-option label="短视频" value="3"></el-option>&ndash;&gt;-->

<!--              </el-select>-->
<!--            </el-form-item>-->
<!--          </el-col>-->


          


        </el-row>
        <el-row>
<!--          <el-col :span="8">-->
<!--            <el-form-item prop="city_list" label="城市"  style="width:460px;"  label-width="93px">-->
<!--              <el-col :lg="8" :sm="12" :xs="24"   >-->
<!--                  <el-select  v-model="formData.city_list"   multiple  style="width:460px;"     placeholder="请选择"   @change="test" >-->
<!--                    <el-option-->
<!--                      v-for=" item in citylist" :key="item.id" :label="item.cname" :value="item.id"></el-option>-->
<!--                  </el-select>-->
<!--              </el-col>-->
<!--            </el-form-item>-->
<!--          </el-col>-->



          
        </el-row>

      </el-form-item>

      <el-form-item>
        <!--统计设置-start -->
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
      <!--基础信息 -start-->

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
      formData: {
        id: '',
        source_link:"",
        cate_id:'',
        is_original:'0',
        name: '',
        sort: 50,
        status:'0',
        context:'',//内容详情
        lable:'',
        keyword:'',
        is_top:false,
        is_index:false,
        title:'',
        order_type:0,
        city_list:[],
        tags:[],
        resource_type:1,
        top_time:'',
        has_comment:'1',
        release_time:'',
        num_read: 0,
        num_collect: 0,
        img_url:[],
        img_ids:[],
        num_share: 0,
        region_no:'',
        description:'',
        forid:'',//关联id
        forname:'',
        num_thumbup: 0,
        is_propert_news:false,
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
      formLabelWidth: "123px",
      categoryList:[],
      tagList:[],//获取标签列表
      nearbyList:[],//周边的列表
      facilitieList:[],//设施的列表
      //附加信息中已选中的数据
      selected_tags:[],
      selected_city:[],
      citylist:[],
      is_fy:false,
      isIndeterminate:false,
      checkAll: false,
      default_src:'',//上传图片浏览全地址
      innerVisible: false,
      info: [],
      page_loading: false,

    };
  },
  watch: {
    selected_tags(val){
      for(var i=0 ; i<val.length;i++){
        if((val[i] == 35) || (val[i] == 45)){
          this.formData.is_propert_news = true
          break;
        }else{
            this.formData.is_propert_news = false
        }
      }
      

      this.formData.lable = val.join(',')
    },
    selected_city(val){
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
      this.formData.p_cate_id = this.$urlData.p_cate_id

      this.getInfo(this.formData.id,this.formData.p_cate_id);
    }else{
      //添加操作
      this.resetForm('formData')
    }
    this.getTagList();
    this.getCategoryList()
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
          url: "news/getInfo",
          data: {id: id,p_cate_id:p_cate_id}
      }).then(res => {

          that.page_loading = false
          if(res.code==1){
            var formData = res.data
            console.log(formData)
            formData.status=String(formData.status)
            formData.forid= Number(formData.forid)
            formData.order_type=formData.order_type?Number(formData.order_type):0
            formData.has_comment=String(formData.has_comment)
            formData.is_original=String(formData.is_original)
            formData.is_propert_news=Boolean(formData.is_propert_news)
            formData.is_top     =formData.is_top==0 ? false:true
            formData.is_index     =formData.is_index==0 ? false:true
            formData.recommend_place = formData.recommend_place?formData.recommend_place:[]
            // console.log(formData.recommend_place,333);
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
              default_src: that.$getRealImgUrl(formData.cover),
            })
            console.log(755,this.selected_city)
            console.log(744,this.citylist)
          }else{
            util.Message.error(res.msg);
          }
      });
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
    setTopAndINdex(e){
        console.log(e)
    },
    clearUpload(){
      this.formData.video_url  = '';
      this.$refs.voideUpload.videoFlag =true;
    },
    // clearUploadImg(){
    //   this.formData.img_url  = [];
    // },
    setPropertNesData(e){
       var taht= this;
       if(!e){
         this.clearInner();
       }
    },
    getCategoryList(){
      var that = this

      util.requests("post", {
          url: "news/getCategoryListAll",
          data:{pid:[9, 20,24]}
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
          url: "news/getExtraIsTrueList",
      }).then(res => {
          if(res.code==1){
            that.setDataArr({
              tagList : res.data,
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
          cate_id:'',
          name: '',
          is_original:'0',
          sort: 50,
          status:'0',
          context:'',//内容详情
          lable:'',
          keyword:'',
          is_top:false,
          is_index:false,
          title:'',
          order_type:0,
          tags:[],
          resource_type:1,
          city_list:[],
          top_time:'',
          has_comment:'1',
          release_time:'',
          num_read: 0,
          num_collect: 0,
          img_url:[],
          img_ids:[],
          num_share: 0,
          region_no:'',
          forid:'',//关联id
          forname:'',
          num_thumbup: 0,
          is_propert_news:false,
          recommend_place:[],
          source_link:'',
          description:''
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
          that.formData.status  = that.formData.status == 2?0:that.formData.status;
          util.requests("post",{
            url: '/news/edit',
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
            url: '/news/edit',
            data: that.formData
          }).then(function(res){
            console.log(res);
            that.page_loading = false
            if(res.code==1){
              util.Message.success('操作成功');
              setTimeout(function () {
                util.openPage({url:-1});
              },300)
              console.log('234234');
              // this.openPage({url:'/news/index'});
            }else{
              try {
                that.page_loading = false
                util.Message.error(res.msg);
              }catch (e) {
                console.log(11111);
              }

            }
          })

    },
    setImgLimit(e){
      this.$refs.imgUpload&&this.$refs.imgUpload.resetData();
      this.formData.img_ids = [];
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


