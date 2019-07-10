<?php
/*
 * @Descripttion: 系统更新模块
 * @version: 1.0
 * @Author: ChenJie
 * @Date: 2019-06-26 14:52:34
 * @LastEditors: ChenJie
 * @LastEditTime: 2019-06-27 14:51:05
 */

namespace app\admin\controller;
use app\admin\module\Newfeaturerecommend as newFeatureRecommendModel;

class Newfeaturerecommend extends Base{
    
    /**
     * 新功能推荐列表
     *
     * @return void
     * @author Chenjie
     * @Date 2019-06-26 14:52:34
     */
    public function index(){
        if($this->CMS->CheckPurview('newfeaturerecommendmanage') == false){
            $this->NoPurview();
        }

        $param = input('param.');

        $newFeatureRecommendModel = new newFeatureRecommendModel();
        $result = $newFeatureRecommendModel->index($param);

        $this->assign('param',$param);
        $this->assign('datalist',$result['datalist']);
        $this->assign('page',$result['page']);
        return $this->fetch();
    }
    /**
     * 新功能推荐新增/编辑
     *
     * @return void
     * @author Chenjie
     * @Date 2019-06-26 14:52:28
     */
    public function edit(){
        $param = input('param.');
        $id = isset($param['id']) ? $param['id'] : 0;
        $newFeatureRecommendModel = new newFeatureRecommendModel();

        if(request()->isPost()){
            if($this->CMS->CheckPurview('newfeaturerecommendmanage',$param['action']) == false){
                $this->NoPurview();
            }
            $result = $newFeatureRecommendModel->edit($param);
            if($result){
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }else{
                $this->success('操作失败');
            }
        }
        $datainfo = [];
        if($id){
           $datainfo = $newFeatureRecommendModel->getNewFeatureById($id);
        }
        $this->assign("datainfo",$datainfo);
        return $this->fetch();
    }
    /**
     * 新功能推荐删除
     *
     * @return void
     * @author Chenjie
     * @Date 2019-06-26 14:53:08
     */
    public function delete(){
        if($this->CMS->CheckPurview('newfeaturerecommendmanage','del') == false){
            $this->NoPurview();
        }
        $id = input('param.id');

        $newFeatureRecommendModel = new newFeatureRecommendModel();
        $result = $newFeatureRecommendModel->del($id);

        if($result){
            return 1;
        }else{
            return 0;
        }
    }
    /**
     * 新功能推荐页面
     *
     * @return void
     * @author Chenjie
     * @Date 2019-06-26 14:53:22
     */
    public function singlepage(){
        $id = input('param.id/d');
        $newFeatureRecommendModel = new newFeatureRecommendModel();
        $datainfo = $newFeatureRecommendModel->singlepage($id);

        $this->assign('datainfo',$datainfo);
        return $this->fetch();
    }
    /**
     * 新功能提醒页面
     *
     * @return void
     * @author Chenjie
     * @Date 2019-06-26 14:53:30
     */
    public function tip(){
        $id = input('param.id/d');
        $newFeatureRecommendModel = new newFeatureRecommendModel();
        $datainfo = $newFeatureRecommendModel->singlepage($id);

        if(!$id){
            session("newFeaturesRemind",null);
        }
        $this->assign('datainfo',$datainfo);
        return $this->fetch();
    }
}