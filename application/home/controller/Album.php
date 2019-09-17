<?php

namespace app\home\controller;

use think\face\AipFace;
use think\Request;

class Album extends BaseAuth {
    /**
     * 相册列表页面
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function albumlist(){
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite=$config['id'];

        if(isset($request['p'])){
            $ipage = $request['p'];

        }else{
            $ipage = 1;
        }
        $pagesize = 10;
        $this->assign("ipage",$ipage);


        $node_info = db('node')->where('idsite='.$idsite.' and nodeid='.$request['nodeid'])->find();
        if(empty($node_info))
        {
            header("location:/error.php?msg=".urlencode("栏目不存在，有疑问请和管理联系！")."&url=".urlencode("/".$sitecode));
            exit();
        }
        $offset = ($ipage - 1) * $pagesize;//分页数据的偏移量
        $result = db('album')->where(['site_id'=>$idsite,'state'=>1,'activity_id'=>0])->order("create_time desc,id desc")->field('activity_id',true)->limit($offset,$pagesize)->select();
        //取出相片数量
        if($result){
            foreach ($result as $k=>&$v){
                $count = db('album_photo')->where(['site_id'=>$idsite,'album_id'=>$v['id'],'is_delete'=>0])->count();
                $v['photo_num'] = $count;
            }
        }
        //获得相冊列表模版路径
        $roottpl = 'template/modules';
        $url =  $roottpl.'/album/album_list.html';

        if (Request::instance()->isPost() && isset($request['ajax'])) {
            // echo json_encode($request);exit;
            $url = $roottpl . '/album/ajax_album_list.html';
        }
        $this->assign('roottpl','/'.$roottpl);
        $this->assign('nodeid',$request['nodeid']);
        $this->assign('node_info',$node_info);
        $this->assign('pageSize',$pagesize);
        $this->assign('idsite',$idsite);
        $this->assign('pageIndex',$ipage);
        $this->assign('sitecode',$sitecode);
        $this->assign('result_data',$result);
        $this->assign('SelectFooterTab',1);
        return $this->fetch($url);
    }

    /**
     * 图片列表页面
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function photolist(){
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite=$config['id'];
        $user_info = $this->userinfo;
        if(!$request['album_id']){
            header("location:/error.php?msg=".urlencode("缺少相册的标识")."&url=".urlencode("/".$sitecode));exit();
        }
        //查询出该相册下的图片列表
        $result = db('album_photo')->where(['site_id'=>$idsite,'album_id'=>$request['album_id'],'is_delete'=>0])->order("create_time desc,id desc")->field('is_delete',true)->select();
        //获得相冊列表模版路径
        $roottpl = 'template/modules';
        //如果不是点单张图片的话，就是直接进相册列表就加一条相册浏览访问记录
        if(!$request['photo_id']){
            db('album')->where(['site_id'=>$idsite,'state'=>1,'id'=>$request['album_id']])->setInc('view_count',1);
            $url =  $roottpl.'/album/photo_list.html';
        }else{
            $url =  $roottpl.'/album/photo_detail.html';
            $this->assign('photo_id',$request['photo_id']);//点击的图片
            //查看该用户是否对图片进行点赞过
            if($result){
                foreach ($result as $k=>&$v){
                    $like_info = db('album_photo_like_record')->where(['site_id'=>$idsite,'photo_id'=>$v['id'],'user_id'=>$user_info['idmember']])->find();
                    //如果有点赞过，那么就是1，没有就是0
                    $v['is_like'] = $like_info?1:0;
                }
            }
//            halt($result);
        }
        if(isset($request['key'])){
            $this->assign('key',$request['key']);//图片的键值
        }
//        halt($result);

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('idsite',$idsite);
        $this->assign('sitecode',$sitecode);
        $this->assign('result_data',$result);
        $this->assign('album_id',$request['album_id']);//相册id
        $this->assign('SelectFooterTab',1);
        return $this->fetch($url);
    }

    /**
     * 点击头像搜索图片列表页面
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function searchphoto(){
        $request = Request::instance()->param();
//        halt($request);
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite=$config['id'];
        $user_info = $this->userinfo;
        if(!$request['album_id']){
            header("location:/error.php?msg=".urlencode("缺少相册的标识")."&url=".urlencode("/".$sitecode));exit();
        }
        if(!isset($request['face_url']) || empty($request['face_url'])){
            header("location:/error.php?msg=".urlencode("缺少图片的路径")."&url=".urlencode("/".$sitecode));exit();
        }
        $face_api = new AipFace(APP_ID, API_KEY, SECRET_KEY);//实例化人脸识别类
        //将图片转换为base64
        $base64_img = $face_api->imgToBase64(trim($request['face_url'],'/'));
        //调用人脸搜索的接口
        $face_result = $face_api->search($base64_img,'BASE64',$idsite.'_'.$request['album_id'],['match_threshold'=>80,'max_user_num'=>50]);
        //判断调用接口是否成功
        if($face_result['error_code'] == 0){
            $result = $face_result['result']['user_list'];//返回的匹配出的用户信息列表
            //否则就是没有搜索到
        }else{
            $result = [];
        }
//        halt($result);
        //获得相冊列表模版路径
        $roottpl = 'template/modules';
        $url =  $roottpl.'/album/search_photo.html';
        //如果有结果,并且是通过点击查看搜索出来的图片
        if($result && $request['photo_id']){
            $photo_id = '';
            $album_id = '';
            $photo_arr = [];
            foreach ($result as $k=>&$v){
                $uid_arr = explode('_',$v['user_id']);
                $photo_id = $uid_arr[1].',';//取到图片id
                $group_id_arr = explode('_',$v['group_id']);
                $album_id = $group_id_arr[1];//取到相册的id（因为每一张搜索出来的图片是同一个相册）
                //此刻得循环查询，因为不循环查询的话，无法确保列表数据的顺序和搜索出来的数据顺序一致。进行去数据库查询搜索出来的这些数据
                $photo_arr[] = db('album_photo')->where(['site_id'=>$idsite,'album_id'=>$album_id,'is_delete'=>0,'id'=>$photo_id])->field('is_delete',true)->find();
            }
            //进行去数据库查询搜索出来的这些数据
//            $result = db('album_photo')->where(['site_id'=>$idsite,'album_id'=>$album_id,'is_delete'=>0,'id'=>['in',trim($photo_id,',')]])->field('is_delete',true)->select();
//            halt($photo_arr);
            $url =  $roottpl.'/album/photo_detail.html';
            $this->assign('photo_id',$request['photo_id']);//点击的图片
            if(isset($request['key'])){
                $this->assign('key',$request['key']);//图片的键值
            }
            //查看该用户是否对图片进行点赞过
            if($photo_arr){
                foreach ($photo_arr as $k=>&$v){
                    $like_info = db('album_photo_like_record')->where(['site_id'=>$idsite,'photo_id'=>$v['id'],'user_id'=>$user_info['idmember']])->find();
                    //如果有点赞过，那么就是1，没有就是0
                    $v['is_like'] = $like_info?1:0;
                }
            }
            $result = $photo_arr;
        }
//        halt($result);

        $this->assign('roottpl','/'.$roottpl);
        $this->assign('idsite',$idsite);
        $this->assign('sitecode',$sitecode);
        $this->assign('result_data',$result);
        $this->assign('album_id',$request['album_id']);//相册id
        $this->assign('face_url',$request['face_url']);//搜索的图片路径
        $this->assign('SelectFooterTab',1);
        return $this->fetch($url);
    }

    /**
     * 对图片进行评论
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addphotocomment(){
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite=$config['id'];
        $user_info = $this->userinfo;
        if(!$request['photo_id']){
            header("location:/error.php?msg=".urlencode("缺少图片的标识")."&url=".urlencode("/".$sitecode));exit();
        }
        //查询出该评论的图片信息
        $result = db('album_photo')->where(['site_id'=>$idsite,'is_delete'=>0,'id'=>$request['photo_id']])->field('is_delete',true)->find();
        //封装要插入图片评论表中的数据
        $add_data = [
            'create_time'=>date('Y-m-d H:i:s',time()),
            'site_id'=>$idsite,
            'photo_id'=>$request['photo_id'],
            'min_face_url'=>$result['min_face_url'],
            'user_id'=>$user_info['idmember'],
            'user_nickname'=>$user_info['nickname'],
            'content'=>$request['content'],
            'album_id'=>$result['album_id'],
        ];
//        halt($add_data);
        $bool = db('album_comment')->insert($add_data);
        //评论数量加1
        if($bool){
            db('album_photo')->where(['site_id'=>$idsite,'is_delete'=>0,'id'=>$request['photo_id']])->update(['comment_num'=>$result['comment_num']+1]);
        }
        return $bool;
    }

    /**
     * 对图片进行点赞
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function photolike(){
        $request = Request::instance()->param();
        $sitecode=$request['sitecode'];
        $config=$this->wxConfig;
        $idsite=$config['id'];
        $user_info = $this->userinfo;
        if(!$request['photo_id']){
            header("location:/error.php?msg=".urlencode("缺少图片的标识")."&url=".urlencode("/".$sitecode));exit();
        }
        //查询出该图片信息
        $result = db('album_photo')->where(['site_id'=>$idsite,'is_delete'=>0,'id'=>$request['photo_id']])->field('is_delete',true)->find();
        $update = [];
        //如果是点赞的话
        if($request['action'] == 'add'){
            $add_data = [
                'create_time'=>date('Y-m-d H:i:s',time()),
                'site_id'=>$idsite,
                'photo_id'=>$request['photo_id'],
                'min_face_url'=>$result['min_face_url'],
                'user_id'=>$user_info['idmember'],
                'user_nickname'=>$user_info['nickname'],
                'album_id'=>$result['album_id'],
            ];
            //进行生成一条点赞记录
            $add_bool = db('album_photo_like_record')->insert($add_data);
            if($add_bool){
                $update['good_num'] = $result['good_num'] + 1;
            }
        }elseif($request['action'] == 'sub' && $result['good_num'] > 0){
            //删除该图片的该用户的点赞记录
            $del_bool = db('album_photo_like_record')->where(['site_id'=>$idsite,'photo_id'=>$request['photo_id'],'user_id'=>$user_info['idmember']])->delete();
            if($del_bool){
                $update['good_num'] = $result['good_num'] - 1;
            }
        }
        //如果需要修改
        if($update){
            $bool = db('album_photo')->where(['site_id'=>$idsite,'is_delete'=>0,'id'=>$request['photo_id']])->update($update);
        }else{
            $bool = false;
        }
//        halt($bool);
        return $bool;
    }


}