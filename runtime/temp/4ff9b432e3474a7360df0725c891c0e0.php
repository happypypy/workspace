<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"D:\workspace\work\public/../application/admin\view\sitemanage\index.html";i:1562315963;}*/ ?>
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
                    <div class="oa_content-area clearfix">
                        <div class="oa_content-main">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top">
                                        <div class="oa_title clearfix">
                                            <span class="oa_title-btn">
                                                <ul>
                                                  <?php if($cms->CheckPurview('sitemanage','add')){ ?>
                                                  <li class="oa_selected">
                                                      <a href="javascript:CustomOpen('<?php echo url('sitemanage/sitedeal','&action=add',''); ?>', 'role','站点添加', 560,550)">添加站点</a>
                                                  </li>
                                                  <?php } ?>
                                                </ul>
                                            </span>
                                            <span class="oa_ico-left"></span>
                                            站点列表
                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center;"><input id="checked"  onclick="DoCheck();" type="checkbox" class="checkall" /></th>
                                                    <th width="45"><span class="oa_arr-text-list-title"></span>站点ID</th>
                                                    <th><span class="oa_arr-text-list-title"></span>站点名称</th>
                                                    <th width="100"><span class="oa_arr-text-list-title"></span>站点代号</th>
                                                    <th width="30"><span class="oa_arr-text-list-title"></span>排序</th>
                                                    <th width="60"><span class="oa_arr-text-list-title"></span>过期时间</th>
                                                    <th width="60"><span class="oa_arr-text-list-title"></span>是否启用</th>
                                                    <th width="170"><span class="oa_arr-text-list-title"></span>操作</th>
                                                </tr>
                                                <?php if(is_array($site_list) || $site_list instanceof \think\Collection || $site_list instanceof \think\Paginator): $i = 0; $__LIST__ = $site_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td align="center"><input class="checked_one" type="checkbox" value="<?php echo $vo['id']; ?>" /></td>
                                                    <td><span><?php echo $vo['id']; ?></span></td>
                                                    <td><?php echo $vo['site_name']; ?></td>
                                                    <td><?php echo $vo['site_code']; ?></td>
                                                    <td><?php echo $vo['order']; ?></td>
                                                    <td><span><?php  echo ($vo['expiretime']<time()?"<span style='color:red;'>".date('Y-m-d',$vo['expiretime'])."</span>":"<span >".date('Y-m-d',$vo['expiretime'])."</span>") ?></td>
                                                    <td><?php echo $vo['is_use']==1?"是":"否"; ?></td>
                                                    <td width="100">
                                                        <a href="<?php echo url('index/leftbar1','idsite='.$vo['id']); ?>" target="leftFrame">进入站点</a>
                                                        <a href="javascript:CustomOpen('<?php echo url('sitemanage/sitedeal','id='.$vo['id'].'&action=edit',''); ?>','role','站点修改',560,550)"><?php echo lang('revise'); ?></a>
                                                        <a onmousemove="javascript:show_rqcode(this,'<?php echo $vo['site_code']; ?>')" onmouseout="javascript:close_rqcode()" href="/<?php echo $vo['site_code']; ?>" target="_blank">浏览网页</a>
                                                        <br/>
                                                        <a href="<?php echo url('/weixin/createmune/'.$vo['site_code']); ?>" target="_blank">创建菜单</a>
                                                        <a href="javascript:init_wxtemplate('<?php echo $vo['id']; ?>')">初始化微信消息模版</a>
                                                        <br/>
                                                        <a href="<?php echo url('/admin/marketingpackage/my_marketing_package/id/'.$vo['id']); ?>">营销包</a>
                                                        <a href="javascript:CustomOpen('<?php echo url('sitemanage/get_wechat_list','id='.$vo['id'],''); ?>','getwechatlist','获取公众号用户',560,250)">获取公众号用户</a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                                <tr>
                                                    <td colspan="8" style="height:40px;"><a href="javascript:batch_wxtempty()">批量初始化微信消息模版</a></td>
                                                </tr>
                                            </table>
                                            <div class="oa_bottom clearfix">
                                                <div class="clearfix">
                                                    <div class="oa_page-controls">
                                                        <ul>
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
<div id="rqcode" style="display: none; height: 230px;width: 230px;background-color: #FFFFFF; border: solid 2px #000000; text-align: center;padding-top: 20px; ;position: absolute;margin-right: 150px" >

