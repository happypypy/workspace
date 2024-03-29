<?php

namespace app\home\controller;

use think\Request;
use think\Log;

class CashCoupon extends BaseAuth {
    //活动详情领取优惠券的页面
    public function receive_cashed(){
        $request = Request::instance()->param();
        $id = $request['id']; // 当前活动id
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite = $this->siteid;

        //判断是否开启了现金券营销包
        if(!checkedMarketingPackage($idsite,'cashed')){
            return json(['success'=>false,'message'=>'优惠券功能还没开通！']);
        }
        $roottpl = 'template/modules/';

        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $api = new \think\wx\Api( array(
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ));

        $signPackage=$api->get_jsapi_config($url);
        $this->assign('signPackage',$signPackage);
        $bmflag=0;
        $userid = $this->userinfo['idmember'];
        //先查询产品的信息
        $activity_info=db("activity")->field('chrcontent',true)->where(array('idactivity'=>$id,'siteid'=>$idsite))->find();
        if(!$activity_info){
            return json(['success'=>false,'message'=>'该产品信息丢失']);
        }
        //查询该产品的现金券设置信息
        $activity_cashed = db("activity_cashed_card_set")->where(array('activity_id'=>$id,'is_receive_cashed'=>1))->find();
        if(!$activity_cashed){
            return json(['success'=>false,'message'=>'该产品的现金券设置信息丢失']);
        }
        //查询用户的信息
        $user_info = db("member")->field("dtbirthday",true)->where(array('idmember'=>$userid))->find();
        //看可领券的系统用户类型设置
        if($activity_cashed['receive_activity_cashed_intstate_user']){
            $receive_activity_cashed_intstate_user = explode(',',$activity_cashed['receive_activity_cashed_intstate_user']);
            //看用户的状态
            if(!in_array($user_info['intstate'],$receive_activity_cashed_intstate_user)){
                return json(['success'=>false,'message'=>'抱歉，您不符合领取条件']);
            }
        }
        //看可领券的自定义用户分类设置
        if($activity_cashed['receive_activity_cashed_categoryid']){
            $receive_activity_cashed_categoryid = explode(',',$activity_cashed['receive_activity_cashed_categoryid']);
            if(!in_array($user_info['categoryid'],$receive_activity_cashed_categoryid)){
                return json(['success'=>false,'message'=>'抱歉，您不符合领取条件']);
            }
        }
        //查询该用户对该产品的领取情况
        $user_receive_info = db('cashed_card_receive')->field('id')->where(['receive_member_id'=>$userid,'cashed_type'=>2,'receive_activity_id'=>$id,'site_id'=>$idsite])->find();
        if($user_receive_info){
            return json(['success'=>false,'message'=>'您好，您已经领取该现金券，本现金券每个用户只能领取一次，谢谢！']);
        }
        //进行封装添加领取记录的数据
        $add_receive_param['create_time'] = date('Y-m-d H:i:s',time());//领取时间
        $add_receive_param['receive_activity_id'] = $id;//领取产品id
        // 保证不会有重复领取编号存在
        while (true) {
            $receive_no = date('YmdHis') . rand(100000, 999999); // 订单编号
            $receive_no_count = db('cashed_card_receive')->where("receive_no = '$receive_no'")->count();
            if ($receive_no_count == 0)
                break;
        }
        $add_receive_param['receive_no'] = $receive_no;//领取编号
        $add_receive_param['cashed_type'] = 2;//现金券类型
        $add_receive_param['cashed_amount'] = $activity_cashed['activity_cashed_amount'];//现金券金额
        $add_receive_param['cashed_validity_time'] = date('Y-m-d H:i:s',strtotime(" + {$activity_cashed['activity_cashed_validity']} day",time()));//有效期时间
        $add_receive_param['cashed_validity_day'] = $activity_cashed['activity_cashed_validity'];//有效期天数
        $add_receive_param['receive_cashed_name'] = '产品专用现金券';//领取的现金券标题
        $add_receive_param['receive_activity_name'] = $activity_info['chrtitle'];//领取来源（产品名称）
        $add_receive_param['receive_member_id'] = $userid;//领取人会员id（用户）
        $add_receive_param['receive_nick_name'] = $user_info['nickname'];//领取人的昵称
        $add_receive_param['receive_header_image'] = $user_info['userimg'];//领取人的头像
        $add_receive_param['receive_source'] = 1;//领取渠道
        $add_receive_param['used_status'] = 1;//使用状态
        $add_receive_param['site_id'] = $idsite;//站点id
        //执行插入数据
        $bool = db('cashed_card_receive')->insert($add_receive_param);
        if($bool){
            return json(['success'=>true,'message'=>'领取成功，可前往报名页面直接使用']);
        }else{
            return json(['success'=>false,'message'=>'领取失败']);
        }
    }

