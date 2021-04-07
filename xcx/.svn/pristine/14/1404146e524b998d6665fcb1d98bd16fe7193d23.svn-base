//数组操作函数
const ArrayFun={
	//获取数组某一属性列的值
	column:function(arr,key_name){
		//获取key一列
		var len = arr.length
		var result = [];
		for (var i = 0; i < len; i++) {
			result[i] = arr[i][key_name]
		};
		return result
	},
	/**
	 * 数组根据某个属性排序
	 * @param {*} arr  操作的数组
	 * @param {*} attr 要排序的属性
	 * @param {*} type 默认升序排列 asc/desc
	 */
	sortBy: function(arr,attr,type='asc'){
			//进行属性对比
			function mySort(attr,type){
				var rev = (type == 'asc') ? 1 : -1; //升序:降序	
				return function(a,b){
						a = a[attr];
						b = b[attr];
						if(a < b){
								return rev * -1;
						}
						if(a > b){
								return rev * 1;
						}
						return 0;
				}
			}

			var newarr= JSON.parse(JSON.stringify(arr))
			newarr.sort(mySort(attr,type)) 
			return newarr
	},

	/**
	 * 判断数组是否含某值
	 * @param {*} arr 数组
	 * @param {*} val 搜索值
	 */
  indexOfArr:function (arr,val) {
    for (var i = 0; i < arr.length; i++) {
      if (arr[i] == val) {
				return i;
			}
    }
    return -1;
	},
	/**
	 * 判断数组除了某个下标之外是否含某值
	 * @param {*} arr 数组
	 * @param {*} val 搜索值
	 * @param {*} withoutIdx 排除的索引值 
	 */
	indexOfArrWithOutIndex:function(arr,val,withoutIdx){
		for (var i = 0; i < arr.length; i++) {
      if (arr[i] == val &&withoutIdx!=i) {
				return i;
			}
    }
    return -1;
	},
	/**
	 * 获取指定数组属性的最大最小值
	 * @param {*} arr 
	 * @param {*} attr 
	 * @param {*} type max,min
	 */
	getMaxOrMin: function(arr,attr,type='max'){
		var attrList= this.column(arr,attr)
		if(attrList.length==0){return 0}
		
		if(type=='max'){
			return Math.max.apply( Math, attrList )
		}else{
			return Math.min.apply( Math, attrList );
		}
	},

	//数组去重
	unique : function (arr){
		var res = [];
		var obj = {};
		for(var i=0; i<arr.length; i++){
		 if( !obj[arr[i]] ){
			obj[arr[i]] = 1;
			res.push(arr[i]);
		 }
		} 
		return res;
	 },
  //针对一维数组合并去重
  MergeArrayUnique:function (arr1, arr2) {
    var arr = arr1.concat(arr2);
  	arr = this.unique(arr);//再引用上面的任意一个去重方法
    return arr;
	},
	// 交集
	intersectionArray : function(arr1,arr2){
		return arr1.filter(function(v){ return indexOfArr(arr2,v) > -1 })
	},
	// 差集
	differenceArray : function(arr1,arr2){
		return arr1.filter(function(v){ return indexOfArr(arr2,v) === -1 })
	},
  
  //针对一维数组删除指定的值
  removeArrayVal: function (arr, val) {
    let indexOfArr = function(a,v) {
      for (var i = 0; i < a.length; i++) {
        if (a[i] == v) return i;
      }
      return -1;
    }
    var index = indexOfArr(arr,val);
    if (index > -1) {
      arr.splice(index, 1);
    }
    return arr;
	},
	//将二位数组转成指定key下的数组
	ArraytoKeyObj: function(arr,key_name){
		var newarr= JSON.parse(JSON.stringify(arr))
		var obj={}
		for(var i in newarr){
			if(newarr[i][key_name]){
				obj[newarr[i][key_name]]=newarr[i]
			}
		}
		return obj
	},
  //数据复制为纯数组
  copytoArray: function (arr) {
		if(typeof(arr)=='object'){
			let o={}
		//Object.assign(o,arr) 
			o=JSON.parse(JSON.stringify(arr))
			arr=[]
			for(let i in o){
				arr.push(o[i])
			} 
			return arr
				
		}else{
			return arr
		}
  }
}

export default ArrayFun