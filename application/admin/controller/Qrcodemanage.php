<?php
/*
 * @Descripttion: 二维码管理
 * @version: v1.0
 * @Author: ChenJie
 * @Date: 2019-08-01 15:16:19
 * @LastEditors: ChenJie
 * @LastEditTime: 2019-08-09 10:48:41
 */
namespace app\admin\controller;
use app\admin\module\Qrcodemanage as qrcodeManageModule;
use think\Request;

class Qrcodemanage extends Base{
    /**
     * 二维码管理
     *
     * @return void
     * @author Chenjie
     * @Date 2019-08-01 15:17:25
     */
    public function index(){
        if($this->CMS->CheckPurview('qrcodemanage') == false){
            $this->NoPurview();
        }
        $param = input('param.');
        $param['p'] = isset($param['p']) ? $param['p'] : 1;
        $param['qrcode_name'] = isset($param['qrcode_name']) ? $param['qrcode_name'] : '';

        $qrcodeManage = new qrcodeManageModule();
        $result = $qrcodeManage->index($param);

        $this->assign("data",$result['data']);
        $this->assign("page",$result['page']);
        $this->assign("param",$param);
        return $this->fetch();
    }

    // 二维码增加和修改
    public function qrcode_modi(){
        $param = input('param.');
        $id = isset($param['id']) ? $param['id'] : 0;
        $action = isset($param['action']) ? $param['action'] : '';
        $member = new \app\admin\module\Member();

        if(request()->isPost()){
            if($this->CMS->CheckPurview('qrcodemanage',$action) == false){
                $this->NoPurview();
            }
            $result = $member->qrcode_modi($param);
            if($result){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error('操作失败');
            }
        }
        $result = $member->get_qrcode_info($id);

        $this->assign("datainfo",$result);
        return $this->fetch();
    }
    // 二维码删除
    public function qrcode_del(){
        if($this->CMS->CheckPurview('qrcodemanage','del') == false){
            $this->NoPurview();
        }
        $param = input('param.');
        
        $member = new \app\admin\module\Member();
        $result = $member->qrcode_del($param);

        if($result){
            return 1;
        }else{
            return 0;
        }
    }
    // 查看关注会员
    public function view_member(){
        if($this->CMS->CheckPurview('qrcodemanage','view') == false){
            $this->NoPurview();
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member;
        if($this->CMS->CheckPurview('qrcodemanage','view') == false){
            $request['accountid']=session('AccountID');
        }

        $result = $obj->index($request);
        $member_list = $result['member_list'];
        //dump($member_list);
        $statistical = $result['statistical'];
        $page = $result['page'];
        $search = $result['search'];

        $obj1 = new \app\admin\module\activity(session('idsite'));
        $hyfl=$obj1->getDic("hyfl");
        $_account=$obj1->getUser();
        $account=[];
        if($_account)
        {
            foreach ($_account as $k=>$vo)
            {
                $account[$vo['idaccount']]=$vo['chrname'];
            }
        }
        $usertype=[];
        foreach ($hyfl as $vo)
        {
            $usertype[$vo['code']]=$vo['name'];
        }



        $this->assign('usertype',$usertype);
        $this->assign('hyfl',$hyfl);
        $this->assign('account',$account);
        $this->assign('member_list',$member_list);
        $this->assign('statistical',$statistical);
        $this->assign('search',$search);
        $this->assign('page',$page);
        $this->assign('idsite',$result['idsite']);
        $this->assign('is_cashed',checkedMarketingPackage($result['idsite'],'cashed'));
        return $this->fetch();
    }
}