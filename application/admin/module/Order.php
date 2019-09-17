<?php

namespace app\admin\module;
use think\Model;
use think\Page;
use think\Log;
use think\db\Query;
use app\common\model\Stock;

class Order extends Model
{

    //列表
    public function index($request)
    {
        $Search_arr = array();
        $search = array();
        if (empty($request['intflag']))
            $request['intflag'] = 2;

        if ($request['intflag'] != 5) {
            $Search_arr['intflag'] = $request['intflag'];
        }
        $Search_arr['idsite'] = session('idsite');
        $search['ordersn'] = '';
        if (isset($request['ordersn']) && $request['ordersn'] != '') {
            $Search_arr['ordersn'] = array('like', '%' . $request['ordersn'] . '%');
            $search['ordersn'] = $request['ordersn'];
        }
        $search['wechatid'] = '';
        if (isset($request['wechatid']) && $request['wechatid'] != '') {
            $Search_arr['wechatid'] = array('like', '%' . $request['wechatid'] . '%');
            $search['wechatid'] = $request['wechatid'];
        }
        $search['chrusername'] = '';
        if (isset($request['chrusername']) && $request['chrusername'] != '') {
            $Search_arr['chrusername'] = array('like', '%' . $request['chrusername'] . '%');
            $search['chrusername'] = $request['chrusername'];
        }
        $search['chrtitle'] = '';
        if (isset($request['chrtitle']) && $request['chrtitle'] != '') {
            $Search_arr['chrtitle'] = array('like', '%' . $request['chrtitle'] . '%');
            $search['chrtitle'] = $request['chrtitle'];
        }
        $search['state'] = '0';
        if (isset($request['state']) && $request['state'] != '' && $request['state'] != '0') {
            $Search_arr['state'] = $request['state'];
            $search['state'] = $request['state'];
        }
        $search['chrkey'] = '';
        if (isset($request['chrkey']) && $request['chrkey'] != '') {
            $Search_arr['txtdata'] = array('like', '%' . $request['chrkey'] . '%');
            $search['chrkey'] = $request['chrkey'];
        }
        $search['dtstart'] = "";
        $search['dtend'] = "";
        if (isset($request['dtstart']) && $request['dtstart'] != '' && isset($request['dtend']) && $request['dtend'] != '') {
            $Search_arr['dtcreatetime'] = array(array('>', $request['dtstart']), array('<', $request['dtend'] . " 23:59:59"),"and");
//            $Search_arr['dtcreatetime'] = array('<', $request['dtend'] . " 23:59:59");
            $search['dtstart'] = $request['dtstart'];
            $search['dtend'] = $request['dtend'];
        }

        $search['singntype'] = '';
        if(isset($request['singntype']) && (int)$request['singntype'] > 0){
            $search['singntype'] =  (int)$request['singntype'] ;
            $Search_arr['singntype'] =  (int)$request['singntype'] ;
        }

        $search['signusername'] = '';
        if(isset($request['signusername']) && $request['signusername'] != ''){
            $Search_arr['signusername'] =   array('like', '%' . trim($request['signusername']) . '%');
            $search['signusername'] = trim($request['signusername']);
        }
        $search['dtsigntimestart'] = '';
        $search['dtsigntimestartend'] = '';
        if (isset($request['dtsigntimestart']) && $request['dtsigntimestart'] != '' && isset($request['dtsigntimestartend']) && $request['dtsigntimestartend'] != '') {
            $Search_arr['dtsigntime'] = array(array('>', $request['dtsigntimestart']), array('<', $request['dtsigntimestartend'] . " 23:59:59"),"and");
            $search['dtsigntimestart'] = $request['dtsigntimestart'];
            $search['dtsigntimestartend'] = $request['dtsigntimestartend'];
        }
 

        if ($request['intflag'] == 4 ) {
            $Search_arr['state']= [ [ 'eq' , 10] , [ 'eq' ,11 ] ,  [ 'eq' , 13 ] , 'or' ];
        }
        elseif($request['intflag'] == 1){
             $Search_arr['state']= [ 'eq' , 12] ;
        }elseif($request['intflag'] == 2){
             $Search_arr['state']= [ 'eq' , 1] ;
        }elseif($request['intflag'] == 3){
             $Search_arr['state']= [ 'eq' , 2] ;
        }

        //如果有活动id,那么就是来自数据分析
        if(isset($request['product_id']) && $request['product_id'] != ''){
            $product_id = $request['product_id'];
            //如果活动id有逗号,那么就是其他(市),拼接条件
            if(strpos($product_id,',')) {
                $Search_arr['dataid'] = ['not in', $product_id];//"除去"
                $search['product_id'] = $product_id;
            }
        }
        //如果有用户下单id,那么就是来自数据分析
        if(isset($request['fiduser']) && $request['fiduser'] != ''){
            $Search_arr['fiduser'] = $search['fiduser'] = $request['fiduser'];
        }
        $search['origin'] = isset($request['origin']) && $request['origin'] != ''?$request['origin']:'';

        $refundcount = db('order')->where(array("state" => 5, "idsite" => session('idsite')))->count();
        $signupcount = db('order')->where(array("state" => 12, "idsite" => session('idsite')))->count();

        $count = db('order')->where($Search_arr)->count();
        $page = new Page($count, PAGE_SIZE);
        $data = db('order')->where($Search_arr)->order('id desc')->limit($page->firstRow . ',' . $page->pageSize)->select();

        foreach ($data as &$value){
            if((int)$value["ischarge"]==1){
                $value["price"] = "0.00";
            }
        }

        $arr = array();
        $arr['search'] = $search;
        $arr['refundcount'] = $refundcount;
        $arr['signupcount'] = $signupcount;
        $arr['pager'] = $page;
        $arr['data'] = $data;
        return $arr;
    }

