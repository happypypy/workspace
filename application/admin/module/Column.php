<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/14
 * Time: 11:37
 */

namespace app\admin\module;
use think\Model;


class Column extends Model{

    //栏目列表
    public function index($nodetype){
        //获取所有栏目
        $idsite = session('idsite');
        $node = $this->node_list($nodetype,0,$idsite);
        $result['data'] = $node;
        $result['idsite'] = $idsite;
        return $result;

    }

//增改查页面处理
    public function deal($data){
        $result = [];
        $idsite = session('idsite');
        //$nodetype=$data['nodetype'];

        if(array_key_exists('id',$data)){
            $result['data'] = db('node')->where('(idsite='.$idsite.' or idsite=0) and nodeid='.$data['id'])->find();
        }else{
            $result['data'] = [
                'nodeid'       =>  '',
                'nodename'     =>  '',
                'nodetype'     =>1,
                'idmodel'      =>  '',
                'modelname'    =>  '',
                'parentid'     =>  '',
                'parentpath'   =>  '',
                'idroot'       =>  '',
                'level'        =>  '',
                'child'        =>  '',
                'arrchildId'   =>  '',
                'idorder'      =>  '',
                'nodedir'      =>  '',
                'parentdir'    =>  '',
                'tips'         =>  '',
                'remark'       =>  '',
                'nodepicurl'   =>  '',
                'metakeywords' =>  '',
                'metaremark'   =>  '',
                'showonmenu'   =>  1,
                'showonpath'   =>  1,
                'isonepage'    =>  2,
                'itempagesize' =>  '',
                'linkurl'      =>  '',
                'option'       =>  '',
                'templateofnodeindex'   =>  '',
                'templateofnodelist'    =>  '',
                'templateofnodecontent' =>  '',
                'commentmodel'  => '',
                'entrymodel'  => '',
                'idsite'       =>  session('idsite'),
            ];
        }
        //获得节点列表
        //$nodelist= $this->node_list($nodetype,0,session('idsite'));
        $nodelist= $this->node_list(0,0,session('idsite'));
        $modellist = db('model')->where('isusing=1 and (idsite='.session('idsite').' or idsite=0)')->select();
        $result['nodelist'] = $nodelist;
        $result['modellist'] = $modellist;
        $result['data']['action'] = $data['action'];
        return $result;
    }


