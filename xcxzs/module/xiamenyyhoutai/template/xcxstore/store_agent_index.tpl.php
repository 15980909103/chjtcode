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
    <script src="/public/js/admin/clipboard.min.js" charset="utf-8"></script>
    <style>.my-img{width: 50px;height: 50px;}.my-detail-img{width: 100px;height:50px;}</style>
  </head>
  <body class="layui-anim layui-anim-up">
    <div class="x-nav">
      <span class="layui-breadcrumb" lay-separator=">"></span>
      <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>
    <div id="my_body" class="x-body">
      <h1 style="text-align: center;padding-bottom:20px;"><?=$data['title']?></h1>
      <xblock>
        <button class="layui-btn" onclick="my_addag()"><i class="layui-icon">&#xe608;</i>添加</button>
      </xblock>
      <table class="layui-table layui-form">
        <thead>
        <tr>
          <th>ID</th>
          <th>人员头像</th>
          <th>人员昵称</th>
          <th>职位</th>
          <th>添加时间</th>
          <th>绑定时间</th>
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
      });
      function getPageData() {
        var id = "<?=$data['id']?>";
        var curr = arguments[0] ? arguments[0] : 1;
        var limit = arguments[1] ? arguments[1] : PAGELIMIT;
        ajax("/xiamenyyhoutai/xcxstore/store_agent_page",{id:id,curr:curr,limit:limit},function (data) {
          if(data.success){
            $("#tbody").empty();
            for(var i in data['data']){
              var id = data['data'][i]['said'];
              var myimg='',mydelbtn="";
              if(data['data'][i]['headimgurl']=='未绑定'){
                myimg=data['data'][i]['headimgurl'];
              }else{
                myimg='<img class="my-img" src="'+data['data'][i]['headimgurl']+'" alt="暂无图片" />';
              }
              if(data['data'][i]['type']!='店长'){
                mydelbtn='<button type="button" class="layui-btn-danger layui-btn layui-btn-xs" onclick="click_del('+id+')"><i class="layui-icon">&#xe640;</i>删除</button>';
              }
              var $content = '<tr>' +
                      '<td>'+parseInt((curr-1)*PAGELIMIT+parseInt(i)+1)+'</td>' +
                      '<td>'+myimg+'</td>' +
                      '<td>'+data['data'][i]['nickname']+'</td>' +
                      '<td>'+data['data'][i]['type']+'</td>' +
                      '<td>'+data['data'][i]['create_time']+'</td>' +
                      '<td>'+data['data'][i]['update_time']+'</td>' +
                      '<td class="td-manage">'+
                      '<button type="button" class="layui-btn layui-btn-normal layui-btn-xs my-copy" data-clipboard-text="'+data['data'][i]['active_code_url']+'"><i class="layui-icon">&#xe64c;</i>复制链接</button>'+
                      '<button type="button" class="layui-btn layui-btn layui-btn-xs" onclick="resetBind('+id+')" ><i class="layui-icon">&#xe9aa;</i>重新绑定</button>'+
                      mydelbtn+
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
          ajax("/xiamenyyhoutai/xcxstore/store_agent_del",{said:id},function(res){
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
      //添加店员
      function my_addag(){
        layer.confirm('确认要添加吗？',function(){
          ajax("/xiamenyyhoutai/xcxstore/store_agent_add",{id:"<?=$data['id']?>"},function(res){
            if(res.success){
              layer.alert("保存成功", {icon: 6},function (index2) {
                getPageData($('#my_body').data('curr'));
                layer.close(index2);
                var index = layer.getFrameIndex(window.name);
                layer.close(index);
              });
            }else{
              layer.msg(res.message,{icon: 5});
            }
          });
        });
      }
      //重新绑定
      function resetBind(id){
        layer.confirm('确认要重新绑定吗？',function(){
          ajax("/xiamenyyhoutai/xcxstore/store_agent_reset",{said:id},function(res){
            if(res.success){
              layer.alert("重置成功", {icon: 6},function (index2) {
                getPageData($('#my_body').data('curr'));
                layer.close(index2);
                var index = layer.getFrameIndex(window.name);
                layer.close(index);
              });
            }else{
              layer.msg(res.message,{icon: 5});
            }
          });
        });
      }
      //复制链接
      $(function(){
        var clipboard = new ClipboardJS('.my-copy');
        clipboard.on('success', function (e) {
          layer.msg('复制成功！');
        });
        clipboard.on('error', function (e) {
          layer.msg('复制失败！');
        });
      })
    </script>
  </body>
</html>