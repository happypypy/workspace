<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:22:"template/M3/index.html";i:1569402615;s:60:"D:\workspace\work\public\template\modules\common\header.html";i:1569375256;s:60:"D:\workspace\work\public\template\modules\common\footer.html";i:1568605126;}*/ ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="yes" name="apple-mobile-web-app-capable">
  <meta content="yes" name="apple-touch-fullscreen">
  <meta content="telephone=no,email=no" name="format-detection">
  <meta name="viewport"
    content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,user-scalable=0" />
  <script src="/static/template/pub/js/common.js"></script>
  <link rel="stylesheet" href="/static/template/pub/css/main.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="/template/M3/css/index.css">
  <link rel="stylesheet" href="/static/template/skin/M1/css/skin.css">
  <script src="/static/template/pub/js/jquery.min.js"></script>
  <script src="/static/template/pub/js/swiper.min.js"></script>
  <script src="/static/template/pub/js/menu.js"></script>
  <title>童享云</title>
</head>

<body class="flexCol">
 <header class="flex">
  <a href="/<?php echo $sitecode; ?>" class="logo flex">
    <img src="<?php echo $cms->GetConfigVal('webset','weblogo',$idsite);; ?>">
  </a>
  <div class="menu-btns iconfont">
    <span class="menu-btn1 iconfont fl" onclick="window.location='/<?php echo $sitecode; ?>/mine'">&#xe615;</span>
    <span class="menu-btn2 iconfont fl bgColor" id="open-menu">&#xe602;</span>
    <span class="menu-btn3 iconfont fl bgColor" id="close-menu">&#xe677;</span>
  </div>
</header>
<aside>
  <div class="menu" id="menu">
    <ul>
	  <li class="border-line"><a href="/<?php echo $sitecode; ?>">首页</a></li>
	  <?php $result = $cms->GetAllNodes(['showonmenu'=>1,'nodetype'=>['in','1,2,3'],'parentid'=>0],'idorder asc,nodeid asc','',20,$idsite); if(is_array($result) || $result instanceof \think\Collection || $result instanceof \think\Paginator): $i = 0; $__LIST__ = $result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?>
      <li class="border-line"><a href="<?php echo strstr($menu['url'],'http') ? $menu['url'] : '/'.$sitecode.'/'.$menu['url']; ?>"><?php echo $menu['nodename']; ?></a></li>
	  <?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
  </div>
  <div class="menu-cover"></div>
