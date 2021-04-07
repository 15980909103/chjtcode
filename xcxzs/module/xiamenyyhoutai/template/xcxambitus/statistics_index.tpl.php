<!DOCTYPE html>
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
    <style>.layui-input{line-height: 38px;}.my-img{width: 50px;height: 50px;}.my-detail-img{width: 100px;height:50px;}}</style>
</head>
<body>
<div class="x-nav">
    <span class="layui-breadcrumb" lay-separator=">"></span>
    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i></a>
</div>
<div id="pie1" style="height:400px;width:50%;float:left"></div>
<div id="line1" style="height:400px;width:50%;float:right"></div>
<script src="/public/js/echarts.min.js"></script>
<script type="text/javascript">
    layui.use([],function(){
        get_statsx();
        ge_day_statsx();
    });
    var yearMonthDay;//前七天日期
    var userDayCount;//一天注册客户数
    var agentDayCount;//一天注册经纪人数
    var buildingReportedDayCount;//一天楼盘带看数
    var browsingBuildingDayCount;//一天楼盘浏览数
    var browsingEssayDayCount;//一天文章浏览数
    var shareVisitingCardDayCount;//一天 经纪人名片分享数
    var shareArticleDayCount;//一天 经纪人文章分享数
    var shareBuildingDayCount; //一天 经纪人楼盘分享数
    function ge_day_statsx() {
        ajax("/xiamenyyhoutai/xcxambitus/get_day_statsx",{},function (data) {
            if (data.success){
                yearMonthDay             = data['date'];
                userDayCount             = data['userDayCount'];
                agentDayCount            = data['agentDayCount'];
                buildingReportedDayCount = data['buildingReportedDayCount'];
                browsingBuildingDayCount = data['browsingBuildingDayCount'];
                browsingEssayDayCount    = data['browsingEssayDayCount'];
                shareVisitingCardDayCount= data['shareVisitingCardDayCount'];
                shareArticleDayCount     = data['shareArticleDayCount'];
                shareBuildingDayCount    = data['shareBuildingDayCount'];
            }
        })
    }
    var myChart_line = echarts.init(document.getElementById('line1'));
    //折现图
     option_line1 = {
         title: {
             text: "近七天数据走势",
             x: "center",
             y: "35px"
         },
         tooltip: {
             trigger: "item",
             formatter: "{a} <br/>{b} : {c}"
         },
         legend: {
             x: 'left',
             data: ['注册客户数','注册经纪人','楼盘带看数','楼盘浏览数','文章浏览数','经纪人名片分享数',
                    '经纪人文章分享数','经纪人楼盘分享数']
         },
         xAxis: [
             {
                 type: "category",
                 name: "x",
                 splitLine: {show: false},
                 data: [yearMonthDay[-7],yearMonthDay[-6],yearMonthDay[-5],yearMonthDay[-4],yearMonthDay[-3],yearMonthDay[-2],yearMonthDay[-1]]
             }
         ],
         yAxis: [
             {
                 type: "log",
                 name: "y"
             }
         ],
         toolbox: {
             show: true,
             feature: {
                 mark: {
                     show: true
                 },
                 dataView: {
                     show: true,
                     readOnly: true
                 },
                 restore: {
                     show: true
                 },
                 saveAsImage: {
                     show: true
                 }
             }
         },
         calculable: true,
         series: [
             {
                 name: "注册客户数",
                 type: "line",
                 data: [userDayCount[-7],userDayCount[-6],userDayCount[-5],userDayCount[-4],
                     userDayCount[-3],userDayCount[-2],userDayCount[-1]]
             },
             {
                 name: "注册经纪人",
                 type: "line",
                 data: [agentDayCount[-7],agentDayCount[-6],agentDayCount[-5],agentDayCount[-4],
                     agentDayCount[-3],agentDayCount[-2],agentDayCount[-1]]
             },
             {
                 name: "楼盘带看数",
                 type: "line",
                 data: [buildingReportedDayCount[-7],buildingReportedDayCount[-6],buildingReportedDayCount[-5],
                     buildingReportedDayCount[-4],buildingReportedDayCount[-3],buildingReportedDayCount[-2],
                     buildingReportedDayCount[-1]]
             },
             {
                 name: "楼盘浏览数",
                 type: "line",
                 data: [browsingBuildingDayCount[-7],browsingBuildingDayCount[-6],browsingBuildingDayCount[-5],
                     browsingBuildingDayCount[-4],browsingBuildingDayCount[-3],browsingBuildingDayCount[-2],
                     browsingBuildingDayCount[-1]]

             },
             {
                 name: "文章浏览数",
                 type: "line",
                 data: [browsingEssayDayCount[-7],browsingEssayDayCount[-6],browsingEssayDayCount[-5],
                     browsingEssayDayCount[-4],browsingEssayDayCount[-3],browsingEssayDayCount[-2],
                     browsingEssayDayCount[-1]]

             },
             {
                 name: "经纪人名片分享数",
                 type: "line",
                 data: [shareVisitingCardDayCount[-7],shareVisitingCardDayCount[-6],shareVisitingCardDayCount[-5],
                     shareVisitingCardDayCount[-4],shareVisitingCardDayCount[-3],shareVisitingCardDayCount[-2],
                     shareVisitingCardDayCount[-1]]

             },
             {
                 name: "经纪人文章分享数",
                 type: "line",
                 data: [shareArticleDayCount[-7],shareArticleDayCount[-6],shareArticleDayCount[-5],
                     shareArticleDayCount[-4],shareArticleDayCount[-3],shareArticleDayCount[-2],
                     shareArticleDayCount[-1]]

             },
             {
                 name: "经纪人楼盘分享数",
                 type: "line",
                 data: [shareBuildingDayCount[-7],shareBuildingDayCount[-6],shareBuildingDayCount[-5],
                     shareBuildingDayCount[-4],shareBuildingDayCount[-3],shareBuildingDayCount[-2],
                     shareBuildingDayCount[-1]]

             }
         ]
     };
     // 为echarts对象加载数据
     myChart_line.setOption(option_line1);

    var userCount;//总客户数
    var agentCount; //总经纪人数
    var buildingCount;//总楼盘数
    var buildingReportedCount;//总楼盘带看数
    var browsingBuildingCount;//楼盘浏览总数
    var browsingEssayCount;//文章浏览总数
    var shareVisitingCardCount;//经纪人名片分享总数
    var shareArticleCount;//经纪人文章分享总数
    var shareBuildingCount;//经纪人楼盘分享总数
    function get_statsx() {
        ajax("/xiamenyyhoutai/xcxambitus/get_statsx",{},function (data) {
            if (data.success){
                userCount              = data['data']['userCount'];
                agentCount             = data['data']['agentCount'];
                buildingCount          = data['data']['buildingCount'];
                buildingReportedCount  = data['data']['buildingReportedCount'];
                browsingBuildingCount  = data['data']['browsingBuildingCount'];
                browsingEssayCount     = data['data']['browsingEssayCount'];
                shareVisitingCardCount = data['data']['shareVisitingCardCount'];
                shareArticleCount      = data['data']['shareArticleCount'];
                shareBuildingCount     = data['data']['shareBuildingCount'];
            }
        });
    }
    var myChart_pie = echarts.init(document.getElementById('pie1'));

    //饼图
    option_pie1 = {
        title : {
            text: '各项数据总量',
            subtext: '请勿泄露',
            x:'center'
        },
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
            orient : 'vertical',
            x : 'left',
            data:['总客户数','总经纪人数','总楼盘数','总楼盘带看数','楼盘浏览总数','文章浏览总数','经纪人名片分享总数',
                  '经纪人文章分享总数','经纪人楼盘分享总数']
        },
        toolbox: {
            show : true,
            feature : {
                mark : {show: true},
                dataView : {show: true, readOnly: false},
                magicType : {
                    show: true,
                    type: ['pie', 'funnel'],
                    option: {
                        funnel: {
                            x: '25%',
                            width: '50%',
                            funnelAlign: 'left',
                            max: 1548
                        }
                    }
                },
                restore : {show: true},
                saveAsImage : {show: true}
            }
        },
        calculable : true,
        series : [
            {
                name:'总数',
                type:'pie',
                radius : '55%',
                center: ['50%', '60%'],
                data:[
                    {value:userCount, name:'总客户数'},
                    {value:agentCount, name:'总经纪人数'},
                    {value:buildingCount, name:'总楼盘数'},
                    {value:buildingReportedCount, name:'总楼盘带看数'},
                    {value:browsingBuildingCount, name:'楼盘浏览总数'},
                    {value:browsingEssayCount, name:'文章浏览总数'},
                    {value:shareVisitingCardCount, name:'经纪人名片分享总数'},
                    {value:shareArticleCount, name:'经纪人文章分享总数'},
                    {value:shareBuildingCount, name:'经纪人楼盘分享总数'}
                ]
            }
        ]
    };
    myChart_pie.setOption(option_pie1);
</script>
</body>
</html>