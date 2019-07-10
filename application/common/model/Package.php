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
class Package extends MyQuery{
    
    public function optimisticLock()
    {
        return 'version';
    }

    public function stockName()
    {
        return 'stock';
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
        $package = db('package')->where(['package_id' => $packageId])->find();
        if(!$package)
        {
            return false;
        }
        $package['stock'] = Stock::getStock($package);
        return $package;
    }
}