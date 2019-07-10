<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:69:"D:\workspace\work\public/../application/admin\view\configs\index.html";i:1561691684;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
<link href="/static/css/page.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
<script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
<script type="text/javascript" src="/static/js/tabscommon.js"></script>
<script type="text/javascript" src="/static/js/cntotc.js"></script>
<script type="text/javascript" src="/static/js/del-checked.js"></script>
<script type="text/javascript">
$(function(){
	$(document).ready(function() {
		$('.oa_text-list tr').addClass('odd');
		$('.oa_text-list tr:even').addClass('even');
	});
    $('.oa_text-list tr').hover(
        function(){
            $(this).addClass('oa_mouseover-bg');
        },
        function(){
            $(this).removeClass('oa_mouseover-bg');
        }
    );
	});
</script>
</head>
<body>
<div class="oa_wrapper">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  	<tr class="oa_wrapper-display">
    	<td class="oa_wrapper-top-arr-left">&nbsp;</td>
      <td class="oa_wrapper-top-arr-middle"></td>
      <td class="oa_wrapper-top-arr-right">&nbsp;</td>
    </tr>
    <tr>
      <td class="oa_wrapper-middle-arr-left oa_wrapper-display"></td>
      <td class="oa_wrapper-middle-arr-middle">
      	<div class="oa_location clearfix"><span class="oa_ico-left"></span>location<span class="oa_ico-right"></span></div>
        <div class="oa_main clearfix">
        	<div class="oa_subnav clearfix" style="display: none;">

			  <div class="oa_subnav-tab clearfix">
             <ul>
                <li class="oa_on"><em><a href="<?php echo url('configs/index','idsite='.$idsite); ?>">网站配置</a></em></li>
                <li ><em><a href="<?php echo url('configs/configself','idsite='.$idsite); ?>">管理自定义配置</a> </em></li>
              </ul>
            </div>
            <div class="oa_subnav-list clearfix">
              <ul>
                <?php if(is_array($configmenu) || $configmenu instanceof \think\Collection || $configmenu instanceof \think\Paginator): $i = 0; $__LIST__ = $configmenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['isshow'] == 1): ?>
                <li><em><a href="<?php echo url('configs/index','menucode='.$vo['chrcode'].'&chrname='.$vo['chrname'].'&idsite='.$idsite); ?>"><?php echo $vo['chrname']; ?></a></em></li>
                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
              </ul>
            </div>
          </div>
          <div class="oa_content-area clearfix">
            <div class="oa_content-main">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td valign="top">
                    <div class="oa_pop">
                      <div class="oa_title clearfix">
                        <span class="oa_ico-right"></span>
                        <span class="oa_title-btn"></span>
                        <span class="oa_ico-left"></span>
                        <span><?php echo $menuname['chrname']; ?></span>
                        <?php if(is_array($lang) || $lang instanceof \think\Collection || $lang instanceof \think\Paginator): if( count($lang)==0 ) : echo "" ;else: foreach($lang as $k=>$vo): ?>
                        <a class="lang" lang="<?php echo $k; ?>"> <?php echo $vo; ?></a>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                      </div>
                      <div class="oa_pop-main">
                        <div class="oa_edition">
                          <form action="<?php echo url('configs/datapost'); ?>" enctype="multipart/form-data" method="post">
                            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition">
                              <?php if(is_array($lang) || $lang instanceof \think\Collection || $lang instanceof \think\Paginator): if( count($lang)==0 ) : echo "" ;else: foreach($lang as $k=>$v): $k=='cn'?$prefix='':$prefix=$k.'_'; if(is_array($menurule) || $menurule instanceof \think\Collection || $menurule instanceof \think\Paginator): $i = 0; $__LIST__ = $menurule;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['isshow'] == 1): ?>
                              <tr class="div_configs <?php echo $k; ?>" style="display: none">
                                <td width="240" class="oa_cell-left">
                                  <?php echo $vo['chrname']; if(in_array(($vo['fieldtype']), explode(',',"1,2,3"))): if($prefix == 'tc_'): ?>(繁体)<?php endif; if($prefix == 'en_'): ?>(英文)<?php endif; if($prefix == ''): endif; endif; ?>：
                                </td>
                                <td class="oa_cell-right">

                                  <?php if($vo['fieldtype'] == 10): ?>
                                  <input $style type="text"  value="<?php echo $vo['defaultval']; ?>"  name="<?php echo $vo['fieldname']; ?>" id="<?php echo $vo['fieldname']; ?>" class="form-control large" readonly="readonly"  style="width:200px;display:initial;"/>
                                  <input onclick="GetUploadify(1,'<?php echo $vo['fieldname']; ?>','admin','undefined','*.jpg;*.jpeg;*.png;*.gif;*.jpeg;');" type="button" value="上传图片"/>
                                  <input onclick="openimg('<?php echo $vo['fieldname']; ?>')" type="button" value="查看图片"/>
                                  <?php else: ?>
                                      <?php echo getControl($vo,$vo[$prefix.'defaultval'],$prefix); endif; if($vo['fieldname'] == 'stencildir'): ?>
                                  <input onclick="GetUploadify(1,'stencildir','admin','','*.html;');" value="选择" type="button">
                                  <?php endif; if(in_array(($vo['fieldtype']), explode(',',"1,2,3"))): if($prefix == 'tc_'): ?>
                                  <input type="button" onclick="convert('<?php echo $vo['fieldname']; ?>')" value="转成繁体" />
                                  <?php endif; endif; ?>
                                  <div style="color: #ff7070;padding: 2px;"><?php echo $vo['tips']; ?></div>
                                </td>
                              </tr>
                              <?php endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
                              <tr>
                                <td width="240" class="oa_cell-left">
                                  关注后自动回复文字
                                </td>
                                <td class="oa_cell-right">
                                  <span>注:插入链接请用以下标签  &lt;a href='地址'>文字内容&lt;/a></span>
                                  <!--<span>换行请在文字末尾加上  \n\n</span>-->
                                  <br/>
                                  <textarea id="reply_word" name="reply_word" is_null="1" cols="50" rows="3"  class="input oa_input-200"  style="width:800px;height:80px;"><?php echo $reply_word; ?></textarea>
                                </td>
                              </tr>
                              <tr>
                                <td width="240" class="oa_cell-left">
                                  关注后自动回复图片
                                </td>
                                <td class="oa_cell-right">
                                  <input name="reply_img" id="reply_img" type="text" value="<?php echo $reply_img; ?>"   chname=""   class="form-control "  style="width:800px;" />
                                  <input type="button" onclick="GetUploadify(1,'reply_img','admin','undefined','*.jpg;*.jpeg;*.png;*.gif;*.jpeg;');" value="上传图片" />
                                  <input onclick="openimg('reply_img')" type="button" value="查看图片"/>
                                </td>
                              </tr>
                            </table>
                            <div class="oa_bottom clearfix">
                              <div class="clearfix">
                                <div class="oa_op-btn clearfix">
                                  <input type="hidden" name="menucode" value="<?php echo $menuname['chrcode']; ?>">
                                  <input type="hidden" name="idsite" value="<?php echo $idsite; ?>"/>
                                  <input value="保存" type="submit" class="oa_input-submit" />
                                </div>
                              </div>
                              <div class="oa_bottom-bottom"><em></em></div>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </table>
              <div class="oa_content-main-bottom"></div>
            </div>
          </div>
        </div>
      </td>
      <td class="oa_wrapper-middle-arr-right oa_wrapper-display"></td>
    </tr>
    <tr class="oa_wrapper-display">
      <td class="oa_wrapper-bottom-arr-left">&nbsp;</td>
      <td class="oa_wrapper-bottom-arr-middle"></td>
      <td class="oa_wrapper-bottom-arr-right">&nbsp;</td>
    </tr>
  </table>
</div>
</body>
<script type="text/javascript">
    $(".lang").eq(0).css("color","#f00");
    var type = $(".lang").eq(0).attr("lang");
    $("table .div_configs").hide();
    $("."+type).show();
    $(".lang").click(function () {
        $(this).css("color","#f00");//给点击的a标签加红色
        $(this).siblings().css("color","#000");//被点击之外的a标签颜色为黑色
        var lang = $(this).attr("lang");
        $(".div_configs").hide();
        $("."+lang).show();
    });
</script>
</html>