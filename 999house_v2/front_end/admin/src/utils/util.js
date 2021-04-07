import md5 from 'js-md5';//引入md5
import Qs from 'qs'//post data数据格式转换
import router from '@/router'
import request from './request'
import OSS from 'ali-oss'

import exoprtStorage from '../utils/modules/storage'
import exoprtArrayFun from '../utils/modules/ArrayFun'
import exoprtDataFun from '../utils/modules/DataFun'
import exoprtForm from '../utils/modules/form'
import exoprtMessage from '../utils/modules/Message'
import exoprtExportFile from '../utils/modules/ExportFile'
import exoprtTree from '../utils/modules/Tree'
import handleClipboard from '../utils/modules/clipboard'


//固定位数随机字符串,默认32位
export function randomString(len) {
  　　len = len || 32;
    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
  　　var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
  　　var maxPos = $chars.length;
  　　var pwd = '';
  　　for (var i = 0; i < len; i++) {
  　　　　pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
  　　}
  　　return pwd;
  }
 //对象键值排序
 export function objKeySort(obj) {
  var newkey = Object.keys(obj).sort()
  var newObj = {}
  for (var i = 0; i < newkey.length; i++) {
      newObj[newkey[i]] = obj[newkey[i]];
  }
  return newObj;
}
//签名使用
export function makeSign(data){
  data=objKeySort(data)
  if(data.hasOwnProperty('sign')){
	  delete data['sign']
  }
  data=Qs.stringify(data)
  //console.log(data)
  var appkey='jxFLOTYBdFgOzB6ZeXUMOFv1xcdDjcpe'
  data=data+'&key='+appkey
  //console.log(md5(data).toLowerCase(),'-----------',data)
  return md5(data).toLowerCase()
}

import CryptoJS from 'crypto-js'
export const myCrypt= {
	decrypt(str){
		return this.base64_decode(this.base64_decode(str))
	},
	base64_encode(str){
		var src = CryptoJS.enc.Utf8.parse(str);
		var base64string = CryptoJS.enc.Base64.stringify(src);
		return base64string.toString() //转字符串
	},
	base64_decode(base64string){
		var src =CryptoJS.enc.Base64.parse(base64string )
		return CryptoJS.enc.Utf8.stringify(src) //转字符串
	},
}

export const requests = function(method,e,that) {
	if (!method) {
		console.log('缺少设置请求类型')
		return
	}
	if (!e.hasOwnProperty('url')) {
		console.log('缺少url')
		return
	}
	let obj = {}
	/* if (getToken()) {
		e.data = Object.assign(e.data, {
			token: getToken()
		})
	} */


	if (method == 'post') {
		obj = {
			url: e.url,
			method: 'post',
			data: e.data
		}
	} else if (method == 'get') {
		obj = {
			url: e.url,
			method: 'get',
			params: e.data
		}
	}
	if(e.headers){
		obj.headers=e.headers
	}

	return request(obj)
}
export const uploadFileToOss = function(e) {
	let config;
	let file_type = 'image';
	if(e.type){
		file_type = e.type;
	}
	requests("post", {
		url: '/setting/getOss',
		// data: {type: file_type}
	}).then(function(res){
		if(res.code == 1){
			config = res.data.image;
			//console.log(config);
			let client= new OSS({
				accessKeyId: config.access_key_id,
				accessKeySecret: config.access_key_secret,
				bucket: config.bucket,
				region: config.region
			});

			let curr_date = exoprtDataFun.getFormatDate('','');
			let random = Math.random().toString().substr(3,10) + Date.now();
			let file_name = Number(random).toString(36);
			let start = e.file.name.lastIndexOf('.');
			let suffix = e.file.name.substring(start, e.file.name.length);
			let path = '/'+file_type+'/'+curr_date+'/'+file_name+suffix;
			//console.log(path);
			client.put(path, e.file).then(function(res){
				e.success(res);
			}).catch(function(err){
			    //console.log(err);
				e.fail(err);
			})
		}else{
			e.fail('获取oss配置失败');
		}
	})
}
/**
 * 跳转
 * @param {*} e={url,data,hreftype}
 * @param {*} dotype 跳转传参类型
 */
