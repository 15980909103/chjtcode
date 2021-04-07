<template>
  <div class="mapeditor_container">
    <el-form :model="formData" :rules="rules" ref="formData">
      <el-form-item>
        <div class="mapeditor-title">基础信息</div>
        <el-row class="mapeditor-content">
            <el-col :lg="8" :sm="12" :xs="24">
              <el-form-item prop="name" label="商品名称" label-width="110px">
                <el-input placeholder="请输入商品名称" v-model="formData.name"></el-input>
              </el-form-item>
            </el-col>
            <el-col :lg="8" :sm="12" :xs="24">
              <el-form-item prop="price" label="价格" label-width="110px">
                <el-input :disabled='inputDisabled' placeholder="请输入规格价格" v-model="formData.price"></el-input>
              </el-form-item>
            </el-col> 
            <el-col :lg="8" :sm="12" :xs="24">
              <el-form-item prop="commission" label="佣金" label-width="110px">
                <el-input :disabled='inputDisabled' placeholder="请输入佣金" v-model="formData.commission">
                  <el-select style="width: 110px;" v-model="formData.commission_type" slot="prepend" placeholder="请选择">
                    <el-option label="比例类型" value="0"></el-option>
                    <el-option label="金额类型" value="1"></el-option>
                  </el-select>
                </el-input>
              </el-form-item>
            </el-col> 
        </el-row>
        <el-row>
          <el-col :lg="8" :sm="12" :xs="24" style="display:none;">
            <el-form-item prop="commission" label="类型" required label-width="110px">
              <span style="margin-left: 15px;">
                <el-radio v-model="formData.type" label="0">管理类型</el-radio>
                <el-radio v-model="formData.type" label="1">记账类型</el-radio>
              </span>
            </el-form-item>
          </el-col>
          <el-col :lg="8" :sm="12" :xs="24">
            <el-form-item  label="排序"  label-width="120px">
              <el-input  placeholder="越大越靠前" v-model="formData.sort"></el-input>
            </el-form-item>
          </el-col>

          <el-col :lg="8" :sm="12" :xs="24" style="display:none;">
            <el-form-item label="状态" label-width="120px">
              <el-select style="width:100%;" v-model="formData.status" placeholder="请选择">
                <el-option label="禁用" value="0"></el-option>
                <el-option label="启用" value="1"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>

        <!-- <el-button v-if="formData.id==1" @click="addAttr" style="margin-left: 126px;" type="primary" icon="el-icon-circle-plus-outline" size="mini">规格添加</el-button> -->

        <el-row class="mapeditor-content attr-box" v-for="(item,index) in formData.attr" :key="index">
            <el-col :lg="6" :sm="12" :xs="24">
              <el-form-item label="规格名称" label-width="95px" :prop="'attr.' + index + '.attr_name'"
    :rules="{ required: true, message: '规格名称不能为空', trigger: 'blur' }">
                <el-input placeholder="请输入商品名称" v-model="formData.attr[index].attr_name"></el-input>
              </el-form-item>
            </el-col>
            <el-col :lg="6" :sm="12" :xs="24">
              <el-form-item label="价格"  label-width="60px" :prop="'attr.' + index + '.attr_price'"
    :rules="[{ required: true, message: '规格价格不能为空', trigger: 'blur' }]">
                <el-input placeholder="请输入规格价格" v-model="formData.attr[index].attr_price"></el-input>
              </el-form-item>
            </el-col> 
            <el-col :lg="6" :sm="12" :xs="24">
              <el-form-item label="级别等级"  label-width="80px" :prop="'attr.' + index + '.level'"
    :rules="[{ required: true, message: '级别为数字', trigger: 'blur' }]">
                <el-input :disabled='true' placeholder="请输入级别" v-model="formData.attr[index].level"></el-input>
              </el-form-item>
            </el-col>
            <el-col :lg="6" :sm="12" :xs="24">
              <el-form-item label="佣金"  label-width="60px" :prop="'attr.' + index + '.attr_commission'"
    :rules="[{ required: true, message: '规格佣金不能为空', trigger: 'blur' }]">
                <el-input placeholder="请输入规格佣金" v-model="formData.attr[index].attr_commission">
                  <el-select style="width: 110px;" v-model="formData.attr[index].attr_commission_type" slot="prepend" placeholder="请选择">
                    <el-option label="比例类型" value="0"></el-option>
                    <el-option label="金额类型" value="1"></el-option>
                  </el-select>
                </el-input>
              </el-form-item>
            </el-col> 
            <!-- <i class="el-icon-circle-close" @click="removeAttr(index)"></i> -->
        </el-row>
      </el-form-item>


      <el-form-item>
        <div class="mapeditor-title">
          图片上传
          <span>(建议文件小于3M)</span>
        </div>
        <el-row class="mapeditor-content">
            <el-col :lg="24" :sm="24" :xs="24">
              <el-form-item prop="cover" label="封面图" label-width="90px">
                <img-upload ref="imgUpload" :default_src.sync='default_src1' url="Upload/imgUpload"  :uploadedImg="onUploadedImg" :thumb="{isthumb:1,width:480,height:480}"></img-upload>
              </el-form-item>
            </el-col>
        </el-row>
      </el-form-item>

      <el-form-item>
        <div class="mapeditor-title">详情信息</div>
        <el-row class="mapeditor-content">
            <el-col :lg="16" :sm="20" :xs="24">
              <el-form-item prop="content" label="内容详情" required label-width="90px">
                <Tinymce ref="editor" :height="400" v-model="formData.content" />
              </el-form-item>
            </el-col>
        </el-row>
      </el-form-item>

      <el-form-item style="text-align:center" >
        <el-button type="primary" icon="el-icon-success" @click="submitForm('formData')">保存</el-button>
        <el-button  icon="el-icon-refresh" v-if="!formData.id" @click="resetForm('formData')">重置</el-button>
        <el-button type="danger" icon="el-icon-circle-close" @click="openPage({url: -1, hreftype: 'navigateBack'})">返回</el-button>
      </el-form-item>
    </el-form>

  </div>
