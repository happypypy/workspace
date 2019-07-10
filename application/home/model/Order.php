<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/3
 * Time: 10:59
 */

namespace app\home\model;
use think\Model;

class Order extends Model {
    
    public function getOrder($ordersn)
    {
        return $this->where(['ordersn' => $ordersn])->find();
    }
}