 /* 本地存储与本地会话存储操作 */
 const myStorage={
	funcall:function (type){
		let typecall=''
		if(type=='local'){
			typecall=localStorage
		}
		if(type=='session'){
			typecall=sessionStorage
		}
		return {
			//新增key-value,若key已存在,则更新value;expirsetime过期时间单位秒
			set : function (name, val ,expirsetime=0) {
				if(type=='local'){//如果是本地存储加入有效期时间
					if(expirsetime){
						expirsetime=new Date().getTime()+Number(expirsetime)*1000
					}
					val={value:val,expirsetime:expirsetime}
				}
				typecall.setItem(name, JSON.stringify(val))
			},
			//对已有的key-value加进新的value
			add : function (name, addVal) {
				let oldVal = funcall(type).get(name)
				let newVal = oldVal.concat(addVal)
				funcall(type).set(name, newVal)
			},
			//根据key获取对应的值;
			get : function (name) {
				var val=JSON.parse(typecall.getItem(name))
				if(type=='local'){//如果是本地存储加入有效期时间
					if(val){
						if (val.expirsetime&&new Date().getTime()>=val.expirsetime) {
							localStorage.removeItem(name);//过期删除
							val=null
						} else {
							val= val.value;
						}
					}
				}
				return val
			},		
			//根据key移除对应的值		
			del:function (name) {
				typecall.removeItem(name);
			},
			//移除全部key-value
			clear:function () {
				typecall.clear();
			},	
			//根据索引获取对应key
			getkey:function (index) {
				typecall.key(index);
			},	
		}
	}
}
myStorage.local={
	...myStorage.funcall('local')
}
myStorage.session={
	...myStorage.funcall('session')
}
/* 本地存储与本地会话存储操作end */

export default{
	...myStorage
}