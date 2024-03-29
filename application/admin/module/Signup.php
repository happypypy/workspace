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
 * Date:2018-06-26 */
namespace app\admin\module;
use think\Model;
use think\Page;
use think\Session;

class Signup extends Model{
    //列表
    public function allindex($request){

        $search=array();

        $Search_str="idsite=".session('idsite');
        $count = db('signup_template')->where($Search_str)->count();

        $page = new Page($count,PAGE_SIZE);
        $data = db('signup_template')->where($Search_str)->order('id desc')->limit($page->firstRow.','.$page->pageSize)->select();

        $arr = array();
        $arr['search'] = $search;
        $arr['pager'] = $page;
        $arr['data'] = $data;
        return $arr;
    }
    public function index($request){

        $search=array();

        $Search_str="idsite=".session('idsite') ." and userid=".session('AccountID');
        $count = db('signup_template')->where($Search_str)->count();

        $page = new Page($count,PAGE_SIZE);
        $data = db('signup_template')->where($Search_str)->order('id desc')->limit($page->firstRow.','.$page->pageSize)->select();

        $arr = array();
        $arr['search'] = $search;
        $arr['pager'] = $page;
        $arr['data'] = $data;
        return $arr;
    }

    public function copyTemplate($id)
    {
        $rows = db('signup_template')->where('id=:id and idsite=:idsite',['id'=>$id,'idsite'=>session('idsite')])->find();
        if($rows)
        {
            $data=[];
            $data["action"]="add";
            $data["title"]=$rows["title"]."[复制]";
            $data["remark"]=$rows["remark"];
            $this->PostData($data);
            $id = db('signup_template')->getLastInsID();

            $result = db('signup_template_sub')->where("idsite=".session('idsite')." and pid=".$rows['id'])->order('id desc')->select();
            if($result)
            {
                foreach ($result as $k=>$vo)
                {
                    $tmpArr=[];
                    $tmpArr['action']='add';
                    $tmpArr['pid']=$id;
                    $tmpArr['title']=empty($vo['title'])?"":$vo['title'];
                    $tmpArr['remark']=empty($vo['remark'])?"":$vo['remark'];
                    $tmpArr['chrtype']=empty($vo['chrtype'])?"":$vo['chrtype'];
                    $tmpArr['sn']=empty($vo['sn'])?"":$vo['sn'];
                    $this->PostDatasub($tmpArr);
                }
            }
            $result = db('signup_template_sub1')->where("idsite=".session('idsite')." and pid=".$rows['id'])->order('id desc')->select();
            if($result)
            {
                foreach ($result as $k=>$vo)
                {
                    $tmpArr=[];
                    $tmpArr['action']='add';
                    $tmpArr['pid']=$id;
                    $tmpArr['title']=empty($vo['title'])?"":$vo['title'];
                    $tmpArr['remark']=empty($vo['remark'])?"":$vo['remark'];
                    $tmpArr['chrtype']=empty($vo['chrtype'])?"":$vo['chrtype'];
                    $tmpArr['sn']=empty($vo['sn'])?"":$vo['sn'];
                    $this->PostDatasub1($tmpArr);
                }
            }
        }

    }

    //增改查页面处理
    public function deal($data){
        if(array_key_exists('id',$data)){
            $result = db('signup_template')->where('id=:id and idsite=:idsite',['id'=>$data['id'],'idsite'=>session('idsite')])->find();
        }else{
            $result = [
                'id'      =>  '',
                'title'      =>  '',
                'remark'      =>  '',
                'state'      =>  '',
                'userid'      =>  '',
                'username'      =>  '',
                'createtime'      =>  '',
            ];
        }
        $result['action'] = $data['action'];
        return $result;
    }