    //付款后分享的页面
    public function share_cashed(){
        $request = Request::instance()->param();

        //支付过后页面会弹出自定义内容
        $content = 0;
        if(isset($request['jump']) && $request['jump'] == 1){
            $content=1;
        }
        $this->assign('pay_after',$content);

        $order_id = $request['order_id']; // 当前订单id
        $plan_id = $request['plan_id']; // 当前现金券计划id
        $userid = $this->userinfo['idmember'];
        $order_info = [];
        $activity_cashed = [];
        //查询用户的信息
        $user_info = db('member')->where(['idmember'=>$userid])->find();
        $sitecode=$request['sitecode'];
        $config = $this->wxConfig;
        $idsite=$config['id'];
        //判断是否开启了现金券营销包
        if(!checkedMarketingPackage($idsite,'cashed')){
            die('没有权限,请先订购营销包！');
        }
        $link_url = '';
        //如果是订单过来的分享
        if($order_id && $plan_id == 0){
            //如果账号的状态为游客的话,把该用户的昵称该为购买用户
            if($user_info['intstate'] == 3){
                db('member')->where(['idmember'=>$userid])->update(['nickname'=>'购买用户']);
            }
            //查询订单的信息
            $order_info = db("order")->where(array('id'=>$order_id,'fiduser'=>$userid))->find();
            if(!$order_info){
                echo '订单丢失';exit;
            }
            //查询该产品的现金券设置信息
            $activity_cashed = db("activity_cashed_card_set")->alias('a')->join('cms_cashed_plan p','a.cashed_plan_id=p.id')->where(array('a.activity_id'=>$order_info['dataid'],'a.is_share_cashed'=>1))->find();
            if(!$activity_cashed){
                echo '现金券计划信息丢失';exit;
            }
            //分享出去的链接
            $link_url =ROOTURL."/{$sitecode}/receiveshare/{$userid}/{$order_id}/0";
            //如果是管理员扫描进来
        }elseif ($plan_id && $order_id == 0){
            //查询该产品的现金券计划信息
            $activity_cashed = db("cashed_plan")->where(array('id'=>$plan_id,'is_open'=>1))->find();
            if(!$activity_cashed){
                echo '现金券计划信息丢失';exit;
            }
            //管理员分享出去的链接
            $link_url =ROOTURL."/{$sitecode}/receiveshare/{$userid}/0/{$plan_id}";
        }else{
            echo '请求参数错误';exit;
        }
        //查询用户的信息
        $user_info = db('member')->where(['idmember'=>$userid])->find();
        $roottpl = 'template/modules/';
        $api = new \think\wx\Api( array(
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ));
        //默认当前页面链接用来计算签名
        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $signPackage=$api->get_jsapi_config($url);
        $this->assign('signPackage',$signPackage);
        $this->assign('link_url',$link_url);//分享的链接
        //查询站点的信息
        $site_info = db('site_manage')->where(['id'=>$idsite])->find();
        $site_config_info = db('config_rule')->field('defaultval')->where(['idsite'=>$idsite,'fieldname'=>'weblogo'])->find();

        //获得栏目列表模版路径
        $url =$roottpl.'/cashed/share.html';


        $this->assign('roottpl','/'.$roottpl);
        $this->assign('order_info',$order_info);
        $this->assign('qrcodeurl',$this->qrcodeurl());
        $this->assign('idsite',$idsite);
        $this->assign('id',$order_id);
        $this->assign('plan_id',$plan_id);
        $this->assign('sitecode',$this->sitecode);
        $this->assign('activity_cashed',$activity_cashed);
        $this->assign('SelectFooterTab',2);
        $this->assign('user_info',$user_info);//用户信息
        $this->assign('site_info',$site_info);//站点信息
        $this->assign('site_config_info',$site_config_info);//站点配置信息
        $this->assign('root_url',ROOTURL);//站点配置信息
        $this->assign('appsecret',$signPackage['jsapi_ticket']);//公众号秘钥
        return $this->fetch($url);
    }

