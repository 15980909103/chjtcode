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
    <link rel="stylesheet" href="/public/css/admin/floor_index.css">
    <script src="/public/js/jquery-2.0.0.min.js" charset="utf-8"></script>
    <script src="/public/js/layui2/layui.js" charset="utf-8"></script>
    <script src="/public/js/admin/x-layui.js" charset="utf-8"></script>
    <script src="/public/js/admin/public.js" charset="utf-8"></script>
    <script src="/public/js/vue.min.js" charset="utf-8"></script>
    <script src="/public/js/jquery-ui.js" charset="utf-8"></script>
    <style>.layui-input{line-height: 38px;}.myimg{margin-left: 10px;}#container {width:400px;height:300px;margin: 20px 0;}</style>
</head>
<body>
<div id="app" class="x-body">
    <div id="my-img-div" :style="'background-image: url('+backImg+');background-size:100% 100%;'">
        <div class='view1-floor' :style="'top:'+item.f_top+'%;left:'+item.f_left+'%;'" v-for="item in floorList" :data-id="item.id" v-cloak>{{item.title}} | {{item.sales_status}}</div>
    </div>
    <xblock>
        <button class="layui-btn" id="logoUpload"><i class="layui-icon">&#xe608;</i>上传楼盘地图</button>
        <button class="layui-btn" onclick="click_edit('添加','/xiamenyyhoutai/xcxbuilding/floor_add',<?=$data['id']?>,'680','450');"><i class="layui-icon">&#xe608;</i>添加</button>
        <button class="layui-btn layui-btn-normal" @click="onSave"><i class="layui-icon">&#xe609;</i>保存</button>
    </xblock>
    <table id="my_body" class="layui-table layui-form">
        <thead>
        <tr>
            <th>ID</th>
            <th>楼栋</th>
            <th>销售状态</th>
            <th>楼层数</th>
            <th>户数</th>
            <th>产权年限</th>
            <th>开盘时间</th>
            <th>交房时间</th>
            <th>状态</th>
            <th>添加时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody id="tbody"></tbody>
    </table>
    <div id="page"></div>
