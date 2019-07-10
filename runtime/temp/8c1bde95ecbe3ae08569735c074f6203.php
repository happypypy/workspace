<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:90:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\account\accountdeal.html";i:1561691685;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
<link href="/static/css/page.css?1" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="oa_pop">
  <div class="oa_pop-main">
      <div style="height: 6px"></div>
      <div class="oa_title clearfix">
          <span class="oa_ico-right"></span>
          <span class="oa_title-btn"></span>
          <span class="oa_ico-left"></span>
        <?php if($account['action'] == 'edit'): ?><?php echo lang('account editor'); elseif($account['action'] == 'view'): ?><?php echo lang('account check'); else: ?><?php echo lang('account add'); endif; ?>
      </div>
  	<div class="oa_edition">
        <form action="<?php echo url('admin/account/post_data'); ?>" method="post">
            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                <tr>
                    <td width="100" class="oa_cell-left"><span style="color: red;">*</span>&nbsp;<?php echo lang('account'); ?>：</td>
                    <td class="oa_cell-right">
                        <?php if($account['action'] == 'add'): ?>
                            <input type="text" name="chraccount" value="<?php echo $account['chraccount']; ?>" class="oa_input-200" />
                        <?php elseif($account['action'] == 'edit'): ?>
                            &nbsp;<?php echo $account['chraccount']; endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">
                        <span style="color: red;">*</span>&nbsp;<?php echo lang('account name'); ?>：
                    </td>
                    <td class="oa_cell-right">
                        <input type="text" name="chrname" value="<?php echo $account['chrname']; ?>" class="oa_input-200" />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left"><span style="color: red;">*</span>&nbsp;<?php echo lang('account password'); ?>：</td>
                    <td class="oa_cell-right">
                        <!-- <input name="chrpassword" autocomplete="off" onfocus="this.type=password" type="text" value="********" class="oa_input-200" /> -->
                        <input name="chrpassword" autocomplete="off"  type="password" value="********" class="oa_input-200" />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left"><?php echo lang('status'); ?>：</td>
                    <td class="oa_cell-right">
                        <input type="radio" name="intflag" value="1"  <?php echo $account['intflag']=="1"?"checked":""; ?>/><?php echo lang('not locked'); ?> &nbsp;&nbsp;&nbsp;
                        <input type="radio" name="intflag" value="2"  <?php echo $account['intflag']=="2"?"checked":""; ?>/><?php echo lang('lock'); ?>
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left"><?php echo lang('serial'); ?>：</td>
                    <td class="oa_cell-right">
                        <input type="text" name="intsn" value="<?php echo $account['intsn']; ?>" class="oa_input-200" />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left"><?php echo lang('describe'); ?>：</td>
                    <td class="oa_cell-right">
                        <textarea name="chrremark" value="3" cols="22" style="border: solid 1px #CCCCCC"><?php echo $account['chrremark']; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">站点ID：</td>
                    <td class="oa_cell-right">
                        <input type="text" name="siteid" value="<?php echo $account['siteid']; ?>" class="oa_input-200" />
                    </td>
                </tr>
                <tr>
                    <td style="padding:10px;">
                        <?php if($account['action'] == 'add'): ?>
                            <input type="submit" name="submit" value="<?php echo lang('add account'); ?>"><?php elseif($account['action'] == 'edit'): ?>
                            <input type="submit" name="submit" value="<?php echo lang('revise'); ?>"><?php else: endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><input type="hidden" name="action" value="<?php echo $account['action']; ?>"/></td>
                    <td><?php if($account['action'] == 'edit'): ?><input type="hidden" name="account_id" value="<?php echo $account['idaccount']; ?>"/><?php endif; ?></td>
                    <?php echo token('__token__', 'sha1'); ?>
                </tr>
            </table>
        </form>
    </div>
  </div>

</div>

</body>
<script type="text/javascript">
    $("#tab tr td").on('click',"a",function(){

        $(this).parent().parent().remove();
    });
    $("#tab tr td a").click(function(){

    })
</script>
</html>