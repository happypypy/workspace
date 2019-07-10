<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/31
 * Time: 16:39
 */

namespace app\admin\controller;
use think\Request;

class Pattern extends Base {

    public function index(){
        if($this->CMS->CheckPurview('patternmanage','view')==false){
            $this->NoPurview();
        }
        $pattern_obj = new \app\admin\module\Pattern();

        $arr = $pattern_obj->index();
        $page = $arr['pager'];
        $model_list = $arr['data'];
        $this->assign('page',$page);
        $this->assign('modellist',$model_list);
        return $this->fetch();
    }

    //模型添加，编辑跳转
    public function patterndeal(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('patternmanage',$request['action'])==false){
            $this->NoPurview();
        }
        $obj = new \app\admin\module\Pattern();
        $pattern_info = $obj->model_deal($request);
        $this->assign('patterninfo',$pattern_info);
        return $this->fetch();
    }

    //模型删除
    public function modeldel(){
        if($this->CMS->CheckPurview('patternmanage','del')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        //判断是否为公有

        $pattern_obj = new \app\admin\module\Pattern();
        $result = $pattern_obj->model_del($request);
        $bool1 = $result['bool1'];
        $bool2 = $result['bool2'];
        if($bool1 && $bool2){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    //模型选中删除
    public function delchecked(){
        if($this->CMS->CheckPurview('patternmanage','del')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $pattern_obj = new \app\admin\module\Pattern();
        $result = $pattern_obj->del_checked($request);
        $bool1 = $result['bool1'];
        $bool2 = $result['bool2'];
        if($bool1 && $bool2){
            return 1;
        }else{
            return 0;
        }
    }

    //模型添加，编辑提交地址
    public function modelpost(){
        $request = Request::instance()->param();

        if(empty($request['modelname'])) {
            return $this->error('前面带*号的为必填项');
        }
        if($this->CMS->CheckPurview('patternmanage',$request['action'])==false){
            $this->NoPurview();
        }
        if(empty($request['modelname'])){
            $this->error('名称不能为空');
        }
        $pattern_obj = new \app\admin\module\Pattern();
        $bool = $pattern_obj->model_post($request);
        if($bool !== false){
            $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
        }else{
            $this->error('操作失败');
        }
    }

    //
    public function getFieldType($Type_id = 0)
    {
        $fieldTypeArray = array(
            "未知类型", "单行文本", "多行文本",
            "编辑器", "多选列表框", "复选框",
            "单选按钮", "单选下拉列表框", "数字型",
            "日期类型", "图片", "多图片",
            "文件", "多文件", "模版",
            "相关内容", "相关栏目", "相关栏目(多选)", "只读文本", "弹窗分页(单选)", "弹窗分页(多选)", "相关产品");
        //if ($Type_id < 0 || $Type_id > 21)
        //{
        //    fieldType = 0;
        //}
        //return fieldTypeArray[fieldType];
        return $fieldTypeArray;
    }

    public function updateorderid()
    {
        $upData = I('post.');

        foreach ($upData as $key => $value) {
            if (strpos($key, '#')) {
                $TmpArr = explode('#', $key);
                M('cms_modelfield')->where('TypeField_ID =' . $TmpArr[1])->save(array('OrderID' => $value));

            }
        }
        $this->success("操作成功", U('Admin/CMSModel/listfield/model_id/' . $upData['model_id']));
    }

    //模型字段列表,启用列表
    public function modelfieldinfo(){
        if($this->CMS->CheckPurview('modelfield','view')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();


        $pattern_obj = new \app\admin\module\Pattern();
        $field_list = $pattern_obj->field_info($request);
        $this->assign('request',$request);
        $this->assign('idsite',session('idsite'));
        $this->assign('fieldlist',$field_list);
        return $this->fetch();
    }

    //所有字段列表
    public function modelfieldall(){
        if($this->CMS->CheckPurview('modelfield','view')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $pattern_obj = new \app\admin\module\Pattern();
        $field_list = $pattern_obj->field_all($request);
        $this->assign('request',$request);
        $this->assign('idsite',session('idsite'));
        $this->assign('fieldlist',$field_list);
        return $this->fetch();
    }


    //模型字段修改跳转
    public function fielddeal(){
        if($this->CMS->CheckPurview('modelfield','edit')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $pattern_obj = new \app\admin\module\Pattern();
        $field_info = $pattern_obj->field_deal($request);
        if ($field_info == 3){
           // $this->error('无权限修改');
        }

        $Dic=db('work_book')->where('isshow=1')->order('order asc')->select();

        $this->assign('request',$request);
        $this->assign('dic',$Dic);
        $field_type = [1,2,3,8,9,10,11,12,13,14,15,16,17,18];
        $this->assign('fieldtype',$field_type);
        $this->assign('fieldinfo',$field_info);
        return $this->fetch();
    }


    //修改字段提交地址
    public function fieldpost(){
        if($this->CMS->CheckPurview('modelfield','edit')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $pattern_obj = new \app\admin\module\Pattern();
        $bool = $pattern_obj->field_post($request);

        if($bool){
            if($bool == 3){
                $this->error('无权限修改');
            }else{
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }

        }else{
            $this->error('操作失败');
        }
    }


    //修改模型字段是否显示，为空，启用
    public function changeTableVal(){
        $request = Request::instance()->param();
        $table = $request['table']; // 表名

        //判断权限，因为changeTableVal太多地方用到，改起来麻烦，写了个兼容的判断
        switch ($table) {
            case "comment":
                if ($this->CMS->CheckPurview('commentmanage', 'edit', 'comment') == false) {
                    $this->NoPurview();
                }
                break;
            case "signup_template":
                if ($this->CMS->CheckPurview('signup', 'changetableVal', 'signup') == false) {
                    $this->NoPurview();
                }
                break;
            default:
                if ($this->CMS->CheckPurview('modelfield', 'edit') == false) {
                    $this->NoPurview();
                }
                break;
        }

        $id_name = $request['id_name']; // 表主键id名
        $id_value = $request['id_value']; // 表主键id值
        $field  = $request['field']; // 修改哪个字段
        $value  = $request['value']; // 修改字段值
        $bool = db($table)->where("$id_name = $id_value")->update(array($field=>$value)); // 根据条件保存修改的数据
        if($bool){
            return 1;
        }else{
            return 2;
        }
    }

    //修改排序
    public function fieldsort(){
        if($this->CMS->CheckPurview('modelfield','edit')==false){
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $id_name = $request['id_name'];// 表主键id值
        $id_value = $request['id_value']; //input 值
        $map['idtypefield'] = $id_name;
        $bool = db('modelfield')->where($map)->setField(array('idorder'=>$id_value));
        if($bool){
            return 1;
        }

    }
}