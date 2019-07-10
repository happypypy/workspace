<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\order\index.html";i:1562314390;}*/ ?>
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
                data:"id="+s,
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
                                                                        <td width="100" class="oa_cell-left">订单号：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input  type="text" name="ordersn" value="<?php echo $search['ordersn']; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">报名人姓名：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input  type="text" name="chrusername" value="<?php echo $search['chrusername']; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">活动名称：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input  type="text" name="chrtitle" value="<?php echo $search['chrtitle']; ?>" placeholder="活动或者课程名称" >
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">报名信息：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input  type="text" title=""  name="chrkey" value="<?php echo $search['chrkey']; ?>" placeholder="真实姓名,手机,身份证等">
                                                                        </td>
                                                                    </tr>

                                                                    <?php if($intflag==5) { ?>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">报名状态：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="state">
                                                                                <option value="0" >所有状态</option>
                                                                                <option value="1" <?php if($search['state']==1) { echo "selected"; } ?>>已报名，待审核</option>
                                                                                <option value="3" <?php if($search['state']==3) { echo "selected"; } ?>>已报名，已审核</option>
                                                                                <option value="2" <?php if($search['state']==2) { echo "selected"; } ?>>已报名，审核不通过</option>
                                                                                <option value="12" <?php if($search['state']==12) { echo "selected"; } ?>>已报名，待支付</option>
                                                                                <option value="4" <?php if($search['state']==4) { echo "selected"; } ?>>已报名，已支付</option>
                                                                                <option value="5" <?php if($search['state']==5) { echo "selected"; } ?>>已报名，退款中</option>
                                                                                <option value="8" <?php if($search['state']==8) { echo "selected"; } ?>>已报名，退款不通过</option>
                                                                                <option value="6" <?php if($search['state']==6) { echo "selected"; } ?>>已部分退款，继续服务</option>
                                                                                <option value="7" <?php if($search['state']==7) { echo "selected"; } ?>>已退款，继续服务</option>
                                                                               <!-- <option value="9" <?php if($search['state']==9) { echo "selected"; } ?>>删除</option>-->
                                                                                <option value="13" <?php if($search['state']==13) { echo "selected"; } ?>>已部分退款，终止服务</option>
                                                                                <option value="11" <?php if($search['state']==11) { echo "selected"; } ?>>已退款，终止服务</option>
                                                                                <option value="10" <?php if($search['state']==10) { echo "selected"; } ?>>终止服务</option>
                                                                            </select>

                                                                        </td>
                                                                    </tr>
                                                                    <?php } else if($intflag==6) { ?>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">报名状态：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="state">
                                                                                <option value="0" >所有状态</option>
                                                                                <option value="5" <?php if($search['state']==5) { echo "selected"; } ?>>已报名，退款中</option>
                                                                                <option value="6" <?php if($search['state']==6) { echo "selected"; } ?>>已报名，部分退款</option>
                                                                                <option value="7" <?php if($search['state']==7) { echo "selected"; } ?>>已报名，已退款</option>
                                                                                <option value="8" <?php if($search['state']==8) { echo "selected"; } ?>>已报名，退款不通过</option>
                                                                                <option value="11" <?php if($search['state']==11) { echo "selected"; } ?>>已取消，已退款</option>
                                                                           </select>
                                                                        </td>
                                                                    </tr>
                                                                    <?php } else{ ?>
                                                                    <tr <?php echo $intflag!=5?' style="display: none;"':''; ?>>
                                                                        <td width="100" class="oa_cell-left">报名状态：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="hidden" name="state" value="0"/>
                                                                        </td>
                                                                    </tr>
                                                                    <?php } ?>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">报名时间：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="text"   style="width: 80px;"  autocomplete="off" id="dtstart" name="dtstart" class="form-control"  value="<?php echo $search['dtstart']; ?>"> -
                                                                            <input type="text" style="width: 80px;"  autocomplete="off" id="dtend" name="dtend" class="form-control"  value="<?php echo $search['dtend']; ?>">
                                                                            <script language='JavaScript'>seltime("dtstart","YYYY-MM-DD");seltime("dtend","YYYY-MM-DD");</script>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">签到方式：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="singntype">
                                                                                <option value="0" >选择</option>
                                                                                <option value="1" <?php if($search['singntype']==1) { echo "selected"; } ?>>扫码签到</option>
                                                                                <option value="2" <?php if($search['singntype']==2) { echo "selected"; } ?>>输码签到</option>
                                                                                <option value="3" <?php if($search['singntype']==3) { echo "selected"; } ?>>电脑签到</option>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">签到人：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input  type="text" name="signusername" value="<?php echo $search['signusername']; ?>" placeholder="签到人" >
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">签到时间：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="text"   style="width: 80px;"  autocomplete="off" id="dtsigntimestart" name="dtsigntimestart" class="form-control"  value="<?php echo $search['dtsigntimestart']; ?>"> -
                                                                            <input type="text" style="width: 80px;"  autocomplete="off" id="dtsigntimestartend" name="dtsigntimestartend" class="form-control"  value="<?php echo $search['dtsigntimestartend']; ?>">
                                                                            <script language='JavaScript'>seltime("dtsigntimestart","YYYY-MM-DD");seltime("dtsigntimestartend","YYYY-MM-DD");</script>
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

                                                    <th WIDTH="135"><span class="oa_arr-text-list-title"></span>订单号</th>
                                                    <th><span class="oa_arr-text-list-title"></span>活动名称</th>
                                                    <th width="100"><span class="oa_arr-text-list-title"></span>套餐名称</th>
                                                    <th WIDTH="80"><span class="oa_arr-text-list-title"></span>订单来源</th>
                                                    <th width="100"><span class="oa_arr-text-list-title"></span>订单状态</th>
                                                    <th WIDTH="80"><span class="oa_arr-text-list-title"></span>数量/价格</th>
                                                    <th WIDTH="90"><span class="oa_arr-text-list-title"></span>报名人姓名</th>
                                                    <th WIDTH="120"><span class="oa_arr-text-list-title"></span>报名时间</th>
                                                    <th width="30"><span class="oa_arr-text-list-title"></span>签到</th>
                                                    <th width="<?php if(in_array(($intflag), explode(',',"2,3,4"))): ?>50<?php else: ?>90<?php endif; ?>"><span class="oa_arr-text-list-title"></span>操作</th>

                                                </tr>

                                                <?php $tmpflag=1; $tmpname=""; if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>

                                                <tr>
                                                    <td align="center"><input class="checked_one" type="checkbox" name="id[]" value="<?php echo $vo['wechatid']; ?>" /></td>
                                                    <td><?php echo $vo['ordersn']; ?></td>
                                                    <td title="<?php echo $vo['chrtitle']; ?>"><?php echo $vo['chrtitle']; ?></td>
                                                    <td title="<?php echo $vo['payname']; ?>"><?php echo $vo['payname']; ?></td>
                                                    <td><?php echo $vo['source']; ?></td>
                                                    <td style="color:<?php echo $order_state_color[$vo['state']]; ?>;" title="<?php echo $order_state[$vo['state']]; ?>"><?php echo $order_state[$vo['state']]; ?></td>
                                                    <td><?php echo $vo['paynum']; ?>/<?php echo $vo['price']; ?></td>
                                                    <td title="<?php echo $vo['chrusername']; ?>"><?php echo $vo['chrusername']; ?></td>
                                                    <td><?php echo $vo['dtcreatetime']; ?></td>
                                                    <td id="issign_<?php echo $vo['id']; ?>"><?php echo $vo['issign']==1?"<span style=' color: red'>是<span>":"否"; ?></td>
                                                    <td>
                                                        <a href="javascript:CustomOpen('<?php echo url('order/modi','action=edit&id='.$vo['id'],''); ?>','order','订单详情',650,600)">订单详情 </a>
                                                        <?php if($cms->CheckPurview('order','refund') && ($vo['state']>3 && $vo['state']!=12) && empty($vo['transaction_id'])==false){ ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('order/refund','action=edit&id='.$vo['id'],''); ?>','refund','退款',650,550)">退款 </a>
                                                        <?php } ?>
                                                        <br/>
                                                        <?php if($intflag==5){ ?>
                                                        <a href="javascript:send_sms_msg('<?php echo $vo['fiduser']; ?>')">发送短信 </a>
                                                        <?php } if($vo['issign']!=1 && ($vo["state"]>=3 && $vo["state"]<=8 && $vo["state"]!=5)){ ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('activity/issign','id='.$vo['id'],''); ?>','issign','签到',550,220)">签到 </a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>


                                                <?php endforeach; endif; else: echo "" ;endif; ?>

                                            </table>
                                            <?php if(($intflag == '2')): ?>
                                            <div style="position:absolute;margin-top: 18px;margin-left: 12px;">
                                            <button   id="settype"	type="button"  >批量设置用户类型</button>

                                            <select name="settype" id="selecttype">
                                                <option value="please">==请选择用户类型==</option>
                                                <?php if(is_array($hyfl) || $hyfl instanceof \think\Collection || $hyfl instanceof \think\Paginator): if( count($hyfl)==0 ) : echo "" ;else: foreach($hyfl as $key=>$v): ?>
                                                    <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </select>
                                            </div>
                                            <script>
                                                //批量设置用户类型
                                                $("#settype").click(function(){
                                                    if($('input[name="id[]"]:checked').length>0){
                                                        var chk_value =[];//定义一个数组
                                                        $('input[name="id[]"]:checked').each(function(){
                                                            //遍历每一个名字为id的复选框，其中选中的执行函数
                                                            chk_value.push($(this).val())//将选中的值添加到数组chk_value中
                                                        });
                                                        openid=chk_value.join(',')
                                                        var type=$("#selecttype").val()
                                                        //console.log(type)
                                                        if(type=="please"){
                                                            layer.confirm('您还未选择用户类型')
                                                            return  false
                                                        }
                                                        var typename=$("#selecttype option:selected").text()
                                                        typename="<span style='color: red'>"+typename+"</span>"
                                                        layer.confirm("您确定把用户类型改为 "+typename+" 吗？",
                                                            {
                                                                btn: ['确定','取消'] //按钮
                                                            },function(){
                                                                $.ajax({
                                                                    type: 'post',
                                                                    url:"<?php echo url('order/setUsersType'); ?>?type="+type,
                                                                    dataType: 'json',
                                                                    data:{'openid':openid,'intflag':<?php echo $intflag; ?>},
                                                                    success:function(res){
                                                                        if(res==1){
                                                                            layer.confirm('修改成功')
                                                                        }else{
                                                                            layer.confirm('没有需要修改的用户类型！')
                                                                        }
                                                                    }

                                                                })
                                                            })
                                                    }else{
                                                        layer.confirm("您还未选择订单！")
                                                    }

                                                })
                                            </script>
                                <?php endif; ?>
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
            <td class="oa_wrapper-bottom-arr-middle"></td>
            <td class="oa_wrapper-bottom-arr-right">&nbsp;</td>
        </tr>
    </table>
</div>


<script type="text/javascript">
    function check() {
        var starttime = $('#dtstart').val();
        var endtime = $('#dtend').val();
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