    //添加，修改提交地址
    public function PostData($data){
        $tmpArr=array();
        if(isset($data['title'])) $tmpArr['title']=trim($data['title']);
        if(isset($data['remark'])) $tmpArr['remark']=trim($data['remark']);
        $tmpArr['state']=isset($data['state'])?trim($data['state']):"0";

        if($data['action'] == 'add'){
            $tmpArr['userid']=session('AccountID');
            $tmpArr['username']=session('UserName');
            $tmpArr['createtime']=date("Y-m-d H:i:s");
            $tmpArr['idsite']=session('idsite');
            $bool = db('signup_template')->insert($tmpArr);
            $id =db('user')->getLastInsID();
            $pid = db('signup_template')->getLastInsID();
            $tmp = array("pid"=>$pid,"title"=>"姓名","chrtype"=>9,"sn"=>1,"dtcreatetime"=>date("Y-m-d H:i:s"),"idsite"=>session('idsite'),"is_must"=>1);
            db('signup_template_sub')->insert($tmp);

            $tmp = array("pid"=>$pid,"title"=>"联系电话","chrtype"=>10,"sn"=>2,"dtcreatetime"=>date("Y-m-d H:i:s"),"idsite"=>session('idsite'),"is_must"=>1);
            db('signup_template_sub')->insert($tmp);

        }else{

            if(db('signup_template')->where('id=:id and idsite=:idsite',['id'=>$data['id'],'idsite'=>session('idsite')])->update($tmpArr))
            {
               $id=$data['id'];
            }
            else
            {
                $id=0;
            }

        }
        return $id;
    }


    //删除
    public function del($data){

        if(isset($data['id'])==false)
            return false;
        if(strstr($data['id'],','))
            $bool = db('signup_template')->where('idsite=:idsite',['idsite'=>session('idsite')])->where('id','in',explode(',',$data['id']))->delete();
        else
            $bool = db('signup_template')->where('id=:id and idsite=:idsite',['id'=>$data['id'],'idsite'=>session('idsite')])->delete();

        return $bool;
    }

    //列表
    public function indexsub($request){

        $Search_str="idsite=".session('idsite')." and pid=".$request['pid'];
        $count = db('signup_template_sub')->where($Search_str)->count();
        $page = new Page($count,PAGE_SIZE);
        $data = db('signup_template_sub')->where($Search_str)->order('sn asc,id asc')->limit($page->firstRow.','.$page->pageSize)->select();

        $arr = array();
        $arr['search'] = array();
        $arr['pager'] = $page;
        $arr['data'] = $data;
        return $arr;
    }
    //增改查页面处理
    public function dealsub($data){
        if(array_key_exists('id',$data)){
            $result = db('signup_template_sub')->where('id=:id and idsite=:idsite',['id'=>$data['id'],'idsite'=>session('idsite')])->find();
        }else{
            $result = [
                'id'      =>  '',
                'pid'      =>  '',
                'title'      =>  '',
                'remark'      =>  '',
                'chrvalue'    => '',
                'chrtype'      =>  1,
                'sn'      =>  '',
                'dtcreatetime'      =>  '',
                'is_must' => 0
            ];
        }
        $result['action'] = $data['action'];
        return $result;
    }

    //添加，修改提交地址
    public function PostDatasub($data){
        $tmpArr=array();
        if(isset($data['pid'])) $tmpArr['pid']=trim($data['pid']);
        if(isset($data['title'])) $tmpArr['title']=trim($data['title']);
        if(isset($data['remark'])) $tmpArr['remark']=trim($data['remark']);
        if(isset($data['chrvalue'])) $tmpArr['chrvalue']= str_replace("，",",", trim($data['chrvalue']));
        if(isset($data['chrtype'])) $tmpArr['chrtype']=trim($data['chrtype']);
        if(isset($data['sn'])) $tmpArr['sn']=trim($data['sn']);
        if(isset($data['is_must'])) $tmpArr['is_must']=trim($data['is_must']);

        if($data['action'] == 'add'){
            $current_chrtype_num = 0;
            if((int)$tmpArr['chrtype'] >8){
                $current_chrtype_num = db('signup_template_sub')->where('pid=:pid and chrtype=:chrtype',['pid'=>$tmpArr['pid'],'chrtype'=>$tmpArr['chrtype']])->count();
            }
            if($current_chrtype_num > 0){
                return false;
            }
            $tmpArr['dtcreatetime']=date("Y-m-d H:i:s");
            $tmpArr['idsite']=session('idsite');
            $bool = db('signup_template_sub')->insert($tmpArr);
        }else{

            $current_chrtype_num = 0;
            if((int)$tmpArr['chrtype'] >8){
                $current_chrtype_num = db('signup_template_sub')->where('pid=:pid and chrtype=:chrtype and id<>:id',['pid'=>$tmpArr['pid'],'chrtype'=>$tmpArr['chrtype'],"id"=>$data["id"]])->count();
            }

            if($current_chrtype_num > 0){
                return false;
            }
            $bool = db('signup_template_sub')->where('id=:id and idsite=:idsite',['id'=>$data['id'],'idsite'=>session('idsite')])->update($tmpArr);
        }
        return $bool;
    }

