<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:83:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\sms\send_log.html";i:1561691686;s:72:"C:\phpStudy\PHPTutorial\WWW\work\application\admin\view\sms\sms_nav.html";i:1561691686;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>短信管理</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
    <script type="text/javascript" src="/static/js/tabscommon.js"></script>
    <script type="text/javascript" src="/static/js/del-checked.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <link rel="stylesheet" type="text/css" href="/static/js/daterangepicker/daterangepicker-bs3.css" />
    <script type="text/javascript" src="/static/js/daterangepicker/moment.min.js"></script>
    <script type="text/javascript" src="/static/js/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript">

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
    <?php if($cms->CheckPurview('sms','send') || $cms->CheckPurview('sms','sendmanage')){ ?>
        <li id="send_log" onclick="javascript:window.location='<?php echo url('sms/send_log',''); ?>'"><em>发送记录</em></li>
    <?php } if($cms->CheckPurview('sms','recharge')){ ?>
        <li id="sms_recharge_list" onclick="javascript:window.location='<?php echo url('sms/sms_recharge_list',array('id'=>1,'flag'=>10)); ?>'"><em>短信充值</em></li>
    <?php } if($cms->CheckPurview('sms','autosend')){ ?>
        <li id="sms_open_config" onclick="javascript:window.location='<?php echo url('sms/sms_open_config',''); ?>'"><em>发送设置</em></li>
    <?php } if($cms->CheckPurview('sms','opensms')){ ?>
        <li id="sms_apply" onclick="javascript:window.location='<?php echo url('sms/sms_apply',''); ?>'"><em>短信申请</em></li>
    <?php } if($cms->CheckPurview('sms','msgpattern')){ ?>
        <li id="sms_template_list" onclick="javascript:window.location='<?php echo url('sms/sms_template_list',''); ?>'"><em>短信模版</em></li>
    <?php } ?>
</ul>

<script type="text/javascript">
    $("#" + "<?= $action_name ?>").addClass("oa_on");
</script>
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
                                            <?php echo lang('search'); ?>
                                        </div>
                                        <div class="oa_search-area clearfix">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>
                                                        <div class="oa_search-type clearfix">
                                                            <form action="" method="post" id="form1" onsubmit="return check()">
                                                                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">发送状态：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="status" style="width: 200px">
                                                                                <option value="">请选择</option>
                                                                                <option value="0" <?php if(is_numeric($search["status"]) && $search["status"]==0){echo "selected";} ?>>待发送</option>
                                                                                <option value="1" <?php if($search["status"]==1){echo "selected";} ?>>发送成功</option>
                                                                                <option value="2" <?php if($search["status"]==2){echo "selected";} ?>>发送失败</option>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">发送号码：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="text" name="mobile" value="<?php echo $search["mobile"]; ?>" />
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">发送内容：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="text" name="content" value="<?php echo $search["content"]; ?>" />
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">创建时间：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="text"  style="width: 80px;"  autocomplete="off" id="create_begin_time" name="create_begin_time" class="form-control"  value="<?php echo $search['create_begin_time']; ?>"> -
                                                                            <input type="text" style="width: 80px;" autocomplete="off" id="create_end_time" name="create_end_time" class="form-control"  value="<?php echo $search['create_end_time']; ?>">
                                                                            <script language='JavaScript'>seltime("create_begin_time","YYYY-MM-DD");seltime("create_end_time","YYYY-MM-DD");</script>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">发送时间：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="text"  style="width: 80px;"  autocomplete="off" id="send_begin_time" name="send_begin_time" class="form-control"  value="<?php echo $search['send_begin_time']; ?>"> -
                                                                            <input type="text" style="width: 80px;" autocomplete="off" id="send_end_time" name="send_end_time" class="form-control"  value="<?php echo $search['send_end_time']; ?>">
                                                                            <script language='JavaScript'>seltime("send_begin_time","YYYY-MM-DD");seltime("send_end_time","YYYY-MM-DD");</script>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td height="30"></td>
                                                                        <td class="oa_cell-right"><input  name="" type="submit" value="<?php echo lang('search'); ?>" class="oa_search-btn" /></td>
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
                                                <a href="javascript:CustomOpen('<?php echo url('sms/send_sms_with_xlsx'); ?>', 'send_sms_with_xlsx','短信发送', 700,550)">短信发送</a>
                                                <a href="javascript:CustomOpen('<?php echo url('sms/sms_batch_send'); ?>', 'sms_batch_send','短信批量发送', 700,550)">短信批量发送</a>
                                            </ul>
                                        </span>
                                            <span class="oa_ico-left"></span>
                                            短信管理
                                        </div>
                                        <div class="oa_text-list clearfix">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                                                        <tr class="oa_text-list-title">
                                                            <th width="70"><span class="oa_arr-text-list-title"></span>发送号码</th>
                                                            <th><span class="oa_arr-text-list-title"></span>发送内容</th>
                                                            <th width="60"><span class="oa_arr-text-list-title"></span>发送状态</th>
                                                            <th width="120"><span class="oa_arr-text-list-title"></span>发送时间</th>
                                                            <th width="120"><span class="oa_arr-text-list-title"></span>创建时间</th>
                                                        </tr>
                                                        <?php if($list == null): ?><tr><td colspan="6" style="text-align: center"><?php echo lang('data empty'); ?></td></tr><?php else: if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                        <tr>
                                                            <td><?php echo $vo['mobile']; ?></td>
                                                            <td><?php echo $vo['content']; ?></td>
                                                            <td><?php echo $vo['str_status']; ?></td>
                                                            <td><?php echo $vo['send_time']; ?></td>
                                                            <td><?php echo $vo['create_time']; ?></td>
                                                        </tr>
                                                        <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                                    </table>

                                                <div class="oa_bottom clearfix">
                                                    <div class="clearfix">
                                                        <div class="oa_page-controls">
                                                            <ul>
                                                                <li></li>
                                                                <li>
                                                                    <?php if($page != null): ?>
                                                                    <?php echo $page->show(); endif; ?>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="oa_bottom-bottom"><em></em></div>
                                                </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
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
    function empty() {
        window.location.reload();
    }

    function check() {
        var starttime = $('#send_begin_time').val();
        var endtime = $('#send_end_time').val();
        var start = new Date(starttime.replace("-", "/").replace("-", "/"));
        var end = new Date(endtime.replace("-", "/").replace("-", "/"));
        if (end < start) {
            layer.alert("发送开始时间不能大于结束时间！");
            return false;
        }

        var starttime = $('#create_begin_time').val();
        var endtime = $('#create_end_time').val();
        var start = new Date(starttime.replace("-", "/").replace("-", "/"));
        var end = new Date(endtime.replace("-", "/").replace("-", "/"));
        if (end < start) {
            layer.alert("创建开始时间不能大于结束时间！");
            return false;
        }
        return true;
    }
</script>
</body>
</html>