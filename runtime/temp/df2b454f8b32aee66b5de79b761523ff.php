<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:91:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\member\qrcode_manage.html";i:1561691687;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>二维码管理</title>
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
                url:"<?php echo url('member/qrcode_del'); ?>",
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
        // 预览图片
        function preview(img){
            var rqcode = $("#rqcode");
            rqcode.html("<img width='150' id='img_rqcode' src='"+img+"' />");
            var yy=event.clientY-50;
            if(yy<10) {yy=10};

            $("#rqcode").css("top",yy+"px").css("left",event.clientX+50+"px");
            rqcode.show();
        }
        // 关闭预览
        function closePreview(){
            var rqcode = $("#rqcode");
            rqcode.html("");
            rqcode.hide();
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
                                <li onclick="javascript:window.location='<?php echo url('member/index',''); ?>'" ><em>用户管理</em></li>
                                <li onclick="javascript:window.location='<?php echo url('member/followup',''); ?>'" ><em>访谈记录</em></li>
                                <li class="oa_on"><em>二维码管理</em></li>
                            </ul>
                        </div>
                    </div>
                    <div class="oa_content-area clearfix">
                        <div class="oa_content-main">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top">
                                        <div class="oa_title clearfix">
                                            <span class="oa_ico-right" style="margin-right: 10px;">
                                            </span>
                                            <span class="oa_ico-left"></span>
                                            <?php echo lang('search'); ?>
                                        </div>
                                        <div class="oa_search-area clearfix">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>
                                                        <div class="oa_search-type clearfix">
                                                            <form action="<?php echo url('member/qrcode_manage'); ?>" method="post" id="form1">
                                                                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                                                    <tr>
                                                                        <td width="150" class="oa_cell-left">二维码名称：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input id="qrcode_name" type="text" name="qrcode_name" value="<?php echo $param['qrcode_name']; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td height="30"></td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="submit" value="查询" class="oa_search-btn" />
                                                                        </td>
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
                                                <ul>
                                                    <li class="oa_selected">
                                                        <a href="javascript:CustomOpen('<?php echo url('member/qrcode_modi','&action=add',''); ?>', 'qrcode_modi','新增二维码', 330,220)">新增二维码</a>
                                                    </li>
                                                </ul>
                                            </span>
                                            <span class="oa_ico-left"></span>二维码管理
                                            <span class="oa_ico-right" style="margin-right: 10px;">
                                            </span>
                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">

                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center;">
                                                        <input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" />
                                                    </th>
                                                    <th width="20">ID</th>
                                                    <th width="70">二维码图片</th>
                                                    <th>二维码名称</th>
                                                    <th>二维码描述</th>
                                                    <th width="120">创建时间</th>
                                                    <th width="60">操作</th>
                                                </tr>
                                                <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td align="center">
                                                        <input class="checked_one" type="checkbox" name="id[]" value="<?php echo $vo['id']; ?>" />
                                                    </td>
                                                    <td><?php echo $vo['id']; ?></td>
                                                    <td align="center"><img src="<?php echo $vo['ticket']; ?>" width="50" height="50" onmousemove="preview('<?php echo $vo['ticket']; ?>')" onmouseout="closePreview()" style="margin: 5px 0;" /></td>
                                                    <td><?php echo $vo['qrcode_name']; ?></td>
                                                    <td><?php echo $vo['qrcode_desc']; ?></td>
                                                    <td><?php echo date("Y-m-d H:i:s",$vo['create_time']); ?></td>
                                                    <td>
                                                        <a href="javascript:CustomOpen('<?php echo url('admin/member/qrcode_modi','id='.$vo['id'].'&action=edit',''); ?>', 'qrcode','二维码修改', 330, 220)">修改</a>
                                                        <a href="#" onclick="del_checked(<?php echo $vo['id']; ?>,'<?php echo $vo['qrcode_name']; ?>');" >删除</a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </table>
                                            <div class="oa_bottom clearfix">
                                                <div class="clearfix">
                                                    <div class="oa_op-btn clearfix">
                                                        <input type="button" name="btn2" value="删除"  onclick="javascript:del_checked('')"></input>
                                                    </div>
                                                    <div class="oa_page-controls">
                                                        <ul>
                                                            <?php echo $page->show(); ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="clearfix">
                                                    <div class="clearfix">
                                                        <div class="oa_op-btn clearfix">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="oa_bottom-bottom"><em></em></div>
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
<div id="rqcode" style="display: none; height: 160px;width: 160px;background-color: #FFFFFF; border: solid 2px #000000; text-align: center;padding-top: 20px; ;position: absolute;margin-right: 150px" />
</body>
</html>