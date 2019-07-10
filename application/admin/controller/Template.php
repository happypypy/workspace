<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/2/27
 * Time: 15:36
 */

namespace app\admin\controller;


use think\Request;

class Template extends Basesite {

    //模版列表
    public function index(){
        $obj = new \app\admin\module\Template();
        $result = $obj->index();
        $page = $result['page'];
        $template_list = $result['data'];
        $seleTemplate= GetConfigVal('weboption','rootdir',$this->idsite);
        $this->assign('idsite',$this->idsite);
        $this->assign('page',$page);
        $this->assign('selcode',$seleTemplate);
        $this->assign('template_list',$template_list);
        return $this->fetch();
    }

    //添加模版
    public function tempdeal(){
        return $this->fetch();
    }

    //模版提交地址
    public function temppost(){
        $request = Request::instance()->param();

        if(empty($request['temname']) || empty($request['dirname'])) {
            return $this->error('前面带*号的为必填项');
        }
        $bool = db('template')->insert($request);
        if($bool){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    //模版启用
    public function changetemplate (){
        $request = Request::instance()->param();
        cache('config'.$this->idsite,null);
        $bool = db('config_rule')->where('menucode=\'weboption\' and fieldname=\'rootdir\' and idSite='.$this->idsite)->update(['defaultval'=>$request['code'],'en_defaultval'=>$request['code'],'tc_defaultval'=>$request['code']]);
        //db('template')->where('idtemplate!='.$request['id'])->update(['is_use'=>2]);
        if($bool !== false){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
}