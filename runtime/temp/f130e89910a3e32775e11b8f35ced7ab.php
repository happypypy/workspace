<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:31:"template/M7/activity/index.html";i:1561691706;s:67:"C:\phpStudy\PHPTutorial\WWW\work\public\template\M7\lib\header.html";i:1561691705;s:68:"C:\phpStudy\PHPTutorial\WWW\work\public\template\M7\lib\footer0.html";i:1561691705;s:67:"C:\phpStudy\PHPTutorial\WWW\work\public\template\M7\lib\footer.html";i:1561691705;}*/ ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,user-scalable=0" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">

<title>活动</title>
<meta name="keywords" content="" />
<meta name="description" content="" />

<script type="text/javascript" src="<?php echo $roottpl; ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $roottpl; ?>/js/swiper.min.js"></script>
<script type="text/javascript" src="<?php echo $roottpl; ?>/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/swiper.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/common.css">
<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/pc.css">
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
		<form id="frm" method="post">
			<div class="classify-choose flex flex-middle">

				<div class="select">
					<select name="typeid">
						<option value="">全部类别</option>
						<?php if(is_array($hdfl) || $hdfl instanceof \think\Collection || $hdfl instanceof \think\Paginator): $i = 0; $__LIST__ = $hdfl;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
						<option value="<?php echo $vo['code']; ?>" <?php if($vo['code']==$typeid) { echo "selected"; } ?> ><?php echo $vo['name']; ?></option>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
				</div>
				<div class="select">
					<select name="tagid">
						<option value="">全部标签</option>
						<?php if(is_array($hdbq) || $hdbq instanceof \think\Collection || $hdbq instanceof \think\Paginator): $i = 0; $__LIST__ = $hdbq;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
						<option value="<?php echo $vo['code']; ?>" <?php if($vo['code']==$tagid) { echo "selected"; } ?> ><?php echo $vo['name']; ?></option>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
				</div>
				<div class="select">
					<select name="intflag">
						<option value="">全部</option>
						<option <?php if($intflag=="1") { echo "selected"; } ?>  value="1">进行中</option>
						<option <?php if($intflag=="2") { echo "selected"; } ?> value="2">已结束</option>
					</select>
				</div>

			</div>
		</form>
		<div style="border-top:solid 1px #e0e0e0"></div>
		<div class="select-bar fixed" style="position:static; display: none;">
			<ul class="select-bar-tab" id="select-bar-tab">
				<li class="on"><span>进行中活动</span></li>
				<li><span>已结束活动</span></li>
			</ul>
		</div>
		<ul class="list" id="data">
			<?php  if(!$result_data) { ?>
			<li>
				<div class="noword">
					<div class="txt" >没有相关的活动</div>
				</div>
			</li>
			<?php }
			foreach($result_data as $k=>$val){ ?>
			<li>
				<a href="<?php echo (empty($val['chrurl'])?'/'.$sitecode.'/detail/'.$val['idactivity']:$val['chrurl']) ?>">
					<img src="<?php echo $val['chrimg']; ?>">
					<div class="word">
						<div class="tit"><?php if($val['is_receive_cashed'] == 1 && $is_cashed): ?><span class="iconfont coupon-link">&#xe624;</span><?php endif; ?><?php echo $val['chrtitle']; ?></div>
						<div class="txt"><?php echo $val['chrsummary']; ?></div>
						<div class="info">
							<!--<div class="type">活动</div>-->
							<div class="view"><span><?php echo $val['hits']; ?></span>浏览</div>
							<div class="time"><?php echo date('m-d',strtotime($val['dtpublishtime'])); ?></div>
						</div>
					</div>
				</a>
			</li>
			<?php } ?>
		</ul>
		<!--<ul class="list" id="dataload" style="display: none">-->
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
	<script language="JavaScript">
        $(function(){
            $(".classify-choose select").change(
                function(){
                    $("#frm").submit();
                });
        });
	</script>

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
            //类别id
            var typeid = $('select[name="typeid"]').val();
            //标签id
            var tagid = $('select[name="tagid"]').val();
            //状态
            var intflag = $('select[name="intflag"]').val();
            // alert(typeid);
            $.ajax({
                url: "/<?php echo $sitecode; ?>/activity/<?php echo $nodeid; ?>/"+ipage+"?&ajax=1",
                type: 'POST',
                cache: false,
                data:{"typeid":typeid,"tagid":tagid,"intflag":intflag} ,
                success : function(data) {

                    if(data== 11)
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
</body>
</html>
