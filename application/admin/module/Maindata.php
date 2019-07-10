<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/08/31
 * Time: 16:07
 */

namespace app\admin\module;
use think\Model;
use think\db;
use think\Page;

class Maindata  extends Model{

    //角色列表
    public function test(){

        $Tmp='<root>'."\r\n";
        $Tmp=$Tmp.'      <item Title="8.28例会内容" Url="/Skins/fra/BBS/BBSChiled.aspx?&amp;sysModuleID=36179&amp;discussionid=12125&amp;teamid=118&amp;key=42E13473ECE4425FCFE7C1219B34325B" DateTime="08.28 10:18" />'."\r\n";
        $Tmp=$Tmp.'      <item Title="8月21日例会内容" Url="/Skins/fra/BBS/BBSChiled.aspx?&amp;sysModuleID=36179&amp;discussionid=12116&amp;teamid=118&amp;key=65ED74F98C99681FAF099430CC78C225" DateTime="08.21 11:28" />'."\r\n";
        $Tmp=$Tmp.'      <item Title="8月14日例会内容" Url="/Skins/fra/BBS/BBSChiled.aspx?&amp;sysModuleID=36179&amp;discussionid=12101&amp;teamid=118&amp;key=A5B5F3A9794CC3851767E1758BD02D36" DateTime="08.14 09:23" />'."\r\n";
        $Tmp=$Tmp.'      <item Title="CMS 文件下载" Url="/Skins/fra/BBS/BBSChiled.aspx?&amp;sysModuleID=36179&amp;discussionid=12100&amp;teamid=116&amp;key=E6F6608A783D0413A1D8D2C951642CF7" DateTime="08.11 15:28" />'."\r\n";
        $Tmp=$Tmp.'      <item Title="8月7日例会内容" Url="/Skins/fra/BBS/BBSChiled.aspx?&amp;sysModuleID=36179&amp;discussionid=12088&amp;teamid=118&amp;key=42C9A6150ADF93D13AB43DE67D4BF867" DateTime="08.07 09:22" />'."\r\n";
        $Tmp=$Tmp.'</root>'."\r\n";
        return $Tmp;
    }

    public function listAccount($data)
    {
        $t1 = microtime(true);
        $TopN=5;
        if(isset($data['TopN'])) $TopN=$data['TopN'];
        $t2 = microtime(true);
        $result = db('account')->limit(0,$TopN)->order('intsn asc,idaccount desc')-> select();
        //$result = db('account')->select();
        $t3 = microtime(true);
        $Tmp='<root>'."\r\n";
        foreach ($result as $value)
        {
            $Tmp=$Tmp.'      <item Title="'.$value['chrname'].'['.$value['chraccount'].']" Url="'.url('admin/account/accountdeal', ['id'=>$value['idaccount'],'action'=>'view']) .'" DateTime="08.07 09:22" />'."\r\n";
        }
        $Tmp=$Tmp.'</root>'."\r\n";
        $t4 = microtime(true);
        $msg=$TopN.'    程序耗时'.round($t4-$t1,10).'秒->T2:'.round($t3-$t2,10).'秒,T3:'.round($t4-$t3,10).'秒'."\r\n";
        //if($t4-$t1>0.2)
         //   $this->writeLog($msg);

        return $Tmp;

    }

    public function listActivityBacklog($data)
    {
        $TopN=5;
        $siteid=empty(session('idsite'))?0:session('idsite');
        if(isset($data['TopN'])) $TopN=$data['TopN'];
        $result = db('activity')->where(array('intflag'=>1,'siteid'=>$siteid))->limit(0,$TopN)->order('dtsignstime asc,idactivity asc')-> select();
        //$result = db('account')->select();
        $Tmp='<root>'."\r\n";
        foreach ($result as $value)
        {
            $Tmp=$Tmp.'      <item Title="'.$value['chrtitle'].'" Url="'.url('admin/activity/activitycheck', ['id'=>$value['idactivity'],'action'=>'edit']) .'" DateTime="'.date('Y-m-d',strtotime($value['dtsignstime'])).'" />'."\r\n";
        }
        $Tmp=$Tmp.'</root>'."\r\n";

        return $Tmp;

    }

