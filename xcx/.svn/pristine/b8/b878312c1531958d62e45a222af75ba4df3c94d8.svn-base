<template>
  <div class="_container">
    <div class="repastitle">{{repastitle}}</div>
    <el-form label-width="120px" ref="form" :model="form">
      <el-form-item label="上级" prop="parent_id" :rules="{ required: true, message: '请选择上级', trigger: 'change' }">
        <el-col :lg="12" :md="12" :xs="24">
          
          <select-tree v-if="tableData.length>0"
            style='width="100%"'
            :data="tableData" 
            :defaultProps="defaultProps"
            :checkedKeys="defaultCheckedKeys"
            @popoverHide='popoverHide'
            > 
           </select-tree>

        </el-col>
      </el-form-item>
      <el-form-item label="名称" prop="name" :rules="{ required: true, message: '名称不能为空', trigger: 'change' }">
        <el-col :lg="12" :md="12" :xs="24">
          <el-input v-model="form.name" placeholder="请输入名称" auto-complete="off"></el-input>
        </el-col>
      </el-form-item>

      <div v-if="form.type!=0">
          <el-form-item label="权限url" prop="url" :rules="{ required: true, message: '权限url不能为空', trigger: 'change' }">
            <el-col :lg="12" :md="12" :xs="24">
              <el-input v-model="form.url" placeholder="请输入权限url,模块/控制器/方法?参数" auto-complete="off"></el-input>
            </el-col>
          </el-form-item>
          <el-form-item label="其他附加权限url" prop="extra_urls" >
            <el-col :lg="12" :md="12" :xs="24">
              <el-input
                type="textarea"
                :autosize="{ minRows: 2 }"
                placeholder="请输入附加权限请求,多个以,隔开"
                auto-complete="off"
                v-model="form.extra_urls">
              </el-input>
            </el-col>
          </el-form-item>
          <el-form-item v-if="form.parent_id>0" label="上级按钮触发" :rules="{ required: true, message: '权限url不能为空', trigger: 'change' }">
            <el-col :lg="12" :md="12" :xs="24">
              <el-radio  v-for="item in btn_show_options"
              :key="item.value"
              :label="item.value"
               v-model="form.btn_show">{{item.label}}</el-radio>
            </el-col>
          </el-form-item>

          <el-form-item label="页面路径" prop="page" >
            <el-col :lg="12" :md="12" :xs="24">
              <el-input v-model="form.page" placeholder="请输入页面路径" auto-complete="off"></el-input>
            </el-col>
          </el-form-item>
      </div>
      
      <el-form-item label="图标" prop="icon" >
        <el-col :lg="12" :md="12" :xs="24">
          <el-input v-model="form.icon" placeholder="请输入图标" auto-complete="off"></el-input>
        </el-col>
      </el-form-item>
      <el-form-item label="排序" prop="icon" >
        <el-col :lg="12" :md="12" :xs="24">
          <el-input v-model="form.sort" placeholder="请输入排序" auto-complete="off"></el-input>
        </el-col>
      </el-form-item>
      <el-form-item label="备注" prop="remark" >
        <el-col :lg="12" :md="12" :xs="24">
          <el-input
            type="textarea"
            :autosize="{ minRows: 2 }"
            placeholder="请输入备注内容"
            auto-complete="off"
            v-model="form.remark">
          </el-input>
        </el-col>
      </el-form-item>
      <el-form-item label="状态" prop="status" >
        <el-col :lg="12" :md="12" :xs="24">
          <el-select style="width:100%" v-model="form.status" placeholder="请选择">
            <el-option 
              v-for="item in status_options"
              :key="item.value"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-col>
      </el-form-item>
      <el-form-item label="菜单激活路径" prop="active_page" v-if="form.status=='0'">
        <el-col :lg="12" :md="12" :xs="24">
          <el-input v-model="form.active_page" placeholder="请输入左侧菜单hover路径" auto-complete="off"></el-input>
        </el-col>
      </el-form-item>
      <el-form-item label="类型" prop="type" >
        <el-col :lg="12" :md="12" :xs="24">
          <el-select style="width:100%" v-model="form.type" placeholder="请选择" >
            <el-option
              v-for="item in type_options"
              :key="item.value"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-col>
      </el-form-item>

      <el-form-item>
        <el-button type="primary" @click="onSubmit">保 存</el-button>
        <el-button type="danger" @click="cancelForm">返 回</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script>