    //签到
    public function sign($data)
    {
        $arr = [];
        $arr['issign'] = 1;
        $arr["singntype"]="3";
        $arr["issignremark"]=$data['remark'];
        $arr["signuserid"]=session('AccountID');
        $arr["signusername"]=session('UserName');
        $arr["dtsigntime"]=date("Y-m-d H:i:s",time());
        db('order')->where("id=" .$data["id"])->update($arr);
        return true;
    }

    //增改查页面处理
    public function deal($data)
    {
        if (array_key_exists('id', $data)) {
            $result = db('order')->where('id=:id and idsite=:idsite', ['id' => $data['id'], 'idsite' => session('idsite')])->find();
        } else {
            $result = [];
            $result['ordersn'] = ''; //订单号//
            $result['fiduser'] = ''; //报名人id//
            $result['wechatid'] = ''; //报名人微信id//
            $result['chrusername'] = ''; //报名人姓名//
            $result['dataid'] = ''; //活动id//
            $result['chrtitle'] = ''; //活动名称//
            $result['chraddress'] = ''; //活动详细地点//
            $result['dtstart'] = ''; //活动开始时间//
            $result['dtend'] = ''; //活动结束时间//
            $result['signfromid'] = ''; //报名表单id//
            $result['pusilhid'] = ''; //发布人id//
            $result['pusilhname'] = ''; //发布人姓名//
            $result['marketid'] = ''; //商务id//
            $result['marketname'] = ''; //商务名称//
            $result['state'] = ''; //订单状态:1.起草,待审核 2.已报名,审核不通过 3.已报名,已审批，4.已报名,已付款，5.已报名,退款中，6已报名，部分退款，7已报名，已退款，8.已报名,退款不通过，9.删除 10.已取消//
            $result['paytype'] = ''; //收款方式 1。平台收款 2.商家自行收款//
            $result['paytype1'] = ''; //支付方式 1：微信支付 2：优惠卷支付 3：积分支付 4：线下支付//
            $result['payid'] = ''; //购买的套餐id//
            $result['paynum'] = ''; //购买数量//
            $result['payname'] = ''; //购买的套餐名称//
            $result['price'] = ''; //订单总价格//
            $result['price1'] = ''; //活动代理价格//
            $result['price2'] = ''; //积分抵扣的金额//
            $result['prepay_id'] = ''; //微信下单订单号//
            $result['transaction_id'] = ''; ////
            $result['dtpaytime'] = ''; //支付时间//
            $result['dtrefundtime'] = ''; //用户发起退款的时间//
            $result['refundsn'] = ''; //商户请求退款的单号//
            $result['wxrefundsn'] = ''; //微信完成退款的单号//
            $result['dtwxrefundtime'] = ''; //微信完成退款的时间//
            $result['isrefundpart'] = ''; //是否是部分退款 0：不是 1：是//
            $result['refundprice'] = ''; //退款金额//
            $result['refundremark'] = ''; //退款原因//
            $result['refundpic'] = ''; //退款上传图片//
            $result['refundremark1'] = ''; //后台确认退款时的备注信息//
            $result['refundsn2'] = ''; //二次商户请求退款的单号//
            $result['refundmsg2'] = ''; //二次退款原因//
            $result['refundprice2'] = ''; //二次退款金额//
            $result['dtwxrefundtime2'] = ''; //二次微信完成退款的时间//
            $result['wxrefundsn2'] = ''; //二次微信完成退款的单//
            $result['cancelremark'] = ''; //订单取消原因//
            $result['dtcreatetime'] = ''; //创建时间//
            $result['issign'] = ''; //是否已签到 1：是//
            $result['signuserid'] = ''; //签到验证人id//
            $result['signusername'] = ''; //签到验证人昵称//
            $result['dtsigntime'] = ''; //签到时间//
            $result['issettlement'] = ''; //是否已结算 1:是//
            $result['isrefund'] = ''; //是否允许退款 1：允许 2:不允许//
            $result['couriercode'] = ''; //物流编号//
            $result['couriername'] = ''; //物流名称//
            $result['couriersn'] = ''; //物流单号//
            $result['txtdata'] = ''; //模版数据，多个字段用“☆”分开//
            $result['group_buy_order_id'] = null; //拼团订单id//
        }
        $result['action'] = $data['action'];
        return $result;
    }

