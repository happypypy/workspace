<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:87:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\menu\operatelist.html";i:1561691686;}*/ ?>
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
        data:"type=operate&id="+s,
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

                    <div class="oa_content-area clearfix">
                        <div class="oa_content-main">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top">
                                        <div class="oa_title clearfix">
                                            <span class="oa_ico-right"></span>
                                            <span class="oa_title-btn">
                                            <ul>
                                                <?php if($cms->CheckPurview('operatemanage','add')){ ?>
                                                <li class="oa_selected">
                                                   <a href="javascript:CustomOpen('<?php echo url('menu/operatedeal','moducode='.$code['moducode'].'&resourcecode='.$code['resourcecode'].'&action=add',''); ?>', 'account','<?php echo lang('operate add'); ?>', 410, 290)"><?php echo lang('operate add'); ?></a>
                                                </li>
                                                <?php } ?>
                                            </ul>
                                            </span>
                                            <span class="oa_ico-left"></span>
                                            <?php echo lang('operate list'); ?>
                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center;"><input id="checked" onclick="DoCheck();" name="" type="checkbox" value="" /></th>
                                                    <th><span class="oa_arr-text-list-title"></span><?php echo lang('operate name'); ?></th>
                                                    <th width="200"><span class="oa_arr-text-list-title"></span><?php echo lang('operate code'); ?></th>
                                                    <th width="200"><span class="oa_arr-text-list-title"></span><?php echo lang('operate'); ?></th>
                                                </tr>
                                                <?php if(is_array($operatelist) || $operatelist instanceof \think\Collection || $operatelist instanceof \think\Paginator): $i = 0; $__LIST__ = $operatelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td align="center"><input class="checked_one" name="" type="checkbox" value="<?php echo $vo['idoperate']; ?>" /></td>
                                                    <td><span><?php echo $vo['chrname']; ?></span></td>
                                                    <td><?php echo $vo['chrcode']; ?></td>
                                                    <td width="100">
                                                        <?php if($cms->CheckPurview('resourcemanage','edit')){ ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('menu/operatedeal','id='.$vo['idoperate'].'&action=edit',''); ?>', 'account','<?php echo lang('revise'); ?>', 400, 290)"><?php echo lang('revise'); ?></a>
                                                        <?php } if($cms->CheckPurview('resourcemanage','del')){ ?>
                                                        <a href="<?php echo url('menu/del','operatecode='.$vo['chrcode'].'&id='.$vo['idoperate'],''); ?>"><?php echo lang('delete'); ?></a></td>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </table>
                                            <div class="oa_bottom clearfix">
                                                <div class="clearfix">
                                                    <div class="oa_op-btn clearfix">
                                                        <input value="<?php echo lang('delete'); ?>" onclick="del_checked();" type="button" class="oa_input-submit" />
                                                        <input value="<?php echo lang('return'); ?>" onclick="back();" type="button" class="oa_input-submit" />
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