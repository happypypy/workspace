<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:35:"template/M2/signin/dailysignin.html";i:1562315896;s:68:"C:\phpStudy\PHPTutorial\WWW\work\public\template\M4\lib\header2.html";i:1561691701;s:68:"C:\phpStudy\PHPTutorial\WWW\work\public\template\M4\lib\footer3.html";i:1561691701;}*/ ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="yes" name="apple-mobile-web-app-capable">
  <meta content="yes" name="apple-touch-fullscreen">
  <meta content="telephone=no,email=no" name="format-detection">
  <meta name="viewport"
    content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,user-scalable=0" />
  <script src="/static/js/jquery.min.js"></script>
  <script src="/static/js/integral.js"></script>
  <link rel="stylesheet" href="/static/css/mobile.css">
  <title>积分商城</title>
</head>

<body class="flexCol">
  <header class="flex">
    <a href="/<?php echo $sitecode; ?>" class="logo flex">
      <img src="<?php echo $cms->GetConfigVal('webset','weblogo',$idsite);; ?>">
    </a>
    <div class="menu-btns iconfont">
      <span class="menu-btn1 iconfont fl" onclick="javascript:location.href='/<?php echo $sitecode; ?>/mine'">&#xe615;</span>
      <span class="menu-btn2 iconfont fl" id="open-menu">&#xe602;</span>
      <span class="menu-btn3 iconfont fl" id="close-menu">&#xe677;</span>
    </div>
  </header>
  <aside>
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
  </aside>

  <section class="score-section">
    <div class="score-content">
      <div class="qiandao-user flex">
        <div class="pic"><img src="<?php echo $userinfo['userimg']; ?>"></div>
        <div class="txt">
          <div class="name"><?php echo $userinfo['nickname']; ?>,欢迎您！</div>
          <div class="score flex">
            <span>积分：</span>
            <a href="/<?php echo $sitecode; ?>/integralrecord"><?php echo $userinfo['integral']; ?></a>
            <u>签到规则</u>
          </div>
        </div>
      </div>
      <div class="qiandao-item flexCol" id="signin">
        <?php if($is_signin==1): ?>
          <img style="pointer-events: none;"  src="/static/images/mobile/weiqiandao1.png">
          <p style="background-color: #999">已签到</p>
        <?php else: ?>
          <img style="pointer-events: none;" src="/static/images/mobile/weiqiandao.png">
          <p>点击签到</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="qiandao-list">
      <ul id="qiandao-con">

      </ul>
    </div>

    <div class="load" style="display: none"><span id="dataload" class="iconfont iconload">&#xe618;</span></div>
  </section>
  <footer class="footer">
    <div class="trademark-box">
        <div class="trademark">
        <div>童享云体验</div>
        <div>copyright 2018<br>咨询电话及微信：13927458435 </div>
        </div>
        <div onclick="location='https://www.tongxiang123.cn/tongxiang'">
        <div>童享云提供技术支持</div>
        <div>www.tongxiang123.com</div>
        </div>
    </div>
    <div class="footer-nav-box">
        <div class="footernav">
        <ul class="flex">
            <li>
            <a href="/<?php echo $sitecode; ?>/integralmall" class="flexCol">
                <span><i class="iconfont">&#xe60a;</i></span>
                <p>积分商城</p>
            </a>
            </li>

            <li>
            <a href="/<?php echo $sitecode; ?>/mine" class="flexCol">
                <span><i class="iconfont">&#xe666;</i></span>
                <p>个人中心</p>
            </a>
            </li>
        </ul>
        </div>
    </div>
</footer>

</body>
<script src="/static/js/layer/layer.js"></script>
<script src="/static/js/pub.js"></script>

<script type="text/javascript">
  document.body.style.visibility = "visible";
