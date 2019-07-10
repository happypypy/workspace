<?php
namespace app\admin\controller;
use app\admin\module\Marketingpackage as MarketingpackageModel;
use think\Exception;

class Marketingpackage  extends Base {

    // 营销包管理
    public function index(){
        if($this->CMS->CheckPurview('marketingpackagemanage')==false){
            $this->NoPurview();
        }
        $search = input('param.');
        $search['marketing_package_code'] = isset($search['marketing_package_code']) ? $search['marketing_package_code'] : "";
        $search['marketing_package_name'] = isset($search['marketing_package_name']) ? $search['marketing_package_name'] : "";
        $search['p'] = isset($search['p']) ? intval($search['p']) : 1;

        $marketingpackagemodel = new MarketingpackageModel();
        $result = $marketingpackagemodel->index($search);

        $this->assign("page",$result['page']);
        $this->assign("data",$result['data']);
        $this->assign("search",$search);
        return $this->fetch();
    }

    // 添加和编辑营销包
    public function modi(){
        $postData = input('param.');
        $id = isset($postData['id']) ? intval($postData['id']) : 0;
        $datainfo = [];

        $marketingpackagemodel = new MarketingpackageModel();
        if(request()->isPost()){
            if($this->CMS->CheckPurview('marketingpackagemanage',$postData['action'])==false){
                $this->NoPurview();
            }

            try{
                $marketingpackagemodel->modi($postData);
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }catch(Exception $e){
                $this->error('操作失败');
            }
        }
        
        if($id){
            $datainfo = $marketingpackagemodel->deal($id);
        }

        $this->assign('datainfo',$datainfo);
        return $this->fetch();
    }

    // 删除营销包
    public function del(){
        if($this->CMS->CheckPurview('marketingpackagemanage','del')==false){
            $this->NoPurview();
        }

        $id = input('param.id');
        $marketingpackagemodel = new MarketingpackageModel();
        $result = $marketingpackagemodel->del($id);

        if($result){
            return 1;
        }else{
            return 0;
        }
    }

    // 设置套餐
    public function setmeal(){
        if($this->CMS->CheckPurview('marketingpackagemanage','setmeal')==false){
            $this->NoPurview();
        }
        $param = input('param.');
        $id = isset($param['id']) ? $param['id'] : 0;
        
        $marketingpackagemodel = new MarketingpackageModel();

        // 保存套餐
        if(request()->isPost()){
            $moduleid = isset($param['moduleid']) ? $param['moduleid'] : '';
            $moduleids = "";
            if($moduleid){
                foreach($moduleid as $value){
                    $moduleids .= $value.',';
                }
                $moduleids = rtrim($moduleids, ",");
            }

            try{
                $marketingpackagemodel->setmeal($id, $moduleids);
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            }catch(Exception $e){
                $this->error('操作失败');
            }
        }

        // 扩展模型
        $extended_module_list = $marketingpackagemodel->get_extended_module();

        // 套餐模型
        $marketing_package = $marketingpackagemodel->deal($id);
        $module_ids = explode(',', $marketing_package['module_id']);

        $this->assign('id', $id);
        $this->assign('module_ids', $module_ids);
        $this->assign('extended_module_list', $extended_module_list);
        return $this->fetch();
    }

    // 我的营销包
    public function my_marketing_package(){
        if($this->CMS->CheckPurview('sitemanage','setmeal')==false){
            $this->NoPurview();
        }
        $search = input('param.');
        $siteid = $search['id'];
        $search['marketing_package_code'] = isset($search['marketing_package_code']) ? $search['marketing_package_code'] : "";
        $search['marketing_package_name'] = isset($search['marketing_package_name']) ? $search['marketing_package_name'] : "";
        $search['p'] = isset($search['p']) ? intval($search['p']) : 1;

        $marketingpackagemodel = new MarketingpackageModel();
        $result = $marketingpackagemodel->get_my_marketing_package($search);

        $this->assign("page", $result['page']);
        $this->assign("data", $result['data']);
        $this->assign("search", $search);
        $this->assign("siteid", $siteid);
        return $this->fetch();
    }

    // 营销包开关
    public function marketing_package_switch(){
        if($this->CMS->CheckPurview('sitemanage','switch')==false){
            $this->NoPurview();
        }
        $param = input('param.');
        $state = isset($param['state']) ? $param['state'] : 'use';
        $siteid = isset($param['siteid']) ? intval($param['siteid']) : 0;
        $marketing_package_id = isset($param['id']) ? intval($param['id']) : 0;
        $marketing_package_code = isset($param['code']) ? $param['code'] : 0;
        $result = false;

        try{
            // 营销包开通
            if($state == 'use'){
                $result = db('marketing_package_record')->where(['siteid'=>$siteid,'marketing_package_id'=>$marketing_package_id])->find();
                if($result){
                    $map = [
                        'siteid' => $siteid,
                        'marketing_package_id' => $marketing_package_id,
                    ];
                    $map2 = [
                        'idsite' => $siteid,
                        'marketing_package_id' => $marketing_package_id,
                    ];
                    db('marketing_package_record')->where($map)->delete();
                    db('module')->where($map2)->delete();
                    $result = true;
                }else{
                    $data = [
                        'siteid' => $siteid,
                        'marketing_package_code' => $marketing_package_code,
                        'marketing_package_id' => $marketing_package_id,
                        'create_time' => time()
                    ];
    
                    db('marketing_package_record')->insert($data);
    
                    $moduleids = db('marketing_package')->where('id',$marketing_package_id)->value("module_id");
                    $extended_module = db('extended_module')->whereIn('idmodule',$moduleids)->select();
                    foreach($extended_module as $value){
                        $value['idmodule'] = 0;
                        $value['idsite'] = $siteid;
                        $value['marketing_package_id'] = $marketing_package_id;
                        db('module')->insert($value);
                    }

                    $result = true;
                }
            }
            // 营销包关闭
            else{
                $map = [
                    'siteid' => $siteid,
                    'marketing_package_id' => $marketing_package_id,
                ];
                $map2 = [
                    'idsite' => $siteid,
                    'marketing_package_id' => $marketing_package_id,
                ];
                db('marketing_package_record')->where($map)->delete();
                db('module')->where($map2)->delete();
                $result =  true;
            }

            $marketing_package_name = db('marketing_package')->where('id',$marketing_package_id)->value('marketing_package_name');
            // 写入日志
            $data2 = [
                'idaccount' => session('AccountID'),
                'chrname' => session('UserName'),
                'siteid' => $siteid,
                'marketing_package_id' => $marketing_package_id,
                'marketing_package_name' => $marketing_package_name,
                'state' => $state,
                'ip' => getip(),
                'create_time' => time()
            ];
            db('marketing_package_log')->insert($data2);
        }catch(Exception $e){
            $result = false;
        }


        if($result){
            $this->success('操作成功',url('/admin/marketingpackage/my_marketing_package/',['id' => $siteid]));
        }else{
            $this->error('操作失败');
        }
    }
}