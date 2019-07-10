<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:94:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\newfeaturerecommend\tip.html";i:1561691688;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>新功能推荐</title>
  <style>
    *{
      margin: 0;
      padding: 0;
    }
    body{
      background: #f1f1f1;
      padding: 20px;
    }
    .new-container{
      padding: 10px 20px;
      background: #fff;
      font-size: 14px;
      border: 1px solid #c9c9c9;
      height:20px;
    }
    .new-container .title{
      font-size: 24px;
      line-height: 40px;
      font-weight: bold;
      text-align: center;
      border-bottom: 1px solid #c9c9c9;
      color: #333;
	  padding-bottom:10px;
    }
    .new-container .published{
      text-align: center;
      font-size: 12px;
      color: #666;
      line-height: 30px;
      border-bottom: 1px solid #c9c9c9;
    }
    .new-container .content{
		margin:10px 0;
		line-height:180%;
    }
  </style>
</head>
<body>
  <div class="new-container">
        <div style="float:left;">
            <a href="<?php echo url('newfeaturerecommend/singlepage'); ?>" target="_blank" style="text-decoration: none"><?php echo $datainfo['title']; ?></a>
        </div>
        <div style="float: right;">
            <?php echo !empty($datainfo['update_time'])?date("Y-m-d", $datainfo['update_time']) : date("Y-m-d", $datainfo['create_time']); ?>
        </div>
  </div>
</body>
</html>