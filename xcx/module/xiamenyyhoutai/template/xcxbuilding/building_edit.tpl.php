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
    <script src="/public/js/admin/area.js" charset="utf-8"></script>
    <script src="/public/js/admin/formSelects.js" charset="utf-8"></script>
    <style>
        .layui-input{line-height: 38px;}.myimg{margin-left: 10px;}#container {width:400px;height:300px;margin: 20px 0;}
        .x-body ul.layui-tab-title li:nth-child(1) i,.x-body ul.layui-tab-title li:nth-child(2) i,.x-body ul.layui-tab-title li:nth-child(3) i,.x-body ul.layui-tab-title li:nth-child(4) i{display: none;}
    </style>
</head>
<body>
<div class="x-body">
    <div class="layui-tab layui-tab-card" lay-filter="mynav" lay-allowClose="true">
        <ul class="layui-tab-title">
            <li class="layui-this">基本信息</li>
            <li>轮播图</li>
            <li>周边信息</li>
            <li>楼栋信息</li>
            <li>报备规则</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <iframe src="/xiamenyyhoutai/xcxbuilding/basic_index?id=<?=$id?>" frameborder="0" style="width:100%;"></iframe>
            </div>
            <div class="layui-tab-item">
                <iframe src="/xiamenyyhoutai/xcxbuilding/shuffle_index?id=<?=$id?>" frameborder="0" style="width:100%;"></iframe>
            </div>
            <div class="layui-tab-item">
                <iframe src="/xiamenyyhoutai/xcxbuilding/map_index?id=<?=$id?>" frameborder="0" style="width:100%;"></iframe>
            </div>
            <div class="layui-tab-item">
                <iframe src="/xiamenyyhoutai/xcxbuilding/floor_index?id=<?=$id?>" frameborder="0" style="width:100%;"></iframe>
            </div>
            <div class="layui-tab-item">
                <iframe src="/xiamenyyhoutai/xcxbuilding/report_index?id=<?=$id?>" frameborder="0" style="width:100%;"></iframe>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<script>
    layui.use(['form','element'], function() {
        form = layui.form;
        element = layui.element;
        var nowIndex = parent.layer.getFrameIndex(window.name);
        parent.layer.full(nowIndex);
        initIframeHeight();
    });
    //获取页面可用高度
    function initIframeHeight(){
        var myHeight=$(window).height()-85;
        $('iframe').css('height',myHeight+'px');
    }
    function addCardItem(title,url,id){
        var timestamp = ((new Date()).getTime()).toString()+id;
        element.tabAdd('mynav', {
            title: title
            ,content: '<iframe src="'+url+'" frameborder="0" style="width:100%;"></iframe>'
            ,id: timestamp
        });
        initIframeHeight();
        element.tabChange('mynav',timestamp);
    }
</script>