<template>
  <div ref="loginbox" class="login-container">
    <el-form ref="loginForm" :model="loginForm" :rules="loginRules" class="login-form" auto-complete="on" label-position="left">
      <h3 class="title">{{site}}</h3>
      <el-form-item prop="username">
        <span class="svg-container">
          <svg-icon icon-class="user" />
        </span>
        <el-input v-model="loginForm.username" name="username" type="text" auto-complete="on" placeholder="username" />
      </el-form-item>
      <el-form-item prop="password">
        <span class="svg-container">
          <svg-icon icon-class="password" />
        </span>
        <el-input
          :type="pwdType"
          v-model="loginForm.password"
          name="password"
          auto-complete="on"
          placeholder="password"
          @keyup.enter.native="handleLogin" />
        <span class="show-pwd" @click="showPwd">
          <svg-icon :icon-class="pwdType === 'password' ? 'eye' : 'eye-open'" />
        </span>
      </el-form-item>
      <el-form-item>
        <el-button :loading="loading" type="primary" style="width:100%;" @click.native.prevent="handleLogin">
          登录
        </el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import { isvalidUsername } from '@/utils/validate'

export default {
  name: 'Login',
  data() {
    const validateUsername = (rule, value, callback) => {
      callback()
      /* if (!isvalidUsername(value)) {
        callback(new Error('请输入正确的用户名'))
      } else {
        callback()
      } */
    }
    const validatePass = (rule, value, callback) => {
      if (value.length < 5) {
        callback(new Error('密码不能小于5位'))
      } else {
        callback()
      }
    }
    return {
      site:this.$baseconfig.sitename,
      loginForm: {
        username: '',
        password: ''
      },
      loginRules: {
        username: [{ required: true, trigger: 'blur', validator: validateUsername }],
        password: [{ required: true, trigger: 'blur', validator: validatePass }]
      },
      loading: false,
      pwdType: 'password',
      redirect: undefined
    }
  },
  watch: {
    $route: {
      handler: function(route) {
        this.redirect = route.query && route.query.redirect
      },
      immediate: true
    }
  },
  mounted(){
    this.bg()
  },
  methods: {
    showPwd() {
      if (this.pwdType === 'password') {
        this.pwdType = ''
      } else {
        this.pwdType = 'password'
      }
    },
    handleLogin() {
      this.$refs.loginForm.validate(valid => {
        if (valid) {
          this.loading = true
          this.$store.dispatch('Login', this.loginForm).then(() => {
            //登录成功创建webSocket连接
            this.loading = false
            this.$router.push({ path: this.redirect || '/' })
          }).catch((error) => {
            this.$message({
              message: error,
              type: 'error',
              duration: 5 * 1000
            })
            this.loading = false
          })
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    bg(){
      let max_particles    = 100;
      let particles        = [];
      let frequency        = 100;
      let init_num         = max_particles;
      let max_time         = frequency*max_particles;
      let time_to_recreate = false;

      // Enable repopolate
      setTimeout(function(){
        time_to_recreate = true;
      }.bind(this), max_time)

      // Popolate particles
      popolate(max_particles);

      let width = this.$refs.loginbox.clientWidth;
      let height = this.$refs.loginbox.clientHeight;

      var tela = document.createElement('canvas');
          tela.width = width;
          tela.height = height;
          //append("body",tela)
           this.$refs.loginbox.appendChild(tela);

      var canvas = tela.getContext('2d');

      class Particle{
        constructor(canvas, options){
          let colors = ["#feea00","#a9df85","#5dc0ad", "#ff9a00","#fa3f20"]
          let types  = ["full","fill","empty"]
          this.random = Math.random()
          this.canvas = canvas;
          this.progress = 0;

          this.x = (width/2)  + (Math.random()*200 - Math.random()*200)
          this.y = (height/2) + (Math.random()*200 - Math.random()*200)
          this.w = width
          this.h = height
          this.radius = 1 + (8*this.random)
          this.type  = types[this.randomIntFromInterval(0,types.length-1)];
          this.color = colors[this.randomIntFromInterval(0,colors.length-1)];
          this.a = 0
          this.s = (this.radius + (Math.random() * 1))/10;
          //this.s = 12 //Math.random() * 1;
        }

        getCoordinates(){
          return {
            x: this.x,
            y: this.y
          }
        }

        randomIntFromInterval(min,max){
            return Math.floor(Math.random()*(max-min+1)+min);
        }

        render(){
          // Create arc
          let lineWidth = 0.2 + (2.8*this.random);
          let color = this.color;
          switch(this.type){
            case "full":
              this.createArcFill(this.radius, color)
              this.createArcEmpty(this.radius+lineWidth, lineWidth/2, color)
            break;
            case "fill":
              this.createArcFill(this.radius, color)
            break;
            case "empty":
              this.createArcEmpty(this.radius, lineWidth, color)
            break;
          }
        }

        createArcFill(radius, color){
          this.canvas.beginPath();
          this.canvas.arc(this.x, this.y, radius, 0, 2 * Math.PI);
          this.canvas.fillStyle = color;
          this.canvas.fill();
          this.canvas.closePath();
        }

        createArcEmpty(radius, lineWidth, color){
          this.canvas.beginPath();
          this.canvas.arc(this.x, this.y, radius, 0, 2 * Math.PI);
          this.canvas.lineWidth = lineWidth;
          this.canvas.strokeStyle = color;
          this.canvas.stroke();
          this.canvas.closePath();
        }

        move(){

          this.x += Math.cos(this.a) * this.s;
          this.y += Math.sin(this.a) * this.s;
          this.a += Math.random() * 0.4 - 0.2;

          if(this.x < 0 || this.x > this.w - this.radius){
            return false
          }

          if(this.y < 0 || this.y > this.h - this.radius){
            return false
          }
          this.render()
          return true
        }

        calculateDistance(v1, v2){
          let x = Math.abs(v1.x - v2.x);
          let y = Math.abs(v1.y - v2.y);
          return Math.sqrt((x * x) + (y * y));
        }
      }

      /*
      * Function to clear layer canvas
      * @num:number number of particles
      */
      function popolate(num){
        for (var i = 0; i < num; i++) {
        setTimeout(
          function(x){
            return function(){
              // Add particle
              particles.push(new Particle(canvas))
            };
          }(i)
          ,frequency*i);
        }
        return particles.length
      }

      function clear(){
        // canvas.globalAlpha=0.04;
        canvas.fillStyle='#111';
        canvas.fillRect(0, 0, tela.width, tela.height);
        // canvas.globalAlpha=1;
      }

      function connection(){
        let old_element = null
        for(var i in particles){
          var element = particles[i]
          if(i>0){
            let box1 = old_element.getCoordinates()
            let box2 = element.getCoordinates()
            canvas.beginPath();
            canvas.moveTo(box1.x,box1.y);
            canvas.lineTo(box2.x,box2.y);
            canvas.lineWidth = 0.45;
            canvas.strokeStyle="#3f47ff";
            canvas.stroke();
            canvas.closePath();
          }
          old_element = element
        }
        /* $.each(particles, function(i, element){
          if(i>0){
            let box1 = old_element.getCoordinates()
            let box2 = element.getCoordinates()
            canvas.beginPath();
            canvas.moveTo(box1.x,box1.y);
            canvas.lineTo(box2.x,box2.y);
            canvas.lineWidth = 0.45;
            canvas.strokeStyle="#3f47ff";
            canvas.stroke();
            canvas.closePath();
          }

          old_element = element
        }) */
      }

      /*
      * Function to update particles in canvas
      */
      function update(){
        clear();
        connection()
        particles = particles.filter(function(p) { return p.move() })
        // Recreate particles
        if(time_to_recreate){
          if(particles.length < init_num){ popolate(1);}
        }
        requestAnimationFrame(update.bind(this))
      }

      update()
    }
  }
}
</script>


<style rel="stylesheet/scss" lang="scss" scoped>
//$bg:#2d3a4b;
$bg:#000;
$dark_gray:#889aa4;
$light_gray:#0d0d0d;
.login-container {
  position: fixed;
  height: 100%;
  width: 100%;
  background-color: $bg;

  .el-input {
    display: inline-block;
    height: 47px;
    width: 85%;
    input {
      background: transparent;
      border: 0px;
      -webkit-appearance: none;
      border-radius: 0px;
      padding: 12px 5px 12px 15px;
      color: $light_gray;
      height: 47px;
      &:-webkit-autofill {
        -webkit-box-shadow: 0 0 0px 1000px $bg inset !important;
        -webkit-text-fill-color: #fff !important;
      }
    }
  }
  .el-form-item {
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(202, 202, 202, 0.1);
    border-radius: 5px;
    color: #454545;
  }

  .login-form {
    position: absolute;
    left: 0;
    right: 0;
    width: 520px;
    max-width: 100%;
    padding: 35px 35px 15px 35px;
    margin: 0 auto;
    margin-top: 195px;
    background: #fff;
    opacity: 0.95;
    box-shadow: 13px 15px #ccc;
  }
  .tips {
    font-size: 14px;
    color: #fff;
    margin-bottom: 10px;
    span {
      &:first-of-type {
        margin-right: 16px;
      }
    }
  }
  .svg-container {
    padding: 6px 5px 6px 15px;
    color: $dark_gray;
    vertical-align: middle;
    width: 30px;
    display: inline-block;
  }
  .title {
    font-size: 26px;
    font-weight: 400;
    color: $light_gray;
    margin: 0px auto 40px auto;
    text-align: center;
    font-weight: bold;
  }
  .show-pwd {
    position: absolute;
    right: 10px;
    top: 7px;
    font-size: 16px;
    color: $dark_gray;
    cursor: pointer;
    user-select: none;
  }
}
</style>
