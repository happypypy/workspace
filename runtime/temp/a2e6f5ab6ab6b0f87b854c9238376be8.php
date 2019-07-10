<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:22:"template/M3/index.html";i:1562315897;s:52:"D:\workspace\work\public\template\M3\lib\header.html";i:1561691700;s:53:"D:\workspace\work\public\template\M3\lib\footer0.html";i:1561691700;s:52:"D:\workspace\work\public\template\M3\lib\footer.html";i:1561691700;}*/ ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,user-scalable=0" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">

<title><?php echo $cms->GetConfigVal('webset','webname',$idsite);; ?></title>
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
<div class="site-section clearfix bg">
	<div class="banner">
		<div class="swiper-container">
			<div class="swiper-wrapper">
				<?php 
				$result1=$cms->getAD($idsite,51325,6);
				if($result1){
				foreach($result1 as $k=>$v){ ?>
				<div class="swiper-slide"><a href="<?php echo $v['ad_link']==''?'javascript:;':$v['ad_link']; ?>"><img src="<?php echo $v['ad_code']; ?>"></a></div>
				<?php }} else { ?>
				<div class="swiper-slide"><a href="javascript:;"><img src="<?php echo $roottpl; ?>/images/banner_01.jpg"></a></div>
				<?php } ?>

				
			</div>	
			<div class="swiper-pagination"></div>
		</div>
		
	</div>
		<ul class="nav flex">
			<?php  $result=$cms->GetAllNodes(['showonmenu'=>1,'nodetype'=>['in','1,2'],'parentid'=>0],'idorder asc,nodeid asc','',20,$idsite); foreach($result as $k=>$v){
			$jumpUrl ="/". $sitecode.$v["url"];
			if(substr( $v["url"],0,4 ) == "http"){
			$jumpUrl = $v["url"];
			}
			?>
			<li><a href="<?php echo $jumpUrl; ?>"><?php echo $v['nodename']; ?></a></li>
			<?php }?>
		</ul>

		<!-- 拼团 -->
        <?php if(checkedMarketingPackage($idsite, 'group_buy') &&  $groupBuys): ?>
            <div class="news-list activity bgw news-list1">
                <div class="headline">限时拼团<a href="/<?php echo $sitecode; ?>/group_buy_list" class="more">更多&gt;&gt;</a></div>
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
        <?php endif; ?>
		
		<div class="combox bgw">
				<div  class="headline">活动推荐</div>
				<ul class="common-list">
					<?php  $re = $cms->GetActivity($idsite,[],'','idactivity,chkisindex,chrtitle,chrimg_m,chrurl,chrsummary,dtstart,dtpublishtime,hits,s.is_receive_cashed',10);
							$index=0;
							foreach($re as $k=>$val){
								if($val['chkisindex']==1) {
									$index++;
							?>
					<li>
						<a href="<?php echo (empty($val['chrurl'])?'/'.$sitecode.'/detail/'.$val['idactivity']:$val['chrurl']) ?>">
							<img src="<?php echo $val['chrimg_m']; ?>">
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
					<?php }}
							if($index==0){ ?>
					<li style="height:0.3rem;">
						<div class="t9">抱歉，当前没有最新活动信息</div>
					</li>
					<?php }?>
				</ul>
				
		</div>
	<div class="combox bgw">
		<div  class="headline">动态信息</div>
		
		<ul class="common-list">
			<?php $re = $cms->GetContentsIndex($idsite,10);
					if(empty($re)){ ?>
			<li style="height:0.3rem;">
				<div class="t9">抱歉，当前没有最新信息</div>
			</li>
			<?php }
					foreach($re as $ke=>$val){ ?>
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
	</div>

		<?php if($is_sign == '1'): ?>
		<div class="register-box">
			<div class="register flex">
				<div class="iconfont retrac">&#xe881;</div>
				<div class="link-signin"><a href="/<?php echo $sitecode; ?>/dailysignin">点击签到</a></div>
			</div>
		</div>
		<?php endif; ?>
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
<script>
$(function(){
	var swiper1 = new Swiper('.banner .swiper-container', {
	    pagination: {
	    	el: '.banner .swiper-pagination',
	    },
	    autoplay: true,
	    loop: true,
	    watchOverflow: true,
	});
})	
</script>
<script>
	$(function () {
		var flag = 1;
		$('.retrac').on('click', function () {
			if (flag == 1) {
				$(this).html('&#xe65a;').parents('.register-box').addClass('on');
				flag = 0;
			} else {
				$(this).html('&#xe881;').parents('.register-box').removeClass('on');
				flag = 1;
			}

		})
	})
</script>
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript" src="https://map.qq.com/api/js?v=2.exp"></script>
	<script language="JavaScript">
        wx.config({
            debug: false,
            appId: '<?php echo $signPackage["appId"]; ?>',
            timestamp: '<?php echo $signPackage["timestamp"]; ?>',
            nonceStr: '<?php echo $signPackage["nonceStr"]; ?>',
            signature: '<?php echo $signPackage["signature"]; ?>',
            jsApiList: ['getLocation',]
        });
        wx.ready(function () {
            wx.checkJsApi({
                jsApiList: [
                    'getLocation'
                ],
                success: function (res) {
                    // alert(JSON.stringify(res));
                    // alert(JSON.stringify(res.checkResult.getLocation));
                    if (res.checkResult.getLocation == false) {
                        alert('你的微信版本太低，不支持微信JS接口，请升级到最新的微信版本！');
                        return;
                    }
                }
            });
            wx.error(function(res){
               // alert("接口调取失败")
            });

            wx.getLocation({
                type: 'gcj02',
                success: function (res) {
                    getaddress(res.latitude, res.longitude);
                    /*
                    var geocoder = new qq.maps.Geocoder({
                    complete: function (result) {   //解析成功的回调函数
                            var address = result.detail.address;  //获取详细地址信息
                            loginlog(res.latitude, res.longitude,address);
                        }
                    });
                    geocoder.getAddress(new qq.maps.LatLng(res.latitude, res.longitude));
                    */
                },
                cancel: function (res) {
                    alert('用户拒绝授权获取地理位置');
                }
            });
        });
        function loginlog(latitude,longitude,address) {
            var data= {"latitude":latitude, "longitude":longitude, "address": address};
            $.ajax({
                url:"/<?php echo $sitecode; ?>/loginlog",
                data:data,
                type:"post",
                dataType:"json",
                success:function(msg) {
                    //alert(msg);
                }
            })
        }

        function getaddress(latitude,longitude) {
            var url="https://apis.map.qq.com/ws/geocoder/v1";
            var arr = new Array(latitude, longitude);
            var data= {
                key:"VGVBZ-PHEW6-QLBSQ-MJ34Q-CSSHO-3EFDW",
                location: arr.toString(),
                get_poi: 1, //是否返回周边POI列表：1.返回；0不返回(默认)
                parameter: { "scene_type": "tohome", "poi_num": 20 }, //附加控制功能
                output: "jsonp"
            };
            //window.location=url;
            //return;
            $.ajax({
                url:url,
                type:"get",
                data:data,
                contentType : "application/json",
                dataType:"jsonp",
                success: function (data, textStatus) {
                    if (data.status == 0) {
                        var address =data.result.address;
                        address +="["+data.result.formatted_addresses.recommend+"]";
                        loginlog(latitude,longitude,address)
                        // alert(address);

                    } else {
                      //  alert("系统错误");
                    }
                },
                error:function(data, textStatus, errorThrown){

                }

            })
        }


	</script>

</body>
</html>