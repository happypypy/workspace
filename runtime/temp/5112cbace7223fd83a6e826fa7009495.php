<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:92:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\activity\customdetail.html";i:1561691685;}*/ ?>
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

        function sign(obj,id) {
            $.ajax({
                url:"<?php echo url('activity/issign'); ?>",
                data:"id="+id,
                type:"post",
                dataType:"json",
                success:function(msg) {
                    if (msg == 1) {
                        layer.alert('签到成功', {icon:1});
                        $(obj).hide();
                        $("#issign_"+id).html("是");
                    }

                }
            })
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
                    <div class="oa_content-area clearfix">
                        <div class="oa_content-main">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top">

                                        <div class="oa_title clearfix">
                                            <span class="oa_title-btn">

                                              </span>
                                            <span class="oa_ico-left"></span>
                                            咨询详情                                        </div>
                                        <div class="oa_text-list">







                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <th WIDTH="135"><span class="oa_arr-text-list-title"></span>微信ID</th>
                                                    <th ><span class="oa_arr-text-list-title"></span>微信昵称</th>
                                                    <th width="100"><span class="oa_arr-text-list-title"></span>咨询按钮点击数</th>
                                                    <th WIDTH="120"><span class="oa_arr-text-list-title"></span>最后咨询时间</th>
                                                </tr>
                                                <?php $tmpflag=1; $tmpname="";
                                                if(empty($data)) {
                                                ?>
                                                <tr>
                                                    <td colspan="4" style="height: 50px;">当前没有咨询记录</td>
                                                </tr>
                                                <?php } if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td><?php echo $vo['openid']; ?></td>
                                                    <td><?php echo $vo['name']; ?></td>
                                                    <td><?php echo $vo['v']; ?></td>
                                                    <td><?php echo date('Y-m-d H:i:s',$vo['createtime']); ?></td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </table>
                                            <?php $tmpflag=1; $tmpname="";
                                                if(!empty($data)) {
                                                ?>
                                            <div class="oa_bottom clearfix">
                                                <div class="clearfix">
                                                    <div class="oa_op-btn clearfix">

                                                    </div>
                                                    <div class="oa_page-controls">
                                                        <ul>
                                                            <li><?php echo $page->show(); ?></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="oa_bottom-bottom"><em></em></div>
                                            </div>
                                            <?php } ?>
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
</body>
</html>