<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/2
 * Time: 11:11
 */

namespace app\admin\module;
use think\Model;
use think\Page;


class Content extends Model{

    public function index($data){
        $node_info = db('node')->where('nodeid='.$data['nodeid'])->find();//拿到栏目的信息
        $model_id=intval($node_info['idmodel']); //模型id
        $where='idmodel='.$model_id." and isusing=1";
        $ModelField =  db('modelfield')->where($where)->order('idorder asc,idtypefield asc')->select(); //找出对应的模型
        //便利模型字段，查找哪些字段作为查询条件
        $map['nodeid'] = ['eq',$data['nodeid']];
        $map1 = [];
        foreach ($ModelField as $key=>$value){
            if($value['issearch'] == 1){
                if(array_key_exists($value['fieldname'],$data) && !empty($data[$value['fieldname']])){
                    if(is_string($data[$value['fieldname']])) { //判断是否为字符串类型，
                        $map[$value['fieldname']] = ['like','%'.$data[$value['fieldname']].'%'];
                    }else{
                        $map[$value['fieldname']] = ['eq',$data[$value['fieldname']]];
                    }
                    $map1[$value['fieldname']] = $data[$value['fieldname']];
                }else{
                    $map1[$value['fieldname']] = '';
                }
            }
        }

        $count = db('content')->where($map)->count();// 查询满足要求的总记录数
        $Page = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $list = db('content')->where($map)->order('idorder asc,contentid desc')->limit($Page->firstRow.','.$Page->pageSize)->select();
        $result['list'] = $list;
        $result['search'] = $map1;
        $result['modelfield'] = $ModelField;
        $result['page'] = $show;
        return  $result;

    }

    //内容添加，编辑跳转页面
    public function content_deal($data){
        $nodeid = $data['nodeid'];
        if($data['action'] == "add"){
            if($nodeid){
                $node_info = db('node')->where('nodeid='.$nodeid)->find();//拿到指定栏目信息
                $model_id=intval($node_info['idmodel']);//模型id
                $where='idmodel='.$model_id." and isusing=1";

            }
            $contentinfo = '';
            $data['type'] = 1;
            $contentid = 0;
        }

        if($data['action'] == "edit" || $data['action'] == "view"){
            $contentid = $data['contentid'];
            $contentinfo = db('content')->where(array('contentid'=>$contentid))->find();//拿到一条内容的信息

            $node_info = db('node')->where('nodeid='.$contentinfo['nodeid'])->find();//拿到指定栏目信息
            $model_id=intval($node_info['idmodel']);//拿到modelid
            $where='idmodel='.$model_id." and isusing=1";//是否显示列表添加页

            foreach ($contentinfo as $key=>$value){
                if(strstr($value,'|')){
                    $contentinfo[$key] =  explode("|",$value);
                }else{
                    $contentinfo[$key] =$value;
                }
            }
            $data['type'] = 2;
        }
        //拿到所有栏目信息
        $nodename = $this->node_list();
        $FieldList =  db('modelfield')->where($where)->order('idorder asc,idtypefield asc')->select();
        $result['contentinfo'] = $contentinfo;
        $result['fieldlist'] = $FieldList;
        $result['contentid'] = $contentid;
        $result['nodeid'] = $nodeid;
        $result['nodename'] = $nodename;
        $result['modelid'] = $model_id;
        $result['action'] = $data['action'];
        $result['type'] = $data['type'];
        return $result;
    }

    //内容删除
    public function content_del($data){
        if(isset($data)){
            if(strstr($data,',')){
                $arr = explode(',',$data);
                $bool = db('content')->where('contentid','in',$arr)->delete();
            }else{
                $bool = db('content')->where('contentid='.$data)->delete();
            }
        }else{
            $bool = false;
        }

        return $bool;
    }


    //内容添加，修改提交地址
    public function content_post($data){
        $va = [];
        //检测提交的数据是否有敏感词
        foreach ($data as $k=>$v){
            if(is_string($v)){
                $data[$k] = field_filter($v);
            }
        }

        foreach ($data as $key=>$value){
            if(is_array($value) && !empty($value)){
                $va[$key] = implode('|',$value);
            }else{
                $va[$key] = $value;
            }
        }



        $va['userid'] = session('admin_id');
        $va['fielddate1'] = time();
        if ($va['action'] == 'add') {
            $bool = db('content')->strict(false)->insert($va);
        }
        if($va['action'] == 'edit'){
            $nodeinfo = db('node')->where(array('nodeid'=>$va['nodeid']))->find(); //找出对应的栏目信息
            $where = 'idmodel=' . $nodeinfo['idmodel']." and isusing=1";   //模型id
            $modelinfo = db('modelfield')->where($where)->order('idorder asc,idtypefield asc')->select();
            $vakeys = array_keys($va);
            foreach ($modelinfo as $key=>$value){
                for($i=0;$i<count($vakeys);$i++){
                    if(in_array($value['fieldname'],$vakeys)){
                    }else{
                        $va[$value['fieldname']]='';
                    }
                }
            }

            $bool = db('content')->where(array('contentid'=>$va['contentid']))->strict(false)->update($va);
        }
        return $bool;
    }

    /**
     * 获得指定分类下的子分类的数组
     * @access  public
     * @param   int     $no_node_id     要排除的栏目ID

     * @return  mix
     */
    public function node_list($no_node_id=0)
    {
        global $cms_node_g, $cms_node2_g;
        $sql = "SELECT * FROM  cms_node ORDER BY idorder ASC ";
        $cms_node_g = db('node')->query($sql);

        $cms_node_g = convert_arr_key($cms_node_g, 'nodeid');

        foreach ($cms_node_g AS $key => $value)
        {
            if($no_node_id==$value['nodeid'])
                continue;
            if($value['level'] == 0)
            {
                $this->get_cat_tree($no_node_id,$value['nodeid']);
            }
        }

        return $cms_node2_g;
        // $this->display();
    }

    /**
     * 获取指定id下的 所有分类
     * @global type $cms_node_g 所有商品分类
     * @param type $id 当前显示的 菜单id
     * @return 返回数组 Description
     */
    public function get_cat_tree($no_node_id,$id)
    {
        global $cms_node_g, $cms_node2_g;
        $cms_node2_g[$id] = $cms_node_g[$id];
        foreach ($cms_node_g AS $key => $value){
            if($no_node_id==$value['nodeid'])
                continue;
            if($value['parentid'] == $id)
            {
                $this->get_cat_tree($no_node_id,$value['nodeid']);
                $cms_node2_g[$id]['have_son'] = 1; // 还有下级
            }
        }
    }

}