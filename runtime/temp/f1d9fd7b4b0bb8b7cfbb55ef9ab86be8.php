<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"D:\workspace\work\public/../application/admin\view\menu\extended_module_list.html";i:1561691686;}*/ ?>
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


//模块转译
function model_change() {
    var b = $(".checked_one");
    var s = '';
    var val = $("#select").val();
    if(val == ''){
        layer.msg("请选择模块");return;
    }
    for(var i=0;i<b.length;i++){
        if(b[i].checked){
            s+= b[i].value+',';
        }
    }
    s = s.substr(0, s.length - 1);
    $.ajax({
        url:"<?php echo url('menu/modulechange'); ?>",
        data:"id="+s+"&chrcolumncode="+val+'&flag=extended_module&change=2',
        success:function(msg){
            if (msg==1){
                layer.alert('转移成功', {icon: 1}, function(index){
                    location.reload();
                    $(".checked_one").attr("checked",false);
                    layer.close(index);
                });
            }else{
                layer.alert('转移失败', {icon: 2}, function(index){
                    layer.close(index);
                });
            }
        }

    })
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
        data:"type=module&flag=extended_module&id="+s,
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
               <li><em><a href="<?php echo url('menu/columnlist'); ?>"><?php echo lang('column manage'); ?></a></em></li>
               <?php } if($cms->CheckPurview('modulemanage','view')){ ?>
               <li><em><a href="<?php echo url('menu/modulist'); ?>"><?php echo lang('module manage'); ?></a></em></li>
               <li class="oa_on"><em><a href="<?php echo url('menu/extended_module_list'); ?>"><?php echo lang('extended_module name'); ?></a></em></li>
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
                      <span class="oa_ico-left"></span>
                      <?php echo lang('search'); ?>
                    </div>
                    <div class="oa_search-area clearfix">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>
                            <div class="oa_search-type clearfix">
                              <form action="<?php echo url('menu/extended_module_list'); ?>">
                                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                  <tr>
                                    <td width="100" class="oa_cell-left"><?php echo lang('module name'); ?>：</td>
                                    <td class="oa_cell-right"><input name="moduname" type="text" value="<?php echo $request['moduname']; ?>" class="oa_search-input" /></td>
                                  </tr>
                                  <tr>
                                    <td class="oa_cell-left"><?php echo lang('module code'); ?>：</td>
                                    <td class="oa_cell-right"><input name="moducode" type="text" value="<?php echo $request['moducode']; ?>" class="oa_search-input" /></td>
                                  </tr>
                                  <tr>
                                    <td class="oa_cell-left"><?php echo lang('column name'); ?>：</td>
                                    <td class="oa_cell-right">
                                      <select name="columncode">
                                        <option value=""><?php echo lang('column all'); ?></option>
                                        <?php if(is_array($catalist) || $catalist instanceof \think\Collection || $catalist instanceof \think\Paginator): $i = 0; $__LIST__ = $catalist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                        <option value="<?php echo $vo['chrcode']; ?>"<?php echo $vo['chrcode']==$request['columncode']?"selected":""; ?>><?php echo $vo['chrname']; ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                      </select>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td height="30"></td>
                                    <td class="oa_cell-right"><input name="" type="submit" value="<?php echo lang('search'); ?>" class="oa_search-btn" /></td>
                                  </tr>
                                </table>
                              </form>
                            </div>
                          </td>
                        </tr>
                      </table>
                    </div>
                  	<div class="oa_title clearfix">
                      <span class="oa_ico-right"></span>
                      <span class="oa_title-btn">
                        <ul>
                          <?php if($cms->CheckPurview('modulemanage','add')){ ?>
                          <li class="oa_selected"><a href="javascript:CustomOpen('<?php echo url('menu/modudeal','&action=add&flag=extended_module',''); ?>', 'account','<?php echo lang('module add'); ?>', 400, 430)"><?php echo lang('module add'); ?></a></li>
                          <?php } ?>
                        </ul>
                      </span>
                      <span class="oa_ico-left"></span>
                      <?php echo lang('module list'); ?>
                    </div>
                    <div class="oa_text-list">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                        <tr class="oa_text-list-title">
                          <th width="20" style="text-align:center;"><input id="checked" onclick="DoCheck()" name="" type="checkbox" value="" /></th>
                          <th><span class="oa_arr-text-list-title"></span><?php echo lang('module name'); ?></th>
                          <th><span class="oa_arr-text-list-title"></span>模块代号</th>
                          <th><span class="oa_arr-text-list-title"></span><?php echo lang('belong column'); ?></th>
                          <th><span class="oa_arr-text-list-title"></span><?php echo lang('serial'); ?></th>
                          <th><span class="oa_arr-text-list-title"></span><?php echo lang('system'); ?></th>
                          <th width="150"><span class="oa_arr-text-list-title"></span><?php echo lang('operation'); ?></th>
                        </tr>
                        <?php if(is_array($modulist) || $modulist instanceof \think\Collection || $modulist instanceof \think\Paginator): $i = 0; $__LIST__ = $modulist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr>
                          <td align="center"><input class="checked_one" name="moducode[]" type="checkbox" value="<?php echo $vo['idmodule']; ?>" /></td>
                          <td><span><?php echo HtmlEnCode($vo['chrname']); ?></span></td>
                          <td><span><?php echo HtmlEnCode($vo['chrcode']); ?></span></td>
                          <td><?php if(is_array($catalist) || $catalist instanceof \think\Collection || $catalist instanceof \think\Paginator): $i = 0; $__LIST__ = $catalist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><?php echo $vo['codecatalog']==$v['chrcode']?$v['chrname']:""; endforeach; endif; else: echo "" ;endif; ?></td>
                          <td><span><?php echo $vo['intsn']; ?></span></td>
                          <td><?php echo $vo['intflag']==1?"是":"否"; ?></td>
                          <td width="120">

                            <?php if($cms->CheckPurview('resourcemanage','view')){ ?>
                            <a href="<?php echo url('menu/resourcelist','moducode='.$vo['chrcode'],''); ?>"><?php echo lang('resource'); ?></a>
                            <?php } if($cms->CheckPurview('modulemanage','edit')){ ?>
                            <a href="javascript:CustomOpen('<?php echo url('menu/modudeal','code='.$vo['chrcode'].'&action=edit&flag=extended_module',''); ?>', 'account','<?php echo lang('revise'); ?>', 400, 440)"><?php echo lang('revise'); ?></a>
                            <?php } if($cms->CheckPurview('modulemanage','del')){ ?>
                            <a onclick="javascript:if(confirm('确定删除吗？')){return true;}else{return false;};" href="<?php echo url('menu/del','modulecode='.$vo['chrcode'].'&id='.$vo['idmodule'].'&flag=extended_module',''); ?>"><?php echo lang('delete'); ?></a>
                            <?php } ?>
                          <a href="javascript:CustomOpen('<?php echo url('menu/view_site','chrcode='.$vo['chrcode'].'&chrname='.$vo['chrname'],''); ?>', 'menu','<?php echo $vo['chrname']; ?>', 500, 400)">查看企业</a>
                          </td>
                          </td>
                        </tr>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                      </table>
                      <div class="oa_bottom clearfix">
                        <div class="clearfix">
                          <div class="oa_op-btn clearfix">
                           <table>
                             <tr>
                               <td style="background-color: #FFFFFF;border: hidden"><input name="" onclick="javascript:if(confirm('确定删除吗？')){del_checked();}else{return false;};" value="<?php echo lang('delete'); ?>" type="submit" class="oa_input-submit" />&nbsp;</td>
                               <td style="background-color: #FFFFFF;border: hidden">
                                 讲选中模块转入：
                               <select name="chrcolumncode" id="select">
                                 <option value=""><?php echo lang('choice column'); ?></option>
                                 <?php if(is_array($catalist) || $catalist instanceof \think\Collection || $catalist instanceof \think\Paginator): $i = 0; $__LIST__ = $catalist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                 <option value="<?php echo $vo['chrcode']; ?>"><?php echo $vo['chrname']; ?></option>
                                 <?php endforeach; endif; else: echo "" ;endif; ?>
                               </select>
                                 <input  onclick="model_change();" type="button" value="<?php echo lang('save'); ?>">
                               </td>
                             </tr>
                           </table>


                          </div>
                          <div class="oa_page-controls">
                            <ul>
                              <li></li>
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