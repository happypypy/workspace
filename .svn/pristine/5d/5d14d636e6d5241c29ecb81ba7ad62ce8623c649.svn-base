<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/31
 * Time: 16:43
 */

namespace app\admin\module;
use think\Model;
use think\Page;

class Pattern extends Model{

    public function index(){
        $idsite = session('idsite');
        if(empty($idsite)){
            $idsite = 0;
        }

        $count = db('model')->where('idsite='.$idsite.' or idsite=0')->count();
        $page = new Page($count,PAGE_SIZE);
        $model_list = db('model')->where('idsite='.$idsite.' or idsite=0')->limit($page->firstRow.','.$page->pageSize)->select();
        $arr = array();
        $arr['pager'] = $page;
        $arr['data'] = $model_list;

        return $arr;
    }
    
    //模型添加，修改跳转
    public function model_deal($data){

        if($data['action'] == 'edit'){
            $model_info = db('model')->where('idmodel',$data['idmodel'])->find();
        }
        if($data['action'] == 'add'){
            $model_info = [
                'modelname'     => '',
                'modeltype'     => 1,
                'remark'        => '',
                'isusing'       => 2,
                'ispublic'      => 2,
                'idsite'        => session('idsite')
            ];
        }
        $model_info['action'] = $data['action'];
        return $model_info;
    }
    
    //模型删除
    public function model_del($data){
        //删除模型表
        $bool1 = db('model')->where('idmodel = :idmodel',['idmodel'=>$data['idmodel']])->delete();
        //删除模型字段表
        $bool2 = db('modelfield')->where('idmodel=' . $data['idmodel'])->delete();
        $result['bool1'] = $bool1;
        $result['bool2'] = $bool2;
        return $result;
    }

    //模型选中删除
    public function del_checked($data){
        $bool1 = false;
        $bool2 = false;
        if(strstr($data['id'],',')){
            $id = explode(',',$data['id']);
            for ($i=0;$i<count($id);$i++){
                $bool1 = db('model')->where('idmodel',$id[$i])->delete();
                $bool2 = db('modelfield')->where('idmodel',$id[$i])->delete();
            }
        }else{
            $bool1 = db('model')->where('idmodel',$data['id'])->delete();
            $bool2 = db('modelfield')->where('idmodel='.$data['id'])->delete();
        }
        $result['bool1'] = $bool1;
        $result['bool2'] = $bool2;
        return $result;
    }
    
    //模型添加，修改提交地址
    public function model_post($data){

        $arr = [
            'modelname'     => $data['modelname'],
            'modeltype'     => $data['modeltype'],
            'remark'        => $data['remark'],
            'isusing'       => $data['isusing'],
            'ispublic'      => $data['ispublic'],
            'idsite'        => session('idsite')
        ];
        $bool = false;
        if($data['action'] == 'add'){
            $bool = db('model')->insertGetId($arr); //返回添加后的id

            $this->CopyModelField($_POST['modeltype'],$bool);

        }
        if($data['action'] == 'edit'){
            $bool = db('model')->where('idmodel = :idmodel',['idmodel'=>$data['idmodel']])->update($arr);
        }
        return $bool;
    }

    //$TypeID 模型类型 $ModelID 模型id
    private function CopyModelField($TypeID = 0, $ModelID = 0)
    {
        $list = db('typefield')->where('modeltype='.$TypeID)->order('typefieldid asc')->select();
        foreach ($list as $row) {
            $data['idmodel'] = $ModelID;
            $data['fieldtype'] = $row['fieldtype'];
            $data['fieldname'] = $row['fieldname'];
            $data['fieldalias'] = $row['fieldalias'];
            $data['tips'] = $row['tips'];
            $data['remark'] = $row['description'];
            $data['defaultvalue'] = $row['defaultvalue'];
            $data['settings'] = $row['settings'];
            $data['enablenull'] = $row['enablenull'];
            $data['issearch'] = $row['issearch'];
            $data['isusing'] = $row['isusing'];
            $data['idorder'] = $row['idorder'];
            $data['isonly'] = $row['isonly'];
            $data['isdisplayonlist'] = $row['isdisplayonlist'];
            $data['txtwidth'] = $row['txtwidth'];
            $data['txtheight'] = $row['txtheight'];
            //$data['txttype'] = $row['txttype'];
            $data['maxlength'] = $row['maxlength'];
            $r = db('modelfield')->insert($data);
        }
    }

    //模型字段列表,启用列表
    public function field_info($data){
        $field_list = db('modelfield')->where('idmodel',$data['idmodel'])->where('isusing',1)->order('idorder asc')->select();
        return $field_list;
    }
    
    //模型字段列表,全部列表
    public function field_all($data){
        $field_list = db('modelfield')->where('idmodel',$data['idmodel'])->order('idorder asc')->select();
        return $field_list;
    }

