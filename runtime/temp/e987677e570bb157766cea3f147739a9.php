<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"D:\workspace\work\public/../application/admin\view\node\visitlist.html";i:1565322263;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>内容访问数据</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
    <script type="text/javascript" src="/static/js/tabscommon.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <script type="text/javascript" src="/static/js/del-checked.js"></script>
    <link rel="stylesheet" type="text/css" href="/static/js/daterangepicker/daterangepicker-bs3.css" />
    <script type="text/javascript" src="/static/js/daterangepicker/moment.min.js"></script>
    <script type="text/javascript" src="/static/js/daterangepicker/daterangepicker.js"></script>

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
        //全选
        function DoCheck(){
            if($("#checked").is(':checked')){
                $(".checked_one").attr("checked", true);
            }else{
                $(".checked_one").attr("checked", false);
            }
        }

        function del_checked(value,remark) {

            var msg='您确定要删除选定的记录吗？';
            if(value>0) {
                msg='您确定要删除“'+remark+'”吗？';
            }

            layer.confirm(msg, {
                btn: ['确定','取消'] //按钮
            }, function(){
                getid(value);
            }, function(){

            });
        }
        var openids="";
        //删除选中
        function getid(value) {

            var b = $(".checked_one");
            var s = '';
            if(value=="")
            {
                for (var i = 0; i < b.length; i++) {
                    if (b[i].checked) {
                        s += b[i].value + ',';
                    }
                }
                if(s!="")
                    s = s.substr(0, s.length - 1);
            }
            else
            {
                s=value;
            }

            return s;
        }

        function send_msg(id) {
            var v=getid(id);
            if(v=="")
            {
                alert("请选择要发送信息的用户")
                return;
            }
            openids=v;
            CustomOpen('<?php echo url('node/sendmsg','dataid='.$dataid,''); ?>','sendmsg','信息发送',700,400);
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
                                        <div class="oa_search-area clearfix">
                                        </div>
                                        <div class="oa_title clearfix">
                                            <span class="oa_ico-left"></span>
                                            资讯访问数据
                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0"  style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center;"><input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" /></th>
                                                    <th WIDTH="100">户用名称</th>
                                                    <th WIDTH="100">微信昵称</th>
                                                    <th WIDTH="60">是否关注</th>
                                                    <th WIDTH="100">联系电话</th>
                                                    <th WIDTH="105">地区</th>
                                                    <th>地址</th>
                                                    <th WIDTH="125">首次阅读时间</th>
                                                    <th WIDTH="125">最后阅读时间</th>
                                                    <th WIDTH="60">阅读次数</th>
                                                    <th WIDTH="70">阅读时长</th>
                                                    <th WIDTH="60">是否收藏</th>
                                                    <th WIDTH="60">文章转发</th>
                                                    <th width="50"><span class="oa_arr-text-list-title"></span>操作</th>
                                                </tr>
                                                <?php if(empty($data)) { ?>
                                                <tr>
                                                    <td colspan="13" style="height: 50px;">当前没有访问记录</td>
                                                </tr>
                                                <?php } if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td align="center"><input class="checked_one" type="checkbox" name="idactivity[]" value="<?php echo $vo['openid']; ?>" /></td>
                                                    <td><?php echo $vo['chrname']; ?></td>
                                                    <td><?php echo $vo['nickname']; ?></td>
                                                    <td><?php echo $vo['intstate']; ?></td>
                                                    <td><?php echo $vo['chrtel']; ?></td>
                                                    <td><?php echo $vo['intcity']; ?></td>
                                                    <td  title="<?php echo $vo['chraddress']; ?>"><?php echo $vo['chraddress']; ?></td>
                                                    <td><?php echo $vo['stime']; ?></td>
                                                    <td><?php echo $vo['etime']; ?></td>
                                                    <td><?php echo $vo['vcount']; ?></td>
                                                    <td><?php echo $vo['differtime']; ?></td>
                                                    <td><?php echo $vo['collection']; ?></td>
                                                    <td><?php echo $vo['forward']; ?></td>
                                                    <td  style="white-space:normal;padding: 5px; ">
                                                        <a href="javascript:;" onclick="javascript:send_msg('<?php echo $vo['openid']; ?>'); ">发送信息</a>
                                                        <a style="display: none" href="javascript:alert('开发中...'); ">查看用户</a>
                                                    </td>
                                                </tr>

                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </table>
                                            <div class="oa_bottom clearfix">
                                                <div class="clearfix">
                                                    <div class="oa_op-btn clearfix">
                                                        <input type="button" name="btn1" value="发送短信" onclick="javascript:alert('开发中。。。')" />
                                                       <input type="button" name="btn2" value="发送微信"  onclick="javascript:send_msg('')"></input>
                                                    </div>
                                                </div>
                                                <div class="oa_bottom-bottom"><em></em></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="oa_content-main-bottom"></div>
                        </div>
                    </div>
                </div>            </td>
            <td class="oa_wrapper-middle-arr-right oa_wrapper-display"></td>
        </tr>
    </table>
</div>

</body>
<script>

    $('.address').mouseover(function(){
        msg=$(this).attr('msg')
        if(msg !=""){
            layer.tips("<span style='color:#000000'>"+msg+"</span>", $(this),{tips:  [3, '#fff'],time:0});
        }else{
            return false
        }
    })

    $('.address').mouseout(function(){
       layer.closeAll('tips')
    })

</script>
</html>