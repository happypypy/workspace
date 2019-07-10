<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:94:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\integral\integral_index.html";i:1561691689;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>会员管理</title>
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
                                <li class="oa_on"><em>积分管理</em></li>
                                <?php if($cms->CheckPurview('goodsmanage','view')): ?>
                                <li onclick="javascript:window.location='<?php echo url('integral/integral_goods'); ?>'" ><em>商品管理</em></li>
                                <?php endif; if($cms->CheckPurview('goodsmanage','exchangerecord')): ?>
                                <li onclick="javascript:window.location='<?php echo url('integral/exchange_record'); ?>'" ><em>兑换订单</em></li>
                                <?php endif; if($cms->CheckPurview('integralannualreport','view')): ?>
                                <li onclick="javascript:window.location='<?php echo url('integral/integral_report',['action'=>'year']); ?>'" ><em>积分年度报表</em></li>
                                <?php endif; if($cms->CheckPurview('integralmonthlyreport','view')): ?>
                                <li onclick="javascript:window.location='<?php echo url('integral/integral_report',['action'=>'month']); ?>'" ><em>积分月度报表</em></li>
                                <?php endif; if($cms->CheckPurview('integralruleconfig','view')): ?>
                                <li onclick="javascript:window.location='<?php echo url('integral/integral_rule',''); ?>'"><em>积分规则设置</em></li>
                                <?php endif; ?>
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
                                                            <form action="<?php echo url('integral/index'); ?>" method="post" id="form1">
                                                                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                                                    <tr>
                                                                        <td width="150" class="oa_cell-left">用户昵称：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input id="nickname" type="text" name="nickname" value="<?php echo $param['nickname']; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="150" class="oa_cell-left">用户姓名：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input id="chrname" type="text" name="chrname" value="<?php echo $param['chrname']; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="oa_cell-left">积分类别：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="category">
                                                                                <option value="">全部</option>
                                                                                <?php if(is_array($integral_category) || $integral_category instanceof \think\Collection || $integral_category instanceof \think\Paginator): $k = 0; $__LIST__ = $integral_category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?>
                                                                                <option value="<?php echo $k; ?>" <?php echo $k==$param['category']?'selected' : ''; ?>><?php echo $vo; ?></option>
                                                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="oa_cell-left">积分时间</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="radio" onclick="javascript:checkvflag('');" name="vflag"  value="" <?php echo $param['vflag']==0?'checked' : ''; ?>>所有
                                                                            <input type="radio" onclick="javascript:checkvflag('1');" name="vflag"  value="1" <?php echo $param['vflag']==1?'checked' : ''; ?> >今天
                                                                            <input type="radio" onclick="javascript:checkvflag('2');" name="vflag" value="2" <?php echo $param['vflag']==2?'checked' : ''; ?>>最近7天
                                                                            <input type="radio" onclick="javascript:checkvflag('3');" name="vflag" value="3" <?php echo $param['vflag']==3?'checked' : ''; ?>>最近30天
                                                                            <input type="radio" onclick="javascript:checkvflag('4');" name="vflag" value="4" <?php echo $param['vflag']==4?'checked' : ''; ?>>时间范围
                                                                            <span id="divvtime">
                                                                            <input type="text" style="width: 100px;" id="starttime" name="starttime" class="form-control"  value="<?php echo $param['starttime']; ?>"> -
                                                                            <input type="text" style="width: 100px;" id="endtime" name="endtime" class="form-control"  value="<?php echo $param['endtime']; ?>">
                                                                            </span>
                                                                            <script language='JavaScript'>
                                                                                seltime("starttime","YYYY-MM-DD")
                                                                                seltime("endtime","YYYY-MM-DD")
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
                                                                                checkvflag("<?php echo $param['vflag']; ?>");
                                                                            </script>
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
                                                <div style="margin-right:10px;">总积分数量：<?php echo $total_integral; ?></div>
                                            </span>
                                            <span class="oa_ico-left"></span>积分管理
                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center;">
                                                        <input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" />
                                                    </th>
                                                    <th width="20">ID</th>
                                                    <th width="60">头像</th>
                                                    <th>昵称</th>
                                                    <th>姓名</th>
                                                    <th width="80">积分类别</th>
                                                    <th width="140">积分数量</th>
                                                    <th width="80">赠送积分原因</th>
                                                    <th width="120">积分时间</th>
                                                </tr>
                                                <?php if(is_array($member_integral_record) || $member_integral_record instanceof \think\Collection || $member_integral_record instanceof \think\Paginator): $i = 0; $__LIST__ = $member_integral_record;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td align="center">
                                                        <input class="checked_one" type="checkbox" name="integral_record[]"  value="<?php echo $vo['id']; ?>" />
                                                    </td>
                                                    <td><?php echo $vo['id']; ?></td>
                                                    <td><img style="width: 50px;" src="<?php echo empty($vo['userimg'])?'/static/images/userimg.jpg':$vo['userimg'] ?>"></td>
                                                    <td><?php echo $vo['nickname']; ?></td>
                                                    <td><?php echo $vo['chrname']; ?></td>
                                                    <td><?php echo config('integral_category')[$vo['category_id']]; ?></td>
                                                    <td><?php if(in_array(($vo['category_id']), explode(',',"1,2,3,6,7"))): ?>+<?php else: ?>-<?php endif; ?><?php echo $vo['integral']; switch($vo['is_freeze']): case "1": ?>[冻结：活动未完成]<?php break; case "2": ?>[已解冻：活动结束]<?php break; case "3": ?>[已失效：活动退款]<?php break; default: endswitch; ?>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="remark_<?php echo $vo['id']; ?>" value="<?php echo $vo['integral_rmark']; ?>" />
                                                        <div onmousemove="preview(<?php echo $vo['id']; ?>)" onmouseout="closePreview()"><?php echo $vo['integral_rmark']; ?></div>
                                                    </td>
                                                    <td><?php echo date("Y-m-d H:i:s",$vo['create_time']); ?></td>
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
<div id="remark" style="display: none; height: 150px;width: 160px;background-color: #FFFFFF; border: solid 2px #000000; text-align: left;padding:0 10px; line-height: 150%; position: absolute;margin-right: 150px" />
<script type="text/javascript">
        // 预览文字
    function preview(id){
        var text = $("#remark_"+id).val();
        var remark = $("#remark");
        remark.html("<pre style='white-space: pre-wrap;word-wrap: break-word;marign:0;'>"+text+"</pre>");
        var scrollTop = getScrollTop();
        debugger;
        var yy=event.clientY+scrollTop-50;
        if(yy<10) {yy=10};

        $("#remark").css("top",yy+"px").css("left",event.clientX-250+"px");
        remark.show();
    }
    // 关闭预览
    function closePreview(){
        var rqcode = $("#remark");
        rqcode.html("");
        rqcode.hide();
    }
    //取窗口滚动条高度
    function getScrollTop(){
        var scrollTop=0;
        if(document.documentElement&&document.documentElement.scrollTop){
            scrollTop=document.documentElement.scrollTop;
        }else if(document.body){
            scrollTop=document.body.scrollTop;
        }
        return scrollTop;
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