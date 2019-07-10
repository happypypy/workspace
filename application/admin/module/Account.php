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

class Account extends Model{

    //帐号列表
    public function index($data){


        $count = db('account')->where('chraccount LIKE :chraccount and chrname like :chrname',['chraccount'=>'%'.$data['chraccount'].'%','chrname'=>'%'.$data['chrname'].'%'])->count();// 查询满足要求的总记录数
        $Page = new Page($count,PAGE_SIZE);// 实例化分页类 传入总记录数和每页显示的记录数
        $result = db('account')->where('chraccount LIKE :chraccount and chrname like :chrname',['chraccount'=>'%'.$data['chraccount'].'%','chrname'=>'%'.$data['chrname'].'%'])->limit($Page->firstRow.','.$Page->pageSize)->order('intsn asc,idaccount desc')-> select();
        foreach ($result as $key=>$value){
            foreach ($value as $k=>$v){
                $result[$key][$k] = htmlspecialchars($v);
            }
        }
        $arr = array();
        $arr['pager'] = $Page;
        $arr['data'] = $result;
        return $arr;
    }

    //帐号的查看，添加，修改，跳转页面
    public function deal($data){
        //帐号信息
        if(array_key_exists('id',$data)){
            $account = db('account')->where('idaccount=:idaccount',['idaccount'=>$data['id']])->find();
            foreach ($account as $k=>$v){
                $account[$k] = htmlspecialchars($v);
            }

        }else {
            $account=[];
            $account['id'] = '';
            $account['chrname'] = '';
            $account['chraccount'] = '';
            $account['chrpassword'] = '';
            $account['intflag'] = 1;
            $account['intsn'] = '';
            $account['chrremark'] = '';
            $account['siteid'] = '0';
        }
        $account['action'] = $data['action'];
        return $account;
    }

    //帐号删除
    public function account_del($data){
        db('index_user_config')->where('fidaccount=:fidaccount',['fidaccount'=>$data['id']])->delete();
        db('index_user_info')->where('fidaccount=:fidaccount',['fidaccount'=>$data['id']])->delete();
        $bool = db('account')->where('idaccount=:idaccount',['idaccount'=>$data['id']])->delete();

        return $bool;
    }
    public function  login($Account,$siteid=0)
    {
       // if(empty($siteid) || $siteid==0)
       //     return db('account')->where(['chraccount'=>$Account])->find();
       // else
            return db('account')->where(['chraccount'=>$Account,'siteid'=>$siteid])->find();
    }

    public  function  getSiteID($SiteCode)
    {
        $result=db('site_manage')->where(['site_code'=>$SiteCode])->find();
        if($result)
            return $result['id'];
        return 0;
    }
    //帐号选择删除
    public function del_checked($data){
        if(strstr($data['id'],',')){
            $id = explode(',',$data['id']);
            for ($i=0;$i<count($id);$i++){
                db('index_user_config')->where('fidaccount=:fidaccount',['fidaccount'=>$id[$i]])->delete();
                db('index_user_info')->where('fidaccount=:fidaccount',['fidaccount'=>$id[$i]])->delete();
                $bool = db('account')->where('idaccount',$id[$i])->delete();
            }
        }else{
            db('index_user_config')->where('fidaccount=:fidaccount',['fidaccount'=>$data['id']])->delete();
            db('index_user_info')->where('fidaccount=:fidaccount',['fidaccount'=>$data['id']])->delete();
            $bool = db('account')->where('idaccount',$data['id'])->delete();
        }
        return $bool;
    }

    //帐号添加，修改提交的地址
    public function post($data){
        if($data['action'] == 'add'){
            $accountExist = db('account')
                ->field('idaccount')
                ->where([
                    'siteid' => $data['siteid'],
                    'chraccount' => $data['chraccount'],
                ])
                ->find();

            if($accountExist)
            {
                return ['status' => 'fail', 'msg' => '帐号已存在'];
            }
            $data['chrpassword'] = md5($data['chrpassword']);
            $bool = $this->allowField(true)->save($data);
        }else
        {
            if(isset($data['chraccount'])) unset($data['chraccount']);

            if($data['chrpassword'] == '********'){
                $bool = $this->allowField(['chrname','intflag','intsn','siteid','chrremark'])->save($data,['idaccount'=>$data['account_id']]);
            }else{
                $data['chrpassword'] = md5($data['chrpassword']);
                $bool = $this->allowField(true)->save($data,['idaccount'=>$data['account_id']]);
            }
        }
        
        if($bool === false)
        {
            return ['status' => 'fail', 'msg' => '操作失败'];
        }else
        {
            return ['status' => 'success', 'msg' => '操作成功'];
        }
    }

    //帐号角色设置
    public function role_set($data){
        $role_list = db('role')->select();
        $account_role = db('account_role')->where('fidaccount',$data['id'])->select();
        $arr = [];
        $arr['rolelist'] = $role_list;
        $arr['accountrole'] = $account_role;
        return $arr;
    }

    //角色设置提交地址
    public function role_set_post($data){

        db('account_role')->where('fidaccount',$data['accountid'])->delete(); //删除帐号角色
        if(array_key_exists('roleid',$data)){
            if(is_array($data['roleid'])){
                foreach ($data['roleid'] as $key=>$value){
                    $bool = db('account_role')->insert(['fidaccount'=>$data['accountid'],'fidrole'=>$value]);
                }
            }
        }else{
            $bool = true;
        }
        return $bool;
    }

    //刷新账号权限
    public function  RefreshPurview($AccountID)
    {
        db('purview')->where('idaccount=:idaccount',['idaccount'=>$AccountID])->delete();

        $idroles='';
        //跨站角色,获取帐号有哪些角色
        $dt=db('account_role')->where('fidaccount =:fidaccount',['fidaccount'=>$AccountID])->field('fidrole')->select();
        foreach ($dt as $row) {
                $idroles .= $row['fidrole'].',' ;
        }

        $dt1=db('role_operate')->where(['idrole'=>['in',$idroles]])->field('chrmodulecode,chrresourcecode,chroperatecode,idsite')->distinct(true)->select();

        foreach ($dt1 as $rows)
        {
            $rows['idaccount']=$AccountID;
            db('purview')->insert( $rows);
        }
        db('account')->where('idaccount =:idaccount',['idaccount'=>$AccountID])->update(['repurview'=>0]);
    }













}