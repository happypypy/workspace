<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:84:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\comment\index.html";i:1561691686;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>角色管理</title>
	<!--<link rel="stylesheet" type="text/css" href="/static/css/bootstrap.min.css">-->
	<link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
	<link href="/static/css/page.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
	<script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
	<script type="text/javascript" src="/static/js/tabscommon.js"></script>
	<script type="text/javascript" src="/static/js/del-checked.js"></script>
	<script type="text/javascript" src="/static/js/layer/layer.js"></script>
	<link rel="stylesheet" type="text/css" href="/static/js/daterangepicker/daterangepicker-bs3.css" />
	<script type="text/javascript" src="/static/js/daterangepicker/moment.min.js"></script>
	<script type="text/javascript" src="/static/js/daterangepicker/daterangepicker.js"></script>
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

        //批量审核
        function pass_checked(type) {
            var b = $(".checked_one");
            var s = '';
            for(var i=0;i<b.length;i++){
                if(b[i].checked){
                    s+= b[i].value+',';
                }
            }
            s = s.substr(0, s.length - 1);
            $.ajax({
                url:"<?php echo url('Admin/comment/allpass'); ?>",
                data:"type="+type+"&id="+s,
                type:"get",
                success:function(msg){
                    if (msg==1){
                        layer.alert('操作成功', {icon: 1}, function(index){
                            location.reload();
                            $(".checked_one").attr("checked",false);
                            layer.close(index);

                        });
                    }else{

                        layer.alert('操作失败', {icon: 2}, function(index){
                            layer.close(index);
                            location.reload();
                        });
                    }
                },
				error:function(msg){
                    layer.alert('操作失败', {icon: 2}, function(index){
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
											<span class="oa_ico-right"></span>
											<span class="oa_ico-left"></span>
											查询
										</div>
										<div class="oa_search-area clearfix">
											<table width="100%" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td>
														<div class="oa_search-type clearfix">
															<form action="<?php echo url('comment/index'); ?>" method="post" id="form1"  onsubmit="return check()">
																<table width="100%" border="0" cellspacing="1" cellpadding="0">
																	<tr>
																		<td style="height: 35px;" width="100" class="oa_cell-left" >类型：</td>
																		<td class="oa_cell-right">
																			<select name="flag">
																				<option value="0">所有评论</option>
																				<option <?php echo $search['flag']==1?"selected":"" ?> value="1">文章</option>
																				<option <?php echo $search['flag']==2?"selected":"" ?> value="2">活动</option>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td style="height: 35px;" width="100" class="oa_cell-left" >评论人：</td>
																		<td class="oa_cell-right">
																			<input type="text" class="form-control"  style="width: 200px;height: 28px" name="username" value="<?php echo $search['username']; ?>">
																		</td>
																	</tr>
																	<tr>
																		<td width="100" style="height: 35px;" class="oa_cell-left">评论内容：</td>
																		<td class="oa_cell-right">
																			<input type="text" class="form-control" style="width: 400px;height: 28px" name="content" value="<?php echo $search['content']; ?>">
																		</td>
																	</tr>
																	<tr>
																		<td width="100"  style="height: 35px;"  class="oa_cell-left">评论时间：</td>
																		<td class="oa_cell-right">
																			<div style="width:200px;float:left;  margin-top: 5px; " class="input-prepend input-group">
																				<span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
																				<input type="text"  style="height: 28px;"  autocomplete="off" id="stime" name="stime" class="form-control"  value="<?php echo $search['stime']; ?>">
																			</div>
																			<div style="padding: 10px; float: left;">-</div>
																			<div style="width:200px; float:left; margin-top: 5px; " class="input-prepend input-group">
																				<span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
																				<input type="text" style="height: 28px;"  autocomplete="off" id="etime" name="etime" class="form-control"  value="<?php echo $search['etime']; ?>">
																			</div>
																			<script language='JavaScript'>seltime("stime","YYYY-MM-DD");seltime("etime","YYYY-MM-DD");</script>
																		</td>
																	</tr>

																	<tr>
																		<td height="30"></td>
																		<td class="oa_cell-right">
																			<input type="submit" value="查询" class="oa_search-btn" />
																		</td>
																	</tr>
																</table>
															</form>
														</div>
													</td>
												</tr>
											</table>
										</div>
										<div class="oa_title clearfix">
                                            <span class="oa_title-btn">
												<ul>

												</ul>
											</span>
											<span class="oa_ico-left"></span>评论列表
										</div>
										<div class="oa_text-list">
											<table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
												<tr class="oa_text-list-title">
													<th width="35" style="text-align:center;"><input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" /></th>
													<th width="80"><span class="oa_arr-text-list-title"></span>评论对象</th>
													<th><span class="oa_arr-text-list-title"></span>评论内容</th>
													<th width="60"><span class="oa_arr-text-list-title"></span>评论人</th>
													<th width="120"><span class="oa_arr-text-list-title"></span>评论时间</th>
													<th width="50"><span class="oa_arr-text-list-title"></span>已回复</th>
													<th width="30"><span class="oa_arr-text-list-title"></span>显示</th>
													<th width="30"><span class="oa_arr-text-list-title"></span>类别</th>
													<th width="30"><span class="oa_arr-text-list-title"></span>操作</th>
												</tr>
												<?php if(is_array($comment_list) || $comment_list instanceof \think\Collection || $comment_list instanceof \think\Paginator): $i = 0; $__LIST__ = $comment_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
												<tr>
													<td align="center">
														<input class="checked_one" type="checkbox" name="id[]" value="<?php echo $vo['id']; ?>" />
													</td>
													<td style="white-space:normal;padding: 5px; "><?php echo $vo['chrtitle']; ?></td>
													<td style="white-space:normal;padding: 5px; ">

														<?php echo $vo['content']; if($vo['intstate']==4){ ?>
														<br> <span style="color: #3c763d">回复</span>&nbsp;&nbsp;<?php echo date('Y-m-d H:i:s',$vo['retime']); ?><br>
														<?php echo $vo['recontent']; }?>

													</td>
													<td><?php echo $vo['username']; ?></td>
													<td><?php echo date('Y-m-d H:i:s',$vo['createtime']); ?></td>
													<td><?php echo $vo['intstate']==4?"是":"<span style='color:red;'>否</span>" ?></td>
													<td><img width="20" height="20" src="/static/images/<?php echo $vo['show']==1?'yes':'cancel' ?>.png" onclick="changeTableVal('comment','id','<?php echo $vo['id']; ?>','show',this)" style="cursor: pointer"/>
													</td>
													<td><?php echo $vo['flag']==2?"活动":"文章" ?></td>
													<td >
														<?php if($cms->CheckPurview('commentmanage','edit')){ ?>

														   <a href="javascript:CustomOpen('<?php echo url('comment/re','id='.$vo['id'],''); ?>', 'comment','回复评论', 520, 360)">回复</a>

														<?php } ?>
													</td>
												</tr>
												<?php endforeach; endif; else: echo "" ;endif; ?>
											</table>
											<div class="oa_bottom clearfix">
												<div class="clearfix">
													<div class="oa_op-btn clearfix" style="display: none;">
														<input name="" value="全部审核通过" onclick="javascript:if(confirm('确定通过？')){pass_checked(1);}else {return false;};" type="button" class="oa_input-submit" />
														<input name="" value="全部不通过" onclick="javascript:if(confirm('确定不通过吗？')){pass_checked(2);}else {return false;};" type="button" class="oa_input-submit" />
													</div>
													<div class="oa_page-controls">
														<ul><li><?php echo $page->show(); ?></li>
														</ul>
													</div>
												</div>

												<div class="oa_bottom-bottom"><em></em></div>
											</div>
											<style type="text/css">
												a{
													cursor: pointer;
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
<script type="text/javascript">
	function check() {
		var starttime = $('#stime').val();
		var endtime = $('#etime').val();
		var start = new Date(starttime.replace("-", "/").replace("-", "/"));
		var end = new Date(endtime.replace("-", "/").replace("-", "/"));
		if (end < start) {
			layer.alert("开始时间不能大于结束时间！");
			return false;
		}
		return true;
	}
</script>
</body>
</html>