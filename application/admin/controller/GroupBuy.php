<?php

namespace app\admin\controller;

use app\admin\module\GroupBuy as GroupBuyModel;
use think\Request;

class GroupBuy extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->siteId = session('idsite');
        $this->model = new GroupBuyModel;
    }

    public function switchState()
    {
        $request = Request::instance();
        $param = $request->param();
        if($request->isAjax() || 1)
        {
            $result = $this->model->switchState($param['group_buy_id'], $this->siteId, $param['state'] === 'true' ? GroupBuyModel::ENABLE : GroupBuyModel::DISABLE);
            return json($result);
        }
    }


    public function deleteGroupBuy()
    {
        $request = Request::instance();
        $param = $request->param();
        if($request->isAjax() || 1)
        {
            $result = $this->model->deleteGroupBuy($param['group_buy_id'], $this->siteId);
            return json($result);
        }
    }



    public function index()
    {
        echo 'Hello World!';
    }
}
