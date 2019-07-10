<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:85:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\activity\index.html";i:1561691685;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>活动管理</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
    <script type="text/javascript" src="/static/js/tabscommon.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <script type="text/javascript" src="/static/js/clipboard.min.js"></script>
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
                del_checked1(value);
            }, function(){

            });
        }
        function del_recovery(value,remark) {
            var msg='您确定要恢复选定的记录吗？';
            if(value>0) {
                msg='您确定要恢复“'+remark+'”吗？';
            }

            layer.confirm(msg, {
                btn: ['确定','取消'] //按钮
            }, function(index){

                $.ajax({
                    url:"<?php echo url('activity/recovery'); ?>",
                    data:"id="+value,
                    type:"post",
                    dataType:"json",
                    success:function(msg) {
                        if (msg == 1) {
                            layer.alert('恢复成功', {icon:1},function () {
                                location.reload();
                            });
                            $(".checked_one").attr("checked", false);

                        }
                        else if(msg==-1){
                            layer.alert('你没有删除权限', {icon: 5});
                        }
                        else
                        {
                            layer.alert('恢复失败', {icon: 2});
                            //location.reload();
                        }
                    }
                })
                layer.close(index);

            }, function(){

            });
        }

        function copydata(id)
        {
            $.ajax({
                url:"<?php echo url('activity/copydata'); ?>",
                data:"id="+id,
                type:"post",
                dataType:"json",
                success:function(msg) {
                    if (msg == 1) {
                        layer.alert('复制成功',{icon:1},function(){window.location.reload();});
                    }
                    else if(msg==-1){
                        layer.alert('你没有删除权限', {icon: 5});
                    }
                    else
                    {
                        layer.alert('删除失败', {icon: 2});
                        //location.reload();
                    }
                }
            })
        }
        //删除选中
        function del_checked1(value) {
            var b = $(".checked_one");
            var s = '';
            if(value<1)
            {
                for (var i = 0; i < b.length; i++) {
                    if (b[i].checked) {
                        s += b[i].value + ',';
                    }
                }
                s = s.substr(0, s.length - 1);
            }
            else
            {
                s=value;
            }

            $.ajax({
                url:"<?php echo url('activity/delchecked', '&intflag='.$intflag); ?>",
                data:"id="+s,
                type:"post",
                dataType:"json",
                success:function(msg) {
                    if (msg == 1) {
                        layer.alert('删除成功', {icon:1});
                        $(".checked_one").attr("checked", false);
                        location.reload();
                    }
                    else if(msg==-1){
                        layer.alert('你没有删除权限', {icon: 5});
                    }
                    else
                    {
                        layer.alert('删除失败', {icon: 2});
                        //location.reload();
                    }
                }
            })
        }

        function show_rqcode(o,id)
        {
            var obj=$("#rqcode");
            obj.html("<img width='150' id='img_rqcode' src='/admin/Qrcode/activityurl/sitecode/<?php echo $sitecode; ?>/id/"+id+"' />");
            var yy=event.clientY-50;
            if(yy<10) {yy=10};

            $("#rqcode").css("top",yy+"px").css("left",event.clientX-300+"px");
            obj.show();
        }
        function  close_rqcode() {
            var obj=$("#rqcode");
            obj.html("");
            obj.hide();
        }

        function shearch_check() {
            var st=$("#dtstart").val();
            var et=$("#dtend").val();
            if(st!="" && et!="")
            {
                var start=new Date(st.replace("-", "/").replace("-", "/"));
                var end=new Date(et.replace("-", "/").replace("-", "/"));
                if(end<start)
                {
                    layer.alert("举办开始时间不能大于结束时间！")
                     return;
                }
            }
            st=$("#dtpublishtime_s").val();
            et=$("#dtpublishtime_e").val();
            if(st!="" && et!="")
            {
                var start=new Date(st.replace("-", "/").replace("-", "/"));
                var end=new Date(et.replace("-", "/").replace("-", "/"));
                if(end<start)
                {
                    layer.alert("发布开始时间不能大于结束时间！")
                    return;
                }
            }
            $("#form1").submit();

        }
        function copycontent(id){
            var clipboard = new ClipboardJS("."+id);
            clipboard.on('success', function(e) {
                alert('复制成功');
            });
            clipboard.on('error', function(e) {
                alert('当前浏览器不支持次功能');
            });
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
                                <li <?php echo $intflag==6?'class="oa_on"':''; ?> onclick="javascript:window.location='<?php echo url('activity/index','&intflag=6&nodeid='.$nodeid); ?>'"><em>草稿箱  <span style="color: red">(<?php echo $acount[6]; ?>)</span></em></li>
                                <li <?php echo $intflag==1?'class="oa_on"':''; ?> onclick="javascript:window.location='<?php echo url('activity/index','&intflag=1&nodeid='.$nodeid); ?>'"><em>待审批活动  <span style="color: red">(<?php echo $acount[1]; ?>)</span></em></li>
                                <li <?php echo $intflag==3?'class="oa_on"':''; ?> onclick="javascript:window.location='<?php echo url('activity/index','&intflag=3&nodeid='.$nodeid); ?>'"><em>审批不过的活动  <span style="color: red">(<?php echo $acount[3]; ?>)</span></em></li>
                                <li <?php echo $intflag==2?'class="oa_on"':''; ?> onclick="javascript:window.location='<?php echo url('activity/index','&intflag=2&nodeid='.$nodeid); ?>'"><em>已发布活动  <span style="color: red">(<?php echo $acount[2]; ?>)</span></em></li>
                                <li <?php echo $intflag==5?'class="oa_on"':''; ?> onclick="javascript:window.location='<?php echo url('activity/index','&intflag=5&nodeid='.$nodeid); ?>'"><em>7天内即将开始的活动  <span style="color: red">(<?php echo $acount[5]; ?>)</span></em></li>
                                <li <?php echo $intflag==4?'class="oa_on"':''; ?> onclick="javascript:window.location='<?php echo url('activity/index','&intflag=4&nodeid='.$nodeid); ?>'"><em>回收站  <span style="color: red">(<?php echo $acount[4]; ?>)</span></em></li>
                            </ul>
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
                                            搜索
                                        </div>
                                        <div class="oa_search-area clearfix">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>
                                                        <div class="oa_search-type clearfix">
                                                            <form action="" method="post" id="form1">
                                                                <table width="100%" border="0" cellspacing="1" cellpadding="0">

                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">活动标题：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input style="width: 300px;"  type="text" name="chrtitle" value="<?php echo $search['chrtitle']; ?>" >
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">活动分类：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="fidtype" >
                                                                                <option value="" >所有类别</option>
                                                                                <?php if(is_array($hdfl) || $hdfl instanceof \think\Collection || $hdfl instanceof \think\Paginator): $i = 0; $__LIST__ = $hdfl;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                                                <option value="<?php echo $vo['code']; ?>" <?php if($vo['code']==$search['fidtype']) { echo "selected"; } ?> ><?php echo $vo['name']; ?></option>
                                                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                                                            </select>

                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">是否收费：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="ischarge" >
                                                                                <option value="" >所有记录</option>
                                                                                <option value="1" <?php if($search['ischarge']==1) { echo "selected"; } ?> >免费活动</option>
                                                                                <option value="2" <?php if($search['ischarge']==2) { echo "selected"; } ?> >收费活动</option>
                                                                            </select>

                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">是否置顶：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="chkcontentlev" >
                                                                                <option value="" >所有</option>
                                                                                <option value="0" <?php if(is_numeric($search['chkcontentlev']) && $search['chkcontentlev']==0) { echo "selected"; } ?> >否</option>
                                                                                <option value="1" <?php if($search['chkcontentlev']==1) { echo "selected"; } ?> >是</option>
                                                                            </select>

                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">是否首页显示：</td>
                                                                        <td class="oa_cell-right">
                                                                            <select name="chkisindex" >
                                                                                <option value="" >所有</option>
                                                                                <option value="0" <?php if(is_numeric($search['chkisindex']) && $search['chkisindex']==0) { echo "selected"; } ?> >否</option>
                                                                                <option value="1" <?php if($search['chkisindex']==1) { echo "selected"; } ?> >是</option>
                                                                            </select>

                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">举办时间：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="text"  style="width: 80px;"  id="dtstart" name="dtstart" class="form-control"  value="<?php echo $search['dtstart']; ?>"> -
                                                                            <input type="text" style="width: 80px;" id="dtend" name="dtend" class="form-control"  value="<?php echo $search['dtend']; ?>">
                                                                            <script language='JavaScript'>seltime("dtstart","YYYY-MM-DD");seltime("dtend","YYYY-MM-DD");</script>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100"   class="oa_cell-left">发布时间：</td>
                                                                        <td class="oa_cell-right">
                                                                            <input type="text"  style="width: 80px;"  id="dtpublishtime_s" name="dtpublishtime_s" class="form-control"  value="<?php echo $search['dtpublishtime_s']; ?>"> -
                                                                            <input type="text" style="width: 80px;" id="dtpublishtime_e" name="dtpublishtime_e" class="form-control"  value="<?php echo $search['dtpublishtime_e']; ?>">
                                                                            <script language='JavaScript'>seltime("dtpublishtime_s","YYYY-MM-DD");seltime("dtpublishtime_e","YYYY-MM-DD");</script>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100" class="oa_cell-left">活动标签：</td>
                                                                        <td class="oa_cell-right">
                                                                            <?php if(is_array($hdbq) || $hdbq instanceof \think\Collection || $hdbq instanceof \think\Paginator): $i = 0; $__LIST__ = $hdbq;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                                            <input type="checkbox" name="chrtags[]" value="<?php echo $vo['code']; ?>"  <?php if(in_array($vo['code'],$search['chrtags'])) { echo "checked"; } ?> ><?php echo $vo['name']; ?></input>
                                                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td height="30"></td>
                                                                        <td class="oa_cell-right"><input  name="subSearch" type="button" value="搜索" onclick="javascript:shearch_check();" class="oa_search-btn" /></td>
                                                                    </tr>
                                                                </table>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    
                                        <div class="oa_title clearfix">
                                            <span class="oa_title-btn">
                                                <ul>
                                                  <?php if($cms->CheckPurview('contentmanage','add') && $intflag==6){ ?>
                                                  <li class="oa_selected">
                                                      <a href="javascript:CustomOpen('<?php echo url('activity/modi','&action=add&nodeid='.$nodeid,''); ?>', 'activity','新建活动', 1000,600)">新建活动</a>
                                                  </li>
                                                    <?php } ?>
                                                </ul>
                                              </span>
                                            <span class="oa_ico-left"></span>
                                            活动管理                                        </div>
                                        <div class="oa_text-list">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0"  style="table-layout:fixed">
                                                <tr class="oa_text-list-title">
                                                    <th width="20" style="text-align:center;"><input id="checked"  onclick="DoCheck();" name="" type="checkbox" value="" /></th>
                                                    <th><span class="oa_arr-text-list-title"></span>活动标题</th>
                                                    <th WIDTH="60"><span class="oa_arr-text-list-title"></span>所属分类</th>
                                                    <th WIDTH="100"><span class="oa_arr-text-list-title"></span>发布时间</th>
                                                    <?php if($intflag>1) { ?>
                                                    <th WIDTH="100"><span class="oa_arr-text-list-title"></span>审批时间</th>
                                                    <?php } ?>
                                                    <th WIDTH="105"><span class="oa_arr-text-list-title"></span>活动举办时间</th>
                                                    <th WIDTH="105"><span class="oa_arr-text-list-title"></span>报名时间</th>
                                                    <th WIDTH="105" style="display: none;"><span class="oa_arr-text-list-title"></span>价格</th>
                                                    <th WIDTH="50"><span class="oa_arr-text-list-title"></span>订单数</th>
                                                    <?php if($intflag == 2) { ?>
                                                    <th WIDTH="80"><span class="oa_arr-text-list-title"></span>同步状态</th>
                                                    <?php } ?>
                                                    <th width="150"><span class="oa_arr-text-list-title"></span>操作</th>
                                                </tr>
                                                <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <tr>
                                                    <td align="center"><input class="checked_one" type="checkbox" name="idactivity[]" value="<?php echo $vo['idactivity']; ?>" /></td>
                                                    <td><?php echo $vo['chrtitle']; ?></td>
                                                    <td><?php echo $vo['typename']; ?></td>
                                                    <td><?php echo (empty($vo['dtpublishtime'])?"":date("Y-m-d H:i",strtotime($vo['dtpublishtime']))) ?></td>
                                                    <?php if($intflag>1) { ?>
                                                    <td><?php echo (empty($vo['checktime'])?"":date("Y-m-d H:i",$vo['checktime'])) ?></td>
                                                    <?php } ?>
                                                    <td><?php echo (empty($vo['dtstart'])?"":date("Y-m-d H:i",strtotime($vo['dtstart']))) ?>-<br><?php echo (empty($vo['dtend'])?"":date("Y-m-d H:i",strtotime($vo['dtend']))) ?></td>
                                                    <td><?php echo (empty($vo['dtsignstime'])?"":date("Y-m-d H:i",strtotime($vo['dtsignstime']))) ?>-<br><?php echo (empty($vo['dtsignetime'])?"":date("Y-m-d H:i",strtotime($vo['dtsignetime']))) ?></td>
                                                    <td style="display: none;"><?php if($vo['ischarge']==1){echo "免费";}else{echo $vo['min_price']."元起";} ?> </td>
                                                    <td><?php echo $vo['order_num']; ?></td>
                                                    <?php if($intflag == 2) { ?>
                                                    <td>
                                                        <?php if($vo['wntx_sync_status'] == 0){
                                                            echo '未同步';
                                                            }elseif($vo['wntx_sync_status'] == 1){
                                                            echo '待审核';
                                                            }elseif($vo['wntx_sync_status'] == 2){
                                                            echo '同步取消';
                                                            }elseif($vo['wntx_sync_status'] == 3){
                                                            echo '审核失败';
                                                            }elseif($vo['wntx_sync_status'] == 4){
                                                            echo '审核通过';
                                                            }elseif($vo['wntx_sync_status'] == 5){
                                                            echo '对方已下架';
                                                            }

                                                        ?>
                                                    </td>
                                                    <?php } ?>
                                                    <td  style="white-space:normal;padding: 5px; ">
                                                        <a onmousemove="javascript:show_rqcode(this,'<?php echo $vo['idactivity']; ?>')" onmouseout="javascript:close_rqcode()" href="/<?php echo $sitecode; ?>/detail/<?php echo $vo['idactivity']; ?>?type=test" target="_blank">浏览</a>
                                                        <a href="<?php echo url('activity/visitlist','dataid='.$vo['idactivity'],''); ?>" target="_blank">访问数据</a>
                                                        <?php  if($intflag!=4) {  if($intflag==1) {
                                                           if($cms->CheckPurview('contentmanage','checkactivity')){
                                                        ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('activity/activitycheck','id='.$vo['idactivity'].'&action=edit',''); ?>','activity','活动修改',1000,600)">审批</a>
                                                        <?php }} ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('activity/signupindex','id='.$vo['idactivity'],''); ?>','activity','<?php echo $vo['chrtitle']; ?>',1200,600)">查看报名</a>
                                                        <?php if($cms->CheckPurview('contentmanage','add')){ ?>
                                                        <a href="javascript:copydata('<?php echo $vo['idactivity']; ?>')">复制</a>
                                                        <?php } if($cms->CheckPurview('contentmanage','edit') && $intflag!=5){ ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('activity/modi','id='.$vo['idactivity'].'&action=edit',''); ?>','activity','活动修改',1000,600)">修改</a>
                                                        <?php } if($cms->CheckPurview('contentmanage','del')){ ?>
                                                        <a href="#" onclick="del_checked(<?php echo $vo['idactivity']; ?>,'<?php echo $vo['chrtitle']; ?>');" >删除</a>
                                                        <?php } } else{ if($cms->CheckPurview('contentmanage','edit')){ ?>
                                                        <a href="#" onclick="del_recovery(<?php echo $vo['idactivity']; ?>,'<?php echo $vo['chrtitle']; ?>');" >恢复</a>
                                                        <?php } if($cms->CheckPurview('contentmanage','del')){ ?>
                                                        <a href="#" onclick="del_checked(<?php echo $vo['idactivity']; ?>,'<?php echo $vo['chrtitle']; ?>');" >删除</a>
                                                        <?php } } ?>
                                                        <a href="javascript:CustomOpen('<?php echo url('activity/importorder',array('id'=>$vo['idactivity'],'templateid'=>$vo['selsignfrom'])); ?>','activity','活动修改',550,160)">导入报名</a>
                                                        <a href="javascript:void(0);" class="data<?php echo $vo['idactivity']; ?>" data-clipboard-text="http://www.tongxiang123.cn/<?php echo $sitecode; ?>/detail/<?php echo $vo['idactivity']; ?>?type=test" onclick="copycontent('data<?php echo $vo['idactivity']; ?>')" >复制链接</a>
                                                        <a href="javascript:CustomOpen('<?php echo url('activity/customdetail',array('id'=>$vo['idactivity'])); ?>','customdetail','咨询详情',700,400)">咨询详情</a>
                                                        <!--除了待审核中不可以同步-->
                                                        <?php  if($intflag==2) {  if($vo['wntx_sync_status'] != 1){?>
                                                         <a href="javascript:CustomOpen('<?php echo url('activity/one_click_sync',array('id'=>$vo['idactivity'])); ?>','activity','同步到蜗牛童行',800,500)">同步到蜗牛童行</a>
                                                        <!--审核中和审核成功可以取消-->
                                                        <?php }  if(in_array($vo['wntx_sync_status'],array(1,4))) { ?>
                                                        <a href="javascript:cancelWntxSync(<?php echo $vo['idactivity']; ?>)" id="cancel_wn">取消同步到蜗牛童行</a>
                                                        <?php } ?>

                                                        <a href="javascript:CustomOpen('<?php echo url('activity/audit_result',array('id'=>$vo['idactivity'])); ?>','activity','审核记录',700,400)">审核记录</a>

                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </table>
                                            <div class="oa_bottom clearfix">
                                                <div class="clearfix">
                                                    <div class="oa_op-btn clearfix">
                                                        <input name="" value="删除" onclick="del_checked(0,'');" type="button" class="oa_input-submit" />
                                                    </div>
                                                    <div class="oa_page-controls">
                                                        <ul>
                                                            <li><?php echo $page->show(); ?></li>
                                                        </ul>
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
<div id="rqcode" style="display: none; height: 160px;width: 160px;background-color: #FFFFFF; border: solid 2px #000000; text-align: center;padding-top: 20px; ;position: absolute;margin-right: 150px" />
<script type="text/javascript">
    //取消蜗牛童行同步
    function cancelWntxSync(activityid) {
        $.ajax({
            url: "<?php echo url('activity/cancel_wntx_sync'); ?>",
            data: "id="+activityid,
            type: "post",
            dataType: "json",
            success: function (obj) {
                if(obj.status=="success"){
                    //将按钮隐藏
                    $('#cancel_wn').css('display','none');
                    layer.msg("取消同步成功");
                    location.reload();
                }else{
                    layer.msg(obj.msg);
                }
            },
            error: function (obj) {
                layer.msg("网络错误");
            }
        });
    }
</script>
</body>
</html>