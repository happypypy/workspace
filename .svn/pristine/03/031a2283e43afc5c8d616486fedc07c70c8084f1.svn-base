<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/2/26
 * Time: 10:16
 */

namespace app\admin\module;

use think\Exception;
use think\Model;
use think\Page;
use think\Db;
class Sitemanege extends Model{

    public function index($data=array()){
        //搜索
        $search = array(
            "sms_status"=>(isset($data["sms_status"])?$data["sms_status"]:""),
            "site_name"=>(isset($data["site_name"])?$data["site_name"]:""));
        $result['search'] = $search;

        $whereArr = array();
        if(isset($data["sms_status"]) && is_numeric($data["sms_status"])) {
            $whereArr["sms_status"] = (int)$data["sms_status"];
        }
        if(isset($data["site_name"])) {
            $whereArr["site_name"] = array('like',"%".trim($data["site_name"])."%");
        }

        $count = db('site_manage')->where($whereArr)->count();
        $page = new Page($count,PAGE_SIZE);
        $site_list = db('site_manage')->where($whereArr)->order('id')->limit($page->firstRow.','.$page->pageSize)->select();
        $result['page'] = $page;
        $result['data'] = $site_list;
        return $result;
    }

    //站点添加，编辑跳转地址
    public function site_deal($data){
        if($data['action'] == 'add'){
            $site_info['site_code'] = '';
            $site_info['site_name'] = '';
            $site_info['order'] = '';
            $site_info['realm_name'] = '';
            $site_info['appid'] = '';
            $site_info['appsecret'] = '';
            $site_info['token'] = '';
            $site_info['encodingaeskey'] = '';
            $site_info['site_type'] = 2;
            $site_info['is_use'] = 2;
            $site_info['remark'] = '';
            $site_info['paykey'] = '';
            $site_info['cainfo'] = '';
            $site_info['sslcertpath'] = '';
            $site_info['sslkeypath'] = '';
            $site_info['expiretime'] = '';
            $site_info['mchid'] = '';
            $site_info['sms_sign'] = '';
            $site_info['verifyfilepath'] = '';
            $site_info['payment_verification'] = '';

        }else{
            $site_info = db('site_manage')->where('id='.$data['id'])->find();
            if($site_info)
            {
                if(!empty($site_info['expiretime']) && $site_info['expiretime'] >10000)
                    $site_info['expiretime'] =date("Y-m-d",$site_info['expiretime']);
                else
                    $site_info['expiretime']='';
            }
        }

        $site_info['action'] = $data['action'];
        return $site_info;
    }

