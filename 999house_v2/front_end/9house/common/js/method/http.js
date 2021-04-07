
const domain = '';
const loginHrefHost = '';

const $http = (function() {
	const request = (options)=>{
		options = options ||{};  //调用函数时如果options没有指定，就给它赋值{},一个空的Object
		options.method = (options.method || "GET").toUpperCase();  // 请求格式GET、POST，默认为GET
		options.dataType = options.dataType || "json";   //响应数据格式，默认json
		options.timeout = options.timeout || 10000;

		let xhr,
			params = formatParams(options.data);	//options.data请求的数据
		
		//考虑兼容性
		if(window.XMLHttpRequest){
			xhr=new XMLHttpRequest();
		}else if(window.ActiveObject){	//兼容IE6以下版本
			xhr=new ActiveXobject('Microsoft.XMLHTTP');
		}
		
		if( options.header ){
			for( let key in options.header ){
				xhr.setRequestHeader(key, options.header[key]);
			}
		}

		//启动并发送一个请求
		if(options.method=="GET"){
			xhr.open("GET", options.url+"?" + params, true);
			xhr.send(null);
		}else if(options.method=="POST"){
			xhr.open("post", options.url, true);

			// 设置表单提交时的内容类型
			// Content-type数据请求的格式
			xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xhr.send(params);
		}

		// 设置有效时间
		setTimeout(function(){
			if(xhr.readySate !=4 ){
				xhr.abort();
			}
		},options.timeout);

		// 接收
		// options.success成功之后的回调函数  options.error失败后的回调函数
		// xhr.responseText,xhr.responseXML  获得字符串形式的响应数据或者XML形式的响应数据
		xhr.onreadystatechange=function(){
			if(xhr.readyState == 4){
				let status = xhr.status;
				
				if(status >= 200 && status < 300 || status == 304){
					options.success && options.success(xhr.responseText,xhr.responseXML);
				}else{
					options.error&&options.error(status);
				}
			}
		}
	};
	
	const ajax = ( params )=>{
		
		let token = localStorage.getItem('u-Token');
		let cityNo = localStorage.getItem('u-cityNo');
		
		let { 
			url = domain + params.url,
			method = 'POST', 
			data, 
			header = {
				'XX-CityNo': cityNo,
				'XX-Token': token,
				'XX-Device-Type': 'wxh5'
			},
			timeout = 10000
		} = params;
		
		return new Promise( (resolve,reject) =>{
			request({
				url: url,
				type: method,
				data: data,
				dataType:'json',
				timeout: timeout,
				header: header,
				contentType:"application/json",
				success:function(res){
					if( res.statusCode == 200 ){
						let data = res.data;
							
						if( data.code != 1 ){
							// console.log(res)
							if( data.code == '50008' ){
								localStorage.removeItem('u-Token');
								this.login();
							} else {
								this.$toast(data.msg)
								reject(res);
							}
						} else {
							resolve(res);
						}
					} else {
						console.log(res)
						reject(res);
					}
		　　　　		
				},
				//异常处理
				error:function(res){
					reject(res);
				}
			})
		});
	};
	
	const login = ( reflash=0 )=>{
		if( isWechat ){	// 微信客户端
		
			let token = localStorage.getItem('u-Token');
			let code = this.getUrlParamValue('code');
			let state = this.getUrlParamValue('state');
			
			if( reflash==1 || !code || !state ){
				
				let href = loginHrefHost + window.location.search;
				
				window.location.href = domain+'index/public/wxLogin'+'?redirect_uri='+encodeURI(href)
			}else{

				let sendData = {
						code: code,
						state: state,
					};
				
				ajax({
					method: 'GET',
					url: domain+'index/public/wxH5UserLogin',
					data: sendData,
				}).then( res=>{
					
					let data = res.data;

					console.log(res)
					
					// if( data.code != 1 ){
					// 	if( data.msg && (data.msg.indexOf('40163-code') != -1 || data.msg.indexOf('40029-invalid') != -1) ){
							
					// 		localStorage.removeItem('u-Token');
							
					// 		login(1);
							
					// 	} else {
					// 		console.log( data.msg)
					// 	}
					// } else {

					// 	console.log('登录成功');
						
					// 	this.storage('set','token', data.data.token);
					// 	this.storage('set','sid', data.data.sid);
						
					// 	let userbaseinfo = {
					// 		nickname: data.data.nickname,
					// 		headimgurl: data.data.headimgurl,
					// 		uid: data.data.uid,
					// 	}
					// 	// console.log(userbaseinfo)

					// 	localStorage.setItem('userbaseinfo', userbaseinfo)
						
					// }
				}).catch( res=>{
					console.log(res)
				})

			}
			
		}
	};

	//格式化请求参数
	const formatParams = (data)=>{
		let arr=[];
		for(let name in data){
			arr.push(encodeURIComponent(name)+"="+encodeURIComponent(data[name]));
		}
		
		arr.push(("v="+Math.random()).replace(".",""));
		
		return arr.join("&");
	}
	
	//判断是否在微信中
	const isWechat = ()=>{
	    const ua = window.navigator.userAgent.toLowerCase();  
	    if (ua.match(/micromessenger/i) == 'micromessenger') {  
	        // console.log('是微信客户端')  
	        return true;  
	    } else {  
	        // console.log('不是微信客户端')  
	        return false;  
	    }
	};
	
	const getUrlParamValue = (name)=>{
		if (name == null || name == 'undefined') { return null };
		
		let searchStr = decodeURI(location.search);
		
		let infoIndex = searchStr.indexOf(name + "=");
		
		if (infoIndex == -1) { return null };
		
		let searchInfo = searchStr.substring(infoIndex + name.length + 1);
		
		let tagIndex = searchInfo.indexOf("&");
		
		if (tagIndex!= -1) { searchInfo = searchInfo.substring(0, tagIndex); }
		
		return searchInfo;
	};
	
	return {
		ajax,
		isWechat,
		getUrlParamValue
	};
}());

const http = {
	install: function(Vue) {
        Vue.prototype.$http = $http;
    },
}

// 登录混入
const loginMixin = {
	created (){
		console.log(998)
	}
}

export { http, loginMixin }; 
