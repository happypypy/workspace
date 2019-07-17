<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"D:\workspace\work\public/../application/admin\view\order\group_buy_order_list.html";i:1562918462;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>报名管理</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
    <script type="text/javascript" src="/static/js/tabscommon.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <script type="text/javascript" src="/static/js/del-checked.js"></script>
    <link rel="stylesheet" type="text/css" href="/static/js/daterangepicker/daterangepicker-bs3.css" />
    <script type="text/javascript" src="/static/js/daterangepicker/moment.min.js"></script>
    <script type="text/javascript" src="/static/js/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript">
        $(function(){
            $(document).ready(function() {
                $('.oa_text-list tr').addClass('odd');
                $('.oa_text-list tr:even').addClass('even');
            });
            $('.oa_text-list tr').hover(
                function(){
                    $(this).addClass('oa_mouseover-bg');
                },
                function(){
                    $(this).removeClass('oa_mouseover-bg');
                }
            );
        });
        function empty() {
            window.location.reload();
        }
        //全选
        function DoCheck(){
            if($("#checked").is(':checked')){
                $(".checked_one").attr("checked", true);
            }else{
                $(".checked_one").attr("checked", false);
            }
        }

        function del_checked(value,remark) {

            var msg='您确定要删除选定的记录吗？';
            if(value>0) {
                msg='您确定要删除“'+remark+'”吗？';
            }

            layer.confirm(msg, {
                btn: ['确定','取消'] //按钮
            }, function(){
                del_checked1(value);
            }, function(){

            });
        }

        //删除选中
        function del_checked1(value) {
            var b = $(".checked_one");
            var s = '';
            if(value<1)
            {
                for (var i = 0; i < b.length; i++) {
                    if (b[i].checked) {
                        s += b[i].value + ',';
                    }
                }
                s = s.substr(0, s.length - 1);
            }
            else
            {
                s=value;
            }

            $.ajax({
                url:"<?php echo url('order/delchecked'); ?>",
                data:"group_buy_order_id="+s,
                type:"post",
                dataType:"json",
                success:function(msg) {
                    if (msg == 1) {
                        layer.alert('删除成功', {icon:1});
                        $(".checked_one").attr("checked", false);
                        location.reload();
                    }
                    else if(msg==-1){
                        layer.alert('你没有删除权限', {icon: 5});
                    }
                    else
                    {
                        layer.alert('删除失败', {icon: 2});
                        //location.reload();
                    }
                }
            })
        }
    </script>
