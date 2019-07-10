<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:88:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\menu\resourcedeal.html";i:1561691686;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="oa_pop">
    <div style="height: 6px"></div>
    <div class="oa_pop-main">
        <div class="oa_title clearfix">
            <span class="oa_ico-right"></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            <span><?php echo lang('resource add'); ?></span></span>
        </div>
  <div class="oa_pop-main">
  	<div class="oa_edition">
        <form action="<?php echo url('menu/resourcepost'); ?>" method="post">
            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                <tr>
                    <td width="200" class="oa_cell-left"><span style="color: red;">*</span>&nbsp;<?php echo lang('resource name'); ?>：</td>
                    <td class="oa_cell-right"><input name="chrname" type="text" value="<?php echo $resourceinfo['chrname']; ?>" class="oa_input-200" /></td>
                </tr>
                <tr>
                    <td width="200" class="oa_cell-left"><span style="color: red;">*</span>&nbsp;<?php echo lang('resource code'); ?>：</td>
                    <td class="oa_cell-right"><input name="chrcode" type="text" value="<?php echo $resourceinfo['chrcode']; ?>" class="oa_input-200" /></td>
                </tr>
                <tr>
                    <td class="oa_cell-left"><?php echo lang('remarks'); ?>：</td>
                    <td class="oa_cell-right">
                        <textarea name="textremark" cols="30" rows="4" style="border: solid 1px #CCCCCC"><?php echo $resourceinfo['textremark']; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding:10px;"><input name="" type="submit" value="<?php echo lang('save'); ?>" /></td>
                    <td><input type="hidden" name="action" value="<?php echo $resourceinfo['action']; ?>"></td>
                </tr>
                <tr>
                    <td><input type="hidden" name="modulecode" value="<?php echo $resourceinfo['modulecode']; ?>"></td>
                    <?php if($request != null): ?><td><input type="hidden" name="idresource" value="<?php echo $request['id']; ?>"></td><?php endif; ?>
                </tr>
            </table>
        </form>
    </div>
  </div>
</div>
</body>
</html>