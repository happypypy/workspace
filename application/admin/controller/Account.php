<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/4
 * Time: 17:11
 */

namespace app\admin\controller;
use think\Request;

class Account  extends Base {
    //帐号列表
    public function accountlist(){
        if($this->CMS->CheckPurview('accountmanage','view')==false){
            $this->NoPurview();
        }
        $obj = new \app\admin\module\Account;
        //点击帐号管理的默认页
        if(Request::instance()->param() == false){
            $data = [];
            $data['chraccount'] = '';
            $data['chrname'] = '';
        }else{
            //搜索分页
            $data = Request::instance()->param();
            if(array_key_exists('chrname',$data) == false){
                $data['chrname'] = '';
            }
            if(array_key_exists('chraccount',$data) == false){
                $data['chraccount'] = '';
            }
            if(array_key_exists('p',$data) == false){
                $data['p'] = '1';
            }
        }
        $arr = $obj->index($data); //$data是一个数组 chrname,chraccount,page
        $account = $arr['data'];
        $page = $arr['pager'];
        $this->assign('page',$page);
        $this->assign('data',$data);
        $this->assign('account',$account);
        return $this->fetch();
    }

    //帐号的查看，添加，修改，跳转页面
    public function accountdeal(){
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Account();
        $account = $obj->deal($request);
        $this->assign('account',$account);
        return $this->fetch();
    }

    //帐号删除
    public function del(){
        if($this->CMS->CheckPurview('accountmanage','del')==false){
            $this->NoPurview();
        }
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Account();
        $bool = $obj->account_del($data);
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    //帐号选择删除
    public function delchecked(){
        if($this->CMS->CheckPurview('accountmanage','del')==false){
            $this->NoPurview();
        }

        $request = Request::instance()->param();
        $account_obj = new \app\admin\module\Account();
        $bool = $account_obj->del_checked($request);
        if($bool){
            return 1;
        }else{
            return 0;
        }
    }

    //用户管理提交的数据
    public function post_data(){
        $data = Request::instance()->param();
        if($data['action'] == 'add')
        {
            if($this->CMS->CheckPurview('accountmanage','add')==false)
            {
                $this->NoPurview();
            }

            if(empty($data['chraccount']))
            {
                $this->error('前面带*号的为必填项');
            }
        }else
        {
            if($this->CMS->CheckPurview('accountmanage','edit')==false)
            {
                $this->NoPurview();
            }
        }
        if(empty($data['chrname']) || empty($data['chrpassword']))
        {
            $this->error('前面带*号的为必填项');
        }

        if(!db('site_manage')->find($data['siteid']))
        {
            $this->error('站点不存在');
        }

        // $data['siteid'] = session('siteid');    总后台功能，可以为别的站点创建账号
        $obj = new \app\admin\module\Account();
        $result = $obj->post($data);
        if($result['status'] === 'success')
        {
            $this->success($result['msg'], PUBLIC_URL.'postsuccess.html');
        }else
        {
            $this->error($result['msg']);
        }
        
    }

    //帐号角色设置
    public function roleset(){
        if($this->CMS->CheckPurview('accountmanage','roleset')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $account_obj = new \app\admin\module\Account();
        $arr = $account_obj->role_set($request);
        $role_list = $arr['rolelist'];
        $account_role = $arr['accountrole'];
        $this->assign('rolelist',$role_list);
        $this->assign('request',$request);
        $this->assign('accountrole',$account_role);
        return $this->fetch();
    }

    //帐号设置角色提交地址
    public function rolesetpost(){
        if($this->CMS->CheckPurview('accountmanage','roleset')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $account_obj = new \app\admin\module\Account();
        $bool = $account_obj->role_set_post($request);
        if($bool){
            $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
        }else{
            $this->success('操作失败');
        }
    }

    public function _empty($name){
        echo "操作:".$name."不存在";
    }

}