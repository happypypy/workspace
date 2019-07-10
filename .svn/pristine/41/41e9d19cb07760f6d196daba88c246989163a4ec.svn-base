<?php

namespace app\admin\module;

use think\Model;
use think\Page;
use think\Db;
use think\Session;
use think\exception\StaleObjectException;
use think\Exception;
use think\Log;


class Package extends \app\common\model\Package{
    public function updatePackage($data)
    {
        $sql = $this->where(['package_id' => $data['package_id']])->update($data);
    }


    public function deletePackage($activityId, $packageId)
    {
        $order = db('order')->where(['dataid' => $activityId])->find();
        //已发布活动不允许删除套餐
        if($order)
        {
            throw new Exception('已报名活动不允许删除套餐');
        }

        $packageCount = db('package')->where(['activity_id' => $activityId,'state' => 1])->count();
        //如果有效套餐数已小于等于1，拒绝删除
        if($packageCount <= 1)
        {
            throw new Exception('线上下单活动必须至少有一个套餐');
        }

        //删除
        db('package')->where(['package_id' => $packageId])->update(['state' => 0]);
    }


    public function addPackage($packages)
    {
        foreach ($packages as $k => $v) {
            $v['expire_at'] = strtotime($v['expire_at']);
            $v['activity_id'] = $activityId;
            $v['sold'] = 0;
            $v['original_price'] = $v['original_price'] ? : 0;
            $v['cost_price'] = $v['cost_price'] ? : 0;

            $groupBuy = $v['group_buy'];
            unset($v['group_buy']);

            //插入package表，获取主键
            $packageId = db('package')->insert($v, false, true);
            
        }
        // $packageId = db('package')->insertAll($data['packages']);
    }
}