<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:84:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\index\leftbar.html";i:1561691687;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/static/css/leftbar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript">function AddMainTab(url,code,title){window.parent.mainFrame.TabAdd(url,title,code);}</script>
<script type="text/javascript">	
	$(function(){
				$(".leftbar dl.nav dd").hide();
				$(".leftbar dl.nav dt").click(function(){
					$(".leftbar dl.nav dd").not($(this).next()).hide();
					$(".leftbar dl.nav dt").not($(this).next()).removeClass("nav-title");
					$(this).next().slideToggle(1000);
					$(this).toggleClass("nav-title");
					});
				});
</script>
</head>
<body>
<div class="leftbar" id="leftbar">
	<dl class="nav">
    <?php if(is_array($catalist) || $catalist instanceof \think\Collection || $catalist instanceof \think\Paginator): $i = 0; $__LIST__ = $catalist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
  	<dt><span class="nav-ico"><img src="<?php echo $vo['chrimgpath']; ?>" width="22" height="22" /></span><?php echo HtmlEncode($vo['chrname']); ?></dt>
    <dd>
      <ul class="clearfix">
      <?php if(is_array($modulist) || $modulist instanceof \think\Collection || $modulist instanceof \think\Paginator): $i = 0; $__LIST__ = $modulist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($vo['chrcode'] == $v['codecatalog'] && $v['idsite'] == 0): ?>
        <li id="addTabBtn"><a href="javascript:;" onclick="AddMainTab('<?php echo url($v['action']); ?>','<?php echo $v['chrcode']; ?>','<?php echo $v['chrname']; ?>'); return false;"><?php echo HtmlEncode($v['chrname']); ?></a></li>
      <?php endif; endforeach; endif; else: echo "" ;endif; ?>
      </ul>
    </dd>
    <?php endforeach; endif; else: echo "" ;endif; ?>
  </dl>
</div>
</body>
</html>
