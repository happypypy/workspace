<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:48:"D:\workspace\work\thinkphp\tpl\dispatch_jump.tpl";i:1567561553;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>跳转提示</title>
    <style type="text/css">
        *{ background-clip:padding: 0; margin: 0; }
		.rectangle_box{position: absolute;left: 50%;right: 50%;top: 25%;bottom: 50%;margin-left: -189.5px;margin-top: -77px; min-height:400px;}
        body{ background: #fff; font-family: "Microsoft Yahei","Helvetica Neue",Helvetica,Arial,sans-serif; color: #333; font-size: 12px;width: 100%;height: 100%; }
    </style>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
</head>
<body>
<div class="rectangle_box">
<table width="300" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#333333">
  <tr>
    <td height="30" align="center" bgcolor="#CCCCCC">信息提示</td>
   
  </tr>
  <tr>
    <td height="51" align="center" bgcolor="#CCCCCC"><?php echo $code==1?":)":":(" ?> <?php echo(strip_tags($msg));?></td>
    
  </tr>
  <tr>
    <td height="30" align="center" bgcolor="#CCCCCC"> 页面自动 <a id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait">2</b><span style="display: none;"><?php echo($wait);?></span></td>

  </tr>
  <tr>
    <td align="center" bgcolor="#CCCCCC">
    	<p>Copyright©2017-2027<em><a class="copyright" href="http://www.tpshop.cn/">童享云</a></em></p>
		<p><em class="copyright"><a class="copyright" href="http://www.tpshop.cn/">琦玮教育科技</a></em></p>
    </td>
    
  </tr>
  
</table>
</div>
<script type="text/javascript">

        var obj=GetOpenerWin();
        try {
            if (typeof(obj) != null && typeof(obj) != "undefined") {

                if (typeof(obj.return_function) == "function") {
                    obj.return_function(<?php echo $data; ?>);
                }
            }
        }
        catch(e){
        }
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
            var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                    location.href = href;
                    clearInterval(interval);
                };
            }, 1000);
        })();
    </script>
</body>
</html>
