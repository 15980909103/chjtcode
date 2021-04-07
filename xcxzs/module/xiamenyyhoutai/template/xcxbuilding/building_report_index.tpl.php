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
    <style>.layui-input{line-height: 38px;}.my-img{width: 50px;height: 50px;}.my-detail-img{width: 100px;height:50px;}</style>
</head>
<body>
<div class="x-nav">
    <span class="layui-breadcrumb" lay-separator=">"></span>
    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
</div>
<div id="my_body" class="x-body">
    <form class="layui-form x-center" style="width:100%" action="javascript:void(0);">
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>带看客户
            </label>
            <div class="layui-input-inline">
                <select id="user_name" lay-verify="required" lay-search>
                    <option value=""></option>
                    <?php foreach($username as $val){ ?>
                        <option value="<?=$val['user_name']?>"><?=$val['user_name']?></option>
                    <?php } ?>
                </select>
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>客户性别
            </label>
            <div class="layui-input-inline">
                <select id="user_gender" lay-verify="required" lay-search>
                    <option value=""></option>
                    <option value="1">男</option>
                    <option value="2">女</option>
                </select>
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>报备经纪人
            </label>
            <div class="layui-input-inline">
                <select id="agent_id" lay-verify="required" lay-search>
                    <option value=""></option>
                    <?php foreach($agen as $val){ ?>
                        <option value="<?=$val['id']?>"><?=$val['auname']?></option>
                    <?php } ?>
                </select>
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>报备楼盘
            </label>
            <div class="layui-input-inline">
                <select id="building_id" lay-verify="required" lay-search>
                    <option value=""></option>
                    <?php foreach($building as $val){ ?>
                        <option value="<?=$val['id']?>"><?=$val['bbname']?></option>
                    <?php } ?>
                </select>
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>报备状态
            </label>
            <div class="layui-input-inline">
                <select id="status_type" lay-verify="required" lay-search>
                    <option value=""></option>
                    <option value="1">报备</option>
                    <option value="2">带看</option>
                    <option value="3">成交</option>
                    <option value="4">确认业绩</option>
                    <option value="5">开票</option>
                    <option value="6">结佣</option>
                </select>
            </div>
            <label class="layui-form-label">
                <span class="x-red">*</span>评论读取状态
            </label>
            <div class="layui-input-inline">
                <select id="admin_is_read" lay-verify="required" lay-search>
                    <option value=""></option>
                    <option value="0">未读</option>
                    <option value="1">已读</option>
                </select>
            </div>
            <div class="layui-input-inline" style="width:80px">
                <button class="layui-btn" onClick="getPageData();"><i class="layui-icon">&#xe615;</i></button>
            </div>
        </div>
    </form>
    <table class="layui-table layui-form">
        <thead>
        <tr>
            <th>ID</th>
            <th>带看客户</th>
            <th>客户手机号</th>
            <th>客户性别</th>
            <th>报备经纪人</th>
            <th>楼盘名称</th>
            <th>楼盘封面</th>
            <th>预计带看时间</th>
            <th>报备状态</th>
            <th>描述</th>
            <th>创建时间</th>
            <th>更新时间</th>
            <th>报备备注</th>
        </tr>
        </thead>
        <tbody id="tbody"></tbody>
    </table>
    <div id="page"></div>
