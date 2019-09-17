<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"D:\workspace\work\public/../application/admin\view\node\contentlist.html";i:1564211088;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>节点管理</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
    <script type="text/javascript" src="/static/js/tabscommon.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <script type="text/javascript" src="/static/js/del-checked.js"></script>
    <script type="text/javascript" src="/static/js/clipboard.min.js"></script>
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
        //删除选中
        function del_checked(value) {
            if (confirm('确定删除吗？'))
            { }
            else{return false;}
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
                url:"<?php echo url('node/delchecked'); ?>",
                data:"id="+s+"&nodeid=<?php echo $nodeid; ?>",
                type:"post",
                dataType:"json",
                success:function(msg) {
                    if (msg == 1) {
                        layer.alert('删除成功', {icon: 1});
                        $(".checked_one").attr("checked", false);
                        location.reload();
                    }
                    else if(msg==-1){
                        layer.alert('你没有删除权限', {icon: 4});
                    } else {
                        layer.alert('删除失败', {icon: 2});
                        //location.reload();
                    }
                }
            })
        }
        
        function del_recovery(value) {
            if (!confirm('确定从回收站中恢复吗？'))
            { return false;}
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
                url:"<?php echo url('node/recoverychecked'); ?>",
                data:"id="+s,
                type:"post",
                dataType:"json",
                success:function(msg) {
                    if (msg == 1) {
                        layer.alert('从回收站中恢复成功', {icon: 1});
                        $(".checked_one").attr("checked", false);
                        location.reload();
                    }
                    else if(msg==-1){
                        layer.alert('你没有恢复权限', {icon: 4});
                    } else {
                        layer.alert('从回收站中恢复失败', {icon: 2});
                        //location.reload();
                    }
                }
            })
        }
        
        
        function show_rqcode(o,id)
        {
            var obj=$("#rqcode");
            obj.html("<img width='150' id='img_rqcode' src='/admin/Qrcode/contenturl/sitecode/<?php echo $sitecode; ?>/id/"+id+"' />");
            var yy=event.clientY-150;
            if(yy<10) {yy=10};

            $("#rqcode").css("top",yy+"px").css("left",event.clientX-300+"px");
            obj.show();
        }
        function  close_rqcode() {
            var obj=$("#rqcode");
            obj.html("");
            obj.hide();
        }
    </script>