    public function listchecked($data)
    {
        $TopN=5;
        $siteid=empty(session('idsite'))?0:session('idsite');

        if(isset($data['TopN'])) $TopN=$data['TopN'];
        $result = db('activity')->where(array('intflag'=>3,'siteid'=>$siteid))->limit(0,$TopN)->order('dtsignstime asc,idactivity asc')-> select();
        //$result = db('account')->select();

        $Tmp='<root>'."\r\n";
        foreach ($result as $value)
        {
            $Tmp=$Tmp.'      <item Title="'.$value['chrtitle'].'" Url="'.url('admin/activity/activitycheck', ['id'=>$value['idactivity'],'action'=>'edit']) .'" DateTime="'.date('Y-m-d',strtotime($value['dtsignstime'])).'" />'."\r\n";
        }
        $Tmp=$Tmp.'</root>'."\r\n";

        return $Tmp;

    }


    public function listCommentRe($data)
    {
        $TopN=5;
        $siteid=empty(session('idsite'))?0:session('idsite');
        if(isset($data['TopN'])) $TopN=$data['TopN'];
        $result = db('comment')->where(array('reid'=>0,'idsite'=>$siteid))->limit(0,$TopN)->order('id asc')-> select();
        $Tmp='<root>'."\r\n";
        foreach ($result as $value)
        {
            $url=url('admin/comment/re', ['id'=>$value['id']]);
            $js="CustomOpen('".$url."','Comment".$value['id']."','回复评论','520','300');";

            $Tmp=$Tmp.'      <item Title="'.$value['content'].'"  Url="'. $js.'" DateTime="'.date('Y-m-d',$value['createtime']).'" />'."\r\n";
        }
        $Tmp=$Tmp.'</root>'."\r\n";

        return $Tmp;

    }
    
    private function writeLog($msg)
    {
        $CodePath='log/';
        if (is_dir($CodePath)==false) {
            mkdir($CodePath, 0777, true);
        }
        $CodePath=$CodePath.'/'.date("Y-m-d").'.txt';
        file_put_contents($CodePath,$msg,FILE_APPEND);

    }

    //今日数据统计
    public function todayDataStatistics()
    {

        $siteid= session('idsite');//
        $siteid = empty($siteid)?0:$siteid;

        $Tmp = '<root>' . "\r\n";

        //当日新用户数量，当日取消关注用户数量，当日订单数量，当日总销售额
        $todayNewUserNum = db('member')->where(array("dtsubscribetime"=>array(">= time", date("Y-m-d")),"idsite"=>$siteid))->count();
        $todayUnfollowNum = db('member')->where(array("dtunsubscribetime"=>array(">= time", date("Y-m-d")),"idsite"=>$siteid))->count();
        $todayOrderNum = db('order')->where(array("dtcreatetime"=>array(">= time", date("Y-m-d")),"idsite"=>$siteid))->where("state", "not in", [1, 2, 9, 12])->count();
        $todaySales = db('order')->where(array("dtcreatetime"=>array(">= time", date("Y-m-d")),"idsite"=>$siteid))->where("state", "in", [3, 4, 5, 6])->sum("price")?:"0.00";
        $todayRefundOrderNum = db('order')->where(array("dtcreatetime"=>array(">= time", date("Y-m-d")),"idsite"=>$siteid))->where("state", "in", [5,6,7,8,10,11,13])->count();
        $todayRefundSales = db('order')->where(array("dtcreatetime"=>array(">= time", date("Y-m-d")),"idsite"=>$siteid))->where("state", "in", [5,6,7,8,10,11,13])->sum("price")?:"0.00";

        $url=url('admin/member/index', ['rstime'=>date("Y-m-d"),'retime'=>date("Y-m-d")]);
        $Tmp .= "<item Title='新增关注:" . $todayNewUserNum . "' Url='".$url."' DateTime='今日' />\r\n";

        $url=url('admin/member/index', ['cstime'=>date("Y-m-d"),'cetime'=>date("Y-m-d")]);
        $Tmp .= "<item Title='取消关注:" . $todayUnfollowNum . "' Url='".$url."' DateTime='今日' />\r\n";

        $url=url('admin/order/index', ['intflag'=>5,'dtstart'=>date("Y-m-d"),'dtend'=>date("Y-m-d"),"state"=>0]);
        $Tmp .= "<item Title='销售订单:" . $todayOrderNum . "' Url='".$url."' DateTime='今日' />\r\n";
        $Tmp .= "<item Title='销售总额:¥ " . $todaySales . "' Url='".$url."'  DateTime='今日'/>\r\n";


        $url=url('admin/order/index', ['intflag'=>6,'dtstart'=>date("Y-m-d"),'dtend'=>date("Y-m-d"),"state"=>0]);
        $Tmp .= "<item Title='退款订单:" . $todayRefundOrderNum . "' Url='".$url."'  DateTime='今日'/>\r\n";
        $Tmp .= "<item Title='退款金额:¥ " . $todayRefundSales . "' Url='".$url."'  DateTime='今日'/>\r\n";

        $Tmp = $Tmp . '</root>';

        return $Tmp;
    }

