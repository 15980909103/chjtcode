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
                <span class="x-red">*</span>姓名/昵称
            </label>
            <div class="layui-input-inline">
                <select id="user_id" lay-verify="required" lay-search>
                    <option value=""></option>
                    <?php foreach($userArr as $val){ ?>
                        <option value="<?=$val['id']?>"><?=$val['nickName']?></option>
                    <?php } ?>
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
            <th>姓名/昵称</th>
            <th>头像</th>
            <th>收藏楼盘</th>
            <th>楼盘封面</th>
            <th>楼盘所属经纪人</th>
            <th>收藏时间</th>
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
    });
    function getPageData() {
        var curr = arguments[0] ? arguments[0] : 1;
        var limit = arguments[1] ? arguments[1] : PAGELIMIT;
        var user_id=$("#user_id").val();
        ajax("/xiamenyyhoutai/xcxambitus/collection_page",{user_id:user_id,curr:curr,limit:limit},function (data) {
            if(data.success){
                var domain = 'http://'+window.location.host;
                $("#tbody").empty();
                for(var i in data['data']){
                    var $content = '<tr>' +
                        '<td>'+parseInt((curr-1)*PAGELIMIT+parseInt(i)+1)+'</td>' +
                        '<td>'+data['data'][i]['nickName']+'</td>' +
                        '<td class="my-img"><img class="my-img" src="'+data['data'][i]['avatarUrl']+'" alt="暂无图片" /></td>'+
                        '<td>'+data['data'][i]['name']+'</td>' +
                        '<td><img class="my-detail-img" src="'+data['data'][i]['pic']+'" alt="暂无图片" /></td>'+
                        '<td>'+data['data'][i]['username']+'</td>' +
                        '<td>'+data['data'][i]['create_time']+'</td>' +
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
</script>