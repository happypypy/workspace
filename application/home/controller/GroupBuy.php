<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use app\home\model\GroupBuy as GroupBuyModel;

class GroupBuy extends Controller {

    const ACTIVE = 1;
    const INACTIVE = 0;


    public function __construct(Request $request = null)
    {
        // 调用父类构造函数
        parent::__construct($request);

        $this->siteId = session('idsite');
        if(!$this->siteId)
        {
            return json_encode(['status' => 'fail', 'msg' => '请登录后重试']);
        }
        $this->model = new GroupBuyModel;
    }

}