</div>

<script type="text/javascript">

    function show_rqcode(o,code)
    {
       var obj=$("#rqcode");
        obj.html("<img id='img_rqcode' src='/admin/Qrcode/siteurl/code/"+code+"' />");
        var yy=event.clientY-150;
        if(yy<10) {yy=10};

        $("#rqcode").css("top",yy+"px").css("left",event.clientX-300+"px");
        obj.show();
    }
    function  close_rqcode() {
        var obj=$("#rqcode");
        obj.html("");
        obj.hide();
    }

    function joinSite(site_id){
        $("#leftbar").remove();

        $.ajax({
            data:"idsite="+site_id,
            url:"<?php echo url('sitemanage/sitejoin'); ?>",
            success:function () {
                alert(1);
            },
            error:function () {
                alert(2);
            }
        });
    }

    /**
     * 初始化模版
     */
    function init_wxtemplate(idsite) {
        layer.confirm("更新项目所需的微信消息模版，请确保当前微信后台模版可用消息模版大于等于16条？", {
            btn: ['确定','取消'] //按钮
        }, function(){
            var url = "/admin/wxtemplate/init_wxtemplate";
            var layer_index = layer.load(1, {
                time: 0, //不自动关闭
                shade: [0.1,'#fff'] //0.1透明度的白色背景
            });
            $.ajax({
                url:url,
                type:"post",
                data:"idsite="+idsite,
                dataType:"json",
                success:function(obj){
                    layer.close(layer_index);
                    if (obj.status=="success"){
                        layer.alert(obj.msg, {icon: 1}, function(index){
                            layer.close(index);

                        });
                    }else{

                        layer.alert('操作失败', {icon: 2}, function(index){
                        });
                    }
                },
                error:function(msg){
                    layer.close(layer_index);
                    layer.alert('操作失败', {icon: 2}, function(index){
                        layer.close(index);
                    });
                }
            })
        });
    }
    /**
     * 批量初始化微信模板
     */
    function batch_wxtempty(){
        var checkArray = [];
        $('.checked_one').each(function (index, item) {
            if(item.checked){
                checkArray.push($(this).val());
            }
        });
        $.ajax({
            url:"/admin/wxtemplate/batch_wxtemplate",
            type:"post",
            dataType:"json",
            success:function(obj){
                var html = '<table style="padding:10px; text-align: center;" border="1" cellspacing="0" cellpadding="0" algin="center">';
                if(obj.fail_num > 0){
                    html += '<tr><th width="60" height="25">站点ID</th><th width="150">站点名称</th><th width="80">站点代号</th><th width="150">状态</th></tr>';
                    obj.errorData.forEach(item => {
                        html += '<tr><td height="25">'+ item.id +'</td><td>'+ item.site_name +'</td><td>'+ item.site_code +'</td><td>'+ item.msg +'</td></tr>'; 
                    });
                }
                html += '<tr><td colspan="4" height="35" align="left" style="color:red;padding:0 10px;">总站点数：'+obj.total_num+'&nbsp;&nbsp;初始化成功站点:'+obj.success_num+'&nbsp;&nbsp;初始化失败站点：'+ obj.fail_num +'</td></tr>'
                html += '</table>';
                layer.open({
                    type: 1,
                    title: false,
                    closeBtn: 0,
                    shadeClose: true,
                    area: ['300','auto'],
                    content: html
                });
            },
            error:function(msg){
                layer.alert('网络异常,操作失败', {icon: 2}, function(index){
                    layer.close(index);
                });
            }
        })
    }
</script>
</body>
</html>