    /**
     * 获得指定分类下的子分类的数组
     * @access  public
     * @param   int     $no_node_id     要排除的栏目ID

     * @return  mix
     */
    public function node_list($nodetype=0, $no_node_id=0,$idsite)
    {
        //global $cms_node_g, $cms_node2_g;
        //$sql = "SELECT * FROM  cms_node where nodetype=1 ORDER BY idorder ASC ";
        //$cms_node_g = db('node')->query($sql);
        //$cms_node_g = db('node')->where('nodetype ='.$nodetype.' and (idsite='.$idsite.' or idsite=0)' )->order('idorder asc')->select();
        $cms_node_g = db('node')->where('(idsite='.$idsite.' or idsite=0)' )->order('idorder asc')->select();
        $cms_node_g = convert_arr_key($cms_node_g, 'nodeid');
        $cms_node2_g=[];
        foreach ($cms_node_g AS $key => $value)
        {
            if($no_node_id==$value['nodeid'])
                continue;
            if($value['level'] == 0)
            {
                $cms_node2_g= $this->get_cat_tree($no_node_id,$value['nodeid'],$cms_node_g,$cms_node2_g );
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
    public function get_cat_tree($no_node_id,$id,$cms_node_g,$cms_node2_g)
    {
        //global $cms_node_g, $cms_node2_g;
        $cms_node2_g[$id] = $cms_node_g[$id];
        foreach ($cms_node_g AS $key => $value){
            if($no_node_id==$value['nodeid'])
                continue;
            if($value['parentid'] == $id)
            {
                $cms_node2_g=$this->get_cat_tree($no_node_id,$value['nodeid'],$cms_node_g,$cms_node2_g);
                $cms_node2_g[$id]['have_son'] = 1; // 还有下级
            }
        }
        return $cms_node2_g;
    }


    //添加，修改提交地址
    public function PostData($data){

       /* //判断必须填写的是否填写
        if(isset($data['idmodel']) && $data['idmodel'] == 0){
            return $bool = 0;
        }*/


        $tmpArr=array();
        if(isset($data['nodeid'])) $tmpArr['nodeid']=intval($data['nodeid']);
        if(isset($data['nodename'])) $tmpArr['nodename']=$data['nodename'];
//        if(isset($data['idmodel'])) $tmpArr['idmodel']=$data['idmodel'];
        //if(isset($data['modelname'])) $tmpArr['modelname']=$data['modelname'];
        if(isset($data['parentid'])) $tmpArr['parentid']=intval($data['parentid']);
        if(isset($data['parentpath'])) $tmpArr['parentpath']=$data['parentpath'];
        //if(isset($data['idroot'])) $tmpArr['idroot']=$data['idroot'];
        //if(isset($data['level'])) $tmpArr['level']=$data['level'];
        //if(isset($data['child'])) $tmpArr['child']=$data['child'];
        //if(isset($data['arrchildId'])) $tmpArr['arrchildId']=$data['arrchildId'];
        if(isset($data['idorder'])) $tmpArr['idorder']=intval($data['idorder']);
        if(isset($data['nodedir'])) $tmpArr['nodedir']=$data['nodedir']; //栏目的目录名
        //if(isset($data['parentdir'])) $tmpArr['parentdir']=$data['parentdir'];
        if(isset($data['tips'])) $tmpArr['tips']=$data['tips'];
        if(isset($data['remark'])) $tmpArr['remark']=$data['remark'];
        if(isset($data['nodepicurl'])) $tmpArr['nodepicurl']=$data['nodepicurl'];
        if(isset($data['metakeywords'])) $tmpArr['metakeywords']=$data['metakeywords'];
        if(isset($data['metaremark'])) $tmpArr['metaremark']=$data['metaremark'];
        if(isset($data['showonmenu'])) $tmpArr['showonmenu']=intval($data['showonmenu']);
        if(isset($data['showonpath'])) $tmpArr['showonpath']=intval($data['showonpath']);
        if(isset($data['itempagesize'])) $tmpArr['itempagesize']=intval($data['itempagesize']);
        if(isset($data['linkurl'])) $tmpArr['linkurl']=$data['linkurl'];
        if(isset($data['Option1'])) $tmpArr['option']=$data['Option1'];
        if(isset($data['templateofnodeindex'])) $tmpArr['templateofnodeindex']=$data['templateofnodeindex'];
        if(isset($data['templateofnodelist'])) $tmpArr['templateofnodelist']=$data['templateofnodelist'];
        if(isset($data['templateofnodecontent'])) $tmpArr['templateofnodecontent']=$data['templateofnodecontent'];
        if(isset($data['idsite'])) $tmpArr['idsite']=$data['idsite'];
        if(isset($data['isonepage'])) $tmpArr['isonepage']=$data['isonepage'];
        if(isset($data['commentmodel'])) $tmpArr['commentmodel']=$data['commentmodel'];
        if(isset($data['entrymodel'])) $tmpArr['entrymodel']=$data['entrymodel'];
        if(isset($data['nodetype'])) $tmpArr['nodetype']=$data['nodetype'];

        if(isset($tmpArr['option'])){
            $count = count($tmpArr);
            if($count > 1){
                $str = implode(',',$tmpArr['option']);
                $tmpArr['option'] = $str;
            }else{
                $tmpArr['option'] =$tmpArr['option'][0];
            }
        }
        //如果1是单页面
        if($tmpArr['isonepage'] == 1){
            $tmpArr['idmodel']= 45;
            $tmpArr['modelname']= '单页图文模版';
            //不是单页面
        }elseif ($tmpArr['isonepage'] == 2){
            $tmpArr['idmodel']= 35;
            $tmpArr['modelname']= '文章模型';
        }
//        if($tmpArr['idmodel'] != 0){
//            $arr=explode(':',$tmpArr['idmodel']);
//            $tmpArr['idmodel']=intval($arr[0]);
//            $tmpArr['modelname']=$arr[1];
//        }
        if(!$tmpArr['showonmenu']) $tmpArr['showonmenu']=0;
        if(!$tmpArr['showonpath']) $tmpArr['showonpath']=0;
        //echo $pid.'====';
        $arr=$this->GetParentPath($tmpArr['parentid']);

        $tmpArr['parentpath']=$arr[0]; //栏目路径0，913
        $tmpArr['parentdir']=$arr[1];   //栏目路径/
        $tmpArr['level']=count(explode(',',$arr[0]))-2;
        $tmpArr['idroot']=intval(explode(',',$arr[0])[1]);

        if($data['action'] == 'add'){
            $tmpArr['child']=0;
            $tmpArr['arrchildid']="";
            $r = db('node')->insertGetId($tmpArr);
            $this->UpdateParent(intval($data['parentid']),intval($r)); //更新上级的子目数和子栏目
        }else{
            if(intval($data['oldparentid'])!=intval($tmpArr['parentid'])) {
                $node_info = db('node')->where('nodeid='.$tmpArr['nodeid'])->find();//新的节点信息
                $arrChildId=$tmpArr['nodeid'].','.$node_info['arrchildid']; //新的栏目id 和新的栏目所有子栏目集合
                $this->UpdateParent1(intval($tmpArr['parentid']),intval($data['oldparentid']),$arrChildId);
            }
            $r = db('node')->where('nodeid=:id',['id'=>$data['id']])->update($tmpArr);
        }
        return $r;
    }



    //节点删除，同时删除该节点下的内容，如果有子节点则不能删除
    public function column_del($data){

        $del_id=$data['id'];
        $node_info = db('node')->where('nodeid='.$del_id)->find();
        if(intval($node_info['child'])>0) {
            return $bool = 3;
        }
        $icount=db("content")->where(array("nodeid"=>$del_id))->count();
        if($icount>0)
        {
            return $bool = 4;
        }

        $arrChildId=$del_id.','.$node_info['arrchildid'];
        $this->UpdateParent1(0,intval($node_info['parentid']),$arrChildId);
        $bool = db('node')->where('nodeid='.$del_id)->delete();
        return $bool;
    }


    //取得栏目路径
    public function GetParentPath($pid=0)
    {
        $strPath='';
        $strPath1='';
        if($pid==0) return array('0,','/');
        while($pid>0)
        {
            $model_info = array();
            $node_info = db('node')->where('nodeid='.$pid)->find();
            $strPath=$strPath."/".$node_info['nodedir'];
            $strPath1=','.$pid.$strPath1;
            $pid=intval($node_info['parentid']);
        }
        $strPath1='0'.$strPath1.',';

        return array($strPath1,$strPath);
    }

    //添加节点，理新上级的节点数和子节点
    public function UpdateParent($pid=0,$id=0)
    {
        if($pid==0) return;
        while($pid!=0)
        {
            $model_info = array();
            $node_info = db('node')->where('nodeid='.$pid)->find();
            $node_info['child']=intval($node_info['child'])+1;
            $node_info['arrchildid']=trim($node_info['arrchildid'].','.$id,',');
            db('node')->where('nodeid='.$node_info['nodeid'])->update($node_info);
            $pid=intval($node_info['parentid']);
        }
    }

    /**
     * 编辑节点，理新上级的节点数和子节点
     * $pid  新的父id
     * $oldpid 旧的父id
     * $arrID 新的栏目id 和新的栏目所有子栏目集合
     *
     */
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





}