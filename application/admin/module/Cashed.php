<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/14
 * Time: 11:37
 */

namespace app\admin\module;
use think\Model;
use think\Page;
use think\Request;


class Cashed extends Model{
    protected  $siteid=0;
    function __construct($idStie){
        $this->siteid=$idStie;
        parent::__construct();
    }

    /**
     * 查询开启的现金券计划列表
     * @return mixed
     */
    public function getCashedPlan(){
        $result = db('cashed_plan')->field('id,plan_name')->where(['site_id'=>$this->siteid,'is_open'=>1])->order('create_time desc')->select();
        return $result;
    }

    /**
     * 现金券计划列表
     * @param $request
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index($request){
//        $search=array();
//        $Search_arr1=array();

        //名称
        $search['plan_name']=empty($request['plan_name'])?"":$request['plan_name'];
        $search['dtstart']=empty($request['dtstart'])?"":$request['dtstart'];
        $search['dtend']=empty($request['dtend'])?"":$request['dtend'];
        $search['is_open']=empty($request['is_open'])?'':$request['is_open'];
        if($search['plan_name']!='')
        {
            $Search_arr1['plan_name']= array('like','%'.$search['plan_name'].'%');
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

        //是否启用
        if($search['is_open']!='')
        {
            $Search_arr1['is_open']= ['=',$search['is_open']];
        }
        $Search_arr1['site_id']= ['=',$this->siteid];

        //查询条数
        $count = db('cashed_plan')->where($Search_arr1)->count();

        $page = new Page($count,PAGE_SIZE);
        //查询数据
        $data = db('cashed_plan')->where($Search_arr1)->order('create_time desc')->limit($page->firstRow.','.$page->pageSize)->select();

//        var_dump($data);exit;

        $arr = array();
        $arr['search'] = $search;
        $arr['pager'] = $page;
        $arr['data'] = $data;

        return $arr;
    }

    /**
     * 增改查页面处理
     * @param $data
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function deal($data){
        //如果有id的话
        if(array_key_exists('id',$data)){
            $result = db('cashed_plan')->where(['site_id'=>$this->siteid,'id'=>$data['id']])->find();
            //判断概率一
            if($result['plan_probability1']){
                $plan_probability1 = explode(',',$result['plan_probability1']);
                $result['price_min1'] = $plan_probability1[0];
                $result['price_max1'] = $plan_probability1[1];
                $result['rate1'] = $plan_probability1[2];
            }
            //判断概率二
            if($result['plan_probability2']){
                $plan_probability1 = explode(',',$result['plan_probability2']);
                $result['price_min2'] = $plan_probability1[0];
                $result['price_max2'] = $plan_probability1[1];
                $result['rate2'] = $plan_probability1[2];
            }else{
                $result['price_min2'] = '';
                $result['price_max2'] = '';
                $result['rate2'] = '';
            }
            //判断概率三
            if($result['plan_probability3']){
                $plan_probability1 = explode(',',$result['plan_probability3']);
                $result['price_min3'] = $plan_probability1[0];
                $result['price_max3'] = $plan_probability1[1];
                $result['rate3'] = $plan_probability1[2];
            }else{
                $result['price_min3'] = '';
                $result['price_max3'] = '';
                $result['rate3'] = '';
            }
        }else{
            $result = [
                'id'=>'',
                'cashed_validity_day' => '',
                'plan_name' =>'',
                'plan_desc'=>'',
                'is_open'=>'',
                'cashed_num'=>'',
                'site_id'=>$this->siteid,
                'price_min1'=>'',
                'price_max1'=>'',
                'rate1'=>'',
                'price_min2'=>'',
                'price_max2'=>'',
                'rate2'=>'',
                'price_min3'=>'',
                'price_max3'=>'',
                'rate3'=>'',
            ];
        }
//        var_dump($result);exit;
        if(!empty($data['action']))
            $result['action'] = $data['action'];
        return $result;
    }

    /**
     * 执行添加和修改
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function postData($data){
        //有效期天数
        $add_data['cashed_validity_day'] = isset($data['cashed_validity_day']) && !empty($data['cashed_validity_day'])?intval($data['cashed_validity_day']):'';
        //名称
        $add_data['plan_name'] = isset($data['plan_name']) && !empty($data['plan_name'])?trim($data['plan_name']):'';
        //描述
        $add_data['plan_desc'] = isset($data['plan_desc']) ?trim($data['plan_desc']):'';
        //是否启用
        $add_data['is_open'] = isset($data['is_open']) && !empty($data['is_open'])?intval($data['is_open']) :'';
        //红包数量
        $add_data['cashed_num'] = isset($data['cashed_num']) && !empty($data['cashed_num'])?intval($data['cashed_num']) :'';
        //项目一概率
        if(isset($data['price_min1'])  && isset($data['price_max1'])  && isset($data['rate1']) && !empty($data['rate1'])){
            $add_data['plan_probability1'] = $data['price_min1'] .','.$data['price_max1'].','.$data['rate1'];
        }
        //项目二概率
        if(isset($data['price_min2'])  && isset($data['price_max2'])  && isset($data['rate2']) && !empty($data['rate2'])){
            $add_data['plan_probability2'] = $data['price_min2'] .','.$data['price_max2'].','.$data['rate2'];
        }
        //项目三概率
        if(isset($data['price_min3'])  && isset($data['price_max3'])  && isset($data['rate3']) && !empty($data['rate3'])){
            $add_data['plan_probability3'] = $data['price_min3'] .','.$data['price_max3'].','.$data['rate3'];
        }
        if($data['action'] == 'add'){
            $add_data['create_time'] = date('Y-m-d H:i:s',time());
            $add_data['create_account_id'] = session('AccountID');
            $add_data['account_name'] = session('UserName');
            $add_data['site_id'] = session('idsite');
            //执行数据的插入
            $bool = db('cashed_plan')->insert($add_data);
        }else{
            //修改
            if($data['id'] > 0){
                //执行数据的修改
                $bool = db('cashed_plan')->where(['id'=>$data['id']])->update($add_data);
            }
        }
        return $bool;
    }

    /**
     * 删除
     * @param $data
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del($data){
        if(strstr($data['id'],',')){
            $id = explode(',',$data['id']);
            for ($i=0;$i<count($id);$i++){
                $bool = db('cashed_plan')->where('id',$id[$i])->delete();
            }
        }else{
            $bool = db('cashed_plan')->where('id',$data['id'])->delete();
        }
        return $bool;
    }

    /**
     * 现金券领取记录
     * @param $request
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function receive_record($request){
        //领取编号
        $search['receive_no']=empty($request['receive_no'])?"":trim($request['receive_no']);
        //领取时间
        $search['dtstart']=empty($request['dtstart'])?"":$request['dtstart'];
        $search['dtend']=empty($request['dtend'])?"":$request['dtend'];
        //使用时间
        $search['utstart']=empty($request['utstart'])?"":$request['utstart'];
        $search['utend']=empty($request['utend'])?"":$request['utend'];
        $search['receive_member_id']=empty($request['receive_member_id'])?"":$request['receive_member_id'];
        //分享人昵称或者姓名
        $search['share_nick_name']=empty($request['share_nick_name'])?'':$request['share_nick_name'];
        //领券人
        $search['receive_nick_name']=empty($request['receive_nick_name'])?'':$request['receive_nick_name'];
        //现金券类型
        $search['cashed_type']=empty($request['cashed_type'])?'':$request['cashed_type'];
        //状态
        $search['used_status']=empty($request['used_status'])?'':$request['used_status'];
        //现金券计划
        $search['share_plan_id']=empty($request['share_plan_id'])?'':$request['share_plan_id'];
        //活动
        $search['receive_activity_id']=empty($request['receive_activity_id'])?'':$request['receive_activity_id'];
        if($search['cashed_type'] == '' || $search['cashed_type'] == 10){
            $search['receive_activity_id'] = '';
            $search['share_plan_id'] = '';
        }
        if($search['receive_no']!='')
        {
            $Search_arr1['receive_no']= array('like','%'.$search['receive_no'].'%');
        }
        //分享人昵称或者姓名
        if($search['share_nick_name']!='')
        {
            $Search_arr1['share_nick_name']= array('like','%'.$search['share_nick_name'].'%');
        }
        //领券人
        if($search['receive_nick_name']!='')
        {
            $Search_arr1['receive_nick_name']= array('like','%'.$search['receive_nick_name'].'%');
        }

        //现金券类型
        if($search['cashed_type']!='' && $search['cashed_type']!=10)
        {
            $Search_arr1['cashed_type']= array('=',$search['cashed_type']);
        }

        //现金券计划
        if($search['share_plan_id']!='')
        {
            $Search_arr1['share_plan_id']= array('=',$search['share_plan_id']);
        }
        //活动
        if($search['receive_activity_id']!='')
        {
            $Search_arr1['receive_activity_id']= array('=',$search['receive_activity_id']);
        }
        //状态
        if($search['used_status']!='' && $search['cashed_type']!='')
        {
            $Search_arr1['used_status']= array('=',$search['used_status']);
        }
        //状态
        if($search['used_status']!='')
        {
            $Search_arr1['used_status']= array('=',$search['used_status']);
        }
        //领取id
        if($search['receive_member_id']!='')
        {
            $Search_arr1['receive_member_id']= array('=',$search['receive_member_id']);
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

        //校验使用时间的开始时间
        if(!empty($search['utstart']) && empty($search['utend'])){
            if(\DateTime::createFromFormat('Y-m-d',$search['utstart'])){
                $Search_arr1['used_time']= array('>=',$search['utstart']);
            }
        }
        //校验使用时间的结束时间
        if(!empty($search['utend']) && (\DateTime::createFromFormat('Y-m-d',$search['utend'])) && empty($search['utstart'])){
            $Search_arr1['used_time']= ['<=',$search['utend'].' 23:59:59'];
        }

        if(!empty($search['utend']) && (\DateTime::createFromFormat('Y-m-d',$search['utend'])) && !empty($search['utstart']) && \DateTime::createFromFormat('Y-m-d',$search['utstart'])){
            $Search_arr1['used_time']= [['>=',$search['utstart']],['<=',$search['utend'].' 23:59:59']];
        }
        $Search_arr1['site_id']= ['=',$this->siteid];

        //查询条数
        $count = db('cashed_card_receive')->where($Search_arr1)->count();

        $page = new Page($count,PAGE_SIZE);
        //查询数据
        $data = db('cashed_card_receive')->where($Search_arr1)->order('create_time desc')->limit($page->firstRow.','.$page->pageSize)->select();
        //查询领取信息中的现金券计划
        $cashed_plan = db('cashed_card_receive')->field('id,share_plan_id,receive_cashed_name')->where(['site_id'=>$this->siteid])->where('share_plan_id > 0')->group('share_plan_id')->select();
        //查询领取信息中的现金券活动信息
        $cashed_activity = db('cashed_card_receive')->field('id,receive_activity_id,receive_activity_name')->where(['site_id'=>$this->siteid])->where('receive_activity_id > 0')->group('receive_activity_id')->select();
//        var_dump($cashed_activity);
//        var_dump($data);exit;
        //汇总的信息
        $all = db('cashed_card_receive')->field('id,used_status,cashed_amount')->where($Search_arr1)->select();
        $all_number = db('cashed_card_receive')->field('id,used_status,cashed_amount')->where($Search_arr1)->group('receive_member_id')->select();
        $all_amount = 0;//总金额
        $had_used_amount = 0;//已用金额
        $no_used_amount = 0;//未用金额
        $past_amount = 0;//过期金额
        $freeze_amount = 0;//冻结金额
        if($all){
            foreach ($all as $value){
                $all_amount += $value['cashed_amount'];
                //已用
                if($value['used_status'] == 5){
                    $had_used_amount += $value['cashed_amount'];
                    //未用
                }elseif ($value['used_status'] == 1){
                    $no_used_amount += $value['cashed_amount'];
                    //过期金额
                }elseif ($value['used_status'] == 15){
                    $past_amount += $value['cashed_amount'];
                    //冻结金额
                }elseif ($value['used_status'] == 10){
                    $freeze_amount += $value['cashed_amount'];
                }
            }
        }
        $arr = array();
        $arr['search'] = $search;
//        var_dump($arr['search']);
        $arr['pager'] = $page;
        $arr['data'] = $data;
        $arr['count']['all_people'] = count($all);//领取总次数
        $arr['count']['all_number'] = count($all_number);//领取总人数
        $arr['count']['all_amount'] = $all_amount;//总金额
        $arr['count']['had_used_amount'] = $had_used_amount;//已用金额
        $arr['count']['no_used_amount'] = $no_used_amount;//未用金额
        $arr['count']['past_amount'] = $past_amount;//过期金额
        $arr['count']['freeze_amount'] = $freeze_amount;//冻结金额
        $arr['cashed_plan'] = $cashed_plan;//查询领取信息中的现金券计划
        $arr['cashed_activity'] = $cashed_activity;//查询领取信息中的现金券活动信息

        return $arr;
    }

    /**
     * 设置新用户关注发券
     * @param $data
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function new_member_cashed_set($data){
        $result = db('new_member_cashed_set')->where(['site_id'=>$this->siteid])->find();
        //如果有id的话
        if(!$result){
            $result = [
                'id'=>'',
                'is_send_cashed' => '',
                'send_cashed_amount' =>'',
                'send_cashed_validity'=>'',
                'site_id'=>$this->siteid
            ];
        }
        if(Request::instance()->isPost()){
            //如果存在id，那么就是之前设置过，修改
            if(!array_key_exists('id',$data)){
                return $bool = db('new_member_cashed_set')->insert($data);
            }else{
                return $bool = db('new_member_cashed_set')->where(['id'=>$data['id'],'site_id'=>$this->siteid])->update($data);
            }
        }
//        var_dump($result);exit;

        return $result;
    }

    /**
     * 查看用户的领取现金券情况
     * @param $userid
     * @return mixed
     * @throws \think\Exception
     */
    public function get_user_receive_cashed($userid){
        //未使用
        $result['no_use'] = db('cashed_card_receive')->where(['used_status'=>1,'site_id'=>$this->siteid,'receive_member_id'=>$userid])->count();
        //已使用
        $result['has_use'] = db('cashed_card_receive')->where(['used_status'=>5,'site_id'=>$this->siteid,'receive_member_id'=>$userid])->count();
        //冻结
        $result['freeze'] = db('cashed_card_receive')->where(['used_status'=>10,'site_id'=>$this->siteid,'receive_member_id'=>$userid])->count();
        //过期
        $result['past'] = db('cashed_card_receive')->where(['used_status'=>15,'site_id'=>$this->siteid,'receive_member_id'=>$userid])->count();
        return $result;
    }

