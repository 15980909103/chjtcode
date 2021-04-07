<template>
  <div class="m-list">
    <ul>
      <li v-for="(item,index) in newsearch" :class="{ active: sessionIndex === item.id }" @click="select(item.id)">

        <div style="position:relative">
          <div class="dome" v-if="item.unread > 0">{{item.unread}}</div>
          <img class="avatar"  width="30" height="30" :alt="item.name" :src="item.img">
          <p class="name">{{item.name}}</p>
        </div>


      </li>
    </ul>
  </div>
</template>

<script>
    export default {
      props: ['userList', 'sessionIndex', 'session', 'search'],
      created() {
        this.newsearch = this.userList;
      },
      watch:{
        search:function (val) {
          this.newsearch = this.userList;
          this.newsearch = this.newsearch.filter(item => item.name.indexOf(this.search) > -1);
        },
        userList:function (val) {
          this.newsearch = this.userList;
       }
      },

      methods: {
            select (value) {
                this.$emit('update:sessionIndex',value);
                this.$emit('getlist',value);
            }
        },

      data(){
        return {
          newsearch:[],
        }
      },

    };
</script>



<style lang="less">
    .m-list {

      ul {
        list-style: none;
      }
      body, ul {
        margin: 0;
        padding: 0;
      }
        li {
            padding: 12px 15px;
            border-bottom: 1px solid #292C33;
            cursor: pointer;
            transition: background-color .1s;

            &:hover {
                background-color: rgba(255, 255, 255, 0.03);
            }
            &.active {
                background-color: rgba(255, 255, 255, 0.1);
            }
        }
        .avatar, .name {
            vertical-align: middle;
        }
        .avatar {
            border-radius: 2px;
        }
     .dome{
        width: 20px;
        height: 20px;
        text-align: center;


       vertical-align: center;
        font-size: 6px;
        background: #FC4D39;
        padding: 2px 0;
        border-radius: 50%;
        position: absolute;
        top: 0px;
        right: 0px;
      }
        .name {
            display: inline-block;
            margin: 0 0 0 15px;
        }
    }
</style>
