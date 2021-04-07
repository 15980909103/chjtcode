<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>欢迎页面-X-admin2.0</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="/public/css/admin/x-admin2.css?t=<?php echo JsVer;?>" media="all">
    <script src="/public/js/jquery-2.0.0.min.js" charset="utf-8"></script>
    <script src="/public/js/layui2/layui.js" charset="utf-8"></script>
    <script src="/public/js/admin/x-layui.js" charset="utf-8"></script>
    <script src="/public/js/admin/public.js" charset="utf-8"></script>
    <style>.layui-input{line-height: 38px;}.my-img{width: 50px;height: 50px;}.my-detail-img{width: 100px;height:50px;}}</style>
</head>
<body>
<div class="x-nav">
    <span class="layui-breadcrumb" lay-separator=">"></span>
    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
</div>
<div id="my_body" class="x-body">
    <form class="layui-form x-center" style="width:100%" action="javascript:void(0);">
        <div class="layui-form-pane" style="margin-top: 15px;">
            <div class="layui-form-item">
                <label class="layui-form-label">发送客户端</label>
                <div class="layui-input-inline">
                    <select id="username_type" lay-verify="required">
                        <option value=""></option>
                        <option value="1">小程序</option>
                        <option value="2">公众号</option>
                    </select>
                </div>
                <label class="layui-form-label">是否撤销</label>
                <div class="layui-input-inline">
                    <select id="if_revocation" lay-verify="required">
                        <option value=""></option>
                        <option value="0">否</option>
                        <option value="1">是</option>
                    </select>
                </div>
                <div class="layui-input-inline" style="width:80px">
                    <button class="layui-btn" onclick="getPageData();"><i class="layui-icon">&#xe615;</i></button>
                </div>
            </div>
        </div>
    </form>
    <xblock>
        <button class="layui-btn" onclick="click_add('添加','/xiamenyyhoutai/xcxambitus/announcement_inform_add','680','450');"><i class="layui-icon">&#xe608;</i>添加新公告</button>
    </xblock>
    <table class="layui-table layui-form">
        <thead>
        <tr>
            <th>ID</th>
            <th>公告标题</th>
            <th style="width: 15%;">公告内容</th>
            <th>优先级</th>
            <th>发布时间</th>
            <th>是否撤销</th>
            <th>撤销时间</th>
            <th>用户读取状态</th>
        </tr>
        </thead>
        <tbody id="tbody"></tbody>
    </table>
    <div id="page"></div>
</div>
</body>
</html>
<script>
    layui.use(['form','element','laypage'], function(){
        form = layui.form;
        laypage = layui.laypage;
        getPageData();
        //状态监听修改
        form.on('switch(status)', function(data){
            var id=data.value;
            var status=data.elem.checked==true?1:0;
            ajax("/xiamenyyhoutai/xcxambitus/if_revocation_status",{id:id,status:status},function(res){
                if(!res.success){
                    layer.msg('状态修改失败', {icon: 5});
                    $(data.elem).prop('checked',!$(data.elem).prop('checked'));
                    form.render('checkbox');
                }else {
                    getPageData();
                }
            });
        });

    });
    function getPageData() {
        var curr  = arguments[0] ? arguments[0] : 1;
        var limit = arguments[1] ? arguments[1] : PAGELIMIT;
        var username_type = $('#username_type').val();
        var if_revocation    = $('#if_revocation').val();
        ajax("/xiamenyyhoutai/xcxambitus/announcement_inform_page",{username_type:username_type,if_revocation:if_revocation,curr:curr,limit:limit},function (data) {
            if(data.success){
                $("#tbody").empty();
                for(var i in data['data']){
                    var id = data['data'][i]['id'];
                    var status='';
                    if(data['data'][i]['if_revocation']=="是"){
                        status='<input type="checkbox" name="status" lay-text="是|否" lay-filter="status" lay-skin="switch" value="'+id+'" checked>';
                    }else{
                        status='<input type="checkbox" name="status" lay-text="是|否" lay-filter="status" lay-skin="switch" value="'+id+'">';
                    }
                    var $content = '<tr>' +
                        '<td>'+parseInt((curr-1)*PAGELIMIT+parseInt(i)+1)+'</td>' +
                        '<td>'+data['data'][i]['inform_title']+'</td>' +
                        '<td>'+data['data'][i]['inform_content']+'</td>' +
                        '<td>'+data['data'][i]['priority']+'</td>' +
                        '<td>'+data['data'][i]['release_time']+'</td>' +
                        '<td>'+status+'</td>'+
                        '<td>'+data['data'][i]['revocation_time']+'</td>' +
                        '<td>'+data['data'][i]['unread']+"位用户未读;&nbsp;&nbsp;&nbsp;&nbsp;"
                                                +data['data'][i]['read']+"位用户已读"+'</td>' +
                        '</tr>';
                    $("#tbody").append($content);
                }
                $('#my_body').data('curr',curr);
                form.render('checkbox');
                pages(data['count'],curr);
            }else{
                if(curr != 1){
                    getPageData(parseInt(curr-1));
                }else{
                    $("#tbody").empty();
                    $("#tbody").append("<span>您还没有添加数据！！！</span>");
                }
            }
        })
    }
    function pages(allcount,curr) {
        laypage.render({
            elem: 'page'
            ,limit:PAGELIMIT
            ,count: allcount
            ,curr:curr
            ,layout:['prev', 'page', 'next','limit','skip','count']
            ,jump: function (obj, first) {
                if (!first) {
                    PAGELIMIT=obj.limit;
                    getPageData(obj.curr,PAGELIMIT);
                }
            }
        });
    }
    //删除
    function click_del(id){
        layer.confirm('确认要删除吗？',function(){
            ajax("/xiamenyyhoutai/xcxbuilding/agent_building_del",{id:id},function(res){
                if(res.success){
                    layer.msg('已删除!',{icon:6,time:300},function(){
                        getPageData($('#my_body').data('curr'));
                    });
                }else{
                    layer.msg(res.message,{icon: 5});
                }
            });
        });
    }
</script>