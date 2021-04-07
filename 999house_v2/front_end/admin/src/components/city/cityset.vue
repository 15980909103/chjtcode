<template>
  <div class="_container">
    <el-form ref="form" :model="form">
      <div class="mapeditor-title">基础设置</div>
      <el-row class="mapeditor-content">
        <el-col :lg="18" :md="18" :xs="24">
          <el-form-item label="微信公众号appid" label-width="120px">
            <el-input type="text"  v-model="form.wxh5.appid"></el-input>
          </el-form-item>
        </el-col>
        <el-col :lg="18" :md="18" :xs="24">
          <el-form-item label="微信公众号密钥" label-width="120px">
            <el-input type="text"  v-model="form.wxh5.secret"></el-input>
          </el-form-item>
        </el-col>
        <el-col :lg="18" :md="18" :xs="24">
          <el-form-item label="微信公众号token" label-width="120px">
            <el-input type="text"  v-model="form.wxh5.token"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <div class="mapeditor-title">自动回复</div>

      <el-form-item label="消息方式" label-width="110px">
        <el-radio v-model="form.reply_setting.follow_type"  label="2" >图片</el-radio>
        <el-radio v-model="form.reply_setting.follow_type"  label="1" >文字</el-radio>
      </el-form-item>

      <el-form-item label="聊天查找关键词" label-width="110px">
        <el-input v-model="form.reply_setting.lookup_text"></el-input>
      </el-form-item>

      <div v-if="form.reply_setting.follow_type==1? true:false">
        <el-form-item label="关注时回复内容" label-width="110px">
          <el-input v-model="form.reply_setting.follow_content"></el-input>
        </el-form-item>

        <el-form-item label="聊天回复关键词" label-width="110px">
          <el-input v-model="form.reply_setting.lookup_content"></el-input>
        </el-form-item>
      </div>

      <div v-if="form.reply_setting.follow_type==2? true:false">



        <el-form-item label="图文标题" label-width="110px">
          <el-input v-model="form.reply_setting.Title"></el-input>
        </el-form-item>

        <el-form-item label="图文描述" label-width="110px">
          <el-input v-model="form.reply_setting.description"></el-input>
        </el-form-item>

        <el-form-item prop="img" label="图片" label-width="90px"><span style="color:red">(建议图片规格360*200)</span>
          <img-upload ref="imgUpload" :default_src.sync='form.reply_setting.PicUrl' url="Upload/imgUpload"
                      :uploadedImg="onUploadedImg" :thumb="{isthumb:1,width:480,height:480}"></img-upload>
        </el-form-item>

        <el-form-item label="跳转url" label-width="110px">
          <el-input v-model="form.reply_setting.Url"></el-input>
        </el-form-item>
      </div>


      <div class="mapeditor-title">seo设置</div>
      <el-row class="mapeditor-content">
        <el-col :lg="18" :md="18" :xs="24">
          <el-form-item prop="page_title" label="网站名称" label-width="120px">
            <el-input type="text"  v-model="form.seo.site_title"></el-input>
          </el-form-item>
        </el-col>
        <el-col :lg="18" :md="18" :xs="24">
          <el-form-item prop="page_title" label="页面标题" label-width="120px">
            <el-input type="text"  v-model="form.seo.page_title"></el-input>
          </el-form-item>
        </el-col>
        <el-col :lg="18" :md="18" :xs="24">
          <el-form-item prop="page_keywords" label="页面关键字" label-width="120px">
            <el-input type="text"  v-model="form.seo.page_keywords"></el-input>
          </el-form-item>
        </el-col>
        <el-col :lg="18" :md="18" :xs="24">
          <el-form-item prop="page_desc" label="页面描述" label-width="120px">
            <el-input type="text"  v-model="form.seo.page_desc"></el-input>
          </el-form-item>
        </el-col>
      </el-row>

       <div class="mapeditor-title">城市均价</div>
       <el-row class="mapeditor-content">
        <el-button type="primary" @click="onAdd">添加</el-button>
       </el-row>
       <el-row class="mapeditor-content" v-for="(item,i) in cityItems"  :key="'cityItems'+i">
          <el-col :lg="18" :md="18" :xs="24">
            <el-form-item prop="page_desc" label="城市均价" label-width="120px">
              <el-input-number width="2px" v-model="cityItems[i]"></el-input-number>元/㎡
            </el-form-item>
          </el-col>
          <el-col :lg="5" :md="18" :xs="24">
            <el-form-item label-width="120px">
              <el-button  @click="onDelete(i)">删除</el-button>
            </el-form-item>
          </el-col>
       </el-row>

       <div class="mapeditor-title">城市总价</div>
       <el-row class="mapeditor-content">
        <el-button type="primary" @click="onTotalAdd">添加</el-button>
       </el-row>
       <el-row class="mapeditor-content" v-for="(item,i) in totalItems"  :key="'totalItems'+i">
          <el-col :lg="18" :md="18" :xs="24">
            <el-form-item prop="page_desc" label="城市总价" label-width="120px">
              <el-input-number width="2px" v-model="totalItems[i]"></el-input-number>万元
            </el-form-item>
          </el-col>
          <el-col :lg="5" :md="18" :xs="24">
            <el-form-item label-width="120px">
              <el-button  @click="onTotalDelete(i)">删除</el-button>
            </el-form-item>
          </el-col>
       </el-row>

        <div class="mapeditor-title">城市建筑面积</div>
        <el-row class="mapeditor-content">
          <el-button type="primary" @click="onAreaAdd">添加</el-button>
        </el-row>
        <el-row class="mapeditor-content" v-for="(item,i) in areaItems"  :key="'areaItems'+i">
          <el-col :lg="18" :md="18" :xs="24">
            <el-form-item prop="page_desc" label="城市面积" label-width="120px">
              <el-input-number width="2px" v-model="areaItems[i]"></el-input-number>㎡
            </el-form-item>
          </el-col>
          <el-col :lg="5" :md="18" :xs="24">
            <el-form-item label-width="120px">
              <el-button  @click="onAreaDelete(i)">删除</el-button>
            </el-form-item>
          </el-col>
       </el-row>

        <div class="mapeditor-title">地铁</div>
        <el-row>
         <el-form-item prop="subway" label="地铁线路" label-width="110px">
          <el-checkbox-group v-model="subway">
            <el-checkbox  label="1">地铁1号线</el-checkbox>
            <el-checkbox  label="2">地铁2号线</el-checkbox>
            <el-checkbox  label="3">地铁3号线</el-checkbox>
            <el-checkbox  label="4">地铁4号线</el-checkbox>
            <el-checkbox  label="5">地铁5号线</el-checkbox>
            <el-checkbox  label="6">地铁6号线</el-checkbox>
            <el-checkbox  label="7">地铁7号线</el-checkbox>
            <el-checkbox  label="8">地铁8号线</el-checkbox>
            <el-checkbox  label="R1">地铁R1号线</el-checkbox>
            <el-checkbox  label="R2">地铁R2号线</el-checkbox>
            <el-checkbox  label="R3">地铁R3号线</el-checkbox>
          </el-checkbox-group>
        </el-form-item>
        </el-row>


      <el-form-item label-width="136px">
        <el-button type="primary" @click="onSubmit">保 存</el-button>
        <el-button type="danger" @click="cancelForm">取 消</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script>

