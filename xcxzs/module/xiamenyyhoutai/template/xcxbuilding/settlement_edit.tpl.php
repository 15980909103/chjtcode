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
        <input type="hidden" name="id" value="<?=$data['id'];?>">
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>佣金
            </label>
            <div class="layui-input-inline">
                <input type="text" name="commission_change" autocomplete="off" maxlength="250" class="layui-input" value="<?=$data['commission'];?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>备注
            </label>
            <div class="layui-input-block">
                <textarea name="inform_content" lay-verify="required" placeholder="请输入内容" class="layui-textarea"></textarea>
            </div>
        </div>
        <input type="hidden" name="examine_type" value="2"/>
        <div class="layui-form-item" style="text-align:center;">
            <button id="btn" type="button" class="layui-btn"  lay-filter="btn" lay-submit>确认</button>
            <button id="btn2" type="button" class="layui-btn"  lay-filter="btn" lay-submit>驳回</button>
        </div>
    </form>
</div>
</body>
</html>
<script>
    var fileIndex="";
    layui.use(['form'], function(){
        var form = layui.form;
        form.on('submit(btn)', function(data){
            var commission_change=$("input[name='commission_change']").val();
            if(commission_change!=""){
                if(!(/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/.test(commission_change))){
                    layer.msg("佣金有误",{icon: 5});
                    return false;
                }
            }

            $('#btn').click(function(){
                $("input[name='examine_type']").val(2);
            });
            $('#btn2').click(function(){
                $("input[name='examine_type']").val(-1);
            });

            ajax("/xiamenyyhoutai/xcxbuilding/set_settlement",data.field,function(res){
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