</script>
<script>
  var page = 1;
  var pageSize = 10;
  var scrollState = true;  // 下拉滚动状态, true：启用 false: 禁用
  /* 签到规则 */
  $('.score>u').on('click', function () {
    layer.open({
      title: '签到规则',
      content: '<div class="imgWidth100"><?php echo $signrule; ?></div>',
      area: '100%',
      skin: 'my-layer',
      btnAlign: 'c',
    });
  })
  /* 签到提示 */
  $('.qiandao-item').on('click', function () {
    var that = $(this);
    $.ajax({
      type: "POST",
      url: "/<?php echo $sitecode; ?>/membersignin",
      data: {sitecode: '<?php echo $sitecode; ?>'},
      dataType: "JSON",
      success: function(data){
        var msg;
        if (data.status == 1) {
          msg = '签到成功';
          that.children('p').html('已签到').css({ 'background': '#999' }).end().children('img').attr('src', '/static/images/mobile/weiqiandao1.png');
          $("#signin").unbind();
          window.location.reload();
        }else if(data.status == 2){
          msg = '每天只能签到一次哦！记得明天继续签到~';
        }else if(data.status == 3){
          msg = '签到暂未开启~';
        }else if(data.status == 4){
          layer.confirm('<div style="text-align: center"><img style="width: 150px;height: 150px" src="'+ data.qrcodeurl +'" /><br>没有关注或没有登陆，请先关注后才能签到</div>',{btn:['关闭']});
          return;
        }else {
          msg = '签到失败，请稍后重试!';
        }
        layer.open({
            title: '信息',
            content: msg,
            area: '100%',
            skin: 'my-layer',
            btnAlign: 'c',
        });
      },
      error:function(e){  
        console.log(e);  
      }  
    });
  });
  
  loadData();
  // 加载签到记录
  function loadData(){
    scrollState = false;
    $('.load').show();
    $.ajax({
      url:'/<?php echo $sitecode; ?>/getsigninrecord',
      type: 'POST',
      data: {page: page, pageSize:pageSize},
      dataType: "JSON",
      success: function(result){
        var html = '';
        if(page < result.totalPage || result.data.length != 0){
          result.data.forEach(item => {
            html += '<li class="flex">';
            html += '<div class="txt">';
            html += '<div class="time">'+ formatTime(item.create_time) +'</div>';
            if(item.sign_day == 1){
              html += '<div class="tit">第<span>'+ item.sign_day +'</span>天签到</div>'
            }else{
              html += '<div class="tit">连续第<span>'+ item.sign_day +'</span>天签到</div>'
            }
            html += '</div>'
            html += '<div class="add-score">+'+ item.sign_integral +'</div>';
            html += '</li>';
          });
          $('#qiandao-con').append(html);
          scrollState = true;
        }else{
          html = '<div class="signin-tip">亲，你还没有签到哦，快来签到领积分吧！</div>';
          $('#qiandao-con').html(html);
          scrollState = false;
        }
        $('.load').hide();
        page++;
      }
    })
  }
  // 时间格式化
  function formatTime(timestamp){
    var now = new Date(timestamp*1000);
    var   year=now.getFullYear();    
    var   month=now.getMonth()+1;    
    var   date=now.getDate();    
    var   hour=now.getHours();    
    var   minute=now.getMinutes();    
    var   second=now.getSeconds();    
    return   year+"-"+month+"-"+date+"   "+hour+":"+minute+":"+second;    
  }
  //获取滚动条当前的位置
  function getScrollTop() {
      var scrollTop = 0;
      if (document.documentElement && document.documentElement.scrollTop) {
          scrollTop = document.documentElement.scrollTop;
      } else if (document.body) {
          scrollTop = document.body.scrollTop;
      }
      return scrollTop;
  }
  //获取当前可视范围的高度
  function getClientHeight() {
      var clientHeight = 0;
      if (document.body.clientHeight && document.documentElement.clientHeight) {
          clientHeight = Math.min(document.body.clientHeight, document.documentElement.clientHeight);
      } else {
          clientHeight = Math.max(document.body.clientHeight, document.documentElement.clientHeight);
      }
      return clientHeight;
  }
  //获取文档完整的高度
  function getScrollHeight() {
      return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
  }
  //滚动事件触发
  window.onscroll = function () {
    if (getScrollTop() + getClientHeight() === getScrollHeight() && scrollState == true) {
      loadData();
    }
  }
</script>

</html>