    //分享出去后领取现金券的页面
    public function receive_share(){
        $request = Request::instance()->param();
        //获取当前的订单id
        $order_id = $request['order_id']; // 当前订单id
        $plan_id = $request['plan_id']; // 当前现金券计划id
        //获取分享者的id
        $share_id = $request['user_id'];
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite=$config['id'];
        //判断是否开启了现金券营销包
        if(!checkedMarketingPackage($idsite,'cashed')){
            die('没有权限,请先订购营销包！');
        }
        $roottpl = 'template/modules/';
        //分享出去的链接
        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $api = new \think\wx\Api( array(
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ));

        $signPackage=$api->get_jsapi_config($url);
        $this->assign('signPackage',$signPackage);
        $userid = $this->userinfo['idmember'];
        //查询领取用户的信息
        $user_info = db('member')->where(['idmember'=>$userid])->find();
        //查询分享者的信息
        $share_info = db('member')->where(['idmember'=>$share_id])->find();
        $order_info = [];
        $tip_arr = [];//默认的提示信息
        $receive_cashed_list = [];//现金券领取列表
        //随机到领取的金额
        $amount = 0;
        //是否能领取
        $receive_cashed_count_bool = true;
        $activity_cashed = [];
        //如果是付款后的订单分享
        if($order_id && $plan_id == 0){
            //查询订单的信息
            $order_info = db("order")->where(array('id'=>$order_id,'fiduser'=>$share_id))->find();
            if($order_info){
                //判断该用户是否有领取过该订单分享的现金券
                $receive_cashed_info = db('cashed_card_receive')->field('id')->where(['receive_order_id'=>$order_id,'receive_member_id'=>$userid])->find();
                if(!$receive_cashed_info){
                    //查询该产品的现金券设置信息
                    $activity_cashed_card_set = db("activity_cashed_card_set")->field('cashed_plan_id')->where(['activity_id'=>$order_info['dataid']])->find();
                    if($activity_cashed_card_set){
                        $activity_cashed = db("cashed_plan")->where(array('id'=>$activity_cashed_card_set['cashed_plan_id']))->find();
                        if($activity_cashed){
                            $activity_cashed['activity_cashed_validity'] = $activity_cashed['cashed_validity_day'];
                            //获取到领取的金额
                            $amount = $this->get_prize($activity_cashed);
                            if($amount){
                                $amount = $amount /100;
                                //查询出该订单分享出去的领取份数
                                $receive_cashed_count = db('cashed_card_receive')->where(['receive_order_id'=>$order_id])->count();
                                //如果领取的总份数大于等于的话,别人不能再领取
                                if($receive_cashed_count >= $activity_cashed['cashed_num']){
                                    $receive_cashed_count_bool = false;
                                }
                            }else{
                                $tip_arr = ['code'=>5,'message'=>'未找到有效的现金券金额概率项!'];
                            }
                        }else{
                            $tip_arr = ['code'=>5,'message'=>'该产品的现金券计划丢失!'];
                        }
                    }else{
                        $tip_arr = ['code'=>5,'message'=>'未找到该产品的现金券设置信息!'];
                    }
                }else{
                    $tip_arr = ['code'=>1,'message'=>'您已领取'];
                }
            }else{
                $tip_arr = ['code'=>5,'message'=>'未找到有效的分享人订单!'];//数据类的提示
            }
            //否则就是机构管理员分享的
        }elseif ($plan_id && $order_id == 0){
            //判断该用户是否有领取过该现金券计划的分享的现金券
            $receive_cashed_info = db('cashed_card_receive')->field('id')->where(['share_plan_id'=>$plan_id,'receive_member_id'=>$userid,'receive_order_id'=>0])->find();
            if(!$receive_cashed_info) {
                //查询该产品的现金券计划信息
                $activity_cashed = db("cashed_plan")->where(array('id' => $plan_id))->find();
                if ($activity_cashed) {
                    //获取到领取的金额
                    $amount = $this->get_prize($activity_cashed);
                    if ($amount) {
                        $amount = $amount / 100;
                        //查询出该订单分享出去的领取份数
                        $receive_cashed_count = db('cashed_card_receive')->where(['share_plan_id' => $plan_id])->count();
                        //如果领取的总份数大于等于的话,别人不能再领取
                        if ($receive_cashed_count >= $activity_cashed['cashed_num']) {
                            $receive_cashed_count_bool = false;
                        }
                        $activity_cashed['activity_cashed_validity'] = $activity_cashed['cashed_validity_day'];
                    } else {
                        $tip_arr = ['code' => 5, 'message' => '未找到有效的现金券金额概率项!'];
                    }
                } else{
                $tip_arr = ['code'=>5,'message'=>'该产品的现金券计划丢失!'];
                }
            }else{
                $tip_arr = ['code'=>1,'message'=>'您已领取!'];
            }
        }
        if(!$receive_cashed_count_bool){
            $tip_arr = ['code'=>10,'message'=>'现金券已被领完'];
        }
        //如果有生成领取的金额并且是已关注的用户才可以领取
        if($amount != 0 && $user_info['intstate'] == 1 && $receive_cashed_count_bool == true && $activity_cashed){
            //生成领取现金券记录信息,进行封装添加领取记录的数据
            $add_receive_param['create_time'] = date('Y-m-d H:i:s',time());//领取时间
            $add_receive_param['receive_activity_id'] = $order_info?$order_info['dataid']:'';//领取产品id
            // 保证不会有重复领取编号存在
            while (true) {
                $receive_no = date('YmdHis') . rand(100000, 999999); // 订单编号
                $receive_no_count = db('cashed_card_receive')->where("receive_no = '$receive_no'")->count();
                if ($receive_no_count == 0)
                    break;
            }
            $add_receive_param['receive_no'] = $receive_no;//领取编号
            $add_receive_param['cashed_type'] = 1;//现金券类型为分享
            $add_receive_param['cashed_amount'] = $amount;//现金券金额
            $add_receive_param['cashed_validity_time'] = date('Y-m-d H:i:s',strtotime(" + {$activity_cashed['activity_cashed_validity']} day",time()));//有效期时间
            $add_receive_param['cashed_validity_day'] = $activity_cashed['activity_cashed_validity'];//有效期天数
            $add_receive_param['receive_cashed_name'] = $activity_cashed['plan_name'];//领取的现金券标题(计划名称)
            $add_receive_param['receive_activity_name'] = $order_info?$order_info['chrtitle']:'';//领取来源（产品名称）
            $add_receive_param['share_member_id'] = $share_id;//分享人id
            $add_receive_param['is_manage'] = $share_info['ismanage'];//是否是用户管理员
            $add_receive_param['share_nick_name'] = $share_info['nickname'];//分享人的昵称
            $add_receive_param['receive_member_id'] = $userid;//领取人会员id（用户）
            $add_receive_param['receive_nick_name'] = $user_info['nickname'];//领取人的昵称
            $add_receive_param['receive_header_image'] = $user_info['userimg'];//领取人的头像
            $add_receive_param['receive_source'] = 1;//领取渠道
            $add_receive_param['used_status'] = 1;//使用状态
            $add_receive_param['site_id'] = $idsite;//站点id
            $add_receive_param['receive_order_id'] = $order_info?$order_info['id']:'';//领取分享优惠券的订单id
            $add_receive_param['share_plan_id'] = $activity_cashed['id'];//分享现金券的计划id
            //执行插入数据
            $bool = db('cashed_card_receive')->insert($add_receive_param);
            if($bool){
                $tip_arr = ['code'=>1,'message'=>'领取成功'];
            }
        }
        $receive_cashed_one = [];//此次领取的数据
        if($order_id && $plan_id == 0){
            //查询现金券的领取记录
            $receive_cashed_list = db('cashed_card_receive')->where(['receive_order_id'=>$order_id,'share_member_id'=>$share_id])->order('create_time desc')->select();
            //此次领取的数据
            $receive_cashed_one = db('cashed_card_receive')->where(['receive_order_id'=>$order_id,'receive_member_id'=>$userid])->find();
        }elseif ($plan_id && $order_id == 0){
            //查询现金券的领取记录
            $receive_cashed_list = db('cashed_card_receive')->where(['share_plan_id'=>$plan_id,'share_member_id'=>$share_id,'receive_order_id'=>0])->order('create_time desc')->select();
            //此次领取的数据
            $receive_cashed_one = db('cashed_card_receive')->where(['share_plan_id'=>$plan_id,'receive_member_id'=>$userid,'receive_order_id'=>0])->find();
        }
        //查询站点的信息
        $site_info = db('site_manage')->where(['id'=>$idsite])->find();
        //获得栏目列表模版路径
        $url =$roottpl.'/cashed/receive_share.html';


        $this->assign('roottpl','/'.$roottpl);
        $this->assign('order_info',$order_info);
        $this->assign('qrcodeurl',$this->qrcodeurl());
        $this->assign('idsite',$idsite);
        $this->assign('id',$order_id);
        $this->assign('sitecode',$this->sitecode);
        $this->assign('receive_cashed_list',$receive_cashed_list);//现金券的领取记录
        $this->assign('receive_cashed_one',$receive_cashed_one);//此次领取的数据
        $this->assign('tip_arr',$tip_arr);//提示
        $this->assign('amount',$amount);//领取金额
        $this->assign('SelectFooterTab',2);
        $this->assign('user_info',$user_info);//用户信息
        $this->assign('share_info',$share_info);//分享用户信息
        $this->assign('site_info',$site_info);//站点信息
        $this->assign('root_url',ROOTURL);//站点配置信息
        return $this->fetch($url);
    }

