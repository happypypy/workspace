<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:88:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\indexconfig\index.html";i:1561691685;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>首页配置</title>
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
                url:"<?php echo url('Indexconfig/delchecked'); ?>",
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
                                <li class="oa_on"><em>首页配置</em></li>
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
                                                            <form action="" method="post" id="form1">
                                                                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                                                                                                                        <tr>
                                                                        <td width="100" class="oa_cell-left">代号：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input  type="text" name="chrcode" value="<?php echo $search['chrcode']; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                                                                                        <tr>
                                                                        <td width="100" class="oa_cell-left">名称：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input  type="text" name="chrname" value="<?php echo $search['chrname']; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                                                                                        <tr>
                                                                        <td height="30"></td>
                                                                        <td class="oa_cell-right"><input  name="" type="submit" value="<?php echo lang('search'); ?>" class="oa_search-btn" /></td>
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
                                                  <?php if($cms->CheckPurview('Indexconfig','add')){ ?>
                                                  <li class="oa_selected">
                                                      <a href="javascript:CustomOpen('<?php echo url('Indexconfig/modi','&action=add',''); ?>', 'Indexconfig','新建数据', 560,420)">新建数据</a>
                                                  </li>
                                                    <?php } ?>
                                                </ul>
                                              </span>
                                            <span class="oa_ico-left"></span>
                                            首页配置                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center;"><input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" /></th>
                                                                                                        <th width="130"><span class="oa_arr-text-list-title"></span>代号</th>
                                                                                                        <th><span class="oa_arr-text-list-title"></span>名称</th>
                                                                                                        <th width="50"><span class="oa_arr-text-list-title"></span>类型</th>
                                                                                                        <th width="60"><span class="oa_arr-text-list-title"></span>在开方式</th>
                                                                                                        <th width="70"><span class="oa_arr-text-list-title"></span>返回记录数</th>
                                                                                                        <th width="60"><span class="oa_arr-text-list-title"></span>显示类型</th>
                                                                                                        <th width="55"><span class="oa_arr-text-list-title"></span>操作</th>
                                                </tr>
                                                <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td align="center"><input class="checked_one" type="checkbox" name="id[]" value="<?php echo $vo['id']; ?>" /></td>

                                                                                                        <td><?php echo $vo['chrcode']; ?></td>
                                                                                                        <td><?php echo $vo['chrname']; ?></td>
                                                                                                        <td><?php echo $vo['inttype']; ?></td>
                                                                                                        <td><?php echo $vo['intopentype']; ?></td>
                                                                                                        <td><?php echo $vo['inttopn']; ?></td>
                                                                                                        <td><?php echo $vo['distype']; ?></td>
                                                    
                                                    <td>
                                                        <?php if($cms->CheckPurview('Indexconfig','edit')){ ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('Indexconfig/modi','id='.$vo['id'].'&action=edit',''); ?>','Indexconfig','数据修改',560,420)">修改</a>
                                                        <?php } if($cms->CheckPurview('Indexconfig','del')){ ?>
                                                        <a href="#" onclick="del_checked(<?php echo $vo['id']; ?>,'<?php echo $vo['chrname']; ?>');" >删除</a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </table>
                                            <div class="oa_bottom clearfix">
                                                <div class="clearfix">
                                                    <div class="oa_op-btn clearfix">
                                                        <input name="" value="删除" onclick="del_checked(0,'');" type="button" class="oa_input-submit" />
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