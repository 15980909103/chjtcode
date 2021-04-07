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
    <form class="layui-form layui-form-pane" id="form_ajax">
        <input type="hidden" name="id" value="<?php echo $data['id'];?>">
		<div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>名称
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <input type="text" name="name" autocomplete="off" class="layui-input" maxlength="30" value="<?php echo $data['name'];?>">
            </div>
        </div>
		<div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>描述
            </label>
            <div class="layui-input-inline" style="width:500px;">
                <input type="text" name="digest" autocomplete="off" class="layui-input" maxlength="30" value="<?php echo $data['digest'];?>">
            </div>
        </div>
		<div class="layui-form-item layui-form-text">
            <label class="layui-form-label">
                拥有权限
            </label>
            <table  class="layui-table layui-input-block">
                <tbody>
				<?php
				if(empty($auths)){
					echo "暂未设置权限";
				}else{
				foreach($auths as $key=>$val){?>
                <tr>
                    <td>
                        <?php echo $key;?>
                    </td>
                    <td>
                        <div class="layui-input-block">
							<?php
							$i = 1;
							foreach($val as $v){
								echo $i%3==0?'<br/>':'';
							?>
								<span style="width:33.3%;">
									<input name="auths[]" type="checkbox" <?php if(in_array($v['id'],$auth_arr)){?>checked<?php }?> value="<?php echo $v['id'];?>"> <?php echo $v['action_name'];?>
								</span>
                            <?php
							$i++;
							}?>
                        </div>
                    </td>
                </tr>
                <?php }
				}
				?>
                </tbody>
            </table>
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
    layui.use(['form','layer','upload'], function(){
        $ = layui.jquery;
        var form = layui.form() ,layer = layui.layer;
        //监听提交
        form.on('submit(btn)', function(data){
            var postData=$('#form_ajax').serialize();
			//发异步，把数据提交给php
            $.ajax({
                type: 'POST',
                url: "/xiamenyyhoutai/admin/group_editsave",
                //data: data.field,
				data: postData,
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
    });
</script>
</body>
</html>