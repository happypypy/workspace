<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:35:"template/M5/assemble/ajax_list.html";i:1563417721;}*/ ?>
<?php if($groupBuys){ foreach($groupBuys as $groupBuy): ?>
<li>
    <a href="/<?php echo $sitecode; ?>/detail/<?php echo $groupBuy['activity_id']; ?>">
        <div class="pic"><img src="<?php echo $roottpl; ?>/images/bar_03.jpg"></div>
        <div class="txt">
            <div class="title title1"><?php echo $groupBuy['chrtitle']; ?></div>
            <div class="site site1"><i class="iconfont location">&#xe601;</i>适合年龄：<span><?php echo $groupBuy['minage'] == $groupBuy['maxage'] && $groupBuy['maxage'] == 0 ? '不限年龄' : $groupBuy['minage'] . ' ~ ' . $groupBuy['maxage']; ?></span></div>
            <div class="time time1"><i class="iconfont clock">&#xe602;</i><span>活动时间：<?php echo date('Y-m-d', $groupBuy['start_at']); ?> ~ <?php echo date('Y-m-d', $groupBuy['end_at']); ?></span></div>
        </div>
    </a>
    <a href="/<?php echo $sitecode; ?>/detail/<?php echo $groupBuy['activity_id']; ?>">
        <div class="txt spell-txt">
            <div class="spell-txtl">单购价:<del><?php echo $groupBuy['member_price']; ?></del>元</div>
            <span class="spell-txtm">拼团价:<span><?php echo $groupBuy['group_buy_price']; ?></span>元</span>
            <!-- <a href="/<?php echo $sitecode; ?>/detail/<?php echo $groupBuy['activity_id']; ?>"> -->
            <input class="button_1 spell-txtr" type="button" value="我要拼团" onclick="">
            <!-- </a> -->
        </div>
    </a>
</li>
<?php endforeach; }else{echo 11;exit;} ?>