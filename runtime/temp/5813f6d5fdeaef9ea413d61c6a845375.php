<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\filter\index.html";i:1561691688;}*/ ?>
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
function del_checked(url) {

  var b = $(".checked_one");
  var s = '';
  for(var i=0;i<b.length;i++){
      if(b[i].checked){
          s+= b[i].value+',';
      }
  }
  s = s.substr(0, s.length - 1);
  $.ajax({
      url:url,
      data:"id="+s,
      type:"post",
      dataType:"json",
      success:function(msg){
          $(".checked_one").removeAttr("checked");
          if (msg==1){
              layer.alert('<?php echo lang('del success'); ?>', {icon: 1}, function(index){
                  layer.close(index);
                  location.reload();
              });
          }else{
              layer.alert('<?php echo lang('del fail'); ?>', {icon: 2}, function(index){
                  layer.close(index);
                  location.reload();
              });
          }

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
                              <form action="<?php echo url('filter/index'); ?>" method="post" id="form1">
                                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                  <tr>
                                    <td width="100" class="oa_cell-left">敏感词：</td>
                                    <td class="oa_cell-right">
                                      <input type="text" name="content" value="<?php echo $data['content']; ?>">
                                    </td>
                                  </tr>
                                  <tr>
                                    <td height="30">
                                      <input type="hidden" name="idsite" value="<?php echo $idsite; ?>" />
                                    </td>
                                    <td class="oa_cell-right"><input  name="" type="submit" value="<?php echo lang('search'); ?>" class="oa_search-btn" /></td>
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
                          <?php if($cms->CheckPurview('filtermanage','add')){ ?>
                          <li class="oa_selected">
                            <a href="javascript:CustomOpen('<?php echo url('filter/filterdeal','&action=add&idsite='.$idsite); ?>', 'filter','添加敏感词', 400, 260)">添加敏感词</a>
                          </li>
                          <?php } ?>
                        </ul>
                      </span>
                      <span class="oa_ico-left"></span>
                      敏感词列表
                    </div>
                    <div class="oa_text-list" id="ajaxpage">
                      <table id="table" width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                        <tr class="oa_text-list-title">
                          <th width="20" style="text-align:center;"><input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" /></th>
                          <th width="40"><span class="oa_arr-text-list-title"></span>ID</th>
                          <th><span class="oa_arr-text-list-title"></span>敏感词</th>
                          <th><span class="oa_arr-text-list-title"></span>替换为</th>
                          <th width="60"><span class="oa_arr-text-list-title"></span>是否开启</th>
                          <th width="40"><span class="oa_arr-text-list-title"></span>排序</th>
                          <th width="90"><span class="oa_arr-text-list-title"></span>操作</th>
                        </tr>
                        <?php if(is_array($filter_list) || $filter_list instanceof \think\Collection || $filter_list instanceof \think\Paginator): $i = 0; $__LIST__ = $filter_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr>
                          <td align="center"><input class="checked_one" type="checkbox" value="<?php echo $vo['filterid']; ?>" /></td>
                          <td><?php echo $vo['filterid']; ?></td>
                          <td><?php echo $vo['content']; ?></td>
                          <td><?php echo $vo['replace']; ?></td>
                          <td><?php echo $vo['isusing']==1?"是":"否"; ?></td>
                          <td><?php echo $vo['idorder']; ?></td>
                          <td>
                            <a href="javascript:CustomOpen('<?php echo url('filter/filterdeal','id='.$vo['filterid'].'&action=view',''); ?>', 'account','查看', 400, 240)">查看</a>
                            <?php if($cms->CheckPurview('filtermanage','edit')){ ?>
                            <a href="javascript:CustomOpen('<?php echo url('filter/filterdeal','id='.$vo['filterid'].'&action=edit&idsite='.$idsite); ?>', 'account','修改', 400, 240)">修改</a>
                            <?php } if($cms->CheckPurview('filtermanage','del')){ ?>
                            <a onclick="javascript:if (confirm('确定删除吗？')) { return true;}else{return false;};"   href="<?php echo url('filter/filterdel','id='.$vo['filterid']); ?>">删除</a>
                            <?php } ?>
                          </td>
                        </tr>
                        <?php endforeach; endif; else: echo "" ;endif; ?>

                      </table>
                      <div class="oa_bottom clearfix">
                        <div class="clearfix">
                            <div class="oa_op-btn clearfix">
                                <input value="<?php echo lang('delete'); ?>" onclick="javascript:if (confirm('确定删除吗？')) { del_checked('<?php echo url('filter/delchecked'); ?>');}else{return false;};" type="button" class="oa_input-submit" />
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