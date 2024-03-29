<?php

namespace app\home\controller;

use think\Request;
use think\Log;
use app\home\model\GroupBuy as GroupBuyModel;
use think\Session;


class GroupBuy extends BaseAuth {
    
    // 拼团列表
    public function groupBuyList()
    {
        //session_destroy();
        $request = Request::instance()->param();
        $page = isset($request['page']) ? intval($request['page']) : 1;
        //$pageSize = isset($request['pageSize']) ? intval($request['pageSize']) : 10;
        $sitecode=$request['sitecode'];
        if(empty($sitecode))
        {
            echo "站点不存在！";
            exit();
        }

        //确认用户已登陆
        $userid = $this->userinfo['idmember'];
        $config = $this->wxConfig;
        $idsite = $this->siteid;

        //如果有关键词
        if(Request::instance()->isGet() && isset($request['keyword'])){
            $keyword=$request['keyword'];
            Session::flash('keyword',$keyword);
            $groupBuys = GroupBuyModel::getList($idsite, $page, 4,$keyword);
        }else{
            $groupBuys = GroupBuyModel::getList($idsite, $page, 4);
        }
        //cache('config'.$request['idsite']);
        $roottpl = 'template/modules/';

        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $api = new \think\wx\Api( array(
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ));

        $signPackage=$api->get_jsapi_config($url);
        $this->assign('signPackage',$signPackage);
        $url = $roottpl . '/assemble/assemble_list.html';

        if (Request::instance()->isGet() && isset($request['ajax'])) {
            // echo json_encode($result);exit;
            $url = $roottpl . '/assemble/ajax_list.html';
        }

        // 获取拼团数据
        $this->assign('roottpl','/'.$roottpl);
        $this->assign('idsite',$idsite);
        $this->assign('groupBuys',$groupBuys);
        $this->assign('sitecode',$sitecode);
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));
        return $this->fetch($url);
    }

    // 拼团分享
    public function groupBuyShare()
    {
        $request = Request::instance()->param();

        //支付过后页面会弹出自定义内容
        $content = 0;
        if(isset($request['jump']) && $request['jump'] == 1){
            $content=1;
        }
        $this->assign('pay_after',$content);

        $userID = $this->userinfo['idmember'];

        $sharer = db('member')->find($request['sharer_id']);

        $orders = db('order')->where([
                'group_buy_order_id' => $request['group_buy_order_id'],
                // 4.已报名 已支付，
                // 5.已报名 退款中，
                // 6已部分退款 继续服务，
                // 7已退款 继续服务，
                // 8.已报名 退款不通过，
                'state' => ['in', [4,5,6,7,8,14]],
            ])
            ->order('dtpaytime asc')
            ->select();
        Log::info("此拼团的订单：".print_r($orders,true));
        $this->assign('pintuan',1);
        if(empty($orders))
        {
            $orders = db('order')->where([
                'group_buy_order_id' => $request['group_buy_order_id'],
                'state' => ['in', [12,9,10,11,13]],
            ])
                ->order('dtpaytime asc')
                ->select();
            //return false;
            $this->assign('pintuan',0);
        }

        $imgs1 = db('member')->where([
                'idmember' => ['in', array_column($orders, 'fiduser')]
            ])
            ->field(['idmember', 'nickname', 'userimg'])
            ->select();
        $tmp_user=[];
        foreach ($imgs1 as  $v){
            $tmp_user[$v['idmember']]=$v;
        }
        $imgs=[];
        foreach ($orders as $vo)
        {
            $_userid=$vo['fiduser'];
            if(array_key_exists($_userid,$tmp_user))
            {
                $imgs[]= $tmp_user[$_userid];
            }
        }

        Log::info("此拼团的用户头像：".print_r($imgs,true));
        $groupBuyOrder = db('group_buy_order')->find($request['group_buy_order_id']);
        $package = db('package')->find($orders[0]['package_id']);
        $activity = db('activity')->find($package['activity_id']);
        $data = [
            'isStarter' => $userID == $orders[0]['fiduser'],
            'isNew' => !in_array($userID, array_column($orders, 'fiduser')),
            'expiration' => $groupBuyOrder['expire_at'],
            'chrtitle' => $orders[0]['chrtitle'],
            'chrimg_m'=>$activity['chrimg_m'],
            'original_price' => $package['member_price'],
            'group_buy_price' => $orders[0]['price'] / $orders[0]['paynum'],
            'package_name' => $package['keyword1'] . ' ' . $package['keyword2'],
            'group_num' => $groupBuyOrder['group_num'],
            'left' => $groupBuyOrder['group_num'] - $groupBuyOrder['sold'],
            'imgs' => $imgs,
            'state' => $groupBuyOrder['state'],
            'username' => $sharer['nickname'],
            'userimg'=> $sharer['userimg']
        ];


        $groupBuy = db('group_buy')->find($groupBuyOrder['group_buy_id']);

        $config = $this->wxConfig;
        $idsite=$config['id'];
        $roottpl = 'template/modules/';

        $shareUrl = ROOTURL . "/{$request['sitecode']}/group_buy_share/{$request['group_buy_order_id']}/{$request['sharer_id']}";
        $view = 'join';
        if(!$data['isNew'])
        {
            $api = new \think\wx\Api( array(
                'appId' => trim($config['appid']),
                'appSecret'    => trim($config['appsecret']),
            ));
            $signPackage=$api->get_jsapi_config($shareUrl);
            $this->assign('signPackage',$signPackage);
            $view = 'share';
        }
        $url = $roottpl.'/assemble/assemble_' . $view . '.html';
        // var_dump($view);die;
        //分享的图片
        if($data['chrimg_m'] == ''){
            $data['chrimg_m']='/static/images/grouppic/'.mt_rand(1,4).".jpg";
        }
        //获得栏目列表模版路径
        $this->assign('data', $data);
        $this->assign('idsite', $idsite);
        $this->assign('shareUrl', $shareUrl);
        $this->assign('groupBuy', $groupBuy);
        $this->assign('sitecode', $request['sitecode']);
        $this->assign('roottpl', '/' . $roottpl);
        $this->assign('groupBuyOrderId', $request['group_buy_order_id']);
        $this->assign('groupBuyId', $groupBuyOrder['group_buy_id']);
        $this->assign('activityId', $package['activity_id']);
        $this->assign('root_url',ROOTURL);
        return $this->fetch($url);
    }
}