<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:37:"template/M5/node/ajax_index_list.html";i:1561691702;}*/ ?>

<?php  $re = $cms->GetContents($nodeid,[],'idorder DESC,contentid DESC','linkurl,summary,picurl,sys00003,hits,contentid,title,en_title,tc_title',$pageIndex,10);
if($re['data']){
foreach($re['data'] as $k=>$val){ ?>
<li>
    <a href="<?php echo (empty($val['linkurl'])?'/'.$sitecode.'/content/'.$val['contentid']:$val['linkurl']) ?>">
        <img src="<?php echo $val['picurl']; ?>">
        <div class="word">
            <div class="tit"><?php echo $val['title']; ?></div>
            <div class="txt"><?php echo $val['summary']; ?></div>
            <div class="info">
                <!--<div class="type">活动</div>-->
                <div class="view"><span><?php echo $val['hits']; ?></span>浏览</div>
                <div class="time"><?php echo date('m-d',strtotime($val['sys00003'])); ?></div>
            </div>
        </div>
    </a>
</li>
<?php }}else{echo 11;exit;} ?>
        