    //模型字段修改跳转
    public function field_deal($data){
       // if(array_key_exists('idsite',$data) == false || $data['idsite'] == 2){
        //    return $bool = 3;
       // }

        $TypeField_ID = $data['idfield']; //字段ID
        if ($TypeField_ID) {
            $field_info = db('modelfield')->where(array('idtypefield'=>$TypeField_ID))->find();
            $FieldType = $field_info['fieldtype']; //字段类型
            $Settings = $field_info['settings'];   //字段设置
            $childSetting = $field_info['childsetting']; // 三级联动

            if(strstr($childSetting,'∮')){
                $arr2 = explode('∮', $childSetting);
                $field_info["childsetting"] = $arr2;
            }
            $field_info["txtcjzc"]="";
            if($FieldType==23)
            {
                $field_info["txtcjzc"]=$Settings;
                $Settings="";
            }


            if(empty($field_info['txttype'])){
                $field_info['txttype'] = 1;
            }
            $arrTmp = array();
            $arrTmp[0] = '';
            $arrTmp[1] = '';
            $arrTmp[2] = '';
            $arrTmp[3] = '';
            $arrTmp[4] = '';
            $arrTmp[5] = '';
            $arrTmp[6] = '';
            $arrTmp[7] = '';
            if(!empty($Settings)){          //1∮男|1,女|2☆☆☆☆☆
                if(strstr($Settings,'∮')){
                    $arr = explode('∮', $Settings);
                    $arrTmp[0] = $arr[0]; // 1手动设置 2数据库表中获取 3从数据字典中
                    $arr1 = explode('☆', $arr[1]);
                    $arrTmp[1] = $arr1[0]; //1∮男|1,女|2
                    $arrTmp[2] = $arr1[1]; //
                    if (in_array($FieldType, array(4,5,6,7,20))) {  //多选，复选，单选，下拉
                        $arrTmp[3] = $arr1[2];
                        $arrTmp[4] = $arr1[3];
                        $arrTmp[5] = $arr1[4];
                        $arrTmp[6] = $arr1[5];
                        $arrTmp[7] = empty($arr1[6])?"":$arr1[6] ;
                        $field_info["settings"] = $arrTmp;
                    }
                }
            }
            $field_info["settings"] = $arrTmp;
            return $field_info;
        }
    }

    //模型字段修改地址
    public function field_post($data){
       // if(array_key_exists('idsite',$data) == false || $data['idsite'] == 2){
       //     return $bool = 3;
       // }

        $FieldType = $data['fieldtype']; //字段类型
        //setting字段的设置
        if ($FieldType == 1) {
            //验证规则                  正则或函数名                     错误信息提示
            $data["settings"] = $data['dropRegPattern'] . '∮' . $data['txtRegPattern'] . '☆' . $data['txtErrorMessage'];
        } elseif (array_key_exists("fieldtype",$data) && in_array($data['fieldtype'], array(4, 5, 6, 7,20))) {
            if(array_key_exists("GetType",$data)){
                if($data['GetType'] == 1){
                    //                  手动设置，数据库             手动设置格式
                    $data["settings"] = $data['GetType'] . '∮' . $data['ModelName'] . '☆☆☆☆☆☆';
                }elseif ($data['GetType'] == 2){
                    //                                                数据表名                        条件表达式                        获取条数                      文本字段                       字段值
                    $data["settings"] = $data['GetType'] . '∮☆' . $data['txtTableName'] . '☆' . $data['txtExpression'] . '☆' . $data['txtGetNum'] . '☆' . $data['txtTextColumnName'] . '☆' . $data['txtValueColumnName']. '☆';
                }elseif ($data['GetType'] == 3){  //从数据字典中读取
                    $data["settings"] = '3∮☆☆☆☆☆☆'.$data['selDictionary'];
                } else{
                    $data["settings"] = '0∮☆☆☆☆☆☆';
                }
            }else{
                $data["settings"] = '0∮☆☆☆☆☆☆';
            }
        }elseif($FieldType==23)
        {
            $data["settings"] =str_replace("，",",", $data['txtcjzc']) ;
        }
        else{
            $data["settings"] = '0∮☆☆☆☆☆☆';
        }

        //childsetting 字段的设置  省市区的三级联动
        if(array_key_exists("fieldtype",$data) && $data['fieldtype'] == 7){
            $data["childsetting"] = $data['child'].'∮'.$data['childname'].'∮'.$data['childurl'].'∮'.$data['func'];
        }else{
             $data["childsetting"] = null;
         }

        if(key_exists('EnableNull',$data) == false){
            $data['EnableNull'] = '';
        }
        if(key_exists('isusing',$data) == false){
            $data['isusing'] = '';
        }
        if(key_exists('IsSearch',$data) == false){
            $data['IsSearch'] = '';
        }
        if(key_exists('IsDisplayOnList',$data) == false){
            $data['IsDisplayOnList'] = '';
        }
        if(key_exists('isOnly',$data) == false){
            $data['isOnly'] = '';
        }

        $arr = [
            'idmodel'       =>  $data['idmodel'],
            'fieldtype'     =>  $data['fieldtype'],
            'fieldname'     =>  $data['FieldName'],
            'fieldalias'    =>  $data['FieldAlias'],
            'tips'          =>  $data['Tips'],
            'remark'        =>  $data['Description'],
            'defaultvalue'  =>  $data['defaultval'],
            'settings'      =>  $data['settings'],
            'enablenull'    =>  $data['EnableNull'],
            'isusing'       =>  $data['isusing'],
            'idorder'       =>  $data['idorder'],
            'issearch'      =>  $data['IsSearch'],
            'isdisplayonlist' =>  $data['IsDisplayOnList'],
            'isonly'        =>  $data['isOnly'],
            'txtwidth'      =>  $data['txtwidth'],
            'txtheight'     =>  $data['txtheight'],
            'txttype'       =>  $data['txttype'],
            'maxlength'     =>  $data['maxlength'],
            'childsetting'  =>  $data['childsetting']
        ];
        if ($data['action'] == 'edit') {
            $bool = db('modelfield')->where('idtypefield = :idtypefield',['idtypefield'=>$data['idfield']])->update($arr);
        }
        return $bool;
    }
}