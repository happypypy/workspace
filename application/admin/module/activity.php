<?php
/**
 * 天络CMS
 * ============================================================================
 * 版权所有 2017-2027 深圳天络科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinasky.net
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: huangshixin
 * Date:2018-06-17 */
namespace app\admin\module;

use think\db;
use think\db\Query;
use think\Exception;
use think\Model;
use think\Page;
use think\wx\Utils\HttpCurl;

class activity extends Model
{

    protected $siteid = 0;
    public function __construct($idStie)
    {
        $this->siteid = $idStie;
        parent::__construct();
    }

    //列表
    public function index($request)
    {
        $Search_str = '';
        $Search_arr = array();
        $search = array();
        $Search_arr1 = array();

        $search['chrtitle'] = empty($request['chrtitle']) ? "" : $request['chrtitle'];
        $search['fidtype'] = empty($request['fidtype']) ? "" : $request['fidtype'];
        $search['dtstart'] = empty($request['dtstart']) ? "" : $request['dtstart'];
        $search['dtend'] = empty($request['dtend']) ? "" : $request['dtend'];
        $search['dtpublishtime_s'] = empty($request['dtpublishtime_s']) ? "" : $request['dtpublishtime_s'];
        $search['dtpublishtime_e'] = empty($request['dtpublishtime_e']) ? "" : $request['dtpublishtime_e'];
        $search['chrtags'] = empty($request['chrtags']) ? [] : $request['chrtags'];
        $search['ischarge'] = empty($request['ischarge']) ? "" : $request['ischarge'];
        $search['nodeid'] = empty($request['nodeid']) ? 0 : $request['nodeid'];

        if (isset($request['chkcontentlev']) && is_numeric($request['chkcontentlev'])) {
            $search['chkcontentlev'] = $request["chkcontentlev"];
            $Search_arr1['chkcontentlev'] = $search['chkcontentlev'];
        } else {
            $search['chkcontentlev'] = "";
        }
        if (isset($request['chkisindex']) && is_numeric($request['chkisindex'])) {
            $search['chkisindex'] = $request["chkisindex"];
            $Search_arr1['chkisindex'] = $search['chkisindex'];
        } else {
            $search['chkisindex'] = "";
        }

        if ($search['chrtitle'] != '') {
            $Search_arr1['chrtitle'] = array('like', '%' . $search['chrtitle'] . '%');
        }

        if ($search['fidtype'] != '') {
            $Search_arr1['fidtype'] = $search['fidtype'];
        }
        if ($search['ischarge'] != '') {
            $Search_arr1['ischarge'] = $search['ischarge'];
        }

        if ($search['nodeid'] != 0) {
            $Search_arr1['nodeid'] = $search['nodeid'];
        } else {
            $arr['data'] = array();
            return $arr;
        }

        if (!empty($search['chrtags'])) {
            $tmp = "";
            foreach ($search['chrtags'] as $k => $vo) {
                $tmp = $tmp . " chrtags like '%," . $vo . ",%' or";
            }
            $tmp = trim($tmp, "or");
            if ($tmp != "") {
                $Search_str = $Search_str . " (" . $tmp . ") and ";
            }
            //$Search_arr1['chrtags']=array('in',implode(',', $search['chrtags']));

        }
        $Search_arr1['siteid'] = $this->siteid;
        if ($request['intflag'] == 5) {
            $Search_arr1['dtstart'] = array(array('gt', date("Y-m-d", strtotime("1 day"))), array('lt', date("Y-m-d", strtotime("8 day"))));
            $Search_arr1['intflag'] = 2;
        } else {
            $Search_arr1['intflag'] = $request['intflag'];
        }

        // print_r($Search_str);
        if ($search['dtstart'] != '' && $search['dtend'] != '') {
            $Search_str = $Search_str . "((dtstart > '" . $search['dtstart'] . "' and dtstart <'" . $search['dtend'] . ' 23:59:59' . "') or  (dtend >'" . $search['dtstart'] . "' and dtend <'" . $search['dtend'] . ' 23:59:59' . "')  or (dtstart > '" . $search['dtstart'] . "' and dtend <'" . $search['dtend'] . ' 23:59:59' . "') or (dtstart < '" . $search['dtstart'] . "' and dtend >'" . $search['dtend'] . ' 23:59:59' . "')) and ";
            //  $Search_arr['dtstart']=$search['dtstart'];
            //  $Search_arr['dtend']=$search['dtend'] ." 23:59:59" ;
        }
        if ($search['dtpublishtime_s'] != '' && $search['dtpublishtime_e'] != '') {
            $Search_str = $Search_str . ' dtpublishtime > :dtpublishtime_s and dtpublishtime <:dtpublishtime_e and ';
            $Search_arr['dtpublishtime_s'] = $search['dtpublishtime_s'];
            $Search_arr['dtpublishtime_e'] = $search['dtpublishtime_e'] . " 23:59:59";
        }
        $Search_str = rtrim($Search_str, ' and ');

        if ($Search_str == '') {
            $count = db('activity')->where($Search_arr1)->count();
        } else {
            $count = db('activity')->where($Search_arr1)->where($Search_str, $Search_arr)->count();
        }

        $page = new Page($count, PAGE_SIZE);
        if ($Search_str == '') {
            $data = db('activity')->where($Search_arr1)->order('idactivity desc')->limit($page->firstRow . ',' . $page->pageSize)->select();
        } else {
            $data = db('activity')->where($Search_arr1)->where($Search_str, $Search_arr)->order('idactivity desc')->limit($page->firstRow . ',' . $page->pageSize)->select();
        }

        foreach ($data as &$value) {
            $selcontent = $value["selcontent"];
            $arr = explode("☆", $selcontent);
            $min_price = $max_price = 0;
            if (isset($arr[3])) {
                $arr1 = explode("∮", $arr[3]);
                foreach ($arr1 as $key => $v) {
                    if ($key == 0) {
                        $min_price = $v;
                    }
                    if ($min_price > $v) {
                        $min_price = $v;
                    }
                    if ($max_price < $v) {
                        $max_price = $v;
                    }
                }
            }
            $value["min_price"] = $min_price;
            $value["max_price"] = $max_price;
        }

        $acount[5] = db('activity')->where(array('siteid' => $this->siteid, 'intflag' => 2, 'nodeid' => $search['nodeid']))->where("dtstart>'" . date("Y-m-d", strtotime("1 day")) . "' and dtstart<'" . date("Y-m-d", strtotime("8 day")) . "'")->count();
        $acount[1] = db('activity')->where(array('siteid' => $this->siteid, 'intflag' => 1, 'nodeid' => $search['nodeid']))->count();
        $acount[2] = db('activity')->where(array('siteid' => $this->siteid, 'intflag' => 2, 'nodeid' => $search['nodeid']))->count();
        $acount[3] = db('activity')->where(array('siteid' => $this->siteid, 'intflag' => 3, 'nodeid' => $search['nodeid']))->count();
        $acount[4] = db('activity')->where(array('siteid' => $this->siteid, 'intflag' => 4, 'nodeid' => $search['nodeid']))->count();
        $acount[6] = db('activity')->where(array('siteid' => $this->siteid, 'intflag' => 6, 'nodeid' => $search['nodeid']))->count();
        $arr = array();
        $arr['search'] = $search;
        $arr['pager'] = $page;
        $arr['data'] = $data;
        $arr['acount'] = $acount;

        return $arr;
    }

    public function sendmsg($data)
    {
        $arrData = [];
        $arrData['first'] = array("value" => $data['chrtitle'], "color" => $data['chrtitlecolor']);
        $arrData['keyword1'] = array("value" => $data['activityname'], "color" => $data['activitynamecolor']);
        $arrData['keyword2'] = array("value" => $data['activitytime'], "color" => $data['activitytimecolor']);
        $arrData['remark'] = array("value" => $data['remark'], "color" => $data['remarkcolor']);

        $arr = [];
        $template_key = getWxTemplateId("OPENTM205702264", $this->siteid);
        $arr['Template_key'] = $template_key; //"YjSr0Kwm2OzcnUARdI_PrLmy_0JAQU3Rm8nVUVZm0V8";
        $arr['dataid'] = $data['dataid'];
        $arr['data'] = json_encode($arrData);
        $arr['url'] = $data['chrurl'];
        $arr['touser'] = $data['touser'];
        $arr['inttype'] = $data['inttype'];
        $arr['inttype1'] = $data['inttype1'];
        $arr['username'] = $data['username'];
        $arr['userid'] = $data['userid'];
        $arr['state'] = 1;
        $arr['createtime'] = time();
        $arr['key'] = $data['key'];
        $arr['idsite'] = $this->siteid;
        $arr['ip'] = getip();
        $bl = db("sendmsg")->insert($arr);

        return $bl;

    }

