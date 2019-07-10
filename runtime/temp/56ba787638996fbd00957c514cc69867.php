<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\member\index.html";i:1562317314;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>用户管理</title>
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

        function setmanage(id,name,flag) {

            var msg='您确定要取消“'+name+'”管理员身份吗？';
            if(flag=="1") {
                msg='您确定要设“'+name+'”为管理员吗？';
            }

            layer.confirm(msg, {
                btn: ['确定','取消'] //按钮
            }, function(){
                setmanage1(id,flag);
            }, function(){

            });
        }

        function setmanage1(id,flag) {
            $.ajax({
                url:"<?php echo url('member/ismanage'); ?>",
                data:{"id":id,"flag":flag},
                type:"post",
                dataType:"json",
                success:function(msg) {
                    if (msg == 1) {
                        layer.alert('操作成功', {icon: 1},function (index) {
                            location.reload();
                            layer.close(index);
                        });

                    } else {
                        layer.alert('操作失败', {icon: 2},function (index) {
                            layer.close(index);
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
        // 选择二维码回调
        function selectQrcodeCallback(qrcode_name,scene_str){
            if(qrcode_name && scene_str){
                var html = '<input type="hidden" name="qrcode_name" value="'+qrcode_name+'" />';
                    html += '<input type="hidden" name="qr_scene_str" value="'+scene_str+'" /><span>'+qrcode_name+'</span>&nbsp;';
                    html += "<a href=\"javascript:CustomOpen('<?php echo url('admin/member/qrcode_select','',''); ?>', 'qrcode_select','重新选择', 500, 285)\" style='color:red;'>[重新选择]</a>";

                $('#select_qrcode').html(html);
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
                                <li class="oa_on"><em>用户管理</em></li>
                                <li onclick="javascript:window.location='<?php echo url('member/followup',''); ?>'" ><em>访谈记录</em></li>
                                <li onclick="javascript:window.location='<?php echo url('member/qrcode_manage',''); ?>'" ><em>二维码管理</em></li>
                            </ul>
                        </div>
                    </div>
                    <div class="oa_content-area clearfix">
                        <div class="oa_content-main">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top">
                                        <div class="oa_title clearfix">
                                            <span class="oa_ico-right" style="margin-right: 10px;">
                                                <span style="color: red;">用户总数：<?php echo $statistical['nomap']['member_total']; ?></span> 
                                                <span style="color: red;">关注用户总数：<?php echo $statistical['nomap']['follow_memer']; ?></span>  
                                                <span style="color: red;">取消用户总数：<?php echo $statistical['nomap']['nofollow_member']; ?></span>  
                                                <span style="color: red;">游客总数：<?php echo $statistical['nomap']['guest_member']; ?></span>
                                            </span>
                                            <span class="oa_ico-left"></span>
                                            <?php echo lang('search'); ?>
                                        </div>
                                        <div class="oa_search-area clearfix">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>
                                                        <div class="oa_search-type clearfix">
                                                            <form action="<?php echo url('member/index'); ?>" method="post" id="form1">
                                                                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                                                    <tr>
                                                                        <td width="150" class="oa_cell-left">用户编号：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input id="memberid" type="text" name="memberid" value="<?php echo $search['memberid']; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="oa_cell-left">用户姓名：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input id="username" type="text" name="username" value="<?php echo $search['username']; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="oa_cell-left">昵称：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input id="nickname" type="text" name="nickname" value="<?php echo $search['nickname']; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="oa_cell-left">二维码：</td>
                                                                        <td class="oa_cell-right" id="select_qrcode">
                                                                            <?php if($search['qr_scene_str']): ?>
                                                                            <input type="hidden" name="qrcode_name" value="<?php echo $search['qrcode_name']; ?>" />
                                                                            <input type="hidden" name="qr_scene_str" value="<?php echo $search['qr_scene_str']; ?>" />
                                                                            <span><?php echo $search['qrcode_name']; ?></span>&nbsp;
                                                                            <a href="javascript:CustomOpen('<?php echo url('admin/member/qrcode_select','',''); ?>', 'qrcode_select','重新选择', 500, 285)" style="color:red;">[重新选择]</a>
                                                                            <?php else: ?>
                                                                            <a href="javascript:CustomOpen('<?php echo url('admin/member/qrcode_select','',''); ?>', 'qrcode_select','选择二维码', 500, 285)">选择二维码</a>
                                                                            <?php endif; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="oa_cell-left">用户类别：</td>
                                                                        <td class="oa_cell-right">
                                                                            <?php if(is_array($hyfl) || $hyfl instanceof \think\Collection || $hyfl instanceof \think\Paginator): $i = 0; $__LIST__ = $hyfl;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                                            <input type="checkbox" name="categoryid[]" value="<?php echo $vo['code']; ?>" <?php if(in_array($vo['code'],explode(",",trim($search['categoryid'],',')))) { echo "checked"; } ?> ><?php echo $vo['name']; ?></input>
                                                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="oa_cell-left">关注状态：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input name="intstate[]" <?php if(in_array("1",explode(",",trim($search['intstate'],',')))) { echo "checked"; } ?> type="checkbox" value="1"/> 关注
                                                                            <input name="intstate[]" <?php if(in_array("2",explode(",",trim($search['intstate'],',')))) { echo "checked"; } ?> type="checkbox" value="2"/> 取消
                                                                            <input name="intstate[]" <?php if(in_array("3",explode(",",trim($search['intstate'],',')))) { echo "checked"; } ?> type="checkbox" value="3"/> 游客
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="oa_cell-left">是否有手机号码：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="istel">
                                                                                <option value="">全部</option>
                                                                                <option value="1" <?php if($search['istel']==1) { echo "selected"; } ?>>有手机号码</option>
                                                                                <option value="2" <?php if($search['istel']==2) { echo "selected"; } ?>>无手机号码</option>
                                                                            </select>

                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="oa_cell-left">手机号码：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input id="chrtel" type="text" name="chrtel" value="<?php echo $search['chrtel']; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="oa_cell-left">订单条件：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="paynumif">
                                                                                <option value="1" <?php if($search['paynumif']==1) { echo "selected"; } ?>>大于</option>
                                                                                <option value="2" <?php if($search['paynumif']==2) { echo "selected"; } ?>>等于</option>
                                                                                <option value="3" <?php if($search['paynumif']==3) { echo "selected"; } ?>>小于</option>
                                                                            </select>
                                                                            <input id="paynum" style="width: 50px;" type="text" name="paynum" value="<?php echo $search['paynum']; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="oa_cell-left">最近来访：</td>
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
                                                                                seltime("vstime","YYYY-MM-DD")
                                                                                seltime("vetime","YYYY-MM-DD")
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
                                                                    <tr>
                                                                        <td class="oa_cell-left">注册时间范围：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="text"  style="width: 100px;" id="rstime" name="rstime" class="form-control"  value="<?php echo $search['rstime']; ?>"> -
                                                                            <input type="text"  style="width: 100px;" id="retime" name="retime" class="form-control"  value="<?php echo $search['retime']; ?>">
                                                                            <script language='JavaScript'>
                                                                                seltime("rstime","YYYY-MM-DD")
                                                                                seltime("retime","YYYY-MM-DD")
                                                                            </script>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="oa_cell-left">取消时间范围：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="text"  style="width: 100px;" id="cstime" name="cstime" class="form-control"  value="<?php echo $search['cstime']; ?>"> -
                                                                            <input type="text"  style="width: 100px;" id="cetime" name="cetime" class="form-control"  value="<?php echo $search['cetime']; ?>">
                                                                            <script language='JavaScript'>
                                                                                seltime("cstime","YYYY-MM-DD")
                                                                                seltime("cetime","YYYY-MM-DD")
                                                                            </script>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="oa_cell-left">跟进时间：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="radio" onclick="javascript:checkvflag1('');" name="vflag1"  value="">所有
                                                                            <input type="radio" onclick="javascript:checkvflag1('1');" name="vflag1"  value="1" <?php if($search['vflag1']==1) { echo "checked"; } ?>>今天
                                                                            <input type="radio" onclick="javascript:checkvflag1('2');" name="vflag1" value="2" <?php if($search['vflag1']==2) { echo "checked"; } ?>>最近7天
                                                                            <input type="radio" onclick="javascript:checkvflag1('3');" name="vflag1" value="3" <?php if($search['vflag1']==3) { echo "checked"; } ?>>最近30天
                                                                            <input type="radio" onclick="javascript:checkvflag1('4');" name="vflag1" value="4" <?php if($search['vflag1']==4) { echo "checked"; } ?>>时间范围
                                                                            <span id="divvtime1">
                                                                            <input type="text" style="width: 100px;" id="gstime" name="gstime" class="form-control"  value="<?php echo $search['gstime']; ?>"> -
                                                                            <input type="text" style="width: 100px;" id="getime" name="getime" class="form-control"  value="<?php echo $search['getime']; ?>">
                                                                            </span>
                                                                            <script language='JavaScript'>
                                                                                seltime("vstime","YYYY-MM-DD")
                                                                                seltime("vetime","YYYY-MM-DD")
                                                                                function checkvflag1(v) {
                                                                                    if(v!='4')
                                                                                    {
                                                                                        $("#divvtime1").hide();
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        $("#divvtime1").show();
                                                                                    }
                                                                                }
                                                                                checkvflag1("<?php echo $search['vflag1']; ?>");
                                                                            </script>
                                                                        </td>
                                                                    </tr>



                                                                    <?php if($cms->CheckPurview('membermanage','manage')){ ?>
                                                                    <tr>
                                                                        <td class="oa_cell-left">所属商务：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="accountid" >
                                                                                <option value="">不限</option>
                                                                                <option value="-1" <?php if($search['accountid']==-1) { echo "selected"; } ?>>未分配</option>
                                                                                <?php foreach($account as $k=>$vo) { ?>
                                                                                <option value="<?php echo $k; ?>" <?php if($search['accountid']==$k) { echo "selected"; } ?>><?php echo $vo; ?></option>
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
                                              </span>
                                            <span class="oa_ico-left"></span>用户管理
                                            <span class="oa_ico-right" style="margin-right: 10px;">
                                                <a href="javascript:submitForm(4)" style="color: red;">用户总数：<?php echo $statistical['map']['member_total']; ?></a> 
                                                <a href="javascript:submitForm(0)" style="color: red;">关注用户总数：<?php echo $statistical['map']['follow_memer']; ?></a>  
                                                <a href="javascript:submitForm(1)" style="color: red;">取消用户总数：<?php echo $statistical['map']['nofollow_member']; ?></a>  
                                                <a href="javascript:submitForm(2)" style="color: red;">游客总数：<?php echo $statistical['map']['guest_member']; ?></a>
                                            </span>
                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">

                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center;">
                                                        <input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" />
                                                    </th>
                                                    <th width="40">ID</th>
                                                    <th width="60">头像</th>
                                                    <th>昵称</th>
                                                    <th width="60">状态</th>
                                                    <th width="120">关注时间</th>
                                                    <th width="120">取消时间</th>
                                                    <th width="60">访问次数</th>
                                                    <th width="60">当前积分</th>
                                                    <th width="80">所属商务</th>
                                                    <th width="80">跟进日期</th>
                                                    <th width="120">最后访问时间</th>
                                                    <th width="45">管理员</th>
                                                    <th width="190" >操作</th>
                                                </tr>
                                                <?php if(is_array($member_list) || $member_list instanceof \think\Collection || $member_list instanceof \think\Paginator): $i = 0; $__LIST__ = $member_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td align="center">
                                                        <input class="checked_one" type="checkbox" name="idmember[]" openid="<?php echo $vo['openid']; ?>" value="<?php echo $vo['idmember']; ?>" userid="<?php echo $vo['idmember']; ?>" />
                                                    </td>
                                                    <td><?php echo $vo['idmember']; ?></td>
                                                    <td><img style="width: 50px;" src="<?php echo empty($vo['userimg'])?'/static/images/userimg.jpg':$vo['userimg'] ?>"></td>
                                                    <td title="<?php echo $vo['nickname']; ?>">
                                                        <?php echo $vo['nickname']; ?>
                                                        <!--&lt;!&ndash;<?php echo $vo["categoryid"]; ?>&ndash;&gt;-->
                                                     <?php if($vo['categoryid']): ?>
                                                            <div style="margin-top: 2px;">[分类:] <?php if(array_key_exists($vo['categoryid'],$usertype)){echo $usertype[$vo['categoryid']] ;}?></div>
                                                    <?php endif; ?>

                                                    </td>
                                                    <td><?php echo ($vo['intstate']==1)?'正常': ($vo['intstate']==2?'取消关注':'游客') ?></td>
                                                    <td><?php if(!(empty($vo['dtsubscribetime']) || (($vo['dtsubscribetime'] instanceof \think\Collection || $vo['dtsubscribetime'] instanceof \think\Paginator ) && $vo['dtsubscribetime']->isEmpty()))): ?><?php echo date("Y-m-d H:i:s",$vo['dtsubscribetime']); endif; ?></if> </td>
                                                    <td><?php if(!(empty($vo['dtunsubscribetime']) || (($vo['dtunsubscribetime'] instanceof \think\Collection || $vo['dtunsubscribetime'] instanceof \think\Paginator ) && $vo['dtunsubscribetime']->isEmpty()))): ?><?php echo date("Y-m-d H:i:s",$vo['dtunsubscribetime']); endif; ?></td>
                                                    <td><?php echo $vo['visitcount']; ?></td>
                                                    <td><?php echo $vo['integral']; ?></td>
                                                    <td><?php echo array_key_exists($vo['iduser'],$account)?$account[$vo['iduser']]:""; ?></td>
                                                    <td>
                                                        <?php
                                                            if(!empty($vo['followuptime']) && $vo['followuptime']>100)
                                                            {
                                                                $tmp="";
                                                                if($vo['followuptime']<time())
                                                                {
                                                                    $tmp=" style='color:red;'";
                                                                }
                                                                echo "<span".$tmp.">".date('Y-m-d',$vo['followuptime'])."</span>";
                                                            }
                                                         ?>
                                                    </td>

                                                    <td><?php if(!(empty($vo['dtlastvisitteim']) || (($vo['dtlastvisitteim'] instanceof \think\Collection || $vo['dtlastvisitteim'] instanceof \think\Paginator ) && $vo['dtlastvisitteim']->isEmpty()))): ?><?php echo date("Y-m-d H:i:s",$vo['dtlastvisitteim']); endif; ?></td>
                                                    <td><?php echo $vo['ismanage']==1?"是":"否"; ?></td>
                                                    <td >
                                                        <a href="javascript:CustomOpen('<?php echo url('admin/member/deal','idmember='.$vo['idmember'].'&action=view',''); ?>', 'memberview','查看用户信息', 800, 560)">查看</a>
                                                        <a href="javascript:CustomOpen('<?php echo url('admin/member/followupdeal','memberid='.$vo['idmember'].'&action=add',''); ?>', 'memberview','新建访谈记录', 600, 350)">新建访谈记录</a>
                                                        <?php if($cms->CheckPurview('membermanage','manage')){ ?>
                                                        <br/>
                                                        <a href="javascript:CustomOpen('<?php echo url('admin/member/memberdeal','idmember='.$vo['idmember'].'&action=edit',''); ?>', 'member','用户信息修改', 460, 460)">修改</a>
                                                        <a href="javascript:;" onclick="javascript:setmanage('<?php echo $vo['idmember']; ?>','<?php echo $vo['nickname']; ?>','<?php echo $vo['ismanage']==1?0:1; ?>')"><?php echo $vo['ismanage']==1?"取消管理":"设为管理"; ?></a>
                                                        <a href="javascript:send_msg('<?php echo $vo['openid']; ?>');">发送消息</a>
                                                        <a href="javascript:CustomOpen('<?php echo url('admin/member/membergiving','idmember='.$vo['idmember'],''); ?>', 'member','赠送积分', 460, 350)">赠送积分</a>
                                                        <?php if($is_cashed && $cms->CheckPurview('membermanage',' give_cashed ')){ ?>
                                                        <br/>
                                                        <a href="javascript:CustomOpen('<?php echo url('admin/member/give_cashed','idmember='.$vo['idmember'],''); ?>', 'member','赠送现金券', 460, 400)">赠送现金券</a>
                                                        <?php }} ?>
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
                                                <div class="clearfix">
                                                    <div class="clearfix">
                                                        <div class="oa_op-btn clearfix">
                                                            <input type="button" name="btn2" value="发送微信"  onclick="javascript:send_msg('')"></input>
                                                            <input  type="button" name="btn3" value="发送短信"  onclick="send_sms_msg('')" />
                                                        </div>
                                                    </div>
                                                    <div class="oa_bottom-bottom"><em></em></div>
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
    function submitForm(select){
        if(select == 4){
            $("[name='intstate[]']").each(function(){
                $(this).attr("checked", false);
            })
        }else{
            $("[name='intstate[]']").each(function(){ 
                var index = $(this).index();
                if(select == index){
                    $(this).attr("checked", true);
                }else{
                    $(this).attr("checked", false);
                }
            });
        }

        $('#form1').submit();
    }
    function send_msg(openid) {
        if(openid=="")
        {
            openid =getid("");
        }
        if(openid=="")
        {
            alert("该用户openid不存在无法发送");
            return;
        }
        openids=openid;
        CustomOpen('<?php echo url('member/sendmsg'); ?>','activity','信息发送',700,400);
    }

    //删除选中
    function getid(value) {

        var b = $(".checked_one");
        var s = '';
        if(value=="")
        {
            for (var i = 0; i < b.length; i++) {
                if (b[i].checked) {
                    if($(".checked_one").eq(i).attr("openid")!="") {
                        s += $(".checked_one").eq(i).attr("openid") + ',';
                    }
                }
            }
            if(s!="")
                s = s.substr(0, s.length - 1);
        }
        else
        {
            s=value;
        }

        return s;
    }

    function send_sms_msg(id) {
        var v=getuserid(id);
        if(v=="")
        {
            alert("请选择要发送信息的用户")
            return;
        }
        userids=v;
        CustomOpen('<?php echo url('member/send_sms_msg'); ?>','activity','短信发送',700,400);
    }

    //删除选中
    function getuserid(value) {

        var b = $(".checked_one");
        var s = '';
        if(value=="")
        {
            for (var i = 0; i < b.length; i++) {
                if (b[i].checked) {
                    if($(".checked_one").eq(i).attr("userid")!="") {
                        s += $(".checked_one").eq(i).attr("userid") + ',';
                    }
                }
            }
            if(s!="")
                s = s.substr(0, s.length - 1);
        }
        else
        {
            s=value;
        }

        return s;
    }
</script>
</body>
</html>