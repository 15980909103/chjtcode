<template>
   <div id="chat">
     <div class="sidebar">
       <card :user="user" :search.sync="search"></card>
       <list :user-list="sessionList" :session="session" :session-index.sync="sessionIndex" @getlist="getMsgList" :search="search" ></list>
     </div>
     <div class="main">
       <message :session="session" :user="user" :user-list="sessionList"></message>
       <etext :session="session" @sendmsg="sendMsg"></etext>
     </div>
   </div>
</template>
<script>
  var util = require("@/utils/util.js");
  import store from '@/components/chat/store';
  import card from '@/components/chat/card.vue';
  import list from '@/components/chat/list';
  import etext from '@/components/chat/etext';
  import message from '@/components/chat/message';
  export default {

    created() {
      this.user= this.$store.state.user.userinfo ;
      this.getdialogueList()
      let socketurl     = this.$baseconfig.sockethost;
      let token         = this.$store.state.user.token ;
      socketurl         = socketurl+'?token='+token+'&type=admin'
      this.webSocket        = new WebSocket(socketurl)
      this.webSocket.onopen = this.msgopen;
      this.webSocket.onmessage = this.message;
    },
    data () {
      //let serverData = store.fetch();
      let serverData = {
        webSocket:null,
        // 会话列表
        sessionList: [],
      }
      return {
        // 登录用户
        user: {},
        // 会话列表
        sessionList: serverData.sessionList,
        // 搜索key
        search: '',
        // 选中的会话Index
        sessionIndex: 1,
        index:0
      };
    },
    computed: {
      session () {
        let that =this;
        let obj;
        that.sessionList.forEach(function (e,index) {
          if(e.id  == that.sessionIndex){
            obj = e;
            that.index = index;
          }
        })
      return obj;
      }
    },

    methods:{
      getMsgList(e){
        let that = this;
        that.sessionList.forEach(function (e,index) {
          if(e.id  == that.sessionIndex){
            that.index = index;
          }
        })
        that.sessionList[that.index].unread = 0 ;
        if(e && !that.sessionList[that.index].messages){
          util.requests("post",{
            url:"chat/getChatListByUser",
            data:{dialogue_id:e}
          }).then(res=>{
            if(res.code ==1){
              let returnDate = res.data;
              let msglist=[];
              for (let index in returnDate){
                let msg ;
                msg = {
                  text          :  returnDate[index].msg, //会话id
                  name          :  returnDate[index].send_name,
                  img           :  returnDate[index].send_head,
                  send_user_id  :  returnDate[index].send_user_id,
                  date          :  returnDate[index].send_time,
                  type          :  returnDate[index].msg_type,
                  url          :  returnDate[index].msg_url,
                };
                msglist.push(msg);

              }
              // console.log(userlist,23423423);
              that.sessionList[this.index].messages  = msglist;
              that.sessionList  = JSON.parse(JSON.stringify(that.sessionList));
            }else{
              util.Message.error('系统错误');
            }
          })
        }else{

        }
      },
      msgopen(e){
        console.log('连接上了')
      },

      getdialogueList(){
        let that = this;
        util.requests("post",{
          url:"chat/dialogueList",
        }).then(res=>{
           if(res.code ==1){
             let returnDate = res.data;
             let userlist =[];
            for (let index in returnDate){
              let obj ;
              obj = {
                 id :returnDate[index].id,
                 name : returnDate[index].nickname,
                 img :  returnDate[index].headimgurl,
                 to_type :  returnDate[index].to_type,
                 user_id :  returnDate[index].user_id, //如果是群聊 就是群id 如果是单聊就是用户id
                 unread :   returnDate[index].unread ? returnDate[index].unread :0
              };
              userlist.push(obj);

            }
            // console.log(userlist,23423423);
             that.sessionList  = userlist
           }else{
             util.Message.error('无会话');
           }
        })
      },
      message(e){
        console.log(e.data);
        let data = e.data;
        data      = JSON.parse(data);
        if(!data){
          return ;
        }
        switch (data.type) {
          case 'ping':this.webSocket.send(JSON.stringify({type:'ping'}));break;
          case 'say' :this.getMSgCallBack(data);
        }

      },
      sendMsg(e){
        let that = this ;
        let to_user_info = that.sessionList[this.index];
        let to_user_id  =  '';
        let sendData;
        let sendobj  = {
          text          :  e.text, //会话id
          name          :  '假发一带谁都不爱',
          img           :  'https://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83eqML1IepKLibmc3pIWUOYwttsv3mFCvZZYSqQm5Bv5j9PTg1gTicpf9CiaFKIluI3WS5iaHOwlBGeQomA/132',
          send_user_id  :   this.user.user_id,
          type: 1,
          date          :   parseInt(Date.parse(new Date()) /1000 ),
        }
        if(to_user_info.to_type ==1){
            e.group_id = 0;
            to_user_id = to_user_info.user_id;
            sendData  = that.formatSendDate(e,'wliao/Index/usertouser',to_user_id);
        }else{
            e.group_id   = to_user_info.user_id;
            sendData = that.formatSendDate(e,'wliao/Index/usertogroup',0);
        }


        console.log(sendobj,234234234);
        that.sessionList[that.index].messages.push(sendobj);

        if(that.index!=0){
          that.sessionList.unshift(that.sessionList.splice(that.index , 1)[0]);
        }
        that.sessionList  = JSON.parse(JSON.stringify(that.sessionList));
        that.webSocket.send(sendData);
        //往对应数组加入聊天纪录


      },
      formatSendDate(e,url,to_user_id){ //格式话发送数组
        let sendObj = {
          to_user_id    : to_user_id,
          url           : url,
          type          :"1",
          msg_type      :"1",
          send_time     : parseInt(Date.parse(new Date()) /1000 ),
          send_msg      : e.text,
          group_id      : e.group_id ? e.group_id : 0,
        }

        return JSON.stringify(sendObj);

      },
      getMSgCallBack(data){
        let that = this;

        if(data.code ==1){
          //如果是自己接收到的群消息推送回调不作处理
          if(data.sendUserInfo.id == this.user.user_id){
            return ;
          }
          that.sessionIndex =  data.returndata.chat_dialogue_id;
          that.sessionList.forEach(function (e,index) {
            if(e.id  == that.sessionIndex){
              that.index = index;
            }
          })
          //增加未读消息
          that.sessionList[that.index].unread ++ ;
          let sendobj  = {
            text          :   data.returndata.msg, //会话id
            name          :   data.sendUserInfo.user_name,
            img           :   data.sendUserInfo.user_avatar,
            send_user_id  :   data.sendUserInfo.id,
            type          :   data.sendUserInfo.msg_type,
            date          :   parseInt(Date.parse(new Date()) /1000 ),
          }

          that.sessionList[that.index].messages.push(sendobj);
          if(that.index!=0){
            that.sessionList.unshift(that.sessionList.splice(that.index , 1)[0]);
          }
          that.sessionList  = JSON.parse(JSON.stringify(that.sessionList));
        }else{
          util.Message.error(res.msg);
        }
      }


    },
    watch: {
      // 每当sessionList改变时，保存到localStorage中
      sessionList: {
        deep: true,
        handler () {
          store.save({
            user: this.user,
            userList: this.userList,
            sessionList: this.sessionList
          });
        }
      }
    },
    components: {
      card, list, etext, message
    }
  };

</script>
<style lang="less">
  #chat {
    overflow: hidden;
    border-radius: 3px;
    height: 600px;
    width: 800px;
    margin: 0 auto;
    .sidebar, .main {
      height: 100%;
    }
    .sidebar {
      float: left;
      width: 200px;
      color: #f4f4f4;
      background-color: #2e3238;
    }
    .main {
      position: relative;
      overflow: hidden;
      background-color: #eee;
    }
    .m-text {
      position: absolute;
      width: 100%;
      bottom: 0;
      left: 0;
    }
    ._container {
      margin-top: 20px;
      padding: 0px;
      background: #F9FBFF;
    }
    .m-message {
      height: ~'calc(100% - 160px)';
    }
  }
</style>
