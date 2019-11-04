<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/5
 * Time: 11:05
 */

namespace app\admin\controller;
use PHPExcel;
use PHPExcel_IOFactory;
use think\Request;
use think\Exception;

class Member extends Base{

    //会员列表
    public function index(){
        if($this->CMS->CheckPurview('membermanage') == false){
            $this->NoPurview();
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member;
        if($this->CMS->CheckPurview('membermanage','manage') == false){
            $request['accountid']=session('AccountID');
        }

        $result = $obj->index($request);
        $member_list = $result['member_list'];
        //dump($member_list);
        $statistical = $result['statistical'];
        $page = $result['page'];
        $search = $result['search'];

        $obj1 = new \app\admin\module\activity(session('idsite'));
        $hyfl=$obj1->getDic("hyfl");
        $_account=$obj1->getUser();
        $account=[];
        if($_account)
        {
            foreach ($_account as $k=>$vo)
            {
                $account[$vo['idaccount']]=$vo['chrname'];
            }
        }
        $usertype=[];
        foreach ($hyfl as $vo)
        {
            $usertype[$vo['code']]=$vo['name'];
        }



        $this->assign('usertype',$usertype);
        $this->assign('hyfl',$hyfl);
        $this->assign('account',$account);
        $this->assign('member_list',$member_list);
        $this->assign('statistical',$statistical);
        $this->assign('search',$search);
        $this->assign('page',$page);
        $this->assign('idsite',$result['idsite']);
        $this->assign('is_cashed',checkedMarketingPackage($result['idsite'],'cashed'));
        return $this->fetch();
    }

    //查看用户信息
    public function deal(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('membermanage',$request['action'])==false){
            $this->NoPurview();
        }
        $userid=$request['idmember'];
        $obj = new \app\admin\module\Member;
        $result = $obj->member_deal($request);
        $s_order=$obj->statistical_order($userid);
        $s_collection=$obj->statistical_collection($userid);
        $s_comment=$obj->statistical_comment($userid);
        $s_login=$obj->login_log($userid);
        $s_visit=$obj->visit($userid);
        //获取用户的现金券信息
        $cashed_obj = new \app\admin\module\Cashed(session('idsite'));
        $cashed_count = $cashed_obj->get_user_receive_cashed($userid);

        $result['memberinfo']['childage1']=$this->formattime($result['memberinfo']['childage1']);
        $result['memberinfo']['childage2']=$this->formattime($result['memberinfo']['childage2']);
        $result['memberinfo']['childage3']=$this->formattime($result['memberinfo']['childage3']);

        $obj1 = new \app\admin\module\activity(session('idsite'));
        $_account=$obj1->getUser();
        $account=[];
        if($_account)
        {
            foreach ($_account as $k=>$vo)
            {
                $account[$vo['idaccount']]=$vo['chrname'];
            }
        }

        $hyfl=[];
        $_hyfl=$obj1->getDic("hyfl");
        if($_hyfl)
        {
            foreach ($_hyfl as $k=>$vo)
            {
                $hyfl[$vo['code']]=$vo['name'];
            }
        }

        $this->assign('hyfl',$hyfl);
        $this->assign('account',$account);

        $this->assign('memberinfo',$result['memberinfo']);
        $this->assign('s_order',$s_order);
        $this->assign('s_collection',$s_collection);
        $this->assign('s_comment',$s_comment);
        $this->assign('s_login',$s_login);
        $this->assign('s_visit',$s_visit);
        $this->assign('action',$request['action']);
        $this->assign('userid',$userid);
        $this->assign('nodename',$result['nodename']);
        $this->assign('idsite',session('idsite'));
        $this->assign('is_cashed',checkedMarketingPackage(session('idsite'),'cashed'));
        $this->assign('cashed_count',$cashed_count);
        return $this->fetch();
    }

    //查看活动信息
    public function order()
    {
        $request = Request::instance()->param();
        $intflag=isset($request['state'])?$request['state']:'0';
        $userid=isset($request['userid'])?$request['userid']:'0';

        if(is_numeric($intflag)==false || $intflag<1 || is_numeric($userid)==false || $userid<1) {
            exit("非法操作！");
        }
        $obj = new \app\admin\module\Member;
        $arr=$obj->order($request);

        $data = $arr['data'];
        $page = $arr['pager'];

        $this->assign('page',$page);
        $this->assign('data',$data);
        $this->assign('intflag',$intflag);
        $this->assign('order_state',config('order_state'));
        $this->assign('order_state_color',config('order_state_color'));
        return $this->fetch();
    }

