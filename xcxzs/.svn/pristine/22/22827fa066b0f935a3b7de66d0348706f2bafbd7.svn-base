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
                <span class="x-red">*</span>姓名/昵称
            </label>
            <div class="layui-input-block">
                <select name="agent_id" lay-verify="required" lay-search>
                    <?php foreach($agentUserArr as $val){ ?>
                        <option value="<?=$val['id']?>"><?=$val['username']?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>楼盘名称
            </label>
            <div class="layui-input-block">
                <select name="building_id" lay-verify="required" lay-search>
                    <?php foreach($buildingArr as $val){ ?>
                        <option value="<?=$val['id']?>"><?=$val['name']?></option>
                    <?php } ?>
                </select>
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
            ajax("/xiamenyyhoutai/Xcxbuilding/agent_building_doadd",data.field,function(result){
                if(result.success){
                    parent.layer.alert("保存成功", {icon: 6},function (index2) {
                        parent.getPageData(parent.$('#my_body').data('curr'));
                        parent.layer.close(index2);
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                    });
                }else{
                    parent.layer.msg(result.message,{icon: 5});
                }
            });
        });
    })
</script>