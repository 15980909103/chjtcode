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
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>小程序用户
            </label>
            <div class="layui-input-inline">
                <select id="user_id" lay-verify="required" lay-search>
                    <option value=""></option>
                </select>
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>楼盘所属经纪人
            </label>
            <div class="layui-input-inline">
                <select id="agent_user_id" lay-verify="required" lay-search>
                    <option value=""></option>
                </select>
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>楼盘名称
            </label>
            <div class="layui-input-inline">
                <select id="building_building_id" lay-verify="required" lay-search>
                    <option value=""></option>
                </select>
            </div>
            <div class="layui-input-inline" style="width:80px">
                <button class="layui-btn" onclick="getPageData();"><i class="layui-icon">&#xe615;</i></button>
            </div>
        </div>
    </form>
    <table class="layui-table layui-form">
        <thead>
        <tr>
            <th>ID</th>
            <th>小程序用户</th>
            <th>小程序用户头像</th>
            <th>楼盘所属经纪人</th>
            <th>经纪人头像</th>
            <th>楼盘名称</th>
            <th>楼盘封面</th>
            <th>是否开盘通知</th>
            <th>是否降价通知</th>
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
        getSelectData();
        //开盘通知监听修改
        form.on('switch(kaipan_notice)', function(data){
            var id=data.value;
            var kaipan_notice=data.elem.checked==true?1:0;
            ajax("/xiamenyyhoutai/xcxbuilding/building_kaipan_notice",{id:id,kaipan_notice:kaipan_notice},function(res){
                if(!res.success){
                    layer.msg('状态修改失败', {icon: 5});
                    $(data.elem).prop('checked',!$(data.elem).prop('checked'));
                    form.render('checkbox');
                }
            });
        });
        //降价通知监听修改
        form.on('switch(jianjia_notice)', function(data){
            var id=data.value;
            var jianjia_notice=data.elem.checked==true?1:0;
            ajax("/xiamenyyhoutai/xcxbuilding/building_jianjia_notice",{id:id,jianjia_notice:jianjia_notice},function(res){
                if(!res.success){
                    layer.msg('状态修改失败', {icon: 5});
                    $(data.elem).prop('checked',!$(data.elem).prop('checked'));
                    form.render('checkbox');
                }
            });
        });
    });
    function getPageData() {
        var curr                 = arguments[0] ? arguments[0] : 1;
        var limit                = arguments[1] ? arguments[1] : PAGELIMIT;
        var user_id              = $("#user_id").val();
        var agent_user_id        = $("#agent_user_id").val();
        var building_building_id = $("#building_building_id").val();
        ajax("/xiamenyyhoutai/xcxbuilding/building_circularize_page",{user_id:user_id,agent_user_id:agent_user_id,building_building_id:building_building_id,curr:curr,limit:limit},function (data) {
            if(data.success){
                var domain = 'http://'+window.location.host;
                $("#tbody").empty();
                for(var i in data['data']){
                    var kaipan_notice = '';
                    var jianjia_notice = '';
                    if(data['data'][i]['kaipan_notice']==1){
                        kaipan_notice='<input type="checkbox" name="status" lay-text="开启|禁用" lay-filter="kaipan_notice" lay-skin="switch" value="'+data['data'][i]['id']+'" checked>';
                    }else{
                        kaipan_notice='<input type="checkbox" name="status" lay-text="开启|禁用" lay-filter="kaipan_notice" lay-skin="switch" value="'+data['data'][i]['id']+'">';
                    }

                    if(data['data'][i]['jianjia_notice']==1){
                        jianjia_notice='<input type="checkbox" name="status" lay-text="开启|禁用" lay-filter="jianjia_notice" lay-skin="switch" value="'+data['data'][i]['id']+'" checked>';
                    }else{
                        jianjia_notice='<input type="checkbox" name="status" lay-text="开启|禁用" lay-filter="jianjia_notice" lay-skin="switch" value="'+data['data'][i]['id']+'">';
                    }
                    var $content = '<tr>' +
                        '<td>'+parseInt((curr-1)*PAGELIMIT+parseInt(i)+1)+'</td>' +
                        '<td>'+data['data'][i]['xunickname']+'</td>' +
                        '<td class="my-img"><img class="my-img" src="'+data['data'][i]['avatarUrl']+'" alt=""/></td>'+
                        '<td>'+data['data'][i]['aunickname']+'</td>' +
                        '<td><img class="my-detail-img" src="'+data['data'][i]['headimgurl']+'" alt="" /></td>'+
                        '<td>'+data['data'][i]['bbname']+'</td>' +
                        '<td><img class="my-detail-img" src="'+data['data'][i]['pic']+'" alt="" /></td>'+
                        '<td>'+kaipan_notice+'</td>' +
                        '<td>'+jianjia_notice+'</td>' +
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

    /**
     * 搜索框数据渲染
     */
    function getSelectData() {
        var user_id              = $("#user_id");
        var agent_user_id        = $("#agent_user_id");
        var building_building_id = $("#building_building_id");
        ajax("/xiamenyyhoutai/xcxbuilding/building_circularize_search",{},function (data) {
            if (data.success){
                    for (var i in data['userName']){
                       var $content = '<option value="'+data['userName'][i]['id']+'">'+data['userName'][i]['xunickname']+'</option>';
                        user_id.append($content);
                    }
                    for (var j in data['agentName']){
                        var $content = '<option value="'+data['agentName'][j]['id']+'">'+data['agentName'][j]['aunickname']+'</option>';
                        agent_user_id.append($content);
                    }
                    for (var k in data['buildingName']){
                        var $content = '<option value="'+data['buildingName'][k]['id']+'">'+data['buildingName'][k]['bbname']+'</option>';
                        building_building_id.append($content);
                    }
                form.render();
            }else{
                console.log(data.message);
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
</script>