const formatJson=function(filterVal, jsonData) {
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
var downloadLoading_flag=false;
const export_json_to_excel= function (e={}) { //导出已载好的数据
	  var obj_set={
		filename:'',
		tHeader:[],
		filterVal:[],
		bookType:'xlsx', //文件类型 可选(txt,xlsx,csv)
		tableData:[]
	  }
	  obj_set=Object.assign({},obj_set,e)
	  console.log(obj_set)
	  
	  if(downloadLoading_flag){
		  console.log('==========正在导出中=========')
		 return 
	  }
	  downloadLoading_flag = true;
      import("@/vendor/Export2Excel").then(excel => {
        const tHeader = obj_set.tHeader; //标题行
        const filterVal = obj_set.filterVal; //对应数据名
        const tableData = obj_set.tableData;
        const data = formatJson(filterVal, tableData);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: obj_set.filename, //到处的文件名
          autoWidth: true, //自动宽度
          bookType: "xlsx" //文件类型 可选(txt,xlsx,csv)
        });
        downloadLoading_flag = false;
      });
} 
export default{
	export_json_to_excel,
}