<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:80:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\index\bar.html";i:1561691687;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title></title>
<style type="text/css"> 
<!--
html,body{height:100%;}
-->
</style>
</head>
<body style="height:100%;background:url(/static/images/oa_bg-bar.gif) 0 0 repeat-y; padding:0; margin:0;">
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
	<tr>
		<td valign="middle" align="left" style="background:url(/static/images/oa_bg-tabs-top.gif) 0 0 no-repeat;cursor:hand" onClick="setFrames();"><img id="ctlImg" src="/static/images/oa_arr-bar-hid.gif" alt="隐藏菜单" border="0"  /></td>
	</tr>
</table>
<script type="text/javascript"> 
<!--
var cImg=new Image();
var oImg=new Image();
cImg.src="/static/images/oa_arr-bar-show.gif";
oImg.src="/static/images/oa_arr-bar-hid.gif";
function setFrames()
{
	var pWindow=window.parent;
	if(pWindow!=null)
	{
		var frames=pWindow.document.getElementById("pFrame");
		var ctl=document.getElementById("ctlImg");
		if(ctl!=null)
		{
			if(ctl.alt=="隐藏菜单")
			{
				frames.cols="0,12,*";
				ctl.src=cImg.src;
				ctl.alt="显示菜单";
			}
			else
			{
				frames.cols="200,12,*";
				ctl.src=oImg.src;
				ctl.alt="隐藏菜单";
			}
		}
	}
}
-->
</script>
</body>
</html>

