<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:86:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\smsmanage\index.html";i:1561691683;s:82:"C:\phpStudy\PHPTutorial\WWW\work\application\admin\view\smsmanage\sms_sys_nav.html";i:1561691683;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>短信管理</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
    <script type="text/javascript" src="/static/js/tabscommon.js"></script>
    <script type="text/javascript" src="/static/js/del-checked.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <script type="text/javascript">

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
	<?php if($cms->CheckPurview('sendmanage')){ ?>
	    <li id="index" onclick="javascript:window.location='<?php echo url('smsmanage/index',''); ?>'"><em>短信管理</em></li>
    <?php } if($cms->CheckPurview('template', 'manage')){ ?>
	    <li id="templatelist" onclick="javascript:window.location='<?php echo url('smsmanage/templatelist',''); ?>'"><em>短信模版管理</em></li>
    <?php } ?>
</ul>

<script type="text/javascript">
    $("#" + "<?= $action_name ?>").addClass("oa_on");
</script>
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
                                                            <form action="<?php echo url('smsmanage/index'); ?>" method="post" id="form1">
                                                                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">开通状态：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="sms_status" style="width: 200px">
                                                                                <option value="">请选择</option>
                                                                                <option value="0" <?php if(is_numeric($search["sms_status"]) && $search["sms_status"]==0){echo "selected";} ?>>未开通</option>
                                                                                <option value="1" <?php if($search["sms_status"]==1){echo "selected";} ?>>待审核</option>
                                                                                <option value="2" <?php if($search["sms_status"]==2){echo "selected";} ?>>审核通过</option>
                                                                                <option value="3" <?php if($search["sms_status"]==3){echo "selected";} ?>>审核不通过</option>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="oa_cell-left">站点名称：</td>
                                                                        <td class="oa_cell-right"><input id="site_name" name="site_name" type="text" value="<?php echo $search['site_name']; ?>" class="oa_search-input" /></td>
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
                            </ul>
                          </span>
                                            <span class="oa_ico-left"></span>
                                            短信管理
                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <th width="150"><span class="oa_arr-text-list-title"></span>站点名称</th>
                                                    <th width="150"><span class="oa_arr-text-list-title"></span>公司名称</th>
                                                    <th width="80"><span class="oa_arr-text-list-title"></span>短信签名</th>
                                                    <th><span class="oa_arr-text-list-title"></span>备注</th>
                                                    <th width="60"><span class="oa_arr-text-list-title"></span>开通状态</th>
                                                    <th width="60"><span class="oa_arr-text-list-title"></span>是否启用</th>
                                                    <th width="85"><span class="oa_arr-text-list-title"></span>短信剩余条数</th>
                                                    <th width="190"><span class="oa_arr-text-list-title"></span>操作</th>
                                                </tr>
                                                <?php if(is_array($site_list) || $site_list instanceof \think\Collection || $site_list instanceof \think\Paginator): $i = 0; $__LIST__ = $site_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td><?php echo $vo['site_name']; ?></td>
                                                    <td><?php echo $vo['sms_company_name']; ?></td>
                                                    <td><?php echo $vo['sms_sign']; ?></td>
                                                    <td><?php echo $vo['sms_mark']; ?></td>
                                                    <td><?php if($vo['sms_status'] == 0): ?>未开通<?php elseif($vo['sms_status'] == 1): ?>待审核<?php elseif($vo['sms_status'] == 2): ?>审核通过<?php else: ?>审核未通过<?php endif; ?></td>
                                                    <td><?php if($vo['sms_enable'] == 0): ?>未启用<?php else: ?>启用<?php endif; ?></td>
                                                    <td><?php echo $vo['sms_num']; ?></td>
                                                    <td>
                                                        <?php if($vo['sms_status'] > 0): if($cms->CheckPurview('smsmanage', 'recharge')): ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('Smsmanage/review','id='.$vo['id'].'&action=edit',''); ?>','Smsmanage','审核',500,430)">审核</a>
                                                        <?php endif; endif; if($vo['sms_status'] == 2): if($cms->CheckPurview('smsmanage', 'recharge')): ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('smsmanage/sms_recharge','id='.$vo['id']); ?>', 'sms_recharge','短信充值', 400,200)">短信充值</a>
                                                        <?php endif; if($cms->CheckPurview('smsmanage', 'view')): ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('smsmanage/log','id='.$vo['id']); ?>', 'sms_recharge','发送记录', 1000,700)">充值记录</a>
                                                        <a href="javascript:CustomOpen('<?php echo url('smsmanage/send_log','id='.$vo['id']); ?>', 'sms_recharge','发送记录', 1000,700)">发送记录</a>
                                                        <?php endif; endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </table>
                                            <div class="oa_bottom clearfix">
                                                <div class="clearfix">
                                                    <div class="oa_page-controls">
                                                        <ul>
                                                            <li></li>
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
    function delete_reply(id){

        layer.confirm("确定删除？", {
            btn: ['确定','取消'] //按钮
        }, function(){
            var url = "/admin/weixinreplay/delete_reply/wx_replay_id/"+id;
            $.ajax({
                url:url,
                type:"get",
                success:function(obj){
                    console.log(obj+obj.msg);
                    if (obj.msg=="操作成功"){
                        layer.alert('操作成功', {icon: 1}, function(index){
                            location.reload();
                            layer.close(index);

                        });
                    }else{

                        layer.alert('操作失败', {icon: 2}, function(index){
                            layer.close(index);
                        });
                    }
                },
                error:function(msg){
                    layer.alert('操作失败', {icon: 2}, function(index){
                        layer.close(index);
                        location.reload();
                    });
                }
            })
        });
    }

    function empty() {
        window.location.reload();
    }

</script>
</body>
</html>