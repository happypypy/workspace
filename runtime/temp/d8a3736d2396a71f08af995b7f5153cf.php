<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:66:"D:\workspace\work\public/../application/admin\view\node\index.html";i:1561691688;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>无标题文档</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/static/js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="/static/js/jquery.select-1.3.6.js"></script>
    <link rel="stylesheet" type="text/css" href="/static/css/default/easyui.css" />
    <script type="text/javascript" src="/static/js/jquery.easyui.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            $(".oa_search-type select").sSelect();
        })
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
            $('.oa_text-list tr').toggle(
                function () {
                    $(this).addClass('oa_selected');
                },
                function () {
                    $(this).removeClass('oa_selected');
                }
            );
        });
        $(document).ready(function () {
            $('.oa_text-list tr > td:first-child').addClass('oa_text-list-arr');
        });



    </script>

    <script type="text/javascript">
        //<![CDATA[
        function Jquery_EasyUiTreeClick(node)
        {
            var ClickEvent="ListFileinFolder";
            var nodeid=node.id;
            var nodetext=node.text;
            var oFun = null;
            try
            {
                oFun = eval(ClickEvent);
            }
            catch(e){}
            if (oFun == null || typeof(oFun) != "function")
            {
                alert("抱歉没有指定树形点击事件");
            }else{
                oFun(nodeid);
            }
        }
        function ListFileinFolder(id) {
            //if (id > 0) {
                mainFrame.location = "<?php echo url('admin/node/contentlist'); ?>?nodeid=" + id;
          //  } else if (id == 0) {
          //      mainFrame.location = "ContentsList.aspx?sysModuleID=79078";
           // }
        }
        function delRe(toNode)
        {
            var t = $('#tree1');
            var node = t.tree('find',toNode);
            $('#tree1').tree('remove',node.target);
        }
        function treeviewExpand(toNode){
            var t = $('#tree1');
            var node = t.tree('find', toNode);
            t.tree('expand', node.target);
        }
        function addRe(toNode , newtitle , newid)
        {
            var t = $('#tree1');
            //treeviewExpand(toNode);
            var node = t.tree('find', toNode);
            var newnode = t.tree('find', newid);
            if(newnode==null) {
                t.tree('append', {
                    parent: (node?node.target:null),
                    data: [{
                        id: newid,
                        text: newtitle
                    }]
                });
            }
        }
        function modiRe(toNode,content)
        {
            var t = $('#tree1');
            var node = t.tree('find', toNode );
            if (node)
            {
                $('#tree1').tree('update', {
                    target: node.target,
                    text:content
                });
            }
        }
        $(document).ready(function(){
            $('#tree1').tree({
                onClick: function(node){
                    treeviewExpand(node.id);
                    Jquery_EasyUiTreeClick(node);
                }
            });
        });
        //]]>
    </script>

    <script type="text/javascript" language="javascript">
        function ResetIframeHeight() {
            var mainheight = $("#mainFrame").contents().find("body").height()+30;
            if(mainheight<400)
                mainheight=400;
            $("#mainFrame").height(mainheight);
            setTimeout(ResetIframeHeight,2000);
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
                        <div class="oa_content-main">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <!--<td width="180" valign="top" class="oa_tree">-->
                                        <!--<div class="oa_tree">-->
                                            <!--<div class="oa_tree-title"><span class="oa_tree-ico"><img src="/static/images/oa_ico-folder.gif" alt="" /></span>内容管理</div>-->

                                            <!--<div class="oa_tree-list">-->
                                                <!--<ul  id="tree1" class="easyui-tree" data-options="url:' <?php echo url('admin/node/getnode'); ?>',animate:true,lines:true,dnd:false,method:'get',title:'栏目列表' "></ul>-->
                                            <!--</div>-->

                                        <!--</div>-->
                                    <!--</td>-->
                                    <td valign="top">
                                        <div id="divTest">
                                            <iframe id="mainFrame" name="mainFrame" src="<?php echo url('admin/node/contentlist',array('nodeid'=>$nodeid)); ?>" frameborder="0" width="100%"
                                                    style="width: 100%;" onload="ResetIframeHeight()"></iframe>
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