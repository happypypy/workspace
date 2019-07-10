<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\advert\index.html";i:1561691688;}*/ ?>
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
<script type="text/javascript" src="/static/js/del-checked.js"></script>
  <script type="text/javascript" src="/static/js/layer/layer.js"></script>
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

function empty() {
  window.location.reload();
}


//删除选中
function del_checked() {

  var b = $(".checked_one");
  var s = '';
  for(var i=0;i<b.length;i++){
      if(b[i].checked){
          s+= b[i].value+',';
      }
  }
  s = s.substr(0, s.length - 1);
  $.ajax({
      url:"<?php echo url('advert/advdel'); ?>",
      data:"id="+s,
      type:"post",
      dataType:"json",
      success:function(msg){
          $(".checked_one").removeAttr("checked");
          layer.alert('<?php echo lang('del success'); ?>', {icon: 1}, function(index){
              location.reload();
              layer.close(index);
          });

      }
  })
}
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
          <div class="oa_subnav clearfix">
            <div class="oa_subnav-tab clearfix">
              <?php if($idsite==1) { ?>
              <ul>
                <li class="oa_on"><em><a href="<?php echo url('advert/index','idsite='.$idsite); ?>">广告列表</a></em></li>
                <li ><em><a href="<?php echo url('advert/advposition','idsite='.$idsite); ?>">广告位置</a></em></li>
              </ul>
              <?php } ?>
            </div>
          </div>
          <div class="oa_content-area clearfix">
            <div class="oa_content-main">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td valign="top">
                    <div class="oa_title clearfix">
                      <span class="oa_ico-right"></span>
                      <span class="oa_ico-left"></span>
                      <?php echo lang('search'); ?>
                    </div>
                    <div class="oa_search-area clearfix">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>
                            <div class="oa_search-type clearfix">
                              <form action="<?php echo url('advert/index'); ?>" method="post" id="form1">
                                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                  <tr>
                                    <td width="100" class="oa_cell-left">广告位置：</td>
                                    <td class="oa_cell-right">
                                      <select name="pid" style="width: 200px">
                                        <option value="">请选择</option>
                                        <?php if(is_array($ad_position) || $ad_position instanceof \think\Collection || $ad_position instanceof \think\Paginator): $i = 0; $__LIST__ = $ad_position;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                        <option value="<?php echo $vo['position_id']; ?>" <?php if($search['pid'] == $vo['position_id']): ?>selected<?php endif; ?>><?php echo $vo['position_name']; ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                      </select>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td class="oa_cell-left">广告名称：</td>
                                    <td class="oa_cell-right"><input id="ad_name" name="ad_name" type="text" value="<?php echo $search['ad_name']; ?>" class="oa_search-input" /></td>
                                  </tr>
                                  <tr>
                                    <td height="30"></td>
                                    <td class="oa_cell-right"><input  name="" type="submit" value="<?php echo lang('search'); ?>" class="oa_search-btn" /></td>
                                  </tr>
                                  <tr>
                                    <input type="hidden" name="idsite" value="<?php echo $idsite; ?>" />
                                  </tr>
                                </table>
                              </form>
                            </div>
                          </td>
                        </tr>
                      </table>
                    </div>
                    <div class="oa_title clearfix">
                      <span class="oa_ico-right"></span>
                      <span class="oa_title-btn">
                        <ul>
                          <?php if($cms->CheckPurview('admanage','add')){ ?>
                          <li class="oa_selected">
                            <a href="javascript:CustomOpen('<?php echo url('advert/advdeal','action=add&idsite='.$idsite,''); ?>', 'advert','添加广告', 600, 400)">添加广告</a>
                          </li>
                          <?php } ?>
                        </ul>
                      </span>
                      <span class="oa_ico-left"></span>
                      广告列表
                    </div>
                    <div class="oa_text-list" id="ajaxpage">
                      <table id="table" width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                        <tr class="oa_text-list-title">
                          <th width="20" style="text-align:center;"><input id="checked"  onclick="DoCheck();" name="" type="checkbox"/></th>
                          <th width="50"><span class="oa_arr-text-list-title"></span>广告ID</th>
                          <th width="140"><span class="oa_arr-text-list-title"></span>广告位置</th>
                          <th><span class="oa_arr-text-list-title"></span>广告名称</th>
                          <th><span class="oa_arr-text-list-title"></span>广告图片</th>
                          <th width="60"><span class="oa_arr-text-list-title"></span>开始时间</th>
                          <th width="60"><span class="oa_arr-text-list-title"></span>结束时间</th>
                          <th width="40" style="display: none"><span class="oa_arr-text-list-title"></span>新窗口</th>
                          <th width="30"><span class="oa_arr-text-list-title"></span>显示</th>
                          <th width="30"><span class="oa_arr-text-list-title"></span>排序</th>
                          <th width="90"><span class="oa_arr-text-list-title"></span><?php echo lang('operation'); ?></th>
                        </tr>
                            <?php if($ad_list == null): ?><tr><td colspan="10"><?php echo lang('data empty'); ?></td></tr><?php else: if(is_array($ad_list) || $ad_list instanceof \think\Collection || $ad_list instanceof \think\Paginator): $i = 0; $__LIST__ = $ad_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;$showColor = ""; if(strtotime($vo["end_time"]) < time()){ $showColor = "style='color:red';";} ?>
                        <tr class="tr" <?php echo $showColor; ?>>
                              <td align="center"><input class="checked_one" type="checkbox" value="<?php echo $vo['ad_id']; ?>" /></td>
                              <td><span><?php echo $vo['ad_id']; ?></span></td>
                              <td><?php echo $vo['position_name']; ?></td>
                              <td><?php echo $vo['ad_name']; ?></td>
                              <td><img src="<?php echo $vo['ad_code']; ?>" width="160"  alt="" style="margin: 5px"/></td>
                              <td><?php echo $vo['start_time']; ?></td>
                              <td><?php echo $vo['end_time']; ?></td>
                              <td style="display: none"><?php echo $vo['target']==1?"是":"否"; ?></td>
                              <td><?php echo $vo['enabled']==1?"是":"否"; ?></td>
                              <td><?php echo $vo['orderby']; ?></td>
                              <td>
                                <?php if($cms->CheckPurview('admanage','view')){ ?>
                                <a href="<?php echo $vo['ad_code']; ?>" target="_blank">查看</a>
                                <?php } if($cms->CheckPurview('admanage','edit')){ ?>
                                <a href="javascript:CustomOpen('<?php echo url('advert/advdeal','id='.$vo['ad_id'].'&action=edit&idsite='.$idsite); ?>', 'advert','广告编辑', 600, 400)">编辑</a>
                                <?php } if($cms->CheckPurview('admanage','del')){?>
                                <a onclick="javascript:if (confirm('确定删除吗？')) { return true;}else{return false;};" href='<?php echo url('advert/advdel','id='.$vo['ad_id'],''); ?>'>删除</a>
                                <?php } ?>
                              </td>
                          </tr>
                        <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                      </table>
                      <div class="oa_bottom clearfix">
                        <div class="clearfix">
                            <div class="oa_op-btn clearfix">
                                <input value="<?php echo lang('delete'); ?>" onclick="javascript:if (confirm('确定删除吗？')) { del_checked();}else{return false;};" type="button" class="oa_input-submit" />
                            </div>
                            <div class="oa_page-controls">
                              <ul>
                                <li></li>
                                <li><?php echo $page->show(); ?></li>
                              </ul>
                            </div>
                        </div>
                        <div class="oa_bottom-bottom"><em></em></div>
                      </div>
                        <style type="text/css">
                          a{
                            cursor: pointer;
                          }
                            .pagination{
                                display: inline;
                                font-size: 14px;
                                letter-spacing: 4px;
                                font-family: "宋体", Gadget, sans-serif;
                            }
                        </style>
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

<!--加载语言JS-->

</html>