<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:86:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\signup\allindex.html";i:1561691689;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>模版管理</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
    <script type="text/javascript" src="/static/js/tabscommon.js"></script>
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
        function copydata(id)
        {
            $.ajax({
                url:"<?php echo url('signup/copytemplate'); ?>",
                data:"id="+id,
                type:"post",
                dataType:"json",
                success:function(msg) {
                    if (msg == 1) {
                        layer.alert('复制成功',{icon:1},function(){window.location="<?php echo url('signup/index'); ?>";});
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
                    //将选中的所有并且排除掉隐藏的复选框
                    if (b[i].checked && b.eq(i).css('display') != 'none') {
                        s += b[i].value + ',';
                    }
                }
                s = s.substr(0, s.length - 1);
            }
            else
            {
                s=value;
            }
            if(s == ''){
                layer.alert('请选择需要删除的模版!', {icon: 2});return;
            }
            $.ajax({
                url:"<?php echo url('Signup/delchecked'); ?>",
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

        function changeTableVal(table,id_name,id_value,field,obj)
        {
            var src = "";

            if($(obj).attr('src').indexOf("cancel.png") > 0 )
            {
                src = '/static/images/yes.png';
                var val= 1;
            }else{
                src = '/static/images/cancel.png';
                var val = 2;
            }
            $.ajax({
                url:"/Admin/Signup/changeTableVal/table/"+table+"/id_name/"+id_name+"/id_value/"+id_value+"/field/"+field+'/value/'+val,
                success: function(data){
                    $(obj).attr('src',src);
                    if(data == 1){
                        layer.alert("修改成功",{icon:1},function (index) {
                            layer.close(index);
                            if(val==1)
                            {
                                $("#del_"+id_value).hide();
                                $("#del_s_"+id_value).hide();
                            }
                            else
                            {
                                $("#del_"+id_value).show();
                                $("#del_s_"+id_value).show();
                            }

                        });
                    }else {
                        layer.alert("修改失败",{icon:2});
                    }
                }
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
                                <li  onclick="javascript:window.location='<?php echo url('Signup/index'); ?>'"><em>个人模版</em></li>
                                <li class="oa_on"><em>所有模版</em></li>
                            </ul>
                        </div>

                    </div>
                    <div class="oa_content-area clearfix">
                        <div class="oa_content-main">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top">
                                    
                                        <div class="oa_title clearfix">
                                            <span class="oa_title-btn">
                                                <ul>
                                                  <?php if($cms->CheckPurview('Signup','add')){ ?>
                                                  <li class="oa_selected">
                                                      <a href="javascript:CustomOpen('<?php echo url('Signup/modi','&action=add',''); ?>', 'Signup','新建报名模版', 400,260)">新建模版管理</a>
                                                  </li>
                                                    <?php } ?>
                                                </ul>
                                              </span>
                                            <span class="oa_ico-left"></span>
                                            模版管理                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center;"><input id="checked"  onclick="DoCheck();"  name="" type="checkbox" value="" /></th>
                                                    <th width="100"><span class="oa_arr-text-list-title"></span>模版名称</th>
                                                    <th><span class="oa_arr-text-list-title"></span>备注</th>
                                                    <th width="100"><span class="oa_arr-text-list-title"></span>用户名称</th>
                                                    <th width="50"><span class="oa_arr-text-list-title"></span>状态</th>
                                                    <th width="120"><span class="oa_arr-text-list-title"></span>创建时间</th>
                                                    <th width="120"><span class="oa_arr-text-list-title"></span>操作</th>
                                                </tr>
                                                <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td align="center"><input class="checked_one" type="checkbox" id="del_s_<?php echo $vo['id']; ?>" <?php echo $vo['state']==1?'style="display:none"':'' ?>  name="id[]" value="<?php echo $vo['id']; ?>" /></td>
                                                    <td><?php echo $vo['title']; ?></td>
                                                    <td><?php echo $vo['remark']; ?></td>
                                                    <td><?php echo $vo['username']; ?></td>
                                                    <td>
                                                        <img width="20" height="20" src="/static/images/<?php echo $vo['state']==1?'yes':'cancel' ?>.png" onclick="changeTableVal('signup_template','id','<?php echo $vo['id']; ?>','state',this)"/>
                                                    </td>
                                                    <td><?php echo date('Y-m-d',strtotime($vo['createtime'])); ?></td>
                                                    <td>
                                                        <a href="javascript:window.location='<?php echo url('Signup/indexsub',array('pid'=>$vo['id'])); ?>'">字段</a>
                                                        <a href="javascript:copydata('<?php echo $vo['id']; ?>')">复制</a>
                                                        <?php if($cms->CheckPurview('Signup','edit')){ ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('Signup/modi','id='.$vo['id'].'&action=edit',''); ?>','Signup','模版修改',400,260)">修改</a>
                                                        <?php } if($cms->CheckPurview('Signup','del')){ ?>
                                                        <a href="#" id="del_<?php echo $vo['id']; ?>" <?php echo $vo['state']==1?'style="display:none"':'' ?>  onclick="del_checked(<?php echo $vo['id']; ?>,'<?php echo $vo['title']; ?>');" >删除</a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </table>
                                            <div class="oa_bottom clearfix">
                                                <div class="clearfix">
                                                    <div class="oa_op-btn clearfix">
                                                        <input name=""  value="删除" onclick="del_checked(0,'');" type="button" class="oa_input-submit" />
                                                    </div>
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
</body>
</html>