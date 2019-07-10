<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/14
 * Time: 11:26
 */

namespace app\admin\controller;
use think\Request;
use think\Page;


class Cashed extends Basesite {

    /**
     * 现金券计划列表
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index(){
        if($this->CMS->CheckPurview('cashedplan')==false){
            $this->error('无权限');
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Cashed(session('idsite'));

        $arr = $obj->index($request);

        $data = $arr['data'];
        $page = $arr['pager'];

        $this->assign('search',$arr['search']);
        $this->assign('page',$page);
        $this->assign('data',$data);
        $this->assign('sitecode',getSiteCode(session('idsite')));
        return $this->fetch();
    }

    /**
     * 现金券计划添加，修改，查看跳转页面
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function modi(){
        $obj = new \app\admin\module\Cashed(session('idsite'));
        $data = Request::instance()->param();
        if($this->CMS->CheckPurview('cashedplan',$data['action'])==false){
            $this->NoPurview();
        }
//        echo session("UserName");exit;
        if (Request::instance()->isPost()) {
            $bool = $obj->postData($data);
            if($bool !== false){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error('操作失败');
            }
            exit();
        }
        $result = $obj->deal($data);
        $this->assign('datainfo',$result);
        return $this->fetch();
    }

    /**
     * 现金券计划删除
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        if($this->CMS->CheckPurview('cashedplan','delete')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Cashed(session('idsite'));
        $bool = $obj->del($request);
        if($bool){
            return 1;
        }else{
            $this->error('删除失败');
        }
    }

    /**
     * 现金券领取记录
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function receive_record(){
        if($this->CMS->CheckPurview('cashedrecord')==false){
            $this->error('无权限');
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Cashed(session('idsite'));

        $time = date('Y-m-d H:i:s',time());
        //查询用户的可用现金券的列表
        $where['site_id'] = ['=',session('idsite')];
        $where['cashed_validity_time'] = ['<',$time];
        $where['used_status'] = ['=',1];
        //$where_arr['state']=$state;
        //查询所有未使用的过期的现金券
        $all_receive = db('cashed_card_receive')->field('is_manage',true)->where($where)->order("create_time desc")->select();
        //调用修改状态的方法
        update_used_status($all_receive);

        $arr = $obj->receive_record($request);

        $data = $arr['data'];
        $page = $arr['pager'];

        $this->assign('search',$arr['search']);
        $this->assign('page',$page);
        $this->assign('data',$data);
        $this->assign('count',$arr['count']);
        $this->assign('cashed_plan',$arr['cashed_plan']);//现金券计划
        $this->assign('cashed_activity',$arr['cashed_activity']);//领取的现金券活动
        $this->assign('sitecode',getSiteCode(session('idsite')));
        return $this->fetch();
    }

    /**
     * 现金券详情
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function receive_detail()
    {
        if($this->CMS->CheckPurview('cashedrecord')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Cashed(session('idsite'));
        $result = $obj->receive_detail($request);
        $this->assign('datainfo',$result);
        return $this->fetch();
    }

    /**
     * 现金券计划添加，修改，查看跳转页面
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function new_member_cashed_set(){
        $obj = new \app\admin\module\Cashed(session('idsite'));
        $data = Request::instance()->param();
        if($this->CMS->CheckPurview('newusercashed')==false){
            $this->NoPurview();
        }
//        echo session("UserName");exit;
        if (Request::instance()->isPost()) {
//            var_dump($data);exit;
            $bool = $obj->new_member_cashed_set($data);
            if($bool !== false){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
            exit();
        }
        $result = $obj->new_member_cashed_set($data);
        $this->assign('datainfo',$result);
        return $this->fetch();
    }

    /**
     * 现金券的报表
     * @return mixed
     */
    public function cashed_report(){

        if($this->CMS->CheckPurview('cashedreport') == false){
            $this->NoPurview();
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Cashed(session('idsite'));

        $list = $obj->cashed_report_list($request);
        if(!$list['search']){
            $search['search']['begin_year_time'] = '';
            $search['search']['end_year_time'] = '';
            $search['search']['begin_month_time'] = '';
            $search['search']['end_month_time'] = '';
            $search['search']['begintime'] = '';
            $search['search']['endtime'] = '';
        }else{
            $search['search'] = $list['search'];
        }
        $this->assign('list',$list['data']);
        $this->assign('total',$list['汇总']);
        $this->assign('search',$search['search']);
        return $this->fetch();
    }

}