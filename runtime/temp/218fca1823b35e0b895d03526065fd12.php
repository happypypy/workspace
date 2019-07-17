<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:29:"template/M6/order/signup.html";i:1563178000;}*/ ?>
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
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
</head>
<body>

<form class="information-list" method="post" id="frm1" action="/<?php echo $sitecode; ?>/signup_post/<?php echo $id; ?>" enctype="multipart/form-data" >
    <ul>
        <li><font style="color:#ff7902;">*请如实填写以下信息</font></li>
        <?php
             $re1 = $cms->GetActivityInfo($id, "selcontent,selsignfrom, intsignnum",$groupBuyId, $groupBuyOrderId);
        $re = $cms->GetSignupTempSub($re1['selsignfrom']);
        if($re){
        foreach($re as $k=>$vo){ ?>
        <li class="data">
            <div class="tit"><?php if($vo['is_must']): ?><font style="color:red">*</font><?php endif; ?><?php echo $vo['title']; ?>：</div>
            <div class="txt">
                <?php
                //1文本框 2数字框 3电话框 4邮件框 5下拉框 6多行文本框 7图片上传 8身份证
                if($vo['chrtype']==5){ ?>
                <select  name="feild_<?php echo $vo['id']; ?>" class="feild" style="border: 0px; font-size: 16px;width: 100%;">
                    <option value="">请选择<?php echo $vo['title']; ?></option>
                    <?php
                         $arrtmp=explode(",",$vo['chrvalue']);
                         foreach($arrtmp as $k1=>$vo1) {
                    ?>
                    <option value="<?php echo $vo1; ?>"><?php echo $vo1; ?></option>
                    <?php } ?>
                </select>
                <?php } elseif($vo['chrtype']==6){ ?>
                <textarea type="text" name="feild_<?php echo $vo['id']; ?>" style="width: 100%;height: 48px;border: 0px;line-height: 24px;font-size: 16px; "  class="feild" datatype="<?php echo $vo['chrtype']; ?>" ></textarea>
                <?php } elseif($vo['chrtype']==7){ ?>
                <img src="/static/images/addimg.jpg" id="img_<?php echo $vo['id']; ?>" style="width: 0.5rem;height: 0.5rem;" onclick="javascript:selimg('feild_<?php echo $vo['id']; ?>')" class="img">
                <input style="display: none" id="feild_<?php echo $vo['id']; ?>" type="file" name="feild_<?php echo $vo['id']; ?>" value="上传图片" style="height:24px;" class="feild" datatype="<?php echo $vo['chrtype']; ?>" onchange="javascript:changeimg(this,'img_<?php echo $vo['id']; ?>')" />
                <?php } else {
                    $data_value="";
                   if($vo['chrtype']==9) $data_value=$userinfo["chrname"];
                    elseif($vo['chrtype']==10) $data_value=$userinfo["chrtel"];
                    elseif($vo['chrtype']==11) $data_value=$userinfo["chrmail"];
                    elseif($vo['chrtype']==12) $data_value=$userinfo["postal_address"];
                    elseif($vo['chrtype']==13) $data_value=$userinfo["identity_card_num"];

                ?>
                <input type="text" name="feild_<?php echo $vo['id']; ?>" value="<?php echo $data_value; ?>" class="feild" datatype="<?php echo $vo['chrtype']; ?>" />
                <?php } ?>
                <input style="display: none" name="feildname_<?php echo $vo['id']; ?>" type="text" value="<?php echo $vo['title']; ?>∫<?php echo $vo['chrtype']; ?>" class="feildname">
                <input style="display: none" name="feildmust_<?php echo $vo['id']; ?>" type="hidden" value="<?php echo $vo['is_must']; ?>" class="feildmust">
            </div>
        </li>


        <?php }}
        $re2 = $cms->GetSignupTemp($re1['selsignfrom']);
        if($re2 && $re2['issub']==1)
        {
        ?>
        <li><font style="color:#ff7902;">*更多报名信息</font> </li>

        <li style="display:block;" >
            <div id="divsub" >
                <input type="hidden" name="subindex" id="subindex" value="0">
                <img src="/static/images/oa_ico-04-big.gif"  style=" padding-left:0.2rem; padding-right: 0.1rem; width: 0.2rem;height: 0.2rem;" onclick="javascript:addsub()" class="img">
                <span style="color:#777" onclick="javascript:addsub()">增加报名信息</span>
            </div>
        </li>
        <?php } if($re2['remark']): ?>
         <li><pre style="padding: 0 10px;color:  red;line-height: 150%; white-space: pre-wrap;word-wrap: break-word;"><?php echo $re2['remark']?></pre></li>
         <?php endif; ?>
        <li>
            <font style="color:#ff7902;">*请选择套餐</font></li>
        <li>
            <div class="txt">
                <?php
                if($re1['selcontent']) foreach($re1['selcontent'] as $i => $package)
                {
                    $keyword = $package['keyword1'] . ' ' . $package['keyword2'];
                    $price = '(' . $package['member_price'] . '元)';
                if($package['package_sum'] == -1){
                $package['stock']='已售完';
                }
                ?>
                    <div class="pwclass pw" Stock="<?php echo $package['stock']; ?>" price="<?php echo $package['member_price']; ?>"  data="<?php echo $package['package_id']; ?>">
                        <!-- 关键字 -->
                        <?php echo $keyword; ?>
                        <span ><?php echo $price; ?></span>
                        [库存：<?php echo $package['stock']; ?>]
                    </div>
                 <?php } ?>     <!-- endforeach -->

                <input type="hidden" id="pwStock" name="pwStock" value="">
                <input type="hidden" id="pwprice" name="pwprice" value="">
                <input type="hidden" id="payname" name="payname" value="">
            </div>
        </li>
        <div class="choose-num">
            <div class="tit">购买数量：</div>
            <div class="minus" onclick="changepaynum(1);"></div>
            <div class="num" id="divpaynum">1</div>
            <div class="plus" onclick="changepaynum(2);"></div>
            <input type="hidden" id="txtpaynum" name="txtpaynum" value="1"></div>
    </ul>
    <dl class="price-box">
        <dd>订单金额：
            <span class="red">￥</span>
            <span id="spddprice" class="red">0</span></dd>
        <dd style="display: none" >
            <input type="checkbox" id="score">
            <label for="score" class="">使用200积分抵扣2元</label></dd>
        <?php if($activity_cashed && $is_cashed): ?>
        <!--  优惠券  -->
        <dd class="red-packet flex" id="red-packet">
            <div class="txt">优惠券<span>(现金抵扣券)</span></div>
            <div class="num" id="divvolumeprice"><?php echo $cashed_list_count; ?>个可用</div>
            <span class="spsel"></span>
        </dd>
        <dd >实际支付：
            <span class="red">￥</span>
            <span class="red" id="spdiwang">0</span></dd>
        <?php endif; ?>
        <input type="hidden" id="hidvolumeid" name="hidvolumeid" value="">
        <input type="hidden" id="hidvolumeprice" name="hidvolumeprice" value="0">
        <input type="hidden" id="hidvolumeprice1" value="0">
        <dd>
            <input style="display: none" id="subdata1" type="submit"  value="提交">
            <input type="button" class="submit" value="提交" style="background: #ff7800;color: #fff;" onclick="order_confirm();"></dd>
    </dl>
