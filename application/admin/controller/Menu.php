<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/7
 * Time: 9:38
 */

namespace app\admin\controller;
use think\Request;

class Menu extends Base {

    //首页左边栏目，模块列表
    public function index(){

        $menu = new \app\admin\module\Menu();
        $column_index = $menu->catalog_index();
        $module_list = $menu->module_index();
        $this->assign('modulist',$module_list);
        $this->assign('catalist',$column_index);
        return $this->fetch();
    }

    //菜单管理:栏目管理
    public function columnlist(){
        if($this->CMS->CheckPurview('columnmanage','view')==false){
            $this->NoPurview();
        }
        $menu = new \app\admin\module\Menu();
        $arr = $menu->catalog_list();
        $catalog_list = $arr['data'];
        $page = $arr['pager'];
        $this->assign('page',$page);
        $this->assign('catalist',$catalog_list);
        return $this->fetch();
    }

    //菜单管理:模块管理
    public function modulist(){
        if($this->CMS->CheckPurview('modulemanage','view')==false){
            $this->NoPurview();
        }
        $menu = new \app\admin\module\Menu();
        $request = Request::instance()->param();
        if(empty($request)){
            $request = [];
            $request['moduname'] = '';
            $request['moducode'] = '';
            $request['columncode'] = '';
        }else{
            //模块搜索分页
            if(array_key_exists('moduname',$request) == false){
                $request['moduname'] = '';
            }
            if(array_key_exists('moducode',$request) == false){
                $request['moducode'] = '';
            }
            if(array_key_exists('columncode',$request) == false){
                $request['columncode'] = '';
            }
            if(array_key_exists('p',$request) == false){
                $request['p'] = '1';
            }
        }
        $result = $menu->module_list($request);
        $catalog_list = $menu->catalog_index();
        $page = $result['pager'];
        $module_list = $result['data'];
        $this->assign('page',$page);
        $this->assign('request',$request);
        $this->assign('catalist',$catalog_list);
        $this->assign('modulist',$module_list);
        return $this->fetch();
    }

    //菜单管理:扩展模块
    public function extended_module_list(){
        if($this->CMS->CheckPurview('modulemanage','view')==false){
            $this->NoPurview();
        }
        $menu = new \app\admin\module\Menu();
        $request = Request::instance()->param();
        if(empty($request)){
            $request = [];
            $request['moduname'] = '';
            $request['moducode'] = '';
            $request['columncode'] = '';
        }else{
            //模块搜索分页
            if(array_key_exists('moduname',$request) == false){
                $request['moduname'] = '';
            }
            if(array_key_exists('moducode',$request) == false){
                $request['moducode'] = '';
            }
            if(array_key_exists('columncode',$request) == false){
                $request['columncode'] = '';
            }
            if(array_key_exists('p',$request) == false){
                $request['p'] = '1';
            }
        }
        $result = $menu->extended_module_list($request);
        $catalog_list = $menu->catalog_index();
        $page = $result['pager'];
        $module_list = $result['data'];
        $this->assign('page',$page);
        $this->assign('request',$request);
        $this->assign('catalist',$catalog_list);
        $this->assign('modulist',$module_list);
        return $this->fetch();
    }

    //菜单管理:站点过来查看扩展模块
    public function view_extended_module_list(){
        $menu = new \app\admin\module\Menu();
        $request = Request::instance()->param();
        if(empty($request)){
            $request = [];
            $request['moduname'] = '';
            $request['moducode'] = '';
            $request['columncode'] = '';
        }else{
            //模块搜索分页
            if(array_key_exists('moduname',$request) == false){
                $request['moduname'] = '';
            }
            if(array_key_exists('moducode',$request) == false){
                $request['moducode'] = '';
            }
            if(array_key_exists('columncode',$request) == false){
                $request['columncode'] = '';
            }
            if(array_key_exists('p',$request) == false){
                $request['p'] = '1';
            }
        }
        $result = $menu->extended_module_list($request);
        $catalog_list = $menu->catalog_index();
        $page = $result['pager'];
        $module_list = $result['data'];
        $this->assign('page',$page);
        $this->assign('request',$request);
        $this->assign('catalist',$catalog_list);
        $this->assign('modulist',$module_list);
        return $this->fetch();
    }