</template>
<script>
var util = require("@/utils/util.js");
import ImgUpload from '@/components/common/ImgUpload.vue';
import Tinymce from '@/components/Tinymce'
var validateNumber = (rule, value, callback) => {
  if (parseFloat(value).toString() == 'NaN') { 
　　　　callback(new Error('排序必须是数字'));
　　　　return false; 
　　}  

  callback();
};

export default {
  components: {
    Tinymce,
    'img-upload': ImgUpload,
	},
  data() {
    return {
      formData: {
        id: '',
        name: '',
        type: '0',
        cover: '',
        price: 0,
        commission:0,
        commission_type:'0',
        sort:0,
        content:'',
        attr:[],
        status:'0',
      },
      rules: {
        name: [{ required: true, message: "请输入商品名称" }],
        sort: [{ trigger: 'blur', validator: validateNumber,  message: "请输入数字" }],
        cover: [{ required: true, message: "请上传图片" }],
        content: [{ required: true, message: "请输入详情" }],
        price: [{required: true, trigger: 'blur', validator: validateNumber,  message: "请输入数字" }],
        commission: [{required: true, trigger: 'blur', validator: validateNumber,  message: "请输入数字" }],
      },

      default_src1:'',
      page_loading: false,
    };
  },
  computed:{
    inputDisabled(){
      var isdisabled = this.formData.attr.length>0?true:false;
      if(isdisabled==true){
        this.rules.price= [{ trigger: 'blur', validator: validateNumber,  message: "请输入数字" }];
        this.rules.commission= [{ trigger: 'blur', validator: validateNumber,  message: "请输入数字" }];
      }else{
        this.rules.price= [{required: true, trigger: 'blur', validator: validateNumber,  message: "请输入数字" }];
        this.rules.commission= [{required: true, trigger: 'blur', validator: validateNumber,  message: "请输入数字" }];
      }
      return isdisabled
    }
  },
  created: function(){
    if(this.$urlData&&this.$urlData.id){
      var isMiss= this.doMissId(this.$urlData.id)
      if(!isMiss){
        return
      }
      //修改操作
      this.formData.id = this.$urlData.id
      this.getInfo(this.formData.id)
    }else{
      //添加操作
      this.resetForm('formData')
    }
  },
  methods: {
    addAttr(){
      if (parseFloat(this.formData.price).toString() == 'NaN') { 
         util.Message.error('价格请输入数字');
  　　　　return false; 
  　　}
      if (parseFloat(this.formData.commission).toString() == 'NaN') { 
        util.Message.error('佣金请输入数字');
  　　　　return false; 
  　　}

      this.formData.attr.push({
        attr_name:'',
        attr_commission: 0,
        attr_commission_type: '0',
        attr_price: 0,
      })
      
    },
    removeAttr(index){
      this.formData.attr.splice(index,1);
    },
   getInfo(id){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "mall/getGoodsInfo",
          data: {id: id}
      }).then(res => {
          that.page_loading = false
          if(res.code==1){
            var formData = res.data 

            if(!formData.attr){
              formData.attr=[]
            }   
            for(var i in formData.attr){
              formData.attr[i].attr_commission_type = String(formData.attr[i].attr_commission_type)
            }
            formData.status = String(formData.status)
            formData.type = String(formData.type)
            formData.commission_type = String(formData.commission_type)

            that.setDataArr({ 
              formData: formData,
              default_src1: that.$getRealImgUrl(formData.cover), 
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
          name: '',
          type: '0',
          cover: '',
          price: 0,
          commission:0,
          commission_type:'0',
          sort:0,
          content:'',
          attr:[],
          status:'0',
        },
        default_src1:'',
      })
      if(that.$refs.imgUpload){
        that.$refs.imgUpload.resetData() 
      }
      if(that.$refs.editor){//重置富文本信息
        that.$refs.editor.setContent('')
      }     
    },
    //图片上传后操作
    onUploadedImg(e){
      var info = e.res.info
      this.formData['cover'] = info.url;//表单赋值
    },
    
    submitForm(formName) {
      let that = this;
      if(that.formData.attr&&that.formData.attr.length>0){
        var attrnameArr=[]
        for(var i in that.formData.attr){
          var item = that.formData.attr[i]
          attrnameArr.push(item.attr_name)
        }
        let newArr = new Set(attrnameArr);
        newArr= Array.from(newArr);
        if(attrnameArr.length!=newArr.length){
          util.Message.error('规格名称出现重复');
          return
        }
      }
      
      that.$refs[formName].validate((valid) => {
        valid = true
        if (valid) {
          if(that.page_loading){
              return
          }
          that.page_loading = true
          
          util.requests("post",{
            url: '/mall/goodsEdit',
            data: that.formData
          }).then(function(res){
            console.log(res);
            that.page_loading = false
            
            if(res.code==1){
              util.Message.success('操作成功');
            }else{
              util.Message.error(res.msg);
            }
          })
        } else {
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
      margin-bottom: 25px;
    }
  }
  .attr-box{
      position: relative;
      /deep/.el-icon-circle-close{
        position: absolute;
        top: 19px;
        left: 27px;
        font-size: 20px;
        color: #b3b3b3;
      }
    }
}

</style>


