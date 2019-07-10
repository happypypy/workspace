<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:87:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\work\contentdeal.html";i:1561691688;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
<link href="/static/css/page.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="oa_pop">
  <div class="oa_pop-main">
      <div style="height: 6px"></div>
      <div class="oa_title clearfix">
          <span class="oa_ico-right"></span>
          <span class="oa_title-btn"></span>
          <span class="oa_ico-left"></span>
          <?php if($content_info['id']){echo '修改';}else{echo '添加';}?>
      </div>
  	<div class="oa_edition">
        <form action="<?php echo url('Admin/work/contentpost',array('id'=>$request['id'])); ?>" method="post">
            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                <tr>
                    <td width="100" class="oa_cell-left"><span style="color: red;">*</span>&nbsp;名称：</td>
                    <td class="oa_cell-right">
                        <input type="text" name="name" value="<?php echo $content_info['name']; ?>" class="oa_input-300" />
                    </td>
                </tr>
                <tr style="display: none;">
                    <td class="oa_cell-left"><span style="color: red;">*</span>&nbsp;值：</td>
                    <td class="oa_cell-right">
                        <input type="text" name="code" value="<?php echo $content_info['code']; ?>" class="oa_input-100" />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">排序：</td>
                    <td class="oa_cell-right">
                        <input type="text" name="order" value="<?php echo $content_info['order']; ?>" class="oa_input-100" />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">备注：</td>
                    <td class="oa_cell-right">
                        <textarea name="remark" rows="4"   class="oa_textarea-300" ><?php echo $content_info['remark']; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td style="padding:10px;"><input type="submit" value="确定"></td>
                    <td>
                        <input type="hidden" name="action" value="<?php echo $request['action']; ?>" />
                        <?php if($request['parentcode'] != null): ?>
                        <input type="hidden" name="parentcode" value="<?php echo $request['parentcode']; ?>">
                        <?php endif; ?>
                        <input type="hidden" name="bookcode" value="<?php echo $request['bookcode']; ?>">
                    </td>

                </tr>
                <tr>

                </tr>
            </table>
        </form>
    </div>
  </div>
</div>
</body>
<script type="text/javascript">
    $("#tab tr td").on('click',"a",function(){

        $(this).parent().parent().remove();
    });
    $("#tab tr td a").click(function(){

    })
</script>
</html>