<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:86:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\menu\columnlist.html";i:1561691686;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
<link href="/static/css/page.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
<script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
<script type="text/javascript" src="/static/js/tabscommon.js"></script>
<script type="text/javascript" src="/static/js/del-checked.js"></script>
<script type="text/javascript" src="/static/js/layer/layer.js"></script>

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

//删除选中
function del_checked() {
    var b = $(".checked_one");
    var s = '';
    for(var i=0;i<b.length;i++){
        if(b[i].checked){
            s+= b[i].value+',';
        }
    }
    s = s.substr(0, s.length - 1);
    $.ajax({
        url:"<?php echo url('menu/delchecked'); ?>",
        data:"type=column&id="+s,
        type:"post",
        dataType:"json",
        success:function(msg){
            if (msg==1){
                layer.alert('<?php echo lang('del success'); ?>', {icon: 1}, function(index){
                    location.reload();
                    $(".checked_one").attr("checked",false);
                    layer.close(index);
                });
            }else{
                layer.alert('<?php echo lang('del fail'); ?>', {icon: 2}, function(index){
                    layer.close(index);
                    location.reload();
                });
            }
        }
    })
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
        	<div class="oa_subnav clearfix">
			  <div class="oa_subnav-tab clearfix">
              <ul>
               <?php if($cms->CheckPurview('columnmanage','view')){ ?>
                <li class="oa_on"><em><a href="<?php echo url('menu/columnlist'); ?>"><?php echo lang('column manage'); ?></a></em></li>
               <?php } if($cms->CheckPurview('modulemanage','view')){ ?>
                <li ><em><a href="<?php echo url('menu/modulist'); ?>"><?php echo lang('module manage'); ?></a></em></li>
                <li ><em><a href="<?php echo url('menu/extended_module_list'); ?>"><?php echo lang('extended_module name'); ?></a></em></li>
               <?php } ?>
              </ul>
            </div>
          </div>
          <div class="oa_content-area clearfix">
            <div class="oa_content-main">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td valign="top">
                  	<div class="oa_title clearfix">
                      <span class="oa_ico-right"></span>
                      <span class="oa_title-btn">
                        <ul>
                          <?php if($cms->CheckPurview('columnmanage','add')){ ?>
                          <li class="oa_selected"><a href="javascript:CustomOpen('<?php echo url('menu/columndeal','&action=add',''); ?>', 'account','<?php echo lang('column add'); ?>', 450, 360)"><?php echo lang('column add'); ?></a></li>
                          <?php } ?>
                        </ul>
                      </span>
                      <span class="oa_ico-left"></span>
                      <?php echo lang('column list'); ?>
                    </div>
                    <div class="oa_text-list">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                        <tr class="oa_text-list-title">
                          <th width="20" style="text-align:center;"><input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" /></th>
                          <th><span class="oa_arr-text-list-title"></span><?php echo lang('column name'); ?></th>
                          <th width="45"><span class="oa_arr-text-list-title"></span><?php echo lang('serial'); ?></th>
                          <th width="30"><span class="oa_arr-text-list-title"></span><?php echo lang('system'); ?></th>
                          <th width="55"><span class="oa_arr-text-list-title"></span><?php echo lang('operation'); ?></th>
                        </tr>
                        <?php if(is_array($catalist) || $catalist instanceof \think\Collection || $catalist instanceof \think\Paginator): $i = 0; $__LIST__ = $catalist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr>
                          <td align="center"><input class="checked_one" type="checkbox" value="<?php echo $vo['idcatalog']; ?>" /></td>
                          <td><span><?php echo HtmlEncode($vo['chrname']); ?></span></td>
                          <td><span><?php echo $vo['intsn']; ?></span></td>
                          <td><?php if($vo['intflag'] == 1): ?><?php echo lang('yes'); else: ?><?php echo lang('no'); endif; ?></td>
                          <td width="100">

                            <?php if($cms->CheckPurview('columnmanage','edit')){ ?>
                            <a href="javascript:CustomOpen('<?php echo url('menu/columndeal','id='.$vo['idcatalog'].'&action=edit',''); ?>', 'account','<?php echo lang('revise'); ?>', 450, 380)"><?php echo lang('revise'); ?></a>
                            <?php } if($cms->CheckPurview('columnmanage','del')){ ?>
                            <a onclick="javascript:if(confirm('确定删除吗？')){return true;}else{return false;};" href="<?php echo url('menu/del','columncode='.$vo['chrcode'].'&id='.$vo['idcatalog'],''); ?>"><?php echo lang('delete'); ?></a>
                            <?php } ?>

                          </td>
                        </tr>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                      </table>
                      <div class="oa_bottom clearfix">
                        <div class="clearfix">
                          <div class="oa_op-btn clearfix">
                            <input value="<?php echo lang('delete'); ?>" onclick="javascript:if(confirm('确定删除吗？')){del_checked();}else{return false;};" type="button" class="oa_input-submit" />
                          </div>
                          <div class="oa_page-controls">
                            <ul>
                              <li><?php echo $page->show(); ?></li>
                            </ul>
                          </div>
                        </div>
                        <div class="oa_bottom-bottom"><em></em></div>
                      </div>
                      <style type="text/css">
                        a{
                          cursor: pointer;
                        }
                        .pagination{
                          display: inline;
                          font-size: 14px;
                          letter-spacing: 4px;
                          font-family: "宋体", Gadget, sans-serif;
                        }
                      </style>
                    </div>
                  </td>
                </tr>
              </table>
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