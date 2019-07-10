<?php
/**
 * 天络CMS
 * ============================================================================
 * 版权所有 2017-2027 深圳天络科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinasky.net
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: huangshixin
 * Date:2018/06/17 */

namespace app\admin\controller;
use PHPExcel;
use PHPExcel_IOFactory;
use think\Exception;
use think\Request;
use think\wx\Utils\HttpCurl;

class activity extends Basesite {
	//activity列表
	public function index() {
		if ($this->CMS->CheckPurview('contentmanage') == false) {
			$this->error('无权限');
		}
		$request = Request::instance()->param();
		$intflag = isset($request['intflag']) ? $request['intflag'] : '6';
		if (is_numeric($intflag) == false || $intflag < 1) {
			$intflag = 1;
		}

		$request['intflag'] = $intflag;
		$obj = new \app\admin\module\activity(session('idsite'));
		$objOrder = new \app\admin\module\Order();

		if (!isset($request['nodeid'])) {
			$this->error('参数错误');
		}
		//其中参数中具有节点id
		$arr = $obj->index($request);
		$data = $arr['data'];
		$page = $arr['pager'];

		$hdfl_arr = [];
		$hdfl = $obj->getDic("hdfl");
		foreach ($hdfl as $k => $vo) {
			$hdfl_arr[$vo['code']] = $vo['name'];
		}

		foreach ($data as $k => $vo) {
			$data[$k]['order_num'] = $objOrder->getOrderNum($vo['idactivity']);
			$data[$k]['typename'] = array_key_exists($vo['fidtype'], $hdfl_arr) ? $hdfl_arr[$vo['fidtype']] : "";
		}
		$hdbq = $obj->getDic("hdbq");
		$this->assign('hdbq', $hdbq);
		$this->assign('search', $arr['search']);
		$this->assign('hdfl', $hdfl);
		$this->assign('page', $page);
		$this->assign('data', $data);
		$this->assign('sitecode', getSiteCode(session('idsite')));
		$this->assign('intflag', $intflag);
		$this->assign('acount', $arr['acount']);
		//来自的节点
		$this->assign('nodeid', $request['nodeid']);
		return $this->fetch();
	}

	public function visitlist() {
		if ($this->CMS->CheckPurview('contentmanage') == false) {
			$this->error('无权限');
		}
		$search = [];
		$search['istel'] = 0;
		$search['isfollow'] = 0;
		$search['readn'] = '';
		$search['readl'] = '';
		$search['regionkey'] = '';

		$data = Request::instance()->param();
		$obj = new \app\admin\module\activity(session('idsite'));
		$result = $obj->visitlist($data);

		if (!empty($data['istel'])) {
			$search['istel'] = $data['istel'];
		}

		if (!empty($data['isfollow'])) {
			$search['isfollow'] = $data['isfollow'];
		}

		if (!empty($data['readn'])) {
			$search['readn'] = $data['readn'];
		}

		if (!empty($data['readl'])) {
			$search['readl'] = $data['readl'];
		}

		if (!empty($data['regionkey'])) {
			$search['regionkey'] = $data['regionkey'];
		}

		$_account = $obj->getUser();
		$account = [];
		if ($_account) {
			foreach ($_account as $k => $vo) {
				$account[$vo['idaccount']] = $vo['chrname'];
			}
		}

		$this->assign('search', $search);
		$this->assign('account', $account);
		$this->assign('data', $result);
		$this->assign('dataid', $data['dataid']);

		$this->assign('sitecode', getSiteCode(session('idsite')));
		return $this->fetch();
	}
	public function sendmsg() {
		if ($this->CMS->CheckPurview('contentmanage') == false) {
			$this->error('无权限');
		}
		$data = Request::instance()->param();

		$obj = new \app\admin\module\activity(session('idsite'));
		if (Request::instance()->isPost()) {
			$key = getNumber();
			$data['key'] = $key;
			$data['title'] = "";
			$data['inttype'] = 2;
			$data['inttype1'] = 1;
			$data['username'] = session("UserName");
			$data['userid'] = session("AccountID");

			$result = $obj->sendmsg($data);
			if ($result) {
				$result1 = send_msg($key, getSiteCode(session('idsite')));
				exit(json_encode($result1));
			} else {
				exit(json_encode(array("state" => 0, "key" => "", "msg" => "数据更新失败")));
			}

		}

		$info = $obj->deal(array("id" => $data["dataid"]));
		$chrurl = ROOTURL . "/" . getSiteCode(session('idsite')) . "/detail/" . $data["dataid"];
		$chrname = "";
		$activitytime = "";
		if ($info) {
			$chrname = $info["chrtitle"];
			$activitytime = $info["dtstart"] . "~" . $info["dtend"];
		}

		$this->assign('dataid', $data["dataid"]);
		$this->assign('sitecode', getSiteCode(session('idsite')));
		$this->assign('chrurl', $chrurl);
		$this->assign('chrname', $chrname);
		$this->assign('activitytime', $activitytime);
		return $this->fetch();
	}
	public function copydata() {
		$data = Request::instance()->param();
		$obj = new \app\admin\module\activity(session('idsite'));
		if ($obj->copydata($data['id'])) {
			echo 1;
		} else {
			echo 0;
		}
		exit();
	}

