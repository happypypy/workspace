<?php
return array(
    //默认错误跳转对应的模版文件
    'TMPL_ACTION_ERROR' => 'public:dispatch_jump',
    //默认成功跳转对应的模版文件
    'TMPL_ACTION_SUCCESS' => 'public:dispatch_jump'

    //'VIEW_PATH'       =>'./application/admin/', // 改变某个模块的模版文件目录
    //'DEFAULT_THEME'   =>'View2', // 模版名称
    //'DATA_BACKUP_PATH'	=> 'public/upload/sqldata/', //数据库备份根路径
    //'DATA_BACKUP_PART_SIZE'	=> 20971520, //数据库备份卷大小
    //'DATA_BACKUP_COMPRESS'	=> 0, //数据库备份文件是否启用压缩
    //'DATA_BACKUP_COMPRESS_LEVEL' => 9 //数据库备份文件压缩级别

    ,
    'admin_lang_list' => ['cn'=>'中文','en'=>'英文','tc'=>'繁体'],
    'sysadmin'=>'admin',
    'no_down_domain' => ['mmbiz.qpic.cn']

);