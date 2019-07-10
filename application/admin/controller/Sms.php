<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/2/23
 * Time: 17:31
 */

namespace app\admin\controller;


use think\Request;
use think\Exception;
use app\admin\module\Common;

class Sms extends Basesite
{
    //短信申请
    public function sms_apply()
    {
        if(!$this->CMS->CheckPurview('sms','opensms')){
            $this->NoPurview();
        }

        $data = Request::instance()->param();

        $obj = new \app\admin\module\Sitemanege;
        if (Request::instance()->isPost()) {
            $bool = $obj->sms_post($data);
            if ($bool) {
                $this->success('操作成功', url('sms/sms_apply'));
            } else {
                $this->error('操作失败');
            }
            exit();
        }

        $idsite = session('idsite');
        $sm_info = db('site_manage')->where('id=' . $idsite)->find();

        $this->assign('sm_info', $sm_info);
        return $this->fetch();
    }

    public function sms_open_config()
    {
        if(!$this->CMS->CheckPurview('sms','autosend')){
            $this->NoPurview();
        }

        $data = Request::instance()->param();

        $obj = new \app\admin\module\Sitemanege;
        if (Request::instance()->isPost()) {
            $bool = $obj->sms_post_open_config($data);
            if ($bool) {
                $this->success('操作成功', url('sms/sms_open_config'));
            } else {
                $this->error('操作失败');
            }
            exit();
        }

        $datainfo=$obj->sms_get_open_config();

        $this->assign('data', $datainfo);
        return $this->fetch();
    }
    //短信充值列表
    public function sms_recharge_list()
    {
        if(!$this->CMS->CheckPurview('sms','recharge')){
            $this->NoPurview();
        }

        $data = Request::instance()->param();

        $obj = new \app\admin\module\Sitemanege;
        $result = $obj->sms_recharge_list($data);

        //列表
        $this->assign('list', $result['data']);
        $this->assign('page', $result['page']);
        $this->assign('search', $result["search"]);
        return $this->fetch();
    }

    //短信支付功能
    public function pay()
    {
        $request = Request::instance()->param();
        if (isset($request["idsite"])) {
            $idsite = $request["idsite"];
        } else {

            $idsite = session('idsite');
        }
        //如果没有订单id，那么就是第一次生成订单的充值
        if(!isset($request['sms_order_id'])){
            //短信数量
            $sms_num = isset($request["sms_num"]) ? $request["sms_num"] : 0;

            $total = $sms_num * SINGLE_SMS_PRICE;
            $ordersn = "SMS" . getOrderSn();
            $accountid = session("AccountID");
            if(!isset($idsite) || !isset($accountid)) {
                echo json_encode(array("status" => "fail", "msg" => "请先登录后充值"));
                exit;
            }

            $order = array();
            $order["order_sn"] = $ordersn;
            $order["sms_num"] = $sms_num;
            $order["order_price"] = $total;
            $order["create_time"] = date("Y-m-d H:i:s");
            $order["idsite"] = $idsite;
            $order["accountid"] = $accountid;
            $order["ip"] = getip();
            $rs = db("sms_order")->insert($order);

            if(!$rs){
                echo json_encode(array("status" => "fail", "msg" => "创建订单失败"));
                exit;
            }
            //否则就是继续支付
        }else{
            //查找出该订单的数据
            $sms_order = db("sms_order")->field('order_price,order_sn')->where(['sms_order_id'=>$request['sms_order_id'],'status'=>0])->find();
            if(!$sms_order) echo json_encode(array("status" => "fail", "msg" => "未找到需要继续支付的订单"));
            $ordersn = $sms_order['order_sn'];
            $total = $sms_order['order_price'];
        }

        $sitecode = getSiteCode(7);
        $config = getWeiXinConfig(strtolower($sitecode));
        $api = new \think\wx\Api(
            array(
                'appId' => trim($config['appid']),
                'appSecret' => trim($config['appsecret']),
                'mchId' => trim($config['mchid']),
                'key' => trim($config['paykey'])
            )
        );
        $conf = [];
        $body = "短信充值";
        $conf['body'] = $body;
        $conf['product_id'] = 5;
        $conf['notify_url'] = ROOTURL . "/home/index/sms_pay_notify";
        $conf['out_trade_no'] = $ordersn;
        $conf['total_fee'] = $total * 100;//总金额
        $conf['trade_type'] = 'NATIVE';
        $rs = $api->wxPayUnifiedOrder($conf);
        if ($rs["return_code"] == "SUCCESS" && $rs["result_code"] == "SUCCESS") {
            $code_url = $rs["code_url"];
            echo json_encode(array("status" => "success", "pay_url" => "/admin/api/qrCode/url/" . base64_encode($code_url)));
            exit;
        }
        echo json_encode(array("status" => "fail", "msg" => "创建支付订单失败"));
        exit;
    }


