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

                $bool=db('order')->where(array('transaction_id'=>$out_trade_no))->update($arr);
                $tmp_Result['state']=4;
                $tmp_Result['intflag']=5;
                $tmp_Result['paytype1']=1;



                $idsite=$tmp_Result['idsite'];
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
                    $member = db('member')->where('idmember',$tmp_Result['fiduser'])->find();
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
                }

                if($bool)
                {
                    // 发送支付成功微信消息
                    template_bm($tmp_Result['id']);
                    //发短信
                    $replace = [];
                    //给客户和商务发短信通知  类型：2--下单成功
                    //Log::debug(date('Y-m-d H:i:s') . ' 收费产品付款成功,订单信息:$order=' . print_r($tmp_Result, true));
                    sysSendMsg($idsite, 2, $tmp_Result, $replace);

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
}