    //添加，修改提交地址
    public function PostData($data)
    {
        $tmpArr = array();
        if (isset($data['ordersn'])) $tmpArr['ordersn'] = trim($data['ordersn']);
        if (isset($data['fiduser'])) $tmpArr['fiduser'] = trim($data['fiduser']);
        if (isset($data['wechatid'])) $tmpArr['wechatid'] = trim($data['wechatid']);
        if (isset($data['chrusername'])) $tmpArr['chrusername'] = trim($data['chrusername']);
        if (isset($data['dataid'])) $tmpArr['dataid'] = trim($data['dataid']);
        if (isset($data['chrtitle'])) $tmpArr['chrtitle'] = trim($data['chrtitle']);
        if (isset($data['chraddress'])) $tmpArr['chraddress'] = trim($data['chraddress']);
        if (isset($data['dtstart'])) $tmpArr['dtstart'] = trim($data['dtstart']);
        if (isset($data['dtend'])) $tmpArr['dtend'] = trim($data['dtend']);
        if (isset($data['signfromid'])) $tmpArr['signfromid'] = trim($data['signfromid']);
        if (isset($data['pusilhid'])) $tmpArr['pusilhid'] = trim($data['pusilhid']);
        if (isset($data['pusilhname'])) $tmpArr['pusilhname'] = trim($data['pusilhname']);
        if (isset($data['marketid'])) $tmpArr['marketid'] = trim($data['marketid']);
        if (isset($data['marketname'])) $tmpArr['marketname'] = trim($data['marketname']);
        if (isset($data['state'])) $tmpArr['state'] = trim($data['state']);
        if (isset($data['paytype'])) $tmpArr['paytype'] = trim($data['paytype']);
        if (isset($data['paytype1'])) $tmpArr['paytype1'] = trim($data['paytype1']);
        if (isset($data['payid'])) $tmpArr['payid'] = trim($data['payid']);
        if (isset($data['paynum'])) $tmpArr['paynum'] = trim($data['paynum']);
        if (isset($data['payname'])) $tmpArr['payname'] = trim($data['payname']);
        if (isset($data['price'])) $tmpArr['price'] = trim($data['price']);
        if (isset($data['price1'])) $tmpArr['price1'] = trim($data['price1']);
        if (isset($data['price2'])) $tmpArr['price2'] = trim($data['price2']);
        if (isset($data['prepay_id'])) $tmpArr['prepay_id'] = trim($data['prepay_id']);
        if (isset($data['transaction_id'])) $tmpArr['transaction_id'] = trim($data['transaction_id']);
        if (isset($data['dtpaytime'])) $tmpArr['dtpaytime'] = trim($data['dtpaytime']);
        if (isset($data['dtrefundtime'])) $tmpArr['dtrefundtime'] = trim($data['dtrefundtime']);
        if (isset($data['refundsn'])) $tmpArr['refundsn'] = trim($data['refundsn']);
        if (isset($data['wxrefundsn'])) $tmpArr['wxrefundsn'] = trim($data['wxrefundsn']);
        if (isset($data['dtwxrefundtime'])) $tmpArr['dtwxrefundtime'] = trim($data['dtwxrefundtime']);
        if (isset($data['isrefundpart'])) $tmpArr['isrefundpart'] = trim($data['isrefundpart']);
        if (isset($data['refundprice'])) $tmpArr['refundprice'] = trim($data['refundprice']);
        if (isset($data['refundremark'])) $tmpArr['refundremark'] = trim($data['refundremark']);
        if (isset($data['refundpic'])) $tmpArr['refundpic'] = trim($data['refundpic']);
        if (isset($data['refundremark1'])) $tmpArr['refundremark1'] = trim($data['refundremark1']);
        if (isset($data['refundsn2'])) $tmpArr['refundsn2'] = trim($data['refundsn2']);
        if (isset($data['refundmsg2'])) $tmpArr['refundmsg2'] = trim($data['refundmsg2']);
        if (isset($data['refundprice2'])) $tmpArr['refundprice2'] = trim($data['refundprice2']);
        if (isset($data['dtwxrefundtime2'])) $tmpArr['dtwxrefundtime2'] = trim($data['dtwxrefundtime2']);
        if (isset($data['wxrefundsn2'])) $tmpArr['wxrefundsn2'] = trim($data['wxrefundsn2']);
        if (isset($data['cancelremark'])) $tmpArr['cancelremark'] = trim($data['cancelremark']);
        if (isset($data['dtcreatetime'])) $tmpArr['dtcreatetime'] = trim($data['dtcreatetime']);
        if (isset($data['issign'])) $tmpArr['issign'] = trim($data['issign']);
        if (isset($data['signuserid'])) $tmpArr['signuserid'] = trim($data['signuserid']);
        if (isset($data['signusername'])) $tmpArr['signusername'] = trim($data['signusername']);
        if (isset($data['dtsigntime'])) $tmpArr['dtsigntime'] = trim($data['dtsigntime']);
        if (isset($data['issettlement'])) $tmpArr['issettlement'] = trim($data['issettlement']);
        if (isset($data['isrefund'])) $tmpArr['isrefund'] = trim($data['isrefund']);
        if (isset($data['couriercode'])) $tmpArr['couriercode'] = trim($data['couriercode']);
        if (isset($data['couriername'])) $tmpArr['couriername'] = trim($data['couriername']);
        if (isset($data['couriersn'])) $tmpArr['couriersn'] = trim($data['couriersn']);
        if (isset($data['txtdata'])) $tmpArr['txtdata'] = trim($data['txtdata']);
        if (isset($data['checkinfo'])) $tmpArr['checkinfo'] = trim($data['checkinfo']);

        if (isset($data['intflag'])) {
            $tmpArr['intflag'] = trim($data['intflag']);
            if ($data['intflag'] == 3) {        //审核不通过
                // 库存锁定状态改变
                $tmpArr['stock_locked'] = 0;
                $tmpArr['state'] = '2';
                $tmpArr['checktime'] =time();
                $tmpArr['checkname'] = session('UserName');
                $tmpArr['checkuserid'] = session('AccountID');
            } elseif ($data['intflag'] == 5) {  //审核通过
                $tmpArr['checktime'] =time();
                $tmpArr['checkname'] = session('UserName');
                $tmpArr['checkuserid'] = session('AccountID');
                $tmpArr['state'] = '3';
            } elseif ($data['intflag'] == 4) {  //取消报名
                // 库存锁定状态改变
                $tmpArr['stock_locked'] = 0;
                $tmpArr['state'] = '10';
            }
        }

        // 审核免费活动
        Log::debug(date('Y-m-d :H:i:s') . ' 审核免费活动，数据：' . print_r($data, true));
        if ($data['action'] == 'add') {
            $bool = db('order')->insert($tmpArr);
        } else {        //修改订单

            try
            {
                $query = new Query;
                $query->startTrans();
                $order = db('order')->where(['id' => $data['id'], 'idsite' => session('idsite')])->find();
                $bool = db('order')->where('id=:id and idsite=:idsite', ['id' => $data['id'], 'idsite' => session('idsite')])->update($tmpArr);
                // 审核不通过||取消报名，释放锁定的库存
                if(in_array($data['intflag'], [3, 4]) && $order['stock_locked'] == 1)
                {
                    changeStock($order['package_id'], $order['paynum'], false);
                }
                
                if(!array_key_exists('fiduser',$data))
                {
                    $tmp_Result= db('order')->where('id=:id and idsite=:idsite', ['id' => $data['id'], 'idsite' => session('idsite')])->find();
                    if($tmp_Result)
                    {
                        $data['fiduser']=$tmp_Result['fiduser'];
                    }
                }
                template_bm($data['id']);
                $query->commit();
            } catch(Exception $e)
            {
                Log::error('订单审核失败，data：' . print_r($data, true));
                $query->rollBack();
                return false;
            }
        }
        SetUserPayCount($data['fiduser']);
        return $bool;
    }



