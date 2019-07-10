<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:86:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\member\followup.html";i:1561691687;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>访谈记录管理</title>
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
        //删除选中
        function del_checked(url) {
            var b = $(".checked_one");
            var s = '';
            for (var i = 0; i<b.length; i++) {
                if (b[i].checked) {
                    s += b[i].value + ',';
                }
            }
            s = s.substr(0, s.length - 1);

            $.ajax({
                url:url,
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
                    <?php if($onlyMember == 0): ?>
                    <div class="oa_subnav clearfix">
                        <div class="oa_subnav-tab clearfix">
                            <ul>
                                <li onclick="javascript:window.location='<?php echo url('member/index',''); ?>'"><em>用户管理</em></li>
                                <li class="oa_on"><em>访谈记录</em></li>
                                <li onclick="javascript:window.location='<?php echo url('member/qrcode_manage',''); ?>'" ><em>二维码管理</em></li>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="oa_content-area clearfix">
                        <div class="oa_content-main">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top">
                                        <div class="oa_title clearfix"  style="<?php if($onlyMember == 1): ?>display: none;<?php endif; ?>">
                                            <span class="oa_ico-right"></span>
                                            <span class="oa_ico-left"></span>
                                            <?php echo lang('search'); ?>
                                        </div>
                                        <div class="oa_search-area clearfix" style="<?php if($onlyMember == 1): ?>display: none;<?php endif; ?>">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>
                                                        <div class="oa_search-type clearfix">
                                                            <form action="<?php echo url('member/followup'); ?>" method="post" id="form1">
                                                                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                                                    <tr>
                                                                        <td class="oa_cell-left" width="150">跟进时间：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="radio" onclick="javascript:checkvflag('');" name="vflag"  value="">所有
                                                                            <input type="radio" onclick="javascript:checkvflag('1');" name="vflag"  value="1" <?php if($search['vflag']==1) { echo "checked"; } ?>>今天
                                                                            <input type="radio" onclick="javascript:checkvflag('2');" name="vflag" value="2" <?php if($search['vflag']==2) { echo "checked"; } ?>>最近7天
                                                                            <input type="radio" onclick="javascript:checkvflag('3');" name="vflag" value="3" <?php if($search['vflag']==3) { echo "checked"; } ?>>最近30天
                                                                            <input type="radio" onclick="javascript:checkvflag('4');" name="vflag" value="4" <?php if($search['vflag']==4) { echo "checked"; } ?>>时间范围
                                                                            <span id="divvtime">
                                                                            <input type="text" style="width: 100px;" id="vstime" name="vstime" class="form-control"  value="<?php echo $search['vstime']; ?>"> -
                                                                            <input type="text" style="width: 100px;" id="vetime" name="vetime" class="form-control"  value="<?php echo $search['vetime']; ?>">
                                                                            </span>
                                                                            <script language='JavaScript'>
                                                                                seltime("vstime","YYYY-MM-DD");
                                                                                seltime("vetime","YYYY-MM-DD");
                                                                                function checkvflag(v) {
                                                                                    if(v!='4')
                                                                                    {
                                                                                        $("#divvtime").hide();
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        $("#divvtime").show();
                                                                                    }
                                                                                }
                                                                                checkvflag("<?php echo $search['vflag']; ?>");
                                                                            </script>
                                                                        </td>
                                                                    </tr>
                                                                    <?php if($cms->CheckPurview('membermanage','manage')){ ?>
                                                                    <tr>
                                                                        <td class="oa_cell-left">所属商务：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="userid" >
                                                                                <option value="">所有商务</option>
                                                                                <?php foreach($account as $k=>$vo) { ?>
                                                                                <option value="<?php echo $k; ?>" <?php if($search['userid']==$k) { echo "selected"; } ?>><?php echo $vo; ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                    <?php } ?>
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
                                                <?php if($onlyMember == 1 and $memberid > 0): ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('admin/member/followupdeal','memberid='.$memberid.'&action=add',''); ?>', 'memberview','新建访谈记录', 600, 620)">新建访谈记录</a>
                                                <?php endif; ?>
                                              </span>
                                            <span class="oa_ico-left"></span>用户管理
                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">

                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center; display: none;" >
                                                        <input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" />
                                                    </th>
                                                    <th width="100">商务名称</th>
                                                    <th width="100">用户名称</th>
                                                    <th width="100">访谈方式</th>
                                                    <th >访谈内容</th>
                                                    <th width="120">跟进时间</th>
                                                    <th width="120">创建时间</th>
                                                    <th width="200" style="display: none;" >操作</th>
                                                </tr>
                                                <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td align="center" style="display: none;">
                                                        <input class="checked_one" type="checkbox" name="id[]" value="<?php echo $vo['id']; ?>" />
                                                    </td>
                                                    <td><?php echo $vo['username']; ?></td>
                                                    <td><?php echo $vo['membername']; ?></td>
                                                    <td><?php echo ($vo['inttype']==1)?'微信/QQ': ($vo['inttype']==2?'电话':'面谈') ?></td>
                                                    <td style="padding-top: 5px; padding-right: 5px; padding-bottom: 5px; padding-left: 5px; white-space: normal;"><?php echo $vo['content']; ?></td>
                                                    <td>
                                                    <?php
                                                            if(!empty($vo['uptime']) && $vo['uptime']>100)
                                                        {
                                                        $tmp="";
                                                        if($vo['uptime']<time())
                                                        {
                                                        $tmp=" style='color:red;'";
                                                        }
                                                        echo "<span".$tmp.">".date('Y-m-d H:i:s',$vo['uptime'])."</span>";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php if(!(empty($vo['createtime']) || (($vo['createtime'] instanceof \think\Collection || $vo['createtime'] instanceof \think\Paginator ) && $vo['createtime']->isEmpty()))): ?><?php echo date("Y-m-d H:i:s",$vo['createtime']); endif; ?></td>
                                                    <td  style="display: none">
                                                        <a href="javascript:CustomOpen('<?php echo url('admin/member/memberdeal','id='.$vo['id'].'&action=edit',''); ?>', 'member','用户信息修改', 450, 560)">修改</a>
                                                        <a onclick="javascript:if (confirm('确定删除吗？')) { return true;}else{return false;};" href='<?php echo url('member/del','id='.$vo['id'],''); ?>'>删除</a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </table>
                                            <div class="oa_bottom clearfix">
                                                <div class="clearfix">
                                                    <div class="oa_op-btn clearfix">

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
</body>
</html>