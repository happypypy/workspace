<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:86:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\role\columninfo.html";i:1561691688;}*/ ?>
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

    //全选
    function DoCheck(modulecode) {

        var flag = $("#"+modulecode).is(':checked');

            if(flag){
                $("."+modulecode).attr("checked", true);
            }else{
                $("."+modulecode).removeAttr('checked');
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
                                            <span class="oa_ico-right"></span>
                                            <span class="oa_title-btn"></span>
                                            <span class="oa_ico-left"></span>
                                            <span style="color: #2A5B9D">“<?php echo $role_info['rolename']; ?>”<?php echo lang('role purview'); ?></span>
                                        </div>
                                        <form action="<?php echo url('role/purviewpost'); ?>" method="post">
                                            <div class="oa_text-list">
                                                <?php if(is_array($columnname) || $columnname instanceof \think\Collection || $columnname instanceof \think\Paginator): $i = 0; $__LIST__ = $columnname;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v3): $mod = ($i % 2 );++$i;?><!--循环栏目-->
                                                   <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <th colspan="3" style=" background-color: #c1c1c1; text-align: center;color: red;font-size: 14px;height: 28px;line-height: 28px"><?php echo $v3['chrname']; ?></th>
                                                    </tr>
                                                    <?php if(is_array($columninfo) || $columninfo instanceof \think\Collection || $columninfo instanceof \think\Paginator): $i = 0; $__LIST__ = $columninfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?> <!--循环模块-->
                                                    <?php if($v3['chrcode'] == $vo['codecatalog']): ?>
                                                    <tr>
                                                        <td align="right" style="width:150px;"><input id="<?php echo $vo['chrcode']; ?>" class="check_all" type="checkbox" onclick='DoCheck("<?php echo $vo['chrcode']; ?>");' value="<?php echo $vo['chrcode']; ?>" ><?php echo $vo['chrname']; ?></td>
                                                        <td >
                                                            <?php if(is_array($resourcelist) || $resourcelist instanceof \think\Collection || $resourcelist instanceof \think\Paginator): $i = 0; $__LIST__ = $resourcelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?> <!--循环资源-->
                                                            <?php if($v['modulecode'] == $vo['chrcode']): ?>

                                                            <div style="font-size: 12px;height: 24px;line-height: 24px;">
                                                                <span  style="display: inline-block;width: 130px; text-align: right;padding-right: 20px"><?php echo $v['chrname']; ?></span>

                                                                <?php if(is_array($operatelist) || $operatelist instanceof \think\Collection || $operatelist instanceof \think\Paginator): $i = 0; $__LIST__ = $operatelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?> <!--循环操作-->
                                                                <?php if($v1['chrmodulecode'] == $vo['chrcode'] and $v1['chrresourcecode'] == $v['chrcode']): ?>
                                                                    <input class="<?php echo $vo['chrcode']; ?>" id="<?php echo $vo['chrcode']; ?>_<?php echo $v['chrcode']; ?>_<?php echo $v1['chrcode']; ?>" type="checkbox" name="operate_list[]" value="<?php echo $vo['chrcode']; ?>_<?php echo $v['chrcode']; ?>_<?php echo $v1['chrcode']; ?>" <?php if(is_array($roleoperate) || $roleoperate instanceof \think\Collection || $roleoperate instanceof \think\Paginator): $i = 0; $__LIST__ = $roleoperate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v2): $mod = ($i % 2 );++$i;?> <?php echo $v2['chrmodulecode']==$vo['chrcode']&&$v1['chrcode']==$v2['chroperatecode']&&$v2['chrresourcecode']==$v['chrcode']?"checked":""; endforeach; endif; else: echo "" ;endif; ?>><?php echo $v1['chrname']; endif; endforeach; endif; else: echo "" ;endif; ?>
                                                            </div>

                                                            <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                                        </td>

                                                    </tr>
                                                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                                   </table>
                                                <br>
                                                <br>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>

                                                <div class="oa_bottom clearfix">
                                                    <div class="clearfix">
                                                        <div class="oa_op-btn clearfix">
                                                            <?php if(is_array($roleoperate) || $roleoperate instanceof \think\Collection || $roleoperate instanceof \think\Paginator): $i = 0; $__LIST__ = $roleoperate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v2): $mod = ($i % 2 );++$i;?>
                                                            <input type="hidden" name="modulecode[]" value="<?php echo $vo['chrcode']; ?>">
                                                           <!-- <input type="hidden" name="module[]" value="<?php echo $v2['chrmodulecode']; ?>">
                                                            <input type="hidden" name="resource[]" value="<?php echo $v2['chrresourcecode']; ?>">-->
                                                            <input type="hidden" name="roleoperate[]" value="<?php echo $v2['chrmodulecode']; ?>_<?php echo $v2['chrresourcecode']; ?>_<?php echo $v2['chroperatecode']; ?>">
                                                            <input type="hidden" name="roleoperate_id[]" value="<?php echo $v2['idrole_operate']; ?>">
                                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                                            <input type="hidden" name="roleid" value="<?php echo $request['roleid']; ?>">
                                                            <input type="hidden" name="idsite" value="<?php echo $role_info['idsite']; ?>">
                                                            <input name="" value="<?php echo lang('save'); ?>" type="submit" class="oa_input-submit" />
                                                            <input name="" value="<?php echo lang('return'); ?>" onclick="back();" type="button" class="oa_input-submit" />
                                                        </div>
                                                    </div>
                                                    <div class="oa_bottom-bottom"><em></em></div>
                                                </div>
                                            </div>
                                        </form>
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