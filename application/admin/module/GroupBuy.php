<?php

namespace app\admin\module;

use think\Model;

class GroupBuy extends Model {

    const ENABLE = 1;
    const DISABLE = 0;


    public function getGroupBuyList($request = []) {
        $result = $this->where($request)
            ->field(
                [
                    'group_buy_id', //团购活动id
                    'cms_package.package_id',
                    'group_num', //成团人数
                    'start_at', //团购开始时间
                    'end_at', //团购截止时间
                    'group_buy_price', //团购价
                    'time_limit', //拼团有效期
                    'cms_group_buy.state', //团购活动状态
                    'group_buy_type', //团购类型
                    'rebate_rate', //返利比例
                    'cms_package.keyword1', //套餐关键字1
                    'cms_package.keyword2', //套餐关键字2
                    'cms_activity.chrtitle', //活动标题
                ]
            )
            ->join('package', 'cms_package.package_id = cms_group_buy.package_id', 'left')
            ->join('activity', 'cms_package.activity_id = cms_activity.idactivity', 'left')
        // ->fetchSql(true)
            ->select();
        var_dump($result);die;
    }

    public function switchState($groupBuyId, $siteId, $state) {
        $res = db('group_buy')->where(
                [
                    'group_buy_id' => $groupBuyId,
                    'site_id' => $siteId
                ]
            )
            // ->fetchSql(true)
            ->update(
                [
                    'state' => $state
                ]
            );
        if($res)
        {
            return ['status' => 'success', 'msg' => '修改成功'];
        }
        return ['status' => 'fail', 'msg' => '修改失败'];
    }


    public function deleteGroupBuy($groupBuyId, $siteId)
    {
        $orderNum = db('group_buy_order')->where(['group_buy_id' => $groupBuyId])->count();
        if($orderNum > 0)
        {
            return ['status' => 'fail', 'msg' => '不允许删除，已有订单生成'];
        }

        $res = db('group_buy')->where(
                [
                    'group_buy_id' => $groupBuyId,
                    'site_id' => $siteId
                ]
            )
            // ->fetchSql(true)
            ->delete();
            // var_dump($res);die;
        if($res)
        {
            return ['status' => 'success', 'msg' => '删除成功'];
        }
        return ['status' => 'fail', 'msg' => '删除失败'];
    }
}