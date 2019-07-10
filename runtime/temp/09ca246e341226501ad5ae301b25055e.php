<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:34:"template/M1/mine/ajax_comment.html";i:1561691694;}*/ ?>

            <?php if($list){foreach($list as $k=>$vo) {?>
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
            <?php }}else{echo 11;exit;} ?>
        