    public function visitlist($data)
    {
        $result3 = [];
        $openid = "";
        $dataid = $data['dataid'];



        $result1=[];
        $result=db('visit_record')->field('openid,MIN(stime) as stime,MAX(stime) as etime,SUM(differtime)as differtime,COUNT(*) as vcount, source')->where(array('aid'=>$dataid,'flag'=>2))->group('openid')->select();
        foreach ($result as $k=>$vo)
        {
            if(empty($vo['openid']))
            {
                continue;
            }
            if (!empty($data['readn']) && $data['readn'] > 0 && $data['readn'] >= $vo['vcount']) {
                continue;
            }
            if (!empty($data['readl']) && $data['readl'] > 0 && $data['readl'] >= $vo['differtime']) {
                continue;
            }

            $result1[$vo['openid']] = $vo;
            $openid = $openid . ",'" . $vo['openid'] . "'";
        }
        $openid = trim($openid, ",");

        if ($openid == "") {
            return [];
        }

        $where = " openid in (" . $openid . ")";
        if (!empty($data['isfollow']) && $data['isfollow'] == 1) {
            $where = $where . " and intstate=1 ";
        }
        if (!empty($data['istel']) && $data['istel'] == 1) {
            $where = $where . " and length(chrtel)>3 ";
        }

        $where1 = "level=2";
        if (!empty($data['regionkey'])) {
            $where1 = $where1 . " and name like '%" . trim($data['regionkey']) . "%' ";
        }

        $sityList = db("region")->where($where1)->select();
        $sityArr = [];
        $sityids = '';
        foreach ($sityList as $k => $vo) {
            $sityArr[$vo['id']] = $vo['name'];
            if (!empty($data['regionkey'])) {
                $sityids = $sityids . "," . $vo['id'];
            }
        }
        $sityids = trim($sityids, ",");

        if (!empty($sityids)) {
            $where = $where . " and intcity in (" . $sityids . ")";
        } elseif (!empty($data['regionkey'])) {
            return [];
        }

        $arruser = [];
        $orderlist = db("order")->field('fiduser,sum(paynum) as vcount ')->where(array('dataid' => $dataid))->group('fiduser')->select();
        foreach ($orderlist as $k => $vo) {
            $arruser[$vo['fiduser']] = $vo['vcount'];
        }

        $result2 = db("member")->where($where)->select();

        if ($result2) {
            foreach ($result2 as $k => $vo) {

                $forward1 = 0;
                $forward2 = 0;
                $result_g = db('forwarded_log')->where(array('userid' => $vo['idmember'], 'datatype' => 2, 'dataid' => $dataid))->select(); //转发数
                if ($result_g) {
                    foreach ($result_g as $x => $vx) {
                        if ($vx['inttype'] == 1) {
                            $forward1 = $forward1 + 1;
                        } else if ($vx['inttype'] == 2) {
                            $forward2 = $forward2 + 1;
                        }
                    }
                }


                $result2[$k]['stime']=empty($result1[$vo['openid']]['stime'])?"":date("Y-m-d H:i:s",$result1[$vo['openid']]['stime']);
                $result2[$k]['etime']=empty($result1[$vo['openid']]['etime'])?"":date("Y-m-d H:i:s",$result1[$vo['openid']]['etime']);
                $result2[$k]['differtime']=fromatetime($result1[$vo['openid']]['differtime']);
                $result2[$k]['vcount']=$result1[$vo['openid']]['vcount'];
                $result2[$k]['source']=$result1[$vo['openid']]['source'];
                $result2[$k]['intstate']=$vo['intstate']==1?"已关注":"未关注";
                $result2[$k]['collection']=db('collection')->where(array('userid'=>$vo['idmember'],'flag'=>2,'dataid'=>$dataid))->count();
                $result2[$k]['forward']=$forward1."/".$forward2;
                $result2[$k]['intcity']=array_key_exists($vo['intcity'],$sityArr)?$sityArr[$vo['intcity']]:'-';
                $result2[$k]['paynum']=array_key_exists($vo['idmember'],$arruser)?$arruser[$vo['idmember']]:'-';
            }
        }
        return $result2;
        /*
    foreach ($result1 as $k=>$vo)
    {
    $result1[$k]['chrname']="-";
    $result1[$k]['nickname']="-";
    $result1[$k]['intcity']="-";
    $result1[$k]['chraddress']="-";
    $result1[$k]['intstate']="0";
    $result1[$k]['chrtel']="-";
    $result1[$k]['collection']="-";
    $result1[$k]['forward']="-";//转发数
    $result1[$k]['stime']=empty($result1[$k]['stime'])?"":date("Y-m-d H:i:s",$result1[$k]['stime']);
    $result1[$k]['etime']=empty($result1[$k]['etime'])?"":date("Y-m-d H:i:s",$result1[$k]['etime']);
    $result1[$k]['differtime']=fromatetime($result1[$k]['differtime']);
    if(array_key_exists($vo['openid'],$result3))
    {
    $result1[$k]['chrname']=$result3[$vo['openid']]['chrname'];
    $result1[$k]['nickname']=$result3[$vo['openid']]['nickname'];
    $result1[$k]['intcity']=$result3[$vo['openid']]['intcity'];
    $result1[$k]['chraddress']=$result3[$vo['openid']]['chraddress'];
    $result1[$k]['intstate']=$result3[$vo['openid']]['intstate']==1?"关注":"";
    $result1[$k]['chrtel']=$result3[$vo['openid']]['chrtel'];;
    $result1[$k]['collection']=db('collection')->where(array('userid'=>$result3[$vo['openid']]['idmember'],'flag'=>2,'dataid'=>$dataid))->count();
    }
    }
    return $result1;
     */
    }

    //增改查页面处理
    public function deal($data)
    {
        $activity_cashed_info = [];
        if (array_key_exists('id', $data)) {
            $result = db('activity')->where('siteid=:siteid and idactivity=:id', ['siteid' => $this->siteid, 'id' => $data['id']])->find();
            $result['selcontent'] = db('package')->where(
                    [
                        'activity_id' => $data['id'],
                        'state' => 1,
                    ]
                )
                ->field(
                    [
                        'package_id',
                        'activity_id',
                        'keyword1',
                        'keyword2',
                        'original_price',
                        'member_price',
                        'package_sum',
                        'sold',
                        'cost_price',
                        'level1_commission_rate',
                        'level2_commission_rate',
                        'level3_commission_rate',
                        'FROM_UNIXTIME(expire_at, "%Y-%m-%d") expire_at',
                    ]
                )
                ->select();
            foreach ($result['selcontent'] as $key => $value) {
                $result['selcontent'][$key]['group_buy'] = db('group_buy')->where(
                    [
                        'package_id' => $value['package_id'],
                    ]
                )
                    ->field(
                        [
                            'group_buy_id',
                            'package_id',
                            'group_num',
                            'FROM_UNIXTIME(start_at) start_at',
                            'FROM_UNIXTIME(end_at) end_at',
                            'group_buy_price',
                            'allow_coupon',
                            'time_limit',
                            'allow_refund',
                            'state',
                            'time_limit_type',
                            'show_on_homepage',
                        ]
                    )
                    ->select();
            }
            //查询活动现金券设置表信息
            $activity_cashed_info = db('activity_cashed_card_set')->field('id', true)->where(['activity_id' => $data['id']])->find();
        } else {
            $result = [
                'idactivity' => '',
                'chrtitle' => '',
                'short_title' => '',
                'chrkeyword' => '',
                'fidtype' => '0',
                'chrtype' => '',
                'intselmarket' => 0,
                'selmarketname' => '',
                'fidprovince' => '0',
                'fidcity' => '0',
                'fidarea' => '0',
                'fiddistrict' => '0',
                'chraddress' => '',
                'chrimg' => '',
                'chrimg_m' => '',
                'dtstart' => '',
                'dtend' => '',
                'dtsignstime' => '',
                'dtsignetime' => '',
                'ischarge' => '',
                'chrrange' => '',
                'minage' => '',
                'maxage' => '',
                'chrsummary' => '',
                'chrworth' => '',
                'chrchargemark' => '',
                'chrnotice' => '',
                'chraddressdetail' => '',
                'chrmap' => '',
                'chrmaplng' => '',
                'chrmaplat' => '',
                'chrcontent' => '',
                'dtpublishtime' => date("Y-m-d H:i:s"),
                'chrtags' => '',
                'abilitytags' => '',
                'abilitytagsname' => '',
                'attributetags' => '',
                'attributetagsname' => '',
                'signurl' => '',
                'sharebackpic' => '',
                'chksignup' => '',
                'chkissubscribe' => '',
                'intsignnum' => 0,
                'selsignfrom' => '',
                'chkpay' => '',
                'selpaytype1' => '',
                'chkismobile' => '',
                'chkisidcard' => '',
                'intmaxpaynum' => '',
                'intmaxmobilepaynum' => '',
                'intmaxidcardpaynum' => '',
                'chkisshare' => '',
                'selcontent' => '',
                'chkvolume' => '',
                'intvolumenum' => '',
                'intvolumeprice' => '',
                'chkcash' => '',
                'intcashprice1' => '0',
                'intcashnum1' => '0',
                'intcashprice2' => '0',
                'intcashnum2' => '0',
                'intcashday' => '1',
                'chktags' => '',
                'chkcontentlev' => '',
                'contentlevtime' => '',
                'chkisindex' => '',
                'chkisthird' => '',
                'chkdown' => '',
                'txtfwtk' => '',
                'memberprice' => '',
                'checkinfo' => '',
                'intflag' => '1',
                'chrurl' => '',
                'isrefund' => 0,
                'wntx_sync_status' => 30,
            ];
        }
        if ($activity_cashed_info) {
            $result = array_merge($result, $activity_cashed_info);
        } else {
            //初始化
            $result['is_use_cashed'] = ''; //活动是否可以使用现金券
            $result['max_use'] = ''; //最大使用数量
            $result['max_amount'] = ''; //最大使用金额
            $result['is_receive_cashed'] = ''; //是否开启领用专用券
            $result['activity_cashed_amount'] = ''; //领取的活动专用券金额
            $result['activity_cashed_validity'] = ''; //活动专用券的有效期天数
            $result['receive_activity_cashed_intstate_user'] = ''; //可领券的系统用户分类
            $result['receive_activity_cashed_categoryid'] = ''; //可领活动专用券的自定义用户分类
            $result['cashed_plan_id'] = ''; //现金券计划id
            $result['is_share_cashed'] = ''; //付款后是否可以分享现金券
        }

//        var_dump($result);exit;

        if (!empty($data['action'])) {
            $result['action'] = $data['action'];
        }
        if (!empty($data['usertype'])) {
            $result['usertype'] = $data['usertype'];
        }

        return $result;
    }
    public function copydata($id)
    {
        $row = db('activity')->where('siteid=:siteid and idactivity=:id', ['siteid' => $this->siteid, 'id' => $id])->find();
        if (!$row) {
            return false;
        }

        unset($row['idactivity']);
        unset($row['checktime']);
        unset($row['dtstart']);
        unset($row['dtend']);
        unset($row['intselmarket']);
        unset($row['chkcontentlev']);
        unset($row['contentlevtime']);
        unset($row['chkisindex']);
        unset($row['chkdown']);
        unset($row["dtpublishtime"]);
        unset($row["dtsignstime"]);
        unset($row["dtsignetime"]);
        unset($row['dtpublishtime']);
        $row['sold'] = $row['version'] = 0;
        $row['chrtitle'] = $row['chrtitle'] . "[复制]";
        $row['checkinfo'] = "";
        $row['intflag'] = 6;
        $row['publishid'] = session('AccountID');
        $row['intselmarket'] = 0;

        $bool = db('activity')->insert($row);
        return $bool;

    }

