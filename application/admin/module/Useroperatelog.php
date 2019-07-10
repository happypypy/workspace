<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/2/28
 * Time: 14:46
 */

namespace app\admin\module;


use think\Model;
use think\Page;

class Useroperatelog extends Model {
    // 日志列表
    public function index($search){
        $module_name = isset($search['module_name']) ? $search['module_name'] : '';
        $keyword = isset($search['keyword']) ? $search['keyword'] : '';
        $operate_type = isset($search['operate_type']) ? $search['operate_type'] : '';
        $operate_time = isset($search['operate_time']) ? $search['operate_time'] : '';
        $page = isset($search['p']) ? intval($search['p']) : 1;
        $map = ['siteid' => session('idsite')];

        // 模块名称
        if($module_name){
            $module = explode(",",$module_name);
            $module_type = $module[0];
            $module_id = $module[1];
            if($module_type == 'catalogid'){
                $map['column_id'] = $module_id;
            }else if($module_type == 'moduleid'){
                $map['module_id'] = $module_id;
            }else if($module_type == 'nodeid'){
                $map['node_id'] = $module_id;
            }
        }
        // 操作类型
        if($operate_type){
            $map["operate_type"] = $operate_type;
        }

        // 操作时间
        if($operate_time){
            $starttime = strtotime($operate_time." 00:00");
            $endtime = strtotime($operate_time." 23:59");
            $map["operate_time"] = ["between",[$starttime,$endtime]];
        }

        // 关键词
        if($keyword){
            $map["explain"] = ['like', '%'.$keyword.'%'];
        }

        $totalRecord = db('site_operate_log')->where($map)->count();
        $datalist = db('site_operate_log')->where($map)->page($page,PAGE_SIZE)->select();

        $page = new PAGE($totalRecord,PAGE_SIZE);

        return ['page'=>$page,'datalist'=>$datalist];
    }
    // 获取模块列表
    public function getModuleList(){
        $moduleList = db('catalog')->field("idcatalog,chrname,chrcode")->where('intflag',2)->select();
        // 获取二级分类
        foreach($moduleList as $key=>$value){
            $value['second'] = db('module')
                ->field("idmodule,chrname,codecatalog,chrcode")
                ->where([
                    'codecatalog'=>$value['chrcode'],
                    'idsite' => ["in",[0,session("idsite")]]
                ])
                ->order('intsn asc,idmodule asc')
                ->select();

            // 获取三级分类
            foreach($value['second'] as $key2=>$value2){
                if($value2['chrcode'] == 'activity'){
                    $value2['three'] = db('node')->field("nodeid,nodename")->where(['idsite'=>session("idsite"), 'nodetype'=>2])->select();
                }
                if($value2['chrcode'] == 'node'){
                    $value2['three'] = db('node')->field("nodeid,nodename")->where(['idsite'=>session("idsite"), 'nodetype'=>1])->select();
                }
                $value['second'][$key2] = $value2;
            }
            $moduleList[$key] = $value;
        }
        return $moduleList;
    }
}