	public function activitycheck() {
		if ($this->CMS->CheckPurview('contentmanage', 'checkactivity') == false) {
			$this->error('无权限');
		}
		$data = Request::instance()->param();
		$obj = new \app\admin\module\activity(session('idsite'));
		if (Request::instance()->isPost()) {

			if ($this->CMS->CheckPurview('contentmanage', $data['action']) == false) {
				$this->error('无权限');
			}

			$data['checktime'] = time();
			$bool = $obj->PostData($data);
			if ($bool !== false) {
				$this->success('操作成功', PUBLIC_URL . 'postsuccess.html');
			} else {
				$this->error('操作失败');
			}
			exit();
		}

		$hdfl = $obj->getDic("hdfl");
		$this->assign('hdfl', $hdfl);
		$hdbq = $obj->getDic("hdbq");
		$this->assign('hdbq', $hdbq);
		$FromTemp = $obj->getFromTemp();
		$this->assign('fromtemp', $FromTemp);
		$this->assign('user', $obj->getUser());

		$datainfo = $obj->deal($data);
		$this->assign('datainfo', $datainfo);
		return $this->fetch();
	}

	public function refund() {
		if ($this->CMS->CheckPurview('contentmanage', 'refund') == false) {
			$this->error('无权限');
		}
		$data = Request::instance()->param();

		$obj = new \app\admin\module\activity(session('idsite'));
		$datainfo = $obj->signupmodi($data);
		$this->assign('datainfo', $datainfo);
		$this->assign('sitecode', getSiteCode(session('idsite')));
		$this->assign('order_state', config('order_state'));
		return $this->fetch();
	}

    //签到
    public function issign()
    {
        if($this->CMS->CheckPurview('contentmanage','issign')==false){
            $this->error('无权限');
        }
        $data = Request::instance()->param();
        $id=$data['id'];
        $obj =new \app\admin\module\activity(session('idsite'));
        if(Request::instance()->isPost())
        {
            if($obj->issign($data))
            {
                //获取订单信息
                $order = db('order')->where(array('id'=>$data['id'],'idsite'=>$this->idsite))->find();
                //替换信息
                $replace = [];
                //给客户和商务发短信通知    类型：10--签到
                sysSendMsg($this->idsite, 10, $order, $replace);
                $this->success('签到成功',PUBLIC_URL.'postsuccess.html');
            }
        }
        $this->assign('id',$id);
        return $this->fetch();
    }