    /**
     * 现金券报表列表
     */
    public function cashed_report_list($data)
    {
        if (!isset($data["time_range"])) {
            return false;
        }
        $search['begin_year_time'] = '';
        $search['end_year_time'] = '';
        $search['begin_month_time'] = '';
        $search['end_month_time'] = '';
        $search['begintime'] = '';
        $search['endtime'] = '';
        $start_date = $end_date = $type = "";
        switch ($data["time_range"]) {
            case "year":
                if(\DateTime::createFromFormat('Y',$data['begin_year_time']) && \DateTime::createFromFormat('Y',$data['end_year_time'])){
                    $start_date = $data['begin_year_time'].'-01-01';//年的开始时间
                    $end_date = $data['end_year_time'].'-12-28';//年的结束时间
                    $type = '%Y-%m';
                    $sub = $data['end_year_time'] - $data['begin_year_time'] + 1;
                    $lasting_day = $sub*12;
                    $search['begin_year_time'] = $data['begin_year_time'];
                    $search['end_year_time'] = $data['end_year_time'];
                }
                break;
            case "month":
                if(\DateTime::createFromFormat('Y-m',$data['begin_month_time']) && \DateTime::createFromFormat('Y-m',$data['end_month_time'])){
                    $start_date = $data['begin_month_time'].'-01';//年的开始时间
                    $end_date = $data['end_month_time'].'-'.$this->daysInMonth(date('Y',strtotime($data['end_month_time'])),date('m',strtotime($data['end_month_time'])));
                    $type = '%Y-%m-%d';
                    $lasting_day = round((strtotime($end_date . " 23:59:59") - strtotime($start_date)) / (3600 * 24));
                    $search['begin_month_time'] = $data['begin_month_time'];
                    $search['end_month_time'] = $data['end_month_time'];
                }
                break;
            case "custom":
                if(\DateTime::createFromFormat('Y-m-d',$data['begintime']) && \DateTime::createFromFormat('Y-m-d',$data['endtime'])){
                    $start_date = $data["begintime"];
                    $end_date = $data["endtime"];
                    $type = '%Y-%m-%d';
                    $lasting_day = round((strtotime($end_date . " 23:59:59") - strtotime($start_date)) / (3600 * 24));
                    $search['begintime'] = $data['begintime'];
                    $search['endtime'] = $data['endtime'];
                }
                break;
        }
        if (empty($start_date) || empty($end_date)) {
            return false;
        }
        //获取领券总人数
        $receive_people = $this->get_receive_people_num($start_date, $end_date,$type);
//        var_dump($receive_people);
        //领券总次数
        $receive_total = $this->get_receive_num($start_date, $end_date,$type);
        //领取总金额
        $receive_total_amount = $this->get_receive_amount($start_date, $end_date,$type);
        //已用总金额
        $had_used_amount = $this->get_had_used_amount($start_date, $end_date,$type);
        //未用总金额
        $no_used_amount = $this->get_no_used_amount($start_date, $end_date,$type);
        //过期总金额
        $no_past_amount = $this->get_past_amount($start_date, $end_date,$type);
        //冻结总金额
        $freeze_amount = $this->get_freeze_amount($start_date, $end_date,$type);
        $list = array();
        //定义初始化值
        $total_receive_people = $total_receive_total = $total_receive_total_amount = $total_had_used_amount = $total_no_used_amount = $total_no_past_amount = $total_freeze_amount =  0;
        //echo $end_date;
       // echo date("Y-m-d H:i:s", strtotime("-10 month",strtotime($end_date)));exit;
        for ($i = ($lasting_day - 1); $i >= 0; $i--) {
            //如果是年的话
            if($data["time_range"] == 'year'){
                $date = date("Y-m", strtotime("-".$i." month",strtotime($end_date)));
            }else{
                $date = date("Y-m-d", (strtotime($end_date . " 23:59:59") - (3600 * 24 * $i)));
            }
            $list['data'][$date]["receive_people"] = isset($receive_people[$date]) ? $receive_people[$date] : 0;//获取领券总人数
            $list['data'][$date]["receive_total"] = isset($receive_total[$date]) ? $receive_total[$date] : 0;//领券总次数
            $list['data'][$date]["receive_total_amount"] = isset($receive_total_amount[$date]) ? $receive_total_amount[$date] : 0;//领取总金额
            $list['data'][$date]["had_used_amount"] = isset($had_used_amount[$date]) ? $had_used_amount[$date] : 0;//已用总金额
            $list['data'][$date]["no_used_amount"] = isset($no_used_amount[$date]) ? $no_used_amount[$date] : 0;//未用总金额
            $list['data'][$date]["no_past_amount"] = isset($no_past_amount[$date]) ? $no_past_amount[$date] : 0;//过期总金额
            $list['data'][$date]["freeze_amount"] = isset($freeze_amount[$date]) ? $freeze_amount[$date] : 0;//冻结总金额
            $total_receive_people += $list['data'][$date]["receive_people"];
            $total_receive_total += $list['data'][$date]["receive_total"];
            $total_receive_total_amount += $list['data'][$date]["receive_total_amount"];
            $total_had_used_amount += $list['data'][$date]["had_used_amount"];
            $total_no_used_amount += $list['data'][$date]["no_used_amount"];
            $total_no_past_amount += $list['data'][$date]["no_past_amount"];
            $total_freeze_amount += $list['data'][$date]["freeze_amount"];
        }
//        var_dump($list);exit;
        if($list) {
            $list["汇总"] = array("total_receive_people"=>$total_receive_people,"total_receive_total"=>$total_receive_total,"total_receive_total_amount" => $total_receive_total_amount,"total_had_used_amount" => $total_had_used_amount,"total_no_used_amount" => $total_no_used_amount,"total_no_past_amount" => $total_no_past_amount,'total_freeze_amount'=>$total_freeze_amount);
        }else{
            $list["汇总"] = [];
        }
        $list['search'] = $search;
        return $list;
    }

