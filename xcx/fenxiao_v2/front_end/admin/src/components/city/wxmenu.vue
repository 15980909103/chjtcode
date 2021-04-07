<template>
  <div class="_container">
    <div class="mapeditor-title">微信公众号菜单设置</div>
    <!--  -->
    <div class="content" style="width:900px;margin:0 auto;">
      <div id="app-menu">
        <!-- 预览窗 -->
        <div class="weixin-preview">
          <div class="weixin-hd">
            <div class="weixin-title">{{weixinTitle}}</div>
          </div>
          <div class="weixin-bd">
            <ul class="weixin-menu" id="weixin-menu">
              <!--s菜单-->
              <li
                      v-for="(btn,i) in menu.button" :key="i"
                      class="menu-item"
                      :class="{current:selectedMenuIndex===i&&selectedMenuLevel()==1}"
                      @click="selectedMenu(i)"
              >
                <!--&lt;!&ndash;e菜单&ndash;&gt;-->
                <div class="menu-item-title">
                  <i class="icon_menu_dot"></i>
                  <span>{{ btn.name }}</span>
                </div>
                <ul class="weixin-sub-menu" v-show="selectedMenuIndex===i">
                  <li
                          v-for="(sub,i2) in btn.sub_button" :key="i2"
                          class="menu-sub-item"
                          :class="{current:selectedSubMenuIndex===i2&&selectedMenuLevel()==2}"
                          @click.stop="selectedMenu(i2,'sub')"
                  >
                    <div class="menu-item-title">
                      <span>{{sub.name}}</span>
                    </div>
                  </li>
                  <li v-if="btn.sub_button.length<5" class="menu-sub-item" @click.stop="addMenu(2)">
                    <div class="menu-item-title">
                      <i class="icon14_menu_add"></i>
                    </div>
                  </li>
                  <i class="menu-arrow arrow_out"></i>
                  <i class="menu-arrow arrow_in"></i>
                </ul>
              </li>
              <li class="menu-item" v-if="menu.button.length<3" @click="addMenu(1)">
                <i class="icon14_menu_add"></i>
              </li>
            </ul>
          </div>
        </div>
        <!-- 主菜单 -->
        <div class="weixin-menu-detail" v-if="selectedMenuLevel()==1">
          <div class="menu-input-group" style="border-bottom: 2px #e8e8e8 solid;">
            <div class="menu-name">{{menu.button[selectedMenuIndex].name}}</div>
            <div class="menu-del" @click="delMenu">删除菜单</div>
          </div>
          <div class="menu-input-group">
            <div class="menu-label">菜单名称</div>
            <div class="menu-input">
              <input
                      type="text"
                      name="name"
                      placeholder="请输入菜单名称"
                      class="menu-input-text"
                      v-model="menu.button[selectedMenuIndex].name"
                      @input="checkMenuName(menu.button[selectedMenuIndex].name)"
              >
              <p class="menu-tips" style="color:#e15f63" v-show="menuNameBounds">字数超过上限</p>
              <p class="menu-tips">字数不超过4个汉字或8个字母</p>
            </div>
          </div>
          <template v-if="menu.button[selectedMenuIndex].sub_button.length==0">
            <div class="menu-input-group">
              <div class="menu-label">菜单内容</div>
              <el-select v-model="menu.button[selectedMenuIndex].type" style="width:300px;margin-left:10px;">
                <el-option
                        v-for="option in type_options"
                        :key="option.value"
                        :label="option.label"
                        :value="option.value">
                </el-option>
              </el-select>
            </div>
            <div class="menu-content" v-if="selectedMenuType()=='view'">
              <div class="menu-input-group">
                <p class="menu-tips">订阅者点击该子菜单会跳到以下链接</p>
                <div class="menu-label">页面地址</div>
                <div class="menu-input">
                  <input
                          type="text"
                          placeholder
                          class="menu-input-text"
                          v-model="menu.button[selectedMenuIndex].url"
                          v-on:change="checkUrl(menu.button[selectedMenuIndex].url)"
                  >
                  <p class="menu-tips cursor"  v-show="urlTitle">来自素材库 - 《{{menu.button[selectedMenuIndex].title}}》</p>
                  <el-button plain @click="selectNewsUrl()" v-show="urlTitle">重新选择</el-button>
                  <p class="menu-tips cursor" @click="selectNewsUrl()" v-show="!urlTitle">从公众号图文消息中选择</p>


                </div>
              </div>
            </div>
            <div class="menu-msg-content" v-else-if="selectedMenuType()=='media_id'">
              <el-tabs type="border-card">
                <el-tab-pane label="用户管理">
                  <span slot="label"><i class="icon_msg_sender"></i> 图文消息</span>
                  <div class="menu-msg-panel">
                    <div
                            class="menu-msg-select"
                            v-if="menu.button[selectedMenuIndex].media_id==''||menu.button[selectedMenuIndex].media_id==undefined"
                            @click="selectMaterialId"
                    >
                      <i class="icon36_common add_gray"></i>
                      <strong>从素材库中选择</strong>
                    </div>
                    <div class="menu-msg-select" v-else>
                      <div class="menu-msg-title">
                        <i class="icon_msg_sender"></i>
                        {{material.title}}
                      </div>
                      <a :href="material.url" target="_blank" class="btn btn-sm btn-info">查看</a>
                      <div class="btn btn-sm btn-danger" @click="delMaterialId">删除</div>
                    </div>
                  </div>
                </el-tab-pane>
                <el-tab-pane label="配置管理">
                  <span slot="label"><i class="icon_t"></i> <img src="" alt="">文字</span>
                  配置管理</el-tab-pane>
                <el-tab-pane label="角色管理">角色管理</el-tab-pane>
                <el-tab-pane label="定时任务补偿">定时任务补偿</el-tab-pane>
              </el-tabs>

              <!--<div class="menu-msg-head">-->
              <!--<i class="icon_msg_sender"></i>图文消息-->
              <!--</div>-->

              <!--</div>-->
            </div>
            <div class="menu-content" v-else-if="selectedMenuType()=='miniprogram'">
              <div class="menu-input-group">
                <p class="menu-tips">订阅者点击该子菜单会跳到以下小程序</p>
                <div class="menu-label">小程序APPID</div>
                <div class="menu-input">
                  <input
                          type="text"
                          placeholder="小程序的appid（仅认证公众号可配置）"
                          class="menu-input-text"
                          v-model="menu.button[selectedMenuIndex].appid"
                  >
                </div>
              </div>
              <div class="menu-input-group">
                <div class="menu-label">小程序路径</div>
                <div class="menu-input">
                  <input
                          type="text"
                          placeholder="小程序的页面路径 pages/Index/index"
                          class="menu-input-text"
                          v-model="menu.button[selectedMenuIndex].pagepath"
                  >
                </div>
              </div>
              <div class="menu-input-group">
                <div class="menu-label">备用网页</div>
                <div class="menu-input">
                  <input
                          type="text"
                          placeholder
                          class="menu-input-text"
                          v-model="menu.button[selectedMenuIndex].url"
                  >
                  <p class="menu-tips">旧版微信客户端无法支持小程序，用户点击菜单时将会打开备用网页。</p>
                </div>
              </div>
            </div>

            <div class="menu-content" v-else-if="selectedMenuType()==3">
              <div class="menu-input-group">
                <p class="menu-tips">用于消息接口推送，不超过128字节</p>
                <div class="menu-label">菜单KEY值</div>
                <div class="menu-input">
                  <input
                          type="text"
                          placeholder
                          class="menu-input-text"
                          v-model="menu.button[selectedMenuIndex].key"
                  >
                </div>
              </div>
            </div>
          </template>
        </div>
        <!-- 子菜单 -->
        <div class="weixin-menu-detail" v-if="selectedMenuLevel()==2">
          <div class="menu-input-group" style="border-bottom: 2px #e8e8e8 solid;">
            <div class="menu-name">{{menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].name}}</div>
            <div class="menu-del" @click="delMenu">删除子菜单</div>
          </div>
          <div class="menu-input-group">
            <div class="menu-label">子菜单名称</div>
            <div class="menu-input">
              <input
                      type="text"
                      placeholder="请输入子菜单名称"
                      class="menu-input-text"
                      v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].name"
                      @input="checkMenuName(menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].name)"
              >
              <p class="menu-tips" style="color:#e15f63" v-show="menuNameBounds">字数超过上限</p>
              <p class="menu-tips">字数不超过8个汉字或16个字母</p>
            </div>
          </div>
          <div class="menu-input-group">
            <div class="menu-label">子菜单内容</div>
            <div class="menu-input">
              <el-select v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].type" style="width:300px;margin-left:10px;">
                <el-option
                        v-for="option in type_options"
                        :key="option.value"
                        :label="option.label"
                        :value="option.value">
                </el-option>
              </el-select>
            </div>
          </div>
          <div class="menu-content" v-if="selectedMenuType()=='view'">
            <div class="menu-input-group">
              <p class="menu-tips">订阅者点击该子菜单会跳到以下链接</p>
              <div class="menu-label">页面地址</div>
              <div class="menu-input">
                <input
                        type="text"
                        placeholder
                        class="menu-input-text"
                        v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].url"
                        v-on:change="checkUrl(menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].url)"
                >
                <p class="menu-tips cursor" style="color: #8D8D8D;font-size: 14px" v-show="urlTitle">来自素材库 - 《{{menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].title}}》</p>
                <el-button plain @click="selectNewsUrl()" v-show="urlTitle">重新选择</el-button>
                <p class="menu-tips cursor" @click="selectNewsUrl()" v-show="!urlTitle">从公众号图文消息中选择</p>
              </div>
            </div>
          </div>
          <div class="menu-msg-content" v-else-if="selectedMenuType()=='media_id'">
            <div class="menu-msg-head">
              <i class="icon_msg_sender"></i>图文消息
            </div>
            <div class="menu-msg-panel">
              <div
                      class="menu-msg-select"
                      v-if="!menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].media_id"
                      @click="selectMaterialId"
              >
                <i class="icon36_common add_gray"></i>
                <strong>从素材库中选择</strong>
              </div>
              <div class="menu-msg-select" v-else>

                <i class="icon_msg_sender"></i>
                {{menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].title}}
                <!--<a-->
                <!--:href="material.url"-->
                <!--target="_blank"-->
                <!--class="btn btn-sm btn-info"-->
                <!--&gt;查看</a>-->

                <div class="btn btn-sm btn-danger" @click="delMaterialId">删除</div>
              </div>
            </div>
          </div>

          <div class="menu-content" v-else-if="selectedMenuType()=='miniprogram'">
            <div class="menu-input-group">
              <p class="menu-tips">订阅者点击该子菜单会跳到以下小程序</p>
              <div class="menu-label">小程序APPID</div>
              <div class="menu-input">
                <input
                        type="text"
                        placeholder="小程序的appid（仅认证公众号可配置）"
                        class="menu-input-text"
                        v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].appid"
                >
              </div>
            </div>
            <div class="menu-input-group">
              <div class="menu-label">小程序路径</div>
              <div class="menu-input">
                <input
                        type="text"
                        placeholder="小程序的页面路径 pages/Index/index"
                        class="menu-input-text"
                        v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].pagepath"
                >
              </div>
            </div>
            <div class="menu-input-group">
              <div class="menu-label">备用网页</div>
              <div class="menu-input">
                <input
                        type="text"
                        placeholder
                        class="menu-input-text"
                        v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].url"
                >
                <p class="menu-tips">旧版微信客户端无法支持小程序，用户点击菜单时将会打开备用网页。</p>
              </div>
            </div>
          </div>

          <div class="menu-content" v-else-if="selectedMenuType()==3">
            <div class="menu-input-group">
              <p class="menu-tips">用于消息接口推送，不超过128字节</p>
              <div class="menu-label">菜单KEY值</div>
              <div class="menu-input">
                <input
                        type="text"
                        placeholder
                        class="menu-input-text"
                        v-model="menu.button[selectedMenuIndex].sub_button[selectedSubMenuIndex].key"
                >
              </div>
            </div>
          </div>

        </div>
      </div>

      <el-dialog title="选择素材" :visible.sync="materialBox" @close="closeArticle">
        <div style="height:300px;overflow: auto;">
          <div v-infinite-scroll="loadMore2" infinite-scroll-disabled="busy" infinite-scroll-distance="10" >
            <div style="margin: 0 auto;width:256px;position: relative;top:150px;" v-show="articleBox"><strong>素材管理中还没有图文哦</strong></div>
            <ul>
              <li
                      v-for="(list,i) in listData"
              >
                <div class="article-list" @mouseenter="onMouseOver(i)" @mouseleave="onMouseout(i)" @click="articleClick(i)">
                  <ul>
                    <div class="edit_mask" v-show="sure[i] || mask[i]">
                      <i class="icon_card_selected">已选择</i>
                    </div>
                    <div class="appmsg_info">
                      <em class="appmsg_date">更新于 {{list.create_time}}</em>
                    </div>
                    <li v-for="(article,j) in list.val">
                      <!--第一个显示框-->
                      <div class="big-img-box" v-if="j==0">
                        <div :style="{backgroundImage: 'url('+getRealImgUrl(article.img)+')'}"  class="big-img-box-inline" >
                          <div class="big-tile">
                            <span class="card_appmsg_title">{{article.title}}</span>
                          </div>
                        </div>
                        <div style="border-bottom:1px solid #E8E8E8;width:93%;margin:0 auto;position:relative;top: 16%;"></div>
                      </div>

                      <div class="small-img-box" v-else>
                        <div class="card_appmsg">
                          <div class="weui-desktop-vm_primary card_appmsg_hd">
                            <strong class="card_appmsg_title js_appmsg_title" style="color: black;width:185px">{{article.title}}</strong>
                          </div>
                          <div class="card_appmsg_thumb js_appmsg_thumb" :style="{backgroundImage: 'url('+getRealImgUrl(article.img)+')'}"></div>
                        </div>
                        <div style="border-bottom:1px solid #E8E8E8;width:93%;margin:0 auto;position:relative;top: 41%;"></div>
                      </div>
                    </li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <el-row :gutter="20" style="text-align:center">
          <el-button type="primary" icon="el-icon-search" @click="onImageTextSure" :disabled="articleSelectStatus">确定</el-button>
          <el-button type="info" plain @click="onImageTextCancel">&nbsp;取消&nbsp;</el-button>
        </el-row>
      </el-dialog>

      <el-dialog title="公众号内容" :visible.sync="dialogTableVisible" @close="closeMaterial">
        <el-form :inline="true" :model="material" class="form-serch">
          <el-form-item>
            <el-input v-model="material.title" placeholder="标题" prefix-icon="el-icon-search"></el-input>
          </el-form-item>

          <el-form-item>
            <el-button type="primary" icon="el-icon-search" @click="onSerch">查询</el-button>
          </el-form-item>
        </el-form>
        <el-table
                ref="singleTable"
                :data="tableData"
                v-loadmore="loadMore"
                v-loading="loadingData"
                height="350px"
                highlight-current-row
                @current-change="handleCurrentChange"
                @row-click = "showRow"
                style="width: 100%"
                border
        >
          <el-table-column label="选择" width="70" center>
            <template slot-scope="scope">
              <el-radio class="radio" v-model="radio"  :label="scope.$index"  @change.native="getCurrentRow(scope.$index)" >&nbsp;</el-radio>
            </template>
          </el-table-column>
          <el-table-column property="title" label="标题"></el-table-column>
          <el-table-column property="create_time" label="发布日期" width="180"></el-table-column>
        </el-table>

        <el-row :gutter="20" style="text-align:center">
          <el-button type="primary" icon="el-icon-search" @click="onSure" :disabled="radioStatus">确定</el-button>
          <el-button type="info" plain @click="onCancel">&nbsp;取消&nbsp;</el-button>
        </el-row>
      </el-dialog>

      <div class="weixin-btn-group">
        <el-button type="primary" @click="onSubmit" >保存</el-button>
      </div>
    </div>

  </div>
