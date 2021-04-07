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
    <style>
        .layui-input{line-height: 38px;}.myimg{margin-left: 10px;}
        .my-div{height: 36px;line-height: 36px;padding-left: 10px;border:1px solid #eee;border-radius: 3px}
    </style>
</head>
<body>
<div class="x-body">
    <form class="layui-form layui-form-pane">
        <?php if (empty($record['cycle_type'])){ ?>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span class="x-red">*</span>当前状态
                </label>
                <div class="layui-input-inline">
                    <div class="my-div"><?=$record['status_type']?></div>
                </div>
            </div>
        <?php }else{ ?>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>勿扰周期
            </label>
            <div class="layui-input-inline">
                <div class="my-div"><?=$record['cycle_type']?></div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>开始时间段
            </label>
            <div class="layui-input-inline">
                <div class="my-div"><?=$record['start_time']?></div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>结束时间段
            </label>
            <div class="layui-input-inline">
                <div class="my-div"><?=$record['end_time']?></div>
            </div>
        </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span class="x-red">*</span>当前状态
                </label>
                <div class="layui-input-inline">
                    <div class="my-div"><?=$record['status_type']?></div>
                </div>
            </div>
        <?php } ?>
    </form>
</div>
</body>
</html>
<script>
    layui.use(['form'], function(){
        form = layui.form;
    });
</script>