	//activity添加，修改，查看跳转页面
	public function modi() {
		$data = Request::instance()->param();
		$obj = new \app\admin\module\activity(session('idsite'));
		$cashed_obj = new \app\admin\module\Cashed(session('idsite'));
		if (Request::instance()->isPost()) {
			if ($this->CMS->CheckPurview('contentmanage', $data['action']) == false) {
				$this->error('无权限');
			}
			if (isset($data["intflag"])) {
				switch ((int) $data["intflag"]) {
				case 2:
				case 3:
					if ($this->CMS->CheckPurview('contentmanage', "checkactivity") == false) {
						$this->error('无权限');
					}
					break;
				}
				if ((int) $data["intflag"] == 0) {
					unset($data["intflag"]);
				}
			}
//            var_dump($data);exit;
			$res = $obj->PostData($data);
			if ($res['status'] === 'success') {
			    //dump($data);
				$this->success('操作成功', PUBLIC_URL . 'postsuccess.html');
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
		//dump($datainfo);
		$this->assign('datainfo', $datainfo);
		$this->assign('idsite', session('idsite'));
        $this->assign('is_cashed',checkedMarketingPackage(session('idsite'),'cashed'));
		return $this->fetch();
	}
	//删除
	public function del() {
		if ($this->CMS->CheckPurview('contentmanage', 'del') == false) {
			$this->error('无权限');
		}

		$request = Request::instance()->param();
		$obj = new \app\admin\module\activity(session('idsite'));
		$bool = $obj->del($request);
		if ($bool) {
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}

	}
	public function recovery() {
		if ($this->CMS->CheckPurview('contentmanage', 'add') == false) {
			$this->error('无权限');
		}

		$request = Request::instance()->param();
		$obj = new \app\admin\module\activity(session('idsite'));
		$bool = $obj->recovery($request['id']);
		if ($bool) {
			echo "1";
		} else {
			echo "0";
		}
		exit();
	}

	//选中删除
	public function delchecked() {
		if ($this->CMS->CheckPurview('contentmanage', 'del') == false) {
			return -1;
		}
		$request = Request::instance()->param();
		$role_obj = new \app\admin\module\activity(session('idsite'));
		$bool = $role_obj->del($request);
		if ($bool) {
			return 1;
		} else {
			return 0;
		}
	}

	public function signupindex() {

		if ($this->CMS->CheckPurview('contentmanage', 'del') == false) {
			return -1;
		}

		$request = Request::instance()->param();
		$obj = new \app\admin\module\activity(session('idsite'));
		$arr = $obj->signupindex($request);
		$data = $arr['data'];
		$page = $arr['pager'];
		$search = $arr['search'];

		$p = isset($request["p"]) ? $request["p"] : 1;
		$id = isset($request["id"]) ? $request["id"] : 0;

		$this->assign('search', $search);
		$this->assign('page', $page);
		$this->assign('data', $data);
		$this->assign('dataid', $id);
		$this->assign('p', $p);
		$this->assign('order_state', config('order_state'));
		$this->assign('order_state_color', config('order_state_color'));
		return $this->fetch();
	}

	//增改查页面处理
	public function signupmodi() {
		$data = Request::instance()->param();

		if (Request::instance()->isPost()) {
			if ($this->CMS->CheckPurview('contentmanage', 'checksignup') == false) {
				$this->error('无权限');
			}
			$obj = new \app\admin\module\Order();
			$bool = $obj->PostData($data);
			if ($bool !== false) {
				// 发送短信
				//获取订单信息
				$order = db('order')->where(['id' => $data['id'], 'idsite' => $this->idsite])->find();
				//替换信息
				$replace = [];
				if ($data['intflag'] == 3) //审核不通过
				{
					//给客户和商务发短信通知  类型：9--免费活动审核不通过
					sysSendMsg($this->idsite, 9, $order, $replace);
				} elseif ($data['intflag'] == 4) //取消报名
				{

				} elseif ($data['intflag'] == 5) //审核通过
				{
					//给客户和商务发短信通知  类型：8--免费活动审核通过
					sysSendMsg($this->idsite, 8, $order, $replace);
				}
				$this->success('操作成功', PUBLIC_URL . 'postsuccess.html');
			} else {
				$this->error('操作失败');
			}
			exit();
		}

		$obj = new \app\admin\module\activity(session('idsite'));
		$datainfo = $obj->signupmodi($data);
		$this->assign('datainfo', $datainfo);
		$this->assign('order_state', config('order_state'));
		return $this->fetch();
	}

    //导入订单    
    public function importorder()
    {
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('contentmanage','add')==false){
            $this->error('无权限');
        }
        $id=$request["id"];
        $templateid=$request["templateid"];

        if (Request::instance()->isPost())
        {
            $obj= new \app\admin\module\activity(session('idsite'));
            if ($_FILES['file1']["error"] > 0)
            {
                $this->error('文件上传出错，请重新上传文件');
                exit;
            }
            else
            {
                $tmp_file_paht= $_FILES['file1']["tmp_name"];
                $data=$this->import_excel($tmp_file_paht);
                if($data)
                {
                    if(!empty($templateid) && $templateid>0)
                    {
                        $result_t=$obj->template_sub($templateid);
                    }

                    $txtfield="";
                    $txtdata="";
                    foreach ($data as $k=>$vo)
                    {
                        if($k==0)
                        {
                            $tmp_1="";
                            $tmp_2="报名人姓名☆套餐名称☆购买数量☆订单总额☆订单来源";
                            $ci=count($vo);
                            if($ci<5)
                            {
                                $this->error('模版格式有变化，请重新下载模版，填写数据2');
                                exit;
                            }
                            $tmp_1=$vo[0]."☆".$vo[1]."☆".$vo[2]."☆".$vo[3]."☆".$vo[4];
                            if($tmp_2!=$tmp_1)
                            {
                                //echo "===".$tmp_1."==<br>";
                                //echo "===".$tmp_2."==<br>";
                                $this->error('模版格式有变化，请重新下载模版，填写数据3');
                                exit;
                            }
                            if(count($vo)-5<count($result_t))
                            {
                                //echo "===".count($vo)."==<br>";
                                //echo "===".count($result_t)."==<br>";
                                $this->error('模版格式有变化，请重新下载模版，填写数据4');
                                exit;
                            }
                            $tmp_1="";
                            $tmp_2="";
                            if(!empty($result_t))
                            {
                                foreach ($result_t as $k1=>$vo1)
                                {
                                    $tmp_1=$tmp_1."☆".$vo[$k1+5];
                                    $tmp_2=$tmp_2."☆".$vo1['title'];
                                    $txtfield=$txtfield."☆".$vo1['title']."∫".$vo1['chrtype'];
                                }
                            }
                            $txtfield=trim($txtfield,'☆');
                            if($tmp_2!=$tmp_1)
                            {
                                $this->error('模版格式有变化，请重新下载模版，填写数据5');
                                exit;
                            }
                        }
                        else
                        {

                            $UserName=$vo[0];
                            $payname=$vo[1];
                            $payCount=is_numeric($vo[2])?$vo[2]:0;;
                            $price=is_numeric($vo[3])?$vo[3]:1;
                            $source=$vo[4];

                            $txtdata="";
                            $trueName = "";
                            $mobile = "";
                            foreach ($result_t as $k1=>$vo1)
                            {
                                if($vo1['title'] == "姓名"){
                                    $trueName = !empty($vo[$k1+5]) ?  $vo[$k1+5] : '';
                                }else if($vo1['title'] == "联系电话"){
                                    $mobile = !empty($vo[$k1+5]) ? $vo[$k1+5] : '';
                                }
                                $txtdata=$txtdata."☆".$vo[$k1+5];
                            }

                            $txtdata=trim($txtdata,'☆');

                            $result = $obj->addOrder($id,$UserName,$payname,$price,$payCount,$txtfield,$txtdata,$source,4);

                            if($result && $mobile){
                                if($request["action"] == 'import_sms'){
                                    if($trueName){
                                        $trueName .= '，';
                                    };
                                    $siteid = session("idsite");
                                    $checkcode = db('order')->where('ordersn',$result)->value('checkcode');
                                    $sitecode = db('site_manage')->where('id',$siteid)->value("site_code");
                                    $url = ROOTURL."/".$sitecode."/siteqrcode";
                                    $msg_content = $trueName.'兑换码：'.$checkcode.'，请在活动前完成兑换，兑换操作详情点击：'.$url;
									smsSendSchedule($siteid, $mobile, $msg_content, session("AccountID"), session("Username"), 1, date('Y-m-d H:i:s'));
                                }
                            }
                        }
                    }
                }
                else
                {
                    $this->error('请选择上传文件');
                }

            }
            $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            exit();
        }

        $this->assign('id',$id);
        $this->assign('templateid',$templateid);
        return $this->fetch();
    }

	public function ordertemplate() {
		$request = Request::instance()->param();
		if ($this->CMS->CheckPurview('contentmanage', 'add') == false) {
			$this->error('无权限');
		}
		$templateid = $request["templateid"];

		if (!empty($templateid)) {
			$obj = new \app\admin\module\activity(session('idsite'));
			$result = $obj->template_sub($templateid);
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
		$objc->setCellValue('A1', '报名人姓名');
		$objc->setCellValue('B1', '套餐名称');
		$objc->setCellValue('C1', '购买数量');
		$objc->setCellValue('D1', '订单总额');
		$objc->setCellValue('E1', '订单来源');

		$objc->getColumnDimension('A')->setWidth(12);
		$objc->getColumnDimension('B')->setWidth(12);
		$objc->getColumnDimension('C')->setWidth(12);
		$objc->getColumnDimension('D')->setWidth(12);
		$objc->getColumnDimension('E')->setWidth(12);
		if (!empty($result)) {
			foreach ($result as $k => $vo) {
				$objc->getColumnDimension($this->getExcetCode($k + 5))->setWidth(12);
				$objc->setCellValue($this->getExcetCode($k + 5) . "1", $vo['title']);
			}
		}

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
	function getExcetCode($i) {
		$k = intval($i / 26);
		$i = $i % 26;

		//$v='0123456789abcdefghijklmnopqrstuvwsyzABCDEFGHIJKLMNOPQRSTUVWSYZ';
		$v = 'ABCDEFGHIJKLMNOPQRSTUVWSYZ';
		$strV = "";
		if ($k > 0) {
			$strV .= $v[$k - 1];
		}
		$strV .= $v[$i];
		return $strV;

	}
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
	public function customdetail() {
		$request = Request::instance()->param();
		$obj = new \app\admin\module\activity(session('idsite'));
		$arr = $obj->customdetail($request);
		$data = $arr['data'];
		$page = $arr['pager'];

		$this->assign('page', $page);
		$this->assign('data', $data);
		return $this->fetch();
	}

	public function audit_result() {
		$request = Request::instance()->param();
		$obj = new \app\admin\module\activity(session('idsite'));
		$arr = $obj->getActivityDetail($request);
//        var_dump($arr);exit;
		$this->assign('data', $arr);
		return $this->fetch();
	}

	/**
	 * 活动订单导出
	 */
	public function activity_order_export() {

		$request = Request::instance()->param();
		$obj = new \app\admin\module\activity(session('idsite'));
		$arr = $obj->signupindex($request);
		//var_dump($arr);exit;
		$list = $arr["data"];

		$order_state = config('order_state');
		ob_clean();

		//游玩日期	状态	报名人	是否签到	活动名称	订单号	购买套餐	购买数量	总金额	退款金额	支付时间	短信码
		//2.加载PHPExcle类库
		require_once dirname(__FILE__) . '/../../../extend/PHPExcel/PHPExcel.php';
		//3.实例化PHPExcel类
		$objPHPExcel = new \PHPExcel();
		$PHPSheet = $objPHPExcel->getActiveSheet();
		//5.设置表格头（即excel表格的第一行）
		$PHPSheet
			->setCellValue('A1', '订单号')
			->setCellValue('B1', '活动名称')
			->setCellValue('C1', '报名人')
			->setCellValue('D1', '状态')
			->setCellValue('E1', '是否签到')
			->setCellValue('F1', '购买套餐')
			->setCellValue('G1', '购买数量')
			->setCellValue('H1', '总金额')
			->setCellValue('I1', '退款金额')
			->setCellValue('J1', '支付时间');

		$model = array();

		//设置单元格宽度
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(25);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(60);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(25);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(25);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(25);
		for ($i = 75; $i < 85; $i++) {
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(strtoupper(chr($i)))->setWidth(25);
		}
//        for($i=0)
		$len = count($list);
		for ($i = 0; $i < $len; $i++) {
			$PHPSheet->setCellValue('A' . ($i + 2), $list[$i]['ordersn']); //添加订单号
			$PHPSheet->setCellValue('B' . ($i + 2), $list[$i]['chrtitle']); //添加活动名称
			$PHPSheet->setCellValue('C' . ($i + 2), $list[$i]['chrusername']); //添加报名人
			$PHPSheet->setCellValue('D' . ($i + 2), $order_state[$list[$i]['state']]); //添加状态
			$PHPSheet->setCellValue('E' . ($i + 2), (!empty($list[$i]['issign']) ? "已签到" : "未签到")); //是否签到
			$PHPSheet->setCellValue('F' . ($i + 2), $list[$i]['payname']); //购买套餐
			$PHPSheet->setCellValue('G' . ($i + 2), $list[$i]['paynum']); //购买数量
			$PHPSheet->setCellValue('H' . ($i + 2), $list[$i]['price']); //总金额
			$PHPSheet->setCellValue('I' . ($i + 2), $list[$i]['refundprice']); //退款金额
			$PHPSheet->setCellValue('J' . ($i + 2), $list[$i]['dtpaytime']); //支付时间
			if ($list[$i]['txtdata']) {
				$tmp_txtfield = explode("☆", $list[$i]['txtfield']);
				$tmp_txtdata = explode("☆", $list[$i]['txtdata']);
				foreach ($tmp_txtdata as $key => $tmp) {
					if (!isset($tmp_txtfield[$key])) {
						continue;
					}
					$txtfield = explode("∫", $tmp_txtfield[$key]);
					$txtdata = explode("∫", $tmp);
					$tmp_index = array_search($txtfield[0], $model);
					if ($tmp_index === false) {
						$tmp_index = count($model);
						$model[$tmp_index] = $txtfield[0];
					}
					$PHPSheet->setCellValue(strtoupper(chr(75 + $tmp_index)) . "1", $txtfield[0]);
					$PHPSheet->setCellValue(strtoupper(chr(75 + $tmp_index)) . ($i + 2), ' ' . $txtdata[0]); //支付时间
				}
			}
			if ($list[$i]['txtdata1']) {
				$tmp_txtfield = explode("☆", $list[$i]['txtfield1']);
				$tmp_txtdata = explode("☆", $list[$i]['txtdata1']);
				foreach ($tmp_txtdata as $key => $tmp) {
					if (!isset($tmp_txtfield[$key])) {
						continue;
					}
					$txtfield = explode("∫", $tmp_txtfield[$key]);
					$txtdata = explode("∫", $tmp);
					$tmp_index = array_search($txtfield[0], $model);
					if ($tmp_index === false) {
						$tmp_index = count($model);
						$model[$tmp_index] = $txtfield[0];
					}
					$PHPSheet->setCellValue(strtoupper(chr(75 + $tmp_index)) . "1", $txtfield[0]);
					$PHPSheet->setCellValue(strtoupper(chr(75 + $tmp_index)) . ($i + 2), ' ' . $txtdata[0]); //支付时间
				}
			}

		}

		//7.设置保存的Excel表格名称

		//8.设置当前激活的sheet表格名称；
		$objPHPExcel->getActiveSheet()->setTitle("活动报名信息");

		//4.激活当前的sheet表
		$objPHPExcel->setActiveSheetIndex(0);
		//9.设置浏览器窗口下载表格
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$filename = urlencode("活动报名信息") . date('ymd', time()) . '.xlsx';
		header("Content-Disposition: attachment;filename=$filename");

		//生成excel文件
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//下载文件在浏览器窗口
		$objWriter->save('php://output');
		exit;
	}

	public function sendmsg1() {
		if ($this->CMS->CheckPurview('contentmanage') == false) {
			$this->error('无权限');
		}
		$data = Request::instance()->param();

		$obj = new \app\admin\module\activity(session('idsite'));
		if (Request::instance()->isPost()) {
			$key = getNumber();
			$data['key'] = $key;
			$data['title'] = "";
			$data['inttype'] = 2;
			$data['inttype1'] = 1;
			$data['username'] = session("UserName");
			$data['userid'] = session("AccountID");

			$result = $obj->sendmsg1($data);

			if ($result['status'] === 'success') {
				$result1 = send_msg($key, getSiteCode(session('idsite')));
				exit(json_encode($result1));
			} else {
				exit(json_encode(array("state" => 0, "key" => "", "msg" => $result['msg'])));
			}
		}

		$idmember = isset($data["idmember"]) ? $data["idmember"] : 0;
		$nickname = isset($data["nickname"]) ? $data["nickname"] : "";
		$iduser = isset($data["iduser"]) ? $data["iduser"] : 0;

		$info = $obj->deal(array("id" => $data["dataid"]));
		$chrurl = ROOTURL . "/admin/member/deal/idmember/" . $idmember . "/action/view";
		$chrname = "";
		$activitytime = "";
		if ($info) {
			$chrname = $info["chrtitle"];
			$activitytime = $info["dtstart"] . "~" . $info["dtend"];
		}

		$this->assign('dataid', $data["dataid"]);
		$this->assign('sitecode', getSiteCode(session('idsite')));
		$this->assign('nickname', $nickname);
		$this->assign('nowtime', date("Y年m月d日 H:i"));
		$this->assign('chrurl', $chrurl);
		$this->assign('chrname', $chrname);
		$this->assign('iduser', $iduser);
		$this->assign('activitytime', $activitytime);
		return $this->fetch();
	}

	public function send_sms_msg() {
		if ($this->CMS->CheckPurview('contentmanage') == false) {
			$this->error('无权限');
		}
		$data = Request::instance()->param();
		$obj = new \app\admin\module\Sitemanege;
		if (Request::instance()->isPost()) {
			if (isset($data["mobile"])) {
				$data["mobile"] = implode(",", $data["mobile"]);
			}
			$msg = $obj->sms_batch_send($data);
			if ($msg == "success") {
				exit(json_encode(array("state" => 1)));
			} else {
				exit(json_encode(array("state" => 0, "msg" => $msg)));
			}
		}

		$idsite = session('idsite');
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

	//通过用户id获取手机号码列表
	public function get_mobile_by_user_ids() {
		$data = Request::instance()->param();
		$mobile_list = array();
		if (isset($data["userids"])) {
			$userids = $data["userids"];
			$whereArr = array();
			$whereArr["idsite"] = session('idsite');
			$whereArr["idmember"] = array("in", explode(",", $userids));
			$rows = db("member")->where($whereArr)->field("chrtel")->select();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					if ($row["chrtel"]) {
						$mobile_list[] = $row["chrtel"];
					}
				}
			}
		}
		echo json_encode(array("mobile_list" => $mobile_list));
		exit;
	}

	//一键同步
	public function one_click_sync() {
		$data = Request::instance()->param();
		$id = isset($data["id"]) ? $data["id"] : 0;

		$url = config("wxnt_sync_url");
		$rs = HttpCurl::post($url, http_build_query(array("code" => "searchcommon", "productid" => $id)), "json");
		$rs = json_decode(json_encode($rs), true);
//        var_dump($rs["data"]["data"]);

		$abilitytagsname = $summer_chrtypename = $chrtypename = $chraddress = "";
		$tid = $province = $city = $issummercamp = $tourismid = $fiddistrict = $fidarea = 0;
		if (isset($rs["code"]) && $rs["code"] == 1) {
			$info = $rs["data"]["data"][0];
			if (isset($info)) {
				$tid = 1;
				$chrtypename = $info["chrtypename"];
				if ($chrtypename) {
					$chrtypename = explode(",", $chrtypename);
				}
				$chraddress = $info["chraddress"];
				$fiddistrict = isset($info["fiddistrict"][0]) ? $info["fiddistrict"][0] : 0;
				$fidarea = isset($info["fidarea"][0]) ? $info["fidarea"][0] : 0;
				$abilitytagsname = trim($info["abilitytagsname"]);
				if ($abilitytagsname) {
					$abilitytagsname = explode(",", $abilitytagsname);

					//将能力名称处理
					if ($info['abilitytags']) {
						foreach ($info['abilitytags'] as $k => $v) {
							if (!strstr($abilitytagsname[$k], '|')) {
								$abilitytagsname[$k] = $v . "|" . $abilitytagsname[$k];
							}
						}
					}
//                    var_dump($abilitytagsname);exit;
				}

				if (isset($info["issummercamp"])) {
					$issummercamp = $info["issummercamp"];
					$province = $fidarea;
					$city = $fiddistrict;
					$summer_chrtypename = $chrtypename;
					$chrtypename = "";
					$tid = 2;
					$tourismid = $info["tourismid"];
				}
			}
		}
		$chrtypename = json_encode($chrtypename);
		$abilitytagsname = json_encode($abilitytagsname);
		$summer_chrtypename = json_encode($summer_chrtypename);

		$this->assign('chrtypename', $chrtypename);
		$this->assign('summer_chrtypename', $summer_chrtypename);
		$this->assign('abilitytagsname', $abilitytagsname);
		$this->assign('chraddress', $chraddress);
		$this->assign('fiddistrict', $fiddistrict); //活动商圈
		$this->assign('fidarea', $fidarea); //活动区域
		$this->assign('tourismid', $tourismid); //国家
		$this->assign('issummercamp', $issummercamp); //是否夏令营
		$this->assign('province', $province); //省
		$this->assign('city', $city); //市
		$this->assign('tid', $tid);
		$this->assign('id', $id);
		return $this->fetch();
	}

	//通过code获取蜗牛童行那边的信息
	public function get_wntx_info_by_code() {
		try {
			$data = Request::instance()->param();
			$code = isset($data["code"]) ? $data["code"] : "";
			$pid = isset($data["pid"]) ? $data["pid"] : "";
			if (empty($code)) {
				//code参数不能为空！
				exit(json_encode(array("status" => "fail", "msg" => "code param not empty!")));
			}
			$url = config("wxnt_sync_url");
			$rs = HttpCurl::post($url, http_build_query(array("code" => $code, "pid" => $pid)), "json");
			$rs = json_decode(json_encode($rs), true);
			if ($rs["code"] == 1) {
				$list = $rs["data"]["data"];
				echo json_encode(array("status" => "success", "list" => $list));
				exit;
			} else {
				exit(json_encode(array("status" => "fail", "msg" => "get info error!")));
			}
		} catch (\Exception $ex) {
			exit(json_encode(array("status" => "fail", "msg" => "api error!")));
		}
	}

	/**
	 * 保存蜗牛童行同步信息
	 */
	public function save_wntx_sync_info() {

		$data = Request::instance()->param();
		if (Request::instance()->isPost()) {

			$obj = new \app\admin\module\activity(session('idsite'));
			$result = $obj->save_wntx_sync_info($data);
			if ($result == "success") {
				exit(json_encode(array("status" => "success", "msg" => "save success!")));
			} else {
				exit(json_encode(array("status" => "fail", "msg" => $result)));
			}
		} else {
			exit(json_encode(array("status" => "fail", "msg" => "http request method error!")));
		}
	}

	//取消蜗牛童行同步
	public function cancel_wntx_sync() {
		$data = Request::instance()->param();
		if (Request::instance()->isPost()) {

			$obj = new \app\admin\module\activity(session('idsite'));
			$data["sync_status"] = 2;
			$result = $obj->change_sync_status($data);
			if ($result == "success") {
				exit(json_encode(array("status" => "success", "msg" => "save success!")));
			} else {
				exit(json_encode(array("status" => "fail", "msg" => $result)));
			}
		} else {
			exit(json_encode(array("status" => "fail", "msg" => "http request method error!")));
		}
	}

	/**
	 * 后台管理 删除套餐
	 * @author Hlt
	 * @DateTime 2019-05-13T16:42:14+0800
	 * @return   [type]                   [description]
	 */
	public function deletePackage() {
		try
		{
			if (!$this->CMS->CheckPurview('contentmanage', 'del')) {
				throw new Exception('没有权限');
			}
			$request = Request::instance();
			$param = $request->param();
			if (!isset($param['activity_id']) || !isset($param['package_id'])) {
				throw new Exception('缺少参数');
			}

			$packageObj = new \app\admin\module\Package;
			$packageObj->deletePackage($param['activity_id'], $param['package_id']);

			exit(json_encode(['status' => 'success', 'msg' => '删除成功']));
		} catch (Exception $e) {
			exit(json_encode(['status' => 'fail', 'msg' => $e->getMessage()]));
		}

	}
}