<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/3
 * Time: 9:37
 */

namespace app\home\controller;
use think\Exception;
use think\Request;
use think\captcha\Captcha;
use think\Url;
use think\Log;
use think\Verify;
use think\Db;
use PHPExcel_IOFactory;
use PHPExcel;
use think\wx\Utils\SHA1;
use app\admin\module\Common;
use app\home\model\Package;
use app\home\model\GroupBuyOrder;
use app\home\model\GroupBuy;
use think\db\Query;

class Index extends Base {

    private $PageSize=5;

    public function reg()
    {
        $request = Request::instance()->param();
        if (Request::instance()->isPost())
        {
            $msg=[];
            $msg['state']=0;
            $msg['msg']="";

            $companyname=$request['companyname'];
            $Contacts=$request['Contacts'];
            $Contactstel=$request['Contactstel'];
            $txtICode=$request['txtICode'];
            $verify = new Verify();

            if($companyname=="")
            {
                $msg['msg']="企业名称不能为空";
            }
            else if($Contacts=="")
            {
                $msg['msg']="联系人不能为空";
            }
            else if($Contactstel=="")
            {
                $msg['msg']="联系电话不能为空";
            }
            else if($txtICode=="")
            {
                $msg['msg']="验证码不能为空";
            }
            else if(!$verify->check($txtICode, "loginsn"))
            {
                $msg['msg']="验证码不正确";
            }
            else
            {
                $result = db('reg')->where(array("contactstel"=>$Contactstel))->find();
                if($result)
                {
                    $msg['msg']="联系电话已登记，不能重复登记。";
                }
            }


            if($msg['msg']!="")
            {
                exit(json_encode($msg));
            }


            $arr=[];
            $arr["companyname"]=$companyname;
            $arr["contacts"]=$Contacts;
            $arr["contactstel"]=$Contactstel;
            $arr["cratetime"]=date("Y-m-d H:i:s",time());
            $result = db('reg')->insert($arr);
            if($result)
            {
                $msg['state']=1;
            }
            else
            {
                $msg['msg']="信息保存失败";
            }
            exit(json_encode($msg));
        }
        return $this->fetch('template/reg.html');
    }
    
    public function test()
    {
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));

        $api = new \think\wx\Api(
            array(
                'appId' => trim($config['appid']),
                'appSecret'    => trim($config['appsecret']),
            )
        );
        $imgurl= $api->get_qrcode_url_by_str('213556489798798798798');
        $template_url="template/weixin.html";
        $this->assign('msg',"本活动要求关注后才能报名<br>请扫下面二维码关注");
        $this->assign('imgurl',$imgurl);
        return $this->fetch($template_url);

    }
    public function qrcodeurl()
    {
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));

        $api = new \think\wx\Api(
            array(
                'appId' => trim($config['appid']),
                'appSecret'    => trim($config['appsecret']),
            )
        );
        $imgurl= $api->get_qrcode_url();
        return $imgurl;
    }
    public function sendmsg()
    {


        /*
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        // api模块 - 包含各种系统主动发起的功能
        $api = new \think\wx\Api(
            array(
                'appId' => trim($config['appid']),
                'appSecret'    => trim($config['appsecret']),
            )
        );

        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $signPackage=$api->get_jsapi_config($url);

        $idsite=$config['id'];
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        $url = $roottpl.'/order/s.html';
        $this->assign('sitecode',$sitecode);
        $this->assign('roottpl',$roottpl);
        $this->assign('signPackage',$signPackage);

        return $this->fetch($url);
        */

        /*
        $request = Request::instance()->param();
        $id=$request['id'];

        $s=template_tg($id);
        //$s= send_msg($id,"tongxiang");
       print_r($s);
        */
    }
    //首页
    public function index(){
        //session_destroy();
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        if(empty($sitecode))
        {
            echo "站点不存在！";
            exit();
        }
        //确认用户已登陆
        $this->setuserinfo(ROOTURL.url("/".$sitecode));

        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        //cache('config'.$request['idsite']);
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);

        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $api = new \think\wx\Api( array(
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ));

        $signPackage=$api->get_jsapi_config($url);
        $this->assign('signPackage',$signPackage);

        if(empty($node_info['templateofnodeindex'])){
            $url = $roottpl.'/'.GetConfigVal("weboption","indexfilename",$idsite); //模版路径
        }else{
            $url = $roottpl.'/'.$node_info['templateofnodeindex'];
        }

        // 签到是否开启
        $is_sign = 0;
        if(checkedMarketingPackage($idsite,'integral')){
            $is_sign = db('integral_rule_config')->where('idsite',$idsite)->value('is_sign');
        }

        // 获取首页拼团数据
        $groupBuys = GroupBuy::getList($idsite);

        //访问记录
        $this->loginCount();

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('idsite',$idsite);
        $this->assign('groupBuys',$groupBuys);
        $this->assign('sitecode',$sitecode);
        $this->assign('SelectFooterTab',1);
        $this->assign('is_sign',$is_sign);
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));
        return $this->fetch($url);
    }

    public function signin()
    {
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $this->assign('state',"2");
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        $checkcode=empty($request['checkcode'])?"":$request['checkcode'];
        $id=empty($request['id'])?"":$request['id'];
        $token=$config['token'];
        $strkey=empty($request['key'])?"":$request['key'];
        $flag=empty($request['flag'])?"1":$request['flag'];




        $msg="";
        $row=[];
        $frmdata=[];
        $frmdatasub=[];

        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        $this->assign('roottpl','/'.$roottpl);
        $url = $roottpl.'/mine/signin.html';

        //确认用户已登陆
        if($this->setuserinfo(ROOTURL.url("/".$sitecode."/signin"))==false)
        {
            $msg="请联系管理，配置好公众号和平台绑定";
        }

        $userid=$this->getUserInfo('userid');
        if(empty($userid) || $userid<1)
        {
            $msg="管理员账号不存在，请确认后重新签到。";
        }

        if( $msg=="")
        {
            $userinfo=db("member")->where(array("idmember"=>$userid,'idsite'=>$idsite))->find();
        }


        if (Request::instance()->isPost() && !empty($strkey) && !empty($id) && $msg=="")
        {

            if($strkey!= md5($token.$id))
            {
                exit(json_encode(array("state"=>2,"msg"=>"数据验证不通！")));
            }

            if(empty($userinfo))
            {
                exit(json_encode(array("state"=>2,"msg"=>"管理员账号不存在，请确认后重新签到！！")));
            }
            if($userinfo['ismanage']!=1)
            {
                exit(json_encode(array("state"=>2,"msg"=>"你不是管理，不能为用户签到！")));
            }

            $arr=[];
            $arr["issign"]="1";
            $arr["singntype"]=$flag;//1扫码签到，2输码签到，3电脑签到
            $arr["signuserid"]=$userinfo['idmember'];
            $arr["signusername"]=$userinfo['nickname'];
            $arr["dtsigntime"]=date("Y-m-d H:i:s",time());
            $result = db('order')->where(array('ordersn'=>$id,'idsite'=>$idsite))->update($arr);
            if($result)
            {
                //获取订单信息
                $order = db('order')->where(array('ordersn'=>$id,'idsite'=>$idsite))->find();
                $replace = [];
                //给客户和商务发短信通知    类型：10--签到
                sysSendMsg($idsite, 10, $order, $replace);
                exit(json_encode(array("state"=>1,"msg"=>"签到成功！")));
            }
            else
            {
                exit(json_encode(array("state"=>2,"msg"=>"签到失败！")));
            }
        }

        $strkey="";
        if($msg=="") {
            if (empty($userinfo)) {
                $msg = "管理员账号不存在，请确认后重新签到！";
            } else {
                if ($userinfo['ismanage'] != 1)
                    $msg = "你不是管理，不能为用户签到！";
            }
        }
        if((!empty($id) || !empty($checkcode)) && $msg=="")
        {
            if(empty($checkcode))
            {
                $row = db('order')->where(array('ordersn'=>$id,'idsite'=>$idsite))->find();
            }
            else
            {
                $row = db('order')->where(array('checkcode'=>$checkcode,'idsite'=>$idsite))->find();
            }

            if($row)
            {
                if(!empty($row['txtdata']))
                {
                    $row2= explode("☆", $row['txtfield']);
                    $row1= explode("☆", $row['txtdata']);
                    foreach($row1 as $k=>$vo) {
                        $arr = explode("∫", $row2[$k]);
                        $datatype = 1;
                        $datafield = $arr[0];
                        if (count($arr) > 1) {
                            $datatype = $arr[1];
                        }
                        if($datatype==7)
                        {
                            $frmdata[$datafield]='<img src="'.$vo.'" style="border: 0px;height: 50px;padding: 1px;"  />';
                        }
                        else
                        {
                            $frmdata[$datafield]=$vo;
                        }

                    }
                }
                if(!empty($row['txtdata1'])) {

                    $row2 = explode("☆", $row['txtfield1']);

                    $frmdatasubRow = explode("§", $row['txtdata1']);

                    foreach ($frmdatasubRow as $k1 => $vo) {
                        $row1 = explode("☆", $vo);
                        $frmdata1 = [];
                        foreach ($row1 as $k => $vo) {
                            $arr = explode("∫", $row2[$k]);
                            $datatype = 1;
                            $datafield = $arr[0];
                            if (count($arr) > 1) {
                                $datatype = $arr[1];
                            }
                            if ($datatype == 7) {
                                $frmdata1[$datafield] = '<img src="' . $vo . '" style="border: 0px;height: 50px;padding: 1px;"  />';
                            } else {
                                $frmdata1[$datafield] = $vo;
                            }
                        }
                        $frmdatasub[] = $frmdata1;

                    }
                }
                $strkey= md5($token.$row['ordersn']);
            }
            else
            {
                $msg="没有找到相关订单数据";
            }
        }


        $url1=ROOTURL.$_SERVER['REQUEST_URI'];
        $api = new \think\wx\Api( array(
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ));

        $signPackage=$api->get_jsapi_config($url1);
        $this->assign('signPackage',$signPackage);

        $this->assign('idsite',$idsite);
        $this->assign('sitecode',$sitecode);
        $this->assign('flag',$flag);
        $this->assign('roottpl','/'.$roottpl);
        $this->assign('orderinfo',$row);
        $this->assign('key',$strkey);
        $this->assign('frmdatasub',$frmdatasub);
        $this->assign('frmdata',$frmdata);
        $this->assign('msg',$msg);
        $this->assign('order_state',config('order_state'));
        $this->assign('order_paytype1',config('order_paytype1'));
        return $this->fetch($url);

    }

    public function usermodi()
    {
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];

        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        $url = $roottpl.'/mine/usermodi.html';

        $userid=$this->getUserInfo('userid');
        if (Request::instance()->isPost())
        {
            $data=[];
            $data['chraccount']=$request['chraccount'];
            $data['chrname']=$request['chrname'];
            $data['nickname']=$request['nickname'];
            $data['chrtel']=$request['chrtel'];
            $data['chrmail']=$request['chrmail'];
            $bool=db('member')->where(array('idmember'=>$userid,'idsite'=>$idsite))->update($data);
            if($bool)
                echo "1";
            else
                echo "0";
            exit();
        }

        $row=db('member')->where(array('idmember'=>$userid,'idsite'=>$idsite))->find();

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('idsite',$idsite);
        $this->assign('userinfo',$row);
        $this->assign('sitecode',$sitecode);
        $this->assign('SelectFooterTab',1);
        return $this->fetch($url);

    }

    // 每日签到
    public function dailysignin(){
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        $config = $this->getWeiXinConfig(strtolower($sitecode));
        $idsite = $config['id'];
        $userid = $this->getUserInfo('userid');

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

        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
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
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        $userid = $this->getUserInfo('userid');
        $config = $this->getWeiXinConfig(strtolower($sitecode));
        $idsite = $config['id'];

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
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];

        $config = $this->getWeiXinConfig(strtolower($sitecode));
        $idsite = $config['id'];

        $userid = $this->getUserInfo('userid');

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
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        $config = $this->getWeiXinConfig(strtolower($sitecode));
        $idsite = $config['id'];
        $userid = $this->getUserInfo('userid');

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

        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
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
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        $config = $this->getWeiXinConfig(strtolower($sitecode));
        $idsite = $config['id'];

        $userid = $this->getUserInfo('userid');
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
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        $config = $this->getWeiXinConfig(strtolower($sitecode));
        $idsite = $config['id'];
        $userid = $this->getUserInfo('userid');
        $tabType = isset($request['tabType']) ? $request['tabType'] : 0;

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'integral')){
            exit('没有权限,请先订购营销包！');
        }

        // 用户信息
        $userinfo = db('member')->where('idsite='.$idsite.' and  idmember='.$userid)->find();

        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        $url = $roottpl.'/integral/integral_mall.html';
        $this->assign('idsite', $idsite);
        $this->assign("userinfo", $userinfo);
        $this->assign("sitecode", $sitecode);
        $this->assign("tabType", $tabType);
        return $this->fetch($url);
    }

    // 获取积分商品API
    public function getintegralmall(){
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        $config = $this->getWeiXinConfig(strtolower($sitecode));
        $userid = $this->getUserInfo('userid');
        $idsite = $config['id'];
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
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        $page = isset($request['page']) ? intval($request['page']) : 1;
        $pageSize = isset($request['pageSize']) ? intval($request['pageSize']) : 10;

        $config = $this->getWeiXinConfig(strtolower($sitecode));
        $userid = $this->getUserInfo('userid');
        $idsite = $config['id'];
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
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        $config = $this->getWeiXinConfig(strtolower($sitecode));
        $idsite = $config['id'];
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        $userid = $this->getUserInfo('userid');

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
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        $config = $this->getWeiXinConfig(strtolower($sitecode));
        $idsite = $config['id'];
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        $url = $roottpl.'/integral/integral_exchange.html';
        $userid = $this->getUserInfo('userid');

        // 营销包是否订购
        if(!checkedMarketingPackage($idsite,'integral')){
            exit('没有权限,请先订购营销包！');
        }

        // 用户信息
        $userinfo = db('member')->where('idsite='.$idsite.' and  idmember='.$userid)->find();

        // 商品详情
        $integral_mall_goods = db('integral_mall_goods')->where('id',$request['id'])->find();
        if(Request::instance()->isPost()){

        }

        $this->assign("idsite",$idsite);
        $this->assign("userinfo",$userinfo);
        $this->assign("integral_mall_goods",$integral_mall_goods);
        $this->assign("sitecode",$sitecode);
        return $this->fetch($url);
    }

    // 兑换提交
    public function exchangegoods(){
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        $config = $this->getWeiXinConfig(strtolower($sitecode));
        $idsite = $config['id'];
        $userid = $this->getUserInfo('userid');
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
        $request = Request::instance()->param();
        $userid = $this->getUserInfo('userid');
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

    // 机构二维码
    public function siteqrcode(){
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        $config = $this->getWeiXinConfig(strtolower($sitecode));
        $idsite = $config['id'];
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);

        $api = new \think\wx\Api([
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ]);

        // 生成二维码
        $site_qrcode_url = cache("site_".$idsite."_qrcode_url");
        if(!$site_qrcode_url){
            $qrocde  = $api->create_qrcode($idsite)[1];
            if($qrocde){
                $ticket = $qrocde->ticket;
                $qrocde_url = $api->get_qrcode_url($ticket);
                cache("site_".$idsite."_qrcode_url",$qrocde_url);
            }
        }
        
        $url = $roottpl.'/mine/siteqrcode.html';
        $this->assign("site_qrocde_url", $site_qrcode_url);
        return $this->fetch($url);
    }

    //内容列表页面
    public function lists(){
        $request = Request::instance()->param();
        //检测是否需要登录才能浏览
        //if(strstr($node_info['option'],'3')){
        //    return "请登录";
        //}
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];

        if(isset($request['p'])){
            $ipage = $request['p'];
        }else{
            $ipage = 1;
        }
        $pagesize = 10;


        $node_info = db('node')->where('idsite='.$idsite.' and nodeid='.$request['nodeid'])->find();
        if(empty($node_info))
        {
            header("location:/error.php?msg=".urlencode("栏目不存在，有疑问请和管理联系！")."&url=".urlencode("/".$sitecode));
            exit();
        }


        //获得栏目列表模版路径
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        if(empty($node_info['templateofnodelist'])){
            $url =  $roottpl.'/'.GetConfigVal("weboption","columnlist",$idsite); //模版路径
        }else{
            $url =  $roottpl.'/'.$node_info['templateofnodelist'];
        }

        if (Request::instance()->isPost() && isset($request['ajax'])) {
            // echo json_encode($request);exit;
            $url = $roottpl . '/node/ajax_index_list.html';
        }
        $this->assign('roottpl','/'.$roottpl);
        $this->assign('nodeid',$request['nodeid']);
        $this->assign('node_info',$node_info);
        $this->assign('pageSize',$pagesize);
        $this->assign('idsite',$idsite);
        $this->assign('pageIndex',$ipage);
        $this->assign('sitecode',$sitecode);
        $this->assign('SelectFooterTab',1);
        return $this->fetch($url);
    }

    //活动
    public function activity(){
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];

        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        $nodeid=$request['nodeid'];
        $typeid=isset($request['typeid'])?$request['typeid']:"";
        $tagid=isset($request['tagid'])?$request['tagid']:"";

        $intflag=isset($request['intflag'])?$request['intflag']:"1";

        if(isset($request['p'])){
            $ipage = $request['p'];
        }else{
            $ipage = 1;
        }
        $pagesize = 10;

        $node_info = db('node')->where('idsite='.$idsite.' and nodeid='.$request['nodeid'])->find();
        if(empty($node_info))
        {
            header("location:/error.php?msg=".urlencode("栏目不存在，有疑问请和管理联系！")."&url=".urlencode("/".$sitecode));
            exit();
        }

        $search=[];
        $search['siteid']= $idsite;
        $search['chkdown']=array('neq',1);
        $search['intflag'] = 2;
        $search['nodeid'] = $nodeid;

        if(!empty($typeid)) $search['fidtype']=$typeid;
        if(!empty($tagid)) $search['chrtags']= array('like', "%,".$tagid.",%");
        if($intflag=="1")
        {
            $search['dtsignetime']=array('>',date('Y-m-d H:i:s',time()));
        }
        else if($intflag=="2")
        {
            $search['dtsignetime']=array('<',date('Y-m-d H:i:s',time()));
        }
        $offset = ($ipage - 1) * $pagesize;
        $show_field="idactivity,chrtitle,chrimg_m,chrimg,chrurl,chrsummary,dtstart,dtpublishtime,hits,s.is_receive_cashed,a.usertype,a.idactivity";
        $result = db('activity')->alias('a')->join('activity_cashed_card_set s','a.idactivity=s.activity_id','left')->where($search)->order("chkcontentlev desc,contentlevtime desc,dtpublishtime desc")->field($show_field)->limit($offset,$pagesize)->select();
        //dump($result);
        //获得栏目列表模版路径
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        $url =  $roottpl.'/activity/index.html';
        if (Request::instance()->isPost() && isset($request['ajax'])) {
            // echo json_encode($result);exit;
            $url = $roottpl . '/activity/ajax_index_list.html';
        }

        $obj = new \app\admin\module\activity($idsite);

        $hdfl=$obj->getDic("hdfl");
        $this->assign('hdfl',$hdfl);
        $hdbq=$obj->getDic("hdbq");
        $this->assign('hdbq',$hdbq);
        $this->assign('roottpl','/'.$roottpl);
        $this->assign('typeid',$typeid);
        $this->assign('tagid',$tagid);
        $this->assign('intflag',$intflag);
        $this->assign('pageSize',$pagesize);
        $this->assign('node_info',$node_info);
        $this->assign('idsite',$idsite);
        $this->assign('result_data',$result);
        $this->assign('pageIndex',$ipage);
        $this->assign('sitecode',$sitecode);
        $this->assign('SelectFooterTab',2);
        $this->assign('nodeid',$nodeid);
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));



        return $this->fetch($url);
    }

