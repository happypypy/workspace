<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"D:\workspace\work\public/../application/admin\view\pattern\modelfieldall.html";i:1561691683;}*/ ?>
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
        var __public="";
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
													  <a href="<?php echo url('pattern/index',['idsite'=>1]); ?>">返回</a>
												  </li>
												</ul>
                      						</span>
											<span class="oa_ico-left"></span>
											模型字段列表：<a href="<?php echo url('pattern/modelfieldall','idmodel='.$request['idmodel']); ?>" style="color: #2E00FF">所有字段</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo url('pattern/modelfieldinfo','idmodel='.$request['idmodel']); ?>">正在使用字段</a>
										</div>
										<div class="oa_subnav clearfix">
											<!--<div class="oa_subnav-list clearfix">
                                              <ul>
                                                <li><em>我的项目</em></li>
                                                <li class="oa_on">任务管理</li>
                                                <li>项目日志</li>
                                              </ul>
                                            </div>-->
										</div>
										<div class="oa_text-list">
											<table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
												<tr class="oa_text-list-title">
													<th width="20" style="text-align:center;"><input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" /></th>
													<th><span class="oa_arr-text-list-title"></span>ID</th>
													<th width="100"><span class="oa_arr-text-list-title"></span>字段名称</th>
													<th width="200"><span class="oa_arr-text-list-title"></span>字段别名</th>
													<th width="150"><span class="oa_arr-text-list-title"></span>字段类型</th>
													<th><span class="oa_arr-text-list-title"></span>是否为空</th>
													<th><span class="oa_arr-text-list-title"></span>列表显示</th>
													<th><span class="oa_arr-text-list-title"></span>是否启用</th>
													<th><span class="oa_arr-text-list-title"></span>排序</th>
													<?php if($idsite == 1): ?>
													<th><span class="oa_arr-text-list-title"></span>操作</th>
													<?php endif; ?>
												</tr>
												<?php if(is_array($fieldlist) || $fieldlist instanceof \think\Collection || $fieldlist instanceof \think\Paginator): $i = 0; $__LIST__ = $fieldlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
												<tr>
													<td align="center"><input class="checked_one" type="checkbox" value="" /></td>
													<td><span><?php echo $vo['idtypefield']; ?></span></td>
													<td><?php echo $vo['fieldname']; ?></td>
													<td><?php echo $vo['fieldalias']; ?></td>
													<td>
														<?php if($vo['fieldtype'] == 1): ?>单行文本<?php endif; if($vo['fieldtype'] == 2): ?>多行文本<?php endif; if($vo['fieldtype'] == 3): ?>编辑器<?php endif; if($vo['fieldtype'] == 4): ?>多选列表框<?php endif; if($vo['fieldtype'] == 5): ?>复选框<?php endif; if($vo['fieldtype'] == 6): ?>单选按钮<?php endif; if($vo['fieldtype'] == 7): ?>下拉列表<?php endif; if($vo['fieldtype'] == 8): ?>数字型<?php endif; if($vo['fieldtype'] == 9): ?>日期<?php endif; if($vo['fieldtype'] == 10): ?>图片<?php endif; if($vo['fieldtype'] == 11): ?>多图片<?php endif; if($vo['fieldtype'] == 12): ?>文件<?php endif; if($vo['fieldtype'] == 13): ?>多文件<?php endif; if($vo['fieldtype'] == 14): ?>单行文本<?php endif; if($vo['fieldtype'] == 15): ?>相关内容<?php endif; if($vo['fieldtype'] == 16): ?>相关栏目<?php endif; if($vo['fieldtype'] == 17): ?>相关栏目(多选)<?php endif; if($vo['fieldtype'] == 18): ?>只读文本<?php endif; if($vo['fieldtype'] == 19): ?>弹窗分页(单选<?php endif; if($vo['fieldtype'] == 20): ?>弹窗分页(多选)<?php endif; if($vo['fieldtype'] == 21): ?>相关产品<?php endif; if($vo['fieldtype'] == 22): ?>日期和时间<?php endif; ?>
													</td>
													<td>
														<?php if($vo['enablenull'] == 1): ?>
														<img width="20" height="20" src="/static/images/yes.png" onclick="changeTableVal('modelfield','idtypefield','<?php echo $vo['idtypefield']; ?>','enablenull',this)"/>
														<?php else: ?><img width="20" height="20" src="/static/images/cancel.png" onclick="changeTableVal('modelfield','idtypefield','<?php echo $vo['idtypefield']; ?>','enablenull',this)"/>
														<?php endif; ?>
													</td>
													<td>
														<?php if($vo['isdisplayonlist'] == 1): ?>
														<img width="20" height="20" src="/static/images/yes.png" onclick="changeTableVal('modelfield','idtypefield','<?php echo $vo['idtypefield']; ?>','isdisplayonlist',this)"/>
														<?php else: ?><img width="20" height="20" src="/static/images/cancel.png" onclick="changeTableVal('modelfield','idtypefield','<?php echo $vo['idtypefield']; ?>','isdisplayonlist',this)"/>
														<?php endif; ?>
													</td>
													<td>
														<?php if($vo['isusing'] == 1): ?>
														<img width="20" height="20" src="/static/images/yes.png" onclick="changeTableVal('modelfield','idtypefield','<?php echo $vo['idtypefield']; ?>','isusing',this)"/>
														<?php else: ?><img width="20" height="20" src="/static/images/cancel.png" onclick="changeTableVal('modelfield','idtypefield','<?php echo $vo['idtypefield']; ?>','isusing',this)"/>
														<?php endif; ?>
													</td>
													<td><input style="width: 60px" class="idorder" onblur="changeSort('<?php echo $vo['idtypefield']; ?>',this);" type="text" name="idorder" value="<?php echo $vo['idorder']; ?>" /></td>
													<?php if($idsite == 1): ?>
													<td width="100">
														<?php if($cms->CheckPurview('modelfield','edit')){ ?>
														<a href="javascript:CustomOpen('<?php echo url('pattern/fielddeal','idmodel='.$request['idmodel'].'&idfield='.$vo['idtypefield'].'&action=edit',''); ?>','role','字段内容修改',680, 760)"><?php echo lang('revise'); ?></a>
														<?php } ?>
													</td>
													<?php endif; ?>
												</tr>
												<?php endforeach; endif; else: echo "" ;endif; ?>
											</table>
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
<script type="text/javascript">

    function changeSort(id_name,obj){
        var id_value = $(obj).val();
        $.ajax({
            url:"/Admin/Pattern/fieldsort/id_name/"+id_name+"/id_value/"+id_value,
            success: function(data){
                if(data == 1){
                    layer.alert("修改成功",{icon:1});
                }
            }
        });
    }
</script>
</html>