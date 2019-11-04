<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/24
 * Time: 10:54
 */

namespace app\admin\module;
use think\Model;
use think\Page;

class Configs extends Model{

    //网站设置
    public function index($data){
        $idsite = session('idsite');
        if(array_key_exists('menucode',$data) == false){
            $menu_one = db('config_menu')->where('(idsite=0 or idsite='.$idsite.') and isshow=1')->order('intsn')->find();
            $menu_rule = db('config_rule')->where('menucode=\''.$menu_one['chrcode'].'\' and  (idsite='.$idsite.")")->select();
        }else{
            $map['chrcode'] = $data['menucode'];
            $menu_rule = db('config_rule')->where('menucode=\''.$data['menucode'].'\' and (idsite='.$idsite.")")->select();
            $menu_one = db('config_menu')->where('chrcode=\''.$data['menucode'].'\' and (idsite=0 or idsite='.$idsite.') and isshow=1')->find();
        }

        foreach ($menu_rule as $key=>$value){
            foreach ($value as $ke=>$val)
            if(strstr($val,'_')){
                $arr = explode('_',$val);
                $value[$ke] = $arr;
                $menu_rule[$key] = $value;
            }
        }


        $search_arr = array();
        $type = 3;
        $search_arr["type"] = $type;
        $search_arr["siteid"] = $idsite;
        $wx_reply = db('wx_reply')->where($search_arr)->find();

        //配置菜单
        $config_menu = db('config_menu')->where('idsite=0 or idsite='.$idsite)->order('intsn')->select();
        $arr = [];
        $arr['menudata'] = $config_menu;
        $arr['ruledata'] = $menu_rule;
        $arr['menuone'] = $menu_one;
        $arr['idsite'] = $idsite;

        if(!empty($wx_reply)) {
            $arr['reply_word'] = $wx_reply["content"];
            $arr['reply_img'] = $wx_reply["reply_img_url"];
        }else{
            $arr['reply_word'] = "";
            $arr['reply_img'] = "";
        }

        return $arr;
    }
    
    //数据提交
    public function data_post($data){

        $reply_word  = $data["reply_word"];
        unset($data["reply_word"]);
        $reply_img  = $data["reply_img"];
        unset($data["reply_img"]);

        $config_lang_type = config('admin_lang_list');
        if(empty($config_lang_type)){
            $lang_key[] = 'cn';
        }else{
            foreach ($config_lang_type as $key=>$value){
                $lang_key[] = $key;
            }
        }
        // 某个菜单所有的配置项
        $bool = false;
        $field_list = db('config_rule')->where("menucode='".$data["menucode"]."' and isshow=1 and (idsite='".session('idsite')."')")->field('fieldname')->select();
        foreach ($field_list as $key=>$value){
            for ($i=0;$i<count($lang_key);$i++){
                if($lang_key[$i] == 'cn'){
                    $prefix = '';
                }else{
                    $prefix = $lang_key[$i].'_';
                }
                //如果吗没有数据提交
                if(array_key_exists($prefix.$value['fieldname'],$data)){
                    $arr[$value['fieldname']][$prefix.'defaultval'] = $data[$prefix.$value['fieldname']];
                }else{
                    $arr[$value['fieldname']][$prefix.'defaultval'] = '';
                }

             }
            $bool = db('config_rule')->where(['fieldname'=>$value['fieldname'],'idsite'=>session('idsite')])->update($arr[$value['fieldname']]);

        }
        $result['bool'] = true;
        $result['menucode'] = $data['menucode'];


        //如果有填写自动关注回复的内容和图片，则在这里处理事件
        if(!empty($reply_word) || !empty($reply_img)){
            $this ->add_wx_reply($reply_word,$reply_img);
        }

        return $result;
    }


