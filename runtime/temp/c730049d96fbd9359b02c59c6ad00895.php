<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:33:"template/M1/order/signuppost.html";i:1561798994;}*/ ?>
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
    <script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/swiper.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/common.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/pc.css">

</head>
<body>
<script type="text/javascript" src="/static/js/layer/layer.js"></script>
<script type="text/javascript">

<?php if($ischarge==2 && $flag==1 && $price > 0) {
    $data=json_decode($data,true);
?>
    wx.config({
       //debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: '<?php echo $config["appId"]; ?>', // 必填，公众号的唯一标识
        timestamp:<?php echo $config["timestamp"]; ?> , // 必填，生成签名的时间戳
        nonceStr: '<?php echo $config["nonceStr"]; ?>', // 必填，生成签名的随机串
        signature: '<?php echo $config["signature"]; ?>',// 必填，签名
        jsApiList: ['chooseWXPay'] // 必填，需要使用的JS接口列表
    });
    wx.ready(function () {

        wx.chooseWXPay({
        timestamp: <?php echo $data["timeStamp"]; ?>, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
        nonceStr: '<?php echo $data["nonceStr"]; ?>', // 支付签名随机串，不长于 32 位
        package: '<?php echo $data["package"]; ?>', // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
        signType: '<?php echo $data["signType"]; ?>', // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
        paySign: '<?php echo $data["paySign"]; ?>', // 支付签名
            success: function (res) {
                // 支付成功后的回调函数
                //layer.confirm('',{btn:['关闭'],btn1: function(index, layero){ window.location="/<?php echo $sitecode; ?>/detail/<?php echo $dataID; ?>";}});
                //如果需要分享的话  那么就跳转到分享的页面
                    // 使用以上方式判断前端返回,微信团队郑重提示：
                    //res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                <?php if($is_cashed && $activity_cashed_set && $activity_cashed_set['cashed_plan_id'] > 0){ ?>
                        window.location="/<?php echo $sitecode; ?>/share/<?php echo $order_id; ?>/0";
                    <?php }else{ ?>
                        window.location="/<?php echo $sitecode; ?>/detail/<?php echo $dataID; ?>";
                    <?php } ?>

            },
            // // 支付取消回调函数
            // cancel: function (res) {
            //     window.location="/<?php echo $sitecode; ?>/signuplist";
            // },
            // // 支付失败回调函数
            // fail: function (res) {
            //     window.location="/<?php echo $sitecode; ?>/signuplist";
            // }
        });
    });
<?php } else {
    if($flag==2)
    {
            ?>
        layer.confirm('<?php echo $errmsg; ?>',{btn:['关闭'],btn1: function(index, layero){ window.location="/<?php echo $sitecode; ?>/detail/<?php echo $dataID; ?>"; return false;}});
        <?php

        if($err_arr){  ?>
            layer.confirm('<?php echo $err_arr[0]["err"]; ?>',{btn:['关闭'],btn1: function(index, layero){ window.location="/<?php echo $sitecode; ?>/againorder/<?php echo $order_id; ?>"; return false;}});
 <?php }}
    else
    {
    ?>
    layer.confirm('报名成功！',{btn:['关闭'],btn1: function(index, layero){ window.location="/<?php echo $sitecode; ?>/detail/<?php echo $dataID; ?>";}});
<?php }} ?>


</script>
<script>
</script>
</body>
</html>
