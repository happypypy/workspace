<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\cashed\index.html";i:1561691683;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>活动管理</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
    <script type="text/javascript" src="/static/js/tabscommon.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <script type="text/javascript" src="/static/js/clipboard.min.js"></script>
    <script type="text/javascript" src="/static/js/del-checked.js"></script>
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
        //全选
        function DoCheck(){
            if($("#checked").is(':checked')){
                $(".checked_one").attr("checked", true);
            }else{
                $(".checked_one").attr("checked", false);
            }
        }

        function del_checked(value,remark) {

            var msg='您确定要删除选定的记录吗？';
            if(value>0) {
                msg='您确定要删除“'+remark+'”吗？';
            }

            layer.confirm(msg, {
                btn: ['确定','取消'] //按钮
            }, function(){
                del_checked1(value);
            }, function(){

            });
        }
        function del_recovery(value,remark) {
            var msg='您确定要恢复选定的记录吗？';
            if(value>0) {
                msg='您确定要恢复“'+remark+'”吗？';
            }

            layer.confirm(msg, {
                btn: ['确定','取消'] //按钮
            }, function(index){

                $.ajax({
                    url:"<?php echo url('activity/recovery'); ?>",
                    data:"id="+value,
                    type:"post",
                    dataType:"json",
                    success:function(msg) {
                        if (msg == 1) {
                            layer.alert('恢复成功', {icon:1},function () {
                                location.reload();
                            });
                            $(".checked_one").attr("checked", false);

                        }
                        else if(msg==-1){
                            layer.alert('你没有删除权限', {icon: 5});
                        }
                        else
                        {
                            layer.alert('恢复失败', {icon: 2});
                            //location.reload();
                        }
                    }
                })
                layer.close(index);

            }, function(){

            });
        }

        function copydata(id)
        {
            $.ajax({
                url:"<?php echo url('activity/copydata'); ?>",
                data:"id="+id,
                type:"post",
                dataType:"json",
                success:function(msg) {
                    if (msg == 1) {
                        layer.alert('复制成功',{icon:1},function(){window.location.reload();});
                    }
                    else if(msg==-1){
                        layer.alert('你没有删除权限', {icon: 5});
                    }
                    else
                    {
                        layer.alert('删除失败', {icon: 2});
                        //location.reload();
                    }
                }
            })
        }
        //删除选中
        function del_checked1(value) {
            var b = $(".checked_one");
            var s = '';
            if(value<1)
            {
                for (var i = 0; i < b.length; i++) {
                    if (b[i].checked) {
                        s += b[i].value + ',';
                    }
                }
                s = s.substr(0, s.length - 1);
            }
            else
            {
                s=value;
            }

            $.ajax({
                url:"<?php echo url('cashed/del'); ?>",
                data:"id="+s,
                type:"post",
                dataType:"json",
                success:function(msg) {
                    if (msg == 1) {
                        layer.alert('删除成功', {icon:1});
                        $(".checked_one").attr("checked", false);
                        location.reload();
                    }
                    else if(msg==-1){
                        layer.alert('你没有删除权限', {icon: 5});
                    }
                    else
                    {
                        layer.alert('删除失败', {icon: 2});
                        //location.reload();
                    }
                }
            })
        }

        function show_rqcode(o,id)
        {
            var obj=$("#rqcode");
            obj.html("<img width='150' id='img_rqcode' src='/admin/Qrcode/share_cashed/sitecode/<?php echo $sitecode; ?>/id/"+id+"' />");
            var yy=event.clientY-50;
            if(yy<10) {yy=10};

            $("#rqcode").css("top",yy+"px").css("left",event.clientX-300+"px");
            obj.show();
        }
        function  close_rqcode() {
            var obj=$("#rqcode");
            obj.html("");
            obj.hide();
        }

        function shearch_check() {
            var st=$("#dtstart").val();
            var et=$("#dtend").val();
            if(st!="" && et!="")
            {
                var start=new Date(st.replace("-", "/").replace("-", "/"));
                var end=new Date(et.replace("-", "/").replace("-", "/"));
                if(end<start)
                {
                    layer.alert("创建开始时间不能大于结束时间！")
                     return;
                }
            }

            $("#form1").submit();

        }
        function copycontent(id){
            var clipboard = new ClipboardJS("."+id);
            clipboard.on('success', function(e) {
                alert('复制成功');
            });
            clipboard.on('error', function(e) {
                alert('当前浏览器不支持次功能');
            });
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
                                <?php if($cms->CheckPurview('cashedplan','view')){ ?>
                                <li class="oa_on" onclick="javascript:window.location='<?php echo url('cashed/index',''); ?>'"><em>现金券计划列表 </em></li>
                                <?php } if($cms->CheckPurview('cashedrecord','view')){  ?>
                                <li onclick="javascript:window.location='<?php echo url('cashed/receive_record',''); ?>'"><em>现金券领取记录 </em></li>
                                <?php } if($cms->CheckPurview('newusercashed','set')){?>
                                <li onclick="javascript:window.location='<?php echo url('cashed/new_member_cashed_set',''); ?>'"><em>新用户关注发券设置 </em></li>
                                <?php } if($cms->CheckPurview('cashedreport','view')){?>
                                <li onclick="javascript:window.location='<?php echo url('cashed/cashed_report',''); ?>'"><em>报表 </em></li>
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
                                            搜索
                                        </div>
                                        <div class="oa_search-area clearfix">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>
                                                        <div class="oa_search-type clearfix">
                                                            <form action="" method="post" id="form1">
                                                                <table width="100%" border="0" cellspacing="1" cellpadding="0">

                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">名称：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input style="width: 300px;"  type="text" name="plan_name" value="<?php echo $search['plan_name']; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">是否启用：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="is_open" >
                                                                                <option value="" >所有</option>
                                                                                <option value="1" <?php if(1==$search['is_open']) { echo "selected"; } ?> >启用</option>
                                                                                <option value="2" <?php if(2==$search['is_open']) { echo "selected"; } ?> >禁用</option>
                                                                            </select>

                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td width="100"   class="oa_cell-left">创建时间：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="text"  style="width: 80px;"  id="dtstart" name="dtstart" class="form-control"  value="<?php echo $search['dtstart']; ?>"> -
                                                                            <input type="text" style="width: 80px;" id="dtend" name="dtend" class="form-control"  value="<?php echo $search['dtend']; ?>">
                                                                            <script language='JavaScript'>seltime("dtstart","YYYY-MM-DD");seltime("dtend","YYYY-MM-DD");</script>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td height="30"></td>
                                                                        <td class="oa_cell-right"><input  name="subSearch" type="button" value="搜索" onclick="javascript:shearch_check();" class="oa_search-btn" /></td>
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
                                                  <?php if($cms->CheckPurview('cashedplan','add')){ ?>
                                                  <li class="oa_selected">
                                                      <a href="javascript:CustomOpen('<?php echo url('cashed/modi','&action=add',''); ?>', 'activity','新建现金券计划', 450,450)">新建现金券计划</a>
                                                  </li>
                                                    <?php } ?>
                                                </ul>
                                              </span>
                                            <span class="oa_ico-left"></span>
                                            现金券计划列表                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0"  style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center;"><input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" /></th>
                                                    <th WIDTH="400"><span class="oa_arr-text-list-title"></span>名称</th>
                                                    <th><span class="oa_arr-text-list-title"></span>描述</th>
                                                    <th WIDTH="60"><span class="oa_arr-text-list-title"></span>有效期</th>
                                                    <th WIDTH="105"><span class="oa_arr-text-list-title"></span>创建人</th>
                                                    <th WIDTH="120"><span class="oa_arr-text-list-title"></span>创建时间</th>
                                                    <th WIDTH="60" ><span class="oa_arr-text-list-title"></span>派发数量</th>
                                                    <th WIDTH="60"><span class="oa_arr-text-list-title"></span>是否启用</th>
                                                    <th width="120"><span class="oa_arr-text-list-title"></span>操作</th>
                                                </tr>
                                                <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td align="center"><input class="checked_one" type="checkbox" name="id[]" value="<?php echo $vo['id']; ?>" /></td>
                                                    <td><?php echo $vo['plan_name']; ?></td>
                                                    <td><?php echo $vo['plan_desc']; ?></td>
                                                    <td><?php echo $vo['cashed_validity_day']; ?>天</td>
                                                    <td><?php echo $vo['account_name']; ?></td>
                                                    <td><?php echo $vo['create_time']; ?></td>
                                                    <td><?php echo $vo['cashed_num']; ?></td>
                                                    <td><?php if($vo['is_open'] == 1): ?>启用<?php else: ?>禁用<?php endif; ?></td>
                                                    <td  style="white-space:normal;padding: 5px; ">
                                                        <?php  if($cms->CheckPurview('cashedplan','edit') ){ ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('cashed/modi','id='.$vo['id'].'&action=edit',''); ?>','activity','修改现金券计划',450,450)">修改</a>
                                                        <?php }  if($cms->CheckPurview('cashedplan','delete') ){ ?>
                                                        <a href="#" onclick="del_checked(<?php echo $vo['id']; ?>,'<?php echo $vo['plan_name']; ?>');">删除</a>
                                                        <?php }  if($vo['is_open'] == 1){ ?>
                                                        <a onmousemove="javascript:show_rqcode(this,'<?php echo $vo['id']; ?>')" onmouseout="javascript:close_rqcode()" style="cursor: pointer">扫码分享券</a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </table>
                                            <div class="oa_bottom clearfix">
                                                <div class="clearfix">
                                                    <?php  if($cms->CheckPurview('cashedplan','delete') ){ ?>
                                                    <div class="oa_op-btn clearfix">
                                                        <input name="" value="删除" onclick="del_checked(0,'');" type="button" class="oa_input-submit" />
                                                    </div>
                                                    <?php } ?>
                                                    <div class="oa_page-controls">
                                                        <ul>
                                                            <li><?php echo $page->show(); ?></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="oa_bottom-bottom"><em></em></div>
                                            </div>
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
<div id="rqcode" style="display: none; height: 160px;width: 160px;background-color: #FFFFFF; border: solid 2px #000000; text-align: center;padding-top: 20px; ;position: absolute;margin-right: 150px" />
</body>
</html>