    /**
     * 添加微信关注自动回复
     * @param $reply_word     自动回复的内容
     * @param $reply_img      自动回复的图片
     * @return bool
     */
    private function add_wx_reply($reply_word,$reply_img)
    {
        if (empty($reply_word) && empty($reply_img)) {
            return false;
        }
        $siteid = session('idsite');
        $search_arr = array();
        $type = 3;
        $search_arr["type"] = $type;
        $search_arr["siteid"] = session('idsite');
        $wx_reply = db('wx_reply')->where($search_arr)->find();

        $wx_replay_id = isset($wx_reply["wx_replay_id"]) ? $wx_reply["wx_replay_id"] : 0;

        //要保存到数据库的字段数组
        $post_data = array();
        $post_data["siteid"] = $siteid;
        $post_data["type"] = $type;
        $post_data["keyword"] = "被关注自动回复";
        $post_data["content"] = trim($reply_word);
        $post_data["reply_img_url"] = trim($reply_img);
        $post_data["media_id"] = 0;
        if(!empty($reply_img)){
            $config=getWeiXinConfig(strtolower(getSiteCode(session('idsite'))));
            $api = new \think\wx\Api(
                array(
                    'appId' => trim($config['appid']),
                    'appSecret'    => trim($config['appsecret'])
                )
            );
            $rs = $api ->add_material("image",(__ROOT__."/public".$reply_img));
            $media_id = "";
            if(!empty($rs[1])){
                $media_id = $rs[1]->media_id;
            }
            if(empty($media_id)){
                $post_data["reply_img_url"] = "";
            }else{
                $post_data["media_id"] = $media_id;
            }

        }else{
            $post_data["media_id"] = "";
        }

        if ($wx_replay_id > 0) {   //修改

            $bool = db('wx_reply')->where('wx_replay_id=:id', ['id' => $wx_replay_id])->update($post_data);
        } else {                   //新增

            $bool = db('wx_reply')->insert($post_data);
        }

        if ($bool) {
            return true;
        } else {
            return false;
        }
    }


    //获取菜单
    public function config_menu(){
        $config_menu = db('config_menu')->where('idsite='.session('idsite').' or idsite=0' )->select();
        return $config_menu;
    }
    
    //自定义配置
    public function config_self(){
        $menu_count = db('config_menu')->where('idsite='.session('idsite').' or idsite=0')->count();
        $page = new Page($menu_count,PAGE_SIZE);
        $config_menu = db('config_menu')->where('idsite='.session('idsite').' or idsite=0')->limit($page->firstRow.','.$page->pageSize)->order('intsn')->select();
        $arr['pager'] = $page;
        $arr['data'] = $config_menu;
        $arr['idsite'] = session('idsite');
        return $arr;
    }

    //菜单配置项列表
    public function config_rule($data){
        $rule_list = db('config_rule')->where('menucode = :menucode and (idsite= :idsite)',['menucode'=>$data['menucode'],'idsite'=>session('idsite')])->order('intsn')->select();
        return $rule_list;

    }

    //单个菜单配置项配置
    public function config_info($data){
        $config_info = db('menucode = :menucode and (idsite=0 or idsite= :idsite)',['menucode'=>$data['menucode'],'idsite'=>session('idsite')])->select();
        return $config_info;
    }

    //修改菜单跳转页面
    public function config_deal($data){

        if($data['action'] == 'add'){
            $config_info = [
                'chrname'   =>  '',
                'chrcode'   =>  '',
                'intsn'     =>  '',
                'isshow'    =>  1
            ];
        }else{
            $config_info = db('config_menu')->where('chrcode = :chrcode',['chrcode'=>$data['menucode']])->find();
        }
        $config_info['action'] = $data['action'];
        return $config_info;
    }

    //菜单删除地址
    public function config_del($data){
        $bool = db('config_menu')->where('chrcode = :chrcode',['chrcode'=>$data['menucode']])->delete();
        return $bool;
    }
    
    //修改菜单提交地址
    public function config_post($data){
        $arr = [
            'chrname'   =>  $data['chrname'],
            'chrcode'   =>  $data['chrcode'],
            'intsn'     =>  $data['intsn'],
            'isshow'    =>  $data['isshow']
        ];
       if($data['action'] == 'add'){
           $bool = db('config_menu')->insert($arr);
       }else{
           $bool = db('config_menu')->where('chrcode = :chrcode',['chrcode'=>$data['menucode']])->update($arr);
       }
        return $bool;
    }

