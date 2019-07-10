<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/1/9
 * Time: 14:18
 */

namespace app\admin\controller;
use think\Request;

class Filter extends Base {

    public function index(){
        if($this->CMS->CheckPurview('filtermanage','view')==false){
            $this->NoPurview();
        }
        $obj = new \app\admin\module\Filter;
        //点击帐号管理的默认页
        if(Request::instance()->param() == false){
            $data = [];
            $data['content'] = '';

        }else{
            //搜索分页
            $data = Request::instance()->param();
            if(array_key_exists('content',$data) == false){
                $data['content'] = '';
            }
            if(array_key_exists('p',$data) == false){
                $data['p'] = '1';
            }
        }
        $arr = $obj->index($data);
        $filter_list = $arr['data'];
        $page = $arr['pager'];
        $this->assign('page',$page);
        $this->assign('data',$data);
        $this->assign('idsite',$arr['idsite']);
        $this->assign('filter_list',$filter_list);
        return $this->fetch();
    }

    public function filterdeal(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('filtermanage',$request['action'])==false){
            $this->NoPurview();
        }
        $obj = new \app\admin\module\Filter();
        $filter = $obj->filter_deal($request);
        $this->assign('filter',$filter);
        return $this->fetch();
    }

    public function filterdel(){
        if($this->CMS->CheckPurview('filtermanage','del')==false){
            $this->NoPurview();
        }
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Filter();
        $bool = $obj->filter_del($data);
        if(!empty(session('filter_'.session('idsite'))))
        {
            session('filter_'.session('idsite'),null);
        }
        $this->CMS->filter_cache(session('idsite'));
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }


    }

    //选中删除
    public function delchecked(){
        if($this->CMS->CheckPurview('filtermanage','del')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $pattern_obj = new \app\admin\module\Filter();
        $bool = $pattern_obj->del_checked($request);
        $this->CMS->filter_cache(session('idsite'));
        if($bool){
            return 1;
        }else{
            return 0;
        }
    }

    public function filterpost(){
        $data = Request::instance()->param();
        if($this->CMS->CheckPurview('filtermanage',$data['action'])==false){
            $this->NoPurview();
        }
        if(!empty(session('filter_'.session('idsite'))))
        {
            session('filter_'.session('idsite'),null);
        }
        if (empty($data['content'])){
            $this->error('前面带*号的为必填项');
        }else{
            $obj = new \app\admin\module\Filter();
            $bool = $obj->filter_post($data);
            $this->CMS->filter_cache(session('idsite'));
            if($bool !== false){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error('操作失败');
            }
        }
    }

}