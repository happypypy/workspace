<?php
/**
 * 天络CMS
 * ============================================================================
 * 版权所有 2017-2027 深圳天络科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinasky.net
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: huangshixin
 * Date:2018/06/26 */

namespace app\admin\controller;
use think\Request;

class Signup extends Base {
    //signup列表
    public function index(){
        if($this->CMS->CheckPurview('signup')==false){
             $this->error('无权限');
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Signup();

        $arr = $obj->index($request);
        $data = $arr['data'];
        $page = $arr['pager'];

        $this->assign('search',$arr['search']);
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function allindex(){
        if($this->CMS->CheckPurview('signup')==false){
            $this->error('无权限');
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Signup();

        $arr = $obj->allindex($request);
        $data = $arr['data'];
        $page = $arr['pager'];

        $this->assign('search',$arr['search']);
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }


    //signup添加，修改，查看跳转页面
    public function modi(){
        $data = Request::instance()->param();

        if (Request::instance()->isPost())
        {
            if($this->CMS->CheckPurview('signup','manage')==false){
                $this->error('无权限');
            }
            $obj = new \app\admin\module\Signup();
            $id = $obj->PostData($data);
            if($id>0){
                if(isset($data['state']) && $data['state']==1)
                {
                    $this->success('操作成功',PUBLIC_URL.'postsuccess.html','"'.$data['title'].'",'.$id);
                }
                else
                {
                    $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
                }
            }else{
                $this->error('操作失败');
            }
            exit();
        }

        $obj = new \app\admin\module\Signup();
        $datainfo = $obj->deal($data);
        $this->assign('datainfo',$datainfo);
        return $this->fetch();
    }
    public function  copyTemplate()
    {
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Signup();
        $obj->copyTemplate($data["id"]);
        return 1;
    }
    //删除
    public function del()
    {
        if($this->CMS->CheckPurview('signup','manage')==false){
            $this->error('无权限');
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Signup();
        $bool = $obj->del($request);
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }

    }
    //选中删除
    public function delchecked(){
        if($this->CMS->CheckPurview('signup','manage')==false){
            return -1;
        }
        $request = Request::instance()->param();
        $role_obj = new \app\admin\module\Signup();

        $bool = $role_obj->del($request);
        if($bool){
            return 1;
        }else{
            return 0;
        }
    }
    //signup列表
    public function indexsub(){
        if($this->CMS->CheckPurview('signup')==false){
            $this->error('无权限');
        }

        $pid="0";
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Signup();
        if(!empty($request["pid"]))
        {
            $pid=$request["pid"];
        }

        $arr = $obj->indexsub($request);
        $data = $arr['data'];
        $page = $arr['pager'];

        $this->assign('search',$arr['search']);
        $this->assign('page',$page);
        $this->assign('data',$data);
        $this->assign('pid',$pid);
        return $this->fetch();
    }
    //signup添加，修改，查看跳转页面
    public function modisub(){
        $data = Request::instance()->param();
        $pid="0";
        if(!empty($data["pid"]))
        {
            $pid=$data["pid"];
        }
        if (Request::instance()->isPost())
        {
            if($this->CMS->CheckPurview('signup','manage')==false){
                $this->error('无权限');
            }
            $obj = new \app\admin\module\Signup();
            $bool = $obj->PostDatasub($data);
            if($bool !== false){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error('操作失败');
            }
            exit();
        }

        $obj = new \app\admin\module\Signup();
        $datainfo = $obj->dealsub($data);
        $this->assign('datainfo',$datainfo);
        $this->assign('pid',$pid);
        return $this->fetch();
    }
    //删除
    public function delsub()
    {
        if($this->CMS->CheckPurview('signup','manage')==false){
            $this->error('无权限');
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Signup();
        $bool = $obj->delsub($request);
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }

    }

    //选中删除
    public function delcheckedsub(){
        if($this->CMS->CheckPurview('signup','manage')==false){
            return -1;
        }
        $request = Request::instance()->param();
        $role_obj = new \app\admin\module\Signup();
        $bool = $role_obj->delsub($request);
        if($bool){
            return 1;
        }else{
            return 0;
        }
    }
    //signup列表
    public function indexsub1(){
        if($this->CMS->CheckPurview('signup')==false){
            $this->error('无权限');
        }

        $pid="0";
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Signup();
        if(!empty($request["pid"]))
        {
            $pid=$request["pid"];
        }

        $arr = $obj->indexsub1($request);
        $data = $arr['data'];
        $page = $arr['pager'];

        $this->assign('search',$arr['search']);
        $this->assign('page',$page);
        $this->assign('data',$data);
        $this->assign('pid',$pid);
        return $this->fetch();
    }
    //signup添加，修改，查看跳转页面
    public function modisub1(){
        $data = Request::instance()->param();
        $pid="0";
        if(!empty($data["pid"]))
        {
            $pid=$data["pid"];
        }
        if (Request::instance()->isPost())
        {
            if($this->CMS->CheckPurview('signup','manage')==false){
                $this->error('无权限');
            }
            $obj = new \app\admin\module\Signup();
            $bool = $obj->PostDatasub1($data);
            if($bool !== false){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error('操作失败');
            }
            exit();
        }

        $obj = new \app\admin\module\Signup();
        $datainfo = $obj->dealsub1($data);
        $this->assign('datainfo',$datainfo);
        $this->assign('pid',$pid);
        return $this->fetch();
    }
    //删除
    public function delsub1()
    {
        if($this->CMS->CheckPurview('signup','manage')==false){
            $this->error('无权限');
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Signup();
        $bool = $obj->delsub1($request);
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }

    }

    //选中删除
    public function delcheckedsub1(){
        if($this->CMS->CheckPurview('signup','manage')==false){
            return -1;
        }
        $request = Request::instance()->param();
        $role_obj = new \app\admin\module\Signup();
        $bool = $role_obj->delsub1($request);
        if($bool){
            return 1;
        }else{
            return 0;
        }
    }



    //修改模型字段是否显示，为空，启用
    public function changeTableVal(){
        if($this->CMS->CheckPurview('signup','manage')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $table = $request['table']; // 表名
        $id_name = $request['id_name']; // 表主键id名
        $id_value = $request['id_value']; // 表主键id值
        $field  = $request['field']; // 修改哪个字段
        $value  = $request['value']; // 修改字段值
        $bool = db($table)->where("$id_name = $id_value")->update(array($field=>$value)); // 根据条件保存修改的数据
        if($bool){
            return 1;
        }else{
            return 2;
        }
    }

}