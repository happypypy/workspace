<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"D:\workspace\work\public/../application/admin\view\index\loginsite.html";i:1561691687;}*/ ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"><!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
<meta name="format-detection" content="telephone=no" />
<meta name="keywords" content="童享云"/>
<meta name="description" content="童享云"/>
<link rel="stylesheet" href="/static/login/css/normalize.css">
<link type="text/css" rel="stylesheet" href="/static/login/css/common.css" />
<link  href="/static/tncode/style.css?v=27" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="/static/tncode/tn_code.js?v=35"></script>
<script type="text/javascript" src="/static/js/layer/layer.js"></script>
<script type="text/javascript" src="/static/login/js/common.js"></script>
<title>登陆</title>
</head>


<script type="text/javascript">
function aaaaa() {
    $("#form1").submit();
}
var _old_onload = window.onload;
window.onload = function(){
    if(typeof _old_onload == 'function'){
        _old_onload();
    }
    tncode.onsuccess(aaaaa)
    tncode.ImgUrl="/admin/index/tncode";
    tncode.CheckUrl="/admin/index/checktn";
    tncode.init();

};

function fleshVerify(){
    $('#imgVerify').attr('src',"<?php echo url('admin/index/vertify'); ?>?r="+Math.floor(Math.random()*100));
}

function ReSet()
{
    $('#txtAccount').val('');
    $('#txtPassword').val('');
    $('#txtICode').val('');
    fleshVerify();
}

function get_cookie(key)
{
    var getCookie = document.cookie.replace(/[ ]/g,'');
    var resArr = getCookie.split(';');
    var res='';
    for(var i = 0;i< resArr.length;i++)
    {
        var arr = resArr[i].split('=');
        if(arr[0] == key)
        {
            res = arr[1];
            break;
        }
    }
    return unescape(res);
}


//JS操作cookies方法!
//写cookies
function setCookie(name,value)
{
    var Days = 30;
    var exp = new Date();
    exp.setTime(exp.getTime() + Days*24*60*60*1000);
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}

//读取cookies
function getCookie(name)
{
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
    if(arr=document.cookie.match(reg))
        return unescape(arr[2]);
    else
        return "";
}

function ch()
{

    if($('#txtSiteCode').val()=='')
    {
        layer.alert('站点代号不能为空', {icon: 4}, function(index){
            $('#txtAccount').focus();
            layer.close(index);
        });
        //$('#msg').html('账号不能为空')
        $('#txtSiteCode').focus();
        return false;
    }
    if($('#txtAccount').val()=='')
    {
        layer.alert('账号不能为空', {icon: 4}, function(index){
            $('#txtAccount').focus();
            layer.close(index);
        });
        //$('#msg').html('账号不能为空')
        $('#txtAccount').focus();
        return false;
    }
    if($('#txtPassword').val()=='')
    {
        layer.alert('密码不能为空', {icon: 2}, function(index){
            $('#txtPassword').focus();
            layer.close(index);
        });
       // $('#msg').innerText('密码不能为空')
        return false;
    }
    /*
    if($('#txtICode').val()=='')
    {
        layer.alert('验证码不能为空', {icon: 2}, function(index){
            $('#txtICode').focus();
            layer.close(index);
        });
        //$('#msg').innerText('验证码不能为空')
        return false;
    }
    */
    setCookie('SiteCode',$('#txtSiteCode').val());
    tncode.show();
    return false;
   // $("#form1").submit();
}



</script>
<body>
<!--[if lt IE 7]>
      <p class="browsehappy" style="text-align:center;font-size:24px;"><strong>您的浏览器版本过低， 请升级您的浏览器</strong></p>
<![endif]-->
<div class="index-bg">
    <div class="index-table">
        <div class="index-table-cell">
            <div class="login-content">
                <div class="login-content-tit">欢迎使用童享云系统管理平台</div>
                <form class="login-form" id="form1" method="post" action="<?php echo url('admin/index/loginsite'); ?>">
                    <div class="login-item">
                        <label class="tit titone" for="txtSiteCode">会员<span class="titSpan">号</span></label>
                        <label class="colon" for="txtSiteCode">：</label>
                        <div class="txt">
                            <input type="text" class="text" placeholder="请输入童享云会员号" id="txtSiteCode" name="txtSiteCode"/>
                        </div>
                    </div>
                    <div class="login-item">
                        <label class="tit tittwo" for="txtAccount">账<span class="titSpan">号</span></label>
                        <label class="colon" for="txtAccount">：</label>
                        <div class="txt">
                            <input type="text" class="text" placeholder="请输入企业账号" id="txtAccount"  name="txtAccount"/>
                        </div>
                    </div>
                    <div class="login-item">
                        <label class="tit titthr" for="txtPassword">密<span class="titSpan">码</span></label>
                        <label class="colon" for="txtPassword">：</label>
                        <div class="txt">
                            <input type="password" class="text" placeholder="请输入密码" id="txtPassword" name="txtPassword"/>
                        </div>
                    </div>
                    <div class="login-submit">
                        <input type="button" onclick="javascript:return ch();  " name="" value="登录" class="submit" />
                    </div>
                    <div class="login-reset">
                        <input type="reset" name="" value="清除" class="reset" />
                    </div>
					<div id="msg" class="login-tips"><?php echo $msg; ?></div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="tncode_div_bg" id="tncode_div_bg"></div>
<div class="tncode_div" id="tncode_div">
    <div class="loading">加载中</div>
    <canvas class="tncode_canvas_bg"></canvas>
    <canvas class="tncode_canvas_mark"></canvas>
    <div class="hgroup"></div>
    <div class="tncode_msg_error"></div>
    <div class="tncode_msg_ok"></div>
    <div class="slide">
        <div class="slide_block"></div>
        <div class="slide_block_text">拖动左边滑块完成上方拼图</div>
    </div>
    <div class="tools">
        <div class="tncode_refresh" style="padding-left:30px;width:80px; vertical-align:middle;text-align:left;color:#555;">刷新</div>
    </div>
</div>



<script language="JavaScript">
    $("#txtSiteCode").val(getCookie("SiteCode"));
</script>

</body>
</html>

