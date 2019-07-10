<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:84:"D:\workspace\work\public/../application/admin\view\order\group_buy_order_detail.html";i:1562662119;}*/ ?>
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
<div class="oa_content-area clearfix">
    <div class="oa_content-main">
        <div class="oa_title clearfix">
            <span class="oa_title-btn">

              </span>
            <span class="oa_ico-left"></span>
            报名管理
        </div>
        <div class="oa_text-list">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                <tr class="oa_text-list-title">
                    <th width="20" style="text-align:center;"><input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" /></th>

                    <th WIDTH="135"><span class="oa_arr-text-list-title"></span>订单号</th>
                    <th><span class="oa_arr-text-list-title"></span>活动名称</th>
                    <th><span class="oa_arr-text-list-title"></span>套餐名称</th>
                    <th WIDTH="80"><span class="oa_arr-text-list-title"></span>订单来源</th>
                    <th width="100"><span class="oa_arr-text-list-title"></span>订单状态</th>
                    <th WIDTH="80"><span class="oa_arr-text-list-title"></span>数量/价格</th>
                    <th WIDTH="90"><span class="oa_arr-text-list-title"></span>报名人姓名</th>
                    <th WIDTH="120"><span class="oa_arr-text-list-title"></span>报名时间</th>
                    <th width="30"><span class="oa_arr-text-list-title"></span>签到</th>
                    <th width="90"><span class="oa_arr-text-list-title"></span>操作</th>

                </tr>

                <?php $tmpflag=1; $tmpname=""; if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>

                <tr>
                    <td align="center"><input class="checked_one" type="checkbox" name="id[]" value="<?php echo $vo['id']; ?>" /></td>
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
                        <?php if($cms->CheckPurview('order','refund') && ($vo['state']>3 && $vo['state']!=12) && empty($vo['transaction_id'])==false): ?>
                        <a href="javascript:CustomOpen('<?php echo url('order/refund','action=edit&id='.$vo['id'],''); ?>','refund','退款',650,550)">退款 </a>
                        <?php endif; ?>
                        <br/>
                        <a href="javascript:send_sms_msg('<?php echo $vo['fiduser']; ?>')">发送短信 </a>
                        <a href="javascript:CustomOpen('<?php echo url('activity/issign','id='.$vo['id'],''); ?>','issign','签到',550,220)">签到 </a>
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
    </div>
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