<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:32:"template/M6/mine/order_list.html";i:1564999662;s:53:"D:\workspace\work\public\template\M6\lib\footer0.html";i:1561691704;}*/ ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,user-scalable=0" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">

<title>我的报名</title>
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

<!--site section start-->
<div class="site-section clearfix" id="container" style="padding-top: 48px;">
    <div class="select-bar fixed">
        <ul class="select-bar-tab" id="select-bar-tab">
            <li id="s000" class="btn"><span id="s_name">全部报名</span></li>
            <li id="s100"><a href="#"  onclick="javascript:sel(100)">未开始</a></li>
            <li id="s200"><a href="#"  onclick="javascript:sel(200)">已结束</a></li>
        </ul>
        <div class="select-wrapper">
            <div class="select-main h294">
                <div class="select-right">
                    <div class="select-scroller">
                        <ul>
                            <li id="s0" onclick="javascript:sel(0)">全部报名</li>

                            <?php   foreach ($order_state as $k1=>$vo) { ?>
                            <li id="s<?php echo $k1; ?>" onclick="javascript:sel(<?php echo $k1; ?>)"><?php echo $vo; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="section-shade" id="section-shade"></div>
    <div class="news-list activity">
        <ul id="data">
            <?php if(empty($list)) { ?>
                <li>没找到相关报名信息</li>
            <?php } foreach($list as $k=>$vo) {?>
            <li>
                <div class="flex">
                    <div class="pic"><img src="<?php echo $vo['chrimg']; ?>" /></div>
                    <div class="txt">
                        <div class="title" onclick="javascript:goinfo(<?php echo $vo['dataid']; ?>)"><?php echo $vo['chrtitle']; ?></div>
                        <div class="price"><i class="iconfont price">&#xe620;</i><span>价格：<?php echo $vo['price']; ?>元</span></div>
                        <div class="state"><i class="iconfont laiyuan">&#xe60e;</i>状态：<span><?php echo $order_state[$vo['state']]; ?></span><span style="color: red;">&nbsp;&nbsp;<?php echo $vo['issign']==1?"(已签到)":""; ?></span></div>
                        <!-- <div class="style"><i class="iconfont laiyuan">&#xe60e;</i>方式：<span><?php echo $order_paytype1[$vo['paytype1']]; ?></span></div> -->

                        <!-- 拼团 -->
                        <?php if(checkedMarketingPackage($idsite, 'group_buy') && !empty($vo['group_buy_order_state_name'])): ?>
                            <div class="state"><i class="iconfont laiyuan">&#xe60e;</i>拼团：<span><?php echo $vo['group_buy_order_state_name']; ?></span></div>
                        <?php endif; ?>
                        
                        <div class="time"><i class="iconfont clock">&#xe602;</i><span>购买时间：<?php echo $vo['dtcreatetime']; ?></span></div>
                        <div class="time"><i class="iconfont clock">&#xe602;</i><span>活动时间：<?php echo $vo['dtstart']; ?>~<?php echo $vo['dtend']; ?></span></div>                        <div class="btn">
                            <a href="/<?php echo $sitecode; ?>/orderdetail/<?php echo $vo['id']; ?>" style="background: #d98bb3;">订单详情</a>
                            <?php if($vo['state']==4 && $vo['isrefund']==1 && $vo['issign']!=1) { ?>
                            <a href="#" id="refund_<?php echo $vo['id']; ?>" onclick="javascript:refund(<?php echo $vo['id']; ?>)" style="background: #ed958f;">申请退款</a>
                            <?php } ?>
                        <!--支付成功后-->
                        <?php if($is_cashed && $vo['state']==4 && $vo['share_plan_id'] > 0) { ?>
                        <a href="/<?php echo $sitecode; ?>/share/<?php echo $vo['id']; ?>/0" style="background: #5fd2b8; ">分享现金券</a>
                        <?php } if(checkedMarketingPackage($idsite, 'group_buy') && !empty($vo['group_buy_order_id']) && $vo['group_buy_order_state'] != 0 && $vo['group_buy_order_state'] != 4): ?>
                        <a href="/<?php echo $sitecode; ?>/group_buy_share/<?php echo $vo['group_buy_order_id']; ?>/<?php echo $userid; ?>" style="background: #d9b38b; ">拼团情况</a>
                        <?php endif; ?>
                        
                        <!--待支付可以手动改为终止服务-->
                        <?php if($vo['state']==12 && $vo['stock_locked'] ==1) { ?>
                        <a onclick="cancel_order(<?php echo $vo['id']; ?>)"  style="background: #666; ">取消订单</a>
                        <?php } ?>
                        <!--终止服务可以进行重新下单-->
                        <!--<?php if($vo['state']==10) { ?>-->
                        <!--<a  href="/<?php echo $sitecode; ?>/againorder/<?php echo $vo['id']; ?>"  style="background: #d9b38b; ">重新下单</a>-->
                        <!--<?php } ?>-->
                        <a href="#" style="background: #d9b38b; display: none" >查看约玩</a>
                        </div>
                    </div>
                </div>
            </li>
            <?php } ?>
        </ul>
        <div id="dataload" class="iconfont iconload" style="display: none">&#xe72f;</div>
    </div>
    <form id="frm" class="comment-form"  method="post" enctype="multipart/form-data">
        <div class="comment-textarea">
            <p>退款理由</p>
            <textarea name="content" id="content"></textarea>
        </div>
        <div class="comment-textarea">
            <br>
            <p>退款图片</p>
            <input type="file" name="image" id="image" />
        </div>
        <div class="comment-submit">
            <input type="hidden" id="orderid" name="orderid" value="">
            <input type="button" value="取消" id="close-refund" />
            <input type="button" onclick="javascript:sava_refund();" value="提交" />
        </div>
    </form>
    <div class="toTop-btn" title="回到顶部" onclick="toTop()"></div>
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
<!--site section end-->

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

<script type="text/javascript" src="/static/js/layer/layer.js"></script>
<script type="text/javascript">
    //取消订单
    function cancel_order(id)
    {
        $.ajax({
            url: "/<?php echo $sitecode; ?>/cancelorder/"+id,
            type: 'POST',
            cache: false,
            dataType:"json",
            success : function(data) {
                alert(data.msg);
                location.reload();
            }
        });

    }
    function refund(id)
    {
        $("#orderid").val(id);
        $(".comment-form,.cover").show();
    }
    function goinfo(id)
    {
        window.location="/<?php echo $sitecode; ?>/detail/"+id;
    }

    function sava_refund()
    {
        if($("#content").val()=="")
        {
            //layer.alert("请输入退款理由！",{icon:2});
            alert("请输入退款理由！");
            return;
        }
        $.ajax({
            url: "/<?php echo $sitecode; ?>/refund",
            type: 'POST',
            cache: false,
            data: new FormData($('#frm')[0]),
            processData: false,
            contentType: false,
            dataType:"json",
            success : function(data) {
                if (data.state == 1) {
                    //layer.alert("退款申请成功！",{icon:1});
                    alert("退款申请成功！");
                    $(".comment-form,.cover").hide();
                    $("#refund_"+$("#orderid").val()).hide();
                } else {
                    //layer.alert(data.msg,{icon:2});
                    alert(data.msg);
                }

            }
        });

    }

    $(function(){
        $("#close-refund").on("click",function(){
            $(".comment-form,.cover").hide();
        })

        $(".select-bar-tab li.btn").each(function(e){
            $(this).on("click",function(){
                if($(this).hasClass("on")){
                    $(this).removeClass("on");
                    $(".select-main").removeClass("active");
                    $(".section-shade").hide();
                }else{
                    $(this).addClass("on").siblings().removeClass("on");
                    $(".section-shade").show();
                    $(".select-main").eq(e).addClass("active").siblings(".select-main").removeClass("active");
                }
            });
        })
        $(".select-left li").on("click",function(){
            $(this).addClass("on").siblings().removeClass("on");
        });
        $(".select-right li").on("click",function(){
            $(this).addClass("on").siblings().removeClass("on");
            $(".section-shade").hide();
            $(".select-main").removeClass("active");
            $(".select-bar-tab li").removeClass("on");
        });
        $(".section-shade").on("click",function(){
            if($(".select-main").hasClass("active")){
                $(".section-shade").hide();
                $(".select-main").removeClass("active");
                $(".select-bar-tab li").removeClass("on");
            }
        })
        sets(<?php echo $state; ?>);
    });
    function sel(index) {
        window.location='/<?php echo $sitecode; ?>/signuplist/'+index;
    }

    function sets(index) {
        if(index>99)
        {
            $("#s"+index).css("background-color","#eee")
        }
        else
        {
            $("#s000").css("background-color","#eee")
            $("#s_name").html($("#s"+index).html());
        }
    }

    //==============核心代码=============
    var winH = $(window).height(); //页面可视区域高度

    var ipage=1;
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
        // var state = $('input[name="states"]').val();
        $.ajax({
            url: "/<?php echo $sitecode; ?>/signuplist/<?php echo $state; ?>",
            type: 'POST',
            cache: false,
            data:{"ipage":ipage,"ajax":1} ,
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