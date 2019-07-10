<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/21
 * Time: 15:03
 */

namespace app\common\model;
use think\Model;
use think\Page;
use think\Db;
use think\Session;
use think\exception\StaleObjectException;
use think\Exception;
use think\Log;
use think\db\MyQuery;

/**
 * 本类中所有方法，除返回正常参数外，出错时应抛出异常，在controller层回滚事务
 * 不在本类中做事务操作，防止事务嵌套导致的奇怪结果
 * 因为父类的乐观锁机制，在本类的实例中使用fetchSql(true)，乐观锁开启时，会产生查询操作
 */
class Stock extends Model
{
    /**
     * 获取套餐库存
     * @author Hlt
     * @DateTime 2019-05-10T10:23:16+0800
     * @param    array                   $package       套餐数据
     * @return   integer                                套餐库存
     */
    public static function getStock($package)
    {
        if($package['package_sum'] > 0)
        {
            return $package['package_sum'] - $package['sold'];
        }

        $activity = db('activity')
            ->field(
                [
                    'sold',
                    'intsignnum'
                ]
            )
            ->find($package['activity_id']);
        return $activity['intsignnum'] == 0 ? INF : ($activity['intsignnum'] - $activity['sold']);
    }

}