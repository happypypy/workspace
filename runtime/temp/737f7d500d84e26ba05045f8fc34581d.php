<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:85:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\index\leftbar1.html";i:1561691687;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/static/css/leftbar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript">function AddMainTab(url,code,title){window.parent.mainFrame.TabAdd(url,title,code);}</script>
<script type="text/javascript">
    $(function () {
        //$(".leftbar dl.nav dd").hide();
        $(".leftbar dl.nav dt").click(function () {
            $(".leftbar dl.nav dd").not($(this).next()).hide();
            $(".leftbar dl.nav dt").not($(this).next()).removeClass("nav-title");
            $(this).next().stop().slideToggle(500);
            $(this).toggleClass("nav-title");
        });

        $('.secondMenu').click(function () {
            $(this).next('.thirdMenu').stop().slideToggle(500).parents('#addTabBtn').siblings().find('.thirdMenu').stop().slideUp(500);
        })
    });
</script>
</head>
<body>
<div class="leftbar" id="leftbar">
	<dl class="nav">
    <?php if(is_array($catalist) || $catalist instanceof \think\Collection || $catalist instanceof \think\Paginator): $i = 0; $__LIST__ = $catalist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
  	<dt><span class="nav-ico"><img src="<?php echo !empty($vo['chrimgpath'])?$vo['chrimgpath']:'/uploads/column/ico_01.gif'; ?>" width="22" height="22" /></span><?php echo HtmlEncode($vo['chrname']); ?></dt>
    <dd>
      <ul class="clearfix">
      <?php if(is_array($modulist) || $modulist instanceof \think\Collection || $modulist instanceof \think\Paginator): $i = 0; $__LIST__ = $modulist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($vo['chrcode'] == $v['codecatalog']): ?>
          <!--如果是产品,并且有三级-->
          <?php if($v['chrcode']=="activity" && !empty($activity_arr)) { ?>
        <li id="addTabBtn" class="secondMenuLi"><a href="javascript:;" class="secondMenu"><?php echo HtmlEncode($v['chrname']); ?></a>
            <ul class="thirdMenu">
                <?php if(is_array($activity_arr) || $activity_arr instanceof \think\Collection || $activity_arr instanceof \think\Paginator): $i = 0; $__LIST__ = $activity_arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$activity): $mod = ($i % 2 );++$i;?>
                <li id="addTabBtn"><a href="#" onclick="AddMainTab('<?php echo url($v['action'],'nodeid='.$activity['nodeid']); ?>','<?php echo $v['chrcode'].'_'.$activity['nodeid']; ?>','<?php echo $activity['nodename']; ?>'); return false;"><?php echo HtmlEncode($activity['nodename']); ?></a></li>
                <?php endforeach; endif; else: echo "" ;endif; ?>

            </ul>
        </li>
          <!--如果是资讯,并且有三级-->
          <?php } elseif($v['chrcode']=="node" && !empty($info_arr)) { $hui = 0; ?>
          <li id="addTabBtn" class="secondMenuLi"><a href="javascript:;" class="secondMenu"><?php echo HtmlEncode($v['chrname']); ?></a>
              <ul class="thirdMenu">
                  <?php if(is_array($info_arr) || $info_arr instanceof \think\Collection || $info_arr instanceof \think\Paginator): $i = 0; $__LIST__ = $info_arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?>
                  <li id="addTabBtn"><a href="javascript:;" onclick="AddMainTab('<?php echo url($v['action'],'nodeid='.$info['nodeid']); ?>','<?php echo $v['chrcode'].'_'.$info['nodeid']; ?>','<?php echo $info['nodename']; ?>'); return false;"><?php echo HtmlEncode($info['nodename']); ?></a></li>
                  <?php endforeach; endif; else: echo "" ;endif; ?>
                  <li id="addTabBtn"><a href="javascript:;" onclick="AddMainTab('<?php echo url($v['action'],'nodeid='.$hui); ?>','<?php echo $v['chrcode'].'_'.$hui; ?>','回收站'); return false;">回收站</a></li>
              </ul>
          </li>
          <?php } elseif($v['chrcode']!="node" && $v['chrcode']!="activity")  { ?>
          <li id="addTabBtn" class="secondMenuLi"><a href="javascript:;" class="secondMenu" onclick="AddMainTab('<?php echo url($v['action'],'idsite='.$idsite); ?>','<?php echo $v['chrcode']; ?>','<?php echo $v['chrname']; ?>'); return false;"><?php echo HtmlEncode($v['chrname']); ?></a></li>
          <?php } endif; endforeach; endif; else: echo "" ;endif; ?>
      </ul>
    </dd>
    <?php endforeach; endif; else: echo "" ;endif; ?>
  </dl>
</div>
</body>
</html>