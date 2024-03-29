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

class Template extends Model {

    public function index(){

        $count = db('template')->group('cid')->count();
        $page = new Page($count,21);
        $template_list = db('template')->limit($page->firstRow.','.$page->pageSize)->select();
//        dump($template_list);
        $tmp_list=[];
        foreach($template_list as $k=>$v){
            if(substr($v['dirname'],0,strrpos($v['dirname'],'_')) == ''){
                $tmp_list[$v['dirname']][]=$v;
            }else{
                $tmp_list[substr($v['dirname'],0,strrpos($v['dirname'],'_'))][]=$v;
            }
        }
//        dump($tmp_list);
        $result['page'] = $page;
        $result['data'] = $tmp_list;
        return $result;
    }
}