    public  function getOrderNum($dataid)
    {
        return db('order')->where(array('dataid'=>$dataid,'idsite'=>session('idsite')))->count();
    }

    
    //$ischarge 是否付费，默认免费
    /**
     * 添加订单
     * @param    integer                   $siteid    站点id
     * @param    integer                   $userID    用户id
     * @param    string                    $wxID      用户微信openid
     * @param    string                    $UserName  用户名
     * @param    integer                   $dataID    关联活动id
     * @param    integer                   $package_id套餐id
     * @param    string                    $txtfield  报名时填写的表单数据（键和类型）
     * @param    string                    $txtdata   报名时填写的表单数据（值）
     * @param    string                    $payname   购买的套餐|活动名称
     * @param    decimal                   $price     价格
     * @param    integer                   $payCount  购买数量
     * @param    string                    $OrderSn   订单号
     * @param    string                    $txtfield1 [description]
     * @param    string                    $txtdata1  [description]
     * @param    integer                   $ischarge  是否付费，1--免费，2--收费
     */
    public function addOrder($siteid,$userID,$wxID,$UserName,$dataID, $package_id, $txtfield,$txtdata,$payname,$price,$payCount=1,$OrderSn='',$cashed_amount, $receive_cashed_id,$share_plan_id,$txtfield1='',$txtdata1='',$ischarge=1, $transaction_id = '', $group_buy_order_id = null,$spokesman_order)
    {
        $row = db('activity')->where(["siteid" => $siteid, "idactivity" => $dataID])->find();
        if(!$row)
        {
            return false;
        }

        if(!empty($group_buy_order_id))
        {
            $groupBuyOrder = db('group_buy_order')->find($group_buy_order_id);
            if(!$groupBuyOrder)
            {
                return false;
            }
            $groupBuy = db('group_buy')->find($groupBuyOrder['group_buy_id']);
            $row['isrefund'] = $groupBuy['allow_refund'];
        }

        $user_row=db('member')->where(["idsite" => $siteid, "idmember" => $userID])->find();
        if(!$user_row)
        {
            return false;
        }
        if($OrderSn=='')
        {
            $OrderSn=getOrderSn();
        }
        $result = [];
        $result['ordersn']=$OrderSn; //订单号//
        $result['transaction_id']=$transaction_id; //微信支付订单号//
        $result['fiduser']=$userID; //报名人id//
        $result['wechatid']=$wxID; //报名人微信id//
        $result['chrusername']=$UserName; //报名人姓名//
        $result['dataid']=$dataID; //活动id//
        $result['package_id']=$package_id; //套餐id//
        $result['chrtitle']=$row['chrtitle']; //活动名称//
        $result['ischarge']=$row['ischarge']; //是否收费 1:免费 2收费
        $result['chrimg']=$row['chrimg']; //活动图片//
        $result['chraddress']=$row['chraddressdetail']; //活动详细地点//
        $result['dtstart']=$row['dtstart']; //活动开始时间//
        $result['dtend']=$row['dtend']; //活动结束时间//
        //$result['signfromid']=''; //报名表单id//
        $result['pusilhid']=$row['publishid']; //发布人id//
        $result['pusilhname']=$row['publishname']; //发布人姓名//
        $result['marketid']=$row['intselmarket']; //商务id//
        $result['marketname']=$row['selmarketname']; //商务名称//
        $result['group_buy_order_id']=$group_buy_order_id; //关联拼团订单id//
        //免费活动默认状态为已报名,待审核，付费活动默认初始状态为已报名，待付款
        $state = 1;
        $intflag = 2;
        if($ischarge == 2){
            $state = 12;
            $intflag = 1;
        }
        $result['state'] = $state; //订单状态:1.起草,待审核 2.已报名,审核不通过 3.已报名,已审批，4.已报名,已付款，5.已报名,退款中，6已报名，部分退款，7已报名，已退款，8.已报名,退款不通过，9.删除 10.已取消//
        //$result['paytype']=$row['']; //收款方式 1。平台收款 2.商家自行收款//
        //$result['paytype1']=$row['']; //支付方式 1：微信支付 2：优惠卷支付 3：积分支付 4：线下支付//
        //$result['payid']=$row['']; //购买的套餐id//
        $result['paynum']=$payCount; //购买数量//
        $result['payname']=$payname; //购买的套餐名称//
        $result['price']=$price; //订单总价格//
        //$result['price1']=$row['']; //活动代理价格//
        //$result['price2']=$row['']; //积分抵扣的金额//
        //$result['prepay_id']=$row['']; //微信下单订单号//
        //$result['transaction_id']=$row['']; ////
        //$result['dtpaytime']=$row['']; //支付时间//
        //$result['dtrefundtime']=$row['']; //用户发起退款的时间//
        //$result['refundsn']=$row['']; //商户请求退款的单号//
        //$result['wxrefundsn']=$row['']; //微信完成退款的单号//
        //$result['dtwxrefundtime']=$row['']; //微信完成退款的时间//
        //$result['isrefundpart']=$row['']; //是否是部分退款 0：不是 1：是//
        //$result['refundprice']=$row['']; //退款金额//
        //$result['refundremark']=$row['']; //退款原因//
        //$result['refundpic']=$row['']; //退款上传图片//
        //$result['refundremark1']=$row['']; //后台确认退款时的备注信息//
        //$result['refundsn2']=$row['']; //二次商户请求退款的单号//
        //$result['refundmsg2']=$row['']; //二次退款原因//
        //$result['refundprice2']=$row['']; //二次退款金额//
        //$result['dtwxrefundtime2']=$row['']; //二次微信完成退款的时间//
        //$result['wxrefundsn2']=$row['']; //二次微信完成退款的单//
        //$result['cancelremark']=$row['']; //订单取消原因//
        $result['dtcreatetime']=date("Y-m-d H:i:s"); //创建时间//
        //$result['issign']=$row['']; //是否已签到 1：是//
        //$result['signuserid']=$row['']; //签到验证人id//
        //$result['signusername']=$row['']; //签到验证人昵称//
        //$result['dtsigntime']=$row['']; //签到时间//
        $result['issettlement']=0; //是否已结算 1:是//
        $result['isrefund']= $row["isrefund"]; //是否允许退款 1：允许 2:不允许//
        //$result['couriercode']=''; //物流编号//
        //$result['couriername']=''; //物流名称//
        //$result['couriersn']=''; //物流单号//
        $result['txtfield']=$txtfield; //模版字段，多个字段用“☆”分开//
        $result['txtdata']=$txtdata; //模版数据，多个字段用“☆”分开//
        $result['txtfield1']=$txtfield1; //子表单字段，多个字段用“☆”分开//
        $result['txtdata1']=$txtdata1; //子表单数据，多个字段用“☆”分开，多数据用“§”分开//
        $result['intflag']= $intflag; //1待下单的报名,2待审批的报名,3审查不通过的报名,4已取消的报名,5所有报名,6退款的报名//
        $result['idsite']=$row['siteid'];//站点id
        $result['lock_stock_at'] = time();//锁定库存时间
        $result['stock_locked'] = 1;//库存锁定状态
        $result['cashed_amount'] = $cashed_amount;//抵用现金券金额
        $result['receive_cashed_id'] = $receive_cashed_id;//使用的领取现金券id,多个券用逗号隔开
        $result['share_plan_id'] = $share_plan_id;//分享现金券的计划id
        //如果有分销数据
        if($spokesman_order && $price > 0){
            $spokesman_order['source'] = '代言人订单';
            $result = array_merge($result,$spokesman_order);
        }
        $bool= db('order')->insert($result);
        if($bool)
        {
            
            $info= db('order')->where(array('ordersn'=>$OrderSn))->find();
            if($info)
            {
               // template_bm($info['id']);
            }
            update_member_info_by_relate_templates($userID,$row['siteid'],$txtfield,$txtdata);

            return $OrderSn;
            //发送信息

        }else{
            file_put_contents( 'abc.txt',  print_r($result,true),FILE_APPEND);
        }
         return "";

    }

