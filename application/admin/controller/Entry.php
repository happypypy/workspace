<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/13
 * Time: 14:23
 */

namespace app\admin\controller;
use think\Request;

class Entry extends Base {

    //报名列表
    public function index(){
        if($this->CMS->CheckPurview('entrymanage','view') == false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();

        $obj = new \app\admin\module\Entry();
        $result = $obj->index($request);
        $entry_list = $result['data'];
        $page = $result['page'];
        $search = $result['search'];
        $search_field = $result['entry_field'];
        $this->assign('page',$page);
        $this->assign('search',$search);
        $this->assign('entry_list',$entry_list);
        $this->assign('entry_field',$search_field);
        $this->assign('idsite',session('idsite'));
        return $this->fetch();
    }

    //查看订单详情
    public function entryview(){

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Entry();
        $result = $obj->entry_view($request);
        $entry_info = $result['entry_info'];
        $entry_field = $result['entry_field'];
        //$user_info = $result['user_info'];
        $this->assign('entry_info',$entry_info);
        $this->assign('entry_field',$entry_field);
        return $this->fetch();
    }

    //用户详情

    public function userinfo(){
        $request = Request::instance()->param();
        $user_info = db('member')->where('idmember='.$request['userid'])->find(); //会员信息
        $member_model = db('model')->where('idsite='.session('idsite').' and isusing = 1')->find(); //会员模型
        if(empty($member_model)){
            $member_model = db('model')->where('idsite=0 and isusing = 1 and modeltype = 3')->find();
        }
        $member_field = db('modelfield')->where('idmodel='.$member_model['idmodel'].' and isusing = 1')->select();
        $entry_info = db('entry')->where('id='.$request['entryid'])->find();//报名信息
        $this->assign('member_field',$member_field);
        $this->assign('user_info',$user_info);
        $this->assign('entry_info',$entry_info);
        return $this->fetch();
    }
}