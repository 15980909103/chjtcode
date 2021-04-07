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
                <span class="x-red">*</span>楼栋
            </label>
            <div class="layui-input-inline">
                <input type="text" name="title" lay-verify="required" placeholder="如“ 1号楼 ”" autocomplete="off" class="layui-input" value="<?=$data['title']?>">
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
                <span class="x-red">*</span>楼层
            </label>
            <div class="layui-input-inline">
                <select name="floor_number" lay-verify="required">
                    <?php for($i=1;$i<=50;$i++){?>
                        <option value="<?=$i?>" <?=$i==$data['floor_number']?'selected':''?>><?=$i?>层</option>
                    <?php } ?>
                </select>
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>户数
            </label>
            <div class="layui-input-inline">
                <input type="number" name="house_number" lay-verify="required" min="0" autocomplete="off" class="layui-input" value="<?=$data['house_number']?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>开盘时间
            </label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input" id="kaipan_time" name="kaipan_time" lay-verify="required">
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>交房时间
            </label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input" id="jiaofan_time" name="jiaofan_time" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>产权年限
            </label>
            <div class="layui-input-block">
                <select name="year_number" lay-verify="required">
                    <?php foreach($property_year as $val){?>
                        <option value="<?=$val?>" <?=$val==$data['year_number']?'selected':''?>><?=$val?>年</option>
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
    var myField="";
    var fileIndex="";
    layui.use(['form','laydate','upload'], function(){
        var form = layui.form;
        var laydate = layui.laydate;
        var upload = layui.upload;
        laydate.render({elem: '#kaipan_time',value:"<?=$data['kaipan_time']?>",trigger: 'click'});
        laydate.render({elem: '#jiaofan_time',value:"<?=$data['jiaofan_time']?>",trigger: 'click'});
        var logoUpload=upload.render({
            elem: '#logoUpload' //绑定元素
            ,url: "/xiamenyyhoutai/xcxbuilding/floor_doedit" //上传接口
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
                ajax("/xiamenyyhoutai/xcxbuilding/floor_doedit",{parame:JSON.stringify(parameter)},function(res){
                    if(res.success){
                        parent.layer.alert("保存成功", {icon: 6},function (index2) {
                            parent.initFloorList();
                            parent.getPageData(parent.$('#my_body').data('curr'));
                            parent.layer.close(index2);
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                        });
                    }else{
                        parent.layer.msg(res.message,{icon: 5});
                    }
                });
            }else{
                logoUpload.upload();
            }
        });
    });

</script>