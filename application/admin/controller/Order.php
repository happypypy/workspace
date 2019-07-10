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
 * Date:2018/06/29 */

namespace app\admin\controller;
use think\Request;
use think\Db;
use app\admin\module\Package;
use think\Exception;
use think\db\Query;

class Order extends Base {
    //order列表
    public function index(){
        if($this->CMS->CheckPurview('order')==false){
             $this->error('无权限');
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Order();
        $intflag=isset($request['intflag'])?$request['intflag']:'2';
        if(is_numeric($intflag)==false || $intflag<1) {
            $intflag = 2;
            $request['intflag']=2;
        }
        $arr = $obj->index($request);
        $data = $arr['data'];
        $page = $arr['pager'];
        $refundcount=0;
        $signupcount=$arr["signupcount"];

        //用户分类
        $res=db('work_content')->where(['bookcode'=>'hyfl'])->select();
        $this->assign('hyfl',$res);


        $this->assign('search',$arr['search']);
        $this->assign('page',$page);
        $this->assign('refundcount',$arr['refundcount']);
        $this->assign('data',$data);
        $this->assign('signupcount',$signupcount);
        $this->assign('intflag',$intflag);
        $this->assign('order_state',config('order_state'));
        $this->assign('order_state_color',config('order_state_color'));
        return $this->fetch();
    }

    //order添加，修改，查看跳转页面
    public function modi(){
        $data = Request::instance()->param();

        if (Request::instance()->isPost())
        {
            if($this->CMS->CheckPurview('order',"manage")==false){
                $this->error('无权限');
            }

            try{
                $query = new Query;
                $query->startTrans();
                $order = db('order')
                    ->where(['id' => $data['id']])
                    ->field(
                        [
                            'package_id',
                            'paynum',
                            'intflag',
                        ]
                    )
                    ->find();
                $obj = new \app\admin\module\Order();
                $bool = $obj->PostData($data, session('idsite'));

                if($bool !== false)
                {
                    //发送短信通知
                    $obj->signInNotice($data['id']);
                }
                $query->commit();
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }catch(Exception $e){
                $query->rollBack();
                Log::error('报名审核失败，[ SQL ] ' . Db::getLastSql());
                $this->error('操作失败');
            }
            exit();
        }

        $obj = new \app\admin\module\Order();
        $datainfo = $obj->deal($data);
        $this->assign('datainfo',$datainfo);
        $this->assign('order_state',config('order_state'));
        $this->assign('is_cashed',checkedMarketingPackage(session('idsite'),'cashed'));
        return $this->fetch();
    }

    //签到
    public function issign()
    {
        if($this->CMS->CheckPurview('contentmanage','issign')==false){
            $this->error('无权限');
        }
        $data = Request::instance()->param();
        $id=$data['id'];
        $obj =new \app\admin\module\Order();
        if(Request::instance()->isPost())
        {
            if($obj->sign($data))
                $this->success('签到成功',PUBLIC_URL.'postsuccess.html');
        }
        $this->assign('id',$id);
        return $this->fetch();
    }

    //删除
    public function del()
    {
        if($this->CMS->CheckPurview('order','manage')==false){
            $this->error('无权限');
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Order();
        $bool = $obj->del($request);
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }

    }

    //选中删除
    public function delchecked(){
        if($this->CMS->CheckPurview('order','manage')==false){
            return -1;
        }
        $request = Request::instance()->param();
        $role_obj = new \app\admin\module\Order();
        $bool = $role_obj->del($request);
        if($bool){
            return 1;
        }else{
            return 0;
        }
    }

    public function refund(){

        if($this->CMS->CheckPurview('order','refund')==false){
            $this->error('无权限');
        }
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Order();
        $datainfo = $obj->deal($data);
        $this->assign('datainfo',$datainfo);
        $this->assign('sitecode',getSiteCode(session('idsite')));
        $this->assign('idsite',session('idsite'));
        $this->assign('order_state',config('order_state'));
        return $this->fetch();

    }


    public function groupBuyOrderList()
    {
        $request = Request::instance()->param();

        $siteId = session('idsite');

        $where = ['site_id' => $siteId];

        $order = new \app\admin\module\Order();

        $list = $order->groupBuyOrderList($request);

        $refundcount = db('order')->where(array("state" => 5, "idsite" => session('idsite')))->count();
        $signupcount = db('order')->where(array("state" => 12, "idsite" => session('idsite')))->count();

        
        $this->assign('stateMap', config('group_state'));
        $this->assign('list', $list['data']);
        $this->assign('page', $list['page']);
        $this->assign('intflag', 7);
        $this->assign('request', $request);
        $this->assign('refundcount', $refundcount);
        $this->assign('signupcount', $signupcount);

        return $this->fetch();
    }


    public function groupBuyOrderDetail()
    {
        if($this->CMS->CheckPurview('order')==false){
             $this->error('无权限');
        }
        
        $groupBuyOrderId = Request::instance()->param('group_buy_order_id');
        if(!$groupBuyOrderId || !is_numeric($groupBuyOrderId))
        {
            $this->error('参数错误');
        }

        $order = new \app\admin\module\Order();
        $subOrderList = $order->subGroupBuyOrderList($groupBuyOrderId);


        $data = $subOrderList['data'];
        $page = $subOrderList['pager'];

        $this->assign('page',$page);
        $this->assign('data',$data);
        $this->assign('order_state',config('order_state'));
        $this->assign('order_state_color',config('order_state_color'));
        return $this->fetch();

    }

    //批量设置用户的类型
    public  function setUsersType(){
        if(I('get.type') and I('post.openid')){
            $typeid=I('get.type');
            $openid=I('post.openid');
            //dump($openid);
            $map['openid'] = array('in',$openid);
            $map['idsite']=session('idsite');
            $res=db('member')->where($map)->update(['categoryid'=>$typeid]);
              if($res){
                  return 1;//修改成功
              }else{
                  return 2;//没有需要修改的数据
              }
    }
    }
}