    /**
     * 获取领券总人数
     */
    private function get_receive_people_num($start_date,$end_date,$type)
    {
        if($type == 'year'){
            $end_date = date('Y-m',strtotime($end_date)).'-31';
        }
        $idsite = session('idsite');
        $where = "site_id =$idsite and create_time>= '{$start_date} 00:00:00'"." and create_time<= '{$end_date} 23:59:59'";
        $sql = "select DATE_FORMAT(create_time,'{$type}') as d,COUNT( DISTINCT receive_member_id)  as num  from cms_cashed_card_receive where ".$where." group by d";
//        echo $sql;exit;
        $list = db("cashed_card_receive")->query($sql);
        $return = array();

        foreach ($list as $member){
            $return[$member["d"]] = $member["num"];
        }
        return $return;
    }

    /**
     * 领券总金额
     */
    private function get_receive_amount($start_date,$end_date,$type)
    {
        if($type == 'year'){
            $end_date = date('Y-m',strtotime($end_date)).'-31';
        }
        $idsite = session('idsite');
        $where = "site_id =$idsite and create_time>= '{$start_date} 00:00:00'"." and create_time<= '{$end_date} 23:59:59'";
        $sql = "select DATE_FORMAT(create_time,'{$type}') as d, sum(cashed_amount) as num from cms_cashed_card_receive where ".$where." group by d ";
        $list = db("cashed_card_receive")->query($sql);
//        var_dump($list);exit;
        $return = array();
        foreach ($list as $member){
            $return[$member["d"]] = $member["num"];
        }
        return $return;
    }

