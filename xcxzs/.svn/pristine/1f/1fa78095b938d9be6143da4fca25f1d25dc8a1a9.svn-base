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
    <style>.my-img{width: 50px;height: 50px;}.my-detail-img{width: 100px;height:50px;}</style>
  </head>
  <body class="layui-anim layui-anim-up">
    <div class="x-nav">
      <span class="layui-breadcrumb" lay-separator=">"></span>
      <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>
    <div id="my_body" class="x-body">
      <form id="myForm" class="layui-form x-center" style="width:100%" action="javascript:void(0);">
        <div class="layui-form-pane" style="margin-top: 15px;">
          <div class="layui-form-item">
            <label class="layui-form-label">城市名称</label>
            <div class="layui-input-inline">
              <input class="layui-input" id="title" value="">
            </div>
            <div class="layui-input-inline" style="width:80px">
              <button class="layui-btn" onclick="getPageData();"><i class="layui-icon">&#xe615;</i></button>
            </div>
          </div>
        </div>
      </form>
      <table class="layui-table layui-form">
        <thead>
        <tr>
          <th>ID</th>
          <th>城市名称</th>
          <th>城市代码</th>
          <th>是否启用</th>
          <th>是否常用</th>
          <th>操作</th>
        </tr>
        </thead>
        <tbody id="tbody"></tbody>
      </table>
      <div id="page"></div>
    </div>
    <script>
      setNavList();
      layui.use(['form','laypage','element'], function(){
        form = layui.form;
        laypage = layui.laypage;
        getPageData();
        form.on('switch(status)', function(data){
          var id=data.value;
          var status=data.elem.checked==true?1:0;
          ajax("/xiamenyyhoutai/xcxbuilding/building_city_status",{id:id,status:status},function(res){
            if(!res.success){
              layer.msg('状态修改失败', {icon: 5});
              $(data.elem).prop('checked',!$(data.elem).prop('checked'));
              form.render('checkbox');
            }
          });
        });
          form.on('switch(is_common)', function(data){
              var id=data.value;
              var common=data.elem.checked==true?1:0;
              ajax("/xiamenyyhoutai/xcxbuilding/building_city_common",{id:id,common:common},function(res){
                  if(!res.success){
                      layer.msg('状态修改失败', {icon: 5});
                      $(data.elem).prop('checked',!$(data.elem).prop('checked'));
                      form.render('checkbox');
                  }
              });
          });
      });
      function getPageData() {
        var curr = arguments[0] ? arguments[0] : 1;
        var limit = arguments[1] ? arguments[1] : PAGELIMIT;
        var title=$('#title').val();
        ajax("/xiamenyyhoutai/xcxbuilding/building_city_list",{title:title,status:status,curr:curr,limit:limit},function (data) {
          if(data.success){
            $("#tbody").empty();
            for(var i in data['data']){
              var id = data['data'][i]['id'];
              var status='',common='';
              if(data['data'][i]['status']==1){
                status='<input type="checkbox" name="status" lay-text="开启|禁用" lay-filter="status" lay-skin="switch" value="'+id+'" checked>';
              }else{
                status='<input type="checkbox" name="status" lay-text="开启|禁用" lay-filter="status" lay-skin="switch" value="'+id+'">';
              }
              if(data['data'][i]['is_common']==1){
                  common='<input type="checkbox" name="is_common" lay-text="开启|禁用" lay-filter="is_common" lay-skin="switch" value="'+id+'" checked>';
              }else{
                  common='<input type="checkbox" name="is_common" lay-text="开启|禁用" lay-filter="is_common" lay-skin="switch" value="'+id+'">';
              }
              var $content = '<tr>' +
                      '<td>'+parseInt((curr-1)*PAGELIMIT+parseInt(i)+1)+'</td>' +
                      '<td>'+data['data'][i]['city_name']+'</td>' +
                      '<td>'+data['data'][i]['city_no']+'</td>' +
                      '<td>'+status+'</td>' +
                      '<td>'+common+'</td>' +
                      '<td class="td-manage">'+
                      '<button type="button" class="layui-btn layui-btn layui-btn-xs" onclick="click_edit(\'区域管理\',\'/xiamenyyhoutai/xcxbuilding/city_area_index\','+data['data'][i]['city_no']+',\'600\',\'500\')" ><i class="layui-icon">&#xe642;</i>区域管理</button>'+
                      '</td>' +
                      '</tr>';
              $("#tbody").append($content);
            }
            $('#my_body').data('curr',curr);
            form.render('checkbox');
            pages(data['count'],curr);
          }else{
            if(curr != 1){
              getPageData(parseInt(curr-1));
            }else{
              $("#tbody").empty();
              $("#tbody").append("<span>您还没有添加数据！！！</span>");
            }
          }
        })
      }
      function pages(allcount,curr) {
        laypage.render({
          elem: 'page'
          ,limit:PAGELIMIT
          ,count: allcount
          ,curr:curr
          ,layout:['prev', 'page', 'next','limit','skip','count']
          ,jump: function (obj, first) {
            if (!first) {
              PAGELIMIT=obj.limit;
              getPageData(obj.curr,PAGELIMIT);
            }
          }
        });
      }
    </script>
  </body>
</html>