<?php
/*
 * @Descripttion: 预约对象模型 
 * @version: v1.0
 * @Author: ChenJie
 * @Date: 2019-07-08 16:27:07
 * @LastEditors: ChenJie
 * @LastEditTime: 2019-07-09 14:51:42
 */

namespace app\admin\module;

use think\Model;
use think\Page;

class Subscribe extends Model{
    /**
     * 获取预约对象列表
     *
     * @param [type] $data
     * @return void
     * @author Chenjie
     * @Date 2019-07-08 16:39:08
     */
    public function index($param){
        $map = [];
        $object_name = isset($param['object_name']) ? rtrim($param['object_name']) : '';
        $category_id =  isset($param['category_id']) ? intval($param['category_id']) : 0;
        $start_time = isset($param['start_time']) ? strtotime($param['start_time']) : '';
        $end_time = isset($param['end_time']) ? strtotime($param['end_time']) : '';

        if($object_name){
            $map['object_name'] = ['like','%'.$object_name.'%'];
        }

        if($category_id){
            $map['category_id'] = $category_id;
        }

        if($start_time && $end_time){
            $map['start_time'] = ['egt', $start_time];
            $map['end_time'] = ['elt', $end_time];
        }

        $total_record = db('subscribe_object')->where($map)->count();
        $datalist = db('subscribe_object')->where($map)->page($param['p'],PAGE_SIZE)->order('create_time desc')->select();

        $page = new Page($total_record,PAGE_SIZE);

        return ['page' => $page, 'datalist' => $datalist];
    }
    /**
     * 预约对象新增/编辑
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-09 10:28:01
     */
    public function object_edit($param){
        $id = isset($param['id']) ? intval($param['id']) : 0;
        $data = [
            'object_name' => isset($param['object_name']) ? rtrim($param['object_name']) : '',
            'category_id' => isset($param['category_id']) ? intval($param['category_id']) : 0,
            'category_name' => isset($param['category_name']) ? rtrim($param['category_name']) : '',
            'max_number' => isset($param['max_number']) ? intval($param['max_number']) : 0,
            'min_number' => isset($param['min_number']) ? intval($param['min_number']) : 0,
            'is_auditing' => isset($param['is_auditing']) ? intval($param['is_auditing']) : 0,
            'start_time' => isset($param['start_time']) ? strtotime($param['start_time']) : 0,
            'end_time' => isset($param['end_time']) ? strtotime($param['end_time']) : 0,
            'is_enable' => isset($param['is_enable']) ? intval($param['is_enable']) : 0,
            'description' => isset($param['description']) ? rtrim($param['description']) : '',
        ];
        if($id){
            $result = db('subscribe_object')->where('id',$id)->update($data);
        }else{
            $data['siteid'] = session('idsite');
            $data['create_time'] = time();
            $result = db('subscribe_object')->insert($data);
        }
        return $result;
    }
    /**
     * 预约对象删除
     *
     * @param [type] $param
     * @return void
     * @author Chenjie
     * @Date 2019-07-09 12:01:40
     */
    public function object_delete($param){
        $result = false;
        $id = isset($param['id']) ? $param['id'] : 0;
        if($id){
            $ids = explode(',',$id);
            if(count($ids) > 0){
                $result = db('subscribe_object')->whereIn('id',$ids)->delete();
            }
        }
        return $result;
    }
    /**
     * 获取预约对象分类
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-08 16:38:50
     */
    public function get_work_content(){
        return db('work_content')->field('id,name')->where(['idsite'=>session('idsite'),'bookcode'=>'yyfl'])->select();
    }
    /**
     * 根据ID获取预约对象
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-09 11:21:08
     */
    public function get_subscribe_object_by_id($id){
        return db('subscribe_object')->where('id',$id)->find();
    }
}