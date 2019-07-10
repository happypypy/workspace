<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/21
 * Time: 15:03
 */

namespace app\admin\module;
use think\Model;
use think\Page;
use think\Db;
use think\Session;

class Marketingpackage extends Model{

    // 营销包管理
    public function index($data){
        $map = [];
        $marketing_package_code = $data['marketing_package_code'];
        $marketing_package_name = $data['marketing_package_name'];

        if($marketing_package_code){
            $map['marketing_package_code'] = $marketing_package_code;
        }

        if($marketing_package_name){
            $map['marketing_package_name'] = ['like', '%'.$marketing_package_name.'%'];
        }
        
        $total_record = db('marketing_package')->where($map)->count();
        $resutl = db('marketing_package')->where($map)->page($data['p'],PAGE_SIZE)->order('idorder asc')->select();

        $page = new Page($total_record,PAGE_SIZE);
        return ['data' => $resutl, 'page' => $page];
    }

    // 营销包添加或修改
    public function modi($postData){
        $action = isset($postData['action']) ? $postData['action'] : '';
        $id = isset($postData['id']) ? intval($postData['id']) : 0; 

        $data = [
            'marketing_package_name' => isset($postData['marketing_package_name']) ? $postData['marketing_package_name'] : '',
            'marketing_package_code' => isset($postData['marketing_package_code']) ? $postData['marketing_package_code'] : '',
            'marketing_package_desc' => isset($postData['marketing_package_desc']) ? $postData['marketing_package_desc'] : '',
            'idorder' => intval($postData['idorder']),
            'is_use' => intval($postData['is_use']),
        ];

        if($action == 'add'){
            $resutl = db('marketing_package')->insert($data);
        }else{
            $resutl = db('marketing_package')->where('id',$id)->update($data);
        }

        return $resutl;
    }

    // 营销包详情
    public function deal($id){
        return db('marketing_package')->where('id',$id)->find();
    }

    // 删除营销包
    public function del($id){
        $ids = explode(',',$id);
        $bool = false;
        if(count($ids) > 0){
            $bool = db('marketing_package')->where('id','in',$ids)->delete();
        }
        return $bool;
    }

    // 获取扩展模型
    public function get_extended_module(){
        return db('extended_module')->field('idmodule,chrname,textremark')->select();
    }

    // 设置套餐
    public function setmeal($id, $moduleids){
        return db('marketing_package')->where('id', $id)->setField('module_id', $moduleids);
    }

    // 获取我的营销包
    public function get_my_marketing_package($data){
        $map = ['is_use' => 1];
        $marketing_package_code = $data['marketing_package_code'];
        $marketing_package_name = $data['marketing_package_name'];
        
        if($marketing_package_code){
            $map['marketing_package_code'] = $marketing_package_code;
        }
        
        if($marketing_package_name){
            $map['marketing_package_name'] = ['like', '%'.$marketing_package_name.'%'];
        }
        
        $total_record = db('marketing_package')->where($map)->count();
        $resutl = db('marketing_package')->where($map)->page($data['p'],PAGE_SIZE)->order('idorder asc')->select();
        foreach($resutl as $key=>$value){
            $marketing_package_record = db('marketing_package_record')->where(['siteid'=>$data['id'],'marketing_package_id'=>$value['id']])->find();
            if($marketing_package_record){
                $is_use = true;
            }else{
                $is_use = false;
            }
            $resutl[$key]['is_use'] = $is_use;
        }

        $page = new Page($total_record,PAGE_SIZE);
        return ['data' => $resutl, 'page' => $page];
    }
}