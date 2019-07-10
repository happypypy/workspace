<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\order\modi.html";i:1561691687;}*/ ?>
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

</head>
<body>
<div class="oa_pop">
    <div class="oa_pop-main">
        <div style="height: 6px"></div>
        <div class="oa_title clearfix">
            <span class="oa_ico-right"><div  style="padding-right:10px;"><a target="_blank" href="<?php echo url('admin/activity/signupindex',['id'=>$datainfo['dataid']]); ?>">查看该产品的所有订单</a></div></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            基础信息        </div>
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
                        <td width="150" class="oa_cell-left">购买数理：</td>
                        <td class="oa_cell-right"><?php echo $datainfo['paynum']; ?></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">订单总价格：</td>
                        <td class="oa_cell-right"><?php echo $datainfo['price']; ?></td>
                    </tr>
                    <?php if($is_cashed && $datainfo['cashed_amount'] > 0): ?>
                    <tr>
                        <td width="150" class="oa_cell-left">现金券抵扣：</td>
                        <td class="oa_cell-right"><?php echo $datainfo['cashed_amount']; ?>(元)</td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td width="150" class="oa_cell-left">订单状态：</td>
                        <td class="oa_cell-right"><?php echo $order_state[$datainfo['state']]; ?></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">订单来源：</td>
                        <td class="oa_cell-right"><?php echo $datainfo['source']; ?></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">创建时间：</td>
                        <td class="oa_cell-right"><?php echo $datainfo['dtcreatetime']; ?></td>
                    </tr>

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
                    <td width="150" class="oa_cell-left">退款原因2：</td>
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


        <div class="oa_title clearfix">
            <span class="oa_ico-right"></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            报名表单       </div>
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
                <?php } ?>
            </table>
        </div>
        <?php
        if(!empty($datainfo['txtdata1']))
        {
            $subdata= explode("§", $datainfo['txtdata1']);
        ?>
        <div class="oa_title clearfix">
            <span class="oa_ico-right"></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            更多报名信息       </div>
        <div class="oa_edition" style="margin-bottom: 5px">
            <?php
            $row2= explode("☆", $datainfo['txtfield1']);
            foreach($subdata as $k1=>$vo1)
            {
                $row1= explode("☆", $vo1);
            ?>
            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
            <?php
                foreach($row1 as $k=>$vo)
                {
                    $arr= explode("∫", $row2[$k]);
                    $datatype=1;
                    $datafield=$arr[0];
                    if(count($arr)>1)
                    {
                    $datatype=$arr[1];
                    }
                ?>
                <tr>
                    <td width="150" class="oa_cell-left"><?php echo $datafield; ?><?php echo $k1+1; ?>：</td>
                    <td class="oa_cell-right">
                        <?php
                                if($datatype==7)
                                    echo '<a href="'.$row1[$k].'" target="_blank"> <img src="'.$row1[$k].'" style="border: 0px;height: 50px;padding: 1px;"  /></a>';
                        else
                        echo $row1[$k];
                        ?>

                    </td>

                </tr>
                <?php } ?>
            </table>
            <?php } ?>
        </div>
        <?php } if($datainfo['intflag']!=1  && $datainfo['intflag']!=4 && $datainfo['intflag']!=5  && $datainfo['intflag']!=6) { ?>
        <form action="" method="post">
            <div class="oa_title clearfix">
            <span class="oa_ico-right"></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            审批信息        </div>
            <div class="oa_edition" style="margin-bottom: 5px">
                <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">

                    <tr>
                        <td width="150" class="oa_cell-left">审批意见：</td>
                        <td class="oa_cell-right"><textarea name="checkinfo" rows="4" style="width: 400px;"><?php echo $datainfo['checkinfo']; ?></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:5px;">
                            <input type="hidden" value="3" name="intflag" id="intflag" />
                            <?php if($datainfo['state']==1) { ?>
                            <input type="submit" onclick="javascript:$('#intflag').val('5')" value="审批通过">
                            <?php if($datainfo['intflag']!=3) { ?>
                            <input type="submit" value="审批不通过">
                            <?php }} if($datainfo['intflag']==3 && $datainfo['state']==2) { ?>
                                <input type="submit" onclick="javascript:$('#intflag').val('5')" value="重新审批为通过">
                            <?php } if($datainfo['ischarge']==1){ ?>
                            <input type="submit" onclick="javascript:$('#intflag').val('4')" value="取消报名">
                            <?php } ?>
                        </td>
                    </tr>


                </table>
            </div>
        </form>
        <?php } if($datainfo['issign']==1) { ?>
        <div class="oa_title clearfix">
            <span class="oa_ico-right"></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            签到信息       </div>
        <div class="oa_edition" style="margin-bottom: 5px">
            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                <tr>
                    <td width="150" class="oa_cell-left">签到时间：</td>
                    <td class="oa_cell-right"><?php echo $datainfo['dtsigntime']; ?></td>
                </tr>
                <tr>
                    <td width="150" class="oa_cell-left">签到方式：</td>
                    <td class="oa_cell-right"><?php switch($datainfo['singntype']){case 1: echo "扫码签到"; break; case 2:echo "输码签到";break;case 3: echo "电脑签到";break;} ?></td>
                </tr>
                <tr>
                    <td width="150" class="oa_cell-left">签到备注：</td>
                    <td class="oa_cell-right"><?php echo $datainfo['issignremark']; ?></td>
                </tr>
                <tr>
                    <td width="150" class="oa_cell-left">签到人：</td>
                    <td class="oa_cell-right"><?php echo $datainfo['signusername']; ?></td>
                </tr>

            </table>
        </div>
        <br />
        <br />
        <?php } ?>
    </div>
</div>
</body>
</html>