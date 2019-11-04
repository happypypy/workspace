<?php

namespace app\home\controller;
use think\Db;
use think\Request;
use think\Log;
use app\home\model\GroupBuyOrder;


class Notify{
    /**
     * 订单支付回调
     * @author Hlt
     * @DateTime 2019-04-23T16:41:37+0800
     * @return   string                   success|fail
     */
    public function signup_post1()
    {
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        //$config=$this->wxConfig;
        $input= file_get_contents("php://input");
        $xml='';
       // file_put_contents(__ROOT__.'\test.txt',$input);
       //Log::info('支付回调参数:' . print_r($input, true));
        if($input){
            /*
             <xml><appid><![CDATA[wxd79730a60b28b7b4]]></appid>
                <bank_type><![CDATA[CFT]]></bank_type>
                <cash_fee><![CDATA[1]]></cash_fee>
                <fee_type><![CDATA[CNY]]></fee_type>
                <is_subscribe><![CDATA[Y]]></is_subscribe>
                <mch_id><![CDATA[1511368881]]></mch_id>
                <nonce_str><![CDATA[X5Sgl3rVGgpANheBlCjoPRMBlkvrm3r2]]></nonce_str>
                <openid><![CDATA[oZS4v1aiMfreRinDgG-uWZFEDpnk]]></openid>
                <out_trade_no><![CDATA[1534125205]]></out_trade_no>
                <result_code><![CDATA[SUCCESS]]></result_code>
                <return_code><![CDATA[SUCCESS]]></return_code>
                <sign><![CDATA[E49A339E327F7E3054EFC786E0177DC4]]></sign>
                <time_end><![CDATA[20180813095333]]></time_end>
                <total_fee>1</total_fee>
                <trade_type><![CDATA[JSAPI]]></trade_type>
                <transaction_id><![CDATA[4200000161201808135093928210]]></transaction_id>
                </xml>
             * */
            $xml = simplexml_load_string($input);
            //$money = (string)$xml->total_fee;
            $return_code = (string)$xml->return_code;
            //$result_code = (string)$xml->result_code;
            //$attach = (string)$xml->attach;
            $out_trade_no = (string)$xml->out_trade_no;
        }

//        $sitecode='tongxiang2';
//        $return_code='SUCCESS';
//        $out_trade_no='20190731164929933644';

       // Log::info('支付回调数据：' . print_r($xml, true));
        if($return_code=='SUCCESS'){
            //业务处理　　//修改订单/用户状态
                $arr=[];
                $arr['state']=4;
                $arr['intflag']=5;
                $arr['paytype1']=1;

                $tmp_Result= db('order')->where(array('transaction_id'=>$out_trade_no))->find();
                $idsite=$tmp_Result['idsite'];
                if(empty($tmp_Result)) {
                    Log::info('支付回订单不存在，订单流水号为：' . $out_trade_no);
                    echo "SUCCESS";
                    exit();
                }
                if($tmp_Result['state']==4)
                {
                    echo "SUCCESS";
                    exit();
                }
            //码库功能
            //1.判断此套餐是否使用了码库
            $useCode = db('activity_codebase')->where(['id_package' => $tmp_Result['package_id'], 'state' => 1, 'id_site' => $idsite])
                ->find();
            if ($useCode) {
                //查询编码，将编码写入订单
                $code = $this->useCodebase($useCode['id'], $tmp_Result['paynum'], $idsite);
                if($code)
                    $arr['checkcode'] = $code['code'];
                    $arr['checkcodeid'] = $code['id'];

            }

                $member = db('member')->where('idmember',$tmp_Result['fiduser'])->find();

                $bool=db('order')->where(array('transaction_id'=>$out_trade_no))->update($arr);
                $tmp_Result['state']=4;
                $tmp_Result['intflag']=5;
                $tmp_Result['paytype1']=1;
                if(isset($arr['checkcode'])){
                    $tmp_Result['checkcode']=$arr['checkcode'];
                }
                SetUserPayCount($tmp_Result['fiduser']);

                // 拼团业务
                //Log::info('拼团业务判断结果：' . print_r([!empty($tmp_Result['group_buy_order_id']), $tmp_Result['group_buy_order_id']], true));
                if(!empty($tmp_Result['group_buy_order_id']) && $tmp_Result['group_buy_order_id']!=0){
                    $groupBuyOrderModel = new GroupBuyOrder;
                    $res = $groupBuyOrderModel->afterStart($tmp_Result);
                    Log::info('支付后拼团操作 函数参数为：' . print_r([$tmp_Result,$res], true));
                }

                $integral_rule_config = db('integral_rule_config')->where(['idsite'=>$idsite])->find();
                $is_signup = 0;
                if(checkedMarketingPackage($idsite,'integral')){
                    $is_signup = $integral_rule_config['is_signup'];
                }
                // 是否已经开启，报名增加积分
                if($is_signup == 1){
                    $integral_rate = isset($integral_rule_config['signup_integral']) ? $integral_rule_config['signup_integral'] : 1;

                    // 报名增加积分记录
                    $integral = intval($tmp_Result['price'] -  $tmp_Result['price1'] - $tmp_Result['price2']) * $integral_rate;
                    // 如果积分大于0，才增加冻结积分
                    if($integral > 0){
                        db('member_integral_record')->insert([
                            'siteid' => $idsite,
                            'member_id' => $tmp_Result['fiduser'],
                            'userimg' => $member['userimg'],
                            'chrname' => $member['chrname'],
                            'nickname' => $member['nickname'],
                            'order_id' => $tmp_Result['id'],
                            'category_id' => 2,
                            'integral' => $integral,
                            'integral_rmark' => '',
                            'is_freeze' => 1,
                            'create_time' => time(),
                        ]);

                        // 发送客服消息
                        $msg = '报名成功，你获取到【'.$integral.'积分】<a href=\"'.request()->domain().'/'.$sitecode.'/integralrecord\">【查看详情】</a>';
                        send_ordinary_msg($sitecode, $member['openid'], $msg);

                    }
                }


                //判断该订单是否使用了现金券
                if($tmp_Result['receive_cashed_id']){
                    //将现金券使用id分割为数组
                    $receive_cashed_id_arr = explode(',',$tmp_Result['receive_cashed_id']);
                    $update_param['used_activity_id'] = $tmp_Result['dataid'];//使用产品标识id
                    $update_param['used_activity_name'] = $tmp_Result['chrtitle'];//使用产品名称
                    $update_param['used_time'] = date('Y-m-d H:i:s',time());//使用现金券时间
                    $update_param['used_order_id'] = $tmp_Result['id'];//使用订单id
                    $update_param['used_status'] = 5;//改为已使用
                    foreach ($receive_cashed_id_arr as $value){
                        db('cashed_card_receive')->where(['id'=>$value])->update($update_param);
                    }
                }
                //判断该订单是否是分销订单并且要产生对应的订单数据
                if($tmp_Result['source'] == '代言人订单' && $tmp_Result['spokesman_user_id3']){
                    $this->after_distribution($tmp_Result,$idsite);
                    //判断该订单是否是新用户下单，然后进行交易后锁客（2019年10月13）
                    if($tmp_Result['is_new_user'] == 1){
                        $this->lock_new_user_by_spokesman($tmp_Result,$member,$idsite);
                    }
                }

                if($bool)
                {
                    // 发送支付成功微信消息
                    template_bm($tmp_Result['id']);
                    //发短信
                    $replace = [];
                    //给客户和商务发短信通知  类型：2--下单成功
                    //Log::debug(date('Y-m-d H:i:s') . ' 收费产品付款成功,订单信息:$order=' . print_r($tmp_Result, true));
                   if(isset($arr['checkcode']) && $arr['checkcode'] != ''){
                       $msgtemplate=db('activity')->where(['idactivity'=>$tmp_Result['dataid']])->value('msgtemplate');
                       if($msgtemplate){
                           sendCodeMsg($idsite,2,$tmp_Result, $replace);
                       }else{
                           sysSendMsg($idsite, 2, $tmp_Result, $replace);
                       }
                   }else{
                       sysSendMsg($idsite, 2, $tmp_Result, $replace);
                   }


                }
            ob_start();
            echo "SUCCESS";
           // echo exit('<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>');
                //return sprintf("<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>");
            }

        else
        {
            echo 'fail';
        }
    }

