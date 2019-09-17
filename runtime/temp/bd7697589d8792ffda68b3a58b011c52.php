<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:67:"D:\workspace\work\public/../application/admin\view\column\modi.html";i:1565406519;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>栏目管理</title>
<link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
<link href="/static/css/page.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
 <script type="text/javascript" src="/static/js/del-checked.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
</head>
<body>
<div class="oa_pop">
    <div style="height: 6px"></div>
    <div class="oa_pop-main">
        <div class="oa_title clearfix">
            <span class="oa_ico-right"></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            栏目管理
        </div>
        <div class="oa_edition">
            <form id="frm" action="" method="post">
                <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                    <tr>
                        <td width="150" class="oa_cell-left">栏目名称：</td>
                        <td class="oa_cell-right">
                            <input name="nodename" id="nodename"  type="text" value="<?php echo $datainfo['nodename']; ?>" class="oa_input-200" />
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">类型：</td>
                        <td class="oa_cell-right">
                            <select name="nodetype" id="nodetype">
                                <option value="1" <?php if($datainfo['nodetype'] == 1): ?>selected="selected"<?php endif; ?>>资讯</option>
                                <option value="2" <?php if($datainfo['nodetype'] == 2): ?>selected="selected"<?php endif; ?>>产品</option>
                            </select>
                        </td>
                    </tr>
                    <tr style="display: none;">
                        <td width="150" class="oa_cell-left">所属栏目：</td>
                        <td class="oa_cell-right">
                            <select name="parentid">
                                <option value="0">根目录</option>
                                <?php if(is_array($nodelist) || $nodelist instanceof \think\Collection || $nodelist instanceof \think\Paginator): $i = 0; $__LIST__ = $nodelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                    <option value="<?php echo $vo['nodeid']; ?>" <?php if($datainfo['parentid'] == $vo['nodeid']): ?>selected="selected"<?php endif; ?>><?php for($x=0;$x<=$vo['level'];$x++){ echo "&nbsp;&nbsp;&nbsp;&nbsp;";} ?><?php echo $vo['nodename']; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                            <?php if($datainfo['action'] == 'edit'): ?>
                            <input type="hidden" name="oldparentid" value="<?php echo $datainfo['parentid']; ?>">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <!--<tr>-->
                        <!--<td width="150" class="oa_cell-left">栏目内容模型：</td>-->
                        <!--<td class="oa_cell-right">-->
                            <!--<select name="idmodel">-->
                                <!--<option value="0" >请选择模型</option>-->
                                <!--<?php if(is_array($modellist) || $modellist instanceof \think\Collection || $modellist instanceof \think\Paginator): $i = 0; $__LIST__ = $modellist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>-->
                                <!--<option value="<?php echo $vo['idmodel']; ?>:<?php echo $vo['modelname']; ?>" <?php if($datainfo['idmodel'] == $vo['idmodel']): ?>selected="selected"<?php endif; ?>><?php echo $vo['modelname']; ?></option>-->
                                <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                            <!--</select>-->
                        <!--</td>-->
                    <!--</tr>-->
                    <tr>
                        <td width="150" class="oa_cell-left">栏目图片地址：</td>
                        <td class="oa_cell-right">
                            <input name="nodepicurl" id="nodepicurl" type="text" value="<?php echo $datainfo['nodepicurl']; ?>" class="oa_input-300" /><br>
                            <input onclick="GetUploadify(1,'nodepicurl','admin','undefined','*.jpg;*.jpeg;*.png;*.gif;*.jpeg;');" type="button" value="上传图标"/>
                            <input onclick="SelectImg(1,'nodepicurl','admin','undefined','*.jpg;*.jpeg;*.png;*.gif;*.jpeg;');" type="button" value="选择图标"/>
                        </td>
                    </tr>
                    <tr style="display: none">
                        <td width="150" class="oa_cell-left">栏目的目录名称：</td>
                        <td class="oa_cell-right"><input name="nodedir" type="text" value="<?php echo $datainfo['nodedir']; ?>" class="oa_input-200" /></td>
                    </tr>

                    <tr style="display: none;">
                        <td width="150" class="oa_cell-left">栏目提示：</td>
                        <td class="oa_cell-right">
                            <input name="tips" type="text" value="<?php echo $datainfo['tips']; ?>" class="oa_input-200" />
                        </td>
                    </tr>
                    <tr  style="display: none;">
                        <td width="150" class="oa_cell-left">栏目说明：</td>
                        <td class="oa_cell-right">
                            <input name="remark" type="text" value="<?php echo $datainfo['remark']; ?>" class="oa_input-200" />
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">序号：</td>
                        <td class="oa_cell-right">
                            <input name="idorder" type="text" value="<?php echo $datainfo['idorder']; ?>" class="oa_input-200" />
                        </td>
                    </tr>

                    <tr style="display: none">
                        <td width="150" class="oa_cell-left">栏目META关键词：</td>
                        <td class="oa_cell-right">
                            <input name="metakeywords" type="text" value="<?php echo $datainfo['metakeywords']; ?>" class="oa_input-200" />
                        </td>
                    </tr>
                    <tr style="display: none">
                        <td width="150" class="oa_cell-left">栏目META网页描述：</td>
                        <td class="oa_cell-right">
                            <input name="metaremark" type="text" value="<?php echo $datainfo['metaremark']; ?>" class="oa_input-200" />
                        </td>
                    </tr>
                    <tr style="display: none">
                        <td width="150" class="oa_cell-left" >每页显示的内容数：</td>
                        <td class="oa_cell-right"><input name="itempagesize" type="text" value="<?php echo $datainfo['itempagesize']; ?>" class="oa_input-200" /></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">跳转地址：</td>
                        <td class="oa_cell-right"><input name="linkurl" id="linkurl" type="text" value="<?php echo $datainfo['linkurl']; ?>" class="oa_input-200" /></td>
                    </tr>
                    <tr  style="display: none">
                        <td width="150" class="oa_cell-left" >选项：</td>
                        <td class="oa_cell-right">
                            <!-- <input type="checkbox"  name="Option1[]" value=1 <?php echo in_array(1, explode(',',$datainfo['option']))?'checked="checked"':''; ?>  />&nbsp;是否推荐栏目<br>-->
                            <input type="checkbox"  name="Option1[]" value=2 id="is_comment" <?php echo in_array(2, explode(',',$datainfo['option']))?'checked="checked"':''; ?>  />&nbsp;是否允许评论<br>
                            <!-- <input type="checkbox"  name="Option1[]" value=3 <?php echo in_array(3, explode(',',$datainfo['option']))?'checked="checked"':''; ?>  />&nbsp;前台浏览是否需要登陆<br>-->
                            <!-- <input type="checkbox"  name="Option1[]" value=4 <?php echo in_array(4, explode(',',$datainfo['option']))?'checked="checked"':''; ?>  />&nbsp;是否允许投稿<br>-->
                            <input type="checkbox"  name="Option1[]" value=5 id="is_entry" <?php echo in_array(5, explode(',',$datainfo['option']))?'checked="checked"':''; ?>  />&nbsp;是否可以报名
                        </td>
                    </tr>
                    <tr id="comment_model"  style="display: none">
                        <td width="150" class="oa_cell-left">选择评论模型：</td>
                        <td class="oa_cell-right">
                            <select name="commentmodel">
                                <option value="0">请选择评论模型</option>
                                <?php if(is_array($modellist) || $modellist instanceof \think\Collection || $modellist instanceof \think\Paginator): $i = 0; $__LIST__ = $modellist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo $vo['idmodel']; ?>" <?php if($datainfo['commentmodel'] == $vo['idmodel']): ?>selected="selected"<?php endif; ?>><?php echo $vo['modelname']; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr id="entry_model"  style="display: none">
                        <td width="150" class="oa_cell-left">选择报名模型：</td>
                        <td class="oa_cell-right">
                            <select name="entrymodel">
                                <option value="0">请选择报名模型</option>
                                <?php if(is_array($modellist) || $modellist instanceof \think\Collection || $modellist instanceof \think\Paginator): $i = 0; $__LIST__ = $modellist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo $vo['idmodel']; ?>" <?php if($datainfo['entrymodel'] == $vo['idmodel']): ?>selected="selected"<?php endif; ?>><?php echo $vo['modelname']; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr >
                        <td width="150" class="oa_cell-left">是否为单页面：</td>
                        <td class="oa_cell-right">
                            <input name="isonepage" type="radio" value="1" <?php if($datainfo['isonepage'] == 1): ?>checked<?php endif; ?> />是&nbsp;&nbsp;&nbsp;&nbsp;
                            <input name="isonepage" type="radio" value="2" <?php if($datainfo['isonepage'] == 2): ?>checked<?php endif; ?> />否
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">是否在顶部菜单处显示：</td>
                        <td class="oa_cell-right">
                            <input name="showonmenu" type="radio" value="1" <?php if($datainfo['showonmenu'] == 1): ?>checked<?php endif; ?> />是&nbsp;&nbsp;&nbsp;&nbsp;
                            <input name="showonmenu" type="radio" value="2" <?php if($datainfo['showonmenu'] == 2): ?>checked<?php endif; ?> />否
                        </td>
                    </tr>
                    <tr style="display: none">
                        <td width="150" class="oa_cell-left">是否位置导航处显示：</td>
                        <td class="oa_cell-right">
                            <input name="showonpath" type="radio" value="1" <?php if($datainfo['showonpath'] == 1): ?>checked<?php endif; ?> />是&nbsp;&nbsp;&nbsp;&nbsp;
                            <input name="showonpath" type="radio" value="2" <?php if($datainfo['showonpath'] == 2): ?>checked<?php endif; ?> />否
                        </td>
                    </tr>
                    <tr style="display: none">
                        <td width="150" class="oa_cell-left">栏目首页模版：</td>
                        <td class="oa_cell-right"><input name="templateofnodeindex" type="text" value="<?php echo $datainfo['templateofnodeindex']; ?>" class="oa_input-200" /></td>
                    </tr>
                    <tr style="display: none">
                        <td width="150" class="oa_cell-left">栏目列表页模版：</td>
                        <td class="oa_cell-right"><input name="templateofnodelist" type="text" value="<?php echo $datainfo['templateofnodelist']; ?>" class="oa_input-200" /></td>
                    </tr>
                    <tr style="display: none">
                        <td width="150" class="oa_cell-left">栏目内容页模版：</td>
                        <td class="oa_cell-right"><input name="templateofnodecontent" type="text" value="<?php echo $datainfo['templateofnodecontent']; ?>" class="oa_input-200" /></td>
                    </tr>
                    <tr><td colspan="2" style="padding:10px;"><input type="button" onclick="javascript:checkdata();" value="确定"></td></tr>
                    <tr>
                        <?php if($datainfo['action'] == 'edit'): ?>
                        <td><input type="hidden" name="nodeid" value="<?php echo $datainfo['nodeid']; ?>"></td>
                        <?php endif; ?>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    function checkdata() {
        var tmpValue=$("#nodename").val();
        if(tmpValue=="")
        {
            alert("栏目名称不能为空。");
            return ;
        }
        else if(tmpValue.length>6)
        {
            alert("栏目名称不能大于6个字符，建议用4个字符。");
            return ;
        }


        $("#frm").submit();

    }

    if($("#is_comment").is(":checked")){
        //$("#comment_model").show();
    }else {
        $("#comment_model").hide();
    }

    if($("#is_entry").is(":checked")){
        $("#entry_model").show();
    }else {
        $("#entry_model").hide();
    }

    $("#is_comment").click(function(){
        if($("#is_comment").is(":checked")){
           // $("#comment_model").show();
        }else {
            $("#comment_model").hide();
        }
    });

    $("#is_entry").click(function(){
        if($("#is_entry").is(":checked")){
          //  $("#entry_model").show();
        }else {
            $("#entry_model").hide();
        }
    });

</script>

</html>