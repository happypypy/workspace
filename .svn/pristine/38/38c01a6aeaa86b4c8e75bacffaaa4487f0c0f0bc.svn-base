<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/7
 * Time: 9:40
 */

namespace app\admin\module;
use think\Model;
use think\Page;
use think\Request;

class Menu extends Model {

    //左边栏目的获取
    public function catalog_index(){
        $column_index = db('catalog')->select();
        return $column_index;
    }
    //左边模块的获取
    public function module_index(){
        $module_index = db('module')->select();
        return $module_index;
    }

    //栏目列表
    public function catalog_list(){
        $count = db('catalog')->count();
        $page = new Page($count,PAGE_SIZE);
        $catalog_list = db('catalog')->where('idcatalog > :idcatalog',['idcatalog'=>0])->order('intsn asc,idcatalog desc')->limit($page->firstRow.','.$page->pageSize)->select();
        $arr = array();
        $arr['pager'] = $page;
        $arr['data'] = $catalog_list;
        return $arr;
    }

    //模块列表
    public function module_list($data){

        if($data['columncode']){
            $count = db('module')->where('chrname LIKE :chrname and chrcode like :chrcode and codecatalog = :codecatalog',['chrname'=>'%'.$data['moduname'].'%','chrcode'=>'%'.$data['moducode'].'%','codecatalog'=>$data['columncode']])->count();
            $page = new Page($count,PAGE_SIZE);
            $module_list = db('module')->where('chrname LIKE :chrname and chrcode like :chrcode and codecatalog = :codecatalog',['chrname'=>'%'.$data['moduname'].'%','chrcode'=>'%'.$data['moducode'].'%','codecatalog'=>$data['columncode']])->order('codecatalog ,intsn asc,idmodule desc')->limit($page->firstRow.','.$page->pageSize)->select();
        }else{
            $count = db('module')->where('chrname LIKE :chrname and chrcode like :chrcode and codecatalog like :codecatalog',['chrname'=>'%'.$data['moduname'].'%','chrcode'=>'%'.$data['moducode'].'%','codecatalog'=>'%'.$data['columncode'].'%'])->count();
            $page = new Page($count,PAGE_SIZE);
            $module_list = db('module')->where('chrname LIKE :chrname and chrcode like :chrcode and codecatalog like :codecatalog',['chrname'=>'%'.$data['moduname'].'%','chrcode'=>'%'.$data['moducode'].'%','codecatalog'=>'%'.$data['columncode'].'%'])->order('codecatalog ,intsn asc,idmodule desc')->limit($page->firstRow.','.$page->pageSize)->select();
        }

        $arr = array();
        $arr['pager'] = $page;
        $arr['data'] = $module_list;
        return $arr;
    }

    //扩展模块列表
    public function extended_module_list($data){

        if($data['columncode']){
            $count = db('extended_module')->where('chrname LIKE :chrname and chrcode like :chrcode and codecatalog = :codecatalog',['chrname'=>'%'.$data['moduname'].'%','chrcode'=>'%'.$data['moducode'].'%','codecatalog'=>$data['columncode']])->count();
            $page = new Page($count,PAGE_SIZE);
            $module_list = db('extended_module')->where('chrname LIKE :chrname and chrcode like :chrcode and codecatalog = :codecatalog',['chrname'=>'%'.$data['moduname'].'%','chrcode'=>'%'.$data['moducode'].'%','codecatalog'=>$data['columncode']])->order('codecatalog ,intsn asc,idmodule desc')->limit($page->firstRow.','.$page->pageSize)->select();
        }else{
            $count = db('extended_module')->where('chrname LIKE :chrname and chrcode like :chrcode and codecatalog like :codecatalog',['chrname'=>'%'.$data['moduname'].'%','chrcode'=>'%'.$data['moducode'].'%','codecatalog'=>'%'.$data['columncode'].'%'])->count();
            $page = new Page($count,PAGE_SIZE);
            $module_list = db('extended_module')->where('chrname LIKE :chrname and chrcode like :chrcode and codecatalog like :codecatalog',['chrname'=>'%'.$data['moduname'].'%','chrcode'=>'%'.$data['moducode'].'%','codecatalog'=>'%'.$data['columncode'].'%'])->order('codecatalog ,intsn asc,idmodule desc')->limit($page->firstRow.','.$page->pageSize)->select();
        }

        $arr = array();
        $arr['pager'] = $page;
        $arr['data'] = $module_list;
        return $arr;
    }

