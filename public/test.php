<?php

//设置文件类型为图像
header('Content-Type:image/png');

//创建画布
$image = imagecreatetruecolor(5500,5500);

//为画布分配颜色
$color = imagecolorallocate($image,174,48,96);

//填充颜色
imagefill($image,0,0,$color);

//生成图像
imagepng($image);

//保存图像,生成图像和保存图像需要分为两步,要么只能生成,要么只能保存
imagepng($image,'./1.png');

exit();


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