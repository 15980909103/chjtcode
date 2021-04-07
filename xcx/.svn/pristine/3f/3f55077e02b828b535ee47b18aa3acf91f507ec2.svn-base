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
    <script src="/public/js/admin/formSelects.js" charset="utf-8"></script>
    <script src="/public/js/UEditor/ueditor.config.js" charset="utf-8"></script>
    <script src="/public/js/UEditor/ueditor.all.js" charset="utf-8"></script>
    <script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key=7VABZ-GKERX-R5K4U-ZNGQ6-6Z5B7-BZFC7"></script>
    <style>.layui-input{line-height: 38px;}.myimg{margin-left: 10px;}#container {width:400px;height:300px;margin: 20px 0;}</style>
</head>
<body>
<div class="x-body">
    <form class="layui-form layui-form-pane">
        <input type="hidden" name="id" value="<?=$data['id'];?>">


        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px;">
                <span class="x-red">*</span>带看提前预约/分钟
            </label>
            <div class="layui-input-inline">
                <input type="text" name="early_hours" lay-verify="required" placeholder="提前几小时预约带看" autocomplete="off" maxlength="10" class="layui-input" value="<?=$data['early_hours']?>">
            </div>
            <label class="layui-form-label" style="width: 190px;">
                <span class="x-red">*</span>报备的保护时间/分钟
            </label>
            <div class="layui-input-inline">
                <input type="text" name="protect_set[status1_hours]" lay-verify="required" placeholder="报备的保护时间" autocomplete="off" maxlength="250" class="layui-input" value="<?=$data['protect_set']['status1_hours']?>">
            </div>
            <label class="layui-form-label" style="width: 190px;">
                <span class="x-red">*</span>带看的保护时间/小时
            </label>
            <div class="layui-input-inline">
                <input type="text" name="protect_set[status2_hours]" lay-verify="required" placeholder="带看的保护时间" autocomplete="off" maxlength="250" class="layui-input" value="<?=$data['protect_set']['status2_hours']?>">
            </div>
            <label class="layui-form-label" style="width: 190px;">
                <span class="x-red">*</span>成交的保护时间/小时
            </label>
            <div class="layui-input-inline">
                <input type="text" name="protect_set[status3_hours]" lay-verify="required" placeholder="成交的保护时间" autocomplete="off" maxlength="250" class="layui-input" value="<?=$data['protect_set']['status3_hours']?>">
            </div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>在线确客
            </label>
            <div class="layui-input-block">
                <textarea name="online_rules" placeholder="请输入内容" class="layui-textarea"><?=$data['online_rules']?></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>佣金规则
            </label>
            <div class="layui-input-block">
                <textarea name="commission_rules" placeholder="请输入内容" class="layui-textarea"><?=$data['commission_rules']?></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>报备规则
            </label>
            <div class="layui-input-block">
                <textarea name="report_rules" placeholder="请输入内容" class="layui-textarea"><?=$data['report_rules']?></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>带看规则
            </label>
            <div class="layui-input-block">
                <textarea name="look_rules" placeholder="请输入内容" class="layui-textarea"><?=$data['look_rules']?></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>结佣规则
            </label>
            <div class="layui-input-block">
                <textarea name="servant_rules" placeholder="请输入内容" class="layui-textarea"><?=$data['servant_rules']?></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red"></span>目标客户
            </label>
            <div class="layui-input-block">
                <textarea name="target_rules" placeholder="请输入内容" class="layui-textarea"><?=$data['target_rules']?></textarea>
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
    var ue = UE.getEditor('supporting_information',{
        initialFrameHeight:190,
        toolbars: [[
            'fullscreen', 'source', '|', 'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
            'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload','insertcode', 'webapp', 'pagebreak', 'template', 'background', '|',
            'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
            'print', 'preview', 'searchreplace', 'drafts', 'help'
        ]]
    });
    ue.ready(function() {
        ue.setContent('<?=$data["supporting_information"]?>');
    });
    var fileIndex="";
    var myField="";

    layui.use(['form','laydate','element','laypage'], function(){
        form = layui.form;

        var laydate = layui.laydate;
        /*========================================= 基本信息 ==========================================*/

        form.on('submit(btn)', function(data){
            //获取楼盘标记
            var _formSe=formSelects.arr;
            var _flag="";
            for(var i in _formSe){
                _flag+=","+_formSe[i].val;
            }

            var tempField=$.extend({},data.field,{flag:_flag});
            myField=JSON.stringify(tempField);
            if(fileIndex==""){
                var parameter=$.extend({},tempField,{type:'empty'});
                ajax("/xiamenyyhoutai/xcxbuilding/report_doedit",parameter,function(res){
                    if(res.success){
                        parent.parent.layer.alert("保存成功", {icon: 6},function (index2) {
                            parent.parent.getPageData(parent.parent.$('#my_body').data('curr'));
                            parent.parent.layer.close(index2);
//                            var index = parent.parent.layer.getFrameIndex(parent.window.name);
//                            parent.parent.layer.close(index);
                        });
                    }else{
                        layer.msg(res.message,{icon: 5});
                    }
                });
            }else{
                logoUpload.upload();
            }
        });
        var myFlag="<?=$data['flag']?>";
        myFlag=myFlag.split(",");
        formSelects.arr=[];
        if(myFlag.length>0){
            for(var i in myFlag){
                if(myFlag[i]!=""){
                    formSelects.arr.push({name:myFlag[i],val:myFlag[i]});
                }
            }
        }
        formSelects.on({
            layFilter: 'flag',	//绑定select lay-filter
            left: '【',			//显示的符号left
            right: '】',			//显示的符号right
            separator: ''		//多选分隔符
        });
    });

</script>