</form>
<?php if($activity_cashed): ?>
<!-- 优惠券列表 -->
<div class="choose-redpacket">
    <div class="redpacket-btn">
        <div class="flex">
        <p>可选张数：<?php if($activity_cashed['max_use'] == 0): ?>不限<?php else: ?><?php echo $activity_cashed['max_use']; ?>张<?php endif; ?><br>最多抵扣金额：<?php if($activity_cashed['max_amount'] == 0): ?>不限<?php else: ?><?php echo $activity_cashed['max_amount']; ?>元<?php endif; ?></p>
        <a href="javascript:;" class="btn confirm" onclick="closevolume();">确认选择</a>
        </div>
        <div class="redpacket-rule-box">
            <div class="redpacket-rule-tit flex">
                <div class="redpacket-rule">现金券使用规则</div>
                <div class="arrow iconfont">&#xe60a;</div>
            </div>
            <ol class="redpacket-content" style="display: none">
                <li>一张现金券只能使用一次，不兑现，不找零；</li>
                <li>如取消的订单中使用了现金券，退回金额为实际支付金额，现金券退回到活动券。</li>
                <li>每张现金券都设有有效期，请在有效期内使用，逾期失效。</li>
            </ol>
        </div>
    </div>

    <?php if($cashed_list){ ?>
    <div class="choose-box">
        <?php foreach($cashed_list as $val){ ?>
        <div class="choose-item flex" onclick="choosevolume(<?php echo $val['id']; ?>,<?php echo $val['cashed_amount']; ?>,this)">
            <div class="amount">
                <div class="num"><span><?php echo $val['cashed_amount']; ?></span></div>
            </div>
            <div class="txt">
                <div class="info"><?php echo $val['receive_cashed_name']; ?></div>
                <div class="date"><?php echo $val['cashed_validity_time']; ?></div>
            </div>
            <span class="choose-icon"></span>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</div>
<?php endif; ?>
<div id="div_sub_m" style="display: none">

    <?php $re = $cms->GetSignupTempSub1($re1['selsignfrom']);
    if($re){ ?>
    <div style=" width: 100%;padding-bottom: 10px;position: relative;">
        <div onclick="javascript:delsub(this)" style="position: absolute;right: 10px;top: -5px; width: 20px;height: 20px;border-radius: 10px;border: 2px solid #999;text-align: center;background: #ddd url('/static/images/oa_ico-14.gif') center no-repeat;"></div>
        <table  style="width: 90%; border: 1px solid #ccc;padding: 5px; ">
            <tr>
                <td>
                    <?php

                    foreach($re as $k=>$vo){ ?>
                    <li  class="#data">
                        <div class="tit"><?php echo $vo['title']; ?>{index}：</div>
                        <div class="txt">
                            <?php
                            //1文本框 2数字框 3电话框 4邮件框 5下拉框 6多行文本框 7图片上传 8身份证
                            if($vo['chrtype']==5){ ?>
                            <select  name="feild_sub_<?php echo $vo['id']; ?>_{index}" class="feild" style="border: 0px; font-size: 16px;width: 100%;">
                                <option value="">请选择<?php echo $vo['title']; ?></option>
                                <?php
                                 $arrtmp=explode(",",$vo['chrvalue']);
                                 foreach($arrtmp as $k1=>$vo1) {
                                ?>
                                <option value="<?php echo $vo1; ?>"><?php echo $vo1; ?></option>
                                <?php } ?>
                            </select>
                            <?php } elseif($vo['chrtype']==6){ ?>
                            <textarea type="text" name="feild_sub_<?php echo $vo['id']; ?>}_{index}" style="width: 100%;height: 48px;border: 0px;line-height: 24px;font-size: 16px; "  class="feild" datatype="<?php echo $vo['chrtype']; ?>" ></textarea>
                            <?php } elseif($vo['chrtype']==7){ ?>
                            <img src="/static/images/addimg.jpg" id="img_sub_<?php echo $vo['id']; ?>_{index}" style="width: 0.5rem;height: 0.5rem;" onclick="javascript:selimg('feild_sub_<?php echo $vo['id']; ?>_{index}')" class="img">
                            <input style="display: none" id="feild_sub_<?php echo $vo['id']; ?>_{index}" type="file" name="feild_sub_<?php echo $vo['id']; ?>_{index}" value="上传图片" style="height:24px;" class="feild" datatype="<?php echo $vo['chrtype']; ?>" onchange="javascript:changeimg(this,'img_sub_<?php echo $vo['id']; ?>_{index}')" />
                            <?php } else { ?>
                            <input type="text" name="feild_sub_<?php echo $vo['id']; ?>_{index}" value="" class="feild" datatype="<?php echo $vo['chrtype']; ?>" />
                            <?php } ?>
                            <input style="display: none" name="feildname_sub_<?php echo $vo['id']; ?>_{index}" type="text" value="<?php echo $vo['title']; ?>∫<?php echo $vo['chrtype']; ?>" class="feildname">
                        </div>
                    </li>
                    <?php } ?>

                </td>
            </tr>
        </table>
    </div>
    <?php } ?>
</div>

<div class="gray-cover"></div>
<div class="order-confirm">
    <div class="head flex flex-middle">
        <p class="fx1">请确认您的报名信息</p>
        <span class="close-confirm" onclick="close_confirm();"></span>
    </div>
    <div class="txt">
        <div id="datainfo">
            <div class="item flex">
                <div class="tit">姓名：</div>
                <div class="info">方晓亮</div>
            </div>
        </div>
        <div class="btn flex">
            <a href="javascript:;" class="continue" onclick="javascript:submitedata();">继续提交</a>
            <a href="javascript:;" class="return" onclick="close_confirm();">返回修改</a>
        </div>
    </div>
</div>

<script type="text/javascript" src="/static/js/layer/layer.js"></script>
<script type="text/javascript">

    function  addsub(){
        var html_m=$("#div_sub_m").html();
        var reg = new RegExp("#data","g")
        html_m=html_m.replace(reg,"data");
        var index_x=$("#subindex").val();
        index_x=eval(index_x)+1;
        $("#subindex").val(index_x);
        reg = new RegExp("{index}","g")
        html_m=html_m.replace(reg,index_x);

        //rowindex=document.getElementById("tradd").rowIndex;
        $("#divsub").before(html_m);

        //$("#divsub").html($("#divsub").html()+html_m);
    }

    function  delsub(obj) {
        $(obj).parent().remove();
    }

    function changepaynum(obj) {
        var commonid = "5253";
        var maxpaynum = "0";
        if (maxpaynum == null || maxpaynum == "") {
            maxpaynum = 0;
        }
        var num = parseInt($("#txtpaynum").val());
        if (maxpaynum == 1) {
            layer.alert('该特价票每个身份证限购一张');
            return;
        } else if (commonid == 4755 || commonid == 4770 || commonid == 4860 || commonid == 5146 || commonid == 5148 || commonid == 5164) {
            layer.alert('该特价票每个身份证限购一张');
            return;
        } else if (maxpaynum > 1) {
            if (obj == 2 && num >= maxpaynum) {
                layer.alert('该特价票每个身份证限购' + maxpaynum + '张');
                return;
            } else if ((commonid == 5139 || commonid == 5149) && obj == 2 && num >= 5) {
                layer.alert('该特价票每个身份证限购5张');
                return;
            }
        }

        var totalkd = parseInt($("#spkd").html());
        <?php if($activity_cashed): ?>
        var maxvolumeprice = <?php echo $activity_cashed['max_amount']; ?>;//可使用最大金额
        var volumeprice = $("#hidvolumeprice").val() * 1;
        //如果不是不限时
        if(maxvolumeprice != 0) {
            if (volumeprice > maxvolumeprice) {
                volumeprice = maxvolumeprice * 1;
            }
        }
        $("#hidvolumeprice").val(volumeprice);
        <?php endif; ?>

            if (obj == 1) {
                if (commonid == 5957) {
                    if (num <= 2) {
                        layer.alert('您好！该套餐2张起售，谢谢');
                        return;
                    }
                }
                if (num <= 1) {
                    $("#txtpaynum").val(1);
                    $("#divpaynum").html(1);
                    if (commonid == 4134) {
                        $("#spyh").html(12);
                        $("#spddprice").html($("#pwprice").val() - 12 + totalkd);
                        $("#spdiwang").html($("#pwprice").val() - 12 + totalkd);
                        <?php if($activity_cashed): ?>
                        var spddprice = $("#spddprice").html();
                        if (spddprice > 0) {
                            if (spddprice <= volumeprice) {
                                $("#spdiwang").html(0.01);
                            } else {
                                $("#spdiwang").html((spddprice - volumeprice).toFixed(2));
                            }
                        }
                        <?php endif; ?>
                        } else {
                            $("#spddprice").html($("#pwprice").val().toFixed(2));
                            $("#spdiwang").html($("#pwprice").val().toFixed(2));
                            var spddprice = $("#spddprice").html();
                            <?php if($activity_cashed): ?>
                            if (spddprice > 0) {
                                if (spddprice <= volumeprice) {
                                    $("#spdiwang").html(0.01);
                                } else {
                                    $("#spdiwang").html((spddprice - volumeprice).toFixed(2));
                                }
                            }
                            <?php endif; ?>
                            }
                        } else {
                            $("#txtpaynum").val(num - 1);
                            $("#divpaynum").html(num - 1);
                            if (commonid == 4134) {
                                $("#spyh").html((num - 1) * 12);
                                $("#spdiwang").html($("#pwprice").val() * (num - 1) - ((num - 1) * 12) + totalkd);
                                $("#spddprice").html($("#pwprice").val() * (num - 1) - ((num - 1) * 12) + totalkd);
                                <?php if($activity_cashed): ?>
                                var spddprice = $("#spddprice").html();
                                if (spddprice > 0) {
                                    if (spddprice <= volumeprice) {
                                        $("#spdiwang").html(0.01);
                                    } else {
                                        $("#spdiwang").html((spddprice - volumeprice).toFixed(2));
                                    }
                                }
                                <?php endif; ?>
                                } else {
                                    $("#spdiwang").html(($("#pwprice").val() * (num - 1)).toFixed(2));
                                    $("#spddprice").html(($("#pwprice").val() * (num - 1)).toFixed(2));
                                    <?php if($activity_cashed): ?>
                                    var spddprice = $("#spddprice").html();
                                    if (spddprice > 0) {
                                        if (spddprice <= volumeprice) {
                                            $("#spdiwang").html(0.01);
                                        } else {
                                            $("#spdiwang").html((spddprice - volumeprice).toFixed(2));
                                        }
                                    }
                                    <?php endif; ?>
                                    }
                                }
                            } else if (obj == 2) {
                                $("#txtpaynum").val(num + 1);
                                $("#divpaynum").html(num + 1);
                                if (commonid == 4134) {
                                    $("#spyh").html((num + 1) * 12);
                                    $("#spdiwang").html($("#pwprice").val() * (num + 1) - ((num + 1) * 12) + totalkd);
                                    $("#spddprice").html($("#pwprice").val() * (num + 1) - ((num + 1) * 12) + totalkd);
                                    <?php if($activity_cashed): ?>
                                    var spddprice = $("#spddprice").html();
                                    if (spddprice > 0) {
                                        if (spddprice <= volumeprice) {
                                            $("#spdiwang").html(0.01);
                                        } else {
                                            $("#spdiwang").html((spddprice - volumeprice).toFixed(2));
                                        }
                                    }
                                    <?php endif; ?>
                                    } else {
                                        $("#spdiwang").html(($("#pwprice").val() * (num + 1)).toFixed(2));
                                        $("#spddprice").html(($("#pwprice").val() * (num + 1)).toFixed(2));
                                        <?php if($activity_cashed): ?>
                                        var spddprice = $("#spddprice").html();
                                        if (spddprice > 0) {
                                            if (spddprice <= volumeprice) {
                                                $("#spdiwang").html(0.01);
                                            } else {
                                                $("#spdiwang").html((spddprice - volumeprice).toFixed(2));
                                            }
                                        }
                                        <?php endif; ?>
                                        }
                                    }
                                }
                                function submitedata(){

                                    var data = new FormData(document.getElementById("frm1"));
                                    layer.load(1, {
                                        shade: [0.1,'#fff'] //0.1透明度的白色背景
                                    });
                                    $.ajax({
                                            type: 'post',
                                            url: "/<?php echo $sitecode; ?>/signup_post/<?php echo $id; ?>",
                                            dataType: 'json',
                                            data: data,
                                            contentType: false, //不设置内容类型
                                            processData: false, //不处理数据
                                            success: function (data) {
                                                //关闭继续提交弹窗
                                                close_confirm()
                                                layer.closeAll('loading');

                                                //格式化data数据
                                                data=JSON.parse(data)
                                                //用户再次提交订单
                                                //追加元素
                                                //console.log(data)
                                                //console.log(data.order_id)
                                                var htmlappend="<input id='orderid' type=\"hidden\" name=\"order_id\" value=\""+data.order_id+"\">"
                                                $("#txtpaynum").after(htmlappend)
                                                if(data.res==1){
                                                    if(data.ischarge==2 && data.flag==1 && data.price > 0) {
                                                        jsondata=$.parseJSON(data.data)
                                                        wx.config({
                                                            //debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                                                            appId: data.config["appId"], // 必填，公众号的唯一标识
                                                            timestamp:data.config["timestamp"] , // 必填，生成签名的时间戳
                                                            nonceStr: data.config["nonceStr"], // 必填，生成签名的随机串
                                                            signature:data.config["signature"],// 必填，签名
                                                            jsApiList: ['chooseWXPay']// 必填，需要使用的JS接口列表

                                                        });
                                                        wx.ready(function () {
                                                            wx.chooseWXPay({
                                                                timestamp: jsondata["timeStamp"], // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                                                                nonceStr: jsondata["nonceStr"], // 支付签名随机串，不长于 32 位
                                                                package: jsondata["package"], // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
                                                                signType: jsondata["signType"], // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                                                                paySign: jsondata["paySign"], // 支付签名
                                                                success: function (res) {
                                                                    if(data.is_cashed && data.activity_cashed_set && data.activity_cashed_set.cashed_plan_id > 0){
                                                                        window.location="/"+data.sitecode+"/share/"+data.order_id+"/0"
                                                                    }else{
                                                                        window.location="/"+data.sitecode+"/detail/"+data.dataID
                                                                    }
                                                                },

                                                            })

                                                        })
                                                        flag=IsPC()
                                                        if(!flag){
                                                            layer.confirm('您好，请用手机登录微信，进入公众号会员中心完成支付，谢谢！',
                                                                {
                                                                    btn: ["关闭"] //按钮)
                                                                })
                                                        }

                                                    }else{
                                                        if(data.flag==2) {
                                                            layer.confirm(
                                                                data.errmsg,
                                                                {
                                                                    btn:['关闭'],
                                                                    btn1: function(index, layero){
                                                                        window.location = "/" + data.sitecode + "/detail/" + data.dataID;
                                                                        return false;
                                                                    }});
                                                            if(data.err_arr.length != 0.){
                                                                layer.confirm(
                                                                    data.err_arr[0]["err"],
                                                                    {
                                                                        btn:['关闭'],
                                                                        btn1: function(index, layero){
                                                                            window.location="/"+data.sitecode+"/againorder/"+data.order_id;
                                                                            return false;
                                                                        }});
                                                            }
                                                        } else{
                                                            layer.confirm('报名成功！',{btn:['关闭'],btn1: function(index, layero){ window.location="/"+data.sitecode+"/detail/"+data.dataID}});
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    );
                                }
                                function close_confirm(){
                                    $(".gray-cover,.order-confirm").hide();
                                }
                                $(".pwclass").click(function(){
                                    $(this).addClass("pwon").siblings().removeClass("pwon");
                                    $("#pwStock").val($(this).attr('Stock'));
                                    $("#pwprice").val($(this).attr('price'));
                                    $("#payname").val($(this).attr('data'));
                                    loadprice();
                                });
                                $(".pwclass").eq(0).click();
                                function IsPC() {
                                    var userAgentInfo = navigator.userAgent;
                                    var Agents = ["Android", "iPhone",
                                        "SymbianOS", "Windows Phone",
                                        "iPad", "iPod"];
                                    var flag = false;
                                    for (var v = 0; v < Agents.length; v++) {
                                        if (userAgentInfo.indexOf(Agents[v]) > -1) {
                                            flag = true;
                                            break;
                                        }
                                    }
                                    return flag;
                                }
                                function submitedata(){
                                    //如果是在pc端提示并结束
                                    //    var ua = window.navigator.userAgent.toLowerCase();
                                    //    if (ua.match(/MicroMessenger/i) != 'micromessenger') {
                                    //        close_confirm()
                                    //        layer.confirm('请在微信移动客户端中打开',
                                    //            {
                                    //                btn: ["关闭"] //按钮)
                                    //            })
                                    //        return false;
                                    //    }
                                    //    var ua = window.navigator.userAgent.toLowerCase();
                                    flag=IsPC()
                                    if(!flag){
                                        close_confirm()
                                        layer.confirm('请在微信移动客户端中打开',
                                            {
                                                btn: ["关闭"] //按钮)
                                            })
                                        return false
                                    }
                                    var data = new FormData(document.getElementById("frm1"));
                                    layer.load(1, {
                                        shade: [0.1,'#fff'] //0.1透明度的白色背景
                                    });
                                    $.ajax({
                                            type: 'post',
                                            url: "/<?php echo $sitecode; ?>/signup_post/<?php echo $id; ?>",
                                            dataType: 'json',
                                            data: data,
                                            contentType: false, //不设置内容类型
                                            processData: false, //不处理数据
                                            success: function (data) {
                                                //关闭继续提交弹窗
                                                close_confirm()
                                                layer.closeAll('loading');
                                                //格式化data数据
                                                data=JSON.parse(data)
                                                //用户再次提交订单
                                                //追加元素
                                                // console.log(data)
                                                //console.log(data.order_id)
                                                var htmlappend="<input id='orderid' type=\"hidden\" name=\"order_id\" value=\""+data.order_id+"\">"
                                                $("#txtpaynum").after(htmlappend)
                                                if(data.res==1){
                                                    if(data.ischarge==2 && data.flag==1 && data.price > 0) {
                                                        jsondata=$.parseJSON(data.data)
                                                        wx.config({
                                                            //debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                                                            appId: data.config["appId"], // 必填，公众号的唯一标识
                                                            timestamp:data.config["timestamp"] , // 必填，生成签名的时间戳
                                                            nonceStr: data.config["nonceStr"], // 必填，生成签名的随机串
                                                            signature:data.config["signature"],// 必填，签名
                                                            jsApiList: ['chooseWXPay']// 必填，需要使用的JS接口列表

                                                        });
                                                        wx.ready(function () {
                                                            wx.chooseWXPay({
                                                                timestamp: jsondata["timeStamp"], // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                                                                nonceStr: jsondata["nonceStr"], // 支付签名随机串，不长于 32 位
                                                                package: jsondata["package"], // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
                                                                signType: jsondata["signType"], // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                                                                paySign: jsondata["paySign"], // 支付签名
                                                                success: function (res) {
                                                                    if(data.is_cashed && data.activity_cashed_set && data.activity_cashed_set.cashed_plan_id > 0){
                                                                        window.location="/"+data.sitecode+"/share/"+data.order_id+"/0"
                                                                    }else{
                                                                        window.location="/"+data.sitecode+"/detail/"+data.dataID
                                                                    }
                                                                }
                                                            })

                                                        })

                                                    }else{
                                                        if(data.flag==2) {
                                                            layer.confirm(
                                                                data.errmsg,
                                                                {
                                                                    btn:['关闭'],
                                                                    btn1: function(index, layero){
                                                                        window.location = "/" + data.sitecode + "/detail/" + data.dataID;
                                                                        return false;
                                                                    }});
                                                            if(data.err_arr){
                                                                layer.confirm(
                                                                    data.err_arr[0]["err"],
                                                                    {
                                                                        btn:['关闭'],
                                                                        btn1: function(index, layero){
                                                                            window.location="/"+data.sitecode+"/againorder/"+data.order_id;
                                                                            return false;
                                                                        }});
                                                            }
                                                        } else{
                                                            layer.confirm('报名成功！',{btn:['关闭'],btn1: function(index, layero){ window.location="/"+data.sitecode+"/detail/"+data.dataID}});
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    );
                                }



                                function loadprice() {

                                    $("#spdiwang").html(($("#pwprice").val() * $("#txtpaynum").val()).toFixed(2));
                                    $("#spddprice").html(($("#pwprice").val() * $("#txtpaynum").val()).toFixed(2));
                                    <?php if($activity_cashed): ?>
                                    var maxvolumeprice = <?php echo $activity_cashed['max_amount']; ?>;//可使用最大金额
                                    var volumeprice = $("#hidvolumeprice").val() * 1;
                                    //如果不是不限时
                                    if(maxvolumeprice != 0) {
                                        if (volumeprice > maxvolumeprice) {
                                            volumeprice = maxvolumeprice * 1;
                                        }
                                    }
                                    $("#hidvolumeprice").val(volumeprice);
                                    var spddprice = $("#spddprice").html();
                                    if (spddprice > 0) {
                                        if (spddprice <= volumeprice) {
                                            $("#spdiwang").html(0.01);
                                        } else {
                                            $("#spdiwang").html((spddprice - volumeprice).toFixed(2));
                                        }
                                    }
                                    <?php endif; ?>
                                    }
                                    loadprice();

                                    function  checkdata(datavalue,datatype,ismust) {
                                        var json ={"state":1,"msg" : "" };
                                        if(datavalue=="" && ismust==1)
                                        {
                                            json.state=0;
                                            json.msg="不能为空";
                                            return json;
                                        }
                                        //1文本框 2数字框 3电话框 4邮件框 5下拉框 6多行文本框 7图片上传 8身份证 9 关联姓名框 10关联电话号码框 11关联邮箱框 12关联邮寄地址框 13关联身份证框
                                        switch(datatype)
                                        {
                                            case "2":
                                                var reg =/^(-?\d+)(\.\d+)?$/;
                                                if(reg.test(datavalue)==false)
                                                {
                                                    json.state=0;
                                                    json.msg="必须输入数字";
                                                }
                                                break;
                                            case "3":
                                            case "10":
                                                var isMobile = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1})|(14[0-9]{1}))+\d{8})$/;
                                                var isPhone = /^(?:(?:0\d{2,3})-)?(?:\d{7,8})(-(?:\d{3,}))?$/;
                                                if(isMobile.test(datavalue)==false && isPhone.test(datavalue)==false)
                                                {
                                                    json.state=0;
                                                    json.msg="输入格式不正确";
                                                }
                                                break;
                                            case "4":
                                            case "11":
                                                var reg =/^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,4}$/;
                                                if(reg.test(datavalue)==false)
                                                {
                                                    json.state=0;
                                                    json.msg="输入格式不正确";
                                                }
                                                break;
                                            case "8":
                                            case "13":
                                                var reg =/^[1-9]\d{5}(18|19|20)\d{2}((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/;
                                                if(reg.test(datavalue)==false)
                                                {
                                                    json.state=0;
                                                    json.msg="输入格式不正确";
                                                }
                                                break;
                                        }
                                        return json;
                                    }

</script>
<script>
    function selimg(sname){
        $("#"+sname).click();
    }
    function changeimg(obj,imgname){
        var objUrl = getObjectURL(obj.files[0]) ;//获取文件信息
        console.log("objUrl = "+objUrl);
        if (objUrl) {
            $("#"+imgname).attr("src", objUrl);
        }
    } ;
    function getObjectURL(file) {
        var url = null;
        if (window.createObjectURL!=undefined) {
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url ;
    }
</script>
<script type="text/javascript">
    $(function () {
        $("#red-packet").on("click", function () {
            $(".choose-redpacket").show();
            $("body").addClass("open-redpacket");
        })
        $(".close-redpacket").on("click", function () {
            $(".choose-redpacket").hide();
            $("body").removeClass("open-redpacket");
        })

        var flag = 0;
        $('.redpacket-rule-tit').on("click",function () {
            if(flag == 0){
                $('.redpacket-content').show();
                $('.redpacket-rule-tit .arrow').html('&#xe614;')
                flag = 1;
            }else{
                $('.redpacket-content').hide();
                $('.redpacket-rule-tit .arrow').html('&#xe60a;')
                flag = 0;
            }
            
        })
    })

    window.issel = true;

    //如果该活动可用现金券
    <?php if($activity_cashed): ?>
    function choosevolume(obj, obj1, obj2) {
        var volumeids = $("#hidvolumeid").val();
        if (volumeids.indexOf(',' + obj + ',') > -1) { //已选过的就移除
            volumeids = volumeids.replace(',' + obj + ',', ',');
            if (volumeids == ",") {
                volumeids = "";
            }
            $("#hidvolumeid").val(volumeids);
            var volumeprice = $("#hidvolumeprice1").val() * 1 - obj1 * 1;
            $("#hidvolumeprice").val(volumeprice);
            $("#hidvolumeprice1").val(volumeprice);
            $("#divvolumeprice").html("-" + volumeprice);
            $(obj2).removeClass("on");
            issel = true;
        } else {
            var maxvolumeprice = <?php echo $activity_cashed['max_amount']; ?>;//可使用最大金额
            if(maxvolumeprice != 0) {
                if (issel == false) {
                    layer.alert('您选择的现金券总金额已经超过活动最大抵扣额，不需要继续选择');
                    return;
                }
            }
            var volumenum = <?php echo $activity_cashed['max_use']; ?>;//可使用最大数量
            //不等于不限时
            if(volumenum != 0){
                if (volumeids.split(',').length - 1 > volumenum && volumenum != 0) {
                    layer.alert('该活动最多可使用' + volumenum + '张现金券');
                    return;
                }
            }
            maxvolumeprice = maxvolumeprice * 1;
            var volumeprice = $("#hidvolumeprice1").val() * 1 + obj1 * 1;
            if(maxvolumeprice != 0) {
                if (volumeprice > maxvolumeprice) {
                    layer.alert('该活动最多可抵扣' + maxvolumeprice + '元，您已选择' + volumeprice.toFixed(2) + '元，金额超出部分也将抵扣完');
                    //return;
                    //volumeprice=maxvolumeprice*1;
                    issel = false;
                }
            }
            if (volumeids == "") {
                volumeids = ',' + obj + ',';
            } else {
                volumeids += obj + ",";
            }
            $("#hidvolumeid").val(volumeids);
            $("#hidvolumeprice").val(volumeprice.toFixed(2));
            $("#hidvolumeprice1").val(volumeprice.toFixed(2));
            $("#divvolumeprice").html("-" + volumeprice.toFixed(2));
            $(obj2).addClass("on");
        }
        var spddprice = $("#spddprice").html();
        if (obj > 0) {
            if (spddprice > 0) {
                if (spddprice <= volumeprice) {
                    $("#spdiwang").html(0.01);
                } else {
                    $("#spdiwang").html((spddprice - volumeprice).toFixed(2));
                }
            }
        } else {
            $("#spdiwang").html(spddprice);
        }
    }
    function closevolume() {
        var maxvolumeprice = <?php echo $activity_cashed['max_amount']; ?>;//可使用最大金额
        var volumeprice = $("#hidvolumeprice").val() * 1;
        //如果不是不限时
        if(maxvolumeprice != 0) {
            if (volumeprice > maxvolumeprice) {
                volumeprice = maxvolumeprice * 1;
            }
        }
        $("#hidvolumeprice").val(volumeprice.toFixed(2));
        $("#divvolumeprice").html("-" + volumeprice.toFixed(2));
        var spddprice = $("#spddprice").html();
        if (spddprice > 0) {
            if (spddprice <= volumeprice) {
                $("#spdiwang").html(0.01);
            } else {
                $("#spdiwang").html((spddprice - volumeprice).toFixed(2));
            }
        }

        $(this).addClass("on").siblings().removeClass("on");
        $(".choose-redpacket").hide();
        $("body").removeClass("open-redpacket");
    }
    <?php endif; ?>
</script>
</body>
</html>