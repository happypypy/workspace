<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/14
 * Time: 11:26
 */

namespace app\admin\controller;
use think\Request;
use think\Page;


class Column extends Base {

    //节点列表
    public function index(){
        if($this->CMS->CheckPurview('columnmanage','view')==false){
            $this->error('无权限');
        }
        $obj = new \app\admin\module\Column;
        //$request = Request::instance()->param();

        $result1 = $obj->index(1);
        $result2 = $obj->index(2);

        $this->assign('idsite',$result1['idsite']);
        $this->assign('node1',$result1['data']);  //所有内容栏目
        $this->assign('node2',$result2['data']);  //所有活动栏目
        return $this->fetch();
    }

    //节点添加，修改，查看跳转页面
    public function modi(){
        $data = Request::instance()->param();
        if($this->CMS->CheckPurview('columnmanage',$data['action'])==false){
            $this->NoPurview();
        }

        if (Request::instance()->isPost()) {

            if(empty($data['nodename']))
            {
                $this->error('栏目名称不能为空。');
            }
            elseif(mb_strlen($data['nodename'])>6)
            {
                $this->error('栏目名称长度不能大于6个字符。');
            }

            $obj = new \app\admin\module\Column();
            $bool = $obj->PostData($data);
            if($bool !== false){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error('操作失败');
            }
            exit();
        }

        $obj = new \app\admin\module\Column();
        $result = $obj->deal($data);
        $datainfo = $result['data'];
        $nodelist = $result['nodelist'];
        $modellist = $result['modellist'];
        $this->assign('nodelist',$nodelist);
        $this->assign('modellist',$modellist);
        $this->assign('datainfo',$datainfo);
        return $this->fetch();
    }

    //删除
    public function columndel()
    {
        if($this->CMS->CheckPurview('columnmanage','del')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Column();
        $bool = $obj->column_del($request);
        if($bool){
            if($bool == 3){
                $this->error('该栏目下有内容不能删除，请先删除内容');
            }
            else{

                $this->success('删除成功');
            }
        }else{
            $this->error('删除失败');
        }
    }

    //删除，理新上级的节点数和子节点
    public function UpdateParent1($pid=0,$oldpid=0,$arrID="")
    {
        $arrTmp=explode(',',trim($arrID,','));
        $childCount=count($arrTmp);
        //print_r($arrTmp);
        if($oldpid>0)
        {
            while($oldpid>0)
            {
                $model_info = array();
                $node_info = db('node')->where('nodeid='.$oldpid)->find();
                $node_info['child']=intval($node_info['child'])-$childCount;
                $arrChildId=','.$node_info['arrchildid'].',';
                for($i=0;$i<count($arrTmp);$i++)
                {
                    if($arrTmp[$i]!="")
                    {
                        $arrChildId=str_replace(','.$arrTmp[$i].',',',',$arrChildId);
                    }
                }
                $node_info['arrchildid']=trim($arrChildId,',');
                db('node')->where('nodeid='.$node_info['nodeid'])->update($node_info);
                $oldpid=intval($node_info['parentid']);
            }
        }
        if($pid>0)
        {
            while($pid>0)
            {
                $model_info = array();
                $node_info = db('node')->where('nodeid='.$pid)->find();
                $node_info['child']=intval($node_info['child'])+$childCount;
                $arrChildId=$node_info['arrchildid'];
                if($arrChildId=="")
                {$node_info['arrchildid']=trim($arrID,',') ;}
                else
                {$node_info['arrchildid']=trim($node_info['arrchildid'].','.$arrID,',') ;}
                db('node')->where('nodeid='.$node_info['nodeid'])->update($node_info);
                $pid=intval($node_info['parentid']);
            }
        }
    }

//    public function checkShowNum(){
//            $shownum=db('node')->where(['idsite'=>session('idsite'),'showonmenu'=>1])->count();
//            if($shownum >=10){
//                return json("操作失败，首页最多显示20个栏目，你已经超过，请修改后再操作！");
//            }
//    }
}