    public function activitycheck($data)
    {
        $result = db('activity')->where('siteid=:siteid and idactivity=:id', ['siteid' => $this->siteid, 'id' => $data['id']])->find();

        if (!$result) {
            echo "数据不存在！";
            exit();
        }

        if (!empty($result['selcontent'])) {
            $selcontent = [];
            $arr = explode("☆", $result['selcontent']);
            foreach ($arr as $k => $vo) {
                $selcontent[] = explode("∮", $vo);
            }
            $result['selcontent'] = $selcontent;
        }
        $result['action'] = $data['action'];
        return $result;
    }

    //添加，修改提交地址
    public function PostData($data)
    {
        $tmpArr = array();
        if (isset($data['chrtitle'])) {
            $tmpArr['chrtitle'] = trim($data['chrtitle']);
        }

        if (isset($data['short_title'])) {
            $tmpArr['short_title'] = trim($data['short_title']);
        }

        if (isset($data['chrkeyword'])) {
            $tmpArr['chrkeyword'] = trim($data['chrkeyword']);
        }

        if (isset($data['fidtype'])) {
            $tmpArr['fidtype'] = trim($data['fidtype']);
        }

        if (isset($data['chrtype'])) {
            $tmpArr['chrtype'] = trim($data['chrtype']);
        }

        if (isset($data['fidprovince'])) {
            $tmpArr['fidprovince'] = trim($data['fidprovince']);
        }

        if (isset($data['fidcity'])) {
            $tmpArr['fidcity'] = trim($data['fidcity']);
        }

        if (isset($data['fidarea'])) {
            $tmpArr['fidarea'] = trim($data['fidarea']);
        }

        if (isset($data['fiddistrict'])) {
            $tmpArr['fiddistrict'] = trim($data['fiddistrict']);
        }

        if (isset($data['chraddress'])) {
            $tmpArr['chraddress'] = trim($data['chraddress']);
        }

        if (isset($data['chrimg'])) {
            $tmpArr['chrimg'] = trim($data['chrimg']);
        }

        if (isset($data['chrimg_m'])) {
            $tmpArr['chrimg_m'] = trim($data['chrimg_m']);
        }

        if (isset($data['dtstart']) && trim($data['dtstart']) != "") {
            $tmpArr['dtstart'] = trim($data['dtstart']);
        }

        if (isset($data['dtend']) && trim($data['dtend']) != "") {
            $tmpArr['dtend'] = trim($data['dtend']);
        }

        if (isset($data['dtsignstime']) && trim($data['dtsignstime']) != "") {
            $tmpArr['dtsignstime'] = trim($data['dtsignstime']);
        }

        if (isset($data['dtsignetime']) && trim($data['dtsignetime']) != "") {
            $tmpArr['dtsignetime'] = trim($data['dtsignetime']);
        }

        if (isset($data['ischarge'])) {
            $tmpArr['ischarge'] = trim($data['ischarge']);
        }

        if (isset($data['chrrange'])) {
            $tmpArr['chrrange'] = trim($data['chrrange']);
        }

        if (isset($data['minage'])) {
            $tmpArr['minage'] = trim($data['minage']);
        }

        if (isset($data['maxage'])) {
            $tmpArr['maxage'] = trim($data['maxage']);
        }

        if (isset($data['chrsummary'])) {
            $tmpArr['chrsummary'] = trim($data['chrsummary']);
        }

        if (isset($data['chrworth'])) {
            $tmpArr['chrworth'] = trim($data['chrworth']);
        }

        if (isset($data['chrchargemark'])) {
            $tmpArr['chrchargemark'] = trim($data['chrchargemark']);
        }

        if (isset($data['chrnotice'])) {
            $tmpArr['chrnotice'] = trim($data['chrnotice']);
        }

        if (isset($data['chraddressdetail'])) {
            $tmpArr['chraddressdetail'] = trim($data['chraddressdetail']);
        }

        if (isset($data['chrmap'])) {
            $tmpArr['chrmap'] = trim($data['chrmap']);
        }

        if (isset($data['chrmaplng'])) {
            $tmpArr['chrmaplng'] = trim($data['chrmaplng']);
        }

        if (isset($data['chrmaplat'])) {
            $tmpArr['chrmaplat'] = trim($data['chrmaplat']);
        }

        if (isset($data['chrcontent'])) {
            $tmpArr['chrcontent'] = htmlentities($data['chrcontent']);
        }

        if (isset($data['dtpublishtime']) && trim($data['dtpublishtime']) != "") {
            $tmpArr['dtpublishtime'] = trim($data['dtpublishtime']);
        }

        if (isset($data['chrtags'])) {
            $tmpArr['chrtags'] = "," . implode(',', $data['chrtags']) . ",";
        }

        if (isset($data['abilitytags'])) {
            $tmpArr['abilitytags'] = trim($data['abilitytags']);
        }

        if (isset($data['abilitytagsname'])) {
            $tmpArr['abilitytagsname'] = trim($data['abilitytagsname']);
        }

        if (isset($data['attributetags'])) {
            $tmpArr['attributetags'] = trim($data['attributetags']);
        }

        if (isset($data['attributetagsname'])) {
            $tmpArr['attributetagsname'] = trim($data['attributetagsname']);
        }

        if (isset($data['signurl'])) {
            $tmpArr['signurl'] = trim($data['signurl']);
        }

        if (isset($data['sharebackpic'])) {
            $tmpArr['sharebackpic'] = trim($data['sharebackpic']);
        }

        if (isset($data['chrurl'])) {
            $tmpArr['chrurl'] = trim($data['chrurl']);
        }

        if (isset($data['isrefund'])) {
            $tmpArr['isrefund'] = intval($data['isrefund']);
        }

        if (isset($data['chksignup'])) {
            $tmpArr['chksignup'] = trim($data['chksignup']);
        } else {
            $tmpArr['chksignup'] = 0;
        }

        if (isset($data['chkissubscribe'])) {
            $tmpArr['chkissubscribe'] = trim($data['chkissubscribe']);
        } else {
            $tmpArr['chkissubscribe'] = 0;
        }

        $tmpArr['intsignnum'] = 0;
        if (isset($data['selsignfrom'])) {
            $tmpArr['selsignfrom'] = trim($data['selsignfrom']);
        }

        if (isset($data['chkpay'])) {
            $tmpArr['chkpay'] = trim($data['chkpay']);
        } else {
            $tmpArr['chkpay'] = 0;
        }

        if (isset($data['selpaytype1'])) {
            $tmpArr['selpaytype1'] = trim($data['selpaytype1']);
        }

        if (isset($data['chkismobile'])) {
            $tmpArr['chkismobile'] = trim($data['chkismobile']);
        } else {
            $tmpArr['chkismobile'] = 0;
        }

        if (isset($data['chkisidcard'])) {
            $tmpArr['chkisidcard'] = trim($data['chkisidcard']);
        } else {
            $tmpArr['chkisidcard'] = 0;
        }

        if (isset($data['intmaxpaynum'])) {
            $tmpArr['intmaxpaynum'] = trim($data['intmaxpaynum']);
        }

        if (isset($data['intmaxmobilepaynum'])) {
            $tmpArr['intmaxmobilepaynum'] = trim($data['intmaxmobilepaynum']);
        }

        if (isset($data['intmaxidcardpaynum'])) {
            $tmpArr['intmaxidcardpaynum'] = trim($data['intmaxidcardpaynum']);
        }

        if (isset($data['chkisshare'])) {
            $tmpArr['chkisshare'] = trim($data['chkisshare']);
        } else {
            $tmpArr['chkisshare'] = 0;
        }

        if (isset($data['checkinfo'])) {
            $tmpArr['checkinfo'] = trim($data['checkinfo']);
        }

        if (isset($data['checktime'])) {
            $tmpArr['checktime'] = trim($data['checktime']);
        }

        if (isset($data['usertype'])) {
            $tmpArr['usertype'] = "," . join(',', $data['usertype']) . ",";
        }

        $tmpArr['selcontent'] = '';

        $tmpArr['memberprice'] = 0;
        $intselmarket = isset($data['intselmarket']) ? $data['intselmarket'] : "";
        if ($intselmarket != "") {
            $arrT = explode(':', $intselmarket);
            $tmpArr['intselmarket'] = $arrT[0];
            $tmpArr['selmarketname'] = $arrT[1];
        }

        if (isset($data['chkvolume'])) {
            $tmpArr['chkvolume'] = trim($data['chkvolume']);
        } else {
            $tmpArr['chkvolume'] = 0;
        }

        if (isset($data['intvolumenum'])) {
            $tmpArr['intvolumenum'] = trim($data['intvolumenum']);
        }

        if (isset($data['intvolumeprice'])) {
            $tmpArr['intvolumeprice'] = trim($data['intvolumeprice']);
        }

        if (isset($data['chkcash'])) {
            $tmpArr['chkcash'] = trim($data['chkcash']);
        } else {
            $tmpArr['chkcash'] = 0;
        }

        if (isset($data['intcashprice1'])) {
            $tmpArr['intcashprice1'] = trim($data['intcashprice1']);
        }

        if (isset($data['intcashnum1'])) {
            $tmpArr['intcashnum1'] = trim($data['intcashnum1']);
        }

        if (isset($data['intcashprice2'])) {
            $tmpArr['intcashprice2'] = trim($data['intcashprice2']);
        }

        if (isset($data['intcashnum2'])) {
            $tmpArr['intcashnum2'] = trim($data['intcashnum2']);
        }

        if (isset($data['intcashday'])) {
            $tmpArr['intcashday'] = trim($data['intcashday']);
        }

        if (isset($data['chktags'])) {
            $tmpArr['chktags'] = trim($data['chktags']);
        } else {
            $tmpArr['chktags'] = 0;
        }

        if (isset($data['chkcontentlev'])) {
            $tmpArr['chkcontentlev'] = trim($data['chkcontentlev']);
            $tmpArr['contentlevtime'] = date("Y-m-d H:i:s", time());
        } else {
            $tmpArr['chkcontentlev'] = 0;
            $tmpArr['contentlevtime'] = null;
        }
//        if(isset($data['contentlevtime'])) $tmpArr['contentlevtime']=trim($data['contentlevtime']);
        if (isset($data['chkisindex'])) {
            $tmpArr['chkisindex'] = trim($data['chkisindex']);
        } else {
            $tmpArr['chkisindex'] = 0;
        }

        if (isset($data['chkisthird'])) {
            $tmpArr['chkisthird'] = trim($data['chkisthird']);
        } else {
            $tmpArr['chkisthird'] = 0;
        }

        if (isset($data['chkdown'])) {
            $tmpArr['chkdown'] = trim($data['chkdown']);
        } else {
            $tmpArr['chkdown'] = 0;
        }

        if (isset($data['txtfwtk'])) {
            $tmpArr['txtfwtk'] = htmlentities($data['txtfwtk']);
        }

        if (isset($data['intflag'])) {
            $tmpArr['intflag'] = trim($data['intflag']);
        }

        $tmpArr['siteid'] = $this->siteid;
        //现金券相关的参数
        $cashed_param['is_use_cashed'] = isset($data['is_use_cashed']) ? $data['is_use_cashed'] : 2; //活动是否可以使用现金券
        $cashed_param['max_use'] = !empty($data['max_use']) ? intval($data['max_use']) : ''; //最大使用数量
        $cashed_param['max_amount'] = isset($data['max_amount']) ? $data['max_amount'] : ''; //最大使用金额
        $cashed_param['is_receive_cashed'] = isset($data['is_receive_cashed']) ? $data['is_receive_cashed'] : 2; //是否开启领用专用券
        $cashed_param['activity_cashed_amount'] = isset($data['activity_cashed_amount']) ? $data['activity_cashed_amount'] : ''; //领取的活动专用券金额
        $cashed_param['activity_cashed_validity'] = !empty($data['activity_cashed_validity']) ? intval($data['activity_cashed_validity']) : ''; //活动专用券的有效期天数
        //如果含有关注状态
        if (isset($data['receive_activity_cashed_intstate_user']) && !empty($data['receive_activity_cashed_intstate_user'])) {
            //将数组拼接为字符串
            $int_state_str = implode(',', $data['receive_activity_cashed_intstate_user']);
            $cashed_param['receive_activity_cashed_intstate_user'] = $int_state_str; //可领券的系统用户分类
        }
        //如果含有自定义用户分类
        if (isset($data['receive_activity_cashed_categoryid']) && !empty($data['receive_activity_cashed_categoryid'])) {
            //将数组拼接为字符串
            $category_id_str = implode(',', $data['receive_activity_cashed_categoryid']);
            $cashed_param['receive_activity_cashed_categoryid'] = $category_id_str; //可领活动专用券的自定义用户分类
        }
        $cashed_param['cashed_plan_id'] = isset($data['cashed_plan_id']) ? intval($data['cashed_plan_id']) : ''; //现金券计划id
        $cashed_param['is_share_cashed'] = isset($data['is_share_cashed']) ? $data['is_share_cashed'] : 2; //付款后是否可以分享现金券
        // var_dump($data);die;
        try
        {
            $query = new Query;
            $query->startTrans();
            if ($data['action'] == 'add')
            {
                //如果是添加的话
                if (isset($data['nodeid'])) {
                    $tmpArr['nodeid'] = intval($data['nodeid']);
                }

                $data['intflag'] = 1;
                $data['publishid'] = session('AccountID');
                $activityId = db('activity')->insert($tmpArr, false, true);

                if (isset($data['chksignup']) && $data['chksignup'] == 1) {
                    $insertPackage = [];
                    $groupBuys = [];
                    foreach ($data['packages'] as $k => $v) {
                        $v['expire_at'] = strtotime($v['expire_at']);
                        $v['activity_id'] = $activityId;
                        $v['sold'] = 0;
                        $v['original_price'] = $v['original_price'] ?: 0;
                        $v['cost_price'] = $v['cost_price'] ?: 0;
                        $v['site_id'] = $this->siteid;

                        $groupBuyData = [];

                        if (isset($v['group_buy'])) {
                            // 全是新拼团
                            $groupBuyData = $v['group_buy'];
                            unset($v['group_buy']);
                            //插入package表，获取主键
                            $packageId = db('package')->insert($v, false, true);
                            foreach ($groupBuyData as $key => $value) {
                                // 验证拼团数据
                                // var_dump($value);
                                // $query->rollBack();die;

                                if (!isset($value['group_num']) || (int) $value['group_num'] != $value['group_num'] || $value['group_num'] < 1) {
                                    // var_dump($value, isset($value['group_num']), is_integer($value['group_num']), $value['group_num'] < 1);
                                    // $query->rollBack();die;
                                    throw new Exception('成团数量格式错误');
                                }
                                if (!isDate($value['start_at'])) {
                                    throw new Exception('拼团开始时间格式错误');
                                }
                                if (!isDate($value['end_at'])) {
                                    throw new Exception('拼团结束时间格式错误');
                                }

                                if(strtotime($value['end_at']) <= strtotime($value['start_at']))
                                {
                                    throw new Exception('拼团结束时间格式错误');
                                }

                                if (isset($value['time_limit']) && ( (int) $value['time_limit'] != $value['time_limit'] || $value['time_limit'] < 1)) {
                                    throw new Exception('组团时间限制格式错误');
                                }

                                // $groupBuyData[$key]['time_limit'] = (int) $value['time_limit'];
                                if ($groupBuyData[$key]['time_limit_type'] == 1 && (!isset($groupBuyData[$key]['time_limit']) || $groupBuyData[$key]['time_limit'] < 1)) {
                                    throw new Exception('拼团时间限制格式错误');
                                }
                                if (!in_array($value['allow_refund'], [0, 1]) || !in_array($value['time_limit_type'], [1, 2])) {
                                    throw new Exception('数据错误');
                                }
                                if (!in_array($value['show_on_homepage'], [0, 1])) {
                                    throw new Exception('数据错误');
                                }
                                $groupBuyData[$key]['create_at'] = time();
                                $groupBuyData[$key]['site_id'] = session('idsite');
                                $groupBuyData[$key]['start_at'] = strtotime($value['start_at']);
                                $groupBuyData[$key]['end_at'] = strtotime($value['end_at']);
                                $groupBuyData[$key]['state'] = isset($value['state']) ? 1 : 0;
                                $groupBuyData[$key]['allow_refund'] = $value['allow_refund'];
                                $groupBuyData[$key]['show_on_homepage'] = $value['show_on_homepage'];
                                $groupBuyData[$key]['package_id'] = $packageId;
                            }
                            $groupBuys = array_merge($groupBuys, $groupBuyData);
                        } else {
                            $insertPackage[] = $v;
                        }
                    }
                }
                $cashed_param['activity_id'] = $activityId;
                //执行插入活动现金券设置表数据
                db('activity_cashed_card_set')->insert($cashed_param);
                //如果是编辑活动，那么可能有新套餐，新套餐下都是新拼团；旧套餐下有新拼团或旧拼团修改
            } else {
                $activity_info = db('activity')->where('idactivity=:id', ['id' => $data['id']])->field("wntx_sync_status,intflag")->find();
                $bool = db('activity')->where('idactivity=:id', ['id' => $data['id']])->update($tmpArr);

                if (isset($data['chksignup']) && $data['chksignup'] == 1) {
                    $insertPackage = [];
                    $groupBuys = [];
                    // print_r($data['packages']);die;
                    foreach ($data['packages'] as $k => $v) {
                        if ($v['package_sum'] < 0) {
                            continue;
                        }
                        $v['expire_at'] = strtotime($v['expire_at']);
                        $v['original_price'] = $v['original_price'] ?: 0;
                        $v['cost_price'] = $v['cost_price'] ?: 0;

                        if (isset($v['package_id']) && !empty($v['package_id'])) {
                            // $package = new \app\admin\module\Package;
                            // $package->updatePackage($v);
                            // 旧套餐 涉及新拼团添加或者旧拼团修改
                            if (isset($v['group_buy'])) {
                                // 有拼团数据
                                $groupBuyData = $v['group_buy'];
                                unset($v['group_buy']);
                                foreach ($groupBuyData as $key => $value) {
                                    // 验证拼团数据
                                    if (!isset($value['group_num']) || (int) $value['group_num'] != $value['group_num'] || $value['group_num'] <= 1) {
                                        throw new Exception('成团数量格式错误，成团数量必须大于等于2');
                                    }
                                    if (!isDate($value['start_at'])) {
                                        throw new Exception('拼团开始时间格式错误');
                                    }
                                    if (!isDate($value['end_at'])) {
                                        throw new Exception('拼团结束时间必须大于拼团开始时间');
                                    }
                                    // $groupBuyData[$key]['time_limit'] = (int) $value['time_limit'];
                                    if ($value['time_limit_type'] == 1 && (!isset($value['time_limit']) || $value['time_limit'] < 1)) {
                                        throw new Exception('拼团时间限制格式错误');
                                    }
                                    if (!in_array($value['allow_refund'], [0, 1]) || !in_array($value['time_limit_type'], [1, 2])) {
                                        throw new Exception('数据错误');
                                    }

                                    if (isset($value['time_limit']) && ( (int) $value['time_limit'] != $value['time_limit'] || $value['time_limit'] < 1)) {
                                        throw new Exception('组团时间限制格式错误');
                                    }
                                    $value['create_at'] = time();
                                    $value['site_id'] = session('idsite');
                                    $value['start_at'] = strtotime($value['start_at']);
                                    $value['end_at'] = strtotime($value['end_at']);
                                    $value['state'] = isset($value['state']) && $value['state'] ? 1 : 0;

                                    if (isset($value['group_buy_id'])) {
                                        // 旧拼团
                                        $value['update_at'] = time();
                                        unset($value['state']);
                                        db('group_buy')->update($value);
                                    } else {
                                        // 新拼团
                                        $value['package_id'] = $v['package_id'];
                                        $value['create_at'] = time();
                                        $value['site_id'] = session('idsite');
                                        $value['start_at'] = $value['start_at'];
                                        $value['end_at'] = $value['end_at'];
                                        $groupBuys[] = $value;
                                    }
                                }
                            }

                            // $where = [
                            //     'package_id' => $v['package_id'],
                            //     // 'sold' => ['elt', $v['package_sum']]
                            // ];
                            // $insertPackage[] = $v;
                            $a = db('package')->update($v);
                            // var_dump($v, $a);die;
                        } else {
                            // 新套餐 全是新拼团
                            $v['activity_id'] = $data['id'];
                            $v['sold'] = 0;
                            $v['site_id'] = $this->siteid;

                            if (isset($v['group_buy'])) {
                                // 有拼团 必须先插入套餐以获取套餐id
                                $groupBuyData = $v['group_buy'];
                                unset($v['group_buy']);
                                // 新建套餐涉及到拼团部分，需要单独插入以实时获取套餐id
                                $packageId = db('package')->insert($v, false, true);
                                foreach ($groupBuyData as $key => $value) {
                                    // 新拼团
                                    $value['package_id'] = $packageId;
                                    $value['state'] = isset($value['state']) && $value['state'] ? 1 : 0;
                                    $value['create_at'] = time();
                                    $value['site_id'] = session('idsite');
                                    $value['start_at'] = strtotime($value['start_at']);
                                    $value['end_at'] = strtotime($value['end_at']);
                                    $groupBuys[] = $value;
                                }
                            } else {
                                // 无拼团，批量插入
                                $insertPackage[] = $v;
                            }
                        }
                    }
                }
                //先查询是否具有该活动设置现金券的信息
                $set_cashed_info = db('activity_cashed_card_set')->where(['activity_id' => $data['id']])->find();
                //如果不存在   那么就是插入数据
                if (!$set_cashed_info) {
                    $cashed_param['activity_id'] = $data['id'];
                    //执行插入活动现金券设置表数据
                    db('activity_cashed_card_set')->insert($cashed_param);
                } else {
                    //执行修改活动现金券设置表数据
                    db('activity_cashed_card_set')->where(['activity_id' => $data['id']])->update($cashed_param);
                }
                //如果商品已发布，并且同步状态为已同步，则同步更改状态为下架
                if ($activity_info["intflag"] == 2 && in_array($activity_info['wntx_sync_status'], array(1, 3, 4))) {
//                    echo 111;exit;
                    $this->change_sync_status(array("id" => $data['id'], "sync_status" => 2));
                }
                $id = isset($data['id']) ? (int) $data['id'] : 0;
            }

            if(!empty($insertPackage)){
                db('package')->insertAll($insertPackage);
            }

            // 使用replace实现更新和插入
            if(!empty($groupBuys)){
                db('group_buy')->insertAll($groupBuys, true);
            }

            $query->commit();
        } catch (Exception $e) {
            $query->rollBack();
            // throw $e;
            return ['status' => 'fail', 'msg' => $e->getMessage()];
        }
        $chrkeyword = isset($data["chrkeyword"]) ? $data["chrkeyword"] : "";
        $dtstart = isset($tmpArr["dtstart"]) ? strtotime($tmpArr["dtstart"]) : 0;
        $dtend = isset($tmpArr["dtend"]) ? strtotime($tmpArr["dtend"]) : 0;
//        $this -> addKeyWordToReply($id,$chrkeyword,$dtstart,$dtend,$this->siteid);
        return ['status' => 'success', 'msg' => '操作成功'];
    }

