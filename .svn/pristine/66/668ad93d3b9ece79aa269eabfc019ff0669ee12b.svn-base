<?php
/*
 * @Descripttion: 用户操作日志
 * @version: v1.0
 * @Author: ChenJie
 * @Date: 2019-07-02 14:28:51
 * @LastEditors: ChenJie
 * @LastEditTime: 2019-07-03 14:28:20
 */
namespace app\admin\controller;
use app\admin\module\Useroperatelog as UseroperatelogModel;

class Useroperatelog extends Basesite{
    /**
     * 日志列表
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-02 14:30:34
     */
    public function index(){
        if($this->CMS->CheckPurview('useroperatelogmanage','view')==false){
            $this->error('无权限');
        }

        $search = input('param.');
        $search['module_name'] = isset($search['module_name']) ? $search['module_name'] : '';
        $search['operate_type'] = isset($search['operate_type']) ? $search['operate_type'] : '';
        
        // 站点日志列表
        $userOperateLogModel = new UseroperatelogModel;
        $result = $userOperateLogModel->index($search);
        // 操作类型
        $conifg_operate_type = config('operate_type');
        // 模块名称列表
        $module_list = $userOperateLogModel->getModuleList();

        $this->assign("search", $search);
        $this->assign("page", $result['page']);
        $this->assign("datalist", $result['datalist']);
        $this->assign("conifg_operate_type", $conifg_operate_type);
        $this->assign("module_list",$module_list);
        return $this->fetch();
    }
}