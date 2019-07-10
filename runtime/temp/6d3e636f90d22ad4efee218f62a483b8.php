<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:68:"D:\workspace\work\public/../application/admin\view\index\header.html";i:1561691687;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/static/css/header.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
</head>
<body>
<div id="header">
	<div class="header clearfix">
        <table width="100%">
            <tr><td>
                 <img src="/static/images/logo.png" style="border: 0px;height: 50px;padding: 1px;" />
            </td>
            <td style="line-height:10px; width: 500px;display: none;" align="center">
                <span style="font-size: 16px;font-weight: 700"><?php echo lang('lang'); ?>:</span>
                <button type="button" lang='cn' class='btn'><?php echo lang('chinese'); ?></button>
                <button type="button" lang='en' class='btn'><?php echo lang('english'); ?></button>
                <button type="button" lang='co' class='btn'><?php echo lang('complex'); ?></button>
            </td>
            </tr>
        </table>
    </div>
  <div class="clearfix">
  	<div class="loginmsg"><?php echo $conpayname; ?><strong><a href="#"> <?php echo $username; ?></a></strong>您好，欢迎使用童享云 <em><a style="display: none;" href="#"><?php echo lang('account set'); ?></a></em><?php echo $expiremsg; ?></div>
    <div class="op-btn clearfix">
    	<ul>
            <li class="logout"><a href="<?php echo url('Admin/index/loginsite'); ?>" target="_parent"><?php echo lang('cancel'); ?></a></li>
            <?php if($siteflag==1) { ?>
            <li><a href="<?php echo url('admin/index/leftbar'); ?>" target="leftFrame">返回</a></li>
            <?php } ?>
        </ul>
    </div>
  </div>
</div>
<script>
    $('.btn').click(function(){
        var data={'lang':$(this).attr('lang')};
        $.get("<?php echo url('index/lang'); ?>",data,function(){
            window.parent.location.reload();
            //location.reload();
        })
    });
</script>
</body>
</html>
