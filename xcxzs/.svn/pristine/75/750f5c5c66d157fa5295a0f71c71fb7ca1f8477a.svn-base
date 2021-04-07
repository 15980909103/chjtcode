<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>
	   九房网
	</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/public/css/admin/x-admin.css?t=<?php echo JsVer;?>" media="all">
	<script type="text/javascript" src="/public/js/jquery-2.0.0.min.js"></script>
</head>
<body>
	<div class="x-body">
		<blockquote class="layui-elem-quote">
			欢迎使用九房网后台！
		</blockquote>
		<p>上次登录IP：<?php echo $arr['ip'];?></p>
		<p>上次登录时间：<?php echo $arr['time'];?></p>
		<p>本地时间：<span id="localtime"><font color="#000000"></font></span></p>
		<table class="layui-table">
			<thead>
				<tr>
					<th colspan="2" scope="col">服务器信息</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>服务器域名 [ IP ] </td>
					<td><?php echo $arr['domain_ip'];?></td>
				</tr>
				<tr>
					<td>服务器系统类型及版本号</td>
					<td><?php echo $arr['system'];?></td>
				</tr>
				<tr>
					<td>剩余空间</td>
					<td><?php echo $arr['space'];?></td>
				</tr>
				<tr>
					<td>运行环境 </td>
					<td><?php echo $arr['environment'];?></td>
				</tr>
				<tr>
					<td>PHP运行方式</td>
					<td><?php echo $arr['operation_mode'];?></td>
				</tr>
				<tr>
					<td>服务器当前时间 </td>
					<td id="localhost_time"><?php echo $arr['gmdate_time'];?></td>
				</tr>
				<tr>
					<td>Web端口</td>
					<td><?php echo $arr['port'];?></td>
				</tr>
				<tr>
					<td>脚本最大执行时间</td>
					<td><?php echo $arr['max_ex_time'];?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="layui-footer footer footer-demo">
		<div class="layui-main">
			<p>感谢layui,百度Echarts,jquery</p>
			<p>
				<a>
					Copyright ©2018 x-admin v2.3 All Rights Reserved.
				</a>
			</p>
			<p>
				<a target="_blank">
					九房网
				</a>
			</p>
		</div>
	</div>
	<script type="text/javascript">
		function showLocale(objD){
			var str,colorhead,colorfoot;
			var yy = objD.getYear();
			if(yy<1900) yy = yy+1900;
			var MM = objD.getMonth()+1;
			if(MM<10) MM = '0' + MM;
			var dd = objD.getDate();
			if(dd<10) dd = '0' + dd;
			var hh = objD.getHours();
			if(hh<10) hh = '0' + hh;
			var mm = objD.getMinutes();
			if(mm<10) mm = '0' + mm;
			var ss = objD.getSeconds();
			if(ss<10) ss = '0' + ss;
			var ww = objD.getDay();
			if  ( ww==0 )  colorhead="<font color=\"#000000\">";
			if  ( ww > 0 && ww < 6 )  colorhead="<font color=\"#000000\">";
			if  ( ww==6 )  colorhead="<font color=\"#000000\">";
			if  (ww==0)  ww="星期日";
			if  (ww==1)  ww="星期一";
			if  (ww==2)  ww="星期二";
			if  (ww==3)  ww="星期三";
			if  (ww==4)  ww="星期四";
			if  (ww==5)  ww="星期五";
			if  (ww==6)  ww="星期六";
			colorfoot="</font>";
			str = colorhead + yy + "-" + MM + "-" + dd + " " + hh + ":" + mm + ":" + ss + "  " + ww + colorfoot;
			return(str);
		}

		function tick(){
			var localhost_time = new Date("<?php echo $arr['gmdate_time'];?>"),today = new Date(),text_time;
			var y = localhost_time.getFullYear();
			var m = localhost_time.getMonth()+1;//获取当前月份的日期
			var d = localhost_time.getDate();
			var h = localhost_time.getHours(); //获取系统时，
			var n = localhost_time.getMinutes(); //分
			var s = localhost_time.getSeconds(); //秒
			var str = "日一二三四五六".charAt(new Date().getDay());
			text_time = y+'-'+m+'-'+d+' '+h+':'+n+':'+s+' '+'星期'+str;
			document.getElementById("localhost_time").innerHTML = text_time;
			document.getElementById("localtime").innerHTML = showLocale(today);
			localhost_time = new Date(localhost_time.getTime()+1000);
			window.setTimeout("tick()", 1000);
		}
		tick();
	</script>
</body>
</html>