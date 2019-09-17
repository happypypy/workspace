<?php
/*
 * 新预约功能
 * @Descripttion:   
 * @version: 1.0
 * @Author: ChenJie
 * @Date: 2019-09-03 10:53:27
 * @LastEditors: ChenJie
 * @LastEditTime: 2019-09-06 11:41:25
 */

namespace app\admin\module;
use think\Model;
use think\Page;

class Reserve extends Model{
    // 门店列表
    public function storelist($param){
        $page = isset($param['p']) ? intval($param['p']) : 0;
        $keyword = isset($param['keyword']) ? $param['keyword'] : '';
        $map = ['siteid' => session('idsite')];

        if($keyword){
            $map['store_name|store_phone|store_address'] = ['like',"%{$keyword}%"];
        }

        $totalRecord = db('reserve_store')->where($map)->count();
        $datalist = db('reserve_store')->where($map)->page($page,PAGE_SIZE)->select();
        
        $page = new Page($totalRecord,PAGE_SIZE);

        return ['page' => $page, 'datalist' => $datalist];
    }
    // 门店添加/修改
    public function storemodify($param){
        $id = isset($param['id']) ? intval($param['id']) : 0;
        $action = isset($param['action']) ? $param['action'] : 'add';
        
        $data = [
            'siteid' => session('idsite'),
            'store_name' => isset($param['store_name']) ? $param['store_name'] : '',
            'store_phone' => isset($param['store_phone']) ? $param['store_phone'] : '',
            'store_address' => isset($param['store_address']) ? $param['store_address'] : '',
            'longitude' => isset($param['longitude']) ? $param['longitude'] : '',
            'latitude' => isset($param['latitude']) ? $param['latitude'] : '',
            'time_type' => isset($param['time_type']) ? $param['time_type'] : 0,
            'business_hours' => isset($param['business_hours']) ? $param['business_hours'] : '[]',
            'reserve_number' => isset($param['reserve_number']) ? intval($param['reserve_number']) : 0,
            'business_status' => isset($param['business_status']) ? $param['business_status'] : 0,
        ];
        
        $result = false;
        if($action == 'add'){
            $result = db('reserve_store')->insert($data);
        }else{
            $result = db('reserve_store')->where('id',$id)->update($data);
        }
        return $result;
    }
    // 门店详情
    public function storedetail($id){
        return db('reserve_store')->where('id',$id)->find();
    }
    // 门店删除
    public function storedelete($ids){
        $idArray = explode(',', $ids);
        if (count($idArray) <= 0) {
            return false;
        }
        return db('reserve_store')->where('id', 'in', $idArray)->delete();
    }
    // 子产品列表
    public function subproductlist($param){
        $page = isset($param['p']) ? intval($param['p']) : 0;
        $map = ['siteid' => session('idsite')];
        
        $sub_product_name = isset($param['sub_product_name']) ? $param['sub_product_name'] : '';
        if($sub_product_name){
            $map['$sub_product_name'] = ['like',"%{$sub_product_name}%"];
        }

        $totalRecord = db('reserve_sub_product')->where($map)->count();
        $datalist = db('reserve_sub_product')->where($map)->page($page,PAGE_SIZE)->select();

        $page = new Page($totalRecord,PAGE_SIZE);

        return ['page'=>$page, 'datalist'=>$datalist];
    }
}