    //模块资源列表
    public function resourcelist(){
        if($this->CMS->CheckPurview('resourcemanage','view')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $menu = new \app\admin\module\Menu();
        $arr = $menu->resource_list($request);
        $page = $arr['pager'];
        $resource_list = $arr['data'];
        $this->assign('moducode',$request['moducode']);
        $this->assign('page',$page);
        $this->assign('resourcelist',$resource_list);
        return $this->fetch();
    }

    /**
     * 设置启用禁用扩展模块
     */
    public function set_extended_module(){
        $request = Request::instance()->param();
        $menu = new \app\admin\module\Menu();
        //调用模型中的设置扩展模块启用禁用
        $result = $menu->set_extended_module($request);
        if($result['success']){
            $this->success("{$result['message']}");
        }else{
            $this->error("{$result['message']}");
        }
    }

    /**
     * 查看扩展模块的企业列表
     * @return mixed
     */
    public function view_site(){
        $request = Request::instance()->param();
        $menu = new \app\admin\module\Menu();
        if(!isset($request['chrcode']) || !isset($request['chrname'])){
            $this->error("缺少必要的参数");
        }
        //调用查看扩展模块运用的企业的方法
        $arr = $menu->view_site($request);
        $page = $arr['pager'];
        $list = $arr['data'];
        $this->assign('page',$page);
        $this->assign('data',$list);
//        var_dump($arr);exit;
        return $this->fetch();
    }

    //栏目，模块，资源，删除，模块移至另一个栏目下
    public function del(){
        if($this->CMS->CheckPurview('columnmanage','del')==false){
            $this->NoPurview();
        }
        if($this->CMS->CheckPurview('modulemanage','del')==false){
            $this->NoPurview();
        }
        if($this->CMS->CheckPurview('resource','del')==false){
            $this->NoPurview();
        }

        $request = Request::instance()->param();
        $menu = new \app\admin\module\Menu();
        $bool = $menu->del($request);
        if($bool){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    //选中删除
    public function delchecked(){
        if($this->CMS->CheckPurview('columnmanage','del')==false){
            $this->NoPurview();
        }
        if($this->CMS->CheckPurview('modulemanage','del')==false){
            $this->NoPurview();
        }
        if($this->CMS->CheckPurview('resourcemanage','del')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $menu = new \app\admin\module\Menu();
        $bool = $menu->del_checked($request);
        if($bool){
            return 1;
        }else{
            return 0;
        }
    }

    //模块移至另一个栏目下
    public function modulechange(){

        $request = Request::instance()->param();
        $menu = new \app\admin\module\Menu();
        $bool = $menu->del($request);
        if($bool){
            return 1;
        }else{
            return 2;
        }
    }


    //栏目添加，修改，跳转页面
    public function columndeal(){
        $request = Request::instance()->param();
        $menu = new \app\admin\module\Menu();
        $column_info = $menu->column_deal($request);
        $this->assign('columninfo',$column_info);
        return $this->fetch();
    }

    //栏目添加，修改提交地址
    public function columnpost(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('columnmanage',$request['action'],1)==false){
            $this->NoPurview();
        }
        if (empty($request['chrcode']) || empty($request['chrname']) || empty($request['intsn'])) {
            $this->error('前面带*号的为必填项');
        }else{
            $menu = new \app\admin\module\Menu();
            $bool = $menu->column_post($request);
            if($bool == '栏目代号已存在'){
                $this->error($bool);
            }elseif($bool == true){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error('操作失败');
            }
        }
    }

    //模块添加，修改，跳转页面
    public function modudeal(){
        $request = Request::instance()->param();
        $menu = new \app\admin\module\Menu();
        $module_info = $menu->module_deal($request);
        $column_list = $menu->catalog_index();
        $this->assign('request',$request);
        $this->assign('columnlist',$column_list);
        $this->assign('moduinfo',$module_info);
        return $this->fetch();
    }

    //模块添加，修改,提交地址
    public function modupost(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('modulemanage',$request['action'])==false){
            $this->NoPurview();
        }
        if (empty($request['chrcode'])
                || empty($request['chrname'])
                || empty($request['codecatalog'])
                || empty($request['operation'])
                || empty($request['intsn'])
            )
        {
            $this->error('前面带*号的为必填项');
        }else{
            $menu = new \app\admin\module\Menu();
            $bool = $menu->module_post($request);
            if($bool == '模块代号已存在'){
                $this->error($bool);
            }elseif($bool == true){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error('操作失败');
            }
        }
    }

    //资源添加，修改，跳转页面
    public function resourcedeal(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('resourcemanage',$request['action'])==false){
            $this->NoPurview();
        }
        $menu = new \app\admin\module\Menu();
        if(array_key_exists('id',$request) == false){
            $request['id'] = '';
        }
        $resource_info = $menu->resource_deal($request);
        $this->assign('request',$request);
        $this->assign('resourceinfo',$resource_info);
        return $this->fetch();
    }

    //资源提交的数据地址
    public function resourcepost(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('resourcemanage',$request['action'])==false){
            $this->NoPurview();
        }
        if (empty($request['chrcode']) || empty($request['chrname'])){
            $this->error('前面带*号的为必填项');
        }else{
            $menu = new \app\admin\module\Menu();
            $bool = $menu->resource_post($request);
            if($bool !== false){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->success('操作失败');
            }
        }
    }

    //资源：操作列表
    public function operatelist(){
        $request = Request::instance()->param();
        /*if($this->CMS->CheckPurview('operatemanage','view')==false){
            $this->NoPurview();
        }*/
        $menu = new \app\admin\module\Menu();
        $arr = $menu->operate_list($request);
        $operate_list = $arr['data'];
        $page = $arr['pager'];
        $this->assign('page',$page);
        $this->assign('code',$request);
        $this->assign('operatelist',$operate_list);
        return $this->fetch();
    }

    //操作添加，修改跳转页面
    public function operatedeal(){
        $request = Request::instance()->param();
        /*if($this->CMS->CheckPurview('operatemanage',$request['action'])==false){
            $this->NoPurview();
        }*/
        $menu = new \app\admin\module\Menu();
        $operate_info = $menu->operate_deal($request);
        //$this->assign('operateinfo',$request);
        $this->assign('operateinfo',$operate_info);
        return $this->fetch();
    }

    //操作，添加，修改提交页面
    public function operatepost(){
        $request = Request::instance()->param();

        /*if($this->CMS->CheckPurview('operatemanage',$request['action'])==false){
            $this->NoPurview();
        }*/
        if (empty($request['chrcode']) || empty($request['chrname'])) {
            $this->error('前面带*号的为必填项');
        }else{
            $menu = new \app\admin\module\Menu();
            $bool = $menu->operate_post($request);
            if($bool !== false){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->success('操作失败');
            }
        }

    }











}