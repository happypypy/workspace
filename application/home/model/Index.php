<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/3
 * Time: 10:59
 */

namespace app\home\model;
use think\Model;

class Index extends Model {

    //获得商品分类列表
    public function index(){

        //多有商品分类
        $goods_category = db('goods_category')->select();
        //热门商品分类
        $hot_category = db('goods_category')->limit(6)->select();
        return $goods_category;
    }

    //获得头部的栏目列表
    public function navigation_list(){
        $navigation_list = db('navigation')->select();
        return $navigation_list;
    }
}