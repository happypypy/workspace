<?php
/**
 * 天络CMS
 * ============================================================================
 * 版权所有 201７-2027 深圳天络科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinasky.net
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: huangshixin
 * Date: 2017-06-22
 */

namespace app\admin\controller;
use think\Controller;
use think\Session;
class Basesite extends Controller{
    public $cateTrre = array();
	/**
     * 析构函数
     */
    function __construct(){
        Session::start();
        $this->initTheme();
        header("Cache-control: private");  
        parent::__construct();		
       
    }    
    public $CMS=null;//这里声明
    public $lang = 'cn';
    public $idsite=0;

    /*
     * 初始化操作
     */
    public function _initialize(){
       if(empty(session('idsite')))
       {
           echo "<script>if(window.top.document.getElementById('pFrame')){window.top.location.href= '".url('Admin/index/loginsite')."';}else{window.parent.parent.location.href='".url('Admin/index/loginsite')."';}</script>";
           exit;
           header("Location:".url("admin/index/loginsite"));
       }

        $this->idsite = session('idsite');
	    define('MODULE_NAME',$this->request->module());  // 当前模块名称是
        define('CONTROLLER_NAME',$this->request->controller()); // 当前控制器名称
        define('ACTION_NAME',$this->request->action()); // 当前操作名称是
		//echo MODULE_NAME."<br>";
		//echo CONTROLLER_NAME."<br>";
		//echo ACTION_NAME."<br>";
		$this->CMS=new CmsFunction();
        $this->assign('cms',$this->CMS);
		$this->assign('action_name', ACTION_NAME);

		//获取cookie lang_type 语言类型
       /* if(cookie('lang_type')){
            $this->lang = cookie('lang_type');
        }
        if($this->lang == 'cn'){
            $this->assign('prefix','');
        }else{
            $this->assign('prefix',$this->lang.'_');
        }*/

        //获取配置启用了几种语言
        //config('admin_lang_list')?$config_lang_type = config('admin_lang_list'):$config_lang_type = ['cn'=>'简体中文'];
        $config_lang_type = ['cn'=>''];
        $this->assign('lang',$config_lang_type);

        //$this->assign('action',ACTION_NAME);
        //过滤不需要登陆的行为

        if(in_array(ACTION_NAME,array('login','logout','vertify','forget_pwd','loginsite')) || in_array(CONTROLLER_NAME,array('Ueditor','Uploadify','Table'))){
        	//return;
        }else{
        	if(session('AccountID') > 0 ){
        		if(CONTROLLER_NAME=='Index')
        		    return;
        	    if($this->CMS->CheckPurview()==false) //检查操作权限
                {
                    //header("Location:".config('view_replace_str.__PUBLIC__')."/nopurview.html");
                    //$this->error('没权限',url('Admin/index/login'),1);
                   // exit;
                }
        	}else{
                echo "<script>if(window.top.document.getElementById('pFrame')){window.top.location.href= '".url('Admin/index/loginsite')."';}else{window.parent.parent.location.href='".url('Admin/index/loginsite')."';}</script>";
                exit;
        		$this->error('请先登陆',url('Admin/index/loginsige'),1);
        	}
        }
        $this -> operate_log();
    }
    
    // 初始化主题
    public function initTheme(){
        $theme = cache('AccountID_'.session("AccountID").'_theme') ? cache('AccountID_'.session("AccountID").'_theme') : 'v1';
        if($theme == 'v1'){
            config('template.view_path', APP_PATH.'/admin/view/');
        }else{
            config('template.view_path', APP_PATH.'/admin/view2/');
        }
    }


    //无权限页面
    public function NoPurview(){
       header("Location:".config('view_replace_str.__PUBLIC__')."/nopurview.html");
        exit;
    }
    public function  CheckSite()
    {
        if(empty($idsite) || $idsite<1) {
            $this->error('系统超时，请重新登陆', url('Admin/index/loginsite'), 1);
            exit();
        }
    }

    public function ajaxReturn($data,$type = 'json'){
        exit(json_encode($data));
    }

    private function operate_log($remark=""){
        $accountId = session("AccountID");
        $userName = session("UserName");
        $siteid = session('idsite');
        $param = $this->request->param();
        $filter_controller_name_list = array("Maindata");
        if(empty($accountId) || empty($param) || in_array(CONTROLLER_NAME,$filter_controller_name_list)){
            return false;
        }
        $log = array();
        $log["create_time"] = date("Y-m-d H:i:s");
        $log["idaccount"] = $accountId;
        $log["chrname"] = $userName?:"";
        $log["route"] = MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME;
        $log["request"] = json_encode(array("params"=>$param,"method"=>$this->request->method()));
        $log["remark"] =$remark;
        $log["ip"] = getip();
        $log["siteid"] = isset($siteid)?$siteid:0;
        db("account_log")->insert($log);
        return true;
    }


}
