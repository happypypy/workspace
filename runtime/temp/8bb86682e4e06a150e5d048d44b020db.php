<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:84:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\menu\modudeal.html";i:1561691686;}*/ ?>
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
            <span><?php echo lang('module name'); ?></span></span>
        </div>
  <div class="oa_pop-main">
  	<div class="oa_edition">
        <form action="<?php echo url('menu/modupost'); ?>" enctype="multipart/form-data" method="post">
            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                <tr>
                    <td width="100" class="oa_cell-left"><span style="color: red;">*</span>&nbsp;<?php echo lang('module name'); ?>：</td>
                    <td class="oa_cell-right"><input name="chrname" type="text" value="<?php echo $moduinfo['chrname']; ?>" class="oa_input-200" /></td>
                </tr>
                <tr>
                    <td width="100" class="oa_cell-left"><span style="color: red;">*</span>&nbsp;<?php echo lang('module code'); ?>：</td>
                    <td class="oa_cell-right"><input name="chrcode" type="text" value="<?php echo $moduinfo['chrcode']; ?>" class="oa_input-200" /></td>
                </tr>
                <tr>
                    <td width="100" class="oa_cell-left"><span style="color: red;">*</span>&nbsp;<?php echo lang('module file'); ?>：</td>
                    <td class="oa_cell-right"><input name="operation" type="text" value="<?php echo $moduinfo['operation']; ?>" class="oa_input-200" /></td>
                </tr>
                <tr>
                    <td width="100" class="oa_cell-left"><span style="color: red;">*</span>&nbsp;<?php echo lang('serial'); ?>：</td>
                    <td class="oa_cell-right"><input name="intsn" type="text" value="<?php echo $moduinfo['intsn']; ?>" class="oa_input-200" /></td>
                </tr>
                <tr>
                    <td width="100" class="oa_cell-left">是否系统：</td>
                    <td class="oa_cell-right">
                        <input type="radio" name="intflag" value="1"<?php echo $moduinfo['intflag']==1?"checked":""; ?>>是
                        <input type="radio" name="intflag" value="2"<?php echo $moduinfo['intflag']==2?"checked":""; ?>>否
                    </td>
                </tr>
                <tr>
                    <td width="100" class="oa_cell-left"><span style="color: red;">*</span>&nbsp;<?php echo lang('column belong'); ?>：</td>
                    <td class="oa_cell-right">
                        <select name="codecatalog">
                            <option value=""><?php echo lang('choice column'); ?></option>
                            <?php if(is_array($columnlist) || $columnlist instanceof \think\Collection || $columnlist instanceof \think\Paginator): $i = 0; $__LIST__ = $columnlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo $vo['chrcode']; ?>" <?php echo $vo['chrcode']==$moduinfo['codecatalog']?"selected":""; ?>><?php echo $vo['chrname']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="200" class="oa_cell-left"><?php echo lang('module picture'); ?>：</td>
                    <td class="oa_cell-right"><?php if($moduinfo['action'] == 'add'): ?>
                        <input name="chrimgpath1" type="hidden" value=""><input type="file" name="chrimgpath"/><?php else: ?>
                        <input name="chrimgpath1" type="text" value="<?php echo $moduinfo['chrimgpath']; ?>" class="oa_input-200" />
                        <input type="file" name="chrimgpath"/>
                        <?php endif; ?></td>
                </tr>
                <tr>
                    <td class="oa_cell-left"><?php echo lang('remarks'); ?>：</td>
                    <td class="oa_cell-right">
                        <textarea name="textremark" cols="30" rows="4" style="border: solid 1px #CCCCCC"><?php echo $moduinfo['textremark']; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <input type="hidden" value="<?php echo $request['flag']; ?>" name="flag">
                    <td colspan="2" style="padding:10px;"><input name="" type="submit" value="<?php echo lang('save'); ?>" /></td>
                    <td><input type="hidden" name="action" value="<?php echo $moduinfo['action']; ?>"></td>
                </tr>
                <tr>
                    <?php if($moduinfo['action'] == 'edit'): ?>
                    <td><input type="hidden" name="moduleid" value="<?php echo $moduinfo['idmodule']; ?>"></td>
                    <?php endif; ?>
                </tr>
            </table>
        </form>
    </div>
  </div>
</div>
</body>
</html>