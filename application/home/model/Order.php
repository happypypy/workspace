<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/3
 * Time: 10:59
 */

namespace app\home\model;
use think\Model;

class Order extends Model {
    
    public function getOrder($ordersn)
    {
        return $this->where(['ordersn' => $ordersn])->find();
    }

    //修改订单数据
    public function updateOrder($result,$dataID,$orderID,$userID,$siteid,$receive_cashed_id='')
    {
        $row = db('activity')->where(["siteid" => $siteid, "idactivity" => $dataID])->find();
        if(!$row)
        {
            return false;
        }
        $user_row=db('member')->where(["idsite" => $siteid, "idmember" => $userID])->find();
        if(!$user_row)
        {
            return false;
        }

//        $result['ordersn']=$OrderSn; //订单号//
//        $result['transaction_id']=$transaction_id; //微信支付订单号//
        $result['fiduser']=$userID; //报名人id//
        $result['wechatid']=$user_row['openid']; //报名人微信id//
        $result['chrusername']=$user_row['chrname']; //报名人姓名//
        $result['idsite']=$row['siteid'];//站点id
        $result['chrtitle']=$row['chrtitle']; //活动名称//
        $result['ischarge']=$row['ischarge']; //是否收费 1:免费 2收费
        $result['chrimg']=$row['chrimg_m']; //活动图片//
        $result['chraddress']=$row['chraddressdetail']; //活动详细地点//
        $result['dtstart']=$row['dtstart']; //活动开始时间//
        $result['dtend']=$row['dtend']; //活动结束时间//
        //$result['signfromid']=''; //报名表单id//
        $result['pusilhid']=$row['publishid']; //发布人id//
        $result['pusilhname']=$row['publishname']; //发布人姓名//
        $result['marketid']=$row['intselmarket']; //商务id//
        $result['marketname']=$row['selmarketname']; //商务名称//
        $result['isrefund']= $row["isrefund"]; //是否允许退款 1：允许 2:不允许//
        $result['lock_stock_at'] = time();//锁定库存时间
        $result['stock_locked'] = 1;//库存锁定状态
        $result['dtcreatetime']=date("Y-m-d H:i:s"); //创建时间//
        $result['dataid']=$dataID; //活动id//

        $state = 1;
        $intflag = 2;
        if($row['ischarge'] == 2){
            $state = 12;
            $intflag = 1;
        }
        $result['state'] = $state; //订单状态:1.起草,待审核 2.已报名,审核不通过 3.已报名,已审批，4.已报名,已付款，5.已报名,退款中，6已报名，部分退款，7已报名，已退款，8.已报名,退款不通过，9.删除 10.已取消//
        $result['intflag']= $intflag; //1待下单的报名,2待审批的报名,3审查不通过的报名,4已取消的报名,5所有报名,6退款的报名//
    $flag=1;
        if($receive_cashed_id){
            //将现金券使用id分割为数组
            $receive_cashed_id_arr = explode(',',$receive_cashed_id);
            //封装要修改领取现金券信息的数据
            $update_param['used_status'] = 10;//使用状态改为冻结
            $update_param['freeze_time'] = date('Y-m-d H:i:s',time());//冻结时间
            foreach ($receive_cashed_id_arr as $value){
                //查询出其领取的现金券是否过期
                $receive_cashed_info = db('cashed_card_receive')->where(['id'=>$value])->where('cashed_validity_time','<',date('Y-m-d H:i:s',time()))->find();
                if($receive_cashed_info){
                    $errmsg='该订单存在过期的现金券';
                    $ajaxdate['flag']=2;
                    $ajaxdate['errmsg']=$errmsg;
                    return $ajaxdate;
                }else{
                    //执行修改
                    db('cashed_card_receive')->where(['id'=>$value])->update($update_param);
                }
            }

            //如果没有错误信息,并且支付金额为0的话
            if($flag==1 && $result['price'] == 0 && $result['ischarge']==2){
                //将现金券使用id分割为数组
                $receive_cashed_id_arr = explode(',',$receive_cashed_id);
                $result['state']=4;
                $result['intflag']=5;
                $result['paytype1']=1;
                //将订单变为已支付
                $update_param['used_activity_id'] = $row['idactivity'];//使用活动标识id
                $update_param['used_activity_name'] = $row['chrtitle'];//使用活动名称
                $update_param['used_time'] = date('Y-m-d H:i:s',time());//使用现金券时间
                $update_param['used_status'] = 5;//改为已使用
                foreach ($receive_cashed_id_arr as $value){
                    db('cashed_card_receive')->where(['id'=>$value])->update($update_param);
                }

            }
        }

        if($orderID>0)
        {
            $bool= db('order')->where(['id'=>$orderID])->update($result);
        }
        else
        {
            $orderID= db('order')->insert($result,false,true);
            $bool=true;
        }

        if($bool)
        {
          $info= db('order')->where(['id'=>$orderID])->find();
              if($info['state']==4) {
                  template_bm($info['id']);
                  //发短信
                  $replace = [];
                  //给客户和商务发短信通知  类型：2--下单成功
                  //Log::debug(date('Y-m-d H:i:s') . ' 收费产品付款成功,订单信息:$order=' . print_r($tmp_Result, true));
                  sysSendMsg($siteid, 2, $info, $replace);
                  if($info['source'] == '代言人订单' && $info['spokesman_user_id3']){
                      $this->after_distribution($info,$siteid);
                  }
              }

           if(array_key_exists('txtfield',$result) && array_key_exists('txtdata',$result))
           {
               update_member_info_by_relate_templates($userID,$row['siteid'], $result['txtfield'], $result['txtdata']);
           }
            return $info['ordersn'];//返回订单号
            //发送信息

        }else{
            file_put_contents( 'abc.txt',  print_r($result,true),FILE_APPEND);
        }
        return "";

    }
//本来是控制器的方法，但是由于模型里不能调用控制器方法，为了减少数据库的查询，复制一份放到模型（前台没有分销模型）
    private  function after_distribution($tmp_Result,$idsite){

        //将用户得到的对应的佣金分配到该用户的冻结账户中
        if($tmp_Result['spokesman_user_id3']){
            $commission = $tmp_Result['sell_commission'];
            $user_id = $tmp_Result['spokesman_user_id3'];
            $member = db('member')->where(['idmember'=>$user_id,'idsite'=>$idsite])->find();
            //修改代言人的数据
            db('member')->where(['idmember'=>$user_id,'idsite'=>$idsite])->setInc(['total_commission'=>$member['total_commission'] + $commission,'freeze_commission'=>$member['freeze_commission'] + $commission,'spokesman_pay_num'=>$member['spokesman_pay_num'] + 1]);
            //修改活动代言表中的支付订单数
            db('spokesman_activity')->where(['activity_id'=>$tmp_Result['dataid'],'spokesman_user_id'=>$user_id,'site_id'=>$idsite])->setInc('spokesman_pay_num',1);
        }
        //如果上级代言人有
        if($tmp_Result['spokesman_user_id2']){
            $commission = $tmp_Result['bounty_commission2'];
            $user_id = $tmp_Result['spokesman_user_id2'];
            $member = db('member')->where(['idmember'=>$user_id,'idsite'=>$idsite])->find();
            //修改代言人的数据
            db('member')->where(['idmember'=>$user_id,'idsite'=>$idsite])->setInc(['total_commission'=>$member['total_commission'] + $commission,'freeze_commission'=>$member['freeze_commission'] + $commission,'spokesman_pay_num'=>$member['spokesman_pay_num'] + 1]);
            //修改活动代言表中的支付订单数
            db('spokesman_activity')->where(['activity_id'=>$tmp_Result['dataid'],'spokesman_user_id'=>$user_id,'site_id'=>$idsite])->setInc('spokesman_pay_num',1);
        }
        //如果上上级代言人有
        if($tmp_Result['spokesman_user_id1']){
            $commission = $tmp_Result['bounty_commission1'];
            $user_id = $tmp_Result['spokesman_user_id1'];
            $member = db('member')->where(['idmember'=>$user_id,'idsite'=>$idsite])->find();
            //修改代言人的数据
            db('member')->where(['idmember'=>$user_id,'idsite'=>$idsite])->setInc(['total_commission'=>$member['total_commission'] + $commission,'freeze_commission'=>$member['freeze_commission'] + $commission,'spokesman_pay_num'=>$member['spokesman_pay_num'] + 1]);
            //修改活动代言表中的支付订单数
            db('spokesman_activity')->where(['activity_id'=>$tmp_Result['dataid'],'spokesman_user_id'=>$user_id,'site_id'=>$idsite])->setInc('spokesman_pay_num',1);
        }
        //发送分销支付后的模板消息
            template_bm_commission(0,$tmp_Result['ordersn']);

    }
}