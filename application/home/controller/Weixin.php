<?php
namespace app\home\controller;

use think\Controller;
use think\Request;
class Weixin  extends Controller
{
    public function index()
    {
        $request = Request::instance()->param();
        // 这是使用了Memcached来保存access_token
        //file_put_contents( 'file.txt',  "\r\n=======================\r\n".strtolower($request['sitecode'])."\r\n=======================\r\n",FILE_APPEND);
        $config=getWeiXinConfig(strtolower($request['sitecode']));

        // 开发者中心-配置项-AppID(应用ID)
        $appId =trim($config['appid']) ;// 'wxde570158b0f8a924';
        // 开发者中心-配置项-AppSecret(应用密钥)
        $appSecret = trim($config['appsecret']);//'ef197459898020dd1c1531bf6c08948b';
        // 开发者中心-配置项-服务器配置-Token(令牌)
        $token =trim($config['token']);// 'chinasky2018';
        // 开发者中心-配置项-服务器配置-EncodingAESKey(消息加解密密钥)
        $encodingAESKey =trim($config['encodingaeskey']);//'xTNesy5kYpK0kU4NPQ08bmWC4gOLgQtColFUFoJUflZ';
        //微信支付商户号
        $mchid =trim($config['mchid']);

        // wechat模块 - 处理用户发送的消息和回复消息
       //file_put_contents( 'file.txt',  "\r\n=======================\r\n".json_encode($config)."\r\n=======================\r\n",FILE_APPEND);

        $wechat = new \think\wx\Wechat(array(
            'appId' => $appId,
            'token' =>     $token,
            'encodingAESKey' =>    $encodingAESKey //可选
        ));
        //file_put_contents( 'file.txt', json_encode($config),FILE_APPEND);
         // api模块 - 包含各种系统主动发起的功能
        $api = new \think\wx\Api(
            array(
                'appId' => $appId,
                'appSecret'    => $appSecret
            )
        );
        ob_clean();
		$obj=$wechat->serve();
        //file_put_contents( 'file.txt',  "\r\n=======================\r\n".json_encode($obj)."\r\n=======================\r\n",FILE_APPEND);

        //$word = json_encode($obj)."\r\n";  //双引号会换行 单引号不换行
	    //$word = time()."\r\n";  //双引号会换行 单引号不换行
        //file_put_contents( 'file.txt', $word,FILE_APPEND);
        switch ($obj->MsgType)
        {
            case 'event':
                if($obj->Event=='subscribe')  //关注
                {
                    if(!isset($_SESSION)){
                        session_start();
                    }
                    session_destroy();
                    $openid=$obj->FromUserName;  //openid
                    $weixinid=$obj->ToUserName; //微信id
                    $dtTime=$obj->CreateTime;   //关注时间
					$userinfo= $api->get_user_info($openid);
					file_put_contents('1111.txt',json_encode($userinfo[1]));
                    //file_put_contents( 'file.txt',  "\r\n=======================\r\n".json_encode($userinfo)."\r\n=======================\r\n",FILE_APPEND);
                    $this->addUser($userinfo[1],$config['id']);
                    //$word = json_encode($userinfo)."\r\n";  //双引号会换行 单引号不换行
                    //file_put_contents($filename, $word,FILE_APPEND);

                    $msg = $userinfo[1]->nickname.'，您好，欢迎光临！！！';

                    $wx_reply = db('wx_reply')->where(["type"=>3,"siteid"=>$config['id']])->find();
                    $content = $media_id = "";

                    if(!empty($wx_reply)){
                        if($wx_reply["content"]) {
                            $content = $wx_reply["content"];
                        }
                        if($wx_reply["media_id"]) {
                            $media_id = $wx_reply["media_id"];
                        }

                        if($content && $media_id){
                            $msg = array(
                                "type"=>"image",
                                "media_id"=>$media_id
                            );
                            $wechat->reply($msg);
                            
                            if(!empty($openid)) {
                                $api->send($openid, $content);
                            }

                            $msg = "";
                        }elseif($content){
                            $msg = $content;
                        }elseif($media_id){
                            $msg = array(
                                "type"=>"image",
                                "media_id"=>$media_id
                            );
                        }
                    }
                    if(!empty($msg)) {
                        $wechat->reply($msg);
                    }
                    $this->setuserinfo($openid,$config['id']);
                }
                else if($obj->Event=='unsubscribe') //取消关注
                {
                    $openid=$obj->FromUserName;  //openid
                    //$weixinid=$obj->ToUserName; //微信id
                    //$dtTime=$obj->CreateTime;   //取消关注时间
                    $this->setMemberState($openid);
                    session_destroy();
                    $wechat->reply('再见！');
                }
                else if($obj->Event=="TEMPLATESENDJOBFINISH"){//模版消息

                }
                else if($obj->Event=="SCAN"){


                }
                else{
                    $openid=$obj->FromUserName;  //openid
                    $this->setuserinfo($openid,$config['id']);
                   // $wechat->reply('欢迎光临！');
                }
                break;
            case 'text':
                $msg ='';// '您好！有问题请直接联系微信客服（微信号：lt1554803672）进行咨询，或点击下方图片放大后识别直接添加客服微信，谢谢您的配合！';
                $content = $obj -> Content;

                $openid=$obj->FromUserName;  //openid

                $msg1 = $media_id = "";
                $trueName = "";  // 报名人姓名
                if(!empty($content)) {

                    // 查询活动信息
                    $result = db('activity')->where(array(
                                        'intflag' => 2, 
                                        "dtend" => array(">= time", date("Y-m-d", time())), 
                                        "chrkeyword" => array("like", "%" . trim($content) . "%"),"siteid"=>$config['id']
                                    ))
                                    ->limit(0, 5)
                                    ->order('idactivity desc')
                                    ->field('idactivity,chrtitle')
                                    ->select();
                    
                    
                    if(strstr($content,"+") !== false){
                        $field = explode("+", $content);
                        $mobile = $field[0];
                        $checkcode = $field[1];

                        // 查询订单信息
                        $order = db('order')
                                    ->where([
                                        'idsite'=>$config['id'],
                                        'checkcode' => $checkcode,
                                        'txtdata' => ['like','%'.$mobile.'%']
                                    ])
                                    ->find();
                        

                        // 如果订单存在，则回复二维码
                        if($order){
                            $sitecode = db('site_manage')->where('id',$order['idsite'])->value("site_code");         // 站点代码
                            $qrcode_url = ROOTURL.'/admin/Qrcode/signin/code/'.$sitecode.'/id/'.$order['ordersn'];   // 签到二维码
                            $config = getWeiXinConfig($sitecode);

                            $txtdata = $order['txtdata'];
                            $trueName = explode("☆",$txtdata)[0];

                            // 初始化微信API
                            $api = new \think\wx\Api([
                                'appId' => trim($config['appid']),
                                'appSecret'    => trim($config['appsecret'])
                            ]);

                            // 下载二维码图片
                            $material_path = $this->pullRemoteImages($config['id'],$qrcode_url);
                            // 上传到微信服务器
                            $result = $api ->add_material("image", $material_path)[1];

                            // 获取媒体ID
                            $media_id = "";
                            if($result){
                                $media_id = $result->media_id;
                            }

                            // 设置回复，图片消息内容
                            if($media_id){
                                $msg = [
                                    'type' => 'image',
                                    'media_id' => $media_id
                                ];
                            }
                            $trueName = $trueName ? $trueName."，" : '';
                            $msg1 = "您好，". $trueName ."您已成功完成{$order['chrtitle']}兑换。签到时，请展示以下二维码给工作人员完成核销。感谢您的信任，祝您生活愉快！";
                            $api->send($openid, $msg1);
                            $wechat->reply($msg);
                            exit;
                        }
                    }else if(!empty($result)) {
                        $msg = "嗨~，以下是和“".trim($content)."”相关的产品信息\n\n";
                        $activity_num = count($result);
                        foreach ($result as $key => $r) {
                            $url = "https://www.tongxiang123.cn".url(strtolower($request['sitecode']) . '/detail/' . $r["idactivity"]);
                            $msg .= "<a href=\"" . $url . "\">{$r["chrtitle"]}</a>";
                            if($activity_num > ($key+1)){
                                $msg .= "\n\n";
                            }
                        }
                    }else {
                        $wx_reply = db('wx_reply')->where(["type" => 1, "siteid" => $config['id'],"keyword"=>array("like", "%" . trim($content) . "%")])->find();

                        if(empty($wx_reply)){
                            $wx_reply = db('wx_reply')->where(["type" => 2, "siteid" => $config['id']])->find();
                        }

                        if(!empty($wx_reply)){
                            if($wx_reply["content"]) {
                                $msg1 = $wx_reply["content"];
                            }
                            if(!empty($wx_reply["reply_img_url"]))
                            {
                                if($wx_reply["media_id"]) {
                                    $media_id = $wx_reply["media_id"];
                                }
                            }

                            if($msg1 && $media_id){
                                $msg = array(
                                    "type"=>"image",
                                    "media_id"=>$media_id
                                );

                                if(!empty($openid)) {
                                    $api->send($openid, $msg1);
                                }
                            }elseif($msg1){
                                $msg = $msg1;
                            }elseif($media_id){
                                $msg = array(
                                    "type"=>"image",
                                    "media_id"=>$media_id
                                );
                            }
                        }
                    }
                }
                if(!empty($msg))
                {
                    $wechat->reply($msg);
                }

                //发送客服二维码图片
                //kfmessagetemplate.sendkfimagemessage(message.FromUserName, function (flag, msg) {
                //    console.log(msg);
                //});
                break;
            default:
				$wechat->reply('您好，欢迎光临！');
                //res.reply('欢迎来到儿童信息网！');
        }
		
		//file_put_contents($filename, $word,FILE_APPEND);

    }

