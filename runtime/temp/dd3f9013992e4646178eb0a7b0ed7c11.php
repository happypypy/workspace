<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:29:"template/M1/mine/comment.html";i:1561691694;s:68:"C:\phpStudy\PHPTutorial\WWW\work\public\template\M1\lib\footer0.html";i:1561691693;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,user-scalable=0" />
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">

	<title>我的评论</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />

	<script type="text/javascript" src="<?php echo $roottpl; ?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $roottpl; ?>/js/swiper.min.js"></script>
	<script type="text/javascript" src="<?php echo $roottpl; ?>/js/common.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/swiper.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/common.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/pc.css">
</head>
<body style="background: #e7e7e7;">

<!--site section start-->
<div class="site-section clearfix" id="container" style="padding-top: 48px;">
	<div class="select-bar fixed">
		<ul class="select-bar-tab" id="select-bar-tab">
			<li class="on" <?php if($flag == 0){echo 'style="background-color: rgb(238, 238, 238);"';} ?>><a href="/<?php echo $sitecode; ?>/comment/0">全部</a></li>
			<li class="on" <?php if($flag == 2){echo 'style="background-color: rgb(238, 238, 238);"';} ?>><a href="/<?php echo $sitecode; ?>/comment/2">活动评论</a></li>
			<li <?php if($flag == 1){echo 'style="background-color: rgb(238, 238, 238);"';} ?>><a href="/<?php echo $sitecode; ?>/comment/1">资讯评论</a></li>
		</ul>
	</div>

	<div class="comment-search flex" style="display: none">
		<form id="frm1" method="post">
		<input type="text" placeholder="关键字" name="key" class="text fx1" />
		<a href="javascript:;"  class="iconfont submit">&#xe635;</a>
		</form>
	</div>

	<div class="news-list activity">
		<ul id="data">
			<?php if(empty($list)) { ?>
			<li>没找到相关评论记录</li>
			<?php } foreach($list as $k=>$vo) { ?>
			<li id="data_<?php echo $vo['id']; ?>">
				<div class="txt">
					<div class="title"><?php echo $vo['chrtitle']; ?></div>
					<div class="style"><i class="iconfont laiyuan">&#xe66a;</i>评论内容：<span><?php echo $vo['content']; ?></span></div>
					<div class="time"><i class="iconfont clock">&#xe602;</i><span>评论时间：<?php echo date("Y-m-d H:i:s",$vo['createtime']); ?></span></div>
					<?php if($vo['intstate']==4) {?>
					<div class="style"><i class="iconfont laiyuan">&#xe66a;</i>回复内容：<span><?php echo $vo['recontent']; ?></span></div>
					<div class="time"><i class="iconfont clock">&#xe602;</i><span>回复时间：<?php echo date("Y-m-d H:i:s",$vo['retime']); ?></span></div>
					<?php } ?>
					<div class="btn">
						<a href="javascript:;" onclick="javascript:del('<?php echo $vo['id']; ?>')" style="float: right;">删除</a>
					</div>
				</div>
			</li>
			<?php } ?>
		</ul>
		<div id="dataload" class="iconfont iconload" style="display: none">&#xe72f;</div>
	</div>

	<div class="toTop-btn" title="回到顶部" onclick="toTop()"></div>
			<div style="padding: 10px">
			<div style="font-size: 0.12rem; text-align: center;color: #808080">
				<div><?php echo $cms->GetConfigVal('webset','webname',$idsite);; ?></div>
				<div><?php echo str_replace("\r\n","<br>", $cms->GetConfigVal('webset','copyright',$idsite));?></div>
			</div>
			<div style="font-size: 0.1rem; text-align: center;color: #808080;padding-top: 10px;" onclick="location='https://www.tongxiang123.cn/tongxiang'">
				<div>童享云提供技术支持</div>
				<div>www.tongxiang123.com</div>
			</div>
		</div>
</div>
<!--site section end-->

<div class="footer">
	<ul>
		<li>
			<a href="/<?php echo $sitecode; ?>">
				<span><i class="iconfont home">&#xe617;</i></span>
				<p>主页</p>
			</a>
		</li>
		<li class="on">
			<a href="/<?php echo $sitecode; ?>/mine">
				<span><i class="iconfont head">&#xe606;</i></span>
				<p>我的</p>
			</a>
		</li>
	</ul>
</div>
<script type="text/javascript" src="/static/js/layer/layer.js"></script>
<script language="JavaScript">
    function  del(id) {
        layer.confirm('你确定要删除吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var data= {"id":id};
            $.ajax({
                url:"/delcomment",
                data:data,
                type:"post",
                dataType:"json",
                success:function(msg) {
                    if(msg==1)
                    {
                        $("#data_"+id).remove();
                        layer.confirm('删除成功！',{btn:['关闭']});
                    }
                    else
                    {
                        layer.confirm('删除失败！',{btn:['关闭']});
                    }
                }
            })
        }, function(){

        });
    }

    //==============核心代码=============
    var winH = $(window).height(); //页面可视区域高度

    var ipage=1;
    var scrollHandler = function () {
        var pageH = $(document).height();
        var scrollT = $(window).scrollTop(); //滚动条top

        if(pageH-winH-scrollT<1)
        {
            LoadData(ipage)
            ipage++;
        }
    }
    //定义鼠标滚动事件
    $(window).scroll(scrollHandler);
    //==============核心代码=============

    function LoadData(ipage)
    {
        $("#dataload").show();
        $ (window).unbind ('scroll');
        // var state = $('input[name="states"]').val();
        $.ajax({
            url: "/<?php echo $sitecode; ?>/comment/<?php echo $flag; ?>",
            type: 'POST',
            cache: false,
            data:{"ipage":ipage,"ajax":1} ,
            success : function(data) {

                if(data== 11)
                {
                    $("#dataload").hide();
                    // $("#loadmsg").html("已无更多数据");
                    return;
                }
                $("#dataload").hide();
                $("#data").html($("#data").html()+data);
                $(window).scroll(scrollHandler);
            }
        });

    }
</script>
</body>
</html>