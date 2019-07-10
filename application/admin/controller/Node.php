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
 * Date:2017/08/25 */

namespace app\admin\controller;
use think\Request;
use think\Page;

class node extends Basesite {
    //节点列表
    public function index(){
        $obj = new \app\admin\module\Node();
        $request = Request::instance()->param();
        $node_list = $obj->index(1);
        $this->assign('node',$node_list);
        $this->assign('nodeid',$request['nodeid']);
        return $this->fetch();
        }

    public function getnode()
    {
        $arr=[];
        $obj = new \app\admin\module\Node();
        $request = Request::instance()->param();

        if(empty($request['id']))
        {
            $pid=0;
        }
        else
        {
            $pid=$request['id'];
        }
        $node_list = $obj->getNodeList($pid,1);

        foreach ($node_list as $key=>$value)
        {
            $arrTmp=[];
            $arrTmp['id']=$value['nodeid'];
            $arrTmp['text']=$value['nodename'];
            $arrTmp['state']=$value['child']==0?"open":"closed";
            $arr[]=$arrTmp;
        }

        $arr[] = array("id"=>"0","text"=>"回收站","state"=>"open");

        exit(json_encode($arr));
    }


    //内容列表
    public function contentlist(){
        if($this->CMS->CheckPurview('contentmanage','view')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
//        var_dump($request);exit;
        $obj = new \app\admin\module\Node();
        $result = $obj->content_list($request);
        if($request["nodeid"]==0){
            $result['modelfield'][0] =array("fieldalias"=>"栏目名称","issearch"=>0,"isdisplayonlist"=>1,"fieldname"=>"nodename");
            $result['modelfield'][1] =array("fieldalias"=>"标题","issearch"=>0,"isdisplayonlist"=>1,"fieldname"=>"title");
            $node_list = $obj->index(1);
            $format_node_list = array();
            foreach ($node_list as $node){
                $format_node_list[$node["nodeid"]] = $node["nodename"];
            }

            foreach ($result["list"] as &$value){
                if(isset($format_node_list[$value["nodeid"]])) {
                    $value["nodename"] = $format_node_list[$value["nodeid"]];
                }else{
                    $value["nodename"] =  "";
                }
            }
        }

        $list = $result['list'];
        $result['list'] = $list;
        $ModelField = $result['modelfield'];
        $search = $result['search'];
        $show = $result['page'];

        $isonepage=false;

        $result_node=$obj->getOneNode($request['nodeid']);
        if($result_node && $list)
        {
            if($result_node['isonepage']==1)
            {$isonepage=true;}

        }

        $this->assign('rooturl',ROOTURL);// 内容
        $this->assign('isonepage',$isonepage);// 内容
        $this->assign('list',$list);// 内容
        $this->assign('nodeid',$request['nodeid']);
        $this->assign('sitecode',getSiteCode(session('idsite')));
        $this->assign('search',$search);
        $this->assign('modelfield',$ModelField);// 内容字段
//        var_dump($ModelField);exit;
        $this->assign('page',$show);// 赋值分页输出
        return $this->fetch();
    }

    //添加，修改内容
    public function contentdeal(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('contentmanage',$request['action'])==false){
            $this->NoPurview();
        }
        $obj = new \app\admin\module\Node();
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
        if($this->CMS->CheckPurview('contentmanage','del')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $contentid = $request['id'];
        $nodeid = $request['nodeid'];
        $obj = new \app\admin\module\Node();
        $bool = $obj->content_del($contentid,$nodeid);
        if($bool){
            return 1;
        }else{
            return 2;
        }
    }

    //添加，修改内容提交地址
    public function contentpost(){
        $request = Request::instance()->param();

        if($this->CMS->CheckPurview('contentmanage',$request['action'])==false){
            $this->NoPurview();
        }
        $nodeid = $request['nodeid'];
        $obj = new \app\admin\module\Node();
        $bool = $obj->content_post($request);
        if($bool !== false){
            $this->success('操作成功',url('/admin/node/contentlist/','nodeid='.$nodeid));
        }else{
            $this->error("操作失败");
        }
    }

    // 弹框控件
    public function popup(){
        $request = Request::instance()->param();
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



    public function visitlist()
    {
        if($this->CMS->CheckPurview('contentmanage')==false){
            $this->error('无权限');
        }
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Node();
        $result= $obj->visitlist($data);
        $this->assign('data',$result);
        $this->assign('dataid',$data['dataid']);

        $this->assign('sitecode',getSiteCode(session('idsite')));
        return $this->fetch();
    }

    public function sendmsg()
    {
        if($this->CMS->CheckPurview('contentmanage')==false){
            $this->error('无权限');
        }
        $data = Request::instance()->param();

        $obj = new \app\admin\module\Node();
        if (Request::instance()->isPost()) {
            $key=getNumber();
            $data['key']=$key;
            $data['title']="";
            $data['inttype']=1;
            $data['inttype1']=1;
            $data['username']=session("UserName");
            $data['userid']=session("AccountID");
            $obj1 = new \app\admin\module\activity(session('idsite'));
            $result = $obj1->sendmsg($data);
            if ($result)
            {
                $result1=send_msg($key,getSiteCode(session('idsite')));
                exit(json_encode($result1));
            }
            else
                exit(json_encode(array("state" => 0, "key" => "", "msg" => "数据更新失败")));
        }

        $info=db('content')->where(array('contentid'=>$data['dataid']))->find();;

        $chrurl=ROOTURL."/".getSiteCode(session('idsite'))."/content/".$data["dataid"];
        $chrname="";
        $activitytime="";
        if($info)
        {
            $chrname=$info["title"];
            $activitytime="";
        }

        $this->assign('dataid',$data["dataid"]);
        $this->assign('sitecode',getSiteCode(session('idsite')));
        $this->assign('chrurl',$chrurl);
        $this->assign('chrname',$chrname);
        $this->assign('activitytime',$activitytime);
        return $this->fetch();
    }

    public function recoverychecked(){
        if($this->CMS->CheckPurview('contentmanage','del')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $contentid = $request['id'];
        $obj = new \app\admin\module\Node();
        $bool = $obj->recoverychecked($contentid);
        if($bool){
            return 1;
        }else{
            return 2;
        }
    }

}