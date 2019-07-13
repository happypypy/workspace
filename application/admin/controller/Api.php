<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: JY
 * Date: 2015-09-23
 */

namespace app\admin\controller;
use Home\Logic\UsersLogic;
use think\Controller;
use think\Request;
use think\Log;
use think\Db;
use think\db\Query;
use app\home\model\GroupBuyOrder;
use think\wx\Utils\HttpCurl;

class Api extends Controller {
    
    public  $send_scene;
     
    /*
     * 获取地区
     */
    public function getRegion(){
        $request = Request::instance()->param();
        $parent_id = $request['parent_id'];
        if(array_key_exists('selected',$request)){
            $selected = $request['selected'];
        }else{
            $selected = 0;
        }

        /*$parent_id = I('get.parent_id');
        $selected = I('get.selected',0); */
        $data = db('region')->where("parent_id=".$parent_id)->select();
        $html = '';
        if($data){
            foreach($data as $h){
            	if($h['id'] == $selected){
            		$html .= "<option value='{$h['id']}' selected>{$h['name']}</option>";
            	}
            	else {
                    $html .= "<option value='{$h['id']}'>{$h['name']}</option>";
                }
            }
        }
        
        echo $html;
    }


    public  function refund_return()
    {

        $input= file_get_contents("php://input");
        if($input){
            /*
             <xml>
                <return_code><![CDATA[SUCCESS]]></return_code>
                <return_msg><![CDATA[OK]]></return_msg>
                <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
                <mch_id><![CDATA[10000100]]></mch_id>
                <nonce_str><![CDATA[NfsMFbUFpdbEhPXP]]></nonce_str>
                <sign><![CDATA[B7274EB9F8925EB93100DD2085FA56C0]]></sign>
                <result_code><![CDATA[SUCCESS]]></result_code>
                <transaction_id><![CDATA[1008450740201411110005820873]]></transaction_id>
                <out_trade_no><![CDATA[1415757673]]></out_trade_no>
                <out_refund_no><![CDATA[1415701182]]></out_refund_no>
                <refund_id><![CDATA[2008450740201411110000174436]]></refund_id>
                <refund_channel><![CDATA[]]></refund_channel>
                <refund_fee>1</refund_fee>
             </xml>
             * */
            $xml = simplexml_load_string($input);
            //$money = (string)$xml->total_fee;
            $return_code = (string)$xml->return_code;
            //$attach = (string)$xml->attach;
            $out_trade_no = (string)$xml->out_trade_no;
            $out_refund_no = (string)$xml->out_refund_no;
        }


        if($return_code=='SUCCESS'){
            echo 'SUCCESS';
            exit();
            $result= db('order')->where(array('ordersn'=>$out_trade_no))->find();

            //业务处理　　//修改订单/用户状态
            $arr=[];
            $arr['state']= $result['isrefundpart']==1?6:7;
            if(empty($result["refundsn"]))
            {
                $arr['wxrefundsn']=$out_refund_no;
                $arr['dtwxrefundtime']=date("Y-m-d H:i:s",time());
            }
            else
            {
                $arr['wxrefundsn2']=$out_refund_no;
                $arr['dtwxrefundtime2']=date("Y-m-d H:i:s",time());
            }

            $bool=db('order')->where(array('ordersn'=>$out_trade_no))->update($arr);
            echo 'SUCCESS';
        }
        else
        {
            echo 'FAIL';
        }

    }
    /*
     * 退款
     */
    public function refund()
    {
        $request = Request::instance()->param();
        $sitecode=isset($request['sitecode']) ? $request['sitecode'] : "";
        if($sitecode=="")
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'企业代号不能为空')));
        }
        $ordersn=array_key_exists('ordersn',$request)?$request['ordersn']:"";

        if($ordersn=="")
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'订单不存在！')));
        }

        $datainfo=db('order')->where(array('ordersn'=>$ordersn))->find();
        if(empty($datainfo))
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'订单不存在！')));
        }

        $data=[];

        $data['out_trade_no']=$ordersn;//商户订单号
        $data['out_refund_no']=date('YmdHis').rand(100000,999999);//商户退款单号
        $data['refund_fee']=array_key_exists('refundprice',$request)?($request['refundprice']*100):"";//退款金额
        $data['total_fee']=($datainfo['price']*100);//总金额
        $refundremark=array_key_exists('refundremark',$request)?$request['refundremark']:"";//退款原因
        //$data['refund_desc']=array_key_exists('refundremark',$request)?$request['refundremark']:"";//退款原因
        $data['notify_url']=ROOTURL."/admin/Api/refund_return";//异步接收微信支付退款结果通知的回调地址

        if($data['total_fee']<$data['refund_fee'])
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'退款金额不能大于订单总金额')));
        }
        if($datainfo['state']<4 || $datainfo['state']>6)
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'订单不能退款')));
        }

        $arr=[];
        if(empty($datainfo['refundsn']))
        {
            $arr['refundsn']= $data['out_refund_no'];//商户退款单号
            $arr['refundprice']=$request['refundprice']; //退款金额
            $arr['refundremark1']=$refundremark;//退款原因
            $arr['refundpic']=array_key_exists('refundpic',$request)?$request['refundpic']:"";//退款上传图片
            $arr['dtwxrefundtime']=date("Y-m-d H:i:s",time());
        }
        else
        {
            $arr['refundsn2']= $data['out_refund_no'];//商户退款单号
            $arr['refundprice2']=$request['refundprice']; //退款金额
            $arr['refundremark2']=$refundremark;//退款原因
            $arr['refundpic2']=array_key_exists('refundpic',$request)?$request['refundpic']:"";//退款上传图片
            $arr['dtwxrefundtime2']=date("Y-m-d H:i:s",time());
        }

        $arr['isrefundpart']=$request['isrefundpart']; //是否是部分退款 0：不是 1：是
        $arr['dtrefundtime']=date("Y-m-d H:i:s");//用户发起退款的时间
        $arr['state']="5";//退款中

        if(!empty($datainfo['flag']) && $datainfo['flag']==1 )
        {
            $arr['state']="10";//取消服务
        }

        $arr['intflag']=6;


        // api模块 - 包含各种系统主动发起的功能
        $config=getWeiXinConfig($sitecode);
        $api = new \think\wx\Api(
            array(
                'appId' => trim($config['appid']),
                'appSecret'      => trim($config['appsecret']),
                'mchId'          => trim($config['mchid']),
                'key'            => trim($config['paykey']) ,
                'sslKeyPath'     => __ROOT__."/cart/".trim($config['sslkeypath']) ,
                'sslCertPath'    => __ROOT__."/cart/".trim($config['sslcertpath']),
                'sslrootcatPath' => __ROOT__."/cart/".trim($config['cainfo']),
            )
        );

        $result=$api->refund($data);
        Log::debug('微信退款返回信息:$result = ' . print_r($result, true));
        // $result['state'] = 1;
        // $result['info'] = ['return_code' => 'SUCCESS', 'result_code' => 'SUCCESS', 'out_trade_no' => '20190705112011171756'];  //调试短信发送功能使用
        if($result['state']==1)
        {
            // TODO 微信退款失败
            $obj=$result['info'];
            if($obj["return_code"]=="SUCCESS")
            {
                if($obj["result_code"]=="SUCCESS")
                {
                    //替换信息
                    $replace = [];
                    try
                    {
                        //业务处理　　//修改订单/用户状态
                        if($request['flag']==1)
                        {
                            //取消服务
                            //给客户和商务发短信通知  类型：5--已退款终止服务
                            $type = 5;
                            $arr['state']= $request['isrefundpart']==1?13:11;

                            if($datainfo['stock_locked'] == 1)
                            {
                                //释放库存
                                $arr['stock_locked'] = 0;
                                if ($datainfo['group_buy_order_id'])
                                {
                                    $groupBuyOrder = new GroupBuyOrder;
                                    $res = $groupBuyOrder->releaseGroupBuyOrderStock($datainfo['group_buy_order_id'], $datainfo['paynum']);
                                   // db('group_buy_order')->where(['group_buy_order_id' => $datainfo['group_buy_order_id']])->setDec("buy_num",$datainfo['paynum']);
                                    if(!$res)
                                    {
                                        throw new Exception('数据错误');
                                    }
                                }else
                                {
                                    changeStock($datainfo['package_id'], $datainfo['paynum'], false);
                                }
                            }
                        }else
                        {
                            // 不取消服务
                            //给客户和商务发短信通知  类型：4--已退款继续服务
                            $type = 4;
                            $arr['state']= $request['isrefundpart']==1?6:7;
                        }


                        //订单修改不做事务
                        $bool=db('order')->where(array('ordersn'=>$obj["out_trade_no"]))->update($arr);

                        $order_id = $datainfo['id'];
                        $member_id = $datainfo['fiduser'];

                        $idsite = db('site_manage')->where('site_code',$sitecode)->value('id');
                        // 全额退款, 失效积分
                        if($request['isrefundpart'] == 0){
                            db('member_integral_record')->where(['siteid'=>$idsite, 'member_id' => $member_id, 'order_id' => $order_id])->update(['category_id' => 4, 'is_freeze' => 3]);
                        }
                        // 部分退款，解冻部分积分
                        else{
                            $signup_integral = db('integral_rule_config')->where('idsite',$idsite)->value('signup_integral');
                            $signup_integral = $signup_integral>0 ? $signup_integral : 1;
                            $new_integral = intval($datainfo['price'] -  $datainfo['price1'] - $datainfo['price2'] - $request['refundprice']) * $signup_integral;
                            db('member_integral_record')->where(['siteid'=>$idsite, 'member_id' => $member_id, 'order_id' => $order_id])->setField(['integral' => $new_integral]);
                        }

                    }catch(Exception $e)
                    {
                        Log::error('退款退活动后释放库存失败， [ SQL ] ' . Db::getLastSql());
                        exit(json_encode(['state' => 0, 'msg' => '退款成功，库存释放失败']));
                    }

                    $order = array_merge($datainfo, $arr);
                    //发送短信
                    sysSendMsg($config['id'], $type, $order, $replace);

                    Log::debug('微信退款数据：' .  print_r($order, true));

                    //发送微信消息
                    template_tg($datainfo["id"]);
                }
            }
        }

        exit(json_encode($result));
    }

    /**
     * 拒绝退款
     */
    public function refuse_refund(){
        $request = Request::instance()->param();
        $sitecode=isset($request['sitecode']) ? $request['sitecode'] : "";
        if($sitecode=="")
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'企业代号不能为空')));
        }
        $ordersn=array_key_exists('ordersn',$request)?$request['ordersn']:"";

        if($ordersn=="")
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'订单不存在！')));
        }

        $datainfo=db('order')->where(array('ordersn'=>$ordersn))->find();
        if(empty($datainfo))
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'订单不存在！')));
        }


        if($datainfo['state']<4 || $datainfo['state']>6)
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'订单不能操作')));
        }

        $refundremark=array_key_exists('refundremark',$request)?$request['refundremark']:"";//退款原因

        $arr=[];

        $arr['refundremark1']=$refundremark;//退款原因

        $arr['state']="8";//拒绝退款

        $bool=db('order')->where(array('ordersn'=>$ordersn))->update($arr);

        if($bool) {
            //获取订单信息
            $replace = [];
            //获取站点id
            $config = getWeiXinConfig($sitecode);
            //给客户和商务发短信通知  类型：6--拒绝退款
            sysSendMsg($config['id'], 6, $datainfo, $replace);
            Log::debug(date('Y-m-d H:i:s') . '调起微信发送测试');
            template_tg($datainfo["id"]);

            exit(json_encode(array( 'state'=>0,'msg'=>'拒绝退款成功！')));
        }
        exit(json_encode(array( 'state'=>0,'msg'=>'拒绝退款失败！')));
    }

    public function issign()
    {
        $request = Request::instance()->param();
    }

    public function getTwon(){
    	$parent_id = I('get.parent_id');
    	$data = db('region')->where("parent_id=$parent_id")->select();
    	$html = '';
    	if($data){
    		foreach($data as $h){
    			$html .= "<option value='{$h['id']}'>{$h['name']}</option>";
    		}
    	}
    	if(empty($html)){
    		echo '0';
    	}else{
    		echo $html;
    	}
    }

    public function getProvince()
    {
        $province = db('region')->field('id,name')->where(['level'=>1])->select();
        foreach($province as $key=>$val){
            $province[$key]['city'] = M('region')->field('id,name')->where(['parent_id'=> $province[$key]['id']])->select();
        }
        $res = ['status'=>1,'msg'=>'获取成功','result'=>$province];
        //$this->AjaxReturn($res);
        return $res;
    }
    public function getArea(){
        $id = I('id');
        if($id){
            $area = M('region')->field('id,name,parent_id as pid')->where(['parent_id'=>$id])->select();
            $res = ['status'=>1,'msg'=>'获取成功','result'=>$area];
        }else{
            $res = ['status'=>0,'msg'=>'获取失败,参数有误','result'=>''];
        }
        //$this->AjaxReturn($res);
        return $res;
    }
    
    /*
     * 获取商品分类
     */
    public function get_category(){
        $html="";
        $parent_id = I('get.parent_id','0'); // 商品分类 父id  
        empty($parent_id) && exit('');
        $list = db('goods_category')->where(array('parent_id'=>$parent_id))->select();
        foreach($list as $k => $v)
        {             
            $html .= "<option value='{$v['id']}' rel='{$v['commission']}'>{$v['name']}</option>";
        }            
        exit($html);
    }
    
     public function get_cates(){
         $html="";
     	$parent_id = I('get.parent_id','0'); // 商品分类 父id
     	empty($parent_id) && exit('');
     	$list = db('goods_category')->where(array('parent_id'=>$parent_id))->select();
     	foreach($list as $k => $v)
     	{
     		$html .= "<input type='checkbox' name='subcate[]' rel='{$v['commission']}' data-name='{$v['name']}' value='{$v['id']}'>".$v['name'];
     	}
     	exit($html);
     }    

    /**
     * 前端发送短信方法: APP/WAP/PC 共用发送方法
     */
    public function send_validate_code(){
         
        $this->send_scene = C('SEND_SCENE');
        
        $type = I('type');
        $scene = I('scene');    //发送短信验证码使用场景
        $mobile = I('mobile');
        $sender = I('send');
        $mobile = !empty($mobile) ?  $mobile : $sender ;
        $session_id = I('unique_id' , session_id());
        session("scene" , $scene);
        
        if($type == 'email'){
            //发送邮件验证码
            $logic = new UsersLogic();
            $res = $logic->send_validate_code($sender, $type);
            $this->ajaxReturn($res);
            
        }else{
            //发送短信验证码
            $res = checkEnableSendSms($scene);       
            if($res['status'] != 1){
                $this->ajaxReturn($res);
            }
            
            //判断是否存在验证码
            $data = db('sms_log')->where(array('mobile'=>$mobile,'session_id'=>$session_id))->order('id DESC')->find();
            //获取时间配置
            $sms_time_out = tpCache('sms.sms_time_out');
            $sms_time_out = $sms_time_out ? $sms_time_out : 120;
            //120秒以内不可重复发送
            if($data && (time() - $data['add_time']) < $sms_time_out){
                $return_arr = array('status'=>-1,'msg'=>$sms_time_out.'秒内不允许重复发送');
            }
            //随机一个验证码
            $code =  rand(1000,9999);
            $row = db('sms_log')->add(array('mobile'=>$mobile,'code'=>$code,'add_time'=>time(),'session_id'=>$session_id , 'status' => 0));
            
            $user = session('user');
            if ($scene == 6){
                 
                if(!$user['user_id']){
                    //登录超时
                    $return_arr = array('status'=>-1,'msg'=>'登录超时');
                    //$this->ajaxReturn($return_arr);
                    return $return_arr;
                }
                $params = array('code'=>$code);
                 
                if($user['nickname']){
                    $params['user_name'] = $user['nickname'];
                }
            }
            $params['code'] =$code;
            
            //发送短信
            $resp = sendSms($scene , $mobile , $params);
             
            if($resp['status'] == 1){
                //发送成功, 修改发送状态位成功
                db('sms_log')->where(array('mobile'=>$mobile,'code'=>$code,'session_id'=>$session_id , 'status' => 0))->save(array('status' => 1));
                $return_arr = array('status'=>1,'msg'=>'发送成功,请注意查收');
                //$this->ajaxReturn($return_arr);
                return $return_arr;
            }else{
                $return_arr = array('status'=>-1,'msg'=>'发送失败,请联系管理员');
                //$this->ajaxReturn($return_arr);
                return $return_arr;
            }
        }
    }
    
    /**
     * 验证短信验证码: APP/WAP/PC 共用发送方法
     */
    public function check_validate_code(){
        $code = I('post.code');
        $mobile = I('mobile');
        $send = I('send');
        $sender = empty($mobile) ? $send : $mobile; 
        $type = I('type');
        $session_id = I('unique_id', session_id());
        $logic = new UsersLogic();
        
        $res = $logic->check_validate_code($code, $sender , $session_id , $type);   
        $this->ajaxReturn($res);
    }
    
    /**
     * 检测手机号是否已经存在
     */
    public function issetMobile()
    {
      $mobile = I("mobile",'0');  
      $users = M('users')->where("mobile = '$mobile'")->find();
      if($users)
          exit ('1');
      else 
          exit ('0');      
    }   

    
    /**
     * 检测邮件是否已经存在
     */
    public function issetEmail()
    {
        $mobile = I("email",'0');
        $users = M('users')->where("email = '$mobile'")->find();
        if($users)
            exit ('1');
        else
            exit ('0');
    }

    /**
     * 查询物流
     */
    public function queryExpress()
    {
        $shipping_code = I('shipping_code');
        $invoice_no = I('invoice_no');
        if(empty($shipping_code) || empty($invoice_no)){
            $this->AjaxReturn(array('status'=>0,'message'=>'参数有误','result'));
        }
        $this->AjaxReturn(queryExpress($shipping_code,$invoice_no));
    }


    /**
     * 定时发送短信
     * @author Hlt
     * @DateTime 2019-04-20T13:45:25+0800
     * @param    int                     $num      每次发短信的号码数
     * @return                               
     */
    public function textTimedSending($num)
    {
        //缓存有效期
        $expire = 90;

        //根据缓存中的working项(是否正在工作)决定本次定时任务是否执行
        $schedule = cache('schedule');
        //程序首次执行，还没有缓存
        if(!$schedule)
        {
            $schedule = [
                'index' => 0,
                'working' => true
            ];
        }else
        {
            //上次定时任务还在执行
            if($schedule['working'] === true)
            {
                //记录警告信息
                Log::warning('上次定时任务执行时间过长，已执行到' . $schedule['index']);
                return;
            }
            //上次定时任务已结束
            //置是否工作状态为true
            $schedule['working'] = true;
        }

        //更新缓存中的工作状态为true
        cache('schedule', $schedule, $expire);

        $obj = new \app\admin\module\Api();
        $index = $obj->executeTextSchedule($schedule['index'], $num);

        //更新缓存中的工作状态为false
        $schedule['index'] = $index;
        $schedule['working'] = false;
        cache('schedule', $schedule, $expire);
        echo '发送完成';
    }

    /**
     * 定时解冻积分
     *
     * @param int $num  起始位置
     * @return void
     * @author Chenjie
     */
    public function thawActivityIntegral(){
        $param = input('param.');
        $starttime = isset($param['starttime']) ? $param['starttime'] : '';
        $endtime = isset($param['endtime']) ? $param['endtime'] : '';

        $obj = new \app\admin\module\Api();
        $integral_number = $obj->executeThawActivityIntegral($starttime, $endtime);
        trace('解冻时间:'.$starttime.'-'.$endtime.',解冻了'.$integral_number.'条记录','log');
        echo '解冻完成';
    }

    /**
     * url to qr code
     * @DateTime 2019-04-22T16:23:26+0800
     * @return   stream                   二维码
     */
    public function qrCode()
    {
        $request = Request::instance()->param();
        ob_end_clean();
        include_once('../thinkphp/library/think/Phpqrcode/phpqrcode.php');
        $url = isset($request["url"]) ? $request["url"] : "";
        if (empty($url)) {
            exit("no data");
        }
        $url = base64_decode($url);
        $size = 6;
        Vendor('Phpqrcode.phpqrcode');
        $obj = new \QRcode();
        $obj::png($url, false, QR_ECLEVEL_L, $size, 2, false, 0xFFFFFF, 0x000000);
        exit();
    }


    /**
     * 测试支付功能回调处理
     * @author Hlt
     * @DateTime 2019-04-23T09:49:44+0800
     * @return   string                   success/fail
     */
    public function testPayNotify()
    {
        try {
            $input = file_get_contents("php://input");
            $params = Request::instance()->param();
            if ($input) {
                $xml = simplexml_load_string($input,'SimpleXMLElement', LIBXML_NOCDATA);
                Log::debug('微信支付回调内容：' . print_r(json_decode(json_encode((array)$xml), 1),true));
                $return_code = (string)$xml->return_code;
            }
            if (strtoupper($return_code)  == 'SUCCESS') {
                // file_put_contents( 'testPayNotify.txt',"代码执行到这里",FILE_APPEND);
                //获取订单信息
                $siteManage = db('site_manage')->where(['site_code' => $params['sitecode']])->find();
                if(empty($siteManage)){
                    echo 'fail';
                }else
                {
                    db('site_manage')->where(['site_code' => $params['sitecode']])->update(['payment_verification' => 1]);
                    echo 'success';
                }
            } else {
                echo 'fail';
            }
        } catch (Exception $ex)
        {
            echo 'fail';
        }
    }


    /**
     * 搜索超过特定时间未支付的订单，释放库存
     * @author Hlt
     * @DateTime 2019-05-23T11:15:21+0800
     * @return   void
     */
    public function checkExpiredOrder()
    {
        $expiredOrders = db('order')->where(
                [
                    'state' => 12,
                    'lock_stock_at' => ['elt', time() - config('order_expire_time')],
                    'stock_locked' => 1
                ]
            )
            ->field(
                [
                    'id',
                    'package_id',
                    'paynum',
                    'receive_cashed_id',
                    'group_buy_order_id',
                    'stock_locked',
                ]
            )
            ->select();
        $groupBuyOrder = new GroupBuyOrder;
        foreach ($expiredOrders as $key => $expiredOrder)
        {
            try
            {
                $query = new Query;
                $query->startTrans();
                if($expiredOrder['group_buy_order_id'] != '' && $expiredOrder['group_buy_order_id'] != 0)
                {
                    // 拼团订单，释放库存路径不同
                    $res = $groupBuyOrder->releaseGroupBuyOrderStock($expiredOrder['group_buy_order_id'], $expiredOrder['paynum']);

                }else
                {
                    $res = changeStock($expiredOrder['package_id'], $expiredOrder['paynum'], false);

                }

                if($res)
                {
                   db('order')->where(['id' => $expiredOrder['id']])->update(['stock_locked' => 0]);
                }

                //判断该订单是否使用了现金券
                if($expiredOrder['receive_cashed_id'] != ''){
                    $receive_cashed_id_arr = explode(',',$expiredOrder['receive_cashed_id']);
                    foreach ($receive_cashed_id_arr as $value){
                        //将现金券进行释放(修改为未使用)
                        db('cashed_card_receive')->where(['id'=>$value])->update(['used_status'=>1,'release_time'=>date('Y-m-d H:i:s',time())]);
                    }
                }
                $query->commit();
            }catch(Exception $e)
            {
                Log::error('释放未支付订单库存失败，订单号为' . $expiredOrder['id']);
                $query->rollBack();
            }
        }
    }

    /**
     * 获取审核同步结果
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function getAuditResult(){
        //限制ip
        if(!in_array(getip(),array('121.42.228.150'))){
            echo json_encode(array('success'=>false,'message'=>'IP错误'));exit;
        }
        //限制请求方式
        if(!Request::instance()->isPost()){
            echo json_encode(array('success'=>false,'message'=>'请用POST请求'));exit;
        }
        $params = file_get_contents("php://input");
        Log::info('接收的请求参数:'.$params);
        //校验参数
        $params = $this->verifyGetAuditResultParam(json_decode($params,true));
        //查询该活动的待审核的数据
        $activity_info = db('activity')->where(['idactivity' => $params['activity_id']])->find();
        //如果找不到数据
        if(!$activity_info) {
            echo json_encode(array('success'=>false,'message'=>'该活动的同步状态不处于待审核'));exit;
        }
        //取出审核备注
        $audit_remark = json_decode($activity_info['audit_remark'])?json_decode($activity_info['audit_remark']):array();
        //将本次的审核情况累加
        array_push($audit_remark,array('audit_status'=>$params['audit_status'],'audit_remark'=>$params['audit_remark'],'audit_time'=>$params['audit_time']));
        $update_data['audit_remark'] = json_encode($audit_remark);
        //审核状态  为1就是成功,2就是审核失败,3就是下架
        if($params['audit_status'] == 1){
            $update_data['wntx_sync_status'] = 4;
        }elseif($params['audit_status'] == 2){
            $update_data['wntx_sync_status'] = 3;
        }else{
            $update_data['wntx_sync_status'] = 5;
        }
        //修改数据库
        $bool = db('activity')->where(['idactivity' => $params['activity_id']])->update($update_data);
        if($bool){
            echo json_encode(array('success'=>true,'message'=>'请求成功'));exit;
        }else{
            echo json_encode(array('success'=>false,'message'=>'操作数据库失败'));exit;
        }

    }

    /**
     * 校验参数
     * @param array $params 参数
     * @return mixed
     */
    public function verifyGetAuditResultParam($params){
        //校验参数
        if(!array_key_exists('activity_id',$params)){
            echo json_encode(array('success'=>false,'message'=>'缺少活动id标识'));exit;
        }
        //校验审核状态参数
        if(!array_key_exists('audit_status',$params)){
            echo json_encode(array('success'=>false,'message'=>'缺少审核状态'));exit;
        }
        if(!in_array($params['audit_status'],array(1,2,3))){
            echo json_encode(array('success'=>false,'message'=>'审核状态值超出范围'));exit;
        }
        if(!array_key_exists('audit_remark',$params)){
            echo json_encode(array('success'=>false,'message'=>'缺少审核意见'));exit;
        }
        if(!array_key_exists('audit_time',$params)){
            echo json_encode(array('success'=>false,'message'=>'缺少审核时间'));exit;
        }
        //审核时间格式
        if(!\DateTime::createFromFormat('Y-m-d H:i:s',$params['audit_time'])){
            echo json_encode(array('success'=>false,'message'=>'审核时间格式错误'));exit;
        }
        return $params;
    }


    /**
     * 定时器，用于检测超时的拼团订单 拼团开启一定时间后超时  拼团活动结束
     * @author Hlt
     * @DateTime 2019-06-21T17:58:52+0800
     * @return   [type]                   [description]
     */
    public function checkExpiredGroupBuyOrder()
    {
        // 拼团活动结束

        //查询出所有过期的开团订单id
        $expiredGroupBuyOrders = db('group_buy_order')->where([
                // 0-开启拼团未支付，1-拼团中
                'state' => ['in',[0, 1]],
                'expire_at' => ['lt', time()]
            ])
            ->column('group_buy_order_id');

        $expiredOrders = db('order')->where([
                'group_buy_order_id' => ['in', $expiredGroupBuyOrders],
                // 状态条件
                // 4.已报名 已支付，
                // 5.已报名 退款中，
                // 6已部分退款 继续服务，
                // 7已退款 继续服务，
                // 8.已报名 退款不通过，
                // 12.已报名 待支付，
                'state' => ['in', [4,5,6,7,8,12]]
            ])
            ->field([
                'group_buy_order_id',
                'paynum',
                'id',
                'stock_locked',
            ])
            ->select();

        try
        {
            $query = new Query;
            $query->startTrans();
            $groupBuyOrder = new GroupBuyOrder;
            // 挨个订单释放库存
            foreach ($expiredOrders as $key => $expiredOrder)
            {
                // 退款
                if($expiredOrder['state'] != 12)
                {
                    $sitecode = getSiteCode($expiredOrder['idsite']);
                    $params = [
                        'sitecode' => $sitecode,
                        'ordersn' => $expiredOrder['ordersn'],
                        'refundprice' => $expiredOrder['price'],
                        'flag' => 1,
                        'price' => $expiredOrder['price'],
                        'isrefundpart' => 0,
                        'refundremark' => '拼团失败退款',
                    ];
                    $result = HttpCurl::post($_SERVER['HTTP_HOST'] . url('admin/api/refund'), $params);
                    $result = json_decode($result, true);
                    if($result['state'] == 0)
                    {
                        throw new Exception('退款失败');
                    }
                }

                if($expiredOrder['stock_locked'] == 1)
                {
                    $groupBuyOrder->releaseGroupBuyOrderStock($expiredOrder['group_buy_order_id'], $expiredOrder['paynum']);

                }

            }

            // 挨个拼团订单发送失败微信消息
            foreach ($expiredGroupBuyOrders as $key => $expiredGroupBuyOrder)
            {
                $groupBuyOrder->sendGroupBuyFailedWechatMsg($expiredGroupBuyOrder['group_buy_order_id'], $expiredGroupBuyOrder['site_id'], '拼团已过期');
            }

            // 修改所有订单状态为已退款终止服务 或已删除状态
//            dump(array_column($expiredOrders, 'id'));

            db('order')->where([
                    'id' => ['in', array_column($expiredOrders, 'id')],
                    'state' => ['in', [4,5,6,7,8]]
                ])->update([
                    'state' => 11,
                    'stock_locked' => 0
                ]);
            db('order')->where([
                    'id' => ['in', array_column($expiredOrders, 'id')],
                    'state' => 12
                ])->update([
                    'state' => 9,
                    'stock_locked' => 0
                ]);
            $query->commit();

        }catch(\Exception $e)
        {
            $query->rollBack();
            Log::error('定时停止拼团失败，info:' . print_r($e, true));

        }
    }
}