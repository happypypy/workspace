<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/2/23
 * Time: 17:31
 */

namespace app\admin\controller;


use think\Request;

class Smsmanage extends Base
{


    //短信管理
    public function index()
    {
        if(!$this->CMS->CheckPurview('smsmanage')){
            $this->NoPurview();
        }
        
        $obj = new \app\admin\module\Sitemanege;
        $data = Request::instance()->param();
        $result = $obj->index($data);
        $search = $result["search"];
        $this->assign('site_list', $result['data']);
        $this->assign('page', $result['page']);
        $this->assign('search', $search);
        return $this->fetch();
    }


    /**
     * 日志
     * @return bool|void
     */
    public function log(){
        if(!$this->CMS->CheckPurview('smsmanage','view')){
            $this->NoPurview();
        }

        $obj = new \app\admin\module\Sitemanege;
        $data = Request::instance()->param();
        $result = $obj->sms_recharge_log_list($data);
//        var_dump($result);exit;
        $search = $result["search"];
        $this->assign('site_list', $result['data']);
        $this->assign('page', $result['page']);
        $this->assign('search', $search);
        return $this->fetch();
    }

    /**
     * 发送日志
     */
    public function send_log(){
        if(!$this->CMS->CheckPurview('smsmanage','view')){
            $this->NoPurview();
        }

        $obj = new \app\admin\module\Sitemanege;
        $data = Request::instance()->param();
        $result = $obj->sms_send_log_list($data);
        $search = $result["search"];
        $this->assign('list', $result['data']);
        $this->assign('page', $result['page']);
        $this->assign('search', $search);
        return $this->fetch();
    }

    //充值
    public function sms_recharge(){
        if(!$this->CMS->CheckPurview('smsmanage','recharge')){
            $this->NoPurview();
        }

        $data = Request::instance()->param();
        $obj = new \app\admin\module\Sitemanege;
        if (Request::instance()->isPost()) {
            $bool = $obj->sms_recharge($data);
            if ($bool) {
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            } else {
                $this->error('操作失败');
            }
            exit();
        }
        return $this->fetch();
    }

    public function review(){
        if(!$this->CMS->CheckPurview('smsmanage','review')){
            $this->NoPurview();
        }

        $data = Request::instance()->param();
        $obj = new \app\admin\module\Sitemanege;
        if (Request::instance()->isPost()) {
            $bool = $obj->sms_review($data);
            if ($bool) {
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            } else {
                $this->error('操作失败');
            }
            exit();
        }
        $idsite = isset($data["id"])?$data["id"]:0;
        $sm_info = db('site_manage')->where('id=' . $idsite)->find();

        $this->assign('sm_info', $sm_info);
        return $this->fetch();
    }


    /**
     * 展示短信模版列表
     * @access public
     */
    public function templatelist()
    {
        if(!$this->CMS->CheckPurview('template','manage')){
            $this->NoPurview();
        }

        $obj = new  \app\admin\module\Sitemanege;
        $result = $obj->sms_sys_template_list($this->idsite);

        $this->assign('list', $result['data']);
        $this->assign('page', $result['page']);
        $this->assign('template_type', config('template_type'));
        // $this->assign('search', $result['search']);

        return $this->fetch();
    }


    /**
     * 编辑、新建短信模版时获取模版数据
     * @access public
     */
    public function template()
    {
        if(!$this->CMS->CheckPurview('template' , 'manage'))
        {
            $this->NoPurview();
        }

        $request = Request::instance();
        $params = $request->param();
        $obj = new \app\admin\module\Sitemanege();
        $sms_sys_template = $obj->get_sms_sys_template($params, $this->idsite);

        if(isset($params['action']))
            $this->assign('action',$params['action']);
        $this->assign('sms_sys_template',$sms_sys_template);
        $this->assign('template_type', config('template_type'));

        return $request->isAjax() ? json($sms_sys_template) :  $this->fetch();
    }

    /**
     * 处理编辑、新建短信模版的提交数据
     * @access public
     */
    public function templatepost()
    {
        if(!$this->CMS->CheckPurview('template' , 'manage'))
        {
            $this->NoPurview();
        }

        $request = Request::instance()->param();
        if (empty($request['name']) || empty($request['content']) || empty($request['inttype']))
        {
            $this->error('前面带*号的为必填项');
        }

        // var_dump($request);die;

        if(!isset($request['action']) || !in_array($request['action'], ['create', 'revise']))
        {
            $this->error('参数错误');
        }


        $obj = new \app\admin\module\Sitemanege();
        $result = $obj->sms_sys_template_post($request);
        if($result['status'] == 'success')
        {
            $this->success($result['msg'],PUBLIC_URL.'postsuccess.html');
        }else
        {
            $this->error($result['msg']);
        }
    }

    /**
     * 删除短信模版
     * @access public
     */
    public function templatedel()
    {
        if(!$this->CMS->CheckPurview('template' , 'manage'))
        {
            $this->NoPurview();
        }

        $request = Request::instance()->param();

        if(!isset($request['id']) || empty($request['id']))
        {
            $this->error('参数错误，请刷新页面');
        }

        $option = [
            'id' => $request['id'],
        ];

        $obj = new \app\admin\module\Sitemanege();

        if($obj->sms_sys_template_del($option))
        {
            $this->success('操作成功');
        }else
        {
            $this->error('操作失败');
        }
    }
}