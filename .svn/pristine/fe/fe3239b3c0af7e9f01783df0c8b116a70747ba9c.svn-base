<?php

namespace app\home\model;

use think\Model;
use think\Exception;
use think\Log;
use think\Db;

class Package extends Model{
   
    // //用于生成package类实例（有库存|库存不限）
    // public static function Instance($activityId, $packageId)
    // {
    //     $package = $this->table('package')
    //         ->where(
    //             [
    //                 'package_id' => $packageId,
    //                 'activity_id' => $activityId
    //             ]
    //         )
    //         // ->field('stock')
    //         ->find();
    //     if($package['package_sum'] > 0)
    //     {
    //         return new unlimitedPackage();
    //     }else
    //     {
    //         return new limitedPackage();
    //     }
    // }

    public function getStock($activityId, $packageId)
    {
        $package = db('package')
            ->where(
                [
                    'package_id' => $packageId,
                    'activity_id' => $activityId
                ]
            )
            // ->field('stock')
            ->find();
        if($package)
        {
            return \app\common\model\Stock::getStock($package);
        }

        return 0;
    }

    /**
     * 获取套餐数据
     * @author Hlt
     * @DateTime 2019-05-10T10:22:22+0800
     * @param    integer                   $packageId   套餐id
     * @return   array                                  套餐内容
     */
    public function getPackage($packageId)
    {
        $package = db('package')->where(
                [
                    'package_id' => $packageId,
                    'expire_at' => ['egt', strtotime('-1 day')]
                ]
            )
            ->find();
        if(!$package)
        {
            return false;
        }
        $package['stock'] = \app\common\model\Stock::getStock($package);
        return $package;
    }
}