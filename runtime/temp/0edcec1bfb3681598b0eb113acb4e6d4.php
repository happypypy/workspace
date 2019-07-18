<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:39:"template/M1/assemble/assemble_list.html";i:1563421729;s:52:"D:\workspace\work\public\template\M1\lib\header.html";i:1561691693;}*/ ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport"
        content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,user-scalable=0" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>拼团列表</title>
    <script type="text/javascript" src="<?php echo $roottpl; ?>/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $roottpl; ?>/js/common.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/common.css">
    <script type="text/javascript" src="<?php echo $roottpl; ?>/js/swiper.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/swiper.min.css">

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
    <div class="menu-cover"></div>
    <div class="site-section clearfix bg">
        <!--<div class="pintuan-img">-->
        <!--</div>-->
        <?php
			$result1=$cms->getAD($idsite,51328,3);
        if($result1){ ?>
        <div class="banner">
            <div class="swiper-container swiper1">
                <div class="swiper-wrapper">
                    <?php
        foreach($result1 as $k=>$v){ ?>
                    <div class="swiper-slide"><a href="<?php echo $v['ad_link']==''?'javascript:;':$v['ad_link']; ?>"><img src="<?php echo $v['ad_code']; ?>"></a></div>
                    <?php }?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <?php  }else{ ?>
        <div class="pintuan-img pintuan-list-img">
        </div>
        <?php ;} ?>
        <form id="frm">
            <div class="pintuan-search">
                <input class="pintuan-search-in" placeholder="请输入关键字搜索" name="keyword" value="<?php echo \think\Session::get('keyword'); ?>">
                <span class="iconfont">&#xe605;</span>
            </div>
        </form>


        <div class="news-list activity news-list1">
            <ul id="datalist">
                <?php foreach($groupBuys as $groupBuy): ?>
                    <li>
                        <a href="/<?php echo $sitecode; ?>/detail/<?php echo $groupBuy['activity_id']; ?>">
                            <div class="pic"><img src="<?php echo $roottpl; ?>/images/bar_03.jpg"></div>
                            <div class="txt">
                                <div class="title title1"><?php echo $groupBuy['chrtitle']; ?></div>
                                <div class="site site1"><i class="iconfont location">&#xe601;</i>适合年龄：<span><?php echo $groupBuy['minage'] == $groupBuy['maxage'] && $groupBuy['maxage'] == 0 ? '不限年龄' : $groupBuy['minage'] . ' ~ ' . $groupBuy['maxage']; ?></span></div>
                                <div class="time time1"><i class="iconfont clock">&#xe602;</i><span>活动时间：<?php echo date('Y-m-d', $groupBuy['start_at']); ?> ~ <?php echo date('Y-m-d', $groupBuy['end_at']); ?></span></div>
                            </div>
                        </a>
                        <a href="/<?php echo $sitecode; ?>/detail/<?php echo $groupBuy['activity_id']; ?>">
                            <div class="txt spell-txt">
                                <div class="spell-txtl">单购价:<del><?php echo $groupBuy['member_price']; ?></del>元</div>
                                <span class="spell-txtm">拼团价:<span><?php echo $groupBuy['group_buy_price']; ?></span>元</span>
                                <!-- <a href="/<?php echo $sitecode; ?>/detail/<?php echo $groupBuy['activity_id']; ?>"> -->
                                    <input class="button_1 spell-txtr" type="button" value="我要拼团" onclick="">
                                <!-- </a> -->
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<div id="dataload" class="iconfont iconload" style="display: none">&#xe72f;</div>

<script>
    $(function(){
        //padding-top:
        var swiper1 = new Swiper('.banner .swiper-container.swiper1', {
            pagination: {
                el: '.banner .swiper-pagination',
            },
            autoplay: {
                delay:2000,
            },
            loop: true,
            watchOverflow: true,
        });
    })
</script>
</body>
<script>
    //点击之后，让搜索条件维持，不点击，不维持
    $('.iconfont').click(function(){
        $("#frm").submit()
    })


    var winH = $(window).height(); //页面可视区域高度

    var page=2;
    var scrollHandler = function () {
        var pageH = $(document).height();
        var scrollT = $(window).scrollTop(); //滚动条top

        if(pageH-winH-scrollT<1)
        {
            LoadData(page)
            page++;
        }
    }

    //定义鼠标滚动事件
    $(window).scroll(scrollHandler);

    function LoadData(page){
        $("#dataload").show();
        $ (window).unbind ('scroll');
        var keyword=$('.pintuan-search-in').val();

        $.ajax({
            url: "/<?php echo $sitecode; ?>/group_buy_list" ,
            data:{"keyword":keyword,"page":page,'ajax':1},
            success:function(data){
                if(data==11)
                {
                    $("#dataload").hide();
                    return false
                }

                $("#dataload").hide();
                $("#datalist").html($("#datalist").html()+data);
                $(window).scroll(scrollHandler);
            }
        })


    }
</script>

</html>