</head>
<body>
<div class="oa_wrapper">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="oa_wrapper-middle-arr-left oa_wrapper-display"></td>
            <td class="oa_wrapper-middle-arr-middle">
                <div class="oa_location clearfix"><span class="oa_ico-left"></span>location<span class="oa_ico-right"></span></div>
                <div class="oa_main clearfix" style="margin-top:-6px;">
                    <div class="oa_subnav clearfix">

                    </div>

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
                                                            <form action="<?php echo url('node/contentlist'); ?>" method="post" id="form1">
                                                                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                                                    <?php if(is_array($modelfield) || $modelfield instanceof \think\Collection || $modelfield instanceof \think\Paginator): if( count($modelfield)==0 ) : echo "" ;else: foreach($modelfield as $k=>$vl): if($vl['issearch'] == 1): if(in_array(($vl['fieldtype']), explode(',',"4,5,6,7"))): ?>  <!--4 多选 5复选 6单选按钮 7下拉列表-->
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left"><?php echo $vl['fieldalias']; ?>：</td>
                                                                        <td class="oa_cell-right">
                                                                            <?php echo getControl($vl,$search[$vl['fieldname']]); ?>
                                                                        </td>
                                                                    </tr>
                                                                    <?php else: ?>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left"><?php echo $vl['fieldalias']; ?>：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input class="chraccount" type="text" name="<?php echo $vl['fieldname']; ?>" value="<?php echo $search[$vl['fieldname']]; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                    <?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                                                                    <tr>
                                                                        <td height="30"></td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="submit" value="查询" class="oa_search-btn" />
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <input type="hidden" name="nodeid" value="<?php echo $nodeid; ?>"/>
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
                                                  <?php if($cms->CheckPurview('contentmanage','add') && $nodeid>0 && $isonepage==false){ ?>
                                                  <li class="oa_selected">
                                                      <a href="<?php echo url('node/contentdeal','nodeid='.$nodeid.'&action=add',''); ?>">添加内容</a>
                                                  </li>
                                                    <?php } ?>
                                                </ul>
                                              </span>
                                            <span class="oa_ico-left"></span>内容管理
                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center;">
                                                        <input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" />
                                                    </th>
                                                    <th width="50"><span class="oa_arr-text-list-title"></span>内容ID</th>

                                                    <?php if(is_array($modelfield) || $modelfield instanceof \think\Collection || $modelfield instanceof \think\Paginator): if( count($modelfield)==0 ) : echo "" ;else: foreach($modelfield as $k=>$vo): if($vo['isdisplayonlist'] == 1): ?>
                                                            <th width='<?php if(($vo['fieldname'] == 'title')): elseif(($vo['fieldname'] == 'hits') OR  ($vo['fieldname'] == 'idorder')): ?>50<?php elseif($vo['fieldname'] == 'sys00003'): ?>60<?php else: ?>120<?php endif; ?>'><span class="oa_arr-text-list-title"></span><?php echo $vo['fieldalias']; ?></th>
                                                        <?php endif; endforeach; endif; else: echo "" ;endif; ?>

                                                    <th width='195'><span class="oa_arr-text-list-title"></span>操作</th>
                                                </tr>

                                                <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td align="center">
                                                        <input class="checked_one" type="checkbox" name="contentid[]" value="<?php echo $vo['contentid']; ?>" />
                                                    </td>
                                                    <td><?php echo $vo['contentid']; ?></td>
                                                    <?php if(is_array($modelfield) || $modelfield instanceof \think\Collection || $modelfield instanceof \think\Paginator): if( count($modelfield)==0 ) : echo "" ;else: foreach($modelfield as $k=>$vl): if($vl['isdisplayonlist'] == 1): ?>
                                                            <td title="<?php echo $vo[strtolower($vl['fieldname'])]; ?>"><?php echo $vo[strtolower($vl['fieldname'])]; ?></td>
                                                        <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                                    <td>
                                                        <a onmousemove="javascript:show_rqcode(this,'<?php echo $vo['contentid']; ?>')" onmouseout="javascript:close_rqcode()" href="/<?php echo $sitecode; ?>/content/<?php echo $vo['contentid']; ?>" target="_blank">浏览</a>
                                                        <a href="<?php echo url('node/visitlist','dataid='.$vo['contentid'],''); ?>" target="_blank">访问数据</a>
                                                        <a class="data<?php echo $vo['contentid']; ?>"  data-clipboard-text="<?php echo $rooturl; ?>/<?php echo $sitecode; ?>/content/<?php echo $vo['contentid']; ?>" onclick="copycontent('data<?php echo $vo['contentid']; ?>')">复制链接</a>
                                                        <?php if($cms->CheckPurview('contentmanage','edit') & $nodeid>0){ ?>

                                                        <a href="<?php echo url('node/contentdeal','nodeid='.$nodeid.'&contentid='.$vo['contentid'].'&action=edit',''); ?>">修改</a>
                                                        <?php } if($nodeid==0){?>
                                                        <a href="#" onclick="del_recovery(<?php echo $vo['contentid']; ?>);" >恢复</a>
                                                        <?php } if($cms->CheckPurview('contentmanage','del') && $isonepage==false){ ?>
                                                        <a href="#" onclick="del_checked(<?php echo $vo['contentid']; ?>);" >删除</a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </table>
                                            <div class="oa_bottom clearfix">
                                                <div class="clearfix">
                                                    <div class="oa_op-btn clearfix">
                                                        <?php if($cms->CheckPurview('contentmanage','del') && $isonepage==false){ ?>
                                                        <input name="" value="删除" onclick="del_checked(0);" type="button" class="oa_input-submit" />
                                                        <?php } if($nodeid==0){?>
                                                        <input name="" value="恢复" onclick="del_recovery(0);"  type="button" class="oa_input-submit" />
                                                        <?php } ?>
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
<div id="rqcode" style="display: none; height: 160px;width: 160px;background-color: #FFFFFF; border: solid 2px #000000; text-align: center;padding-top: 20px; ;position: absolute;margin-right: 150px" />
<script type="application/javascript">
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
</body>
</html>