    //修改配置项跳转页面
    public function rule_deal($data){
        if($data['action'] == 'add'){
            $rule_info = [
                'idmenu'        =>  '',
                'menucode'       =>  '',
                'chrname'       =>  '',
                'fieldname'     =>  '',
                'fieldtype'     =>  '',
                'intsn'         =>  '',
                'content'       =>  '',
                'isshow'        =>  2,
                'intflag'       =>  2,
                'tips'          =>  '',
                'remark'        =>  '',
                'defaultval'    =>  '',
                'txtwidth'      =>  '',
                'txtheight'     =>  '',
                //'attrlist'    =>  '',
                'txttype'       =>  1,
                'type'       =>  1,
                'maxlength'     =>  '',
                'settings'      =>  '',
                'childsetting'  =>  '2∮∮∮'
            ];
        }elseif($data['action'] == 'edit'){
            $rule_info = db('config_rule')->where('id = :id',['id'=>$data['id']])->find();
        }

        $FieldType = $rule_info['fieldtype'];
        $Settings = $rule_info['settings'];
        $childSetting = $rule_info['childsetting'];

        if(!empty($childSetting)){
            if(strstr($childSetting,'∮')){
                $arr = explode('∮',$childSetting);
                $rule_info['childsetting'] = $arr;
            }

        }

        $arrTmp = array();

        if ($FieldType == 1) {
            $arr = explode('∮', $Settings);
            $arrTmp[0] = $arr[0];
            $arr1 = explode('☆', $arr[1]);
            $arrTmp[1] = $arr1[0];
            $arrTmp[2] = $arr1[1];
            $rule_info["settings"] = $arrTmp;
        } elseif (in_array($FieldType, array(4, 5, 6, 7, 19, 20))) {
            $arrTmp = array("", "", "", "", "", "", "");
            $arr = explode('∮', $Settings);
            $arrTmp[0] = $arr[0];
            $arr1 = explode('☆', $arr[1]);
            $arrTmp[1] = $arr1[0];
            $arrTmp[2] = $arr1[1];
            $arrTmp[3] = $arr1[2];
            $arrTmp[4] = $arr1[3];
            $arrTmp[5] = $arr1[4];
            $arrTmp[6] = $arr1[5];
            $rule_info["settings"] = $arrTmp;
        }else{
            $rule_info["settings"][0] = '';
            $rule_info["settings"][1] = '';
            $rule_info["settings"][3] = '';
            $rule_info["settings"][4] = '';
            $rule_info["settings"][5] = '';
            $rule_info["settings"][6] = '';
        }

        $rule_info['action'] = $data['action'];
        return $rule_info;
    }

    //删除配置项
    public function rule_del($data){
        $bool = db('config_rule')->where('id = :id',['id'=>$data['id']])->delete();
        return $bool;
    }

