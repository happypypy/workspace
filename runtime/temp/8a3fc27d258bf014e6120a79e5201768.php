<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:86:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\account\roleset.html";i:1561691685;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>角色管理</title>
<link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
<link href="/static/css/page.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
<script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
<script type="text/javascript" src="/static/js/tabscommon.js"></script>
<script type="text/javascript" src="/static/js/del-checked.js"></script>
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
                            <form action="<?php echo url('account/rolesetpost'); ?>" method="post">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top">
                                        <div class="oa_title clearfix">
                                            <span class="oa_title-btn"></span>
                                            <span class="oa_ico-left"></span>
                                            <?php echo lang('role set'); ?>
                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center;"><input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" /></th>
                                                    <th><span class="oa_arr-text-list-title"></span><?php echo lang('role name'); ?></th>
                                                    <th width="200"><span class="oa_arr-text-list-title"></span><?php echo lang('describe'); ?></th>
                                                </tr>
                                                <?php if(is_array($rolelist) || $rolelist instanceof \think\Collection || $rolelist instanceof \think\Paginator): $i = 0; $__LIST__ = $rolelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td align="center">
                                                        <input class="checked_one" type="checkbox" name="roleid[]" value="<?php echo $vo['idrole']; ?>" <?php if(is_array($accountrole) || $accountrole instanceof \think\Collection || $accountrole instanceof \think\Paginator): $i = 0; $__LIST__ = $accountrole;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><?php echo $v['fidrole']==$vo['idrole']?"checked":""; endforeach; endif; else: echo "" ;endif; ?>/>
                                                    </td>
                                                    <td><?php echo $vo['rolename']; ?></td>
                                                    <td><?php echo $vo['roleremark']; ?></td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                                <tr>
                                                    <td colspan="3" style="padding:10px;">
                                                        <input type="submit" name="" value="<?php echo lang('save'); ?>">
                                                        <input type="hidden" name="accountid" value="<?php echo $request['id']; ?>">
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            </form>
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