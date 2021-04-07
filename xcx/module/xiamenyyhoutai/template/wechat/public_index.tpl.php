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
        <style>
            /*分页样式*/
            .pagination{display:inline-block;padding-left:0;margin:20px 0 0 0;border-radius:4px;}
            .pagination li{display:inline;}
            .pagination li a,.pagination li span{position:relative;float:left;padding:6px 12px;line-height:1.42857143;color:#393D49;background:#fff;margin:0 0 0 8px;border:1px solid #eee}
            .pagination li a:hover{color:#fff;background:#009688}
            .pagination .active span{background:#009688;color:#fff}
            .pagination .disabled{display:none}
        </style>
    </head>
    <body>
        <div class="x-nav">
            <span class="layui-breadcrumb">
              <a><cite>首页</cite></a>
              <a><cite>微信管理</cite></a>
              <a><cite>公众号列表</cite></a>
            </span>
            <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right"  href="javascript:location.replace(location.href);" title="刷新"><i class="layui-icon" style="line-height:30px">ဂ</i></a>
        </div>
        <div class="x-body">
            <form class="layui-form x-center" action="/xiamenyyhoutai/wechat/public_index" method="get" id="myForm" style="width:95%">
                <div class="layui-form-pane" style="margin-top: 15px;">
                  <div class="layui-form-item">
                    <div class="layui-input-inline">
						<input class="layui-input" name="name" placeholder="公众号名称" value="">
					</div>
					<div class="layui-input-inline">
						<input class="layui-input" name="token" placeholder="公众号token" value="">
					</div>
					<div class="layui-input-inline">
						<input class="layui-input" name="appid" placeholder="公众号appid" value="">
					</div>
					<div class="layui-input-inline">
						<input class="layui-input" name="appsecret" placeholder="公众号appsecret" value="">
					</div>
                    <div class="layui-input-inline" style="width:80px">
                        <div class="layui-btn" onClick="button()" lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i></div>
                    </div>
                  </div>
                </div> 
            </form>
            <xblock><button class="layui-btn" onClick="add('添加公众号','/xiamenyyhoutai/wechat/public_add','800','510')"><i class="layui-icon">&#xe608;</i>添加</button><span class="x-right" style="line-height:40px">共有数据：<?php echo $total; ?> 条</span></xblock>
            <table class="layui-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" name="" value="" onClick="othercheck()"></th>
                        <th>ID</th>
						<th>公众号名称</th>
						<th>公众号原始ID</th>
						<th>公众号token</th>
						<th>公众号appid</th>
						<th>公众号appsecret</th>
						<th>创建时间</th>
						<th>修改时间</th>
						<th>操作</th>
                    </tr>
                </thead>
                <tbody>
				<?php foreach($rows as $v){?>
                    <tr class="<?php echo $v['id'];?>">
                        <td>
                            <input type="checkbox" value="1" name="checkbox" class="checkbox">
                        </td>
                        <td class="questions_id"><?php echo $v['id'];?></td>
                        <td>
                            <?php echo $v['name'];?>
                        </td>
						<td>
                            <?php echo $v['primordial_id'];?>
                        </td>
						<td>
                            <?php echo $v['token'];?>
                        </td>
						<td>
                            <?php echo $v['appid'];?>
                        </td>
						<td>
                            <?php echo $v['appsecret'];?>
                        </td>
                        <td >
							<?php echo date('Y-m-d H:i:s',$v['atime']);?>
                        </td>
						<td >
							<?php echo date('Y-m-d H:i:s',$v['utime']);?>
                        </td>
                        <td class="td-manage">
                            <a title="编辑" href="javascript:;" onClick="edit('编辑','/xiamenyyhoutai/wechat/public_edit/<?php echo $v['id'];?>','4','','510')"
                            class="ml-5" style="text-decoration:none">
                                <i class="layui-icon">&#xe642;</i>
                            </a>
                            <a title="删除" href="javascript:;" onClick="del(this,'<?php echo $v['id'];?>')"
                            style="text-decoration:none">
                                <i class="layui-icon">&#xe640;</i>
                            </a>
                        </td>
                    </tr>
                <?php }?>
                </tbody>
            </table>
            <div id="page"><?php echo $pagination; ?></div>
        </div>
        <script src="/public/js/layui/layui.js" charset="utf-8"></script>
        <script src="/public/js/admin/x-layui.js" charset="utf-8"></script>
        <script>
            function ajax(url,data,fun){
                $.ajax({
                    url: url,
                    data: data,
                    type: "post",
                    dataType: "json",
                    timeout: 5000,
                    success: fun
                })
            }

            layui.use(['form','laydate','element','laypage','layer'], function(){
                $ = layui.jquery;//jquery
              laydate = layui.laydate;//日期插件
              lement = layui.element();//面包导航
              layer = layui.layer;//弹出层
            });

            function button(){
                document.getElementById("myForm").submit();
            }

            function getByClass(sClass){
                var aResult=[];
                var aEle=document.getElementsByTagName('*');
                for(var i=0;i<aEle.length;i++){
                    /*当className相等时添加到数组中*/
                    if(aEle[i].className==sClass){
                        aResult.push(aEle[i]);
                    }
                }
                return aResult;
            }

             /*添加*/
            function add(title,url,w,h){
                x_admin_show(title,url,w,h);
            }
            //编辑 
           function edit (title,url,id,w,h) {
                x_admin_show(title,url,w,h); 
            }

            /*用户-查看*/
            function member_show(title,url,w,h){
                x_admin_show(title,url,w,h);
            }

            /*删除*/
            function del(obj,id){
                layer.confirm('确认要删除吗？',function(index){
                    //发异步删除数据
                    ajax("/xiamenyyhoutai/wechat/public_del/"+id,{},function(res){
                        if(res.ajax_error){
                            layer.msg('您没有操作权限!',{icon: 2,time:1500});return false;
                        }
                        if(res.success){
                            $(obj).parents("tr").remove();
                            layer.msg('已删除!',{icon:1,time:1000});
                        }else{
                            layer.msg('删除失败!',{icon: 2,time:1000});
                        }
                    });
                });
            }
            </script>
            <script>
            //反选
            var CheckBox=document.getElementsByName('checkbox');
            function othercheck(){
                for(i=0;i<CheckBox.length;i++){
                    if(CheckBox[i].checked==true){ CheckBox[i].checked=false;}
                    else{ CheckBox[i].checked=true}
                }
            }
            </script>
    </body>
</html>