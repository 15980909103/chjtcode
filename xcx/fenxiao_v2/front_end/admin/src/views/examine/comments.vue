<template>
  <div class="mapmange_container">
    <div class="mapmange_content">
      <el-form :inline="true" :model="searchData" class="form-serch">
        <el-form-item>
           <el-date-picker
            style="width:380px;"
            v-model="searchTime"
            value-format="yyyy-MM-dd"
            type="daterange"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期">
          </el-date-picker>
        </el-form-item>

        <el-form-item>
          <el-input v-model="searchData.object_name" placeholder="请输入文章标题" prefix-icon="el-icon-search"></el-input>
        </el-form-item>

        <el-form-item label="审核状态">
          <el-select v-model="searchData.status" placeholder="请选择">
            <el-option label="全部" value="-1"></el-option>
            <el-option label="待审核" value="0"></el-option>
            <el-option label="已通过" value="1"></el-option>
            <el-option label="未通过" value="2"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
        </el-form-item>
      </el-form>

      <!-- ========================================================= -->
      <el-table :data="tableData" style="width: 100%">
        <el-table-column prop="id" label="ID" width="80" align="center"></el-table-column>
        <el-table-column prop="object_name" label="文章标题" align="center"></el-table-column>
        <el-table-column prop="grade" label="评分"  align="center">
          <template slot-scope="scope">
          <el-rate v-model="scope.row.grade" :max="5" disabled ></el-rate>
          </template>
        </el-table-column>
        <el-table-column label="提交时间" align="center">
          <template slot-scope="scope">
           <span>{{getFormatDate(scope.row.create_time,2)}}</span>
          </template>
        </el-table-column>
        <el-table-column  label="审核时间"  align="center">
          <template slot-scope="scope">
           <span>{{getFormatDate(scope.row.check_time,2)}}</span>
          </template>
        </el-table-column>
   
        <el-table-column label="状态" width="80" align="center">
          <template slot-scope="scope">
            <el-tag v-if="scope.row.status=='-1'"  effect="plain">全部</el-tag>
            <el-tag v-if="scope.row.status=='0'"  effect="plain">待审核</el-tag>
            <el-tag v-if="scope.row.status=='1'"  type="success" effect="dark">已通过</el-tag>
            <el-tag v-if="scope.row.status=='2'" type="info" effect="dark">未通过</el-tag>
          </template> 
        </el-table-column>


        <el-table-column label="操作"  align="center">
          <template slot-scope="scope">
            <el-button v-if="scope.row.status=='0'" type="primary" size="mini" @click="doEdit('edit',{id: scope.row.id})">编辑</el-button>
            <el-button v-else type="primary" size="mini" @click="doEdit('see',{id: scope.row.id})">查看</el-button>
          </template>
        </el-table-column>
      </el-table>
      <!-- ========================================================= -->

      <!-- ============分页=============== -->
      <pagination-box :pagination="pagination" @pageChange="pageChange" ></pagination-box>

       <!-- 编辑弹窗 -->
      <el-dialog v-if="dialogFormVisible"
        title="审核"
        :visible.sync="dialogFormVisible"
        width="800px"
        :close-on-click-modal="false"
        @close="doEditCancel"
        class="dialog-examine"
      >
        <el-form style="padding-right:50px;" :model="form" ref="form" :rules="rules">
          <el-form-item label="用户昵称" :label-width="formLabelWidth" >
            <div>
              <img v-if="form.avatar" class="avatar" :src="getRealImgUrl(form.avatar)"/>
              <span style="vertical-align: text-bottom;">{{form.user_nickname}}</span>
            </div>
          </el-form-item>

          <el-form-item label="文章标题" :label-width="formLabelWidth" >
            <el-input v-model="form.object_name" disabled></el-input>
          </el-form-item>
          <el-form-item label="评分" :label-width="formLabelWidth" >
            <el-input v-model="form.grade" disabled></el-input>
          </el-form-item>
          <el-form-item label="评论内容" :label-width="formLabelWidth">
            <el-input v-model="form.content" disabled></el-input>
          </el-form-item>

          <el-divider><i class="el-icon-news"></i></el-divider>

          <el-form-item label="审核状态" :label-width="formLabelWidth" prop="status" >
              <div v-if="formtype=='edit'">
                <el-radio-group v-model="form.status">
                  <el-radio label="2">未通过</el-radio>
                  <el-radio label="1">已通过</el-radio>  
                </el-radio-group>  
              </div>
              <div v-else>
                <el-radio-group v-model="form.status">
                  <el-radio label="2">未通过</el-radio>
                  <el-radio label="1">已通过</el-radio>  
                </el-radio-group>  
              </div>      
          </el-form-item>
          <el-form-item v-if="form.status=='1'" label="官方回复" :label-width="formLabelWidth" prop="reply">
            <el-input type="textarea"
  :autosize="{ minRows: 2, maxRows: 4}" v-model="form.reply"></el-input>
          </el-form-item>

        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button type="primary" @click="doEditSubmit">确 定</el-button>
          <el-button type="danger" @click="doEditCancel">取 消</el-button>
        </div>
      </el-dialog>
      

    </div>
  </div>
</template>
<script>
var util = require("@/utils/util.js");
import paginationBox from '@/components/common/pagination.vue';

