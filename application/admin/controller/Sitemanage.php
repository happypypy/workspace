<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/2/26
 * Time: 10:15
 */

namespace app\admin\controller;
use think\Request;
use think\Log;


class Sitemanage  extends Base {

    //站点列表
    public function index(){
        if($this->CMS->CheckPurview('sitemanage','view')==false){
            $this->NoPurview();
        }
        $obj = new \app\admin\module\Sitemanege;
        $result = $obj->index();
        $this->assign('site_list',$result['data']);
        $this->assign('page',$result['page']);
        return $this->fetch();

    }

    //站点添加，编辑跳转地址
    public function sitedeal(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('sitemanage',$request['action'])==false){
            $this->NoPurview();
        }

        $obj = new \app\admin\module\Sitemanege;
        $result = $obj->site_deal($request);
        
        $tmp = [$result['appid'], $result['appsecret'], $result['mchid'], $result['paykey']];
        $key = md5(implode(',', $tmp));

        $this->assign('key' , $key);
        $this->assign('site_info',$result);
        return $this->fetch();
    }

    //站点提交地址
    public function sitepost(){
        $request = Request::instance()->param();

        if(empty($request['site_name']) || empty($request['site_code'])) {
            return $this->error('前面带*号的为必填项');
        }
        if($this->CMS->CheckPurview('sitemanage',$request['action'])==false){
            $this->NoPurview();
        }
        $obj = new \app\admin\module\Sitemanege;
        $result = $obj->site_post($request);
        if($result['status'] === 'success'){
            $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
        }else{
            $this->error($result['msg']);
        }
    }
    public function test()
    {
        $request = Request::instance()->param();
        $id=$request['id'];
        $obj = new \app\admin\module\Sitemanege;
        $bool = $obj->site_ini($id);
        echo "OK";
        exit();
    }


    /**
     * 生成token
     */
    public function createToken()
    {
        mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = "";
        chr(45);
        $uuid = substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);

        echo json_encode(array("token" => $uuid));
        exit;
    }



    /**
     * 测试支付功能
     * @author Hlt
     * @DateTime 2019-04-22T16:21:29+0800
     * @return   array                   创建微信支付订单是否成功及错误信息
     */
    public function testPay()
    {
 
        $params = Request::instance()->param();

        $obj = new \app\admin\module\Sitemanege;
        $result = $obj->site_post($params);
        if($result['status'] === 'fail')
        {
            exit(json_encode($result));
        }

        if(empty($params['mchid']) || empty($params['paykey']))
        {
            exit(json_encode(['status' => 'fail', 'msg' => '请填写支付API密钥和商户号']));
        }
        $api = new \think\wx\Api(
            array(
                'appId' => trim($params['appid']),
                'appSecret' => trim($params['appsecret']),
                'mchId' => trim($params['mchid']),
                'key' => trim($params['paykey'])
            )
        );
        $conf = [];
        $conf['body'] = '支付功能测试';
        $conf['product_id'] = 10;
        $conf['notify_url'] = ROOTURL . "/admin/api/testPayNotify/sitecode/" . $params['site_code'];
        $conf['out_trade_no'] = 'TEST' . getOrderSn();
        $conf['total_fee'] = 1;//总金额
        $conf['trade_type'] = 'NATIVE';
        $rs = $api->wxPayUnifiedOrder($conf);
        if ($rs["return_code"] == "SUCCESS" && $rs["result_code"] == "SUCCESS") {
            $code_url = $rs["code_url"];
            echo json_encode(array("status" => "success", "pay_url" => "/admin/api/qrCode/url/" . base64_encode($code_url)));
            exit;
        }
        Log::warning('支付功能测试，不能发起支付，$conf = ' . print_r($conf, true) . ' $rs = ' . print_r($rs, true));
        echo json_encode(array("status" => "fail", "msg" => "创建支付订单失败"));
        exit;
    }

    // 更新微信用户列表
    public function get_wechat_list(){
        $param = input('param.');
        $idsite = $param['id'] ? intval($param['id']) : 0;
        $page = isset($param['page']) ? intval($param['page']) : 1;
        $pagesize = 100;

        // 获取配置文件
        $conifg = db('site_manage')->field('appid,appsecret')->where('id',$idsite)->find();
        $api = new \think\wx\Api([
            'appId'=> $conifg['appid'],
            'appSecret' => $conifg['appsecret']
        ]);

        $openid_list = cache("openid_list_".$idsite);
        // 如果缓存无数据, 则获取OPENID列表
        if(!$openid_list){
            $next_openid = '';                       // 最后一个openid
            $user_list = $api->get_user_list()[1];   // 用户列表
            $total = $user_list->total;              // 数据总数
            $pull_number = ceil($total / 10000) - 1; // 拉取次数

            // 循环获取OPENID列表
            $openid_list = [];
            for($i=0; $i<=$pull_number; $i++){
                if($i != 0){
                    $openid_list = (array)$user_list->data->openid;   
                }else{
                    $tmp_openid_list = $api->get_user_list($next_openid)[1];
                    $tmp_openid_list = (array)$tmp_openid_list->data->openid;
                    $openid_list = array_merge($openid_list,$tmp_openid_list);
                }
                $next_openid = $user_list->next_openid;
            }
            cache("openid_list_".$idsite,$openid_list);
        }

        // OPENID列表分页
        $total_record = count($openid_list);  // 总条数
        $total_page = ceil($total_record/$pagesize);
        $start = ($page-1) * $pagesize;       // 偏移量，当前页-1乘以每页显示条数
        $tmp_openid_list = array_slice($openid_list, $start, $pagesize);

        // 批量抓取用户信息
        $user_info_list = (array)$api->batch_get_user_info($tmp_openid_list)[1]->user_info_list;

        // 循环用户信息,存在则更新信息,否则写入数据库
        foreach($user_info_list as $user_info){
            $user_info = (array)$user_info;
            $regionId = getRegionIDs($user_info['province'],$user_info['city']);
            $data = [
                'openid' => $user_info['openid'],
                'nickname' => $user_info['nickname'],
                'intsex' => $user_info['sex'],
                'intcity' => $regionId[1],
                'intprovince' => $regionId[0],
                'userimg' => $user_info['headimgurl'],
                'dtsubscribetime' => $user_info['subscribe_time'],
                'subscribe_scene' => $user_info['subscribe_scene'],
                'qr_scene' => $user_info['qr_scene'],
                'qr_scene_str' => $user_info['qr_scene_str'],
                'intstate' => 1,
                'intlock' => 2,
                'idsite' => $idsite
            ];
			$data2 = [
				'openid' => $user_info['openid'],
				'userimg' => $user_info['headimgurl'],
				'intstate' => 1,
				'old_intstate' => 0,
				'dtsubscribetime' => $user_info['subscribe_time'],
				'dtunsubscribetime' => '',
				'idsite' => $idsite
			];
            $member = db('member')->where(['openid'=>$user_info['openid'],'idsite'=>$idsite])->find();
            if($member){
                $result = db('member')->where(['openid'=>$user_info['openid'],'idsite'=>$idsite])->update($data);
				if($result){
					$data2['old_intstate'] = $member['intstate'];
					if($member['intstate'] == 1){
						$remark = '关注';
					}else if($member['intstate'] == 3){
						$remark = '游客转关注';
					}else if($member['intstate'] == 2){
						$remark = '重新关注';
                    }
                    $data2['idmember'] = $member['idmember'];
					member_log($data2,1,$remark);
				}
            }else{
                $idmember = db('member')->insertGetId($data);
                $data2['idmember'] = $idmember;
				member_log($data2,1,'关注');
            }
        }

        // 进度百分比
        if($total_record <= $pagesize){
            $percentage = 100;
        }else{
            $percentage = number_format((($page * $pagesize) / $total_record),1) * 100;
        }

        $page++;
        if($page-1 >= $total_page){
            cache("openid_list_".$idsite,null);
        }
        
        // 返回结果
        $result = [
            'page'=>$page, 
            'total_record' => $total_record,
            'total_page'=>$total_page, 
            'percentage'=>$percentage
        ];

        $this->assign("idsite",$idsite);
        $this->assign("result",$result);
        return $this->fetch();
    }
}