    /**
     * 添加关键词到自动回复中
     * @param $id
     * @param $keyword
     * @param $dtstart
     * @param $dtend
     * @param $siteid
     * @return bool|int|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    private function addKeyWordToReply($id, $keyword, $dtstart, $dtend, $siteid)
    {
        if (empty($keyword)) {
            return false;
        }

        $post_data = array();
        $post_data["keyword"] = $keyword;
        $post_data["start_time"] = $dtstart;
        $post_data["end_time"] = $dtend;
        $post_data["content_id"] = $id;
        $post_data["content_from"] = 1;
        $post_data["siteid"] = $siteid;
        $wx_reply_info = db("wx_reply")->where(array("type" => 1, "content_from" => 1, "content_id" => $id))->find();
        if (empty($wx_reply_info)) {

            $bool = db('wx_reply')->insert($post_data);
        } else {
            $wx_replay_id = $wx_reply_info["wx_replay_id"];
            $bool = db('wx_reply')->where('wx_replay_id=:id', ['id' => $wx_replay_id])->update($post_data);
        }
        return $bool;
    }

    //删除
    public function del($data)
    {
        if (isset($data['id']) == false) {
            return false;
        }

        $Flag = empty($data['intflag']) ? 1 : $data['intflag'];

        if ($Flag == 4) {
            if (strstr($data['id'], ',')) {
                $bool = db('activity')->where('siteid=' . $this->siteid)->where('idactivity', 'in', explode(',', $data['id']))->delete();
            } else {
                $bool = db('activity')->where('siteid=' . $this->siteid . ' and idactivity=:id', ['id' => $data['id']])->delete();
            }

        } else {
            $tmpArr['intflag'] = 4;
            if (strstr($data['id'], ',')) {
                $bool = db('activity')->where('siteid=' . $this->siteid)->where('idactivity', 'in', explode(',', $data['id']))->update($tmpArr);
            } else {
                $bool = db('activity')->where('siteid=' . $this->siteid . ' and idactivity=:id', ['id' => $data['id']])->update($tmpArr);
            }

        }

        return $bool;
    }
    public function recovery($id = 0)
    {
        $tmpArr['intflag'] = 1;
        return db('activity')->where('siteid=' . $this->siteid . ' and idactivity=:id', ['id' => $id])->update($tmpArr);
    }

    public function getDic($code)
    {
        return db('work_content')->where(array('idsite' => $this->siteid, 'bookcode' => $code))->field('id,name,code')->order('order')->select();
    }
    public function getFromTemp()
    {
        return db('signup_template')->where(array('idsite' => $this->siteid, 'state' => 1, 'userid' => session('AccountID')))->order('id desc')->select();

    }
    public function template_sub($templateid)
    {
        return db('signup_template_sub')->where(array('idsite' => $this->siteid, 'pid' => $templateid))->order('sn asc,id asc')->select();
    }
    public function getUser()
    {
        return db('account')->where(array('siteid' => $this->siteid, 'intflag' => 1))->order('idaccount asc,intsn asc')->select();

    }
    public function signupindex($request)
    {
        $idactivity = $request['id'];
        $whereArr = array('dataid' => $idactivity, 'idsite' => session('idsite'));

        $search = array("issign" => "", "payname" => "", "dtstart" => "", "dtend" => "", "state" => "");

        foreach ($search as $key => &$v) {
            if (isset($request[$key])) {
                $v = $request[$key];
            }
        }

        if (isset($request["issign"]) && is_numeric($request["issign"])) {
            if ((int) $request["issign"] > 0) {
                $whereArr["issign"] = 1;
            } else {
                $whereArr["issign"] = array('EXP', Db::raw('IS NULL'));
            }
        }

        if (isset($request["payname"]) && !empty($request["payname"])) {
            $whereArr["payname"] = array('like', '%' . $request["payname"] . '%');
        }

        if (isset($request["state"]) && (int) $request["state"] > 0) {
            $whereArr["state"] = (int) $request["state"];
        }

        if (isset($request['dtstart']) && $request['dtstart'] != '' && isset($request['dtend']) && $request['dtend'] != '') {
            $whereArr['dtcreatetime'] = array(array('>', $request['dtstart']), array('<', $request['dtend'] . " 23:59:59"), "and");
        }

        $count = db('order')->where($whereArr)->count();

        $pagesize = 2000; //PAGE_SIZE;
        $page = new Page($count, $pagesize);
        //
        $data = db('order')->where($whereArr)->limit($page->firstRow . ',' . $page->pageSize)->order('id asc')->select();

        $arr = array();
        $arr['pager'] = $page;
        $arr['data'] = $data;
        $arr['search'] = $search;
        return $arr;
    }

    public function issign($data)
    {
        if (array_key_exists('id', $data)) {

            $arr = [];
            $arr["issign"] = "1";
            $arr["singntype"] = "3";
            $arr["issignremark"] = $data['remark'];
            $arr["signuserid"] = session('AccountID');
            $arr["signusername"] = session('UserName');
            $arr["dtsigntime"] = date("Y-m-d H:i:s", time());
            $idsite = session('idsite');
            $result = db('order')->where('id=:id and idsite=:idsite', ['id' => $data['id'], 'idsite' => $idsite])->update($arr);
            if ($result) {
                return true;
            }
        }
        return false;

    }
    //增改查页面处理
    public function signupmodi($data)
    {
        if (array_key_exists('id', $data)) {
            $result = db('order')->where('id=:id and idsite=:idsite', ['id' => $data['id'], 'idsite' => session('idsite')])->find();
        } else {
            $result = [];
            $result['ordersn'] = ''; //订单号//
            $result['fiduser'] = ''; //报名人id//
            $result['wechatid'] = ''; //报名人微信id//
            $result['chrusername'] = ''; //报名人姓名//
            $result['dataid'] = ''; //活动id//
            $result['chrtitle'] = ''; //活动名称//
            $result['chraddress'] = ''; //活动详细地点//
            $result['dtstart'] = ''; //活动开始时间//
            $result['dtend'] = ''; //活动结束时间//
            $result['signfromid'] = ''; //报名表单id//
            $result['pusilhid'] = ''; //发布人id//
            $result['pusilhname'] = ''; //发布人姓名//
            $result['marketid'] = ''; //商务id//
            $result['marketname'] = ''; //商务名称//
            $result['state'] = ''; //订单状态:1.起草,待审核 2.已报名,审核不通过 3.已报名,已审批，4.已报名,已付款，5.已报名,退款中，6已报名，部分退款，7已报名，已退款，8.已报名,退款不通过，9.删除 10.已取消//
            $result['paytype'] = ''; //收款方式 1。平台收款 2.商家自行收款//
            $result['paytype1'] = ''; //支付方式 1：微信支付 2：优惠卷支付 3：积分支付 4：线下支付//
            $result['payid'] = ''; //购买的套餐id//
            $result['paynum'] = ''; //购买数量//
            $result['payname'] = ''; //购买的套餐名称//
            $result['price'] = ''; //订单总价格//
            $result['price1'] = ''; //活动代理价格//
            $result['price2'] = ''; //积分抵扣的金额//
            $result['prepay_id'] = ''; //微信下单订单号//
            $result['transaction_id'] = ''; ////
            $result['dtpaytime'] = ''; //支付时间//
            $result['dtrefundtime'] = ''; //用户发起退款的时间//
            $result['refundsn'] = ''; //商户请求退款的单号//
            $result['wxrefundsn'] = ''; //微信完成退款的单号//
            $result['dtwxrefundtime'] = ''; //微信完成退款的时间//
            $result['isrefundpart'] = ''; //是否是部分退款 0：不是 1：是//
            $result['refundprice'] = ''; //退款金额//
            $result['refundremark'] = ''; //退款原因//
            $result['refundpic'] = ''; //退款上传图片//
            $result['refundremark1'] = ''; //后台确认退款时的备注信息//
            $result['refundsn2'] = ''; //二次商户请求退款的单号//
            $result['refundmsg2'] = ''; //二次退款原因//
            $result['refundprice2'] = ''; //二次退款金额//
            $result['dtwxrefundtime2'] = ''; //二次微信完成退款的时间//
            $result['wxrefundsn2'] = ''; //二次微信完成退款的单//
            $result['cancelremark'] = ''; //订单取消原因//
            $result['dtcreatetime'] = ''; //创建时间//
            $result['issign'] = ''; //是否已签到 1：是//
            $result['signuserid'] = ''; //签到验证人id//
            $result['signusername'] = ''; //签到验证人昵称//
            $result['dtsigntime'] = ''; //签到时间//
            $result['issettlement'] = ''; //是否已结算 1:是//
            $result['isrefund'] = ''; //是否允许退款 1：允许 2:不允许//
            $result['couriercode'] = ''; //物流编号//
            $result['couriername'] = ''; //物流名称//
            $result['couriersn'] = ''; //物流单号//
            $result['txtdata'] = ''; //模版数据，多个字段用“☆”分开//
        }
        $result['action'] = $data['action'];
        return $result;
    }

    public function addOrder($dataID, $UserName, $payname, $price, $payCount, $txtfield, $txtdata, $source = "平台", $state = 1)
    {

        $row = db('activity')->where("idactivity = " . $dataID)->find();
        if (!$row) {
            return false;
        }
        $checkcode = '';
        $OrderSn = getOrderSn();
        if ($source != "平台") {
            $checkcode = getOrderCode();
        }

        $result = [];
        $result['ordersn'] = $OrderSn; //订单号//
        //$result['fiduser']=$userID; //报名人id//
        //$result['wechatid']=$wxID; //报名人微信id//
        $result['chrusername'] = $UserName; //报名人姓名//
        $result['dataid'] = $dataID; //活动id//
        $result['chrtitle'] = $row['chrtitle']; //活动名称//
        $result['ischarge'] = $row['ischarge']; //是否收费 1:免费 2收费
        $result['chrimg'] = $row['chrimg']; //活动图片//
        $result['chraddress'] = $row['chraddressdetail']; //活动详细地点//
        $result['dtstart'] = $row['dtstart']; //活动开始时间//
        $result['dtend'] = $row['dtend']; //活动结束时间//
        //$result['signfromid']=''; //报名表单id//
        $result['pusilhid'] = $row['publishid']; //发布人id//
        $result['pusilhname'] = $row['publishname']; //发布人姓名//
        $result['marketid'] = $row['intselmarket']; //商务id//
        $result['marketname'] = $row['selmarketname']; //商务名称//
        $result['state'] = $state; //订单状态:1.起草,待审核 2.已报名,审核不通过 3.已报名,已审批，4.已报名,已付款，5.已报名,退款中，6已报名，部分退款，7已报名，已退款，8.已报名,退款不通过，9.删除 10.已取消//
        //$result['paytype']=$row['']; //收款方式 1。平台收款 2.商家自行收款//
        //$result['paytype1']=$row['']; //支付方式 1：微信支付 2：优惠卷支付 3：积分支付 4：线下支付//
        //$result['payid']=$row['']; //购买的套餐id//
        $result['paynum'] = $payCount; //购买数量//
        $result['payname'] = $payname; //购买的套餐名称//
        $result['price'] = $price; //订单总价格//
        $result['source'] = $source; //来源//
        //$result['price1']=$row['']; //活动代理价格//
        //$result['price2']=$row['']; //积分抵扣的金额//
        //$result['prepay_id']=$row['']; //微信下单订单号//
        //$result['transaction_id']=$row['']; ////
        //$result['dtpaytime']=$row['']; //支付时间//
        //$result['dtrefundtime']=$row['']; //用户发起退款的时间//
        //$result['refundsn']=$row['']; //商户请求退款的单号//
        //$result['wxrefundsn']=$row['']; //微信完成退款的单号//
        //$result['dtwxrefundtime']=$row['']; //微信完成退款的时间//
        //$result['isrefundpart']=$row['']; //是否是部分退款 0：不是 1：是//
        //$result['refundprice']=$row['']; //退款金额//
        //$result['refundremark']=$row['']; //退款原因//
        //$result['refundpic']=$row['']; //退款上传图片//
        //$result['refundremark1']=$row['']; //后台确认退款时的备注信息//
        //$result['refundsn2']=$row['']; //二次商户请求退款的单号//
        //$result['refundmsg2']=$row['']; //二次退款原因//
        //$result['refundprice2']=$row['']; //二次退款金额//
        //$result['dtwxrefundtime2']=$row['']; //二次微信完成退款的时间//
        //$result['wxrefundsn2']=$row['']; //二次微信完成退款的单//
        //$result['cancelremark']=$row['']; //订单取消原因//
        $result['dtcreatetime'] = date("Y-m-d H:i:s"); //创建时间//
        //$result['issign']=$row['']; //是否已签到 1：是//
        //$result['signuserid']=$row['']; //签到验证人id//
        //$result['signusername']=$row['']; //签到验证人昵称//
        //$result['dtsigntime']=$row['']; //签到时间//
        $result['issettlement'] = 0; //是否已结算 1:是//
        $result['isrefund'] = 2; //是否允许退款 1：允许 2:不允许//
        //$result['couriercode']=''; //物流编号//
        //$result['couriername']=''; //物流名称//
        //$result['couriersn']=''; //物流单号//
        $result['txtfield'] = $txtfield; //模版字段，多个字段用“☆”分开//
        $result['txtdata'] = $txtdata; //模版数据，多个字段用“☆”分开//
        $result['intflag'] = 5; //1待下单的报名,2待审批的报名,3审查不通过的报名,4已取消的报名,5所有报名,6退款的报名//
        $result['idsite'] = $row['siteid'];
        $result['checkcode'] = $checkcode; //签到码

        $bool = db('order')->insert($result);
        if ($bool) {
            $info = db('order')->where(array('ordersn' => $OrderSn))->find();
            if ($info) {
                template_bm($info['id']);
                return $OrderSn;
            }
            //发送信息

        }
        return "";

    }

    public function customdetail($request)
    {
        $id = $request['id'];

        $count = db('waiter_visit')->where(array('aid' => $id, 'flag' => 2, 'idsite' => session('idsite')))->group('openid')->count();
        $page = new Page($count, PAGE_SIZE);
        $data = db('waiter_visit')->field('openid,name,count(*) as v,max(createtime) as createtime')->where(array('aid' => $id, 'flag' => 2, 'idsite' => session('idsite')))->group('openid')->order('id asc')->limit($page->firstRow . ',' . $page->pageSize)->select();

        $arr = array();
        $arr['pager'] = $page;
        $arr['data'] = $data;
        return $arr;

    }

    /**
     * 获取活动订单数量
     */
    public function getActivityOrderCount($idactivity)
    {

        if (empty($idactivity) || (int) $idactivity <= 0 || (int) $idactivity != $idactivity) {
            return 0;
        }
        $num = db("order")->where("dataid=" . $idactivity)->count();
        return $num;
    }