export default {
  components: {
      'pagination-box': paginationBox,
	},
  data() {
    var validateStatus = (rule, value, callback) => {
      if (value!='1'&&value!='2') { 
  　　　　callback(new Error('请选择审核状态'));
  　　　　return false; 
  　　} 
      callback();
    };

    return {
      dialogFormVisible: false,
      formLabelWidth: "123px",
      form: {
        status: "0",
        reply: ''
      },
      rules: { 
        status: [
          { required: true, validator: validateStatus, trigger: 'change' },
        ],
      },
      formtype:'',
      currentImg:'',
      dialogImgsVisible:false,

      searchTime:[],
      searchData: {
        //  搜索数据
        startdate: '',
        enddate: '',
        object_name: "",
        status: "0",//-1全部
      },
      page_loading : false,
      tableData: [],
      pagination: {}, //分页数据
    };
  },
  watch: {
    searchTime(newVal){
      if(newVal){
        this.searchData.startdate = newVal[0]
        this.searchData.enddate = newVal[1]
      }else{
        this.searchData.startdate = ''
        this.searchData.enddate = ''
      }
    }
  },
  created: function(){
    this.getList(this.searchData)
  },
  methods: {
    getList(searchdata={}){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "comment/getList",
          data: searchdata
      }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.setDataArr({
              tableData : res.data.list,
              pagination : {
                page : res.data.current_page,
                pagecount : res.data.last_page,
                pagesize : Math.ceil(res.data.total / res.data.last_page)
              }
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    onSerch() {
      //console.log("查询",this.searchData);
      this.getList(this.searchData)
    },
    //分页操作
    pageChange: function(page) {
      let post_data = Object.assign({},this.searchData);
      post_data.page = page;
      this.getList(post_data)
    },
    getInfo(id){
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "comment/getInfo",
          data: {id:id}
      }).then(res => {
          that.page_loading = false
          if(res.code==1){
            that.setDataArr({
              form : res.data,
              dialogFormVisible : true
            })
            that.form.status=String(that.form.status)
          }else{
            util.Message.error(res.msg);
          }
      });
    },


    resetData(){
      this.setDataArr({
        form: {
          name: "",
          describe: "",
          status: "0",
          reply: ''
        },
        formtype:'',
      })
    },
    doEditCancel(){
      var that=this
      that.resetData()
      if(that.dialogFormVisible == true){
        that.dialogFormVisible = false
      }
    },
    doEdit(dotype,e={}){
      let that = this
      that.formtype = dotype

      if(e.id){
         this.getInfo(e.id)
      }
      
    },
    doEditSubmit(){
      let that=this
      that.$refs['form'].validate((valid) => {
        if (valid) {
          util.requests("post", {
            url: "comment/edit",
            data: that.form
          }).then(res => {
              that.page_loading = false
              //console.log(res.data)
              if(res.code==1){
                util.Message.success('操作成功');
                that.onSerch()
                setTimeout(() => {
                  that.doEditCancel()
                }, 1000);
              }else{
                util.Message.error(res.msg);
              }
          });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    doDel(id){
      var that = this
      that.$confirm('是否删除此条记录?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        if(that.page_loading){
            return
        }
        that.page_loading = true
        util.requests("post", {
            url: "comment/del",
            data: {id: id}
          }).then(res => {
            that.page_loading = false
            if(res.code==1){
              that.onSerch()
              util.Message.success('操作成功');
            }else{
              util.Message.error(res.msg);
            }
        });
      }).catch(() => {
         //console.log('取消')     
      });        
    },
    
    getRealImgUrl(url){
      return this.$getRealImgUrl(url)
    },

    getFormatDate:util.DataFun.getFormatDate,
    openPage: util.openPage
  }
};
</script>
<style lang="scss" scoped>
.mapmange_container {
  min-height: calc(100vh - 50px);
  background: #f0f2f5;
  padding-top: 20px;
  .mapmange_content {
    background: #fff;
    min-height: calc(100vh - 90px);
    border-radius: 2px;
    padding: 20px 10px;
    .form-serch {
      text-align: right;
      .el-input {
        width: 300px;
      }
      .el-select {
        width: 150px;
      }
    }
  }
  /deep/.dialog-examine .el-input.is-disabled .el-input__inner,/deep/.dialog-examine .el-textarea.is-disabled .el-textarea__inner{
    cursor: auto;
    color: #606266;
  }
  .dialog-examine .avatar{
      width: 35px;
      height: 35px;
      border-radius: 50%;
  }
  .dialog-examine .thumbnail{
    width: 110px;
    height:110px;
    margin-right: 9px;
    display: inline-block;
    position: relative;
  }
  .dialog-examine .thumbnail:nth-child(5n){
     margin-right: 0;
   }
  .dialog-examine .thumbnail {
    .el-image{
      width: 100%;
      height: 100%;
    }
    .el-upload-list__item-actions {
      position: absolute;
      width: 100%;
      height: 100%;
      left: 0;
      top: 0;
      cursor: default;
      text-align: center;
      color: #fff;
      opacity: 0;
      font-size: 20px;
      background-color: rgba(0,0,0,.5);
      transition: opacity .3s;
    }
    .el-upload-list__item-actions:hover {
      opacity: 1;
      cursor: pointer;
    }
    .el-upload-list__item-actions span {
      line-height: 110px;
      display: none;
    }
    .el-upload-list__item-actions:hover span {
      display: inline-block;
    }
  }
  

  .pagination {
    margin-top: 20px;
    text-align: right;
  }
}
</style>