    /**
     * 领券总次数
     */
    private function get_receive_num($start_date,$end_date,$type)
    {
        if($type == 'year'){
            $end_date = date('Y-m',strtotime($end_date)).'-31';
        }
        $idsite = session('idsite');
        $where = "site_id =$idsite and create_time>= '{$start_date} 00:00:00'"." and create_time<= '{$end_date} 23:59:59'";
        $sql = "select DATE_FORMAT(create_time,'{$type}') as d, count(*) as num from cms_cashed_card_receive where ".$where." group by d ";
        $list = db("cashed_card_receive")->query($sql);
        $return = array();
        foreach ($list as $member){
            $return[$member["d"]] = $member["num"];
        }
        return $return;
    }

    /**
     * 已用总金额
     */
    private function get_had_used_amount($start_date,$end_date,$type)
    {
        if($type == 'year'){
            $end_date = date('Y-m',strtotime($end_date)).'-31';
        }
        $idsite = session('idsite');
        $where = "site_id =$idsite and create_time>= '{$start_date} 00:00:00'"." and create_time<= '{$end_date} 23:59:59' and used_status = 5";//已用的总金额
        $sql = "select DATE_FORMAT(create_time,'{$type}') as d, sum(cashed_amount) as num from cms_cashed_card_receive where ".$where." group by d ";
        $list = db("cashed_card_receive")->query($sql);
        $return = array();
        foreach ($list as $member){
            $return[$member["d"]] = $member["num"];
        }
        return $return;
    }

