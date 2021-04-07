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
    <script src="/public/js/UEditor/ueditor.config.js" charset="utf-8"></script>
    <script src="/public/js/UEditor/ueditor.all.js" charset="utf-8"></script>
    <style>.layui-input{line-height: 38px;}.myimg{margin-left: 10px;}.layui-anim.layui-anim-upbit{z-index: 9999;}</style>
</head>
<body>
<div class="x-body">
    <form class="layui-form layui-form-pane">
        <input type="hidden" name="id" value="<?=$data['id'];?>">
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
                <img id="mylogoimg" class="myimg" src="<?=$data['pic']?>" alt="暂无图片" style="max-width: 100%;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>户型
            </label>
            <div class="layui-input-inline">
                <input type="text" name="title" lay-verify="required" placeholder="如“ 3室2厅2卫 ”" autocomplete="off" class="layui-input" value="<?=$data['title']?>">
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>销售状态
            </label>
            <div class="layui-input-inline">
                <select name="sales_status" lay-verify="required">
                    <?php foreach($sales_status as $val){?>
                        <option value="<?=$val?>" <?=$val==$data['sales_status']?'selected':''?>><?=$val?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>房屋类型
            </label>
            <div class="layui-input-inline">
                <select name="house_type" lay-verify="required">
                    <?php foreach($house_type as $val){?>
                        <option value="<?=$val?>" <?=$val==$data['house_type']?'selected':''?>><?=$val?></option>
                    <?php } ?>
                </select>
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>户型结构
            </label>
            <div class="layui-input-inline">
                <select name="family_structure" lay-verify="required">
                    <?php foreach($family_structure as $val){?>
                        <option value="<?=$val?>" <?=$val==$data['family_structure']?'selected':''?>><?=$val?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>朝向
            </label>
            <div class="layui-input-inline">
                <select name="orientation" lay-verify="required">
                    <?php foreach($orientation as $val){?>
                        <option value="<?=$val?>" <?=$val==$data['orientation']?'selected':''?>><?=$val?></option>
                    <?php } ?>
                </select>
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>建筑面积
            </label>
            <div class="layui-input-inline">
                <input type="number" name="construction_area" lay-verify="required" min="0" value="<?=$data['construction_area']?>" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>价格
            </label>
            <div class="layui-input-inline">
                <input type="text" name="price_total" lay-verify="required" placeholder="如“ 500万/套 ”" autocomplete="off" class="layui-input" value="<?=$data['price_total']?>">
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>排序
            </label>
            <div class="layui-input-inline">
                <input type="number" name="sort" lay-verify="required" min="0" value="0" autocomplete="off" class="layui-input" value="<?=$data['sort']?>">
            </div>
        </div>
        <hr>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">
                <span class="x-red"></span>空间信息
            </label>
            <div class="layui-input-block">
                <script id="spatial_information" name="spatial_information" type="text/plain"></script>
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
    var ue = UE.getEditor('spatial_information',{
        initialFrameHeight:190,
        toolbars: [[
            'fullscreen', 'source', '|', 'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
            'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload','insertcode', 'webapp', 'pagebreak', 'template', 'background', '|',
            'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
            'print', 'preview', 'searchreplace', 'drafts', 'help'
        ]]
    });
    ue.ready(function() {
        ue.setContent('<?=$data["spatial_information"]?>');
    });
    layui.use(['form','upload'], function(){
        var form = layui.form;
        var upload = layui.upload;
        var logoUpload=upload.render({
            elem: '#logoUpload' //绑定元素
            ,url: "/xiamenyyhoutai/xcxbuilding/door_doedit" //上传接口
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
            myField=JSON.stringify(data.field);
            if(fileIndex==""){
                var parameter=$.extend({},data.field,{type:'empty'});
                ajax("/xiamenyyhoutai/xcxbuilding/door_doedit",{parame:JSON.stringify(parameter)},function(res){
                    if(res.success){
                        parent.layer.alert("保存成功", {icon: 6},function (index2) {
                            parent.getPageData(parent.$('#my_body').data('curr'));
                            parent.layer.close(index2);
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                        });
                    }else{
                        layer.msg(res.message,{icon: 5});
                    }
                });
            }else{
                logoUpload.upload();
            }

        });
    });

</script>