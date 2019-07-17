<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:68:"D:\workspace\work\public/../application/admin\view\work\content.html";i:1561691688;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>角色管理</title>
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
              url:"<?php echo url('role/delchecked'); ?>",
              data:"id="+s,
              type:"post",
              dataType:"json",
              success:function(msg){
                  if (msg==1){
                      layer.alert('<?php echo lang('del success'); ?>', {icon: 1}, function(index){
                          location.reload();
                          $(".checked_one").attr("checked",false);
                          layer.close(index);
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
          <div class="oa_subnav clearfix">
            <div class="oa_subnav-tab clearfix">
              <ul>
                <li style="display: none"><a href="<?php echo url('Admin/work/index'); ?>"><em>分类管理</em></a></li>
                <?php if(is_array($book_list) || $book_list instanceof \think\Collection || $book_list instanceof \think\Paginator): if( count($book_list)==0 ) : echo "" ;else: foreach($book_list as $key=>$vo): ?>
                <li <?php if($vo['code'] == $request['bookcode']): ?>class="oa_on"<?php endif; ?>><em><a href="<?php echo url('Admin/work/content','bookcode='.$vo['code']); ?>"><?php echo $vo['name']; ?></a></em></li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
              </ul>
            </div>
          </div>
          <div class="oa_content-area clearfix">
            <div class="oa_content-main">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td valign="top">
                    <div class="oa_title clearfix">
                      <span class="oa_title-btn">
                        <ul>
                          <li class="oa_selected">
                            <?php if($cms->CheckPurview('bookcontent','add')){ ?>
                              <a href="javascript:CustomOpen('<?php echo url('Admin/work/contentdeal','action=add&id=0&bookcode='.$request['bookcode']); ?>','work','字典管理',450,300)">添加</a>
                            <?php } ?>
                          </li>
                        </ul>
                      </span>
                      <span class="oa_ico-left"></span>列表
                    </div>
                    <div class="oa_text-list">
                      <table id="table" width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                        <tr class="oa_text-list-title">
                          <th ><span class="oa_arr-text-list-title"></span>名称</th>
                          <th width="100" style="display: none;"><span class="oa_arr-text-list-title"></span>代号</th>
                          <th width="40"><span class="oa_arr-text-list-title"></span>排序</th>
                          <th width="60"><span class="oa_arr-text-list-title"></span>操作</th>
                        </tr>
                        <?php if(is_array($content_list) || $content_list instanceof \think\Collection || $content_list instanceof \think\Paginator): $i = 0; $__LIST__ = $content_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr>
                          <td style="padding-left:1em">
                            <?php echo $vo['name']; ?>
                          </td>
                          <td style="display: none;"><?php echo $vo['code']; ?></td>
                          <td><?php echo $vo['order']; ?></td>
                          <td>
                            <?php if($cms->CheckPurview('bookcontent','edit')){ ?>
                            <a href="javascript:CustomOpen('<?php echo url('Admin/work/contentdeal','action=edit&bookcode='.$request['bookcode'].'&id='.$vo['id']); ?>','work','字典管理',450,300)">修改</a>
                            <?php } if($cms->CheckPurview('bookcontent','del')){ ?>
                            <a href="javascript:;" onclick="javascript:del_checked('<?php echo url('admin/work/contentdel','bookcode='.$request['bookcode'].'&id='.$vo['id']); ?>', '<?php echo $vo['name']; ?>')" >删除</a>
                            <?php } ?>
                          </td>
                        </tr>
                        <?php if(array_key_exists('children',$vo)): if(is_array($vo['children']) || $vo['children'] instanceof \think\Collection || $vo['children'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                        <tr id="<?php echo $v['code']; ?>" class="<?php echo $v['level']; ?>_<?php echo $v['parentcode']; ?> <?php echo $v['parentpath']; ?><?php echo $v['code']; ?>" <?php if($v['level'] != 0): ?>style="display:none;"<?php endif; ?>>
                          <td><span><?php echo $v['id']; ?></span></td>
                          <td style="padding-left:<?php echo ($v['level'] * 3); ?>em">
                            <?php if($v['child'] != 0): ?>
                            <span class="<?php echo $v['level']; ?>_<?php echo $v['code']; ?>_show <?php echo $v['parentpath']; ?>_show _show" onclick="show_hidden('<?php echo $v['parentpath']; ?>',<?php echo $v['level']; ?>,'<?php echo $v['code']; ?>','show');">+</span>
                            <span class="<?php echo $v['level']; ?>_<?php echo $v['code']; ?>_hide <?php echo $v['parentpath']; ?>_hide _hide" onclick="show_hidden('<?php echo $v['parentpath']; ?>',<?php echo $v['level']; ?>,'<?php echo $v['code']; ?>','hide');" style="display: none">-</span>
                            <?php endif; ?>
                            <?php echo $v['name']; ?>
                          </td>
                          <td><?php echo $v['code']; ?></td>
                          <td><?php echo $v['order']; ?></td>
                          <td>
                            <?php if($cms->CheckPurview('bookcontent','add')){ if($v['nextlevel'] == 1): ?>
                            <a href="javascript:CustomOpen('<?php echo url('Admin/work/contentdeal','action=add&bookcode='.$request['bookcode'].'&parentcode='.$v['code']); ?>','work','字典管理',400,300)">添加</a>
                            <?php endif; } if($cms->CheckPurview('bookcontent','edit')){ ?>
                            <a href="javascript:CustomOpen('<?php echo url('Admin/work/contentdeal','action=edit&bookcode='.$request['bookcode'].'&code='.$v['code']); ?>','work','字典管理',400,300)">修改</a>
                            <?php } if($cms->CheckPurview('bookcontent','del')){ ?>
                            <a href="javascript:;" onclick="javascript:del_checked('<?php echo url('admin/work/contentdel','code='.$v['code']); ?>', '<?php echo $v['name']; ?>')" >删除</a>
                            <?php } ?>
                          </td>

                        </tr>
                        <?php if(array_key_exists('children',$v)): if(is_array($v['children']) || $v['children'] instanceof \think\Collection || $v['children'] instanceof \think\Paginator): $i = 0; $__LIST__ = $v['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?>
                        <tr id="<?php echo $v1['code']; ?>" class="<?php echo $v1['level']; ?>_<?php echo $v1['parentcode']; ?> <?php echo $v1['parentpath']; ?><?php echo $v1['code']; ?>" <?php if($v1['level'] != 0): ?>style="display:none;"<?php endif; ?>>
                          <td><span><?php echo $v1['id']; ?></span></td>
                          <td style="padding-left:<?php echo ($v1['level'] * 3); ?>em">
                            <?php if($v1['child'] != 0): ?>
                            <span class="<?php echo $v1['level']; ?>_<?php echo $v1['code']; ?>_show <?php echo $v1['parentpath']; ?>_show _show" onclick="show_hidden('<?php echo $v1['parentpath']; ?>',<?php echo $v1['level']; ?>,'<?php echo $v1['code']; ?>','show');">+</span>
                            <span class="<?php echo $v1['level']; ?>_<?php echo $v1['code']; ?>_hide <?php echo $v1['parentpath']; ?>_hide _hide" onclick="show_hidden('<?php echo $v1['parentpath']; ?>',<?php echo $v1['level']; ?>,'<?php echo $v1['code']; ?>','hide');" style="display: none">-</span>
                            <?php endif; ?>
                            <?php echo $v1['name']; ?>
                          </td>
                          <td><?php echo $v1['code']; ?></td>
                          <td><?php echo $v1['order']; ?></td>
                          <td>
                            <?php if($cms->CheckPurview('bookcontent','add')){ if($v1['nextlevel'] == 1): ?>
                            <a href="javascript:CustomOpen('<?php echo url('Admin/work/contentdeal','action=add&bookcode='.$request['bookcode'].'&parentcode='.$v1['code']); ?>','work','字典管理',400,300)">添加</a>
                            <?php endif; } if($cms->CheckPurview('bookcontent','edit')){ ?>
                            <a href="javascript:CustomOpen('<?php echo url('Admin/work/contentdeal','action=edit&bookcode='.$request['bookcode'].'&code='.$v1['code']); ?>','work','字典管理',400,300)">修改</a>
                            <?php } if($cms->CheckPurview('bookcontent','del')){ ?>
                            <a href="javascript:;" onclick="javascript:del_checked('<?php echo url('admin/work/contentdel','code='.$v1['code']); ?>', '<?php echo $v1['name']; ?>')" >删除</a>
                            <?php } ?>
                          </td>
                        </tr>
                        <?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                      </table>
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
<script type="text/javascript">
    function show_hidden(path,level,id,type){
        var val = level+1;
        if(type == 'show'){  //展开
            $("."+val+"_"+id).show();
            $("."+level+"_"+id+"_show").hide();
            $("."+level+"_"+id+"_hide").show();
        }else{ //收缩
            var aaa=$("#table tr");
            for(var i=0;i<aaa.length;i++){
                if(aaa.eq(i).attr("class").indexOf(path+id)!==-1){
                    if(!aaa.eq(i).hasClass(path+id)){
                        aaa.eq(i).hide();
                        aaa.eq(i).find("._show").show();
                        aaa.eq(i).find("._hide").hide();
                    }
                }
            }
            $("."+level+"_"+id+"_show").show();
            $("."+level+"_"+id+"_hide").hide();
        }

    }
    function del_checked(url,remark) {

        var msg='您确定要删除选定的记录吗？';
            msg='您确定要删除“'+remark+'”吗？';

        layer.confirm(msg, {
            btn: ['确定','取消'] //按钮
        }, function(){
            window.location=url;
        }, function(){

        });
    }

</script>
</body>
</html>