<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:40:"template/M4/assemble/assemble_share.html";i:1563268550;s:52:"D:\workspace\work\public\template\M1\lib\header.html";i:1561691693;}*/ ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport"
        content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,user-scalable=0" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>拼团分享</title>
    <script type="text/javascript" src="<?php echo $roottpl; ?>/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $roottpl; ?>/js/swiper.min.js"></script>
    <script type="text/javascript" src="<?php echo $roottpl; ?>/js/common.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>

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

    <div class="menu-cover"></div>
    <div class="site-section clearfix bg">
        <div class="pintuan-img">
            <div class="pintuan-img-l">
                <img src="<?php echo $data['imgs'][0]['userimg']; ?>" alt="">
            </div>
            <div class="pintuan-img-r">
                <p>我是<span><?php echo $data['username']; ?></span></p>
                <?php if($data['isStarter']): ?>
                    <p>我发起了拼团，赶快来参与吧</p>
                <?php else: ?>
                    <p>我参加了拼团，赶快来参与吧</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="ass-share-info">
            <?php if($data['state'] == 2 ): ?>
            <div class="ass-timebox">恭喜您，已成团！</div>

            <?php elseif($data['state'] == 4): ?>
            <div class="ass-timebox">很遗憾，拼团已解散！</div>

            <?php else : ?>

            <div class="ass-timebox">距离结束拼团还有：
                <span class="count-down">
                <span class="day">0</span> 天 <span class="hour">0</span> 时 <span class="minute">0</span> 分 <span class="sec">0</span> 秒
            </span><!-- <span>拼团已结束</span> --></div>

            <?php endif; ?>
            <div class="ass-share-item">
                <a href="">
                    <div class="pic"><img src="<?php echo $data['imgs'][0]['userimg']; ?>" alt=""></div>
                    <div class="txt">
                        <div class="tit"><?php echo $data['chrtitle']; ?></div>
                        <div class="time"><?php echo $data['package_name']; ?></div>
                        <div class="source">单购价：<?php echo $data['original_price']; ?>元 &nbsp;&nbsp;拼团价：<?php echo $data['group_buy_price']; ?>元</div>
                    </div>
                </a>
            </div>

            <div class="present-box">
                <?php if($data['left'] > 0): ?>
                    <div>需拼团人数：<span><?php echo $data['group_num']; ?>件</span>&nbsp;&nbsp;<span>还差<?php echo $data['left']; ?>人</span></div>
                <?php else: ?>
                    <div><span>已成团</span></div>
                <?php endif; ?>
                <div>已参团用户：
                    <?php foreach ($data['imgs'] as $key => $img): ?>
                    <img class="user" src="<?php echo $img['userimg']; ?>" alt=""><?php echo $img['nickname']; ?>,
                <?php endforeach; ?>
                </div>
            </div>
             <div class="share-btn-box">
                <!-- 拼团有效期和拼团状态 -->
                <?php if($data['state'] == 2): ?>
                    <button class="compelte-btn share-btn">已成团</button>
                <?php elseif($data['state'] == 4): ?>
                    <button disabled="disabled" class="closed-btn share-btn">拼团解散</button>
                <?php elseif($data['state'] == 0): ?>
                    <button disabled="disabled" class="unpaied-btn share-btn">拼团未支付</button>
                <?php elseif($data['expiration'] > time()): ?>
                    <button class="share-btn">分享</button>
                <?php else: ?>
                    <button disabled="disabled" class="closed-btn share-btn">拼团已结束</button>
                <?php endif; ?>
            </div>
            <div class="ass-rule">
                <p>拼团规则说明：</p>
                <p>1.点击活（或产品）进入详情介绍页面，通过“我要开团”入口进入订单支付页面，付款成功后，按页面提示分享给微信好友参团。</p>
                <p>2.好友通过拼团发起者的拼团详情页面，点击“我要参团”进入订单支付页面，付款成功后，按页面提示分享给微信好友。如果详情页面显示已经满员，可点击“我要开团”发起新的拼团。</p>
                <p>3.参团人数在有效期内达到拼团人数（成团人数要求请看对应活动拼团说明），即拼团成功。拼团时间到期后，若未达到拼团人数，即拼团失败，拼团订单于24小时内全额原路退款。</p>
                <p>4.发起拼团或参与拼团，或许有次数限制，限制规则由具体活动而定，请参考拼团说明信息。</p>
                <p>5.当前活动专用优惠券不能用于拼团支付。</p>
                <p>最终解析权归蜗牛童行所有。</p>
            </div>
        </div>
        

    </div>
