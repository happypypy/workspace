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

namespace app\home\controller;
use think\Controller;
use think\Session;
use think\Cms;
use think\Request;

class Base extends Controller{
    public $cateTrre = array();
	/**
     * 析构函数
     */
    function __construct(){
	    //Session::start();
        session_start();
        header("Cache-control: private");

        $request = Request::instance()->param();
        if(!empty($request['sitecode'])) {
            $sitecode=$request['sitecode'];
            $data = db('site_manage')->field('id,expiretime')->where(['site_code' => $sitecode])->find();

            if (empty($data['expiretime']) || time() > $data['expiretime'])
            {
                header("location:/error.php?msg=" . urlencode("网站异常，请与机构联系。") . "&url=" );
                exit();
            }
            if(empty(session("UserInfo_".$sitecode."_siteid")))
            {
                $this->ClearSeaaionInfo($sitecode);
            }
            else if(session("UserInfo_".$sitecode."_siteid")!=$data['id'])
            {
                $this->ClearSeaaionInfo($sitecode);
            }

            $current_site_code = \session("current_site_code");
            if(!isset($current_site_code) || empty($current_site_code)){
                \session("current_site_code",$sitecode);
            }else{
                if($current_site_code != $sitecode){
                    $this->ClearSeaaionInfo($sitecode);
                }
            }
        }
        parent::__construct();		
       
    }
    public function ClearSeaaionInfo($sitecode)
    {
        session("UserInfo_".$sitecode."_nickname",null);
        session("UserInfo_".$sitecode."_openid",null);
        session("UserInfo_".$sitecode."_ismanage",null);
        session("UserInfo_".$sitecode."_userid",null);
        session("UserInfo_".$sitecode."_siteid",null);
        session("UserInfo_".$sitecode."_load",null);
    }
    public $CMS=null;//这里声明
    public $lang = 'cn';

    /*
     * 初始化操作
     */
    public function _initialize(){

	    define('MODULE_NAME',$this->request->module());  // 当前模块名称是
        define('CONTROLLER_NAME',$this->request->controller()); // 当前控制器名称
        define('ACTION_NAME',$this->request->action()); // 当前操作名称是

        $this->CMS = new Cms;
        $this->assign('cms',$this->CMS);
        if(cookie('lang_type')){
            $this->lang = session('aa');
        }
        config('home_lang_list')?$config_lang_type = config('home_lang_list'):$config_lang_type = ['cn'=>'简体中文'];
        $this->assign('lang',$config_lang_type);

        //加载语言常量
        $constant =  str_replace('\\','/',APP_PATH . 'home/constant/'.$this->lang.'.php');
        require_once $constant;

    }

}
