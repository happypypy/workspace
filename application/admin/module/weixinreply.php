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
namespace app\admin\module;
use think\Model;
use think\Page;

class weixinreply extends Model{

    protected  $siteid=0;
    function __construct($idStie){
        $this->siteid=$idStie;
        parent::__construct();
    }

    //列表
    public function index($request){
        $search_arr = array();
        $type = isset($request["type"])?$request["type"]:1;
        $search_arr["type"] = $type;
        $search_arr["siteid"] = session('idsite');
        $count = db('wx_reply')->where($search_arr)->count();
        $data = db('wx_reply')->where($search_arr)->order('status desc,sort desc,wx_replay_id desc')->select();

        $page = new Page($count,PAGE_SIZE);
        $arr = array();
        $arr['pager'] = $page;
        $arr['data'] = $data;
        $arr['count'] = $count;

        return $arr;
    }

    /**
     * 增加数据
     * @param $data             要保持的数据数组
     * @param $wx_replay_id     微信回复主键id
     * @param $siteid           站点id
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function PostData($data,$wx_replay_id,$siteid)
    {

        //要保存到数据库的字段数组
        $post_data = array();
        $post_data["siteid"] = $siteid;


        if(empty($data["content"]) && empty($data["reply_img_url"])){
            return false;
        }

        //定义要保存的字段名称数组
        $field_name_array = array("keyword", "content","type","sort","reply_img_url");

        foreach ($data as $key => $value) {
            if (!in_array($key, $field_name_array)) {
                continue;
            }
            //去除前后空格，过滤sql语句
            $post_data[$key] = trim($value);
        }


        if(isset($post_data["reply_img_url"])){
            $reply_img = $post_data["reply_img_url"];
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

        $type = $data["type"];

        switch ($type) {
            case 2:
                $search_arr["type"] = $type;
                $search_arr["siteid"] = session('idsite');
                $count = db('wx_reply')->where($search_arr)->count();
                if($count>=1 && $wx_replay_id<=0){
                    return false;
                }
                break;
            case 3:
                $search_arr["type"] = $type;
                $search_arr["siteid"] = session('idsite');
                $count = db('wx_reply')->where($search_arr)->count();
                if($count>=1 && $wx_replay_id<=0){
                    return false;
                }
                $post_data["keyword"] = "被关注自动回复";
//                $post_data["end_time"] = time() + (86400 * 365 * 20);  //被关注回复，时间设置为当前时间往后20年
                break;
        }

        if ($wx_replay_id > 0) {   //修改

            $bool = db('wx_reply')->where('wx_replay_id=:id', ['id' => $wx_replay_id])->update($post_data);
        } else {                   //新增

            $bool = db('wx_reply')->insert($post_data);
        }
        return $bool;
    }


    /**
     * 获取微信回复信息
     * @param $wx_replay_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_reply_info($wx_replay_id)
    {
        if ((int)$wx_replay_id <= 0 || (int)$wx_replay_id != $wx_replay_id) {
            return array("content"=>"","keyword"=>"","sort"=>"","reply_img_url"=>"");
        }

        $data = db('wx_reply')->where("wx_replay_id=".$wx_replay_id)->find();
        return $data;
    }

    /**
     * 删除回复
     */
    public function delete_reply($data){
        if(isset($data["wx_replay_id"])) {
            $wx_replay_id = $data["wx_replay_id"];
        }else{
            $wx_replay_id = 0;
        }

        if(empty($wx_replay_id) || (int)$wx_replay_id <=0){
            return false;
        }

        $rs = db('wx_reply')->where('wx_replay_id',$wx_replay_id)->delete();
        return $rs;
    }
}