    //站点提交地址
    public function site_post($data){
        isset($data['is_use'])?$new_data['is_use'] = $data['is_use']:$new_data['is_use'] = 2;
        $new_data['site_code'] = $data['site_code'];
        $new_data['site_name'] = $data['site_name'];
        $new_data['order'] = $data['order'];
        $new_data['realm_name'] = $data['realm_name'];
        $new_data['appid'] = $data['appid'];
        $new_data['appsecret'] = $data['appsecret'];
        $new_data['token'] = $data['token'];
        $new_data['encodingaeskey'] = $data['encodingaeskey'];
        $new_data['site_type'] = $data['site_type'];
        $new_data['mchid'] = $data['mchid'];
        $new_data['remark'] = $data['remark'];
        $new_data['paykey'] = $data['paykey'];
        $new_data['cainfo'] = $data['cainfo'];
        $new_data['sslcertpath']= $data['sslcertpath'];
        $new_data['sslkeypath']= $data['sslkeypath'];
        $new_data['expiretime']=empty($data['expiretime'])?'':strtotime($data['expiretime']);
        $new_data['remindflag']= 1;
        $new_data['sms_sign'] = $data['sms_sign'];
        $new_data['verifyfilepath'] = isset($data["verifyfilepath"])?$data["verifyfilepath"]:"";

        $tmp = [$data['appid'], $data['appsecret'], $data['mchid'], $data['paykey']];
        if( md5(implode(',', $tmp)) != $data['key'] )
        {
            $new_data['payment_verification'] = 0;
        }

        if($data['action'] == 'add'){
            //判断站点代号的唯一性
            if(db('site_manage')->where(['site_code' => $new_data['site_code']])->find())
            {
                return ['status' => 'fail', 'msg' => '站点代号重复'];
            }

            $bool = db('site_manage')->insert($new_data);
            if($bool)
            {
                $id=Db::getLastInsID();
                $this->site_ini($id,'童享云');
                $row=db('account')->where(array('chraccount'=>'admin','siteid'=>$id))->find();
                if($row)
                {
                    $arr=[];
                    $arr['fidaccount']=$row['idaccount'];
                    $arr['fidrole']='5';
                    db('account_role')->insert($arr);
                }
            }
        }else{
            //保证站点代号唯一
            $map['site_code'] = $data['site_code'];
            $map['id'] = ['neq',$data['id']];
            if(db('site_manage')->where($map)->find())
                return ['status' => 'fail', 'msg' => '站点代号重复'];

            db('site_manage')->where(['id' => $data['id']])->update($new_data);
        }

        $config=cache("WeiXinConfig");
        if(isset($config) && !empty($config) && isset($config[$data['site_code']])){
            unset($config[$data['site_code']]);
            cache("WeiXinConfig",$config);
        }

        return ['status' => 'success', 'msg' => '添加成功'];
    }
    public function site_ini($id,$sitename='')
    {
        if(empty($id))
        {
            return;
        }
        $txt=file_get_contents('site_ini.sql');
        $txt=str_replace('{siteid}',$id,$txt);
        $txt=str_replace('{sitename}',$sitename,$txt);
        $_arr = explode(");", $txt);
        db('node')->where(array('idsite'=>$id))->delete();
        db('config_rule')->where(array('idsite'=>$id))->delete();
        foreach ($_arr as $_value) {
            if(trim($_value)!="")
            {
                //echo $_value."<br>";
                Db::execute($_value.");");

            }
        }
    }

