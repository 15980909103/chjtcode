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
</head>
<body>
<div class="x-body">
    <form class="layui-form layui-form-pane">
        <input type="hidden" name="id" value="<?=$data['id']?>">
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>昵称
            </label>
            <div class="layui-input-inline">
                <input type="text" name="nickname" required="" lay-verify="required" autocomplete="off" maxlength="250" class="layui-input" value="<?=$data['nickName']?>">
            </div>
            <label class="layui-form-label">
                <span class="x-red"></span>性别
            </label>
            <div class="layui-input-inline">
                <select name="gender">
                    <option value="0" <?=$data['gender']=='0'?'selected':''?>>未知</option>
                    <option value="1" <?=$data['gender']=='1'?'selected':''?>>男</option>
                    <option value="2" <?=$data['gender']=='2'?'selected':''?>>女</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>国家
            </label>
            <div class="layui-input-inline">
                <input type="text" name="country" autocomplete="off" maxlength="250" class="layui-input" value="<?=$data['country']?>">
            </div>
            <label class="layui-form-label">
                <span class="x-red"></span>省份
            </label>
            <div class="layui-input-inline">
                <input type="text" name="province" autocomplete="off" maxlength="250" class="layui-input" value="<?=$data['province']?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>城市
            </label>
            <div class="layui-input-inline">
                <input type="text" name="city" autocomplete="off" maxlength="250" class="layui-input" value="<?=$data['city']?>">
            </div>
            <label class="layui-form-label">
                <span class="x-red"></span>电话
            </label>
            <div class="layui-input-inline">
                <input type="text" name="phone" autocomplete="off" maxlength="250" class="layui-input" value="<?=$data['phone']?>">
            </div>
        </div>

        <div class="layui-form-item" style="text-align:center;">
            <button id="btn" type="button" class="layui-btn" lay-filter="btn" lay-submit>保存</button>
        </div>
    </form>
</div>
</body>
</html>
<script>
    layui.use(['form'], function(){
        var form = layui.form;
        form.on('submit(btn)', function(data){
            ajax("/xiamenyyhoutai/user/user_doedit",data.field,function(res){
                if(res.success){
                    parent.layer.alert("保存成功", {icon: 6},function (index2) {
                        parent.getPageData(parent.$('#my_body').data('curr'));
                        parent.layer.close(index2);
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                    });
                }else{
                    parent.layer.msg(res.message,{icon: 5});
                }
            });
        });
    })
</script>