    //修改订单数据
    public function updateOrder($siteid,$userID,$wxID,$UserName,$dataID, $package_id, $txtfield,$txtdata,$payname,$price,$payCount=1,$OrderSn='',$cashed_amount, $receive_cashed_id,$share_plan_id,$txtfield1='',$txtdata1='',$ischarge=1, $transaction_id = '', $group_buy_order_id = '',$order_id)
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
        if($OrderSn=='')
        {
            $OrderSn=getOrderSn();
        }
        $result = [];
//        $result['ordersn']=$OrderSn; //订单号//
//        $result['transaction_id']=$transaction_id; //微信支付订单号//
        $result['fiduser']=$userID; //报名人id//
        $result['wechatid']=$wxID; //报名人微信id//
        $result['chrusername']=$UserName; //报名人姓名//
        $result['dataid']=$dataID; //活动id//
        $result['package_id']=$package_id; //套餐id//
        $result['chrtitle']=$row['chrtitle']; //活动名称//
        $result['ischarge']=$row['ischarge']; //是否收费 1:免费 2收费
        $result['chrimg']=$row['chrimg']; //活动图片//
        $result['chraddress']=$row['chraddressdetail']; //活动详细地点//
        $result['dtstart']=$row['dtstart']; //活动开始时间//
        $result['dtend']=$row['dtend']; //活动结束时间//
        //$result['signfromid']=''; //报名表单id//
        $result['pusilhid']=$row['publishid']; //发布人id//
        $result['pusilhname']=$row['publishname']; //发布人姓名//
        $result['marketid']=$row['intselmarket']; //商务id//
        $result['marketname']=$row['selmarketname']; //商务名称//
        $result['group_buy_order_id']=$group_buy_order_id; //关联拼团订单id//
        //免费活动默认状态为已报名,待审核，付费活动默认初始状态为已报名，待付款
        $state = 1;
        $intflag = 2;
        if($ischarge == 2){
            $state = 12;
            $intflag = 1;
        }
        $result['state'] = $state; //订单状态:1.起草,待审核 2.已报名,审核不通过 3.已报名,已审批，4.已报名,已付款，5.已报名,退款中，6已报名，部分退款，7已报名，已退款，8.已报名,退款不通过，9.删除 10.已取消//
        //$result['paytype']=$row['']; //收款方式 1。平台收款 2.商家自行收款//
        //$result['paytype1']=$row['']; //支付方式 1：微信支付 2：优惠卷支付 3：积分支付 4：线下支付//
        //$result['payid']=$row['']; //购买的套餐id//
        $result['paynum']=$payCount; //购买数量//
        $result['payname']=$payname; //购买的套餐名称//
        $result['price']=$price; //订单总价格//
        //$result['price1']=$row['']; //活动代理价格//
        //$result['price2']=$row['']; //积分抵扣的金额//
        //$result['prepay_id']=$row['']; //微信下单订单号//
        $result['transaction_id']=$transaction_id; ////
        //$result['dtpaytime']=$row['']; //支付时间//
        //$result['dtrefundtime']=$row['']; //用户发起退款的时间//
        //$result['refundsn']=$row['']; //商户请求退款的单号//
        //$result['wxrefundsn']=$row['']; //微信完成退款的单号//
        //$result['dtwxrefundtime']=$row['']; //微信完成退款的时间//
        //$result['isrefundpart']=$row['']; //是否是部分退款 0：不是 1：是//
        //$result['refundprice']=$row['']; //退款金额//
        //$result['refundremark']=$row['']; //退款原因//
        //$result['refundpic']=$row['']; //退款上传图片//
        //$result['refundremark1']=$row['']; //后台确认退款时的备注信息//
        //$result['refundsn2']=$row['']; //二次商户请求退款的单号//
        //$result['refundmsg2']=$row['']; //二次退款原因//
        //$result['refundprice2']=$row['']; //二次退款金额//
        //$result['dtwxrefundtime2']=$row['']; //二次微信完成退款的时间//
        //$result['wxrefundsn2']=$row['']; //二次微信完成退款的单//
        //$result['cancelremark']=$row['']; //订单取消原因//
        $result['dtcreatetime']=date("Y-m-d H:i:s"); //创建时间//
        //$result['issign']=$row['']; //是否已签到 1：是//
        //$result['signuserid']=$row['']; //签到验证人id//
        //$result['signusername']=$row['']; //签到验证人昵称//
        //$result['dtsigntime']=$row['']; //签到时间//
        $result['issettlement']=0; //是否已结算 1:是//
        $result['isrefund']= $row["isrefund"]; //是否允许退款 1：允许 2:不允许//
        //$result['couriercode']=''; //物流编号//
        //$result['couriername']=''; //物流名称//
        //$result['couriersn']=''; //物流单号//
        $result['txtfield']=$txtfield; //模版字段，多个字段用“☆”分开//
        $result['txtdata']=$txtdata; //模版数据，多个字段用“☆”分开//
        $result['txtfield1']=$txtfield1; //子表单字段，多个字段用“☆”分开//
        $result['txtdata1']=$txtdata1; //子表单数据，多个字段用“☆”分开，多数据用“§”分开//
        $result['intflag']= $intflag; //1待下单的报名,2待审批的报名,3审查不通过的报名,4已取消的报名,5所有报名,6退款的报名//
        $result['idsite']=$row['siteid'];//站点id
        $result['lock_stock_at'] = time();//锁定库存时间
        $result['stock_locked'] = 1;//库存锁定状态
        $result['cashed_amount'] = $cashed_amount;//抵用现金券金额
        $result['receive_cashed_id'] = $receive_cashed_id;//使用的领取现金券id,多个券用逗号隔开
        $result['share_plan_id'] = $share_plan_id;//分享现金券的计划id