    /**
     * @param $data
     * @return bool|int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function sms_post($data)
    {
        if (empty($data)) {
            return false;
        }

        //必填项不能为空，为空，返回false
        if (!isset($data["sms_business_license"]) || empty($data["sms_business_license"])) {
            return false;
        }
        if (!isset($data["sms_sign"]) || empty($data["sms_sign"])) {
            return false;
        }

        //要保存的字段列表，如果字段在里面，保存到要提交的数据数组中。
        $field_list = array("sms_business_license", "sms_sign", "sms_mark", "sms_status","sms_contact_name","sms_contact_telephone","sms_contact_position","sms_company_name","sms_agreement_path");

        $post_data = array();
        foreach ($data as $key => $value) {
            if (in_array($key, $field_list)) {
                $post_data[$key] = trim($value);
            }
        }
        $idsite = session('idsite');
        $idsite = isset($idsite) ? $idsite : 0;
        if ((int)$idsite <= 0 || (int)$idsite != $idsite) {
            return false;
        }

        $post_data["sms_status"] = 1;
        $post_data["sms_apply_time"] = date("Y-m-d H:i:s");
        $post_data["sms_apply_comment"] = "";


        $bool = db('site_manage')->where('id=' . $idsite)->update($post_data);
        return $bool;
    }

    public  function  sms_post_open_config($data)
    {
        if (empty($data)) {
            return false;
        }

        $arr=array();
        $arr[]=empty($data["s0000"])?"0":$data["s0000"];
        for($i=0;$i<20;$i++)
        {
            $tmp="";
            for($j=0;$j<4;$j++)
            {
                $Key=1000+($i*100)+$j+1;
                $tmp=$tmp.(empty($data["s".$Key])?"0":$data["s".$Key]);
            }
            $arr[]=$tmp;
        }

        $tmpValue=implode(",",$arr);
        $idsite = session('idsite');
        if(db('sms_open_config')->where('idsite=' . $idsite)->count())
        {
            db('sms_open_config')->where('idsite=' . $idsite)->update(array('config'=>$tmpValue));
        }
        else
        {
            db('sms_open_config')->insert(array('config'=>$tmpValue,'idsite'=>$idsite));
        }
        return true;
    }

    public  function  sms_get_open_config()
    {
        $idsite = session('idsite');
        $result= db('sms_open_config')->where(['idsite' => $idsite])->find();
        $tmpValue="0,0000,0000,0000,0000,0000,0000,0000,0000,0000,0000,0000,0000,0000,0000,0000,0000,0000,0000,0000,0000";
        if($result)
        {
            $tmpValue=$result['config'];
        }

        $arr=explode(',',$tmpValue);
        $i_l=count($arr);
        $data=[];
        $data['0000']=$arr[0];
        for($i=1;$i<21;$i++)
        {
            $tmp_value='0000';
            if($i<$i_l)
            {
                $tmp_value=$arr[$i];
            }
            $value_arr=str_split($tmp_value);
            $v_l=count($value_arr);
            for($j=0;$j<4;$j++)
            {
                $value=0;
                if($j<$v_l)
                {
                    $value=$value_arr[$j];
                }
                $Key=1000+(($i-1)*100)+$j+1;
                $data[$Key]=$value;
            }
        }
        return $data;
    }


    /**
     * 短信审核
     * @param $data
     * @return bool|int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function sms_review($data){
        if (empty($data)) {
            return false;
        }
        //必填项不能为空，为空，返回false
        if (!isset($data["sms_status"]) || empty($data["sms_status"])) {
            return false;
        }

        $post_data = array();
        $post_data["sms_status"] = (int)$data["sms_status"];
        $post_data["sms_apply_comment"] = isset($data["sms_apply_comment"])?$data["sms_apply_comment"]:"";
        if((int)$data["sms_status"]==2){
            $post_data["sms_enable"] = 1;
        }else{
            $post_data["sms_enable"] = 0;
        }
        $post_data["sms_review_time"] = date("Y-m-d H:i:s");
        $accountID = session("AccountID");
        $post_data["sms_review_idaccount"] = $accountID;
        $idsite = $data["id"];
        $idsite = isset($idsite) ? $idsite : 0;
        if ((int)$idsite <= 0 || (int)$idsite != $idsite) {
            return false;
        }
        $bool = db('site_manage')->where('id=' . $idsite)->update($post_data);
        return $bool;
    }


    /**
     * 短信充值列表
     */
    public function sms_recharge_list($data)
    {
        $result = array();
        try {
            //搜索
            $search = array(
                "status" => (isset($data["status"]) ? $data["status"] : ""),
//                "site_name" => (isset($data["site_name"]) ? $data["site_name"] : "")
            );
            $result['search'] = $search;
            $idsite = session('idsite');
            $idsite = isset($idsite) ? $idsite : 0;
            $whereArr = array();
            $whereArr["idsite"] = $idsite;

            if(isset($data["status"]) && is_numeric($data["status"])) {
                $whereArr["status"] = (int)$data["status"];
            }

            $count = db('sms_order')->where($whereArr)->count();
            $page = new Page($count, PAGE_SIZE);
            $list = db('sms_order')->where($whereArr)->order('sms_order_id desc')->limit($page->firstRow . ',' . $page->pageSize)->select();
            foreach ($list as &$value){
                $str_status = "";
                switch ($value["status"]){
                    case 0:
                        $str_status = "未支付";
                        break;
                    case 1:
                        $str_status = "已支付";
                        break;
                }
                $value["str_status"] = $str_status;
            }
            $result['page'] = $page;
            $result['data'] = $list;
        } catch (Exception $ex) {
            //处理异常
        }
        return $result;
    }

