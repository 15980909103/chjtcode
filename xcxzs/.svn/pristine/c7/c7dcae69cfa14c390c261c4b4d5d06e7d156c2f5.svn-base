// calendar
let calendar=null;
let isInitCalendar = true;
let weekTd1="<div>日</div><div>一</div><div>二</div><div>三</div><div>四</div><div>五</div><div>六</div>";
let weekTd2="";
const monthDict=["","一月份","二月份","三月份","四月份","五月份","六月份","七月份","八月份","九月份","十月份","十一月份","十二月份"];

Vue.component('use-calendar', {
  template: `
    <div id="calendar" class="calendar">
        <div id="calendar-panel" class="calendar-panel">
            <div id="my-week" v-html="weekTd"></div>
            <div id="calendar-month-wrap" class="calendar-month-wrap fix">
                <div id="calendar-month" class="calendar-month fix">
                    <div id="calendar-month-viewA" class="calendar-month-view"></div>
                    <div id="calendar-month-viewB" class="calendar-month-view"></div>
                    <div id="calendar-month-viewC" class="calendar-month-view"></div>
                </div>
            </div>
            <div id="calendar-week" class="calendar-week">
                <div id="calendar-week-viewA" class="calendar-week-view"></div>
                <div id="calendar-week-viewB" class="calendar-week-view"></div>
                <div id="calendar-week-viewC" class="calendar-week-view"></div>
            </div>
        </div>
        <div id="calendar-expand" class="calendar-expand" v-cloak>
          {{ expandText }}
          <span class="calendar-expand-today" v-tap="{methods: onGoToday}" v-if="isToday">今</span>
          <img :class="['calendar-expand-img', is_month? 'up': 'down']" src="../../static/image/dianzhang-xiangxia.png">
          <span class="calendar-expand-month" v-cloak>{{ selectMonth }}</span>
        </div>
    </div>
  `,
  props: {
    staticPath: {
      type: String,
      default: './'
    }
  },
  data() {
    return {
      weekTd: '',
      isToday: false,
      isMonth: true,
      expandText: '',
      selectMonth: '',
      toggleData:{
          year: '',
          month: '',
          day: ''
      },
      is_month: false
    }
  },
  mounted() {
    this.initStyle();
    this.initCalendar();
  },
  computed: {
    currentDate() {
        const date = this.toggleData;
        return `${date.year}-${date.month}-${date.day}`;
    },
  },
  watch:{
    isMonth: {
        handler: function(val, oldVal) {
            if(val) {
              this.weekTd=weekTd1;
            }
            else {
              this.weekTd=weekTd2;
            }
        },
        immediate: true
    },
    currentDate(value) {
      if (!isInitCalendar) {
        this.$emit('change', value);
      }
    }
  },
  methods: {
    initStyle() {
      var css = document.createElement('style');
      css.type='text/css';
      css.setAttributeNode(document.createAttribute('scopped'));
      css.appendChild(document.createTextNode(`@import "${this.staticPath}static/css/components/use-calendar.css";`));
      this.$el.appendChild(css);
    },
    initCalendar() {
      //日历初始化
      calendar = new Calendar(
          document.getElementById('calendar-panel'),
          null,
          (self, dateRange) => {
              var nowMonth = self.activeView.currentTime.month;
              this.selectMonth = monthDict[nowMonth];
              if(self.activeView.name === 'month'){
                  if(isInitCalendar){
                      var date = self.date;
                      this.setExpandText(date.year,date.month,date.day);
                  }
              }
          },
          (res) => {
              if(res.activeView.name=="month"){
                  this.is_month=true;
              }else{
                  this.is_month=false;
              }
              var toggleData=this.toggleData;
              this.setExpandText(toggleData.year,toggleData.month,toggleData.day);
          }
      );
      calendar.init();
      document.getElementById('calendar-expand').onclick = () => {
          calendar.toggle(this.toggleData);
      };
      $("#calendar").on('click','.i-day',(e) => {
          this.isDisplayCalendar = false;
          var element = $(e.currentTarget);
          var year = element.data('year');
          var month = element.data('month');
          var day = element.data('day');
          this.setExpandText(year, month, day);
      });
      calendar.toggle(this.toggleData);
      calendar.toggle(this.toggleData);
      calendar.toggle(this.toggleData);
      isInitCalendar = false;
    },
    onGoToday(){
        var myDate = new Date();
        var year=myDate.getFullYear();
        var month=myDate.getMonth()+1;
        var day=myDate.getDate();
        if(this.is_month){
            calendar.setTime(year,month);
            calendar.resize();
        }else{
            //周日历开始为周日
            var tmpDate=new Date(year+'/'+month+'/'+day);
            var tmpxq=tmpDate.getDay();
            if(tmpxq>0){
                tmpDate.setDate(tmpDate.getDate() - tmpxq);
                calendar.activeView.setTime(tmpDate.getFullYear(),tmpDate.getMonth()+1,tmpDate.getDate());
            }else{
                calendar.activeView.setTime(year,month,day);
            }
            calendar.render();
        }
        this.setExpandText(year,month,day);
    },
    //设置日期与周几
    setExpandText(year,month,day){
        var sDate = new Date(year+'/'+month+'/'+day);
        var weekStart = sDate.getDay();
        var weekday=["周日","周一","周二","周三","周四","周五","周六"];
        this.expandText=month+'月'+day+'日 '+weekday[weekStart];
        this.toggleData={year:year,month:month,day:day};
        this.selectMonth=monthDict[parseInt(month)];

        //判断点击的日期是否今天
        var myDate = new Date();
        var newYear = myDate.getFullYear();
        var nMonth = myDate.getMonth()+1;
        var nDay = myDate.getDate();
        if(newYear==year && nMonth==month && nDay==day){
            this.isToday=false;
        }else{
            this.isToday=true;
        }
        //选中类
        $('#calendar').find(".i-tick").removeClass('i-tick');
        $("[data-year='"+year+"'][data-month='"+month+"'][data-day='"+day+"']").find('span').addClass('i-tick');
        // if(isclick) {
        //     isclick = false;
        //     setTimeout(function () {
        //         isclick = true;
        //     }, 500);
        //     this.onGetSortData(year+'-'+month+'-'+day);
        // }
    },
    getPath(path) {
      return `${this.staticPath}/${path}`;
    }
  }
})
function setsetScrollHeight(scrollPanel='') {  //设置滚动区域高度
  if (scrollPanel) {
    var clientHeight = document.documentElement.clientHeight;    //页面高度
    var swiperHeight = $(scrollPanel).offset().top;  //滚动区域距顶部距离
    var diffHeight=clientHeight-swiperHeight;
    // $(`${scrollPanel} .scroll-content`).css('height', diffHeight+'px');
    $(scrollPanel).css('height', diffHeight+'px');
  }
}
