<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;

class Maindata  extends Base {

    public function test()
    {
        $obj = new \app\admin\module\Maindata();
        $tmp= $obj->test();
        exit($tmp);
    }
    public function listaccount()
    {
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Maindata();
        $tmp= $obj->listAccount($data);
        exit($tmp);
    }

    //待审批活动
    public function listactivitybacklog()
    {
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Maindata();
        $tmp= $obj->listActivityBacklog($data);
        exit($tmp);
    }

    //待审不通过的活动
    public function listchecked()
    {
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Maindata();
        $tmp= $obj->listchecked($data);
        exit($tmp);
    }

    //待审回复评论
    public function listcommentre()
    {
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Maindata();
        $tmp= $obj->listCommentRe($data);
        exit($tmp);
    }

    //今日数据统计
    public function todayDataStatistics(){
        $obj = new \app\admin\module\Maindata();
        $tmp= $obj->todayDataStatistics();
        exit($tmp);
    }

    //待审核退款记录
    public function waitRefundRecord(){

        $data = Request::instance()->param();
        $obj = new \app\admin\module\Maindata();
        $tmp= $obj->waitRefundRecord($data);
        exit($tmp);
    }

    //待跟踪用户列表
    public function waitFollowClient(){
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Maindata();
        $tmp= $obj->waitFollowClient($data);
        exit($tmp);
    }
    // 系统更新列表
    public function systemUpdate(){
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Maindata();
        $tmp= $obj->systemUpdate($data);
        exit($tmp);
    }
    // 新功能提醒列表
    public function newfeatuRerecommend(){
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Maindata();
        $tmp= $obj->newfeatuRerecommend($data);
        exit($tmp);
    }
}
