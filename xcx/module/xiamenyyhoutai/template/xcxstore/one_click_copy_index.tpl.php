<!DOCTYPE html>
<html>
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
    <style>.my-img{width: 50px;height: 50px;}.my-detail-img{width: 100px;height:50px;}.my-switch{min-width:75px;}</style>
</head>
<body class="layui-anim layui-anim-up">
<div class="x-nav">
    <span class="layui-breadcrumb" lay-separator=">"></span>
    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
</div>
<div id="my_body" class="x-body">
    <table class="layui-table layui-form">
        <thead>
        <tr>
            <th>ID</th>
            <th>楼盘名称</th>
            <th class="my-switch">状态</th>
        </tr>
        </thead>
        <tbody id="tbody"></tbody>
    </table>
    <div id="page"></div>
</div>
<script>
    setNavList();
    layui.use(['form','laypage','element'], function(){
        form = layui.form;
        laypage = layui.laypage;
        getPageData();
        form.on('switch(status)', function(data){
            var id=data.value;
            var status=data.elem.checked==true?1:0;
            ajax("/xiamenyyhoutai/xcxstore/copy_switch",{id:id,status:status},function(res){
                if(!res.success){
                    layer.msg('状态修改失败', {icon: 5});
                    $(data.elem).prop('checked',!$(data.elem).prop('checked'));
                    form.render('checkbox');
                }
            });
        });

    });
    function getPageData() {
        var curr = arguments[0] ? arguments[0] : 1;
        var limit = arguments[1] ? arguments[1] : PAGELIMIT;
        var name=$('#name').val();
        var status=$('#status').val();
        ajax("/xiamenyyhoutai/xcxstore/get_one_click_copy_list",{},function (data) {
            if(data.success){
                $("#tbody").empty();
                for(var i in data['data']){
                    var id = data['data'][i]['id'];
                    var status='',is_hot="",is_open_project="";
                    if(data['data'][i]['status']==1){
                        status='<input type="checkbox" name="status" lay-text="开启|禁用" lay-filter="status" lay-skin="switch" value="'+id+'" checked>';
                    }else{
                        status='<input type="checkbox" name="status" lay-text="开启|禁用" lay-filter="status" lay-skin="switch" value="'+id+'">';
                    }

                    var $content = '<tr>' +
                        '<td>'+parseInt((curr-1)*PAGELIMIT+parseInt(i)+1)+'</td>' +
                        '<td>'+data['data'][i]['name']+'</td>' +
                        '<td>'+status+'</td>' +
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
    // function pages(allcount,curr) {
    //     laypage.render({
    //         elem: 'page'
    //         ,limit:PAGELIMIT
    //         ,count: allcount
    //         ,curr:curr
    //         ,layout:['prev', 'page', 'next','limit','skip','count']
    //         ,jump: function (obj, first) {
    //             if (!first) {
    //                 PAGELIMIT=obj.limit;
    //                 getPageData(obj.curr,PAGELIMIT);
    //             }
    //         }
    //     });
    // }
    //删除
    function click_del(id){
        layer.confirm('确认要删除吗？',function(){
            ajax("/xiamenyyhoutai/xcxbuilding/building_del",{id:id},function(res){
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
</body>
</html>