//    private function  getUserTypeName($db_type,$value)
//    {
//        $tmp="";
//        if(empty($value))
//            return $tmp;
//        $arr=explode(',',$value);
//        if(empty($arr))
//            return $tmp;
//
//        foreach ( $arr as $vo) {
//            if(!empty($vo) && array_key_exists($vo,$db_type))
//            {
//                $tmp=$tmp.",".$db_type[$vo];
//            }
//        }
//        $tmp=trim($tmp,',');
//        return $tmp;
//    }
    //限时优惠活动列表
    public function cashed_activity(){
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];

        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        //判断是否开启了现金券营销包
        if(!checkedMarketingPackage($idsite,'cashed')){
            die('没有权限,请先订购营销包！');
        }
        //现金券的类型
        $type = $request['type'];
        //现金券的活动id
        $activity_id = $request['activity_id'];

        if(isset($request['p'])){
            $ipage = $request['p'];
        }else{
            $ipage = 1;
        }
        $pagesize = 10;
        $search=[];
        //如果等于1的话,那么就是活动专用
        if($type == 2){
            $search['idactivity'] = $activity_id;
        }
        $search['siteid']= $idsite;
        $search['chkdown']=array('neq',1);
        $search['intflag'] = 2;
        $search['dtsignetime']=array('>',date('Y-m-d H:i:s',time()));
        $search['s.is_use_cashed']= 1;
        $offset = ($ipage - 1) * $pagesize;
        $show_field="idactivity,chrtitle,chrimg_m,chrimg,chrurl,chrsummary,dtstart,dtpublishtime,hits,s.is_receive_cashed";
        $result = db('activity')->alias('a')->join('activity_cashed_card_set s','a.idactivity=s.activity_id','left')->where($search)->order("chkcontentlev desc,contentlevtime desc,dtpublishtime desc")->field($show_field)->limit($offset,$pagesize)->select();
//        var_dump($result);exit;
        //获得栏目列表模版路径
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
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
//        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));

        return $this->fetch($url);
    }

    //活动详情页面
    public function detail(){
        $request = Request::instance()->param();
        $id = $request['id']; // 当前内容id
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];


        if(empty( $request['type'])) {
            //确认用户已登陆
            // if ($this->setuserinfo(ROOTURL . url("/" . $sitecode . "/detail/" . $id)) == false) {
                // echo "你没有登陆，或没配置好公众号和平台绑定。";
                // exit();
            // }
        }
        //检测它的父节点是否需要登录才能浏览
        //$content_info = db('activity')->where('idactivity='.$id)->field('siteid')->find();

        //$comment_list = db('comment')->where('id='.$id.' and intlock=1')->select();
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);

        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $api = new \think\wx\Api( array(
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ));
       
        $signPackage=$api->get_jsapi_config($url);
        $this->assign('signPackage',$signPackage);
        $bmflag=0;
        $userid=$this->getUserInfo("userid");
            // if(!empty( $request['type'])) {
            $activity = $datainfo=db("activity")->field("chkissubscribe, idactivity,usertype")->where(array('idactivity'=>$id))->find();
            if($datainfo["chkissubscribe"]==1)      //需要关注
            {
                if(empty($userid))
                {
                    $bmflag=1;
                }else
                {
                    $datainfo=db("member")->field("intstate")->where(array('idmember'=>$userid,'intstate'=>1))->find();
                    if(empty($datainfo))
                    {
                        $bmflag=2;
                    }
                }
            }
            //查询该活动的现金券设置信息
        $activity_cashed = db("activity_cashed_card_set")->where(array('activity_id'=>$id,'is_receive_cashed'=>1))->find();

        // 查询活动中的拼团
        $packages = db('package')->where([
                'activity_id' => $activity['idactivity']
            ])
            ->field([
                'package_id',
                'member_price',
                'concat_ws(" ", keyword1, keyword2) package_name'
            ])
            ->select();
        $groupBuys = [];
        $groupBuyOrders = [];
        $now = time();
        // 已成团数量
        foreach ($packages as $key => $package)
        {
            $tmpGroupBuys = db('group_buy')->where([
                    'package_id' => $package['package_id'],
                    'state' => 1,
                    'start_at' => ['elt', $now],
                    'end_at' => ['egt', $now],
                ])
                ->field([
                    'group_buy_id',
                    'group_num',
                    'group_buy_price',
                    'end_at - ' . $now . ' expiration',
                    '\'' . $package['member_price'] . '\'' . ' member_price',
                    '\'' . $package['package_name'] . '\'' . ' package_name'
                ])
                ->select();
            $groupBuys = array_merge($groupBuys, $tmpGroupBuys);

            // 获取已开的拼团
            $groupBuyIds = array_column($tmpGroupBuys, 'group_buy_id');
            if(!$groupBuyIds)
            {
                continue;
            }

            $query = new Query;
            $tmpGroupBuyOrders = db('group_buy_order')->where([
                    'group_buy_id' => ['in', $groupBuyIds],
                    'state' => 1,
                    'group_num' => ['gt', $query->raw('sold')],
                    'expire_at' => ['egt', time()]
                ])
                ->field([
                    'group_num - sold left_num',
                    'group_buy_order_id',
                    'group_buy_id',
                    '\'' . $package['package_name'] . '\'' . ' package_name'
                ])
                ->select();
            // 获取团长信息
            foreach ($tmpGroupBuyOrders as $key => $tmpGroupBuyOrder)
            {
                $firstOrder = db('order')->where([
                        'group_buy_order_id' => $tmpGroupBuyOrder['group_buy_order_id'],
                        // 4.已报名 已支付，
                        // 5.已报名 退款中，
                        // 6已部分退款 继续服务，
                        // 7已退款 继续服务，
                        // 8.已报名 退款不通过，
                        'state' => ['in', [4,5,6,7,8]]
                    ])
                    ->field('fiduser')
                    ->order('id asc')
                    ->find();
                $user = db('member')->where(['idmember' => $firstOrder['fiduser']])->field(['userimg', 'nickname'])->find();
                $tmpGroupBuyOrder['userimg'] = $user['userimg'];
                $tmpGroupBuyOrder['username'] = $user['nickname'];
                $groupBuyOrders[] = $tmpGroupBuyOrder;
            }

        }

        $completeGroupNum = db('group_buy_order')->where([
                'group_buy_id' => ['in', array_column($groupBuys, 'group_buy_id')],
                'state' => 2
            ])
            ->count();

        $visitflag=0;
        if($userid>0)
        {
            $visitinfo=db("collection")->where(array('dataid'=>$id,'userid'=>$userid,'flag'=>2))->find();
            if($visitinfo)
                $visitflag=1;

        }

        //判断用户是否有权限可以查看此详情页
        if(!empty($activity['usertype'])){
            //.获取用户分类
            $userid=$this->getUserInfo("userid");
            $usertype=db('member')->where(['idmember'=>$userid])->column('categoryid');
            $usertype=$usertype[0];
            //获取活动的用户权限
            $activityusertype=$activity['usertype'];
            //获取分类的名字
            $obj = new \app\admin\module\activity($idsite);
            $hyfl=$obj->getDic("hyfl");
            $type=[];
            foreach($hyfl as $v){
                $type[$v['id']]=$v['name'];
            }
            $usertypename='';
            foreach (explode(',',trim($activityusertype,',')) as $v){
                $usertypename=$usertypename.$type[$v].',';
            }
            $usertypename=rtrim($usertypename,',');
            if(strpos($activityusertype,','.$usertype.',') == false){
                $usertypeflag=1;
                $this->assign('usertypeflag',$usertypeflag);
                $this->assign('usertype',$usertypename);
            }
        }

        
        //获得栏目列表模版路径
        $url =$roottpl.'/activity/detail.html';

        $this->assign('roottpl','/'.$roottpl);
        //$this->assign('comment_list',$comment_list);
        $this->assign('bmflag',$bmflag);
        $this->assign('qrcodeurl',$this->qrcodeurl());
        $this->assign('idsite',$idsite);
        $this->assign('visitflag',$visitflag);
        $this->assign('id',$id);
        $this->assign('groupBuys',$groupBuys);
        $this->assign('groupBuyOrders',$groupBuyOrders);
        $this->assign('completeGroupNum',$completeGroupNum);
        $this->assign('sitecode',$this->getsitecode($idsite));
        $this->assign('activity_cashed',$activity_cashed);
        $this->assign('SelectFooterTab',2);
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));
        return $this->fetch($url);
    }

    //活动详情领取优惠券的页面
    public function receive_cashed(){
        $request = Request::instance()->param();
        $id = $request['id']; // 当前活动id
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        //站点id
        $idsite=$config['id'];
        //判断是否开启了现金券营销包
        if(!checkedMarketingPackage($idsite,'cashed')){
            die('没有权限,请先订购营销包！');
        }
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);

        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $api = new \think\wx\Api( array(
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ));

        $signPackage=$api->get_jsapi_config($url);
        $this->assign('signPackage',$signPackage);
        $bmflag=0;
        $userid=$this->getUserInfo("userid");
        //先查询活动的信息
        $activity_info=db("activity")->field('chrcontent',true)->where(array('idactivity'=>$id,'siteid'=>$idsite))->find();
        if(!$activity_info){
            return json(['success'=>false,'message'=>'该活动信息丢失']);
        }
        //查询该活动的现金券设置信息
        $activity_cashed = db("activity_cashed_card_set")->where(array('activity_id'=>$id,'is_receive_cashed'=>1))->find();
        if(!$activity_cashed){
            return json(['success'=>false,'message'=>'该活动的现金券设置信息丢失']);
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
        //查询该用户对该活动的领取情况
        $user_receive_info = db('cashed_card_receive')->field('id')->where(['receive_member_id'=>$userid,'cashed_type'=>2,'receive_activity_id'=>$id,'site_id'=>$idsite])->find();
        if($user_receive_info){
            return json(['success'=>false,'message'=>'同一用户每个活动只能领取一次']);
        }
        //进行封装添加领取记录的数据
        $add_receive_param['create_time'] = date('Y-m-d H:i:s',time());//领取时间
        $add_receive_param['receive_activity_id'] = $id;//领取活动id
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
        $add_receive_param['receive_cashed_name'] = '活动专用现金券';//领取的现金券标题
        $add_receive_param['receive_activity_name'] = $activity_info['chrtitle'];//领取来源（活动名称）
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
        $order_id = $request['order_id']; // 当前订单id
        $plan_id = $request['plan_id']; // 当前现金券计划id
        $userid=$this->getUserInfo("userid");
        $order_info = [];
        $activity_cashed = [];
        //查询用户的信息
        $user_info = db('member')->where(['idmember'=>$userid])->find();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
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
            //查询该活动的现金券设置信息
            $activity_cashed = db("activity_cashed_card_set")->alias('a')->join('cms_cashed_plan p','a.cashed_plan_id=p.id')->where(array('a.activity_id'=>$order_info['dataid'],'a.is_share_cashed'=>1))->find();
            if(!$activity_cashed){
                echo '现金券计划信息丢失';exit;
            }
            //分享出去的链接
            $link_url =ROOTURL."/{$sitecode}/receiveshare/{$userid}/{$order_id}/0";
            //如果是管理员扫描进来
        }elseif ($plan_id && $order_id == 0){
            //查询该活动的现金券计划信息
            $activity_cashed = db("cashed_plan")->where(array('id'=>$plan_id,'is_open'=>1))->find();
            if(!$activity_cashed){
                echo '现金券计划信息丢失';exit;
            }
            //管理员分享出去的链接
            $link_url =ROOTURL."/{$sitecode}/receiveshare/{$userid}/0/{$plan_id}";
        }else{
            echo '请求参数错误';exit;
        }
//        echo $url;exit;
        //查询用户的信息
        $user_info = db('member')->where(['idmember'=>$userid])->find();
