<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/28
 * Time: 10:29
 */

namespace app\admin\controller;
use think\Request;

class Comment extends Base{

    //评论列表
    public function index(){
        if($this->CMS->CheckPurview('commentmanage','view')==false){
            $this->NoPurview();
        }
        $obj = new \app\admin\module\Comment;
        $request = Request::instance()->param();
        $result = $obj->index($request);
        $comment_list = $result['comment_list'];
        $page = $result['page'];
        $search = $result['search'];
        $this->assign('comment_list',$comment_list);
        $this->assign('search',$search);
        $this->assign('page',$page);
        $this->assign('idsite',$result['idsite']);
        return $this->fetch();
    }

    //审核(单个审核)
    public function auditing(){
        if($this->CMS->CheckPurview('commentmanage','auditing')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Comment;
        $bool = $obj->auditing($request);
        if($bool){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    //批量审核
    public function allpass(){
        if($this->CMS->CheckPurview('commentmanage','auditing')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Comment;
        $num = $obj->all_pass($request);
        return $num;
    }

    //查看评论
    public function commentview(){
        if($this->CMS->CheckPurview('commentmanage','view')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Comment;
        $result = $obj->comment_view($request);
        $this->assign('comment_info',$result['comment_info']);
        $this->assign('model_field',$result['model_field']);
        return $this->fetch();
    }
    public function re(){
        //commentmanage
        if($this->CMS->CheckPurview('commentmanage','edit')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $id=$request['id'];
        $row=[];
//        $row['recontent']="";
        // var_dump($id, Request::instance()->isPost());die;
        if($id>0)
        {
            $idsite = session('idsite');
            if (Request::instance()->isPost())
            {
                //如果是回复的话
                if(empty($request['recontent']) && array_key_exists('sub1',$request)){
                    $this->error('回复内容不能为空');
                }
                $arr=[];
                $arr['recontent']=$request['recontent'];
                $arr['rename']=session('UserName');
                $arr['reid']=session('AccountID');
                $arr['retime']=time();
                $arr['intstate']=4;
//                $arr['show']=($request['show']==2?2:1);
                //如果是屏蔽才修改状态(回复不修改状态)
                if(array_key_exists('sub2',$request)){
                    $arr['show'] = $request['show'];
                }

                $comment = db('comment')->where(array('id'=>$id,'idsite'=>$idsite))->find();
//                if($arr['recontent'] == $comment['recontent'] && $arr['show'] == $comment['show'])
//                {
//                    $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
//                }

                $bool=db('comment')->where(array('id'=>$id,'idsite'=>$idsite))->update($arr);
                // $bool = true;
                if($bool)
                {
                    //如果本次回复是活动类回复，且本操作是修改或添加评论回复，则发短信
                    if($arr['recontent'] != $comment['recontent'] && $comment['flag'] == 2)
                    {
                        $obj = new \app\admin\module\Comment;
                        $obj->replyCommentNotice($comment['dataid'], $comment['iduser']);
                    }
                    $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
                }else
                {
                    $this->error('操作失败');
                }
            }
            $row=db('comment')->where(array('id'=>$id,'idsite'=>$idsite))->find();

        }
        $this->assign('info',$row);
        return $this->fetch();
    }

}