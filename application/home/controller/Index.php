<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/3
 * Time: 9:37
 */

namespace app\home\controller;
use think\Exception;
use think\exception\ErrorException;
use think\Image;
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
use think\Session;

class Index extends BaseAuth {

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
        $config=$this->wxConfig;

        $api = new \think\wx\Api(
            array(
                'appId' => trim($config['appid']),
                'appSecret'    => trim($config['appsecret']),
            )
        );
        $imgurl= $api->get_qrcode_url_by_str('213556489798798798798');
        $template_url="template/weixin.html";
        $this->assign('msg',"本产品要求关注后才能报名<br>请扫下面二维码关注");
        $this->assign('imgurl',$imgurl);
        return $this->fetch($template_url);

    }
    public function qrcodeurl()
    {
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;

        $api = new \think\wx\Api(
            array(
                'appId' => trim($config['appid']),
                'appSecret'    => trim($config['appsecret']),
            )
        );
        $imgurl= $api->get_qrcode_url();
        return $imgurl;
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
        $userid = $this->userinfo['idmember'];

        $config = $this->wxConfig;
        $idsite=$config['id'];
        //cache('config'.$request['idsite']);
        $temp1=GetConfigVal("weboption","rootdir",$idsite);
        $tmp_style='';
        if(strstr($temp1,'_'))
        {
            $arr=explode('_',$temp1);

            $temp=$arr[0];
            $tmp_style=$arr[1];
        }
        else
        {
            $temp=$temp1;
        }

        if($temp == ''){
            $temp='M1';
        }
        $roottpl = 'template/'.$temp;
        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $api = new \think\wx\Api( array(
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ));
        $signPackage=$api->get_jsapi_config($url);
        $this->assign('signPackage',$signPackage);
        $url = $roottpl.'/'.GetConfigVal("weboption","indexfilename",$idsite); //模版路径

        // 签到是否开启
        $is_sign = 0;
        if(checkedMarketingPackage($idsite,'integral')){
            $is_sign = db('integral_rule_config')->where('idsite',$idsite)->value('is_sign');
        }

        // 获取首页拼团数据
        $groupBuys = GroupBuy::getList($idsite);

        //访问记录
        $this->loginCount();
        //$url="template/M2/index.html";
        //$tmp_style='m';
        $this->assign('tmp_style',$tmp_style);
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
        $config=$this->wxConfig;
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

        $roottpl = 'template/modules/';
        $this->assign('roottpl','/'.$roottpl);
        $url = $roottpl.'/mine/signin.html';

        $userid=$this->userinfo['idmember'];
        //确认用户已登陆
        if($userid<1)
        {
            $msg="请联系管理，配置好公众号和平台绑定";
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

    /**
     * 签到数据加载
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-18 15:55:04
     */
    public function signinloaddata(){
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        $config = $this->wxConfig;
        $idsite = $config['id'];
        $url = isset($request['url']) ? $request['url'] : '';
        $flag = isset($request['flag'])? $request['flag'] : 1;
        $checkcode = isset($request['checkcode'])? $request['checkcode'] : '';
        $operation = isset($request['operation'])? $request['operation'] : '';
        $param = explode("/", $url);
        $type = isset($param[2]) ? $param[2] : $request['signintype'];  // 数据类型
        $id = isset($param[3]) ? $param[3] : 0;                       // 数据ID


        $msg = '';              // 提示消息
        $row = [];              // 产品数据
        $frmdata = [];          // 表单数据
        $frmdatasub = [];       // 子表单数据
        $subscriberecord = [];  // 预约记录
        $roottpl = '';          // 模板目录
        $template_url = '';     // 模板地址
        // 产品签到
        if($type == 'signin'){
            // 产品签到数据加载
            if((empty($operation) && !empty($id) || empty($operation) && !empty($checkcode)))
            {
                if($id){
                    $row = db('order')->where(array('ordersn'=>$id,'idsite'=>$idsite))->find();
                }else{
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
                }
                else
                {
                    exit("没有找到相关订单数据");
                }
            }

            // 产品点击签到
            if ($operation=='signin' && !empty($id) || $operation=='signin' && !empty($checkcode))
            {
                $userid = $this->userinfo['idmember'];
                if(empty($userid) || $userid<1)
                {
                    exit(json_encode(array("state"=>2,"msg"=>"管理员账号不存在，请确认后重新签到。")));
                }

                if($msg=="")
                {
                    $userinfo=db("member")->where(array("idmember"=>$userid,'idsite'=>$idsite))->find();
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
                $arr["singntype"]= !empty($id) ? 1 : 2;//1扫码签到，2输码签到，3电脑签到
                $arr["signuserid"]=$userinfo['idmember'];
                $arr["signusername"]=$userinfo['nickname'];
                $arr["dtsigntime"]=date("Y-m-d H:i:s",time());
                $map = [
                    'idsite'=>$idsite,
                ];
                if($id){
                    $map['ordersn'] = $id;
                }else{
                    $map['checkcode'] = $checkcode;
                }
                $result = db('order')->where($map)->update($arr);
                if($result)
                {
                    //获取订单信息
                    $order = db('order')->where($map)->find();
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
            $roottpl = 'template/modules/';
            $template_url = $roottpl.'/mine/ajax_signin.html';
        }else if($type == 'subscribe'){
            // 预约签到数据加载
            if(empty($operation) && !empty($id) || empty($operation) && !empty($checkcode))
            {
                if(empty($checkcode))
                {
                    $subscriberecord = db('subscribe_record')->where(array('id'=>$id,'siteid'=>$idsite))->find();
                }
                else
                {
                    $subscriberecord = db('subscribe_record')->where(array('checkcode'=>$checkcode,'siteid'=>$idsite))->find();
                }

                if(!$subscriberecord)
                {
                    exit("没有找到相关预约数据");
                }
            }
            // 预约点击签到
            if ($operation=='subscribe' && !empty($id) || $operation=='subscribe' && !empty($checkcode))
            {
                $userid = $this->userinfo['idmember'];
                if(empty($userid) || $userid<1)
                {
                    $msg = "管理员账号不存在，请确认后重新签到。";
                }

                if($msg=="")
                {
                    $userinfo=db("member")->where(array("idmember"=>$userid,'idsite'=>$idsite))->find();
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
                $arr["is_signin"] = 1;
                $arr["signin_way"] = !empty($id) ? 1 : 2;//1扫码签到，2输码签到，3电脑签到
                $arr["signin_member_id"] = $userinfo['idmember'];
                $arr["signin_member_nickname"] = $userinfo['nickname'];
                $arr["signin_time"] = time();
                $map = [
                    'siteid'=>$idsite,
                ];
                if($id){
                    $map['id'] = $id;
                }else{
                    $map['checkcode'] = $checkcode;
                }
                $result = db('subscribe_record')->where($map)->update($arr);
                if($result)
                {
                    $subscribe_record = db('subscribe_record')->where($map)->find();
                    $mobile = db('subscribe_member_cart')->where('id',$subscribe_record['member_cart_id'])->value('mobile');
                    $week = isset($subscribe_record['week']) ? $subscribe_record['week'] : '';
                    $period = isset($subscribe_record['week']) ? $subscribe_record['period'] : '';
                    $place = isset($subscribe_record['place']) ? $subscribe_record['place'] : '';
                    $replace = [
                        '{title}' => $subscribe_record['subscribe_object_name'].$week.$period.$place,
                        '{name}' => $subscribe_record['member_nickanme'] ?: '',
                    ];
                    sysSendMsg($idsite, 13, [], $replace, $mobile);
                    exit(json_encode(array("state"=>1,"msg"=>"签到成功！")));
                }
                else
                {
                    exit(json_encode(array("state"=>2,"msg"=>"签到失败！")));
                }
            }
            $roottpl = 'template/modules/';
            $template_url = $roottpl.'/subscribe/ajaxsubscribesignin.html';
        }

        $this->assign('checkcode',$checkcode);
        $this->assign('signintype',$type);
        $this->assign('url',$url);
        $this->assign('sitecode',$sitecode);
        $this->assign('orderinfo',$row);
        $this->assign('frmdatasub',$frmdatasub);
        $this->assign('frmdata',$frmdata);
        $this->assign('subscriberecord',$subscriberecord);
        $this->assign('order_state',config('order_state'));
        $this->assign('order_paytype1',config('order_paytype1'));
        return $this->fetch($template_url);
    }

    public function usermodi()
    {
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];

        $config=$this->wxConfig;
        $idsite=$config['id'];
        $roottpl = 'template/modules/';
        $url = $roottpl.'/mine/usermodi.html';

        $userid=$this->userinfo['idmember'];
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

    // 机构二维码
    public function siteqrcode(){
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        $config = $this->wxConfig;
        $idsite = $config['id'];
        $roottpl = 'template/modules/';

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
        $config=$this->wxConfig;
        $idsite=$config['id'];

        if(isset($request['p'])){
            $ipage = $request['p'];

        }else{
            $ipage = 1;
        }
        $pagesize = 10;
        $this->assign("ipage",$ipage);


        $node_info = db('node')->where('idsite='.$idsite.' and nodeid='.$request['nodeid'])->find();
        if(empty($node_info))
        {
            header("location:/error.php?msg=".urlencode("栏目不存在，有疑问请和管理联系！")."&url=".urlencode("/".$sitecode));
            exit();
        }

        $obj = new \app\admin\module\activity($idsite);

        $zxbq=$obj->getDic("zxbq");
        $bq=[];
        foreach($zxbq as $v){
            $bq[$v['code']]=$v['name'];
        }
        $this->assign('zxbq',$bq);


        //获得栏目列表模版路径
        $roottpl = 'template/modules/node/';

    //    if($node_info['listtype']==2)
    //         $url =  $roottpl.'index_m.html';
    //     elseif($node_info['listtype']==3)
    //         $url =  $roottpl.'index_b.html';
    //     else{
    //         $url =  $roottpl.'index.html';
    //     }

          $url =  $roottpl.'index.html';
//        if(empty($node_info['templateofnodelist'])){
//            $url =  $roottpl.'/'.GetConfigVal("weboption","columnlist",$idsite); //模版路径
//        }else{
//            $url =  $roottpl.'/'.$node_info['templateofnodelist'];
//        }

        if (Request::instance()->isPost() && isset($request['ajax'])) {
            // echo json_encode($request);exit;
            // if($node_info['listtype']==2)
            //     $url =  $roottpl.'ajax_index_list_m.html';
            // elseif($node_info['listtype']==3)
            //     $url =  $roottpl.'ajax_index_list_b.html';
            // else{
            //     $url =  $roottpl.'ajax_index_list.html';
            // }
            $url =  $roottpl.'ajax_index_list.html';
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

    //产品
    public function activity(){
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];

        $config=$this->wxConfig;
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
        $show_field="idactivity,chrtitle,chrimg_m,chrimg,chrurl,chrsummary,dtstart,dtpublishtime,hits,s.is_receive_cashed,a.usertype,a.idactivity,min_price,ischarge";
        $result = db('activity')->alias('a')->join('activity_cashed_card_set s','a.idactivity=s.activity_id','left')->where($search)->order("chkcontentlev desc,contentlevtime desc,dtpublishtime desc")->field($show_field)->limit($offset,$pagesize)->select();

        $totalRecord = db('activity')->alias('a')->join('activity_cashed_card_set s','a.idactivity=s.activity_id','left')->where($search)->order("chkcontentlev desc,contentlevtime desc,dtpublishtime desc")->field($show_field)->count();

        $totalPage = ceil($totalRecord/$pagesize);
        //获得栏目列表模版路径
        $roottpl = 'template/modules/';

        $url = $roottpl . 'activity/index.html';

        if (Request::instance()->isPost() && isset($request['ajax'])) {
            $url = $roottpl . 'activity/ajax_index_list.html';
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
        $this->assign('totalPage',$totalPage);
        $this->assign('pageIndex',$ipage);
        $this->assign('sitecode',$sitecode);
        $this->assign('SelectFooterTab',2);
        $this->assign('nodeid',$nodeid);
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));
        return $this->fetch($url);
    }

    //产品详情页面
    public function detail(){
        $request = Request::instance()->param();
        $id = $request['id']; // 当前内容id
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite=$config['id'];
        $userid=$this->userinfo['idmember'];
        $link_url='';
        $link_img='';
//dump($request);die;
        //支付过后的弹窗页面
        $content = 0;
        if(isset($request['jump']) && $request['jump'] == 1){
            $content=1;
        }

        if(empty( $request['type'])) {

        }
        //检测它的父节点是否需要登录才能浏览
        //$content_info = db('activity')->where('idactivity='.$id)->field('siteid')->find();

        //$comment_list = db('comment')->where('id='.$id.' and intlock=1')->select();
        $roottpl = 'template/modules/';

        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $api = new \think\wx\Api( array(
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ));

        $signPackage=$api->get_jsapi_config($url);
        $this->assign('signPackage',$signPackage);
        $bmflag=0;
            // if(!empty( $request['type'])) {
            $activity = $datainfo=db("activity")->field("chkissubscribe, idactivity,usertype,chrimg,is_distribution,chrimg_m")->where(array('idactivity'=>$id))->find();
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
            //查询该产品的现金券设置信息
        $activity_cashed = db("activity_cashed_card_set")->where(array('activity_id'=>$id,'is_receive_cashed'=>1))->find();

        // 查询产品中的拼团
        $packages = db('package')->where([
                'activity_id' => $activity['idactivity'],
                'expire_at'=>['gt',time()]
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

            $now1=time();
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
                    '\'' . $package['package_name'] . '\'' . ' package_name',
                    "expire_at - $now1 expiration"
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
                    ->field(['fiduser','state'])
                    ->order('id asc')
                    ->find();
                if(empty($firstOrder)){
                    unset($tmpGroupBuyOrders[$key]);
                    continue;
                }
                //如果第二个待支付的人未付款，第三个却付款了，团长如何改变？
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
            $userid=$this->userinfo['idmember'];
            $usertype=db('member')->where(['idmember'=>$userid])->column('categoryid');
            $usertype=$usertype[0];
            //获取产品的用户权限
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

            if(strpos($activityusertype,','.$usertype.',') === false){
                $usertypeflag=1;
                $this->assign('usertypeflag',$usertypeflag);
                $this->assign('usertype',$usertypename);
            }
        }


        //获得栏目列表模版路径
        $url =$roottpl.'/activity/detail.html';

        $user_info=db("member")->field("intstate",true)->where(array('idmember'=>$userid))->find();

        //查询该用户对该产品的领取情况
        $user_receive_info = db('cashed_card_receive')->field('id')->where(['receive_member_id'=>$userid,'cashed_type'=>2,'receive_activity_id'=>$id,'site_id'=>$idsite])->find();

        $share_id = 0;
        $a = 0;
        //是否是新用户
        $is_new_user = $this->is_new_user;
        //默认当前页面
        $link_url = ROOTURL.$_SERVER['REQUEST_URI'];
        //分享图片为产品的小图片
        $link_img = ROOTURL."{$activity['chrimg_m']}";
        //判断链接进来的人是不是代言人并且机构开启了分销
        if(checkedMarketingPackage($idsite,'distribution')){
            //看是否有分享链接进来的
            if(array_key_exists('share_id',$request)){
                $share_id = $request['share_id'];
            }
            //看是否有通过海报二维码进来的
            if(array_key_exists('a',$request)){
                $a = $request['a'];
            }
            //查询该机构的分销设置
            $spokesman_set_item = db('spokesman_set_item')->field('create_time',true)->where(['site_id'=>$idsite])->find();
            //查看分销方式，如果机构设置的是人人代言，为了让其每个人的分享都带有分享id
            if($spokesman_set_item['distribution_methods'] == 2){
                $link_url = ROOTURL."/{$sitecode}/detail/{$id}?share_id={$userid}";
                //判断用户的分享是否用自己的头像
                if($user_info['is_use_header_img'] == 1){
                    //分享图片为分享人的头像
                    $link_img = $user_info['userimg'];
                }else{
                    if(!$activity['chrimg_m']){
                        //2019年8月26号将用户的头像换成了固定的图片
                        $link_img = ROOTURL.'/static/images/share_postman.jpg';
                    }
                }
            }
            //如果是代言人（2019年10月12号开始改变为，只要是代言人，那么其分享的产品都是带分享id的，目的是可以锁定用户）
            if($user_info['spokesman_grade'] != 0){
                $link_url = ROOTURL."/{$sitecode}/detail/{$id}?share_id={$userid}";
                //判断用户的分享是否用自己的头像
                if($user_info['is_use_header_img'] == 1){
                    //分享图片为分享人的头像
                    $link_img = $user_info['userimg'];
                }else{
                    if(!$activity['chrimg_m']){
                        //2019年8月26号将用户的头像换成了固定的图片
                        $link_img = ROOTURL.'/static/images/share_postman.jpg';
                    }
                }
                //普通人进来
            }else{
                //如果有分享id并且是新加的用户
                if(($share_id || $a) && $is_new_user){
                    $lock_user_id = $share_id?$share_id:$a;
                    //查找出分享用户的信息
                    $lock_user_info=db("member")->where(array('idmember'=>$lock_user_id,'idsite'=>$idsite))->find();
                    //如果有设置过锁客，并且是浏览活动即锁客,还有未锁客过的,有分享用户的信息，并且分享用户是代言人
                    if($spokesman_set_item && $spokesman_set_item['lock_way'] ==  5 && $user_info['intlock'] != 1 && $lock_user_info && $lock_user_info['spokesman_grade'] != 0){
                        //进行修改用户的锁客信息,改为已锁客
                        $update_user_data = [
                            'intlock'=>1,'lock_user_id'=>$lock_user_id,'lock_u_chrname'=>$lock_user_info['u_chrname'],
                            'lock_u_chrtel'=>$lock_user_info['u_chrtel'],'lock_nick_name'=>$lock_user_info['nickname'],'lock_time'=>date('Y-m-d H:i:s',time())
                        ];
                        $update_user_bool = db("member")->where(array('idmember'=>$userid,'idsite'=>$idsite))->update($update_user_data);
                        //如果锁客成功并且需要发送锁客通知
                        if($update_user_bool && $spokesman_set_item['lock_notice'] == 1){
                            template_tg_lock($lock_user_info,$user_info);
                        }
                    }
                }
            }
        }
        $this->assign('roottpl','/'.$roottpl);
        //$this->assign('comment_list',$comment_list);
        $this->assign('bmflag',$bmflag);
        $this->assign('qrcodeurl',$this->qrcodeurl());
        $this->assign('qrcodeurl_str',$api->get_qrcode_url_by_str(json_encode(['data_id'=>$id,'share_id'=>$share_id,'a'=>$a])));//带有参数的公众号二维码(生成带有分享人的参数的二维码)
        $this->assign('idsite',$idsite);
        $this->assign('visitflag',$visitflag);
        $this->assign('id',$id);
        $this->assign('groupBuys',$groupBuys);
        $this->assign('groupBuyOrders',$groupBuyOrders);
        $this->assign('completeGroupNum',$completeGroupNum);
        $this->assign('sitecode',$this->getsitecode($idsite));
        $this->assign('activity_cashed',$activity_cashed);
        $this->assign('SelectFooterTab',2);
        $this->assign('user_info',$user_info);
        $this->assign('link_url',$link_url);
        $this->assign('link_img',$link_img);
        $this->assign('share_id',$share_id);//分销的用户id
        $this->assign('a',$a);//二维码的用户id
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));
        $this->assign('is_distribution',checkedMarketingPackage($idsite,'distribution'));
        $this->assign('user_receive_info',$user_receive_info);
        $this->assign('is_new_user',$is_new_user);
        $this->assign('pay_after',$content);
        return $this->fetch($url);
    }

    //产品详情领取优惠券的页面(因为改过授权的继承,这里是将这个ajax请求还原在index处)
    public function receive_cashed(){
        $request = Request::instance()->param();
        $id = $request['id']; // 当前产品id
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite=$config['id'];

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
        $userid=$this->userinfo['idmember'];
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


    /**
     * 手动取消订单
     * @return \think\response\Json
     * @throws \think\exception\PDOException
     */
    public function cancel_order(){
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $userID=$this->userinfo['idmember'];
        $err_arr = [];
        //查询该用户的待支付订单
        $expiredOrders = db('order')->where(
            [
                'state' => 12,
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
                    'receive_cashed_id',
                    'group_buy_order_id',
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
            //判断是否为团购产品，
            if($expiredOrders['group_buy_order_id'] != '' && $expiredOrders['group_buy_order_id'] != 0)
            {
                // 拼团订单，释放库存路径不同
                $groupBuyOrder = new GroupBuyOrder;
                $res = $groupBuyOrder->releaseGroupBuyOrderStock($expiredOrders['group_buy_order_id'], $expiredOrders['paynum']);
            }else
            {
                $res = changeStock($expiredOrders['package_id'], $expiredOrders['paynum'], false);
            }
            //$res = changeStock($expiredOrders['package_id'], $expiredOrders['paynum'], false);
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
        $userID=$this->userinfo['idmember'];
        $order_id=$request['id'];

        $userinfo=db('member')->where(array('idmember'=>$userID,'intstate'=>['neq',2]))->find();

        $config=$this->wxConfig;
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
        if(!empty($order_info['group_buy_order_id']) && $order_info['group_buy_order_id']!=0)
        {
            $groupBuyOrderId = $order_info['group_buy_order_id'];
            $groupinfo=db('group_buy_order')->where(['group_buy_order_id' => $groupBuyOrderId])->field(['group_buy_id','state','group_num'])->find();
            $groupBuyId = $groupinfo['group_buy_id'];
            $groupBuyOrderState=$groupinfo['state'];
            if($groupBuyOrderState ==0){
                //开团
                $this->assign('groupjoin',0);
                $this->assign('group_num',$groupinfo['group_num']);
//                dump($groupjoin);
            }elseif($groupBuyOrderState ==1){
                $this->assign('groupjoin',1);
//                dump($groupjoin);
            }
        }
        if(!$order_info){
            $err_arr = ['err'=>'未找到该用户的订单'];
        }else{
            $time = date('Y-m-d H:i:s',time());
            //查询该订单购买的产品信息(已上架并且开启报名)
            $activity_info = db('activity')->where(['idactivity'=>$order_info['dataid'],'intflag'=>2,'chksignup'=>1])->find();
            if(!$activity_info){
                $err_arr = ['err'=>'该产品不处于已上架状态或者未开启报名'];
            }else{
                //判断产品是否还处于可以报名
                if(time() < strtotime($activity_info['dtstart']) || time() > strtotime($activity_info['dtend'])){
                    $err_arr = ['err'=>'抱歉，该产品已结束'];
                }
                if(time() < strtotime($activity_info['dtsignstime']) || time() > strtotime($activity_info['dtsignetime'])){
                    $err_arr = ['err'=>'抱歉，该产品未处于允许的下单时间内'];
                }else{
                    //查询该产品的设置可用券的情况
                    $activity_cashed = db("activity_cashed_card_set")->where(array('activity_id'=>$order_info['dataid'],'is_use_cashed'=>1))->find();
                    if($activity_cashed){
                        //查询用户的可用现金券的列表
                        $where['site_id'] = ['=',$idsite];
                        $where['receive_member_id'] = ['=',$userID];
                        $where['cashed_validity_time'] = ['>=',$time];
                        $where_str = "(cashed_type = 2 and receive_activity_id = {$order_info['dataid']} ) or (cashed_type in(1,3,4) and used_status = 1)";
                        if($order_info['receive_cashed_id']){
                            //2019年7月25修改为  将该订单冻结的现金券也展示出来
                            $where_str .= "or (id in({$order_info['receive_cashed_id']}) and used_status = 10)";
                        }
                        $cashed_list = db('cashed_card_receive')->field('freeze_time',true)->where($where)->where($where_str)->order('create_time desc')->select();
//                        die($cashed_list);
                        $cashed_list_count = db('cashed_card_receive')->where($where)->where($where_str)->count();
                        //如果该活动的最大可使用张数，大于现金券列表的数量，就显示现金券列表的数量
                        if($activity_cashed['max_use'] > $cashed_list_count){
                            $activity_cashed['max_use'] = $cashed_list_count;
                        }
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

        if(!empty($order_info['group_buy_order_id']) && $order_info['group_buy_order_id']!=0)
        {
            $activity_cashed = [];
        }

        $roottpl = 'template/modules/';
        //获得栏目列表模版路径
        $url =$roottpl.'/order/again_order.html';

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('userinfo',$userinfo);
        $this->assign('sitecode',$sitecode);
        $this->assign('err_arr',$err_arr);//错误信息
        $this->assign('id', $order_info['dataid']);//产品id
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
        $this->assign('SelectFooterTab',2);
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));
        return $this->fetch($url);
    }

    public function waiter(){
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite=$config['id'];

        $roottpl = 'template/modules/';
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
        $userID=$this->userinfo['idmember'];
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


        $userinfo=db('member')->where(array('idmember'=>$userID,'intstate'=>['neq',2]))->find();

        $config=$this->wxConfig;
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
        //查询该产品的设置可用券的情况
        $groupBuy = db('group_buy')->find($groupBuyId);
        if($groupBuy && $groupBuy['allow_coupon'] == 0)
        {
            $activity_cashed = [];
        }else
        {
            $activity_cashed = db("activity_cashed_card_set")->where(array('activity_id'=>$id,'is_use_cashed'=>1))->find();
        }
//        halt($activity_cashed);
        if($activity_cashed){
            //查询用户的可用现金券的列表
            $where['site_id'] = ['=',$idsite];
            $where['receive_member_id'] = ['=',$userID];
            $where['cashed_validity_time'] = ['>=',$time];
            $where['used_status'] = ['=',1];
            $cashed_list = db('cashed_card_receive')->field('freeze_time',true)->where($where)->where("(cashed_type = 2 and receive_activity_id = {$request['id']} ) or cashed_type in(1,3,4)")->order('create_time desc')->select();
            $cashed_list_count = db('cashed_card_receive')->where($where)->where("(cashed_type = 2 and receive_activity_id = {$request['id']} ) or cashed_type in(1,3,4)")->count();
            //如果该活动的最大可使用张数，大于现金券列表的数量，就显示现金券列表的数量
            if($activity_cashed['max_use'] > $cashed_list_count){
                $activity_cashed['max_use'] = $cashed_list_count;
            }
        }else{
            $cashed_list = [];
            $cashed_list_count = 0;
        }
        $roottpl = 'template/modules/';
        //获得栏目列表模版路径
        $url =$roottpl.'/order/signup.html';
        $share_id = 0;
        //看是否有通过海报二维码进来的
        if(array_key_exists('a',$request) && !empty($request['a'])){
            $share_id = intval($request['a']);
        }
        //看是否是通过分享链接过来的
        if(array_key_exists('share_id',$request) && !empty($request['share_id'])){
            $share_id = intval($request['share_id']);
        }
        //如果两个都不为空的话,那么就是当扫码进来的,取扫码的id
        if(array_key_exists('a',$request) && !empty($request['a']) && array_key_exists('share_id',$request) && !empty($request['share_id'])){
            $share_id = intval($request['a']);
        }
        $is_new_user = 0;
        //看是否是新用户2019年10月13号
        if(array_key_exists('is_new_user',$request) && !empty($request['is_new_user'])){
            $is_new_user = intval($request['is_new_user']);
        }
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
        $this->assign('share_id',$share_id);//分享id
        $this->assign('is_new_user',$is_new_user);//分享id
        return $this->fetch($url);
    }

    /**
     * 免费|收费产品报名
     * ---------------------------------------------------------
     * 职责
     * ---------------------------------------------------------
     * 验证登录状态
     * 创建|查询订单（收集报名表单数据并保存、校验）(校验产品限制)
     * 判断产品限制（关注、手机号、身份证，收费）
     * 扣除库存
     * 发起支付
     * ---------------------------------------------------------
     * @DateTime 2019-04-23T17:18:24+0800
     * @return   [type]                   [description]
     */
    public function signup_post_bak()
    {
        //获取传入的参数及初始化各种变量
        $request = Request::instance()->param();
//        var_dump($request);exit;
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite=$config['id'];
        $dataID =$request['id']; //id
        $userID=$this->userinfo['idmember'];
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
            $content_info = db('activity')->where(['idactivity' => $dataID])->field('is_distribution,share_time,chkissubscribe,ischarge,chkismobile,chkisidcard,intmaxpaynum,intmaxmobilepaynum,intmaxidcardpaynum,chrtitle,selsignfrom')->find();

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
                $config=$this->wxConfig;
                $api = new \think\wx\Api( array(
                    'appId' => trim($config['appid']),
                    'appSecret'    => trim($config['appsecret']),
                ));

                $imgurl= $api->get_qrcode_url();
                $template_url="template/weixin.html";
                $this->assign('msg',"本产品要求关注后才能报名<br>请扫下面二维码关注");
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
                        if(!isset($request['group_buy_order_id']) || $request['group_buy_order_id'] ==0 )
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
                            //如果是参团的，要先减掉原来占用的库存
                                if(isset($request['groupjoin']) && $request['groupjoin']==1){
                                     $paynum_be=db('order')->where('id',$request['order_id'])->value('paynum');
                                    $stock=$groupBuyOrder['group_num']-$groupBuyOrder['sold']+$paynum_be;
                                }else{
                                    $stock = $groupBuyOrder['group_num'] - $groupBuyOrder['sold'];
                                 }

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

                //事务，添加订单、锁定库存
//                Db::startTrans();
//                try
//                {
                    //创建新连接，专用于事务操作
//                    $query = new Query;
//                    $query->startTrans();
                    $order_info = [];
                    $activity_cashed_set = [];
                    $spokesman_order = [];//代言人订单相关数据
                    //查询出产品的信息
                    $activity_info = db('activity')->field('chrkeyword',true)->where(['idactivity'=>$dataID,'siteid'=>$idsite])->find();
                    //查看是否是分销商品
                    if($activity_info['is_distribution'] == 1){
                        #region    判断购买代言产品的时候  是否需要生成已代言产品的数据
                        $spokesman_activity = ['default'=>1];
                        $parent_spokesman_activity = ['default'=>1];
                        //查询购买人的信息
                        $buy_user_info = db('member')->field('idmodel',true)->where(['idmember'=>$userID,'idsite'=>$idsite])->find();
                        $request['share_id'] = array_key_exists('share_id',$request)?$request['share_id']:'';
                        //判断是否有分享用户id
                        if($request['share_id']){
                            //查询出该代言人的用户信息
                            $share_info = db('member')->field('idmodel',true)->where(['idmember'=>$request['share_id'],'idsite'=>$idsite])->find();
                            if($share_info){
                                //查询出是否具有生成已代言的产品信息
                                $spokesman_activity = db('spokesman_activity')->field('site_id',true)->where(['activity_id'=>$dataID,'site_id'=>$idsite,'spokesman_user_id'=>$request['share_id']])->find();
                                //插入的数据
                                $spokesman_user_id = $request['share_id'];
                                $spokesman_nick_name = $share_info['nickname'];
                                $spokesman_name = $share_info['u_chrname'];
                                //看有没有上级
                                if($share_info['parent_user_id']){
                                    //查询出上级是否具有生成已代言的产品信息
                                    $parent_spokesman_activity = db('spokesman_activity')->field('site_id',true)->where(['activity_id'=>$dataID,'site_id'=>$idsite,'spokesman_user_id'=>$share_info['parent_user_id']])->find();
                                    $parent_spokesman_user_id = $share_info['parent_user_id'];
                                    $parent_spokesman_nick_name = $share_info['parent_nick_name'];
                                    $parent_spokesman_name = $share_info['parent_u_chrname'];
                                }
                            }
                        }else{
                            //判断购买人是否是代言人,如果是的话
                            if($buy_user_info['spokesman_grade'] != 0) {
                                //看有没有上级
                                if ($buy_user_info['parent_user_id']) {
                                    //查询出上级是否具有生成已代言的产品信息
                                    $parent_spokesman_activity = db('spokesman_activity')->field('site_id', true)->where(['activity_id' => $dataID, 'site_id' => $idsite, 'spokesman_user_id' => $buy_user_info['parent_user_id']])->find();
                                    $parent_spokesman_user_id = $buy_user_info['parent_user_id'];
                                    $parent_spokesman_nick_name = $buy_user_info['parent_nick_name'];
                                    $parent_spokesman_name = $buy_user_info['parent_u_chrname'];
                                }
                                $spokesman_user_id = $userID;
                                $spokesman_nick_name = $buy_user_info['nickname'];
                                $spokesman_name = $buy_user_info['u_chrname'];
                                //查询出是否具有生成已代言的产品信息
                                $spokesman_activity = db('spokesman_activity')->field('site_id', true)->where(['activity_id' => $dataID, 'site_id' => $idsite, 'spokesman_user_id' => $userID])->find();
                                //否则就是用户购买,如果有分享id(这时候可能就是用户直接通过海报二维码下单)
                            }
                        }
                        //如果没有代言过,没有代言数据
                        if(!$spokesman_activity){
                            //进行数据库的插入
                            db("spokesman_activity")->insert([
                                'activity_id'=>$dataID,
                                'chrtitle'=>$activity_info['chrtitle'],
                                'chrimg'=>$activity_info['chrimg_m'],
                                'dtstart'=>$activity_info['dtstart'],
                                'dtend'=>$activity_info['dtend'],
                                'dtsignstime'=>$activity_info['dtsignstime'],
                                'dtsignetime'=>$activity_info['dtsignetime'],
                                'spokesman_time'=>date('Y-m-d H:i:s',time()),
                                'spokesman_user_id'=>$spokesman_user_id,
                                'spokesman_nick_name'=>$spokesman_nick_name,
                                'spokesman_name'=>$spokesman_name,
                                'get_commission'=>0,
                                'site_id'=>$idsite,
                            ]);
                            //进行修改用户的产品代言个数
                            db('member')->where(['idmember'=>$spokesman_user_id,'idsite'=>$idsite])->setInc('spokesman_activity_num',1);
                        }
                        //上级代言
                        if(!$parent_spokesman_activity){
                            //进行数据库的插入
                            db("spokesman_activity")->insert([
                                'activity_id'=>$dataID,
                                'chrtitle'=>$activity_info['chrtitle'],
                                'chrimg'=>$activity_info['chrimg_m'],
                                'dtstart'=>$activity_info['dtstart'],
                                'dtend'=>$activity_info['dtend'],
                                'dtsignstime'=>$activity_info['dtsignstime'],
                                'dtsignetime'=>$activity_info['dtsignetime'],
                                'spokesman_time'=>date('Y-m-d H:i:s',time()),
                                'spokesman_user_id'=>$parent_spokesman_user_id,
                                'spokesman_nick_name'=>$parent_spokesman_nick_name,
                                'spokesman_name'=>$parent_spokesman_name,
                                'get_commission'=>0,
                                'site_id'=>$idsite,
                            ]);
                            //进行修改用户的产品代言个数
                            db('member')->where(['idmember'=>$parent_spokesman_user_id,'idsite'=>$idsite])->setInc('spokesman_activity_num',1);
                        }
                        #endregion

                        #region判断是否有分销的分享用户id
                        if($request['share_id']){
                            $share_id = $request['share_id'];
                            //查询出该代言人的用户信息
                            $share_info = db('member')->field('idmodel',true)->where(['idmember'=>$share_id,'idsite'=>$idsite])->find();
                            if($share_info){
                                #region  判断分享人是否有上级
                                if($share_info['parent_user_id']){
                                    //代言人获得销售佣金
                                    $spokesman_order['spokesman_user_id3'] = $share_id;
                                    $spokesman_order['spokesman_name3'] = $share_info['u_chrname'];
                                    $spokesman_order['spokesman_nick_name3'] = $share_info['nickname'];
                                    $spokesman_order['sell_commission'] = $paynum * $package['sell_commission'];//获得的佣金是销售佣金
                                    //上级获得奖励金
                                    $spokesman_order['spokesman_user_id2'] = $share_info['parent_user_id'];
                                    $spokesman_order['spokesman_name2'] = $share_info['parent_u_chrname'];
                                    $spokesman_order['spokesman_nick_name2'] = $share_info['parent_nick_name'];
                                    $spokesman_order['bounty_commission2'] = $paynum * $package['bounty_commission'];//获得的佣金是奖励金
                                }else{
                                    $spokesman_order['spokesman_user_id3'] = $share_id;
                                    $spokesman_order['spokesman_name3'] = $share_info['u_chrname'];
                                    $spokesman_order['spokesman_nick_name3'] = $share_info['nickname'];
                                    $spokesman_order['sell_commission'] = $paynum * ($package['sell_commission'] + $package['bounty_commission']);//代言人获得的佣金是销售佣金和奖励金
                                }
                                #endregion
                            }
                        }else{
                            //判断购买人是否是代言人,如果是的话
                            if($buy_user_info['spokesman_grade'] != 0) {
                                //没有分享id,那么就是自己购买,判断有没有上级,有的话,那么就是自己只得销售金,没有的话,就是得销售金和奖励金
                                if ($buy_user_info['parent_user_id']) {
                                    //代言人获得销售佣金
                                    $spokesman_order['spokesman_user_id3'] = $userID;
                                    $spokesman_order['spokesman_name3'] = $buy_user_info['u_chrname'];
                                    $spokesman_order['spokesman_nick_name3'] = $buy_user_info['nickname'];
                                    $spokesman_order['sell_commission'] = $paynum * $package['sell_commission'];//代言人获得的佣金是销售佣金
                                    //上级获得奖励金
                                    $spokesman_order['spokesman_user_id2'] = $buy_user_info['parent_user_id'];
                                    $spokesman_order['spokesman_name2'] = $buy_user_info['parent_u_chrname'];
                                    $spokesman_order['spokesman_nick_name2'] = $buy_user_info['parent_nick_name'];
                                    $spokesman_order['bounty_commission2'] = $paynum * $package['bounty_commission'];//获得的佣金是奖励金
                                }
                                //否则  那么就没有上级的话  是一级代言人自己购买
                                $spokesman_order['spokesman_user_id3'] = $userID;
                                $spokesman_order['spokesman_name3'] = $buy_user_info['u_chrname'];
                                $spokesman_order['spokesman_nick_name3'] = $buy_user_info['nickname'];
                                $spokesman_order['sell_commission'] = $paynum * ($package['sell_commission'] + $package['bounty_commission']);//代言人获得的佣金是销售佣金和奖励金
                            }
                        }
                        #endregion
                    }
                    if(isset($request['group_buy_id']) && !empty($request['group_buy_id']))
                    {

                        $groupBuyOrderModel = new GroupBuyOrder;
                        if(isset($request['group_buy_order_id']) && !empty($request['group_buy_order_id']) && $request['group_buy_order_id'] !=0)
                        {
                            $groupBuyOrderId=$request['group_buy_order_id'];
                            //如果post请求就是参团，需要锁定本次订单库存
                            //如果是get请求，修改团购表，修改order表
                            if(isset($_GET['group_buy_order_id']) && array_key_exists('order_id',$request) && !array_key_exists('groupjoin',$request)){
                                $orderid=$request['order_id'];
                                 //修改拼团order
                                $paynum_before=db('order')->where(['id'=>$orderid])->value('paynum');
                                if($paynum != $paynum_before){
                                    $res=db('group_buy_order')->update(['sold'=>$paynum,'group_buy_order_id'=>$groupBuyOrderId]);
                                }else{
                                    $res=1;
                                }
                                //修改order
                                $transaction_id=getOrderSn();
                                $obj = new \app\admin\module\Order();
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
                                    0, //$cashed_amount,
                                    '', //$receive_cashed_id,
                                    '', //$share_plan_id,
                                    $txtfield1,
                                    $txtdata1,
                                    $ischarge,
                                    $transaction_id,
                                    $request['group_buy_order_id'],
                                    $request['order_id']//订单id
                                );
                                $ajaxdate['order_id']=$request['order_id'];
                                $ajaxdate['group_buy_order_id']=$request['group_buy_order_id'];

                                if(!$ordersn)
                                {
                                    throw new Exception('订单创建失败');
                                }


                            }else{
                                $groupBuyOrderId=$request['group_buy_order_id'];

                                //用户参与拼团
                                //第一次拉起支付
                                if(isset($_POST['group_buy_order_id']) && !array_key_exists('order_id',$request) && !array_key_exists('groupjoin',$request)){
                                    $groupBuyOrderId = $request['group_buy_order_id'];
                                    $res = $groupBuyOrderModel->join($groupBuyOrderId, $paynum);
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
                                        $groupBuyOrderId,
                                        $spokesman_order
                                    );
                                    $order_id = db('order')->where(['ordersn'=>$ordersn])->value('id');
                                    $ajaxdate['order_id']=$order_id;
                                    $transaction_id=$ordersn;


                                }elseif($_GET['group_buy_order_id'] && array_key_exists('order_id',$request) && $request['groupjoin']==1){
                                    //更新
                                    //更新团购order
                                    $orderid=$request['order_id'];
                                    $paynum_before=db('order')->where(['id'=>$orderid])->value('paynum');
                                    if($paynum != $paynum_before){
                                        $res=db('group_buy_order')->where(['group_buy_order_id'=>$groupBuyOrderId])->setInc('sold',$paynum-$paynum_before);
                                    }else{
                                        $res=1;
                                    }
                                    //更新order
                                    $transaction_id=getOrderSn();
                                    $obj = new \app\admin\module\Order();
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
                                        0, //$cashed_amount,
                                        '', //$receive_cashed_id,
                                        '', //$share_plan_id,
                                        $txtfield1,
                                        $txtdata1,
                                        $ischarge,
                                        $transaction_id,
                                        $request['group_buy_order_id'],
                                        $request['order_id']//订单id
                                    );
                                    $order_id = db('order')->where(['ordersn'=>$ordersn])->value('id');
                                    $ajaxdate['order_id']=$order_id;
                                    if(!$ordersn)
                                    {
                                        throw new Exception('订单创建失败');
                                    }

                                }

                                $ajaxdate['group_buy_order_id']=$groupBuyOrderId;
                                $ajaxdate['groupjoin']=1;

                            }

                        } else
                        {
                            //新增拼团
                            $res = $groupBuyOrderId = $groupBuyOrderModel->start($request['group_buy_id'], $paynum, $package['stock'], $idsite, $content_info['chrtitle'],$payname,$this->userinfo['nickname'],$this->userinfo['idmember']);

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
                                $groupBuyOrderId,
                                $spokesman_order
                            );
                            $order_id = db('order')->where(['ordersn'=>$ordersn])->value('id');
                            $ajaxdate['order_id']=$order_id;
                            $ajaxdate['group_buy_order_id']=$groupBuyOrderId;
                            $transaction_id=$ordersn;
                        }

                        if(!$res)
                        {
                            throw new Exception('已售完');
                        }

                    }else
                    {
                        //非拼团订单 拼团订单id为空
                        $groupBuyOrderId = '';
                        $obj = new \app\admin\module\Order();
                        $ordersn = getOrderSn();
                        $cashed_amount = 0;//使用优惠券金额
                        $receive_cashed_id = '';//使用优惠券的领取现金券id
                        //查询该产品是否可以分享优惠券
                        $activity_cashed_set = db('activity_cashed_card_set')->field('cashed_plan_id,is_share_cashed,activity_id')->where(['activity_id'=>$dataID,'is_share_cashed'=>1])->find();
                        //判断使用了优惠券相关的信息
                        if($request['hidvolumeid'] && $request['hidvolumeprice'] > 0){
                            //去除优惠券id左右两边的逗号
                            $receive_cashed_id = trim($request['hidvolumeid'],',');
                            //如果抵用的优惠券大于订单的金额,那么就支付一分钱
                            if($request['hidvolumeprice'] >= $price){
                                $price = 0.00;
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
                                $groupBuyOrderId,
                                $spokesman_order
                            );
                            $transaction_id=$ordersn;
                            //获取订单id
                            $order_id = db('order')->where(['ordersn'=>$ordersn])->value('id');
                            $ajaxdate['order_id']=$order_id;
                            $res = true;
                            $stockLocked = db('order')->field('stock_locked')->find($order_id);
                            if($stockLocked['stock_locked'] == 1)
                            {
                                //减库存
                                $res = changeStock($package['package_id'], $paynum);
                            }
                            if(!$res)
                            {
                                throw new Exception('商品库存不足');
                            }
                            //那么就是修改订单的数据
                        }elseif(!array_key_exists('order_state',$request) && array_key_exists('order_id',$request)){
                            $res = true;
                            $add=true;
                            $stockLocked = db('order')->field('stock_locked,paynum')->find($request['order_id']);
                            if($stockLocked['stock_locked'] == 1)
                            {
                                //先释放原来的库存
                                $add=changeStock($package['package_id'], $stockLocked['paynum'],false);
                                //减库存
                                $res = changeStock($package['package_id'], $paynum);
                            }
                            if(!$res && !$add)
                            {
                                throw new Exception('商品库存不足');
                            }
                            $transaction_id=getOrderSn();
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
                                $transaction_id,
                                $groupBuyOrderId,
                                $request['order_id']//订单id
                            );
                            $ajaxdate['order_id']=$request['order_id'];

                        }
//                        halt($ordersn);
                        if(!$ordersn)
                        {
                            throw new Exception('订单创建失败');
                        }

                        //此时提交事务,是为了下面能查到该订单
                        //Db::commit();
                        //生成订单后开始将使用掉的现金券修改状态和增加使用对象
                        $order_info = db('order')->where(['ordersn'=>$ordersn])->find();


                        if($order_info){
                            //判断该订单原来是否具有使用现金券
                            if(array_key_exists('former_receive_cashed_id',$request) && !empty($request['former_receive_cashed_id'])){
                                $where_update_cashed['id'] = ['in',$request['former_receive_cashed_id']];
                                //将冻结的现金券释放
                                db('cashed_card_receive')->where($where_update_cashed)->update(['used_status'=>1,'release_time'=>date('Y-m-d H:i:s',time())]);
                            }
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
                                //如果没有错误信息,并且支付金额为0的话
                                if(!$err_arr && $price == 0){
                                    //将现金券使用id分割为数组
                                    $receive_cashed_id_arr = explode(',',$receive_cashed_id);
                                    $order_update['state']=4;
                                    $order_update['intflag']=5;
                                    $order_update['paytype1']=1;
                                    //将订单变为已支付
                                    db('order')->where(['ordersn'=>$ordersn])->update($order_update);
                                    $update_param['used_activity_id'] = $order_info['dataid'];//使用产品标识id
                                    $update_param['used_activity_name'] = $order_info['chrtitle'];//使用产品名称
                                    $update_param['used_time'] = date('Y-m-d H:i:s',time());//使用现金券时间
                                    $update_param['used_order_id'] = $order_info['id'];//使用订单id
                                    $update_param['used_status'] = 5;//改为已使用
                                    foreach ($receive_cashed_id_arr as $value){
                                        db('cashed_card_receive')->where(['id'=>$value])->update($update_param);
                                    }
                                }
                            }
                        }
                    }
                    // 不要删，兼容现金券代码 若没有运行到现金券部分，则整个事务必须提交，若要删掉这行代码，必须考虑合并本方法中的三个addOrder调用
                  //  Db::commit();;

                    //处理短信及微信预下单操作
                    if($ischarge==2 && $price > 0)    //付费产品
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

                        $data=$this->wechatpay($api,$transaction_id,$content_info['chrtitle'].'['. $payname.']',$price,$dataID);
                        $config=$api->get_jsapi_config();
                        $ajaxdate['data']=$data;
                        $ajaxdate['config']=$config;
                        $ajaxdate['activity_cashed_set']=$activity_cashed_set;
//                        $this->assign('data',$data);
//                        $this->assign('config',$config);
//                        $this->assign('activity_cashed_set',$activity_cashed_set);//产品现金券的设置
                        if($order_info){
                            //修改成异步拉起微信支付
                            //$order_id=$order_info['id'];
                           $ajaxdate['order_id']=$order_info['id'];
//                            $this->assign('order_id',$order_info['id']);//订单id
                        }else{
                            $order_id='';
                        }
                        Log::debug(date('Y-m-d H:i:s') . ' 收费产品报名成功');
                    }else   //免费产品，发短信
                    {
                        //获取订单信息
                        $order = db('order')->where(['ordersn' => $ordersn])->find();
                        $replace = [];
                        Log::debug(date('Y-m-d H:i:s') . ' 免费产品报名成功,订单信息:$order=' . print_r($order, true));
                        //给客户和商务发短信通知  类型：7--免费产品报名成功
                        sysSendMsg($idsite, 7, $order, $replace);
                    }
//                }catch (Exception $e)
//                {
//                    Db::rollBack();
//                    Log::error('下单及扣除库存失败。[ SQL ] ' . Db::getLastSql());
//                    $errmsg ='下单及扣除库存失败';//
//                    $flag = 2;
//                    dump($e->getMessage());
//                    // throw $e;
//                    dump($e->getLine());
//                }
            }
        }
// var_dump($data);die;
        $roottpl = 'template/modules/';
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
//
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


        $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
      // return $this->fetch($url);
      return $ajaxdate;
    }


   //分销
   private function distribution($userID,$share_id,$paynum,$content_info,$package)
   {
       $activity_cashed_set = [];
       $spokesman_order = [];//代言人订单相关数据

       #region    判断购买代言产品的时候  是否需要生成已代言产品的数据
       $idsite=$content_info['siteid'];
       $dataID=$content_info['idactivity'];
       $spokesman_activity = ['default'=>1];
       $parent_spokesman_activity = ['default'=>1];
       //查询购买人的信息
       $buy_user_info = db('member')->field('idmodel',true)->where(['idmember'=>$userID,'idsite'=>$idsite])->find();
       //查询该机构的分销设置信息
       $spokesman_set_item_info = db('spokesman_set_item')->field('create_time',true)->where(['site_id'=>$idsite])->find();
       //判断购买者是否被锁客
       $is_lock = false;
       $is_commission_priority = $spokesman_set_item_info['commission_priority_set'] == 2?true:false;//佣金优先是否是锁客
       $lock_user_info = [];
       //如果该用户被锁客，查询出锁客用户的信息
       if($buy_user_info['intlock'] == 1 && $buy_user_info['lock_user_id']){
           $is_lock = true;
           $lock_user_info = db('member')->field('idmodel',true)->where(['idmember'=>$buy_user_info['lock_user_id'],'idsite'=>$idsite])->find();
       }

       //判断是否有分享用户id,如果有
       if($share_id){
           //查询出该代言人的用户信息
           $share_info = db('member')->field('idmodel',true)->where(['idmember'=>$share_id,'idsite'=>$idsite])->find();
           if($share_info){
               //判断该机构设置的佣金优先级是否是锁客(2019年10月30号修改)，并且该用户是否被锁客
               $share_info = $is_commission_priority && $is_lock?$lock_user_info:$share_info;
               $spokesman_user_id = $is_commission_priority && $is_lock?$lock_user_info['idmember']:$share_id;
               //查询出是否具有生成已代言的产品信息
               $spokesman_activity = db('spokesman_activity')->field('site_id',true)->where(['activity_id'=>$content_info['idactivity'],'site_id'=>$idsite,'spokesman_user_id'=>$spokesman_user_id])->find();
               //插入的数据
               $spokesman_nick_name = $share_info['nickname'];
               $spokesman_name = $share_info['u_chrname'];
               //看有没有上级
               if($share_info['parent_user_id']){
                   //查询出上级是否具有生成已代言的产品信息
                   $parent_spokesman_activity = db('spokesman_activity')->field('site_id',true)->where(['activity_id'=>$content_info['idactivity'],'site_id'=>$idsite,'spokesman_user_id'=>$share_info['parent_user_id']])->find();
                   $parent_spokesman_user_id = $share_info['parent_user_id'];
                   $parent_spokesman_nick_name = $share_info['parent_nick_name'];
                   $parent_spokesman_name = $share_info['parent_u_chrname'];
               }
           }
       }else{
           //判断购买人是否是代言人,如果是的话
           if($buy_user_info['spokesman_grade'] != 0 ) {
               //看有没有上级
               if ($buy_user_info['parent_user_id']) {
                   //查询出上级是否具有生成已代言的产品信息
                   $parent_spokesman_activity = db('spokesman_activity')->field('site_id', true)->where(['activity_id' => $dataID, 'site_id' => $idsite, 'spokesman_user_id' => $buy_user_info['parent_user_id']])->find();
                   $parent_spokesman_user_id = $buy_user_info['parent_user_id'];
                   $parent_spokesman_nick_name = $buy_user_info['parent_nick_name'];
                   $parent_spokesman_name = $buy_user_info['parent_u_chrname'];
               }
               $spokesman_user_id = $userID;
               $spokesman_nick_name = $buy_user_info['nickname'];
               $spokesman_name = $buy_user_info['u_chrname'];
               //查询出是否具有生成已代言的产品信息
               $spokesman_activity = db('spokesman_activity')->field('site_id', true)->where(['activity_id' => $dataID, 'site_id' => $idsite, 'spokesman_user_id' => $userID])->find();
               //判断购买的人是否有被代言人锁(这时候得是普通用户)
           } elseif($is_lock && $buy_user_info['spokesman_grade'] == 0){
               //判断锁的人是否是代言人
               if($lock_user_info['spokesman_grade'] != 0 ) {
                   //看有没有上级
                   if ($lock_user_info['parent_user_id']) {
                       //查询出上级是否具有生成已代言的产品信息
                       $parent_spokesman_activity = db('spokesman_activity')->field('site_id', true)->where(['activity_id' => $dataID, 'site_id' => $idsite, 'spokesman_user_id' => $lock_user_info['parent_user_id']])->find();
                       $parent_spokesman_user_id = $lock_user_info['parent_user_id'];
                       $parent_spokesman_nick_name = $lock_user_info['parent_nick_name'];
                       $parent_spokesman_name = $lock_user_info['parent_u_chrname'];
                   }
                   $spokesman_user_id = $lock_user_info['idmember'];
                   $spokesman_nick_name = $lock_user_info['nickname'];
                   $spokesman_name = $lock_user_info['u_chrname'];
                   //查询出是否具有生成已代言的产品信息
                   $spokesman_activity = db('spokesman_activity')->field('site_id', true)->where(['activity_id' => $dataID, 'site_id' => $idsite, 'spokesman_user_id' => $lock_user_info['idmember']])->find();
               }
           }
       }
       //如果没有代言过,没有代言数据
       if(!$spokesman_activity){
           //进行数据库的插入
           db("spokesman_activity")->insert([
               'activity_id'=>$dataID,
               'chrtitle'=>$content_info['chrtitle'],
               'chrimg'=>$content_info['chrimg_m'],
               'dtstart'=>$content_info['dtstart'],
               'dtend'=>$content_info['dtend'],
               'dtsignstime'=>$content_info['dtsignstime'],
               'dtsignetime'=>$content_info['dtsignetime'],
               'spokesman_time'=>date('Y-m-d H:i:s',time()),
               'spokesman_user_id'=>$spokesman_user_id,
               'spokesman_nick_name'=>$spokesman_nick_name,
               'spokesman_name'=>$spokesman_name,
               'get_commission'=>0,
               'site_id'=>$idsite,
           ]);
           //进行修改用户的产品代言个数
           db('member')->where(['idmember'=>$spokesman_user_id,'idsite'=>$idsite])->setInc('spokesman_activity_num',1);
       }
       //上级代言
       if(!$parent_spokesman_activity){
           //进行数据库的插入
           db("spokesman_activity")->insert([
               'activity_id'=>$dataID,
               'chrtitle'=>$content_info['chrtitle'],
               'chrimg'=>$content_info['chrimg_m'],
               'dtstart'=>$content_info['dtstart'],
               'dtend'=>$content_info['dtend'],
               'dtsignstime'=>$content_info['dtsignstime'],
               'dtsignetime'=>$content_info['dtsignetime'],
               'spokesman_time'=>date('Y-m-d H:i:s',time()),
               'spokesman_user_id'=>$parent_spokesman_user_id,
               'spokesman_nick_name'=>$parent_spokesman_nick_name,
               'spokesman_name'=>$parent_spokesman_name,
               'get_commission'=>0,
               'site_id'=>$idsite,
           ]);
           //进行修改用户的产品代言个数
           db('member')->where(['idmember'=>$parent_spokesman_user_id,'idsite'=>$idsite])->setInc('spokesman_activity_num',1);
       }
       #endregion

       #region判断是否有分销的分享用户id
       if($share_id>0){
           //查询出该代言人的用户信息
           $share_info = db('member')->field('idmodel',true)->where(['idmember'=>$share_id,'idsite'=>$idsite])->find();
           if($share_info){
               //判断该机构设置的佣金优先级是否是锁客(2019年10月30号修改)，并且该用户是否被锁客
               $share_info = $is_commission_priority && $is_lock?$lock_user_info:$share_info;
               $spokesman_user_id = $is_commission_priority && $is_lock?$lock_user_info['idmember']:$share_id;
               #region  判断分享人是否有上级
               if($share_info['parent_user_id']){
                   //代言人获得销售佣金
                   $spokesman_order['spokesman_user_id3'] = $spokesman_user_id;
                   $spokesman_order['spokesman_name3'] = $share_info['u_chrname'];
                   $spokesman_order['spokesman_nick_name3'] = $share_info['nickname'];
                   $spokesman_order['sell_commission'] = $paynum * $package['sell_commission'];//获得的佣金是销售佣金
                   //上级获得奖励金
                   $spokesman_order['spokesman_user_id2'] = $share_info['parent_user_id'];
                   $spokesman_order['spokesman_name2'] = $share_info['parent_u_chrname'];
                   $spokesman_order['spokesman_nick_name2'] = $share_info['parent_nick_name'];
                   $spokesman_order['bounty_commission2'] = $paynum * $package['bounty_commission'];//获得的佣金是奖励金
               }else{
                   $spokesman_order['spokesman_user_id3'] = $spokesman_user_id;
                   $spokesman_order['spokesman_name3'] = $share_info['u_chrname'];
                   $spokesman_order['spokesman_nick_name3'] = $share_info['nickname'];
                   $spokesman_order['sell_commission'] = $paynum * ($package['sell_commission'] + $package['bounty_commission']);//代言人获得的佣金是销售佣金和奖励金
               }
               #endregion
           }
           //没有分享id,那么就是自己购买,判断有没有上级,有的话,那么就是自己只得销售金,没有的话,就是得销售金和奖励金
       }else{
           //判断购买人是否是代言人,如果是的话
           if($buy_user_info['spokesman_grade'] != 0) {
               if ($buy_user_info['parent_user_id']) {
                   //代言人获得销售佣金
                   $spokesman_order['spokesman_user_id3'] = $userID;
                   $spokesman_order['spokesman_name3'] = $buy_user_info['u_chrname'];
                   $spokesman_order['spokesman_nick_name3'] = $buy_user_info['nickname'];
                   $spokesman_order['sell_commission'] = $paynum * $package['sell_commission'];//代言人获得的佣金是销售佣金
                   //上级获得奖励金
                   $spokesman_order['spokesman_user_id2'] = $buy_user_info['parent_user_id'];
                   $spokesman_order['spokesman_name2'] = $buy_user_info['parent_u_chrname'];
                   $spokesman_order['spokesman_nick_name2'] = $buy_user_info['parent_nick_name'];
                   $spokesman_order['bounty_commission2'] = $paynum * $package['bounty_commission'];//获得的佣金是奖励金
               }else{
                   //否则  那么就没有上级的话  是一级代言人自己购买
                   $spokesman_order['spokesman_user_id3'] = $userID;
                   $spokesman_order['spokesman_name3'] = $buy_user_info['u_chrname'];
                   $spokesman_order['spokesman_nick_name3'] = $buy_user_info['nickname'];
                   $spokesman_order['sell_commission'] = $paynum * ($package['sell_commission'] + $package['bounty_commission']);//代言人获得的佣金是销售佣金和奖励金
               }
               //判断购买的人是否有被代言人锁(这时候得是普通用户)
           } elseif($is_lock && $buy_user_info['spokesman_grade'] == 0){
               //判断锁的人是否是代言人
               if($lock_user_info['spokesman_grade'] != 0 ) {
                   if ($lock_user_info['parent_user_id']) {
                       //锁的人获得销售佣金
                       $spokesman_order['spokesman_user_id3'] = $lock_user_info['idmember'];
                       $spokesman_order['spokesman_name3'] = $lock_user_info['u_chrname'];
                       $spokesman_order['spokesman_nick_name3'] = $lock_user_info['nickname'];
                       $spokesman_order['sell_commission'] = $paynum * $package['sell_commission'];//锁的人获得的佣金是销售佣金
                       //锁的人的上级获得奖励金
                       $spokesman_order['spokesman_user_id2'] = $lock_user_info['parent_user_id'];
                       $spokesman_order['spokesman_name2'] = $lock_user_info['parent_u_chrname'];
                       $spokesman_order['spokesman_nick_name2'] = $lock_user_info['parent_nick_name'];
                       $spokesman_order['bounty_commission2'] = $paynum * $package['bounty_commission'];//获得的佣金是奖励金
                   }else{
                       //否则  那么就没有上级的话  锁的人是一级代言人
                       $spokesman_order['spokesman_user_id3'] = $lock_user_info['idmember'];
                       $spokesman_order['spokesman_name3'] = $lock_user_info['u_chrname'];
                       $spokesman_order['spokesman_nick_name3'] = $lock_user_info['nickname'];
                       $spokesman_order['sell_commission'] = $paynum * ($package['sell_commission']);//锁的人获得的佣金是销售佣金
                   }
               }
           }
       }

       #endregion

       return $spokesman_order;
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
     * 免费|收费产品报名
     * ---------------------------------------------------------
     * 职责
     * ---------------------------------------------------------
     * 验证登录状态
     * 创建|查询订单（收集报名表单数据并保存、校验）(校验产品限制)
     * 判断产品限制（关注、手机号、身份证，收费）
     * 扣除库存
     * 发起支付
     * ---------------------------------------------------------
     * @DateTime 2019-04-23T17:18:24+0800
     * @return   [type]                   [description]
     */
    //验证个人信息，返回关注状态
    private function verification_user($userID,$idsite){
        if($userID=="")
        {
            $ajaxdate['flag']=2;
            $ajaxdate['errmsg']="信息获取错误！";
            return $ajaxdate;
        }

        $arr['idmember']=$userID;
        $arr['idsite']=$idsite;
        $row=db('member')->where($arr)->find();
        if(empty($row))
        {
            $ajaxdate['flag']=2;
            $ajaxdate['errmsg']="用户信息不存在！";
            return $ajaxdate;
        }
        return $row;
    }
    //验证产品信息和表单字段
    private  function verification_activity($userID,$content_info,$paynum,$intstate,$request,$idsite,$dataID,$orderid){
        $txtdata="";
        $txtfield="";
        $txtdata1="";
        $txtfield1="";
        $arrfield=[];
        $arrdata=[];
        $errmsg='';
        $payname=$request['payname'];
        if(empty($content_info))
        {
            $ajaxdate['flag']=2;
            $ajaxdate['errmsg']="产品已下架！";
            return  $ajaxdate;
        }
        $Tid=$content_info['selsignfrom'];  //报名表单ID
        $chkissubscribe=$content_info['chkissubscribe']; //是否需要关注 1为需要关注
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
            $ajaxdate['flag']=2;
            $ajaxdate['errmsg']="单次最大购买数量不能大于".$intmaxpaynum;
            return $ajaxdate;
        }

        //关注才能下单
        if($chkissubscribe==1)
        {

            if(empty($userID) || $userID<1 || $intstate!=1)
            {
                $imgurl= $this->qrcodeurl();
                $ajaxdate['flag']=2;
                $ajaxdate['errmsg']="<div style=\"text-align: center\"><img style=\"width: 150px;height: 150px\" src=\"{$imgurl}\" /><br>没有关注或没有登陆，请先关注后才能报名</div>";
                return $ajaxdate;
            }
        }
        //处理报名表单
        if($Tid>0)
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
                                        if($chkismobile==1 && $vo1['id'] != $orderid)
                                        {
                                            $errmsg="单个手机号码只能订购一次";
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
                            $errmsg="单个手机号码累计只能购买".$intmaxmobilepaynum."份";
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
                                        if($chkisidcard==1 && $vo1['id'] != $orderid)
                                        {
                                            $errmsg="单个身份证只能订购一次";
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
                            $errmsg="单个身份证累计只能购买".$intmaxidcardpaynum."份";
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

                        // $aaaaa = $request[$filekey];
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
                                    $_data[]=$request["old_file_{$vo['id']}"];
                                }else{
                                    $_data[]='';
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
                                $_data[]="/".$filepath;
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
        if($chkismobile_flag)
        {
            $errmsg="没有设置手机字段，请联系管理员";
        }
        if($chkisidcard_flag)
        {
            $errmsg="没有设置身份证字段，请联系管理员";
        }

        if(empty($payname))
        {
            $errmsg="报名失败，数据错误！";
        }

        if($errmsg!="")
        {
            $ajaxdate['flag']=2;
            $ajaxdate['errmsg']=$errmsg;
            return $ajaxdate;
        }

        $result['txtfield']=$txtfield;
        $result['txtdata']=$txtdata;
        $result['txtfield1']=$txtfield1;
        $result['txtdata1']=$txtdata1;
        return $result;

    }
    //验证套餐库存
    private function verification_package($package,$paynum,$group_buy_id,$request,$order_info){
        if(empty($package))
        {
            $ajaxdate['flag']=2;
            $ajaxdate['errmsg']='报名失败，套餐不存在或已过期！';;
            return $ajaxdate;
        }

        $payname = $package['keyword1'] . ' ' . $package['keyword2'] . '(' . $package['member_price'] . '元)' ;
        $price = $package['member_price'] * $paynum;
        // 是否是拼团订单
        // var_dump($request);die;
        if($group_buy_id!=0)
        {
            // TODO 验证拼团有效期
            $groupBuy = db('group_buy')->find($request['group_buy_id']);
            if(empty($groupBuy))
            {
                $ajaxdate['flag']=2;
                $ajaxdate['errmsg']='拼团不存在！';;
                return $ajaxdate;
            }

            if($groupBuy['end_at'] < time()){
                $ajaxdate['flag']=2;
                $ajaxdate['errmsg']='拼团已结束！';;
                return $ajaxdate;
            }

            if($groupBuy['state'] == 0){
                $ajaxdate['flag']=2;
                $ajaxdate['errmsg']='拼团已下架！';;
                return $ajaxdate;
            }

            $price = $groupBuy['group_buy_price'] * $paynum;
            if(!isset($request['group_buy_order_id']) || $request['group_buy_order_id'] ==0 )
            {
                // TODO 验证拼团启用状态
                // 开团购买数量小于等于拼团数量限制 拼团数量小于等于套餐剩余库存
                if($groupBuy['group_num']< $paynum)
                {
                    $ajaxdate['flag']=2;
                    $ajaxdate['errmsg']='报名失败，购买数量不能超过成团所需数量！';
                    return $ajaxdate;
                }

                if($package['stock'] < $groupBuy['group_num']){
                    $ajaxdate['flag']=2;
                    $ajaxdate['errmsg']='报名失败，库存不足！';;
                    return $ajaxdate;
                }
            }
            else
            {
                $groupBuyOrder = db('group_buy_order')->find($request['group_buy_order_id']);
                //先判断是否过期
                if($groupBuyOrder['state'] == 4 || (!empty($groupBuyOrder['expire_at']) &&  $groupBuyOrder['expire_at']< time()) ){
                    $ajaxdate['flag']=2;
                    $ajaxdate['errmsg']='报名失败，该拼团已失效！';
                    return $ajaxdate;
                }
                $paynum_be=0;
                if($order_info)
                {
                    $paynum_be=$order_info['paynum'];
                }

                $stock=$groupBuyOrder['group_num']-$groupBuyOrder['sold']+$paynum_be;
                if($stock < $paynum)
                {
                    $ajaxdate['flag']=2;
                    $ajaxdate['errmsg']='拼团报名失败，库存不足！';;
                    return $ajaxdate;
                }
            }
        }else
        {
            if($package['stock'] < $paynum)
            {
                $ajaxdate['flag']=2;
                $ajaxdate['errmsg']='报名失败，库存不足！';;
                return $ajaxdate;
            }
        }
        $result['price']=$price;
        $result['payname']=$payname;
        return $result;
    }
    //验证拼团
    private  function groupOrder($groupBuyOrderId,$request,$package,$idsite,$content_info,$payname,$spokesman_order,$price,$_data,$orderid,$ordersn,$paynum,$dataID,$userID,$order_info){
        $obj = new \app\home\model\Order();
        $groupBuyOrderModel = new GroupBuyOrder;
        //新增拼团
        if($groupBuyOrderId<1) {
            $groupBuyOrderId = $groupBuyOrderModel->start($request['group_buy_id'], 0, $package['stock'], $idsite, $content_info['chrtitle'], $payname, $this->userinfo['nickname'], $this->userinfo['idmember']);
            if(!$groupBuyOrderId)
            {
                $ajaxdate['flag']=2;
                $ajaxdate['errmsg']="开团失败";
                return $ajaxdate;
            }
            $ajaxdate['group_buy_order_id']=$groupBuyOrderId;
        }

        if(isset($_POST['group_buy_order_id']) && $_POST['group_buy_order_id'] !=0 && $_POST['group_buy_order_id'] !='')
        {
            $ajaxdate['groupjoin']=1;
        }

        //如果有分销数据
        if($spokesman_order && $price > 0){
            $spokesman_order['source'] = '代言人订单';
            $_data=array_merge($_data,$spokesman_order);
        }
        //用户参与拼团
        //第一次拉起支付
        if($orderid<1){
            // TODO 整合添加订单功能
            $_data['ordersn']=$ordersn; //订单号//
            $_data['group_buy_order_id']=$groupBuyOrderId; //关联拼团订单id//
            $groupBuyOrderModel->join($groupBuyOrderId, $paynum);
            $ordersn = $obj->updateOrder($_data,$dataID,0,$userID,$idsite);
            $orderid = db('order')->where(['ordersn'=>$ordersn])->value('id');
            $ajaxdate['order_id']=$orderid;
        }
        else
        {
            //更新
            //更新团购库存
            $paynum_before=$order_info['paynum'];
            if($paynum != $paynum_before){
                $res= $groupBuyOrderModel->join($groupBuyOrderId, $paynum-$paynum_before);
                if($res==false)
                {
                    $ajaxdate['flag']=2;
                    $ajaxdate['errmsg']="已超过团购数量";
                    return $ajaxdate;
                }
            }

            //更新order
            $_data['group_buy_order_id']=$groupBuyOrderId; //关联拼团订单id//
            $ordersn = $obj->updateOrder($_data,$dataID,$orderid,$userID,$idsite);
            if(!$ordersn)
            {
                $ajaxdate['flag']=2;
                $ajaxdate['errmsg']="订单创建失败";
                return $ajaxdate;
            }
        }

        $ajaxdate['group_buy_order_id']=$groupBuyOrderId;

        return $ajaxdate;

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

    public function signup_post()
    {
        //获取传入的参数及初始化各种变量
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $group_buy_id=empty($request['group_buy_id'])?"0":$request['group_buy_id'];
        $config=$this->wxConfig;
        $idsite=$config['id'];
        $dataID =$request['id']; //id
        $userID=$this->userinfo['idmember'];
        $errmsg="";
        $flag=1;
        $result_order=null;
        $order_info = [];
        $ajaxdate=[];
        $ajax_order_id = 0;
        $orderid=isset($request['order_id'])?$request['order_id']:0;//订单ID
        if($orderid>0)
        {
            $order_info=db('order')->where(['id'=>$orderid])->find();
        }

        if(empty($request['txtpaynum']) || empty($request['txtpaynum'])){
            $this->redirect(url('/'.$sitecode.'/detail/'.$dataID));
        }

        $row=$this->verification_user($userID,$idsite);
        if(isset($row['flag']))
            return json_encode($row, JSON_UNESCAPED_UNICODE);

        $intstate=$row['intstate'];//会员状态（1关注 2取消关注 3游客）',
        $paynum=$request['txtpaynum'];
        $payname=$request['payname'];

        $content_info = db('activity')->where(['idactivity' => $dataID])->field('chrkeyword,is_distribution,share_time,chkissubscribe,ischarge,chkismobile,chkisidcard,intmaxpaynum,intmaxmobilepaynum,intmaxidcardpaynum,chrtitle,selsignfrom,chrimg_m,dtstart,dtend,dtsignstime,dtsignetime,siteid,idactivity')->find();

        //产品
        $res_activity=$this->verification_activity($userID,$content_info,$paynum,$intstate,$request,$idsite,$dataID,$orderid);
        if(isset($res_activity['errmsg']))
            return json_encode($res_activity, JSON_UNESCAPED_UNICODE);

        $packageObj = new Package;
        $package = $packageObj->getPackage($payname);

        //套餐
        $res_pacakge=$this->verification_package($package,$paynum,$group_buy_id,$request,$order_info);
        if(isset($res_pacakge['errmsg'])){
            return json_encode($res_pacakge, JSON_UNESCAPED_UNICODE);
        }else{
            $payname=$res_pacakge['payname'];
            $price=$res_pacakge['price'];
        }

        //是否收费 1:免费 2收费
        $ischarge=$content_info['ischarge'];
        $activity_cashed_set = [];
        $spokesman_order = [];//代言人订单相关数据

        $request['share_id'] = array_key_exists('share_id',$request)?$request['share_id']:'';
        //看是否是新用户下单2019年10月13号锁客功能新加
        $request['is_new_user'] = array_key_exists('is_new_user',$request)?$request['is_new_user']:0;
        //查看是否是分销商品
        if($content_info['is_distribution'] == 1){
            $spokesman_order=$this->distribution($userID, $request["share_id"],$paynum,$content_info,$package);
        }

        $group_buy_id=isset($request['group_buy_id'])?$request['group_buy_id']:0;                   //拼团信息ID
        $groupBuyOrderId=isset($request['group_buy_order_id'])?$request['group_buy_order_id']:"";    //开团信息ID
        if(empty($groupBuyOrderId)) $groupBuyOrderId=isset($_GET['group_buy_order_id'])?$_GET['group_buy_order_id']:0;    //开团信息ID
        $order_state=isset($request['order_state'])?$request['order_state']:0;                      //0正常订单，大于0为从原订时间复一个新订单
        $ordersn=getOrderSn();
        $transaction_id=getOrderSn();                                                                //支付流水ID

        if($orderid>0){
            if($order_info){
                $ordersn=$order_info['ordersn'];
            }
        }

        $_data=[];
        $_data['package_id']=$package['package_id']; //套餐id//
        $_data['paynum']=$paynum; //购买数量//
        $_data['payname']=$payname; //购买的套餐名称//
        $_data['price']=$price; //订单总价格//
        $_data['txtfield']=$res_activity['txtfield']; //模版字段，多个字段用“☆”分开//
        $_data['txtdata']=$res_activity['txtdata']; //模版数据，多个字段用“☆”分开//
        $_data['txtfield1']=$res_activity['txtfield1']; //子表单字段，多个字段用“☆”分开//
        $_data['txtdata1']=$res_activity['txtdata1']; //子表单数据，多个字段用“☆”分开，多数据用“§”分开//
        $_data['transaction_id']=$transaction_id; //支付流水ID//
        //如果是新用户
        if($request['is_new_user'] == 1){
            $_data['is_new_user']=$request['is_new_user']; //看是否是新用户下单2019年10月13号锁客功能新加
        }


            $obj = new \app\home\model\Order();
            if($group_buy_id>0)
            {
                $res_group=$this->groupOrder($groupBuyOrderId,$request,$package,$idsite,$content_info,$payname,$spokesman_order,$price,$_data,$orderid,$ordersn,$paynum,$dataID,$userID,$order_info);
                if(isset($res_group['errmsg'])){
                    return json_encode($res_group, JSON_UNESCAPED_UNICODE);
                }else{
                    $ajaxdate=array_merge($ajaxdate,$res_group);
                    if(isset($ajaxdate['order_id']))
                        $ajax_order_id=$ajaxdate['order_id'];
                }

            }else
            {
                //非拼团订单 拼团订单id为空
                $groupBuyOrderId = '';
                $cashed_amount = 0;//使用优惠券金额
                $receive_cashed_id = '';//使用优惠券的领取现金券id
                //查询该产品是否可以分享优惠券
                $activity_cashed_set = db('activity_cashed_card_set')->field('cashed_plan_id,is_share_cashed,activity_id')->where(['activity_id'=>$dataID,'is_share_cashed'=>1])->find();
                //判断使用了优惠券相关的信息
                if($request['hidvolumeid'] && $request['hidvolumeprice'] > 0){
                    //去除优惠券id左右两边的逗号
                    $receive_cashed_id = trim($request['hidvolumeid'],',');
                    //如果抵用的优惠券大于订单的金额,那么就支付一分钱
                    if($request['hidvolumeprice'] >= $price){
                        $price = 0.00;
                    }else{
                        $price = round($price-$request['hidvolumeprice'],2);//订单的金额减去使用优惠券的金额
                    }
                    $cashed_amount = $request['hidvolumeprice'];
                }
                $_data['cashed_amount'] = $cashed_amount;//抵用现金券金额
                $_data['price']=$price;
                $_data['receive_cashed_id'] = $receive_cashed_id;//使用的领取现金券id,多个券用逗号隔开

                //分享现金券的计划id
                $share_plan_id = $activity_cashed_set?$activity_cashed_set['cashed_plan_id']:'';
                $_data['share_plan_id'] = $share_plan_id;//分享现金券的计划id

                $_paynum=$paynum;   //要减的库存
                $_data['group_buy_order_id']=$groupBuyOrderId; //关联拼团订单id//
                //如果有分销数据
                if($spokesman_order){
                    $spokesman_order['source'] = '代言人订单';
                }
                $_data=array_merge($_data,$spokesman_order);

                //判断该订单原来是否具有使用现金券
                if(array_key_exists('former_receive_cashed_id',$request) && !empty($request['former_receive_cashed_id'])){
                    $where_update_cashed['id'] = ['in',$request['former_receive_cashed_id']];
                    //将冻结的现金券释放
                    db('cashed_card_receive')->where($where_update_cashed)->update(['used_status'=>1,'release_time'=>date('Y-m-d H:i:s',time())]);
                }

                //0-未占用库存，1-占用库存'
                if($orderid>0 && $order_state<1) {
                    if ($order_info["stock_locked"] == 1) {
                        $_paynum = $_paynum - $order_info["paynum"];
                    }
                }
                //减库存
                $res=true;
                if($_paynum!=0)
                {
                    $res = changeStock($package['package_id'], $_paynum);
                }
                if(!$res)
                {
                    $ajaxdate['flag']=2;
                    $ajaxdate['errmsg']="库存不足";
                    return json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                }

                if($orderid<1 || $order_state>0)
                {
                    $ordersn=getOrderSn();
                    $_data['ordersn']=$ordersn; //订单号//
                    $ordersn = $obj->updateOrder($_data,$dataID,0,$userID,$idsite,$receive_cashed_id);
                    if(isset($ordersn['errmsg'])){
                        return json_encode($ordersn, JSON_UNESCAPED_UNICODE);
                    }
                    $order_info= db('order')->where(['ordersn'=>$ordersn])->find();
                    $ajax_order_id =$order_info['id'];
                }
                else
                {
                    $ordersn = $obj->updateOrder($_data,$dataID,$orderid,$userID,$idsite,$receive_cashed_id);
                    if(isset($ordersn['errmsg'])){
                        return json_encode($ordersn, JSON_UNESCAPED_UNICODE);
                    }
                }
                if(!$ordersn)
                {
                    $ajaxdate['flag']=2;
                    $ajaxdate['errmsg']="订单创建失败".$ordersn;
                    return json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                }
            }

            //如果有订单id并且修改过订单的金额
        if($orderid>0) {
            if ($order_info["is_change_price"] == 1) {
                $price = $order_info["price"];
            }
        }
        //处理短信及微信预下单操作
        if($ischarge==2 && $price > 0)    //付费产品
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
            $data=$this->wechatpay($api,$transaction_id,$content_info['chrtitle'].'['. $payname.']',$price,$dataID);
            $config=$api->get_jsapi_config();
            $ajaxdate['data']=$data;
            $ajaxdate['config']=$config;
            $ajaxdate['activity_cashed_set']=$activity_cashed_set; ////产品现金券的设置
            Log::debug(date('Y-m-d H:i:s') . ' 收费产品报名成功');
        }elseif($ischarge==2 && $price == 0){
            Log::debug(date('Y-m-d H:i:s') . ' 收费产品使用现金券，全部抵扣报名成功');

        }else   //免费产品，发短信
        {
            //获取订单信息
            $order = db('order')->where(['ordersn' => $ordersn])->find();
            $replace = [];
            Log::debug(date('Y-m-d H:i:s') . ' 免费产品报名成功,订单信息:$order=' . print_r($order, true));
            //给客户和商务发短信通知  类型：7--免费产品报名成功

            //码库功能
            //1.判断此套餐是否使用了码库
            $useCode = db('activity_codebase')->where(['id_package' => $order['package_id'], 'state' => 1, 'id_site' => $idsite])
                ->find();
            if ($useCode) {
                //查询编码，将编码写入订单
                $coderes = $this->useCodebase($useCode['id'], $order['paynum'], $idsite);
                if($coderes){
                    db('order')->where(['ordersn' => $ordersn])->update(['checkcode'=>$coderes['code'],'checkcodeid'=>$coderes['id']]);
                    sendCodeMsg($idsite, 7, $order, $replace);
                }
            }else{
                sysSendMsg($idsite, 7, $order, $replace);
            }

        }

        //判断支付过后的页面跳转问题
        $payjump=db('config_jump')->where(['id_site'=>$idsite,'isjump'=>1])->value('id');
        if($payjump){
            $ajaxdate['payjump']=1;
        }else{
            $ajaxdate['payjump']=2;
        }


        $ajaxdate['userid']=$userID;
        $roottpl = 'template/'.GetConfigVal("weboption","rootdir",$idsite);
        $ajaxdate['order_id']=$ajax_order_id;
        $ajaxdate['flag']=$flag;
        $ajaxdate['price']=$price;
        $ajaxdate['errmsg']=$errmsg;
        $ajaxdate['roottpl']='/'.$roottpl;
        $ajaxdate['sitecode']=$sitecode;
        $ajaxdate['ischarge']=$ischarge;
        $ajaxdate['ordersn']=$ordersn;
        $ajaxdate['dataID']=$dataID;
        $ajaxdate['is_cashed']=checkedMarketingPackage($idsite,'cashed');
        return json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);

    }

    public function signup_post_bak2()
    {
        //获取传入的参数及初始化各种变量
        $request = Request::instance()->param();
//        var_dump($request);exit;
        $sitecode=$request['sitecode'];
        $group_buy_id=empty($request['group_buy_id'])?"0":$request['group_buy_id'];
        $config=$this->wxConfig;
        $idsite=$config['id'];
        $dataID =$request['id']; //id
        $userID=$this->userinfo['idmember'];
        $errmsg="";
        $flag=1;
        $txtdata="";
        $txtfield="";
        $txtdata1="";
        $txtfield1="";
        $arrfield=[];
        $arrdata=[];
        $result_order=null;
        $order_info = [];


        $orderid=isset($request['order_id'])?$request['order_id']:0;                                //订单ID
        if($orderid>0)
        {
            $order_info=db('order')->where(['id'=>$orderid])->find();
        }


        #region 数据验证
        if($userID=="")
        {
            $ajaxdate['flag']=2;
            $ajaxdate['errmsg']="信息获取错误！";
            $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
            return $ajaxdate;
        }

        if(empty($request['txtpaynum']) || empty($request['txtpaynum'])){
            $this->redirect(url('/'.$sitecode.'/detail/'.$dataID));
        }


        $arr['idmember']=$userID;
        $arr['idsite']=$idsite;
        $row=db('member')->where($arr)->find();
        if(empty($row))
        {
            $ajaxdate['flag']=2;
            $ajaxdate['errmsg']="用户信息不存在！";
            $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
            return $ajaxdate;
        }


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
        $content_info = db('activity')->where(['idactivity' => $dataID])->field('chrkeyword,is_distribution,share_time,chkissubscribe,ischarge,chkismobile,chkisidcard,intmaxpaynum,intmaxmobilepaynum,intmaxidcardpaynum,chrtitle,selsignfrom,chrimg_m,dtstart,dtend,dtsignstime,dtsignetime,siteid,idactivity')->find();

        if(empty($content_info))
        {
            $ajaxdate['flag']=2;
            $ajaxdate['errmsg']="产品已下架！";
            $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
            return $ajaxdate;
        }

        $Tid=$content_info['selsignfrom'];  //报名表单ID
        $chkissubscribe=$content_info['chkissubscribe']; //是否需要关注 1为需要关注
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
            $ajaxdate['flag']=2;
            $ajaxdate['errmsg']="单次最大购买数量不能大于".$intmaxpaynum;
            $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
            return $ajaxdate;
        }

        //关注才能下单
        if($chkissubscribe==1)
        {
            if(empty($userID) || $userID<1 || $intstate!=1)
            {
                $imgurl= $this->qrcodeurl();
                $ajaxdate['flag']=2;
                $ajaxdate['errmsg']="<div style=\"text-align: center\"><img style=\"width: 150px;height: 150px\" src=\"{$imgurl}\" /><br>没有关注或没有登陆，请先关注后才能报名</div>";
                $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                return $ajaxdate;
            }
        }


        //处理报名表单
        if($Tid>0)
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
                                        if($chkismobile==1 && $vo1['id'] != $orderid)
                                        {
                                            $errmsg="单个手机号码只能订购一次";
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
                            $errmsg="单个手机号码累计只能购买".$intmaxmobilepaynum."份";
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
                                        if($chkisidcard==1 && $vo1['id'] != $orderid)
                                        {
                                            $errmsg="单个身份证只能订购一次";
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
                            $errmsg="单个身份证累计只能购买".$intmaxidcardpaynum."份";
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

        if($chkismobile_flag)
        {
            $errmsg="没有设置手机字段，请联系管理员";
        }
        if($chkisidcard_flag)
        {
            $errmsg="没有设置身份证字段，请联系管理员";
        }

        if(empty($payname))
        {
            $errmsg="报名失败，数据错误！";
        }

        if($errmsg!="")
        {
            $ajaxdate['flag']=2;
            $ajaxdate['errmsg']=$errmsg;
            $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
            return $ajaxdate;
        }


        $ordersn = "";



        $packageObj = new Package;
        $package = $packageObj->getPackage($payname);
        if(empty($package))
        {
            $ajaxdate['flag']=2;
            $ajaxdate['errmsg']='报名失败，套餐不存在或已过期！';;
            $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
            return $ajaxdate;
        }

        $payname = $package['keyword1'] . ' ' . $package['keyword2'] . '(' . $package['member_price'] . '元)' ;
        $price = $package['member_price'] * $paynum;
        // 是否是拼团订单
        // var_dump($request);die;
        if($group_buy_id!=0)
        {
            // TODO 验证拼团有效期
            $groupBuy = db('group_buy')->find($request['group_buy_id']);
            if(empty($groupBuy))
            {
                $ajaxdate['flag']=2;
                $ajaxdate['errmsg']='拼团不存在！';;
                $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                return $ajaxdate;
            }

            if($groupBuy['end_at'] < time()){
                $ajaxdate['flag']=2;
                $ajaxdate['errmsg']='拼团已结束！';;
                $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                return $ajaxdate;
            }

            if($groupBuy['state'] == 0){
                $ajaxdate['flag']=2;
                $ajaxdate['errmsg']='拼团已下架！';;
                $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                return $ajaxdate;
            }

            $price = $groupBuy['group_buy_price'] * $paynum;
            if(!isset($request['group_buy_order_id']) || $request['group_buy_order_id'] ==0 )
            {
                // TODO 验证拼团启用状态
                // 开团购买数量小于等于拼团数量限制 拼团数量小于等于套餐剩余库存
                if($groupBuy['group_num']< $paynum)
                {
                    $ajaxdate['flag']=2;
                    $ajaxdate['errmsg']='报名失败，购买数量不能超过成团所需数量！';
                    $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                    return $ajaxdate;
                }

                if($package['stock'] < $groupBuy['group_num']){
                    $ajaxdate['flag']=2;
                    $ajaxdate['errmsg']='报名失败，库存不足！';;
                    $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                    return $ajaxdate;
                }
            }
            else
            {
                $groupBuyOrder = db('group_buy_order')->find($request['group_buy_order_id']);
                //先判断是否过期
                if($groupBuyOrder['state'] == 4 || (!empty($groupBuyOrder['expire_at']) &&  $groupBuyOrder['expire_at']< time()) ){
                    $ajaxdate['flag']=2;
                    $ajaxdate['errmsg']='报名失败，该拼团已失效！';
                    $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                    return $ajaxdate;
                }

                $paynum_be=0;
                if($order_info)
                {
                    $paynum_be=$order_info['paynum'];
                }

               $stock=$groupBuyOrder['group_num']-$groupBuyOrder['sold']+$paynum_be;

                if($stock < $paynum)
                {
                    $ajaxdate['flag']=2;
                    $ajaxdate['errmsg']='拼团报名失败，库存不足！';;
                    $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                    return $ajaxdate;
                }
            }
        }else
        {
            if($package['stock'] < $paynum)
            {
                $ajaxdate['flag']=2;
                $ajaxdate['errmsg']='报名失败，库存不足！';;
                $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                return $ajaxdate;
            }
        }




        //是否收费 1:免费 2收费
        $ischarge=$content_info['ischarge'];


        //事务，添加订单、锁定库存
//                Db::startTrans();
//                try
//                {
        //创建新连接，专用于事务操作
//                    $query = new Query;
//                    $query->startTrans();


        $activity_cashed_set = [];
        $spokesman_order = [];//代言人订单相关数据


        #endregion

        $request['share_id'] = array_key_exists('share_id',$request)?$request['share_id']:'';
        //查看是否是分销商品
        if($content_info['is_distribution'] == 1){
            $spokesman_order=$this->distribution($userID, $request["share_id"],$paynum,$content_info,$package);
        }

        $group_buy_id=isset($request['group_buy_id'])?$request['group_buy_id']:0;                   //拼团信息ID
        $groupBuyOrderId=isset($request['group_buy_order_id'])?$request['group_buy_order_id']:"";    //开团信息ID
        if(empty($groupBuyOrderId)) $groupBuyOrderId=isset($_GET['group_buy_order_id'])?$_GET['group_buy_order_id']:0;    //开团信息ID
        $order_state=isset($request['order_state'])?$request['order_state']:0;                      //0正常订单，大于0为从原订时间复一个新订单
        $ordersn=getOrderSn();
        $transaction_id=getOrderSn();                                                                //支付流水ID

        if($orderid>0)
        {
              if($order_info)
            {
                $ordersn=$order_info['ordersn'];
            }
        }

        $_data=[];
        $_data['package_id']=$package['package_id']; //套餐id//
        $_data['paynum']=$paynum; //购买数量//
        $_data['payname']=$payname; //购买的套餐名称//
        $_data['price']=$price; //订单总价格//
        $_data['txtfield']=$txtfield; //模版字段，多个字段用“☆”分开//
        $_data['txtdata']=$txtdata; //模版数据，多个字段用“☆”分开//
        $_data['txtfield1']=$txtfield1; //子表单字段，多个字段用“☆”分开//
        $_data['txtdata1']=$txtdata1; //子表单数据，多个字段用“☆”分开，多数据用“§”分开//
        $_data['transaction_id']=$transaction_id; //支付流水ID//

//        $_data['group_buy_order_id']=$group_buy_order_id; //关联拼团订单id//
//        $_data['issettlement']=0; //是否已结算 1:是//
//        $_data['cashed_amount'] = $cashed_amount;//抵用现金券金额
//        $_data['receive_cashed_id'] = $receive_cashed_id;//使用的领取现金券id,多个券用逗号隔开
//        $_data['share_plan_id'] = $share_plan_id;//分享现金券的计划id



        $obj = new \app\home\model\Order();
        if($group_buy_id>0)
        {
            $groupBuyOrderModel = new GroupBuyOrder;
            //新增拼团
            if($groupBuyOrderId<1) {
                $groupBuyOrderId = $groupBuyOrderModel->start($request['group_buy_id'], 0, $package['stock'], $idsite, $content_info['chrtitle'], $payname, $this->userinfo['nickname'], $this->userinfo['idmember']);
                if(!$groupBuyOrderId)
                {
                    $ajaxdate['flag']=2;
                    $ajaxdate['errmsg']="开团失败";
                    $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                    return $ajaxdate;
                }
                $ajaxdate['group_buy_order_id']=$groupBuyOrderId;
            }

            if(isset($_POST['group_buy_order_id']) && $_POST['group_buy_order_id'] !=0 && $_POST['group_buy_order_id'] !='')
            {
                $ajaxdate['groupjoin']=1;
            }

            //如果有分销数据
            if($spokesman_order && $price > 0){
                $spokesman_order['source'] = '代言人订单';
                $_data=array_merge($_data,$spokesman_order);
            }
            //用户参与拼团
            //第一次拉起支付
            if($orderid<1){
                // TODO 整合添加订单功能
                $_data['ordersn']=$ordersn; //订单号//
                $_data['group_buy_order_id']=$groupBuyOrderId; //关联拼团订单id//
                $groupBuyOrderModel->join($groupBuyOrderId, $paynum);
                $ordersn = $obj->updateOrder($_data,$dataID,0,$userID,$idsite);
                $orderid = db('order')->where(['ordersn'=>$ordersn])->value('id');
            }
            else
            {
                //更新
                //更新团购库存
                $paynum_before=$order_info['paynum'];
                if($paynum != $paynum_before){
                   $res= $groupBuyOrderModel->join($groupBuyOrderId, $paynum-$paynum_before);
                   if($res==false)
                   {
                       $ajaxdate['flag']=2;
                       $ajaxdate['errmsg']="已超过团购数量";
                       $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                       return $ajaxdate;
                   }
                }

                //更新order
                $_data['group_buy_order_id']=$groupBuyOrderId; //关联拼团订单id//
                $ordersn = $obj->updateOrder($_data,$dataID,$orderid,$userID,$idsite);
                if(!$ordersn)
                {
                    $ajaxdate['flag']=2;
                    $ajaxdate['errmsg']="订单创建失败";
                    $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                    return $ajaxdate;
                }
            }

            $ajaxdate['group_buy_order_id']=$groupBuyOrderId;

        }else
        {
            //非拼团订单 拼团订单id为空
            $groupBuyOrderId = '';
            $cashed_amount = 0;//使用优惠券金额
            $receive_cashed_id = '';//使用优惠券的领取现金券id
            //查询该产品是否可以分享优惠券
            $activity_cashed_set = db('activity_cashed_card_set')->field('cashed_plan_id,is_share_cashed,activity_id')->where(['activity_id'=>$dataID,'is_share_cashed'=>1])->find();
            //判断使用了优惠券相关的信息
            if($request['hidvolumeid'] && $request['hidvolumeprice'] > 0){
                //去除优惠券id左右两边的逗号
                $receive_cashed_id = trim($request['hidvolumeid'],',');
                //如果抵用的优惠券大于订单的金额,那么就支付一分钱
                if($request['hidvolumeprice'] >= $price){
                    $price = 0.00;
                }else{
                    $price = round($price-$request['hidvolumeprice'],2);//订单的金额减去使用优惠券的金额
                }
                $cashed_amount = $request['hidvolumeprice'];
            }
            $_data['cashed_amount'] = $cashed_amount;//抵用现金券金额
            $_data['price']=$price;
            $_data['receive_cashed_id'] = $receive_cashed_id;//使用的领取现金券id,多个券用逗号隔开

            //分享现金券的计划id
            $share_plan_id = $activity_cashed_set?$activity_cashed_set['cashed_plan_id']:'';
            $_data['share_plan_id'] = $share_plan_id;//分享现金券的计划id

            //如果有订单状态参数和订单id   那么就是终止服务再次下单   或者默认下单没有订单id时就是新增数据
            $ajaxdate['request']=$request;

            $_paynum=$paynum;   //要减的库存
            $_data['group_buy_order_id']=$groupBuyOrderId; //关联拼团订单id//
            //如果有分销数据
            if($spokesman_order && $price > 0){
                $spokesman_order['source'] = '代言人订单';
            }

            $_data=array_merge($_data,$spokesman_order);


            //0-未占用库存，1-占用库存'
            if($orderid>0 && $order_state<1) {
                if ($order_info["stock_locked"] == 1) {
                    $_paynum = $_paynum - $order_info["paynum"];
                }
            }
            //减库存
            $res=true;
            if($_paynum!=0)
            {
                $res = changeStock($package['package_id'], $_paynum);
            }

            if(!$res)
            {
                $ajaxdate['flag']=2;
                $ajaxdate['errmsg']="库存不足";
                $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                return $ajaxdate;
            }

            if($orderid<1 || $order_state>0)
            {
                $ordersn=getOrderSn();
                $_data['ordersn']=$ordersn; //订单号//
                $ordersn = $obj->updateOrder($_data,$dataID,0,$userID,$idsite,$receive_cashed_id);
                $order_info= db('order')->where(['ordersn'=>$ordersn])->find();
                $orderid =$order_info['id'];
            }
            else
            {
                $ordersn = $obj->updateOrder($_data,$dataID,$orderid,$userID,$idsite,$receive_cashed_id);
            }

            if(!$ordersn)
            {
                $ajaxdate['flag']=2;
                $ajaxdate['errmsg']="订单创建失败1".$ordersn;
                $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);
                return $ajaxdate;
            }


            //判断该订单原来是否具有使用现金券
            if(array_key_exists('former_receive_cashed_id',$request) && !empty($request['former_receive_cashed_id'])){
                $where_update_cashed['id'] = ['in',$request['former_receive_cashed_id']];
                //将冻结的现金券释放
                db('cashed_card_receive')->where($where_update_cashed)->update(['used_status'=>1,'release_time'=>date('Y-m-d H:i:s',time())]);
            }
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
                        $flag = 2;
                    }else{
                        //执行修改
                        db('cashed_card_receive')->where(['id'=>$value])->update($update_param);
                    }
                }

                //如果没有错误信息,并且支付金额为0的话
                if($flag==1 && $price == 0){
                    //将现金券使用id分割为数组
                    $receive_cashed_id_arr = explode(',',$receive_cashed_id);
                    $order_update['state']=4;
                    $order_update['intflag']=5;
                    $order_update['paytype1']=1;
                    //将订单变为已支付
                    db('order')->where(['ordersn'=>$ordersn])->update($order_update);
                    $update_param['used_activity_id'] = $order_info['dataid'];//使用产品标识id
                    $update_param['used_activity_name'] = $order_info['chrtitle'];//使用产品名称
                    $update_param['used_time'] = date('Y-m-d H:i:s',time());//使用现金券时间
                    $update_param['used_order_id'] = $order_info['id'];//使用订单id
                    $update_param['used_status'] = 5;//改为已使用
                    foreach ($receive_cashed_id_arr as $value){
                        db('cashed_card_receive')->where(['id'=>$value])->update($update_param);
                    }
                    //发送报名成功提示消息
                    template_bm($order_info['id']);
                }
            }

        }

        // 不要删，兼容现金券代码 若没有运行到现金券部分，则整个事务必须提交，若要删掉这行代码，必须考虑合并本方法中的三个addOrder调用
        //  Db::commit();;

        //处理短信及微信预下单操作
        if($ischarge==2 && $price > 0)    //付费产品
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
           // Log::info('微信公众号信息：' .print_r($config,true));
            $data=$this->wechatpay($api,$transaction_id,$content_info['chrtitle'].'['. $payname.']',$price,$dataID);
            $config=$api->get_jsapi_config();
            $ajaxdate['data']=$data;
            $ajaxdate['config']=$config;
            $ajaxdate['activity_cashed_set']=$activity_cashed_set; ////产品现金券的设置

            if($order_info){
                //修改成异步拉起微信支付
                $ajaxdate['order_id']=$order_info['id'];
            }
            Log::debug(date('Y-m-d H:i:s') . ' 收费产品报名成功');
        }else   //免费产品，发短信
        {
            //获取订单信息
            $order = db('order')->where(['ordersn' => $ordersn])->find();
            $replace = [];
            Log::debug(date('Y-m-d H:i:s') . ' 免费产品报名成功,订单信息:$order=' . print_r($order, true));
            //给客户和商务发短信通知  类型：7--免费产品报名成功
            sysSendMsg($idsite, 7, $order, $replace);
        }

        // var_dump($data);die;
        $roottpl = 'template/modules/';

        // var_dump($flag, $errmsg)
        $ajaxdate['order_id']=$orderid;
        $ajaxdate['res']=1;
        $ajaxdate['flag']=$flag;
        $ajaxdate['price']=$price;
        $ajaxdate['errmsg']=$errmsg;
        $ajaxdate['roottpl']='/'.$roottpl;
        $ajaxdate['sitecode']=$sitecode;
        $ajaxdate['ischarge']=$ischarge;
        $ajaxdate['ordersn']=$ordersn;
        $ajaxdate['dataID']=$dataID;
        $ajaxdate['is_cashed']=checkedMarketingPackage($idsite,'cashed');
        $ajaxdate=json_encode($ajaxdate, JSON_UNESCAPED_UNICODE);

        return $ajaxdate;
    }

    //个人信息
    public function mine(){
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;

        $userid=$this->userinfo['idmember'];
        //确认用户已登陆
        if($userid<1)
        {
            echo "请联系管理，配置好公众号和平台绑定";
            exit();
        }


        $userRow=[];//个人信息
        //$collection=[];//我的收藏
        // $comment=[];//我的评论
        // $signup=[];//我的报名

        $idsite=$config['id'];


        $userid=$this->userinfo['idmember'];

        $userRow = db('member')->where('idsite='.$idsite.' and  idmember='.$userid)->find();
        $ismanage=$userRow['ismanage'];
        //判断session中的值是否跟数据库保持一致，如果不是的话，那么就重新设置session
        if($ismanage != $this->userinfo['ismanage']){
            session("UserInfo_".$sitecode."_ismanage",empty($userRow['ismanage'])?0:1);
        }


        //cache('config'.$request['idsite']);
        $roottpl = 'template/modules/';
        $url = $roottpl.'/mine/index.html';

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('idsite',$idsite);
        $this->assign('sitecode',$sitecode);
        $this->assign('userinfo',$userRow);
        $this->assign('ismanage',$ismanage);
        $this->assign('is_activities',checkedMarketingPackage($idsite,'activities'));
        $this->assign('is_integral',checkedMarketingPackage($idsite,'integral'));
        $this->assign('is_subscribe',checkedMarketingPackage($idsite,'subscribe'));
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));
        $this->assign('is_distribution',checkedMarketingPackage($idsite,'distribution'));//是否具有分销功能营销包
        // $this->assign('comment',$comment);
        // $this->assign('signup',$signup);

        $this->assign('SelectFooterTab',1);
        return $this->fetch($url);
    }

    public function signuplist()
    {

        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite=$config['id'];
        $userid=$this->userinfo['idmember'];
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

                if($result[$key]['state']==11){
                    $result[$key]['group_buy_order_state_name'] = '拼团失败';
                    $result[$key]['group_buy_order_state'] = 4;
                }

                $groupBuy = db('group_buy')->field('allow_refund')->find($groupBuyOrder['group_buy_id']);
                $result[$key]['isrefund'] = $groupBuy['allow_refund'];
            }
        }

        $roottpl = 'template/modules/';
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
        $config=$this->wxConfig;
        $idsite=$config['id'];
        $userid=$this->userinfo['idmember'];
        $state=0;
        $ipage=empty($request['ipage'])?0:$request['ipage'];
        if(!empty($request['state']))
            $state=$request['state'];

        $txtkey=isset($request["txtkey"])?trim($request["txtkey"]):"";

        $where_arr=[];

        if($this->userinfo['ismanage']!=1)
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

        $roottpl = 'template/modules/';
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
        $config=$this->wxConfig;
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

    /**
     * 修改订单金额
     * @return   [type]                   [description]
     */
    public function ajax_update_order_price()
    {
        if(Request::instance()->isPost()==false)
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'非法操作')));
        }

        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite=$config['id'];
        $orderid=$request["order_id"];
        $user_info = $this->userinfo;
        //查询订单的信息
        $datainfo=db("order")->where(array('id'=>$orderid,'idsite'=>$idsite,'state'=>12))->find();
        if(empty($datainfo))
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'待支付订单不存在！')));
        }
        //修改订单表中的金额，并且将该订单的是否更改过订单金额的字段改为是
        $update_bool = db("order")->where(array('id'=>$orderid,'idsite'=>$idsite,'state'=>12))->update(['is_change_price'=>1,'price'=>$request['new_price']]);
        if($update_bool)
        {
            $add_data = ['user_id'=>$user_info['idmember'],'order_id'=>$orderid,'name'=>$user_info['nickname'],
                'old_price'=>$datainfo['price'],'new_price'=>$request['new_price'],'remark'=>$request['remark'],'site_id'=>$idsite];
            //执行插入修改订单金额记录
            db('update_order_price')->insert($add_data);
            exit(json_encode(array( 'state'=>1,'msg'=>'修改成功！')));
        }else
        {
            exit(json_encode(array( 'state'=>0,'msg'=>'修改订单表数据失败！')));
        }
    }


    public function orderdetail()
    {

        $request = Request::instance()->param();
        $id=$request['id'];
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite=$config['id'];

        $row = db('order')->where(array('id'=>$id))->find();

        if($row && $row['group_buy_order_id'])
        {
            $package = db('package')->find($row['package_id']);
            $row['payname'] = $package['keyword1'] . ' ' . $package['keyword2'] . ' 拼团价' . $row['price'] / $row['paynum'] . ' (原价 ' . $package['member_price'] . ')';

            $groupBuyInfo=db('group_buy_order')->where(['group_buy_order_id'=>$row['group_buy_order_id']])->find();
            $map = [
                0 => '开启拼团未支付',
                1 => '拼团中',
                2 => '拼团成功',
                3 => '拼团到期',
                4 => '拼团取消'
            ];
            $groupBuyInfo['state']=$map[$groupBuyInfo['state']];
            $this->assign('groupBuyInfo',$groupBuyInfo);

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

        $roottpl = 'template/modules/';
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
        $config=$this->wxConfig;
        $idsite=$config['id'];

        if($this->userinfo['ismanage']!=1)
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

        //查询订单金额的修改记录
        $order_price_record = db('update_order_price')->field('site_id',true)->where(['site_id'=>$idsite,'order_id'=>$id])->order('create_time desc')->select();
        $roottpl = 'template/modules/';
        //获得栏目列表模版路径


        $url =$roottpl.'/mine/ordermanage_detail.html';

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('sitecode',$sitecode);
        $this->assign('orderinfo',$row);
        $this->assign('frmdata',$frmdata);
        $this->assign('idsite',$idsite);
        $this->assign('order_price_record',$order_price_record);
        $this->assign('order_state',config('order_state'));
        $this->assign('order_paytype1',config('order_paytype1'));

        return $this->fetch($url);

    }

    public function collection()
    {
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite=$config['id'];

        $userid=$this->userinfo['idmember'];
        $ipage=empty($request['ipage'])?0:$request['ipage'];
        $page_size = 10;
        $collection = db('collection')->where('idsite='.$idsite.' and  userid='.$userid)->limit($ipage*$page_size,$page_size)->select();

        //cache('config'.$request['idsite']);
        $roottpl = 'template/modules/';
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

        // $userinfo=db('member')->where(array('idmember'=>$this->userinfo['idmember']))->find();

        $flag=empty($request['flag'])?1:$request['flag'];
        $arr=[];
        $arr['iduser']=$this->userinfo['idmember'];
        $arr['username']=$this->userinfo['nickname'];
        $arr['userimg']=$this->userinfo['userimg'];
        $arr['idsite']=$request['idsite'];
        $arr['dataid']=$request['dataid'];
        $arr['chrtitle']=$request['chrtitle'];
        $arr['content']=$request['content'];
        $arr['flag']=$flag;
        $arr['createtime']=time();
        $arr['intstate']=2;
        $arr['show']=0;
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
        $config=$this->wxConfig;
        $key=empty($request['key'])?'':$request['key'];
        $idsite=$config['id'];
        $flag=I('flag',0);
        $userid=$this->userinfo['idmember'];
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
        $roottpl = 'template/modules/';
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
        $config=$this->wxConfig;
        $key=empty($request['key'])?'':$request['key'];
        $idsite=$config['id'];
        $flag=I('flag',0);
        $userid=$this->userinfo['idmember'];
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
            $data['reid']=$this->userinfo['idmember'];
            $data['rename']=$this->userinfo['nickname'];
            $bool=db('comment')->where(array('id'=>$id,'idsite'=>$idsite))->update($data);
            if($bool)
            {
                Log::debug('微信前端回复评论后发短信 ' . print_r($comment, true));
                $obj = new \app\admin\module\Comment;
                $obj->replyCommentNotice($comment['dataid'], $comment['iduser'], $idsite);
                echo "1";
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
        $roottpl = 'template/modules/';
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

    public function showcomment(){
        $request = Request::instance()->param();
        $id=$request['id'];
        if($id==0)
            return 0;

        $show=$request['show'];
        if($show == 1)
            $show = 2;
        else
            $show = 1;

        $bool=db('comment')->where(['id'=>$id])->update(['show'=>$show]);

        if($bool)
            return 1;
        else
            return 0;

    }

    public function delcomment(){
        $request = Request::instance()->param();
        $userid=$this->userinfo['idmember'];
        $id=I('id',0);
        if($id==0)
        {
            echo '0';
            exit();
        }

        $arr=[];
        $arr['id']=$id;
        if($this->userinfo['ismanage']!=1)
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
        $content_info = db('content')->where('contentid='.$request['contentid'])->field('siteid,nodeid,fieldspare10')->find();
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
        $roottpl = 'template/modules/';
        //获得栏目列表模版路径

        $sitecode=$this->getsitecode($idsite);

        $config=$this->wxConfig;

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
        $userid=$this->userinfo['idmember'];
        if($userid>0)
        {
            $visitinfo=db("collection")->where(array('dataid'=>$content_id,'userid'=>$userid,'flag'=>1))->find();
            if($visitinfo)
                $visitflag=1;

        }

        //查询用户是否有权限查看此文章
        $content_usertype=$content_info['fieldspare10'];
        if(!empty($content_usertype)){
            $usertype=db('member')->where(['idmember'=>$userid])->column('categoryid');

            if(empty($usertype))
            {
                $usertype="123456asdfzdsfv";
            }
            else
            {
                $usertype=$usertype[0];
            }

            //dump($usertype);

            //获取分类的名字
            $obj = new \app\admin\module\activity($idsite);
            $hyfl=$obj->getDic("hyfl");
            $type=[];
            foreach($hyfl as $v){
                $type[$v['id']]=$v['name'];
            }
            $usertypename='';
            foreach (explode('|',$content_usertype) as $v){
                if(array_key_exists($v,$type))
                {
                    $usertypename=$usertypename.$type[$v].',';
                }
            }
            $usertypename=rtrim($usertypename,',');
            if(strpos($content_usertype,$usertype) === false){
                $usertypeflag=1;
                $this->assign('usertypeflag',$usertypeflag);
                $this->assign('usertype',$usertypename);
            }


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
        $arr['userid']=$this->userinfo['idmember'];
        $arr['username']=$this->userinfo['nickname'];
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
        $arr['userid']=$this->userinfo['idmember'];
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
        $arr["openid"]=$this->userinfo['openid'];
        $arr["name"]=$this->userinfo['nickname'];
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
        if($flag==3)
        {
            $cate = db('activities_day_summary')->where('id='.$ContentID)->setInc('view_count',1);
        }
        else if($flag==1)
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
        $arr['userid']=$this->userinfo['idmember'];
        $arr['username']=$this->userinfo['nickname'];
        $arr['openid']=$this->userinfo['openid'];
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
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $key="UserInfo_".$sitecode.'_'.$v;

        if(empty(session($key)))
        {
         //   if(empty(session("UserInfo_load_".$sitecode)))
            {
                session("UserInfo_load_".$sitecode,true);
                $this->getuserinfo1();
            }
            if(empty(session($key)))
            {
                return "";
            }
        }
        return session($key);
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

        if(empty($this->userinfo['idmember']))
        {
            echo '登陆已超时！';
            //return true;
        }

        $request = Request::instance()->param();
        $sitecode=strtolower($request['sitecode']);
        $config=$this->wxConfig;;

        $conf=[];
        //$conf['attach']='童享云';
        $length = $this->_strlen($name);
        if($length >=18) {
            $body = mb_substr($name, 0, 15, 'utf-8')."...";
        }else{
            $body = $name;
        }
        $conf['body']= $body;
        $conf['openid']=$this->userinfo['openid'];
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
        $config=$this->wxConfig;
        $idsite=$config['id'];
        $userid=$this->userinfo['idmember'];
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
        $userid=$this->userinfo['idmember'];
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
        $config=$this->wxConfig;
        $idsite=$config['id'];
        $userid=$this->userinfo['idmember'];
        $arr=[];
        $share_id = '';
        $a = '';
        if(!empty($request["dataid"])) $arr["dataid"]=$request["dataid"];
        if(!empty($request["chrtitle"])) $arr["chrtitle"]=$request["chrtitle"];
        if(!empty($request["chrdesc"])) $arr["chrdesc"]=$request["chrdesc"];
        if(!empty($request["chrlink"])) $arr["chrlink"]=$request["chrlink"];
        if(!empty($request["imgurl"])) $arr["imgurl"]=$request["imgurl"];
        if(!empty($request["datatype"])) $arr["datatype"]=$request["datatype"];
        if(!empty($request["inttype"])) $arr["inttype"]=$request["inttype"];
        //分销的分享id
        if(!empty($request["share_id"])) $share_id = intval($request["share_id"]);
        //用户二维码id
        if(!empty($request["a"])) $a = intval($request["a"]);
        //如果有用户二维码id
        if ($a){
            $share_id = $a;
        }
        $query = new Query();
        $query->startTrans();
        try {
            //查询出产品的信息
            $activity_info = db('activity')->field('chrkeyword',true)->where(['idactivity'=>$arr["dataid"],'siteid'=>$idsite])->find();
            //查询该机构的分销设置
            $spokesman_set_item = db('spokesman_set_item')->field('create_time',true)->where(['site_id'=>$idsite])->find();
            //查询出用户的用户是否有代言过该商品
            $spokesman_activity = db('spokesman_activity')->field('site_id',true)->where(['activity_id'=>$arr["dataid"],'site_id'=>$idsite,'spokesman_user_id'=>$userid])->find();
            //查询用户的信息
            $user_info = db('member')->where(['idmember'=>$userid,'idsite'=>$idsite])->find();
            //封装插入代言活动表中的数据
            $add_spokesman_activity_data = [
                'activity_id'=>$arr["dataid"],
                'chrtitle'=>$activity_info['chrtitle'],
                'chrimg'=>$activity_info['chrimg_m'],
                'dtstart'=>$activity_info['dtstart'],
                'dtend'=>$activity_info['dtend'],
                'dtsignstime'=>$activity_info['dtsignstime'],
                'dtsignetime'=>$activity_info['dtsignetime'],
                'spokesman_time'=>date('Y-m-d H:i:s',time()),
                'get_commission'=>0,
                'site_id'=>$idsite,
            ];
            //机构开启了分销
            if(checkedMarketingPackage($idsite,'distribution')) {
                #region如果机构是人人分销的模式,分享则成为代言人
                if ($spokesman_set_item['distribution_methods'] == 2) {
                    //如果代言过的话，那么就是分享次数加一
                    if ($spokesman_activity) {
                        //进行修改
                        db('spokesman_activity')->where(['activity_id' => $arr["dataid"], 'site_id' => $idsite, 'spokesman_user_id' => $userid])->setInc('oneself_share_time', 1);
                        //分享就进行成为代言人
                    } else {
                        //如果是分销活动的话
                        if ($activity_info['is_distribution'] == 1) {
                            //新增代言活动的数据
                            $add_spokesman_activity_data['spokesman_user_id'] = $userid;
                            $add_spokesman_activity_data['spokesman_nick_name'] = $user_info['nickname'];
                            $add_spokesman_activity_data['spokesman_name'] = $user_info['u_chrname'];
                            $add_spokesman_activity_data['oneself_share_time'] = 1;
                            //进行插入数据
                            db('spokesman_activity')->insert($add_spokesman_activity_data);
                            //修改该用户的代言产品数量
                            db('member')->where(['idmember' => $userid, 'idsite' => $idsite])->update(['spokesman_activity_num' => $user_info['spokesman_activity_num'] + 1]);
                        }
                    }
                    //判断该用户是否是代言人(不是代言人的话)
                    if ($user_info['spokesman_grade'] == 0) {
                        //绑定时间
                        $update_user_data['spokesman_time'] = date('Y-m-d H:i:s', time());
                        $update_user_data['is_balance'] = 1;//默认开启分销结算
                        //等级
                        $update_user_data['spokesman_grade'] = 2;
                        //查询选择机构的一级代言人的信息
                        $one_spokesman = db('member')->where(['idmember' => $spokesman_set_item['spokesman_one_user_id'], 'idsite' => $idsite])->find();
                        if ($one_spokesman) {
                            //上级代言人
                            $update_user_data['parent_user_id'] = $one_spokesman['idmember'];
                            //上级姓名
                            $update_user_data['parent_u_chrname'] = $one_spokesman['u_chrname'];
                            //上级手机号
                            $update_user_data['parent_u_chrtel'] = $one_spokesman['u_chrtel'];
                            //上级的昵称
                            $update_user_data['parent_nick_name'] = $one_spokesman['nickname'];
                            //进行修改为代言人
                            db('member')->where(['idmember' => $userid, 'idsite' => $idsite])->update($update_user_data);
                        }
                    }
                } #endregion
                else {
                    if ($activity_info['is_distribution'] == 1) {
                        //如果有分销的分享id(普通别人分享你的链接或者自己通过分享的链接再次分享)
                        if ($share_id) {
                            //查询出该是否有代言过该商品
                            $share_spokesman_activity = db('spokesman_activity')->field('site_id', true)->where(['activity_id' => $arr["dataid"], 'site_id' => $idsite, 'spokesman_user_id' => $share_id])->find();
                            //查询出代言人的信息
                            $spokesman_info = db('member')->where(['idmember' => $share_id, 'idsite' => $idsite])->find();
                            //如果代言过,那么就进行修改代言产品表中的分享数据
                            if ($share_spokesman_activity) {
                                //如果分享id和该用户id一致  那么就是代言人自己分享
                                if ($userid == $share_id) {
                                    $update_spokesman_activity['oneself_share_time'] = $spokesman_activity['oneself_share_time'] + 1;
                                } else {
                                    //他人分享（这里其实就是普通用户）
                                    $update_spokesman_activity['others_share_time'] = $spokesman_activity['others_share_time'] + 1;
                                }
                                //进行修改
                                db('spokesman_activity')->where(['activity_id' => $arr["dataid"], 'site_id' => $idsite, 'spokesman_user_id' => $share_id])->update($update_spokesman_activity);
                            } else {
                                //新增代言活动的数据
                                $add_spokesman_activity_data['spokesman_user_id'] = $share_id;
                                $add_spokesman_activity_data['spokesman_nick_name'] = $spokesman_info['nickname'];
                                $add_spokesman_activity_data['spokesman_name'] = $spokesman_info['u_chrname'];
                                $add_spokesman_activity_data['oneself_share_time'] = 1;
                                //进行数据库的插入
                                db("spokesman_activity")->insert($add_spokesman_activity_data);
                                //进行修改用户的产品代言个数
                                db('member')->where(['idmember' => $share_id, 'idsite' => $idsite])->setInc('spokesman_activity_num', 1);
                            }
                            //那就是自己直接进详情页进行分享
                        } else {
                            //如果代言过的话，那么就是分享次数加一
                            if ($spokesman_activity) {
                                //进行修改
                                db('spokesman_activity')->where(['activity_id' => $arr["dataid"], 'site_id' => $idsite, 'spokesman_user_id' => $userid])->setInc('oneself_share_time', 1);
                                //就进行成为代言人
                            } else {
                                //新增代言活动的数据
                                $add_spokesman_activity_data['spokesman_user_id'] = $userid;
                                $add_spokesman_activity_data['spokesman_nick_name'] = $user_info['nickname'];
                                $add_spokesman_activity_data['spokesman_name'] = $user_info['u_chrname'];
                                $add_spokesman_activity_data['oneself_share_time'] = 1;
                                //进行插入数据
                                db('spokesman_activity')->insert($add_spokesman_activity_data);
                            }
                        }
                    }
                }
            }
            //修改产品表中的分享次数
            db('activity')->where(['idactivity'=>$arr["dataid"],'siteid'=>$idsite])->update(['share_time'=>$activity_info['share_time'] + 1]);
            $arr["userid"]=$userid;
            $arr["idsite"]=$idsite;
            $arr["ip"]=getip();
            $arr["createtime"]=time();

            $bl=db("forwarded_log")->insert($arr);
            $query->commit();
        } catch (\Exception $e) {
            $query->rollBack();
            Log::error('分享执行数据库出错，info:' . print_r($e, true));
        }

    }

    //增加用户
    public function addUser($data,$siteid,$sitecode)
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
            session("UserInfo_".$sitecode."_nickname",$userinfo["nickname"]);
            session("UserInfo_".$sitecode."_openid",$userinfo["openid"]);
            session("UserInfo_".$sitecode."_ismanage",empty($userinfo["ismanage"])?0:$userinfo["ismanage"]);
            session("UserInfo_".$sitecode."_userid",$userinfo['idmember']);
            session("UserInfo_".$sitecode."_siteid",$userinfo['idsite']);
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
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];

        if(strstr(getip(),'192.168.168'))
        {
            //$userinfo=db('member')->where(array('openid'=>"oZS4v1aiMfreRinDgG-uWZFEDpnk"))->find();
            $userinfo=db('member')->where(array('openid'=>"oZS4v1WWxE2vzQKc6vjUNJmnmlp8"))->find();
           // $userinfo=db('member')->find(534);
            if($userinfo)
            {
                session("UserInfo_".$sitecode."_nickname",$userinfo["nickname"]);
                session("UserInfo_".$sitecode."_openid",$userinfo["openid"]);
                session("UserInfo_".$sitecode."_ismanage",0);
                session("UserInfo_".$sitecode."_userid",$userinfo['idmember']);
                session("UserInfo_".$sitecode."_siteid",$userinfo['idsite']);
            }
            return;
        }


        if($this->isMobile()==false || (empty($request['type'])==false && $request['type']=='test'))
        {
            return;
        }

        $config=$this->wxConfig;
        $idsite=$config['id'];
        $url=ROOTURL.$_SERVER['REQUEST_URI'];
        $api = new \think\wx\Api(
            array(
                'appId' => trim($config['appid']),
                'appSecret'    => trim($config['appsecret']),
            )
        );
        $reload=false;
        if(!empty(session('getuserinfog_code')) && !empty($request['code']))
        {
            if(session('getuserinfog_code')==$request['code'])
            {
                $reload=true;
            }
        }

        if(empty($request['code']) || $reload==true)
        {
            //snsapi_base snsapi_userinfo
            $url= $api->get_authorize_url('snsapi_base',$url,1);
            //echo $url;
            header("location:".$url);
            exit();
        }
        $result = $api->get_userinfo_by_authorize('snsapi_userinfo');
        //print_r($result);
        $this->addUser($result[1],$idsite,$sitecode);
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
                    $config = $this->wxConfig;;
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

    public function activitymanage(){
        $request = Request::instance()->param();
        $sitecode = $request['sitecode'];
        if($this->userinfo['ismanage']!=1)
        {
            header("location:/error.php?msg=".urlencode("您没有权限操作！")."&url=".urlencode("/".$sitecode));
            exit();
        }
        $config = $this->wxConfig;
        $idsite = $config['id'];
        if(isset($_POST['nodeid'])){
            $nodeid=$_POST['nodeid'];
        }elseif(isset($request['nodeid'])){
            $nodeid=$request['nodeid'];
        }else{
            $nodeid=0;
        }

        $typeid = isset($request['typeid']) ? $request['typeid'] : "";
        $tagid = isset($request['tagid']) ? $request['tagid'] : "";
        $keyword=isset($request['keyword'])?$request['keyword']:"";
        $intflag = isset($request['intflag']) ? $request['intflag'] : "1";

        if (isset($request['p'])) {
            $ipage = $request['p'];
        } else {
            $ipage = 1;
        }
        $pagesize = 10;

        $nodes=db('node')->where(["idsite"=>$idsite,'nodetype'=>2])->field(['nodeid','nodename'])->select();
        if(empty($nodes)){
            header("location:/error.php?msg=" . urlencode("栏目不存在，有疑问请和管理联系！") . "&url=" . urlencode("/" . $sitecode));
            exit();
        }
        if($nodeid == 0){
            $nodeid=$nodes[0]['nodeid'];
        }
        $search = [];
        $search['siteid'] = $idsite;
//        $search['chkdown'] = array('neq', 1);
        $search['intflag'] = 2;
        $search['nodeid'] = $nodeid;

        if (!empty($typeid)) $search['fidtype'] = $typeid;
        if (!empty($tagid)) $search['chrtags'] = array('like', "%," . $tagid . ",%");
        if(!empty($keyword)) {
            $search['chrtitle']=['like',"%".$keyword."%"];
            Session::flash('keyword', $keyword);
        }
        if ($intflag == "1") {
            $search['dtsignetime'] = array('>', date('Y-m-d H:i:s', time()));
        } else if ($intflag == "2") {
            $search['dtsignetime'] = array('<', date('Y-m-d H:i:s', time()));
        } elseif($intflag == "3"){
            $search['intflag']=6;
        }elseif($intflag == "4"){
            $search['intflag']=1;
        }elseif($intflag == "5"){
            $search['intflag']=3;
        }else{
            unset($search['intflag']);
        }
        $offset = ($ipage - 1) * $pagesize;
        $show_field = "idactivity,chrtitle,chrimg_m,chrimg,chrurl,chrsummary,dtstart,dtpublishtime,hits,s.is_receive_cashed,a.usertype,a.idactivity,a.intflag";
        $result = db('activity')->alias('a')->join('activity_cashed_card_set s', 'a.idactivity=s.activity_id', 'left')->where($search)->order("chkcontentlev desc,contentlevtime desc,dtpublishtime desc")->field($show_field)->limit($offset, $pagesize)->select();
        $roottpl = 'template/modules/';
        $url = $roottpl . '/mine/activitymanage.html';
        if (Request::instance()->isPost() && isset($request['ajax'])) {
            // echo json_encode($result);exit;
            $url = $roottpl . '/mine/ajax_activitymanage.html';
        }
        $obj = new \app\admin\module\activity($idsite);
        $hdfl = $obj->getDic("hdfl");
        $this->assign('hdfl', $hdfl);
        $hdbq = $obj->getDic("hdbq");
        $this->assign('hdbq', $hdbq);
        $this->assign('nodes',$nodes);
        $this->assign('roottpl', '/' . $roottpl);
        $this->assign('typeid', $typeid);
        $this->assign('tagid', $tagid);
        $this->assign('intflag', $intflag);
        $this->assign('pageSize', $pagesize);
        $this->assign('idsite', $idsite);
        $this->assign('result_data', $result);
        $this->assign('pageIndex', $ipage);
        $this->assign('sitecode', $sitecode);
        $this->assign('SelectFooterTab', 2);
        $this->assign('nodeid', $nodeid);
        $this->assign('is_cashed', checkedMarketingPackage($idsite, 'cashed'));


        return $this->fetch($url);
    }

    public function activitymanagecopy(){
        $data = Request::instance()->param();
        $idsite = $data['siteid'];
        $obj = new \app\admin\module\activity($idsite);
        if ($obj->copydata($data['id'])) {
            echo 1;
        } else {
            echo 0;
        }
        exit();
    }


    public function activitymanageModi(){
        $data = Request::instance()->param();
        $sitecode = $data['sitecode'];
        if($this->userinfo['ismanage']!=1)
        {
            header("location:/error.php?msg=".urlencode("您没有权限操作！")."&url=".urlencode("/".$sitecode));
            exit();
        }
        $config = $this->wxConfig;
        $idsite = $config['id'];
        $userID = $this->userinfo['idmember'];
        $accountID=db('account')->where(['idmember'=>$userID])->value('idaccount');
        $userName= $this->userinfo['nickname'];
        session('idsite',$idsite);
        session('AccountID',$accountID);
        session('UserName',$userName);
        $obj = new \app\admin\module\activity($idsite);
        $cashed_obj = new \app\admin\module\Cashed($idsite);
        if($data['action'] == 'add'){
            $data['nodeid']=$data['id'];
            unset($data['id']);
        }
        if (Request::instance()->isPost()) {

            if (isset($data["intflag"])) {

                if ((int) $data["intflag"] == 0) {
                    unset($data["intflag"]);
                }
            }
            if(empty($data['usertype']))
                $data['usertype']=[];
            //base64转换为图片保存在服务器，并把名字保存数据库

            if(!empty($data['chrimg_m'])){
                $small_imgfile=$this->base64_image_content($data['chrimg_m'],$idsite);
                $data['chrimg_m']=$small_imgfile;
            }

            if(!empty($data['chrimg'])){
                $big_imgfile=$this->base64_image_content($data['chrimg'],$idsite);
                $data['chrimg']=$big_imgfile;
            }


           if(!empty($data['distribution_img'])){
               $distribution_img=$this->base64_image_content($data['distribution_img'],$idsite);
               $data['distribution_img']=$distribution_img;
           }

            $res = $obj->PostData($data);
            if ($res['status'] === 'success') {
                //dump($data);
                $this->success('操作成功', '/'.$sitecode."/activitymanage/".$data['nodeid']);
            } else {
                $this->error($res['msg']);
            }
            exit();
        }



        $hdfl = $obj->getDic("hdfl");
        $this->assign('hdfl', $hdfl);
        //获取自定义会员分类
        $hyfl = $obj->getDic("hyfl");
        $this->assign('hyfl', $hyfl);
        //获取现金券计划列表
        $cashed_plan = $cashed_obj->getCashedPlan();
        $this->assign('cashed_plan', $cashed_plan);
        $hdbq = $obj->getDic("hdbq");
        $this->assign('hdbq', $hdbq);
        $FromTemp = $obj->getFromTemp();
        $this->assign('fromtemp', $FromTemp);
        $user = $obj->getUser();
        $this->assign('user', $user);

        $activityId = isset($data["id"]) ? (int) $data["id"] : 0;
        $orderNum = $obj->getActivityOrderCount($activityId);
        $this->assign('ordernum', $orderNum);

        $datainfo = $obj->deal($data);

        //dump($datainfo);//die;
        $this->assign('datainfo', $datainfo);
        $this->assign('idsite', $idsite);
        $this->assign('is_cashed',checkedMarketingPackage($idsite,'cashed'));//是否具有现金券营销包
        $this->assign('is_distribution',checkedMarketingPackage($idsite,'distribution'));//是否具有分销功能营销包
        $this->assign('sitecode', $sitecode);
        $roottpl = 'template/modules/';
        $url = $roottpl . '/mine/activitymanagemodi.html';
        // halt($url);
        return $this->fetch($url);
    }

    public function base64_image_content($base64_image_content,$idsite)
    {
        $filepath='public/uploads/'.$idsite.'/admin/'.date('Y').'/'.date('m-d').'/';
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type = $result[2];
            if (!is_dir($filepath)) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($filepath, 0777,true);
            }
            $new_file = $filepath .getNumber(). ".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
                return '/' . $new_file;
            } else {
                return false;
            }
        } else {
            return $base64_image_content;
        }
    }

   public function deleteGroupBuy(){
        $request = Request::instance()->param();
        $siteId=$request['idsite'];
        $groupBuyId=$request['group_buy_id'];
        $orderNum = db('group_buy_order')->where(['group_buy_id' => $groupBuyId])->count();
        if($orderNum > 0)
        {
            return ['status' => 'fail', 'msg' => '不允许删除，已有订单生成'];
        }
        $res = db('group_buy')->where(
            [
                'group_buy_id' => $groupBuyId,
                'site_id' => $siteId
            ]
        )
            ->delete();

        if($res)
        {
            return ['status' => 'success', 'msg' => '删除成功'];
        }
        return ['status' => 'fail', 'msg' => '删除失败'];
    }

    public function deletePackage()
    {
        $request = Request::instance()->param();
        try{
            if (!isset($request['activity_id']) || !isset($request['package_id'])) {
                throw new Exception('缺少参数');
            }
            $activityId=$request['activity_id'];
            $packageId=$request['package_id'];
            $order = db('order')->where(['dataid' => $activityId])->find();
            //已发布产品不允许删除套餐
            if($order)
            {
                throw new Exception('已报名产品不允许删除套餐');
            }
            $packageCount = db('package')->where(['activity_id' => $activityId,'state' => 1])->count();
            //如果有效套餐数已小于等于1，拒绝删除
            if($packageCount <= 1)
            {
                throw new Exception('线上下单产品必须至少有一个套餐');
            }
            //删除
            db('package')->where(['package_id' => $packageId])->update(['state' => 0]);

            exit(json_encode(['status' => 'success', 'msg' => '删除成功']));
        }catch (Exception $e){
            exit(json_encode(['status' => 'fail', 'msg' => $e->getMessage()]));
        }

    }

    public function activityChkdown(){
        $data=Request::instance()->param();
        $res = db('activity')->where(['siteid'=>$data['idsite'],'idactivity'=>['in',explode(',', $data['id'])]])->update(['chkdown'=>1]);
        if($res)
        {
            return ['status' => 'success', 'msg' => '下架成功'];
        }
        return ['status' => 'fail', 'msg' => '下架失败'];
    }


    public function payjump(){

        $config = $this->wxConfig;
        $idsite = $config['id'];

        $data=Request::instance()->param();
//        $jumpurl=$data['jumpurl'];
        $content=db('config_jump')->where(['id_site'=>$idsite,'isjump'=>1])->value('content');

        $roottpl = 'template/modules/';
        $url = $roottpl . '/order/payjump.html';
        $this->assign('content',$content);
//        $this->assign('jumpurl',$jumpurl);
        return $this->fetch($url);

    }

}