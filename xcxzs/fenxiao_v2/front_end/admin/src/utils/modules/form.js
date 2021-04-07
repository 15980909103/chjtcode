
import { Message } from 'element-ui';
import { requests } from '@/utils/util.js';

//表单提交
function formSubmit(ref,that,post_fields=[]) {
	if (!ref) {
	    console.log("缺少制订的ref");
	    return false;
	}
	if(!that||!that[ref]){
		console.log('传入参数有误')
	}
	that[ref].errmsg='';//清空错误数据
	let data = '';let url=''
	if(that.$refs[ref]){
	    data = that.$refs[ref].model; //获取表单数据
	    data.doref=ref
	    url=that[ref].url;
	}    
	console.log(data)
	
	if(post_fields){
		if(Array.isArray(post_fields)){
			var postdata={}
			for(let i in post_fields){
				postdata[post_fields[i]]=data[post_fields[i]]
			}
			data=postdata;
		}else{
			console.log('post_fields 需为数组')
			return
		}
	}
	
	return new Promise(function(resolve, reject) {
		that.$refs[ref].validate(valid => {
			if (valid) {
				requests("post", { url: url, data: data }).then(function(res) {			    
				    resolve(res);				    
				}).catch((err) =>{console.log(err)});
				return;
			} else {
			console.log("error submit!!");
			return false;
			}
		})
	});
}

function formOpen(e,that) {
	//e={title:title,ref:ref,index:index, row:row}
	if(!e.ref){
		console.log('缺少指定的ref')
		return
	}
	if(!e.url){
		console.log('缺少指定提交的url')
		return
	}
	if(!that||!that[e.ref]){
		console.log('传入参数有误')
	}
	that[e.ref].title=e.title;
	that[e.ref].visible=true;//显示
	that[e.ref].url=e.url;

	that[e.ref].formdata={};//清空表单数据
	if(e.formdata){//处于编辑时      
		let formdata={};
		///获取传入表单的数据，不可formdata=e.formdata,会将一些隐藏属性一起过去，表单修改时会同步修改传入e.formdata的值
		for(let i in e.formdata){
				formdata[i]=e.formdata[i]
		}
		that[e.ref].formdata=formdata
	}
		
}

//关闭表单
function formCancel(ref,that) {
	if(!that||!that[ref]){
		console.log('传入参数有误')
	}
	if(that[ref].hasOwnProperty('visible')){
		that[ref].visible=false;//隐藏
	}
	that.$refs[ref].clearValidate();
	that[ref].formdata={};//清空表单数据
	//clearValidate()验证结果重置
	//resetFields()数据重置 表单打开时如已有操作清空对应数组键值,此时这边不用使用,加会出现多次切换打开时加载不了数据,这时使用clearValidate()+打开时页面操作清空对应数组键值方案
	//this.$refs[formName].resetFields();
}
//重置
function formReset(ref,that){
	that.$refs[ref].resetFields();
}
export default{
	formSubmit,
	formOpen,
	formCancel,
	formReset
}