    /**
     * 短信充值
     * @param $data      //充值数据
     * @return bool
     */
    public function sms_recharge($data)
    {
        try {
            if (empty($data)) {
                return false;
            }

            if (!isset($data["id"]) || (int)$data["id"] <= 0) {
                return false;
            }

            $idsite = (int)$data["id"];

            if (!isset($data["sms_num"]) || $data["sms_num"] <= 0) {
                return false;
            }
            $filter_list = array("sms_num", "type", "recharge_price");
            $post_arr = array();
            foreach ($data as $key => $value) {
                if (in_array($key, $filter_list)) {
                    $post_arr[$key] = $value;
                }
            }
            if (empty($post_arr)) {
                return false;
            }
            $post_arr["idsite"] = $idsite;
            $post_arr["accountid"] = session("AccountID");
            $post_arr["create_time"] = date("Y-m-d H:i:s");
            $post_arr["ip"] = getip();
            //开启事务
            Db::startTrans();
            $rs = Db::name("sms_recharge_log")->insert($post_arr);
            if ($rs) {
                $site_manage_info = Db::name("site_manage")->where(array("id" => $idsite))->find();
                $update_arr = array();
                if($data["sms_num"]>0) {
                    $update_arr["sms_num"] = (int)$site_manage_info["sms_num"] + $data["sms_num"];
                    $update_arr["sms_total_num"] = (int)$site_manage_info["sms_total_num"] + $data["sms_num"];
                }
                if(isset($data["recharge_price"]) && $data["recharge_price"]>0) {
                    $update_arr["sms_recharge_total_money"] = round($site_manage_info["sms_recharge_total_money"], 2) + round($data["recharge_price"], 2);

                }
                $flag = Db::name("site_manage")->where(array("id" => $idsite))->update($update_arr);
                if(!$flag){
                    //更新失败回滚
                    Db::rollback();
                    return false;
                }
                Db::commit();
                return true;
            } else {
                Db::rollback();
                return false;
            }
        } catch (Exception $ex) {
//            print_r($ex);exit;
            return false;
        }
    }

    /**
     * 短信充值日志列表
     */
    public function sms_recharge_log_list($data)
    {
        $result = array("search"=>array(),"page"=>"","data"=>array());
        try {
            //搜索
            $search = array(
                "type" => (isset($data["type"]) ? $data["type"] : ""),
//                "site_name" => (isset($data["site_name"]) ? $data["site_name"] : "")
            );
            $result['search'] = $search;

            if (!isset($data["id"]) || (int)$data["id"] <= 0) {
                return $result;
            }

            $idsite = (int)$data["id"];

            $whereArr = array();
            $whereArr["idsite"] = $idsite;

            if(isset($data["type"]) && is_numeric($data["type"])) {
                $whereArr["type"] = (int)$data["type"];
            }

            $count = db('sms_recharge_log')->where($whereArr)->count();
            $page = new Page($count, PAGE_SIZE);
            $list = db('sms_recharge_log')->where($whereArr)->order('sms_recharge_log_id desc')->limit($page->firstRow . ',' . $page->pageSize)->select();

            $account_ids = array_column($list,"accountid");

            $account_array = db('account')->where(array("idaccount"=>array("in",$account_ids),"siteid"=>$idsite))->field("idaccount,chrname")->select();

            $format_account_array = array();

            foreach ($account_array as $account){
                $format_account_array[$account["idaccount"]] = $account["chrname"];
            }

            foreach ($list as &$value){
                $str_status = "";
                switch ($value["type"]){
                    case 1:
                        $str_status = "线上";
                        break;
                    case 2:
                        $str_status = "机构充值";
                        break;
                    case 3:
                        $str_status = "赠送";
                        break;
                }
                $value["str_type"] = $str_status;
                $value["str_account"] = isset($format_account_array[$value["accountid"]])?$format_account_array[$value["accountid"]]:"系统";
            }
            $result['page'] = $page;
            $result['data'] = $list;
        } catch (Exception $ex) {
            //处理异常
        }
        return $result;
    }

    
    /**
     * 短信发送日志列表
     */
    public function sms_send_log_list($data)
    {
        $result = array("search"=>array(),"page"=>"","data"=>array());
        try {
            //搜索
            $search = array(
                "status" => (isset($data["status"]) ? $data["status"] : ""),
                "content" => (isset($data["content"]) ? $data["content"] : ""),
                "mobile" => (isset($data["mobile"]) ? $data["mobile"] : ""),
                "send_begin_time" => (isset($data["send_begin_time"]) ? $data["send_begin_time"] : ""),
                "send_end_time" => (isset($data["send_end_time"]) ? $data["send_end_time"] : ""),
                "create_begin_time" => (isset($data["create_begin_time"]) ? $data["create_begin_time"] : ""),
                "create_end_time" => (isset($data["create_end_time"]) ? $data["create_end_time"] : "")
            );
            $result['search'] = $search;

            if (!isset($data["id"]) || (int)$data["id"] <= 0) {
                return $result;
            }

            $idsite = (int)$data["id"];

            $whereArr = array();
            $whereArr["idsite"] = $idsite;

            //状态
            if(isset($data["status"]) && is_numeric($data["status"])) {
                $whereArr["status"] = (int)$data["status"];
            }

            //内容
            if(isset($data["content"])) {
                $whereArr["content"] = array("like","%".trim($data["content"])."%");
            }

            //手机号码
            if(isset($data["mobile"])) {
                $whereArr["mobile"] = array("like","%".trim($data["mobile"])."%");
            }

            if (isset($data['send_begin_time']) && $data['send_begin_time'] != '' && isset($data['send_end_time']) && $data['send_end_time'] != '') {
                $whereArr['send_time'] = array(array('>', $data['send_begin_time']), array('<', $data['send_end_time'] . " 23:59:59"), "and");
            }

            if (isset($data['create_begin_time']) && $data['create_begin_time'] != '' && isset($data['create_end_time']) && $data['create_end_time'] != '') {
                $whereArr['create_time'] = array(array('>', $data['create_begin_time']), array('<', $data['create_end_time'] . " 23:59:59"), "and");
            }

            $count = db('sms_send_schedule')->where($whereArr)->count();
            $page = new Page($count, PAGE_SIZE);
            $list = db('sms_send_schedule')->where($whereArr)->order('sms_send_schedule_id desc')->limit($page->firstRow . ',' . $page->pageSize)->select();

            foreach ($list as &$value){
                $str_status = "";
                switch ($value["status"]){
                    case 0:
                        $str_status = "待发送";
                        break;
                    case 1:
                        $str_status = "发送成功";
                        break;
                    case 2:
                        $str_status = "发送失败";
                        break;
                }
                $value["str_status"] = $str_status;
            }
            $result['page'] = $page;
            $result['data'] = $list;
        } catch (Exception $ex) {
            //处理异常
        }
        return $result;
    }


