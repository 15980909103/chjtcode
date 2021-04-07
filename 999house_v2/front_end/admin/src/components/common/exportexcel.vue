<template>
	<span>
    <span class="exportexcel-btn">
      <el-button class="defult" @click="exportExcel">导出</el-button>
    </span>

    <el-dialog class="dialog-progressbox" title="导出进度" :visible.sync="exportExcelInt.progressShow" width="500px" :close-on-click-modal="false" :close-on-press-escape="false" @close="dialogClose">
        <div class="progressbox">
            <el-progress type="circle" :percentage="Number(exportExcelInt.percentage)" ></el-progress>
        </div>         
    </el-dialog>
	</span>  
</template>

<script>
var util = require("../../utils/util.js");
/* setArr:{
      url,filename,total,pagesize,tHeader:[],filterVal:[],ajaxSuccess:funciton(response){}
    }
  */
export default {
  name: "exportexcel-box",
  props: ["setArr"],
  data() {
    return {
      exportExcelInt: {
        progressShow: false,
        percentage: 0,
        loading: false
      }
    };
  },
  methods: {
    //取消继续导出
    dialogClose: function() {
      this.exportExcelInt.progressShow =false 
      this.exportExcelInt.loading = false;
    },
    //导出操作
    exportExcel: function() {
      var self = this;
      if (!self.setArr.post || !self.setArr.post.url) {
        console.log("缺少url");
        return;
      }
      var url = self.setArr.post.url;
      if (!self.setArr.filename) {
        console.log("缺少要保存的filename设置");
        return;
      }

      if (self.exportExcelInt.loading == true) {
        //正在导出中
        return;
      }
      self.exportExcelInt.loading = true;
      self.exportExcelInt.progressShow = true;
      self.exportExcelInt.percentage = 0; //设置当前进度百分比为0
      
      //计算分几次请求
      var request_times = Math.ceil(
        self.setArr.post.total / self.setArr.post.pagesize
      ); 
      //设置最大请求数
      if(request_times>10){
        self.dialogClose()
        self.$message.error('导出的数据量太大，可缩小所需要的时间段')
        return
      }

      var funcs = []; //Promise.all要用到的参数, 存放每次请求的Promise对象
      var complete_count = 0; //成功请求次数

      function doSetTimeout(i, resolve, reject) {
        setTimeout(function() {
          var params = {}; //post的参数data
          params = self.setArr.post.data ? self.setArr.post.data : {};
          params.page = i;
          if (self.exportExcelInt.loading == false) {
            //关闭时强制取消后面的请求
            return;
          }

          util.requests("post", { url: url, data: params }, self).then(function(response) {
              var rsdata = []; //存储此次返回的总数据
              rsdata = self.setArr.ajaxSuccess(response);

              if (response.status == 1||response.code == 1) {
                complete_count++; //成功请求次数+1
                self.exportExcelInt.percentage =
                  (100 * complete_count) / request_times;
                resolve(rsdata);
              } else {
                reject();
              }
            });
        }, i * 1000);
      }

      // 循环请求次数，构造请求的Promise对象集合并插入funcs数组
      for (var i = 1; i <= request_times; i++) {
        var func = new Promise(function(resolve, reject) {
          //定义Promise并处理请求逻辑
          doSetTimeout(i, resolve, reject); //定时请求
        });
        funcs.push(func);
      }

      //使用Promise.all批量调用funcs里面的函数，并合并数据，最后生成Excel
      Promise.all(funcs).then(function(values) {
        var listArr = [];
        //将数据合并
        for (var i = 0; i < values.length; i++) {
          for (var j = 0; j < values[i].length; j++) {
            listArr.push(values[i][j]);
          }
        }

        if (self.exportExcelInt.loading == false) {
          //关闭时强制取消后面操作
          return;
        }

        //将数据导出成excel
        import("@/vendor/Export2Excel").then(excel => {
          const tHeader = self.setArr.tHeader;
          const filterVal = self.setArr.filterVal;
          const list = listArr;
          const data = formatJson(filterVal, list);
          excel.export_json_to_excel({
            header: tHeader,
            data,
            filename: self.setArr.filename,
            autoWidth: true
          });

          self.exportExcelInt.loading = false;
        });
      });

      function formatJson(filterVal, jsonData) {
        return jsonData.map(v =>
          filterVal.map(j => {
            if (j === "timestamp") {
              return parseTime(v[j]);
            } else {
              return v[j];
            }
          })
        );
      }
    }
    //////////////////////////////////////////
  }
};
</script>



<style>
.dialog-progressbox .progressbox {
  text-align: center;
}
</style>