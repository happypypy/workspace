<?php

namespace app\home\controller;
use think\Db;

class Subscribe extends BaseAuth {
    /**
     * 会员卡列表
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-17 14:44:35
     */
    public function membercart(){
        $request = input('param.');
        $ipage = isset($request['ipage']) ? intval($request['ipage']) : 1;
        $sitecode = $request['sitecode'];
        $userid = $this->userinfo['idmember'];
        $idsite = $this->siteid;

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'subscribe')){
            die('没有权限,请先订购，预约营销包！');
        }
        $map = [
            'siteid' => $idsite,
            'member_id' => $userid
        ];

        $total_record = db('subscribe_member_cart')->where($map)->count();
        $member_cart_list = db('subscribe_member_cart')->where($map)->page($ipage,$this->pageSize)->select();
        $total_page = ceil($total_record/$this->pageSize);

        $roottpl = 'template/modules/';
        $url = $roottpl.'/subscribe/membercart.html';
        if (request()->isPost() && isset($request['ajax'])) {
            $url = $roottpl . '/subscribe/ajaxmembercart.html';
        }

        $this->assign('total_page', $total_page);
        $this->assign('member_cart_list', $member_cart_list);
        $this->assign('sitecode', $sitecode);
        $this->assign('roottpl', '/'.$roottpl);
        $this->assign('idsite', $idsite);
        return $this->fetch($url);
    }
    /**
     * 会员卡验证
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-18 14:20:47
     */
    public function membercartcheck(){
        $request = input('param.');
        $membercartid = isset($request['membercartid']) ? $request['membercartid'] : 0;

        $member_cart = db('subscribe_member_cart')->where('id',$membercartid)->find();

        // 判断会员卡是否过期
        if($member_cart['end_time'] < time()){
          return ['status'=>-1, 'msg'=>'预约失败，会员卡过期!'];
        }
        // 判断预约次数超限
        $use_number = intval($member_cart['use_number']); // 已预约次数
        if($use_number >= $member_cart['available_number'] && $member_cart['available_number'] != 0){
            return ['status'=>-1, 'msg'=>'预约失败，会员卡超出可预约次数'];
        }

        return ['status'=>1, '验证成功'];
    }
    /**
     * 预约功能
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-17 15:24:10
     */
    public function subscribe(){
        $request = input('param.');
        $ipage = isset($request['ipage']) ? intval($request['ipage']) : 1;
        $member_cart_id = $request['id'];
        $sitecode = $request['sitecode'];
        $idsite = $this->siteid;

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'subscribe')){
            die('没有权限,请先订购，预约营销包！');
        }

        // 查询所有session_key
        $where = '';
        $session_key_list = db('subscribe_allow_session')->where('member_cart_id',$member_cart_id)->column('session_key');
        foreach($session_key_list as $value){
            $session_param = explode("-", $value);
            $object_id = $session_param[0];     // 项目ID
            $session_key = '';
            foreach($session_param as $key=>$value){
                if($key !== 0){
                    $session_key .= $value. "-";
                }
            }
            $session_key = rtrim($session_key,"-");  // 场次key

            $where .='subscribe_object_id='.$object_id.' and session_key=\''.$session_key.'\' or ';
        }
        $where = rtrim($where,"or ");
        // 查询session_key数据
        $map = [];

        // 生成一周日期
        $current_time = time();
        // 获取当前周几
        $weekArray = ["周日","周一","周二","周三","周四","周五","周六"];
        $subscribe_session = [];
        // 叠加生成数据
        for ($i=0; $i<7; $i++){
            $between_time = strtotime(date('Y-m-d' ,strtotime( '+' . $i.' days', $current_time)));   // 当前时间
            $week = $weekArray[date('w', $between_time)];    // 周几
            $map['week'] = $week;
            $where1 = $between_time.' between start_time and end_time';
            $subscribe_session = array_merge($subscribe_session,(array)db('subscribe_session')->field("*,".$between_time." as subscribe_time")->where($where)->where($where1)->where($map)->select());
            //echo ($subscribe_session[0]);exit;
        }
        
        $roottpl = 'template/modules/';
        $url = $roottpl.'/subscribe/subscribe.html';
        if (request()->isPost() && isset($request['ajax'])) {
            $url = $roottpl . '/subscribe/ajaxsubscribe.html';
        }

        $this->assign('subscribe_session', $subscribe_session);
        $this->assign('sitecode', $sitecode);
        $this->assign('roottpl', '/'.$roottpl);
        $this->assign('idsite', $idsite);
        $this->assign('member_cart_id', $member_cart_id);
        return $this->fetch($url);
    }
    /**
     * 预约提交
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-17 15:41:34
     */
    public function subscribepost(){
        $request = input('param.');
        $userid = $this->userinfo['idmember'];
        $idsite = $this->siteid;
        $membercartid = isset($request['membercartid']) ? $request['membercartid'] : 0;
        $sessionid = isset($request['sessionid']) ? $request['sessionid'] : 0;
        $subscribenumber = 1;  // 会员卡预约次数
        $subscribetime = isset($request['subscribetime']) ? intval($request['subscribetime']) : 0;  // 会员卡预约次数

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'subscribe')){
            die('没有权限,请先订购，预约营销包！');
        }

        $member_cart = db('subscribe_member_cart')->where('id',$membercartid)->find();
        $subscribe_session = db('subscribe_session')->where('id',$sessionid)->find();

        // 判断预约次数超限
        $use_number = intval($member_cart['use_number']) + $subscribenumber; // 已预约次数
        if($use_number > $member_cart['available_number'] && $member_cart['available_number'] != 0){
            return ['status'=>-1, 'msg'=>'预约失败，会员卡超出可预约次数'];
        }
        // 判断会员卡是否过期
        else{
            if($member_cart['end_time'] < time()){
                return ['status'=>-1, 'msg'=>'预约失败，会员卡过期!'];
            }
        }

        // 生成预约订单号
        $subscribe_no = '';
        while (true) {
            $subscribe_no = "yy". date('YmdHis') . rand(100000, 999999); // 订单编号
            $subscribe_record = db('subscribe_record')->where("subscribe_no = '$subscribe_no'")->count();
            if ($subscribe_record == 0)
                break;
        }

        $data = [
            'siteid' => $idsite,
            'subscribe_no' => $subscribe_no,
            'member_cart_id' => $member_cart['id'],
            'member_cart_name' => $member_cart['member_cart_name'],
            'account_id' => 0,
            'member_id' => $userid,
            'member_nickanme' => $this->userinfo['nickname'],
            'subscribe_time' => $subscribetime,
            'subscribe_object_id' => $subscribe_session['subscribe_object_id'],
            'subscribe_object_name' => $subscribe_session['subscribe_object_name'],
            'subscribe_session_id' => $subscribe_session['id'],
            'subscribe_session_name' => $subscribe_session['session_name'],
            'week' => $subscribe_session['week'],
            'period' => $subscribe_session['period'],
            'place' => $subscribe_session['place'],
            'subscribe_number' => $subscribenumber,
            'create_time' => time(),
            'is_signin' => 0,
        ];

        Db::startTrans();
        try{
            // 已约人数增加
            db('subscribe_session')->where('id', $sessionid)->setInc('subscribe_number', $subscribenumber);
            // 会员卡使用次数增加
            db('subscribe_member_cart')->where('id', $membercartid)->setInc('use_number', $subscribenumber);
            // 写入预约记录
            $record_id = db('subscribe_record')->insertGetId($data);
            Db::commit();
            return ['status'=>1, 'data'=>['record_id'=>$record_id], 'msg'=>'预约成功'];
        }catch(Exception $e){
            Db::rollback();
            return ['status'=>-2, 'msg'=>$e->getMessage()];
        }
    }
    /**
     * 取消预约
     *
     * @return boolean
     * @author Chenjie
     * @Date 2019-08-16 09:49:09
     */
    public function cancel_subscribe(){
        $param = input('param.');
        $id = isset($param['id']) ? $param['id'] : 0;
        $subscribe_record = db('subscribe_record')->where('id',$id)->find();
        $subscribe_time = isset($subscribe_record['subscribe_time']) ? $subscribe_record['subscribe_time'] : 0;             // 预约时间
        $cancel_day = db('subscribe_object')->where('id',$subscribe_record['subscribe_object_id'])->value('cancel_day');    // 可取消预约时间
        $expire_time = strtotime('+'.$cancel_day.' day',$subscribe_time);   // 过期时间

        if(!$subscribe_record){
            return json(['code'=>-1,'msg'=>'未找到有效的预约订单']);
        }

        if($expire_time < time()){
            return ['code'=>-1, 'msg'=>'取消失败，超过可取消预约时间'];
        }

        $result = db('subscribe_record')->where('id',$id)->update([
            'subscribe_status' => 1,
            'cancel_member_id' => $this->userinfo['idmember'],
            'cancel_member_nickname' => $this->userinfo['nickname'],
            'cancel_time' => time(),
        ]);
        if(!$result){
            return json(['code'=>-1,'msg'=>'已经取消预约，请勿重复取消操作']);
        }
        return json(['code'=>1,'msg'=>'取消预约成功']);
        
    }
    /**
     * 预约项目详情
     *
     * @return void
     * @author Chenjie
     * @Date 2019-08-14 14:17:21
     */
    public function objectdetail(){
        $request = input('param.');
        $sitecode = $request['sitecode'];
        $idsite = $this->siteid;
        $id = $request['id'];

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'subscribe')){
            die('没有权限,请先订购，预约营销包！');
        }

        $datainfo = db('subscribe_object')->where('id',$id)->find();

        $roottpl = 'template/modules/';
        $url = $roottpl.'/subscribe/objectdetail.html';

        $this->assign('idsite', $idsite);
        $this->assign('sitecode', $sitecode);
        $this->assign('roottpl', '/'.$roottpl);
        $this->assign("datainfo", $datainfo);
        return $this->fetch($url);
    }
    /**
     * 预约记录
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-17 16:04:06
     */
    public function subscriberecord(){
        $request = input('param.');
        $id = isset($request['id']) ? $request['id'] : 0;
        $userid = $this->userinfo['idmember'];
        $ipage = isset($request['ipage']) ? intval($request['ipage']) : 1;
        $sitecode = $request['sitecode'];
        $idsite = $this->siteid;

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'subscribe')){
            die('没有权限,请先订购，预约营销包！');
        }

        $map = [
            'siteid' => $idsite,
            'member_id' => $userid
        ];
        if($id){
            $map['member_cart_id'] = $id;
        }
        $total_record = db('subscribe_record')->where($map)->count();
        $subscribe_record_list = db('subscribe_record')->where($map)->page($ipage,$this->pageSize)->order('create_time desc')->select();
        $total_page = ceil($total_record / $this->pageSize);

        $roottpl = 'template/modules/';
        $url = $roottpl.'/subscribe/subscriberecord.html';
        if (request()->isPost() && isset($request['ajax'])) {
            $url = $roottpl . '/subscribe/ajaxsubscriberecord.html';
        }
        $this->assign('idsite', $idsite);
        $this->assign('sitecode', $sitecode);
        $this->assign('roottpl', '/'.$roottpl);
        $this->assign("total_page", $total_page);
        $this->assign("subscribe_record_list", $subscribe_record_list);
        return $this->fetch($url);
    }

    /**
     * 预约详情
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-17 16:25:54
     */
    public function subscriberecorddetail(){
        $request = input('param.');
        $id = $request['id'];
        $sitecode = $request['sitecode'];
        $idsite = $this->siteid;

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'subscribe')){
            die('没有权限,请先订购，预约营销包！');
        }

        $subscriberecord = db('subscribe_record')->where('id',$id)->find();


        $roottpl = 'template/modules/';
        $url = $roottpl.'/subscribe/subscriberecorddetail.html';

        $this->assign('idsite', $idsite);
        $this->assign('sitecode', $sitecode);
        $this->assign("subscriberecord",$subscriberecord);
        $this->assign('roottpl', '/'.$roottpl);
        return $this->fetch($url);
    }
}