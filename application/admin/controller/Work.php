<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/20
 * Time: 11:45
 */

namespace app\admin\controller;
use think\Request;

class Work extends Base {

    public function index(){
        //分类管理
        if($this->CMS->CheckPurview('bookcate','view')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Work;
        $result = $obj->index($request);

        $this->assign('book_list',$result['data']);
        return $this->fetch();
    }

    //分类删除
    public function bookdel(){
        if($this->CMS->CheckPurview('bookcate','del')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();

        $bool = db('work_book')->where('code='.$request['bookcode'])->delete();
        //删除分类，删除分类下的所有内容
        db('work_content')->where('bookcode='.$request['bookcode'])->delete();
        if($bool){
            $this->success('删除失败');
        }else{
            $this->error('删除失败');
        }
    }

    //分类处理
    public function bookdeal(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('bookcate',$request['action'])==false){
            $this->error('无权限');
        }
        $obj = new \app\admin\module\Work;
        $result = $obj->book_deal($request);
        $this->assign('book_info',$result);
        $this->assign('request',$request);
        return $this->fetch();
    }

    //分类提交
    public function bookpost(){
        $request = Request::instance()->param();

        if(empty($request['name']) || empty($request['code'])) {
            return $this->error('前面带*号的为必填项');
        }
        if($this->CMS->CheckPurview('bookcate',$request['action'])==false){
            $this->error('无权限');
        }
        $obj = new \app\admin\module\Work;
        $bool = $obj->book_post($request);
        if($bool !== false){
            $this->success("操作成功",PUBLIC_URL.'postsuccess.html');
        }else{
            $this->error("操作失败");
        }
    }


    // 字典内容列表
    public function content(){
        if($this->CMS->CheckPurview('bookcontent','view')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Work;
        $result = $obj->content($request);
        $book_info = $result['book_info'];
        $content_list = $result['content_list'];
        $book_list = $result['book_list'];

        if(empty($request['bookcode']))
        {
            if(!empty($book_list))
            {
                $request['bookcode'] =$book_list[0]['code'];
            }
        }

        $this->assign('book_info',$book_info);
        $this->assign('request',$request);
        $this->assign('content_list',$content_list);
        $this->assign('book_list',$book_list);
        return $this->fetch();
    }


    //字典内容处理
    public function contentdeal(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('bookcontent',$request['action'])==false){
            $this->error('无权限');
        }
        $obj = new \app\admin\module\Work;

        if(array_key_exists('parentcode',$request) == false){
            $request['parentcode'] = '';
        }
        $result = $obj->content_deal($request);
        $this->assign('content_info',$result);
        $this->assign('request',$request);
        return $this->fetch();

    }

    //字典数据提交
    public function contentpost(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('bookcontent',$request['action'])==false){
            $this->error('无权限');
        }

        if(empty($request['name'])) {
            return $this->error('前面带*号的为必填项');
        }
        $obj = new \app\admin\module\Work;
        $bool = $obj->content_post($request);
        if($bool !== false){
            $this->success("操作成功",PUBLIC_URL."postsuccess.html");
        }else{
            $this->error("操作失败");
        }

    }

    //字典数据删除
    public function contentdel(){
        $request = Request::instance()->param();
        if($this->CMS->CheckPurview('bookcontent','del')==false){
            $this->error('无权限');
        }
        $obj = new \app\admin\module\Work;
        $bool = $obj->content_del($request);
        if($bool){
            $this->success("删除成功",url("Admin/work/content",["bookcode"=>$request['bookcode']]));
        }else{
            $this->error("删除失败");
        }
    }
}