</div>
</body>
</html>
<script>
    layui.use(['form','element','laypage'], function(){
        form = layui.form;
        laypage = layui.laypage;
        getPageData();
    });
    function getPageData() {
        var curr          = arguments[0] ? arguments[0] : 1;
        var limit         = arguments[1] ? arguments[1] : PAGELIMIT;
        var user_name       = $("#user_name").val();
        var user_gender   = $('#user_gender').val();
        var agent_id      = $('#agent_id').val();
        var building_id   = $('#building_id').val();
        var status_type   = $('#status_type').val();
        var admin_is_read = $('#admin_is_read').val();
        ajax("/xiamenyyhoutai/xcxbuilding/building_report_page",{status_type:status_type,building_id:building_id,agent_id:agent_id,user_name:user_name,user_gender:user_gender,admin_is_read:admin_is_read,curr:curr,limit:limit},function (data) {
            if(data.success){
                var domain = 'http://'+window.location.host;
                $("#tbody").empty();
                for(var i in data['data']){
                    var myStatusBtn="";
                    if((data['data'][i]['status_type']>1 && data['data'][i]['status_type']<90) || data['data'][i]['status_type']<1){
                        var _statusTypeName="";
                        switch(data['data'][i]['status_type']){
                            case '0':
                                _statusTypeName='审核';
                                break;
														case '2':
                                _statusTypeName='成交';
                                break;
                            case '3':
                                _statusTypeName='确认业绩';
                                break;
                            case '4':
                                _statusTypeName='开票';
                                break;
                            case '5':
                                _statusTypeName='结佣';
                                break;
                        }
                        myStatusBtn='<button type="button" class="layui-btn layui-btn-danger layui-btn-xs" onclick="onSetStatusType('+data['data'][i]['status_type']+','+data['data'][i]['id']+')"><i class="layui-icon">&#xe66e;</i>'+_statusTypeName+'</button>';
                    }
                    if (data['data'][i]['status_type']==6 || data['data'][i]['status_type']==99){
                        myStatusBtn= '';
                    }
                    var $content = '<tr>' +
                        '<td>'+parseInt((curr-1)*PAGELIMIT+parseInt(i)+1)+'</td>' +
                        '<td>'+data['data'][i]['user_name']+'</td>'+
                        '<td>'+data['data'][i]['user_phone']+'</td>'+
                        '<td>'+data['data'][i]['user_gender']+'</td>'+
                        '<td>'+data['data'][i]['auname']+'</td>'+
                        '<td>'+data['data'][i]['bbname']+'</td>'+
                        '<td><img class="my-detail-img" src="'+data['data'][i]['pic']+'"/></td>'+
                        '<td>'+data['data'][i]['take_time']+'</td>'+
                        '<td>'+data['data'][i]['status_type_name']+'</td>'+
                        '<td>'+data['data'][i]['describe']+'</td>'+
                        '<td>'+data['data'][i]['create_time']+'</td>'+
                        '<td>'+data['data'][i]['update_time']+'</td>'+
                        '<td class="td-manage">'+
                        // myStatusBtn+
                        '<button type="button" class="layui-btn layui-btn layui-btn-xs" onclick="click_edit(\'查看\',\'/xiamenyyhoutai/xcxbuilding/building_report_info\','+data['data'][i]['id']+',\'350\',\'450\')" ><i class="layui-icon">&#xe642;</i>查看</button>'+
                        // '<button type="button" class="layui-btn layui-btn layui-btn-xs" onclick="click_edit(\'评论\',\'/xiamenyyhoutai/xcxbuilding/get_comment\','+data['data'][i]['id']+',\'650\',\'450\')" ><i class="layui-icon">&#xe642;</i>评论</button>'+
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
    /**
     * @param status_type   当前状态
     */
    function onSetStatusType(status_type,id){
        console.log('id'+id);
				if(status_type==0){
					layer.confirm('是否审核通过', {
						btn: ['审核通过','审核不通过'] //按钮
					}, function(){
						ajax("/xiamenyyhoutai/xcxbuilding/set_reported",{id:id,status_type:status_type,text:""},function (data) {
									if (data.success){
											getPageData($('#my_body').data('curr'));
											layer.msg("修改成功");
									}else {
											layer.msg(data.message);
									}
							})
					}, function(){
						ajax("/xiamenyyhoutai/xcxbuilding/set_reported",{id:id,status_type:status_type,text:"",flag:1},function (data) {
									if (data.success){
											getPageData($('#my_body').data('curr'));
											layer.msg("修改成功");
									}else {
											layer.msg(data.message);
									}
							})
					});
				}else{
					layer.prompt({title: '请输入报备信息', formType: 2,maxlength:200}, function(text, index){
							layer.close(index);
							console.log(text);
							ajax("/xiamenyyhoutai/xcxbuilding/set_reported",{id:id,status_type:status_type,text:text},function (data) {
									if (data.success){
											getPageData($('#my_body').data('curr'));
									}else {
											layer.msg(data.message);
									}
							})
					});
				}
    }
</script>