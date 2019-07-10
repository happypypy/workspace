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

        $count = db('template')->count();
        $page = new Page($count,10);
        $template_list = db('template')->limit($page->firstRow.','.$page->pageSize)->select();
        $result['page'] = $page;
        $result['data'] = $template_list;
        return $result;
    }
}