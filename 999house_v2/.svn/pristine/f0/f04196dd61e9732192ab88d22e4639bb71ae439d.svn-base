
const $api = (function() {
	/**
	 * JS引入其他组件
	 * 组件js与css放在同路径下
	 * components(路径): array/string
	 */
	const addComponents = (components) =>{
		const type = typeof components;
		
		return new Promise( (res,rej)=>{
			if( ( type == 'object' && isArray(components) ) || type == 'string' ){
				 
				let doc = document;

				if( isArray(components) ){
					components.map((item,index) =>{
						let script = doc.createElement('script');
						let css = doc.createElement('link');
						script.setAttribute("type","text/javascript");
						script.setAttribute("src",item+'.js');
						css.setAttribute("rel","stylesheet");
						css.setAttribute("type","text/css");
						css.setAttribute("href",item+'.css');
						doc.getElementsByTagName("body")[0].appendChild(css);
						doc.getElementsByTagName("body")[0].appendChild(script);
					});
				} else {
					let script = doc.createElement('script');
					let css = doc.createElement('link');
					script.setAttribute("type","text/javascript");
					script.setAttribute("src",components+'.js');
					css.setAttribute("rel","stylesheet");
					css.setAttribute("type","text/css");
					css.setAttribute("href",components+'.css');
					doc.getElementsByTagName("body")[0].appendChild(css);
					doc.getElementsByTagName("body")[0].appendChild(script);
				}
				
				window.onload = ()=>{
					res();
				};
			} else {
				rej('param type err');
			}
		})
	}
	
	// 深度克隆
	const deepClone = (obj) =>{
		if(typeof obj !== "object" && typeof obj !== 'function') {
			//原始类型直接返回
			return obj;        
		}
		var o = isArray(obj) ? [] : {}; 
		for(let i in obj) {  
			if(obj.hasOwnProperty(i)){ 
				o[i] = typeof obj[i] === "object" ? deepClone(obj[i]) : obj[i]; 
			} 
		} 
		return o;
	}
	
	const isArray = (arr) =>{
		return Object.prototype.toString.call(arr) === '[object Array]';  
	}
	
	// 判断是否为空
	const isEmpty = (value) =>{
		switch (typeof value) {
			case 'undefined':
				return true;
			case 'string':
				if (value.replace(/(^[ \t\n\r]*)|([ \t\n\r]*$)/g, '').length == 0) return true;
				break;
			case 'boolean':
				if (!value) return true;
				break;
			case 'number':
				if (0 === value || isNaN(value)) return true;
				break;
			case 'object':
				if (null === value || value.length === 0) return true;
				for (var i in value) {
					return false;
				}
				return true;
		}
		return false;	
	}
	
	// 缓存
	const local = {
		localSet: ( name, val ) =>{
			localStorage.setItem(name, val);
		},
		localGet: ( name ) =>{
			return localStorage.getItem(name);
		}
	}
	
	const session = {
		sessionSet: ( name, val ) =>{
			sessionStorage.setItem(name, val);
		},
		sessionGet: ( name ) =>{
			return sessionStorage.getItem(name);
		}
	}
	
	// 跳转
	const goPage = (url,data) =>{
		console.log(data)
		if( data ){
			let option = '?';
			
			for( let i in data ){
				option += `${i}=${encodeURI(data[i])}&`;
			}
			
			option = option.substring(0,option.length-1);
			
			url += option;
		}
		
		console.log(url);

		window.location.href = url;
	}
	
	// 文字去除空格
	const trim = (str, pos = 'both') =>{
		if (pos == 'both') {
			return str.replace(/^\s+|\s+$/g, "");
		} else if (pos == "left") {
			return str.replace(/^\s*/, '');
		} else if (pos == 'right') {
			return str.replace(/(\s*$)/g, "");
		} else if (pos == 'all') {
			return str.replace(/\s+/g, "");
		} else {
			return str;
		}
	}
	
	// html特殊字符转换
	const htmlEscape = (val) =>{
		return val.replace(/[<>&"]/g, function (c) { return { '<': '&lt;', '>': '&gt;', '&': '&amp;', '"': '&quot;' }[c]; });
	}
	
	// 删除url某项参数 返回 新url + 参数对象
	const funcUrlDel = (names) =>{
		if(typeof(names)=='string'){
			names = [names];
		}
		
		const loca = window.location;
		const obj = {}
		const arr = loca.search.substr(1).split("&");
		const newData = {};
		
		//获取参数转换为object
		for(var i = 0; i < arr.length; i++) {
			arr[i] = arr[i].split("=");
			obj[arr[i][0]] = arr[i][1];
		};
		
		//删除指定参数
		if( names ){
			for(let i = 0; i < names.length; i++) {
				 delete obj[names[i]];
			}
		}
		
		// 排序
		Object.keys(obj).sort().map(key => {
			newData[key]=decodeURI(obj[key])
		})
		
		//重新拼接url
		const url = loca.origin + loca.pathname + "?" + JSON.stringify(newData).replace(/[\"\{\}]/g, "").replace(/\:/g, "=").replace(/\,/g, "&");
		
		return { url: url , option: newData };
	}
	
	// 手机号加密
	const encryptPhone = ( phone ) =>{
		
		phone = String(phone);
		
		return phone.replace(phone.substring(3,7), "****");
	}


	return {
		trim,
		goPage,
		isEmpty,
		deepClone,
		funcUrlDel,
		htmlEscape,
		encryptPhone,
		addComponents,
		
		localSet: local.localSet,
		localGet: local.localGet,
		sessionSet: session.sessionSet,
		sessionGet: session.sessionGet,
	};
}());

// 公共方法映射到Vue原型链; 
Vue.prototype.$api = $api;