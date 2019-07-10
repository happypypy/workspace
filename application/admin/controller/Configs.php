<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/24
 * Time: 9:20
 */

namespace app\admin\controller;
use think\Request;

class Configs extends Base {

    //网站栏目信息（网站配置默认）
    public function index(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('configweb','view')==false){
            $this->error('无权限');
        }
        $config_obj = new \app\admin\module\Configs();
        $arr = $config_obj->index($request);
        $config_menu = $arr['menudata'];
        $menu_rule = $arr['ruledata'];
        $menu_name = $arr['menuone'];
        $idsite = $arr['idsite'];
        $reply_word = $arr["reply_word"];
        $reply_img = $arr["reply_img"];
        $arr = [];
        foreach ($menu_rule as $key => $value) {
            $arr[$menu_name['chrcode']][$value['fieldname']] = $value['defaultval'];
        }
        $this->assign('menuname', $menu_name);
        $this->assign('menurule', $menu_rule);
        $this->assign('configmenu', $config_menu);
        $this->assign('idsite', $idsite);
        $this->assign('reply_word',$reply_word);
        $this->assign('reply_img', $reply_img);
        return $this->fetch();
    }

    //数据提交（网站配置：栏目保存）
    public function datapost(){

        if($this->CMS->CheckPurview('configweb','edit')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $config_obj = new \app\admin\module\Configs();
        $result = $config_obj->data_post($request);
        cache('config'.$request['idsite'],null);
        if($result['bool']){
            $this->success('操作成功',url('configs/index','menucode='.$result['menucode']));
        }else{
            $this->success('操作失败');
        }
    }

    //管理自定义配置
    public function configself(){
        if($this->CMS->CheckPurview('configweb')==false){
            $this->error('无权限');
        }

        $config_obj = new \app\admin\module\Configs();
        $arr = $config_obj->config_self();
        $page = $arr['pager'];
        $config_menu = $arr['data'];
        $this->assign('idsite',$arr['idsite']);
        $this->assign('page',$page);
        $this->assign('configmenu',$config_menu);
        return $this->fetch();
    }

    //修改菜单跳转页面
    public function configdeal(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('configweb',$request['action'])==false){
            $this->error('无权限');
        }
        $config_obj = new \app\admin\module\Configs();
        $config_info = $config_obj->config_deal($request);
        $this->assign('request',$request);
        $this->assign('configinfo',$config_info);
        return $this->fetch();
    }

    //删除菜单
    public function configdel(){
        if($this->CMS->CheckPurview('configweb','del')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $config_obj = new \app\admin\module\Configs();
        $bool = $config_obj->config_del($request);
        if($bool){
            $this->success('删除成功');
        }else{
            $this->success('删除失败');
        }
    }

    //修改,添加菜单提交地址
    public function configpost(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('configweb',$request['action'])==false){
            $this->error('无权限');
        }
        if (empty($request['chrname']) || empty($request['chrcode']) || empty($request['intsn'])){
            $this->error('前面带*号的为必填项');
        }else{
            $config_obj = new \app\admin\module\Configs();
            $bool = $config_obj->config_post($request);
            if($bool !== false){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->success('操作失败');
            }
        }
    }

    //导入
    public function menuleading(){
        $obj = new \app\admin\module\Configs();
        //如果是子站删除该子站所有菜单，导入主站的配置菜单
        $result = $obj->menu_leading();
        if($result['bool1'] && $result['bool2']){
            $this->success('导入成功');
        }else{
            $this->error('导入失败');
        }
    }

    //导出
    public function menuexport(){
        $request = Request::instance()->param();
        $idsite = $request['idsite'];
        $menu_list = db('config_menu')->where('idsite='.$idsite)->select();
        $rule_list = db('config_rule')->where('idsite='.$idsite)->select();
        $arr=[];
        foreach ($menu_list as $key=>$value){
            $arr[$key]=$value;
            $arr[$key]["data"]=[];
            foreach ($rule_list as $ke=>$val){
                if($value['chrcode'] == $val['menucode']){
                    $arr[$key]["data"][]=$val;
                }
            }
        }
        $str = json_encode($arr,true); //将数组转化对象
        $str = $str.']';
        $filename = "config_menu.txt";
        header('Content-Type:file/txt'); //指定下载文件类型
        header('Content-Disposition: attachment; filename="'.$filename.'"'); //指定下载文件的描述
        header('Content-Length:'.strlen($str)); //指定下载文件的大小

        //将文件内容读取出来并直接输出，以便下载
        echo $str;
        exit();
    }

    //配置列表
    public function configrule(){
        if($this->CMS->CheckPurview('configrule','view')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $config_obj = new \app\admin\module\Configs();
        $rule_list = $config_obj->config_rule($request);
        $config_menu = $config_obj->config_menu();
        $this->assign('request',$request);
        $this->assign('configmenu',$config_menu);
        $this->assign('rulelist',$rule_list);
        $this->assign('idsite',session('idsite'));
        return $this->fetch();
    }

    //修改配置项跳转页面
    public function ruledeal(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('configrule',$request['action'])==false){
            $this->error('无权限');
        }
        $config_obj = new \app\admin\module\Configs();
        $rule_info = $config_obj->rule_deal($request);
        $config_menu = $config_obj->config_menu();
        $this->assign('request',$request);
        $this->assign('ruleinfo',$rule_info);
        $this->assign('configmenu',$config_menu);
        return $this->fetch();
    }

    //删除配置项
    public function ruledel(){
        if($this->CMS->CheckPurview('configrule','del')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $config_obj = new \app\admin\module\Configs();
        $bool = $config_obj->rule_del($request);
        if($bool){
            $this->success('删除成功');
        }else{
            $this->success('删除失败');
        }
    }

    //配置项修改，添加提交地址
    public function rulepost(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('configrule',$request['action'])==false){
            $this->error('无权限');
        }
       if(empty($request['fieldname']) || empty($request['chrname']) || empty($request['fieldtype'])){
           $this->error('前面带*号的为必填项');
       }
        $config_obj = new \app\admin\module\Configs();
        $bool = $config_obj->rule_post($request);
        if ($bool == '多项选择属性列表不能为空') {
            $this->error($bool);
        }elseif($bool !== false) {
            $this->success("操作成功",PUBLIC_URL.'postsuccess.html');
        }else{
            $this->error("操作失败");
        }

    }

    //changge ajax 提交数据
    public function ajaxpost(){
        $request = Request::instance()->param();
        $rule_list = db('config_rule')->where("menucode='".$request['menucode']."' and ( idsite=0 or idsite=".session('idsite').")")->select();
        $this->assign('rulelist',$rule_list);
        if(request()->isAjax()){
            return $ajax = $this->fetch();
        }else
            return 2;

        //return ['rulelist'=>$rule_info];
    }
























}