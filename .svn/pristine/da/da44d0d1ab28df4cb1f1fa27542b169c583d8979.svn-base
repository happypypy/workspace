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
 * Date:2018/06/17 */

namespace app\admin\controller;
use PHPExcel;
use PHPExcel_IOFactory;
use think\Exception;
use think\Request;
use think\wx\Utils\HttpCurl;

class Album extends Basesite {

    /**
     * 相册列表
     * @return mixed
     */
	public function index() {
		if ($this->CMS->CheckPurview('album_manage','view') == false) {
			$this->error('无权限');
		}
		$request = Request::instance()->param();
		$obj = new \app\admin\module\Album(session('idsite'));

		//其中参数中具有节点id
		$arr = $obj->index($request);
		$data = $arr['data'];
		$page = $arr['pager'];

		$this->assign('search', $arr['search']);
		$this->assign('page', $page);
		$this->assign('data', $data);
		$this->assign('sitecode', getSiteCode(session('idsite')));
		return $this->fetch();
	}

    /**
     * 相册添加，修改，查看跳转页面
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function modi(){
        $obj = new \app\admin\module\Album(session('idsite'));
        $data = Request::instance()->param();
        if($this->CMS->CheckPurview('album_manage',$data['action'])==false){
            $this->NoPurview();
        }
//        echo session("UserName");exit;
        if (Request::instance()->isPost()) {
            $bool = $obj->postData($data);
            if($bool['success'] !== false){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->error("操作失败，{$bool['message']}");
            }
            exit();
        }
        $result = $obj->deal($data);
        $this->assign('datainfo',$result);
        return $this->fetch();
    }

    /**
     * 相册删除
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        if($this->CMS->CheckPurview('album_manage','delete')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Album(session('idsite'));
        $result = $obj->del($request);
        return json($result);
    }

    /**
     * 图片列表
     * @return mixed
     */
    public function photo_list() {
        if ($this->CMS->CheckPurview('photo_manage','view') == false) {
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Album(session('idsite'));

        //其中参数中具有节点id
        $arr = $obj->photo_list($request);
        $data = $arr['data'];
        $page = $arr['pager'];

        $this->assign('search', $arr['search']);
        $this->assign('page', $page);
        $this->assign('data', $data);
        $this->assign('album_id', $request['album_id']);//相册id
        $this->assign('sitecode', getSiteCode(session('idsite')));
        return $this->fetch();
    }

    /**
     * 上传图片和添加图片的页面
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function add_photo(){
        $obj = new \app\admin\module\Album(session('idsite'));
        $data = Request::instance()->param();
        if($this->CMS->CheckPurview('photo_manage','add_photo')==false){
            $this->NoPurview();
        }
//        echo session("UserName");exit;
        if (Request::instance()->isPost()) {
            $bool = $obj->postAddPhoto($data);
            if($bool){
                return json(['code'=>0,'message'=>'执行成功']);
            }else{
                return json(['code'=>-1,'message'=>'执行失败']);
            }
        }
        $this->assign('album_id', $data['album_id']);//相册id
        return $this->fetch();
    }

    /**
     * 暂时假删除图片
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function false_del_photo()
    {
        if($this->CMS->CheckPurview('photo_manage','delete_photo')==false){
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Album(session('idsite'));
        $bool = $obj->false_del_photo($request);
        if($bool){
            return 1;
        }else{
            return -1;
        }
    }

    /**
     * 暂时假删除图片
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del_photo()
    {
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Album(session('idsite'));
        $bool = $obj->del_photo($request);
        if($bool){
            return 1;
        }else{
            return -1;
        }
    }

    /**
     * 图片评论列表列表
     * @return mixed
     */
    public function comment_list() {
        if ($this->CMS->CheckPurview('photo_manage','photo_comment') == false) {
            $this->error('无权限');
        }
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Album(session('idsite'));

        //其中参数中具有节点id
        $arr = $obj->comment_list($request);
//        halt($arr);
        $data = $arr['data'];
        $page = $arr['pager'];

        $this->assign('search', $arr['search']);
        $this->assign('page', $page);
        $this->assign('data', $data);
        $this->assign('album_id', $request['album_id']);//相册id
        $this->assign('sitecode', getSiteCode(session('idsite')));
        return $this->fetch();
    }

    /**
     * 回复评论
     * @return mixed
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function reply()
    {
        //commentmanage
        if ($this->CMS->CheckPurview('photo_manage','reply_comment') == false) {
            $this->NoPurview();
        }
        $request = Request::instance()->param();
        $id = $request['id'];
        $row = [];
        //        $row['recontent']="";
        // var_dump($id, Request::instance()->isPost());die;
        if ($id > 0) {
            $idsite = session('idsite');
            if (Request::instance()->isPost()) {
                //如果是回复的话
                if (empty($request['recontent']) && array_key_exists('sub1', $request)) {
                    $this->error('回复内容不能为空');
                }
                $arr = [];
                $arr['reply_content'] = $request['recontent'];
                $arr['reply_name'] = session('UserName');
                $arr['account_id'] = session('AccountID');
                $arr['reply_time'] = date('Y-m-d H:i:s',time());
                $bool = db('album_comment')->where(array('id' => $id, 'site_id' => $idsite))->update($arr);
                // $bool = true;
                if ($bool) {
                    $this->success('操作成功', PUBLIC_URL . 'postsuccess.html');
                } else {
                    $this->error('操作失败');
                }
            }
            $row = db('album_comment')->where(array('id' => $id, 'site_id' => $idsite))->find();
        }
        $this->assign('info', $row);
        return $this->fetch();
    }
}