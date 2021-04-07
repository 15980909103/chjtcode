<template>
  <div class="_container">
    <div class="tb-top">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-button icon="el-icon-back" @click="openPage({url:-1,hreftype:'navigateBack'})">返回</el-button>
      </el-form>
    </div>

    <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增店铺</el-button>

    <!-- 表格 -->
    <el-table class="tb-title"  :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column prop="shop_name" label="店铺名" align="center"></el-table-column>
      <el-table-column prop="shop_type_string" label="经营类型" align="center"></el-table-column>
      <el-table-column prop="shop_lable_string" label="优惠标签" align="center"></el-table-column>
      <el-table-column prop="send_coupon_num" label="店铺发放数量" align="center"></el-table-column>
      <el-table-column prop="coupon_surplus_num" label="剩余数量" align="center"></el-table-column>
<!--      <el-table-column prop="type" label="活动时间"  align="center" width="280">-->
<!--        <template slot-scope="scope">-->
<!--          {{getFormatDate(scope.row.start_time)}} &#45;&#45; {{getFormatDate(scope.row.end_time)}}-->
<!--        </template>-->
<!--      </el-table-column>-->

      <el-table-column prop="opt" label="操作" align="center">
        <template slot-scope="scope">
          <el-button type="success" size="mini" @click="doEdit({...scope.row})">编辑</el-button>
<!--          <el-button type="danger" size="mini" @click="del({id:scope.row.id},scope.$index)">删除</el-button>-->
        </template>
      </el-table-column>
    </el-table>

    <!-- 新增弹窗部分 -->
   <el-dialog
      :title="formData.id?'编辑':'新增'"
      :visible.sync="dialogVisibleEdit"
      width="1200px"
      :close-on-click-modal="false"
      @close="doEditCancel('formData')"
    >
      <el-form style="padding-right:50px;" :model="formData" ref="formData" :rules="rules">
          <el-form-item label="门店图" :label-width="formLabelWidth" ref="shop_img" prop="shop_img">
            <img-upload2 ref="imgUpload" url="upload/imgUpload" :limit='1' :thumb='{isthumb:1,width:1200,height:850}' :show-file-list="false" :fileList.sync="formData.shop_img"  ></img-upload2>
            <!-- <img-upload ref="imgUpload" url="upload/imgUpload" :thumb='{isthumb:1,width:750,height:750}' :default_src.sync='default_src' :uploadedImg="onUploadedImg" ></img-upload> -->
          </el-form-item>
          <el-form-item label="序号" :label-width="formLabelWidth">
            <el-input  v-model="formData.sort" placeholder="请输入排序越大越靠前"></el-input>
          </el-form-item>

          <el-form-item label="店铺名称" :label-width="formLabelWidth">
            <el-input  v-model="formData.shop_name" placeholder="请输入店铺名称"></el-input>
          </el-form-item>
            <el-col :span="24">
              <el-form-item label="绑定核销人员" :label-width="formLabelWidth"  prop="user_ids">
                <el-select
                  style="width: 100%"
                  v-model="formData.user_ids"
                  filterable
                  remote
                  multiple
                  reserve-keyword
                  placeholder="请输入昵称搜索"
                  :remote-method="remoteMethod"
                  :loading="loading">
                  <el-option
                    v-for="item in user_list"
                    :key="item.id"
                    :label="item.nickname"
                    :value="item.id">
                  </el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-form-item label="经营类型" :label-width="formLabelWidth">
               <el-input  v-model="formData.shop_type_string" placeholder="请输入店铺经营类型"></el-input>
           </el-form-item>

