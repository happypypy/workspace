<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:32:"template/M3/activity/detail.html";i:1562557713;s:52:"D:\workspace\work\public\template\M3\lib\header.html";i:1561691700;s:53:"D:\workspace\work\public\template\M3\lib\footer0.html";i:1561691700;s:53:"D:\workspace\work\public\template\M3\lib\footer1.html";i:1561691700;}*/ ?>
<?php $info = $cms->GetActivityInfo($id,'nodeid,intflag,chkdown,chrtitle,chrcontent,chrimg,publishname,dtpublishtime,chksignup,dtstart,dtend,dtsignstime,dtsignetime,intsignnum,minage,maxage,chrrange,ischarge,txtfwtk,chraddressdetail,chrmap,chrmaplng,chrmaplat,usertype');
if(empty($info))
{
header("location:/error.php?msg=".urlencode("没找到相关活动，有疑问请和管理联系！")."&url=".urlencode("/".$sitecode));
exit();
}
elseif(($info["intflag"]!=2 || $info["chkdown"]==1)  && empty($_GET["type"]))
{
header("location:/error.php?msg=".urlencode("活动不存在，有疑问请和管理联系！")."&url=".urlencode("/".$sitecode));
exit();
}elseif(isset($usertypeflag)){
header("location:/error.php?msg=".urlencode('你好，该内容只有【'.$usertype.'】用户才可以查看/购买。如有疑问，请联系客服，谢谢！')."&url=".urlencode("/".$sitecode));
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

	<title><?php echo $info['chrtitle']; ?></title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />

	<script type="text/javascript" src="<?php echo $roottpl; ?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $roottpl; ?>/js/swiper.min.js"></script>
	<script type="text/javascript" src="<?php echo $roottpl; ?>/js/common.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/swiper.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/common.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $roottpl; ?>/style/css/pc.css">
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
	<div class="where jigou">
		<p><?php echo $cms->getNodeName($info['nodeid']); ?></p>
		<a href="/<?php echo $sitecode; ?>" class="iconfont">&#xe617;</a>
	</div>

	<div class="rich_media clearfix">
		<div class="rich_media_area_primary">
            <h2 class="rich_media_title"><?php echo $info['chrtitle']; ?></h2>
			<?php if($activity_cashed && $is_cashed): ?>
			<!-- 获取优惠券 -->
			<div class="get-coupon">
				<div class="tit"><span>会员专享</span></div>
				<div class="tips">会员专享现金券仅限当前活动使用，领取后<?php echo $activity_cashed['activity_cashed_validity']; ?>天内有效</div>
				<div class="coupon">
					<div class="price"><?php echo intval($activity_cashed['activity_cashed_amount']); ?></div>
					<div class="txt">仅限本活动使用</div>
					<a href="javascript:void(0);" onclick="getvolume1();" class="btn"></a>
				</div>
			</div>
			<?php endif; ?>
            
			<div class="rich_media_meta_list">
				<em class="rich_media_meta rich_media_meta_text">发布时间：</em>
				<em class="rich_media_meta rich_media_meta_text"><?php echo date('Y-m-d',strtotime($info['dtpublishtime'])); ?></em>
				<br>
				<em class="rich_media_meta rich_media_meta_text">活动时间：</em>
				<em class="rich_media_meta rich_media_meta_text"><?php echo date('Y-m-d',strtotime($info['dtstart'])); ?> -</em>
				<em class="rich_media_meta rich_media_meta_text"><?php echo date('Y-m-d',strtotime($info['dtend'])); ?></em>
				<br>
				<em class="rich_media_meta rich_media_meta_text">报名时间：</em>
				<em class="rich_media_meta rich_media_meta_text"><?php echo date('Y-m-d',strtotime($info['dtsignstime'])); ?> -</em>
				<em class="rich_media_meta rich_media_meta_text"><?php echo date('Y-m-d',strtotime($info['dtsignetime'])); ?></em>
				<br>
				<em class="rich_media_meta rich_media_meta_text">参与方式：</em>
				<em class="rich_media_meta rich_media_meta_text"><?php echo $info['chrrange']==1?'家长及儿童':($info['chrrange']==2?'仅儿童':'仅家长') ?></em>
				<br>
				<em class="rich_media_meta rich_media_meta_text">费用情况：</em>
				<em class="rich_media_meta rich_media_meta_text"><?php echo $info['ischarge']==1?"免费":"收费" ?></em>
				<br>
				<em class="rich_media_meta rich_media_meta_text">适合年龄：</em>
				<?php if($info['minage']==0 && $info['maxage']==0 ) { ?>
				<em class="rich_media_meta rich_media_meta_text">不限年龄</em>
				<?php } else { ?>
				<em class="rich_media_meta rich_media_meta_text"><?php echo $info['minage']; ?>岁 -</em>
				<em class="rich_media_meta rich_media_meta_text"><?php echo $info['maxage']; ?>岁</em>
				<?php } ?>
				<br>
				<em class="rich_media_meta rich_media_meta_text">最大人数：</em>
				<em class="rich_media_meta rich_media_meta_text"><?php echo $info['intsignnum']; ?>人</em>
			</div>
			
			<?php if(!empty($info['chrmap']) || !empty($info['txtfwtk'])) { ?>
			<div class="rich_media_meta_list" style="padding-top: 10px;">
				<em class="rich_media_meta rich_media_meta_text">交通指引</em>
				<?php if(!empty($info['chrmap'])) { ?>
				<p><span style="font-size: 13px; "><?php echo $info['chrmap']; ?></span><p>
				<?php } if(!empty($info['chraddressdetail'])) { ?>
				<p><span style="font-size: 13px; color: chocolate; "><?php echo $info['chraddressdetail']; ?><a href="javascript:;" onClick="javascript:goadrr() ;">【查看地图】</a></span><p>
				<?php } ?>
			</div>
			<?php } if(!empty($info['txtfwtk'])) { ?>
			<div class="rich_media_meta_list">
				<em class="rich_media_meta rich_media_meta_text">服务条款</em>
				<?php echo replearurl($info['txtfwtk']) ?>
			</div>
            <?php } ?>
            
           
            
            
            <div class="docs-pictures clearfix">
                <?php echo replearurl($info['chrcontent']) ?>
            </div>
		</div>
	</div>
	<?php $commentlist = $cms->GetComment($idsite,$id); ?>
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
					<li>

						<a href="/<?php echo $sitecode; ?>/activity/<?php echo $info['nodeid']; ?>">
							<span><i class="iconfont home">&#xe682;</i></span>
							<p>更多</p>
						</a>

					</li>

					<li id="openService">
						<a href="/<?php echo $sitecode; ?>/waiter/2/<?php echo $id; ?>">
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

					<li class="on">
						<a href="javascript:;" onclick="javascript:bm();" >
							<span><i class="iconfont message">&#xe608;</i></span>
							<p id="openSign" >报名</p>
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
    visitdata(<?php echo $idsite; ?>,<?php echo $id; ?>,'<?php echo $info['chrtitle']; ?>',2);
    function add_comment1()
    {
        var content=$("#content").val();
        add_comment(<?php echo $idsite; ?>,<?php echo $id; ?>,'<?php echo $info['chrtitle']; ?>',content,2)
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
        add_collection(<?php echo $idsite; ?>,<?php echo $id; ?>,'<?php echo $info['chrtitle']; ?>',2);
        $("#colle").css("color","#ff7902");
        visitflag="1";

    }
    $(function () {
    <?php
        if(time()>strtotime($info['dtsignetime']))
        { ?>
            $("#openSign").html("报名结束");
        <?php } ?>
    })
    function bm() {
    	<?php if($info['chksignup']!=1){ ?>
            layer.confirm('没有开启报名功能',{btn:['关闭']});
        <?php } else if(time()<strtotime($info['dtsignstime'])) { ?>
            layer.confirm("报名将在<?php echo date('Y-m-d H:i:s',strtotime($info['dtsignstime'])); ?>开始，现在还不能报名",{btn:['关闭']});
        <?php } else if(time()>strtotime($info['dtsignetime'])) { ?>
            layer.confirm('报名已结束，不能再报名',{btn:['关闭']});
        <?php } else if($bmflag==2) { ?>
            layer.confirm('<div style="text-align: center"><img style="width: 150px;height: 150px" src="<?php echo $qrcodeurl; ?>" /><br>没有关注或没有登陆，请先关注后才能报名</div>',{btn:['关闭']});
        <?php } else if($bmflag==2) { ?>
            layer.confirm('没有关注，请先关注后才能报名',{btn:['关闭']});
        <?php } else  { ?>
            window.location="/<?php echo $sitecode; ?>/signup/<?php echo $id; ?>";
        <?php } ?>
    }
    function  forwardedlog(title,desc,link,imgurl,inttype) {
        var dataid='<?php echo $id; ?>';
        var data= {'dataid':dataid,"chrtitle":title, "chrdesc":desc, "chrlink": link,"imgurl":imgurl,'datatype':2,'inttype':inttype};
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
</script>

<!--<script type="text/javascript" src="https://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>-->
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script language="JavaScript">
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"]; ?>',
        timestamp: '<?php echo $signPackage["timestamp"]; ?>',
        nonceStr: '<?php echo $signPackage["nonceStr"]; ?>',
        signature: '<?php echo $signPackage["signature"]; ?>',
        jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage','openLocation']
    });
    wx.ready(function () {
        /*
        //自定义“分享给朋友”及“分享到QQ”按钮的分享内容（1.4.0）
        wx.updateAppMessageShareData({
            title: '<?php echo $info["chrtitle"]; ?>', // 分享标题
            desc: '活动时间：<?php echo date("Y-m-d",strtotime($info["dtstart"])); ?>-<?php echo date("Y-m-d",strtotime($info["dtend"])); ?>', // 分享描述
            link: '<?php echo $signPackage["url"]; ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'https://www.tongxiang123.cn<?php echo $info["chrimg"]; ?>', // 分享图标
        }, function(res) {
            alert(JSON.stringify(res));
            forwardedlog('<?php echo $info["chrtitle"]; ?>', '活动时间：<?php echo date("Y-m-d",strtotime($info["dtstart"])); ?>-<?php echo date("Y-m-d",strtotime($info["dtend"])); ?>','<?php echo $signPackage["url"]; ?>','<?php echo $info["chrimg"]; ?>',1);
            //这里是回调函数
        });

        //自定义“分享到朋友圈”及“分享到QQ空间”按钮的分享内容（1.4.0）
        wx.updateTimelineShareData({
            title: '<?php echo $info["chrtitle"]; ?>', // 分享标题
            link: '<?php echo $signPackage["url"]; ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'https://www.tongxiang123.cn<?php echo $info["chrimg"]; ?>', // 分享图标
        }, function(res) {
            //这里是回调函数
            alert(JSON.stringify(res));
            forwardedlog('<?php echo $info["chrtitle"]; ?>','','<?php echo $signPackage["url"]; ?>','<?php echo $info["chrimg"]; ?>',2);
        });
        */

        //获取“分享到朋友圈”按钮点击状态及自定义分享内容接口（即将废弃）
        wx.onMenuShareTimeline({
            title: '<?php echo $info["chrtitle"]; ?>', // 分享标题
            link: '<?php echo $signPackage["url"]; ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'https://www.tongxiang123.cn<?php echo $info["chrimg"]; ?>', // 分享图标
            success: function () {
                forwardedlog('<?php echo $info["chrtitle"]; ?>', '', '<?php echo $signPackage["url"]; ?>', '<?php echo $info["chrimg"]; ?>', 2);
            }
        });

        //获取“分享给朋友”按钮点击状态及自定义分享内容接口（即将废弃）
        wx.onMenuShareAppMessage({
            title: '<?php echo $info["chrtitle"]; ?>', // 分享标题
            desc: '活动时间：<?php echo date("Y-m-d",strtotime($info["dtstart"])); ?>-<?php echo date("Y-m-d",strtotime($info["dtend"])); ?>', // 分享描述
            link: '<?php echo $signPackage["url"]; ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'https://www.tongxiang123.cn<?php echo $info["chrimg"]; ?>', // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                forwardedlog('<?php echo $info["chrtitle"]; ?>', '活动时间：<?php echo date("Y-m-d",strtotime($info["dtstart"])); ?>-<?php echo date("Y-m-d",strtotime($info["dtend"])); ?>','<?php echo $signPackage["url"]; ?>','<?php echo $info["chrimg"]; ?>',1);

            }
        });

    });

    wx.error(function(res){
       // alert("接口调取失败")
    });
    function goadrr() {
        wx.openLocation({
            latitude: parseFloat(<?php echo $info['chrmaplat']; ?>),
            longitude: parseFloat(<?php echo $info['chrmaplng']; ?>),
            name: "详细地址",
            address: "<?php echo $info['chraddressdetail']; ?>",
            success: function () {
                layer.close(laynsg);
            },
            fail: function (res) {
                layer.alert(JSON.stringify(res));
                layer.close(laynsg);
            }
        });
    }

    function getvolume1() {
        $.ajax({
            url: "/<?php echo $sitecode; ?>/receive/<?php echo $id; ?>",
            data: {
            },
            type: 'post',
            dataType: 'json',
            success: function (result) {
                layer.alert(result.message)//提示
            },
            error: function () {
                alert('删除失败');
            }
        });
    }

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