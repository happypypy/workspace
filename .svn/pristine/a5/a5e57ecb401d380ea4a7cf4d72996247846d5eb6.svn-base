<?php
/*
 * @Descripttion: 系统更新模块
 * @version: 1.0
 * @Author: ChenJie
 * @Date: 2019-06-24 11:54:05
 * @LastEditors: ChenJie
 * @LastEditTime: 2019-06-26 17:06:11
 */

namespace app\admin\controller;
use app\admin\module\Systemupdate as systemUpdateModel;

class Systemupdate extends Base{
    
    /**
     * 系统更新列表
     *
     * @return void
     * @author Chenjie
     * @Date 2019-06-24 14:29:40
     */
    public function index(){
        if($this->CMS->CheckPurview('systemupdatemanage') == false){
            $this->NoPurview();
        }

        $param = input('param.');

        $systemUpdateModel = new systemUpdateModel();
        $result = $systemUpdateModel->index($param);

        $this->assign('param',$param);
        $this->assign('datalist',$result['datalist']);
        $this->assign('page',$result['page']);
        return $this->fetch();
    }
    /**
     * 系统更新新增/编辑
     *
     * @return void
     * @author Chenjie
     * @Date 2019-06-24 14:30:27
     */
    public function edit(){
        $param = input('param.');
        $id = isset($param['id']) ? $param['id'] : 0;

        $systemUpdateModel = new systemUpdateModel();
        if(request()->isPost()){
            if($this->CMS->CheckPurview('systemupdatemanage',$param['action']) == false){
                $this->NoPurview();
            }
            
            $result = $systemUpdateModel->edit($param);
            if($result){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->success('操作失败');
            }
        }
        $datainfo = [];
        if($id){
           $datainfo = $systemUpdateModel->getSystemUpdateeById($id);
        }
        $this->assign("datainfo",$datainfo);
        return $this->fetch();
    }
    /**
     * 系统更新删除
     *
     * @return void
     * @author Chenjie
     * @Date 2019-06-24 14:31:34
     */
    public function delete(){
        if($this->CMS->CheckPurview('systemupdatemanage','del') == false){
            $this->NoPurview();
        }
        $id = input('param.id');

        $systemUpdateModel = new systemUpdateModel();
        $result = $systemUpdateModel->del($id);

        if($result){
            return 1;
        }else{
            return 0;
        }
    }
    /**
     * 系统更新页面
     *
     * @return void
     * @author Chenjie
     * @Date 2019-06-25 11:50:49
     */
    public function singlepage(){
        $id = input('param.id/d');
        $systemUpdateModel = new systemUpdateModel();
        $datainfo = $systemUpdateModel->singlepage($id);

        if(!$id){
            session("isUpdateRemind",null);
        }
        $this->assign('datainfo',$datainfo);
        return $this->fetch();
    }
    /**
     * 系统提醒页面
     *
     * @return void
     * @author Chenjie
     * @Date 2019-06-25 11:50:49
     */
    public function tip(){
        $id = input('param.id/d');
        $systemUpdateModel = new systemUpdateModel();
        $datainfo = $systemUpdateModel->singlepage($id);

        if(!$id){
            session("isUpdateRemind",null);
        }
        $this->assign('datainfo',$datainfo);
        return $this->fetch();
    }
}