<!--          <el-form-item label="店铺可建优惠券数量" :label-width="formLabelWidth">-->
<!--            <el-input  v-model="formData.create_coupon_num" placeholder="请输入店铺经营类型" @blur="setCoupon"></el-input>-->
<!--          </el-form-item>-->

        <el-form-item label="店铺总发放量" :label-width="formLabelWidth">
          <el-input  v-model="formData.send_coupon_num" placeholder="请输入店铺经营类型"></el-input>
        </el-form-item>

          <el-form-item label="优惠标签" :label-width="formLabelWidth">
            <el-input  v-model="formData.shop_lable_string" placeholder="请输入店铺优惠标签"></el-input>
          </el-form-item>


        <el-form-item>
          <div class="mapeditor-title"> 优惠券信息</div>
        </el-form-item>
          <div v-for="(tiem,index) in formData.couponlist">
            <el-row >
              <el-form-item label="优惠信息" :label-width="formLabelWidth">
                <el-col :span="5">
                  <el-input  v-model="formData.couponlist[index].coupon_describe" placeholder="如满100减20   或   满100打9折">
<!--                    <template slot="prepend">满</template>-->
<!--                    <template slot="append">元</template>-->
                  </el-input>
                </el-col>
                <el-col :span="5">
                  <el-input  v-model="tiem.coupon_send_unm" placeholder="数量">
                    <template slot="prepend">发放</template>
                    <template slot="append">张</template>
                  </el-input>
                </el-col>
                <el-col :span="10">
                    <el-date-picker
                          v-model="tiem.time"
                          type="datetimerange"
                          value-format="yyyy-MM-dd HH:mm:ss"
                          range-separator="至"
                          start-placeholder="开始日期"
                          end-placeholder="结束日期">
                      </el-date-picker>
                </el-col>
                <el-col :span="4">
                  <el-button type="danger" size="mini" @click="delCouponList(index)">删除</el-button>
                  <el-button  size="mini" type="primary" @click="editRuleText(tiem)">规则说明</el-button>
                </el-col>
                
              </el-form-item>
            </el-row>
          </div>

        <el-button type="success" size="mini" @click="couponlistAdd">添加</el-button>




      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="doSubmit('formData')">确 定</el-button>
        <el-button type="danger" @click="doEditCancel('formData')">取 消</el-button>
      </div>
    </el-dialog>

    <el-dialog
      title="规则编辑"
      :visible.sync="dialogRuleTextEdit"
      width="800px"
      :close-on-click-modal="false"
      @close="ruleTextEditCancel()"
    >
      <el-form style="padding-right:50px;" :model="formData_rule" ref="formData_rule" :rules="rules">
        
        <el-form-item label="状态" :label-width="formLabelWidth">
           <el-radio-group v-model="formData_rule.enble_contextrule">
             <el-radio  label="1">启用</el-radio>
             <el-radio  label="0">禁用</el-radio>
           </el-radio-group>
        </el-form-item>

        <el-form-item prop="context_rule" label="活动规则" required label-width="80px">
                <Tinymce ref="editor" :height="400"   v-model="formData_rule.context_rule" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="ruleTextEditSubmit()">确 定</el-button>
        <el-button type="danger" @click="ruleTextEditCancel()">取 消</el-button>
      </div>
    </el-dialog>
    
  </div>
</template>
<script>
// import { log } from 'util';
var util = require("@/utils/util.js");
import ImgUpload from '@/components/common/ImgUpload.vue';
import DyTags from '@/components/common/DyTags.vue';
import baseMixin from  '@/mixin/baseMixin';
import EstatesNew from '@/components/InnerTable/EstatesNew.vue';
import LiveRoom from '@/components/InnerTable/LiveRoom.vue';
import ImgUpload2 from '@/components/common/ImgUpload2.vue';
import Tinymce from '@/components/Tinymce';

