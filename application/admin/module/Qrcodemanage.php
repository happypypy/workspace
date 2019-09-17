<?php
/*
 * @Descripttion: 二维码管理
 * @version: v1.0
 * @Author: ChenJie
 * @Date: 2019-08-01 15:16:19
 * @LastEditors: ChenJie
 * @LastEditTime: 2019-08-01 15:30:55
 */
namespace app\admin\module;
use think\Model;
use think\Page;

class Qrcodemanage extends Model{
    // 获取二维码列表
    public function index($data){
        $idsite = session("idsite");

        $map = ['idsite' => $idsite];
        if($data['qrcode_name']){
            $map['qrcode_name'] = ['like','%'.$data['qrcode_name'].'%'];
        }

        $totalRecord = db('qrcode_manage')->where($map)->count();
        $result = db('qrcode_manage')->where($map)->page($data['p'],PAGE_SIZE)->order('id desc')->select();        
        $page = new Page($totalRecord, PAGE_SIZE);
        
        return ['page' => $page, 'data'=> $result];
    }
    // 获取二维码详情
    public function get_qrcode_info($id){
        return db('qrcode_manage')->where('id',$id)->find();
    }
    // 二维码增加或者修改
    public function qrcode_modi($data){
        $id = isset($data['id']) ? $data['id'] : 0;
        $action = isset($data['action']) ? $data['action'] : 0;
        $idsite = session('idsite');
        $scene_str = getSceneStr();

        // 获取配置
        $config = db('site_manage')->field('appid,appsecret')->where('id',$idsite)->find();
        $api = new \think\wx\Api([
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ]);

        // 生成二维码
        $ticket  = $api->get_qrcode_url_by_str($scene_str);

        $data = [
            'idsite' => $idsite,
            'scene_str' => $scene_str,
            'ticket' => $ticket,
            'qrcode_name' => isset($data['qrcode_name']) ? $data['qrcode_name'] : '',
            'qrcode_desc' => isset($data['qrcode_desc']) ? $data['qrcode_desc'] : '',
            'create_time' => time()
        ];

        if($action == 'add'){
            $result = db('qrcode_manage')->insert($data);
        }else{
            $result = db('qrcode_manage')->where('id',$id)->update($data);
        }

        return $result;
    }
    // 二维码删除
    public function qrcode_del($data){
        $id = isset($data['id']) ? $data['id'] : 0;

        $result = false;
        if(strstr( $data['id'],',')){
            $result = db('qrcode_manage')->whereIn('id',explode(",",$id))->delete();
        }else{
            $result = db('qrcode_manage')->where('id',$id)->delete();
        }

        return $result;
    }
    // 二维码选择
    public function qrcode_select($data){
        $idsite = session('idsite');
        $totalRecord = db('qrcode_manage')->where('idsite',$idsite)->count();
        $result = db('qrcode_manage')->where('idsite',$idsite)->page($data['p'],PAGE_SIZE)->select();
        $page = new Page($totalRecord, PAGE_SIZE);
        
        return ['page' => $page, 'data'=> $result];
    }
}