    //限时优惠产品列表
    public function cashed_activity(){
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];

        $idsite = $this->siteid;
        //判断是否开启了现金券营销包
        if(!checkedMarketingPackage($idsite,'cashed')){
            die('没有权限,请先订购营销包！');
        }
        //现金券的类型
        $type = $request['type'];
        //现金券的产品id
        $activity_id = $request['activity_id'];

        if(isset($request['p'])){
            $ipage = $request['p'];
        }else{
            $ipage = 1;
        }
        $pagesize = 10;
        $search=[];
        //如果等于1的话,那么就是产品专用
        if($type == 2){
            $search['idactivity'] = $activity_id;
        }
        $search['siteid']= $idsite;
        $search['chkdown']=array('neq',1);
        $search['intflag'] = 2;
        $search['dtsignetime']=array('>',date('Y-m-d H:i:s',time()));
        $search['s.is_use_cashed']= 1;
        $offset = ($ipage - 1) * $pagesize;
        $show_field="idactivity,chrtitle,chrimg_m,chrimg,chrurl,chrsummary,dtstart,dtpublishtime,hits,s.is_receive_cashed,nodeid,min_price,ischarge";
        $node_info = [];
        $result = db('activity')->alias('a')->join('activity_cashed_card_set s','a.idactivity=s.activity_id','left')->where($search)->order("chkcontentlev desc,contentlevtime desc,dtpublishtime desc")->field($show_field)->limit($offset,$pagesize)->select();