    //添加，修改配置项提交地址
    public function rule_post($data){
        $FieldType = $data['fieldtype']; //字段类型
        if ($FieldType == 1) {
            //验证规则                  正则或函数名                     错误信息提示
            $data["settings"] = $data['dropRegPattern'] . '∮' . $data['txtRegPattern'] . '☆' . $data['txtErrorMessage'];
        } elseif (in_array($data['fieldtype'], array(4, 5, 6, 7, 19, 20))) {
            if ($data['GetType'] == 1){
                if(empty($data['ModelName'])){
                    return $bool = '多项选择属性列表不能为空';
                }
                //                  手动设置，数据库             手动设置格式
                $data["settings"] = $data['GetType'] . '∮' . $data['ModelName'] . '☆☆☆☆☆';
            }elseif ($data['GetType'] == 2){
                //                                                数据表名                        条件表达式                        获取条数                      文本字段                       字段值
                $data["settings"] = $data['GetType'] . '∮☆' . $data['txtTableName'] . '☆' . $data['txtExpression'] . '☆' . $data['txtGetNum'] . '☆' . $data['txtTextColumnName'] . '☆' . $data['txtValueColumnName'];
            }
        }else{
            $data["settings"] = '';
        }


        //判断是否是级联
        if($FieldType == 7){
            if(array_key_exists('child',$data)){
                if($data['child'] == 1){
                    $data['childsetting'] = $data['child'].'∮'.$data['childname'].'∮'.$data['childurl'].'∮'.$data['func'];
                }else{
                    $data['childsetting'] = '2∮∮∮';
                }
            }else{
                $data['childsetting'] = '2∮∮∮';
            }
        }else{
            $data['childsetting'] = '2∮∮∮';
        }


        $arr = [
            'idmenu'       =>  $data['id_menu'],
            'chrname'      =>  $data['chrname'],
            'fieldname'    =>  $data['fieldname'],
            'fieldtype'    =>  $data['fieldtype'],
            'intsn'        =>  $data['intsn'],
            'isshow'       =>  $data['isshow'],
            'intflag'      =>  $data['intflag'],
            'tips'         =>  $data['tips'],
            'remark'       =>  $data['remark'],
            'defaultval'   =>  $data['defaultval'],
            'txtwidth'     =>  $data['txtwidth'],
            'txtheight'    =>  $data['txtheight'],
            'maxlength'    =>  $data['maxlength'],
            //'attrlist'   =>  $data['attrlist'],
            'type'         =>  $data['type'],
            'txttype'      =>  $data['txttype'],
            'settings'     =>  $data['settings'],
            'childsetting' =>  $data['childsetting'],
            'idsite'       =>  session('idsite')
        ];

        if ($data['action'] == 'add') {
            $bool = db('config_rule')->insert($arr);
        }
        if ($data['action'] == 'edit') {
            $bool = db('config_rule')->where('id = :id',['id'=>$data['id']])->update($arr);
        }

        return $bool;
    }

    //菜单导入
    public function menu_leading(){


        $file = request()->file('configs');
        if($file){
            $info = $file->move(ROOT_PATH.'uploads','');
            $configs = file_get_contents(ROOT_PATH.'uploads/'.$info->getFilename());
        }else{
            return $result = false;
        }
        $configs_arr = json_decode($configs,true);

        //删除数据库数据
        $menu = db('config_menu')->where('idsite='.session('idsite'))->select();
        foreach ($menu as $key=>$value){
            $map['menucode'] = $value['chrcode'];
            $map['idsite'] = session('idsite');
            db('config_rule')->where($map)->delete();
        }
        db('config_menu')->where('idsite='.session('idsite'))->delete();
        //插入数据库
        $bool1 = false;
        $bool2 = false;
        $menu = [];
        $rule = [];
        $rule_new = [];
        foreach ($configs_arr as $key=>$value){
            unset($value['data']);
            unset($value['id']);
            $value['idsite'] = session('idsite');
            $menu[] = $value;
        }

        foreach ($configs_arr as $key=>$value){
            $rule[] = $value['data'];
        }
        foreach ($rule as $key=>$value){
            foreach ($value as $k=>$v){
                unset($v['id']);
                unset($v['site']);
                $v['idsite'] = session('idsite');
                $rule_new[] = $v;
            }
        }
        foreach ($menu as $key=>$value){
            $bool1 = db('config_menu')->insert($value);
        }
        foreach ($rule_new as $key=>$value){
            $bool2 = db('config_rule')->insert($value);
        }
        $result['bool1'] = $bool1;
        $result['bool2'] = $bool2;
        return $result;
    }


    public function wx_menu_post($data){
        $idsite = session('idsite');
        if(empty($idsite) || (int)$idsite <=0){
            return false;
        }
        if(!isset( $data['menu']) || empty( $data['menu'])){
            return false;
        }
        $arr = [
            'wx_menu'   =>  $data['menu']
        ];
        $bool = db('site_manage')->where('id='.$idsite)->update($arr);
        return $bool;
    }

}