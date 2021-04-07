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
    <link rel="stylesheet" href="/public/css/aui.css">
    <script src="/public/js/jquery-2.0.0.min.js" charset="utf-8"></script>
    <script src="/public/js/layui2/layui.js" charset="utf-8"></script>
    <script src="/public/js/admin/x-layui.js" charset="utf-8"></script>
    <script src="/public/js/admin/public.js" charset="utf-8"></script>
    <script src="/public/js/vue.min.js" charset="utf-8"></script>
    <style>
        .my-content{width: 100vw;min-height: 100vh;background-color: #f5f5f5;}
        .my-chat-content{width: 100vw;}
        .aui-chat{overflow-x: hidden !important;}
        .aui-chat .aui-chat-left .aui-chat-media,.aui-chat .aui-chat-right .aui-chat-media{height: 2rem;}
        .aui-chat .aui-chat-media img{height: 100%;}
        .aui-chat .aui-chat-status{color:#cdcdcd;}
        .my-both{clear: both;width: 100vw;height: 8vh;}
        .my-footer{width: 100vw;height: 8vh;padding: 1.5vh 5vw;background-color: #FFF;position: fixed;bottom: 0;display: flex;justify-content:space-between;}
        .footer-input{
            border: 0 !important;
            height: 1.5rem !important;
            /*height:5vh !important;*/
            width: 70vw !important;
            margin-bottom: 0 !important;
            background-color: #F7F7F7 !important;
        }
        .aui-btn-info {
            color: #ffffff !important;
            background-color: #03a9f4 !important;
        }
        button, .aui-btn {
            position: relative;
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 400;
            font-family: inherit;
            text-decoration: none;
            text-align: center;
            margin: 0;
            background: #dddddd;
            padding: 0 0.6rem;
            height: 1.5rem;
            line-height: 1.5rem;
            border-radius: 0.2rem;
            white-space: nowrap;
            text-overflow: ellipsis;
            vertical-align: middle;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            -webkit-user-select: none;
            user-select: none;
        }
        .my-footer{
            height: auto;
            /*box-sizing: content-box;*/
        }
        .my-footer input{
            line-height: 21px;
            width: 100%;
            /*height: 40px;*/
            margin-bottom: 15px;
            padding: 10px 15px;
            -webkit-user-select: text;
            border: 1px solid rgba(0,0,0,.2);
            border-radius: 3px;
            outline: 0;
            background-color: #fff;
            -webkit-appearance: none;
        }
    </style>
</head>
<body>
<div id="app" class="my-content">
    <div class="my-chat-content">
        <section class="aui-chat">
            <?php foreach ($list as $val){ ?>
                <?php if ($val['send_from']==1){ ?>
            <div class="aui-chat-header"><?=$val['create_time'] ?></div>
            <div class="aui-chat-item aui-chat-left">
                <div class="aui-chat-media">
                    <img src="<?=$val['auheadimgurl'] ?>"/>
                </div>
                <div class="aui-chat-inner">
                    <div class="aui-chat-name"><?=$val['nickname'] ?></div>
                    <div class="aui-chat-content">
                        <div class="aui-chat-arrow"></div>
                        <?=$val['content'] ?>
                    </div>
                </div>
            </div>
            <?php }
                if ($val['send_from']==2){
            ?>
            <div class="aui-chat-header"><?=$val['create_time'] ?></div>
            <div class="aui-chat-item aui-chat-right">
                <div class="aui-chat-media">
                    <img src="<?=$val['auheadimgurl'] ?>"/>
                </div>
                <div class="aui-chat-inner">
                    <div class="aui-chat-name"><?=$val['name'] ?></div>
                    <div class="aui-chat-content">
                        <div class="aui-chat-arrow"></div>
                        <?=$val['content'] ?>
                    </div>
                </div>
            </div>
                    <?php } ?>
            <?php } ?>
            <div class="my-both"></div>
        </section>
    </div>
    <div class="my-footer">
        <input id="reported_id" type="hidden" value="<?=$reported_id ?>">
        <input id="comment_content" class="footer-input" type="text" placeholder="请输入..."/>
        <div id="comment_submit" class="aui-btn aui-btn-info">发送</div>
    </div>
</div>
</body>
</html>
<script>
    $('html,body').animate({scrollTop:$('.my-both').offset().top},800);
    $('#comment_submit').click(function () {
        var reported_id = $("#reported_id").val();
        var comment_content = $("#comment_content").val();
        if ($.trim(comment_content)==''){
            return;
        }
        ajax("/xiamenyyhoutai/xcxbuilding/admin_submit_comment",{reported_id:reported_id,comment_content:comment_content},function (data) {
            if (data.success){
                var _thml="<div class='aui-chat-header'>"+data.data[0]['create_time']+"</div>" +
                    "            <div class='aui-chat-item aui-chat-right'>" +
                    "                <div class='aui-chat-media'>" +
                    "                    <img src='"+data.data[0]['auheadimgurl']+"'/>" +
                    "                </div>" +
                    "                <div class='aui-chat-inner'>" +
                    "                    <div class='aui-chat-name'>"+data.data[0]['name']+"</div>" +
                    "                    <div class='aui-chat-content'>" +
                    "                        <div class='aui-chat-arrow'></div>" +
                    "                        "+data.data[0]['content']+
                    "                    </div>" +
                    "                </div>" +
                    "            </div>";
                $('.my-both').before(_thml);
                $('html,body').animate({scrollTop:$('.my-both').offset().top},800);
                $("#comment_content").val('');
            }
        });
    });
</script>