        if($result) $node_info = db('node')->where('idsite='.$idsite.' and nodeid='.$result[0]['nodeid'])->find();
        //获得栏目列表模版路径
        $roottpl = 'template/modules/';
        $url =  $roottpl.'/activity/cashed_activity.html';
        if (Request::instance()->isPost() && isset($request['ajax'])) {
            // echo json_encode($result);exit;
            $url = $roottpl . '/activity/ajax_cashed_activity.html';
        }

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('pageSize',$pagesize);
        $this->assign('idsite',$idsite);
        $this->assign('result_data',$result);
        $this->assign('type',$type);
        $this->assign('activity_id',$activity_id);
        $this->assign('pageIndex',$ipage);
        $this->assign('sitecode',$sitecode);
        $this->assign('SelectFooterTab',2);
        $this->assign('node_info',$node_info);
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));

        return $this->fetch($url);
    }

    /**
     * 概率计算函数
     * @param $proArr
     * @return int|string
     */
    function get_rand($proArr) {
        $result = '';

        //概率数组的总概率精度
        $proSum = array_sum($proArr);

        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        //返回结果
        return $result;
    }

    /**
     * 配置奖项数组后并得到奖项
     * @param $activity_cashed
     * @return int|string
     */
    function get_prize($activity_cashed){
        //奖项数组
        $prize_arr = [];
        //如果有项目一概率设置
        if($activity_cashed['plan_probability1']){
            $plan_probability1 = explode(',',$activity_cashed['plan_probability1']);
            //随机金额到分
            $plan_probability1 = ['amount'=>mt_rand($plan_probability1[0] * 100,$plan_probability1[1] * 100),'rate'=>$plan_probability1[2]];
            //将金额追加到奖项数组中
            array_push($prize_arr,$plan_probability1);
        }
        //如果有项目二概率设置
        if($activity_cashed['plan_probability2']){
            $plan_probability2 = explode(',',$activity_cashed['plan_probability2']);
            //随机金额到分
            $plan_probability2 = ['amount'=>mt_rand($plan_probability2[0] * 100,$plan_probability2[1] * 100),'rate'=>$plan_probability2[2]];
            //将金额追加到奖项数组中
            array_push($prize_arr,$plan_probability2);
        }
        //如果有项目三概率设置
        if($activity_cashed['plan_probability3']){
            $plan_probability3 = explode(',',$activity_cashed['plan_probability3']);
            //随机金额到分
            $plan_probability3 = ['amount'=>mt_rand($plan_probability3[0] * 100,$plan_probability3[1] * 100),'rate'=>$plan_probability3[2]];
            //将金额追加到奖项数组中
            array_push($prize_arr,$plan_probability3);
        }
        $arr = [];
        //循环奖项
        foreach ($prize_arr as $key => $val) {
            $arr[$val['amount']] = $val['rate'];
        }
        //根据概率获取奖项值
        $amount = $this->get_rand($arr);
        return $amount;
    }

    /**
     * 我的现金券列表
     * @return mixed
     */
    public function my_cashed_list()
    {

        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        $idsite = $this->siteid;
        //判断是否开启了现金券营销包
        if(!checkedMarketingPackage($idsite,'cashed')){
            die('没有权限,请先订购营销包！');
        }
        $userid = $this->userinfo['idmember'];
        $ipage=empty($request['ipage'])?0:$request['ipage'];
        $state=1;//默认是未使用
        $where_arr=[];
        $where_arr['site_id']=$idsite;
        $where_arr['receive_member_id']=$userid;
        if(!empty($request['state'])){
            $where_arr['used_status'] = $request['state'];//状态
        }
        $time = date('Y-m-d H:i:s',time());
        //查询用户的可用现金券的列表
        $where['site_id'] = ['=',$idsite];
        $where['receive_member_id'] = ['=',$userid];
        $where['cashed_validity_time'] = ['<',$time];
        $where['used_status'] = ['=',1];
        //$where_arr['state']=$state;
        //查询所有未使用的过期的现金券
        $all_receive = db('cashed_card_receive')->field('is_manage',true)->where($where)->order("create_time desc")->select();
        //调用修改状态的方法
        update_used_status($all_receive);
        //查询数据
        $resule = db('cashed_card_receive')->where($where_arr)->order("create_time desc")->limit($ipage*$this->pageSize,$this->pageSize)->select();

        $roottpl = 'template/modules/';
        //获得栏目列表模版路径
        $url =$roottpl.'/mine/my_cashed_list.html';
        if (Request::instance()->isPost() && isset($request['ajax'])) {
            $url = $roottpl . '/mine/ajax_my_cashed_list.html';
        }

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('sitecode',$sitecode);
        $this->assign('list',$resule);
        $this->assign('idsite',$idsite);
        $this->assign('SelectFooterTab',1);
        $this->assign('order_state',config('order_state'));
        $this->assign('flag',$request['state']);
        $this->assign('order_paytype1',config('order_paytype1'));

        return $this->fetch($url);

    }
}