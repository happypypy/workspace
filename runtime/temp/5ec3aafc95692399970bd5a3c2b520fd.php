<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"D:\workspace\work\public/../application/admin\view\activity\issign.html";i:1561691685;}*/ ?>
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
            签到        </div>
        <div class="oa_edition">
            <form action="" method="post" id="frm">
                <table id="tabcontent" width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                    <tr>
                        <td width="150" class="oa_cell-left">签到备注：</td>
                        <td class="oa_cell-right">
                            <textarea  style="width:400px;height:100px;" class="span12 ckeditor" id="remark"  name="remark"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:10px;">
                            <input type="submit" value="签到" >
                            <input type="button" value="取消" onclick="javascript:CloseDiv();">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>

</body>
</html>