    public function sendmsg1($data)
    {
        $account = db('account')->where("idaccount", $data["iduser"])->find();
        $touser = $account["openid"];

        if (empty($touser)) {
            return ['status' => 'fail', 'msg' => '该商务未绑定微信'];
        }

        $arrData = [];
        $arrData['first'] = array("value" => $data['chrtitle'], "color" => $data['chrtitlecolor']);
        $arrData['keyword1'] = array("value" => $data['nickname'], "color" => $data['activitynamecolor']);
        $arrData['keyword2'] = array("value" => $data['nowtime'], "color" => $data['activitytimecolor']);
        $arrData['remark'] = array("value" => $data['remark'], "color" => $data['remarkcolor']);

        $arr = [];
        //OPENTM206165551
        $template_key = getWxTemplateId("OPENTM206165551", $this->siteid);
        $arr['Template_key'] = $template_key; //"vb7FWt57VW9LAmkitWP4Bwtp4bw2gxdcOHXNgyBQ4aY";
        $arr['dataid'] = 0;
        $arr['data'] = json_encode($arrData);
        $arr['url'] = $data['chrurl'];
        $arr['touser'] = $touser;
        $arr['inttype'] = $data['inttype'];
        $arr['inttype1'] = $data['inttype1'];
        $arr['username'] = $data['username'];
        $arr['userid'] = $data['userid'];
        $arr['state'] = 1;
        $arr['createtime'] = time();
        $arr['key'] = $data['key'];
        $arr['idsite'] = $this->siteid;
        $arr['ip'] = getip();
        $bl = db("sendmsg")->insert($arr);

        if ($bl) {
            return ['status' => 'success', 'msg' => '发送成功'];
        } else {
            return ['status' => 'fail', 'msg' => '发送失败'];
        }

    }

