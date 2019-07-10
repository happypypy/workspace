<?php


namespace app\admin\module;

use think\Model;
use think\Page;
use think\Exception;
use think\Db;

class Systemupdate extends Model{
    // 系统更新列表
    public function index($param){
        $map = [];
        $page = isset($param['page']) ? $param['page'] : 1;
        $title = isset($param['title']) ? $param['title'] : '';
        if(!empty($title)){
            $map['title'] = ['like','%'.$title.'%'];
        }
        
        $total_record = db('system_update')->count();
        $result = db('system_update')
            ->where($map)
            ->page($page,PAGE_SIZE)
            ->order('update_time desc')
            ->select();

        $page = new Page($total_record,PAGE_SIZE);

        return ['datalist'=>$result, 'page'=>$page];
    }
    // 系统更新新增/编辑
    public function edit($param){
        $action = isset($param['action']) ? $param['action'] : '';
        $is_open = !empty($param['is_open']) ? intval($param['is_open']) : 0;
        $data = [
            'title' => !empty($param['title']) ? $param['title'] : '',
            'content' => !empty($param['content']) ? $param['content'] : '',
            'is_open' => $is_open,
            'update_time' => time()
        ];

        $result = false;
        Db::startTrans();
        try{
            if($action == 'add'){
                $data['account_id'] = session("AccountID");
                $data['chrname'] = session("UserName");
                $data['create_time'] = time();
                db('system_update')->insert($data);
            }else{
                db('system_update')->where('id',$param['id'])->update($data);
            }
            if($is_open == 1){
                db('account')->where("1=1")->setField("is_update_remind",1);
            }
            Db::commit();
            $result = true;
        }catch(Exception $e){
            $result = false;
            Db::rollback();
        }

        return $result;
    }
    // 根据ID获取系统更新
    public function getSystemUpdateeById($id){
        return db('system_update')->where('id',$id)->find();
    }
    // 系统更新删除
    public function del($id){
        if(strstr($id, ',')){
            $ids = explode(',', $id);
            $result = db('system_update')->whereIn('id',$ids)->delete();
        }else{
            $result = db('system_update')->where('id',$id)->delete();
        }
        return $result;
    }
    // 系统更新单页
    public function singlepage($id){
        $map = ['is_open' => 1];
        if($id){
            $map['id'] = $id;
        }
        return db('system_update')->where($map)->order('update_time desc')->find();
    }
}