export const openPage = function(e,dotype='get') {
	if (!e.hasOwnProperty('url')) {
		console.log('缺少跳转url')
		return
	}

	var urlobj = {}
	if(!e.hasOwnProperty('data')) {
		e.data={}//类型
	}
	if(dotype=='post'){//url不显示参数,类似post
		urlobj={
			name: e.url.replace(/\//g,''), //约定name为url去除'/'所拼接的
			params: e.data  //跳转后获取从params获取参数数据
		}
	}else{ //url显示参数,类似get
		urlobj={
			path: e.url,
			query: e.data //跳转后获取从query获取参数数据
		}
	}
	if(e.url&&typeof e.url === 'number' && !isNaN(e.url)){
		e.hreftype = 'navigateBack';
	}

	switch (e.hreftype) {
		case 'redirectTo':
		    return router.replace(urlobj)
			break;
		case 'navigateBack':
			if (parseInt(e.url) >= 0) {
				console.log('请设置为负数,即返回的层数');
				return
			}
			return router.go(e.url)
			break;
    default: //navigateTo
			return router.push(urlobj)
	}
}

//去除字符串两端空格
export const trim=function(str){
	return str.replace(/(^\s*)|(\s*$)/g, "");
}


//删除操作和提示
export const doDel=function(e){
	//e={url，data,successFun}
	if(!e.url){
		console.log('缺少指定url')
		return
	}
	MessageBox.confirm('确认删除?', '提示', {
		confirmButtonText: '确定',
		cancelButtonText: '取消',
		type: 'warning'
	}).then(() => {
		requests('post',{url:e.url,data:e.data}).then(function(res){
			e.successFun(res)
			message.success('删除成功')
		})
	}).catch(() =>{});

}

//vue渲染经过转义的html字符串方法 html解码
export const htmlDecode=function(str) {
	let e = document.createElement('div');
	e.innerHTML = str;
	return e.childNodes.length === 0 ? '' : e.childNodes[0].nodeValue;
}

//图片直接下载
export const downloadImg =function (url,name) {
	let image = new Image()
	image.setAttribute('crossOrigin', 'anonymous')
	image.src = url
	image.onload = () => {
		let canvas = document.createElement('canvas')
		canvas.width = image.width
		canvas.height = image.height
		let ctx = canvas.getContext('2d')
		ctx.drawImage(image, 0, 0, image.width, image.height)
			canvas.toBlob((blob) => {
			let url = URL.createObjectURL(blob)
			download(url,name)
			// 用完释放URL对象
			URL.revokeObjectURL(url)
		})
	}

	function download(href, name) {
		let eleLink = document.createElement('a')
		eleLink.download = name
		eleLink.href = href
		eleLink.click()
		eleLink.remove()
	}
}

var DeepCopyFun = function (object) {
  let resultObject = {};
  for (let obj in object) {
      if (typeof (object[obj]) == "object" && !Array.isArray(object[obj])) {
          let x = {}
          x[obj] = DeepCopy(object[obj])
          Object.assign(resultObject, x);
      } else {
          let x = {};
          x[obj] = object[obj];
          Object.assign(resultObject, x);
      }
  }
  return resultObject;
}

export const Message=exoprtMessage; //ajax返回消息提醒操作
export const myStorage=exoprtStorage; //本地存储与本地会话存储操作
export const ArrayFun=exoprtArrayFun; //数组操作
export const DataFun=exoprtDataFun; //时间操作
export const form=exoprtForm; //表单提交
export const ExportFile=exoprtExportFile //数据导出
export const Tree=exoprtTree
export const clip=handleClipboard//复制粘贴
export const DeepCopy= DeepCopyFun




