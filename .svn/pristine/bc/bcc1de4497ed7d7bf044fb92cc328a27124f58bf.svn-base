<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/30
 * Time: 17:50
 */

namespace app\admin\module;
use think\Model;
use think\Page;

class Role extends Model{

    //角色列表
    public function index(){

        $count = db('role')->count();
        $page = new Page($count,PAGE_SIZE);
        $role = db('role')->order('idrole desc')->limit($page->firstRow.','.$page->pageSize)->select();
        $arr = array();
        $arr['pager'] = $page;
        $arr['data'] = $role;
        return $arr;
    }

    //角色增删改查页面处理
    public function role_deal($data){
        $idsite = session('idsite');
        if(array_key_exists('id',$data)){
            $role = db('role')->where('idrole=:idrole',['idrole'=>$data['id']])->find();
        }else{
            $role = [
                'rolename'      =>  '',
                'roleremark'    =>  '',
                'idsite'        =>  $idsite
            ];
        }
        $role['action'] = $data['action'];
        return $role;
    }

    //角色添加，修改提交地址
    public function role_post($data){
        $data_arr = [
            'rolename'      =>  $data['rolename'],
            'roleremark'    =>  $data['roleremark'],
            'idsite'        =>  $data['idsite']
        ];
        if($data['action'] == 'add'){
            $bool = db('role')->insert($data_arr);
        }else{
            $bool = db('role')->where('idrole=:idrole',['idrole'=>$data['roleid']])->update($data_arr);
        }
        //修改账户权限状态
        //db('account')->where('idaccount','>',0)->setField('repurview',1);
        db('account')->where(array('siteid'=>0))->setField('repurview',1);

        return $bool;
    }

    //角色删除
    public function role_del($data){
        $bool = db('role')->where('idrole=:idrole',['idrole'=>$data['id']])->delete();
        return $bool;
    }

    //角色选中删除
    public function del_checked($data){
        if(strstr($data['id'],',')){
            $id = explode(',',$data['id']);
            for ($i=0;$i<count($id);$i++){
                $bool = db('role')->where('idrole=:idrole',['idrole'=>$id[$i]])->delete();
            }
        }else{
            $bool = db('role')->where('idrole=:idrole',['idrole'=>$data['id']])->delete();
        }
        return $bool;
    }

    //角色权限设置
    public function role_operate($data){
        $role_operate = db('role_operate')->where('idrole',$data['roleid'])->select();
        return $role_operate;
    }

    //获取所有栏目
    public function column_list(){
        $column_list = db('catalog')->select();
        return $column_list;
    }

    //获取所有模块
    public function module_list(){
        $module_list = db('module')->select();
        return $module_list;
    }

    //获取具体栏目信息
    public function column_info($data){

        $column_name = db('catalog')->select();
        $module = db('module')->where(['idsite'=>0])->select(); //栏目下的所有模块
        $role_operate = db('role_operate')->where('idrole',$data['roleid'])->order('idorder asc')->select();
        if(empty($role_operate)){
            $role_operate[] = [
                'chrmodulecode'=>'',
                'chrresourcecode'=>'',
                'chroperatecode'=>'',
                'idrole_operate'=>''
                ];
        }
        $arr['roleoperate'] = $role_operate;
        $arr = [];
        $arr['columnname'] = $column_name;
        $arr['roleoperate'] = $role_operate;
        $arr['module'] = $module;
        return $arr;
    }

    //具体栏目下有哪些模块
    /*public function module_info($data){
        $column_info = db('catalog')->where('idcatalog',$data['columnid'])->find();
        $module_info = db('module')->where('codecatalog',$data['columnid'])->select();
        foreach($module_info as $key=>$value){
            $resource_list[] = db('resource')->where('modulecode',$value['chrcode'])->select();
        }
        foreach ($resource_list as $k=>$v){
            $resource_list[] = $v;
        }
        var_dump($resource_list);
        foreach ($module_info as $ke=>$val){
            foreach ($resource_list as $k=>$v){
                $operate_list = db('operate')->where('chrmodulecode',$val['chrcode'])->where('chrresourcecode',$v['chrcode'])->select();
            }
        }
        return $operate_list;
    }*/

    //具体栏目下有哪些模块
    public function module($data){
        $module = db('module')->where('codecatalog',$data['columnid'])->select();
        return $module;
    }

    //资源列表
    public function resource_list(){
        $resource_list = db('resource')->select();
        return $resource_list;
    }

    //操作列表
    public function operate_list(){
        $resource_list = db('operate')->order('idorder asc')->select();
        return $resource_list;
    }

   /* //点击模块设置权限
    public function module_info($data){
        $module_info = db('module')->where('chrcode',$data['modulecode'])->where('codecatalog',$data['columnid'])->find();
        $resource_list = db('resource')->where('modulecode',$module_info['chrcode'])->select();//模块下的资源
        $operate_list = db('operate')->where('chrmodulecode',$module_info['chrcode'])->select();//模块所有资源的操作
        $role_operate = db('role_operate')->where('idrole',$data['roleid'])->select();
        $arr = [];
        $arr['module'] = $module_info;
        $arr['resource'] = $resource_list;
        $arr['operate'] = $operate_list;
        $arr['roleoperate'] = $role_operate;
        return $arr;
    }*/

    //修改角色操作
    public function operate_edit($request){
        $base = $request['roleoperate'];    //数据库的数据
        //$module_code = $request['modulecode'];  //模块代号
        //$resource = array_unique($request['resource']); //资源
        $role_id = $request['roleid'];  //角色id
        $idsite = $request['idsite'];

        db('role_operate')->where('idrole',$role_id)->delete();
        $bool = '';
        if(array_key_exists('operate_list',$request)){
            $post = $request['operate_list'];   //提交过来的数据

            foreach ($post as $key=>$value){
                $arr = explode('_',$value);
                $data = [
                    'idrole'    =>  $role_id,
                    'chrmodulecode' =>  $arr[0],
                    'chrresourcecode'   =>  $arr[1],
                    'chroperatecode'    =>  $arr[2],
                    'idsite'    =>  $idsite
                ];
                $bool = db('role_operate')->insert($data);
            }
        }
        /*if(array_key_exists('operate_list',$request)){
            $post = $request['operate_list'];   //提交过来的数据

            //插入操作
            foreach ($post as $key=>$value){
                if(in_array($value,$base)){
                }else{
                    $arr = explode('_',$value);
                    $data = [
                        'idrole'    =>  $role_id,
                        'chrmodulecode' =>  $arr[0],
                        'chrresourcecode'   =>  $arr[1],
                        'chroperatecode'    =>  $arr[2]
                    ];
                    $bool = db('role_operate')->insert($data);
                }
            }

            //删除没有提交过来的操作
            if(in_array('__',$base) == false){
                foreach($base as $key=>$value){
                    if(in_array($value,$post)){
                    }else{
                        $arr = explode('_',$value);
                        $data = [
                            'idrole'    =>  $role_id,
                            'chrmodulecode' =>  $arr[0],
                            'chrresourcecode'   =>  $arr[1],
                            'chroperatecode'    =>  $arr[2]
                        ];
                        $bool = db('role_operate')->where($data)->delete();
                    }

                }
            }

        }else{

            $str = $request['roleoperate_id'];
            foreach ($str as $key=>$value){
                $bool = db('role_operate')->where('idrole_operate',$value)->delete();
            }
        }*/

        //修改账户权限状态
        db('account')->where(array('siteid'=>0) )->setField('repurview',1);
        return $bool;
    }
}