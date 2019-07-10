<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:42:"template/M2/mine/ajax_mine_order_list.html";i:1561691699;}*/ ?>

            <?php if($list){foreach($list as $k=>$vo) {?>
            <li>
                <div class="flex">
                    <div class="pic"><img  src="<?php echo $vo['chrimg']; ?>" /></div>
                    <div class="txt">
                        <div class="title"><?php echo $vo['chrtitle']; ?></div>
                        <div class="price"><i class="iconfont price">&#xe620;</i><span>价格：<?php echo $vo['price']; ?>元</span></div>
                        <div class="state"><i class="iconfont laiyuan">&#xe60e;</i>状态：<span><?php echo $order_state[$vo['state']]; ?></span><span style="color: red;">&nbsp;&nbsp;<?php echo $vo['issign']==1?"(已签到)":""; ?></span></div>
                        <!-- <div class="style"><i class="iconfont laiyuan">&#xe60e;</i>方式：<span><?php echo $order_paytype1[$vo['paytype1']]; ?></span></div> -->
                        <div class="time"><i class="iconfont clock">&#xe602;</i><span>购买时间：<?php echo $vo['dtcreatetime']; ?></span></div>
                        <div class="time"><i class="iconfont clock">&#xe602;</i><span>活动时间：<?php echo $vo['dtstart']; ?>~<?php echo $vo['dtend']; ?></span></div>
                        <div class="btn">
                            <a href="/<?php echo $sitecode; ?>/orderdetail/<?php echo $vo['id']; ?>" style="background: #d98bb3;">订单详情</a>
                            <?php if($vo['state']==4 && $vo['isrefund']==1  && $vo['issign']!=1) { ?>
                            <a href="#" id="refund_<?php echo $vo['id']; ?>" onclick="javascript:refund(<?php echo $vo['id']; ?>)" style="background: #ed958f;">申请退款</a>
                            <?php } ?>
                            <!--支付成功后-->
                            <?php if($is_cashed && $vo['state']==4 && $vo['share_plan_id'] > 0) { ?>
                            <a href="/<?php echo $sitecode; ?>/share/<?php echo $vo['id']; ?>/0" style="background: #5fd2b8; ">分享现金券</a>
                            <?php } ?>
                            <!--待支付可以手动改为终止服务-->
                            <?php if($vo['state']==12 && $vo['stock_locked'] ==1) { ?>
                            <a onclick="cancel_order(<?php echo $vo['id']; ?>)"  style="background: #666; ">取消订单</a>
                            <?php } ?>
                            <!--终止服务可以进行重新下单-->
                            <?php if($vo['state']==10) { ?>
                            <a  href="/<?php echo $sitecode; ?>/againorder/<?php echo $vo['id']; ?>"  style="background: #d9b38b; ">重新下单</a>
                            <?php } ?>
                            <a href="#" style="background: #d9b38b; display: none" >查看约玩</a>
                        </div>
                    </div>
                </div>
            </li>
            <?php }}else{echo 11;exit;} ?>
        