    //删除
    public function delsub($data){
        if(isset($data['id'])==false)
            return false;

        if(strstr($data['id'],','))
            $bool = db('signup_template_sub')->where('idsite=:idsite',['idsite'=>session('idsite')])->where('id','in',explode(',',$data['id']))->delete();
        else
            $bool = db('signup_template_sub')->where('id=:id and idsite=:idsite',['id'=>$data['id'],'idsite'=>session('idsite')])->delete();

        return $bool;
    }

    //子表单列表
    public function indexsub1($request){

        $Search_str="idsite=".session('idsite')." and pid=".$request['pid'];
        $count = db('signup_template_sub')->where($Search_str)->count();
        $page = new Page($count,PAGE_SIZE);
        $data = db('signup_template_sub1')->where($Search_str)->order('sn asc,id asc')->limit($page->firstRow.','.$page->pageSize)->select();

        $arr = array();
        $arr['search'] = array();
        $arr['pager'] = $page;
        $arr['data'] = $data;
        return $arr;
    }
    //子表单增改查页面处理
    public function dealsub1($data){
        if(array_key_exists('id',$data)){
            $result = db('signup_template_sub1')->where('id=:id and idsite=:idsite',['id'=>$data['id'],'idsite'=>session('idsite')])->find();
        }else{
            $result = [
                'id'      =>  '',
                'pid'      =>  '',
                'title'      =>  '',
                'remark'      =>  '',
                'chrvalue'    => '',
                'chrtype'      =>  '',
                'sn'      =>  '',
                'dtcreatetime'      =>  '',
                'is_must' => 0
            ];
        }
        $result['action'] = $data['action'];
        return $result;
    }
    //子表单添加，修改提交地址
    public function PostDatasub1($data){
        $tmpArr=array();
        if(isset($data['pid'])) $tmpArr['pid']=trim($data['pid']);
        if(isset($data['title'])) $tmpArr['title']=trim($data['title']);
        if(isset($data['remark'])) $tmpArr['remark']=trim($data['remark']);
        if(isset($data['chrvalue'])) $tmpArr['chrvalue']= str_replace("，",",", trim($data['chrvalue']));
        if(isset($data['chrtype'])) $tmpArr['chrtype']=trim($data['chrtype']);
        if(isset($data['sn'])) $tmpArr['sn']=trim($data['sn']);
        if(isset($data['is_must'])) $tmpArr['is_must']=trim($data['is_must']);


        if($data['action'] == 'add'){
            $tmpArr['dtcreatetime']=date("Y-m-d H:i:s");
            $tmpArr['idsite']=session('idsite');
            $bool = db('signup_template_sub1')->insert($tmpArr);
        }else{
            $bool = db('signup_template_sub1')->where('id=:id and idsite=:idsite',['id'=>$data['id'],'idsite'=>session('idsite')])->update($tmpArr);
        }
        return $bool;
    }

    //子表单删除
    public function delsub1($data){
        if(isset($data['id'])==false)
            return false;

        if(strstr($data['id'],','))
            $bool = db('signup_template_sub1')->where('idsite=:idsite',['idsite'=>session('idsite')])->where('id','in',explode(',',$data['id']))->delete();
        else
            $bool = db('signup_template_sub1')->where('id=:id and idsite=:idsite',['id'=>$data['id'],'idsite'=>session('idsite')])->delete();

        return $bool;
    }

}