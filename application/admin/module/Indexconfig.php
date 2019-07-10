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
 * Date:2017-08-28 */
namespace app\admin\module;
use think\Model;
use think\Page;

class Indexconfig extends Model{

    //列表
    public function index($request){
        $Search_str='';
        $Search_arr=array();
        $search=array();

        $search['chrcode']='';
        if(isset($request['chrcode']) && $request['chrcode']!='')
        {
            $Search_str=$Search_str.' chrcode like :chrcode and';
            $Search_arr['chrcode']='%'.$request['chrcode'].'%';
            $search['chrcode']=$request['chrcode'];
        }
        $search['chrname']='';
        if(isset($request['chrname']) && $request['chrname']!='')
        {
            $Search_str=$Search_str.' chrname like :chrname and';
            $Search_arr['chrname']='%'.$request['chrname'].'%';
            $search['chrname']=$request['chrname'];
        }
        $Search_str=trim($Search_str,' and');

       if($Search_str=='')
          $count = db('index_config')->count();
        else
            $count = db('index_config')->where($Search_str,$Search_arr)->count();

        $page = new Page($count,PAGE_SIZE);
        if($Search_str=='')
            $data = db('index_config')->order('id desc')->limit($page->firstRow.','.$page->pageSize)->select();
        else
            $data = db('index_config')->where($Search_str,$Search_arr)->order('id desc')->limit($page->firstRow.','.$page->pageSize)->select();

        if($data)
        {
            foreach($data as $key=>$value)
            {
                $data[$key]['inttype']=$this->getInttype($value['inttype']);
                $data[$key]['intopentype']=$this->getIntopentype($value['intopentype']);
                $data[$key]['distype']=$this->getDistype($value['distype']);
            }
        }


        $arr = array();
        $arr['search'] = $search;
        $arr['pager'] = $page;
        $arr['data'] = $data;
        return $arr;
    }

    private function getInttype($index)
    {
        switch ($index) {
            case '1':
                return "列表";
            case '2':
                return "工作状态";
            case '3':
                return "简介";
            default:
                return "";
        }
    }
    private function getIntopentype($index)
    {
        switch ($index) {
            case '1':
                return "本窗体";
            case '2':
                return "新窗体";
            default:
                return "";
        }
    }
    private function getDistype($index)
    {
        switch ($index) {
            case '1':
                return "不显示";
            case '2':
                return "显示";
            case '3':
                return "固定";
            default:
                return "";
        }
    }


    //增改查页面处理
    public function deal($data){
        if(array_key_exists('id',$data)){
            $result = db('index_config')->where('id=:id',['id'=>$data['id']])->find();
        }else{
            $result = [
                                'id'      =>  '',
                                'chrcode'      =>  '',
                                'chrname'      =>  '',
                                'inttype'      =>  '',
                                'chrlink'      =>  '',
                                'intopentype'      =>  '',
                                'chrico'      =>  '',
                                'inttopn'      =>  '',
                                'chrsrc'      =>  '',
                                'distype'      =>  '',
                            ];
        }
        $result['action'] = $data['action'];
        return $result;
    }

    //添加，修改提交地址
    public function PostData($data){
        $tmpArr=array();
        if(isset($data['chrcode'])) $tmpArr['chrcode']=trim($data['chrcode']);
        if(isset($data['chrname'])) $tmpArr['chrname']=trim($data['chrname']);
        if(isset($data['inttype'])) $tmpArr['inttype']=trim($data['inttype']);
        if(isset($data['chrlink'])) $tmpArr['chrlink']=trim($data['chrlink']);
        if(isset($data['intopentype'])) $tmpArr['intopentype']=trim($data['intopentype']);
        if(isset($data['chrico'])) $tmpArr['chrico']=trim($data['chrico']);
        if(isset($data['inttopn'])) $tmpArr['inttopn']=trim($data['inttopn']);
        if(isset($data['chrsrc'])) $tmpArr['chrsrc']=trim($data['chrsrc']);
        if(isset($data['distype'])) $tmpArr['distype']=trim($data['distype']);

        if($data['action'] == 'add'){
            $bool = db('index_config')->insert($tmpArr);
        }else{
            $bool = db('index_config')->where('id=:id',['id'=>$data['id']])->update($tmpArr);
        }

        //给没有固定代号的用户，加上固定代码代号数据
        if(trim($data['distype'])=='3' && $bool)
        {
            $arrAccountID=array();
            $result=db('index_user_info')->where('chrcode=:chrcode',['chrcode'=>$data['chrcode']])->select();
            if($result) {
                foreach ($result as $value) {
                    $arrAccountID[] = $value['fidaccount'];
                }
                $result = db('account')->where('idaccount', 'not in', $arrAccountID)->select();
                if($result) {
                    $arr['Code'] = $data['chrcode'];
                    foreach ($result as $value) {
                        $objIndex = new \app\admin\module\Index();
                        $objIndex->addBox($arr, $value['idaccount']);
                    }
                }
            }
        }


        return $bool;
    }

    public function CheckCode($code,$id=0)
    {
        $result=db('index_config')->where('chrcode=:chrcode',['chrcode'=>$code])->select();
        if($result)
        {
            if( count($result)>1) {
                return true;
            }
            if($id==0) {
                if (count($result) > 0)
                    return true;
            }
            else
            {
                if (count($result) == 1 && $result[0]['id'] != $id)
                    return true;
            }
        }
        return false;
    }

    //角色删除
    public function del($data){
        if(isset($data['id'])==false)
            return false;

        if(strstr($data['id'],','))
            $bool = db('index_config')->where('id','in',explode(',',$data['id']))->delete();
        else
            $bool = db('index_config')->where('id=:id',['id'=>$data['id']])->delete();

        return $bool;
    }

}