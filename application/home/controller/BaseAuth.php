<?php
/*
 * @Descripttion: 手机端项目公共类
 * @version: 
 * @Author: ChenJie
 * @Date: 2019-08-07 11:04:16
 * @LastEditors: ChenJie
 * @LastEditTime: 2019-08-29 17:48:28
 */

 namespace app\home\controller;
 use think\Controller;
 use think\Cms;
 use think\Request;


 class BaseAuth extends Controller{
    public $CMS = null;//这里声明
    public $lang = 'cn';
    protected $siteid = 0;    // 机构ID
    protected $sitecode = ''; // 机构代号
    protected $wxConfig = []; // 微信配置信息
    protected $userinfo = []; // 微信用户信息
    protected $pageSize = 4;  // 每次显示数量
    protected $is_new_user = 0;//是否是详情进的新用户
	/**
     * 析构函数
     */
    function __construct(){
	    //Session::start();
        session_start();
        header("Cache-control: private");
        $param = input('param.');
        $sitecode = isset($param['sitecode']) ? $param['sitecode'] : '';

        // 如果站点代号存在的话
        if($sitecode){
            // 初始化配置信息
            $this->initWxConfig($sitecode);
            // 判断机构是否过期
            if (empty($this->wxConfig['expiretime']) || time() > $this->wxConfig['expiretime'])
            {
                header("location:/error.php?msg=" . urlencode("网站异常，请与机构联系。") . "&url=" );
                exit();
            }
            // 拉起微信授权
            $this->baseAuth();
        }
        parent::__construct();
        $this->assign('siteid',$this->siteid);
        $this->assign('idsite',$this->siteid);
        $this->assign('sitecode',$this->sitecode);

    }
    /**
     * 拉起微信授权
     *
     * @return void
     * @author Chenjie
     * @Date 2019-08-12 11:12:16
     */
    private function baseAuth(){
        $request = request()->param();
        
        // 本地环境, 返回测试用户
        if(strstr(request()->ip(),'192.168.168') || strstr(request()->ip(),'127.0.0.1')){
            $userinfo = db('member')->field('idmember,openid,intstate,chrname,nickname,intsex,intcity,intprovince,userimg,dtsubscribetime,unionid,ismanage')->where(['openid' => "oZS4v1aiMfreRinDgG-uWZFEDpnk"])->find();
            // 存入公共变量
            $this->userinfo = $userinfo;
            // 授权成功存入session
            session('siteid_'.$this->siteid.'_userinfo',$userinfo);
            return;
        }

        $userinfo = session('siteid_'.$this->siteid.'_userinfo');
        if($userinfo){
            // 重新获取用户状态、以保证关注状态和管理员状态为最新
            $member = db('member')->field('idmember,openid,intstate,chrname,nickname,intsex,intcity,intprovince,userimg,dtsubscribetime,unionid,ismanage')->where('openid',$userinfo['openid'])->find();
            $this->userinfo = $member;
            session('siteid_'.$this->siteid.'_userinfo',$this->userinfo);
            return;
        }

        // 如果是手机端或者预览模式, 不拉取会员信息
        // if($this->isMobile()==false || (empty($request['type'])==false && $request['type']=='test'))
        // {
        //    return;
        // }
        
        $param = input('param.');
        $current_url = request()->url(true);    // 当前URL
        //  拉起授权
        $api = new \think\wx\Api(['appId' => $this->wxConfig['appid'],'appSecret' => $this->wxConfig['appsecret']]);
        if(!isset($param['code'])){
            $authorize_url = $api->get_authorize_url('snsapi_base',$current_url);
            $this->redirect($authorize_url,301);
        }else{
            // 根据code，获取用户信息
            $result = $api->get_userinfo_by_authorize('snsapi_userinfo');
            $userinfo = $result[1];
            // 如果openid存在
            if($userinfo){
                // 获取省市
                $regionIds = [];
                if(!empty($userinfo->province) && !empty($userinfo->city)) {
                    $regionIds = getRegionIDs($userinfo->province, $userinfo->city);
                }
                $openid = $userinfo->openid;    
                $member = db('member')->field('idmember,openid,intstate,chrname,nickname,intsex,intcity,intprovince,userimg,dtsubscribetime,unionid,ismanage')->where('openid',$openid)->find();

                $data = [
                    'idsite' => $this->siteid,
                    'intstate' => $userinfo->subscribe ? 1 : 3,
                    'chrname' => isset($userinfo->nickname) ? $userinfo->nickname : '游客',
                    'openid' => isset($userinfo->openid) ? $userinfo->openid : '',
                    'nickname' => isset($userinfo->nickname) ? $userinfo->nickname : '游客',
                    'intsex' => isset($userinfo->sex) ? intval($userinfo->sex) : 0,
                    'intcity' => isset($regionIds[1]) ? $regionIds[1] : '',
                    'intprovince' => isset($regionIds[0]) ? $regionIds[0] : '',
                    'userimg' => isset($userinfo->headimgurl) ? $userinfo->headimgurl : '/static/images/userimg.jpg',
                    'dtsubscribetime' => isset($userinfo->subscribe_time) ? $userinfo->subscribe_time : '',
                    'unionid' => isset($userinfo->unionid) ? intval($userinfo->unionid) : 0,
                    'ismanage' => 0,
                ];

                // 如果用户存在，更新数据
                if($member){
                   // $result = db('member')->where('openid',$openid)->update($data);
                    // 如果关注状态更新
                    if($member['intstate'] !== $data['intstate']){
                        member_log($member,1);
                    }
                    $idmember = $member['idmember'];
                    // 如果存在，判断是否管理员
                    $data['ismanage'] = $member['ismanage'];
                }
                // 不存在，写入数据
                else{
                    //判断是否具有代言人的参数，是的话
                    if(array_key_exists('share_id',$request) || array_key_exists('a',$request)){
                        //给基类的是否是新用户属性赋值
                        $this->is_new_user = 1;
                    }
                    $data['openid'] = isset($userinfo->openid) ? $userinfo->openid : '';
                    $data['subscribe_scene'] = isset($userinfo->subscribe_scene) ? $userinfo->subscribe_scene : '';
                    $data['qr_scene'] = isset($userinfo->qr_scene) ? $userinfo->qr_scene : '';
                    $data['qr_scene_str'] = isset($userinfo->qr_scene_str) ? $userinfo->qr_scene_str : '';
                    $idmember = db('member')->insertGetId($data);
                    member_log($data,1);
                    $member = db('member')->field('idmember,openid,intstate,chrname,nickname,intsex,intcity,intprovince,userimg,dtsubscribetime,unionid,ismanage')->where('openid',$openid)->find();
                }

                // 存入公共变量
                $data['idmember'] = $idmember;
                $this->userinfo = $member;
                // 登陆成功存入session
                session('siteid_'.$this->siteid.'_userinfo',$member);
            }
            // 授权拉取失败
            else{
                echo "错误代号：".$result[0]->errcode."<br>错误信息：".$result[0]->errmsg;
                exit();
            }
        }
    }
    /**
     * 初始化配置信息
     *
     * @param string $sitecode 机构代号
     * @return void
     * @author Chenjie
     * @Date 2019-08-28 09:31:03
     */
    private function initWxConfig($sitecode){
        $wxCofnig = cache("WeiXinConfig") ? cache("WeiXinConfig") : [];
        if(!array_key_exists($sitecode,$wxCofnig)){
            $site_manage = db('site_manage')->field('id,site_name,appid,appsecret,token,encodingaeskey,mchid,paykey,cainfo,sslcertpath,sslkeypath,expiretime')->where(['site_code'=>strtolower($sitecode)])->find();
            if($site_manage)
            {
                $wxCofnig[$sitecode] = $site_manage;
                cache("WeiXinConfig",$wxCofnig);
            }else{
                exit($sitecode." 不存在！");
            }
        }
        $this->siteid = $wxCofnig[$sitecode]['id'];
        $this->sitecode = $sitecode;
        $this->wxConfig =  $wxCofnig[$sitecode];
    }
    /*
     * 初始化操作
     */
    public function _initialize(){
	    define('MODULE_NAME',request()->module());  // 当前模块名称是
        define('CONTROLLER_NAME',request()->controller()); // 当前控制器名称
        define('ACTION_NAME',request()->action()); // 当前操作名称是

        // CMS初始化
        $this->CMS = new Cms;
        // CMS渲染到页面
        $this->assign('cms',$this->CMS);
        
        if(cookie('lang_type')){
            $this->lang = cookie('lang_type');
        }
        config('home_lang_list') ? $config_lang_type = config('home_lang_list') : $config_lang_type = ['cn'=>'简体中文'];
        $this->assign('lang',$config_lang_type);

        //加载语言常量
        $constant =  str_replace('\\','/',APP_PATH . 'home/constant/'.$this->lang.'.php');
        require_once $constant;

    }

    /**
     * 是否手机端打开
     *
     * @return boolean
     * @author Chenjie
     * @Date 2019-08-19 16:19:34
     */
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

    /**
     * 微信二维码生成
     *
     * @return void
     * @author Chenjie
     * @Date 2019-08-27 16:07:53
     */
    public function qrcodeurl()
    {
         $api = new \think\wx\Api(
             array(
                 'appId' => trim($this->wxConfig['appid']),
                 'appSecret'    => trim($this->wxConfig['appsecret']),
             )
         );
         $imgurl= $api->get_qrcode_url();
         return $imgurl;
    }
 }
?>