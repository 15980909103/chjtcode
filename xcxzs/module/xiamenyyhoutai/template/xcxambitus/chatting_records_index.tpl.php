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
    <link rel="stylesheet" href="/public/js/pictureViewer/css/pictureViewer.css?t=<?php echo JsVer;?>">
    <script src="/public/js/jquery-2.0.0.min.js" charset="utf-8"></script>
    <script src="/public/js/layui2/layui.js" charset="utf-8"></script>
    <script src="/public/js/admin/x-layui.js" charset="utf-8"></script>
    <script src="/public/js/admin/public.js" charset="utf-8"></script>
    <script src="/public/js/pictureViewer/js/jquery.mousewheel.min.js" charset="utf-8"></script>
    <script src="/public/js/pictureViewer/js/pictureViewer.js" charset="utf-8"></script>
    <style>.my-img{width: 50px;height: 50px;}.my-detail-img{width: 100px;height:50px;}</style>
</head>
<body class="layui-anim layui-anim-up">
<div class="x-nav">
    <span class="layui-breadcrumb" lay-separator=">"></span>
    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
</div>
<div id="my_body" class="x-body">
    <form id="myForm" class="layui-form x-center" style="width:100%" action="javascript:void(0);">
        <div class="layui-form-pane" style="margin-top: 15px;">
            <div class="layui-form-item">
                <label class="layui-form-label">发送方向</label>
                <div class="layui-input-inline">
                    <select id="from_type" lay-verify="required">
                        <option value=""></option>
                        <option value="1">小程序向经纪人发消息</option>
                        <option value="2">经纪人向小程序发消息</option>
                    </select>
                </div>
                <label class="layui-form-label">读取状态</label>
                <div class="layui-input-inline">
                    <select id="agent_read" lay-verify="required">
                        <option value=""></option>
                        <option value="0">未读</option>
                        <option value="1">已读</option>
                    </select>
                </div>
                <div class="layui-input-inline" style="width:80px">
                    <button class="layui-btn" onclick="getPageData();"><i class="layui-icon">&#xe615;</i></button>
                </div>
            </div>
        </div>
    </form>
    <table class="layui-table layui-form">
        <thead>
        <tr>
            <th>ID</th>
            <th>发送人</th>
            <th>发送人头像</th>
            <th style="width: 15%;">文字内容</th>
            <th>聊天图</th>
            <th>接收人</th>
            <th>接收人头像</th>
            <th>发送方向</th>
            <th>接收人读取状态</th>
            <th>创建时间</th>
        </tr>
        </thead>
        <tbody id="tbody"></tbody>
    </table>
    <div id="page"></div>
</div>
<script>
    setNavList();
    layui.use(['form','laypage','element'], function(){
        form    = layui.form;
        laypage = layui.laypage;
        getPageData();
        $('.image-list').on('click', '.cover', function () {
            var this_     = $(this);
            var images    = this_.parents('.image-list').find('.cover');
            var imagesArr = new Array();
            $.each(images, function (i, image) {
                imagesArr.push($(image).children('img').attr('src'));
            });
            $.pictureViewer({
                images: imagesArr, //需要查看的图片，数据类型为数组
                initImageIndex: this_.index() + 1, //初始查看第几张图片，默认1
                scrollSwitch: true //是否使用鼠标滚轮切换图片，默认false
            });
        });
    });
    function getPageData() {
        var curr  = arguments[0] ? arguments[0] : 1;
        var limit = arguments[1] ? arguments[1] : PAGELIMIT;
        var from_type  = $('#from_type').val();
        var agent_read = $('#agent_read').val();
        ajax("/xiamenyyhoutai/xcxambitus/chatting_records_page",{agent_read:agent_read,from_type:from_type,curr:curr,limit:limit},function (data) {
            if(data.success){
                console.log(data);
                $("#tbody").empty();
                for(var i in data['data']){
                    var id = data['data'][i]['id'];
                    var imgs="";
                    var image=data['data'][i]['image'];
                    for(var j in image){
                        imgs+='<div class="cover"><img class="my-img" src="'+image[j]+'"/></div>';
                    }
                    imgs="<div class='image-list'>"+imgs+"</div>";
                    var $content = '<tr>' +
                        '<td>'+parseInt((curr-1)*PAGELIMIT+parseInt(i)+1)+'</td>' +
                        '<td>'+data['data'][i]['transmit_name']+'</td>' +
                        '<td class="my-img"><img class="my-img" src="'+data['data'][i]['transmit_headimg']+'" alt="暂无图片" /></td>'+
                        '<td>'+data['data'][i]['content']+'</td>' +
                        '<td>'+imgs+'</td>' +
                        '<td>'+data['data'][i]['receive_name']+'</td>' +
                        '<td class="my-img"><img class="my-img" src="'+data['data'][i]['receive_headimg']+'" alt="暂无图片" /></td>'+
                        '<td>'+data['data'][i]['from_type']+'</td>' +
                        '<td>'+data['data'][i]['read_type']+'</td>' +
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
        },function (res) {
            console.log(res);
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
</body>
</html>