<?php

namespace app\home\controller;
use think\Db;

class Integral extends BaseAuth {    
    // 每日签到
    public function dailysignin(){
        $request = input('param.');
        $sitecode = $request['sitecode'];
        $userid = $this->userinfo['idmember'];
        $idsite = $this->siteid;

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'integral')){
            exit('没有权限,请先订购营销包！');
        }


        // 用户信息
        $userinfo = db('member')->where('idsite='.$idsite.' and  idmember='.$userid)->find();

        // 签到规则
        $sign_rule = db('integral_rule_config')->where('idsite',$idsite)->value("sign_rule");

        // 是否签到
        $signintime = db('member_sign_record')->where(["siteid"=>$idsite, "member_id" => $userid])->order("create_time desc")->value("create_time");
        $signintime = strtotime(date('Y-m-d', $signintime));
        $today = strtotime('today');
        if($signintime == $today){
            $is_signin = 1;
        }else{
            $is_signin = 0;
        }

        // 签到记录
        $member_sign_record = db('member_sign_record')->where(["siteid"=>$idsite, "member_id" => $userid])->order("create_time desc")->select();

        $roottpl = 'template/modules/';
        $url = $roottpl.'/signin/dailysignin.html';

        $this->assign("idsite",$idsite);
        $this->assign("sitecode",$sitecode);
        $this->assign("userinfo",$userinfo);
        $this->assign("signrule",$sign_rule);
        $this->assign("is_signin",$is_signin);
        $this->assign("member_sign_record",$member_sign_record);
        return $this->fetch($url);
    }

    // 获取签到记录
    public function getsigninrecord(){
        $request = input('param.');
        $userid = $this->userinfo['idmember'];
        $idsite = $this->siteid;

        $page = isset($request['page']) ? intval($request['page']) : 1;
        $pageSize = isset($request['pageSize']) ? intval($request['pageSize']) : 10;

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'integral')){
            exit('没有权限,请先订购营销包！');
        }
        
        // 总记录条数
        $totalRecord = db('member_sign_record')->where(["siteid"=>$idsite, "member_id" => $userid])->order("create_time desc")->count();
        // 签到记录
        $result = db('member_sign_record')->where(["siteid"=>$idsite, "member_id" => $userid])->order("create_time desc")->page($page,$pageSize)->select();
        // 总页数
        $totalPage = ceil($totalRecord/$pageSize);
        
        $retData = [
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRecord' => $totalRecord,
            'totalPage' => $totalPage,
            'data' =>  $result,
        ];

        return json($retData);
    }

    // 点击签到API
    public function membersignin(){
        $request = input('param.');
        $sitecode = $request['sitecode'];

        $userid = $this->userinfo['idmember'];
        $idsite = $this->siteid;

        $member_sign_record = db('member_sign_record')->where(["siteid"=>$idsite, "member_id" => $userid])->order("create_time desc")->find();

        $status = 0;  //签到状态，0:签到失败, 1:签到成功，2:今日已签到，3：签到是否开启
        $today = strtotime('today');
        $yesterday = strtotime('yesterday');
        $signintime = strtotime(date('Y-m-d', $member_sign_record['create_time']));  // 签到时间
        
        // 会员信息
        $member = db('member')->where('idmember',$userid)->find();

        // 获取配置信息
        $integral_rule_config = db('integral_rule_config')->where('idsite',$idsite)->find();
        $signin_integral = json_decode($integral_rule_config['signin_integral']);

        // 是否游客状态
        if($member['intstate'] == 2 || $member['intstate'] == 3){
            $status = 4;
        }
        // 签到是否开启
        else if($integral_rule_config['is_sign'] == 0){
            $status = 3;
        }else{
            // 是否已经签到
            if($signintime == $today){
                $status = 2;
            }else{
                Db::startTrans();
                try{
                    $record_integral = 0;  // 记录积分
                    
                    // 是否连续签到
                    if($yesterday == $signintime){
                        $continue_sign = $member['continue_sign'] + 1;
                        $integral = $member['integral'];

                        if($continue_sign == 2){
                            $integral += $signin_integral[1];
                            $record_integral = $signin_integral[1];
                        }else if($continue_sign >= 3){
                            $integral += $signin_integral[2];
                            $record_integral = $signin_integral[2];
                        }
                        
                        db('member')->where('idmember',$userid)->setInc(["integral" => $integral, "continue_sign" => $continue_sign]);
                    }else{
                        $continue_sign = 1;
                        $integral = $member['integral'] + $signin_integral[0];
                        $record_integral = $signin_integral[0];
                        db('member')->where('idmember',$userid)->setInc(["integral" => $integral, "continue_sign" => 1]);
                    }

                    // 增加签到记录
                    db('member_sign_record')->insert([
                        'siteid' => $idsite,
                        'member_id' => $userid,
                        'sign_integral' => $record_integral,
                        'sign_day' => $continue_sign,
                        'create_time' => time()
                    ]);

                    // 增加积分记录
                    db('member_integral_record')->insert([
                        'siteid' => $idsite,
                        'member_id' => $userid,
                        'userimg' => $member['userimg'],
                        'chrname' => $member['chrname'],
                        'nickname' => $member['nickname'],
                        'order_id' => 0,
                        'category_id' => 1,
                        'integral' => $record_integral,
                        'integral_rmark' => '',
                        'is_freeze' => 0,
                        'create_time' => time(),
                    ]);

                    // 发送客服消息
                    $msg = '签到成功，你获取到【'.$record_integral.'积分】<a href=\"'.request()->domain().'/'.$sitecode.'/integralrecord\">【查看详情】</a>';
                    send_ordinary_msg($sitecode, $member['openid'], $msg);

                    $status = 1;
                    Db::commit();
                }catch(Exception $e){
                    $status = 0;
                    Db::rollback();
                }
            }
        }

        return json(['qrcodeurl' => $this->qrcodeurl(), 'status' => $status]);
    }

    // 积分记录
    public function integralrecord(){
        $request = input('param.');
        $sitecode = $request['sitecode'];
        $userid = $this->userinfo['idmember'];
        $idsite = $this->siteid;

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'integral')){
            exit('没有权限,请先订购营销包！');
        }

        // 用户信息
        $userinfo = db('member')->where('idsite='.$idsite.' and  idmember='.$userid)->find();

        // 积分分类
        $member_integral_category = config('integral_category');

        // 积分记录
        $member_integral_record = db('member_integral_record')
            ->field("id, category_id, integral, create_time")
            ->where(['siteid'=>$idsite, 'member_id'=>$userid])
            ->select();

        $roottpl = 'template/modules/';
        $url = $roottpl.'/integral/integral_record.html';

        $this->assign("idsite",$idsite);
        $this->assign("sitecode",$sitecode);
        $this->assign("userinfo",$userinfo);
        $this->assign("member_integral_category", $member_integral_category);
        $this->assign("member_integral_record", $member_integral_record);
        return $this->fetch($url);
    }

    // 获取积分记录API
    public function getintegralrecord(){
        $request = input('param.');
        $idsite = $this->siteid;

        $userid = $this->userinfo['idmember'];
        $menu_type = $request['menu_type'];
        $page = isset($request['page']) ? intval($request['page']) : 1;
        $pageSize = isset($request['pageSize']) ? intval($request['pageSize']) : 10;
        $category_id = $request['category_id'];

        $map = ['siteid' => $idsite, 'member_id' => $userid];
        if($menu_type == 1){
            $map['category_id'] = ['in','1,2,3,6,7'];
        }else if($menu_type == 2){
            $map['category_id'] = ['in','4,5'];
        }
        if($category_id){
            $map['category_id'] = $category_id;
        }

        // 总条数
        $totalRecord = db('member_integral_record')->where($map)->count();

        // 积分记录
        $result = db('member_integral_record')
            ->field("id, category_id, integral, is_freeze, create_time")
            ->where($map)
            ->page($page, $pageSize)
            ->order('create_time desc')
            ->select();

        // 总页数
        $totalPage = ceil($totalRecord/$pageSize);
        
        $retData = [
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRecord' => $totalRecord,
            'totalPage' => $totalPage,
            'data' =>  $result,
        ];

        return json($retData);
    }

    // 积分商城
    public function integralmall(){
        $request = input('param.');
        $sitecode = $request['sitecode'];
        $userid = $this->userinfo['idmember'];
        $idsite = $this->siteid;
        $tabType = isset($request['tabType']) ? $request['tabType'] : 0;

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'integral')){
            exit('没有权限,请先订购营销包！');
        }

        // 用户信息
        $userinfo = db('member')->where('idsite='.$idsite.' and  idmember='.$userid)->find();

        $roottpl = 'template/modules/';
        $url = $roottpl.'/integral/integral_mall.html';
        $this->assign('idsite', $idsite);
        $this->assign("userinfo", $userinfo);
        $this->assign("sitecode", $sitecode);
        $this->assign("tabType", $tabType);
        return $this->fetch($url);
    }

    // 获取积分商品API
    public function getintegralmall(){
        $request = input('param.');
        $userid = $this->userinfo['idmember'];
        $idsite = $this->siteid;
        $tabType = isset($request['tabType']) ? intval($request['tabType']) : 0;
        $page = isset($request['page']) ? intval($request['page']) : 1;
        $pageSize = isset($request['pageSize']) ? intval($request['pageSize']) : 10;

        // 全部商品
        if($tabType == 0){
            $totalRecord = db('integral_mall_goods')->where(['siteid' => $idsite, 'is_display' => 1])->count();
            $result = db('integral_mall_goods')->where(['siteid' => $idsite, 'is_display' => 1])->page($page,$pageSize)->order('create_time desc')->select();
        }
        // 可兑换商品
        else if($tabType == 1){
            $integral = db('member')->where('idmember',$userid)->value("integral");
            $totalRecord = db('integral_mall_goods')->where(['siteid' => $idsite, 'goods_number'=>['gt',0], "integral" => ["<=",$integral], 'is_display' => 1])->count();
            $result = db('integral_mall_goods')->where(['siteid' => $idsite, 'goods_number'=>['gt',0], "integral" => ["<=",$integral], 'is_display' => 1])->page($page,$pageSize)->order('create_time desc')->select();
        }

        $totalPage = ceil($totalRecord/$pageSize);  // 总分页

        $retData = [
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRecord' => $totalRecord,
            'totalPage' => $totalPage,
            'data' => $result
        ];

        return json($retData);
    }

    // 获取兑换记录API
    public function getexchangerecord(){
        $request = input('param.');
        $page = isset($request['page']) ? intval($request['page']) : 1;
        $pageSize = isset($request['pageSize']) ? intval($request['pageSize']) : 10;

        $userid = $this->userinfo['idmember'];
        $idsite = $this->siteid;
        $field = 'id, order_no, goods_id, goods_name, goods_thumb, integral, exchange_number, order_status, courier_number, create_time';

        $totalRecord = db('integral_mall_exchange_record')->where(['siteid'=>$idsite, 'member_id'=>$userid])->count();

        $result = db('integral_mall_exchange_record')
            ->field($field)
            ->where(['siteid'=>$idsite, 'member_id'=>$userid])
            ->page($page,$pageSize)
            ->order('create_time desc')
            ->select();

        $totalPage = ceil($totalRecord/$pageSize);  // 总分页

        $retData = [
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRecord' => $totalRecord,
            'totalPage' => $totalPage,
            'data' => $result
        ];

        return json($retData);
    }

    // 积分详情
    public function integraldetail(){
        $request = input('param.');
        $sitecode = $request['sitecode'];
        $userid = $this->userinfo['idmember'];
        $idsite = $this->siteid;
        $roottpl = 'template/modules/';

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'integral')){
            exit('没有权限,请先订购营销包！');
        }

        // 用户信息
        $userinfo = db('member')->where('idsite='.$idsite.' and  idmember='.$userid)->find();

        // 商品详情
        $integral_mall_goods = db('integral_mall_goods')->where('id',$request['id'])->find();
        if(!$integral_mall_goods){
            exit('商品已经删除！');
        }
        $integral_mall_goods['goods_content'] = isset($integral_mall_goods['goods_content']) ? htmlspecialchars_decode($integral_mall_goods['goods_content']) : '';

        if($integral_mall_goods['is_display'] == 0){
            exit('商品已经下架！');
        }

        $url = $roottpl.'/integral/integral_detail.html';

        $this->assign("idsite",$idsite);
        $this->assign("userinfo",$userinfo);
        $this->assign("integral_mall_goods",$integral_mall_goods);
        $this->assign("sitecode",$sitecode);
        return $this->fetch($url);
    }

    // 我要兑换
    public function integralexchange(){
        $request = input('param.');
        $sitecode = $request['sitecode'];
        $idsite = $this->siteid;
        $roottpl = 'template/modules/';
        $url = $roottpl.'/integral/integral_exchange.html';
        $userid = $this->userinfo['idmember'];

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'integral')){
            exit('没有权限,请先订购营销包！');
        }

        // 用户信息
        $userinfo = db('member')->where('idsite='.$idsite.' and  idmember='.$userid)->find();

        // 商品详情
        $integral_mall_goods = db('integral_mall_goods')->where('id',$request['id'])->find();

        $this->assign("idsite",$idsite);
        $this->assign("userinfo",$userinfo);
        $this->assign("integral_mall_goods",$integral_mall_goods);
        $this->assign("sitecode",$sitecode);
        return $this->fetch($url);
    }

    // 积分兑换管理
    public function integralexchangemanage(){
        $request = input('param.');
        $order_status = isset($request['order_status']) ? intval($request['order_status']) : 0;
        $ipage = isset($request['ipage']) ? intval($request['ipage']) : 1;
        $keyword = isset($request['keyword']) ? $request['keyword'] : '';
        $sitecode = $request['sitecode'];
        $idsite = $this->siteid;
        $roottpl = 'template/modules/';

        $map = ['siteid' => $idsite];
        if($keyword){
            $map['order_no|consignee_name|consignee_phone'] = $keyword;
        }
        if($order_status != 3){
            $map['order_status'] = $order_status;
        }

        $total_record =  db('integral_mall_exchange_record')->where($map)->count();
        $datalist = db('integral_mall_exchange_record')->where($map)->page($ipage,$this->pageSize)->select();


        $url = $roottpl.'/integral/integral_exchange_manage.html';
        if (request()->isPost() && isset($request['ajax'])) {
            $url = $roottpl . '/integral/ajax_integral_exchange_manage.html';
        }
        
        $this->assign('total_page',ceil($total_record/$this->pageSize));
        $this->assign('order_status',$order_status);
        $this->assign('idsite',$idsite);
        $this->assign('sitecode', $sitecode);
        $this->assign('keyword',$keyword);
        $this->assign('roottpl', '/'.$roottpl);
        $this->assign("datalist",$datalist);
        return $this->fetch($url);
    }

    // 积分兑换管理详情
    public function integralexchangemanagedetail(){
        $request = input('param.');
        $sitecode = $request['sitecode'];
        $id = isset($request['id']) ? $request['id'] : 0;
        $idsite = $this->siteid;
        $roottpl = 'template/modules/';
        $url = $roottpl.'/integral/integral_exchange_manage_detail.html';

        $datainfo = db('integral_mall_exchange_record')->where('id',$id)->find();
        $datainfo['is_virtual'] = db('integral_mall_goods')->where('id',$datainfo['goods_id'])->value('is_virtual');

        $this->assign('idsite',$idsite);
        $this->assign('sitecode', $sitecode);
        $this->assign('roottpl', '/'.$roottpl);
        $this->assign('datainfo', $datainfo);
        return $this->fetch($url);
    }

    // 兑换提交
    public function exchangegoods(){
        $request = input('param.');
        $idsite = $this->siteid;
        $userid = $this->userinfo['idmember'];
        $goodsid = $request['id'];

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'integral')){
            exit('没有权限,请先订购营销包！');
        }

        // 用户信息
        $userinfo = db('member')->where('idsite='.$idsite.' and  idmember='.$userid)->find();
        $integral_mall_goods = db('integral_mall_goods')->where('id',$goodsid)->find();
        $total_integral = $request['exchange_number'] * $integral_mall_goods['integral'];
        $msg = '';

        if($request['exchange_number'] > $integral_mall_goods['goods_number']){
            $status = 3;
        }else if($userinfo['integral'] >= $total_integral){
            $order_no = getIntegralOrderSn();
            Db::startTrans();
            try{
                // 创建兑换订单
                $order_id = db('integral_mall_exchange_record')->insertGetId([
                    'siteid' => $idsite,
                    'member_id' => $userid,
                    'userimg' => $userinfo['userimg'],
                    'nickname' => $userinfo['nickname'],
                    'goods_id' => $goodsid,
                    'goods_name' => $integral_mall_goods['goods_name'],
                    'goods_thumb' => $integral_mall_goods['goods_thumb'],
                    'order_no' => $order_no,
                    'exchange_number' => $request['exchange_number'],
                    'integral' => $integral_mall_goods['integral'] * $request['exchange_number'],
                    'order_status' => 0,
                    'consignee_name' => $request['consignee_name'],
                    'consignee_phone' => $request['consignee_phone'],
                    'consignee_address' => $request['consignee_address'],
                    'create_time' => time(),
                ]);
                
                // 扣除积分
                db('member')->where('idmember',$userid)->setDec('integral',$total_integral);

                // 积分记录
                db('member_integral_record')->insert([
                    'siteid' => $idsite,
                    'member_id' => $userid,
                    'userimg' => $userinfo['userimg'],
                    'chrname' => $userinfo['chrname'],
                    'nickname' => $userinfo['nickname'],
                    'order_id' => 0,
                    'category_id' => 5,
                    'integral' =>  $integral_mall_goods['integral'] * $request['exchange_number'],
                    'integral_rmark' => '',
                    'is_freeze' => 0,
                    'create_time' => time(),
                ]);
                // 扣减库存
                db('integral_mall_goods')->where('id',$goodsid)->setDec('goods_number', $request['exchange_number']);
                
                // 增加已兑换数量
                db('integral_mall_goods')->where('id',$goodsid)->setInc('exchange_number', $request['exchange_number']);
                // 发送模版消息
                template_integral_exchange($integral_mall_goods['id'], $order_id);
                $status = 1;
                Db::commit();
            }catch(Exception $e){
                $msg = $e->getMessage();
                $status = 0;
                Db::rollback();
            }
        }else{
            $status = 2;
        }
        return json(['status' => $status,'msg'=>$msg]);
    }

    // 积分验证
    public function integralvalidation(){
        $request = input('param.');
        $userid = $this->userinfo['idmember'];
        $goodsid = $request['goods_id'];
        $goods_integral = $request['goods_integral'];
        $exchange_num = $request['exchange_num'];
        $total_integral = $exchange_num * $goods_integral;

        // 用户信息
        $integral = db('member')->where('idmember='.$userid)->value('integral');

        // 库存信息
        $goods_number = db('integral_mall_goods')->where('id',$goodsid)->value('goods_number');

        // 判断库存
        if($goods_number < $exchange_num){
            $status = 1;
        }
        // 判断积分
        else if($integral < $total_integral){
            $status = 2;
        }else{
            $status = 0;
        }

        return json(['status' => $status]);
    }
}