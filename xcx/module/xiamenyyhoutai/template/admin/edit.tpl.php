<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>
        X-admin v1.0
    </title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="/public/css/admin/x-admin2.css?t=<?php echo JsVer;?>" media="all">
    <script src="/public/js/admin/area.js" charset="utf-8"></script>
</head>
<body>
<div class="x-body">
    <form class="layui-form layui-form-pane">
        <input type="hidden" name="id" value="<?php echo $data['id'];?>">
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>姓名
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <input type="text" name="name" required="" lay-verify="required"
                       autocomplete="off" class="layui-input" maxlength="50" value="<?php echo $data['name'];?>">
            </div>
        </div>
		<div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>用户名
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <input type="text" name="username" autocomplete="off" class="layui-input" value="<?php echo $data['username'];?>">
            </div>
        </div>
		<div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>密码
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <input type="password" name="password" autocomplete="off" class="layui-input">
            </div>
        </div>
		<div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>分组
            </label>
            <div class="layui-input-inline" style="width:500px;">
				<select name="gid" lay-filter="types">
					<option value="0" <?php if($data['gid']==0){?>selected<?php }?>>超级管理员</option>
					<?php foreach($groups as $v){?>
					<option value="<?php echo $v['id'];?>" <?php if($data['gid']==$v['id']){?>selected<?php }?>><?php echo $v['name'];?></option>
					<?php }?>
				</select>
            </div>
        </div>
        <div class="layui-form-item"  id="groupname">
            <label class="layui-form-label">
                <span class="x-red"></span>绑定人员 <?php $data['data']['gid']?>
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <select name="channel_id">
                    <option value="0" <?php if($data['channel_id']==0){?>selected<?php }?>>请选择</option>
                    <?php foreach($list as $v){?>
                        <option value="<?php echo $v['said'];?>" <?php if($data['channel_id']==$v['said']){?>selected<?php }?>><?php echo $v['select_name'];?></option>
                    <?php }?>
                </select>
            </div>
        </div>
		<div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>状态
            </label>
            <div class="layui-input-inline" style="width:500px;">
				<select name="status">
					<option value="1" <?php if($data['status']==1){?>selected<?php }?>>启用</option>
					<option value="0" <?php if($data['status']==0){?>selected<?php }?>>禁用</option>
				</select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="padding:0;border:0;width:108px;">
                <button type="button" class="layui-btn" id="logoUpload">
                    <i class="layui-icon">&#xe67c;</i>上传头像
                </button>
            </label>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>头像
            </label>
            <div class="layui-input-inline">
                <img id="mylogoimg" class="myimg" src="<?php echo $data['img'];?>" alt="暂无图片" style="max-width: 100%;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>授权二维码
            </label>
            <div class="layui-input-inline" style="width:500px;">
				<img src="/xiamenyyhoutai/admin/qrcode/<?php echo $data['id'];?>" width="200">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                省份
            </label>
            <div class="layui-input-inline">
                <select id="province" name="province" lay-filter="province" >
                    <option value="">请选择省</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                城市
            </label>
            <div class="layui-input-inline">
                <select id="city" name="city" lay-filter="city" >
                    <option value="">请选择市</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item" style="text-align:center;">
            <button  class="layui-btn" lay-filter="btn" lay-submit="">
                保存
            </button>
        </div>
    </form>
