<?php

namespace app\home\model;
use think\Model;

class Activities extends Model {

    private $PageSite=20;
    protected  $siteid=0;
    function __construct($idStie){
        $this->siteid=$idStie;
        parent::__construct();
    }

    //活动
    public function SearchActivities($id=0,$OpenID='', $ipage=1)
    {
        if($id<1)
        {
            $stu_where=[];
            $stu_where['parent1_openid']=$OpenID;
            $stu_where['parent2_openid']=$OpenID;
            $stu_where['parent3_openid']=$OpenID;
            $stu_result= db('activities_student')->whereOr($stu_where)->field('id_activities')->select();

            if(empty($stu_result))
            {
                return [];
            }

            $tmpArr=[];
            foreach ($stu_result as $vo)
            {
                $tmpArr[]=$vo['id_activities'];
            }

            $where=[];
            $where['id']=['in',$tmpArr];
        }
        $where['id_site']=$this->siteid;
        if($id>0) $where['id']=$id;
        $result= db('activities')->where($where)->order('start_time desc')->limit(($ipage-1)*$this->PageSite,$this->PageSite)->select();
        return $result;
    }
    //每日总结
    public function SearchDaySummary ( $activitiesid,$id=0,$ipage=1)
    {
        $where=[];
        $where['id_site']=$this->siteid;
        if($activitiesid>0) $where['id_activities']=$activitiesid;
        if($id>0) $where['activities_student']=$id;
        return db('activities_day_summary')->where($where)->order('id desc')->limit(($ipage-1)*$this->PageSite,$this->PageSite)->select();
    }
    //学生
    public function SearchStudent ( $activitiesid=0,$OpenID="",$ipage=1)
    {
        $where=[];
        $where['id_site']=$this->siteid;
        if($activitiesid>0) $where['id_activities']=$activitiesid;
        $stu_where=[];
        if($OpenID!="")
        {
            $stu_where['parent1_openid']=$OpenID;
            $stu_where['parent2_openid']=$OpenID;
            $stu_where['parent3_openid']=$OpenID;
        }
        $result= db('activities_student')->where($where)->whereOr($stu_where)->order('id asc')->limit(($ipage-1)*$this->PageSite,$this->PageSite)->select();
       // print_r(db('activities_student')->getLastSql());
        return $result;
    }
    //学生评价
    public function SearchStudentComment ( $id=0,$activitiesid,$studentid=0,$ipage=1)
    {
        $where=[];

        $where['id_site']=$this->siteid;
        if($activitiesid>0) $where['id_activities']=$activitiesid;
        if($studentid>0) $where['id_stu']=$studentid;
        if($id>0) $where['activities_student']=$id;
        return db('activities_stu_comment')->where($where)->order('id asc')->limit(($ipage-1)*$this->PageSite,$this->PageSite)->select();
    }
    //老师
    public function Searchteacher ( $activitiesid,$id=0,$ipage=1)
    {
        $where=[];
        if($activitiesid>0) $where['id_activities']=$activitiesid;
        if($id>0) $where['activities_student']=$id;
        return db('activities_teacher')->where($where)->order('id asc')->limit(($ipage-1)*$this->PageSite,$this->PageSite)->select();
    }

}