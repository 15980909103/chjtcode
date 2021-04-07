<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <title>提示</title>
    <style>
        body{
            margin: 0;
            padding: 0;
        }
        .my-message_box{
            position: relative;
            width: 100%;
            height:100vh;
        }
        .my-message{
            position: absolute;
            top:40%;
            left:0;
            width: 100%;
            transform: translate(0,-50%);
            text-align: center;
        }
    </style>
</head>
<body>
<div class="my-message_box">
    <div class="my-message"><?=$message?></div>
</div>

</body>
</html>