</aside>

  <section class="section index-section">
    <div class="banner">
      <div class="swiper-container swiper1 swiper-container1">
        <ul class="swiper-wrapper">
          <?php  $result1=$cms->getAD($idsite,51325,6); if(empty($result1) || (($result1 instanceof \think\Collection || $result1 instanceof \think\Paginator ) && $result1->isEmpty())): ?>
          <div class="swiper-slide"><a href="javascript:;"><img src="/static/modules/images/banner_01.jpg"></a></div>
          <?php else: if(is_array($result1) || $result1 instanceof \think\Collection || $result1 instanceof \think\Paginator): $i = 0; $__LIST__ = $result1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
          <div class="swiper-slide"><a href="<?php echo $v['ad_link']==''?'javascript:;':$v['ad_link']; ?>"><img src="<?php echo $v['ad_code']; ?>"></a></div>
          <?php endforeach; endif; else: echo "" ;endif; endif; ?>
        </ul>
        <ol class="swiper-pagination"></ol>
      </div>
    </div>
    <div class="nav">
      <ul class="flex navbar">
        <?php foreach($result as $k=>$v){
        $jumpUrl ="/". $sitecode.$v["url"];
        if(substr( $v["url"],0,4 ) == "http"){
        $jumpUrl = $v["url"];
        }
        ?>
        <li class=" bgColor"><a class="flexCol navbar-link" href="<?php echo $jumpUrl; ?>">
          <p><?php echo $v['nodename']; ?></p>
        </a></li>
        <?php }?>
      </ul>
    </div>

    <!-- 拼团 -->
    <?php if(checkedMarketingPackage($idsite, 'group_buy') &&  $groupBuys): ?>
    <div class="index-common-wrap">
      <div class="index-wrap-title">限时拼团<a href="/<?php echo $sitecode; ?>/group_buy_list" class="more">更多&gt;&gt;</a></div>
      <ul class="index-common-list">
        <?php if(empty($groupBuys) || (($groupBuys instanceof \think\Collection || $groupBuys instanceof \think\Paginator ) && $groupBuys->isEmpty())): ?>
        <li class="index-list-item no-data">抱歉，当前没有拼团活动信息~</li>
        <?php else: if(is_array($groupBuys) || $groupBuys instanceof \think\Collection || $groupBuys instanceof \think\Paginator): $i = 0; $__LIST__ = $groupBuys;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$groupBuy): $mod = ($i % 2 );++$i;?>
        <li class="index-list-item ">
          <a href="/<?php echo $sitecode; ?>/detail/<?php echo $groupBuy['activity_id']; ?>" class="flexCol">
            <div class="flex">
              <div class="index-list-item-img"><img src="<?php echo $groupBuy['chrimg_m']; ?>"></div>
              <div class="index-list-item-txt flexCol">
                <div class="info title fontColor flex">
                  <?php echo $groupBuy['chrtitle']; ?>
                </div>
                <div class="info age flex">
                  <div class="tit">适合年龄</div>：
                  <div class="txt"><?php echo $groupBuy['minage'] == $groupBuy['maxage'] && $groupBuy['maxage'] == 0 ? '不限年龄' : $groupBuy['minage'] . ' ~ ' . $groupBuy['maxage']; ?></div>
                </div>
                <div class="info time flex">
                  <div class="tit">使用时间</div>：
                  <div class="txt"><?php echo date('Y-m-d', $groupBuy['start_at']); ?> ~ <?php echo date('Y-m-d', $groupBuy['end_at']); ?></div>
                </div>
              </div>
            </div>
            <div class="index-list-item-price">
              <div class="txt1">单购:￥<del><?php echo $groupBuy['member_price']; ?></del></div>
              <div class="txt2 red"><?php echo $groupBuy['group_num']; ?>人拼团:￥<span><?php echo $groupBuy['group_buy_price']; ?></span></div>
              <input class="normal-btn fr" type="button" value="我要拼团" onclick="">
            </div>
          </a>
        </li>
        <?php endforeach; endif; else: echo "" ;endif; endif; ?>

      </ul>
    </div>
    <?php endif; if($tmp_style == ''): ?>
    <!-- 小图 -->
    <!--热门推荐-->
    <div class="index-activity-wrap">
      <div class="index-wrap-title">热门推荐</div>
      <ul class="index-activity-list flex">
        <?php  $re = $cms->GetActivity($idsite,array('chkdown'=>array('neq',1)),'','idactivity,chkisindex,chrtitle,chrimg_m,chrurl,chrsummary,dtstart,dtpublishtime,hits,s.is_receive_cashed,min_price',10);
        $index=0;
        foreach($re as $k=>$val){
        if($val['chkisindex']==1) {
        $index++;
        ?>

        <li class="index-activity-item-s">
          <a href="<?php echo (empty($val['chrurl'])?'/'.$sitecode.'/detail/'.$val['idactivity']:$val['chrurl']) ?>" class="flex">
            <div class="index-activity-item-img"><img src="<?php echo $val['chrimg_m']; ?>"><span style="display: none"
                                                                                     class="index-activity-addr">车公庙</span></div>
            <div class="index-activity-item-txt flexCol">
              <div class=" index-activity-item-title"><?php if($val['is_receive_cashed'] == 1 && $is_cashed): ?><span
                      class="iconfont fontColor">&#xe624;</span><?php endif; ?><?php echo $val['chrtitle']; ?>
              </div>
              <div class=" index-activity-item-view">
                <div class="index-activity-price"><span class="red">￥<em><?php echo $val['min_price']; ?></em>起</span></div>
                <div class="eyes"><?php echo $val['hits']; ?></div>
                <div class="time"><?php echo date('m-d',strtotime($val['dtpublishtime'])); ?></div>
              </div>
              <!-- <div class=" index-activity-item-tag bgColor flex">
                <span class="tag">活动标标</span>|
                <span class="tag">活动标标</span>|
                <span class="age">参与龄标</span>
              </div> -->
            </div>
          </a>
        </li>
        <?php }}
                if($index==0){ ?>
        <li style="height:0.3rem;">
          <div class="t9">抱歉，当前没有最新产品信息</div>
        </li>
        <?php }?>
      </ul>
    </div>
    <!--广告位-->
    <div class="sup-banner banner">
      <div class="swiper-container swiper2 swiper-container1">
        <ul class="swiper-wrapper">
          <?php $result1=$cms->getAD($idsite,51327,3); if(empty($result1) || (($result1 instanceof \think\Collection || $result1 instanceof \think\Paginator ) && $result1->isEmpty())): ?>
          <li class="swiper-slide"><a href="javascript:;"><img class="swiper-slideImg" src="/static/modules/images/bar_03.jpg"></a></li>
          <?php else: if(is_array($result1) || $result1 instanceof \think\Collection || $result1 instanceof \think\Paginator): $i = 0; $__LIST__ = $result1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
          <li class="swiper-slide"><a href="<?php echo $v['ad_link']==''?'javascript:;':$v['ad_link']; ?>"><img src="<?php echo $v['ad_code']; ?>"></a></li>
          <?php endforeach; endif; else: echo "" ;endif; endif; ?>
        </ul>
        <ol class="swiper-pagination"></ol>
      </div>
    </div>
    <!--动态信息-->
    <div class="index-activity-wrap">
      <div class="index-wrap-title">动态信息</div>
      <ul class="index-activity-list flex">
        <?php $re = $cms->GetContentsIndex($idsite,10);
        if(empty($re)){ ?>
        <li style="height:0.3rem;">
        <li class="no-data">抱歉，当前没有最新动态信息</li>
        </li>
        <?php }
                foreach($re as $ke=>$val){ ?>

        <li class="index-activity-item-s">
          <a href="<?php echo (empty($val['linkurl'])?'/'.$sitecode.'/content/'.$val['contentid']:$val['linkurl']) ?>" class="flex">
            <div class="index-activity-item-img"><img src="<?php echo $val['picurl']; ?>">
            </div>
            <div class="index-activity-item-txt flexCol">
              <div class=" index-activity-item-title">
                <?php echo $val['title']; ?>
              </div>
              <div class=" index-activity-item-view">
                <div class="eyes"><?php echo $val['hits']; ?></div>
                <div class="time"><?php echo date('m-d',strtotime($val['sys00003'])); ?></div>
              </div>
            </div>
          </a>
        </li>
        <?php } ?>
      </ul>
    </div>
    <?php endif; ?>
    <!-- 中图 -->
    <?php if($tmp_style == 'm'): ?>
    <!--热门推荐-->
    <div class="index-activity-wrap">
      <div class="index-wrap-title">热门推荐</div>
      <ul class="index-activity-list flex">
        <?php  $re = $cms->GetActivity($idsite,array('chkdown'=>array('neq',1)),'','idactivity,chkisindex,chrtitle,chrimg,chrurl,chrsummary,dtstart,dtpublishtime,hits,s.is_receive_cashed,min_price',10);
        $index=0;
        foreach($re as $k=>$val){
        if($val['chkisindex']==1) {
        $index++;
        if($k/2 == 0){
        ?>
        <li class="index-activity-item-m">
          <a href="<?php echo (empty($val['chrurl'])?'/'.$sitecode.'/detail/'.$val['idactivity']:$val['chrurl']) ?>" class="flexCol">
            <div class="index-activity-item-img"><img src="<?php echo $val['chrimg']; ?>"><span style="display: none"
                                                                                   class="index-activity-addr">车公庙</span>
              <div class="index-activity-price"><span class="red">￥<em><?php echo $val['min_price']; ?></em>起</span></div>
            </div>
            <div class="index-activity-item-txt flexCol">
              <div class=" index-activity-item-title flex"> <?php if($val['is_receive_cashed'] == 1 && $is_cashed): ?><span
                      class="iconfont fontColor">&#xe624;</span><?php endif; ?><?php echo $val['chrtitle']; ?>
              </div>
            </div>
            <div class="index-activity-item-view flex">
              <div class="eyes"><?php echo $val['hits']; ?></div>
              <div class="time"><?php echo date('m-d',strtotime($val['dtpublishtime'])); ?></div>
            </div>
          </a>
        </li>

        <?php }else{ ?>
        <li class="index-activity-item-m">
          <a href="<?php echo (empty($val['chrurl'])?'/'.$sitecode.'/detail/'.$val['idactivity']:$val['chrurl']) ?>" class="flexCol">
            <div class="index-activity-item-img"><img src="<?php echo $val['chrimg']; ?>"><span style="display: none"
                                                                                   class="index-activity-addr">车公庙</span>
              <div class="index-activity-price"><span class="red">￥<em><?php echo $val['min_price']; ?></em>起</span></div>
            </div>
            <div class="index-activity-item-txt flexCol">
              <div class=" index-activity-item-title flex"> <?php if($val['is_receive_cashed'] == 1 && $is_cashed): ?><span
                      class="iconfont fontColor">&#xe624;</span><?php endif; ?><?php echo $val['chrtitle']; ?>
              </div>
            </div>
            <div class="index-activity-item-view flex">
              <div class="eyes"><?php echo $val['hits']; ?></div>
              <div class="time"><?php echo date('m-d',strtotime($val['dtpublishtime'])); ?></div>
            </div>
          </a>
        </li>

        <?php }}}
                if($index==0){ ?>
        <li style="height:0.3rem;">
          <div class="t9">抱歉，当前没有最新产品信息</div>
        </li>
        <?php }?>
      </ul>
    </div>
    <!--动态信息-->
    <!--广告位-->
    <div class="sup-banner banner">
      <div class="swiper-container swiper2 swiper-container1">
        <ul class="swiper-wrapper">
          <?php $result1=$cms->getAD($idsite,51327,3); if(empty($result1) || (($result1 instanceof \think\Collection || $result1 instanceof \think\Paginator ) && $result1->isEmpty())): ?>
          <li class="swiper-slide"><a href="javascript:;"><img class="swiper-slideImg" src="/static/modules/images/bar_03.jpg"></a></li>
          <?php else: if(is_array($result1) || $result1 instanceof \think\Collection || $result1 instanceof \think\Paginator): $i = 0; $__LIST__ = $result1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
          <li class="swiper-slide"><a href="<?php echo $v['ad_link']==''?'javascript:;':$v['ad_link']; ?>"><img src="<?php echo $v['ad_code']; ?>"></a></li>
          <?php endforeach; endif; else: echo "" ;endif; endif; ?>
        </ul>
        <ol class="swiper-pagination"></ol>
      </div>
    </div>
    <div class="index-activity-wrap">
      <div class="index-wrap-title">动态信息</div>
      <ul class="index-activity-list flex">
        <?php $re = $cms->GetContentsIndex($idsite,10);
        if(empty($re)){ ?>
        <li style="height:0.3rem;">
        <li class="no-data">抱歉，当前没有最新动态信息</li>
        </li>
        <?php }
                foreach($re as $ke=>$val){
        if($k/2 == 0){
        ?>
        <li class="index-activity-item-m">
          <a href="<?php echo (empty($val['linkurl'])?'/'.$sitecode.'/content/'.$val['contentid']:$val['linkurl']) ?>" class="flexCol">
            <div class="index-activity-item-img">
              <img src="<?php echo $val['fieldspare6']; ?>">
            </div>
            <div class="index-activity-item-txt flexCol">
              <div class=" index-activity-item-title"><?php echo $val['title']; ?>
              </div>
            </div>
            <div class="index-activity-item-view flex">
              <div class="eyes"><?php echo $val['hits']; ?></div>
              <div class="time"><?php echo date('m-d',strtotime($val['sys00003'])); ?></div>
            </div>
          </a>
        </li>
        <?php }else{ ?>
        <li class="index-activity-item-m">
          <a href="<?php echo (empty($val['linkurl'])?'/'.$sitecode.'/content/'.$val['contentid']:$val['linkurl']) ?>" class="flexCol">
            <div class="index-activity-item-img">
              <img src="<?php echo $val['fieldspare6']; ?>">
            </div>
            <div class="index-activity-item-txt flexCol">
              <div class=" index-activity-item-title"><?php echo $val['title']; ?>
              </div>
            </div>
            <div class="index-activity-item-view flex">
              <div class="eyes"><?php echo $val['hits']; ?></div>
              <div class="time"><?php echo date('m-d',strtotime($val['sys00003'])); ?></div>
            </div>
          </a>
        </li>

        <?php }} ?>
      </ul>
    </div>
    <?php endif; ?>
    <!-- 大图 -->
    <?php if($tmp_style == 'b'): ?>
    <!--热门推荐-->
    <div class="index-activity-wrap">
      <div class="index-wrap-title">热门推荐</div>
      <ul class="index-activity-list flex">
        <?php  $re = $cms->GetActivity($idsite,array('chkdown'=>array('neq',1)),'','idactivity,chkisindex,chrtitle,chrimg,chrurl,chrsummary,dtstart,dtpublishtime,hits,s.is_receive_cashed,min_price',10);
        $index=0;
        foreach($re as $k=>$val){
        if($val['chkisindex']==1) {
        $index++;
        ?>
        <li class="index-activity-item-b">
          <a href="<?php echo (empty($val['chrurl'])?'/'.$sitecode.'/detail/'.$val['idactivity']:$val['chrurl']) ?>" class="flexCol">
            <div class="index-activity-item-img">
              <img src="<?php echo $val['chrimg']; ?>">
              <span class="index-activity-addr" style="display: none">车公庙</span>
              <div class="index-activity-price"><span class="red">￥<em><?php echo $val['min_price']; ?></em>起</span></div>
            </div>
            <div class="index-activity-item-txt flexCol">
              <div class=" index-activity-item-title"><?php if($val['is_receive_cashed'] == 1 && $is_cashed): ?> <span class="index-activity-token iconfont fontColor">&#xe624;</span><?php endif; ?><?php echo $val['chrtitle']; ?>
                <!-- <div class=" index-activity-item-tag flex">
                      <span class="tag">活动签签</span>
                      <span class="tag">活动签</span>
                      <span class="age">参与年龄动签</span>
                    </div> -->
              </div>
              <div class="index-activity-item-view flex">
                <div class="eyes"><?php echo $val['hits']; ?></div>
                <div class="time"><?php echo date('m-d',strtotime($val['dtpublishtime'])); ?></div>
              </div>
            </div>
          </a>
        </li>
        <?php }}
                if($index==0){ ?>
        <li style="height:0.3rem;">
          <div class="t9">抱歉，当前没有最新产品信息</div>
        </li>
        <?php }?>
      </ul>
    </div>
    <!--广告位-->
    <div class="sup-banner banner">
      <div class="swiper-container swiper2 swiper-container1">
        <ul class="swiper-wrapper">
          <?php $result1=$cms->getAD($idsite,51327,3); if(empty($result1) || (($result1 instanceof \think\Collection || $result1 instanceof \think\Paginator ) && $result1->isEmpty())): ?>
          <li class="swiper-slide"><a href="javascript:;"><img class="swiper-slideImg" src="/static/modules/images/bar_03.jpg"></a></li>
          <?php else: if(is_array($result1) || $result1 instanceof \think\Collection || $result1 instanceof \think\Paginator): $i = 0; $__LIST__ = $result1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
          <li class="swiper-slide"><a href="<?php echo $v['ad_link']==''?'javascript:;':$v['ad_link']; ?>"><img src="<?php echo $v['ad_code']; ?>"></a></li>
          <?php endforeach; endif; else: echo "" ;endif; endif; ?>
        </ul>
        <ol class="swiper-pagination"></ol>
      </div>
    </div>
    <!--动态信息-->
    <div class="index-activity-wrap">
      <div class="index-wrap-title">动态信息</div>
      <ul class="index-activity-list flex">
        <?php $re = $cms->GetContentsIndex($idsite,10);
        if(empty($re)){ ?>
        <li style="height:0.3rem;">
        <li class="no-data">抱歉，当前没有最新动态信息</li>
        </li>
        <?php }
                foreach($re as $ke=>$val){ ?>
        <li class="index-activity-item-b">
          <a href="<?php echo (empty($val['linkurl'])?'/'.$sitecode.'/content/'.$val['contentid']:$val['linkurl']) ?>" class="flexCol">
            <div class="index-activity-item-img"><img src="<?php echo $val['fieldspare6']; ?>"></div>
            <div class="index-activity-item-txt">
              <div>
                <div class=" index-activity-item-title flex"><?php echo $val['title']; ?></div>
              </div>
              <div class="index-activity-item-view index-node-item-view">
                <div class="index-activity-view-inner flex" >
                  <div class="eyes"><?php echo $val['hits']; ?></div>
                  <div class="time"><?php echo date('m-d',strtotime($val['sys00003'])); ?></div>
                </div>
              </div>
            </div>
          </a>
        </li>
        <?php } ?>
      </ul>
    </div>
    <?php endif; ?>
    <div class="register-wrap" >
      <div class="register-link bgColor flex">
        <div class="iconfont retrac">&#xe881;</div>
        <div class="link-signin"><a href="/<?php echo $sitecode; ?>/dailysignin">签到</a></div>
      </div>
    </div>
  </section>

  <footer class="footer">

  </footer>
 <div class="copyright-wrap">
  <div class="copyright">
    <div><?php echo $cms->GetConfigVal('webset','webname',$idsite);; ?></div>
    <div><?php echo str_replace("rn","<br>", $cms->GetConfigVal('webset','copyright',$idsite)); ?></div>
  </div>
  <div class="support" onclick="location='https://www.tongxiang123.cn/tongxiang'">
    <div>童享云提供技术支持</div>
    <div>www.tongxiang123.com</div>
  </div>
</div>


  <script>
    $(function () {
      var mySwiper1 = new Swiper('.swiper-container.swiper1', {
        pagination: {
          el: '.swiper-pagination',
        },
        autoplay: {
          delay: 2000,
        },
        loop: true,
        watchOverflow: true,
      });

      var mySwiper2 = new Swiper('.swiper-container.swiper2', {
        pagination: {
          el: '.swiper-pagination',
        },
        autoplay: {
          delay: 3000,
        },
        loop: true,
        watchOverflow: true,
        effect: 'fade',
      });

    })
  </script>

  <script>
    $(function () {
      var flag = 1;
      $('.retrac').on('click', function () {
        if (flag == 1) {
          $(this).html('&#xe65a;').parents('.register-wrap').addClass('on');
          flag = 0;
        } else {
          $(this).html('&#xe881;').parents('.register-wrap').removeClass('on');
          flag = 1;
        }

      })
    })
  </script>
</body>

</html>