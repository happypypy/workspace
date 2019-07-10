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
 * Date:2017/08/28 */

namespace app\admin\controller;
use think\Request;

class Indexconfig extends Base {
    //数据列表
    public function index(){
        if($this->CMS->CheckPurview('indexconfig','view')==false){
             $this->error('无权限');
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Indexconfig();

        $arr = $obj->index($request);
        $data = $arr['data'];
        $page = $arr['pager'];

        $this->assign('search',$arr['search']);
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
        }

    //数据添加，修改，查看跳转页面
    public function modi(){
        $data = Request::instance()->param();
        if($this->CMS->CheckPurview('indexconfig',$data['action'])==false){
            $this->error('无权限');
        }
        if (Request::instance()->isPost())
        {
            if($this->CMS->CheckPurview('indexconfig',$data['action'])==false){
                $this->error('无权限');
            }

            $id=0;
            $code=trim($data['chrcode']);
            $name=trim($data['chrname']);
            if(isset($data['id']))  $id=$data['id'];

            if($code=="")
            {
                $this->error('操作失败,代号不能为空！');
                exit();
            }
            if($name=="")
            {
                $this->error('操作失败,名称不能为空！');
                exit();
            }

            $obj = new \app\admin\module\Indexconfig();

            if($obj->CheckCode($code,$id))
            {
                $this->error('操作失败,代号已存在！');
                exit();
            }
            $bool = $obj->PostData($data);
            if($bool !== false){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error('操作失败');
            }
            exit();
        }

        $obj = new \app\admin\module\Indexconfig();
        $datainfo = $obj->deal($data);
        $this->assign('datainfo',$datainfo);
        return $this->fetch();
    }
    //删除
    public function del()
    {
        if($this->CMS->CheckPurview('indexconfig','del')==false){
            $this->error('无权限');
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Indexconfig();
        $bool = $obj->del($request);
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }

    }

    //选中删除
    public function delchecked(){
        if($this->CMS->CheckPurview('indexconfig','del')==false){
            return -1;
        }
        $request = Request::instance()->param();
        $role_obj = new \app\admin\module\Indexconfig();
        $bool = $role_obj->del($request);
        if($bool){
            return 1;
        }else{
            return 0;
        }
    }
}