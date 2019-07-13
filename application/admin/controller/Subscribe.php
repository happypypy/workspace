<?php
/*
 * @Descripttion: 预约对象
 * @version: v1.0
 * @Author: ChenJie
 * @Date: 2019-07-08 14:08:58
 * @LastEditors: ChenJie
 * @LastEditTime: 2019-07-09 15:06:00
 */
namespace app\admin\controller;
use app\admin\module\Subscribe as subscribeModel;
use think\Exception;

class Subscribe extends Basesite{
    /**
     * 预约对象列表
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-08 14:11:10
     */
    public function index(){
        $param = input('param.');
        $param['object_name'] = isset($param['object_name']) ? rtrim($param['object_name']) : '';
        $param['category_id'] = isset($param['category_id']) ? $param['category_id'] : '';
        $param['start_time'] = isset($param['start_time']) ? $param['start_time'] : '';
        $param['end_time'] = isset($param['end_time']) ? $param['end_time'] : '';
        $param['p'] = isset($param['p']) ? intval($param['p']) : 0;

        $subscribeModel = new subscribeModel;
        // 获取分类
        $subscribe_object_category = $subscribeModel->get_work_content();
        // 获取数据列表
        $result = $subscribeModel->index($param);

        $this->assign("param", $param);
        $this->assign("subscribe_object_category", $subscribe_object_category);
        $this->assign("page", $result['page']);
        $this->assign("datalist", $result['datalist']);
        return $this->fetch();
    }
    /**
     * 预约对象新增/编辑
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-08 14:11:21
     */
    public function object_edit(){
        $idsite = session('idsite');
        $param = input('param.');
        $param['category_id'] = isset($param['category_id']) ? $param['category_id'] : '';
        $id = isset($param['id']) ? intval($param['id']) : 0;

        $subscribeModel = new subscribeModel;
        
        if(request()->isPost()){
            try {
                $subscribeModel->subscribe_object_edit($param);
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            } catch (Exception $e) {
                $this->error('操作失败');
            }
        }
        
        // 获取分类
        $subscribe_object_category = $subscribeModel->get_work_content();

        // 获取对象详情
        $datainfo = $subscribeModel->get_subscribe_object_by_id($id);

        $this->assign("siteid", $idsite);
        $this->assign("param", $param);
        $this->assign("subscribe_object_category", $subscribe_object_category);
        $this->assign("datainfo", $datainfo);
        return $this->fetch();
    }
    /**
     * 预约对象删除
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-08 14:11:33
     */
    public function object_delete(){
        $param = input('param.');
        $subscribeModel = new subscribeModel;
        $bool = $subscribeModel->subscribe_object_delete($param);
        if($bool){
            return 1;
        }else{
            return 2;
        }
    }
    /**
     * 预约会员卡列表
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-09 15:04:16
     */
    public function member_cart(){
        return $this->fetch();
    }
    /**
     * 预约会员卡新增/编辑
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-09 15:04:52
     */
    public function member_cart_edit(){
        return $this->fetch();
    }
    /**
     * 预约会员卡删除
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-09 15:05:38
     */
    public function member_cart_delete(){
        return $this->fetch();
    }
}