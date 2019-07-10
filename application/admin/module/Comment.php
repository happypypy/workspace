<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/28
 * Time: 10:30
 */

namespace app\admin\module;
use think\Model;
use think\Page;

class Comment extends Model {

    //评论列表
    public function index($data){


        if(array_key_exists('p',$data) == false){
            $data['p'] = '1';
        }
        $search=[];
        $search['flag']="0";
        $search['username']="";
        $search['content']="";
        $search['stime']="";
        $search['etime']="";




        $where="idsite=".session('idsite');
        if(!empty($data['flag']) && $data['flag']!=0)
        {
            $search['flag']=$data['flag'];
            $where=$where." and flag ='".$data['flag']."'";
        }
        if(!empty($data['username']))
        {
            $search['username']=$data['username'];
            $where=$where." and username like '%".$data['username']."%'";
        }
        if(!empty($data['content']))
        {
            $search['content']=$data['content'];
            $where=$where." and content like '%".$data['content']."%'";
        }
        if(!empty($data['stime']) && !empty($data['etime']))
        {
            $search['stime']=$data['stime'];
            $search['etime']=$data['etime'];
            $where=$where." and ( createtime > ".strtotime($data['stime'])." and createtime <".strtotime($data['etime']."23:59:59").")";
        }

        //echo $where;
        $count = db('comment')->where($where)->count();
        $page = new Page($count,PAGE_SIZE);
        $comment_list = db('comment')->where($where)->limit($page->firstRow.','.$page->pageSize)->select();

        $result = [];
        $result['search'] = $search;
        $result['comment_list'] = $comment_list;
        $result['page'] = $page;
        $result['idsite'] = session('idsite');
        return $result;
    }

    //评论审核
    public function auditing($data){
        $bool = db('comment')->where('commentid='.$data['commentid'])->setField('intlock',$data['type']);
    }

    //批量审核
    public function all_pass($data){
        if(array_key_exists('id',$data)){
            if(strstr($data['id'],',')){
                $id = explode(',',$data['id']);
                for ($i=0;$i<count($id);$i++){
                    $bool = db('comment')->where('commentid',$id[$i])->setField('intlock',$data['type']);
                }
            }else{
                $bool = db('comment')->where('commentid',$data['id'])->setField('intlock',$data['type']);
            }
            if($bool){
                return 1;
            }else{
                return 2;
            }
        }else{
            return 2;
        }
    }

    //查看评论
    public function comment_view($data){
        $comment_info = db('comment')->where('commentid='.$data['commentid'])->find();
        //根据模型id找出对应的模型字段
        $model_fields = db('modelfield')->where('idmodel='.$comment_info['idmodel'].' and isusing=1')->select();
        foreach ($model_fields as $key => $value){
            if(!empty($value['childsetting'])){
                if(strstr($value['childsetting'],'∮')){
                    $arr = explode('∮',$value['childsetting']);
                    $model_fields[$key]['childsetting'] = $arr;
                }

            }
        }
        $result['comment_info'] = $comment_info;
        $result['model_field'] = $model_fields;
        return $result;
    }


    public function replyCommentNotice($activityId, $userId, $idsite = '')
    {
        $idsite = $idsite ? : session('idsite');
        //获取商务和活动信息
        $activity = db('activity')
            ->field([
                'cms_activity.short_title',
                'cms_activity.intselmarket',
                'account.mobile'
            ])
            ->join(
                'account',
                'cms_activity.intselmarket = account.idaccount',
                'LEFT'
            )
            // ->fetchSql(true)
            ->where([
                'idactivity' => $activityId
            ])
            ->find();
        //获取用户信息
        $member = db('member')->where(['idmember' => $userId])->find();
        if($activity)
        {
            $order = [];
            $replace = [
                '{name}' => $member['chrname'],
                '{title}' => $activity['short_title'],
            ];
            // var_dump(session('idsite'), 12, $order, $replace, $member['chrtel'], $activity['mobile']);die;
            //给客户和商务发短信通知    类型：12--回复/修改评论
            sysSendMsg($idsite, 12, $order, $replace, $member['chrtel'], $activity['mobile']);
        }
    }


}