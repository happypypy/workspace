<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/14
 * Time: 11:37
 */

namespace app\admin\module;
use think\Exception;
use think\Model;
use think\Page;
use think\Request;
use think\Db;


class Distribution extends Model{
    protected  $siteid=0;
    function __construct($idStie){
        $this->siteid=$idStie;
        parent::__construct();
    }

    /**
     * 海报模板设置
     * @param $data
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function distribution_img_set($data){
        $result = db('spokesman_poster')->where(['site_id'=>$this->siteid])->find();
        //如果有id的话
        if(!$result){
            $result = [
                'id'=>'',
                'spokesman_poster_img' => '',
                'site_id'=>$this->siteid
            ];
        }
        if(Request::instance()->isPost()){
            //如果存在id，那么就是之前设置过，修改
            if(!array_key_exists('id',$data)){
                return $bool = db('spokesman_poster')->insert($data);
            }else{
                return $bool = db('spokesman_poster')->where(['id'=>$data['id'],'site_id'=>$this->siteid])->update($data);
            }
        }
//        var_dump($result);exit;

        return $result;
    }

    /**
     * 可代言的产品列表
     * @param $request
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function spokesman_activity($request){

        //名称
        $search['chrtitle']=empty($request['chrtitle'])?"":$request['chrtitle'];
        if($search['chrtitle']!='')
        {
            $Search_arr1['chrtitle']= array('like','%'.$search['chrtitle'].'%');
        }
        $Search_arr1['siteid']= ['=',$this->siteid];
        $Search_arr1['is_distribution']= ['=',1];//开启了分销的活动
        $Search_arr1['chkdown']=array('neq',1);//上架的
        $Search_arr1['dtsignetime']=array('>',date('Y-m-d H:i:s',time()));//还未过报名结束时间
        $Search_arr1['intflag'] = 2;//状态是已发布的

        //查询条数
        $count = db('activity')->where($Search_arr1)->count();

        $page = new Page($count,PAGE_SIZE);
        //查询数据
        $data = db('activity')->where($Search_arr1)->order('dtpublishtime desc,idactivity desc')->limit($page->firstRow.','.$page->pageSize)->select();
        if($data){
            foreach ($data as $key=>$val){
                $where['spokesman_user_id3'] = array('>',0);
                //只要查出该活动下的代言订单就可以了
                $num = $this->getOrderNum($where,"dataid = {$val['idactivity']}");
                //该代言人代言该活动的所有订单
                $data[$key]['total'] = $num['total'];
                $data[$key]['no_pay'] = $num['no_pay'];
                $data[$key]['refund'] = $num['refund'];
                $data[$key]['spokesman_pay_num'] = $num['pay'];
            }
        }
//        var_dump($data);exit;

        $arr = array();
        $arr['search'] = $search;
        $arr['pager'] = $page;
        $arr['data'] = $data;

        return $arr;
    }

    /**
     * 结算办理记录
     * @param $request
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function balance_record($request){
        //申请人
        $search['u_chrname']=empty($request['u_chrname'])?"":trim($request['u_chrname']);
        //手机号码
        $search['u_chrtel']=empty($request['u_chrtel'])?"":trim($request['u_chrtel']);
        //微信号
        $search['wechat_number']=empty($request['wechat_number'])?"":trim($request['wechat_number']);
        //状态
        $search['audit_status']=empty($request['audit_status'])?'':intval($request['audit_status']);
        //申请时间
        $search['dtstart']=empty($request['dtstart'])?"":$request['dtstart'];
        $search['dtend']=empty($request['dtend'])?"":$request['dtend'];
        //审批时间
        $search['audit_start']=empty($request['audit_start'])?"":$request['audit_start'];
        $search['audit_end']=empty($request['audit_end'])?"":$request['audit_end'];
        //是否是导出数据
        $search['export']=empty($request['export'])?"":$request['export'];
        //申请人
        if($search['u_chrname']!='')
        {
            $Search_arr1['u_chrname']= array('like','%'.$search['u_chrname'].'%');
        }
        //手机号码
        if($search['u_chrtel']!='')
        {
            $Search_arr1['u_chrtel']= array('=',$search['u_chrtel']);
        }

        //微信号
        if($search['wechat_number']!='')
        {
            $Search_arr1['wechat_number']= array('=',$search['wechat_number']);
        }
        //申请人
        if($search['audit_status']!='')
        {
            $Search_arr1['audit_status']= array('=',$search['audit_status']);
        }
        //校验开始时间
        if(!empty($search['dtstart']) && empty($search['dtend'])){
            if(\DateTime::createFromFormat('Y-m-d',$search['dtstart'])){
                $Search_arr1['create_time']= array('>=',$search['dtstart']);
            }
        }
        //校验结束时间
        if(!empty($search['dtend']) && (\DateTime::createFromFormat('Y-m-d',$search['dtend'])) && empty($search['dtstart'])){
            $Search_arr1['create_time']= ['<=',$search['dtend'].' 23:59:59'];
        }

        if(!empty($search['dtend']) && (\DateTime::createFromFormat('Y-m-d',$search['dtend'])) && !empty($search['dtstart']) && \DateTime::createFromFormat('Y-m-d',$search['dtstart'])){
            $Search_arr1['create_time']= [['>=',$search['dtstart']],['<=',$search['dtend'].' 23:59:59']];
        }

        //校验审批时间的开始时间
        if(!empty($search['audit_start']) && empty($search['audit_end'])){
            if(\DateTime::createFromFormat('Y-m-d',$search['audit_start'])){
                $Search_arr1['audit_time']= array('>=',$search['audit_start']);
            }
        }
        //校验审批时间的结束时间
        if(!empty($search['audit_end']) && (\DateTime::createFromFormat('Y-m-d',$search['audit_end'])) && empty($search['audit_start'])){
            $Search_arr1['audit_time']= ['<=',$search['audit_end'].' 23:59:59'];
        }

        if(!empty($search['audit_end']) && (\DateTime::createFromFormat('Y-m-d',$search['audit_end'])) && !empty($search['audit_start']) && \DateTime::createFromFormat('Y-m-d',$search['audit_start'])){
            $Search_arr1['audit_time']= [['>=',$search['audit_start']],['<=',$search['audit_end'].' 23:59:59']];
        }
        $Search_arr1['site_id']= ['=',$this->siteid];

        //查询条数
        $count = db('balance_record')->where($Search_arr1)->count();

        $page = new Page($count,PAGE_SIZE);
        if($search['export'] == 10){
            //查询数据
            $data = db('balance_record')->where($Search_arr1)->order('create_time desc')->select();
        }else{
            //查询数据
            $data = db('balance_record')->where($Search_arr1)->order('create_time desc')->limit($page->firstRow.','.$page->pageSize)->select();
        }
        $arr = array();
        $arr['search'] = $search;
//        var_dump($arr['search']);
        $arr['pager'] = $page;
        $arr['data'] = $data;

        return $arr;
    }

    /**
     * 审批和查看详情页面处理
     * @param $data
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function deal($data){
        $result = db('balance_record')->where(['site_id'=>$this->siteid,'id'=>$data['id']])->find();
        $user_info = db('member')->field('can_commission')->where(['idsite'=>$this->siteid,'idmember'=>$result['user_id']])->find();
//        var_dump($result);exit;
        if(!empty($data['action']))
            $result['action'] = $data['action'];
        $result = array_merge($result,$user_info);
        return $result;
    }

    /**
     * 审批和查看详情页面处理
     * @param $data
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function spokesman_order_detail($data){
        $result = db('order')->where(['idsite'=>$this->siteid,'id'=>$data['id']])->find();
        return $result;
    }

    /**
     * 执行审批
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function postData($data){
        if(!in_array($data['audit_status'],array(5,10))){
            return ['message'=>'审批的状态参数错误','success'=>false];
        }
        //查找该申请记录
        $result = db('balance_record')->where(['site_id'=>$this->siteid,'id'=>$data['id']])->find();
        if(!$result){
            return ['message'=>'申请数据丢失','success'=>false];
        }
        //查询出该用的可结算金额
        $user_info = db('member')->where(['idsite'=>$this->siteid,'idmember'=>$result['user_id']])->find();
        //如果是操作审批通过
        if($data['audit_status'] == 5){
            //判断该用的可结算金额
            if($user_info['can_commission'] < $result['balance_amount']){
                return ['message'=>'结算失败，结算金额大于用户的可结算金额','success'=>false];
            }
        }
        Db::startTrans();
        try {
            //封装修改结算申请记录的数据
            $update_record['audit_status'] = $data['audit_status'];//审批状态
            $update_record['audit_account_id'] = session("AccountID");//审批管理员id
            $update_record['audit_account_chrname'] = session("UserName");//审批管理员账号姓名
            $update_record['audit_time'] = date('Y-m-d H:i:s',time());//审批时间
            $update_record['audit_remark'] = $data['audit_remark'];//审批的备注信息
            //进行修改结算记录的修改
            $record_bool = db('balance_record')->where(['site_id'=>$this->siteid,'id'=>$data['id']])->update($update_record);
            //如果审核成功,那么减去用户的可结算等数据
            if($record_bool && $data['audit_status'] == 5){
                //封装需要修改的数据
                $update_user['can_commission'] = $user_info['can_commission'] - $result['balance_amount'];//可结算金额
                $update_user['already_commission'] = $user_info['already_commission'] + $result['balance_amount'];//已结算金额
                db('member')->where(['idsite'=>$this->siteid,'idmember'=>$result['user_id']])->update($update_user);
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
        }
        return ['message'=>'操作成功','success'=>true];
    }

    /**
     * 分销用户管理
     * @param $request
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function spokesman_list($request){
        //代言人id
        $search['idmember']=empty($request['idmember'])?"":intval($request['idmember']);
        $search['u_chrname']=empty($request['u_chrname'])?"":intval($request['u_chrname']);
        //手机号码
        $search['u_chrtel']=empty($request['u_chrtel'])?"":trim($request['u_chrtel']);
        //微信号
        $search['wechat_number']=empty($request['wechat_number'])?"":trim($request['wechat_number']);
        //加入时间
        $search['dtstart']=empty($request['dtstart'])?"":$request['dtstart'];
        $search['dtend']=empty($request['dtend'])?"":$request['dtend'];
        //是否是导出数据
        $search['export']=empty($request['export'])?"":$request['export'];
        $search['paynumif']=empty($request['paynumif'])?"":$request['paynumif'];  //订单条件
        $search['paynum']=empty($request['paynum'])?"":$request['paynum'];  //订单数
        //分销等级
        $search['spokesman_grade']=empty($request['spokesman_grade'])?"":intval($request['spokesman_grade']);
        //代言人id
        if($search['idmember']!='')
        {
            $Search_arr1['idmember']= array('=',$search['idmember']);
        }
        if($search['u_chrname']!='')
        {
            $Search_arr1['idmember']= array('=',$search['u_chrname']);
        }
        //手机号码
        if($search['u_chrtel']!='')
        {
            $Search_arr1['u_chrtel']= array('=',$search['u_chrtel']);
        }
        //分销等级
        if($search['spokesman_grade']!='')
        {
            $Search_arr1['spokesman_grade']= array('=',$search['spokesman_grade']);
        }else{
            $Search_arr1['spokesman_grade']= ['in','1,2'];//有代言等级的用户
        }

        //微信号
        if($search['wechat_number']!='')
        {
            $Search_arr1['wechat_number']= array('=',$search['wechat_number']);
        }
        if(!empty($search['paynumif']) && !empty($search['paynum'])){
            if($search['paynumif']==2)
            {
                $Search_arr1['spokesman_pay_num']=$search['paynum'];
            }
            else if($search['paynumif']==3)
            {
                $Search_arr1['spokesman_pay_num']=array('lt',$search['paynum']);
            }
            else
            {
                $Search_arr1['spokesman_pay_num']=array('gt',$search['paynum']);
            }
        }
        //校验开始时间
        if(!empty($search['dtstart']) && empty($search['dtend'])){
            if(\DateTime::createFromFormat('Y-m-d',$search['dtstart'])){
                $Search_arr1['spokesman_time']= array('>=',$search['dtstart']);
            }
        }
        //校验结束时间
        if(!empty($search['dtend']) && (\DateTime::createFromFormat('Y-m-d',$search['dtend'])) && empty($search['dtstart'])){
            $Search_arr1['spokesman_time']= ['<=',$search['dtend'].' 23:59:59'];
        }

        if(!empty($search['dtend']) && (\DateTime::createFromFormat('Y-m-d',$search['dtend'])) && !empty($search['dtstart']) && \DateTime::createFromFormat('Y-m-d',$search['dtstart'])){
            $Search_arr1['spokesman_time']= [['>=',$search['dtstart']],['<=',$search['dtend'].' 23:59:59']];
        }
        $Search_arr1['idsite']= ['=',$this->siteid];
        $all_where['idsite'] = ['=',$this->siteid];
        if(array_key_exists('top',$request)){
            $all_where['spokesman_grade']= ['=','1'];
        }else{
            $all_where['spokesman_grade']= ['in','1,2'];
        }
        //查询所有的代言人数据(不分页)
        $u_chrname = db('member')->field('u_chrname,idmember')->distinct('u_chrname')->where($all_where)->order('spokesman_time desc')->select();
        //查询条数
        $count = db('member')->where($Search_arr1)->count();

        $page = new Page($count,PAGE_SIZE);
        if($search['export'] == 10){
            //查询数据
            $data = db('member')->where($Search_arr1)->order('spokesman_time desc')->select();
        }else{
            //查询数据
            $data = db('member')->where($Search_arr1)->order('spokesman_time desc')->limit($page->firstRow.','.$page->pageSize)->select();
        }
        if($data){
            foreach ($data as $key=>$val){
                $num = $this->getOrderNum([],"spokesman_user_id3 = {$val['idmember']} or spokesman_user_id2 = {$val['idmember']} or spokesman_user_id1 = {$val['idmember']}");
                //该代言人代言该活动的所有订单
                $data[$key]['total'] = $num['total'];
                $data[$key]['no_pay'] = $num['no_pay'];
                $data[$key]['refund'] = $num['refund'];
                $data[$key]['spokesman_pay_num'] = $num['pay'];
            }
        }
//        var_dump($data);exit();
        $arr = array();
        $arr['search'] = $search;
//        var_dump($arr['search']);
        $arr['pager'] = $page;
        $arr['data'] = $data;
        $arr['u_chrname'] = $u_chrname;

        return $arr;
    }

    /**
     * 活动代言明细列表
     * @param $request
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function spokesman_activity_detail_list($request){
        //代言人id
        $search['idmember']=empty($request['idmember'])?"":intval($request['idmember']);
        //活动名称
        $search['chrtitle']=empty($request['chrtitle'])?"":trim($request['chrtitle']);
        //加入时间
        $search['dtstart']=empty($request['dtstart'])?"":$request['dtstart'];
        $search['dtend']=empty($request['dtend'])?"":$request['dtend'];
        //如果存在活动id
        $search['activity_id']=empty($request['activity_id'])?"":intval($request['activity_id']);
        //代言人id
        if($search['idmember']!='')
        {
            $Search_arr1['spokesman_user_id']= array('=',$search['idmember']);
        }
        //活动id
        if($search['activity_id']!='')
        {
            $Search_arr1['activity_id']= array('=',$search['activity_id']);
        }
        if($search['chrtitle']!='')
        {
            $Search_arr1['chrtitle']= array('like','%'.$search['chrtitle'].'%');
        }
        //校验开始时间
        if(!empty($search['dtstart']) && empty($search['dtend'])){
            if(\DateTime::createFromFormat('Y-m-d',$search['dtstart'])){
                $Search_arr1['spokesman_time']= array('>=',$search['dtstart']);
            }
        }
        //校验结束时间
        if(!empty($search['dtend']) && (\DateTime::createFromFormat('Y-m-d',$search['dtend'])) && empty($search['dtstart'])){
            $Search_arr1['spokesman_time']= ['<=',$search['dtend'].' 23:59:59'];
        }

        if(!empty($search['dtend']) && (\DateTime::createFromFormat('Y-m-d',$search['dtend'])) && !empty($search['dtstart']) && \DateTime::createFromFormat('Y-m-d',$search['dtstart'])){
            $Search_arr1['spokesman_time']= [['>=',$search['dtstart']],['<=',$search['dtend'].' 23:59:59']];
        }
        $Search_arr1['site_id']= ['=',$this->siteid];
        $all_where['idsite'] = ['=',$this->siteid];
        $all_where['spokesman_grade']= ['in','1,2'];
        //查询所有的代言人数据(不分页)
        $u_chrname = db('member')->field('u_chrname,idmember')->distinct('u_chrname')->where($all_where)->order('spokesman_time desc')->select();
        //查询条数
        $count = db('spokesman_activity')->where($Search_arr1)->count();

        $page = new Page($count,PAGE_SIZE);
            //查询数据
        $data = db('spokesman_activity')->where($Search_arr1)->order('spokesman_time desc')->limit($page->firstRow.','.$page->pageSize)->select();
        if($data){
            foreach ($data as $key=>$val){
                $where['dataid'] = $val['activity_id'];
                $num = $this->getOrderNum($where,"spokesman_user_id3 = {$val['spokesman_user_id']} or spokesman_user_id2 = {$val['spokesman_user_id']} or spokesman_user_id1 = {$val['spokesman_user_id']}");
                //该代言人代言该活动的所有订单
                $data[$key]['total'] = $num['total'];
                $data[$key]['no_pay'] = $num['no_pay'];
                $data[$key]['refund'] = $num['refund'];
                $data[$key]['spokesman_pay_num'] = $num['pay'];
            }
        }
//        var_dump($data);exit();
        $arr = array();
        $arr['search'] = $search;
//        var_dump($arr['search']);
        $arr['pager'] = $page;
        $arr['data'] = $data;
        $arr['u_chrname'] = $u_chrname;

        return $arr;
    }

    /**
     * 代言人订单明细
     * @param $request
     * @return array
     * @throws Exception
     * @throws \think\exception\DbException
     */
    public function spokesman_order($request)
    {
        $Search_arr = array();
        $search = array();

        $Search_arr['idsite'] = session('idsite');
        $search['ordersn'] = '';
        if (isset($request['ordersn']) && $request['ordersn'] != '') {
            $Search_arr['ordersn'] = array('like', '%' . $request['ordersn'] . '%');
            $search['ordersn'] = $request['ordersn'];
        }
        $search['wechatid'] = '';
        if (isset($request['wechatid']) && $request['wechatid'] != '') {
            $Search_arr['wechatid'] = array('like', '%' . $request['wechatid'] . '%');
            $search['wechatid'] = $request['wechatid'];
        }
        //报名人姓名
        $search['chrusername'] = '';
        if (isset($request['chrusername']) && $request['chrusername'] != '') {
            $Search_arr['chrusername'] = array('like', '%' . $request['chrusername'] . '%');
            $search['chrusername'] = $request['chrusername'];
        }
        $search['chrtitle'] = '';
        if (isset($request['chrtitle']) && $request['chrtitle'] != '') {
            $Search_arr['chrtitle'] = array('like', '%' . $request['chrtitle'] . '%');
            $search['chrtitle'] = $request['chrtitle'];
        }
        $search['state'] = '0';
        if (isset($request['state']) && $request['state'] != '' && $request['state'] != '0') {
            $Search_arr['state'] = $request['state'];
            $search['state'] = $request['state'];
        }
        $search['chrkey'] = '';
        if (isset($request['chrkey']) && $request['chrkey'] != '') {
            $Search_arr['txtdata'] = array('like', '%' . $request['chrkey'] . '%');
            $search['chrkey'] = $request['chrkey'];
        }
        $search['dtstart'] = "";
        $search['dtend'] = "";
        if (isset($request['dtstart']) && $request['dtstart'] != '' && isset($request['dtend']) && $request['dtend'] != '') {
            $Search_arr['dtcreatetime'] = array(array('>', $request['dtstart']), array('<', $request['dtend'] . " 23:59:59"),"and");
//            $Search_arr['dtcreatetime'] = array('<', $request['dtend'] . " 23:59:59");
            $search['dtstart'] = $request['dtstart'];
            $search['dtend'] = $request['dtend'];
        }
        //代言人id
        $search['idmember']=empty($request['idmember'])?"":intval($request['idmember']);
        $search['u_chrname']=empty($request['u_chrname'])?"":intval($request['u_chrname']);
        //是否参与结算
        $search['is_balance']=empty($request['is_balance'])?"":intval($request['is_balance']);
        //如果存在活动id
        $search['activity_id']=empty($request['activity_id'])?"":intval($request['activity_id']);
        //是否是导出数据
        $search['export']=empty($request['export'])?"":$request['export'];
        //活动id
        if($search['activity_id']!='')
        {
            $Search_arr['dataid']= array('=',$search['activity_id']);
        }
        //代言人id
        if($search['idmember']!='')
        {
            $Search_arr['spokesman_user_id3|spokesman_user_id2'] = array('=',$search['idmember']);
        }
        if($search['u_chrname']!='')
        {
            $Search_arr['spokesman_user_id3|spokesman_user_id2|spokesman_user_id1']= array('=',$search['u_chrname']);
        }

        if($search['is_balance']!='')
        {
            $Search_arr['is_balance']= array('=',$search['is_balance']);
        }

        $Search_arr['source'] = '代言人订单';

        $count = db('order')->where($Search_arr)->count();
        $page = new Page($count, PAGE_SIZE);
        if($search['export'] == 10){
            $data = db('order')->where($Search_arr)->order('id desc')->select();
        }else{
            $data = db('order')->where($Search_arr)->order('id desc')->limit($page->firstRow . ',' . $page->pageSize)->select();
        }

        $all_where['idsite'] = ['=',$this->siteid];
        $all_where['spokesman_grade']= ['in','1,2'];
        //查询所有的代言人数据(不分页)
        $u_chrname = db('member')->field('u_chrname,idmember')->distinct('u_chrname')->where($all_where)->order('spokesman_time desc')->select();

        foreach ($data as &$value){
            if((int)$value["ischarge"]==1){
                $value["price"] = "0.00";
            }
        }

        $arr = array();
        $arr['search'] = $search;
        $arr['pager'] = $page;
        $arr['data'] = $data;
        $arr['u_chrname'] = $u_chrname;
        return $arr;
    }

    /**
     * 获取总报名/未付款/已退款订单数量
     * @param $where
     * @param $where_str
     * @return array
     */
    public  function getOrderNum($where,$where_str)
    {
        $where['idsite'] = session('idsite');
        $where['source'] = '代言人订单';
        $order =  db('order')->field('state')->where($where)->where($where_str)->select();
        $num = array('total'=>0,'no_pay'=>0,'refund'=>0,'pay'=>0);
        if($order){
            foreach ($order as $value){
                $num['total'] += 1;
                if($value['state'] == 12){
                    $num['no_pay'] += 1;
                }
                //已部分退款，继续服务;已退款，继续服务;已退款，终止服务;已部分退款，终止服务;
                if (in_array($value['state'],array(6,7,11,13))){
                    $num['refund'] += 1;
                }
                //如果是已付款
                if (in_array($value['state'],array(3,4,5,6,7,8,11,13,14))){
                    $num['pay'] += 1;
                }
            }
        }
        return $num;
    }
}