//        var_dump($id);exit;
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
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
        $this->assign('sitecode',$this->getsitecode($idsite));
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
//        var_dump($share_id);exit;
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        //判断是否开启了现金券营销包
        if(!checkedMarketingPackage($idsite,'cashed')){
            die('没有权限,请先订购营销包！');
        }
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        //分享出去的链接
        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $api = new \think\wx\Api( array(
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ));

        $signPackage=$api->get_jsapi_config($url);
        $this->assign('signPackage',$signPackage);
        $userid=$this->getUserInfo("userid");
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
        //如果是付款后的订单分享
        if($order_id && $plan_id == 0){
            //查询订单的信息
            $order_info = db("order")->where(array('id'=>$order_id,'fiduser'=>$share_id))->find();
            if($order_info){
                //判断该用户是否有领取过该订单分享的现金券
                $receive_cashed_info = db('cashed_card_receive')->field('id')->where(['receive_order_id'=>$order_id,'receive_member_id'=>$userid])->find();
                if(!$receive_cashed_info){
                    //查询该活动的现金券设置信息
                    $activity_cashed = db("activity_cashed_card_set")->alias('a')->join('cms_cashed_plan p','a.cashed_plan_id=p.id')->where(array('a.activity_id'=>$order_info['dataid']))->find();
                    if($activity_cashed){
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
//                        var_dump($activity_cashed);exit;
                    }else{
                        $tip_arr = ['code'=>5,'message'=>'该活动的现金券计划丢失!'];
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
                //查询该活动的现金券计划信息
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
                $tip_arr = ['code'=>5,'message'=>'该活动的现金券计划丢失!'];
                }
            }else{
                $tip_arr = ['code'=>1,'message'=>'您已领取!'];
            }
//                        var_dump($activity_cashed);exit;
        }
        if(!$receive_cashed_count_bool){
            $tip_arr = ['code'=>9,'message'=>'现金券已领完'];
        }
        //如果有生成领取的金额并且是已关注的用户才可以领取
        if($amount != 0 && $user_info['intstate'] == 1 && $receive_cashed_count_bool == true){
            //生成领取现金券记录信息,进行封装添加领取记录的数据
            $add_receive_param['create_time'] = date('Y-m-d H:i:s',time());//领取时间
            $add_receive_param['receive_activity_id'] = $order_info?$order_info['dataid']:'';//领取活动id
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
            $add_receive_param['receive_activity_name'] = $order_info?$order_info['chrtitle']:'';//领取来源（活动名称）
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
//        var_dump($receive_cashed_list);exit;
        //查询站点的信息
        $site_info = db('site_manage')->where(['id'=>$idsite])->find();
        //获得栏目列表模版路径
        $url =$roottpl.'/cashed/receive_share.html';


        $this->assign('roottpl','/'.$roottpl);
        $this->assign('order_info',$order_info);
        $this->assign('qrcodeurl',$this->qrcodeurl());
        $this->assign('idsite',$idsite);
        $this->assign('id',$order_id);
        $this->assign('sitecode',$this->getsitecode($idsite));
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
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        //判断是否开启了现金券营销包
        if(!checkedMarketingPackage($idsite,'cashed')){
            die('没有权限,请先订购营销包！');
        }
        $userid=$this->getUserInfo('userid');
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
        $resule = db('cashed_card_receive')->where($where_arr)->order("create_time desc")->limit($ipage*$this->PageSize,$this->PageSize)->select();

        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
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

    /**
     * 手动取消订单
     * @return \think\response\Json
     * @throws \think\exception\PDOException
     */
    public function cancel_order(){
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $userID=$this->getUserInfo('userid');
        $err_arr = [];
        //查询该用户的待支付订单
        $expiredOrders = db('order')->where(
            [
                'state' => 12,
//                'lock_stock_at' => ['elt', time() - config('order_expire_time')],
                'stock_locked' => 1,
                'fiduser' => $userID,
                'id' => $request['id'],
            ]
        )
            ->field(
                [
                    'id',
                    'package_id',
                    'paynum',
                    'receive_cashed_id'
                ]
            )
            ->find();
        if(!$expiredOrders){
            return json(['code'=>0,'msg'=>'未找到该用户的有效订单']);
        }

        try
        {
            $query = new Query;
            $query->startTrans();
            $res = changeStock($expiredOrders['package_id'], $expiredOrders['paynum'], true);
            if($res)
            {
                db('order')->where(['id' => $expiredOrders['id']])->update(['stock_locked' => 0,'state'=>10]);//改为终止服务
            }
            //判断该订单是否使用了现金券
            if($expiredOrders['receive_cashed_id'] != ''){
                $receive_cashed_id_arr = explode(',',$expiredOrders['receive_cashed_id']);
                foreach ($receive_cashed_id_arr as $value){
                    //将现金券进行释放(修改为未使用)
                    db('cashed_card_receive')->where(['id'=>$value])->update(['used_status'=>1,'release_time'=>date('Y-m-d H:i:s',time())]);
                }
            }
            $query->commit();
        }catch(Exception $e)
        {
            Log::error('释放未支付订单库存失败，订单号为' . $expiredOrders['id']);
            $query->rollBack();
        }
        return json(['code'=>1,'msg'=>'取消成功']);

    }

    /**
     * 再一次修改(提交)订单
     * @return   html
     */
    public function again_order()
    {
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $userID=$this->getUserInfo('userid');
        $order_id=$request['id'];

        $userinfo=db('member')->where(array('idmember'=>$userID,'intstate'=>['neq',2]))->find();

        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        $activity_cashed = [];
        $cashed_list_count = [];
        $cashed_list = [];
        $err_arr = [];//错误信息
        $total = 0;
        //查询订单信息
        $order_info = db('order')->where(['id' => $order_id,'fiduser'=>$userID])->find();
        $groupBuyId = '';
        $groupBuyOrderId = '';
        if(!empty($order_info['group_buy_order_id']))
        {
            $groupBuyOrderId = $order_info['group_buy_order_id'];
            $groupBuyId = db('group_buy_order')->where(['group_buy_order_id' => $groupBuyOrderId])->value('group_buy_id');
        }
        if(!$order_info){
            $err_arr = ['err'=>'未找到该用户的订单'];
        }else{
            $time = date('Y-m-d H:i:s',time());
            //查询该订单购买的活动信息(已上架并且开启报名)
            $activity_info = db('activity')->where(['idactivity'=>$order_info['dataid'],'intflag'=>2,'chksignup'=>1])->find();
            if(!$activity_info){
                $err_arr = ['err'=>'该活动不处于已上架状态或者未开启报名'];
            }else{
                //判断活动是否还处于可以报名
                if(time() < strtotime($activity_info['dtstart']) || time() > strtotime($activity_info['dtend'])){
                    $err_arr = ['err'=>'抱歉，该活动已结束'];
                }
                if(time() < strtotime($activity_info['dtsignstime']) || time() > strtotime($activity_info['dtsignetime'])){
                    $err_arr = ['err'=>'抱歉，该活动未处于允许的下单时间内'];
                }else{
                    //查询该活动的设置可用券的情况
                    $activity_cashed = db("activity_cashed_card_set")->where(array('activity_id'=>$order_info['dataid'],'is_use_cashed'=>1))->find();
                    if($activity_cashed){
                        //查询用户的可用现金券的列表
                        $where['site_id'] = ['=',$idsite];
                        $where['receive_member_id'] = ['=',$userID];
                        $where['cashed_validity_time'] = ['>=',$time];
                        $where['used_status'] = ['=',1];
                        $cashed_list = db('cashed_card_receive')->field('freeze_time',true)->where($where)->where("(cashed_type = 2 and receive_activity_id = {$order_info['dataid']} ) or cashed_type in(1,3,4)")->select();
                        $cashed_list_count = db('cashed_card_receive')->where($where)->where("(cashed_type = 2 and receive_activity_id = {$order_info['dataid']} ) or cashed_type in(1,3,4)")->count();
                    }else{
                        $cashed_list = [];
                        $cashed_list_count = 0;
                    }
                }
            }
            //总表单
            $frmdata=[];

            if($order_info && !empty($order_info['txtdata']))
            {
                //处理总表单的字段和值
                $row2= explode("☆", $order_info['txtfield']);//字段
                $row1= explode("☆", $order_info['txtdata']);//值
                foreach($row1 as $k=>$vo) {
                    $arr = explode("∫", $row2[$k]);
                    $datatype = 1;
                    $datafield = $arr[0];
                    if (count($arr) > 1) {
                        $datatype = $arr[1];
                    }
                    $frmdata[$datafield]=$vo;

                }
            }
            //子表单
            $frmdatasub=[];

            if($order_info && !empty($order_info['txtdata1']))
            {
                //分割子表单字段
                $row2= explode("☆", $order_info['txtfield1']);
                //分割子表单的值  一个子表单字段会存在多个的情况
                $frmdatasubRow=explode("§", $order_info['txtdata1']);

                foreach ($frmdatasubRow as $k1=>$vo)
                {
                    //分割每一个子表单字段的值
                    $row1= explode("☆", $vo);
                    $frmdata1=[];
                    foreach($row1 as $k=>$vo) {
                        $arr = explode("∫", $row2[$k]);
                        $datatype = 1;
                        $datafield = $arr[0];
                        if (count($arr) > 1) {
                            $datatype = $arr[1];
                        }
                        $frmdata1[$datafield]=$vo;
                    }
                    $frmdatasub[]=$frmdata1;

                }

            }
            $total = count($frmdatasub);
        }
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        //获得栏目列表模版路径
        $url =$roottpl.'/order/again_order.html';

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('userinfo',$userinfo);
        $this->assign('sitecode',$sitecode);
        $this->assign('err_arr',$err_arr);//错误信息
        $this->assign('id', $order_info['dataid']);//活动id
        $this->assign('activity_cashed',$activity_cashed);
        $this->assign('cashed_list_count',$cashed_list_count);//现金券条数
        $this->assign('cashed_list',$cashed_list);//现金券列表
        $this->assign('frmdatasub',$frmdatasub);//子表单
        $this->assign('frmdata',$frmdata);//总表单
        $this->assign('total',$total);//总表单
        $this->assign('order_id',$order_id);//订单id
        $this->assign('order_info',$order_info);//订单信息
        $this->assign('groupBuyOrderId',$groupBuyOrderId);//订单信息
        $this->assign('groupBuyId',$groupBuyId);//订单信息
//        var_dump($frmdatasub);exit;
        $this->assign('SelectFooterTab',2);
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));
        return $this->fetch($url);
    }

    public function waiter(){
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];

        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        //获得栏目列表模版路径
        $url =$roottpl.'/activity/waiter.html';
        $flag=empty($request['flag'])?0:$request['flag'];
        $id=empty($request['id'])?0:$request['id'];

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('flag',$flag);
        $this->assign('id',$id);
        $this->assign('idsite',$idsite);
        $this->assign('sitecode',$sitecode);
        $this->assign('SelectFooterTab',2);
        return $this->fetch($url);
    }


    /**
     * 报名表单页面？？？
     * @return   html                   
     */
    public function signup()
    {
        $userID=$this->getUserInfo('userid');
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];

        $groupBuyId = isset($request['group_buy_id']) ? $request['group_buy_id'] : '';
        $groupBuyOrderId = isset($request['group_buy_order_id']) ? $request['group_buy_order_id'] : '';


        // 页面跳转 已开团或参团，TODO 跳转到拼团详情页
        if(!empty($groupBuyId))
        {
            $existGroupBuyOrderIds = db('group_buy_order')->where([
                    'group_buy_id' => $groupBuyId
                ])
                ->column('group_buy_order_id');
            $hadOrder = db('order')->where([
                    'group_buy_order_id' => ['in', $existGroupBuyOrderIds],
                    // 4.已报名 已支付，
                    // 5.已报名 退款中，
                    // 6已部分退款 继续服务，
                    // 7已退款 继续服务，
                    // 8.已报名 退款不通过，
                    // 12.已报名 待支付，
                    'state' => ['in', [4,5,6,7,8,12]],
                    'fiduser' => $userID
                ])
                ->value('id');
            // var_dump($hadOrder);die;
            // 已有拼团订单，不允许开团或参团
            if($hadOrder)
            {
                // header("location:/error.php?msg=".urlencode("不允许重复参团")."&url=".urlencode("/".$sitecode));
                // exit();
            }
        }

        //确认用也户已登陆
        //if($this->setuserinfo(ROOTURL.url("/".$sitecode."/signup/".$request['id']))==false)
        //{
        // echo "请联系管理，配置好公众号和平台绑定";
        //exit();
        //}

        $userinfo=db('member')->where(array('idmember'=>$userID,'intstate'=>['neq',2]))->find();

        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];

        $id = $request['id']; //id
        $content_info = db('activity')
            ->where(
                [
                    'idactivity' => $request['id'],
                ]
            )
            ->field('siteid')
            ->find();
        $idsite=$content_info['siteid'];
        $time = date('Y-m-d H:i:s',time());
        //查询该活动的设置可用券的情况
        $groupBuy = db('group_buy')->find($groupBuyId);
        if($groupBuy && $groupBuy['allow_coupon'] == 0)
        {
            $activity_cashed = [];
        }else
        {
            $activity_cashed = db("activity_cashed_card_set")->where(array('activity_id'=>$id,'is_use_cashed'=>1))->find();
        }
        if($activity_cashed){
            //查询用户的可用现金券的列表
            $where['site_id'] = ['=',$idsite];
            $where['receive_member_id'] = ['=',$userID];
            $where['cashed_validity_time'] = ['>=',$time];
            $where['used_status'] = ['=',1];
            $cashed_list = db('cashed_card_receive')->field('freeze_time',true)->where($where)->where("(cashed_type = 2 and receive_activity_id = {$request['id']} ) or cashed_type in(1,3,4)")->order('create_time desc')->select();
            $cashed_list_count = db('cashed_card_receive')->where($where)->where("(cashed_type = 2 and receive_activity_id = {$request['id']} ) or cashed_type in(1,3,4)")->count();
        }else{
            $cashed_list = [];
            $cashed_list_count = 0;
        }
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        //获得栏目列表模版路径
        $url =$roottpl.'/order/signup.html';
        $this->assign('roottpl','/'.$roottpl);
        $this->assign('id', $request['id']);
        $this->assign('userinfo',$userinfo);
        $this->assign('sitecode',$sitecode);
        $this->assign('SelectFooterTab',2);
        $this->assign('activity_cashed',$activity_cashed);
        $this->assign('cashed_list_count',$cashed_list_count);//现金券条数
        $this->assign('cashed_list',$cashed_list);//现金券列表
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));
        $this->assign('groupBuyId',$groupBuyId);//拼团
        $this->assign('groupBuyOrderId',$groupBuyOrderId);//现金券列表
        return $this->fetch($url);
    }

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
        //$config=$this->getWeiXinConfig(strtolower($sitecode));
        $input= file_get_contents("php://input");
       // file_put_contents(__ROOT__.'\test.txt',$input);
       Log::info('支付回调参数:' . print_r($input, true));
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
            //$attach = (string)$xml->attach;
            $out_trade_no = (string)$xml->out_trade_no;
        }
        if($return_code=='SUCCESS'){
            //业务处理　　//修改订单/用户状态
            $arr=[];
            $arr['state']=4;
            $arr['intflag']=5;
            $arr['paytype1']=1;
            $bool=db('order')->where(array('ordersn'=>$out_trade_no))->update($arr);
            // 发送支付成功微信消息
            $result = template_bm(0,$out_trade_no);

            $tmp_Result= db('order')->where(array('ordersn'=>$out_trade_no))->find();

            Log::info('支付回调订单数据：' . print_r($tmp_Result, true));
            if($tmp_Result)
            {
                SetUserPayCount($tmp_Result['fiduser']);

                // 拼团业务
                Log::info('拼团业务判断结果：' . print_r([!empty($tmp_Result['group_buy_order_id']), $tmp_Result['group_buy_order_id']], true));
                if(!empty($tmp_Result['group_buy_order_id']))
                {
                    $groupBuyOrderModel = new GroupBuyOrder;
                    $orderCount = db('order')->where(
                            [
                                'group_buy_order_id' => $tmp_Result['group_buy_order_id'],
                            ]
                        )
                        ->count();
                    if($orderCount == 1)
                    {
                        // 开团
                        $action = '开团';
                    }else
                    {
                        // 参团
                        $action = '参团';
                    }

                    $res = $groupBuyOrderModel->afterStart($tmp_Result, $action);
                    Log::info('支付后拼团操作 函数参数为：' . print_r([$tmp_Result, $action, $res], true));
                }

                $idsite = db('site_manage')->where('site_code',$sitecode)->value('id');
                $integral_rule_config = db('integral_rule_config')->where('idsite',$idsite)->find();
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
                        $msg = '报名活动成功，你获取到【'.$integral.'积分】<a href=\"'.request()->domain().'/'.$sitecode.'/integralrecord\">【查看详情】</a>';
                        send_ordinary_msg($sitecode, $member['openid'], $msg);
                    }
                }

                //判断该订单是否使用了现金券
                if($tmp_Result['receive_cashed_id']){
                    //将现金券使用id分割为数组
                    $receive_cashed_id_arr = explode(',',$tmp_Result['receive_cashed_id']);
                    $update_param['used_activity_id'] = $tmp_Result['dataid'];//使用活动标识id
                    $update_param['used_activity_name'] = $tmp_Result['chrtitle'];//使用活动名称
                    $update_param['used_time'] = date('Y-m-d H:i:s',time());//使用现金券时间
                    $update_param['used_order_id'] = $tmp_Result['id'];//使用订单id
                    $update_param['used_status'] = 5;//改为已使用
                    foreach ($receive_cashed_id_arr as $value){
                        db('cashed_card_receive')->where(['id'=>$value])->update($update_param);
                    }
                }

            }

            //if($bool)
            //{
            //}
            if($bool)
            {
                //查找siteId
                $site = db('site_manage')->field(['id'])->where(['site_code' => $sitecode])->find();
                if($site)
                {
                    //发短信
                    $replace = [];
                    //给客户和商务发短信通知  类型：2--下单成功
                    Log::debug(date('Y-m-d H:i:s') . ' 收费活动付款成功,订单信息:$order=' . print_r($tmp_Result, true));
                    sysSendMsg($site['id'], 2, $tmp_Result, $replace);

                }
            }

            echo 'success';

        }
        else
        {
            echo 'fail';
        }
    }


    /**
     * 免费|收费活动报名
     * ---------------------------------------------------------
     * 职责
     * ---------------------------------------------------------
     * 验证登录状态
     * 创建|查询订单（收集报名表单数据并保存、校验）(校验活动限制)
     * 判断活动限制（关注、手机号、身份证，收费）
     * 扣除库存
     * 发起支付
     * ---------------------------------------------------------
     * @DateTime 2019-04-23T17:18:24+0800
     * @return   [type]                   [description]
     */
    public function signup_post()
    {
        //获取传入的参数及初始化各种变量
        $request = Request::instance()->param();
//        var_dump($request);exit;
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        $dataID =$request['id']; //id
        $userID=$this->getUserInfo('userid');
        $errmsg="";
        $flag=1;
        $txtdata="";
        $txtfield="";
        $txtdata1="";
        $txtfield1="";
        $arrfield=[];
        $arrdata=[];
        $chkissubscribe=0;
        $Tid=0;
        $err_arr = [];
        $result_order=null;


        if($userID=="")
        {
            $userID = 55;
            // $flag=2;
            // $errmsg="用户信息错误！";
        }

        if(empty($request['txtpaynum']) || empty($request['txtpaynum'])){
            $this->redirect(url('/'.$sitecode.'/detail/'.$dataID));
        }

        if($flag==1)
        {
            $arr['idmember']=$userID;
            $row=db('member')->where($arr)->find();

            $wxID=$row['openid'];
            $UserName=$row['nickname'];
            $price=0;
            $intstate=$row['intstate'];//会员状态（1关注 2取消关注 3游客）',
            $paynum=$request['txtpaynum'];
            $payname=$request['payname'];

            //chkissubscribe //是否需要关注 1为需要关注
            //ischarge：是否收费
            //chkismobile：   同一手机号只能报名一次（报名表单中必须有手机类型框）
            //chkisidcard： 同一身份证号只能报名一次（报名表单中必须有身份证类型框）
            //intmaxpaynum：  单次最大购买数量，0表示不限
            //intmaxmobilepaynum：  单个手机号累计购买上限，0表示不限（报名表单中必须有手机类型框）
            //intmaxidcardpaynum：  单个身份证累计购买上限，0表示不限（报名表单中必须有身份证类型框）
            $content_info = db('activity')->where(['idactivity' => $dataID])->field('chkissubscribe,ischarge,chkismobile,chkisidcard,intmaxpaynum,intmaxmobilepaynum,intmaxidcardpaynum,chrtitle,selsignfrom')->find();

            $Tid=$content_info['selsignfrom'];

            $chkissubscribe=$content_info['chkismobile']; //是否需要关注 1为需要关注
            $chkismobile=$content_info['chkismobile'];//   同一手机号只能报名一次（报名表单中必须有手机类型框）
            $chkisidcard=$content_info['chkisidcard'];// 同一身份证号只能报名一次（报名表单中必须有身份证类型框）
            $intmaxpaynum=$content_info['intmaxpaynum'];//  单次最大购买数量，0表示不限
            $intmaxmobilepaynum=$content_info['intmaxmobilepaynum'];//  单个手机号累计购买上限，0表示不限（报名表单中必须有手机类型框）
            $intmaxidcardpaynum=$content_info['intmaxidcardpaynum'];//  单个身份证累计购买上限，0表示不限（报名表单中必须有身份证类型框）
            $chkismobile_flag=false;
            $chkisidcard_flag=false;
            if($chkismobile==1)
            {
                $chkismobile_flag=true;
            }
            if($chkisidcard==1)
            {
                $chkisidcard_flag=true;
            }
            if($intmaxpaynum>0 && $paynum>$intmaxpaynum)
            {
                $flag=2;
                $errmsg="单次最大购买数量不能大于".$intmaxpaynum;
            }
        }

        if($chkissubscribe==1 && $flag==1)
        {
            if(empty($userID) || $userID<1 || $intstate!=1)
            {
                $config=$this->getWeiXinConfig(strtolower($sitecode));
                $api = new \think\wx\Api( array(
                    'appId' => trim($config['appid']),
                    'appSecret'    => trim($config['appsecret']),
                ));

                $imgurl= $api->get_qrcode_url();
                $template_url="template/weixin.html";
                $this->assign('msg',"本活动要求关注后才能报名<br>请扫下面二维码关注");
                $this->assign('imgurl',$imgurl);
                return $this->fetch($template_url);

            }
        }


        if($Tid>0 && $flag==1)
        {
            //表单字段
            $T_result=db('signup_template_sub')->where('pid='.$Tid)->order("sn asc,id asc")->select();
            foreach ($T_result as $k=>$vo)
            {
                $arrfield[]=$vo['title'].'∫'.$vo['chrtype'];
                $filekey='feild_'.$vo['id'];
                if($vo['chrtype']==7)
                {
                    //如果没有图片
                    if ($_FILES[$filekey]["error"] > 0)
                    {
                        //如果存在旧的图片  那么就用旧的图片
                        if(array_key_exists("old_file_{$vo['id']}",$request)){
                            $arrdata[]=$request["old_file_{$vo['id']}"];
                        }else{
                            $arrdata[]='';
                        }
                    }
                    else
                    {
                        $tmp_file_paht= $_FILES[$filekey]["tmp_name"];
                        $newfilename =getNumber(). strrchr($_FILES[$filekey]["name"],'.');
                        $filepath="order/".date('Y').'/'.date('m-d').'/';
                        $filepath= $idsite.'/'. $filepath;
                        $filepath='public/uploads/'.$filepath;

                        if(!is_dir($filepath)){
                            mkdir($filepath, 0777,true);
                        }

                        $filepath=$filepath."/".$newfilename;
                        move_uploaded_file($tmp_file_paht, $filepath);
                        $arrdata[]="/".$filepath;
                    }
                }
                else
                {
                    $datavalue=empty($request[$filekey])?"":$request[$filekey];

                    if(($vo['chrtype']==3 || $vo['chrtype']==10) && ($chkismobile==1 || $intmaxmobilepaynum>0))  // 同一手机号
                    {
                        $tmp_paynum=$paynum;
                        if($datavalue=="")
                        {
                            $errmsg="手机号码不能为空";
                            break;
                        }
                        $chkismobile_flag=false;
                        $tmpResult= db('order')->where(" dataid=".$dataID." and txtdata like '%".$datavalue."%'")->select();
                        if($tmpResult)
                        {
                            foreach ($tmpResult as $k1=>$vo1) {
                                $row2= explode("☆", $vo1['txtfield']);
                                $row1= explode("☆", $vo1['txtdata']);
                                foreach($row1 as $k2=>$vo2){
                                    $arr= explode("∫", $row2[$k2]);
                                    if(count($arr)>1 && ($arr[1]==3 || $arr[1]==10) && $datavalue==$row1[$k2])
                                    {
                                        if($chkismobile==1)
                                        {
                                            $errmsg="每个电话号码只能订购一次";
                                            break;
                                        }
                                        $tmp_paynum=$tmp_paynum+$vo1['paynum'];
                                    }
                                }
                                if($errmsg!="")
                                {
                                    break;
                                }
                            }
                        }

                        if($tmp_paynum>$intmaxmobilepaynum && $intmaxmobilepaynum>0)
                        {
                            $errmsg="单个手机号累计只能购买".$intmaxmobilepaynum."份";
                            break;
                        }

                    }

                    if(($vo['chrtype']==8 || $vo['chrtype']==13) && ($chkisidcard==1 || $intmaxidcardpaynum>0))  //  同一身份证
                    {
                        $tmp_paynum=$paynum;
                        if($datavalue=="")
                        {
                            $errmsg="身份证不能为空";
                            break;
                        }
                        $chkisidcard_flag=false;
                        $tmpResult= db('order')->where(" dataid=".$dataID." and txtdata like '%".$datavalue."%'")->select();
                        if($tmpResult)
                        {
                            foreach ($tmpResult as $k1=>$vo1) {
                                $row2= explode("☆", $vo1['txtfield']);
                                $row1= explode("☆", $vo1['txtdata']);
                                foreach($row1 as $k2=>$vo2){
                                    $arr= explode("∫", $row2[$k2]);
                                    if(count($arr)>1 && ($arr[1]==8 || $arr[1]==13) && $datavalue==$row1[$k2])
                                    {
                                        if($chkisidcard==1)
                                        {
                                            $errmsg="身份证只能订购一次";
                                            break;
                                        }
                                        $tmp_paynum=$tmp_paynum+$vo1['paynum'];
                                    }
                                }
                                if($errmsg!="")
                                {
                                    break;
                                }
                            }
                        }

                        if($tmp_paynum>$intmaxidcardpaynum && $intmaxidcardpaynum>0)
                        {
                            $errmsg="单个身份证累计累计只能购买".$intmaxidcardpaynum."份";
                            break;
                        }

                    }
                    $arrdata[]=$datavalue;
                }
            }

            $txtfield=implode('☆',$arrfield);
            $txtdata=implode('☆',$arrdata);

            //子表单
            $T_result=db('signup_template_sub1')->where('pid='.$Tid)->order("sn asc,id asc")->select();
            $subindex=empty($request['subindex'])?0:$request['subindex'];
            if($subindex>0)
            {
                $arrfield1=[];
                $arrdata1=[];
                foreach ($T_result as $k=>$vo) {
                    $arrfield1[] = $vo['title'] . '∫' . $vo['chrtype'];
                }
                $txtfield1=implode('☆',$arrfield1);

                for($ix=0;$ix<$subindex;$ix++)
                {
                    $isnullflag=true;
                    $_data=[];
                    foreach ($T_result as $k=>$vo)
                    {
                        $filekey='feild_sub_'.$vo['id']."_".($ix+1);

                        if($k==0 && empty($request[$filekey]))
                        {
                            $isnullflag=false;
                            break;
                        }

                        if($vo['chrtype']==7)
                        {
                            if ($_FILES[$filekey]["error"] > 0)
                            {
                                //如果存在旧的图片  那么就用旧的图片
                                if(array_key_exists("old_file_{$vo['id']}",$request)){
                                    $arrdata[]=$request["old_file_{$vo['id']}"];
                                }else{
                                    $arrdata[]='';
                                }
                            }
                            else
                            {
                                $tmp_file_paht= $_FILES[$filekey]["tmp_name"];
                                $newfilename =getNumber(). strrchr($_FILES[$filekey]["name"],'.');
                                $filepath="order/".date('Y').'/'.date('m-d').'/';
                                $filepath= $idsite.'/'. $filepath;
                                $filepath='public/uploads/'.$filepath;

                                if(!is_dir($filepath)){
                                    mkdir($filepath, 0777,true);
                                }

                                $filepath=$filepath."/".$newfilename;
                                move_uploaded_file($tmp_file_paht, $filepath);
                                $arrdata[]="/".$filepath;
                            }
                        }
                        else
                        {
                            $datavalue=empty($request[$filekey])?"":$request[$filekey];
                            $_data[]=$datavalue;
                        }
                    }
                    if($isnullflag)
                    {
                        $arrdata1[]=implode('☆',$_data);
                    }

                }
                $txtdata1=implode('§',$arrdata1);

            }
        }



        $ischarge=1;//是否收费 1:免费 2收费

        $ordersn = "";

        if($errmsg!="")
        {
            $flag=2;
        }
        else
        {
            if($chkismobile_flag)
            {
                $errmsg="没有设置手机字段，请联系管理员";
            }
            if($chkisidcard_flag)
            {
                $errmsg="没有设置身份证字段，请联系管理员";
            }


            //$payname 实际存储package_id
            $package = [];
            if(empty($payname))
            {
                $flag=2;
                $errmsg="报名失败，数据错误！";
            }else
            {
                $packageObj = new Package;
                $package = $packageObj->getPackage($payname);
                if($package)
                {
                    $payname = $package['keyword1'] . ' ' . $package['keyword2'] . '(' . $package['member_price'] . '元)' ;
                    $price = $package['member_price'] * $paynum;
                    // 是否是拼团订单
                    // var_dump($request);die;
                    if(isset($request['group_buy_id']) && !empty($request['group_buy_id']))
                    {
                        // TODO 验证拼团有效期
                        $groupBuy = db('group_buy')->find($request['group_buy_id']);
                        $price = $groupBuy['group_buy_price'] * $paynum;
                        if(!isset($request['group_buy_order_id']))
                        {
                            // TODO 验证拼团启用状态
                            // 开团购买数量小于等于拼团数量限制 拼团数量小于等于套餐剩余库存
                            if($groupBuy['group_num'] >= $paynum && $package['stock'] >= $groupBuy['group_num'])
                            {
                                $flag = 1;
                            }else
                            {
                                $flag = 2;
                                $errmsg = '报名失败，库存不足！';
                            }
                        }else
                        {
                            $groupBuyOrder = db('group_buy_order')->find($request['group_buy_order_id']);
                            $stock = $groupBuyOrder['group_num'] - $groupBuyOrder['sold'];
                            if($stock >= $paynum)
                            {
                                $flag = 1;
                            }else
                            {
                                $flag = 2;
                                $errmsg = '报名失败，库存不足！';
                            }
                        }
                    }else
                    {
                    
                        if($package['stock'] >= $paynum)
                        {
                            $flag = 1;
                        }else
                        {
                            $flag = 2;
                            $errmsg = '报名失败，库存不足！';
                        }
                    }
                }else
                {
                    $flag = 2;
                    $errmsg = '报名失败，套餐不存在或已过期！';
                }
            }


            if($flag == 1)
            {
                if($content_info)
                {
                    $ischarge=$content_info['ischarge'];
                }

                try
                {
                    //事务，添加订单、锁定库存
                    //创建新连接，专用于事务操作
                    $query = new Query;
                    $query->startTrans();
                    $order_info = [];
                    $activity_cashed_set = [];
                    if(isset($request['group_buy_id']) && !empty($request['group_buy_id']))
                    {
                        $groupBuyOrderModel = new GroupBuyOrder;
                        if(isset($request['group_buy_order_id']) && !empty($request['group_buy_order_id']))
                        {
                            //锁定本次订单库存
                            $groupBuyOrderId = $request['group_buy_order_id'];
                            $res = $groupBuyOrderModel->join($groupBuyOrderId, $paynum);
                        }else
                        {
                            //新增拼团
                            $res = $groupBuyOrderId = $groupBuyOrderModel->start($request['group_buy_id'], $paynum, $package['stock'], $idsite, $content_info['chrtitle'],$payname,$this->getUserInfo('nickname'),$this->getUserInfo('userid'));
                        }

                        if(!$res)
                        {
                            throw new Exception('已售完');
                        }


                        $ordersn = getOrderSn();
                        // TODO 整合添加订单功能
                        $obj = new \app\admin\module\Order();
                        $ordersn = $obj->addOrder($idsite,
                            $userID,
                            $wxID,
                            $UserName,
                            $dataID,
                            $package['package_id'],
                            $txtfield,
                            $txtdata,
                            $payname,
                            $price,
                            $paynum,
                            $ordersn,
                            0, //$cashed_amount,
                            '', //$receive_cashed_id,
                            '', //$share_plan_id,
                            $txtfield1,
                            $txtdata1,
                            $ischarge,
                            $ordersn,
                            $groupBuyOrderId
                        );
                    }else
                    {
                        //非拼团订单 拼团订单id为空
                        $groupBuyOrderId = '';
                        $obj = new \app\admin\module\Order();
                        $ordersn = getOrderSn();
                        $cashed_amount = 0;//使用优惠券金额
                        $receive_cashed_id = '';//使用优惠券的领取现金券id
                        //查询该活动是否可以分享优惠券
                        $activity_cashed_set = db('activity_cashed_card_set')->field('cashed_plan_id,is_share_cashed,activity_id')->where(['activity_id'=>$dataID,'is_share_cashed'=>1])->find();
                        //判断使用了优惠券相关的信息
                        if($request['hidvolumeid'] && $request['hidvolumeprice'] > 0){
                            //去除优惠券id左右两边的逗号
                            $receive_cashed_id = trim($request['hidvolumeid'],',');
                            //如果抵用的优惠券大于订单的金额,那么就支付一分钱
                            if($request['hidvolumeprice'] >= $price){
                                $price = 0.01;
                            }else{
                                $price = round($price-$request['hidvolumeprice'],2);//订单的金额减去使用优惠券的金额
                            }
                            $cashed_amount = $request['hidvolumeprice'];
                        }
                        //分享现金券的计划id
                        $share_plan_id = $activity_cashed_set?$activity_cashed_set['cashed_plan_id']:'';
                        //如果有订单状态参数和订单id   那么就是终止服务再次下单   或者默认下单没有订单id时就是新增数据
                        $ajaxdate['request']=$request;
                        if((!array_key_exists('order_state',$request) && !array_key_exists('order_id',$request)) || (array_key_exists('order_state',$request) && array_key_exists('order_id',$request))){
                            $ordersn = $obj->addOrder($idsite,
                                $userID,
                                $wxID,
                                $UserName,
                                $dataID,
                                $package['package_id'],
                                $txtfield,
                                $txtdata,
                                $payname,
                                $price,
                                $paynum,
                                $ordersn,
                                $cashed_amount,
                                $receive_cashed_id,
                                $share_plan_id,
                                $txtfield1,
                                $txtdata1,
                                $ischarge,
                                $ordersn,
                                $groupBuyOrderId
                            );
                            //获取订单id
                            $order_id = Db::name('order')->getLastInsID();
                            $ajaxdate['order_id']=$order_id;
                            //那么就是修改订单的数据
                        }elseif(!array_key_exists('order_state',$request) && array_key_exists('order_id',$request)){
                            $res = true;
                            $stockLocked = db('order')->field('stock_locked')->find($request['order_id']);
                            if($stockLocked == 0)
                            {
                                //减库存
                                $res = changeStock($package['package_id'], $paynum);
                            }
                            if(!$res)
                            {
                                throw new Exception('商品已售完');
                            }

                            $ordersn = $obj->updateOrder($idsite,
                                $userID,
                                $wxID,
                                $UserName,
                                $dataID,
                                $package['package_id'],
                                $txtfield,
                                $txtdata,
                                $payname,
                                $price,
                                $paynum,
                                $ordersn,
                                $cashed_amount,
                                $receive_cashed_id,
                                $share_plan_id,
                                $txtfield1,
                                $txtdata1,
                                $ischarge,
                                $ordersn,
                                $groupBuyOrderId,
                                $request['order_id']//订单id
                            );
                            $ajaxdate['order_id']=$request['order_id'];
                        }
                        if(!$ordersn)
                        {
                            throw new Exception('订单创建失败');
                        }


                        //此时提交事务,是为了下面能查到该订单
                        $query->commit();
                        //生成订单后开始将使用掉的现金券修改状态和增加使用对象
                        $order_info = db('order')->where(['ordersn'=>$ordersn])->find();
                        if($order_info){
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
                                        array_push($err_arr,['err'=>'该订单存在过期的现金券']);
//                                        var_dump($err_arr);exit;
                                        $flag = 2;
                                    }else{
                                        //执行修改
                                        db('cashed_card_receive')->where(['id'=>$value])->update($update_param);
                                    }
                                }
                            }
                        }
                    }
                    //处理短信及微信预下单操作
                    if($ischarge==2 && $price > 0)    //付费活动
                    {
                        // api模块 - 包含各种系统主动发起的功能
                        $api = new \think\wx\Api(
                            array(
                                'appId' => trim($config['appid']),
                                'appSecret'    => trim($config['appsecret']),
                                'mchId'    => trim($config['mchid']),
                                'key'    => trim($config['paykey'])
                            )
                        );

                        $data=$this->wechatpay($api,$ordersn,$content_info['chrtitle'].'['. $payname.']',$price,$dataID);
                        $config=$api->get_jsapi_config();
                        $ajaxdate['data']=$data;
                        $ajaxdate['config']=$config;
                        $ajaxdate['activity_cashed_set']=$activity_cashed_set;
//                        $this->assign('data',$data);
//                        $this->assign('config',$config);
//                        $this->assign('activity_cashed_set',$activity_cashed_set);//活动现金券的设置
                        if($order_info){
                            //修改成异步拉起微信支付
                            //$order_id=$order_info['id'];
                           $ajaxdate['order_id']=$order_info['id'];
//                            $this->assign('order_id',$order_info['id']);//订单id
                        }else{
                            $order_id='';
                        }
                        Log::debug(date('Y-m-d H:i:s') . ' 收费活动报名成功');
                    }else   //免费活动，发短信
                    {
                        //获取订单信息
                        $order = db('order')->where(['ordersn' => $ordersn])->find();
                        $replace = [];
                        Log::debug(date('Y-m-d H:i:s') . ' 免费活动报名成功,订单信息:$order=' . print_r($order, true));
                        //给客户和商务发短信通知  类型：7--免费活动报名成功
                        sysSendMsg($idsite, 7, $order, $replace);
                    }
                    // 不要删，兼容现金券代码 若没有运行到现金券部分，则整个事务必须提交，若要删掉这行代码，必须考虑合并本方法中的三个addOrder调用
                    $query->commit();
                }catch (Exception $e)
                {
                    Db::rollBack();
                    Log::error('下单及扣除库存失败。[ SQL ] ' . Db::getLastSql());
                    $errmsg ='下单及扣除库存失败';// $e->getMessage();
                    $flag = 2;
                    // throw $e;
                }
            }
        }