    /**
     * 短信批量发送
     * @param $data
     * @return string
     */
    public function sms_batch_send($data)
    {
        try {
            //判断短信内容是否为空
            if (!isset($data["content"]) || empty($data["content"])) {
                return "短信内容不能为空！";
            }

            //判断手机号码是否为空
            if (!isset($data["mobile"]) || empty($data["mobile"])) {
                return "手机号码不能为空！";
            }

            $now = date('Y-m-d H:i:s');
            if(isset($data['send_time']))
            {
                $send_time = $data['send_time'] > $now ? $data['send_time'] : $now;
            }else
            {
                $send_time = $now;
            }

            $mobile = $data["mobile"];
            $mobile = str_replace("，", ",", $mobile);
            $mobileArray = explode(",", $mobile);

            $idsite = session('idsite');
            if(!isset($idsite) ||empty($idsite)){
                return "站点不存在！";
            }
            $rs = smsSendSchedule($idsite, $mobileArray, $data["content"], session("AccountID"), session("Username"), 1, $send_time);
            if ($rs["status"] != "success") {
                return $rs["msg"];
            }
        } catch (\Exception $ex) {
            return "abnormal";
        }
        return "success";
    }

    /**
     * 获取短信模版列表
     * @access public
     * @return array|null|false
     */
    public function sms_template_list($siteid)
    {
        $result = ["page" => "","data" => []];
        $db = db('sms_template');
        $where = ['siteid' => $siteid];
        $count = $db->where($where)->count();
        $result['page'] = $page = new Page($count, PAGE_SIZE);

        $result['data'] =  $db->where($where)->limit($page->firstRow, $page->pageSize)->select();
        return $result;
    }
    
    /**
     * 获取特定短信模版内容
     * @access public
     * @return array
     */
    public function get_sms_template($data, $siteid)
    {
        $column_info = [];
        if(array_key_exists('id',$data)){
            $column_info = db('sms_template')
                ->where([
                    'id' => $data['id'],
                    'siteid' => $siteid
                ])
                ->field([
                    'id',
                    'name',
                    'content'
                ])
                ->find();
        }

        if(!$column_info)
        {
            $column_info=[
                'name' => '',
                'content' => '',
            ];
        }

        return $column_info;
    }