    /**
 * 未用总金额
 */
    private function get_no_used_amount($start_date,$end_date,$type)
    {
        if($type == 'year'){
            $end_date = date('Y-m',strtotime($end_date)).'-31';
        }
        $idsite = session('idsite');
        $where = "site_id =$idsite and create_time>= '{$start_date} 00:00:00'"." and create_time<= '{$end_date} 23:59:59' and used_status = 1";
        $sql = "select DATE_FORMAT(create_time,'{$type}') as d, sum(cashed_amount) as num from cms_cashed_card_receive where ".$where." group by d ";
        $list = db("cashed_card_receive")->query($sql);
        $return = array();
        foreach ($list as $member){
            $return[$member["d"]] = $member["num"];
        }
        return $return;
    }

    /**
     * 过期总金额
     */
    private function get_past_amount($start_date,$end_date,$type)
    {
        if($type == 'year'){
            $end_date = date('Y-m',strtotime($end_date)).'-31';
        }
        $idsite = session('idsite');
        $where = "site_id =$idsite and create_time>= '{$start_date} 00:00:00'"." and create_time<= '{$end_date} 23:59:59' and used_status = 15";
        $sql = "select DATE_FORMAT(create_time,'{$type}') as d, sum(cashed_amount) as num from cms_cashed_card_receive where ".$where." group by d ";
        $list = db("cashed_card_receive")->query($sql);
        $return = array();
        foreach ($list as $member){
            $return[$member["d"]] = $member["num"];
        }
        return $return;
    }

