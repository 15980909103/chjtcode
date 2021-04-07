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
    <script src="/public/js/admin/area.js" charset="utf-8"></script>
    <style>.layui-input{line-height: 38px;}.myimg{margin-left: 10px;}</style>
</head>
<body>
<div class="x-body">
    <form class="layui-form layui-form-pane">
        <input type="hidden" name="id" value="<?=$data['id'];?>">
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>标题
            </label>
            <div class="layui-input-block">
                <input type="text" name="title" lay-verify="required" autocomplete="off" maxlength="250" class="layui-input" value="<?=$data['title'];?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>栏目
            </label>
            <div class="layui-input-inline">
                <select name="cid" lay-verify="required">
                    <option value=""></option>
                    <?php foreach($columnData as $val){ ?>
                        <option value="<?=$val['id']?>" <?=$val['id']==$data['cid']?'selected':''?>><?=$val['title']?></option>
                    <?php } ?>
                </select>
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>阅读数
            </label>
            <div class="layui-input-inline">
                <input type="number" name="read_num" lay-verify="required" autocomplete="off" min="0" class="layui-input" value="<?=$data['read_num'];?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>省份
            </label>
            <div class="layui-input-inline layui-form" lay-filter="province2">
                <select id="province" name="province" lay-filter="province" lay-verify="required">
                    <option value="">请选择省</option>
                </select>
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>城市
            </label>
            <div class="layui-input-inline layui-form" lay-filter="city2">
                <select id="city" name="city" lay-filter="city" lay-verify="required">
                    <option value="">请选择市</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>地区
            </label>
            <div class="layui-input-inline layui-form" lay-filter="area2">
                <select id="area" name="area" lay-filter="area" lay-verify="required">
                    <option value="">请选择县/区</option>
                </select>
            </div>
        </div>
        <hr/>
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
                <img id="mylogoimg" class="myimg" src="<?=$data['cover']?>" alt="暂无图片" style="max-width: 100%;">
            </div>
        </div>
        <hr>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>描述简介
            </label>
            <div class="layui-input-block">
                <textarea name="description" id="description" placeholder="请输入内容" class="layui-textarea"><?=$data['description']?></textarea>
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">
                <span class="x-red"></span>内容
            </label>
            <div class="layui-input-block">
                <script id="container" name="content" type="text/plain"></script>
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
    var ue = UE.getEditor('container',{
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
        ue.setContent('<?=$data["content"]?>');
    });
    var areaData = Area;
    var _province="<?=$data['province']?>";
    var _city="<?=$data['city']?>";
    var _area="";
    var fileIndex="";
    layui.use(['form','upload'], function(){
        form = layui.form;
        var upload = layui.upload;
        //初始地区选择
        loadProvince();
        var logoUpload=upload.render({
            elem: '#logoUpload' //绑定元素
            ,url: "/xiamenyyhoutai/xcxarticle/article_doedit" //上传接口
            ,size: 2048
            ,accept: "images"
            ,auto:false
            ,data:{
                id:"<?=$data['id']?>",
                title: function(){
                    return  document.getElementsByName("title")[0].value;
                },
                cid: function(){
                    return  document.getElementsByName("cid")[0].value;
                },
                read_num: function(){
                    return  document.getElementsByName("read_num")[0].value;
                },
                description:function(){
                    return  document.getElementsByName("description")[0].value;
                },
                province:function(){
                    return $('#province').find("option:selected").text();
                },
                city:function(){
                    return $('#city').find("option:selected").text();
                },
                content: function(){
                    return  document.getElementsByName("content")[0].value;
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
                var parameter=$.extend({},data.field,{type:'empty',province:$('#province').find("option:selected").text(),city:$('#city').find("option:selected").text()});
                ajax("/xiamenyyhoutai/xcxarticle/article_doedit",parameter,function(res){
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
            }else{
                logoUpload.upload();
            }
        });
    })
    function loadProvince() {
        var proHtml = '';
        for (var i = 0; i < areaData.length; i++) {
            if(areaData[i].provinceName==_province){
                proHtml += '<option value="' + areaData[i].provinceCode + '_' + areaData[i].mallCityList.length + '_' + i +'" selected>' + areaData[i].provinceName + '</option>';
                loadCity(areaData[i].mallCityList);
            }else{
                proHtml += '<option value="' + areaData[i].provinceCode + '_' + areaData[i].mallCityList.length + '_' + i +'">' + areaData[i].provinceName + '</option>';
            }
        }
        //初始化省数据
        $('form').find('select[name=province]').append(proHtml);
        form.render(null,'province2');
        form.on('select(province)', function(data) {
            $('form').find('select[name=area]').html('<option value="">请选择县/区</option>').parent().hide();
            var value = data.value;
            var d = value.split('_');
            var code = d[0];
            var count = d[1];
            var index = d[2];
            if (count > 0) {
                loadCity(areaData[index].mallCityList);
            } else {
                $('form').find('select[name=city]').parent().hide();
            }
        });
    }
    //加载市数据   '_' + citys[i].mallAreaList.length + '_' + i +
    function loadCity(citys) {
        var cityHtml = '';
        for (var i = 0; i < citys.length; i++) {
            if(citys[i].cityName==_city){
                cityHtml += '<option value="' + citys[i].cityCode + '_' + citys[i].mallAreaList.length + '_' + i +'" selected>' + citys[i].cityName + '</option>';
                loadArea(citys[i].mallAreaList);
            }else{
                cityHtml += '<option value="' + citys[i].cityCode + '_' + citys[i].mallAreaList.length + '_' + i +'">' + citys[i].cityName + '</option>';
            }
        }
        $('form').find('select[name=city]').html(cityHtml).parent().show();
        form.render(null,'city2');
        form.on('select(city)', function(data) {
            var value = data.value;
            var d = value.split('_');
            var code = d[0];
            var count = d[1];
            var index = d[2];
            if (count > 0) {
                loadArea(citys[index].mallAreaList);
            } else {
                $('form').find('select[name=area]').parent().hide();
            }
        });
    }
    //加载县/区数据
    function loadArea(areas) {
        var areaHtml = '';
        for (var i = 0; i < areas.length; i++) {
            if(areas[i].areaName==_area){
                areaHtml += '<option value="' + areas[i].areaCode + '" selected>' + areas[i].areaName + '</option>';
            }else{
                areaHtml += '<option value="' + areas[i].areaCode + '">' + areas[i].areaName + '</option>';
            }
        }
        $('form').find('select[name=area]').html(areaHtml).parent().show();
        form.render(null,'area2');
        form.on('select(area)', function(data) {});
    }
</script>