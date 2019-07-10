<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:27:"template/M2/node/index.html";i:1561691698;s:67:"C:\phpStudy\PHPTutorial\WWW\work\public\template\M2\lib\header.html";i:1561691698;s:68:"C:\phpStudy\PHPTutorial\WWW\work\public\template\M2\lib\footer0.html";i:1561691698;s:67:"C:\phpStudy\PHPTutorial\WWW\work\public\template\M2\lib\footer.html";i:1561691698;}*/ ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,user-scalable=0" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">

<title><?php echo $node_info['nodename']; ?></title>
<meta name="keywords" content="" />
<meta name="description" content="" />

<script type="text/javascript" src="<?php echo $roottpl; ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $roottpl; ?>/js/swiper.min.js"></script>
<script type="text/javascript" src="<?php echo $roottpl; ?>/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/swiper.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/common.css">
</head>
<body>
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
	<div class="site-section clearfix">
		<div class="where jigou">
			<p><?php echo $node_info['nodename']; ?></p>
			<a href="/<?php echo $sitecode; ?>" class="iconfont">&#xe617;</a>
		</div>
		<ul class="common-list" id="data">
			<?php  $re = $cms->GetContents($nodeid,[],'idorder DESC,contentid DESC','linkurl,summary,picurl,sys00003,hits,contentid,title,en_title,tc_title',1,10);
			foreach($re['data'] as $k=>$val){ ?>
			<li>
				<a href="<?php echo (empty($val['linkurl'])?'/'.$sitecode.'/content/'.$val['contentid']:$val['linkurl']) ?>">
					<img src="<?php echo $val['picurl']; ?>">
					<div class="word">
						<div class="tit"><?php echo $val['title']; ?></div>
						<div class="txt"><?php echo $val['summary']; ?></div>
						<div class="info">
							<!--<div class="type">活动</div>-->
							<div class="view"><span><?php echo $val['hits']; ?></span>浏览</div>
							<div class="time"><?php echo date('m-d',strtotime($val['sys00003'])); ?></div>
						</div>
					</div>
				</a>
			</li>
			<?php } ?>
		</ul>
		<!--<ul class="common-list" id="dataload" style="display: none">-->
			<!--<li id="loadmsg" >数据加载中。。。</li>-->
		<!--</ul>-->
		<div id="dataload" class="iconfont iconload" style="display: none">&#xe72f;</div>
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
				<footer style="display: none">
			<div class="footer" >
				<ul>
					<li <?php echo $SelectFooterTab==1?"class='on'":"" ?>>
					<a href="/<?php echo $sitecode; ?>">
						<span><i class="iconfont earth">&#xe600;</i></span>
						<p>资讯</p>
					</a>
					</li>
					<li <?php echo $SelectFooterTab==2?"class='on'":"" ?>>
					<a href="/<?php echo $sitecode; ?>/activity">
						<span><i class="iconfont coffee">&#xe604;</i></span>
						<p>活动</p>
					</a>
					</li>
					<li <?php echo $SelectFooterTab==3?"class='on'":"" ?>>
					<a href="mine.html">
						<span><i class="iconfont head">&#xe606;</i></span>
						<p>我的</p>
					</a>
					</li>
				</ul>
			</div>
		</footer>
	</div>
</body>
<script type="text/javascript" src="/static/js/layer/layer.js"></script>
<script type="text/javascript">

    //==============核心代码=============
    var winH = $(window).height(); //页面可视区域高度

    var ipage=2;
    var scrollHandler = function () {
        var pageH = $(document).height();
        var scrollT = $(window).scrollTop(); //滚动条top

        if(pageH-winH-scrollT<1)
        {
            LoadData(ipage)
            ipage++;
        }
    }
    //定义鼠标滚动事件
    $(window).scroll(scrollHandler);
    //==============核心代码=============

    function LoadData(ipage)
    {

        $("#dataload").show();
        $ (window).unbind ('scroll');
        $.ajax({
            url: "/<?php echo $sitecode; ?>/node/<?php echo $nodeid; ?>/"+ipage+"?&ajax=1",
            type: 'POST',
            cache: false,
            success : function(data) {

                if(data==11)
                {
                    $("#dataload").hide();
                    // $("#loadmsg").html("已无更多数据");
                    return;
                }
                $("#dataload").hide();
                $("#data").html($("#data").html()+data);
                $(window).scroll(scrollHandler);
            }
        });

    }




</script>
</html>