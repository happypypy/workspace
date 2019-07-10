<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/2/23
 * Time: 17:31
 */

namespace app\admin\controller;


use think\Request;

class Advert extends Base{
    //广告列表
    public function index(){

        if($this->CMS->CheckPurview('admanage','view')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Advert();
        $result = $obj->index($request);
        $adv_list = $result['data'];
        $page = $result['page'];
        $ad_position = $result['ad_position'];
        $this->assign('search',$result['search']);
        $this->assign('ad_list',$adv_list);
        $this->assign('page',$page);
        $this->assign('ad_position',$ad_position);
        $this->assign('idsite',$result['idsite']);
        return $this->fetch();
    }

    //广告添加,编辑跳转
    public function advdeal(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('admanage',$request['action'])==false){
            $this->NoPurview();
        }
        $obj = new \app\admin\module\Advert;
        $result = $obj->adv_deal($request);

        $this->assign('ad_info',$result['ad_info']);
        $this->assign('ad_position',$result['ad_position']);
        return $this->fetch();
    }

    //广告提交
    public function advpost(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('admanage',$request['action'])==false){
            $this->NoPurview();
        }
        if(empty($request['pid']) || empty($request['ad_name']) || empty($request['media_type']) || empty($request['ad_code'])) {
            return $this->error('前面带*号的为必填项');
        }
        $obj = new \app\admin\module\Advert;
        $bool = $obj->adv_post($request);
        if($bool !== false){
            $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
        }else{
            $this->error('操作失败');
        }
    }

    //广告删除
    public function advdel(){
        if($this->CMS->CheckPurview('admanage','del')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Advert;
        $bool = $obj->adv_del($request);
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    //广告位置列表页
    public function advposition(){
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Advert;
        $result = $obj->adv_position($request);
        $page = $result['page'];
        $ad_position = $result['data'];
        $this->assign('page',$page);
        $this->assign('idsite',$result['idsite']);
        $this->assign('ad_position',$ad_position);
        return $this->fetch();
    }

    //广告位置添加,编辑跳转
    public function advpositiondeal(){
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Advert;
        $result = $obj->adv_position_deal($request);

        $this->assign('ad_position_info',$result);
        return $this->fetch();
    }

    //广告位提交
    public function advpositionpost(){
        $request = Request::instance()->param();
        if(empty($request['position_name'])) {
            return $this->error('前面带*号的为必填项');
        }
        $obj = new \app\admin\module\Advert;
        $bool = $obj->adv_position_post($request);
        if($bool !== false){
            $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
        }else{
            $this->error('操作失败');
        }
    }

    //广告位置删除
    public function advpositiondel(){
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Advert;
        $bool = $obj->adv_position_del($request);
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    //广告位置代码调用
    public function codeinvoking(){
        $request = Request::instance()->param();
        $this->assign('request',$request);
        return $this->fetch();
    }

}