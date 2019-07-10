<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:27:"template/M1/mine/index.html";i:1562579042;s:52:"D:\workspace\work\public\template\M4\lib\header.html";i:1561691701;s:53:"D:\workspace\work\public\template\M4\lib\footer0.html";i:1561691701;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>会员中心</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta content="telephone=no" name="format-detection">
	<!-- <link rel="shortcut icon" href="images/favicon.ico" />
	<link rel="apple-touch-icon-precomposed" href="images/favicon.png" /> -->
	<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/swiper.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/common.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/pc.css">

	<script type="text/javascript" src="<?php echo $roottpl; ?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $roottpl; ?>/js/swiper.min.js"></script>
	<script type="text/javascript" src="<?php echo $roottpl; ?>/js/common.js"></script>
</head>
<body id="wrapper">
<header>
	<a href="/<?php echo $sitecode; ?>" class="logo"><img src="<?php echo $cms->GetConfigVal('webset','weblogo',$idsite);; ?>"></a>
	<div class="btns">
		<a class="btn1" href="/<?php echo $sitecode; ?>/mine"></a>
		<a class="btn2" href="javascript:;" id="open-menu"></a>
		<a class="btn3" href="javascript:;" id="close-menu"></a>
	</div>
</header>
	<div class="menu" id="menu">
		<ul>
			<?php  $result=$cms->GetAllNodes(['showonmenu'=>1,'nodetype'=>['in','1,2'],'parentid'=>0],'idorder asc,nodeid asc','',20,$idsite);
				foreach($result as $k=>$v){
				$jumpUrl ="/". $sitecode.$v["url"];
				if(substr( $v["url"],0,4 ) == "http"){
				$jumpUrl = $v["url"];
				}
				?>
			<li><a href="<?php echo $jumpUrl; ?>"><?php echo $v['nodename']; ?></a></li>
			<?php }?>
		</ul>
	</div>
	<div class="menu-cover"></div>
<!--site section start-->
<div class="site-section clearfix" id="container"  style="background: #e7e7e7;">
	<div class="mine-head">
		<!-- <img src="style/images/mine_bg.jpg" class="mine-head-bg" /> -->
		<div class="mine-head-box">
			<img src="<?php echo empty($userinfo['userimg'])?'/static/images/userimg.jpg': (strtolower(substr($userinfo['userimg'],0,4))=='http'?$userinfo['userimg']:'/'.$userinfo['userimg']) ?>" />

		</div>
		<div class="mine-head-name"><?php echo $userinfo['nickname']; ?></div>
		<div class="scorenum-box"><a href="/<?php echo $sitecode; ?>/integralrecord" class="scorenum">总积分：<?php echo $userinfo['integral']; ?></a></div>
	</div>

	<a href="/<?php echo $sitecode; ?>/signuplist" class="mine-link mine-link-signup">
		<p>我的报名</p>
	</a>
	<!-- 分销 -->
	<!-- <a href="/<?php echo $sitecode; ?>/signuplist" class="mine-link mine-link-endorse"> -->
		<!-- <p>活动代言</p> -->
	<!-- </a> -->
	<?php if($isbuy): ?>
	<a href="/<?php echo $sitecode; ?>/integralrecord" class="mine-link mine-link-score">
		<p>我的积分</p>
	</a>
	<?php endif; if($is_cashed): ?>
	<a href="/<?php echo $sitecode; ?>/cashedlist/1" class="mine-link mine-link-coupon">
		<p>我的现金券</p>
	</a>
	<?php endif; ?>
	<a href="/<?php echo $sitecode; ?>/collection" class="mine-link mine-link-collection">
		<p>我的收藏</p>
	</a>
	<a href="/<?php echo $sitecode; ?>/comment" class="mine-link mine-link-comment">
		<p>我的评论</p>
	</a>
	<a href="/<?php echo $sitecode; ?>/usermodi" class="mine-link mine-link-information">
		<p>我的资料</p>
	</a>

	<?php if($ismanage) { ?>
	<a href="/<?php echo $sitecode; ?>/signupmanagelist" class="mine-link mine-link-signupmanage">
		<p>报名管理</p>
	</a>
	<a href="/<?php echo $sitecode; ?>/signin" class="mine-link mine-link-signupmanage">
		<p>用户签到</p>
	</a>
	<a href="/<?php echo $sitecode; ?>/commentmanage" class="mine-link mine-link-commentmanage">
		<p>评论管理</p>
	</a>
	<?php } ?>
			<div style="padding: 10px">
			<div style="font-size: 0.12rem; text-align: center;color: #808080">
				<div><?php echo $cms->GetConfigVal('webset','webname',$idsite);; ?></div>
				<div><?php echo str_replace("\r\n","<br>", $cms->GetConfigVal('webset','copyright',$idsite));?></div>
			</div>
			<div style="font-size: 0.1rem; text-align: center;color: #808080;padding-top: 10px;"
				onclick="location='https://www.tongxiang123.cn/tongxiang'">
				<div>童享云提供技术支持</div>
				<div>www.tongxiang123.com</div>
			</div>
		</div>
</div>
</body>
</html>



