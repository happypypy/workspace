<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 核心中文语言包
return [
    // 系统错误提示
    'Undefined variable'        => '未定义变量',
    'Undefined Index'           => '未定义数组索引',
    'Undefined offset'          => '未定义数组下标',
    'Parse error'               => '语法解析错误',
    'Type error'                => '类型错误',
    'Fatal error'               => '致命错误',
    'syntax error'              => '语法错误',
    
    //提示错误
    'del fail'                 => '删除失败',
    'del success'               => '删除成功',

    // 框架核心错误提示
    'dispatch type not support' => '不支持的调度类型',
    'method param miss'         => '方法参数错误',
    'method not exists'         => '方法不存在',
    'module not exists'         => '模块不存在',
    'controller not exists'     => '控制器不存在',
    'class not exists'          => '类不存在',
    'property not exists'       => '类的属性不存在',
    'template not exists'       => '模板文件不存在',
    'illegal controller name'   => '非法的控制器名称',
    'illegal action name'       => '非法的操作名称',
    'url suffix deny'           => '禁止的URL后缀访问',
    'Route Not Found'           => '当前访问路由未定义',
    'Underfined db type'        => '未定义数据库类型',
    'variable type error'       => '变量类型错误',
    'PSR-4 error'               => 'PSR-4 规范错误',
    'not support total'         => '简洁模式下不能获取数据总数',
    'not support last'          => '简洁模式下不能获取最后一页',
    'error session handler'     => '错误的SESSION处理器类',
    'not allow php tag'         => '模板不允许使用PHP语法',
    'not support'               => '不支持',
    'redisd master'             => 'Redisd 主服务器错误',
    'redisd slave'              => 'Redisd 从服务器错误',
    'must run at sae'           => '必须在SAE运行',
    'memcache init error'       => '未开通Memcache服务，请在SAE管理平台初始化Memcache服务',
    'KVDB init error'           => '没有初始化KVDB，请在SAE管理平台初始化KVDB服务',
    'fields not exists'         => '数据表字段不存在',
    'where express error'       => '查询表达式错误',
    'no data to update'         => '没有任何数据需要更新',
    'miss data to insert'       => '缺少需要写入的数据',
    'miss complex primary data' => '缺少复合主键数据',
    'miss update condition'     => '缺少更新条件',
    'pattern data Not Found'      => '模型数据不存在',
    'table data not Found'      => '表数据不存在',
    'delete without condition'  => '没有条件不会执行删除操作',
    'miss relation data'        => '缺少关联表数据',
    'tag attr must'             => '模板标签属性必须',
    'tag error'                 => '模板标签错误',
    'cache write error'         => '缓存写入失败',
    'sae mc write error'        => 'SAE mc 写入错误',
    'route name not exists'     => '路由标识不存在（或参数不够）',
    'invalid request'           => '非法请求',
    'bind attr has exists'      => '模型的属性已经存在',
    'relation data not exists'  => '关联数据不存在',
    'relation not support'      => '关联不支持',

    //头部的语言配置
    'help'                      => '帮助',
    'cancel'                    => '注销',
    'account set'               => '账户设置',

    //左边栏目语言配置
    'system set'                => '系统设置',
    'human resourse'            => '人力资源',
    'enter email'               =>'企业邮箱',
    'dispatch manage'           =>'发文管理',
    'position set'              =>'职位设置',
    'role manage'               =>'角色管理',

    //所有模块公共语言
    'lang'                      => '选择语言',
    'chinese'                   => '中文',
    'english'                   => '英文',
    'complex'                   => '繁体',
    'search'                    => '搜索',
    'status'                    => '状态',
    'serial'                    => '序列号',
    'describe'                  => '描述',
    'operation'                 => '操作',
    'check'                     => '查看',
    'delete'                    => '删除',
    'revise'                    => '修改',
    'remarks'                   => '备注',
    'return'                    => '返回',
    'page count'                => '总页数',
    'display page'              => '每页显示',
    'save'                      => '保存',
    'yes'                       => '是',
    'no'                        => '否',

    //帐号管理模块语言
    'account management'        => '帐号管理',
    'account'                   => '帐号',
    'account name'              => '姓名',
    'account list'              => '账号列表',
    'account id'                => '帐号ID',
    'add account'               => '添加帐号',
    'account log'               => '账号日志',
    'account password'          => '密码',
    'account editor'            => '帐号编辑',
    'account add'               => '帐号添加',
    'account check'             => '帐号查看',
    'lock'                      => '锁定',
    'not locked'                => '正常',
    'data empty'                => '您搜索的数据不存在',
    'role'                      => '角色',
    'choice role'               => '选择角色',
    'role set'                  => '角色设置',
    'role name'                 => '角色名称',

    //菜单管理模块：栏目管理
    'column manage'             => '栏目管理',
    'module manage'             => '标准模块',
    'column list'               => '栏目列表',
    'column name'               => '栏目名称',
    'system'                    => '系统',
    'column add'                => '新建栏目',
    'column code'               => '栏目代号',
    'picture path'              => '图片路径',

    //菜单管理模块：标准模块
    'module all'                => '标准模块',
    'module name'               => '模块名称',
    'extended_module name'      => '扩展模块',
    'belong column'             => '所属栏目',
    'selected module'           => '将选中模块转入',
    'add module'                => '新建模块',
    'module code'               => '模块代号',
    'module path'               => '模块路径',
    'column all'                => '全部栏目',
    'module add'                => '添加模块',
    'resource'                  => '资源',
    'choice column'             => '选择栏目',
    'column belong'             => '所属栏目',
    'module file'               => '模块目录',
    'module picture'            => '模块图片',
    'module list'               => '模块列表',


    //菜单管理模块：资源管理
    'resource list'             => '资源列表',
    'resource code'             => '资源代号',
    'resource add'              => '新建资源',
    'resource name'             => '资源名称',

    //菜单管理模块：操作管理
    'operate list'              => '操作列表',
    'operate code'              => '操作代号',
    'operate add'               => '添加操作',
    'operate name'              => '操作名称',
    'operate'                   => '操作',

    //角色管理
    'role id'                   => '角色ID',
    'role remark'               => '角色描述',
    'role add'                  => '添加角色',
    'purview set'               => '设置权限',
    'role purview'              => '角色权限设置',
    'role view'                 => '角色查看',
    'role edit'                 => '角色修改',
    'all module'                => '全选该模块',
    'role detail'               => '角色详情',

    
    
];
