import { Message,MessageBox } from 'element-ui';

/* 消息返回 */
const message={
	//function ：success，error，warning，info
	funcall:function (type,options){
	let o={}
	if(typeof(e)=='object'){
		o=options
		o.duration=1200
	}else{		
			o={
				message:options,
				duration:1200
			}
	}
		Message[type](o)
	}
}
message.success=function(e){
	message.funcall('success',e)
}
message.error=function(e){
	message.funcall('error',e)
}
message.warning=function(e){
	message.funcall('warning',e)
}
message.info=function(e){
	message.funcall('info',e)
}
/* 消息返回 end*/

export default message