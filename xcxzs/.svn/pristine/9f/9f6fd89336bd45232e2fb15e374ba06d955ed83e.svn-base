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
    <style>.layui-input{line-height: 38px;}.myimg{margin-left: 10px;}</style>
</head>
<body>
<div class="x-body">
    <form class="layui-form layui-form-pane">
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>所属文章
            </label>
            <div class="layui-input-block">
                <select name="aid" lay-verify="required" lay-search>
                    <?php foreach($articleData as $val){ ?>
                        <option value="<?=$val['id']?>"><?=$val['title']?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>用户类型
            </label>
            <div class="layui-input-block">
                <input type="radio" name="user_type" value="1" lay-filter="usertype" title="小程序用户" checked>
                <input type="radio" name="user_type" value="2" lay-filter="usertype" title="经纪人">
            </div>
        </div>
        <div id="xcxuser" class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>小程序用户
            </label>
            <div class="layui-input-block">
                <select name="uid" lay-verify="required" lay-search>
                    <?php foreach($userData as $val){ ?>
                        <option value="<?=$val['id']?>"><?=$val['nickName']?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div id="jjruser" class="layui-form-item" style="display:none;">
            <label class="layui-form-label">
                <span class="x-red">*</span>经纪人
            </label>
            <div class="layui-input-block">
                <select name="uid" lay-verify="required" lay-search>
                    <?php foreach($agentData as $val){ ?>
                        <option value="<?=$val['id']?>"><?=$val['nickname']?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>内容
            </label>
            <div class="layui-input-block">
                <textarea name="content" placeholder="请输入内容" class="layui-textarea"></textarea>
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
        form.on('radio(usertype)', function(data){
            if(data.value=='1'){
                $('#xcxuser').show();
                $('#jjruser').hide();
            }else if(data.value=='2'){
                $('#xcxuser').hide();
                $('#jjruser').show();
            }
        });
        form.on('submit(btn)', function(data){
            ajax("/xiamenyyhoutai/xcxarticle/comments_doadd",data.field,function(res){
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