var util=require('@/utils/util')
import SelectTree from '@/components/common/SelectTree.vue'
export default {
  components: {
			'select-tree': SelectTree,
	},
  data() {
    return {
      btn_show_options: [{
        value: '1',
        label: '设置'
      }, {
        value: '0',
        label: '不设置'
      }],
      status_options: [{
        value: '1',
        label: '在侧边栏显示'
      }, {
        value: '0',
        label: '在侧边栏隐藏'
      }],
      type_options: [{
        value: '1',
        label: '有界面可访问菜单'
      }, {
        value: '2',
        label: '无界面可访问菜单(接口)'
      }, {
        value: '0',
        label: '只作为菜单'
      }],
      parent_id_options: [],
      form:{
        parent_id : '0',
        name      : '',
        url       : '',
        extra_urls:'',
        page      : '',
        active_page: '',
        icon      : '',
        sort      : 0,
        remark    : '',
        type      : '1',
        status    : '1',
        btn_show : '0',
      },

      tableData :[],
      defaultProps: {
        children: 'children',
        label: 'name'
      },
      repastitle:'菜单添加'
    };
  },
  computed:{
    defaultCheckedKeys(){
      return [this.form.parent_id]
    } 
  },
  created: function() {
    var urlData= Object.keys(this.$urlData).length> 0? this.$urlData : {}
    this.getList()
    if(urlData.id){
      this.getInfo(urlData.id)
    }
  },
  methods:{
    getInfo(id){
      var that = this
      util.requests("post", {
          url: "menu/getMenuInfo",
          data:{id: id},
      }).then(res => {
          that.page_loading = false
          if(res.code==1){
            res.data.type = String(res.data.type)
            res.data.status = String(res.data.status)
            res.data.btn_show = String(res.data.btn_show)
            that.setDataArr({
              form : res.data,
              repastitle : '菜单编辑'
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    /**
     * 菜单列表
     */
    getList() {
      var that = this
      if(that.page_loading){
          return
      }
      that.page_loading = true

      util.requests("post", {
          url: "menu/index"
      }).then(res => {
          that.page_loading = false
          if(res.code==1){
            var newarr=[{parent_id: 0,id:0 ,name: "一级菜单"}].concat(res.data.list)
            that.setDataArr({
              tableData : newarr,
            })
          }else{
            util.Message.error(res.msg);
          }
      });
    },
    popoverHide(val){
      console.log(val)
      this.form.parent_id = val
    },

    onSubmit(){
      this.$refs['form'].validate((valid) => {
        if (valid) {
          if(this.form.parent_id==0){
            this.form.btn_show = 0;
          }
          util.requests('post',{
            url:'menu/edit',
            data:{...this.form}
          }).then(res=>{
            if(res.code==1){
              util.Message.success(res.msg)

              setTimeout(() => {
                util.openPage({url:-1})
              }, 800);
            }else{
              util.Message.error(res.msg);
            }
          });
        } else {
          console.log('error submit!');
          return false;
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
  .repastitle {
    height: 40px;
    line-height: 40px;
    padding-left: 10px;
    background-color: rgba($color: #f0f2f5, $alpha: 0.5);
    border-left: 4px solid;
    border-color: #409eff;
    font-size: 16px;
    margin-bottom: 50px;
  }
  .el-form{
      padding-left: 50px;
  }
  .el-form-item.is-success{
    border-color: #C0C4CC!important;
  }
}
</style>


