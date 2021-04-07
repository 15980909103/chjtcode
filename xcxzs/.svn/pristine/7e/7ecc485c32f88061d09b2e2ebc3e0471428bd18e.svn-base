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
    <link rel="stylesheet" href="/public/css/admin/x-admin2.css?t=<?php echo JsVer;?>" media="all">
    <style type="text/css">
        .layui-form-item{
            width: 100%;
        }
        .layui-form-label{
            width: 350px !important;
        }
        .layui-input-block{
            width: 60%;
            float: left;
            margin-left: 0!important;
        }
    </style>
</head>
<body>
<div class="x-nav">
    <span class="layui-breadcrumb"></span>
    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right"  href="javascript:location.replace(location.href);" title="刷新"><i class="layui-icon" style="line-height:30px">ဂ</i></a>
</div>
<div class="x-body">
    <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
        <ul class="layui-tab-title">
            <li class="layui-this">所有配置</li>
        </ul>
        <div class="layui-tab-content" >
            <div class="layui-tab-item layui-show">
                <form class="layui-form layui-form-pane" action="" id="vue">
                    <div class="layui-form-item" v-for="(item, key, index) in items">
                        <label class="layui-form-label">
                            <span class='x-red'>*</span>{{item.describe}}<span v-if="item.key == 'count'">={{count}}人</span>
                        </label>
                        <div class="layui-input-block">
                            <input type="text" :name="item.key" autocomplete="off" v-model="item.value" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <button class="layui-btn" lay-submit lay-filter="*">
                            保存
                        </button>
                    </div>
                </form>
                <div style="height:100px;"></div>
            </div>
        </div>
    </div>
</div>
<script src="/public/js/jquery-2.0.0.min.js" charset="utf-8"></script>
<script src="/public/js/layui2/layui.js" charset="utf-8"></script>
<script src="/public/js/admin/x-layui.js" charset="utf-8"></script>
<script src="/public/js/admin/public.js" charset="utf-8"></script>
<script src="/public/js/vue.min.js" charset="utf-8"></script>
<script>
    setNavList();
    layui.use(['element','layer','form'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer, lement = layui.element;//面包导航;
        var original = [];
        var vue = new Vue({
            el: '#vue',
            data: {
                items:{},
                count: 0
            },
            created: function() {
                var self = this;
                ajax('/xiamenyyhoutai/config/index',{isAjax: true},function(res){
                    if(res.success){
                        self.items = res.config;
                        self.count = res.count;
                        original = JSON.stringify(res.config);
                    }
                },function(){
                    layer.alert("数据请求失败!!!", {icon: 5});
                });
            },
            methods: {
                getArrDifference(arr1, arr2) {
                   var arr = [];
                   arr1.forEach (function(value1,index1){
                       arr2.forEach (function(value2,index2){
                           var data = {};
                           if(value1.id == value2.id){
                               if(value1.value != value2.value){
                                   data.id = value1.id;
                                   data.key = value1.key;
                                   data.value = value1.value;
                                   arr.push(data);
                               }
                           }
                       });
                    });
                    return arr;
                }
            }
        });

        form.on('submit(*)', function(data){
            original = JSON.parse(original);
            var config = vue.getArrDifference(vue.items,original);
            config = JSON.stringify(config);
            ajax('/xiamenyyhoutai/config/edit',{config: config},function(res){
                if(res.success){
                    layer.alert("保存成功!!!", {icon: 6});
                }
            },function(){
                layer.alert("数据请求失败!!!", {icon: 5});
            });
            return false;
        });
    });
</script>
</body>
</html>