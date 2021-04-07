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
</head>
<body>
<div class="x-body">
    <form class="layui-form layui-form-pane">
        <input type="hidden" name="id" value="<?php echo $data['id'];?>">
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>公众号
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <select name="public_id">
					<?php foreach($publics as $v){?>
					<option value="<?php echo $v['id'];?>" <?php if($data['public_id']==$v['id']){?>selected<?php }?>><?php echo $v['name'];?></option>
					<?php }?>
				</select>
            </div>
        </div>
		<div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>名称
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <input type="text" name="name" autocomplete="off" class="layui-input" value="<?php echo $data['name'];?>">
            </div>
        </div>
		<div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>上级
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <select name="parent_id">
					<option value="0">一级菜单</option>
					<?php foreach($menus as $v){?>
					<option value="<?php echo $v['id'];?>" <?php if($data['parent_id']==$v['id']){?>selected<?php }?>><?php echo $v['name'];?></option>
					<?php }?>
				</select>
            </div>
        </div>
		<div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>类型
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <input lay-filter="radio" type="radio" name="type" value="1" title="点击" <?php if($data['type']==1){?>checked<?php }?>>
                <input lay-filter="radio" type="radio" name="type" value="2" title="跳转" <?php if($data['type']==2){?>checked<?php }?>>
                <input lay-filter="radio" type="radio" name="type" value="3" title="小程序" <?php if($data['type']==3){?>checked<?php }?>>
            </div>
        </div>
		<div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>链接/素材
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <input type="text" name="content" autocomplete="off" class="layui-input" value="<?php echo $data['content'];?>">
            </div>
        </div>
		<div id="isXiaocenxu" style="display: none;">
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span class="x-red">*</span>小程序的appid
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="appid" autocomplete="off" class="layui-input" maxlength="255">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span class="x-red">*</span>小程序的页面路径
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="pagepath" autocomplete="off" class="layui-input" maxlength="255">
                </div>
            </div>
        </div>
		<div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>排序
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <input type="number" name="sort" required="" lay-verify="required"
                       autocomplete="off" class="layui-input" min="1" value="<?php echo $data['sort'];?>">
            </div>
        </div>
		<div class="layui-form-item">
            <label class="layui-form-label"><span class="x-red">*</span>状态</label>
            <div class="layui-input-inline">
                <select name="status" lay-verify="required">
                    <option value="0" <?php if($data['status']==0){?>selected<?php }?>>禁用</option>
                    <option value="1" <?php if($data['status']==1){?>selected<?php }?>>启用</option>
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
<script src="/public/js/layui/layui.js" charset="utf-8"></script>
<script src="/public/js/admin/x-layui.js" charset="utf-8"></script>
<script>
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form() ,layer = layui.layer;
        //监听提交
        form.on('submit(btn)', function(data){
            //发异步，把数据提交给php
            $.ajax({
                type: 'POST',
                url: "/xiamenyyhoutai/wechat/menu_editsave",
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
                        layer.alert("保存失败", {icon: 5});
                    }
                },
                error:function(res){
                    layer.alert("请求失败", {icon: 2});
                }
            });
            return false;
        });
		form.on('radio(radio)', function(data){
            if(data.value==3){
                $('#isXiaocenxu').show();
            }else{
                $('#isXiaocenxu').hide();
            }
        });
    });
</script>
</body>
</html>