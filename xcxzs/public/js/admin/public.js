/**
 * Created by USER022 on 2019/1/3.
 */
var DOMAINIMG="http://chfx.999house.com/"
var PAGELIMIT=10;
function ajax(url,datas,fun){
    var err_fun = arguments[3] ? arguments[3] : false;
    var is_async = arguments[4] ? arguments[4] : false;
    $.ajax({
        url: url,
        type: 'POST', //GET
        async: is_async,    //或false,是否异步
        data: datas,
        timeout: 5000,    //超时时间
        dataType: 'json',    //返回的数据格式：json/xml/html/script/jsonp/tex
        success:function(res){
            if(res.ajax_error){
                layer.msg('您没有操作权限!',{icon: 2,time:1500});return false;
            }
            fun(res);
        },
        error:function(err){
            if(err_fun){
                err_fun(err);
            }
        }
    });
}
//设置面包屑导航
function setNavList(){
    //自动获取面包屑导航栏
    var narArr=[];
    var _html="<a><cite>首页</cite></a>";
    var narArrDoc=top.$('#side-nav li.layui-nav-itemed');
    if(narArrDoc.length>0){
        narArrDoc.each(function(index,_this){
            var aTag=$(_this).children('a').get(0);
            narArr.push($(aTag).find('cite').text());
        })
    }

    var narTitle=top.$('.layui-tab-title .layui-this').text();
    narTitle=narTitle.substring(0,narTitle.length-1);
    narArr.push(narTitle);
    for(var i in narArr){
        _html+="<a><cite>"+narArr[i]+"</cite></a>";
    }
    $('.x-nav .layui-breadcrumb').html(_html);
}
//添加
function click_add(title,url,w,h){
    var _heighe=top.$('.layui-show').height();
    if(h>parseInt(_heighe))
        h=_heighe-42;
    x_admin_show(title,url,w,h);
}
//编辑
function click_edit(title,url,id,w,h) {
    var _heighe=top.$('.layui-show').height();
    if(h>parseInt(_heighe))
        h=_heighe-42;
    x_admin_show(title,url+'?id='+id,w,h);
}

//编辑
function click_edit_said(title,url,id,said,w,h) {
    var _heighe=top.$('.layui-show').height();
    if(h>parseInt(_heighe))
        h=_heighe-42;
    x_admin_show(title,url+'?id='+id+'&said='+said,w,h);
}