    /**
     * 保存蜗牛童行信息
     * @param $data
     * @return string
     */
    public function save_wntx_sync_info($data)
    {
        try {
            if (empty($data)) {
                return "数据不能为空！";
            }
            //类型 1-活动 2-夏令营
            if (!isset($data["tid"]) || empty($data["tid"])) {
                return "请选择类型";
            }

            $productid = isset($data["id"]) ? $data["id"] : 0;
            if (empty($productid)) {
                return "请选择商品";
            }

            $post_data = array();
            $post_data["code"] = "commonbll";
            $post_data["tid"] = (int) $data["tid"];
            $post_data["productid"] = $productid; //商品id

            $pretid = (int) $data["pre_tid"];
            if ($pretid > 0 && (int) $data["tid"] != $pretid) {
                return "活动类别不能更改";
            }

            //获取活动信息
            $activity = db("activity")->where(array("idactivity" => $productid, "siteid" => $this->siteid))->find();

            if (empty($activity)) {
                return "活动不存在";
            }
            $duratime = (time() - strtotime($activity["wntx_sync_time"]));
//            if($duratime < 86400){
            //                return "24小时内只能同步一次";
            //            }
            $post_data["chrtitle"] = mb_substr($activity["chrtitle"], 0, 70, 'utf-8');
            $post_data["chrlinkurl"] = ROOTURL . "/" . getSiteCode($this->siteid) . "/detail/" . $productid;
            //活动活动详细地点经度
            $post_data["chrlng"] = $activity["chrmaplng"];
            //维度
            $post_data["chrlat"] = $activity["chrmaplat"];
            //活动详细地点
            $post_data["chraddress"] = $activity["chraddressdetail"];
            //活动小图
            $post_data["chrimg"] = ROOTURL . "/" . $activity["chrimg_m"];
            //活动大图
            $post_data["chrimg1"] = ROOTURL . "/" . $activity["chrimg"];

            $post_data["minage"] = $activity["minage"] ?: 200;
            $post_data["maxage"] = $activity["maxage"] ?: 0;
            $post_data["dtstart"] = $activity["dtstart"];
            $post_data["dtend"] = $activity["dtend"];
            $post_data["chrkeyword"] = $activity["chrkeyword"];
            $post_data["dtsignstime"] = $activity["dtsignstime"];
            $post_data["dtsignetime"] = $activity["dtsignetime"];
            $post_data["ischarge"] = $activity["ischarge"];
            $member_price = 0;
            if ($activity["ischarge"] == 2) {
                $selcontent = $activity["selcontent"];
                $arr = explode("☆", $selcontent);
                if (isset($arr[3])) {
                    $arr1 = explode("∮", $arr[3]);
                    foreach ($arr1 as $key => $v) {
                        if ($key == 0) {
                            $member_price = $v;
                        }
                        if ($member_price > $v) {
                            $member_price = $v;
                        }
                    }
                }
            }
            $post_data["memberprice"] = $member_price;
            $post_data["isdown"] = 1;
            $post_data["ispush"] = 2;

            $post_data["chrdistrict"] = isset($data["chrdistrict"]) ? $data["chrdistrict"] : "";

            switch ((int) $data["tid"]) {
                case 1:
                    //活动分类标签
                    if (!isset($data["label_array"]) || empty($data["label_array"])) {
                        return "请选择活动分类";
                    }
                    $chrtypename = str_replace("-&-", ",", $data["label_array"]);
                    $label_array = explode("-&-", $data["label_array"]);
                    $fidtype1 = $fidtype2 = "";
                    foreach ($label_array as $label) {
                        $tmpArr = explode("|", $label);
                        $fidtype1 .= $tmpArr[0] . ",";
                        $fidtype2 .= $tmpArr[1] . ",";
                    }
                    if ($fidtype1) {
                        $fidtype1 = substr($fidtype1, 0, -1);
                    }
                    if ($fidtype2) {
                        $fidtype2 = substr($fidtype2, 0, -1);
                    }
                    $post_data["fidtype1"] = $fidtype1;
                    $post_data["fidtype2"] = $fidtype2;
                    $post_data["chrtypename"] = $chrtypename;
                    $post_data["fidarea"] = isset($data["activity_area"]) ? $data["activity_area"] : 0;
                    $post_data["fiddistrict"] = isset($data["activity_bussarea"]) ? $data["activity_bussarea"] : 0;
                    break;
                case 2:
                    //活动分类标签
                    if (!isset($data["summer_label_array"]) || empty($data["summer_label_array"])) {
                        return "请选择夏令营活动分类";
                    }
                    $chrtypename = str_replace("-&-", ",", $data["summer_label_array"]);
                    $summer_label_array = explode("-&-", $data["summer_label_array"]);
                    $fidtype1 = $fidtype2 = "";
                    foreach ($summer_label_array as $label) {
                        $tmpArr = explode("|", $label);
                        $fidtype1 .= $tmpArr[0] . ",";
                        $fidtype2 .= $tmpArr[1] . ",";
                    }
                    if ($fidtype1) {
                        $fidtype1 = substr($fidtype1, 0, -1);
                    }
                    if ($fidtype2) {
                        $fidtype2 = substr($fidtype2, 0, -1);
                    }
                    $post_data["fidtype1"] = $fidtype1;
                    $post_data["fidtype2"] = $fidtype2;
                    $post_data["chrtypename"] = $chrtypename;
                    if (isset($data["country"])) {
                        $post_data["tourismid"] = (int) $data["country"];
                        switch ($data["country"]) {
                            case 1:
                                $post_data["tourismname"] = "国内";
                                break;
                            case 2:
                                $post_data["tourismname"] = "国际";
                                break;
                        }
                    }
                    $post_data["fidarea"] = isset($data["province"]) ? ("," . $data["province"] . ",") : 0;
                    $post_data["fiddistrict"] = isset($data["city"]) ? ("," . $data["city"] . ",") : 0;

                    $post_data["chrdistrictlist"] = $data["province"] . "|" . $data["city"] . "|" . $post_data["chrdistrict"];
                    break;
            }
            if (!isset($data["ability_label_array"]) || empty($data["ability_label_array"])) {
                return "请选择能力标签";
            }
            $abilitytagsname = str_replace("-&-", ",", $data["ability_label_array"]);
            $ability_label_array = explode("-&-", $data["ability_label_array"]);
            $abilitytags = "";
            $abilitytagsnames = '';
            foreach ($ability_label_array as $label) {
                $tmpArr = explode("|", $label);
                $abilitytags .= $tmpArr[0] . ",";
                $abilitytagsnames .= $tmpArr[1] . ","; //名称
            }
            if ($abilitytags) {
                $abilitytags = substr($abilitytags, 0, -1);
            }
            //截掉最后一个逗号
            if ($abilitytagsnames) {
                $abilitytagsnames = substr($abilitytagsnames, 0, -1);
            }
            $post_data["abilitytags"] = $abilitytags;
            $post_data["abilitytagsname"] = $abilitytagsnames;
            $site_info = db('site_manage')->field('site_name,expiretime')->where(array('id' => $this->siteid))->find();
            $post_data["chrorganizationname"] = $site_info['site_name']; //机构名(2019年6月5号新加)
            $url = config("wxnt_sync_url");
            $rs = HttpCurl::post($url, http_build_query($post_data), "json");
            \think\Log::info('请求的参数:' . json_encode($post_data) . '---同步接口数据返回' . json_encode($rs));
            $rs = json_decode(json_encode($rs), true);
            if ($rs["code"] == 1) {
                $sync_data = array();
                $sync_data["wntx_sync_status"] = 1;
                $sync_data["wntx_sync_time"] = date("Y-m-d H:i:s");
                db("activity")->where(array("idactivity" => $productid, "siteid" => $this->siteid))->update($sync_data);
                return "success";
            } else {
                return "同步失败";
            }
        } catch (\Exception $ex) {
            return "保存异常，请联系网站开发人员";
        }
    }

