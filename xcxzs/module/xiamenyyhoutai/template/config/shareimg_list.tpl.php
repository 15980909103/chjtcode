<!DOCTYPE html>
<html>
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
    <style>.my-img{width: 200px;height: 200px;}</style>
  </head>
  
  <body class="layui-anim layui-anim-up">
    <div class="x-nav">
      <span class="layui-breadcrumb" lay-separator=">"></span>
      <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>
    <div id="my_body" class="x-body">
      <xblock>
        <button id="qrCode" type="button" class="layui-btn"><i class="layui-icon">&#xe609;</i>上传图片</button>
      </xblock>
      <div>
        <img class="my-img" src="<?=$qrcode?>" alt="暂无图片">
      </div>
    </div>
    <script>
      setNavList();
      layui.use(['form','upload'], function(){
        form = layui.form;
        var upload = layui.upload;
        var logoUpload=upload.render({
          elem: '#qrCode' //绑定元素
          ,url: "/xiamenyyhoutai/config/shareimg" //上传接口
          ,size: 2048
          ,accept: "images"
          ,auto:true
          ,done: function(res){
            if(res.success){
              $('.my-img').attr('src',res.img);
              layer.alert("上传成功");
            }else{
              layer.msg("保存失败");
            }
          }
        });
      });
    </script>
  </body>
</html>