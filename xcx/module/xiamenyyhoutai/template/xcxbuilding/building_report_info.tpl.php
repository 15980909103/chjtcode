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
    <style>
        .layui-input{line-height: 38px;}.myimg{margin-left: 10px;}

        .my-div3-list{width:90vw;margin: 0 5vw;margin-top: 2vh;display:flex;}
        .div3-list-1{width: 20vw;display: flex;flex-direction:column;align-items:center;color: #333333;}
        .div3-list-2{width: 70vw;border-left:1px solid #E8E8E8;position: relative;}
        .div3-list-2::before{content:"\20";width: 2vw;height: 2vw;border:.5vw solid #EBEBEB;background-color:#FFF;border-radius: 50%;position: absolute;top:0;left: -1.5vw;}
        .div3-list-1 div:nth-child(1){font-size:6vw;font-weight: 600;}
        .div3-list-1 div:nth-child(2){font-size:3.8vw;margin-top: .8vh;}
        .div3-list2-title{padding-left: 5vw;color: #222;font-size:4.5vw;}
        .div3-list2-content{font-size: 4.5vw;padding:2vh 0;margin-left:5vw;color:#555555;border-bottom: 1px solid #F7F7F7;}
        .my-type1-1{font-size: 4vw;color: #A3A3A3;}
        .my-type1-2{width: 65vw;margin-top: 2vw;display: flex;justify-content:space-between;}
        .type1-2-img{width: 20vw;height: 16vw;border-radius:1.5vw;}
        .type1-2-content{width:43vw;display: flex;flex-direction:column;justify-content:space-between;}
        .type1-2-1{font-size: 4.5vw;font-weight: 600;}
        .type1-2-2{font-size: 3.5vw;color: #9F9F9F;}
        .type1-2-3{font-size: 3.5vw;color: #9F9F9F;}
        .type1-2-3 span{color: #5970EA;}
        .my-base-text{font-size: 4vw;}
        .my-reported-list{width:95vw;display: flex;flex-direction:column-reverse;}
        /*选中效果*/
        .div3-tick .div3-list-1,.div3-tick .div3-list2-title{color: #D12626;}
        .div3-tick .div3-list-2::before{border:.5vw solid #D12626;}
        .div3-tick .div3-list-2{border-left:1px solid #FBE3E3;}
    </style>

</head>
<body>
<div class="my-reported-list">
    <?php foreach($reportedData as $key=>$val){ ?>
    <div class="my-div3-list <?=$key==count($reportedData)-1?'div3-tick':''?>">
        <div class="div3-list-1">
            <div><?=$val['day'] ?></div>
            <div><?=$val['year_month'] ?></div>
        </div>
        <div class="div3-list-2">
            <div class="div3-list2-title"><?=$val['status_type'] ?></div>
            <div class="div3-list2-content">
                <?php if($key==0){ ?>
                    <div class="my-base-text"><?=$val['content'] ?></div>
                <?php }else{ ?>
                    <div class="my-base-text"><?=$val['content'] ?></div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
</body>
</html>