    /**
     * 冻结总金额
     */
    private function get_freeze_amount($start_date,$end_date,$type)
    {
        if($type == 'year'){
            $end_date = date('Y-m',strtotime($end_date)).'-31';
        }
        $idsite = session('idsite');
        $where = "site_id =$idsite and create_time>= '{$start_date} 00:00:00'"." and create_time<= '{$end_date} 23:59:59' and used_status = 10";
        $sql = "select DATE_FORMAT(create_time,'{$type}') as d, sum(cashed_amount) as num from cms_cashed_card_receive where ".$where." group by d ";
        $list = db("cashed_card_receive")->query($sql);
        $return = array();
        foreach ($list as $member){
            $return[$member["d"]] = $member["num"];
        }
        return $return;
    }

    /**
     * 判断某年的某月有多少天
     * @return [type] [description]
     */
    function daysInMonth($year='',$month=''){
        if(empty($year)) $year = date('Y');
        if(empty($month)) $month = date('m');
        if (in_array($month, array(1, 3, 5, 7, 8, '01', '03', '05', '07', '08', 10, 12))) {
            $text = '31';         //月大
        }elseif ($month == 2 || $month == '02'){
            if ( ($year % 400 == 0) || ( ($year % 4 == 0) && ($year % 100 !== 0) ) ) {   //判断是否是闰年
                $text = '29';        //闰年2月
            } else {
                $text = '28';          //平年2月
            }
        } else {
            $text = '30';             //月小
        }
        return $text;
    }

