<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/24
 * Time: 9:20
 */

namespace app\admin\controller;
use app\admin\module\Integral as IntegralModel;

class Integral extends Basesite {
    // 积分管理
    public function index(){
        if($this->CMS->CheckPurview('integralmanage')==false){
            $this->error('无权限');
        }

        $param = input('param.');
        $param['p'] = isset($param['p']) ? $param['p'] : 1;
        $param['vflag'] = isset($param['vflag']) ? $param['vflag'] : '';       //积分标志
        $param['nickname'] = isset($param['nickname']) ? $param['nickname'] : '';    //用户昵称
        $param['chrname'] = isset($param['chrname']) ? $param['chrname'] : '';       //用户姓名
        $param['starttime'] = isset($param['starttime']) ? $param['starttime'] : '';  //开始时间
        $param['endtime'] = isset($param['endtime']) ? $param['endtime'] : '';       //结束时间
        $param['category'] = isset($param['category']) ? $param['category'] : 0;       //结束时间

        $integral = new IntegralModel;
        $result = $integral->member_integral_record($param);

        // 获取积分分类
        $integral_category = config('integral_category');

        // 搜索条件
        $this->assign('integral_category', $integral_category);
        $this->assign('member_integral_record', $result['data']);
        $this->assign('total_integral', $result['total_integral']);
        $this->assign('page', $result['page']);
        $this->assign('param', $param);
        return $this->fetch('integral_index');
    }

    // 积分年度或月度报表
    public function integral_report(){
        $param = input('param.');
        $action = isset($param['action']) ? $param['action'] : '';

        if($action == 'year'){
            if($this->CMS->CheckPurview('integralannualreport')==false){
                $this->error('无权限');
            }
        }else{
            if($this->CMS->CheckPurview('integralmonthlyreport')==false){
                $this->error('无权限');
            }
        }
        
        $param['begintime']  = isset($param['begintime']) ? $param['begintime'] : '';
        $param['endtime']  = isset($param['endtime']) ? $param['endtime'] : '';

        $integral_report = [];
        if($param['begintime'] && $param['endtime']){
            $integral = new IntegralModel;
            $integral_report = $integral->integral_report($param);
        }

        $this->assign('integral_report', $integral_report);
        $this->assign('param', $param);
        return $this->fetch();
    }

    // 积分规则设置
    public function integral_rule(){
        if($this->CMS->CheckPurview('integralruleconfig','view')==false){
            $this->error('无权限');
        }

        $idsite = session('idsite');
        $integral_rule_obj = new IntegralModel();
        $integral_rule = $integral_rule_obj->integral_rule($idsite);
        $integral_rule['signin_integral'] =  isset($integral_rule['signin_integral']) ? json_decode($integral_rule['signin_integral']) : '';

        $this->assign('idsite', $idsite);
        $this->assign('integral_rule', $integral_rule);
        return $this->fetch();
    }
    
    // 积分配置保存配置
    public function integral_rule_save(){
        if($this->CMS->CheckPurview('integralruleconfig','edit')==false){
            $this->error('无权限');
        }
        $param = input('param.');
        $integral_model = new IntegralModel();
        $signin_integral = isset($param['signin_integral']) ? $param['signin_integral'] : '';

        if($signin_integral[1] < $signin_integral[0]){
            $this->success('第一天不能大于第二天积分',url('integral/integral_rule',['idsite'=>$param['idsite']]));
        }

        if($signin_integral[2] < $signin_integral[1]){
            $this->success('第二天积分不能大于最高积分',url('integral/integral_rule',['idsite'=>$param['idsite']]));
        }

        try{
            $integral_model->integral_rule_save($param);
            $this->success('操作成功',url('integral/integral_rule',['idsite'=>$param['idsite']]));
        }catch(Exception $e){
            $this->success('操作失败');
        }
    }

