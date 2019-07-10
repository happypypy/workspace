
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>错误信息</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta content="telephone=no" name="format-detection">
</head>
<body id="wrapper" style="background: #e7e7e7;" >
	<!--site section start-->
	<div id="container">
		<?php
            $msg=empty($_GET["msg"])? "没找到相关内容，有疑问请和管理联系！":$_GET["msg"];
            $url=empty($_GET["url"])? "window.history.go(-1);":"window.location.href='".$_GET["url"]."';"
		?>
		<div style="padding: 10px">
			<div style=" padding-top:  calc(100vh/2 - 200px);font-size: 1rem; text-align: center;color: #808080">
				<div style="padding: 1rem"><?php echo $msg ?></div>
				<div><a href="javascript:;" onclick="javascript:<?php echo $url; ?>">返回</div>
			</div>
		</div>
	</div>
</body>
</html>