    /**
     * 赠送现金券
     * @param $param
     * @return int|string
     * @throws \think\Exception
     */
    public function give_cashed($param){
        //进行封装添加领取记录的数据
        $add_receive_param['create_time'] = date('Y-m-d H:i:s',time());//领取时间
        // 保证不会有重复领取编号存在
        while (true) {
            $receive_no = date('YmdHis') . rand(100000, 999999); // 订单编号
            $receive_no_count = db('cashed_card_receive')->where("receive_no = '$receive_no'")->count();
            if ($receive_no_count == 0)
                break;
        }
        $add_receive_param['receive_no'] = $receive_no;//领取编号
        $add_receive_param['cashed_type'] = 4;//现金券类型
        $add_receive_param['cashed_amount'] = $param['cashed_amount'];//现金券金额
        $add_receive_param['cashed_validity_time'] = date('Y-m-d H:i:s',strtotime(" + {$param['cashed_validity_day']} day",time()));//有效期时间
        $add_receive_param['cashed_validity_day'] = $param['cashed_validity_day'];//有效期天数
        $add_receive_param['receive_cashed_name'] = '赠送现金券';//领取的现金券标题
        $add_receive_param['receive_activity_name'] = '';//领取来源（活动名称）
        $add_receive_param['receive_activity_id'] = 0;
        $add_receive_param['receive_member_id'] = $param['idmember'];//领取人会员id（用户）
        $add_receive_param['receive_nick_name'] = $param['nickname'];//领取人的昵称
        $add_receive_param['receive_header_image'] = $param['userimg'];//领取人的头像
        $add_receive_param['receive_source'] = 1;//领取渠道
        $add_receive_param['used_status'] = 1;//使用状态
        $add_receive_param['site_id'] = $this->siteid;//站点id
        $add_receive_param['remark'] = $param['remark'];//赠券原因
        //执行插入数据
        $bool = db('cashed_card_receive')->insert($add_receive_param);
        //获取站点
        $sitecode = getSiteCode(session('idsite'));
        if($bool){
            // 发送客服消息
            $msg = '您好,您已成功获得【'.$param['cashed_amount'].'】元的现金券，欢迎使用！现金券已经存入“会员中心->我的现金券”中，快去看看吧！<a href=\"'.request()->domain().'/'.$sitecode.'/cashedlist/1\">【查看详情】</a>';
            send_ordinary_msg($sitecode, $param['openid'], $msg);
        }
        return $bool;
    }

    /**
     * 现金券详情
     * @param $param
     * @return int|string
     * @throws \think\Exception
     */
    public function receive_detail($param){
        $receive_cashed = db('cashed_card_receive')->where(['id'=>$param['id']])->find();
        return $receive_cashed;
    }
}