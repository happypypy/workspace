<?php
/**
 * 天络CMS
 * ============================================================================
 * 版权所有 2017-2027 深圳天络科技有限公司，并保留所有权利。
 * 网站地址: http://www.chinasky.net
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: huangshixin
 * Date:2018-06-17 */
namespace app\home\model;

use think\face\AipFace;
use think\Model;

class Album extends Model
{

    protected $siteid = 0;
    protected $face_api;//人脸接口
    public function __construct($idStie)
    {
        $this->siteid = $idStie;
        $this->face_api = new AipFace(APP_ID, API_KEY, SECRET_KEY);//实例化这个类
        parent::__construct();
    }

    /**
     * 执行用户添加图片的逻辑
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function postAddPhoto($data,$user_info){
        //封装执行插入图片表的数据
        $add_data['create_time'] = date('Y-m-d H:i:s',time());
        $add_data['album_id'] = $data['album_id'];
        $add_data['account_id'] = $user_info['idmember'];
        $add_data['account_name'] = $user_info['nickname'];
        $add_data['site_id'] = $this->siteid;
        $add_data['min_face_url'] = $data['min_face_url'];//小图路径
        $add_data['face_url'] = $data['face_url'];//原图路径
        //执行数据的插入，并且返回插入的数据id
        $id = db('album_photo')->insertGetId($add_data);
        //如果执行成功
        if($id){
            //要给百度的大图图片路径
            $big_img = trim(substr($data['face_url'],0,strrpos($data['face_url'],'.')).'_big'.strstr($data['face_url'],'.'),'/');
            //将图片转换为base64
            $base64_img = $this->face_api->imgToBase64($big_img);
            //先进行人脸检测，看图片是否具有人脸
            $result_detect = $this->face_api->detect($base64_img,imageType,['max_face_num'=>10]);
            //判断调用接口是否成功
            if($result_detect['error_code'] == 0){
                $detect_face_url = '';//默认的存数据库的裁剪人脸路径
                //用来看有几个不需要注册的人脸，保证注册的uid的后缀都是从0-n
                $is_bool = 0;
                //进行裁剪检测出来的人脸
                for ($i=0;$i<$result_detect['result']['face_num'];$i++)
                {
                    //判断人脸置信度
                    if($result_detect['result']['face_list'][$i]['face_probability'] >= 0.8){
                        $filepath='public/uploads/'.$this->siteid.'/'.'cut_photo'.'/'.date('Y').'/'.date('m-d').'/';
                        $tmpPath=$filepath.$result_detect['result']['face_list'][$i]['face_token'].".jpg";
                        $detect_face_url .= '/'.$tmpPath.',';
                        $x=$result_detect['result']['face_list'][$i]['location']['left'];
                        $y=$result_detect['result']['face_list'][$i]['location']['top'];
                        $w=$result_detect['result']['face_list'][$i]['location']['width'];
                        $h=$result_detect['result']['face_list'][$i]['location']['height'];
                        //进行裁剪头像
                        //    $tmp_x = $x-60;
                        $tmp_x = $x-$w*0.2;
                        //    $tmp_y = $y-200;
                        $tmp_y = $y-$h*0.5;
                        //    $tmp_w = $w+200;
                        $tmp_w = $w+$w*0.4;
                        //    $tmp_h = $h+200;
                        $tmp_h = $h+$h*0.6;
                        if($tmp_x < 0)  $tmp_x = 0;
                        if($tmp_y < 0)  $tmp_y = 0;
                        /* 获取原图像尺寸信息 */
                        $img_info = getimagesize($big_img);
                        $source_w=$img_info[0];
                        $source_h=$img_info[1];
                        if($tmp_w > $source_w) $tmp_w = $source_w;
                        if($tmp_h > $source_h) $tmp_h = $source_h;

                        $this->face_api->image_cut($big_img,$tmpPath,$tmp_x,$tmp_y,$tmp_w,$tmp_h);
                        //进行人脸注册，要将图片添加到的组id（注意此刻注册的是裁剪出来的每一张人脸）
                        $group_id = $this->siteid.'_'.$data['album_id'];
                        $num = $is_bool > 0?$i - $is_bool:$i;
                        //要将图片添加到的uid(因为一张图片存在多个人脸的问题，所以uid的$i是代表裁剪的第几的意思)
                        $uid = $this->siteid.'_'.$id.'_'.$num;
                        //将裁剪后的图片转换为base64
                        $base64_img = $this->face_api->imgToBase64($tmpPath);
                        //注册人脸，，//注意，此刻人脸注册传过去的路径是人脸小图的路径，因为便于人脸搜索后在列表直接展示小图
                        $face_result = $this->face_api->addUser($base64_img,imageType,$group_id,$uid,['user_info'=>$data['min_face_url']]);
                        if($face_result['error_code'] == 0){
                            //先查询出该数据库图片的截图face_token
                            $img_data = db('album_photo')->where(['id'=>$id,'site_id'=>$this->siteid])->field('face_token')->find();
                            //将人脸注册后返回的图片标志存数据库
                            if($img_data['face_token']){
                                //如果该图片中已有裁剪图片face_token，那么就拼接
                                $detect_face_token = $img_data['face_token'].','.$face_result['result']['face_token'];
                            }else{
                                $detect_face_token = $face_result['result']['face_token'];
                            }
                            db('album_photo')->where(['id'=>$id,'site_id'=>$this->siteid])->update(['face_token'=>$detect_face_token]);
                        }
                    }else{
                        $is_bool += 1;
                    }
                }
                //如果有人脸
                if($detect_face_url){
                    //进行修改该图片的裁剪人脸路径
                    db('album_photo')->where(['id'=>$id,'site_id'=>$this->siteid])->update(['detect_face_url'=>trim($detect_face_url,',')]);//把最后一个逗号去掉
                }
            }
        }
        return $id;
    }

}