    //短信充值
    public function sms_recharge(){
        if(!$this->CMS->CheckPurview('sms','recharge')){
            $this->NoPurview();
        }

        $idsite = session('idsite');
        if(!isset($idsite) || empty($idsite)){
            die("请先登录");
        }
        $sm_info = db('site_manage')->where('id=' . $idsite)->find();
        if ($sm_info["sms_status"] != 2) {
            die("请先申请开通短信");
        }
        if ($sm_info["sms_num"] <= 0) {
           // die("短信余额不足，请先充值后，再发送短信");
        }
        return $this->fetch();
    }

    //继续支付
    public function sms_again_recharge(){
        if(!$this->CMS->CheckPurview('sms','recharge')){
            $this->NoPurview();
        }

        $idsite = session('idsite');
        if(!isset($idsite) || empty($idsite)){
            die("请先登录");
        }
        $request = Request::instance()->param();
        if(!$request['sms_order_id']) $this->error('缺少必要的参数');
        $this->assign('sms_order_id',$request['sms_order_id']);
        return $this->fetch();
    }

    //发送日志
    public function send_log(){
        if(!$this->CMS->CheckPurview('sms','sendmanage')){
            $this->NoPurview();
        }

        $obj = new \app\admin\module\Sitemanege;
        $data = Request::instance()->param();
        if (isset($data["idsite"])) {
            $idsite = $data["idsite"];
        } else {
            $idsite = session('idsite');
        }

        $data["id"] = $idsite;

        $result = $obj->sms_send_log_list($data);
        $search = $result["search"];
        $this->assign('list', $result['data']);
        $this->assign('page', $result['page']);
        $this->assign('search', $search);
        return $this->fetch();
    }

    //短信批量发送
    public function sms_batch_send()
    {
        $data = Request::instance()->param();

        $obj = new \app\admin\module\Sitemanege;
        $sm_info = $obj->checkSmsNum($this->idsite);

        if (Request::instance()->isPost()) {
            $msg = $obj->sms_batch_send($data);
            if ($msg =="success") {
                $this->success('操作成功', PUBLIC_URL . 'postsuccess.html');
            } else {
                $this->error($msg,null,'',6);
            }
            exit();
        }


        //获取模版列表
        $templates = $obj->sms_template_list($this->idsite);

        $this->assign('sm_info', $sm_info);
        $this->assign('templates', $templates['data']);
        return $this->fetch();
    }

    /**
     * 展示短信模版列表
     * @access public
     */
    public function sms_template_list()
    {
        if(!$this->CMS->CheckPurview('sms','smstemplate')){
            $this->NoPurview();
        }

        $obj = new  \app\admin\module\Sitemanege;
        $result = $obj->sms_template_list($this->idsite);

        $this->assign('list', $result['data']);
        $this->assign('page', $result['page']);
        // $this->assign('search', $result['search']);

        return $this->fetch();
    }


    /**
     * 编辑、新建短信模版时获取模版数据
     * @access public
     */
    public function sms_template()
    {
        if(!$this->CMS->CheckPurview('sms' , 'smstemplate'))
        {
            $this->NoPurview();
        }

        $request = Request::instance();
        $params = $request->param();
        $obj = new \app\admin\module\Sitemanege();
        $sms_template = $obj->get_sms_template($params, $this->idsite);
        if(isset($params['action']))
            $this->assign('action',$params['action']);

        $this->assign('sms_template',$sms_template);
        return $request->isAjax() ? json($sms_template) :  $this->fetch();
    }