export default {
  components: {
    'img-upload': ImgUpload,
    'estates-new': EstatesNew,
    'live-room': LiveRoom,
    'img-upload2': ImgUpload2,
    'Tinymce' : Tinymce
  },
  mixins: [baseMixin],

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
      activeTabs:'',
      activeTabsName:'',
      dialogVisibleEdit: false,
      formLabelWidth: "123px",
      src:'',
      default_src:'',
      page_loading:'',
      thumb:{},
      rules: {
        //cover: [{ validator: validateImg,  message: "请上传图片" }],
      },

      searchData:{
          module:'',
      },
      tableData: [],
      formData_rule:{
        coupon_id:'',
        context_rule: '',
        enble_contextrule: '0',
      },
      formData:{
        id:'',
        activity_id:'',
        sort:'1',
        shop_img:[],
        shop_name:'',
        shop_type_string:'',
        shop_lable_string:'',
        couponlist:[{
          id:'',
          coupon_describe:'',
          coupon_send_unm:'',
          time:'',
        }],
        user_ids:[],
        send_coupon_num:1,
        create_coupon_num:1,
        start_time:'',
        end_time:'',
      },
      activity_id:'',
      moduleList:[],
      user_list:[],
      innerVisible:{ },
      loading:false,
      listData:{},

      dialogRuleTextEdit: false,
    }
  },

  created: function(){
    var that = this

    that.formData.region_no = that.$urlData.region_no
    that.setDataArr({
      activity_id: that.$urlData.activity_id,
      searchData:{
        activity_id: that.$urlData.activity_id,
      },
    })
    that.formData.activity_id = that.activity_id;
    if(!that.activity_id){
      this.$alert('缺失参数', '提示',{
        confirmButtonText: '确定',
        callback: action => {
          util.openPage({url:'/coupon/couponlist'});
        }
      });
      return;
    }

    that.getConstModules()

    that.resetData({
        formData: that.formData,
        default_src: ''
      },function(){
        that.$nextTick(()=>{
          that.$refs.imgUpload&&that.$refs.imgUpload.resetData() //重置图片信息
        })
      })
      that.getList(that.searchData)

  },

  methods:{
    tabsClick(){

    },
    remoteMethod(query) {

      let that  = this;
      if (query !== '') {
        let searchData= {
          user_nickname:query
        }
        this.loading = true;
        util.requests("post", {
          url: "user/list",
          data:searchData,
        }).then(res => {
          if(res.code==1){
            that.setDataArr({
              user_list : res.data.list,
            })
            this.loading = false;
          }else{
            this.user_list =[];
            util.Message.error(res.msg);
          }
        });
      } else {
        this.user_list = [];
      }
    },
    getConstModules(){
      let that = this
      util.requests("post",{
        url:"vote/getConstModules",
      }).then(res=>{
        //console.log(res.data)
        that.moduleList = res.data

        let innerVisible={}
        for(var i in that.moduleList){
          if(that.activeTabs==0){
            that.activeTabs = i
            //console.log(i)
          }
          innerVisible[i] = false
        }
        that.innerVisible = innerVisible
      })
    },
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      util.requests("post",{
        url:"CouponActivity/getShopList",
        data: searchdata
      }).then(res=>{
        that.tableData = res.data.list;
        // that.setDataArr({
        //   listData: Object.assign(that.tableData,listData),
        // })
      })
    },
    getFormatDate(val){
      return util.DataFun.getFormatDate(val,2)
    },

    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },

    doEdit(e={}){
      if(Object.keys(e).length>0){
        this.formData = Object.assign({},e);
        this.formData.start_time = this.getFormatDate(this.formData.start_time)
        this.formData.end_time   =  this.getFormatDate(this.formData.end_time)
        this.user_list           = this.formData.user_list;

      }
      console.log(this.formData,21312);
      this.dialogVisibleEdit = true;
    },
    doEditCancel(formName){
      var that=this
      that.$refs[formName].resetFields()
      that.resetData()
      if(that.dialogVisibleEdit == true){
        that.dialogVisibleEdit = false
      }
    },

    //图片上传后操作
    onUploadedImg(e){
      this.formData.cover = e.res.info.url;
      this.$refs.cover.clearValidate()
    },
    onSearch(){
      this.getList(this.searchData);
    },


  del(e,val){   //确定删除
      let url = "CouponActivity/del";
      let post_data = {id: e.id}
      this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          util.requests("post",{
            url: url,
            data: post_data,
          }).then(res => {
              // console.log(res); return;
              if(res.code==0){
                this.$message({
                  type: 'error',
                  message: res.msg
                });
                return;
              }
              this.tableData.splice(val,1)
              this.$message({
                type: 'success',
                message: '删除成功!'
              });
          })
        })
  },

    // setCoupon(e){
    //  let that =this;
    //   if(that.formData.create_coupon_num < 1 ){
    //     that.$message({ type: 'error', message: '不能小于等于1哦' });
    //     return ;
    //   }
    // },
    delCouponList(index){
      let that = this;
      if( !that.formData.couponlist[index]){
        util.Message.success('参数错误');
      }
      if(that.formData.couponlist.length<=1){
        util.Message.error('最少保留一个哦');
        return false;
      }
      if(that.formData.couponlist[index].id == '' || that.formData.couponlist[index].id === null){
          that.formData.couponlist.splice(index,1);
      }else{
        this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          util.requests("post",{
            url: 'CouponActivity/delCoupon',
            data: {id:that.formData.couponlist[index].id},
          }).then(res => {
            // console.log(res); return;
            if(res.code==0){
              this.$message({
                type: 'error',
                message: res.msg
              });
              return;
            }
            that.formData.couponlist.splice(index,1);
            this.$message({
              type: 'success',
              message: '删除成功!'
            });
          })
        })
        //todo 发送请求
      }
    },
    editRuleText(e){  
      let that = this
      if(that.page_loading==true){
        return
      }
      that.page_loading = true;
      
      util.requests("post", {
          url: "CouponActivity/getCouponRule",
          data: {coupon_id: e.id}
        }).then(res => {
          that.page_loading = false
          
          let coupon_id = res.data.id;
          delete res.data.id;
          that.formData_rule = res.data;
          that.formData_rule.coupon_id = coupon_id
          
          that.formData_rule.enble_contextrule = that.formData_rule.enble_contextrule? String(that.formData_rule.enble_contextrule) : '0';
          that.formData_rule.context_rule = that.formData_rule.context_rule?that.formData_rule.context_rule:''
          if(this.$refs.editor){//重置富文本信息
            this.$refs.editor.setContent(that.formData_rule.context_rule);
          } 
          
          that.dialogRuleTextEdit = true;
      });
    },
    ruleTextEditSubmit(){
      let that = this
      if(that.page_loading==true){
        return
      }
      that.page_loading = true;
      
      util.requests("post", {
          url: "CouponActivity/editCouponRule",
          data: that.formData_rule
        }).then(res => {
          that.page_loading = false
          
          if(res.code==1){
               that.$message({ type: 'success', message: '操作成功!' });
               setTimeout(()=>{
                  that.ruleTextEditCancel()
                },1200)
            }else{
              that.$message({
                type: 'error',
                message: res.msg
              });
            }
      });
    },
    ruleTextEditCancel(){
      this.formData_rule = {
        coupon_id:'',
        context_rule: '',
        enble_contextrule: '0',
      };
      
      this.dialogRuleTextEdit = false;
    },

    couponlistAdd(){
      let that =this;
      // if(that.formData.create_coupon_num <= that.formData.couponlist.length){
      //   that.$message({ type: 'error', message: '优惠券数量不能超过限制数量' });
      //   return false ;
      // }
      let  obj = {
        id:'',
        coupon_describe:'',
        coupon_send_unm:'',
        time:'',
      }
      that.formData.couponlist.push(obj);
    },
    doSubmit(formName){
      var that = this
      //that.formData.vote_id = that.vote_id

      that.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.page_loading){
            return;
          }
          that.page_loading = true;

          let url = "CouponActivity/editCoupon";
          util.requests("post",{
            url: url,
            data: that.formData
          }).then(res=>{
            that.page_loading = false
            //console.log(res)
            if(res.code==1){
               that.$message({ type: 'success', message: '操作成功!' });
               that.dialogVisibleEdit = false;
               that.onSearch();
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
    openPage: util.openPage,

    changeInnerShow(){
      this.innerVisible[this.activeTabs] = true
      console.log(this.innerVisible)
    },
    innerFormData(e){
      console.log(e)
      this.formData.forid = e.id
      this.formData.forname = e.name

      //console.log(this.formData,999999999999)
    },
    clearInner(){
      this.formData.forname = '';
      this.formData.forid = 0;
    },

  }
};
</script>
<style lang="scss" scoped>
  .form-serch {
    text-align: right;
  }
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


