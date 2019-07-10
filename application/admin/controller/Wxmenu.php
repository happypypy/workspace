<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/2/23
 * Time: 17:31
 */

namespace app\admin\controller;


use think\Request;

class Wxmenu extends Base{
    
    //微信公众号菜单
    public function index()
    {
        if ($this->CMS->CheckPurview('wechat-menu', 'wechat-menu-edit') == false) {
            $this->error('无权限');
        }
        $idsite = session('idsite');
        $wx_menu_info = db('site_manage')->where('id=' . $idsite)->find();
        $site_name = "公众号名称";
        if (!empty($wx_menu_info)) {
            $wx_menu = $wx_menu_info["wx_menu"];
            $site_name = $wx_menu_info["site_name"];
        }
        if(empty($wx_menu)) {
            $sitecode = getSiteCode($idsite);
            $wx_menu = '{"menu": { "button":[ {   
                  "type":"view",
                  "name":"进入网站",
                  "url":"'.ROOTURL.'/' . $sitecode . '",
                  "sub_button":[]
                } ] }}';
        }
        $this->assign('wx_menu', $wx_menu);
        $this->assign('site_name', $site_name);
        return $this->fetch();
    }



    /**
     * 微信菜单数据提交
     */
    public function wx_menu_post(){
        if($this->CMS->CheckPurview('manage','edit')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $config_obj = new \app\admin\module\Configs();
        $bool = $config_obj->wx_menu_post($request);
        if($bool !== false){

            $idsite = session('idsite');
            $idsite = isset($idsite)?$idsite:0;
            $sitecode=getSiteCode($idsite);
            $config=getWeiXinConfig(strtolower($sitecode));
            $appId =trim($config['appid']) ;
            $appSecret = trim($config['appsecret']);
            $api = new \think\wx\Api(
                array(
                    'appId' => $appId,
                    'appSecret'    => $appSecret
                )
            );
//            echo json_encode(array("status"=>"success"));exit;
            $menu = json_decode($request['menu'],true);
            $res=$api->create_menu(json_encode($menu["menu"],JSON_UNESCAPED_UNICODE));
            if(isset($res[1])){
                if($res[1]->errmsg=="ok"){
                    echo json_encode(array("status"=>"success"));exit;
                }
            }
            echo json_encode(array("status"=>"fail"));exit;
        }else{
            echo json_encode(array("status"=>"fail"));exit;
        }
    }

    /**
     * 微信菜单数据提交
     */
    public function wx_menu_post_m(){
        if($this->CMS->CheckPurview('manage','edit')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $config_obj = new \app\admin\module\Configs();
        $bool = $config_obj->wx_menu_post($request);
        if($bool !== false){
            echo json_encode(array("status"=>"success"));exit;
        }else{
            echo json_encode(array("status"=>"fail"));exit;
        }
    }
}