    //查看扩展模块的企业列表
    public function view_site($data){
        $count = db('module')->alias('e')->join('site_manage s','s.id=e.idsite')->where(['e.chrcode'=>$data['chrcode']])->order('s.id asc')->count();
        $page = new Page($count,PAGE_SIZE);
        $list = db('module')->alias('e')->join('site_manage s','s.id=e.idsite')->field('s.id,s.site_code,s.site_name')
            ->where(['e.chrcode'=>$data['chrcode']])->order('s.id asc')->limit($page->firstRow.','.$page->pageSize)->select();
        $arr['pager'] = $page;
        $arr['data'] = $list;
        return $arr;
    }

    //资源列表
    public function resource_list($data){
        $count = db('resource')->where('modulecode = :modulecode',['modulecode'=>$data['moducode']])->order('idresource desc')->count();
        $page = new Page($count,PAGE_SIZE);
        $resource_list = db('resource')->where('modulecode = :modulecode',['modulecode'=>$data['moducode']])->order('idresource desc')->limit($page->firstRow.','.$page->pageSize)->select();
        $arr['pager'] = $page;
        $arr['data'] = $resource_list;
        return $arr;
    }

    //栏目，模块，资源，操作删除
    public function del($data){
        if(!isset($data["id"]) || (int)$data["id"] <=0){
            return false;
        }

        //区分是扩展模块还是标准模块
        if(array_key_exists('flag',$data)) {
            $table = $data['flag'] == 'module' ? 'module' : 'extended_module';
        }
        if(array_key_exists('columncode',$data)){  //栏目
            $bool = db('catalog')->where('chrcode = :chrcode and idcatalog=:idcatalog',['chrcode'=>$data['columncode'],'idcatalog'=>$data['id']])->delete();
        }elseif (array_key_exists('modulecode',$data)){ // 模块
            $bool = db("{$table}")->where('chrcode = :chrcode and idmodule=:idmodule',['chrcode'=>$data['modulecode'],'idmodule'=>$data['id']])->delete();
        }elseif(array_key_exists('resourcecode',$data)){ // 资源
            $bool = db('resource')->where('chrcode = :chrcode and idresource=:idresource',['chrcode'=>$data['resourcecode'],'idresource'=>$data['id']])->delete();
        }elseif (array_key_exists('operatecode',$data)){ // 操作
            $bool = db('operate')->where('chrcode = :chrcode and idoperate=:idoperate',['chrcode'=>$data['operatecode'],'idoperate'=>$data['id']])->delete();
        }elseif (array_key_exists('change',$data)){
            //模块移至另一个栏目下
            if(strstr($data['id'],',')){
                $module_id = explode(',',$data['id']);
                for ($i=0;$i<count($module_id);$i++){
                    $bool = db("{$table}")->where('idmodule = :idmodule',['idmodule'=>$module_id[$i]])->update(['codecatalog'=>$data['chrcolumncode']]);
                }
            }else{
                $bool = db("{$table}")->where('idmodule = :idmodule',['idmodule'=>intval($data['id'])])->update(['codecatalog'=>$data['chrcolumncode']]);
            }


        }
        return $bool;
    }

