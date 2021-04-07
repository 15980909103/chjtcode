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
    <link rel="stylesheet" href="/public/css/admin/x-admin.css?t=<?php echo JsVer;?>" media="all">
    <script src="/public/js/jquery-2.0.0.min.js" charset="utf-8"></script>
    <script src="/public/js/layui2/layui.js" charset="utf-8"></script>
    <script src="/public/js/admin/x-layui.js" charset="utf-8"></script>
    <script src="/public/js/admin/area.js" charset="utf-8"></script>
</head>
<body>
<div class="x-body">
    <form class="layui-form layui-form-pane">
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>姓名
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <input type="text" name="name" required="" lay-verify="required" autocomplete="off" class="layui-input" maxlength="50">
            </div>
        </div>
		<div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>用户名
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <input type="text" name="username" autocomplete="off" class="layui-input">
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
					<option value="0">超级管理员</option>
					<?php foreach($groups as $v){?>
					<option value="<?php echo $v['id'];?>"><?php echo $v['name'];?></option>
					<?php }?>
				</select>
            </div>
        </div>
        <div class="layui-form-item"  id="groupname">
            <label class="layui-form-label">
                <span class="x-red"></span>绑定人员
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <select name="personnel">

                </select>
            </div>
        </div>
		<div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>状态
            </label>
            <div class="layui-input-inline" style="width:500px;">
				<select name="status">
					<option value="1">启用</option>
					<option value="0">禁用</option>
				</select>
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
<!--<script src="/public/js/layui/layui.js" charset="utf-8"></script>-->
<!--<script src="/public/js/admin/x-layui.js" charset="utf-8"></script>-->
<script>
    $('#groupname').hide();
    var areaData = Area;
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        form = layui.form
        var layer = layui.layer;
        loadProvince();
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
                url: "/xiamenyyhoutai/admin/addsave",
                data: data.field,
                dataType: "json",
                success: function(res){
                    if(res.ajax_error){
                        layer.msg('您没有操作权限!',{icon: 2,time:1500});return false;
                    }
                    if(res.success){
                        layer.alert("保存成功", {icon: 6},function () {
                            // 获得frame索引
                            var index = parent.layer.getFrameIndex(window.name);
                            //关闭当前frame
                            parent.layer.close(index);
							parent.location.reload();
                        });
                    }else{
                        layer.alert(res.message, {icon: 5});
                    }
                },
                error:function(res){
                    layer.alert("请求失败", {icon: 2});
                }
            });
            return false;
        });
        form.on('select(types)', function(data) {
            var value = data.value;

            if(value == 0 || value == 2 || value == 1 || value ==3 || value == 4 || value == 6) {
                $('#groupname').hide();
                $('#groupinfo').hide();
            } else {
                // console.log(value);
                $.ajax({
                    type: 'POST',
                    url: "/xiamenyyhoutai/admin/personnel",
                    data: {type:data.value},
                    dataType: "json",
                    success: function(res){
                        if(res.ajax_error){
                            layer.msg('您没有操作权限!',{icon: 2,time:1500});return false;
                        }
                        if(res.success){
                            var data = res.success
                            var Html = '<option value="0">请选择</option>'
                            for (var i = 0;i<data.length;i++){
                                var hname = data[i].name
                                if(data[i].agent_name) {
                                    hname += ' ' + data[i].agent_name + ' '
                                }
                                Html += '<option value="' + data[i].said + '" >' + hname + '</option>';
                            }

                            $('form').find('select[name=personnel]').html(Html).parent().show();
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
            proHtml += '<option value="' + areaData[i].provinceCode + '_' + areaData[i].mallCityList.length + '_' + i +'">' + areaData[i].provinceName + '</option>';
        }
        //初始化省数据
        $("form").find('select[name=province]').append(proHtml);
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
            cityHtml += '<option value="' + citys[i].cityCode + '_' + citys[i].mallAreaList.length + '_' + i +'">' + citys[i].cityName + '</option>';
        }
        $('form').find('select[name=city]').html(cityHtml).parent().show();
        form.render();
    }
</script>
</body>
</html>