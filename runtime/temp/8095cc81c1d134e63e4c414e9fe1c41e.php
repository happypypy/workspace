<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"D:\workspace\work\public/../application/admin\view\sitemanage\sitedeal.html";i:1561691683;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
<link href="/static/css/page.css" rel="stylesheet" type="text/css" />
<link href="/static/js/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="/static/js/del-checked.js"></script>
<script type="text/javascript" src="/static/js/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="/static/js/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="/static/js/layer/layer.js"></script>
<script type="text/javascript" src="/static/js/jquery.form.js"></script>
<script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
</head>
<body>
<div class="oa_pop">
    <div class="oa_pop-main" id="part1">
        <div style="height: 6px"></div>
        <div class="oa_title clearfix">
            <span class="oa_ico-right"></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            添加站点
        </div>
        <div class="oa_edition">
            <form action="<?php echo url('sitemanage/sitepost'); ?>" id="config-post" method="post">
                <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                    <tr>
                        <td width="150" class="oa_cell-left"><span style="color: red;">*</span>&nbsp;站点名称：</td>
                        <td class="oa_cell-right"><input name="site_name" type="text" value="<?php echo $site_info['site_name']; ?>" class="oa_input-200" /></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left"><span style="color: red;">*</span>&nbsp;站点代号：</td>
                        <td class="oa_cell-right"><input name="site_code" type="text" value="<?php echo $site_info['site_code']; ?>" class="oa_input-200" /></td>
                    </tr>

                    <tr style="display: none;">
                        <td width="150" class="oa_cell-left">站点类型：</td>
                        <td class="oa_cell-right">
                            <select name="site_type" class="oa_input-100">
                                <?php if($site_info['site_type'] == 1): ?><option value="1">主站</option><?php endif; if($site_info['site_type'] == 2): ?><option value="2">子站</option><?php endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr style="display: none">
                        <td width="150" class="oa_cell-left">域名：</td>
                        <td class="oa_cell-right"><input name="realm_name" type="text" value="<?php echo $site_info['realm_name']; ?>" class="oa_input-200" /></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">过期时间：</td>
                        <td class="oa_cell-right">
                            <input name="expiretime" id="expiretime"  type="text" value="<?php echo $site_info['expiretime']; ?>" class="oa_input-200" /><script language='JavaScript'>seltime("expiretime","YYYY-MM-DD")</script>
                        </td>
                    </tr>

                    <tr>
                        <td width="150" class="oa_cell-left">微信appId：</td>
                        <td class="oa_cell-right"><input name="appid" type="text" value="<?php echo $site_info['appid']; ?>" class="oa_input-200" />
                          <br> <span>开发者中心-配置项-AppID(应用ID)</span></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">微信appSecret：</td>
                        <td class="oa_cell-right"><input name="appsecret" type="text" value="<?php echo $site_info['appsecret']; ?>" class="oa_input-200" />
                            <br> <span>开发者中心-配置项-AppSecret(应用密钥)</span></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">微信token：</td>
                        <td class="oa_cell-right"><input name="token" id="token" type="text" value="<?php echo $site_info['token']; ?>" class="oa_input-200" />
                            <br> <span>开发者中心-配置项-服务器配置-Token(令牌)</span><a href="javascript:createToken();" id="token_id">生成随机token</a></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">微信$encodingAESKey：</td>
                        <td class="oa_cell-right"><input name="encodingaeskey" type="text" value="<?php echo $site_info['encodingaeskey']; ?>" class="oa_input-200" />
                            <br> <span>开发者中心-配置项-服务器配置-EncodingAESKey(消息加解密密钥)</span></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">验证文件：</td>
                        <td class="oa_cell-right">
                            <input id="verifyfilepath" name="verifyfilepath" type="text" value="<?php echo $site_info['verifyfilepath']; ?>" class="oa_input-300" />
                            <br><input onclick="GetUploadify(1,'verifyfilepath','verify','undefined','*.txt');" type="button" value="上传验证文件"/>
                            <br/><br/><span>公众号设置-功能设置-JS接口安全域名-设置 注意事项第三点中的文件</span></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">商户号：</td>
                        <td class="oa_cell-right"><input name="mchid" type="text" value="<?php echo $site_info['mchid']; ?>" class="oa_input-200" />
                            <br> <span>微信支付商户号</span></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">支付API密钥：</td>
                        <td class="oa_cell-right"><input name="paykey" type="text" value="<?php echo $site_info['paykey']; ?>" class="oa_input-200" />
                            <br> <span>API安全-API密钥 中设置</span></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">证书 apiclient_cert.p12：</td>
                        <td class="oa_cell-right"><input id="cainfo" name="cainfo" type="text" value="<?php echo $site_info['cainfo']; ?>" class="oa_input-300" />
                            <br> <input onclick="GetUploadify(1,'cainfo','cart','undefined','*.p12');" type="button" value="上传证书"/>
                            <span>API安全-API证书 中下载</span></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">证书 apiclient_cert.pem：</td>
                        <td class="oa_cell-right"><input id="sslcertpath" name="sslcertpath" type="text" value="<?php echo $site_info['sslcertpath']; ?>" class="oa_input-300" />
                            <br><input onclick="GetUploadify(1,'sslcertpath','cart','undefined','*.pem');" type="button" value="上传证书"/>
                            <span>API安全-API证书 中下载</span></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">证书 apiclient_key.pem：</td>
                        <td class="oa_cell-right"><input id="sslkeypath" name="sslkeypath" type="text" value="<?php echo $site_info['sslkeypath']; ?>" class="oa_input-300" />
                            <br><input onclick="GetUploadify(1,'sslkeypath','cart','undefined','*.pem');" type="button" value="上传证书"/>
                            <span>API安全-API证书 中下载</span></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">短信签名：</td>
                        <td class="oa_cell-right"><input name="sms_sign" type="text" value="<?php echo $site_info['sms_sign']; ?>" class="oa_input-200" /></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">备注：</td>
                        <td class="oa_cell-right"><input name="remark" type="text" value="<?php echo $site_info['remark']; ?>" class="oa_input-200" /></td>
                    </tr>

                    <tr>
                        <td width="150" class="oa_cell-left">排序：</td>
                        <td class="oa_cell-right"><input name="order" type="text" value="<?php echo $site_info['order']; ?>" class="oa_input-200" /></td>
                    </tr>

                    <tr>
                        <td width="150" class="oa_cell-left">是否启用：</td>
                        <td class="oa_cell-right">
                            <input name="is_use" type="checkbox" value="1" <?php echo $site_info['is_use']==1?"checked":""; ?> />
                        </td>
                    </tr>

                    <tr>
                        <td width="150" class="oa_cell-left">支付功能测试：</td>
                        <td class="oa_cell-right">
                            <?= $site_info['payment_verification'] == 1 ? '支付测试成功' : '测试未通过'; ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input type="hidden" name="action" value="<?php echo $site_info['action']; ?>"/>
                            <?php if($site_info['action'] == 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $site_info['id']; ?>" >
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="60px" style="padding: 10px">
                            <input type="hidden" name="key" value="<?php echo $key; ?>"/>
                            <input type="submit" value="确定" class="oa_input-submit"/>
                        </td>
                        <td>
                            <?php if($site_info['payment_verification'] == 0): ?>
                                <input type="button" onclick="testPay()" id="test-pay" value="支付功能测试" class="oa_input-submit"/>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <!-- 二维码页面 -->
    <div class="oa_bottom clearfix" style="display: none;" id="part2">
        <div class="oa_bottom-bottom" style="text-align: center;">
            <em>
                <img id="pay_qrcode" src="" style="width: 12rem;display: none;" alt="支付测试二维码" />
            </em>
            <br/>
            <label id="str_pay_qrcode" style="display: none">请扫描微信二维码进行支付</label>
            <br/><br/>
            <input value="测试完成" type="button" onclick="recharge_complete()" class="oa_input-submit" />
            <!-- <input value="返回上一步" type="button" onclick="back()" class="oa_input-submit" /> -->
        </div>
    </div>
</div>
<br>
<br>
<script language="JavaScript">
    function seltime(id,timeformat) {
        $('#'+id).daterangepicker(
            {
                format:timeformat,
                singleDatePicker: true,
                showDropdowns: true,
                minDate:'2008-04-18',
                maxDate:'2030-01-01',
                startDate: $('#'+id).val()==""?getNow():$('#'+id).val(),
                timePicker : timeformat.indexOf("HH")>0, //是否显示小时和分钟
                timePickerIncrement:1,//time选择递增数
                timePicker12Hour : false, //是否使用12小时制来显示时间

                locale : {
                    applyLabel : '确定',
                    cancelLabel : '取消',
                    fromLabel : '起始时间',
                    toLabel : '结束时间',
                    customRangeLabel : '自定义',
                    daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
                    monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月','七月', '八月', '九月', '十月', '十一月', '十二月' ],
                    firstDay : 1
                }
            }
        );}
        
    function createToken() {
        $.ajax({
            url:"<?php echo url('Admin/sitemanage/createToken'); ?>",
            type:"get",
            dataType:"json",
            success:function(obj){
                $("#token_id").hide();
                $("#token").val(obj.token);
            }
        })
    }



    /**
     * 支付功能测试
     */
    var t;
    var waitTime = 0;
    function testPay(objThis) {
        $("#config-post").submit(function() {
            alert(2);
            return false;
        });
        var url = "/admin/sitemanage/testPay";
        if (waitTime != 0) {
            $("#part1").hide();
            $("#part2").show();
        } else {
            var msg = "本次支付测试将花费0.01元，确认支付？";
            layer.confirm(msg, {
                btn: ['确定','取消'] //按钮
            }, function() {
                layer.closeAll();
                $(objThis).attr("disabled", true);
                $("#config-post").ajaxSubmit({
                    url: url,
                    type: "post",
                    dataType: 'json',
                    success: function (obj) {
                        if (obj.status === "success") {
                            $("#part1").hide();
                            $("#part2").show();
                            waitTime = 60;
                            $(objThis).attr("disabled", false);
                            setTimeout(function () {
                                // $(objThis).val("支付功能测试");
                                clearInterval(t);
                            }, 61000);
                            t = setInterval(function () {
                                waitTime--;
                                $(objThis).val("继续支付(" + (waitTime) + "s后可重新生成订单)");
                            }, 1000);
                            $("#pay_qrcode").show();
                            $("#str_pay_qrcode").show();
                            $("#pay_qrcode").attr("src", obj.pay_url);
                        } else {
                            $(objThis).attr("disabled", false);
                            layer.alert(obj.msg, {icon: 2}, function (index) {
                                layer.close(index);
                            });
                        }
                    },
                    error: function (msg) {
                        $(objThis).attr("disabled", false);
                        layer.alert('操作失败', {icon: 2}, function (index) {
                            layer.close(index);
                            // location.reload();
                        });
                    }
                })
            });
        }
        return false;
    }

    function back() {
        $("#part1").show();
        $("#part2").hide();
    }

    function recharge_complete() {
        $("#part1").show();
        $("#part2").hide();
        window.location.reload();
    }
    function closewin()
    {
        CloseDiv();
        GetOpenerWin().empty();
    }


</script>
</body>
</html>