// var_dump($data);die;
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        $url =$roottpl.'/order/signuppost.html';
//        $this->assign('flag',$flag);
//        $this->assign('price',$price);
//        $this->assign('errmsg',$errmsg);
//        $this->assign('err_arr',$err_arr);
//        $this->assign('roottpl','/'.$roottpl);
//        $this->assign('sitecode',$sitecode);
//        $this->assign('dataID',$dataID);
//        $this->assign('ischarge',$ischarge);
//        $this->assign('ordersn',$ordersn);
//        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));
        // var_dump($flag, $errmsg)
//        $ajaxdate=[
//            'res'=>1,
//            'flag'=>$flag,
//            'price'=>$price,
//            'errmsg'=>$errmsg,
//            'err_arr'=>$err_arr,
//            'roottpl'=>'/'.$roottpl,
//            'sitecode'=>$sitecode,
//            'ischarge'=>$ischarge,
//            'ordersn'=>$ordersn,
//            'dataID'=>$dataID,
//            'is_cashed'=>checkedMarketingPackage($idsite,'cashed'),
//        ];
        $ajaxdate['res']=1;
        $ajaxdate['flag']=$flag;
        $ajaxdate['price']=$price;
        $ajaxdate['errmsg']=$errmsg;
        $ajaxdate['err_arr']=$err_arr;
        $ajaxdate['roottpl']='/'.$roottpl;
        $ajaxdate['sitecode']=$sitecode;
        $ajaxdate['ischarge']=$ischarge;
        $ajaxdate['ordersn']=$ordersn;
        $ajaxdate['dataID']=$dataID;
        $ajaxdate['is_cashed']=checkedMarketingPackage($idsite,'cashed');
        
        //dump($err_arr);
        $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
      // return $this->fetch($url);
      return $ajaxdate;
    }


    //订单再次提交 即未支付订单支付
    public function signup_repost()
    {

        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        $dataID = 0; //id
        $ordersn =$request['ordersn']; //id

        $userID=$this->getUserInfo('userid');
        $paynum=0;
        $errmsg="";
        $flag=1;
        $txtdata="";
        $txtfield="";
        $txtdata1="";
        $txtfield1="";
        $arrfield=[];
        $arrdata=[];
        $err_arr = [];//判断现金券的错误信息
        $chkissubscribe=0;
        $Tid=0;
        $result_order=null;
        $payname='';
        $chkismobile_flag=false;
        $chkisidcard_flag=false;
        $content_info = [];

        if($userID=="")
        {
            // $userID = 55;
            $flag=2;
            $errmsg="用户信息错误！";
        }
        if($ordersn=="" )
        {
            $flag=2;
            $errmsg="数据提交错误！";

        }
        else
        {
            $result_order=db('order')->where(array("ordersn"=>$ordersn))->find();
            if(empty($result_order))
            {
                $flag=2;
                $errmsg="订单数据没有找到！";
            }else
            {
                //判断是否使用了现金券
                if($result_order['receive_cashed_id']){
                    //将现金券使用id分割为数组
                    $receive_cashed_id_arr = explode(',',$result_order['receive_cashed_id']);
                    foreach ($receive_cashed_id_arr as $value){
                        //查询出其领取的现金券是否过期
                        $receive_cashed_info = db('cashed_card_receive')->where(['id'=>$value])->find();
                        //判断该券是否被释放
                        if($receive_cashed_info['used_status'] != 10){
                            $flag = 2;
                            array_push($err_arr,['err'=>'抱歉，因您太久未支付，该订单的现金券已被释放!']);
                        }else{
                            //判断是否过期
                            if(strtotime($receive_cashed_info['cashed_validity_time']) < time()){
                                array_push($err_arr,['err'=>'该订单存在过期的现金券']);
                                $flag = 2;
                            }
                        }
                    }

                }
                $paynum=$result_order['paynum'];
                $dataID=$result_order['dataid'];
                $payname=$result_order['payname'];
                $price=$result_order['price'];
                // 判断套餐是否过期
                $packageObj = new Package;
                $package = $packageObj->getPackage($result_order['package_id']);
                if($package && $flag == 1)
                {
                    // 如果该订单库存已被释放
                    if($result_order['stock_locked'] == 0)
                    {
                        try
                        {
                            $query = new Query;
                            $query->startTrans();
                            // 如果是拼团订单
                            if(!empty($result_order['group_buy_order_id']))
                            {
                                // 验证拼团有效期
                                $config=$this->getWeiXinConfig(strtolower($sitecode));
                                $idsite=$config['id'];
                                //通过该拼团订单对应订单数确定是否开团
                                $orderNum = db('order')->where(
                                        [
                                            'idsite' => $idsite,
                                            'group_buy_order_id' => $result_order['group_buy_order_id'],
                                        ]
                                    )
                                    ->count();
                                
                                $groupBuyOrderModel = new GroupBuyOrder;

                                // 如果是开团订单
                                if($orderNum == 1)
                                {
                                    $res = $groupBuyOrderModel->restart($result_order['group_buy_order_id'], $paynum);
                                }else
                                {
                                    $res = $groupBuyOrderModel->join($result_order['group_buy_order_id']);
                                }

                                if(!$res)
                                {
                                    throw new Exception('库存不足');
                                }
                            }else
                            {
                                // 重新锁定库存
                                $res = changeStock($result_order['package_id'], $paynum);
                                
                                if($res)
                                {
                                    db('order')->where(['id' => $result_order['id']])->update(['lock_stock_at' => time(), 'stock_locked' => 1]);
                                }else
                                {
                                    throw new Exception('库存不足');
                                }
                            }
                            $query->commit();
                        }catch(Exception $e)
                        {
                            $query->rollBack();
                            $flag = 2;
                            $errmsg = '报名失败，库存不足！';
                        }
                    } 
                }else
                {
                    $flag = 2;
                    $errmsg = '套餐已过期';
                }
            }
        }

        //取得活动相关内容
        if($flag==1)
        {
            $arr['idmember']=$userID;
            $row=db('member')->where($arr)->find();

            $wxID=$row['openid'];
            $UserName=$row['nickname'];
            $intstate=$row['intstate'];//会员状态（1关注 2取消关注 3游客）',
            //chkissubscribe //是否需要关注 1为需要关注
            //ischarge：是否收费
            //chkismobile：   同一手机号只能报名一次（报名表单中必须有手机类型框）
            //chkisidcard： 同一身份证号只能报名一次（报名表单中必须有身份证类型框）
            //intmaxpaynum：  单次最大购买数量，0表示不限
            //intmaxmobilepaynum：  单个手机号累计购买上限，0表示不限（报名表单中必须有手机类型框）
            //intmaxidcardpaynum：  单个身份证累计购买上限，0表示不限（报名表单中必须有身份证类型框）
            $content_info = db('activity')->where(['idactivity' => $dataID])->field('chkissubscribe,ischarge,chkismobile,chkisidcard,intmaxpaynum,intmaxmobilepaynum,intmaxidcardpaynum,chrtitle,selsignfrom')->find();

            $Tid=$content_info['selsignfrom'];

            $chkissubscribe=$content_info['chkismobile']; //是否需要关注 1为需要关注
            $chkismobile=$content_info['chkismobile'];//   同一手机号只能报名一次（报名表单中必须有手机类型框）
            $chkisidcard=$content_info['chkisidcard'];// 同一身份证号只能报名一次（报名表单中必须有身份证类型框）
            $intmaxpaynum=$content_info['intmaxpaynum'];//  单次最大购买数量，0表示不限
            $intmaxmobilepaynum=$content_info['intmaxmobilepaynum'];//  单个手机号累计购买上限，0表示不限（报名表单中必须有手机类型框）
            $intmaxidcardpaynum=$content_info['intmaxidcardpaynum'];//  单个身份证累计购买上限，0表示不限（报名表单中必须有身份证类型框）
            $chkismobile_flag=false;
            $chkisidcard_flag=false;
            if($chkismobile==1)
            {
                $chkismobile_flag=true;
            }
            if($chkisidcard==1)
            {
                $chkisidcard_flag=true;
            }
            if($intmaxpaynum>0 && $paynum>$intmaxpaynum)
            {
                $flag=2;
                $errmsg="单次最大购买数量不能大于".$intmaxpaynum;
            }
        }

        //是否需要关注
        if($chkissubscribe==1 && $flag==1)
        {
            if(empty($userID) || $userID<1 || $intstate!=1)
            {
                $config=$this->getWeiXinConfig(strtolower($sitecode));
                $api = new \think\wx\Api(
                    array(
                        'appId' => trim($config['appid']),
                        'appSecret'    => trim($config['appsecret']),
                    )
                );

                $imgurl= $api->get_qrcode_url();
                $template_url="template/weixin.html";
                $this->assign('msg',"本活动要求关注后才能报名<br>请扫下面二维码关注");
                $this->assign('imgurl',$imgurl);
                return $this->fetch($template_url);

            }
        }


        //表单数据检查
        if($Tid>0 && $flag==1)
        {
            $T_field=  explode('☆', $result_order['txtfield']);
            $T_data=  explode('☆', $result_order['txtdata']);

            //表单字段
            foreach ($T_field as $k=>$vo)
            {
                if(strpos($vo,'∫')<1)
                {
                    continue;
                }
                $datavalue=$T_data[$k];
                $field_arr=explode('∫',$vo);  //字段名称和字段类型

                if(($field_arr[1]==3 || $field_arr[1]==10) && ($chkismobile==1 || $intmaxmobilepaynum>0))  // 同一手机号
                {
                    $tmp_paynum = $paynum;
                    if ($datavalue == "") {
                        $errmsg = "手机号码不能为空";
                        break;
                    }
                    $chkismobile_flag = false;

                    $map['txtdata'] = array('like', '%' . $datavalue . '%');
                    $map['dataid'] = $dataID;
                    $tmpResult = db('order')->where($map)->select();
                    if ($tmpResult) {
                        foreach ($tmpResult as $k1 => $vo1) {

                            if ($vo1['ordersn'] == $ordersn) //同一订单
                            {
                                continue;
                            }

                            $row2 = explode("☆", $vo1['txtfield']);
                            $row1 = explode("☆", $vo1['txtdata']);
                            foreach ($row1 as $k2 => $vo2) {
                                $arr = explode("∫", $row2[$k2]);
                                if (count($arr) > 1 && ($arr[1] == 3 || $arr[1] == 10) && $datavalue == $row1[$k2]) {
                                    if ($chkismobile == 1) {
                                        $errmsg = "每个电话号码只能订购一次";
                                        break;
                                    }
                                    $tmp_paynum = $tmp_paynum + $vo1['paynum'];
                                }
                            }
                            if ($errmsg != "") {
                                break;
                            }
                        }
                    }

                    if ($tmp_paynum > $intmaxmobilepaynum && $intmaxmobilepaynum > 0) {
                        $errmsg = "单个手机号累计只能购买" . $intmaxmobilepaynum . "份";
                        break;
                    }
                }

                if(($field_arr[1]==8 || $field_arr[1]==13 )&& ($chkisidcard==1 || $intmaxidcardpaynum>0))  //  同一身份证
                {
                    $tmp_paynum=$paynum;
                    if($datavalue=="")
                    {
                        $errmsg="身份证不能为空";
                        break;
                    }
                    $chkisidcard_flag=false;
                    $map['txtdata'] = array('like', '%' . $datavalue . '%');
                    $map['dataid'] = $dataID;
                    $tmpResult= db('order')->where($map)->select();
                    if($tmpResult)
                    {
                        foreach ($tmpResult as $k1=>$vo1) {
                            if ($vo1['ordersn'] == $ordersn) //同一订单
                            {
                                continue;
                            }
                            $row2= explode("☆", $vo1['txtfield']);
                            $row1= explode("☆", $vo1['txtdata']);
                            foreach($row1 as $k2=>$vo2){
                                $arr= explode("∫", $row2[$k2]);
                                if(count($arr)>1 && ($arr[1]==8 || $arr[1]==13) && $datavalue==$row1[$k2])
                                {
                                    if($chkisidcard==1)
                                    {
                                        $errmsg="身份证只能订购一次";
                                        break;
                                    }
                                    $tmp_paynum=$tmp_paynum+$vo1['paynum'];
                                }
                            }
                            if($errmsg!="")
                            {
                                break;
                            }
                        }
                    }

                    if($tmp_paynum>$intmaxidcardpaynum && $intmaxidcardpaynum>0)
                    {
                        $errmsg="单个身份证累计累计只能购买".$intmaxidcardpaynum."份";
                        break;
                    }

                }
                $arrdata[]=$datavalue;

            }
        }

        $ischarge=1;//是否收费 1:免费 2收费

        $flag=1;

        //如果有错误信息的时候
        if($errmsg!="" || $err_arr)
        {
            $flag=2;
        }
        else
        {
            if($chkismobile_flag)
            {
                $errmsg="没有设置手机字段，请联系管理员";
            }
            if($chkisidcard_flag)
            {
                $errmsg="没有设置身份证字段，请联系管理员";
            }

            $obj = new \app\admin\module\Order();
            //$ordersn=getOrderSn();


            if($content_info)
            {
                $ischarge=$content_info['ischarge'];
            }

            if($ischarge==2 && $price > 0)
            {
                // api模块 - 包含各种系统主动发起的功能
                $api = new \think\wx\Api(
                    array(
                        'appId' => trim($config['appid']),
                        'appSecret'    => trim($config['appsecret']),
                        'mchId'    => trim($config['mchid']),
                        'key'    => trim($config['paykey'])
                    )
                );

                $bl=db("order")->where(array("ordersn"=>$ordersn))->update(array("transaction_id"=>$ordersn));

                $data=$this->wechatpay($api,$ordersn,$content_info['chrtitle'].'['. $payname.']',$price,$dataID);
                $config=$api->get_jsapi_config();
                $this->assign('data',$data);
                $this->assign('config',$config);

            }
            
        }
        //查询该活动是否可以分享优惠券
        $activity_cashed_set = db('activity_cashed_card_set')->field('cashed_plan_id,is_share_cashed,activity_id')->where(['activity_id'=>$dataID,'is_share_cashed'=>1])->find();
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        $url =$roottpl.'/order/signuppost.html';
        $this->assign('flag',$flag);
        $this->assign('price',$price);
        $this->assign('errmsg',$errmsg);
        $this->assign('err_arr',$err_arr);
        $this->assign('roottpl','/'.$roottpl);
        $this->assign('sitecode',$sitecode);
        $this->assign('dataID',$dataID);
        $this->assign('ischarge',$ischarge);
        $this->assign('ordersn',$ordersn);
        $this->assign('order_id',$result_order['id']);//订单id
        $this->assign('activity_cashed_set',$activity_cashed_set);//活动现金券的设置
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));

        return $this->fetch($url);
    }


    //个人信息
    public function mine(){
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));

        //确认用也户已登陆
        if($this->setuserinfo(ROOTURL.url("/".$sitecode."/mine"))==false)
        {
            echo "请联系管理，配置好公众号和平台绑定";
            exit();
        }
        $userid=$this->getUserInfo('userid');

        $userRow=[];//个人信息
        //$collection=[];//我的收藏
        // $comment=[];//我的评论
        // $signup=[];//我的报名

        $idsite=$config['id'];


        $userid=$this->getUserInfo('userid');

        $userRow = db('member')->where('idsite='.$idsite.' and  idmember='.$userid)->find();
        $ismanage=$userRow['ismanage'];
        //判断session中的值是否跟数据库保持一致，如果不是的话，那么就重新设置session
        if($ismanage != $this->getUserInfo('ismanage')){
            session("UserInfo_ismanage",empty($userRow['ismanage'])?0:1);
        }
        // 判断是否购买营销包
        $isbuy = false;
        if(checkedMarketingPackage($idsite,'integral')){
            $isbuy = true;
        }
        // $collection = db('collection')->where('idsite='.$idsite.' and  userid='.$userid)->select();
        // $comment = db('comment')->where('idsite='.$idsite.' and  iduser='.$userid)->select();
        // $signup = db('order')->where('idsite='.$idsite.' and  fiduser='.$userid)->select();

        //echo "<br><br><br><br><br>".$idsite."==".$userid."==".session("UserInfo_siteid");

        //cache('config'.$request['idsite']);
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        $url = $roottpl.'/mine/index.html';

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('idsite',$idsite);
        $this->assign('sitecode',$sitecode);
        $this->assign('userinfo',$userRow);
        $this->assign('ismanage',$ismanage);
        $this->assign('isbuy',$isbuy);
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));
        // $this->assign('comment',$comment);
        // $this->assign('signup',$signup);

        $this->assign('SelectFooterTab',1);
        return $this->fetch($url);
    }

    public function signuplist()
    {

        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        $userid=$this->getUserInfo('userid');
        $ipage=empty($request['ipage'])?0:$request['ipage'];
        $state=0;
        if(!empty($request['state']))
            $state=$request['state'];

        $where_arr=[];
        $where_arr['idsite']=$idsite;
        $where_arr['fiduser']=$userid;

        if($state==100)
        {
            $where_arr['dtstart']=array('>',date('Y-m-d H:i:s')) ;
        }
        elseif($state==200)
        {
            $where_arr['dtend']=array('<',date('Y-m-d H:i:s')) ;
        }
        elseif($state>0)
        {
            $where_arr['state']=$state;
        }

        //$where_arr['state']=$state;

        $result = db('order')->where($where_arr)->order("id desc")->limit($ipage*$this->PageSize,$this->PageSize)->select();

        // 查询拼团订单状态
        $map = [
            0 => '开启拼团未支付',
            1 => '拼团中',
            2 => '拼团成功',
            3 => '拼团到期',
            4 => '拼团取消'
        ];
        foreach ($result as $key => $value)
        {
            if(empty($value['group_buy_order_id']))
            {
                $result[$key]['group_buy_order_state'] = '';
            }else
            {
                $groupBuyOrder = db('group_buy_order')->where([
                        'group_buy_order_id' => $value['group_buy_order_id']
                    ])
                    ->field(['state', 'group_buy_id'])
                    ->find();
                $groupBuyOrder['state'] = $groupBuyOrder['state'] ? : 0;
                $result[$key]['group_buy_order_state_name'] = $map[$groupBuyOrder['state']];
                $result[$key]['group_buy_order_state'] = $groupBuyOrder['state'];

                $groupBuy = db('group_buy')->field('allow_refund')->find($groupBuyOrder['group_buy_id']);
                $result[$key]['isrefund'] = $groupBuy['allow_refund'];
            }
        }

        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        //获得栏目列表模版路径
        $url =$roottpl.'/mine/order_list.html';
        if (Request::instance()->isPost() && isset($request['ajax'])) {
            $url = $roottpl . '/mine/ajax_mine_order_list.html';
        }

        //dump($result);
        $this->assign('roottpl','/'.$roottpl);
        $this->assign('sitecode',$sitecode);
        $this->assign('list',$result);
        $this->assign('idsite',$idsite);
        $this->assign('userid',$userid);
        $this->assign('SelectFooterTab',1);
        $this->assign('order_state',config('order_state'));
        $this->assign('state',$state);
        $this->assign('order_paytype1',config('order_paytype1'));
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));

        return $this->fetch($url);

    }

    public function signupmanagelist()
    {

        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        $userid=$this->getUserInfo('userid');
        $state=0;
        $ipage=empty($request['ipage'])?0:$request['ipage'];
        if(!empty($request['state']))
            $state=$request['state'];

        $txtkey=isset($request["txtkey"])?trim($request["txtkey"]):"";

        $where_arr=[];

        if($this->getUserInfo('ismanage')!=1)
        {
            //echo "你没有权限操作！";
            //exit();
        }

        $where_arr['idsite']=$idsite;

        if($state==100)
        {
            $where_arr['dtstart']=array('>',date('Y-m-d H:i:s')) ;
        }
        elseif($state==200)
        {
            $where_arr['dtend']=array('<',date('Y-m-d H:i:s')) ;
        }
        elseif($state>0)
        {
            $where_arr['state']=$state;
        }
        if(!empty($txtkey))
        {
            $where_arr["txtdata"]=array('like','%'.$txtkey.'%');
        }

        $result = db('order')->where($where_arr)->order("id desc")->limit($ipage*$this->PageSize,$this->PageSize)->select();
        // 查询拼团订单状态
        $map = [
            0 => '开启拼团未支付',
            1 => '拼团中',
            2 => '拼团成功',
            3 => '拼团到期',
            4 => '拼团取消'
        ];
        foreach ($result as $key => $value)
        {
            if(empty($value['group_buy_order_id']))
            {
                $result[$key]['group_buy_order_state'] = '';
            }else
            {
                $state = db('group_buy_order')->where([
                        'group_buy_order_id' => $value['group_buy_order_id']
                    ])
                    ->value('state');

                $result[$key]['group_buy_order_state'] = $map[$state];
            }
        }

        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        //获得栏目列表模版路径

        $url = $roottpl . '/mine/ordermanage_list.html';
        if (Request::instance()->isPost() && isset($request['ajax'])) {
            $url = $roottpl . '/mine/ajax_order_list.html';
        }

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('txtkey',$txtkey);
        $this->assign('sitecode',$sitecode);
        $this->assign('state',$state);
        $this->assign('list',$result);
        $this->assign('idsite',$idsite);
        $this->assign('SelectFooterTab',1);
        $this->assign('order_state',config('order_state'));
        $this->assign('order_paytype1',config('order_paytype1'));

        return $this->fetch($url);

    }


    /**
     * 退款申请
     * @return   [type]                   [description]
     */
    public function refund()
    {
        if(Request::instance()->isPost()==false)
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'非法操作')));
        }

        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];

        $orderid=$request["orderid"];
        $content=$request["content"];
        $upfile="";


        $datainfo=db("order")->where(array('id'=>$orderid,'idsite'=>$idsite))->find();
        if(empty($datainfo))
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'报名订单不存在')));
        }
        if(!empty($datainfo['group_buy_order_id']))
        {
            $groupBuyOrder = db('group_buy_order')->find($datainfo['group_buy_order_id']);
            $groupBuy = db('group_buy')->find($groupBuyOrder['group_buy_id']);
            if($groupBuy['allow_refund'] != 1)
            {
                exit(json_encode(array( 'state'=>0,'msg'=>'该报名订单不允许退款')));
            }
        }elseif($datainfo['isrefund']!=1)
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'该报名订单不允许退款')));
        }

        //处理文件
        if(!empty($_FILES['image']) && !empty($_FILES['image']['name']))
        {
            $file_name=$_FILES['image']['name'];//文件名字
            //$file_type=$_FILES['image']['type'];//文件类型
            //$file_site=$_FILES['image']['size'];//文件大小
            //
            $ext=substr($file_name, strrpos($file_name, '.'));
            $upfile='/public/uploads/'.$idsite ."/order/".date('Y',time())."/".date("m-d",time());
            $filepath= __ROOT__ ."/public". $upfile;
            $newfilename=date("YmdHis",time()).rand(10000,99999).$ext;
            if(is_dir($filepath)==false)
            {
                mkdir($filepath,0777,true);
            }
            $file_tmp_name= $_FILES['image']['tmp_name'];//上传文件路径
            $upfile=$upfile."/".$newfilename;
            move_uploaded_file($file_tmp_name,$filepath."/".$newfilename);//把图片移到服务器目录
        }

        $arr=[];
        if(empty($result['refundsn']))
        {
            $arr['refundremark']=$content;//退款原因
            $arr['refundpic']=$upfile;//退款上传图片
        }else
        {
            $arr['refundmsg2']=$content;//退款原因
            $arr['refundpic2']=$upfile;//退款上传图片
        }
        $arr['dtrefundtime']=date("Y-m-d H:i:s");
        $arr['state']=5;
        $arr['intflag']=6;


        if(db("order")->where(array('id'=>$orderid,'idsite'=>$idsite))->update($arr))
        {
            //替换信息
            $replace = [];
            //给客户和商务发短信通知    类型：3--申请退款
            sysSendMsg($idsite, 3, array_merge($datainfo, $arr), $replace);
            template_tg($orderid);
            exit(json_encode(array( 'state'=>1,'msg'=>'退款申请已提交成功')));
        }else
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'退款申请已提交失改')));
        }
    }


    public function orderdetail()
    {

        $request = Request::instance()->param();
        $id=$request['id'];
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];

        $row = db('order')->where(array('id'=>$id))->find();

        if($row && $row['group_buy_order_id'])
        {
            $package = db('package')->find($row['package_id']);
            $row['payname'] = $package['keyword1'] . ' ' . $package['keyword2'] . ' 拼团价' . $row['price'] / $row['paynum'] . ' (原价 ' . $package['member_price'] . ')';
        }

        $frmdata=[];

        if($row && !empty($row['txtdata']))
        {

            $row2= explode("☆", $row['txtfield']);
            $row1= explode("☆", $row['txtdata']);
            foreach($row1 as $k=>$vo) {
                $arr = explode("∫", $row2[$k]);
                $datatype = 1;
                $datafield = $arr[0];
                if (count($arr) > 1) {
                    $datatype = $arr[1];
                }
                if($datatype==7)
                {
                    $frmdata[$datafield]='<img src="'.$vo.'" style="border: 0px;height: 50px;padding: 1px;"  />';
                }
                else
                {
                    $frmdata[$datafield]=$vo;
                }

            }
        }

        $frmdatasub=[];

        if($row && !empty($row['txtdata1']))
        {

            $row2= explode("☆", $row['txtfield1']);

            $frmdatasubRow=explode("§", $row['txtdata1']);

            foreach ($frmdatasubRow as $k1=>$vo)
            {
                $row1= explode("☆", $vo);
                $frmdata1=[];
                foreach($row1 as $k=>$vo) {
                    $arr = explode("∫", $row2[$k]);
                    $datatype = 1;
                    $datafield = $arr[0];
                    if (count($arr) > 1) {
                        $datatype = $arr[1];
                    }
                    if($datatype==7)
                    {
                        $frmdata1[$datafield]='<img src="'.$vo.'" style="border: 0px;height: 50px;padding: 1px;"  />';
                    }
                    else
                    {
                        $frmdata1[$datafield]=$vo;
                    }
                }
                $frmdatasub[]=$frmdata1;

            }

        }

        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        //获得栏目列表模版路径
        $url =$roottpl.'/mine/order_detail.html';

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('sitecode',$sitecode);
        $this->assign('orderinfo',$row);
        $this->assign('frmdatasub',$frmdatasub);
        $this->assign('frmdata',$frmdata);
        $this->assign('idsite',$idsite);
        $this->assign('order_state',config('order_state'));
        $this->assign('order_paytype1',config('order_paytype1'));
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));

        return $this->fetch($url);

    }

    public function ordermanagedetail()
    {

        $request = Request::instance()->param();
        $id=$request['id'];
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];

        if($this->getUserInfo('ismanage')!=1)
        {
            echo "你没有权限操作！";
            exit();
        }

        //审核通过|不通过
        if (Request::instance()->isPost()) {
            ob_end_clean();
            $flag=$request['flag'];
            if(empty($flag) || !in_array($flag, [2, 3]))
            {
                echo "0";
                exit();
            }

            $arr=[];
            $arr['state']= $flag;

            db('order')->where(array('id'=>$id))->update($arr);

            $tmp_Result= db('order')->where(array('id'=>$id))->find();
            if($tmp_Result)
            {
                SetUserPayCount($tmp_Result['fiduser']);
            }
            template_bm($id);

            $obj = new \app\admin\module\Order;
            Log::debug('前端审核，参数是' . print_r([$id, $flag], true));
            $obj->signInNotice($id, $idsite);

            echo "1";
            exit();

        }


        $row = db('order')->where(array('id'=>$id))->find();
        if($row && $row['group_buy_order_id'])
        {
            $package = db('package')->find($row['package_id']);
            $row['payname'] = $package['keyword1'] . ' ' . $package['keyword2'] . ' 拼团价' . $row['price'] / $row['paynum'] . ' (原价 ' . $package['member_price'] . ')';
        }

        $frmdata=[];

        if($row && !empty($row['txtdata']))
        {
            $row2= explode("☆", $row['txtfield']);
            $row1= explode("☆", $row['txtdata']);
            foreach($row1 as $k=>$vo) {
                $arr = explode("∫", $row2[$k]);
                $datatype = 1;
                $datafield = $arr[0];
                if (count($arr) > 1) {
                    $datatype = $arr[1];
                }
                if($datatype==7)
                {
                    $frmdata[$datafield]='<img src="'.$vo.'" style="border: 0px;height: 50px;padding: 1px;"  />';
                }
                else
                {
                    $frmdata[$datafield]=$vo;
                }

            }
        }

        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        //获得栏目列表模版路径


        $url =$roottpl.'/mine/ordermanage_detail.html';

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('sitecode',$sitecode);
        $this->assign('orderinfo',$row);
        $this->assign('frmdata',$frmdata);
        $this->assign('idsite',$idsite);
        $this->assign('order_state',config('order_state'));
        $this->assign('order_paytype1',config('order_paytype1'));

        return $this->fetch($url);

    }

    public function collection()
    {
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];

        $userid=$this->getUserInfo('userid');
        $ipage=empty($request['ipage'])?0:$request['ipage'];
        $page_size = 10;
        $collection = db('collection')->where('idsite='.$idsite.' and  userid='.$userid)->limit($ipage*$page_size,$page_size)->select();

        //cache('config'.$request['idsite']);
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        $url = $roottpl.'/mine/collection.html';
        if (Request::instance()->isPost() && isset($request['ajax'])) {
            $url = $roottpl . '/mine/ajax_collection.html';
        }

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('idsite',$idsite);
        $this->assign('sitecode',$sitecode);
        $this->assign('collection',$collection);
        return $this->fetch($url);

    }
    function addcomment(){
        $request = Request::instance()->param();

        $userinfo=db('member')->where(array('idmember'=>$this->getUserInfo('userid')))->find();

        $flag=empty($request['flag'])?1:$request['flag'];
        $arr=[];
        $arr['iduser']=$this->getUserInfo('userid');
        $arr['username']=$userinfo['nickname'];
        $arr['userimg']=$userinfo['userimg'];
        $arr['idsite']=$request['idsite'];
        $arr['dataid']=$request['dataid'];
        $arr['chrtitle']=$request['chrtitle'];
        $arr['content']=$request['content'];
        $arr['flag']=$flag;
        $arr['createtime']=time();
        $arr['intstate']=2;
        $arr['show']=1;
        //print_r($arr);
        $bool=db('comment')->insert($arr);
        //评论添加成功
        //TODO 新增评论短信提醒
        // if($bool)
        // {
        //     $activity = db('activity')
        //         ->field([
        //             'short_title',
        //             'intselmarket'
        //         ])
        //         ->where([
        //             'idactivity' => $request['dataid']
        //         ])
        //         ->find();
        //     $order = [];
        //     $replace = [
        //         '{name}' => $arr['username'],
        //         '{title}' => $activity['short_title'],
        //     ];
        //     //给客户和商务发短信通知    类型：12--回复评论
        //     sysSendMsg($idsite, 12, $order, $replace, $activity['intselmarket']);
        // }
        echo "1";
        exit();

    }
    public function comment()
    {
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $key=empty($request['key'])?'':$request['key'];
        $idsite=$config['id'];
        $flag=I('flag',0);
        $userid=$this->getUserInfo('userid');
        $ipage=empty($request['ipage'])?0:$request['ipage'];
        $page_size = 10;
        $arr=[];
        $arr['idsite']=$idsite;
        $arr['iduser']=$userid;
        if($flag>0)
        {
            $arr['flag']=$flag;
        }

        if($key!='')
        {
            $arr['content']=array('like',"%".$key."%");
        }

        $comment = db('comment')->where($arr)->limit($ipage*$page_size,$page_size)->select();
        //cache('config'.$request['idsite']);
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        $url = $roottpl.'/mine/comment.html';
        if (Request::instance()->isPost() && isset($request['ajax'])) {
            $url = $roottpl . '/mine/ajax_comment.html';
        }

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('idsite',$idsite);
        $this->assign('sitecode',$sitecode);
        $this->assign('list',$comment);
        $this->assign('SelectFooterTab',1);
        $this->assign('flag',$flag);
        return $this->fetch($url);

    }


    public function commentmanage()
    {
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $key=empty($request['key'])?'':$request['key'];
        $idsite=$config['id'];
        $flag=I('flag',0);
        $userid=$this->getUserInfo('userid');
        $ipage=empty($request['ipage'])?0:$request['ipage'];
        $page_size = 10;

        if (Request::instance()->isPost())
        {
            $id=$request['dataid'];

            $comment = db('comment')
                ->field('id')
                ->where([
                    'id' => $id,
                    'idsite' => $idsite,
                    'intstate' => 4,
                    'recontent' => $request['content'], 
                ])
                ->find();
            if($comment)
            {
                echo '1';
                exit();
            }
            $comment = db('comment')
                ->field([
                    'dataid',
                    'iduser',
                ])
                ->where([
                    'id' => $id,
                    'idsite' => $idsite,
                ])
                ->find();


            $data=[];
            $data['intstate']=4;
            $data['retime']=time();
            $data['recontent']=$request['content'];
            $data['reid']=$this->getUserInfo('userid');
            $data['rename']=$this->getUserInfo('nickname');
            $bool=db('comment')->where(array('id'=>$id,'idsite'=>$idsite))->update($data);
            if($bool)
            {
                Log::debug('微信前端回复评论后发短信 ' . print_r($comment, true));
                $obj = new \app\admin\module\Comment;
                $obj->replyCommentNotice($comment['dataid'], $comment['iduser'], $idsite);
            }else
            {
                echo "0";
            }
            exit();
        }


        $arr=[];
        $arr['idsite']=$idsite;
        if($flag>0)
        {
            $arr['flag']=$flag;
        }

        if($key!='')
        {
            $arr['content']=array('like',"%".$key."%");
        }

        $comment = db('comment')->where($arr)->order("intstate asc,id desc")->limit($ipage*$page_size,$page_size)->select();
        //cache('config'.$request['idsite']);
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        $url = $roottpl.'/mine/commentmanage.html';

        if (Request::instance()->isAjax() && isset($request['ajax'])) {
            $url = $roottpl . '/mine/ajax_commentmanage.html';
        }

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('idsite',$idsite);
        $this->assign('sitecode',$sitecode);
        $this->assign('list',$comment);
        $this->assign('SelectFooterTab',1);
        $this->assign('flag',$flag);
        return $this->fetch($url);

    }

    public function delcomment(){
        $request = Request::instance()->param();
        $userid=$this->getUserInfo('userid');
        $id=I('id',0);
        if($id==0)
        {
            echo '0';
            exit();
        }

        $arr=[];
        $arr['id']=$id;
        if($this->getUserInfo('ismanage')!=1)
        {
            $arr['iduser']=$userid;
        }

        $bool=db('comment')->where($arr)->delete();

        if($bool)
            echo "1";
        else
            echo "0";

        exit();

    }

    //详情页面
    public function info(){
        $request = Request::instance()->param();
        $content_id = $request['contentid']; // 当前内容id

        //检测它的父节点是否需要登录才能浏览
        $content_info = db('content')->where('contentid='.$request['contentid'])->field('siteid,nodeid')->find();
        if(empty($content_info))
        {
            header("location:/error.php?msg=".urlencode("没找到相关文章，有疑问请和管理联系！")."&url=");
            exit();
        }


        $node_info = db('node')->where('nodeid='.$content_info['nodeid'])->find();
        //if(strstr($node_info['option'],'3')){
        //   return "请登录";
        //}

        //该文章的评论列表
        $comment_list = db('comment')->where('dataid='.$content_id." and `show`=1")->select();
        $idsite=$content_info['siteid'];
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        //获得栏目列表模版路径

        $sitecode=$this->getsitecode($idsite);

        $config=$this->getWeiXinConfig(strtolower($sitecode));

        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $api = new \think\wx\Api(
            array(
                'appId' => trim($config['appid']),
                'appSecret'    => trim($config['appsecret']),
            )
        );
        $signPackage=$api->get_jsapi_config($url);
        $this->assign('signPackage',$signPackage);

        if(empty($node_info['templateofnodecontent'])){
            $url = $roottpl.'/'.GetConfigVal("weboption","contentstencil",$idsite); //模版路径
        }else{
            $url =$roottpl.'/'.$node_info['templateofnodecontent'];
        }

        $visitflag=0;
        $userid=$this->getUserInfo("userid");
        if($userid>0)
        {
            $visitinfo=db("collection")->where(array('dataid'=>$content_id,'userid'=>$userid,'flag'=>1))->find();
            if($visitinfo)
                $visitflag=1;

        }


        $this->assign('roottpl','/'.$roottpl);
        $this->assign('comment_list',$comment_list);
        $this->assign('node_info',$node_info);
        $this->assign('visitflag',$visitflag);
        $this->assign('idsite',$idsite);
        $this->assign('content_id',$content_id);
        $this->assign('sitecode',$this->getsitecode($idsite));
        $this->assign('SelectFooterTab',1);
        return $this->fetch($url);
    }

    //文章翻页
    public function updown(){
        $request = Request::instance()->param();

        $model_type = db('model')->where('modeltype=4 and isusing = 1')->find();
        //根据模型id找出对应的模型字段
        $model_fields = db('modelfield')->where('idmodel='.$model_type['idmodel'].' and isusing=1')->select();
        $this->assign('model_field',$model_fields);
        $this->assign('model_id',$model_type['idmodel']);

        $content_info = db('content')->where('contentid='.$request['contentid'])->find();
        $map['nodeid'] = ['eq',$content_info['nodeid']];
        if($request['action'] == 'up'){
            $map['contentid'] = ['<',$content_info['contentid']];
            $order = 'desc';
        }else{
            $map['contentid'] = ['>',$content_info['contentid']];
            $order = 'asc';
        }

        $content = db('content')->where($map)->order('contentid '.$order)->find();

        if(empty($content)){
            $content_id = $content_info['contentid'];
        }else{
            $content_id = $content['contentid'];
        }
        $this->assign('content_id',$content_id);

        //根据模型id找出对应的模型字段
        $model_type = db('model')->where('modeltype=4 and isusing = 1')->find();
        $model_fields = db('modelfield')->where('idmodel='.$model_type['idmodel'].' and isusing=1')->select();
        //该文章的评论列表
        $comment_list = db('comment')->where('contentid='.$content_id.' and intlock=1')->select();

        //获得栏目列表模版路径
        $node_info = db('node')->where('nodeid='.$content_info['nodeid'])->find();
        if(empty($node_info['templateofnodecontent'])){
            $url = ROOT_PATH.GetConfigVal("weboption","stencildir").GetConfigVal("weboption","contentstencil"); //模版路径
        }else{
            $url = ROOT_PATH.GetConfigVal('weboption','stencildir').$node_info['templateofnodecontent'];
        }
        $this->assign('comment_list',$comment_list);
        $this->assign('model_field',$model_fields);
        return $this->fetch($url);
    }

    //留言
    public function comment1(){
        $request = Request::instance()->param();
        foreach ($request as $key=>$value){
            $request[$key] = field_filter($value);
        }

        $request['addtime'] = time();
        $bool = db('comment')->insert($request);
        if($bool){
            return 1;
        }else{
            return 2;
        }
    }

    //验证码
    public function yzm(){
        //ob_end_clean();
        $captcha = new Captcha();
        $captcha->entry();
    }

    //多语言
    public function lang(){

        switch ($_GET['lang']) {
            case 'cn':
                session('aa', 'cn');
                break;
            case 'en':
                session('aa', 'en');
                break;
            case 'tc':
                session('aa', 'tc');
                break;
            //其它语言
        }

    }

    //收藏
    public function addcollection()
    {
        $request = Request::instance()->param();
        $idsite=$request['idsite'];
        $title=$request['title'];
        $ContentID=$request['id'];
        $flag=empty($request['flag'])?1:$request['flag'];

        $arr=[];
        $arr['userid']=$this->getUserInfo('userid');
        $arr['username']=$this->getUserInfo('nickname');
        $arr['idsite']=$idsite;
        $arr['dataid']=$ContentID;
        $arr['chrtitle']=$title;
        $arr['flag']=$flag;
        $arr['createtime']=time();
        //print_r($arr);
        $bool=db('collection')->insert($arr);
        echo "1";
        exit();
    }

    public function delcollection()
    {
        $request = Request::instance()->param();
        $id=$request['id'];
        $arr=[];

        $arr['id']=$id;
        $arr['userid']=$this->getUserInfo('userid');
        //print_r($arr);
        $bool=db('collection')->where($arr)->delete();
        echo "1";
        exit();
    }

    //访问记录
    public function addwaitervisit()
    {
        $request = Request::instance()->param();
        $arr=[];
        $arr["openid"]=$this->getUserInfo("openid");
        $arr["name"]=$this->getUserInfo("nickname");
        $arr["createtime"]=time();
        $arr["idsite"]=$request['idsite'];
        $arr["flag"]=empty($request['flag'])?0:$request['flag'];
        $arr["aid"]=empty($request['id'])?0:$request['id'];

        db("waiter_visit")->insert($arr);
        exit();
    }

    //访问记录
    public function addVisitRecord()
    {
        $request = Request::instance()->param();
        $idsite=$request['idsite'];
        $title=$request['title'];
        $ContentID=$request['id'];
        $flag=empty($request['flag'])?1:$request['flag'];
        $guid=$request['guid'];
        if($flag==1)
        {
            $cate = db('content')->where('contentid='.$ContentID)->setInc('hits',1);
        }
        else
        {
            $cate = db('activity')->where('idactivity='.$ContentID)->setInc('hits',1);
        }
		$source = '其它';
		$http_referer = $_SERVER["HTTP_REFERER"];
		if($http_referer){
			$host = parse_url($http_referer)['host'];
			$sourceArray = config('source');
			if($sourceArray){
				foreach($sourceArray as $key => $value){
                    if($host == $key){
                        $source = $value;
                        break;
                    }
				}
			}
		}else{
			$source = '本站';
		}

        $this->visitdata($idsite,$ContentID,$title,$flag,$guid,$source);
        exit();
    }

    public function addVisitRecorded()
    {
        $request = Request::instance()->param();
        $guid=$request['guid'];
        $tmptime= db('visit_record')->where(array('guid'=>$guid))->field('stime')->find();
        //print_r($tmptime);
        $tmptime1=time();
        $arr=[];
        $arr['etime']=$tmptime1;
        $arr['differtime']=$tmptime1-$tmptime['stime'];
        $bool=db('visit_record')->where(array('guid'=>$guid))->update($arr);
    }
    public function visitdata($idsite,$dataid,$title,$flag,$guid,$source)
    {
        $arr=[];
        $arr['userid']=$this->getUserInfo('userid');
        $arr['username']=$this->getUserInfo('nickname');
        $arr['openid']=$this->getUserInfo('openid');
        $arr['idsite']=$idsite;
        $arr['aid']=$dataid;
        $arr['atitle']=$title;
        $arr['flag']=$flag;
        $arr['guid']=$guid;
		$arr['source']=$source;
        $arr['stime']=time();

        $arr['ip']=$this->ip();
        $arr['createtime']=date('Y-m-d H:i:s');
        //print_r($arr);
        $bool=db('visit_record')->insert($arr);

    }
    public function ip() {
        //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if('::1'==$ip)
        {
            $ip='127.0.0.1';
        }
        $res =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
        return $res;
        //dump(phpinfo());//所有PHP配置信息
    }
    //读取配配信息
    public function getWeiXinConfig($sitecode)
    {
        $config=cache("WeiXinConfig");
        if(empty($config))
        {
            $config=[];
        }

        if(array_key_exists($sitecode,$config)==false)
        {
            $data=db('site_manage')->field('id,site_name,appid,appsecret,token,encodingaeskey,mchid,paykey,cainfo,sslcertpath,sslkeypath')->where(['site_code'=>$sitecode])->find();
            if($data)
            {
                $config[$sitecode]=$data;
                cache("WeiXinConfig",$config);
            }
            else
            {
                echo $sitecode." 不存在！";
                exit();
            }
        }

        return $config[$sitecode];
    }

    private function  getUserInfo($v)
    {

        if(empty(session("UserInfo_".$v)))
        {
            if(empty(session("UserInfo_load")))
            {
                session("UserInfo_load",true);
                $this->getuserinfo1();
            }
            if(empty(session("UserInfo_".$v)))
            {
                return "";
            }
            //session("UserInfo_".$v,"1");
            //return "1";
            //setcookie("nickname","测试账号1");
            //setcookie("userid","1");
        }
        return session("UserInfo_".$v);
    }

    public function getsitecode($idSite)
    {
        $row = db('site_manage')->where('id='.$idSite)->find();
        if($row)
        {
            return $row['site_code'];
        }
        return '';
    }

    private  function _strlen($str)
    {
        preg_match_all("/./us", $str, $matches);
        return count(current($matches));
    }

    private function wechatpay($api,$ordersn,$name,$total,$dataid)
    {

        if(empty($this->getUserInfo('userid')))
        {
            echo '登陆已超时！';
            //return true;
        }

        $request = Request::instance()->param();
        $sitecode=strtolower($request['sitecode']);
        $config=getWeiXinConfig($sitecode);

        $conf=[];
        //$conf['attach']='童享云';
        $length = $this->_strlen($name);
        if($length >=18) {
            $body = mb_substr($name, 0, 15, 'utf-8')."...";
        }else{
            $body = $name;
        }
        $conf['body']= $body;
        $conf['openid']=$this->getUserInfo('openid');
        $conf['notify_url']=ROOTURL."/".$sitecode."/signup_post1/".$dataid.".html" ;
        $conf['out_trade_no']=$ordersn;
        $conf['total_fee']=$total*100;//总金额
        $conf['trade_type']='JSAPI';
        //$conf['scene_info']='{"h5_info": {"type":"Wap","wap_url": "'.ROOTURL.'","wap_name": "童享云"}}';

        $re=$api->wxPayUnifiedOrder($conf);
        Log::info('微信下单返回'.json_encode($re,JSON_UNESCAPED_UNICODE));
        if($re['return_code']=='FAIL')
        {
            print_r($conf);
            echo $re['return_msg'];
            exit();
        }
        if($re['result_code']=='FAIL')
        {
            print_r($conf);
            echo  $re['err_code'];
            exit();
        }

        $data=$api->getWxPayJsApiParameters($re['prepay_id']);
        return $data;

    }
    public function loginlog()
    {
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        $userid=$this->getUserInfo("userid");
        $arr=[];

        if(!empty($request["latitude"])) $arr["latitude"]=$request["latitude"];
        if(!empty($request["longitude"])) $arr["longitude"]=$request["longitude"];
        if(!empty($request["address"])) $arr["address"]=$request["address"];
        $arr["userid"]=$userid;
        $arr["idsite"]=$idsite;
        $arr["ip"]=getip();
        $arr["createtime"]=time();

        $bl=db("login_log")->insert($arr);
        if($bl)
        {
            $tmpData=db("login_log")->field("address,COUNT(*) as vcount")->where(array("userid"=>$userid))->group("address")->order("vcount desc")->limit(0,1)->select();
            if($tmpData)
            {
                $datainfo=db("member")->where(array("idmember"=>$userid))->find();
                if($datainfo)
                {
                    $visitcount=$datainfo["visitcount"];
                    if(empty($visitcount))
                        $visitcount=0;
                    $visitcount=$visitcount+1;
                    if($tmpData[0]["address"]!=$datainfo["chraddress"])
                    {
                        //db("member")->where(array("idmember"=>$userid))->update(array("chraddress"=>$tmpData[0]["address"],"dtlastvisitteim"=>time(),"visitcount"=>$visitcount));
                        db("member")->where(array("idmember"=>$userid))->update(array("chraddress"=>$tmpData[0]["address"]));
                    }
                    else
                    {
                        db("member")->where(array("idmember"=>$userid))->update(array("dtlastvisitteim"=>time(),"visitcount"=>$visitcount));
                    }

                }
            }
            exit(json_encode(array("state"=>1,"msg"=>"")));
        }
        else
        {
            exit(json_encode(array("state"=>0,"msg"=>"写日记失败！")));
        }

    }

    private function loginCount()
    {
        $userid=$this->getUserInfo("userid");
        if(!empty($userid))
        {
            $datainfo=db("member")->where(array("idmember"=>$userid))->find();
            if($datainfo)
            {
                $visitcount=$datainfo["visitcount"];
                if(empty($visitcount))
                    $visitcount=0;

                $visitcount=$visitcount+1;
                db("member")->where(array("idmember"=>$userid))->update(array("dtlastvisitteim"=>time(),"visitcount"=>$visitcount));
            }
        }
    }

    //转发日记
    public function forwardedlog()
    {
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        $userid=$this->getUserInfo("userid");
        $arr=[];

        if(!empty($request["dataid"])) $arr["dataid"]=$request["dataid"];
        if(!empty($request["chrtitle"])) $arr["chrtitle"]=$request["chrtitle"];
        if(!empty($request["chrdesc"])) $arr["chrdesc"]=$request["chrdesc"];
        if(!empty($request["chrlink"])) $arr["chrlink"]=$request["chrlink"];
        if(!empty($request["imgurl"])) $arr["imgurl"]=$request["imgurl"];
        if(!empty($request["datatype"])) $arr["datatype"]=$request["datatype"];
        if(!empty($request["inttype"])) $arr["inttype"]=$request["inttype"];
        $arr["userid"]=$userid;
        $arr["idsite"]=$idsite;
        $arr["ip"]=getip();
        $arr["createtime"]=time();

        $bl=db("forwarded_log")->insert($arr);
        if($bl)
        {
            exit(json_encode(array("state"=>1,"msg"=>"")));
        }
        else
        {
            exit(json_encode(array("state"=>0,"msg"=>"写日记失败！")));
        }
    }

    private function setuserinfo($url)
    {
        /*
        $openid = $this -> getUserInfo("openid");
        if(!empty($openid)) {
            $is_new_subscribe = cache("new_subscribe_" . $openid);
            if (isset($is_new_subscribe) && $is_new_subscribe == 1) {
                cache("new_subscribe_" . $openid, null);
                session_destroy();
            }
        }
        */
        // if(config("new_subscribe_".$openid))
        $request = Request::instance()->param();
        $userid = $this->getUserInfo('userid');
        if((int)$userid > 0)
        {
                return true;
        }else{
            if (!isset($request['code']) || empty($request['code'])) {
                $this->ClearSeaaionInfo();
            }
        }

        $sitecode=strtolower($request['sitecode']);
        $config=getWeiXinConfig($sitecode);

        if(empty($config['appid']))
        {
            return false;
        }
        // api模块 - 包含各种系统主动发起的功能
        $api = new \think\wx\Api(
            array(
                'appId' => trim($config['appid']),
                'appSecret'    => trim($config['appsecret']),
            )
        );

        //file_put_contents( 'file123.txt', json_encode($request)."\r\n",FILE_APPEND);

        if(empty($request['code']))
        {
            //$url='http://www.tongxiang123.cn/userlogin/'.$sitecode;
            //snsapi_userinfo    snsapi_base
            $url=$api->get_authorize_url('snsapi_base',$url);
            header('Location:'.$url);
            exit();
        }
        else
        {
            $result=$api->get_userinfo_by_authorize('snsapi_userinfo');
            if(empty($result[1]->openid))
            {
                return false;
            // print_r($result);
            }
            else
            {
                $openid=$result[1]->openid;
                $userinfo=db('member')->where(array('openid'=>$openid))->find();
                if($userinfo)
                {
                   $arr=[];
                   $arr['intstate']=3;
                   if(!empty($userinfo['unionid']))
                   {
                       $arr['intstate']=1;//会员状态（1审批通过 2取消关注 3游客）
                       $arr['unionid']=$userinfo['unionid'];;
                   }
                   $arr['visitcount']=empty($userinfo['visitcount'])?1:$userinfo['visitcount']+1;
                   $arr['dtlastvisitteim']=time();

                   db('member')->where(array('openid'=>$openid))->update($arr);

                    session("UserInfo_nickname",$userinfo['nickname']);
                    session("UserInfo_openid",$userinfo["openid"]);
                    session("UserInfo_ismanage",empty($userinfo['ismanage'])?0:1);
                    session("UserInfo_openid",$userinfo['openid']);
                    session("UserInfo_userid",$userinfo['idmember']);
                }
                else
                {
                    $this->addUser($result[1],$config['id']);
                }
                return true;
            }
        }
    }

    //增加用户
    public function addUser($data,$siteid)
    {

        /*
        {    "openid":" OPENID",
            " nickname": NICKNAME,
            "sex":"1",
            "province":"PROVINCE"
            "city":"CITY",
            "country":"COUNTRY",
            "headimgurl":    "http://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/46",
            "privilege":[ "PRIVILEGE1" "PRIVILEGE2"     ],
            "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
          }
        */
        $bool=false;
        if(empty($data) || !isset($data->openid))
            return $bool;

        if(!empty($data->province) && !empty($data->city)) {
            $tmpIDs = getRegionIDs($data->province, $data->city);
            $arr['intcity'] = $tmpIDs[1];
            $arr['intprovince'] = $tmpIDs[0];
        }
        $arr['openid']=$data->openid;
        $arr['chrname']=empty($data->nickname)?"游客": $arr['chrname']=$data->nickname;
        $arr['nickname']=empty($data->nickname)?"游客":$data->nickname;
        if(!empty($data->sex)) $arr['intsex']=$data->sex;
        if(!empty($data->headimgurl)) $arr['userimg']=$data->headimgurl;//$this->getUserImg($data->headimgurl,$siteid);
        $arr['dtcreatetime']=time();
        $dtsubscribetime =0 ;
        //$arr['unionid']=$data->unionid;
        //$arr['subscribe_scene']=$data->subscribe_scene;
        //$arr['qr_scene']=$data->qr_scene;
        $arr['intstate']=3;//会员状态（1审批通过 2取消关注 3游客）
        if(!empty($data->unionid))
        {
            $arr['intstate']=1;//会员状态（1审批通过 2取消关注 3游客）
            $arr['unionid']=$data->unionid;
            $dtsubscribetime = time();
            $arr['dtsubscribetime'] = $dtsubscribetime;
        }
        //$arr['qr_scene_str']=$data->qr_scene_str;
        $arr['intlock']=2;//1锁定  2 未锁定
        $arr['idsite']=$siteid;


        $userinfo=db('member')->where(array('openid'=>$data->openid,'idsite'=>$siteid))->find();
        if(!$userinfo)
        {
            //$arr['visitcount']=1;
            //$arr['dtlastvisitteim']=time();
            $bool=db('member')->insert($arr);

            //添加会员状态日志
            $logArr = $arr;
            $logArr["openid"] = $data->openid;
            $logArr["idmember"] =db('member')->getLastInsID();
            member_log($logArr,1);

            $userinfo=db('member')->where(array('openid'=>$data->openid,'idsite'=>$siteid))->find();
        }

        if($userinfo)
        {
            session("UserInfo_nickname",$userinfo["nickname"]);
            session("UserInfo_openid",$userinfo["openid"]);
            session("UserInfo_ismanage",empty($userinfo["ismanage"])?0:$userinfo["ismanage"]);
            session("UserInfo_userid",$userinfo['idmember']);
            session("UserInfo_siteid",$userinfo['idsite']);
        }
        return $bool;
    }

    //取得用户头像
    public function getUserImg($url,$SiteID)
    {

        $path='public/uploads/'.$SiteID.'/Member/photo';
        if(is_dir($path)==false)
        {
            mkdir($path, 0777, true);
        }
        $path=$path."/".getNumber().".jpg";
        //$url ='http://mmbiz.qpic.cn/mmbiz/PGkxayImcuhpTfGWiagtAY1R8L7C1licueqssxnJSJJntscaUrK6vAiakqo4RXdv2bud2ic3YicVbvIghLFhGc5ByyA/0';
        file_put_contents($path, file_get_contents($url));
        return $path;
    }

    function isMobile() {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return 0;//true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile','MicroMessenger');
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

    //清空用户Session变量

    public function getuserinfo1()
    {
        if(strstr(getip(),'192.168.168'))
        {
            $userinfo=db('member')->where(array('openid'=>"oZS4v1aiMfreRinDgG-uWZFEDpnk"))->find();
           // $userinfo=db('member')->find(534);
            if($userinfo)
            {
                session("UserInfo_nickname",$userinfo["nickname"]);
                session("UserInfo_openid",$userinfo["openid"]);
                session("UserInfo_ismanage",0);
                session("UserInfo_userid",$userinfo['idmember']);
                session("UserInfo_siteid",$userinfo['idsite']);
            }
            return;
        }


        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];

        if($this->isMobile()==false || (empty($request['type'])==false && $request['type']=='test'))
            return;

        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $api = new \think\wx\Api(
            array(
                'appId' => trim($config['appid']),
                'appSecret'    => trim($config['appsecret']),
            )
        );
        if(empty($request['code']))
        {
            //snsapi_base snsapi_userinfo
            $url= $api->get_authorize_url('snsapi_base',$url,1);
            //echo $url;
            header("location:".$url);
            exit();
        }
        $result= $api->get_userinfo_by_authorize('snsapi_userinfo');

        //print_r($result);
        $this->addUser($result[1],$idsite);
        //exit(session("UserInfo_nickname"));
    }


    /**
     * 短信支付通知
     */
    public function sms_pay_notify()
    {
        try {
            $input = file_get_contents("php://input");
            $sign = $return_code = $out_trade_no =  "";
            $total_fee = 0;
            $xml = "";
            if ($input) {
                $xml = simplexml_load_string($input,'SimpleXMLElement', LIBXML_NOCDATA);
                //$money = (string)$xml->total_fee;

                file_put_contents( 'sms_pay_notify.txt', print_r(json_decode(json_encode((array)$xml), 1),true),FILE_APPEND);
                $return_code = (string)$xml->return_code;
                //$attach = (string)$xml->attach;
                $out_trade_no = (string)$xml->out_trade_no;
                $total_fee = $xml->total_fee;
                $sign = $xml->sign;
            }
            if (strtoupper($return_code)  == 'SUCCESS') {
                file_put_contents( 'sms_pay_notify.txt',"代码执行到这里",FILE_APPEND);

                //获取订单信息
                $order_info = db('sms_order')->where(array('order_sn' => $out_trade_no))->find();
                if(empty($order_info)){
                    echo 'fail';
                }else {
                    $sitecode = getSiteCode(7);
                    $config = getWeiXinConfig(strtolower($sitecode));
                    $key = $config['paykey'];

                    //签名验证
                    $verify_sign = SHA1::getSign2($this -> object_array($xml), 'key='.$key);
                    if($sign == $verify_sign) {

                        $order_price = round($order_info["order_price"], 2);
                        //判断校验返回的订单金额是否与商户侧的订单金额一致
                        if (($order_price*100) != $total_fee) {
                            echo "fail";
                        } else {
                            //更新支付状态，支付金额以及支付时间
                            $arr = [];
                            $arr['status'] = 1;
                            $arr['pay_price'] = $total_fee / 100;
                            $arr['pay_time'] = date("Y-m-d H:i:s");
                            $bool = db('sms_order')->where(array('order_sn' => $out_trade_no, "status" => 0))->update($arr);
                            if ($bool) {
                                $sms_num = (int)$order_info["sms_num"];
                                $idsite = $order_info["idsite"];
                                $site_manage_info = db("site_manage")->where(array("id" => $idsite))->find();
                                //更新短信可发送数量
                                $update_arr = array();
                                $update_arr["sms_num"] = (int)$site_manage_info["sms_num"] + $sms_num;
                                $update_arr["sms_total_num"] = (int)$site_manage_info["sms_total_num"] + $sms_num;
                                $update_arr["sms_recharge_total_money"] = round($site_manage_info["sms_recharge_total_money"], 2) + round($order_price, 2);
                                $rs = db("site_manage")->where(array("id" => $idsite))->update($update_arr);
                                if($rs){
                                    //写入短信线上充值记录
                                    $log_arr = array();
                                    $log_arr["sms_num"] = $sms_num;
                                    $log_arr["recharge_price"] = $order_price;
                                    $log_arr["idsite"] = $idsite;
                                    $log_arr["type"] = 1;
                                    $log_arr["create_time"] = date("Y-m-d H:i:s");
                                    $log_arr["ip"] = getip();
                                    db("sms_recharge_log")->insert($log_arr);
                                }
                                echo 'success';
                            } else {
                                echo 'fail';
                            }
                        }
                    }else{
                        echo 'fail';
                    }
                }
            } else {
                echo 'fail';
            }
        } catch (Exception $ex) {
            echo 'fail';
        }
        exit;
    }

    //对象转数组
    private function object_array($array)
    {
        if (is_object($array)) {
            $array = (array)$array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = $this -> object_array($value);
            }
        }
        return $array;
    }


    public function groupBuyShare()
    {
        $request = Request::instance()->param();

        $userID=$this->getUserInfo('userid');

        $sharer = db('member')->find($request['sharer_id']);

        $orders = db('order')->where([
                'group_buy_order_id' => $request['group_buy_order_id'],
                // 4.已报名 已支付，
                // 5.已报名 退款中，
                // 6已部分退款 继续服务，
                // 7已退款 继续服务，
                // 8.已报名 退款不通过，
                'state' => ['in', [4,5,6,7,8]],
            ])
            ->order('id asc')
            ->select();

        if(!$orders)
        {
            return false;
        }

        $imgs = db('member')->where([
                'idmember' => ['in', array_column($orders, 'fiduser')]
            ])
            ->field(['idmember', 'nickname', 'userimg'])
            ->select();

        $groupBuyOrder = db('group_buy_order')->find($request['group_buy_order_id']);
        $package = db('package')->find($orders[0]['package_id']);

        $data = [
            'isStarter' => $userID == $orders[0]['fiduser'],
            'isNew' => !in_array($userID, array_column($orders, 'fiduser')),
            'expiration' => $groupBuyOrder['expire_at'],
            'chrtitle' => $orders[0]['chrtitle'],
            'original_price' => $package['member_price'],
            'group_buy_price' => $orders[0]['price'] / $orders[0]['paynum'],
            'package_name' => $package['keyword1'] . ' ' . $package['keyword2'],
            'group_num' => $groupBuyOrder['group_num'],
            'left' => $groupBuyOrder['group_num'] - $groupBuyOrder['sold'],
            'imgs' => $imgs,
            'state' => $groupBuyOrder['state'],
            'username' => $sharer['nickname'],
        ];

        $groupBuy = db('group_buy')->find($groupBuyOrder['group_buy_id']);

 //dump($groupBuy);die;
        $config=$this->getWeiXinConfig(strtolower($request['sitecode']));
        $idsite=$config['id'];
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);

        $shareUrl = ROOTURL . "/{$request['sitecode']}/group_buy_share/{$request['group_buy_order_id']}/{$request['sharer_id']}";
        $view = 'join';
        if(!$data['isNew'])
        {
            $config=$this->getWeiXinConfig(strtolower($request['sitecode']));
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
        return $this->fetch($url);
    }


    public function groupBuyList()
    {
        //session_destroy();
        $request = Request::instance()->param();
        $page = isset($request['page']) ? intval($request['page']) : 1;
        $pageSize = isset($request['pageSize']) ? intval($request['pageSize']) : 10;
        $sitecode=$request['sitecode'];
        if(empty($sitecode))
        {
            echo "站点不存在！";
            exit();
        }
        //确认用户已登陆
        $this->setuserinfo(ROOTURL.url("/".$sitecode));

        $config=$this->getWeiXinConfig(strtolower($sitecode));
        $idsite=$config['id'];
        //cache('config'.$request['idsite']);
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);

        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $api = new \think\wx\Api( array(
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ));

        $signPackage=$api->get_jsapi_config($url);
        $this->assign('signPackage',$signPackage);

        $url = $roottpl . '/assemble/assemble_list.html';

        // 获取拼团数据
        $groupBuys = GroupBuy::getList($idsite, $page, $pageSize);

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('idsite',$idsite);
        $this->assign('groupBuys',$groupBuys);
        $this->assign('sitecode',$sitecode);
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));
        return $this->fetch($url);
    }
}