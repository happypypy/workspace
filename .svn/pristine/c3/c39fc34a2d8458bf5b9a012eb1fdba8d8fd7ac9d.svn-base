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

class Waiter extends Model {
    protected  $siteid=0;
    function __construct($idStie){
        $this->siteid=$idStie;
        parent::__construct();
    }
    //客服列表
    public function index($data){

        $map['idsite'] = $this->siteid;
        $result = db('waiter')->where($map)->order('id asc')->select();

        $result['data'] = $result;
        return $result;
    }

    //客服添加
    public function modi($data){
        $result=[];
        if($data['action'] == 'add'){
            $result['id'] = '';
            $result['username'] = '';
            $result['userimg'] = '';
            $result['telephone'] = '';
            $result['rqcode'] = '';
            $result['idsite'] = $this->siteid;
        }else{
            $result = db('waiter')->where(array('idsite'=>$this->siteid, 'id'=>$data['id']))->find();
        }
        $result['action']=$data['action'];
        return $result;
    }

    //客服提交
    public function modi_post($data){
        if( isset($data['username'])) $data_arr['username'] = $data['username'];
        if( isset($data['userimg'])) $data_arr['userimg'] = $data['userimg'];
        if( isset($data['telephone'])) $data_arr['telephone'] = $data['telephone'];
        if( isset($data['rqcode'])) $data_arr['rqcode'] = $data['rqcode'];
        $data_arr['idsite']=$this->siteid;


        if($data['action'] == 'add'){
            $bool = db('waiter')->data($data_arr)->insert();
        }else{
            $bool = db('waiter')->where(array('idsite'=>$this->siteid, 'id'=>$data['id']))->update($data_arr);
        }
        return $bool;
    }

    //客服删除
    public function del($data){
        if(strstr($data['id'],',')){
            $id = explode(',',$data['id']);
            for ($i=0;$i<count($id);$i++){
                $bool = db('waiter')->where(array('idsite'=>$this->siteid, 'id'=>$id[$i]))->delete();
            }
        }else{
            $bool = db('waiter')->where(array('idsite'=>$this->siteid, 'id'=>$data['id']))->delete();
        }
        return $bool;
    }
    public function visit($data){

        $map['idsite'] = $this->siteid;
        $count =db('waiter_visit')->where($map)->group('openid')->count();
        $page = new Page($count,PAGE_SIZE);
        $result1=db('waiter_visit')->field(" '' as idmember, openid,name,name as nickname,COUNT(openid) as count,max(createtime) as createtime")->where($map)->group('openid')->order('createtime')->limit($page->firstRow.','.$page->pageSize) ->select();

        $_arr=array();
        foreach ($result1 as $k=>$vo)
        {
            if(!empty($vo['openid']))
            {
                $_arr[]=$vo['openid'];
            }
        }

        $user_arr=[];
        if(!empty($_arr))
        {
            $map1['openid']=array('in',$_arr);
            $result2=db('member')->field('idmember,openid,chrname,nickname')->where($map1)->select();
            if($result2)
            {
                foreach ($result2 as $k=>$vo)
                {
                    $user_arr[$vo['openid']]=$vo;
                }
            }
        }

        foreach ($result1 as $k=>$vo)
        {
            if(!empty($vo['openid']))
            {
                if(array_key_exists($vo['openid'],$user_arr))
                {
                    $result1[$k]['idmember']=$user_arr[$vo['openid']]['idmember'];
                    $result1[$k]['name']=$user_arr[$vo['openid']]['chrname'];
                    $result1[$k]['nickname']=$user_arr[$vo['openid']]['nickname'];
                }
            }
        }


        $result = array();
        $result['pager'] = $page;
        $result['data'] = $result1;
        return $result;
    }
}