</head>
<body>
<div class="oa_wrapper">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="oa_wrapper-display">
            <td class="oa_wrapper-top-arr-left">&nbsp;</td>
            <td class="oa_wrapper-top-arr-middle"></td>
            <td class="oa_wrapper-top-arr-right">&nbsp;</td>
        </tr>
        <tr>
            <td class="oa_wrapper-middle-arr-left oa_wrapper-display"></td>
            <td class="oa_wrapper-middle-arr-middle">
                <div class="oa_location clearfix"><span class="oa_ico-left"></span>location<span class="oa_ico-right"></span></div>
                <div class="oa_main clearfix">
                    <div class="oa_subnav clearfix">

                        <div class="oa_subnav-tab clearfix">
                            <ul>
                                <li <?php echo $intflag==2?'class="oa_on"':''; ?> onclick="javascript:window.location='<?php echo url('order/index','&intflag=2'); ?>'"><em>待审批的报名</em></li>
                                <li <?php echo $intflag==3?'class="oa_on"':''; ?> onclick="javascript:window.location='<?php echo url('order/index','&intflag=3'); ?>'"><em>审查不通过的报名</em></li>
                                <li <?php echo $intflag==4?'class="oa_on"':''; ?> onclick="javascript:window.location='<?php echo url('order/index','&intflag=4'); ?>'"><em>已取消的报名</em></li>
                                <li <?php echo $intflag==5?'class="oa_on"':''; ?> onclick="javascript:window.location='<?php echo url('order/index','&intflag=5'); ?>'"><em>所有报名 </em></li>
                                <li <?php echo $intflag==6?'class="oa_on"':''; ?> onclick="javascript:window.location='<?php echo url('order/index','&intflag=6'); ?>'"><em>退款的报名<span style="color: red">(<?php echo $refundcount; ?>)</span></em></li>
                                <li <?php echo $intflag==1?'class="oa_on"':''; ?> onclick="javascript:window.location='<?php echo url('order/index','&intflag=1'); ?>'"><em>待下单的报名<span style="color: red">(<?php echo $signupcount; ?>)</span></em></li>
                                <li <?php echo $intflag==7?'class="oa_on"':''; ?> onclick="javascript:window.location='<?php echo url('order/groupBuyOrderList'); ?>'"><em>拼团订单</em></li>
                            </ul>
                        </div>

                    </div>
                    <div class="oa_content-area clearfix">
                        <div class="oa_content-main">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top">
                                        <div class="oa_title clearfix">
                                            <span class="oa_ico-right"></span>
                                            <span class="oa_ico-left"></span>
                                            查询
                                        </div>
                                        <div class="oa_search-area clearfix">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>
                                                        <div class="oa_search-type clearfix">
                                                            <form action="" method="post" id="form1" onsubmit="return check()">
                                                                <table width="100%" border="0" cellspacing="1" cellpadding="0">

                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">活动名称：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input  type="text" name="activity_name" value="<?= isset($request['activity_name']) ? $request['activity_name'] : '' ?>" placeholder="活动或者课程名称" >
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">报名状态：</td>
                                                                        <td class="oa_cell-right">
                                                                            <!-- 团购状态：0-开启拼团未支付，1-拼团中，2-拼团成功，3-拼团到期，4-拼团取消 -->
                                                                            <select name="state">
                                                                                <option value="200">所有状态</option>
                                                                                <?php foreach($stateMap as $index=>$vo) { ?>
                                                                                <option value="<?php echo $index; ?>" <?php if(isset($request['state']) && $request['state']==$index) { echo "selected"; } ?> ><?php echo $vo; ?></option>
                                                                                <?php } ?>

                                                                           </select>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">报名时间：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="text" style="width: 80px;"  autocomplete="off" id="start_at" name="start_at" class="form-control"  value="<?php isset($request['start_at']) && is_numeric($request['start_at']) ? date('Y-m-d H:i:s', $request['start_at']) : '';  ?>"> -
                                                                            <input type="text" style="width: 80px;"  autocomplete="off" id="end_at" name="end_at" class="form-control"  value="<?php echo isset($request['end_at']) && is_numeric($request['end_at']) ? date('Y-m-d H:i:s', $request['end_at']) : ''; ?>">
                                                                            <script language='JavaScript'>seltime("start_at","YYYY-MM-DD");seltime("end_at","YYYY-MM-DD");</script>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td height="30"></td>
                                                                        <td class="oa_cell-right"><input  name="" type="submit" value="查询" class="oa_search-btn" /></td>
                                                                    </tr>
                                                                </table>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                     
                                        <div class="oa_title clearfix">
                                            <span class="oa_title-btn">

                                              </span>
                                            <span class="oa_ico-left"></span>
                                            报名管理                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center;"><input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" /></th>

                                                    <th width="70px"><span class="oa_arr-text-list-title"></span>拼团订单号</th>
                                                    <th><span class="oa_arr-text-list-title"></span>活动名称</th>
                                                    <th><span class="oa_arr-text-list-title"></span>套餐名称</th>
                                                    <th WIDTH="85px"><span class="oa_arr-text-list-title"></span>拼团情况</th>
                                                    <th width="100"><span class="oa_arr-text-list-title"></span>开团人</th>
                                                    <th WIDTH="90px"><span class="oa_arr-text-list-title"></span>拼团价/购买数</th>
                                                    <th WIDTH="115px"><span class="oa_arr-text-list-title"></span>拼团时间</th>
                                                    <th WIDTH="90px"><span class="oa_arr-text-list-title"></span>状态</th>
                                                    <th width="<?php if(in_array(($intflag), explode(',',"2,3,4"))): ?>50<?php else: ?>90<?php endif; ?>"><span class="oa_arr-text-list-title"></span>操作</th>

                                                </tr>

                                                <?php $tmpflag=1; $tmpname=""; if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>

                                                <tr>
                                                    <td align="center"><input class="checked_one" type="checkbox" name="group_buy_order_id[]" value="<?php echo $vo['group_buy_order_id']; ?>" /></td>
                                                    <td><?php echo $vo['group_buy_order_id']; ?></td>
                                                    <td title="<?php echo $vo['activity_name']; ?>"><?php echo $vo['activity_name']; ?></td>
                                                    <td title="<?php echo $vo['pachage_name']; ?>"><?php echo $vo['pachage_name']; ?></td>
                                                    <td>所需商品数：<?php echo $vo['group_num']; ?><br/>已售商品数：<?php echo $vo['buy_num']; ?></td>
                                                    <td title="<?php echo $vo['username']; ?>"><?php echo $vo['username']; ?></td>
                                                    <td><?php echo $vo['group_price']; ?>/<?php echo $vo['sold']; ?></td>
                                                    <td><?php if(!empty($vo['activated_at'])) echo date('Y-m-d H:i:s', $vo['activated_at']); ?></td>
                                                    <td><?php echo $stateMap[$vo['state']]; ?></td>
                                                    <td>
                                                        <a href="javascript:CustomOpen('<?php echo url('order/groupBuyOrderDetail','flag=5&group_buy_order_id='.$vo['group_buy_order_id'],''); ?>','order','拼团详情',1300,600)">拼团详情 </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </table>
                                            <div class="oa_bottom clearfix">
                                                <div class="clearfix">
                                                    <div class="oa_op-btn clearfix" style="display: none;">
                                                        <?php if($cms->CheckPurview('order','manage')){ ?>
                                                        <input style="display: none;" name="" value="删除" onclick="del_checked(0,'');" type="button" class="oa_input-submit" />
                                                        <?php } ?>
                                                    </div>
                                                    <div class="oa_page-controls">
                                                        <ul>
                                                            <li><?php echo $page->show(); ?></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="oa_bottom-bottom"><em></em></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="oa_content-main-bottom"></div>
                        </div>
                    </div>
                </div>
            </td>
            <td class="oa_wrapper-middle-arr-right oa_wrapper-display"></td>
        </tr>
        <tr class="oa_wrapper-display">
            <td class="oa_wrapper-bottom-arr-left">&nbsp;</td>
            <td class="oa_wrapper-bottom-arr-mgroup_buy_order_iddle"></td>
            <td class="oa_wrapper-bottom-arr-right">&nbsp;</td>
        </tr>
    </table>
</div>

<script type="text/javascript">
    function check() {
        var starttime = $('#start_at').val();
        var endtime = $('#end_at').val();
        var start = new Date(starttime.replace("-", "/").replace("-", "/"));
        var end = new Date(endtime.replace("-", "/").replace("-", "/"));
        if (end < start) {
            layer.alert("开始时间不能大于结束时间！");
            return false;
        }
        return true;
    }
    
    function send_sms_msg(userid) {
        userids=userid;
        CustomOpen('<?php echo url('activity/send_sms_msg'); ?>','activity','短信发送',700,400);
    }
</script>
</body>
</html>