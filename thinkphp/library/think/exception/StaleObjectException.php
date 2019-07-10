<?php
// +----------------------------------------------------------------------
// | Author: hlt
// +----------------------------------------------------------------------
// | Description: 过时的类提醒，主要用于锁冲突
// +----------------------------------------------------------------------

namespace think\exception;

use think\exception\DbException;

/**
 * Database数据过期相关异常处理类
 */
class StaleObjectException extends DbException
{
    public function __construct($message, array $config, $sql, $code = 301)
    {
        parent::__construct($message, $config, $sql, $code);
    }
}