<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:91:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\activity\importorder.html";i:1561971917;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>签到</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/del-checked.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>

</head>
<body>
<div class="oa_pop">
    <div class="oa_pop-main">
        <div style="height: 6px"></div>
        <div class="oa_title clearfix">
            <span class="oa_ico-right"></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            报名数据导入    </div>
        <div class="oa_edition">
            <form action="" method="post" id="frm" enctype="multipart/form-data">
                <table id="tabcontent" width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                    <tr>
                        <td height="80"  class="oa_cell-left">导入文件：</td>
                        <td class="oa_cell-right" style="padding:10px;">
                           <input type="file" id="file1" name="file1" style="width: 300px; display: inline; " />
						   <a style="color: red;" href="<?php echo url('activity/ordertemplate',array('templateid'=>$templateid)); ?>"> 点击下载模版 </a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:10px;">
                            <input type="hidden" id="action" name="action" value="" />
                            <input type="submit" onclick="javascript:return checkdata(0);" value="确认导入" >
                            <input type="submit" onclick="javascript:return checkdata(1);" value="确认导入并发短信" >
                            <input type="button" value="关闭" onclick="javascript:CloseDiv();">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>

<script language="JavaScript">
    function checkdata(operation) {
        if(operation == 0){
            $('#action').val("import");
        }else{
            $('#action').val("import_sms");
        }

        if($("file1").val()=="")
        {
            alert("请选择要导入的文件");
            return false;
        }
        return true;
    }

</script>

</body>
</html>