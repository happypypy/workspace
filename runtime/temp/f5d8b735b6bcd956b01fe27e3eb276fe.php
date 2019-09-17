<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:68:"D:\workspace\work\public/../application/admin\view\order\refund.html";i:1564559667;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>报名管理</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <script type="text/javascript" src="/static/js/ContorlValidator.js"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>

</head>
<body>
<div class="oa_pop">
    <div class="oa_pop-main">
        <div style="height: 6px"></div>
        <div class="oa_title clearfix">
            <span class="oa_ico-right"></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            其本信息        </div>
        <div class="oa_edition" style="margin-bottom: 5px">
                <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                   <tr>
                        <td width="150" class="oa_cell-left">订单号：</td>
                        <td class="oa_cell-right"><?php echo $datainfo['ordersn']; ?></td>
                    </tr>
                   <tr>
                        <td width="150" class="oa_cell-left">报名人姓名：</td>
                        <td class="oa_cell-right"><?php echo $datainfo['chrusername']; ?></td>
                    </tr>
                   <tr>
                        <td width="150" class="oa_cell-left">活动名称：</td>
                        <td class="oa_cell-right"><?php echo $datainfo['chrtitle']; ?></td>
                    </tr>
                   <tr>
                        <td width="150" class="oa_cell-left">活动时间：</td>
                        <td class="oa_cell-right"><?php echo $datainfo['dtstart']; ?> 至 <?php echo $datainfo['dtend']; ?>
                        </td>
                   </tr>
                   <tr>
                        <td width="150" class="oa_cell-left">商务名称：</td>
                        <td class="oa_cell-right"><?php echo $datainfo['marketname']; ?></td>
                    </tr>
                   <tr>
                        <td width="150" class="oa_cell-left">购买的套餐名称：</td>
                       <td><?php echo $datainfo['payname']; ?></td>
                    </tr>
                   <tr>
                        <td width="150" class="oa_cell-left">订单总价格：</td>
                        <td class="oa_cell-right"><?php echo $datainfo['price']; ?></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">订单状态：</td>
                        <td class="oa_cell-right"><?php echo $order_state[$datainfo['state']]; ?></td>
                    </tr>
                   <tr>
                        <td width="150" class="oa_cell-left">创建时间：</td>
                        <td class="oa_cell-right"><?php echo $datainfo['dtcreatetime']; ?></td>
                    </tr>

                </table>
        </div>

        <div class="oa_title clearfix">
        <span class="oa_ico-right"></span>
        <span class="oa_title-btn"></span>
        <span class="oa_ico-left"></span>
        报名表单        </div>
        <div class="oa_edition" style="margin-bottom: 5px">
            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                <?php
                    $row2= explode("☆", $datainfo['txtfield']);
                    $row1= explode("☆", $datainfo['txtdata']);
                    foreach($row1 as $k=>$vo){
                        $arr= explode("∫", $row2[$k]);
                        $datatype=1;
                        $datafield=$arr[0];
                        if(count($arr)>1)
                        {
                        $datatype=$arr[1];
                        }
                ?>
                <tr>
                    <td width="150" class="oa_cell-left"><?php echo $datafield; ?>：</td>
                    <td class="oa_cell-right">
                        <?php
                                if($datatype==7)
                                    echo '<a href="'.$row1[$k].'" target="_blank"> <img src="'.$row1[$k].'" style="border: 0px;height: 50px;padding: 1px;"  /></a>';
                        else
                        echo $row1[$k];
                        ?>

                    </td>

                </tr>
                <?php } if($datainfo['intflag']!=1  && $datainfo['intflag']!=4 && $datainfo['intflag']!=5  && $datainfo['intflag']!=6) { ?>
                <tr>
                    <td width="150" class="oa_cell-left">审批意见：</td>
                    <td class="oa_cell-right"><textarea name="checkinfo" rows="4" style="width: 400px;"></textarea></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding:5px;">
                        <?php if($datainfo['state']==1) { ?>
                        <input type="hidden" value="3" name="intflag" id="intflag" />
                        <input type="submit" onclick="javascript:$('#intflag').val('5')" value="审批通过">
                        <?php if($datainfo['intflag']!=3) { ?>
                        <input type="submit" value="审批不通过">
                        <?php }} ?>
                        <input type="submit" onclick="javascript:$('#intflag').val('4')" value="取消报名">
                    </td>
                </tr>
                <?php } ?>

            </table>
        </div>

        <?php if(!empty($datainfo['refundsn']) || !empty($datainfo['refundremark'])) { ?>
        <div class="oa_title clearfix">
            <span class="oa_ico-right"></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            退款记录        </div>
        <div class="oa_edition" style="margin-bottom: 5px">
            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                <tr>
                    <td width="150" class="oa_cell-left">退款的单号：</td>
                    <td class="oa_cell-right"><?php echo !empty($datainfo['refundsn'])?$datainfo['refundsn']:"<span style='color: red'>申请中</span>"; ?></td>
                </tr>
                <?php if(!empty($datainfo['dtwxrefundtime'])){ ?>
                <tr>
                    <td width="150" class="oa_cell-left">退款时间：</td>
                    <td class="oa_cell-right"><?php echo $datainfo['dtwxrefundtime']; ?></td>
                </tr>
                <?php } if(!empty($datainfo['refundprice'])){ ?>
                <tr>
                    <td width="150" class="oa_cell-left">退款金额：</td>
                    <td class="oa_cell-right"><?php echo $datainfo['refundprice']; ?></td>
                </tr>
                <?php } if(!empty($datainfo['refundremark'])){ ?>
                <tr>
                    <td width="150" class="oa_cell-left">退款原因：</td>
                    <td class="oa_cell-right"><?php echo $datainfo['refundremark']; ?></td>
                </tr>
                <?php } if(!empty($datainfo['refundremark1'])){ ?>
                <tr>
                    <td width="150" class="oa_cell-left">后台备注：</td>
                    <td class="oa_cell-right"><?php echo $datainfo['refundremark1']; ?></td>
                </tr>
                <?php } if(!empty($datainfo['refundpic'])){ ?>
                <tr>
                    <td width="150" class="oa_cell-left">图片：</td>
                    <td class="oa_cell-right"><img src="<?php echo $datainfo['refundpic']; ?>" height="60"></td>
                </tr>
                <?php } if(!empty($datainfo['refundsn2']) || !empty($datainfo['refundmsg2'])) { ?>
                <tr>
                    <td width="150" class="oa_cell-left">退款的单号2：</td>
                    <td class="oa_cell-right"><?php echo !empty($datainfo['refundsn2'])?$datainfo['refundsn2']:"<span style='color: red'>申请中</span>"; ?></td>
                </tr>
                <?php  if(!empty($datainfo['dtwxrefundtime2'])){ ?>
                <tr>
                    <td width="150" class="oa_cell-left">退款时间2：</td>
                    <td class="oa_cell-right"><?php echo $datainfo['dtwxrefundtime2']; ?></td>
                </tr>
                <?php } if(!empty($datainfo['refundprice2'])){ ?>
                <tr>
                    <td width="150" class="oa_cell-left">退款金额2：</td>
                    <td class="oa_cell-right"><?php echo $datainfo['refundprice2']; ?></td>
                </tr>
                <?php } if(!empty($datainfo['refundmsg2'])){ ?>
                <tr>
                    <td width="150" class="oa_cell-left">备注信息2：</td>
                    <td class="oa_cell-right"><?php echo $datainfo['refundmsg2']; ?></td>
                </tr>
                <?php } if(!empty($datainfo['refundremark2'])){ ?>
                <tr>
                    <td width="150" class="oa_cell-left">后台备注：</td>
                    <td class="oa_cell-right"><?php echo $datainfo['refundremark2']; ?></td>
                </tr>
                <?php } if(!empty($datainfo['refundpic2'])){ ?>
                <tr>
                    <td width="150" class="oa_cell-left">图片2：</td>
                    <td class="oa_cell-right"><img src="<?php echo $datainfo['refundpic2']; ?>" height="60"></td>
                </tr>
                <?php }} ?>
            </table>
        </div>
        <?php } ?>

        <form action="" method="post">
            <div class="oa_title clearfix">
                <span class="oa_ico-right"></span>
                <span class="oa_title-btn"></span>
                <span class="oa_ico-left"></span>
                退款信息        </div>
            <div class="oa_edition" style="margin-bottom: 10px">
                <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">

                        <tr>
                            <td width="150" class="oa_cell-left">退款方式：</td>
                            <td class="oa_cell-right">
                                <select name="isrefundpart" id="isrefundpart" onchange="javascript:set_isrefundpart()">
                                    <option value="">请选择退款方式</option>
                                    <option value="0">全额退款</option>
                                    <option value="1">部份退款</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width="150" class="oa_cell-left">退款金额：</td>
                            <td class="oa_cell-right">
                                <input id="refundprice" name="refundprice" value="" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')"  />
                            </td>
                        </tr>

                        <!--<input type="hidden" id="isrefundpart" name="isrefundpart" value="0">-->
                        <!--<input type="hidden" id="refundprice" name="refundprice" value="<?php echo $datainfo['price']; ?>">-->

                    <tr>
                        <td width="150" class="oa_cell-left">备注信息：</td>
                        <td class="oa_cell-right">
                            <textarea id="refundremark" rows="3" cols="30"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:10px;">
                            <input type="hidden" value="3" name="intflag" id="intflag" />

                                <input type="button" id="btnRefund" onclick="javascript:refund(0)" value="确认退款">

                            <input type="button" id="btnRefund1" onclick="javascript:refund(1)" value="退款退活动">
                            <input type="button" id="btnRefund2" onclick="javascript:refuse_refund()" value="退款不通过">
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </div>
</div>
<script language="JavaScript">
    function set_isrefundpart() {
        $value=$("#isrefundpart").val();
       if($value=="0")
        {
            $("#refundprice").val("<?php echo $datainfo['price']; ?>");
        }
        else
        {
            $("#refundprice").val("");
        }
    }
    function refund(flag)
    {
        if($("#isrefundpart").val()=="")
        {
            layer.msg('请选择退款方式！');
            return;
        }
        if($("#refundprice").val()=="" )
        {
            layer.msg('请输入退款金额！');
            return;
        }
        if(!($("#refundprice").val()>"0"))
        {
            layer.msg('退款金额必须大于0！');
            return;
        }

        $("#btnRefund").attr("disabled","disabled");

        var refundprice=$("#refundprice").val();
        var refund_desc=$("#refund_desc").val();
        var isrefundpart=$("#isrefundpart").val();
        var refundremark=$("#refundremark").val();

        var data= {"sitecode":"<?php echo $sitecode ?>", "ordersn":"<?php echo $datainfo['transaction_id'] ?>", "refundprice": refundprice,"flag":flag, "price":"<?php echo $datainfo['price'] ?>", "refund_desc": refund_desc,"isrefundpart":isrefundpart,"refundremark":refundremark,'transaction_id':"<?php echo $datainfo['transaction_id'] ?>"};
        layer.load(1, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
        $.ajax({
            url:"<?php echo url('admin/Api/refund'); ?>",
            data:data,
            type:"post",
            dataType:"json",
            success:function(msg) {
                layer.closeAll('loading')
                if(msg.state==1)
                {
                    if(msg.info.return_code=="SUCCESS")
                    {

                        if(msg.info.result_code=="SUCCESS")
                        {

                            layer.confirm('退款成功！',{btn:['关闭'],btn1: function(index, layero){
                                    CloseDiv();
                                    GetOpenerWin().empty();
                                }});
                        }
                        else
                        {
                            $("#btnRefund").removeAttr("disabled");
                            layer.msg(msg.info.err_code_des);
                        }

                    }
                    else
                    {
                        $("#btnRefund").removeAttr("disabled");
                        layer.msg(msg.info.return_msg);
                    }

                }
                else
                {
                    if(msg.msg==58){
                        $("#btnRefund").removeAttr("disabled");
                        layer.msg("本地客户端证书有问题")
                    }else{
                        $("#btnRefund").removeAttr("disabled");
                        layer.msg(msg.msg)
                    }

                }
            }
        });
    }


    /**
     * 拒绝退款
     */
    function refuse_refund() {

        $("#btnRefund").attr("disabled","disabled");
        $("#btnRefund1").attr("disabled","disabled");

        var refundremark=$("#refundremark").val();

        var data= {"sitecode":"<?php echo $sitecode ?>", "ordersn":"<?php echo $datainfo['ordersn'] ?>","refundremark":refundremark};
        $.ajax({
            url:"<?php echo url('admin/Api/refuse_refund'); ?>",
            data:data,
            type:"post",
            dataType:"json",
            success:function(msg) {
                if(msg.state==1)
                {
                    if(msg.info.return_code=="SUCCESS")
                    {
                        if(msg.info.result_code=="SUCCESS")
                        {
                            layer.confirm('已拒绝退款！',{btn:['关闭'],btn1: function(index, layero){
                                    CloseDiv();
                                    GetOpenerWin().empty();
                                }});
                        }
                        else
                        {
                            $("#btnRefund").removeAttr("disabled");
                            layer.msg(msg.info.err_code_des);
                        }

                    }
                    else
                    {
                        $("#btnRefund").removeAttr("disabled");
                        layer.msg(msg.info.return_msg);
                    }

                }
                else
                {
                    $("#btnRefund").removeAttr("disabled");
                    layer.msg(msg.msg);
                }
            }
        });
    }
</script>
</body>
</html>