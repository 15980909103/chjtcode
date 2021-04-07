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
            <label class="layui-form-label">微信名称</label>
            <div class="layui-input-inline">
              <input class="layui-input" id="nickname" value="">
            </div>
            <label class="layui-form-label">经纪人姓名</label>
            <div class="layui-input-inline">
              <input class="layui-input" id="name" value="">
            </div>
            <div class="layui-input-inline" style="width:80px">
              <button class="layui-btn" onclick="getPageData();"><i class="layui-icon">&#xe615;</i></button>
            </div>
          </div>
        </div>
      </form>
      <xblock style="height:38px;"></xblock>
      <table class="layui-table layui-form">
        <thead>
        <tr>
          <th>ID</th>
          <th>微信名称</th>
          <th>微信头像</th>
          <th>经纪人姓名</th>
          <th>性别</th>
          <th>所在地</th>
          <th>电话</th>
          <th>个性签名</th>
          <th>优质经纪人</th>
          <th>状态</th>
          <th>添加时间</th>
          <th>修改时间</th>
          <th>查看勿扰</th>
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
          ajax("/xiamenyyhoutai/xcxstore/agent_status",{id:id,status:status},function(res){
            if(!res.success){
              layer.msg('状态修改失败', {icon: 5});
              $(data.elem).prop('checked',!$(data.elem).prop('checked'));
              form.render('checkbox');
            }
          });
        });
        form.on('switch(at_agent)', function(data){
          var id=data.value;
          var at_agent=data.elem.checked==true?1:0;
          ajax("/xiamenyyhoutai/xcxstore/agentAtAgent",{id:id,at_agent:at_agent},function(res){
            if(!res.success){
              layer.msg('修改失败', {icon: 5});
              $(data.elem).prop('checked',!$(data.elem).prop('checked'));
              form.render('checkbox');
            }
          });
        });
      });
      function getPageData() {
        var curr = arguments[0] ? arguments[0] : 1;
        var limit = arguments[1] ? arguments[1] : PAGELIMIT;
        var nickname=$('#nickname').val();
        var name=$('#name').val();
        ajax("/xiamenyyhoutai/xcxstore/agent_page",{nickname:nickname,name:name,curr:curr,limit:limit},function (data) {
          if(data.success){
            $("#tbody").empty();
            for(var i in data['data']){
              var id = data['data'][i]['id'];
              var status='',at_agent='';
              if(data['data'][i]['status']==1){
                status='<input type="checkbox" name="status" lay-text="开启|禁用" lay-filter="status" lay-skin="switch" value="'+id+'" checked>';
              }else{
                status='<input type="checkbox" name="status" lay-text="开启|禁用" lay-filter="status" lay-skin="switch" value="'+id+'">';
              }
              if(data['data'][i]['at_agent']==1){
                at_agent='<input type="checkbox" name="at_agent" lay-text="优质经纪人|普通经纪人" lay-filter="at_agent" lay-skin="switch" value="'+id+'" checked>';
              }else{
                at_agent='<input type="checkbox" name="at_agent" lay-text="优质经纪人|普通经纪人" lay-filter="at_agent" lay-skin="switch" value="'+id+'">';
              }
              var $content = '<tr>' +
                      '<td>'+parseInt((curr-1)*PAGELIMIT+parseInt(i)+1)+'</td>' +
                      '<td>'+data['data'][i]['nickname']+'</td>' +
                      '<td><img class="my-img" src="'+data['data'][i]['headimgurl']+'" alt="暂无图片" /></td>' +
                      '<td>'+data['data'][i]['name']+'</td>' +
                      '<td>'+data['data'][i]['sex']+'</td>' +
                      '<td>'+data['data'][i]['country']+ ' '+data['data'][i]['province']+' '+data['data'][i]['city']+'</td>' +
                      '<td>'+data['data'][i]['phone']+'</td>' +
                      '<td>'+data['data'][i]['signature']+'</td>' +
                      '<td>'+at_agent+'</td>' +
                      '<td>'+status+'</td>' +
                      '<td>'+data['data'][i]['create_time']+'</td>' +
                      '<td>'+data['data'][i]['update_time']+'</td>' +
                      '<td class="td-manage">'+
                      '<button type="button" class="layui-btn layui-btn layui-btn-xs" onclick="click_edit(\'查看\',\'/xiamenyyhoutai/xcxstore/do_not_disturb_record\','+id+',\'350\',\'450\')" ><i class="layui-icon">&#xe642;</i>查看</button>'+
                      '</td>' +
                      '<td class="td-manage">'+
                      '<button type="button" class="layui-btn layui-btn layui-btn-xs" onclick="click_edit(\'编辑\',\'/xiamenyyhoutai/xcxstore/agent_edit\','+id+',\'350\',\'450\')" ><i class="layui-icon">&#xe642;</i>编辑</button>'+
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
      //删除
      function click_del(id){
        layer.confirm('确认要删除吗？',function(){
          ajax("/xiamenyyhoutai/xcxstore/store_del",{id:id},function(res){
            if(res.success){
              layer.msg('已删除!',{icon:6,time:300},function(){
                getPageData($('#my_body').data('curr'));
              });
            }else{
              layer.msg(res.message,{icon: 5});
            }
          });
        });
      }
    </script>
  </body>
</html>