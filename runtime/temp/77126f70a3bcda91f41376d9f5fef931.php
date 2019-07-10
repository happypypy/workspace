<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:86:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\index\indexsite.html";i:1561691687;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>童享云-机构管理端</title>
</head>
<frameset rows="88,*" cols="*" frameborder="no" border="0" framespacing="0">
  <frame src="<?php echo url('admin/index/header'); ?>" name="topFrame" scrolling="No" noresize="noresize" id="topFrame" title="topFrame" />
  <frameset rows="*" cols="200,12,*" framespacing="0" frameborder="no" border="0" id="pFrame">
    <frame src="<?php echo url('admin/index/leftbar1'); ?>" name="leftFrame" scrolling="auto" id="leftFrame" title="leftFrame" />
    <frame src="<?php echo url('admin/index/bar'); ?>" name="leftFrame" scrolling="No" noresize="noresize" id="leftFrame" title="leftFrame" />
    <frame src="<?php echo url('admin/index/tabs'); ?>" name="mainFrame" id="mainFrame" title="mainFrame" />
  </frameset>
</frameset>
<noframes><body>
<?php echo url("","",true,false);?>
</body></noframes>
</html>

