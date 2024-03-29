<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/2/23
 * Time: 17:43
 */

namespace app\admin\module;

use think\Page;
use think\Model;

class Advert extends Model {

    //广告列表
    public function index($data){
        if(array_key_exists('idsite',$data) == false){
            $data['idsite'] = 0;
        }
        $idsite = $data['idsite'];

        if(array_key_exists('ad_name',$data)){
            $map['ad_name'] = ['like','%'.$data['ad_name'].'%'];
            $search['ad_name'] = $data['ad_name'];
        }else{
            $search['ad_name'] = '';
        }
        if(isset($data['pid']) && (int)$data['pid']>0){
            $map['pid'] = ['eq',$data['pid']];
            $search['pid'] = $data['pid'];
        }else{
            $search['pid'] = '';
        }

        $map['idsite'] = $idsite;
        if (isset($map)){
            $count = db('ad')->where($map)->count();
            $page = new Page($count,PAGE_SIZE);
            $adv_list = db('ad')->where($map)->limit($page->firstRow.','.$page->pageSize)->order('orderby asc')->select();
        }else{
            $count = db('ad')->count();
            $page = new Page($count,PAGE_SIZE);
            $adv_list = db('ad')->limit($page->firstRow.','.$page->pageSize)->order('orderby asc')->select();
        }

        foreach ($adv_list as $key=>$value){
            $position_info = db('ad_position')->where('position_id='.$value['pid'])->field('position_id,position_name')->find();
            $adv_list[$key]['position_name'] = $position_info['position_name'];
        }

        $ad_position = db('ad_position')->order('position_id asc')->field('position_id,position_name')->select();
        $result['page'] = $page;
        $result['search'] = $search;
        $result['data'] = $adv_list;
        $result['ad_position'] = $ad_position;
        $result['idsite'] = $idsite;
        return $result;
    }

    //广告添加,编辑跳转
    public function adv_deal($data){
        if($data['action'] == 'add'){
            $ad_info['pid'] = '';
            $ad_info['media_type'] = '';
            $ad_info['ad_name'] = '';
            $ad_info['en_ad_name'] = '';
            $ad_info['tc_ad_name'] = '';
            $ad_info['ad_link'] = '';
            $ad_info['ad_code'] = '';
            $ad_info['start_time'] = '';
            $ad_info['end_time'] = '';
            $ad_info['link_man'] = '';
            $ad_info['en_link_man'] = '';
            $ad_info['tc_link_man'] = '';
            $ad_info['link_email'] = '';
            $ad_info['link_phone'] = '';
            $ad_info['click_count'] = '';
            $ad_info['enabled'] = 1;
            $ad_info['orderby'] = '';
            $ad_info['target'] = 1;
            $ad_info['bgcolor'] = '';
            $ad_info['idsite'] = $data['idsite'];
        }else{
            $ad_info = db('ad')->where('ad_id='.$data['id'])->find();
        }
        $ad_position = db('ad_position')->field('position_id,position_name,ad_width,ad_height')->order('position_id asc')->select();
        $ad_info['action'] = $data['action'];
        $result['ad_position'] = $ad_position;
        $result['ad_info'] = $ad_info;
        return $result;
    }

    //广告提交
    public function adv_post($data){
        isset($data['pid'])?$data_arr['pid'] = $data['pid']:$data_arr['pid'] = 0;


        if( isset($data['media_type'])) $data_arr['media_type'] = $data['media_type'];
        if( isset($data['ad_name'])) $data_arr['ad_name'] = $data['ad_name'];
        if( isset($data['en_ad_name'])) $data_arr['en_ad_name'] = $data['en_ad_name'];
        if( isset($data['tc_ad_name'])) $data_arr['tc_ad_name'] = $data['tc_ad_name'];
        if( isset($data['ad_link'])) $data_arr['ad_link'] = $data['ad_link'];
        if( isset($data['ad_code'])) $data_arr['ad_code'] = $data['ad_code'];
        if( !empty($data['start_time'])) $data_arr['start_time'] = $data['start_time'];
        if( !empty($data['end_time'])) $data_arr['end_time'] = $data['end_time'];
        if( isset($data['orderby'])) $data_arr['orderby'] = $data['orderby'];
        if( isset($data['idsite'])) $data_arr['idsite'] = $data['idsite'];
        if( isset($data['enabled'])) $data_arr['enabled'] = $data['enabled'];
        if( isset($data['target'])) $data_arr['target'] = $data['target'];

        if($data['action'] == 'add'){
            $bool = db('ad')->data($data_arr)->insert();
        }else{
            $bool = db('ad')->where('ad_id',$data['ad_id'])->update($data_arr);
        }
        return $bool;
    }

    //广告删除
    public function adv_del($data){
        if(strstr($data['id'],',')){
            $id = explode(',',$data['id']);
            for ($i=0;$i<count($id);$i++){
                $bool = db('ad')->where('ad_id',$id[$i])->delete();
            }
        }else{
            $bool = db('ad')->where('ad_id',$data['id'])->delete();
        }
        return $bool;
    }

    //广告位置列表页
    public function adv_position($data){
        if(array_key_exists('idsite',$data) == false){
            $data['idsite'] = 0;
        }
        $idsite = $data['idsite'];
        $count = db('ad_position')->where('idsite='.$idsite)->count();
        $page = new Page($count,PAGE_SIZE);
        $ad_position = db('ad_position')->where('idsite='.$idsite)->limit($page->firstRow.','.$page->pageSize)->order('position_id desc')->select();
        $result['page'] = $page;
        $result['data'] = $ad_position;
        $result['idsite'] = $data['idsite'];
        return $result;

    }

    //广告位置添加,编辑跳转
    public function adv_position_deal($data){

        if($data['action'] == 'add'){
            $position_info['position_name'] = '';
            $position_info['ad_width'] = '';
            $position_info['ad_height'] = '';
            $position_info['position_desc'] = '';
            $position_info['position_style'] = '';
            $position_info['is_open'] = 1;
            $position_info['idsite'] = $data['idsite'];
        }else{
            $position_info = db('ad_position')->where('position_id='.$data['id'])->find();
        }
        $position_info['action'] = $data['action'];

        return $position_info;

    }

    //广告位提交地址
    public function adv_position_post($data){
        $data_arr['position_name'] = $data['position_name'];
        $data_arr['ad_width'] = $data['ad_width'];
        $data_arr['ad_height'] = $data['ad_height'];
        $data_arr['position_desc'] = $data['position_desc'];
        $data_arr['is_open'] = $data['is_open'];
        $data_arr['idsite'] = $data['idsite'];

        if($data['action'] == 'add'){
            $bool = db('ad_position')->data($data_arr)->insert();
        }else{
            $bool = db('ad_position')->where('position_id',$data['position_id'])->update($data_arr);
        }
        return $bool;
    }

    //广告位置删除
    public function adv_position_del($data){
        if(strstr($data['id'],',')){
            $id = explode(',',$data['id']);
            for ($i=0;$i<count($id);$i++){
                $bool = db('ad_position')->where('position_id',$id[$i])->delete();
            }
        }else{
            $bool = db('ad_position')->where('position_id',$data['id'])->delete();
        }
        return $bool;

    }

}