    /**
     * 处理编辑、新建短信模版的提交数据
     * @access public
     */
    public function sms_template_post()
    {
        if(!$this->CMS->CheckPurview('sms' , 'smstemplate'))
        {
            $this->NoPurview();
        }

        $request = Request::instance()->param();
        if (empty($request['name']) || empty($request['content']))
        {
            $this->error('前面带*号的为必填项');
        }

        if(!isset($request['action']) || !in_array($request['action'], ['create', 'revise']))
        {
            $this->error('参数错误');
        }
        $request['siteid'] = $this->idsite;

        $obj = new \app\admin\module\Sitemanege();
        $result = $obj->sms_template_post($request);
        // var_dump($result);die;
        if($result['status'] == 'success')
        {
            $this->success($result['msg'], PUBLIC_URL . 'postsuccess.html');
        }else
        {
            $this->error($result['msg']);
        }
    }

    /**
     * 删除短信模版
     * @access public
     */
    public function sms_template_del()
    {
        if(!$this->CMS->CheckPurview('sms' , 'smstemplate'))
        {
            $this->NoPurview();
        }

        $request = Request::instance()->param();

        if(!isset($request['id']) || empty($request['id']))
        {
            $this->error('参数错误，请刷新页面');
        }

        $option = [
            'id' => $request['id'],
            'siteid' => $this->idsite
        ];

        $obj = new \app\admin\module\Sitemanege();

        if($obj->sms_template_del($option))
        {
            $this->success('操作成功');
        }else
        {
            $this->error('操作失败');
        }
    }


    /**
     * 根据上传的excel文件，批量向文件中的手机号发送特定的经过变量替换的短信
     * @author Hlt
     * @DateTime 2019-04-28T14:24:13+0800
     * @return   void
     */
    public function send_sms_with_xlsx()
    {
        $data = Request::instance()->param();

        $obj = new \app\admin\module\Sitemanege;
        $sm_info = $obj->checkSmsNum($this->idsite);

        if (Request::instance()->isPost())
        {
            try
            {
                // 不保存上传的excel文件
                //判断是否有上传文件
                if(empty($_FILES) || !isset($_FILES['mobile-sheet']) || $_FILES['mobile-sheet']['error'] > 0)
                {
                    throw new Exception('请先上传文件', 1);
                }
                // 判断上传文件扩展名
                if(!in_array(pathinfo($_FILES['mobile-sheet']['name'], PATHINFO_EXTENSION), ['xls', 'xlsx']))
                {
                    throw new Exception('只能上传xls,xlsx文件', 2);
                }

                //解析上传的excel文件并生成短信发送计划
                $sms = new \app\admin\module\Sms;
                $sendResult = $sms->sendTextMsgWithExcel();
                if($sendResult['status'] === 'fail')
                {
                    throw new Exception($sendResult['msg'], 3);
                }
                $this->success('操作成功', PUBLIC_URL . 'postsuccess.html');
            } catch (Exception $e)
            {
                // throw $e;
                if(!in_array($e->getCode(), [1,2,3]))
                {
                    Log::error( 'excel批量发送短信出错，错误信息:' . print_r($e->getMessage(), true) );
                }
                $this->error($e->getMessage());
            }
        }


        //获取模版列表
        $templates = $obj->sms_template_list($this->idsite);

        $this->assign('sm_info', $sm_info);
        $this->assign('templates', $templates['data']);
        return $this->fetch();

        $obj = new \app\admin\module\Sitemanege;
        $this->fetch();
    }



    /**
     * 下载承诺函模版
     * @author Hlt
     * @DateTime 2019-04-28T18:10:13+0800
     * @return   void
     */
    public function download_commitment_template()
    {
        $zhName = '承诺函模版.docx';
        $file = ROOT_PATH . 'public' . DS .'uploads' . DS . 'commitment_template.docx';
        try
        {
            $common = new Common;
            $common->downloadFile($file, $zhName);
        } catch (Exception $e)
        {
            $this->error($e->getMessage());
        }
    }

    public function download_text_excel_template()
    {
        $zhName = '数据模板.xlsx';
        $file = ROOT_PATH . 'public' . DS .'uploads' . DS . 'data_template.xlsx';
        try
        {
            $common = new Common;
            $common->downloadFile($file, $zhName);
        } catch (Exception $e)
        {
            $this->error($e->getMessage());
        }
    }
}