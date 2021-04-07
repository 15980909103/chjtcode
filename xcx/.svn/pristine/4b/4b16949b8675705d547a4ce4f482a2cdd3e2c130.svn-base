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
    <style>
        .layui-input{line-height: 38px;}.myimg{margin-left: 10px;}
        .my-list-li{width: 250px;height:400px;overflow-y: auto;border: 1px solid #DDD;margin-bottom: 10px;padding: 10px;}
        .my-label,.layui-form-pane .layui-form-radio{display:block;}
        .my-div-content{display: flex;justify-content:space-between;align-items:center;}
    </style>
</head>
<body>
<div id="app" class="x-body">
    <form class="layui-form layui-form-pane" action="javascript:void(0);">
        <div class="layui-form-item">
            <label class="layui-form-label" style="width:120px;">选择所有楼盘</label>
            <div class="layui-input-block">
                <?php if($data['is_all']=='1'){?>
                    <input type="checkbox" name="is_all" lay-filter="is_all" lay-skin="switch" lay-text="ON|OFF" checked>
                <?php }else{?>
                    <input type="checkbox" name="is_all" lay-filter="is_all" lay-skin="switch" lay-text="ON|OFF">
                <?php }?>
            </div>
        </div>
        <div class="layui-form-item" lay-filter="my-radio" v-show="!is_all" style="display: none;">
            <label class="layui-form-label">
                <span class="x-red">*</span>楼盘名称
            </label>
            <div class="layui-input-inline">
                <input type="text" v-model="buildingText" autocomplete="off" maxlength="250" class="layui-input">
            </div>
            <button type="button" class="layui-btn layui-btn-radius layui-btn-normal" @click="onSearch()">搜索</button>
        </div>
        <div class="my-div-content layui-form" lay-filter="my-radio" v-show="!is_all" style="display: none;">
            <div class="my-list-li">
                <template v-for="(item,index) in list">
                    <label  class="my-label"><input type="radio" name="buildingname" :data-index="index" :value="item.id" lay-ignore>{{item.name}}</label>
                </template>
            </div>
            <div style="width: 60px;">
                <button class="layui-btn" type="button" @click="onGoRight()">=></button>
                <button class="layui-btn" type="button" style="margin-top: 15px;margin-left: 0;" @click="onGoLeft()"><=</button>
            </div>
            <div class="my-list-li">
                <template v-for="(item,index) in resList">
                    <label class="my-label"><input type="radio" name="resname" :data-index="index" :value="item.id" lay-ignore>{{item.name}}</label>
                </template>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width:170px;">允许修改的报备状态</label>
            <div class="layui-input-block">
                <input type="checkbox" name="reported_status" lay-filter="rs" value="0" title="审核" <?=strpos($data['reported_status'],'0')!==false?'checked':''?>>
				<input type="checkbox" name="reported_status" lay-filter="rs" value="2" title="成交" <?=strpos($data['reported_status'],'2')!==false?'checked':''?>>
                <input type="checkbox" name="reported_status" lay-filter="rs" value="3" title="确认业绩" <?=strpos($data['reported_status'],'3')!==false?'checked':''?>>
                <input type="checkbox" name="reported_status" lay-filter="rs" value="4" title="开票" <?=strpos($data['reported_status'],'4')!==false?'checked':''?>>
                <input type="checkbox" name="reported_status" lay-filter="rs" value="5" title="结佣" <?=strpos($data['reported_status'],'5')!==false?'checked':''?>>
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
    var _id=<?=$data['id'];?>;
    var tempIsAll=<?=$data['is_all']?>;
    vm=new Vue({
        el: '#app',
        data: {
            is_all:tempIsAll=='1'?true:false,   //选择所有楼盘
            buildingText:"",
            list:[],
            resList:JSON.parse('<?=$data["resList"]?>')
        },
        mounted: function () {
            this.$nextTick(function () {
                var _this=this;
                layui.use(['form'], function(){
                    form = layui.form;
                    form.on('switch(is_all)', function(data){
                        _this.is_all=data.elem.checked;
                    });
                    form.on('submit(btn)', function(data){
                        //获取报备状态
                        var reported_status="";
                        $('input[name="reported_status"]:checked').each(function(){
                            reported_status+=$(this).val()+',';
                        });
                        //获取报备楼盘
                        var building_ids="";
                        var resList=_this.resList;
                        if(_this.is_all){
                            building_ids='0';
                        }else{
                            for(var i in resList){
                                building_ids+=resList[i].id+',';
                            }
                        }
                        layer.confirm('确认要修改吗？',function(){
                            ajax("/xiamenyyhoutai/admin/reported_manage_doedit",{id:_id,building_ids:building_ids,reported_status:reported_status},function(res){
                                if(res.success){
                                    parent.layer.alert("保存成功", {icon: 6},function (index2) {
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
        },
        methods:{
            onGoRight(){
                var list=this.list;
                var resList=this.resList;
                var index=$("input:radio[name='buildingname']:checked").data('index');
                if(index!=undefined){
                    this.$set(this.resList,resList.length,list[index]);
                    this.list.splice(index,1);
                    this.$nextTick(function (){
                        form.render();
                    });
                }
            },
            onGoLeft(){
                var list=this.list;
                var resList=this.resList;
                var index=$("input:radio[name='resname']:checked").data('index');
                if(index!=undefined){
                    this.$set(this.list,list.length,resList[index]);
                    this.resList.splice(index,1);
                    this.$nextTick(function (){
                        form.render();
                    });
                }
            },
            onSearch(){
                var _this=this;
                if(_this.buildingText==""){
                    layer.msg('请输入搜索内容！');
                    return false;
                }
                ajax("/xiamenyyhoutai/xcxstore/searchBuilding",{name:_this.buildingText},function(res){
                    if(res.success){
                        _this.list=res.data;
                        _this.$nextTick(function (){
                            form.render();
                        });
                    }else{
                        _this.list=[];
                    }
                });
            }
        }
    });
</script>