</div>
</body>
</html>
<script>
    var floorRecord={};
    vm=new Vue({
        el: '#app',
        data: {
            backImg:"<?=$data['floor_img']?>",
            floorList:[]
        },
        mounted: function () {
        this.$nextTick(function () {
            var _this=this;
            layui.use(['form','upload','element','laypage'], function(){
                form = layui.form;
                laypage = layui.laypage;
                var upload = layui.upload;
                initFloorList();
                getPageData();
                form.on('switch(status)', function(data){
                    var id=data.value;
                    var status=data.elem.checked==true?1:0;
                    ajax("/xiamenyyhoutai/xcxbuilding/floor_status",{id:id,status:status},function(res){
                        if(!res.success){
                            layer.msg('状态修改失败', {icon: 5});
                            $(data.elem).prop('checked',!$(data.elem).prop('checked'));
                            form.render('checkbox');
                        }else{
                            initFloorList();
                        }
                    });
                });
                var logoUpload=upload.render({
                    elem: '#logoUpload' //绑定元素
                    ,url: "/xiamenyyhoutai/xcxbuilding/floor_img" //上传接口
                    ,size: 2048
                    ,accept: "images"
                    ,auto:true
                    ,data:{
                        id:function(){
                            return "<?=$data['id']?>";
                        }
                    }
                    ,done: function(res){
                        //上传完毕回调
                        if(res.success){
                            _this.backImg=res.floor_img;
                        }else{
                            layer.msg("上传失败："+res.message, {icon: 5});
                        }
                    }
                });
            });
            //拖拽事件
            $(document).bind('dragstop','.view1-floor', function(event, ui){
                var id=$(event.target).data('id');
                var f_top=(ui.position.top/450*100).toFixed(2);
                var f_left=(ui.position.left/500*100).toFixed(2);
                floorRecord[id]={f_top:f_top,f_left:f_left};
            });
        })
        },
        methods:{
            onSave(){
                var tempfloorRecord=JSON.stringify(floorRecord);
                if(tempfloorRecord=="{}"){
                    layer.msg("未作任何修改！");
                    return false;
                }else{
                    ajax("/xiamenyyhoutai/xcxbuilding/floor_setcoordinates",{parame:tempfloorRecord},function (res) {
                        if(res.success){
                            floorRecord={};
                            layer.msg("保存成功！");
                        }else{
                            layer.msg("保存失败："+res.message,{icon: 5});
                        }
                    });
                }
            }
        }
    });
    function initFloorList(){
        ajax("/xiamenyyhoutai/xcxbuilding/floor_init",{id:<?=$data['id']?>},function (data) {
            if(data.success){
                vm.floorList=data['data'];
                Vue.nextTick(function () {
                    $( ".view1-floor" ).draggable({ containment: "#my-img-div", scroll: false });
                });
            }
        })
    }
    function getPageData() {
        var curr = arguments[0] ? arguments[0] : 1;
        var limit = arguments[1] ? arguments[1] : PAGELIMIT;
        ajax("/xiamenyyhoutai/xcxbuilding/floor_page",{id:<?=$data['id']?>,curr:curr,limit:limit},function (data) {
            if(data.success){
                $("#tbody").empty();
                for(var i in data['data']){
                    var id = data['data'][i]['id'];
                    var status='';
                    if(data['data'][i]['status']==1){
                        status='<input type="checkbox" name="status" lay-text="开启|禁用" lay-filter="status" lay-skin="switch" value="'+id+'" checked>';
                    }else{
                        status='<input type="checkbox" name="status" lay-text="开启|禁用" lay-filter="status" lay-skin="switch" value="'+id+'">';
                    }
                    var $content = '<tr>' +
                        '<td>'+parseInt((curr-1)*PAGELIMIT+parseInt(i)+1)+'</td>' +
                        '<td>'+data['data'][i]['title']+'</td>' +
                        '<td>'+data['data'][i]['sales_status']+'</td>' +
                        '<td>'+data['data'][i]['floor_number']+'</td>' +
                        '<td>'+data['data'][i]['house_number']+'</td>' +
                        '<td>'+data['data'][i]['year_number']+'</td>' +
                        '<td>'+data['data'][i]['kaipan_time']+'</td>' +
                        '<td>'+data['data'][i]['jiaofan_time']+'</td>' +
                        '<td>'+status+'</td>' +
                        '<td>'+data['data'][i]['create_time']+'</td>' +
                        '<td class="td-manage">'+
                        '<button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="onUnitInfo('+'\''+data['data'][i]['title']+'\''+','+id+')" ><i class="layui-icon">&#xe705;</i>单元信息</button>'+
                        '<button type="button" class="layui-btn layui-btn-xs" onclick="click_edit(\'编辑\',\'/xiamenyyhoutai/xcxbuilding/floor_edit\','+id+',\'680\',\'450\')" ><i class="layui-icon">&#xe642;</i>编辑</button>'+
                        '<button type="button" class="layui-btn-danger layui-btn layui-btn-xs" onclick="click_del('+id+')"><i class="layui-icon">&#xe640;</i>删除</button>'+
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
    function click_del(id){
        layer.confirm('确认要删除吗？',function(){
            ajax("/xiamenyyhoutai/xcxbuilding/floor_del",{id:id},function(res){
                if(res.success){
                    layer.msg('已删除!',{icon:6,time:300},function(){
                        initFloorList();
                        getPageData($('#my_body').data('curr'));
                    });
                }else{
                    layer.msg(res.message,{icon: 5});
                }
            });
        });
    }
    function onUnitInfo(title,id){
        parent.addCardItem(title,"/xiamenyyhoutai/xcxbuilding/unit_index?id="+id,id);
    }
</script>