    private  function after_distribution($tmp_Result,$idsite){

        //将用户得到的对应的佣金分配到该用户的冻结账户中
        if($tmp_Result['spokesman_user_id3']){
            $commission = $tmp_Result['sell_commission'];
            $user_id = $tmp_Result['spokesman_user_id3'];
            $member = db('member')->where(['idmember'=>$user_id,'idsite'=>$idsite])->find();
            //修改代言人的数据
            db('member')->where(['idmember'=>$user_id,'idsite'=>$idsite])->setInc(['total_commission'=>$member['total_commission'] + $commission,'freeze_commission'=>$member['freeze_commission'] + $commission,'spokesman_pay_num'=>$member['spokesman_pay_num'] + 1]);
            //修改产品代言表中的支付订单数
            db('spokesman_activity')->where(['activity_id'=>$tmp_Result['dataid'],'spokesman_user_id'=>$user_id,'site_id'=>$idsite])->setInc('spokesman_pay_num',1);
        }
        //如果上级代言人有
        if($tmp_Result['spokesman_user_id2']){
            $commission = $tmp_Result['bounty_commission2'];
            $user_id = $tmp_Result['spokesman_user_id2'];
            $member = db('member')->where(['idmember'=>$user_id,'idsite'=>$idsite])->find();
            //修改代言人的数据
            db('member')->where(['idmember'=>$user_id,'idsite'=>$idsite])->setInc(['total_commission'=>$member['total_commission'] + $commission,'freeze_commission'=>$member['freeze_commission'] + $commission,'spokesman_pay_num'=>$member['spokesman_pay_num'] + 1]);
            //修改产品代言表中的支付订单数
            db('spokesman_activity')->where(['activity_id'=>$tmp_Result['dataid'],'spokesman_user_id'=>$user_id,'site_id'=>$idsite])->setInc('spokesman_pay_num',1);
        }
        //如果上上级代言人有
        if($tmp_Result['spokesman_user_id1']){
            $commission = $tmp_Result['bounty_commission1'];
            $user_id = $tmp_Result['spokesman_user_id1'];
            $member = db('member')->where(['idmember'=>$user_id,'idsite'=>$idsite])->find();
            //修改代言人的数据
            db('member')->where(['idmember'=>$user_id,'idsite'=>$idsite])->setInc(['total_commission'=>$member['total_commission'] + $commission,'freeze_commission'=>$member['freeze_commission'] + $commission,'spokesman_pay_num'=>$member['spokesman_pay_num'] + 1]);
            //修改产品代言表中的支付订单数
            db('spokesman_activity')->where(['activity_id'=>$tmp_Result['dataid'],'spokesman_user_id'=>$user_id,'site_id'=>$idsite])->setInc('spokesman_pay_num',1);
        }
        //发送分销支付后的模板消息
        template_bm_commission(0,$tmp_Result['ordersn']);

    }

