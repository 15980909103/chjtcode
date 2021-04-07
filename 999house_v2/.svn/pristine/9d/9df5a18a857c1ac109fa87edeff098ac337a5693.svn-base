<template>
  <div class="m-message" v-scroll-bottom="session.messages">
    <ul>
      <li v-for="item in session.messages">
        <p class="time"><span>{{item.date | time}}</span></p>
        <div class="main" :class="{ self: item.send_user_id == user.user_id }">
          <img class="avatar" width="30" height="30" :src="item.img" />
          <div class="text" v-if="item.type==1">{{item.text}}</div>
          <img v-else  :src="geturl(item.url)"  class="imgclass">
        </div>
      </li>
    </ul>
  </div>
</template>


<script>
  let that
  export default {
    props: ['session', 'user', 'userList'],
    computed: {
      sessionUser () {
        let users = this.userList.filter(item => item.id === this.session.userId);
        return users[0];
      }
    },

    filters: {
      // 将日期过滤为 hour:minutes
      time (date) {
        if (typeof date === 'string' || typeof date ==='number') {
          date = new Date(parseInt(date) * 1000);
        }
        return date.getHours() + ':' + date.getMinutes();
      }
    },
    created() {
      that = this;
    },

    methods:{
      geturl:function (url) {
        let host = this.$baseconfig.imghost;
        if(url  && url.indexOf('http') == '0'){
          return  url;
        }
        return host+url;
      }
    },
    directives: {
      // 发送消息后滚动到底部
      // 'scroll-bottom' () {
      //   this.nextTick(() => {
      //     this.el.scrollTop = this.el.scrollHeight - this.el.clientHeight;
      //   });
      // }
      'scroll-bottom': function (el) {
        that.$nextTick(function () {
          that.$el.scrollTop = that.$el.scrollHeight - that.$el.clientHeight;
        });
        // el.scrollTop =el.scrollHeight - el.clientHeight;
      }

    }
  };
</script>



<style lang="less">
  .m-message {
    padding: 10px 15px;
    overflow-y: scroll;
    ul {
      list-style: none;
    }
    body, ul {
      margin: 0;
      padding: 0;
    }
    li {
      margin-bottom: 15px;
    }

    .imgclass{
      width: 150px;
    }
    .time {
      margin: 7px 0;
      text-align: center;

      > span {
        display: inline-block;
        padding: 0 18px;
        font-size: 12px;
        color: #fff;
        border-radius: 2px;
        background-color: #dcdcdc;
      }
    }
    .avatar {
      float: left;
      margin: 0 10px 0 0;
      border-radius: 3px;
    }
    .text {
      display: inline-block;
      position: relative;
      padding: 0 10px;
      max-width: ~'calc(100% - 40px)';
      min-height: 30px;
      line-height: 2.5;
      font-size: 12px;
      text-align: left;
      word-break: break-all;
      background-color: #fafafa;
      border-radius: 4px;

      &:before {
        content: " ";
        position: absolute;
        top: 9px;
        right: 100%;
        border: 6px solid transparent;
        border-right-color: #fafafa;
      }
    }
    .self {
      text-align: right;

      .avatar {
        float: right;
        margin: 0 0 0 10px;
      }
      .text {
        background-color: #b2e281;

        &:before {
          right: inherit;
          left: 100%;
          border-right-color: transparent;
          border-left-color: #b2e281;
        }
      }
    }
  }
</style>
