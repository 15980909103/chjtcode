<template>
  <div class="_container">
    <div class="tb-top" style="float:right;margin-bottom: 30px;">
      <el-form :inline="true" :model="searchData" class="form-serch" >
        <el-form-item label="状态">
          <el-select v-model="searchData.status" placeholder="请选择">
             <el-option label="全部" value="-1"></el-option>
            <el-option label="禁用" value="0"></el-option>
            <el-option label="启用" value="1"></el-option>
          </el-select>
        </el-form-item>

        <el-form-item>
          <mycity-select :city_no.sync='searchData.region_no' :unlimitedCity='true' :isMy='true' model='3' siteAreasUrl='city/siteAreas'></mycity-select>
        </el-form-item>

        <el-button type="primary" icon="el-icon-search" @click="onSearch">查询</el-button>
        <el-button type="danger" icon="el-icon-circle-plus-outline" @click="doEdit()">新增</el-button>
      </el-form>
    </div>
    <!-- 表格 -->
    <el-table class="tb-title" :data="tableData" style="width: 100%">
      <el-table-column prop="id" label="ID" width="70" align="center"></el-table-column>
      <el-table-column label="用户头像" align="center">
        <template slot-scope="scope">
           <el-image style="width:80px;height:80px;" :src="getRealImgUrl(scope.row.headimgurl)"></el-image>
        </template>
      </el-table-column>
      <el-table-column prop="nickname" label="用户昵称" width="150" align="center"></el-table-column>
      <el-table-column prop="realname" label="真实姓名" width="150" align="center"></el-table-column>
      <el-table-column prop="phone" label="手机号码" width="150" align="center"></el-table-column>
      <el-table-column prop="create_time" label="时间" width="150" align="center"></el-table-column>


      <el-table-column prop="opt" label="操作" align="center">
        <template slot-scope="scope">
          <el-button type="success" size="mini" @click="doEdit(scope.row)">编辑</el-button>
          <el-button type="danger" size="mini" @click="del(scope.row.id,scope.$index)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <!-- 新增弹窗部分 -->
   <el-dialog
      :title="formData.id?'编辑':'新增'"
      :visible.sync="dialogVisibleEdit"
      width="860px"
      :close-on-click-modal="false"
      @close="doEditCancel('formData')"
    >
      <el-form style="padding-right:50px;" :model="formData" ref="formData" :rules="rules">
        <el-row>
          <el-col :span="24">
            <el-form-item label="买房目的" prop="purpose" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.purpose" placeholder="" :disabled="true"></el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="24">
            <el-form-item label="是否首套" prop="has_num" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.has_num" placeholder="" :disabled="true"></el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="24">
            <el-form-item label="预算" prop="price" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.price" placeholder="" :disabled="true"></el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="24">
            <el-form-item label="想买的区域" prop="region" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.region" placeholder="" :disabled="true"></el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="24">
            <el-form-item label="想住几居室" prop="rooms" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.rooms" placeholder="" :disabled="true"></el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="24">
            <el-form-item label="想买多大面积" prop="built_area" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.built_area" placeholder="" :disabled="true"></el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="24">
            <el-form-item label="其他偏好" prop="feature_tag" :label-width="formLabelWidth">
              <el-input style="width:100%" v-model="formData.feature_tag" placeholder="" :disabled="true"></el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="24">
            <el-form-item label="其他要求" prop="other_requirements" :label-width="formLabelWidth">
              <el-input type="textarea" autosize style="width:100%" v-model="formData.other_requirements" placeholder="" :disabled="true"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        
      </el-form>
      <!-- <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="doSubmit('formData')">确 定</el-button>
        <el-button type="danger" @click="doEditCancel('formData')">取 消</el-button>
      </div> -->
    </el-dialog>


  </div>
</template>
<script>
// import { log } from 'util';
var util = require("@/utils/util.js");
import DyTags from '@/components/common/DyTags.vue';
import baseMixin from  '@/mixin/baseMixin';
import constMixin from  '@/mixin/constMixin';
import paginationBox from '@/components/common/pagination.vue';
import MycitySelect from '@/components/common/MycitySelect.vue';

export default {
  components: {
    'mycity-select': MycitySelect,
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
      dialogVisibleEdit: false,
      formLabelWidth: "123px",
      src:'',
      page_loading:'',
      rules: { 

      },
      searchTime:[],  
      searchData:{   
          status : '-1',
          region_no: '',
      },
      tableData: [],   
      formData:{
        id: 0, 
        status:'0',   
        type:'1',
        name:"",
        sort:"",
        city:"",
        bind_id:"",
        purpose_type:1,
      },

      innerVisible: false,
      innerVisibleTag: false,
    }
  },
  watch: {
    
  },
  created: function(){
    this.user_id = this.$urlData.user_id
    console.log(121,this.user_id)
    let that = this
    that.resetData({
      formData: that.formData,
    },function(){
      
    })

    that.getList(that.searchData)
  },

  methods:{
    // 获取列表
    getList(searchdata={}){   //获取所有数据，或按条件查找数据
      var that = this
      searchdata.user_id = that.user_id
      util.requests("post",{
        url:"SelectLog/getList",
        data: searchdata
      }).then(res=>{
        //console.log(res.data.list)
        that.tableData = res.data.list
      })
    },
    // 新增/编辑弹框
    doEdit(e={}){
      if(Object.keys(e).length>0){
        this.formData = Object.assign({},e);
        this.formData.status = String(this.formData.status)
        this.formData.type = String(this.formData.type)
      }
      this.dialogVisibleEdit = true;
    },
    // 取消弹框
    doEditCancel(formName){
      var that=this
      that.$refs[formName].resetFields()
      that.resetData()
      if(that.dialogVisibleEdit == true){
        that.dialogVisibleEdit = false
      }
    },
    // 删除
    del(id,val){   //确定删除
        this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
          }).then(() => {
            util.requests("post",{
              url: "SelectLog/delete",
              data: {id:id}
            }).then(res => {
                // console.log(res); return;
                if(res.data.code==0){ alert("删除失败："+res.data.msg);return; }
                this.tableData.splice(val,1)
                this.$message({
                  type: 'success',
                  message: '删除成功!'
                });
            })
          })
    },
    openPage: util.openPage,
    // 搜索
    onSearch(){
      this.getList(this.searchData);
    },
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },
  }

};
</script>
<style lang="scss" scoped>
  .tb-title{
    margin-top: 40px;
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