    /**
     * 交易后锁定用户
     * @param $order
     * @param $member
     * @param $siteid
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function lock_new_user_by_spokesman($order,$member,$siteid){
        //查询该机构的分销设置
        $spokesman_set_item = db('spokesman_set_item')->field('create_time',true)->where(['site_id'=>$siteid])->find();
        //代言人
        $lock_user_id = $order['spokesman_user_id3'];
        //查找出分享用户的信息
        $lock_user_info=db("member")->where(array('idmember'=>$lock_user_id,'idsite'=>$siteid))->find();
        //如果有设置过锁客，并且是下单锁,还有未锁客过的,有分享用户的信息，并且分享用户是代言人
        if($spokesman_set_item && $spokesman_set_item['lock_way'] ==  10 && $member['intlock'] != 1 && $lock_user_info && $lock_user_info['spokesman_grade'] != 0){
            //进行修改用户的锁客信息,改为已锁客
            $update_user_data = [
                'intlock'=>1,'lock_user_id'=>$lock_user_id,'lock_u_chrname'=>$lock_user_info['u_chrname'],
                'lock_u_chrtel'=>$lock_user_info['u_chrtel'],'lock_nick_name'=>$lock_user_info['nickname'],'lock_time'=>date('Y-m-d H:i:s',time())
            ];
            $update_user_bool = db("member")->where(array('idmember'=>$member['idmember'],'idsite'=>$siteid))->update($update_user_data);
            //如果锁客成功并且需要发送锁客通知
            if($update_user_bool && $spokesman_set_item['lock_notice'] == 1){
                template_tg_lock($lock_user_info,$member);
            }
        }
    }

    //查询编码写入订单
    public function useCodebase($id,$num,$idsite){
        $codes=db('activity_codedetail')->where(['state'=>2,'id_codebase'=>$id,'id_site'=>$idsite])
            ->field('id,code')->limit($num)->select();
        $tmp_str='';
        $tmp_str1='';
        $res=[];
        if($codes){
            foreach ($codes as $v){
                $tmp_str.=$v['code'].',';
                $tmp_str1.=$v['id'].',';
            }
            $res['code']=$tmp_str=trim($tmp_str,',');
            $res['id']=trim($tmp_str1,',');
            $res1=db('activity_codedetail')->where(['code'=>['in',$tmp_str],'id_codebase'=>$id])->update(['state'=>1]);
        }
        //dump($res);
        return $res;
    }
}