    /**
     * 设置启用禁用
     * @param $data
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function set_extended_module($data){
        //校验必填参数
        if(!isset($data['id_site']) || (int)$data['id_site'] <= 0 || (int)$data['id'] <= 0 || !isset($data['state'])){
            return ['success'=>false,'message'=>'缺少必要的参数'];
        }
        if(!in_array($data['state'],array('use','forbidden'))){
            return ['success'=>false,'message'=>'状态标识超出范围'];
        }
        //查询该扩展模块
        $extended_module = db('extended_module')->where(['idmodule'=>$data['id']])->find();
        //查询标准模块
        $module = db('module')->where(['chrcode'=>$extended_module['chrcode'],'idsite'=>$data['id_site']])->find();
//        var_dump($extended_module);exit;
        //判断是禁用还是启用
        if($data['state'] == 'use'){
            if($module){
                return ['success'=>false,'message'=>'该模块已经存在'];
            }
            $data_arr = [
                'codecatalog'   =>  $extended_module['codecatalog'],
                'chrname'       =>  $extended_module['chrname'],
                'chrcode'       =>  $extended_module['chrcode'],
                'intsn'         =>  $extended_module['intsn'],
                'operation'     =>  $extended_module['operation'],
                'intflag'       =>  $extended_module['intflag'],
                'textremark'    =>  $extended_module['textremark'],
                'chrimgpath'    =>  $extended_module['chrimgpath'],
                'idsite'        =>  $data['id_site']
            ];
            //将扩展模块新加到标准模块中
            $bool = db('module')->insert($data_arr);
            return ['success'=>$bool,'message'=>'启用成功'];
        }else{
            if(!$module){
                return ['success'=>false,'message'=>'禁用失败，该模块不存在标准模块中'];
            }
            //将扩展模块新加到标准模块中
            $bool = db('module')->where(['chrcode'=>$extended_module['chrcode'],'idsite'=>$data['id_site']])->delete();
            return ['success'=>$bool,'message'=>'禁用成功'];
        }
    }

    //选中删除
    public function del_checked($data){
            //区分是扩展模块还是标准模块
            if(array_key_exists('flag',$data)) {
                $table = $data['flag'] == 'module' ? 'module' : 'extended_module';
            }
            if(strstr($data['id'],',')){
                $id = explode(',',$data['id']);
                for ($i=0;$i<count($id);$i++){
                    if($data['type'] == 'column'){
                    $bool = db('catalog')->where('idcatalog',$id[$i])->delete();
                }elseif ($data['type'] == 'module'){
                    $bool = db("{$table}")->where('idmodule',$id[$i])->delete();
                }elseif ($data['type'] == 'resource'){
                    $bool = db('resource')->where('idresource',$id[$i])->delete();
                }else{
                    $bool = db('operate')->where('idoperate',$id[$i])->delete();
                }
            }
        }else{
                if($data['type'] == 'column'){
                    $bool = db('catalog')->where('idcatalog',$data['id'])->delete();
                }elseif ($data['type'] == 'module'){
                    $bool = db("{$table}")->where('idmodule',$data['id'])->delete();
                }elseif ($data['type'] == 'resource'){
                    $bool = db('resource')->where('idresource',$data['id'])->delete();
                }else{
                    $bool = db('operate')->where('idoperate',$data['id'])->delete();
                }
            }

        return $bool;
    }


    //将一个或多个模块移至另一个栏目下
    public function module_change($data){
        if(strstr($data['idmodule'],',')){
            $module_id = explode(',',$data['idmodule']);
            for ($i=0;$i<count($module_id);$i++){
                $bool = db('module')->where('idmodule = :idmodule',['idmodule'=>$module_id[$i]])->setField('codecatalog', $data['chrcolumncode']);
            }
        }else{
            $bool = db('module')->where('idmodule = :idmodule',['idmodule'=>intval($data['idmodule'])])->setField('codecatalog', $data['chrcolumncode']);
        }
    }

    //处理栏目添加，修改，删除跳转页面
    public function column_deal($data){
        if(array_key_exists('id',$data)){
            $column_info = db('catalog')->where('idcatalog = :idcatalog',['idcatalog'=>$data['id']])->find();
            $column_info['idcatalog'] = $data['id'];
            //图片上传路径/tp5/public/uploads/column/
            $url = str_replace('Index.php','',request()->instance()->baseFile()).'uploads/column/';
            $column_info['chrimgpath'] = str_replace($url,'',$column_info['chrimgpath']);
        }else {
            $column_info=[];
            $column_info['textremark'] = '';
            $column_info['chrname'] = '';
            $column_info['chrcode'] = '';
            $column_info['chrimgpath'] = '';
            $column_info['intsn'] = '';
            $column_info['intflag'] = '2';
            $column_info['idcatalog'] = '';
        }
        $column_info['action'] = $data['action'];
        return $column_info;
    }

    //栏目添加，修改提交地址
    public function column_post($data){

        //图片上传路径/tp5/public/uploads/column/
        //$url = 'public' . DS . 'uploads'.DS.'column';
        $url =  'uploads'.DS.'column';

        // 获取表单上传文件
        $file = request()->file('chrimgpath');
        if(!empty($file)){
            // 移动到框架应用根目录/public/uploads/column 目录下
            //$path = 'public' . DS . 'uploads'.DS.'column';

            //上传验证
            $info = $file->validate(['size'=>1567802,'ext'=>'jpg,png,gif'])->move($url);
            $data_arr = [
                'chrname'       =>$data['chrname'],
                'chrcode'       =>$data['chrcode'],
                'intsn'         =>$data['intsn'],
                'textremark'    =>$data['textremark'],
                'intflag'       =>$data['intflag'],
                'chrimgpath'    =>str_replace('\\','/',$info->getSaveName())
            ];
            if($info == false){
                echo $file->getError();
                exit();
            }

        }else{

            if(array_key_exists('chrimgpath1',$data) == false){
                $data['chrimgpath1'] = '';
            }
            $data_arr = [
                'chrname'       =>$data['chrname'],
                'chrcode'       =>$data['chrcode'],
                'intsn'         =>$data['intsn'],
                'textremark'    =>$data['textremark'],
                'intflag'       =>$data['intflag'],
                'chrimgpath'    =>$url.$data['chrimgpath1']
            ];
        }
        if($data['action'] == 'add'){

            $bool = db('catalog')->data($data_arr)->insert();
        }else{
            $bool = db('catalog')->where('idcatalog',$data['idcatalog'])->update($data_arr);
        }
        return $bool;
    }

    //处理模块添加，修改，删除跳转页面
    public function module_deal($data){
        if(array_key_exists('code',$data)){
            if(array_key_exists('flag',$data)){
                $table = $data['flag'] == 'module'?'module':'extended_module';
                $module_info = db("{$table}")->where('chrcode=:chrcode',['chrcode'=>$data['code']])->find();
            }
//            var_dump($module_info);exit;
            //图片上传路径/tp5/public/uploads/column/
            $url = str_replace('Index.php','',request()->instance()->baseFile()).'uploads/module/';
            $module_info['chrimgpath'] = str_replace($url,'',$module_info['chrimgpath']);
        }else {
            $module_info=[];
            $module_info['codecatalog'] = '';
            $module_info['chrname'] = '';
            $module_info['chrname'] = '';
            $module_info['chrcode'] = '';
            $module_info['chrimgpath'] = '';
            $module_info['intsn'] = '';
            $module_info['operation'] = '';
            $module_info['intflag'] = 2;
            $module_info['textremark'] = '';
            $module_info['intflag'] = '';
        }
        $module_info['action'] = $data['action'];
        return $module_info;
    }

    //模块添加，修改提交数据
    public function module_post($data){
        //区分是扩展模块还是标准模块
        if(array_key_exists('flag',$data)) {
            $table = $data['flag'] == 'module' ? 'module' : 'extended_module';
        }
        // 获取表单上传文件
        $file = request()->file('chrimgpath');
        //图片上传路径/tp5/public/uploads/module/
        $url = str_replace('Index.php','',request()->instance()->baseFile()).'uploads/module/';
        if(!empty($file)){
            // 移动到框架应用根目录/public/uploads/module 目录下
            $path = ROOT_PATH . 'public' . DS . 'uploads'.DS.'module';
            //上传验证
            $info = $file->validate(['size'=>1567802,'ext'=>'jpg,png,gif'])->move($path);
            $data_arr = [
                'codecatalog'   =>  $data['codecatalog'],
                'chrname'       =>  $data['chrname'],
                'chrcode'       =>  $data['chrcode'],
                'intsn'         =>  $data['intsn'],
                'operation'     =>  $data['operation'],
                'intflag'       =>  2,
                'textremark'    =>  $data['textremark'],
                'chrimgpath'    => $url.str_replace('\\','/',$info->getSaveName())
            ];
            if($info == false){
                echo $file->getError();
                exit();
            }
        }else{
            $data_arr = [
                'codecatalog'   =>  $data['codecatalog'],
                'chrname'       =>  $data['chrname'],
                'chrcode'       =>  $data['chrcode'],
                'intsn'         =>  $data['intsn'],
                'operation'     =>  $data['operation'],
                'intflag'       =>  $data['intflag'],
                'textremark'    =>  $data['textremark'],
                'chrimgpath'    =>$url.$data['chrimgpath1']
            ];
        }

        if($data['action'] == 'add'){
            $module_list = db("{$table}")->select();
            //检测模块代号是否重复
            foreach ($module_list as $key=>$value){
                if($value['chrcode'] == $data['chrcode']){
                    return $bool = '模块代号已存在';
                }
            }
        }
        if($data['action'] == 'edit'){
            $count1 = db("{$table}")->where('chrcode',$data['chrcode'])->count();
            $count2 = db("{$table}")->where('chrcode',$data['chrcode'])->where('idmodule',$data['moduleid'])->count();
            if($count1 > 1 && $count2 == 1){
                return $bool = '模块代号已存在';
            }
            if($count1 > 0 && $count2 == 0){
                return $bool = '模块代号已存在';
            }
        }


        if($data['action'] == 'add'){
            //当添加标准模块的时候默认站点id为0
            $data_arr['idsite'] = 0;
            $bool = db("{$table}")->insert($data_arr);
        }else{
            $bool = db("{$table}")->where('idmodule',$data['moduleid'])->update($data_arr);
        }

        return $bool;
    }

    //处理资源添加，修改，跳转页面
    public function resource_deal($data){
        if(!empty($data['id'])){
            $resource = db('resource')->where('idresource=:idresource',['idresource'=>$data['id']])->find();
        }else {
            $resource=[];
            $resource['textremark'] = '';
            $resource['chrname'] = '';
            $resource['chrcode'] = '';
            $resource['modulecode'] = $data['moducode'];
        }
        $resource['action'] = $data['action'];
        return $resource;
    }

    //资源添加，修改提交的数据地址
    public function resource_post($data){
        $resource = [];
        $resource['chrname'] = $data['chrname'];
        $resource['chrcode'] = $data['chrcode'];
        $resource['modulecode'] = $data['modulecode'];
        $resource['textremark'] = $data['textremark'];
        if($data['action'] == 'add'){
            $bool = db('resource')->insert($resource);
        }else{
            $bool = db('resource')->where('idresource',$data['idresource'])->update($resource);
        }
        return $bool;
    }

    //资源:操作列表
    public function operate_list($data){
        $count = db('operate')->where(['chrmodulecode'=>$data['moducode'],'chrresourcecode'=>$data['resourcecode']])->count();
        $page = new Page($count,PAGE_SIZE);

        $operate_list = db('operate')
            ->where(['chrmodulecode'=>$data['moducode'],'chrresourcecode'=>$data['resourcecode']])
            ->order('idorder asc,idoperate desc')
            ->limit($page->firstRow.','.$page->pageSize)
            ->select();
        $arr['pager'] = $page;
        $arr['data'] = $operate_list;
        return $arr;
    }

    //操作跳转页面处理
    public function operate_deal($data){
        if(array_key_exists('id',$data)){
            $operate = db('operate')->where('idoperate=:idoperate',['idoperate'=>$data['id']])->find();
            $operate['idoperate'] = $data['id'];
        }else {
            $operate=[];
            $operate['chrname'] = '';
            $operate['chrcode'] = '';
            $operate['chrmodulecode'] = $data['moducode'];
            $operate['chrresourcecode'] = $data['resourcecode'];
            $operate['textremark'] = '';
            $operate['idorder'] = '';
            $operate['idoperate'] = '';
        }
        $operate['action'] = $data['action'];
        return $operate;
    }

    //操作，添加，修改提交页面
    public function operate_post($data){
        $operate = [];
        $operate['chrname'] = $data['chrname'];
        $operate['chrcode'] = $data['chrcode'];
        $operate['textremark'] = $data['textremark'];
        $operate['chrmodulecode'] = $data['moducode'];
        $operate['idorder'] = $data['idorder'];
        $operate['chrresourcecode'] = $data['resourcecode'];
        if($data['action'] == 'add'){
            $bool = db('operate')->insert($operate);
        }else{
            $bool = db('operate')->where('idoperate',$data['id'])->update($operate);
        }
        return $bool;
    }
}