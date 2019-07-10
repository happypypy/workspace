<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/24
 * Time: 13:57
 */

namespace app\admin\module;
use think\Model;



class Index extends Model{

    public function column_index(){
        $column_index = db('catalog')->where('intflag = 1')->select();
       // $url = str_replace('index.php','',request()->instance()->baseFile()).'uploads/column/';
       // foreach ($column_index as $key=>$value){
       //     $column_index[$key]['chrimgpath'] = $url.$value['chrimgpath'];
       // }
        return $column_index;
    }

    public function column_index1(){
        $map['intflag'] = 2; //不是系统
        $column_index = db('catalog')->where($map)->select();

        return $column_index;
    }

    public function module_index(){
        $module_index = db('module')->select();
        foreach ($module_index as $key=>$value){
            $module_index[$key]['action'] = $value['chrcode'].'/'.$value['operation'];
        }
        return $module_index;
    }

    public function module_index1($data){
        if(array_key_exists('idsite',$data)){
            session('idsite',$data['idsite']);
        }else{
            $data['idsite'] = session('idsite');
        }
        $map['intflag'] = 2;
        $module_index = db('module')->where($map)->where('idsite','in',"0,{$data['idsite']}")->order('intsn asc')->select();

        foreach ($module_index as $key=>$value){
            $module_index[$key]['action'] = $value['chrcode'].'/'.$value['operation'];
        }

        $result['data'] = $module_index;
        $result['idsite'] = $data['idsite'];
        return $result;

    }

    public function setUserConfig($data)
    {
        $AccountID=session('AccountID');

        $data_arr = [
            'fidaccount'    =>  $AccountID,
            'intcolumn'      =>  $data['Column'],
            'intcolumn1width'    =>  $data['Column1Width'],
            'intcolumn2width'    =>  $data['Column2Width'],
            'intcolumn3width'    =>  $data['Column3Width']
        ];

        if(db('index_user_config')->where('fidaccount=:fidaccount',['fidaccount'=>$AccountID])->find()) {
            return db('index_user_config')->where('fidaccount=:fidaccount', ['fidaccount' => $AccountID])->update($data_arr);
        }
        else
        {  return db('index_user_config')->insert($data_arr);}
    }

    public function AddBox($data,$userID=0)
    {
        if($userID==0)
            $AccountID=session('AccountID');
        else
            $AccountID=$userID;

        $code=$data['Code'];
        $sn=1;
        $IndexInfo1=db('index_user_info')->where('fidaccount=:fidaccount and sellsnum=:sellsnum',['fidaccount'=>$AccountID,'sellsnum'=>1])->order('sn desc')->find();
        if($IndexInfo1)
        {
            $sn=$IndexInfo1['sn']+1;
        }
        $IndexInfo=db('index_config')->where('chrcode=:chrcode',['chrcode'=>$code])->find();
        if($IndexInfo)
        {
            $arrData=array();
            $arrData['chrcode']=$IndexInfo['chrcode'];
            $arrData['chrname']=$IndexInfo['chrname'];
            $arrData['inttype']=$IndexInfo['inttype'];
            $arrData['chrlink']=$IndexInfo['chrlink'];
            $arrData['intopentype']=$IndexInfo['intopentype'];
            $arrData['chrico']=$IndexInfo['chrico'];
            $arrData['inttopn']=$IndexInfo['inttopn'];
            $arrData['chrsrc']=$IndexInfo['chrsrc'];
            $arrData['distype']=$IndexInfo['distype'];
            $arrData['fidaccount']=$AccountID;
            $arrData['sellsnum']='1';
            $arrData['sn']=$sn;
            return db('index_user_info')->insert($arrData);
        }
        return false;
    }

    public function RemoveBox($data)
    {
        $AccountID=session('AccountID');

        $code=$data['Code'];

        if(db('index_config')->where('chrcode=:chrcode and distype=3',['chrcode'=>$code])->find()) {
            return false;
        }
        else {
            return  db('index_user_info')->where('fidaccount=:fidaccount and chrcode=:chrcode', ['fidaccount' => $AccountID, 'chrcode' => $code])->delete();
        }
    }
    public function SortOrder($data)
    {
        $AccountID=session('AccountID');

        $value=$data['value'];

        $arr=explode('|',$value);

        $data_arr= array();

        foreach ($arr as $key => $vo)
        {
            $tmpArr=explode(':',$vo);
            $data_arr['sn']=$key;
            $data_arr['sellsnum']=str_replace('cell','',$tmpArr[0]);
            db('index_user_info')->where('fidaccount=:fidaccount and chrcode=:chrcode', ['fidaccount' => $AccountID,'chrcode'=>$tmpArr[1]])->update($data_arr);
        }

    }

    public  function SetBoxConfig($data)
    {

        $AccountID=session('AccountID');
        $code=  $data['Code'];
        $data_arr = [
            'inttopn'    =>  $data['TopN'],
            'intopentype'    =>  $data['NewOpen'],
            'chrname'    =>  $data['customTitle'],
        ];
        return db('index_user_info')->where('fidaccount=:fidaccount and chrcode=:chrcode', ['fidaccount' => $AccountID,'chrcode'=>$code])->update($data_arr);
    }

    public function getUserIndexInfo()
    {
        $data= db('index_user_info')->where('fidaccount=:fidaccount',['fidaccount'=>session('AccountID')])->order('sn asc')->select();
        if($data)
            return $data;

        $data=db('index_config')->where('distype=3')->select();
        if($data) {
            $arr=array();
            foreach ($data as $value) {
                $arr['Code']=$value['chrcode'];
                $this->addBox($arr);
            }
        }
        return db('index_user_info')->where('fidaccount=:fidaccount',['fidaccount'=>session('AccountID')])->order('sn asc')->select();
    }
    public function getUserIndexConfig()
    {
        $data= db('index_user_config')->where('fidaccount=:fidaccount',['fidaccount'=>session('AccountID')])->find();
        if($data)
            return $data;

        $arr=array();
        $arr['fidaccount']=session('AccountID');
        $arr['intcolumn']='1';
        $arr['intcolumn1width']=100;
        $arr['intcolumn2width']=0;
        $arr['intcolumn3width']=0;
        db('index_user_config')->insert($arr);
        return db('index_user_config')->where('fidaccount=:fidaccount',['fidaccount'=>session('AccountID')])->find();;
    }
    public function getIndexConfig()
    {
        return db('index_config')->where('distype>1')->select();
    }
}