<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/30
 * Time: 17:48
 */

namespace app\admin\controller;
use think\Request;

class Role extends Base {

    //角色帐号列表
    public function index(){
        if($this->CMS->CheckPurview('rolemanage','view')==false){
            $this->NoPurview();
        }
        $obj = new \app\admin\module\Role();
        $arr = $obj->index();
        $role_list = $arr['data'];
        $page = $arr['pager'];
        $this->assign('page',$page);
        $this->assign('rolelist',$role_list);
        return $this->fetch();
    }

    //角色添加，修改，查看跳转页面
    public function roledeal(){
        $data = Request::instance()->param();
        $role_obj = new \app\admin\module\Role();
        $role_info = $role_obj->role_deal($data);
        $this->assign('roleinfo',$role_info);
        return $this->fetch();
    }

    //角色添加，修改提交地址
    public function rolepost(){
        $request = Request::instance()->param();
        if(empty($request['rolename'])) {
            return $this->error('前面带*号的为必填项');
        }

        if($this->CMS->CheckPurview('rolemanage',$request['action'])==false){
            $this->NoPurview();
        }
        $role_obj = new \app\admin\module\Role();
        $bool = $role_obj->role_post($request);
        if($bool !== false){
            $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
        }else{
            $this->error('操作失败');
        }
    }

    //角色删除
    public function roledel(){
        if($this->CMS->CheckPurview('rolemanage','del')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $role_obj = new \app\admin\module\Role();
        $bool = $role_obj->role_del($request);
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    //选中删除
    public function delchecked(){
        if($this->CMS->CheckPurview('rolemanage','del')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $role_obj = new \app\admin\module\Role();
        $bool = $role_obj->del_checked($request);
        if($bool){
            return 1;
        }else{
            return 0;
        }
    }

   /* //角色权限设置
    public function rolepurview(){
        if($this->CMS->CheckPurview('purviewset','set',1)==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $role_obj = new \app\admin\module\Role();
        //$role_purview = $role_obj->role_purview($request); //拿到角色所有的模块，资源，操作
        $column_list = $role_obj->column_list();
        $module_list = $role_obj->module_list();
        $this->assign('request',$request);
        $this->assign('columnlist',$column_list);
        $this->assign('modulelist',$module_list);
        //$this->assign('rolepurview',$role_purview);
        return $this->fetch();
    }*/

    //角色权限设置
    public function columninfo(){
        if($this->CMS->CheckPurview('rolemanage','purviewset')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param(); //角色id
        $role_info = db('role')->where('idrole='.$request['roleid'])->field('rolename,idsite')->find();
        $role_obj = new \app\admin\module\Role();
        $arr = $role_obj->column_info($request);
        $column_info = $arr['module']; //栏目下的模块
        $column_name = $arr['columnname']; //栏目名称
        $role_operate = $arr['roleoperate'];
        $resource_list = $role_obj->resource_list();
        $operate_list = $role_obj->operate_list();
        $this->assign('role_info',$role_info);
        $this->assign('request',$request);
        $this->assign('columnname',$column_name);
        $this->assign('operatelist',$operate_list);
        $this->assign('roleoperate',$role_operate);
        $this->assign('resourcelist',$resource_list);
        $this->assign('columninfo',$column_info);
        return $this->fetch();
    }

    /*//点击模块设置权限
    public function moduleinfo(){
        $request = Request::instance()->param();
        $role_obj = new \app\admin\module\Role();
        $arr = $role_obj->module_info($request);
        $module_info = $arr['module'];
        $resource_list = $arr['resource'];
        $operate_list = $arr['operate'];
        $role_operate = $arr['roleoperate'];
        $this->assign('request',$request);
        $this->assign('roleoperate',$role_operate);
        $this->assign('moduleinfo',$module_info);
        $this->assign('resourcelist',$resource_list);
        $this->assign('operatelist',$operate_list);
        return $this->fetch();
    }*/

    //修改角色权限
    /*public function purviewedit(){
        $request = Request::instance()->param();

        $role_obj = new \app\admin\module\Role();
        if(array_key_exists('operate',$request)){
            $power = $request['operate'];

            foreach ($power as $key=>$value){
                if(strstr($value,'_')){
                    $arr = explode('_',$value);
                    $power[$key] = $arr;
                }
            }

            $bool = db('purview')->where('idaccount',session('AccountID'))->where('chrmodulecode',$power[0][0])->where('chrresourcecode',$power[0][1])->delete();
            foreach ($power as $ke=>$val){
                $data = [
                    'idaccount'          =>  session('AccountID'),
                    'chrmodulecode'      =>  $val[0],
                    'chrresourcecode'    =>  $val[1],
                    'chroperatecode'     =>  $val[2],
                ];
                //$bool = db('purview')->where('idaccount',session('AccountID'))->where('chrmodulecode',$data['chrmodulecode'])->where('chrresourcecode',$data['chrresourcecode'])->insert();
            }
        }else{
            $bool = db('purview')->where('idaccount',session('AccountID'))->where('chrmodulecode',$request['modulecode'])->setField('chroperatecode', '');
        }

    }*/

    //点击栏目修改角色操作权限
    public function purviewpost(){
        if($this->CMS->CheckPurview('rolemanage','purviewset')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $role_obj = new \app\admin\module\Role();
        $bool = $role_obj->operate_edit($request);
        if($bool !== false){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
}