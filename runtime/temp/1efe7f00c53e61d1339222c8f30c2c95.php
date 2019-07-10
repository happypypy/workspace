<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:89:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\member\give_cashed.html";i:1561691687;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>赠送现金券</title>
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/tabscommon.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <link href="/static/js/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="/static/js/daterangepicker/moment.min.js"></script>
    <script type="text/javascript" src="/static/js/daterangepicker/daterangepicker.js"></script>
</head>
<body>
<div class="oa_pop">
    <div class="oa_pop-main">
        <div style="height: 6px"></div>
        <div class="oa_title clearfix">
            <span class="oa_ico-right"></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            赠送现金券
        </div>
        <div class="oa_edition">
            <form id="handleposition" action="<?php echo url('member/give_cashed'); ?>" method="post" onsubmit="return checkForm()" >
                <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                   <style>
                       input{
                           border: 1px solid #DEDEDE;
                       }
                   </style>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">用户ID：</td>
                        <td class="oa_cell-right">
                            <?php echo $memberinfo['idmember']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">昵称：</td>
                        <td class="oa_cell-right">
                            <?php echo $memberinfo['nickname']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left"><span style="color: red;">*</span>赠券金额：</td>
                        <td class="oa_cell-right">
                            <input type="text" id="cashed_amount" name="cashed_amount" value="" class="oa_input-200" />（元）
                        </td>
                    </tr>
                    <tr>
                        <td class="oa_cell-left">
                            <span style="color: red;">*</span>
                            有效期：</td>
                        <td class="oa_cell-right">
                            <input name="cashed_validity_day" type="text" id="cashed_validity_day" class="oa_input-200" />（天）
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">赠券原因：</td>
                        <td class="oa_cell-right">
                            <textarea  style="width:250px;height:100px;" class="span12 ckeditor" id="remark"  name="remark"></textarea>
                        </td>
                    </tr>
                    <input type="hidden" name="idmember" value="<?php echo $memberinfo['idmember']; ?>">
                    <input type="hidden" name="nickname" value="<?php echo $memberinfo['nickname']; ?>">
                    <input type="hidden" name="userimg" value="<?php echo $memberinfo['userimg']; ?>">
                    <tr id="require1">
                        <td><input style="margin:10px;height: 30px;width: 100px;cursor: pointer" type="submit" value="赠送现金券"></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </form>
        </div>
        <div style="height: 30px"></div>
    </div>

</div>
<script>
function checkForm(){
    var cashed_amount =  $('#cashed_amount').val();
    var cashed_validity_day =  $('#cashed_validity_day').val();
    var remark = $('#remark').val();
    var int_not_zero = /^[1-9]\d{0,8}$/;
    // debugger;
    if(!int_not_zero.test(cashed_amount)){
        alert("请输入非零的整数作为赠券金额");
        return false;
    }
    if(!int_not_zero.test(cashed_validity_day)){
        alert("请输入非零的整数作为有效期");
        return false;
    }
    if(remark.length > 60){
        alert("赠券原因不能大于60个字!");
        return false;
    }
    return true;
}
</script>
</body>
</html>