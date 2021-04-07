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
                <span class="x-red">*</span>公告标题
            </label>
            <div class="layui-input-block">
                <input type="text" name="inform_title" required  lay-verify="required" placeholder="请输入标题" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>公告内容
            </label>
            <div class="layui-input-block">
                <textarea name="inform_content" lay-verify="required" placeholder="请输入内容" class="layui-textarea"></textarea>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>发送群体
            </label>
            <div class="layui-input-block">
                <input type="radio" name="group" value="1" lay-filter="group" title="个人">
                <input type="radio" name="group" value="2" lay-filter="group" title="所有" checked>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>发送方向
            </label>
            <div class="layui-input-block">
                <input type="radio" name="transmitter" lay-filter="transmitter" value="1" title="公众号">
                <input type="radio" name="transmitter" lay-filter="transmitter" value="2" title="小程序" checked>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>优先级
            </label>
            <div class="layui-input-block">
                <input type="radio" name="priority" value="2" title="紧急">
                <input type="radio" name="priority" value="1" title="高">
                <input type="radio" name="priority" value="0" title="普通" checked>
            </div>
        </div>

        <div class="layui-form-item" id="user_hide" style="display: none;">
            <label class="layui-form-label">
                <span class="x-red">*</span>小程序用户
            </label>
            <div class="layui-input-block">
                <select name="user_id" lay-verify="required" lay-search>
                    <?php foreach ($user_arr as $val){ ?>
                        <option value="<?=$val['id'] ?>"><?=$val['nickName']?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="layui-form-item" id="agent_hide" style="display: none;">
            <label class="layui-form-label">
                <span class="x-red">*</span>公众号用户
            </label>
            <div class="layui-input-block">
                <select name="agent_id" lay-verify="required" lay-search>
                    <?php foreach ($agent_user_arr as $val){ ?>
                        <option value="<?=$val['id'] ?>"><?=$val['nickname']?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="layui-form-item" style="text-align:center;">
            <button id="btn" type="button" class="layui-btn" lay-filter="btn" lay-submit>提交</button>
        </div>
    </form>
</div>
</body>
</html>
<script>

    layui.use(['form'], function(){
        var form = layui.form;
        form.on('radio(group)', function(data){
            if(data.value == 2){
                $('#agent_hide').hide();
                $('#user_hide').hide();
            }
        });
        form.on('radio(transmitter)', function(data){
            var vals=$('input:radio[name="group"]:checked').val();
            if(vals == 1){
                if(data.value==1){
                    $('#user_hide').hide();
                    $('#agent_hide').show();

                }else{
                    $('#agent_hide').hide();
                    $('#user_hide').show();
                }
            }else{
                console.log("no")
            }

        });
        form.on('submit(btn)', function(data){
            ajax("/xiamenyyhoutai/xcxambitus/announcement_inform_doAdd",data.field,function(result){
                if(result.success){
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