    //待审核退款记录
    public function waitRefundRecord($data)
    {
        $siteid= session('idsite');//
        $siteid = empty($siteid)?0:$siteid;
        $TopN = 5;
        if (isset($data['TopN'])) $TopN = $data['TopN'];
        $result = db('order')->where(array('state' => 5,"idsite"=>$siteid))->limit(0, $TopN)->order('id asc')->field('id,chrusername,marketname,chrtitle,dtrefundtime')->select();
        $Tmp = '<root>' . "\r\n";
        foreach ($result as $value) {
            $url = url('admin/order/refund','action=edit&id='.$value['id']);
            $js = "CustomOpen('" . $url . "','refund','退款','650','550');";
            $title = $value["chrusername"]."(".$value["marketname"].")"."-".$value["chrtitle"];
            $Tmp = $Tmp . '      <item Title="' . $title . '"  Url="' . $js . '" DateTime="' . $value["dtrefundtime"] . '" />' . "\r\n";
        }
        $Tmp = $Tmp . '</root>' . "\r\n";
        return $Tmp;
    }

    public function waitFollowClient($data)
    {
        $siteid= session('idsite');//
        $siteid = empty($siteid)?0:$siteid;
        $AccountID = session('AccountID');
        $AccountID= empty($AccountID)?0:$AccountID;
        $TopN = 5;
        if (isset($data['TopN'])) $TopN = $data['TopN'];
        $currentDate = date("Y-m-d",time());
        $result = db('member')->where(array('iduser' => $AccountID,"followuptime"=>array("<= time",$currentDate),"idsite"=>$siteid))->limit(0, $TopN)->order('idmember asc')->field('idmember,nickname,chrtel,dtlastvisitteim')->select();
        $Tmp = '<root>' . "\r\n";
        foreach ($result as $value) {
            $url = url('admin/member/followup','memberid='.$value['idmember']."&onlyMember=1");
            $js = "CustomOpen('" . $url . "','memberview','查看会员信息','800','560');";
            $title = $value["nickname"]."(".(!empty($value["chrtel"])?$value["chrtel"]:"未记录").")";
            $Tmp = $Tmp . '      <item Title="' . $title . '"  Url="' . $js . '" DateTime="' .( date("Y-m-d",$value["dtlastvisitteim"])?:"未跟进") . '" />' . "\r\n";
        }
        $Tmp = $Tmp . '</root>' . "\r\n";
        return $Tmp;
    }

    // 系统更新
    public function systemUpdate($data){
        $TopN = 5;
        if (isset($data['TopN'])) $TopN = $data['TopN'];
        $result = db('system_update')->where("is_open",1)->limit(0,$TopN)->order('update_time desc')->select();
        $Tmp = '<root>' . "\r\n";
        foreach ($result as $value) {
            $url = url('systemupdate/singlepage','id='.$value['id']);
            $js = "javascript:CustomOpen('" . $url . "','refund','系统日志','650','500');";
            $Tmp = $Tmp . '      <item Title="' . $value["title"] . '"  Url="' . $js . '" DateTime="' . date("Y-m-d",$value["update_time"] ? $value["update_time"] : $value["create_time"]) . '" />' . "\r\n";
        }
        $Tmp = $Tmp . '</root>' . "\r\n";
        return $Tmp;
    }

    // 新功能提醒
    public function newfeatuRerecommend($data){
        $TopN = 5;
        if (isset($data['TopN'])) $TopN = $data['TopN'];
        $result = db('new_feature_recommend')->where("is_open",1)->limit(0,$TopN)->order('update_time desc')->select();
        $Tmp = '<root>' . "\r\n";
        foreach ($result as $value) {
            $url = url('newfeaturerecommend/singlepage','id='.$value['id']);
            $js = "javascript:CustomOpen('" . $url . "','refund','系统日志','650','500');";
            $Tmp = $Tmp . '      <item Title="' . $value["title"] . '"  Url="' . $js . '" DateTime="' . date("Y-m-d",$value["update_time"] ? $value["update_time"] : $value["create_time"]) . '" />' . "\r\n";
        }
        $Tmp = $Tmp . '</root>' . "\r\n";
        return $Tmp;
    }
}