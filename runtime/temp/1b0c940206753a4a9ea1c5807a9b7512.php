<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:96:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\newfeaturerecommend\index.html";i:1561691688;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>新功能推荐</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
    <script type="text/javascript" src="/static/js/tabscommon.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
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
                                <li class="oa_on"><em>新功能推荐</em></li>
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
                                                            <form action="<?php echo url('newfeaturerecommend/index'); ?>" method="post" id="form1">
                                                                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                                                    <tr>
                                                                        <td width="150" class="oa_cell-left">标题：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input id="title" type="text" name="title" value="<?php echo isset($param['title'])?$param['title']: ''; ?>" >
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
                                                    <?php if($cms->CheckPurview('newfeaturerecommendmanage','add')): ?>
                                                    <li class="oa_selected">
                                                        <a href="javascript:CustomOpen('<?php echo url('newfeaturerecommend/edit','&action=add'); ?>', 'Integralmall','新建新功能推荐', 1000,600)">新建新功能推荐</a>
                                                    </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </span>
                                            <span class="oa_ico-left"></span>新功能推荐
                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center;">
                                                        <input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" />
                                                    </th>
                                                    <th width="20">ID</th>
                                                    <th>标题</th>
                                                    <th width="60">是否开放</th>
                                                    <th width="60">创建者ID</th>
                                                    <th width="50">创建者</th>
                                                    <th width="120">更新日期</th>
                                                    <th width="120">创建时间</th>
                                                    <th width="60">操作</th>
                                                </tr>
                                                <?php if(is_array($datalist) || $datalist instanceof \think\Collection || $datalist instanceof \think\Paginator): $i = 0; $__LIST__ = $datalist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td width="20" style="text-align:center;">
                                                        <input class="checked_one" type="checkbox" name="system_update[]"  value="<?php echo $vo['id']; ?>" />
                                                    </td>
                                                    <td width="20"><?php echo $vo['id']; ?></td>
                                                    <td width="60"><?php echo $vo['title']; ?></td>
                                                    <td><?php echo !empty($vo['is_open'])?'是' : '否'; ?></td>
                                                    <td><?php echo $vo['account_id']; ?></td>
                                                    <td><?php echo $vo['chrname']; ?></td>
                                                    <td><?php echo !empty($vo['update_time'])?date("Y-m-d H:i:s", $vo['update_time']) : ''; ?></td>
                                                    <td><?php echo date("Y-m-d H:i:s",$vo['create_time']); ?></td>
                                                    <td>
                                                        <?php if($cms->CheckPurview('newfeaturerecommendmanage','edit')): ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('newfeaturerecommend/edit','action=edit&id='.$vo['id'],''); ?>','modi','新功能推荐修改',1000,600)">修改</a>
                                                        <?php endif; if($cms->CheckPurview('newfeaturerecommendmanage','del')): ?>
                                                        <a href="#" onclick="del_checked(<?php echo $vo['id']; ?>,'测试日志');" >删除</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </table>
                                            <div class="oa_bottom clearfix">
                                                <div class="clearfix">
                                                    <div class="oa_op-btn clearfix">
                                                        <?php if($cms->CheckPurview('newfeaturerecommendmanage','del')): ?>
                                                        <input name="" value="删除" onclick="del_checked(0,'');" type="button" class="oa_input-submit">
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="oa_page-controls">
                                                        <ul>
                                                            <?php echo $page->show(); ?>
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
<script type="text/javascript">
    //删除选中
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
    function del_checked1(value) {
        var b = $(".checked_one");
        var s = '';
        if(value<1){
            for (var i = 0; i < b.length; i++) {
                if (b[i].checked) {
                    s += b[i].value + ',';
                }
            }
            s = s.substr(0, s.length - 1);
        }else{
            s = value;
        }

        $.ajax({
            url:"<?php echo url('newfeaturerecommend/delete'); ?>",
            data:"id="+s,
            type:"post",
            dataType:"json",
            success:function(msg) {
                if (msg == 1) {
                    layer.alert('删除成功', {icon: 1},function (index) {
                        $(".checked_one").attr("checked", false);
                        location.reload();
                        layer.close(index);
                    });
                } else {
                    layer.alert('删除失败', {icon: 2},function (index) {
                        layer.close(index);
                        location.reload();
                    });
                }
            }
        })
    }
</script>
</body>
</html>