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
    <style>.my-img{width: 200px;height: 200px;}.layui-form-pane .layui-form-label{width:220px !important;}</style>
  </head>
  <body class="layui-anim layui-anim-up">
    <div class="x-nav">
      <span class="layui-breadcrumb" lay-separator=">"></span>
      <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>
    <div id="my_body" class="x-body">
      <form class="layui-form layui-form-pane">
        <!--<div class="layui-form-item">
          <label class="layui-form-label">
            <span class='x-red'>*</span>报备保护(单位：天)
          </label>
          <div class="layui-input-inline">
            <input id="reportProtectDate" type="number" autocomplete="off" class="layui-input" value="<?/*=$report_protect_date*/?>" style="line-height: 38px;">
          </div>
          <button class="layui-btn layui-btn-radius layui-btn-normal" type="button" style="display:inline-block;width: 100px;" onclick="onReportProtectDate()">保存</button>
        </div>-->
        <div class="layui-form-item">
          <label class="layui-form-label">
            <span class='x-red'>*</span>MAX文章数
          </label>
          <div class="layui-input-inline">
            <input id="maxArticle" type="number" autocomplete="off" class="layui-input" value="<?=$max_article?>" style="line-height: 38px;">
          </div>
          <button class="layui-btn layui-btn-radius layui-btn-normal" type="button" style="display:inline-block;width: 100px;" onclick="onSaveMaxArticle()">保存</button>
        </div>
        <div class="layui-form-item">
          <label class="layui-form-label">
            <span class='x-red'>*</span>系统名称
          </label>
          <div class="layui-input-inline">
            <input id="systemName" type="text" autocomplete="off" class="layui-input" value="<?=$system_name?>">
          </div>
          <button class="layui-btn layui-btn-radius layui-btn-normal" type="button" style="display:inline-block;width: 100px;" onclick="onSaveSystemName()">保存</button>
        </div>
        <div class="layui-form-item">
          <label class="layui-form-label">
            <span class='x-red'>*</span>系统头像
          </label>
          <div class="layui-input-inline">
            <button id="qrCode" type="button" class="layui-btn"><i class="layui-icon">&#xe609;</i>上传图片</button>
          </div>
        </div>
        <div class="layui-form-item">
          <div class="layui-input-inline">
            <img class="my-img" src="<?=$system_logo?>" alt="暂无图片">
          </div>
        </div>

        <div class="layui-form-item">
              <label class="layui-form-label">
                  <span class='x-red'>*</span>客服微信
              </label>
              <div class="layui-input-inline">
                  <input id="systemWechat" type="text" autocomplete="off" class="layui-input" value="<?=$system_wechat?>">
              </div>
              <button class="layui-btn layui-btn-radius layui-btn-normal" type="button" style="display:inline-block;width: 100px;" onclick="onSaveSystemWechat()">保存</button>
        </div>

        <div class="layui-form-item">
              <label class="layui-form-label">
                  <span class='x-red'>*</span>客服公司
              </label>
              <div class="layui-input-inline">
                  <input id="systemCompany" type="text" autocomplete="off" class="layui-input" value="<?=$system_company?>">
              </div>
              <button class="layui-btn layui-btn-radius layui-btn-normal" type="button" style="display:inline-block;width: 100px;" onclick="onSaveSystemCompany()">保存</button>
        </div>

        <div class="layui-form-item">
              <label class="layui-form-label">
                  <span class='x-red'>*</span>客服头像
              </label>
              <div class="layui-input-inline">
                  <button id="wechatCode" type="button" class="layui-btn"><i class="layui-icon">&#xe609;</i>上传图片</button>
              </div>
        </div>
        <div class="layui-form-item">
              <div class="layui-input-inline">
                  <img class="my-img" id="img" src="<?=$wechat_logo?>" alt="暂无图片">
              </div>
        </div>

      </form>
    </div>
    <script>
      setNavList();
      layui.use(['form','upload','element'], function(){
        form = layui.form;
        var upload = layui.upload;
        var logoUpload=upload.render({
          elem: '#qrCode' //绑定元素
          ,url: "/xiamenyyhoutai/xcxsetting/systemLogo" //上传接口
          ,size: 2048
          ,accept: "images"
          ,auto:true
          ,done: function(res){
            if(res.success){
              $('.my-img').attr('src',res.img);
            }else{
              layer.msg("保存失败");
            }
          }
        });
      });
      layui.use(['form','upload','element'], function(){
          form = layui.form;
          var upload = layui.upload;
          var logoUpload=upload.render({
              elem: '#wechatCode' //绑定元素
              ,url: "/xiamenyyhoutai/xcxsetting/wechatLogo" //上传接口
              ,size: 2048
              ,accept: "images"
              ,auto:true
              ,done: function(res){
                  if(res.success){
                      $('#img').attr('src',res.img);
                  }else{
                      layer.msg("保存失败");
                  }
              }
          });
      });
      function onReportProtectDate(){
        var reportProtectDate=$('#reportProtectDate').val();
        if(reportProtectDate==''||reportProtectDate<1){
          layer.alert("内容必须大于1");
          return false;
        }
        ajax("/xiamenyyhoutai/xcxsetting/updateReportProtectDate",{reportProtectDate:reportProtectDate},function(res){
          if(res.success){
            layer.msg('保存成功！', {icon: 6});
          }else{
            layer.msg('保存失败！', {icon: 5});
          }
        });
      }
      function onSaveMaxArticle(){
        var maxArticle=$('#maxArticle').val();
        if(maxArticle==''||maxArticle<1){
          layer.alert("内容必须大于1");
          return false;
        }
        ajax("/xiamenyyhoutai/xcxsetting/updateMaxArticle",{maxArticle:maxArticle},function(res){
          if(res.success){
            layer.msg('保存成功！', {icon: 6});
          }else{
            layer.msg('保存失败！', {icon: 5});
          }
        });
      }
      function onSaveSystemName(){
        var systemName=$('#systemName').val();
        if(systemName==""){
          layer.alert("请输入名称！");
          return false;
        }
        ajax("/xiamenyyhoutai/xcxsetting/updateSystemName",{systemName:systemName},function(res){
          if(res.success){
            layer.msg('保存成功！', {icon: 6});
          }else{
            layer.msg('保存失败！', {icon: 5});
          }
        });
      }
      function onSaveSystemWechat(){
          var systemWechat=$('#systemWechat').val();
          if(systemWechat==""){
              layer.alert("请输入微信号！");
              return false;
          }
          ajax("/xiamenyyhoutai/xcxsetting/updateSystemWechat",{systemWechat:systemWechat},function(res){
              if(res.success){
                  layer.msg('保存成功！', {icon: 6});
              }else{
                  layer.msg('保存失败！', {icon: 5});
              }
          });
      }
      function onSaveSystemCompany(){
          var systemCompany=$('#systemCompany').val();
          if(systemCompany==""){
              layer.alert("请输入公司名称！");
              return false;
          }
          ajax("/xiamenyyhoutai/xcxsetting/updateSystemCompany",{systemCompany:systemCompany},function(res){
              if(res.success){
                  layer.msg('保存成功！', {icon: 6});
              }else{
                  layer.msg('保存失败！', {icon: 5});
              }
          });
      }

    </script>
  </body>
</html>