    /**
     * 处理提交内容，新建或更新短信模版
     * @access public
     * @return bool
     */
    public function sms_template_post($request)
    {
        $action = $request['action'];
        unset($request['action']);
        //检查模版内容中是否有【和】。
        $tmp = $request['name'] . $request['content'];
        if(strpos($tmp, '【') !== false || strpos($tmp, '】') !== false)
        {
            return ['status' => 'fail', 'msg' => '模版标题和内容不能出现【】'];
        }

        if($action == 'create')
        {
            $request['create_time'] = time();
            if(db('sms_template')->data($request)->insert())
            {
                return ['status' => 'success', 'msg' => '操作成功'];
            }else
            {
                return ['status' => 'fail', 'msg' => '操作失败'];
            }
        }else
        {
            db('sms_template')->where(['id' => $request['id'], 'siteid' => $request['siteid']])->update($request);
            return ['status' => 'success', 'msg' => '操作成功'];
        }
    }

    /**
     * 删除特定短信模版
     * @access public
     * @return bool
     */
    public function sms_template_del($option)
    {
        return db('sms_template')->where($option)->delete();
    }


    /**
     * 获取系统短信模版列表
     * @access public
     * @return array|null|false
     */
    public function sms_sys_template_list()
    {
        $result = ["page" => "","data" => []];
        $db = db('sms_system_template');
        $count = $db->count();
        $result['page'] = $page = new Page($count, PAGE_SIZE);

        $result['data'] =  $db->limit($page->firstRow, $page->pageSize)->select();
        return $result;
    }
    
    /**
     * 获取特定系统短信模版内容
     * @access public
     * @return array
     */
    public function get_sms_sys_template($data)
    {
        $column_info = [];
        if(array_key_exists('id',$data)){
            $column_info = db('sms_system_template')
                ->where([
                    'id' => $data['id'],
                ])
                ->field([
                    'id',
                    'name',
                    'content',
                    'inttype',
                    'remark'
                ])
                ->find();
        }

        if(!$column_info)
        {
            $column_info=[
                'id' => '',
                'name' => '',
                'content' => '',
                'inttype' => '',
                'remark' => ''
            ];
        }

        return $column_info;
    }

    /**
     * 处理提交内容，新建或更新系统短信模版
     * @access public
     * @return bool
     */
    public function sms_sys_template_post($data)
    {
        $action = $data['action'];
        unset($data['action']);
        //检查模版内容中是否有【和】。
        $tmp = $data['name'] . $data['content'];
        if(strpos($tmp, '【') !== false || strpos($tmp, '】') !== false)
        {
            return ['status' => 'fail', 'msg' => '模版标题和内容不能出现【】'];
        }


        if($action == 'create')
        {
            $data['create_time'] = time();
            if(db('sms_system_template')->data($data)->insert())
            {
                return ['status' => 'success', 'msg' => '操作成功'];
            }else
            {
                return ['status' => 'fail', 'msg' => '操作失败'];
            }
        }else
        {
            db('sms_system_template')->where(['id' => $data['id']])->update($data);
            return ['status' => 'success', 'msg' => '操作成功'];
        }
    }

    /**
     * 删除特定系统短信模版
     * @access public
     * @return bool
     */
    public function sms_sys_template_del($option)
    {
        return db('sms_system_template')->where($option)->delete();
    }


    /**
     * 检查站点是否开通了短信发送功能，及短信剩余条数
     * @author Hlt
     * @DateTime 2019-04-28T14:30:13+0800
     * @param    integer                 $siteId  站点id
     * @return   void
     */
    public function checkSmsNum($siteId)
    {
        $sm_info = db('site_manage')->where('id=' . $siteId)->find();
        if ($sm_info["sms_status"] != 2) {
            die("请先申请开通短信");
        }

        if ($sm_info["sms_num"] <= 0) {
            die("短信余额不足，请先充值后，再发送短信");
        }

        return $sm_info;
    }
}