</template>

<script>

  var util = require("@/utils/util");
  import paginationBox from '@/components/common/pagination.vue';


  export default {
    components: {
      'pagination-box': paginationBox,
    },
    directives: {
      loadmore: {
        // 指令的定义
        bind(el, binding, vnode) {
          const selectWrap = el.querySelector('.el-table__body-wrapper')
          selectWrap.addEventListener('scroll', function() {
            const sign = 100
            const scrollDistance = this.scrollHeight - this.scrollTop - this.clientHeight
            if (scrollDistance <= sign) {
              binding.value()
              // console.log('距离底部小于100了')
              // console.log(vnode.context)
              // // 指令中不能用this关键字
              // vnode.context.getNewData()
            }
          })
        }
      },
    },
    data() {
      return {
        activeName: '2',
        region_no:'',//城市区域编码
        weixinTitle: "公众号菜单",
        menu: { button: [] }, //当前菜单
        selectedMenuIndex: "", //当前选中菜单索引
        selectedSubMenuIndex: "", //当前选中子菜单索引
        menuNameBounds: false, //菜单长度是否过长
        materialBox:false,//选择公众号素材库素材-------
        dialogTableVisible:false,//打开嵌套表格的 Dialog
        pagination: {}, //分页数据
        loadingData:false,//素材库列表数据加载
        loadSign:true,
        page:1,
        last_page:'',
        radioStatus:true,
        urlTitle:false,
        material: {
          title: "",
          url: "",
          thumb_url: ""
        },
        type_options:[{
          label: '跳转网页(view)',
          value: 'view'
        },{
          label: '发送消息(media_id)',
          value: 'media_id'
        },{
          label: '打开指定小程序(miniprogram)',
          value: 'miniprogram'
        }],
        form: {},
        rules: {},
        listData:[],
        articleList:[],
        count:0,
        busy:false,
        mask:[],//移除显示遮罩
        sure:[],//点击显示遮罩
        selectArticleIndex:'',
        articleSelectStatus:true,
        formArticle:{
          title:''
        },
        articleBox:false,
        scpage:1,
        tableData: [],
        tempData:[],
        multipleSelection: [],
        radio:'',
        radioValue:[],
        selected:{}

      };
    },
    created() {
      this.region_no = this.$urlData.region_no
      this.getInfo();
    },
    watch:{

    },

    methods: {
      //滑动加载
      loadMore2: function() {
        var that = this;
        that.busy = true
        setTimeout(() => {
          that.scpage++;
          this.searchSource();
        }, 1000)

      },
      searchSource () {
        var that=this;
        util.requests("post", {
          url: "wxset/getImageTextList2",
          data:{page:that.scpage, region_no:that.region_no}
        }).then(res => {
          if (res.code == 1) {
            console.log(res);
            console.log(that.scpage);
            if(res.data.list){
              that.scrollDisabled = false // 将无限滚动关闭
              that.listData = that.listData.concat(res.data.list)
              that.articleBox = false;
            }else{
              that.scrollDisabled = true
              if(that.scpage==1){
                that.articleBox = true;
              }
            }
            this.busy = false
          } else {
            // this.busy = false
            util.Message.error(res.msg);
          }
        });
      },

      //选择公众号素材库素材
      //从素材库中选择bs
      selectMaterialId: function() {
        this.materialBox = true;
        var that = this;
        util.requests("post", {
          url: "wxset/getImageTextList2",
          data: {region_no:that.region_no}
        }).then(res => {
            console.log(res);
            that.setDataArr({
              listData:res.data.list
            });
          });
      },

      //素材库图文确认按钮
      onImageTextSure(){
        this.materialBox=false;
        this.articleList = this.listData[this.selectArticleIndex].val;
        this.menu.button[this.selectedMenuIndex].sub_button[this.selectedSubMenuIndex].media_id = this.listData[this.selectArticleIndex].media_id
        this.menu.button[this.selectedMenuIndex].sub_button[this.selectedSubMenuIndex].title = this.listData[this.selectArticleIndex].title

      },
      //关闭选择素材库
      closeArticle:function(){
        let arr = [],arr2 = [];
        for(let item in this.listData){
          arr.push(false);
          arr2.push(false);
        }
        this.setDataArr({
          sure:arr,
          mask:arr2,
          selectArticleIndex:''
        })
        this.articleSelectStatus = true;
      },
      //选择素材中的搜索事件
      onMaterialTitleSerch:function(){
        // this.page = 0;
        console.log(this.formArticle);
        this.selectArticleIndex = '';
        this.articleSelectStatus = true;
        var that = this;
        util.requests("post", {
          url: "wxset/getImageTextList2",
          data:{title:this.formArticle.title, region_no:that.region_no}
        }).then(res => {
          console.log(res);
          that.setDataArr({
            listData:res.data.list
          });
          return ;


          if (res.code == 1) {
            var button = [];
            if(res.data.list[0].val != ""){
              res.data.list[0].val.forEach(item=>{
                if(typeof(item.sub_button) == 'undefined' || item.sub_button == null){
                  item.sub_button=[];
                }
              })
              button = res.data.list[0].val;
              console.log(res.data.list[0].val);
            }
            that.setDataArr({
              menu: {button:button}
            });
            console.log(that.menu);
          } else {
            util.Message.error(res.msg);
          }
        });

      },
      //选择素材确认按钮
      articleClick:function(index){
        console.log(index);
        this.selectArticleIndex = index;
        this.articleSelectStatus = false;

        let arr = [];
        for(let item in this.listData){
          arr.push(false);
        }
        arr[index] = true;
        this.setDataArr({
          sure:arr
        })
      },
      //鼠标移入图文列表，显示“遮罩”
      onMouseOver:function(index){
        this.mask[index] = true;
        let arr = [];
        for(let item in this.mask){
          arr.push(this.mask[item])
        }
        this.setDataArr({
          mask:arr
        })

      },

      //鼠标移出图文列表，隐藏“遮罩”
      onMouseout:function(index){
        // console.log(index);
        this.mask[index] = false;
        let arr = [];
        for(let item in this.mask){
          arr.push(this.mask[item])
        }
        this.setDataArr({
          mask:arr
        })
      },


      //素材库图文取消按钮
      onImageTextCancel(){
        this.materialBox=false;
      },
      getRealImgUrl(url){
        return this.$getRealImgUrl(url)
      },
      //重置单选框选中的数据，
      closeMaterial(){
        this.radioStatus = true;
        this.material.title = '';
        this.tableData = this.tempData;
      },
      loadMore () {
        if (this.loadSign) {
          this.loadSign = false
          this.page++
          if (this.page > this.last_page) {
            return
          }
          this.getMaterialList({type:'news',page:this.page});
          setTimeout(() => {
            this.loadSign = true
          }, 1000)
        }
      },


      handleCurrentChange(val,index) {
        this.currentRow = val;
      },

      getCurrentRow(val){
      },
      //确认选择文章
      onSure(){
        if(this.selectedMenuLevel() == 1){
          this.menu.button[this.selectedMenuIndex].title = this.radioValue.title
        }else if(this.selectedMenuLevel() == 2){
          this.menu.button[this.selectedMenuIndex].sub_button[this.selectedSubMenuIndex].title = this.radioValue.title
        }
        this.checkUrlTitle();
        //当前子菜单被选中时
        if(this.selectedSubMenuIndex !== ''){
          this.menu.button[this.selectedMenuIndex].sub_button[this.selectedSubMenuIndex].url = this.radioValue.url;
        }else{
          this.menu.button[this.selectedMenuIndex].url = this.radioValue.url;
        }
        this.dialogTableVisible = false
      },
      //搜索
      onSerch(){
        this.radio = '';
        this.radioStatus = true;
        var v = '';
        var arr = [];
        if(this.material.title.length>0){
          for(let key in this.tempData){
            v = this.tempData[key].title;
            if(v.indexOf(this.material.title) != -1){
              arr.push(this.tempData[key]);
            }
          }
          this.tableData = arr;
        }else{
          this.tableData = this.tempData;
        }
      },
      //分页操作
      pageChange: function(page) {
        let post_data = Object.assign({},this.material,{type:'news'});
        post_data.page = page;
        this.getMaterialList(post_data)
      },
      showRow(row){
        this.radioStatus = false;
        this.radio = this.tableData.indexOf(row);
        this.radioValue = row;
      },
      toggleSelection(rows) {
        if (rows) {
          rows.forEach(row => {
            this.$refs.multipleTable.toggleRowSelection(row);
          });
        } else {
          this.$refs.multipleTable.clearSelection();
        }
      },
      handleSelectionChange(val) {
        this.multipleSelection = val;
      },
      //获取微信菜单数据
      getInfo() {
        var that = this;
        util.requests("post", {
          url: "wxset/menuInfo",
          data: { key: "wechat_menu" ,region_no:that.region_no}
        })
        .then(res => {
          if (res.code == 1) {
            var button = [];
            if(res.data.info.val != ""){
              res.data.info.val.forEach(item=>{
                if(typeof(item.sub_button) == 'undefined' || item.sub_button == null){
                  item.sub_button=[];
                }
              })
              button = res.data.info.val;
            }
            that.setDataArr({
              menu: {button:button}
            });
          } else {
            util.Message.error(res.msg);
          }
        });
      },
      //验证url
      validateUrl(value){
        var reg=/^((ht|f)tps?):\/\/([\w-]+(\.[\w-]+)*\/?)+(\?([\w\-\.,@?^=%&:\/~\+#]*)+)?$/;
        if(!reg.test(value)){
          return false;
        }
        return true;
      },
      //验证url方法绑定在输入框
      checkUrl:function(value){
        this.urlTitle = false;
        var rs = this.validateUrl(value);
        if(!rs){
          util.Message.error('请输入正确的URL');
        }
      },
      //取消提交按钮
      onCancel(){
        this.dialogTableVisible = false;
      },
      //保存提交
      onSubmit() {
        console.log(this.menu);
        var paramUrl = 'wxmenu';
        if(this.menu.button.length ==0){
          paramUrl = 'menuDelete';
        }
        util.requests("post", {
          url: "wxset/"+paramUrl,
          data: { ...this.menu ,region_no:this.region_no}
        }).then(res => {
          {
            if (res.code == 1) {
              util.Message.success(res.msg);
            } else {
              util.Message.error(res.msg);
            }
          }
        });
      },

      //验证URL重置表单url数据
      cardingData(){
        this.menu.button.forEach(item=> {
          if (item.sub_button.length != 0) {
            item.sub_button.forEach(subItem => {
              var rs = this.validateUrl(subItem.url)
              if (!rs) {
                subItem.url = '';
              }
            })
          }
          if (item.url != "") {
            var rs = this.validateUrl(item.url);
            if (!rs) {
              item.url = '';
            }
          }
        })
      },

      //三个菜单点击事件
      selectedMenu: function(i,dotype = '') {
        if(dotype!='sub'){//主菜单
          this.selectedMenuIndex = i;
          this.selectedSubMenuIndex = "";
          var rs_selectedMenu = this.menu.button[this.selectedMenuIndex];
        }else{//子菜单
          this.selectedSubMenuIndex = i;
          var rs_selectedMenu = this.menu.button[this.selectedMenuIndex].sub_button[this.selectedSubMenuIndex];
        }
        //清空选中media_id 防止再次请求
        if ( rs_selectedMenu.media_id != undefined && rs_selectedMenu.media_id != "" && this.selectedMenuType() == "media_id") {
          this.getMaterial(rs_selectedMenu.media_id);
        }
        //检查名称长度
        this.checkMenuName(rs_selectedMenu.name);
      },
      //选中菜单级别
      selectedMenuLevel: function() {
        if (this.selectedMenuIndex !== "" && this.selectedSubMenuIndex === "") {
          //主菜单
          return 1;
        } else if ( this.selectedMenuIndex !== "" && this.selectedSubMenuIndex !== "") {
          //子菜单
          return 2;
        } else {
          //未选中任何菜单
          return 0;
        }
      },
      //获取菜单类型 1. view网页类型，2. media_id类型和view_limited类型 3. click点击类型，4.miniprogram表示小程序类型
      selectedMenuType: function() {
        if (this.selectedMenuLevel() == 1 && this.menu.button[this.selectedMenuIndex].sub_button.length == 0) {
          //主菜单
          return this.menu.button[this.selectedMenuIndex].type
        } else if (this.selectedMenuLevel() == 2) {
          //子菜单
          return this.menu.button[this.selectedMenuIndex].sub_button[this.selectedSubMenuIndex].type
        }
      },
      //添加菜单
      addMenu: function(level) {
        if (level == 1 && this.menu.button.length < 3) { //主菜单
          this.menu.button.push({
            type: "view",
            name: "菜单名称",
            sub_button: [],
            url: ""
          });
          this.selectedMenuIndex = this.menu.button.length - 1;
          this.selectedSubMenuIndex = "";
          this.checkMenuName(this.menu.button[this.selectedMenuIndex].name);
        }
        if ( level == 2 && this.menu.button[this.selectedMenuIndex].sub_button.length < 5 ) { //子菜单
          this.menu.button[this.selectedMenuIndex].sub_button.push({
            type: "view",
            name: "子菜单名称",
            url: ""
          });
          this.selectedSubMenuIndex = this.menu.button[this.selectedMenuIndex].sub_button.length - 1;
          this.checkMenuName(this.menu.button[this.selectedMenuIndex].sub_button[this.selectedSubMenuIndex].name);
        }
      },
      //删除菜单
      delMenu: function() {
        if (
                this.selectedMenuLevel() == 1 &&
                confirm("删除后菜单下设置的内容将被删除")
        ) {
          if (this.selectedMenuIndex === 0) {
            this.menu.button.splice(this.selectedMenuIndex, 1);
            this.selectedMenuIndex = 0;
          } else {
            this.menu.button.splice(this.selectedMenuIndex, 1);
            this.selectedMenuIndex -= 1;
          }
          if (this.menu.button.length == 0) {
            this.selectedMenuIndex = "";
          }
        } else if (this.selectedMenuLevel() == 2) {
          if (this.selectedSubMenuIndex === 0) {
            this.menu.button[this.selectedMenuIndex].sub_button.splice(
                    this.selectedSubMenuIndex,
                    1
            );
            this.selectedSubMenuIndex = 0;
          } else {
            this.menu.button[this.selectedMenuIndex].sub_button.splice(
                    this.selectedSubMenuIndex,
                    1
            );
            this.selectedSubMenuIndex -= 1;
          }
          if (this.menu.button[this.selectedMenuIndex].sub_button.length == 0) {
            this.selectedSubMenuIndex = "";
          }
        }
      },
      //检查菜单名称长度
      checkMenuName: function(val) {
        if (this.selectedMenuLevel() == 1 && this.getMenuNameLen(val) <= 8) {
          this.menuNameBounds = false;
        } else if ( this.selectedMenuLevel() == 2 && this.getMenuNameLen(val) <= 16 ) {
          this.menuNameBounds = false;
        } else {
          this.menuNameBounds = true;
        }
        this.checkUrlTitle();
      },
      checkUrlTitle:function(){
        this.urlTitle = false;
        if (this.selectedMenuLevel() == 1) {
          if(typeof(this.menu.button[this.selectedMenuIndex].title) !== 'undefined'){
            this.urlTitle = true;
          }
        } else if ( this.selectedMenuLevel() == 2 ) {
          if(typeof(this.menu.button[this.selectedMenuIndex].sub_button[this.selectedSubMenuIndex].title) !== 'undefined'){
            this.urlTitle = true;
          }
        } else {
          this.urlTitle = false;
        }
      },
      //获取菜单名称长度
      getMenuNameLen: function(val) {
        var len = 0;
        for (var i = 0; i < val.length; i++) {
          var a = val.charAt(i);
          a.match(/[^\x00-\xff]/gi) != null ? (len += 2) : (len += 1);
        }
        return len;
      },


      //选择公众号图文链接
      selectNewsUrl: function(val) {
        this.dialogTableVisible = true
        if(this.last_page !==''){//开启静态加载
          if(this.page>=this.last_page){
            return ;
          }
        }
        this.getMaterialList({type:'news',page:this.page});
      },
      //获取微信素材库列表
      getMaterialList:function(material = {}){
        var that=this;
        that.loadingData = true;
        util.requests("post", {
          url: "wxset/getMaterialList",
          data:{...material, region_no:that.region_no}
        }).then(res => {
          {
            that.loadingData = false;
            if (res.code == 1) {
              for(let key in res.data.list){
                that.tableData.push(res.data.list[key]);
              }
              that.setDataArr({
                tempData:that.tableData,
                last_page:res.data.last_page
              })
            } else {
              util.Message.error(res.msg);
            }
          }
        });
      },
      //设置选择的素材id
      setMaterialId: function(id, title, url) {
        if (this.selectedMenuLevel() == 1) {
          Vue.set(this.menu.button[this.selectedMenuIndex], "media_id", id);
        } else if (this.selectedMenuLevel() == 2) {
          Vue.set(
                  this.menu.button[this.selectedMenuIndex].sub_button[
                          this.selectedSubMenuIndex
                          ],
                  "media_id",
                  id
          );
        }
        this.material.title = title;
        this.material.url = url;
      },
      //删除选择的素材id
      delMaterialId: function() {
        if (this.selectedMenuLevel() == 1) {
          this.menu.button[this.selectedMenuIndex].media_id = "";

        } else if (this.selectedMenuLevel() == 2) {
          this.menu.button[this.selectedMenuIndex].sub_button[
                  this.selectedSubMenuIndex
                  ].media_id = "";
          this.menu.button[this.selectedMenuIndex].sub_button[this.selectedSubMenuIndex].title = ""
        }
        this.menu =  Object.assign({}, this.menu);

      },
      //设置选择的图文链接
      setNewsUrl: function(url) {
        if (this.selectedMenuLevel() == 1) {
          Vue.set(this.menu.button[this.selectedMenuIndex], "url", url);
        } else if (this.selectedMenuLevel() == 2) {
          Vue.set(
                  this.menu.button[this.selectedMenuIndex].sub_button[
                          this.selectedSubMenuIndex
                          ],
                  "url",
                  url
          );
        }
      },
    }
  };
</script>


<style lang="scss" scoped>
  @import "src/styles/wxmenu.scss";
  ._container {
    margin-top: 20px;
    padding: 20px;
    background: #fff;
    min-height: calc(100vh - 90px);
    .mapeditor-title {
      line-height: 40px;
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
      }
    }
    .article-list{
      width: 287px;
      float: left;
      margin-right:10px;
      position: relative;
    }
    .big-img-box{
      width:284px;
      height:180px;
      background-color:white;
      border:none;
      //position: relative;
    }
    .big-img-box-inline{
      width:256px;
      height:144px;
      margin:0 auto;
      position: relative; /*脱离文档流*/
      top: 50%; /*偏移*/
      transform: translateY(-50%);
      background-size: cover;
      background-position: 50% 50%;
      background-repeat: no-repeat;
      background-color: #ACADAE;
    }
    .card_appmsg_title {
      position: absolute;
      left: 15px;
      right: 15px;
      bottom: 15px;
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
      color: #fff;
      font-weight: 400;
      z-index: 1;
    }
    .small-img-box{
      width:284px;
      height:84px;
      background-color:white;
    }
    .card_appmsg{
      width:256px;
      height:48px;
      margin:0 auto;
      position: relative; /*脱离文档流*/
      top: 50%; /*偏移*/
      transform: translateY(-50%);
    }
    .weui-desktop-vm_primary{
      float: left;
      width:208px;
      height:100%;
    }
    .card_appmsg_title {
      position: absolute;
      left: 15px;
      right: 15px;
      bottom: 15px;
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
      color: #fff;
      font-weight: 400;
      z-index: 1;
    }
    .card_appmsg_thumb{
      float: left;
      width:48px;
      height:100%;
      background-size: cover;
      background-position: 50% 50%;
      background-repeat: no-repeat;
      background-color: #f6f8f9;
    }
    .edit_mask{
      font-size: 0;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.6)!important;
      filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='#99000000',endcolorstr = '#99000000');
      color: #fff;
      z-index:100;
      text-align: center;
      padding: 14px;
    }
    .appmsg_info{
      text-align-last: auto;
      font-size: 13px;
      line-height: 20px;
      margin: 0 14px;
      padding: 12px 0;
      border-bottom: 1px solid #e7e7eb;
    }
    .appmsg_date {
      font-weight: 400;
      font-style: normal;
    }
    .icon_card_selected {

      background: url('https://res.wx.qq.com/mpres/zh_CN/htmledition/comm_htmledition/style/base/base_z46c7e7.png') 0 -5427px no-repeat;
      width: 46px;
      height: 46px;
      vertical-align: middle;
      display: inline-block;
      position: absolute;
      top: 50%;
      left: 50%;
      margin-top: -23px;
      margin-left: -23px;
      line-height: 999em;
      overflow: hidden;
      z-index: 1;
    }

  }
  .el-tabs el-tabs--top el-tabs--border-card{
    box-shadow:none;
  }
</style>
<!--background: url(/static/wxmenu_images/base_z46c7e7.png) 0 -5427px no-repeat;-->