    //创建菜单
	public  function createmune()
    {
        // 这是使用了Memcached来保存access_token
        cache(array(
            //'type'=>'Memcached',
            'host'=>'localhost',
            'port'=>'11211',
            'prefix'=>'think',
            'expire'=>0
        ));

        $request = Request::instance()->param();

        // 这是使用了Memcached来保存access_token

        $config=getWeiXinConfig(strtolower($request['sitecode']));

//        print_r($config);

        // 开发者中心-配置项-AppID(应用ID)
        $appId =trim($config['appid']) ;// 'wxde570158b0f8a924';
        // 开发者中心-配置项-AppSecret(应用密钥)
        $appSecret = trim($config['appsecret']);//'ef197459898020dd1c1531bf6c08948b';
        // 开发者中心-配置项-服务器配置-Token(令牌)
        $token =trim($config['token']);// 'chinasky2018';
        // 开发者中心-配置项-服务器配置-EncodingAESKey(消息加解密密钥)
        $encodingAESKey =trim($config['encodingaeskey']);//'xTNesy5kYpK0kU4NPQ08bmWC4gOLgQtColFUFoJUflZ';
        // wechat模块 - 处理用户发送的消息和回复消息

        // api模块 - 包含各种系统主动发起的功能
        $api = new \think\wx\Api(
            array(
                'appId' => $appId,
                'appSecret'    => $appSecret
            )
        );
		//$tokenTmp=$api->new_access_token();
		//cache('wechat_token', $tokenTmp);

        $request = Request::instance()->param();

        $sitecode=strtolower($request['sitecode']);
        $res=$api->create_menu('
        {
            "button":[
                {   
                  "type":"view",
                  "name":"进入网站",
                  "url":"https://www.tongxiang123.cn/'.$sitecode.'"
                }
            ]
        }');
        /*
        $res2=$api->create_menu('
        {
            "button":[
                {   
                  "type":"view",
                  "name":"进入系统",
                  "url":"http://www.tongxiang123.com/'.$sitecode.'"
                },
                {
                    "name":"主菜单2",
                    "sub_button":[
                        {
                            "type":"click",
                            "name":"点击推事件",
                            "key":"click_event1"
                        },
                        {
                            "type":"view",
                            "name":"跳转URL",
                            "url":"http://www.example.com/"
                        },
                        {
                            "type":"scancode_push",
                            "name":"扫码推事件",
                            "key":"scancode_push_event1"
                        },
                        {
                            "type":"scancode_waitmsg",
                            "name":"扫码带提示",
                            "key":"scancode_waitmsg_event1"
                        }
                    ]
               },
               {
                    "name":"主菜单3",
                    "sub_button":[
                        {
                            "type":"pic_sysphoto",
                            "name":"系统拍照发图",
                            "key":"pic_sysphoto_event1"
                        },
                        {
                            "type":"pic_photo_or_album",
                            "name":"拍照或者相册发图",
                            "key":"pic_photo_or_album_event1"
                        },
                        {
                            "type":"pic_weixin",
                            "name":"微信相册发图",
                            "key":"pic_weixin_event1"
                        },
                        {
                            "type":"location_select",
                            "name":"发送位置",
                            "key":"location_select_event1"
                        }
                    ]
               }
            ]
        }');
        */
        print_r($res);
    }
    
    //增加用户
    public function addUser($data,$siteid)
    {
        /*
        subscribe 用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
        openid 用户的标识，对当前公众号唯一
        nickname 用户的昵称
        sex 用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
        city 用户所在城市
        country 用户所在国家
        province 用户所在省份
        language 用户的语言，简体中文为zh_CN
        headimgurl 用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
        subscribe_time 用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
        unionid 只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
        remark 公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注
        groupid 用户所在的分组ID（兼容旧的用户分组接口）
        tagid_list 用户被打上的标签ID列表
        subscribe_scene 返回用户关注的渠道来源，ADD_SCENE_SEARCH 公众号搜索，ADD_SCENE_ACCOUNT_MIGRATION 公众号迁移，ADD_SCENE_PROFILE_CARD 名片分享，ADD_SCENE_QR_CODE 扫描二维码，ADD_SCENEPROFILE LINK 图文页内名称点击，ADD_SCENE_PROFILE_ITEM 图文页右上角菜单，ADD_SCENE_PAID 支付后关注，ADD_SCENE_OTHERS 其他
        qr_scene 二维码扫码场景（开发者自定义）
        qr_scene_str 二维码扫码场景描述（开发者自定义）

        */

        $regionIds=getRegionIDs($data->province,$data->city);
        $userinfo = [
            'intstate' => $data->subscribe ? 1 : 3,
            'chrname' => isset($data->nickname) ? $data->nickname : '游客',
            'openid' => isset($data->openid) ? $data->openid : '',
            'nickname' => isset($data->nickname) ? $data->nickname : '游客',
            'intsex' => isset($data->sex) ? intval($data->sex) : 0,
            'intcity' => isset($regionIds[1]) ? $regionIds[1] : '',
            'intprovince' => isset($regionIds[0]) ? $regionIds[0] : '',
            'userimg' => isset($data->headimgurl) ? $data->headimgurl : '/static/images/userimg.jpg',
            'dtsubscribetime' => isset($data->subscribe_time) ? $data->subscribe_time : '',
            'unionid' => isset($data->unionid) ? intval($data->unionid) : 0,
            'ismanage' => 0,
        ];

        // 用户关注渠道
        if($data->subscribe_scene){
            $userinfo['subscribe_scene'] = $data->subscribe_scene;
        }
        // 场景ID
        if($data->qr_scene){
            $userinfo['qr_scene'] = $data->qr_scene;
        }
        // 场景字符串
        if($data->qr_scene_str){
            $userinfo['qr_scene_str'] = $data->qr_scene_str;
        }

        $member = db('member')->where(array('openid'=>$data->openid,'idsite'=>$siteid))->find();
        // 日志信息数组
        $logArr = $userinfo;   
        if($member){
            $remark = "关注";
            // 如果状态是游客
            if($member["intstate"] == 3) {
                $remark = "游客转关注";
                $userinfo['is_to_attention'] = 1;//游客转关注的标识
                $this->new_member_set_cashed($siteid,$userinfo,$member['idmember']); //调用新用户关注是否送券的方法
            }
            //2019年10月13号，判断有此时关注的用户是否通过代言人二维码关注的，判断二维码扫码场景描述是否有值
            $this->lock_new_user_by_spokesman($data,$member,$siteid);

            // 更新用户信息
            $bool = db('member')->where(array('openid'=>$data->openid,'idsite'=>$siteid))->update($userinfo);

            // 用户状态不一致，写入日志
            if($userinfo['intstate'] != $member['intstate']){
                $logArr['old_intstate'] = $member['intstate'];
                member_log($logArr,1,$remark);
            }
        }else{
            // 新增用户信息
            $userinfo['idsite'] = $siteid;
            $userinfo['dtcreatetime'] = time();
            $bool = db('member')->insert($userinfo);
            $logArr["idmember"] = db('member')->getLastInsID();
            //调用新用户关注是否送券的方法
            $this->new_member_set_cashed($siteid,$userinfo,$logArr["idmember"]);
            member_log($logArr,1,"关注");
        }

        return $bool;
    }

    /**
     * 新用户关注送券
     * @param $siteid
     * @param $arr
     * @param $idmember
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function new_member_set_cashed($siteid,$arr,$idmember){
        if(checkedMarketingPackage($siteid,'cashed')) {
            //查询新用户关注是否送券的设置
            $new_member_set = db('new_member_cashed_set')->where(['site_id' => $siteid, 'is_send_cashed' => 1])->find();
            //如果赠券
            if ($new_member_set) {
                //进行封装添加领取记录的数据
                $add_receive_param['create_time'] = date('Y-m-d H:i:s', time());//领取时间
                // 保证不会有重复领取编号存在
                while (true) {
                    $receive_no = date('YmdHis') . rand(100000, 999999); // 订单编号
                    $receive_no_count = db('cashed_card_receive')->where("receive_no = '$receive_no'")->count();
                    if ($receive_no_count == 0)
                        break;
                }
                $request = Request::instance()->param();
                $sitecode = strtolower($request['sitecode']);
                $add_receive_param['receive_no'] = $receive_no;//领取编号
                $add_receive_param['cashed_type'] = 3;//现金券类型
                $add_receive_param['cashed_amount'] = $new_member_set['send_cashed_amount'];//现金券金额
                $add_receive_param['cashed_validity_time'] = date('Y-m-d H:i:s', strtotime(" + {$new_member_set['send_cashed_validity']} day", time()));//有效期时间
                $add_receive_param['cashed_validity_day'] = $new_member_set['send_cashed_validity'];//有效期天数
                $add_receive_param['receive_cashed_name'] = '新用户关注专用现金券';//领取的现金券标题
                $add_receive_param['receive_activity_name'] = '';//领取来源（活动名称）
                $add_receive_param['receive_member_id'] = $idmember;//领取人会员id（用户）
                $add_receive_param['receive_nick_name'] = $arr['nickname'];//领取人的昵称
                $add_receive_param['receive_header_image'] = $arr['userimg'];//领取人的头像
                $add_receive_param['receive_source'] = 1;//领取渠道
                $add_receive_param['used_status'] = 1;//使用状态
                $add_receive_param['site_id'] = $siteid;//站点id
                //执行插入数据
                $bool = db('cashed_card_receive')->insert($add_receive_param);
                if($bool){
                    // 发送客服消息
                    $msg = '您好,您已成功获得【'.$new_member_set['send_cashed_amount'].'】元的现金券，欢迎使用！现金券已经存入“会员中心->我的现金券”中，快去看看吧！<a href=\"'.request()->domain().'/'.$sitecode.'/cashedlist/1\">【查看详情】</a>';
                    send_ordinary_msg($sitecode, $arr['openid'], $msg);
                }
            }
        }
    }

    /**
     * 锁定用户，通过代言人公众号二维码关注
     * @param $data
     * @param $member
     * @param $siteid
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function lock_new_user_by_spokesman($data,$member,$siteid){
        if($data->qr_scene_str){
            $qr_scene_str = $data->qr_scene_str;
            //将自定义的代言人关注公众号二维码的参数转化为数组
            $qr_scene_str_arr = json_decode($qr_scene_str,true);
            //如果有分享id
            if((isset($qr_scene_str_arr['share_id']) && $qr_scene_str_arr['share_id']) || (isset($qr_scene_str_arr['a']) && $qr_scene_str_arr['a'])){
                //查询该机构的分销设置
                $spokesman_set_item = db('spokesman_set_item')->field('create_time',true)->where(['site_id'=>$siteid])->find();
                $lock_user_id = $qr_scene_str_arr['share_id']?$qr_scene_str_arr['share_id']:$qr_scene_str_arr['a'];
                //查找出分享用户的信息
                $lock_user_info=db("member")->where(array('idmember'=>$lock_user_id,'idsite'=>$siteid))->find();
                //如果有设置过锁客，并且是下单锁,还有未锁客过的,有分享用户的信息，并且分享用户是代言人
                if($spokesman_set_item && $spokesman_set_item['lock_way'] ==  10 && $member['intlock'] != 1 && $lock_user_info && $lock_user_info['spokesman_grade'] != 0){
                    //进行修改用户的锁客信息,改为已锁客
                    $update_user_data = [
                        'intlock'=>1,'lock_user_id'=>$lock_user_id,'lock_u_chrname'=>$lock_user_info['u_chrname'],
                        'lock_u_chrtel'=>$lock_user_info['u_chrtel'],'lock_nick_name'=>$lock_user_info['nickname'],'lock_time'=>date('Y-m-d H:i:s',time())
                    ];
                    $update_user_bool = db("member")->where(array('idmember'=>$member['idmember'],'idsite'=>$siteid))->update($update_user_data);
                    //如果锁客成功并且需要发送锁客通知
                    if($update_user_bool && $spokesman_set_item['lock_notice'] == 1){
                        template_tg_lock($lock_user_info,$member);
                    }
                    //锁客后，判断是否有产品id，就是关注后，发送客服消息，那其继续回到详情页面
                    if(isset($qr_scene_str_arr['data_id']) && $qr_scene_str_arr['data_id']){
                        $request = Request::instance()->param();
                        $sitecode = strtolower($request['sitecode']);
                        // 发送客服消息
                        $msg = '关注成功，返回<a href=\"'.request()->domain().'/'.$sitecode.'/detail/'.$qr_scene_str_arr['data_id'].'?share_id='.$qr_scene_str_arr['share_id'].'&a='.$qr_scene_str_arr['a'].'\">【继续购买】</a>';
                        send_ordinary_msg($sitecode, $member['openid'], $msg);
                    }
                }
            }
        }
    }

    public function setMemberState($openid1,$flag=2)
    {
        $openid=(string)$openid1;
        $arr=[];
        $arr['intstate']=2;//会员状态（1审批通过 2取消关注 3游客）
        $arr['dtunsubscribetime']=time();
        $arr['ismanage'] = 0;
        
        db('member')->where(array('openid'=>$openid))->update($arr);

        $logArr = $arr;
        $logArr["openid"] =$openid;
        $logArr["old_intstate"] = 1;
        member_log($logArr,1,"取消关注");
    }

    public function abc()
    {

        //$openid="oZS4v1aiMfreRinDgG-uWZFEDpnk";
        //$this->setMemberState($openid);
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

    private function setuserinfo($openid,$siteid)
    {
        $row=db('member')->field('idmember,nickname')->where(['openid'=>$openid,'idsite'=>$siteid])->find();
        if($row)
        {
            setcookie("nickname",$row['nickname']);
            setcookie("userid",$row['idmember']);
        }

    }

    // 拉取远程图片
    private function pullRemoteImages($siteId,$url){
        $path = ROOT_PATH.'public/uploads/'.$siteId.'/reply';
        if(is_dir($path) == false)
        {
            mkdir($path, 0777, true);
        }
        $path .= "/".getNumber().".jpg";
        file_put_contents($path, file_get_contents($url));
        return $path;
    }
}
