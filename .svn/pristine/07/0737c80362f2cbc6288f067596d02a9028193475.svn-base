<?php
/*
 * 新预约功能
 * @Descripttion:   
 * @version: 1.0
 * @Author: ChenJie
 * @Date: 2019-09-03 10:53:27
 * @LastEditors: ChenJie
 * @LastEditTime: 2019-09-06 11:44:37
 */

namespace app\admin\controller;
use app\admin\module\Reserve as ReserveModel;

class Reserve extends Basesite{
    /**
     * 门店列表
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 10:57:01
     */
    public function index(){
        $param = input('param.');
        $reserveModel = new ReserveModel();
        $result = $reserveModel->storelist($param);

        $this->assign('param',$param);
        $this->assign('page',$result['page']);
        $this->assign('datalist',$result['datalist']);
        return $this->fetch();
    }
    /**
     * 门店添加/修改
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 10:58:27
     */
    public function storemodify(){
        $param = input('param.');
        $param['id'] = isset($param['id']) ? $param['id'] : 0;
        $datainfo = ['time_type' => -1, 'business_hours'=> '[]', 'business_hours_decode'=> [],'business_status' => 1];
        $time_type_list = config('time_type');

        $reserveModel = new ReserveModel();
        if(request()->isPost()){
            $result = $reserveModel->storemodify($param);
            if($result){
                $this->success("操作成功",PUBLIC_URL."postsuccess.html");
            }else{
                $this->success("操作失败");
            }
        }

        // 门店详情
        if($param['id']){
            $datainfo = $reserveModel->storedetail($param['id']);
            $datainfo['business_hours_decode'] = json_decode($datainfo['business_hours']);
        }

        // 门店时间段
        $businnes_hours_list = config('businnes_hours_list');
        
        $this->assign('siteid',session('idsite'));
        $this->assign('param',$param);
        $this->assign('time_type_list',$time_type_list);
        $this->assign('businnes_hours_list',$businnes_hours_list);
        $this->assign('datainfo',$datainfo);
        return $this->fetch();
    }
    /**
     * 门店删除
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 10:59:57
     */
    public function storedelete(){
        $param = input('param.');
        $ids = isset($param['id']) ? $param['id'] : 0;
        $reserveModel = new ReserveModel();
        $result = $reserveModel->storedelete($ids);
        if($result){
            echo 1;
        }else{
            echo 0;
        }
    }
    /**
     * 子产品管理
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 11:01:08
     */
    public function subproductlist(){
        $param = input('param.');

        $reserveModel = new ReserveModel();
        $result = $reserveModel->subproductlist($param);

        $this->assign('page',$result['page']);
        $this->assign('datalist',$result['datalist']);
        return $this->fetch();
    }
    /**
     * 子产品添加/修改
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 11:01:40
     */
    public function subproductmodify(){
        $param = input('param.');
        $param['id'] = isset($param['id']) ? $param['id'] : 0;
        
        $reserveModel = new ReserveModel();
        if(request()->isPost()){
            $result = $reserveModel->subproductmodify($param);
            if($result){
                $this->success("操作成功",PUBLIC_URL."postsuccess.html");
            }else{
                $this->success("操作失败");
            }
        }
        
        return $this->fetch();
    }
    /**
     * 子产品删除
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 11:03:34
     */
    public function subproductdel(){
        return $this->fetch();
    }
    /**
     * 复制子产品
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 11:05:16
     */
    public function copysubproduct(){
        return $this->fetch();
    }
    /**
     * 设置取消预约
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 11:04:46
     */
    public function setcancelreserve(){
        return $this->fetch();
    }
    /**
     * 卡名称管理
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 11:13:13
     */
    public function cardnamelist(){
        return $this->fetch();
    }
    /**
     * 卡名称添加/修改
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 11:13:49
     */
    public function cardnamemodify(){
        return $this->fetch();
    }
    /**
     * 卡名称删除
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 11:15:01
     */
    public function cardnamedel(){
        return $this->fetch();
    }
    /**
     * 设置子产品
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 11:15:34
     */
    public function setsubproduct(){
        return $this->fetch();
    }
    /**
     * 会员卡管理
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 14:24:11
     */
    public function membercradlist(){
        return $this->fetch();
    }
    /**
     * 会员卡添加/修改
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 14:24:11
     */
    public function membercradmodify(){
        return $this->fetch();
    }
    /**
     * 会员卡详情
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 14:26:28
     */
    public function membercraddetail(){
        return $this->fetch();
    }
    /**
     * 预约记录管理
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 14:27:19
     */
    public function reserverecordlist(){
        return $this->fetch();
    }
    /**
     * 预约详情
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 14:27:48
     */
    public function reservecorddetail(){
        return $this->fetch();
    }
    /**
     * 修改预约时间
     *
     * @return void
     * @author Chenjie
     * @Date 2019-09-03 14:35:20
     */
    public function modifyreservetime(){
        return $this->fetch();
    }
}