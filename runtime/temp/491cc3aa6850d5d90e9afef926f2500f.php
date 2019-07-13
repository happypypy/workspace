<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:28:"template/M5/node/detail.html";i:1562744525;s:52:"D:\workspace\work\public\template\M5\lib\header.html";i:1561691702;s:53:"D:\workspace\work\public\template\M5\lib\footer0.html";i:1561691702;s:53:"D:\workspace\work\public\template\M5\lib\footer2.html";i:1561691702;}*/ ?>
<?php $info = $cms->GetContentInfo($content_id,'contentid,picurl,title,content,nodeid,inputer,sys00003');
if(empty($info))
{
header("location:/error.php?msg=".urlencode("没找到相关文章，有疑问请和管理联系！")."&url=".urlencode("/".$sitecode));
exit();
}elseif(isset($usertypeflag) && $usertypeflag==1){
header("location:/error.php?msg=".urlencode("你好，该内容只有【".$usertype."】才可以查看/购买。如有疑问，请联系客服，谢谢！")."&url=".urlencode("/".$sitecode));
exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,user-scalable=0" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">

<title><?php echo $info['title']; ?></title>
<meta name="keywords" content="" />
<meta name="description" content="" />

<script type="text/javascript" src="<?php echo $roottpl; ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $roottpl; ?>/js/swiper.min.js"></script>
<script type="text/javascript" src="<?php echo $roottpl; ?>/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/swiper.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/common.css">
</head>
<body>
	<header>
		<a href="/<?php echo $sitecode; ?>" class="logo"><img src="<?php echo $cms->GetConfigVal('webset','weblogo',$idsite);; ?>"></a>
		<div class="btns">
			<a class="btn1" href="/<?php echo $sitecode; ?>/mine"></a>
			<a class="btn2" href="javascript:;" id="open-menu"></a>
			<a class="btn3" href="javascript:;" id="close-menu"></a>
		</div>
	</header>
	<div class="menu" id="menu">
		<ul>
			<?php  $result=$cms->GetAllNodes(['showonmenu'=>1,'nodetype'=>['in','1,2'],'parentid'=>0],'idorder asc,nodeid asc','',20,$idsite);
			foreach($result as $k=>$v){
			$jumpUrl ="/". $sitecode.$v["url"];
			if(substr( $v["url"],0,4 ) == "http"){
			$jumpUrl = $v["url"];
			}
			?>
			<li><a href="<?php echo $jumpUrl; ?>"><?php echo $v['nodename']; ?></a></li>
			<?php }?>
		</ul>
	</div>
	<div class="menu-cover"></div>
	<div class="site-section clearfix">
		<div class="where lianxi">
			<p><?php echo $node_info['nodename']; ?></p>
			<a href="/<?php echo $sitecode; ?>" class="iconfont">&#xe617;</a>
		</div>



		<div class="rich_media clearfix">
			<div class="rich_media_area_primary">
				<?php if($node_info['isonepage']!=1) { ?>
				<h2 class="rich_media_title"><?php echo $info['title']; ?></h2>
				<div class="rich_media_meta_list">
				    <em id="post-date" class="rich_media_meta rich_media_meta_text"><?php echo date('Y-m-d',strtotime($info['sys00003'])); ?></em>
				    <em class="rich_media_meta rich_media_meta_text"><?php echo $info['inputer']; ?></em>
				</div>
				<?php } ?>
				<div class="docs-pictures clearfix">
					<?php echo replearurl($info['content']) ?>
				</div>
			</div>
		</div>
		<?php $commentlist = $cms->GetComment($idsite,$content_id); ?>
		<section class="comment-main">
			<h2 class="comment-main-tit">评论信息</h2>
			<?php if(!$commentlist){ ?>
			<div class="comment-item flex">
				<div class="fx1">
					<div class="name ovh"><p>当前无评论</p></div>
				</div>
			</div>
			<?php } foreach($commentlist as $k=>$vo){  ?>
			<div class="comment-item flex">
				<img src="<?php echo empty($vo['userimg'])?'/static/images/userimg.jpg':$vo['userimg'] ?>" class="comment-head">
				<div class="fx1">
					<div class="name ovh">
						<p><?php echo $vo['username']; ?></p>
						<span><?php echo $k+1; ?>楼</span>
					</div>
					<div class="txt">
						<p><?php echo $vo['content']; ?></p>
					</div>
					<div class="info">
						<div class="time fl"><?php echo date('Y-m-d H:i:s',$vo['createtime']); ?></div>
						<div class="zan fr" style="display: none;">赞</div>
					</div>
					<?php if($vo['reid']>0){ ?>
					<div class="name ovh">
						<p>回复</p>
					</div>
					<div class="txt">
						<p><?php echo $vo['recontent']; ?></p>
					</div>
					<div class="info">
						<div class="time fl"><?php echo date('Y-m-d H:i:s',$vo['retime']); ?></div>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>

			<a href="javascript:;" class="detail-more" style="display: none">查看全部100条评论</a>
		</section>
		<div class="cover"></div>
		<form class="comment-form">
			<div class="comment-textarea">
				<textarea name="content" id="content"></textarea>
			</div>
			<div class="comment-submit">
				<input type="button" value="取消" id="close-comment" />
				<input type="button" onClick="javascript:add_comment1();" value="提交" />
			</div>
		</form>
				<div style="padding: 10px">
			<div style="font-size: 0.12rem; text-align: center;color: #808080">
				<div><?php echo $cms->GetConfigVal('webset','webname',$idsite);; ?></div>
				<div><?php echo str_replace("\r\n","<br>", $cms->GetConfigVal('webset','copyright',$idsite));?></div>
			</div>
			<div style="font-size: 0.1rem; text-align: center;color: #808080;padding-top: 10px;"
				onclick="location='https://www.tongxiang123.cn/tongxiang'">
				<div>童享云提供技术支持</div>
				<div>www.tongxiang123.com</div>
			</div>
		</div>
		        <footer>
			<div class="footer">
				<ul>
					<li style="display: none">
						<a href="/<?php echo $sitecode; ?>/activity">
							<span><i class="iconfont home">&#xe682;</i></span>
							<p>更多</p>
						</a>

					</li>

					<li id="openService">
						<a href="/<?php echo $sitecode; ?>/waiter/1/<?php echo $content_id; ?>">
							<span><i class="iconfont consult">&#xe6f9;</i></span>
							<p>咨询</p>
						</a>
					</li>

					<li>
						<a href="javascript:;" id="show-comment">
							<span><i class="iconfont message">&#xe608;</i></span>
							<p>评论</p>
						</a>
					</li>
					<li>
						<a href="javascript:;" onclick="add_collection1()">
							<span><i id="colle" class="iconfont fav">&#xe616;</i></span>
							<p>收藏</p>
						</a>
					</li>
				</ul>
			</div>
		</footer>

        <script type="text/javascript">
            $(function(){
                $("#show-comment").on("click",function(){
                    $(".comment-form,.cover").show();
                })
                $("#close-comment").on("click",function(){
                    $(".comment-form,.cover").hide();
                })
            })
        </script>
	</div>
</body>
</html>
<script type="text/javascript" src="/static/js/layer/layer.js"></script>
<script type='text/javascript' src='/static/js/visitrecorde.js'></script>
<script language="JavaScript">
    visitdata(<?php echo $idsite; ?>,<?php echo $content_id; ?>,'<?php echo $info['title']; ?>',1);
    function add_comment1()
    {
        var content=$("#content").val();
        add_comment(<?php echo $idsite; ?>,<?php echo $content_id; ?>,'<?php echo $info['title']; ?>',content,1)
    }
    var visitflag="<?php echo $visitflag; ?>";
    $(function(){
        if(visitflag=="1")
        {
            $("#colle").css("color","#ff7902");
        }
    })
    function add_collection1(){
        if(visitflag=="1")
        {
            layer.confirm('已收藏',{btn:['关闭']});
            return;
        }
        add_collection(<?php echo $idsite; ?>,<?php echo $content_id; ?>,'<?php echo $info['title']; ?>',1);;
        $("#colle").css("color","#ff7902");
        visitflag="1";
    }
</script>
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script language="JavaScript">
    function  forwardedlog(title,desc,link,imgurl,inttype) {
        var dataid='<?php echo $content_id; ?>';
        var data= {'dataid':dataid,"chrtitle":title, "chrdesc":desc, "chrlink": link,"imgurl":imgurl,'datatype':1,'inttype':inttype};
        $.ajax({
            url:"/<?php echo $sitecode; ?>/forwardedlog",
            data:data,
            type:"post",
            dataType:"json",
            success:function(msg) {
                //alert(msg);
            }
        })
    }
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"]; ?>',
        timestamp: '<?php echo $signPackage["timestamp"]; ?>',
        nonceStr: '<?php echo $signPackage["nonceStr"]; ?>',
        signature: '<?php echo $signPackage["signature"]; ?>',
        jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage']
    });
    wx.ready(function () {

        //获取“分享到朋友圈”按钮点击状态及自定义分享内容接口（即将废弃）
        wx.onMenuShareTimeline({
            title: '<?php echo $info["title"]; ?>', // 分享标题
            link: '<?php echo $signPackage["url"]; ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'https://www.tongxiang123.cn<?php echo $info["picurl"]; ?>', // 分享图标
            success: function () {
                forwardedlog('<?php echo $info["title"]; ?>', '', '<?php echo $signPackage["url"]; ?>', '<?php echo $info["picurl"]; ?>', 2);
            }
        });

        //获取“分享给朋友”按钮点击状态及自定义分享内容接口（即将废弃）
        wx.onMenuShareAppMessage({
            title: '<?php echo $info["title"]; ?>', // 分享标题
            desc: '', // 分享描述
            link: '<?php echo $signPackage["url"]; ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'https://www.tongxiang123.cn<?php echo $info["picurl"]; ?>', // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                forwardedlog('<?php echo $info["title"]; ?>', '','<?php echo $signPackage["url"]; ?>','<?php echo $info["picurl"]; ?>',1);

            }
        });

    });

    wx.error(function(res){
       // alert("接口调取失败")
    });

</script>

<script>

	// 点击查看大图

	function funcReadImgInfo() {

		var imgs = [];

		var imgObj = $('.rich_media img');//这里改成相应的对象
		var protocol = window.location.protocol;//协议
		var host = window.location.host;//主地址
		var port = window.location.port;//端口

		for (var i = 0; i < imgObj.length; i++) {

			var src = imgObj.eq(i).attr('src');

			src = src.substr(0, 4).toLowerCase() == "http" ? src : protocol + '//' + host + src;

			imgs.push(src);

			imgObj.eq(i).click(function () {

				var nowImgurl = $(this).attr('src');
                
                nowImgurl = nowImgurl.substr(0, 4).toLowerCase() == "http" ? nowImgurl : protocol + '//' + host + nowImgurl;

				WeixinJSBridge.invoke("imagePreview", {

					"urls": imgs,

					"current": nowImgurl

				});

			});

		}

	}

	funcReadImgInfo();
</script>