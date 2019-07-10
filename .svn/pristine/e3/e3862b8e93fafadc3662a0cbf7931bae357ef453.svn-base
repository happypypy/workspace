<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/2/23
 * Time: 17:31
 */

namespace app\admin\controller;


use think\Request;

class Weixinreplay extends Base{
    //广告列表
    public function index(){
        if($this->CMS->CheckPurview('autoreply')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $type = 1;
        $request['type']=$type;
        $obj = new \app\admin\module\weixinreply(session('idsite'));


        $arr = $obj->index($request);

        $this->assign('wx_reply_list',$arr["data"]);
        return $this->fetch();
    }

    /*
     * 收到消息
     */
    public function receive_msg(){

        if ($this->CMS->CheckPurview('autoreply') == false) {
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $type = 2;
        $request['type'] = $type;
        $obj = new \app\admin\module\weixinreply(session('idsite'));


        $arr = $obj->index($request);
        $this->assign('wx_reply_list', $arr["data"]);
        return $this->fetch();
    }

    /**
     * 关注
     */
    public function follow()
    {
        if ($this->CMS->CheckPurview('autoreply') == false) {
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $type = 3;
        $request['type'] = $type;
        $obj = new \app\admin\module\weixinreply(session('idsite'));


        $arr = $obj->index($request);
        $this->assign('wx_reply_list', $arr["data"]);
        return $this->fetch();
    }

    /**
     * 编辑或新增
     */
    public function modi()
    {
//        $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
//    exit;
        $data = Request::instance()->param();

        //微信回复类型
        $type = isset($data["type"]) ? intval($data["type"]) : 0;
        //保存微信回复主键id，新增内容默认为0
        $wx_replay_id = 0;
        if (isset($data["wx_replay_id"])) {
            $wx_replay_id = $data["wx_replay_id"];
            unset($data["wx_replay_id"]);
        }
        $obj = new \app\admin\module\weixinreply(session('idsite'));
        if (Request::instance()->isPost()) {
            if ($this->CMS->CheckPurview('autoreply', $data['action']) == false) {
                $this->error('无权限');
            }
            $data["type"] = $type;
            if (!in_array($type, array(1, 2, 3))) {             //如果非指定类型回复，不允许提交数据
                $this->error('非法提交数据操作');
                exit;
            }



            $bool = $obj->PostData($data, $wx_replay_id,session('idsite'));                   //提交数据
            if($bool !== false){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error('操作失败');
            }
            exit();
        }

        $wx_reply = $obj ->get_reply_info($wx_replay_id);

        $this->assign("type", $type);
        $this->assign("wx_reply", $wx_reply);
        return $this->fetch();
    }

    /**
     * 删除数据
     */
    public function delete_reply(){

        if ($this->CMS->CheckPurview('autoreply', 'del') == false) {
            $this->error('无权限');
            exit;
        }

        $data = Request::instance()->param();
        $obj = new \app\admin\module\weixinreply(session('idsite'));
        $bool = $obj->delete_reply($data);                   //提交数据
        if ($bool) {
            $this->success('操作成功', PUBLIC_URL . 'postsuccess.html');
        } else {
            $this->error('操作失败');
        }
        exit();

    }
}