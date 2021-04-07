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
        <input type="hidden" name="building_id" value="<?=$id;?>">
        <div class="layui-form-item">
            <label class="layui-form-label" style="padding:0;border:0;width:auto;">
                <button type="button" class="layui-btn" id="logoUpload">
                    <i class="layui-icon">&#xe67c;</i>上传封面
                </button>
            </label>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>封面图片
            </label>
            <div class="layui-input-inline">
                <img id="mylogoimg" class="myimg" src="/upload/static/empty.png" alt="暂无图片" style="max-width: 100%;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>标题
            </label>
            <div class="layui-input-block">
                <input type="text" name="title" lay-verify="required" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>跳转链接
            </label>
            <div class="layui-input-block">
                <input type="text" name="url" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>排序
            </label>
            <div class="layui-input-block">
                <input type="number" name="sort" min="0" value="0" autocomplete="off" class="layui-input">
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
    var myField="";
    var fileIndex="";
    layui.use(['form','upload'], function(){
        var form = layui.form;
        var upload = layui.upload;
        var logoUpload=upload.render({
            elem: '#logoUpload' //绑定元素
            ,url: "/xiamenyyhoutai/xcxbuilding/shuffle_doadd" //上传接口
            ,size: 2048
            ,accept: "images"
            ,auto:false
            ,data:{
                parame:function(){
                    return myField;
                }
            }
            ,choose:function(obj){
                var files=obj.pushFile();  //删除文件队列确保只上传一次
                obj.preview(function(index, file, result){
                    delete files[fileIndex];
                    fileIndex=index;
                    $('#mylogoimg').attr('src',result);
                });
            }
            ,done: function(res){
                //上传完毕回调
                if(res.success){
                    parent.layer.alert("保存成功", {icon: 6},function (index2) {
                        parent.getPageData(parent.$('#my_body').data('curr'));
                        parent.layer.close(index2);
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                    });
                }else{
                    layer.msg("上传失败："+res.message, {icon: 5});
                }
            }
        });
        form.on('submit(btn)', function(data){
            if(fileIndex==""){
                layer.msg("请上传封面");
                return false;
            }
            myField=JSON.stringify(data.field);
            logoUpload.upload();
        });
    });

</script>