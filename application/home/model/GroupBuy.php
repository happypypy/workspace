<?php

namespace app\home\model;

use think\Model;

class GroupBuy extends Model {

    const ALLOW_REFUND = 1;
    const DISALLOW_REFUND = 0;


    // public function switch($groupBuyId, $state, $siteId)
    // {
    //     $groupBuy = db('group_buy')->where(
    //             [
    //                 'group_buy_id' => $groupBuyId,
    //                 'site_id' => $siteId
    //             ]
    //         )
    //         ->find();
    //     if(!$groupBuy)
    //     {
    //         return ['status' => 'fail', 'msg' => '修改失败，拼团已不存在，请关闭弹窗后重试'];
    //     }

    //     if($groupBuy['state'] == $state)
    //     {
    //         return ['status' => 'success', 'msg' => '修改成功'];
    //     }

    //     $res = db('group_buy')->where(
    //             [
    //                 'group_buy_id' => $groupBuyId,
    //                 'site_id' => $siteId
    //             ]
    //         )
    //         ->update(
    //             [
    //                 'state' => $state
    //             ]
    //         );
    //     if($res)
    //     {
    //         return ['status' => 'success', 'msg' => '修改成功'];
    //     }else
    //     {
    //         return ['status' => 'fail', 'msg' => '修改失败'];
    //     }
    // }



    public static function getList($siteId, $page = 1, $pageSize = 3)
    {
        $time = time();
        if($pageSize==3){
            $where= [
                'site_id' => $siteId,
                'state' => 1,
                'show_on_homepage' => 1,
                'start_at' => ['elt', $time],
                'end_at' => ['egt', $time],
            ];
        }else{
            $where=[
                'site_id' => $siteId,
                'state' => 1,
                'start_at' => ['elt', $time],
                'end_at' => ['egt', $time],
            ];
        }
        $groupBuys = db('group_buy')
            ->where($where)
            ->page($page, $pageSize)
            // ->fetchSql(true)
            ->select();

        foreach ($groupBuys as $key => $groupBuy)
        {
            $package = db('package')->where([
                    'package_id' => $groupBuy['package_id']
                ])
                ->field([
                    'activity_id',
                    'member_price'
                ])
                ->find();
            $activity = db('activity')->where([
                        'idactivity' => $package['activity_id']
                ])
                ->field([
                    'minage',
                    'maxage',
                    'chrtitle',
                ])
                ->find();


            $activity['member_price'] = $package['member_price'];
            $activity['activity_id'] = $package['activity_id'];
            $groupBuys[$key] = array_merge($groupBuy, $activity);
        }
        return $groupBuys;
    }
}