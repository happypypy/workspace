<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"D:\workspace\work\public/../application/admin\view\template\index.html";i:1561691682;}*/ ?>
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
    $.ajax({
        url:"<?php echo url('role/delchecked'); ?>",
        data:"id="+s,
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
                                            <span class="oa_title-btn">
                                                <?php if($idsite==1){ ?>
                                                <ul>
                                                  <li class="oa_selected">
                                                      <a href="javascript:CustomOpen('<?php echo url('template/tempdeal','&action=add'); ?>', 'template','模版添加', 400,400)">添加模版</a>
                                                  </li>
                                                </ul>
                                                <?php } ?>
                                            </span>
                                            <span class="oa_ico-left"></span>
                                            模版列表
                                        </div>
                                        <div>
                                            <style>
                                                .list{
                                                    float: left;
                                                    margin: 1px;
                                                }
                                            </style>
                                            <?php if(is_array($template_list) || $template_list instanceof \think\Collection || $template_list instanceof \think\Paginator): $i = 0; $__LIST__ = $template_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                            <li class="list" style=" margin: 5px; border: 1px solid #8D8D8D;overflow: hidden">
                                                <table cellpadding="0" cellspacing="0" border="0">
                                                    <tr><td>
                                                        <div style="left: 0px; height: 500px;width: 100%;overflow: hidden;">
                                                        <a href="/template/<?php echo $vo['dirname']; ?>/index.jpg" title="点击查看" target="_blank"><img style="width:200px; " src="/template/<?php echo $vo['dirname']; ?>/index.jpg" alt="" /></a>
                                                        </div>
                                                    </td></tr>
                                                    <tr><td>
                                                        <div style=" padding: 10px;  background: #ccc">
                                                            <?php echo $vo['temname']; ?>&nbsp;&nbsp;&nbsp;
                                                            <?php if(strtolower($vo['dirname']) == strtolower($selcode)): ?>
                                                            <a style="color: red">已启用</a>
                                                            <?php else: ?><a href="<?php echo url('template/changetemplate','code='.$vo['dirname']); ?>">启用</a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td></tr>
                                                </table>
                                            </li>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="oa_bottom clearfix" style="border-right:0;">
                                <div class="clearfix">
                                    <div class="oa_page-controls">
                                        <ul><li><?php echo $page->show(); ?></li></ul>
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