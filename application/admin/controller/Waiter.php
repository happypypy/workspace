<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/2/23
 * Time: 17:31
 */

namespace app\admin\controller;
use think\Request;

class Waiter extends Basesite
{

    //客服列表
    public function index()
    {

        if ($this->CMS->CheckPurview('waiter', 'manage') == false) {
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Waiter($this->idsite);
        $result = $obj->index($request);
        $list = $result['data'];

        $this->assign('list', $list);
        return $this->fetch();
    }

    //客服添加,
    public function modi()
    {
        $request = Request::instance()->param();
        if ($this->CMS->CheckPurview('waiter',$request["action"]) == false) {
            $this->NoPurview();
        }
        $obj = new \app\admin\module\Waiter($this->idsite);
        $result = $obj->modi($request);

        $this->assign('info', $result);
        return $this->fetch();
    }

    //客服提交
    public function postmodi()
    {
        $request = Request::instance()->param();
        if ($this->CMS->CheckPurview('waiter', $request["action"]) == false) {
            $this->NoPurview();
        }

        $obj = new \app\admin\module\Waiter($this->idsite);
        $bool = $obj->modi_post($request);
        if ($bool !== false) {
            $this->success('操作成功', PUBLIC_URL . 'postsuccess.html');
        } else {
            $this->error('操作失败');
        }
    }
    public function del(){
        if ($this->CMS->CheckPurview('waiter', 'del') == false) {
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Waiter($this->idsite);
        $bool = $obj->del($request);
        if($bool){
            return 1;
        }else{
            return 0;
        }
    }
    //访问记录,
    public function visit()
    {
        if ($this->CMS->CheckPurview('waiter') == false) {
            $this->NoPurview();
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Waiter($this->idsite);
        $result = $obj->visit($request);

        $list = $result['data'];
        $page = $result['pager'];

        $this->assign('page',$page);
        $this->assign('list', $list);
        return $this->fetch();


    }

}
