<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:85:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\advert\advdeal.html";i:1561691688;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
<link href="/static/css/page.css?1" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="/static/js/del-checked.js"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
    <script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
</head>
<body>
<div class="oa_pop">
  <div class="oa_pop-main">
      <div style="height: 6px"></div>
      <div class="oa_title clearfix">
          <span class="oa_ico-right"></span>
          <span class="oa_title-btn"></span>
          <span class="oa_ico-left"></span>
          <?php if($ad_info['action'] == 'edit'): ?>广告编辑<?php else: ?>广告添加<?php endif; if(is_array($lang) || $lang instanceof \think\Collection || $lang instanceof \think\Paginator): if( count($lang)==0 ) : echo "" ;else: foreach($lang as $k=>$vo): ?>
          <a class="lang" lang="<?php echo $k; ?>"> <?php echo $vo; ?></a>
          <?php endforeach; endif; else: echo "" ;endif; ?>
      </div>
  	<div class="oa_edition">
        <form action="<?php echo url('admin/advert/advpost'); ?>" method="post">
            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition"  style="border-bottom: #e0e0e0 solid 1px">
                <?php if(is_array($lang) || $lang instanceof \think\Collection || $lang instanceof \think\Paginator): if( count($lang)==0 ) : echo "" ;else: foreach($lang as $k1=>$v1): $k1=='cn'?$prefix='':$prefix=$k1.'_'; ?>
                <tr class="<?php echo $k1; ?> tr">
                    <?php $name = $prefix.'ad_name'; ?>
                    <td width="160" class="oa_cell-left"><span style="color: red;">*</span>&nbsp;广告名称<?php if($v1!=''){ ?> (<?php echo $v1; ?>)<?php }?></if> ：</td>
                    <td class="oa_cell-right">
                        <input type="text" name="<?php echo $name; ?>" value="<?php echo $ad_info[$name]; ?>" class="oa_input-200" />
                    </td>
                </tr>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                <tr style="display: none">
                    <td class="oa_cell-left"><span style="color: red;">*</span>&nbsp;广告类型：</td>
                    <td class="oa_cell-right">
                        <select name="media_type" class="oa_input-100">
                            <option value="1" <?php if($ad_info['media_type'] == 1): ?>selected<?php endif; ?>>图片</option>
                            <!--
                            <option value="2" <?php if($ad_info['media_type'] == 2): ?>selected<?php endif; ?>>flash</option>
                            <option value="3" <?php if($ad_info['media_type'] == 3): ?>selected<?php endif; ?>>文字</option>
                            -->
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">
                        <!--<span style="color: red;">*</span>-->
                        &nbsp;广告链接：</td>
                    <td class="oa_cell-right">
                        <input name="ad_link" type="text" value="<?php echo $ad_info['ad_link']; ?>" class="oa_input-200" />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left"><span style="color: red;">*</span>&nbsp;广告位置：</td>
                    <td class="oa_cell-right">
                        <select name="pid" id="pid" class="oa_input-200">
                            <?php if(is_array($ad_position) || $ad_position instanceof \think\Collection || $ad_position instanceof \think\Paginator): $i = 0; $__LIST__ = $ad_position;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo $vo['position_id']; ?>" width="<?php echo $vo['ad_width']; ?>" height="<?php echo $vo['ad_height']; ?>" <?php if($vo['position_id'] == $ad_info['pid']): ?>selected<?php endif; ?>><?php echo $vo['position_name']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">开始日期：</td>
                    <td class="oa_cell-right">
                        <input name="start_time" type="date" value="<?php echo $ad_info['start_time']; ?>" class="oa_input-200" />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">结束日期：</td>
                    <td class="oa_cell-right">
                        <input name="end_time" type="date" value="<?php echo $ad_info['end_time']; ?>" class="oa_input-200" />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left"><span style="color: red;">*</span>&nbsp;广告图片：</td>
                    <td class="oa_cell-right">
                        <!--<input type="button" onclick="GetUploadify(1,'ad_code','ad','changeimg','*.jpg;*.jpeg;*.png;*.gif;*.jpeg;');" value="上传图片" />-->
                        <!--<input  id="ad_code" name="ad_code" type="hidden" class="oa_input-200" value="<?php echo $ad_info['ad_code']; ?>" />-->
                        <!--<div id="showimg_ad"><?php if(!empty($ad_info['ad_code'])) { ?><img  src="<?php echo $ad_info['ad_code']; ?>" height="100" /><?php } ?></div>-->

                        <input name="ad_code" id="ad_code" type="text" value="<?php echo $ad_info['ad_code']; ?>"  class="form-control " />
                        <input style="display: none;" onclick="GetUploadify(1,'ad_code','ad','changeimg','*.jpg;*.jpeg;*.png;*.gif;*.jpeg;');" type="button" value="上传单图片"/>
                        <input onclick="imgcut()" type="button" value="上传单图片"/>
                        <input onclick="openimg('ad_code')" type="button" value="查看图片"/>
                    </td>
                </tr>
                <tr style="display: none;">
                    <td class="oa_cell-left">是否新窗口：</td>
                    <td class="oa_cell-right">
                        <input name="target" type="radio" value="1" <?php if($ad_info['target'] == 1): ?>checked<?php endif; ?> />是&nbsp;&nbsp;&nbsp;
                        <input name="target" type="radio" value="2" <?php if($ad_info['target'] == 2): ?>checked<?php endif; ?> />否
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">是否显示：</td>
                    <td class="oa_cell-right">
                        <input name="enabled" type="radio" value="1" <?php if($ad_info['enabled'] == 1): ?>checked<?php endif; ?> />是&nbsp;&nbsp;&nbsp;
                        <input name="enabled" type="radio" value="2" <?php if($ad_info['enabled'] == 2): ?>checked<?php endif; ?> />否
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">默认排序：</td>
                    <td class="oa_cell-right">
                        <input type="text" name="orderby" value="<?php echo $ad_info['orderby']; ?>" class="oa_input-200" />
                    </td>
                </tr>

                <tr><td style="padding:10px;"><input type="submit" value="确定"></td></tr>
                <tr>
                    <td><input type="hidden" name="action" value="<?php echo $ad_info['action']; ?>"/></td>
                    <td>
                        <?php if($ad_info['action'] == 'edit'): ?>
                        <input type="hidden" name="ad_id" value="<?php echo $ad_info['ad_id']; ?>"/>
                        <?php endif; ?>
                        <input type="hidden" name="idsite" value="<?php echo $ad_info['idsite']; ?>" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
  </div>

</div>

</body>
<script type="text/javascript">
    
    function imgcut() {
        var index = $("#pid").get(0).selectedIndex;
        var width = $("#pid").find("option").eq(index).attr("width");
        var height = $("#pid").find("option").eq(index).attr("height");
        uploadimgcut('ad_code','admin',width,height);
    }
    
        function changeimg(p) {
            $("#ad_code").val(p);
            $("#showimg_ad").html('<img src="'+p+'" height="100" />')
        }

    $("#tab tr td").on('click',"a",function(){

        $(this).parent().parent().remove();
    });
    $("#tab tr td a").click(function(){

    });
</script>
<script type="text/javascript">

    $(".lang").eq(0).css("color","#f00");
    var obj = $(".lang").eq(0).attr("lang");
    $("table .tr").hide();
    $("."+obj).show();
    $(".lang").click(function () {
        var lang = $(this).attr("lang");
        $(this).css("color","#f00");
        $(this).siblings().css("color","#000");
        $(".tr").hide();
        $(".public").show();
        $("."+lang).show();
    });

</script>
</html>