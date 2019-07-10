<?php

phpinfo();
exit();

$aa=[];
$aa["a"]=a1;
$aa["b"]=b1;
$aa["b"]=b1;
print_r($aa);
$aa=[];
$aa["c"]=c1;
print_r($aa);

?>

<html>
<head>
    <script type="text/javascript" src="/static/js/jquery-3.1.1.js"></script>
</head>
<body>
经度<input type="text" id="tj" name="tj"><br>
纬度<input type="text" id="wj" name="wj"><br>
<input type="button" name="but1" onclick="javascript:aaa();" value="测试">
<script language="JavaScript">
    function aaa()
    {
        var data={
        location:$("#tj").val()+","+$("#wj").val(),
        /*换成自己申请的key*/
        key:"VGVBZ-PHEW6-QLBSQ-MJ34Q-CSSHO-3EFDW",
        get_poi:0
        }
        var url="http://apis.map.qq.com/ws/geocoder/v1/?";
        data.output="jsonp";
        $.ajax({
        type:"get",
        dataType:'jsonp',
        data:data,
        jsonp:"callback",
        jsonpCallback:"QQmap",
        url:url,
        success:function(json){
        /*json对象转为文本 var aToStr=JSON.stringify(a);*/
        var toStr = JSON.stringify(json);

        /*调用业务处理程序*/
        alert(toStr);
        },
        error : function(err){alert("服务端错误，请刷新浏览器后重试")}

        });
    }
</script>
</body>
</html>