        $bool= db('order')->where(['id'=>$order_id])->update($result);
        if($bool)
        {
            $info= db('order')->where(['id'=>$order_id])->find();

            if($info)
            {
               // template_bm($info['id']);
            }

           update_member_info_by_relate_templates($userID,$row['siteid'],$txtfield,$txtdata);
            return $info['ordersn'];//返回订单号
            //发送信息

        }else{
            file_put_contents( 'abc.txt',  print_r($result,true),FILE_APPEND);
        }
        return "";

    }

    //删除
    public function del($data){
        if(isset($data['id'])==false)
            return false;

        if(strstr($data['id'],','))
            $bool = db('order')->where("idsite=".session('idsite'))->where('id','in',explode(',',$data['id']))->delete();
        else
            $bool = db('order')->where("idsite=".session('idsite'))->where('id=:id',['id'=>$data['id']])->delete();

        return $bool;
    }

    /**
     * 会员报表列表
     */
    public function order_report_list($data)
    {
        if (!isset($data["time_range"])) {
            return false;
        }
        $order = db('order');
        $start_date = $end_date = "";
        switch ($data["time_range"]) {
            case "last_week":
                $order->whereTime('dtcreatetime', 'last week');
                break;
            case "this_week":
                $order->whereTime('dtcreatetime', 'week');
                break;
            case "last_month":
                $order->whereTime('dtcreatetime', 'last month');
                break;
            case "this_month":
                $order->whereTime('dtcreatetime', 'month');
                break;
            case "custom":
                if(empty($start_date) || empty($end_date))
                {
                    return false;
                }
                $order->where('dtcreatetime', 'between', [$data['begintime'], $data['endtime'] . ' 23:59:59']);
                break;
        }


        $join = [
            ['cms_package p', 'o.package_id = p.package_id', 'LEFT'],
            ['cms_activity a', 'a.idactivity = p.activity_id', 'LEFT'],
        ];

        $order->where('idsite', session('idsite') ? : 0)
            ->where('o.state', 'in', [3, 4, 5, 6, 7, 8]); // 3.已报名,已审核 4.已报名,已支付 5已报名 退款中 6已部分退款,继续服务 7已退款,继续服务 8已报名退款不通过

        $orderList = $order->alias('o')
            ->join($join)
            ->field(
                [
                    // 活动字段
                    'a.idactivity',
                    'a.selcontent',
                    'a.dtpublishtime',
                    'a.dtsignetime',
                    'a.dtstart',
                    'a.dtend',

                    // 套餐字段
                    'p.cost_price',
                    'p.member_price',
                    'p.package_sum',
                    'p.sold',
                    'p.activity_id',

                    'sum(o.price - o.refundprice - o.refundprice2 - p.cost_price) profit', //利润

                    //订单字段
                    'o.chrtitle',
                    'o.payname',
                    'o.dataid',
                    'o.package_id',
                    'sum(o.price) as pay_price',
                    'o.price1',
                    'concat(p.keyword1, p.keyword2, "(", p.member_price, ")") payname',
                    'sum( o.price - o.refundprice - o.refundprice2 - p.cost_price ) profit', //利润
                    'sum( o.price - o.refundprice - o.refundprice2 ) as pay_price',    //支付金额减去退款金额===>销售金额  TODO 目前最多只能记录两次退款金额
                    'count( 1 ) order_num',
                    'sum(o.paynum) pay_num',
                ]
            )
            ->group('p.package_id')
            ->select();

        $totalOrderNum = $totalPayNum = $totalPayPrice = $totalProfit = $totalStorage = 0;
        $storage = 0;
        foreach ($orderList as $key => $order)
        {
            $storage = Stock::getStock($order);
            switch ($storage)
            {
                case 0:
                    $orderList[$key]['storage'] = '0（售完）';
                    break;
                case INF:
                    $orderList[$key]['storage'] = '充足';
                    break;
                default:
                    $orderList[$key]['storage'] = $storage;
                    break;
            }

            $totalOrderNum = round($totalOrderNum + $order["order_num"]);
            $totalPayNum = round($totalPayNum + $order["pay_num"]);
            $totalPayPrice = round($totalPayPrice + $order["pay_price"], 2);
            $totalProfit = round($totalProfit +  $order['profit'], 2);
            $totalStorage = round($totalStorage + $storage);
        }

        $orderList['汇总数据'] = [
                'chrtitle' => '汇总数据',
                'payname' => '',
                'order_num' => $totalOrderNum,
                'pay_num' => $totalPayNum,
                'pay_price' => number_format($totalPayPrice, 2, '.', ''),
                'profit' => number_format($totalProfit, 2, '.', ''),
                'storage' => $totalStorage == INF ? '充足' : $storage
            ];

        return $orderList;
    }



    /**
     * 会员报表列表
     */
    public function activity_order_report_list($data)
    {
        if (!isset($data["time_range"])) {
            return false;
        }
        $start_date = $end_date = "";
        switch ($data["time_range"]) {
            case "last_week":

                $time = time();
                $last_monday = date('Y-m-d', strtotime('-1 monday', $time));
                $last_sunday = date('Y-m-d', strtotime('-1 sunday', $time));
                if ($last_monday > $last_sunday) {
                    $last_monday = date('Y-m-d', strtotime('-2 monday', $time));
                }
                $start_date = $last_monday;
                $end_date = $last_sunday;
                break;
            case "this_week":
                $start_date = date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600));
                $end_date = date('Y-m-d', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600));
                break;
            case "last_month":
                $start_date = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m', time()) . '-01 00:00:00')));
                $end_date = date('Y-m-d', strtotime(date('Y-m', time()) . '-01 00:00:00') - 86400);
                break;
            case "this_month":
                $start_date = date('Y-m-d', strtotime(date('Y-m', time()) . '-01 00:00:00'));
                $end_date = date('Y-m-d', strtotime(date('Y-m', time()) . '-' . date('t', time()) . ' 00:00:00'));
                break;
            case "custom":
                $start_date = $data["begintime"];
                $end_date = $data["endtime"];
                break;
        }
        if (empty($start_date) || empty($end_date)) {
            return false;
        }

        $idsite = session('idsite')?:0;
        $whereArr = array();
        // $whereArr["intflag"] = 2;
        $whereArr["siteid"] = $idsite;
        $map2 = array();
        switch ($data["time_condition"]){
            case "publish":
                $whereArr['a.dtpublishtime'] = array(array('>=', $start_date), array('<=', $end_date . " 23:59:59"),"and");
                break;
            case "dttime":
                $whereArr['a.dtstart']  =  array('<=', $end_date);
                $whereArr['a.dtend']  =  array('>=', $start_date);
                break;
            case "dtsignetime":
                $whereArr['a.dtsignetime'] = array(array('>=', $start_date), array('<=', $end_date . " 23:59:59"),"and");
                break;
        }

        // $activity_list = db("activity")->where($whereArr)->where(
        //     function ($q) use($map2) {
        //     $q->whereOr($map2);
        // })->field("idactivity,selcontent,dtpublishtime,dtsignetime,dtstart,dtend")->select();

        $join = [
            ['cms_activity a', 'a.idactivity = p.activity_id', 'LEFT'],
            ['cms_order o', 'o.package_id = p.package_id', 'LEFT'],
        ];

        $pacakgeList = db('package')
            ->alias('p')
            ->join($join)
            ->where($whereArr)
            // ->where('o.state', 'in', [3,4,5,6,7]) // 3.已报名,已审核 4.已报名,已支付 5已报名 退款中 6已部分退款,继续服务 7已退款,继续服务 
            ->field(
                [
                    // 活动字段
                    'a.idactivity',
                    'a.selcontent',
                    'a.dtpublishtime',
                    'a.dtsignetime',
                    'a.dtstart',
                    'a.dtend',

                    // 套餐字段
                    'p.cost_price',
                    'p.member_price',
                    'p.package_sum',
                    'p.sold',
                    'concat(p.keyword1, p.keyword2, "(", p.member_price, ")") payname',
                    'p.activity_id',

                    'sum(if(o.state in (3,4,5,6,7,8), o.price - o.refundprice - o.refundprice2 - p.cost_price, 0)) profit', //利润

                    //订单字段
                    'o.chrtitle',
                    // 'o.payname',
                    'o.dataid',
                    'o.package_id',
                    'sum( if(o.state in (3,4,5,6,7,8), o.price - o.refundprice - o.refundprice2, 0)) as pay_price',    //支付金额减去退款金额===>销售金额  TODO 目前最多只能记录两次退款金额
                    'o.price1',
                    'count(if(o.state in (3,4,5,6,7,8), 1, null)) order_num',
                    'sum(if(o.state in (3,4,5,6,7,8), o.paynum, 0)) pay_num',
                ]
            )
            ->group('p.package_id')
            // ->fetchSql(true)
            ->select();
