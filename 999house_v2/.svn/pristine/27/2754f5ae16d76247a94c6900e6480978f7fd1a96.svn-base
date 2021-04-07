<template>
  <div class="_container">
    <div class="repastitle">修改密码</div>
    <el-form label-width="120px" ref="form" :model="form" :rules="rules">
      <el-form-item label="请输入当前密码" prop="oldpassword" >
        <el-col :lg="6" :md="12" :xs="24">
          <el-input v-model="form.oldpassword" placeholder="请输入当前登录密码" auto-complete="off"></el-input>
        </el-col>
      </el-form-item>
      <el-form-item label="新密码" prop="newpassword" >
        <el-col :lg="6" :md="12" :xs="24">
          <el-input v-model="form.newpassword" placeholder="请设置6-20位密码" auto-complete="off"></el-input>
        </el-col>
      </el-form-item>
      <el-form-item label="确认密码" prop="newpassword2" >
        <el-col :lg="6" :md="12" :xs="24">
          <el-input v-model="form.newpassword2" placeholder="请确认新密码" auto-complete="off"></el-input>
        </el-col>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="onSubmit">保 存</el-button>
        <el-button type="danger" @click="cancelForm">取 消</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script>
var util=require('@/utils/util')
export default {
  data() {
    var validatePass = (rule, value, callback) => {
      value=util.trim(value)
      if (value.length <6 ) {
        callback(new Error('请输入至少6位密码'));
      } else {
        if (this.form.newpassword2 !== '') {
          this.$refs.form.validateField('newpassword2');
        }
        callback();
      }
    };
    var validatePass2 = (rule, value, callback) => {
      value=util.trim(value)
      if (value === '') {
        callback(new Error('请再次输入密码'));
      } else if (value !== this.form.newpassword) {
        callback(new Error('两次输入的新密码不一致'));
      } else {
        callback();
      }
    };

    return {
      form:{
        oldpassword : '',
        newpassword : '',
        newpassword2 : ''
      },
      rules: {
        oldpassword: [
          { required: true, message: '请输入密码', trigger: 'change' },
        ],
        newpassword: [
          { required: true, message: '请输入新密码', trigger: 'change' },
          { validator: validatePass, trigger: 'change' }
        ],
        newpassword2: [
          { required: true, message: '请再次输入新密码', trigger: 'change' },
          { validator: validatePass2, trigger: 'change' }
        ]
      }
    };
  },
  methods:{
    onSubmit(){
      this.$refs['form'].validate((valid) => {
        if (valid) {
          util.requests('post',{
            url:'index/editPassword',
            data:{...this.form}
          }).then(res=>{
            {
              if(res.code==1){
                util.Message.success(res.msg)
              }else{
                util.Message.error(res.msg);
              }
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
}
</style>


