<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <script src="/public/js/jquery-2.0.0.min.js" charset="utf-8"></script>
    <script src="/public/js/layui/layui.js" charset="utf-8"></script>
    <script src="/public/js/admin/x-layui.js" charset="utf-8"></script>
    <script src="/public/js/admin/public.js" charset="utf-8"></script>
</head>
<body>
    <div>nihasoa<?=$aaa?></div>
</body>
</html>
<script>
    $(function(){
        ajax("/xiamenyyhoutai/test/db",{aa:'你好'},function(res){
            console.log(res);
        })
    })
</script>