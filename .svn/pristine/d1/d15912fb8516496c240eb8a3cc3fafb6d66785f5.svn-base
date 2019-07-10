<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/21
 * Time: 15:03
 */

namespace think\db;

use think\Config;
use think\Db;
use think\db\Connection;
use think\exception\StaleObjectException;

abstract class MyQuery extends Query
{
    protected $tableName;
    /**
     * 构造函数
     * @access public
     * @param Connection $connection 数据库对象实例
     * @param Model      $model      模型对象
     */
    public function __construct(Connection $connection = null, $model = null)
    {
        $this->connection = $connection ?: Db::connect(Config::get('database'), true);
        $this->prefix     = $this->connection->getConfig('prefix');
        $this->model      = $model;
        // 设置当前连接的Builder对象
        $this->setBuilder();

        $this->class = get_called_class();
        if (empty($this->tableName)) {
            // 当前模型名
            $tableName       = str_replace('\\', '/', $this->class);
            $this->tableName = basename($tableName);
            if (Config::get('class_suffix')) {
                $suffix     = basename(dirname($tableName));
                $this->tableName = substr($this->tableName, 0, -strlen($suffix));
            }
        }
        $this->tableName = strtolower( $this->tableName );
        $this->init();
    }
    /**
     * 乐观锁
     * @author Hlt
     * @DateTime 2019-05-07T09:57:06+0800
     * @return   null|string                   返回null（表示无乐观锁）或者string(乐观锁版本号字段)
     */
    public function optimisticLock()
    {
        return null;
    }


    public function init()
    {
        $this->name($this->tableName);
        /**
         * 注册更新前事件，在记录有更新时
         */
        static::event('before_update', function($query)
        {
            $lock = $query->optimisticLock();
            //开启了乐观锁且数据有更新
            if($lock !== null)
            {

                $tmpQuery = (new static)->setTable($query->getTable());
                //使用该更新语句的条件作为查询条件
                if(isset($query->options['where']))
                {
                    $tmpQuery->options['where'] = $query->options['where'];
                }
                //查询乐观锁版本字段
                $data = $tmpQuery->field($lock)->find();

                //条件增加版本号，版本号自增
                // var_dump([$lock => $data[$lock]]);die;
                $query->where([$lock => $data[$lock]]);
                $query->data([$lock => $data[$lock] + 1]);
            }

            return true;
        });

        static::event('before_delete', function($query)
        {
            $lock = $query->optimisticLock();
            //开启了乐观锁且数据有更新
            if($lock !== null)
            {
                $data = clone($query)->fetchSql(false)->field($lock)->find();
                //增加删除条件--版本号
                $query->where([$lock => $data[$lock]]);
            }
            return true;
        });
    }


    //不使用after事件，而是重写并调用父类方法，主要是为了获取父类方法的返回值
    /**
     * 更新记录
     * @access public
     * @param mixed $data 数据
     * @return integer|string
     * @throws Exception
     * @throws PDOException
     */
    public function update(array $data = [])
    {
        //触发更新前事件
        $this->trigger('before_update', $this);
        // sleep(5);
        $result = parent::update($data);
        //如果开启了乐观锁 且 更新数据量为0 则表示数据已过时
        
        // var_dump($this->optimisticLock(), $result);die;
        if($result === 0 && $this->optimisticLock())
        {
            throw new StaleObjectException('数据已过时，请刷新后重试', [], $this->getLastSql());
        }

        //其他情况
        return $result;
    }


    /**
     * 删除当前的记录
     * @access public
     * @return integer
     */
    public function delete($data = null)
    {
        //触发删除前事件
        $this->trigger('before_delete', $this);
        $result = parent::delete($data);

        // 如果开启了乐观锁 且删除0条记录 则数据已过时或不存在
        if($this->optimisticLock() && $result === 0)
        {
            throw new StaleObjectException('数据已过时或不存在，请刷新后重试', [], $this->getLastSql());
        }

        return $result;
    }



}
