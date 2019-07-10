<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/1/9
 * Time: 14:19
 */

namespace app\admin\module;
use think\Page;
use think\Model;

class Filter extends Model {

    public function index($data){

        session('idsite') ? $idsite = session('idsite') : $idsite = 0;
        $map['content'] = ['like','%'.$data['content'].'%'];
        $map['idsite'] = ['eq',$idsite];
        $count = db('filter')->where($map)->count();// 查询满足要求的总记录数
        $Page = new Page($count,PAGE_SIZE);// 实例化分页类 传入总记录数和每页显示的记录数
        $result = db('filter')->where($map)->limit($Page->firstRow.','.$Page->pageSize)->order('idorder asc,filterid desc')-> select();
        foreach ($result as $key=>$value){
            foreach ($value as $k=>$v){
                $result[$key][$k] = htmlspecialchars($v);
            }
        }
        $arr = array();
        $arr['pager'] = $Page;
        $arr['data'] = $result;
        $arr['idsite'] = $idsite;
        return $arr;
    }

    public function filter_deal($data){
        //帐号信息
        if(array_key_exists('id',$data)){
            $filter = db('filter')->where('filterid=:filterid',['filterid'=>$data['id']])->find();
        }else {
            $filter=[];
            $filter['filterid'] = '';
            $filter['content'] = '';
            $filter['replace'] = '***';
            $filter['isusing'] = 1;
            $filter['idorder'] = 1;
            $filter['idsite'] = session('idsite');
        }
        $filter['action'] = $data['action'];
        return $filter;
    }

    public function filter_del($data){
        $bool = db('filter')->where('filterid=:filterid',['filterid'=>$data['id']])->delete();
        return $bool;
    }

    //选中删除
    public function del_checked($data){
        $bool = false;
        if(strstr($data['id'],',')){
            $id = explode(',',$data['id']);
            for ($i=0;$i<count($id);$i++){
                $bool = db('filter')->where('filterid',$id[$i])->delete();
            }
        }else{
            $bool = db('filter')->where('filterid',$data['id'])->delete();
        }
        return $bool;
    }

    public function filter_post($data){
        $arr_data = [
            'content'   =>  $data['content'],
            'replace'   =>  $data['replace'],
            'isusing'   =>  $data['isusing'],
            'idorder'   =>  $data['idorder'],
            'idsite'    =>  session('idsite')
        ];
        if($data['action'] == 'add'){
            $bool = db('filter')->insert($arr_data);
        }else{
            $bool = db('filter')->where('filterid='.$data['filterid'])->update($arr_data);
        }
        return $bool;
    }


}