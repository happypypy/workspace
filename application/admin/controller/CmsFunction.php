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
use think\Session;
class CmsFunction{
    //权限判断
    //$Resource 资源代号
    //$Operate  操作代号

    //public $idsite;

    public function CheckPurview($Resource='',$Operate='',$CheckKey=''){
        if(session('Account')=='admin')
            return true;
         session('idsite')?$idsite = session('idsite'):$idsite = 0;

        $purview=session("purview".$idsite);
        //session("purview".$idsite,null);
        if(empty($purview)) {
            $this->getPurview($idsite);
            $purview=session("purview".$idsite);
            if(empty($purview))
            {
                return false;
            }
        }

        if($CheckKey=='') {
            $CheckKey = CONTROLLER_NAME;//当前控制器名称/模块代号
        }
        if($Resource!='')
            $CheckKey=$CheckKey."_".$Resource;
        if($Operate!='')
            $CheckKey=$CheckKey."_".$Operate;
        return array_key_exists(strtolower($CheckKey), $purview);

    }

    //权限模块判断
    //$ModuleCode  模块代号
    public function CheckModulePurview($ModuleCode)
    {
        if(session('Account')=='admin')
            return true;
        if($ModuleCode=='')
            return false;

        session('idsite')?$idsite = session('idsite'):$idsite = 0;
        $purview=session("purview".$idsite);
        if(empty($purview)) {
            $this->getPurview($idsite);
            $purview=session("purview".$idsite);
            if(empty($purview))
            {
                return false;
            }
        }

        $CheckKey=$ModuleCode;//当前控制器名称/模块代号
        return array_key_exists(strtolower($CheckKey), $purview);

    }



    //获取权限
    private function  getPurview($idsite)
    {
        if(empty(session('AccountID')))
            return;

        $dt= db('purview')->where(['idaccount'=>['eq',session('AccountID')],'idsite'=>['eq',$idsite]])->select();
        $purview=array();
        foreach ($dt as $row)
        {
            $purview[strtolower($row['chrmodulecode'])]=true;
            $purview[strtolower($row['chrmodulecode'])."_".strtolower($row['chrresourcecode'])]=true;
            $purview[strtolower($row['chrmodulecode'])."_".strtolower($row['chrresourcecode'])."_".strtolower($row['chroperatecode'])]=true;
        }
        session("purview".$idsite,$purview);
    }

    //角色操作查询
    public function roleOperate($role_id,$module_code,$resource_code,$operate_code){
        $role_operate = db('role_operate')
            ->where('idrole',$role_id)
            ->where('chrmodulecode',$module_code)
            ->where('chrresourcecode',$resource_code)
            ->field('chroperatecode')
            ->select();
        $operate = [];
        foreach ($role_operate as $key=>$value){
            $operate .= $value[$key];
        }

           if(strstr($operate,$operate_code)){
                return true;
           }else{
               return false;
           }
    }

    //写错误日
	//$title 标题
	//$msg  出错内容
	public function WriteErrLog($title,$msg)
	{
		echo "dddd";
	}
	//系统操作日记
	//$moduleName 模块名称
	//$msg  日记内容
	//$lever 日记等级：增、删、改、查
	public function WriteSysLog($moduleName,$msg,$lever)
	{
		/*
		$AccountID
		$AccountName
		$moduleName
		$msg
		$lever
		$strIP
		*/
	}

	/**
	 * 获取菜单配置项的值
     *$menucode 菜单代号
     * $rulecode　配置项名（健名）
	 */
    public function GetConfigVal($menucode,$fieldname){
        $arr = [];
        if(cache('config')){
            $arr = cache('config');
        }else{
            $menu_list = db('config_menu')->select();
            $rule_list = db('config_rule')->select();
            foreach ($menu_list as $key=>$value){
                foreach ($rule_list as $ke=>$val){
                    if($value['chrcode'] == $val['menucode']){
                        $arr[$value['chrcode']][$val['fieldname']] = $val['defaultval'];
                    }
                }
            }
            //存入缓存 所有菜单和配置项的值
            cache('config',$arr);
        }
        $data = '';
        foreach ($arr as $key=>$value){
            if($key == $menucode){
                $data = $value[$fieldname];
                break;
            }
        }
        return $data;
        /*$menu_info = db('confiog_menu')->where('id',$idmenu)->find();
        $rule_info = db('config_rule')->where('idmenu',$idmenu)->select();
        $arr = [];
        foreach ($rule_info as $key=>$value){
            $arr[$menu_info['chrcode']][$value['fieldname']] = $value['defaultval'];
        }
        return $arr;*/
    }

    //敏感词缓存
    public function filter_cache($idsite){
        $data = db('filter')->where('isusing=1 and idsite = '.$idsite)->order('idorder asc')->field('content,replace')->select();
        cache('filter'.$idsite,$data);
    }

	 
}
