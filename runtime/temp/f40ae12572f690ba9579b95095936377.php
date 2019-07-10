<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:41:"template/M2/activity/ajax_index_list.html";i:1561691696;}*/ ?>

<?php
if($result_data){
			foreach($result_data as $k=>$val){ ?>
<li>
    <a href="<?php echo (empty($val['chrurl'])?'/'.$sitecode.'/detail/'.$val['idactivity']:$val['chrurl']) ?>">
        <img src="<?php echo $val['chrimg_m']; ?>">
        <div class="word">
            <div class="tit"><?php if($val['is_receive_cashed'] == 1 && $is_cashed): ?><span class="iconfont coupon-link">&#xe624;</span><?php endif; ?><?php echo $val['chrtitle']; ?></div>
            <div class="txt"><?php echo $val['chrsummary']; ?></div>
            <div class="info">
                <!--<div class="type">活动</div>-->
                <div class="view"><span><?php echo $val['hits']; ?></span>浏览</div>
                <div class="time"><?php echo date('m-d',strtotime($val['dtpublishtime'])); ?></div>
            </div>
        </div>
    </a>
</li>
<?php }}else{echo 11;exit;} ?>
        