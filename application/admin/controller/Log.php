<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/17
 * Time: 14:56
 */

namespace app\admin\controller;
use think\Request;

class Log extends Base {

    //日志列表
    public function index(){
        $request = Request::instance()->param();
        $log_obj = new \app\admin\module\Log($request);
        $log = $log_obj->index($request);
        $log_list = $log["list"];
        $page = $log["pager"];
//        var_dump($log_list);exit;
        $this->assign('data',$log_list);
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function configure(){

        return $this->fetch();
    }
}