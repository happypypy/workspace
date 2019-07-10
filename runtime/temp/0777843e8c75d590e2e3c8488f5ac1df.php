<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:84:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\pattern\index.html";i:1561691683;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>角色管理</title>
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
		if(s.length == 0){
			layer.alert('您未选择要删除的信息',{icon:2});
			return false;
		}
		$.ajax({
			url:"<?php echo url('pattern/delchecked'); ?>",
			data:"id="+s,
			type:"post",
			dataType:"json",
			async:false, //设置同步
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
			},
			error:function(msg){
				layer.alert('<?php echo lang('del fail'); ?>', {icon: 2}, function(index){
					layer.close(index);
					location.reload();
				});
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
					<div class="oa_content-area clearfix">
						<div class="oa_content-main">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td valign="top">
										<div class="oa_title clearfix">
                                            <span class="oa_title-btn">
												<ul>
												  <li class="oa_selected">
													  <?php if($cms->CheckPurview('patternmanage','add')){ ?>
													  <a href="javascript:CustomOpen('<?php echo url('pattern/patterndeal','action=add'); ?>', 'role','新建模型', 380,280)">新建模型</a>
													  <?php } ?>
												  </li>
												</ul>
											</span>
											<span class="oa_ico-left"></span>模型列表
										</div>
										<div class="oa_text-list">
											<table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
												<tr class="oa_text-list-title">
													<th width="20" style="text-align:center;"><input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" /></th>
													<th width="20"><span class="oa_arr-text-list-title"></span>ID</th>
													<th width="80"><span class="oa_arr-text-list-title"></span>模型名称</th>
													<th><span class="oa_arr-text-list-title"></span>描述</th>
													<th width="60"><span class="oa_arr-text-list-title"></span>是否启用</th>
													<th width="60"><span class="oa_arr-text-list-title"></span>是否公用</th>
													<th width="110"><span class="oa_arr-text-list-title"></span>操作</th>
												</tr>
												<?php if(is_array($modellist) || $modellist instanceof \think\Collection || $modellist instanceof \think\Paginator): $i = 0; $__LIST__ = $modellist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
												<tr>
													<td align="center"><input class="checked_one" type="checkbox" value="<?php echo $vo['idmodel']; ?>" /></td>
													<td><span><?php echo $vo['idmodel']; ?></span></td>
													<td><?php echo $vo['modelname']; ?></td>
													<td><?php echo $vo['remark']; ?></td>
													<td><?php echo $vo['isusing']==1?"启用":"未启用"; ?></td>
													<td><?php echo $vo['ispublic']==1?"是":"否"; ?></td>
													<td width="100">

														<?php if($cms->CheckPurview('patternmanage','edit')){ ?>
														<a href="javascript:CustomOpen('<?php echo url('pattern/patterndeal','idmodel='.$vo['idmodel'].'&action=edit'); ?>','role','模型编辑',380,280)"><?php echo lang('revise'); ?></a>
														<?php } if($cms->CheckPurview('modelfield','view')){ ?>
														<a href="<?php echo url('pattern/modelfieldinfo','idmodel='.$vo['idmodel']); ?>">字段列表</a>
														<?php } if($cms->CheckPurview('patternmanage','del')){ ?>
														<a onclick="javascript:if(confirm('确定删除吗？')){return true;}else {return false;};" href="<?php echo url('pattern/modeldel','idmodel='.$vo['idmodel'],''); ?>"><?php echo lang('delete'); ?></a>
														<?php } ?>

													</td>
												</tr>
												<?php endforeach; endif; else: echo "" ;endif; ?>
											</table>
											<div class="oa_bottom clearfix">
												<div class="clearfix">
													<div class="oa_op-btn clearfix">
														<input name="" value="<?php echo lang('delete'); ?>" onclick="javascript:if(confirm('确定删除吗？')){del_checked();}else {return false;};" type="button" class="oa_input-submit" />
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