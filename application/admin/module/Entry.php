<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/13
 * Time: 15:04
 */

namespace app\admin\module;
use think\Model;
use think\Page;

class Entry extends Model {

    //报名列表
    public function index($data){
        if(array_key_exists('state',$data) && !empty($data['state'])){
            $map['state'] = ['eq',$data['state']];
        }
        //找出对应的报名模型
        $entry_info = db('model')->where('idsite='.session('idsite').' and modeltype = 5')->find();
        if(empty($entry_info)){
            $entry_info = db('model')->where('idsite=0 and modeltype = 5')->find();
        }
        //模型字段列表
        $map1 = [];
        $entry_field_list = db('modelfield')->where('idmodel='.$entry_info['idmodel'])->select();
        foreach ($entry_field_list as $key=>$value){
            if($value['issearch'] == 1){
                if(array_key_exists($value['fieldname'],$data)&&!empty($data[$value['fieldname']])){
                    $map[$value['fieldname']] = ['like',$data[$value['fieldname']]];
                    $map1[$value['fieldname']] = $data[$value['fieldname']];
                }else{
                    $map1[$value['fieldname']] = '';
                }
            }
        }
        $map['idsite'] = ['eq',session('idsite')];
        //获取报名列表
        $count = db('entry')->where($map)->count();

        if(in_array('state',$map1)==false)
            $map1['state'] =empty($data['state'])?0:$data['state'];

        $page = new Page($count,PAGE_SIZE);
        $entry_list = db('entry')->where($map)->limit($page->firstRow.','.$page->pageSize)->select();
        $result['data'] = $entry_list;
        $result['page'] = $page;
        $result['search'] = $map1;
        $result['entry_field'] = $entry_field_list;
        return $result;

    }

    //查看报名订单详情
    public function entry_view($data){

        $entry_model = db('model')->where('idsite='.session('idsite').' and modeltype=5 and isusing=1')->find();
        if(empty($entry_model)){
            $entry_model = db('model')->where('idsite=0 and modeltype=5 and isusing=1')->find();
        }
        $entry_field = db('modelfield')->where('idmodel='.$entry_model['idmodel'].' and isusing = 1')->select();//报名模型启用的手游字段

        $entry_info = db('entry')->where('id='.$data['entryid'])->find();
        //$user_info = db('member')->where('idmember='.$data['userid'])->find();
        $result['entry_info'] = $entry_info;
        //$result['user_info'] = $user_info;
        $result['entry_field'] = $entry_field;
        return $result;
    }
















}