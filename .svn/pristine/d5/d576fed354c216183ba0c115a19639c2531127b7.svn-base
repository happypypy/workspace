<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/17
 * Time: 14:56
 */

namespace app\admin\module;
use think\Model;
use think\Page;

class Log extends Model{

    //日志列表
    public function index($data){

        $where_arr = array();

        $count = db("account_log")->where($where_arr)->count();
        $page = new Page($count,PAGE_SIZE);
        $log_list = db("account_log")->where($where_arr)->limit($page->firstRow.','.$page->pageSize)->select();

        foreach ($log_list as &$log){
            $request = $log["request"];
            $tmpArr = json_decode($request,true);
            $params = $tmpArr["params"];
            $method = $tmpArr["method"];
            $log["params"] = htmlspecialchars(print_r($params,true));
            $log["method"] = $method;
            unset($log["request"]);
        }
        $return = array();
        $return["list"] = $log_list;
        $return['pager'] = $page;
        return $return;
    }

    /**
     * 配置
     */
    public function configure(){

    }
}