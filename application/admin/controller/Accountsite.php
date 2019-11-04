<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/4
 * Time: 17:11
 */

namespace app\admin\controller;
use think\Request;

class Accountsite  extends Base {



/*
    public function qrcode($url='')
    {

        include_once('../thinkphp/library/think/qrcode/ErrorCorrectionLevel.php') ;
        include_once('../thinkphp/library/think/qrcode/QrCodeInterface.php') ;
        include_once('../thinkphp/library/think/qrcode/qrcode.php') ;

        $url = $url ? $url : input('param.url');
        $qrCode =  new \Endroid\QrCode\QrCode();//创建生成二维码对象
        $qrCode->setText($url)
            ->setSize(150)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setImageType(\Endroid\QrCode\QrCode::IMAGE_TYPE_PNG);

        //>>>>>>>直接输出到浏览器>>>>>>>>>>
        header("Content-type: image/png");
        $qrCode->render(); //输入到浏览器
        //>>>>>>>直接输出到浏览器>>>>>>>>>>

        //>>>>>>>>>保存文件>>>>>>>>>>>
        //$qrCode->save('ziyuanniao.png'); //保存文件
        //>>>>>>>>>保存文件>>>>>>>>>>>
    }*/
    //帐号列表
    public function accountlist(){
        $this->CheckSite();
        if($this->CMS->CheckPurview('accountmanage','view')==false){
            $this->NoPurview();
        }
        $obj = new \app\admin\module\Accountsite(session('idsite'));
        //点击帐号管理的默认页
        if(Request::instance()->param() == false){
            $data = [];
            $data['chraccount'] = '';
            $data['chrname'] = '';
        }else{
            //搜索分页
            $data = Request::instance()->param();
            if(array_key_exists('chrname',$data) == false){
                $data['chrname'] = '';
            }
            if(array_key_exists('chraccount',$data) == false){
                $data['chraccount'] = '';
            }
            if(array_key_exists('p',$data) == false){
                $data['p'] = '1';
            }
        }

        $data['siteid']=session('idsite');
        $arr = $obj->index($data); //$data是一个数组 chrname,chraccount,page
        $account = $arr['data'];
        $page = $arr['pager'];
        $this->assign('page',$page);
        $this->assign('data',$data);
        $this->assign('account',$account);
        return $this->fetch();
    }

    //帐号的查看，添加，修改，跳转页面
    public function accountdeal(){
        $this->CheckSite();
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Accountsite(session('idsite'));
        $account = $obj->deal($request);
        if($request['action'] != 'add' && !isset($account['idaccount'])){
            $this->success('账号不存在', PUBLIC_URL.'postsuccess.html');
        }
        $this->assign('account',$account);
        return $this->fetch();
    }

    //帐号删除
    public function del(){
        $this->CheckSite();
        if($this->CMS->CheckPurview('accountmanage','del')==false){
            $this->NoPurview();
        }
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Accountsite(session('idsite'));
        $bool = $obj->account_del($data);
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    //帐号选择删除
    public function delchecked(){
        $this->CheckSite();
        if($this->CMS->CheckPurview('accountmanage','del')==false){
            $this->NoPurview();
        }

        $request = Request::instance()->param();
        $account_obj = new \app\admin\module\Accountsite(session('idsite'));
        $bool = $account_obj->del_checked($request);
        if($bool){
            return 1;
        }else{
            return 0;
        }
    }

    //用户管理提交的数据
    public function post_data(){
        $this->CheckSite();
        $data = Request::instance()->param();
        if($data['action'] == 'add')
        {
            if($this->CMS->CheckPurview('accountmanage','add')==false){
                $this->NoPurview();
            }
            if(empty($data['chraccount']))
            {
                $this->error('前面带*号的为必填项');
            }
        }else
        {
            if($this->CMS->CheckPurview('accountmanage','edit')==false){
                $this->NoPurview();
            }

        }

        if(empty($data['chrname']) || empty($data['chrpassword']))
        {
            $this->error('前面带*号的为必填项');
        }
        
        $obj = new \app\admin\module\Accountsite(session('idsite'));
        $result = $obj->post($data);
        if($result['status'] === 'success')
        {
            $this->success($result['msg'], PUBLIC_URL.'postsuccess.html');
        }else
        {
            $this->error($result['msg']);
        }
    }

    //帐号角色设置
    public function roleset(){
        $this->CheckSite();
        if($this->CMS->CheckPurview('accountmanage','roleset')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $account_obj = new \app\admin\module\Accountsite(session('idsite'));
        $arr = $account_obj->role_set($request);
        $role_list = $arr['rolelist'];
        $account_role = $arr['accountrole'];
        $this->assign('rolelist',$role_list);
        $this->assign('request',$request);
        $this->assign('accountrole',$account_role);
        return $this->fetch();
    }

    //帐号设置角色提交地址
    public function rolesetpost(){
        $this->CheckSite();
        if($this->CMS->CheckPurview('accountmanage','roleset')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $account_obj = new \app\admin\module\Accountsite(session('idsite'));
        $bool = $account_obj->role_set_post($request);
        if($bool){
            $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
        }else{
            $this->success('操作失败');
        }
    }

}