// print_r($pacakgeList);die;
        $totalOrderNum = $totalPayNum = $totalPayPrice = $totalProfit = $totalStorage = 0;
        foreach ($pacakgeList as $key => $package)
        {
            $storage = Stock::getStock($package);
            switch ($storage)
            {
                case 0:
                    $pacakgeList[$key]['storage'] = '0（售完）';
                    break;
                case INF:
                    $pacakgeList[$key]['storage'] = '充足';
                    break;
                default:
                    $pacakgeList[$key]['storage'] = $storage;
                    break;
            }

            $totalOrderNum = round($totalOrderNum + $package["order_num"]);
            $totalPayNum = round($totalPayNum + $package["pay_num"]);
            $totalPayPrice = round($totalPayPrice + $package["pay_price"], 2);
            $totalProfit = round($totalProfit +  $package['profit'], 2);
            $totalStorage = round($totalStorage + $storage);
        }

        $pacakgeList['汇总数据'] = [
                'chrtitle' => '汇总数据',
                'payname' => '',
                'order_num' => $totalOrderNum,
                'pay_num' => $totalPayNum,
                'pay_price' => number_format($totalPayPrice, 2, '.', ''),
                'profit' => number_format($totalProfit, 2, '.', ''),
                'storage' =>  $totalStorage == INF ? '充足' : $totalStorage,
                'dtpublishtime' => '',
                'dtsignetime' => '',
                'dtstart' => '',
                'dtend' => ''
            ];

        return $pacakgeList;
    }


    /**
     * 审核通过|不通过 短信发送功能
     * @author Hlt
     * @DateTime 2019-04-28T14:40:51+0800
     * @param    integer                   $orderId 订单id
     * @param    integer                   $idsite  站点id
     * @return   void
     */
    public function signInNotice($orderId, $idsite = '')
    {
        if(!$idsite)
        {
            $idsite = session('idsite');
        }


        //发送短信
        //获取订单信息
        $order = db('order')->where(array('id'=>$orderId,'idsite' => $idsite))->find();
        if(!$order)
        {
            return false;
        }
        //替换信息
        $replace = [];
        //场景--审核通过/不通过
        if($order['state'] == 3)
        {
            //给客户和商务发短信通知    类型：8--免费活动审核通过
            $type = 8;
        }elseif($order['state'] == 2)
        {
            //给客户和商务发短信通知    类型：9--免费活动审核不通过
            $type = 9;
        }else
        {
            return false;
        }
        return sysSendMsg($idsite, $type, $order, $replace);
    }



    public function groupBuyOrderList($request)
    {
        // 活动名称
        // 拼团状态
        // 拼团时间
        // 页数
//        拼团情况  group_num  sold
//        开团人
//        价格/购买数
//        拼团时间  activate_at 激活时间
//        状态
        $where = [];
        if(isset($request['activity_name']) && !empty($request['activity_name']))
        {
            $where['activity_name'] = ['like', '%' . $request['activity_name'] . '%'];
        }
        if(isset($request['state']) && $request['state'] != 200)
        {
            $where['state'] = (int)$request['state'];
        }
        if(isset($request['start_at']) && isDate($request['start_at']))
        {
            $where['start_at'] = ['egt', strtotime($request['start_at'])];
        }
        if(isset($request['end_at']) && isDate($request['end_at']))
        {
            $where['end_at'] = ['elt', strtotime($request['end_at'])];
        }

        $count = db('group_buy_order')->where($where)->count();
        $page = new Page($count, PAGE_SIZE);
        $data = db('group_buy_order')
            ->where($where)
            ->order('group_buy_order_id desc')
            ->limit($page->firstRow , $page->pageSize)
            ->select();


        $result = [
            'page' => $page,
            'data' => $data
        ];

        return $result;
    }


    public function subGroupBuyOrderList($groupBuyOrderId)
    {
        $where = ['group_buy_order_id' => $groupBuyOrderId];

        $count = db('order')
            ->where($where)
            ->count();
        $page = new Page($count, PAGE_SIZE);
        $data = db('order')
            ->where($where)
            ->order('id desc')
            ->limit($page->firstRow , $page->pageSize)
            ->select();

        foreach ($data as &$value){
            if((int)$value["ischarge"]==1){
                $value["price"] = "0.00";
            }
        }

        $arr = array();
        $arr['pager'] = $page;
        $arr['data'] = $data;
        return $arr;
    }
}