</body>


<script language="JavaScript">

    (function ($) {
            $.fn.extend({
                countDown: function (options) {
                    var defaults = {
                        day: '.day',
                        hour: '.hour',
                        minute: '.minute',
                        sec: '.sec'
                    },
                        opts = $.extend({}, defaults, options); //对象扩展到opts
                    this.each(function () {     //遍历
                        var $this = $(this);
                        times();    //先执行一次，防止刷新时数字都显示为0
                        var timer = setInterval(times, 1000);   //定时器执行

                        function times() {
                            var nowDate =  Math.round(new Date().getTime() / 1000).toString();
                                endDate = '<?php echo $data["expiration"]; ?>';
                                tms = endDate - nowDate,    //时间差
                                days = Math.floor(tms / 60 / 60 / 24),
                                hours = Math.floor(tms / 60 / 60 % 24),
                                minutes = Math.floor(tms / 60 % 60),
                                secs = Math.floor(tms % 60);

                            if (tms > 0) {  //如果时间差大于0，显示倒计时
                                $this.find(opts.day).text(addZero(days));
                                $this.find(opts.hour).text(addZero(hours));
                                $this.find(opts.minute).text(addZero(minutes));
                                $this.find(opts.sec).text(addZero(secs));
                            } else {    //否则清除定时器，倒计时结束
                                clearInterval(timer);
                                $this.html('拼团已结束')
                            }
                        }
                    });


                    function addZero(t) {  //一位数加0
                        if (t < 10) {
                            return t = '0' + t;
                        } else {
                            return t;
                        }
                    }
                    return this; //返回this方便链式调用
                }
            });
            $('.count-down').countDown(); //默认调用方法
            $('.a2').countDown();
        })(jQuery)

    $(function () {
        $('.share-btn').click(function () {
            var url = document.domain;
            layer.open({
                title: url,
                skin: 'share-layer',
                content: '点击右上角分享给朋友或朋友圈',
                closeBtn: 0,
                btnAlign: 'c',
            })
        })
    });


    <?php if(isset($signPackage)): ?>
        wx.config({
            debug: false,
            appId: '<?php echo $signPackage["appId"]; ?>',
            timestamp: '<?php echo $signPackage["timestamp"]; ?>',
            nonceStr: '<?php echo $signPackage["nonceStr"]; ?>',
            signature: '<?php echo $signPackage["signature"]; ?>',
            jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage','openLocation'],
        });
        wx.ready(function () {

            //获取“分享到朋友圈”按钮点击状态及自定义分享内容接口（即将废弃）
            wx.onMenuShareTimeline({
                title: '<?php echo $data["username"]; ?>分享了<?php echo $data["package_name"]; ?>拼团，还剩<?php echo $data["left"]; ?>份，先抢先得!', // 分享标题
                link: '<?php echo $shareUrl; ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '<?php echo $roottpl; ?>/static/images/wx_20190619175904.jpg', // 分享图标
                success: function () {
                    // alert('已分享');
                }
            });

            //获取“分享给朋友”按钮点击状态及自定义分享内容接口（即将废弃）
            wx.onMenuShareAppMessage({
                title: '<?php echo $data["username"]; ?>分享了<?php echo $data["package_name"]; ?>拼团，还剩<?php echo $data["left"]; ?>份，先抢先得!', // 分享标题
                desc: '<?php echo $data["username"]; ?>' + "<?php echo !empty($data['isStarter'])?'发起' : '参与'; ?>" + '了“交易分享现金券福利”，与你分享', // 分享描述
                link: '<?php echo $shareUrl; ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '<?php echo $roottpl; ?>/static/images/wx_20190619175904.jpg', // 分享图标
                type: 'link', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // alert('已分享');
                }
            });

        });

        wx.error(function(res){
            //alert("接口调取失败")
        });

        // $(function () {
        //     $('.share-btn').click(function () {
        //         var url = document.domain;
        //         layer.open({
        //             title: url,
        //             skin: 'share-layer',
        //             content: '点击右上角分享给朋友或朋友圈',
        //             closeBtn: 0,
        //             btnAlign: 'c',
        //         })
        //     })
        // })
    <?php endif; ?>

</script>

</html>