    /**
     * 查看现金券领取记录
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function receive_record(){

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Cashed(session('idsite'));
        $arr = $obj->receive_record($request);

        $data = $arr['data'];
        $page = $arr['pager'];

        $this->assign('search',$arr['search']);
        $this->assign('page',$page);
        $this->assign('data',$data);
        $this->assign('sitecode',getSiteCode(session('idsite')));
        return $this->fetch();
    }

    //访谈记录
    public function followup()
    {
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('membermanage','manage') == false){
            $request['userid']=session('AccountID');
        }
        $obj = new \app\admin\module\Member;
        $arr=$obj->followup($request);

        $onlyMember = isset($request["onlyMember"])?1:0;
        $memberid = isset($request["memberid"])?(int)$request["memberid"]:0;

        $data = $arr['data'];
        $page = $arr['pager'];
        $search = $arr['search'];

        $obj1 = new \app\admin\module\activity(session('idsite'));
        $_account=$obj1->getUser();
        $account=[];
        if($_account)
        {
            foreach ($_account as $k=>$vo)
            {
                $account[$vo['idaccount']]=$vo['chrname'];
            }
        }
        $this->assign('account',$account);


        $this->assign('search',$search);
        $this->assign('page',$page);
        $this->assign('data',$data);
        $this->assign('onlyMember',$onlyMember);
        $this->assign('memberid',$memberid);
        return $this->fetch();
    }
    //新建访谈记录
    public function followupdeal(){
        $request = Request::instance()->param();

        $obj = new \app\admin\module\Member;

        if(empty($request["memberid"]))
        {
            $this->error("用户不存在");
            exit();
        }
        $memberinfo = db('member')->where(array('idmember'=>$request["memberid"]))->find();
        if($memberinfo)
        {
            $request["membername"]=$memberinfo['nickname'];
        }
        else
        {
            $this->error("用户不存在");
            exit();
        }

        $request['userid']=session('AccountID');
        $request['username']=session('UserName');
        if(Request::instance()->isPost())
        {
            if($obj->followuppost($request))
                $this->success('增加成功',PUBLIC_URL.'postsuccess.html');
        }
        $this->assign('datainfo',$request);
        return $this->fetch();
    }

    //评论
    public function comment(){
        $request = Request::instance()->param();
        $show=isset($request['show'])?$request['show']:'0';
        $userid=isset($request['userid'])?$request['userid']:'0';
        $flag=isset($request['flag'])?$request['flag']:'0';
        if(is_numeric($show)==false || is_numeric($userid)==false || $userid<1|| is_numeric($flag)==false || $flag<1) {
            exit("非法操作！");
        }
        $obj = new \app\admin\module\Member;
        $arr=$obj->comment($request);

        $data = $arr['data'];
        $page = $arr['pager'];

        $this->assign('page',$page);
        $this->assign('comment_list',$data);
        return $this->fetch();
    }

    //收藏
    public function collection(){
        $request = Request::instance()->param();
        $userid=isset($request['userid'])?$request['userid']:'0';
        $flag=isset($request['flag'])?$request['flag']:'0';
        if(is_numeric($userid)==false || $userid<1|| is_numeric($flag)==false || $flag<1) {
            exit("非法操作！");
        }
        $obj = new \app\admin\module\Member;
        $arr=$obj->collection($request);

        $data = $arr['data'];
        $page = $arr['pager'];

        $sitecode=getSiteCode(session('idsite'));
        $this->assign('sitecode',$sitecode);
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    //格式化时间
    private function formattime($t)
    {
        $tmp=0;
        if(empty($t) || $t<100)
            return "";

        $t=strtotime($t);
        $Y1=date("Y",$t);
        $M1=date("m",$t);
        $D1=date("d",$t);

        $Y2=date("Y");
        $M2=date("m");
        $D2=date("d");

        if($Y2==$Y1 || $Y2<$Y1)
            return "0 岁 【".date("Y-m-d",$t)."】";

        if($Y2>$Y1)
            $tmp=$Y2-$Y1;

        if($M1>$M2)
        {
            $tmp=$tmp-1;
        }
        else if($M1==$M2 && $D1>$D2)
        {
            $tmp=$tmp-1;
        }

        return $tmp." 岁 【".date("Y-m-d",$t)."】";

    }

    //用户添加，修改，查看 跳转页面
    public function memberdeal(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('membermanage',$request['action'])==false){
            $this->NoPurview();
        }

        $obj = new \app\admin\module\Member;
        $result = $obj->member_deal($request);

        $obj1 = new \app\admin\module\activity(session('idsite'));
        $hyfl=$obj1->getDic("hyfl");
        $account=$obj1->getUser();

        $assign_business = isset($request["assign_business"])?1:0;

        $this->assign('hyfl',$hyfl);
        $this->assign('assign_business',$assign_business);
        $this->assign('account',$account);
        $this->assign('memberinfo',$result['memberinfo']);
        $this->assign('action',$request['action']);
        $this->assign('nodename',$result['nodename']);
        $this->assign('type',$result['type']);
        $this->assign('memberid',$result['memberid']);
        $this->assign('idsite',session('idsite'));
        $this->assign('is_distribution',checkedMarketingPackage(session('idsite'),'distribution'));//是否具有分销功能营销包
        return $this->fetch();
    }

    //用户添加，修改，查看提交地址
    public function memberpost(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('membermanage',$request['action'])==false){
            $this->NoPurview();
        }
        $obj = new \app\admin\module\Member;
        $result = $obj->member_post($request);
        if($result['bool']){
            $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
        }else{
            $this->error('操作失败');
        }
    }

    //用户删除
    public function memberdel(){
        if($this->CMS->CheckPurview('membermanage','del')==false){
            $this->NoPurview();
        }
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Member();
        $bool = $obj->member_del($data);
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    //选中删除
    public function delchecked(){
        if($this->CMS->CheckPurview('membermanage','del')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $pattern_obj = new \app\admin\module\Member();
        $bool = $pattern_obj->del_checked($request);
        if($bool){
            return 1;
        }else{
            return 0;
        }
    }

    //检测是否惟一
    public function membertest(){
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member();
        $count = $obj->member_test($request);
        if($count > 0){ //有重复
            return 2;
        }else{
            return 1;
        }
    }

    //设为管理员
    public function ismanage()
    {
        if($this->CMS->CheckPurview('membermanage','modi')==false){
            return 0;
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member();
        $bool= $obj->ismanage($request);
        if($bool){
            return 1;
        }else{
            return 2;
        }

    }

    //发送消息
    public function sendmsg(){
        if($this->CMS->CheckPurview('membermanage')==false){
            $this->error('无权限');
        }
        $data = Request::instance()->param();

        $obj = new \app\admin\module\activity(session('idsite'));
        if (Request::instance()->isPost()) {

            if(!isset($data["touser"]) || empty($data["touser"])){
                exit(json_encode(array("state" => 0, "key" => "", "msg" => "获取用户openid失败")));
            }

            $key=getNumber();
            $data['key']=$key;
            $data['title']="";
            $data['inttype']=2;
            $data['inttype1']=1;
            $data['username']=session("UserName");
            $data['userid']=session("AccountID");
            $data['dataid'] = 0;
            $result = $obj->sendmsg($data);
            if ($result)
            {
                $result1=send_msg($key,getSiteCode(session('idsite')));
                exit(json_encode($result1));
            }
            else
                exit(json_encode(array("state" => 0, "key" => "", "msg" => "数据更新失败")));
        }

        $chrurl= "";
        $chrname="";
        $activitytime="";

        $this->assign('sitecode',getSiteCode(session('idsite')));
        $this->assign('chrurl',$chrurl);
        $this->assign('chrname',$chrname);
        $this->assign('activitytime',$activitytime);
        return $this->fetch();
    }

    //发送短信
    public function send_sms_msg()
    {
        if($this->CMS->CheckPurview('membermanage')==false){
            $this->NoPurview();
        }
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Sitemanege;
        if (Request::instance()->isPost()) {
            if (isset($data["mobile"])) {
                $data["mobile"] = implode(",", $data["mobile"]);
            }
            $msg = $obj->sms_batch_send($data);
            if ($msg =="success") {
                exit(json_encode(array("state" => 1)));
            } else {
                exit(json_encode(array("state" => 0,  "msg" => $msg)));
            }
        }


        $idsite =  session('idsite');
        if (!isset($idsite) || empty($idsite)) {
            die("请先登录后再发送短信");
        }
        $sm_info = db('site_manage')->where('id=' . $idsite)->find();
        if ($sm_info["sms_status"] != 2) {
            die("请先申请开通短信");
        }
        if ($sm_info["sms_num"] <= 0) {
            die("短信余额不足，请先充值后，再发送短信");
        }
        $this->assign('sm_info', $sm_info);
        return $this->fetch();
    }

    // 赠送积分
    public function membergiving(){
        if ($this->CMS->CheckPurview('membermanage','giving') == false) {
            $this->error('无权限');
        }

        $param = input('param.');

        $member = new \app\admin\module\Member();
        if(request()->isPost()){

            if($param['integral'] <= 0){
                $this->error('操作失败, 赠送积分不能小于0');
            }

            $result = $member->membergiving($param);
            if($result){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error('操作失败');
            }
        }

        $memberinfo = $member->get_member($param['idmember']);

        $this->assign('memberinfo',$memberinfo);
        return $this->fetch();
    }

    // 赠送现金券
    public function give_cashed(){
        $idsite = session('idsite');
        if(!checkedMarketingPackage($idsite,'cashed')){
            die('没有权限,请先订购营销包！');
        }
        if ($this->CMS->CheckPurview('membermanage','give_cashed') == false) {
            $this->error('无权限');
        }

        $param = input('param.');
        $member = new \app\admin\module\Member();
        if(request()->isPost()){

            $cashed_obj = new \app\admin\module\Cashed($idsite);
            $result = $cashed_obj->give_cashed($param);
            if($result){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error('操作失败');
            }
        }

        $memberinfo = $member->get_member($param['idmember']);

        $this->assign('memberinfo',$memberinfo);
        return $this->fetch();
    }

    /**
     * 分销用户管理
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function spokesman_list(){
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Distribution(session('idsite'));
        $arr = $obj->spokesman_list($request);
        $data = $arr['data'];
        $page = $arr['pager'];

        $this->assign('search',$arr['search']);
        $this->assign('page',$page);
        $this->assign('data',$data);
        $this->assign('u_chrname',$arr['u_chrname']);
        $this->assign('sitecode',getSiteCode(session('idsite')));
        return $this->fetch();
    }
    /**
     * 历史会员
     *
     * @return void
     * @author Chenjie
     * @Date 2019-08-02 10:03:34
     */
    public function history_member(){
        if ($this->CMS->CheckPurview('historymember') == false) {
            $this->error('无权限');
        }
        $param = input('param.');
        $param['p'] = isset($param['p']) ? intval($param['p']) : 1;
        $status = isset($param['status']) ? intval($param['status']) : 1;
        $param['status'] = $status;
        $member = new \app\admin\module\Member();
        $result = $member->get_history_member($param);

        $this->assign('status',$status);
        $this->assign('page',$result['page']);
        $this->assign('datalist',$result['datalist']);
        return $this->fetch();
    }
    /**
     * 重新匹配
     *
     * @return void
     * @author Chenjie
     * @Date 2019-08-02 14:09:22
     */
    public function again_matching(){
        $id = input('param.id');
        if($id == 0){
            if ($this->CMS->CheckPurview('historymember','allagainmatching') == false) {
                $this->error('无权限');
            }
        }else{
            if ($this->CMS->CheckPurview('historymember','againmatching') == false) {
                $this->error('无权限');
            }
        }
        $member = new \app\admin\module\Member();
        $result = $member->again_matching($id);
        return $result;
    }
    /**
     * 发送短信
     *
     * @return 渲染页面
     * @author Chenjie
     * @Date 2019-08-02 16:25:20
     */
    public function send_message()
    {
        if ($this->CMS->CheckPurview('historymember','sendmessage') == false) {
            $this->error('无权限');
        }
        $id = Request::instance()->param('id');
        $idsite = session('idsite');

        $obj = new \app\admin\module\Sitemanege;
        $sm_info = $obj->checkSmsNum($idsite);

        $member = new \app\admin\module\Member();
        $history_member_list = $member->get_history_member_by_id($id);
        $mobile_list = '';
        if($history_member_list){
            if(isset($history_member_list['mobile'])){
                $mobile_list = $history_member_list['mobile'];
            }else{
                foreach($history_member_list as $history_member){
                    $mobile_list .= $history_member['mobile'].",";
                }
                $mobile_list = rtrim($mobile_list, ",");
            }
        }

        if (Request::instance()->isPost())
        {
            try
            {
                //解析上传的excel文件并生成短信发送计划
                $member = new \app\admin\module\Member();
                $sendResult = $member->send_message();
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
        $templates = $obj->sms_template_list($idsite);

        $this->assign('sm_info', $sm_info);
        $this->assign('mobile_list', $mobile_list);
        $this->assign('templates', $templates['data']);
        return $this->fetch();
    }
    /**
     * 导入会员
     *
     * @return void
     * @author Chenjie
     * @Date 2019-08-02 10:25:37
     */
    public function import_member(){
        if ($this->CMS->CheckPurview('historymember','importmember') == false) {
            $this->error('无权限');
        }
        if(request()->isPost()){
            $file = request()->file('file1');
            if($file){
                $filepath = $file->getInfo('filename')['tmp_name'];
                $history_member_list = $this->import_excel($filepath);
                $data = [];
                foreach($history_member_list as $key => $history_member){
                    if($key != 0){
                        $real_name = isset($history_member[0]) ? $history_member[0] : '';
                        $mobile = isset($history_member[1]) ? $history_member[1] : '';
                        $idcart = isset($history_member[3]) ? $history_member[3] : '';

                        if($real_name && $mobile){
                            if(!check_mobile($mobile)){
                                $this->error('第'.$key.'条数据中的手机号码格式不对');
                                exit;
                            }
                            if(!check_idcard($idcart)){
                                $this->error('第'.$key.'条数据中的身份证格式不对');
                                exit;
                            }
                            $tmp_history_member = db('history_member')->where(['siteid'=> session('idsite'), 'real_name' => $real_name, 'mobile'=>$mobile])->find();
                            if(!$tmp_history_member){
                                $member = db('member')->where('chrtel',$mobile)->find();
                                $account = db('account')->where('mobile', $mobile)->find();
                                $data[] = [
                                    'siteid' => session('idsite'),
                                    'real_name' => $real_name,
                                    'mobile' => $mobile,
                                    'user_type' => isset($history_member[2]) ? $history_member[2] : '',
                                    'id_card' => isset($history_member[3]) ? $history_member[3] : '',
                                    'status' => $member || $account ? 1 : 0,
                                    'create_time' => time(),
                                    'source' => isset($history_member[4]) ? $history_member[4] : '',
                                ];
                            }
                        }else{
                            if(!$real_name){
                                $filed = '姓名';
                            }else{
                                $filed = '手机号';
                            }
                            $this->error('第'.$key.'条数据中的'.$filed.'不能为空');
                            exit;
                        }
                    }
                }
                if(count($data) > 0){
                    db('history_member')->insertAll($data);
                }
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error('文件上传出错，请重新上传文件');
                exit;
            }
        }
        return $this->fetch();
    }
    /**
     * 下载会员
     *
     * @return void
     * @author Chenjie
     * @Date 2019-08-02 10:27:47
     */
    public function down_template(){
		if ($this->CMS->CheckPurview('contentmanage', 'add') == false) {
			$this->error('无权限');
		}

		//import('phpexcel.PHPExcel', EXTEND_PATH);
		require_once dirname(__FILE__) . '/../../../extend/PHPExcel/PHPExcel.php';
		$objPHPExcel = new PHPExcel();

		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Europe/London');

		if (PHP_SAPI == 'cli') {
			die('This example should only be run from a Web Browser');
		}

		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
			->setLastModifiedBy("Maarten Balliauw")
			->setTitle("Office 2007 XLSX Test Document")
			->setSubject("Office 2007 XLSX Test Document")
			->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
			->setKeywords("office 2007 openxml php")
			->setCategory("Test result file");

		$objc = $objPHPExcel->setActiveSheetIndex(0);
		$objc->setCellValue('A1', '姓名(必填)');
		$objc->setCellValue('B1', '手机号(必填)');
		$objc->setCellValue('C1', '用户类型');
		$objc->setCellValue('D1', '身份证号');
		$objc->setCellValue('E1', '来源');

		$objc->getColumnDimension('A')->setWidth(12);
		$objc->getColumnDimension('B')->setWidth(14);
		$objc->getColumnDimension('C')->setWidth(12);
		$objc->getColumnDimension('D')->setWidth(20);
		$objc->getColumnDimension('E')->setWidth(12);

		$objPHPExcel->getActiveSheet()->setTitle('template');
		$objPHPExcel->setActiveSheetIndex(0);

		ob_clean();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . urlencode("template.xlsx") . '"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
    }
    /**
     * 导入execl
     *
     * @param [type] $filename
     * @return void
     * @author Chenjie
     * @Date 2019-08-02 11:03:54
     */
	function import_excel($filename) {
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
		date_default_timezone_set('Europe/London');
		require_once dirname(__FILE__) . '/../../../extend/PHPExcel/PHPExcel/IOFactory.php';
		if (!file_exists($filename)) {
			return false;
		}
		$objPHPExcel = PHPExcel_IOFactory::load($filename);
		$arr = $objPHPExcel->getsheet(0)->toArray();
		return ($arr);

	}
}