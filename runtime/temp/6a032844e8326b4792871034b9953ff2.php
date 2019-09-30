<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:68:"D:\workspace\work\public/../application/admin\view\column\index.html";i:1569815464;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>栏目管理</title>
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
    //删除选中
    function del_checked(value) {
        alert(value);
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
        alert(s);
        $.ajax({
            url:"<?php echo url('Column/delchecked'); ?>",
            data:"id="+s,
            success:function(msg) {
                if (msg == 1) {
                    layer.alert('删除成功', {icon: 1});
                    $(".checked_one").attr("checked", false);
                    location.reload();

                }
                else if(msg==-1){
                    layer.alert('你没有删除权限', {icon: 4});
                }
                else if(msg==3){
                    layer.alert('该栏目有子节点，不能删除', {icon: 4});
                }
                else
                {
                    layer.alert('删除失败', {icon: 2});
                    //location.reload();
                }
            }
        })
    }

    function checkForm(){
        var nodename = $(".nodename").val();
        var modelname = $(".modelname").val();
        var nodeid = $(".nodeid").val();
        if(nodename.length == 0 & modelname.length == 0 & nodeid == 0){
            alert("请输入要搜索的条件");
        }else {
            alert("查找中");
            $("#submit").submit();
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
                    <div class="oa_content-area clearfix">
                        <div class="oa_content-main">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top">
                                        <div class="oa_title clearfix">
                                            <span class="oa_title-btn">
                                                <ul>
                                                  <?php if($cms->CheckPurview('columnmanage','add')){ ?>
                                                  <li class="oa_selected">
                                                      <a href="javascript:CustomOpen('<?php echo url('Column/modi','&action=add&idsite='.$idsite); ?>', 'node','新建栏目', 600,400)">新建栏目</a>
                                                  </li>
                                                    <?php } ?>
                                                </ul>
                                              </span>
                                            <span class="oa_ico-left"></span>栏目列表</div>
                                        <div class="oa_text-list">
                                            <table width="100%" id="table" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <!--<th width="20" style="text-align:center;">
                                                        <input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" />
                                                    </th>-->
                                                    <th width="50">栏目ID</th>
                                                    <th>栏目名称</th>
                                                    <th width="75">模型名称</th>
                                                    <th width="30">类型</th>
                                                    <th width="30">序号</th>
                                                    <th width="55">操作</th>
                                                </tr>
                                                <?php if(is_array($node1) || $node1 instanceof \think\Collection || $node1 instanceof \think\Paginator): $i = 0; $__LIST__ = $node1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr id="<?php echo $vo['nodeid']; ?>" class="<?php echo $vo['level']; ?>_<?php echo $vo['parentid']; ?> <?php echo $vo['parentpath']; ?><?php echo $vo['nodeid']; ?>" <?php if($vo['level'] != 0): ?>style="display:none;"<?php endif; ?>>

                                                    <!--<td align="center">
                                                        <input class="checked_one" type="checkbox" name="nodeid[]" value="<?php echo $vo['nodeid']; ?>" />
                                                    </td>-->
                                                    <td><?php echo $vo['nodeid']; ?></td>
                                                    <td  style="padding-left:<?php echo ($vo['level'] * 3)+1; ?>em">
                                                        <?php if($vo['child'] != 0 and $vo['child'] != 0): ?>
                                                        <span class="<?php echo $vo['level']; ?>_<?php echo $vo['nodeid']; ?>_show  _show" onclick="openShrink('<?php echo $vo['parentpath']; ?>',<?php echo $vo['level']; ?>,<?php echo $vo['nodeid']; ?>,'show');">+</span>
                                                        <span class="<?php echo $vo['level']; ?>_<?php echo $vo['nodeid']; ?>_hide  _hide" onclick="openShrink('<?php echo $vo['parentpath']; ?>',<?php echo $vo['level']; ?>,<?php echo $vo['nodeid']; ?>,'hide');" style="display: none">-</span>
                                                        <?php endif; ?>
                                                        <?php echo $vo['nodename']; ?>
                                                    </td>
                                                    <td><?php echo $vo['modelname']; ?></td>
                                                    <td><?php if($vo['nodetype']==1){ echo "资讯" ;}elseif($vo['nodetype']==2){ echo "产品"; }else{ echo '相册';} ?></td>
                                                    <td><?php echo $vo['idorder']; ?></td>
                                                    <td width="100">
                                                        <?php if($cms->CheckPurview('columnmanage','edit')){ ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('Column/modi','id='.$vo['nodeid'].'&action=edit',''); ?>','node','栏目修改',600,400)">修改</a>
                                                        <?php } if($cms->CheckPurview('columnmanage','del')){ ?>
                                                        <a onclick="javascript:if(confirm('确定删除吗？')){return true;}else{return false;}" href="<?php echo url('Admin/Column/columndel',array('id'=>$vo['nodeid'],'nodetype'=>$vo['nodetype'])); ?>">删除</a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </table>
                                        </div>
                                        <br>
                                        <br>
                                        <div style="display: none" class="oa_title clearfix" >
                                            <span class="oa_title-btn">
                                                <ul>
                                                  <?php if($cms->CheckPurview('columnmanage','add')){ ?>
                                                  <li class="oa_selected">
                                                      <a href="javascript:CustomOpen('<?php echo url('Column/modi','&action=add&nodetype=2&idsite='.$idsite); ?>', 'node','新建栏目', 600,450)">新建栏目</a>
                                                  </li>
                                                    <?php } ?>
                                                </ul>
                                              </span>
                                            <span class="oa_ico-left"></span>产品栏目列表</div>
                                        <div class="oa_text-list" style="display: none">
                                            <table width="100%" id="table" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <!--<th width="20" style="text-align:center;">
                                                        <input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" />
                                                    </th>-->
                                                    <th width="100"><span class="oa_arr-text-list-title"></span>栏目ID</th>
                                                    <th><span class="oa_arr-text-list-title"></span>栏目名称</th>
                                                    <th><span class="oa_arr-text-list-title"></span>模型名称</th>
                                                    <th width="180"><span class="oa_arr-text-list-title"></span>操作</th>
                                                </tr>
                                                <?php if(is_array($node2) || $node2 instanceof \think\Collection || $node2 instanceof \think\Paginator): $i = 0; $__LIST__ = $node2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr id="<?php echo $vo['nodeid']; ?>" class="<?php echo $vo['level']; ?>_<?php echo $vo['parentid']; ?> <?php echo $vo['parentpath']; ?><?php echo $vo['nodeid']; ?>" <?php if($vo['level'] != 0): ?>style="display:none;"<?php endif; ?>>

                                                <!--<td align="center">
                                                    <input class="checked_one" type="checkbox" name="nodeid[]" value="<?php echo $vo['nodeid']; ?>" />
                                                </td>-->
                                                <td><?php echo $vo['nodeid']; ?></td>
                                                <td  style="padding-left:<?php echo ($vo['level'] * 3)+1; ?>em">
                                                    <?php if($vo['child'] != 0 and $vo['child'] != 0): ?>
                                                    <span class="<?php echo $vo['level']; ?>_<?php echo $vo['nodeid']; ?>_show  _show" onclick="openShrink('<?php echo $vo['parentpath']; ?>',<?php echo $vo['level']; ?>,<?php echo $vo['nodeid']; ?>,'show');">+</span>
                                                    <span class="<?php echo $vo['level']; ?>_<?php echo $vo['nodeid']; ?>_hide  _hide" onclick="openShrink('<?php echo $vo['parentpath']; ?>',<?php echo $vo['level']; ?>,<?php echo $vo['nodeid']; ?>,'hide');" style="display: none">-</span>
                                                    <?php endif; ?>
                                                    <?php echo $vo['nodename']; ?>
                                                </td>
                                                <td><?php echo $vo['modelname']; ?></td>
                                                <td width="100">
                                                    <?php if($cms->CheckPurview('columnmanage','edit')){ ?>
                                                    <a href="javascript:CustomOpen('<?php echo url('Column/modi','id='.$vo['nodeid'].'&action=edit&nodetype=2',''); ?>','node','栏目修改',600,450)">修改</a>
                                                    <?php } if($cms->CheckPurview('columnmanage','del')){ ?>
                                                    <a onclick="javascript:if(confirm('确定删除吗？')){return true;}else{return false;}" href="<?php echo url('Admin/Column/columndel','id='.$vo['nodeid']); ?>">删除</a>
                                                    <?php } ?>
                                                </td>
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

<script type="text/javascript">
    function openShrink(path,level,id,type){
        var val = level+1;
        if(type == 'show'){  //展开
            $("."+val+"_"+id).show();
            $("."+level+"_"+id+"_show").hide();
            $("."+level+"_"+id+"_hide").show();
        }else{ //收缩


           var aaa=$("#table tr");
            for(var i=0;i<aaa.length;i++){
                    if(aaa.eq(i).attr("class").indexOf(path+id)!==-1){
                        if(!aaa.eq(i).hasClass(path+id)){
                            aaa.eq(i).hide();
                            aaa.eq(i).find("._show").show();
                            aaa.eq(i).find("._hide").hide();
                        }
                    }
            }
            $("."+level+"_"+id+"_show").show();
            $("."+level+"_"+id+"_hide").hide();
        }

    }
</script>
</body>
</html>