var util=require('@/utils/util')
import ImgUpload from '@/components/common/ImgUpload.vue';
import Tinymce from '@/components/Tinymce'
export default {
  components: {
    Tinymce,
    'img-upload': ImgUpload,
	},
  data() {
    return {
      activeName: '1',
      default_src1:'',
       dialogFormVisible_reply_setting: false,
      form:{
        wxh5:{
          appid:'',
          secret:'',
          token:'',
        },
        seo:{
          site_title:'',
          page_title:'',
          page_keywords:'',
          page_desc:''
        },
        reply_setting:{
          follow_type:1,
          lookup_text:'',
          follow_content:'',
          lookup_content:'',
          Title:'',
          description:'',
          PicUrl:'',
          Url:''
        },



        /* poster_img:'' */
      },
      subway:[],
     cityItems:[''],
     totalItems:[''],
     areaItems:['']
    };
  },
  created(){
    console.log(this.$urlData.region_no)
    this.region_no = this.$urlData.region_no
    this.getInfo()
  },
  methods:{

    onAdd(){
      this.cityItems.push('')
    },
    onDelete(index){
       this.cityItems.splice(index, 1)
    },
    onTotalAdd(){
      this.totalItems.push('')
    },
    onTotalDelete(index){
       this.totalItems.splice(index, 1)
    },
    onAreaAdd(){
       this.areaItems.push('')
    },
    onAreaDelete(index){
      this.areaItems.splice(index,1)
    },

    onUploadedImg(e,key){
      var info = e.res.info
      this.form[key] = info.url;//表单赋值
    },
    getInfo(){
      var that = this
      util.requests("post", {
          url: "city/siteInfo",
          data: {key: ['wxh5','seo','average_area','average_price','total_price','reply_setting','subway'], region_no:this.region_no}
      }).then(res => {
          if(res.code==1){
            var form = {}
            if(res.data.list.length==0){
              res.data.list=[
                {key:'wxh5'},
                {key:'seo'},
                {key:'reply_setting'},
              ]
            }
            for(var i in res.data.list){
              var item = res.data.list[i]
              if(item.key == 'average_price'){
                that.cityItems = item.val
              }
              if(item.key == 'total_price'){
                that.totalItems = item.val
              }
               if(item.key == 'average_area'){
                that.areaItems = item.val
              }
              if(item.key == 'subway'){
                 that.subway = item.val
              }


              if(!item.val){
                if(item.key=='wxh5'){
                  item.val = {
                    appid:'',
                    secret:'',
                    token:'',
                  }
                }
                if(item.key=='seo'){
                  item.val = {
                    site_title:'',
                    page_title:'',
                    page_keywords:'',
                    page_desc:''
                  }
                }
                 if(item.key=='reply_setting'){
                  item.val = {
                    follow_type:1,
                    lookup_text:'',
                    follow_content:'',
                    lookup_content:'',
                    Title:'',
                    description:'',
                    PicUrl:'',
                    Url:''
                  }
                }
              }
              form[item.key] = item.val
            }

          if(item.key == 'reply_setting'){
            form['reply_setting']['follow_type'] = String(item.val.follow_type)
          }
          console.log(form)
        console.log( form['reply_setting']['follow_type'] )
            that.setDataArr({
              form : form,
              //default_src1: form.poster_img
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    onSubmit(){
      var that = this

      util.requests('post',{
        url:'city/siteEdit',
        data:{key:{...that.form}, region_no:that.region_no,cityItems:that.cityItems,areaItems:that.areaItems,totalItems:that.totalItems,subway:that.subway}
      }).then(res=>{
        {
          if(res.code==1){
            that.getInfo()
            util.Message.success(res.msg)
          }else{
            util.Message.error(res.msg);
          }
        }
      });
    },
    cancelForm(){
      this.$refs['form'].resetFields();
      util.openPage({url:-1,hreftype:'navigateBack'})
    }
  }
};
</script>
<style lang="scss" scoped>
._container {
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

}
</style>


