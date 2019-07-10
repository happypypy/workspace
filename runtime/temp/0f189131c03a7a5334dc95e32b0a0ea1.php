<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:34:"template/M1/mine/order_detail.html";i:1562203883;s:68:"C:\phpStudy\PHPTutorial\WWW\work\public\template\M1\lib\footer0.html";i:1561691693;}*/ ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,user-scalable=0" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">

<title>报名信息</title>
<meta name="keywords" content="" />
<meta name="description" content="" />

<script type="text/javascript" src="<?php echo $roottpl; ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $roottpl; ?>/js/swiper.min.js"></script>
<script type="text/javascript" src="<?php echo $roottpl; ?>/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/swiper.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/common.css">
<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/pc.css">
</head>
<body style="background: #e7e7e7;">

<form class="information-list">
    <ul>
        <li>
            <div class="head">基本信息</div>
        </li>
        <li>
            <div class="tit">报名对象：</div>
            <div class="txt">
                <div class="info">
                    <span style="cursor:pointer;" class="name" onclick="javascript:window.location='/<?php echo $sitecode; ?>/detail/<?php echo $orderinfo['dataid']; ?>'"><?php echo $orderinfo['chrtitle']; ?></span>
                </div>
            </div>
        </li>
        <li>
            <div class="tit">购买时间：</div>
            <div class="txt">
                <div class="info"><?php echo $orderinfo['dtcreatetime']; ?></div>
            </div>
        </li>
        <li>
            <div class="tit">活动时间：</div>
            <div class="txt">
                <div class="info"><?php echo $orderinfo['dtstart']; ?> 至 <?php echo $orderinfo['dtend']; ?></div>
            </div>
        </li>
        <li>
            <div class="tit">订单号：</div>
            <div class="txt">
                <div class="info"><?php echo $orderinfo['ordersn']; ?></div>
            </div>
        </li>
        <li>
            <div class="tit">套餐：</div>
            <div class="txt">  
                <div class="info"><?php echo $orderinfo['payname']; ?> × <?php echo $orderinfo['paynum']; ?></div>
                
            </div>
        </li>
        <li>
            <div class="tit">总金额：</div>
            <div class="txt">
                <div class="info"><?php echo $orderinfo['price']; ?>(元)</div>
            </div>
        </li>
        <?php if($is_cashed && $orderinfo['cashed_amount'] > 0): ?>
        <li>
            <div class="tit">现金券抵扣：</div>
            <div class="txt">
                <div class="info"><?php echo $orderinfo['cashed_amount']; ?>(元)</div>
            </div>
        </li>
        <?php endif; ?>
        <li>
            <div class="tit">方式：</div>
            <div class="txt">
                <div class="info"><?php echo $order_paytype1[$orderinfo['paytype1']]; ?></div>
            </div>
        </li>
        <li>
            <div class="tit">状态：</div>
            <div class="txt">
                <div class="info">
                    <?php echo $order_state[$orderinfo['state']]; if($orderinfo['state']==12) { ?>
                    <a href="/<?php echo $sitecode; ?>/againorder/<?php echo $orderinfo['id']; ?>" style="background: #d9b38b; margin-left: 20px; padding: 4px 10px;background: #ccc;margin-right: 8px;margin-bottom: 2px;border-radius: 3px;color: #fff;	font-size: 14px; background: #d98bb3" >继续付费</a>
                    <?php } ?>
                </div>
            </div>
        </li>
        <li>
            <div class="head">报名详情</div>
        </li>
        <?php foreach($frmdata as $k=>$vo) { ?>

        <li>
            <div class="tit"><?php echo $k; ?>：</div>
            <div class="txt">
                <div class="info"><?php echo $vo; ?></div>
            </div>
        </li>
       <?php } if(!empty($frmdatasub)) { ?>
        <li>
            <div class="head">更多报名信息</div>
        </li>
        <?php foreach($frmdatasub as $k1=>$vo1) { foreach($vo1 as $k=>$vo) { ?>
        <li>
            <div class="tit"><?php echo $k; ?><?php echo $k1+1; ?>：</div>
            <div class="txt">
                <div class="info"><?php echo $vo; ?></div>
            </div>
        </li>
        <?php }}} if(!empty($orderinfo['refundsn']) || !empty($orderinfo['refundremark'])) { ?>
        <li>
            <div class="head">退款记录</div>
        </li>
        <li>
            <div class="tit">退款的单号：</div>
            <div class="txt">
                <div class="info"><?php echo !empty($orderinfo['refundsn'])?$orderinfo['refundsn']:""; ?></div>
            </div>
        </li>
        <?php if(!empty($orderinfo['dtwxrefundtime'])){ ?>
        <li>
            <div class="tit">退款时间：</div>
            <div class="txt">
                <div class="info"><?php echo $orderinfo['dtwxrefundtime']; ?></div>
            </div>
        </li>
        <?php } if(!empty($orderinfo['refundprice'])){ ?>
        <li>
            <div class="tit">退款金额：</div>
            <div class="txt">
                <div class="info"><?php echo $orderinfo['refundprice']; ?></div>
            </div>
        </li>
        <?php } if(!empty($orderinfo['refundremark'])){ ?>
        <li>
            <div class="tit">退款原因：</div>
            <div class="txt">
                <div class="info"><?php echo $orderinfo['refundremark']; ?></div>
            </div>
        </li>
        <?php } if(!empty($orderinfo['refundpic'])){ ?>
        <li>
            <div class="tit">图片：</div>
            <div class="txt">
                <img src="<?php echo $orderinfo['refundpic']; ?>" height="60">
            </div>
        </li>
        <?php } if(!empty($orderinfo['refundsn2'])  || !empty($orderinfo['refundmsg2'])) { ?>
        <li>
            <div class="tit">退款的单号2：</div>
            <div class="txt">
                <div class="info"><?php echo !empty($orderinfo['refundsn2'])?$orderinfo['refundsn2']:"<span style='color: red'>申请中</span>"; ?></div>
            </div>
        </li>
        <?php if(!empty($orderinfo['dtwxrefundtime2'])){ ?>
        <li>
            <div class="tit">退款时间2：</div>
            <div class="txt">
                <div class="info"><?php echo $orderinfo['dtwxrefundtime2']; ?></div>
            </div>
        </li>
        <?php } if(!empty($orderinfo['refundprice2'])){ ?>
        <li>
            <div class="tit">退款金额2：</div>
            <div class="txt">
                <div class="info"><?php echo $orderinfo['refundprice2']; ?></div>
            </div>
        </li>
        <?php } if(!empty($orderinfo['refundmsg2'])){ ?>
        <li>
            <div class="tit">退款原因2：</div>
            <div class="txt">
                <div class="info"><?php echo $orderinfo['refundmsg2']; ?></div>
            </div>
        </li>
        <?php } if(!empty($orderinfo['refundpic'])){ ?>
        <li>
            <div class="tit">图片2：</div>
            <div class="txt">
                <img src="<?php echo $orderinfo['refundpic']; ?>" height="60">
            </div>
        </li>
        <?php }}} if(($orderinfo["state"]>=3 && $orderinfo["state"]<=8 && $orderinfo["state"]!=5)){
        if($orderinfo["issign"]!=1){
        ?>
        <li>
            <div class="head">签到二维码</div>
        </li>
        <?php } ?>
        <li>
            <?php if($orderinfo["issign"]==1) { ?>
            <div class="tit">
                签到时间：
            </div>
            <div class="txt"><div class="info"><?php echo $orderinfo['dtsigntime']; ?></div></div>
            <?php } else  {?>
            <img id='img_rqcode' src='/admin/Qrcode/signin/code/<?php echo $sitecode; ?>/id/<?php echo $orderinfo['ordersn']; ?>' />
            <?php } ?>
        </li>
        <?php } ?>
    </ul>
    		<div style="padding: 10px">
			<div style="font-size: 0.12rem; text-align: center;color: #808080">
				<div><?php echo $cms->GetConfigVal('webset','webname',$idsite);; ?></div>
				<div><?php echo str_replace("\r\n","<br>", $cms->GetConfigVal('webset','copyright',$idsite));?></div>
			</div>
			<div style="font-size: 0.1rem; text-align: center;color: #808080;padding-top: 10px;" onclick="location='https://www.tongxiang123.cn/tongxiang'">
				<div>童享云提供技术支持</div>
				<div>www.tongxiang123.com</div>
			</div>
		</div>
</form>

<div class="footer">
    <ul>
        <li>
            <a href="/<?php echo $sitecode; ?>">
                <span><i class="iconfont home">&#xe617;</i></span>
                <p>主页</p>
            </a>
        </li>
        <li class="on">
            <a href="/<?php echo $sitecode; ?>/mine">
                <span><i class="iconfont head">&#xe606;</i></span>
                <p>我的</p>
            </a>
        </li>
    </ul>
</div>
</body>
</html>