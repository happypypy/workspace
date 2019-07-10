<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/21
 * Time: 14:56
 */

namespace app\admin\module;
use think\Model;


class Work extends Model {

    //分类列表
    public function index($data){
        $book_list = db('work_book')->where('idsite=0 or idsite='.session('idsite'))->order('order asc')->select();
        $result['data'] = $book_list;
        return $result;

    }

    //分类处理
    public function book_deal($data){
        if($data['action'] == 'add'){
            $book_info = [];
            $book_info['code'] = '';
            $book_info['name'] = '';
            $book_info['lower'] = 2;
            $book_info['isshow'] = 1;
            $book_info['order'] = 1;
        }else{
            $book_info = db('work_book')->where('code',$data['bookcode'])->find();
        }
        return $book_info;
    }

    //分类提交
    public function book_post($data){
        $arr = [];
        $arr['code'] = $data['code'];
        $arr['name'] = $data['name'];
        $arr['isshow'] = $data['isshow'];
        $arr['order'] = $data['order'];
        $arr['idsite'] = session('idsite');
        if($data['action'] == 'add'){
            $bool = db('work_book')->insert($arr);
        }else{
            $bool = db('work_book')->where('code',$data['code'])->update($arr);
        }
        return $bool;
    }

    //content 列表
    public function content($data){

        //获取某个字典分类下的内容
        $map = [];

        //获取所有显示的字典分类
        $book_list= db('work_book')->where('(idsite=0 or idsite='.session('idsite').') and isshow=1')->order('order')->select();
        $book_info=$book_list[0];
        if(empty( $data['bookcode']))
            $data['bookcode']=$book_info['code'];

        $map['idsite'] =session('idsite');
        $map['bookcode'] = $data['bookcode'];
        $tree = db('work_content')->where($map)->order('order')->select();


        $result['book_info'] = $book_info;
        $result['content_list'] = $tree;
        $result['book_list'] = $book_list;
          return $result;
    }

    //content 处理跳转页面
    public function content_deal($data){

        if($data['action'] == 'add'){
            $content_info = [];
            $content_info['id'] = 0;
            $content_info['code'] = '';
            $content_info['name'] = '';
            $content_info['nextlevel'] = 2;
            $content_info['bookcode'] = $data['bookcode'];
            $content_info['order'] = '';
            $content_info['remark'] = '';
        }else{
            $map['id'] = $data['id'];
            $map['idsite'] = session('idsite');
            $content_info = db('work_content')->where($map)->find();
        }
        return $content_info;
    }

    //content 数据提交
    public function content_post($data){
        $arr = [];
        $arr['child'] = 0;
        $arr['code'] = $data['code'];
        $arr['name'] = $data['name'];
        $arr['order'] = $data['order'];
        $arr['remark'] = $data['remark'];
        //$arr['nextlevel'] = $data['nextlevel'];
        preg_match_all('/./us', $data['name'], $match);
        $length = isset($match[0])?count($match[0]):0;
        if($length>10){
            return false;
        }
        if($data['action'] == 'add'){
            $arr['bookcode'] = $data['bookcode'];
            $arr['idsite'] = session('idsite');
            $id = db('work_content')->insertGetId($arr);// insert($arr);
            $bool=db('work_content')->where(array('id'=>$id,'idsite'=>session('idsite')) )->update(array('code'=>$id));
        }else{
            $bool = db('work_content')->where(array('id'=>$data['id'],'idsite'=>session('idsite')) )->update($arr);
        }
        return $bool;
    }

    //内容删除
    public function content_del($data){
        $bool = db('work_content')->where(array('id'=>$data['id'],'idsite'=>session('idsite')) )->delete();
        return $bool;
    }

    /**
     * 获得指定分类下的子分类的数组
     * @access  public
     * @param   int     $no_node_id     要排除的栏目ID

     * @return  mix
     */
    public function work_content_list($no_node_id = 0,$book_code )
    {
        global $cms_node_g, $cms_node2_g; //申明全局变量

        $sql = "SELECT * FROM  cms_work_content WHERE parentcode = ".$book_code." ORDER BY idorder ASC ";

        $cms_node_g = db('work_content')->query($sql); //讲数据集赋给全局变量

        $cms_node_g = convert_arr_key($cms_node_g, 'id');

        foreach ($cms_node_g AS $key => $value)
        {
            if($no_node_id == $value['id'])
                continue;
            if($value['level'] == 0)
            {
                $this->get_cat_tree($no_node_id,$value['id']);
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
            if($no_node_id==$value['id'])
                continue;
            if($value['parentcode'] == $id)
            {
                $this->get_cat_tree($no_node_id,$value['id']);
                $cms_node2_g[$id]['have_son'] = 1; // 还有下级
            }
        }
    }


}