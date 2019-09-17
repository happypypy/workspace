<?php

namespace app\home\controller;
use think\Request;
use think\Session;

class Activities extends BaseAuth {

    //我参与的活动列表
    public function plist()
    {
        $this->objAct=new \app\home\model\Activities($this->siteid);
       // $request = Request::instance()->param();
        $result= $this->objAct->SearchActivities(0,$this->userinfo['openid']);

        //获得栏目列表模版路径
        $roottpl = 'template/modules/';
        $url =  $roottpl.'activities/plist.html';

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('data',$result);
        return $this->fetch($url);
    }

    //我参与的活动详情
    public function pinfo()
    {
        $request = Request::instance()->param();

        $act_id=$request['actid'];
        $act_info=[];
        $result_teach=[];
        $result_Comment=[];
        $result_Summary=[];

        $this->objAct=new \app\home\model\Activities($this->siteid);
        $result_act= $this->objAct->SearchActivities($act_id);

        if($result_act)
        {
            $act_info=$result_act[0];
            $result_teach= $this->objAct->Searchteacher($act_id);
            $result_Comment= $this->objAct->SearchStudentComment(0,$act_id);
            $result_Summary= $this->objAct->SearchDaySummary($act_id);
        }
        //查询相册信息
        $album_info = db('album')->where(['activity_id'=>$act_id,'site_id'=>$this->siteid])->find();
        //获得栏目列表模版路径
        $roottpl = 'template/modules/';
        $url =  $roottpl.'activities/pinfo.html';
        $this->assign('roottpl','/'.$roottpl);
        $this->assign('teacher',$result_teach);
        $this->assign('Comment',$result_Comment);
        $this->assign('Summary',$result_Summary);
        $this->assign('info',$act_info);
        $this->assign('album_id',$album_info['id']?$album_info['id']:0);
        return $this->fetch($url);
    }


    //学生点评
    public function stucomm()
    {
        $request = Request::instance()->param();
        $act_id=$request['actid'];
        $struid=$request['struid'];
        $result= $this->objAct->SearchStudentComment(0,$act_id,$struid);

        //获得栏目列表模版路径
        $roottpl = 'template/modules/';
        $url =  $roottpl.'/activities/stucomm.html';
        $this->assign('roottpl','/'.$roottpl);
        $this->assign('data',$result);
        return $this->fetch($url);
    }
    //学生点评详情
    public function stucommdetail()
    {
        $request = Request::instance()->param();
        $id=$request['id'];
        $result= $this->objAct->SearchStudentComment($id);

        //获得栏目列表模版路径
        $roottpl = 'template/modules/';
        $url =  $roottpl.'/activities/stucommdetail.html';
        $this->assign('roottpl','/'.$roottpl);
        $this->assign('data',$result);
        return $this->fetch($url);
    }

    //每日总结
    public function daysummary()
    {
        $request = Request::instance()->param();
        $act_id=$request['actid'];
        $result= $this->objAct->SearchDaySummary($act_id);
        //SearchActivities

        //获得栏目列表模版路径
        $roottpl = 'template/modules/';
        $url =  $roottpl.'/activities/daysummary.html';
        $this->assign('roottpl','/'.$roottpl);
        $this->assign('data',$result);
        return $this->fetch($url);
    }
    //每日总结详情
    public function daysummarydetail()
    {
        $request = Request::instance()->param();
        $id=$request['id'];
        $result= $this->objAct->SearchDaySummary(0,$id);
        //SearchActivities

        //获得栏目列表模版路径
        $roottpl = 'template/modules/';
        $url =  $roottpl.'/activities/daysummarydetail.html';
        $this->assign('roottpl','/'.$roottpl);
        $this->assign('data',$result);
        return $this->fetch($url);
    }

    public function commentdetail()
    {
        $request = Request::instance()->param();
        $id=$request['cid'];
        $data=db('activities_stu_comment')->where(['id'=>$id])->find();
        //获得栏目列表模版路径
        $roottpl = 'template/modules/';
        $url =  $roottpl.'/activities/commentdetail.html';
        $this->assign('data',$data);
        $this->assign('roottpl','/'.$roottpl);
        return $this->fetch($url);
    }

    public function summarydetail()
    {
        $request = Request::instance()->param();
        $id=$request['suid'];
        $config=$this->wxConfig;
        $idsite=$config['id'];
        $data=db('activities_day_summary')->where(['id'=>$id])->find();
        //获得栏目列表模版路径
        $roottpl = 'template/modules/';
        $url =  $roottpl.'/activities/summarydetail.html';
        $this->assign('su_id',$id);
        $this->assign('idsite',$idsite);
        $this->assign('data',$data);
        $this->assign('roottpl','/'.$roottpl);
        return $this->fetch($url);
    }

}