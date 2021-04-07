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
    <script src="/public/js/vue.min.js" charset="utf-8"></script>
</head>

<body>
<div id="app" class="x-body">
    <form class="layui-form layui-form-pane" action="javascript:void(0);">

        <div class="layui-form-item">
            <label class="layui-form-label" style="width:170px;">权限报备状态</label>
            <div class="layui-input-block">
                <input type="checkbox" name="auth_report_types" lay-filter="rs" value="1" title="报备" <?=strpos($data['resList']['auth_report_types'],'1')!==false?'checked':''?>>
                <input type="checkbox" name="auth_report_types" lay-filter="rs" value="2" title="带看" <?=strpos($data['resList']['auth_report_types'],'2')!==false?'checked':''?>>
                <input type="checkbox" name="auth_report_types" lay-filter="rs" value="3" title="成交" <?=strpos($data['resList']['auth_report_types'],'3')!==false?'checked':''?>>
                <input type="checkbox" name="auth_report_types" lay-filter="rs" value="4" title="确认业绩" <?=strpos($data['resList']['auth_report_types'],'4')!==false?'checked':''?>>
                <input type="checkbox" name="auth_report_types" lay-filter="rs" value="5" title="开票" <?=strpos($data['resList']['auth_report_types'],'5')!==false?'checked':''?>>
                <input type="checkbox" name="auth_report_types" lay-filter="rs" value="6" title="结佣" <?=strpos($data['resList']['auth_report_types'],'6')!==false?'checked':''?>>
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
    var fileIndex="";
    var _id=<?=$data['resList']['said'];?>;
    vm=new Vue({
        el: '#app',
        data: {
            list:[]
        },
        mounted: function () {
        this.$nextTick(function () {
            var _this=this;
            layui.use(['form'], function(){
                form = layui.form;
                form.on('submit(btn)', function(data){
                    //获取报备状态
                    var auth_report_types="";
                    $('input[name="auth_report_types"]:checked').each(function(){
                        auth_report_types+=$(this).val()+',';
                    });

                    layer.confirm('确认要修改吗？',function(){
                        ajax("/xiamenyyhoutai/xcxstore/power_doedit_bind",{id:_id,auth_report_types:auth_report_types},function(res){
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
                    });
                });


            });
        })
        }
    });
</script>