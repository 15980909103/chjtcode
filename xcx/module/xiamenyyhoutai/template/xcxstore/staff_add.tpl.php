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
    <script src="/public/js/admin/area.js" charset="utf-8"></script>
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
            <label class="layui-form-label">
                <span class="x-red">*</span>职位
            </label>
            <div class="layui-input-inline">
                <select name="types" id="type" lay-filter="types" lay-verify="required">
                        <option value="3">项目组长</option>
                        <option value="6">渠道组长</option>
                        <option value="7">项目负责人</option>
                        <option value="8">区域负责人</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>名称
            </label>
            <div class="layui-input-block">
                <input id="name" name="name" placeholder="" class="layui-input">
            </div>
        </div>
<!--        <div class="layui-form-item">-->
<!--            <label class="layui-form-label" style="width:120px;">选择所有公司</label>-->
<!--            <div class="layui-input-block">-->
<!--                <input type="checkbox" name="is_all" lay-filter="is_all" lay-skin="switch" lay-text="ON|OFF">-->
<!--            </div>-->
<!--        </div>-->
        <div class="layui-form-item" lay-filter="my-radio" v-show="!is_all" style="display: none;" id="groupname">
            <label class="layui-form-label">
                <span class="x-red">*</span>分组信息
            </label>
            <div class="layui-input-inline">
                <input type="text" v-model="buildingText" autocomplete="off" maxlength="250" class="layui-input">
            </div>
            <button type="button" class="layui-btn layui-btn-radius layui-btn-normal" @click="onSearch()">搜索</button>
        </div>
        <div class="my-div-content layui-form" lay-filter="my-radio" v-show="!is_all" style="display: none;" id="groupinfo">
            <div class="my-list-li">
                <template v-for="(item,index) in list">
                    <label  class="my-label"><input type="radio" name="buildingname" :data-index="index" :value="item.id" lay-ignore>{{item.title}}</label>
                </template>
            </div>
            <div style="width: 60px;">
                <button class="layui-btn" type="button" @click="onGoRight()">=></button>
                <button class="layui-btn" type="button" style="margin-top: 15px;margin-left: 0;" @click="onGoLeft()"><=</button>
            </div>
            <div class="my-list-li">
                <template v-for="(item,index) in resList">
                    <label class="my-label"><input type="radio" name="resname" :data-index="index" :value="item.id" lay-ignore>{{item.title}}</label>
                </template>
            </div>
        </div>
        <div style="display: none;" id="grouparea">
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span class="x-red">*</span>省份
                </label>
                <div class="layui-input-inline">
                    <select id="province" name="province" lay-filter="province"  <? if(1 == $data['disabled']) {?> <!--disabled="disabled"--> <? }?> >
                    <option value="">请选择省</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span class="x-red">*</span>城市
                </label>
                <div class="layui-input-inline">
                    <select id="city" name="city" lay-filter="city"  <? if(1 == $data['disabled']) {?> <!--disabled="disabled"--> <? }?>>
                    <option value="">请选择市</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span class="x-red">*</span>地区
                </label>
                <div class="layui-input-inline">
                    <select id="area" name="area" lay-filter="area" >
                        <option value="">请选择县/区</option>
                    </select>
                </div>
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
    var valueType = ''
    var areaData = Area;

    vm=new Vue({
        el: '#app',
        data: {
            is_all:false,   //选择所有楼盘
            buildingText:"",
            list:[],
            resList:[]
        },
        mounted: function () {
        this.$nextTick(function () {
            var _this=this;
            layui.use(['form'], function(){
                form = layui.form;
                loadProvince()
                form.on('switch(is_all)', function(data){
                    _this.is_all=data.elem.checked;
                });
                form.on('submit(btn)', function(data){
                    var mgids="";
                    var resList=_this.resList;
                    var type=$('#type').val();
                    var name=$('#name').val();
                    if(_this.is_all){
                        mgids='0';
                    }else{
                        for(var i in resList){
                            mgids+=resList[i].id+',';
                        }
                    }
                    var province=$('#province').find("option:selected").text();
                    var city=$('#city').find("option:selected").text();
                    var area=$('#area').find("option:selected").text();
                    layer.confirm('确认要添加吗？',function(){
                        ajax("/xiamenyyhoutai/xcxstore/staff_doadd",{mgids:mgids,type:type,name:name,province:province,city:city,area:area},function(res){
                            if(res.success){
                                parent.layer.alert("保存成功", {icon: 6},function (index2) {
                                    parent.getPageData(parent.$('#my_body').data('curr'));
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

                form.on('select(types)', function(data) {
                    var value = data.value;
                    valueType = data.value;
                    // console.log(value);
                    if(value == '7' || value == '8') {
                        $('#groupname').hide();
                        $('#groupinfo').hide();
                        if(value == '8') {
                            $('#grouparea').show();
                        }
                    } else {
                        $('#groupname').show();
                        $('#groupinfo').show();
                        $('#grouparea').hide();
                    }
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
                ajax("/xiamenyyhoutai/xcxstore/searchCompany",
                    {name:_this.buildingText,type:valueType},
                    function(res){
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
    function loadProvince() {
        var proHtml = '';
        for (var i = 0; i < areaData.length; i++) {
            if(areaData[i].provinceName=="<?=$data['province']?>") {
                proHtml += '<option value="' + areaData[i].provinceCode + '_' + areaData[i].mallCityList.length + '_' + i +'" selected>' + areaData[i].provinceName + '</option>';
                loadCity(areaData[i].mallCityList);
            } else {
                proHtml += '<option value="' + areaData[i].provinceCode + '_' + areaData[i].mallCityList.length + '_' + i +'">' + areaData[i].provinceName + '</option>';
            }
        }
        //初始化省数据
        $('form').find('select[name=province]').append(proHtml);
        form.render();
        form.on('select(province)', function(data) {
            $('form').find('select[name=area]').html('<option value="">请选择县/区</option>').parent().hide();
            var value = data.value;
            var d = value.split('_');
            var code = d[0];
            var count = d[1];
            var index = d[2];
            if (count > 0) {
                loadCity(areaData[index].mallCityList);
            } else {
                $('form').find('select[name=city]').parent().hide();
            }
        });
    }
    //加载市数据   '_' + citys[i].mallAreaList.length + '_' + i +
    function loadCity(citys) {
        var cityHtml = '';
        for (var i = 0; i < citys.length; i++) {
            if(citys[i].cityName=="<?=$data['city']?>") {
                cityHtml += '<option value="' + citys[i].cityCode + '_' + citys[i].mallAreaList.length + '_' + i +'" selected>' + citys[i].cityName + '</option>';
                loadArea(citys[i].mallAreaList);
            } else {
                cityHtml += '<option value="' + citys[i].cityCode + '_' + citys[i].mallAreaList.length + '_' + i +'">' + citys[i].cityName + '</option>';
            }
        }
        $('form').find('select[name=city]').html(cityHtml).parent().show();
        form.render();
        form.on('select(city)', function(data) {
            var value = data.value;
            var d = value.split('_');
            var code = d[0];
            var count = d[1];
            var index = d[2];
            if (count > 0) {
                loadArea(citys[index].mallAreaList);
            } else {
                $('form').find('select[name=area]').parent().hide();

            }
        });
    }
    //加载县/区数据
    function loadArea(areas) {
        var areaHtml = '';
        for (var i = 0; i < areas.length; i++) {
            areaHtml += '<option value="' + areas[i].areaCode + '">' + areas[i].areaName + '</option>';
        }
        $('form').find('select[name=area]').html(areaHtml).parent().show();
        form.render();
        form.on('select(area)', function(data) {
        });
    }
</script>