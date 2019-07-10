<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/2
 * Time: 11:09
 */

namespace app\admin\controller;
use think\Request;
use think\Page;

class Content extends Base{

    //内容列表
    public function index(){

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Content();
        $result = $obj->index($request);
        $list = $result['list'];
        $result['list'] = $list;
        $ModelField = $result['modelfield'];
        $search = $result['search'];
        $show = $result['page'];
        $this->assign('list',$list);// 内容
        $this->assign('nodeid',$request['nodeid']);
        $this->assign('search',$search);
        $this->assign('modelfield',$ModelField);// 内容字段

        $this->assign('page',$show);// 赋值分页输出
        return $this->fetch();
    }

    //添加，修改内容
    public function contentdeal(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('accountmanage',$request['action'])==false){
            $this->NoPurview();
        }
        $obj = new \app\admin\module\Content();
        $result = $obj->content_deal($request);
        $fieldlist = $result['fieldlist'];
        $nodeid = $result['nodeid'];
        $nodename = $result['nodename'];
        $modelid = $result['modelid'];
        $action = $result['action'];
        $contentinfo = $result['contentinfo'];
        $this->assign('contentinfo',$contentinfo);
        $this->assign('fieldlist',$fieldlist);
        $this->assign('nodeid',$nodeid);
        $this->assign('contentid',$result['contentid']);
        $this->assign('nodename',$nodename);
        $this->assign('modelid',$modelid);
        $this->assign('action',$action);
        $this->assign('type',$result['type']);
        return $this->fetch();
    }

    //内容删除
    public function delchecked(){
        if($this->CMS->CheckPurview('accountmanage','del')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $contentid = $request['id'];
        $obj = new \app\admin\module\Content();
        $bool = $obj->content_del($contentid);
        if($bool){
            return 1;
        }else{
            return 2;
        }
    }

    //添加，修改内容提交地址
    public function contentpost(){
        $request = Request::instance()->param();

        if($this->CMS->CheckPurview('accountmanage',$request['action'])==false){
            $this->NoPurview();
        }
        $nodeid = $request['nodeid'];
        $obj = new \app\admin\module\Content();
        $bool = $obj->content_post($request);
        if($bool !== false){
            $this->success('操作成功',url('/admin/content/index/','nodeid='.$nodeid));
        }else{
            $this->error("操作失败");
        }
    }

    // 弹框控件
    public function popup(){
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Content();
        $obj1 = new \app\admin\module\Node();
        $nodename = $obj1->node_list();
        $this->assign('nodename',$nodename);
        return $this->fetch();
    }

    //ajax处理
    public function ajaxclick(){
        if (!empty($_REQUEST)){
            $map['title'] = array('like',"%".$_REQUEST['title']."%");
            $map['nodeid'] = $_REQUEST['nodeid'];
            $arrData= db('content')->where($map)->select();
        }else{
            $arrData = '';
        }
        return ['data',$arrData];
    }

    //检测字段是否惟一
    public function contenttest(){
        $request = Request::instance()->param();
        $value = $request['value'];
        $modelid = $request['modelid'];
        $field = $request['fieldname'];
        $action = $request['action'];

        $map[$field] = ['eq',$value];
        $map['modelid'] = ['eq',$modelid];


        //获取该模型下所有的内容
        if($action == 1){  //添加
            $count = db('content')->where($map)->count();
        }else{ //编辑
            $contentid = $request['contentid'];
            $map['contentid'] = ['<>',$contentid];
            $count = db('content')->where($map)->count();
        }
        if($count > 0){ //有重复
            return 2;
        }else{
            return 1;
        }
    }
}