</div>
<script src="/public/js/layui2/layui.js" charset="utf-8"></script>
<script src="/public/js/admin/x-layui.js" charset="utf-8"></script>
<script>

    var fileIndex="";

    // $('#groupname').hide();
    var areaData = Area;
    layui.use(['form','upload'], function(){
        $ = layui.jquery;
        form = layui.form;
        var upload = layui.upload;
        var logoUpload=upload.render({
            elem: '#logoUpload' //绑定元素
            ,url: "/xiamenyyhoutai/admin/uploadimg" //上传接口
            ,size: 2048
            ,data:{
                id:"<?=$data['id']?>"
            }
            ,accept: "images"
            ,auto:false
            ,choose:function(obj){
                var files=obj.pushFile();  //删除文件队列确保只上传一次
                obj.preview(function(index, file, result){
                    delete files[fileIndex];
                    fileIndex=index;
                    $('#mylogoimg').attr('src',result);
                });
            }
            ,done: function(res){
                if(res.success){
                    fileIndex="";
                }
            }
        });
        loadProvince();

        var oldType=<?=$data['gid']?>;
        if(oldType == 0 || oldType == 2 || oldType == 1 || oldType ==3 || oldType == 4 || oldType == 6){
            $('#groupname').hide();
        }
        //监听提交
        form.on('submit(btn)', function(data){
            if(data.field.province !== '') {
                data.field.province = $('#province').find("option:selected").text();
            }
            if(data.field.city !== '') {
                data.field.city = $('#city').find("option:selected").text();
            }
            //发异步，把数据提交给php
            $.ajax({
                type: 'POST',
                url: "/xiamenyyhoutai/admin/editsave",
                data: data.field,
                dataType: "json",
                success: function(res){
                    if(res.ajax_error){
                        parent.layer.msg('您没有操作权限!',{icon: 2,time:1500});return false;
                    }
                    if(res.success){
                        logoUpload.upload();
                        parent.layer.alert("保存成功", {icon: 6},function () {
                            // 获得frame索引
                            var index = parent.layer.getFrameIndex(window.name);
                            //关闭当前frame
                            parent.layer.close(index);
							parent.location.reload();
                        });
                    }else{
                        parent.layer.alert(res.message, {icon: 5});
                    }
                },
                error:function(res){
                    parent.layer.alert("请求失败", {icon: 2});
                }
            });
            return false;
        });

        form.on('select(types)', function(data) {
            var value = data.value;
            // console.log(value);
            if(value == 0 || value == 2 || value == 1 || value ==3 || value == 4 || value == 6) {
                $('#groupname').hide();
                $('#groupinfo').hide();
            } else {
                $.ajax({
                    type: 'POST',
                    url: "/xiamenyyhoutai/admin/personnel",
                    data: {'type':value},
                    dataType: "json",
                    success: function(res){
                        if(res.ajax_error){
                            layer.msg('您没有操作权限!',{icon: 2,time:1500});return false;
                        }
                        if(res.success){
                            var data = res.success
                            var Html = '<option value="0"></option>'
                            for (var i = 0;i<data.length;i++){
                                var hname = data[i].name
                                if(data[i].agent_name) {
                                    hname += ' ' + data[i].agent_name + ' '
                                }
                                Html += '<option value="' + data[i].said + i +'">' + hname + '</option>';
                            }

                            $('form').find('select[name=channel_id]').html(Html).parent().show();
                            form.render();
                            // console.log(res.success)

                        }else{
                            layer.alert("保存失败", {icon: 5});
                        }
                    },
                    error:function(res){
                        layer.alert("请求失败", {icon: 2});
                    }
                });
                $('#groupname').show();
                $('#groupinfo').show();
            }
        });
    });

    function loadProvince() {
        var proHtml = '';
        for (var i = 0; i < areaData.length; i++) {
            if(areaData[i].provinceName=="<?=$data['province']?>"){
                proHtml += '<option value="' + areaData[i].provinceCode + '_' + areaData[i].mallCityList.length + '_' + i +'" selected>' + areaData[i].provinceName + '</option>';
                loadCity(areaData[i].mallCityList);
            }else{
                proHtml += '<option value="' + areaData[i].provinceCode + '_' + areaData[i].mallCityList.length + '_' + i +'">' + areaData[i].provinceName + '</option>';
            }
        }
        //初始化省数据
        $('form').find('select[name=province]').append(proHtml);
        form.render();
        form.on('select(province)', function(data) {
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
            if(citys[i].cityName=="<?=$data['city']?>"){
                cityHtml += '<option value="' + citys[i].cityCode + '_' + citys[i].mallAreaList.length + '_' + i +'" selected>' + citys[i].cityName + '</option>';
            }else{
                cityHtml += '<option value="' + citys[i].cityCode + '_' + citys[i].mallAreaList.length + '_' + i +'">' + citys[i].cityName + '</option>';
            }
        }
        $('form').find('select[name=city]').html(cityHtml).parent().show();
        form.render();
    }
</script>
</body>
</html>