    /**
     * 改变同步状态
     * @param $data     需要传递 sync_status：同步状态 1,2   id:活动id
     * @return string
     */
    public function change_sync_status($data)
    {
        try {
            $sync_status = isset($data["sync_status"]) ? $data["sync_status"] : 0;
            if (empty($sync_status) || !in_array($sync_status, array(1, 2))) {
                return "同步状态有误，请传递正确的值1和2";
            }
            $productid = isset($data["id"]) ? $data["id"] : 0;
            $tid = "";
            if (empty($productid)) {
                return "活动id不存在，请先传递活动id";
            }

            $url = config("wxnt_sync_url");
            $rs = HttpCurl::post($url, http_build_query(array("code" => "searchcommon", "productid" => $productid)), "json");
            $rs = json_decode(json_encode($rs), true);
            if ($rs["code"] == 1) {
                $datainfo = isset($rs["data"]["data"][0]) ? $rs["data"]["data"][0] : 0;
                $tid = 1;
                if (isset($datainfo["issummercamp"]) && $datainfo["issummercamp"] == 1) {
                    $tid = 2;
                }
            }
            //获取活动类型
            if (empty($tid)) {
                return "获取活动信息失败,请稍后再试";
            }

            //同步状态
            $post_data = array();
            $post_data["code"] = "commonbll";
            $post_data["tid"] = $tid; //1-活动 2-夏令营
            $post_data["productid"] = $productid; //商品id
            $post_data["isdown"] = $sync_status; //是否下架 1：否 2：是
            $ispush = ($sync_status == 1) ? 2 : 1;
//            $post_data["ispush"] = $ispush;                  //是否刷新发布时间 1：否 2：是
            $rs = HttpCurl::post($url, http_build_query($post_data), "json");
            \think\Log::info('请求的参数:' . json_encode($post_data) . '---取消同步接口数据返回' . json_encode($rs));
            $rs = json_decode(json_encode($rs), true);
            if ($rs["code"] == 1) {
                $sync_data = array();
                $sync_data["wntx_sync_status"] = $sync_status;
                if ($sync_status == 1) {
                    $sync_data["wntx_sync_time"] = date("Y-m-d H:i:s");
                } else {
                    $sync_data["wntx_sync_cancel_time"] = date("Y-m-d H:i:s");
                }
                db("activity")->where(array("idactivity" => $productid, "siteid" => $this->siteid))->update($sync_data);
                return "success";
            } else {
                return "同步失败";
            }
        } catch (\Exception $ex) {
            return "接口异常，请联系网站开发人员";
        }
    }

    /**
     * 查找活动的详情
     * @param $params
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function getActivityDetail($params)
    {
        $result = db('activity')->where(['idactivity' => $params['id'], "siteid" => $this->siteid])->field('audit_remark')->find();
        if ($result['audit_remark']) {
            return array_reverse(json_decode($result['audit_remark'], true));
        }
        return array();
    }
}