    // 积分商品管理
    public function integral_goods(){
        if($this->CMS->CheckPurview('goodsmanage','view')==false){
            $this->error('无权限');
        }

        $param = input('param.');
        $param['p'] = isset($param['p']) ? $param['p'] : 1;
        $param['goods_name'] = isset($param['goods_name']) ? $param['goods_name'] : '';
        $param['age'] = isset($param['age']) ? $param['age'] : "";
        $param['age'] = is_numeric($param['age']) ? $param['age'] : "";

        $integralModel = new IntegralModel();
        $result = $integralModel->goods_manage($param);

        $this->assign('integral_mall_goods',$result['data']);
        $this->assign('page',$result['page']);
        $this->assign('param',$param);
        $this->assign('siteid',session('idsite'));
        return $this->fetch();
    }
    
    // 添加修改积分商品
    public function integral_goods_modi(){
        $param = input('param.');
        $param['id'] = isset($param['id']) ? $param['id'] : 0;

        $integralModel = new integralModel();
        if(request()->isPost()){
            if($this->CMS->CheckPurview('goodsmanage',$param['action'])==false){
                $this->error('无权限');
            }

            $bool = $integralModel->goods_post_data($param);
            if($bool !== false){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error('操作失败');
            }
            exit();
        }

        $datainfo = [];
        if($param['id']){
            $datainfo = $integralModel->goods_deal($param['id']);
        }

        $this->assign('siteid',$param['siteid']);
        $this->assign('datainfo',$datainfo);
        return $this->fetch();
    }

    // 积分商品删除
    public function integral_goods_del(){
        if($this->CMS->CheckPurview('goodsmanage','del')==false){
            $this->error('无权限');
        }

        $param = input('param.');
        $integralModel = new integralModel();
        $bool = $integralModel->goods_del($param);
        if($bool){
            return 1;
        }else{
            return 2;
        }
    }

    // 查看兑换记录
    public function exchange_record(){
        if($this->CMS->CheckPurview('exchangeorder','view')==false){
            $this->error('无权限');
        }

        $param = input('param.');
        $param['p'] = isset($param['p']) ? intval($param['p']) : 1;
        $param['id'] = isset($param['id']) ? intval($param['id']) : 0;
        $param['nickname'] = isset($param['nickname']) ? $param['nickname'] : "";
        $param['order_status'] = isset($param['order_status']) ? $param['order_status'] : 0;

        $integralModel = new integralModel();
        $result = $integralModel->exchange_record($param);
        
        $this->assign('param', $param);
        $this->assign('exchange_record',$result['data']);
        $this->assign('page',$result['page']);
        return $this->fetch();
    }

    // 兑换订单详情
    public function exchange_record_detail(){
        if($this->CMS->CheckPurview('exchangeorder','detail')==false){
            $this->error('无权限');
        }

        $param = input('param.');
        $action = isset($param['action']) ? $param['action'] : '';
        $integralModel = new integralModel();

        if(request()->isPost()){
            $id = $param['id'];
            if($action == 'edit' && $this->CMS->CheckPurview('exchangeorder','edit')==false){
                $this->error('无权限');
            }
            // 确认发货
            if($action == 'delivery' || $action == 'edit'){
                try{

                    $result = $integralModel->confirm_delivery($param);
                    $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
                }catch(Exception $e){
                    $this->error('操作失败');
                }
            }
            
            // 取消订单
            else if($action == 'cancel'){
                $result = $integralModel->cancel_order($id);

                if($result){
                    $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
                }else{
                    $this->error('操作失败，已发货，无法取消订单');
                }
            }
        }

        $exchange_record_detail = $integralModel->exchange_record_detail($param);
        $integral_mall_goods = $integralModel->goods_deal($exchange_record_detail['goods_id']);
        $logistics = $integralModel->get_logistics_list();
        
        $this->assign('action',$action);
        $this->assign('integral_mall_goods',$integral_mall_goods);
        $this->assign('exchange_record_detail',$exchange_record_detail);
        $this->assign('logistics', $logistics);
        return $this->fetch();
    }
}