<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:68:"D:\workspace\work\public/../application/admin\view\report\index.html";i:1567413549;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>用户增减报表</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
    <script type="text/javascript" src="/static/js/tabscommon.js"></script>
    <script type="text/javascript" src="/static/js/del-checked.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <link rel="stylesheet" type="text/css" href="/static/js/daterangepicker/daterangepicker-bs3.css" />
    <script type="text/javascript" src="/static/js/daterangepicker/moment.min.js"></script>
    <script type="text/javascript" src="/static/js/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="/static/js/echarts/echarts.js"></script>
    <script type="text/javascript" src="/static/js/echarts/echartsFunction.js"></script>
    <script type="text/javascript">

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
                                <?php if($cms->CheckPurview('manage','index')){ ?>
                                <li class="oa_on" onclick="javascript:window.location='<?php echo url('report/index',''); ?>'"><em>用户增减报表</em></li>
                                <?php } if($cms->CheckPurview('manage','index')){ ?>
                                <li onclick="javascript:window.location='<?php echo url('report/user_attribute',''); ?>'"><em>用户属性分析</em></li>
                                <?php } if($cms->CheckPurview('manage','index')){ ?>
                                <li onclick="javascript:window.location='<?php echo url('report/buy_user',''); ?>'"><em>购买用户分析</em></li>
                                <?php } if($cms->CheckPurview('manage','order')){ ?>
                                <li onclick="javascript:window.location='<?php echo url('report/order',''); ?>'"><em>订单报表</em></li>
                                <?php } if($cms->CheckPurview('manage','activity')){ ?>
                                <li onclick="javascript:window.location='<?php echo url('report/activity',''); ?>'"><em>产品报表</em></li>
                                <?php } ?>
                                <!--<li onclick="javascript:window.location='<?php echo url('weixinreplay/follow',''); ?>'"><em>用户跟踪报表</em></li>-->
                            </ul>
                        </div>
                    </div>
                    <div class="oa_title clearfix">
                        <span class="oa_title-btn">
                          </span>
                        <span class="oa_ico-left"></span>
                        昨日关键指标
                    </div>
                    <div class="oa_text-list"  style="margin-bottom: 10px">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                            <tr style="height: 180px;">
                                <td style=" text-align: center;">
                                    <!--href="/admin/?type=follow&start_time=<?php echo $list['search']['yesterday']; ?>&origin=member"-->
                                    <div style="margin-bottom: 5px">新关注人数</div>
                                    <div style="margin-bottom: 5px">
                                        <span style="font-size: 20px;font-weight: bold"><a  href="javascript:CustomOpen('<?php echo url('report/getUserList','type=follow&start_time='.$list['search']['yesterday'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_user_data']['follow']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>日 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=follow&start_time='.$list['search']['yesterday'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_user_data']['follow']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>周 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=follow&start_time='.$list['search']['yesterday_week'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_week_user_data']['follow']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>月 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=follow&start_time='.$list['search']['yesterday_month'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_month_user_data']['follow']; ?></a></span>
                                    </div>
                                </td>
                                <td style="text-align: center">
                                    <div style="margin-bottom: 5px">取消关注人数</div>
                                    <div style="margin-bottom: 5px">
                                        <span style="font-size: 20px;font-weight: bold"><a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=unfollow&start_time='.$list['search']['yesterday'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_user_data']['unfollow']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>日 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=unfollow&start_time='.$list['search']['yesterday'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_user_data']['unfollow']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>周 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=unfollow&start_time='.$list['search']['yesterday_week'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_week_user_data']['unfollow']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>月 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=unfollow&start_time='.$list['search']['yesterday_month'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_month_user_data']['unfollow']; ?></a></span>
                                    </div>
                                </td>
                                <td style="text-align: center">
                                    <div style="margin-bottom: 5px">净增关注人数</div>
                                    <div style="margin-bottom: 5px">
                                        <span style="font-size: 20px;font-weight: bold"><?php echo $list['yesterday_user_data']['increase']; ?></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>日 &nbsp;<?php echo $list['yesterday_user_data']['increase']; ?></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>周 &nbsp;<?php echo $list['yesterday_week_user_data']['increase']; ?></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>月 &nbsp;<?php echo $list['yesterday_month_user_data']['increase']; ?></span>
                                    </div>
                                </td>
                                <td style="text-align: center">
                                    <div style="margin-bottom: 5px">累积关注人数</div>
                                    <div style="margin-bottom: 5px">
                                        <span style="font-size: 20px;font-weight: bold"><a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=count&start_time='.$list['search']['yesterday'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_user_data']['count']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>日 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=count&start_time='.$list['search']['yesterday'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_user_data']['count']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>周 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=count&start_time='.$list['search']['yesterday_week'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_week_user_data']['count']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>月 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=count&start_time='.$list['search']['yesterday_month'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_month_user_data']['count']; ?></a></span>
                                    </div>
                                </td>
                                <td style="text-align: center">
                                    <div style="margin-bottom: 5px">新增游客人数</div>
                                    <div style="margin-bottom: 5px">
                                        <span style="font-size: 20px;font-weight: bold"><a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visitor&start_time='.$list['search']['yesterday'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_user_data']['visitor']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>日 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visitor&start_time='.$list['search']['yesterday'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_user_data']['visitor']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>周 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visitor&start_time='.$list['search']['yesterday_week'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_week_user_data']['visitor']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>月 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visitor&start_time='.$list['search']['yesterday_month'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_month_user_data']['visitor']; ?></a></span>
                                    </div>
                                </td>
                                <td style="text-align: center">
                                    <div style="margin-bottom: 5px">累积游客人数</div>
                                    <div style="margin-bottom: 5px">
                                        <span style="font-size: 20px;font-weight: bold"><a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visit_count&start_time='.$list['search']['yesterday'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_user_data']['visit_count']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>日 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visit_count&start_time='.$list['search']['yesterday'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_user_data']['visit_count']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>周 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visit_count&start_time='.$list['search']['yesterday_week'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_week_user_data']['visit_count']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>月 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visit_count&start_time='.$list['search']['yesterday_month'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_month_user_data']['visit_count']; ?></a></span>
                                    </div>
                                </td>
                                <td style="text-align: center">
                                    <div style="margin-bottom: 5px">游客转关注人数</div>
                                    <div style="margin-bottom: 5px">
                                        <span style="font-size: 20px;font-weight: bold"><a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visit_to_follow&start_time='.$list['search']['yesterday'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_user_data']['visit_to_follow']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>日 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visit_to_follow&start_time='.$list['search']['yesterday'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_user_data']['visit_to_follow']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>周 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visit_to_follow&start_time='.$list['search']['yesterday_week'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_week_user_data']['visit_to_follow']; ?></a></span>
                                    </div>
                                    <div style="margin-bottom: 5px">
                                        <span>月 &nbsp;<a href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visit_to_follow&start_time='.$list['search']['yesterday_month'].'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $list['yesterday_month_user_data']['visit_to_follow']; ?></a></span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="oa_search-area clearfix">

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
                                                        <td width="100" class="oa_cell-left">查询范围：</td>
                                                        <td class="oa_cell-right">
                                                            <input type="text"  style="width: 80px;"  id="begintime" name="begintime" class="form-control" autocomplete="off"  value="<?php echo $list['search']['begintime']; ?>"> -
                                                            <input type="text" style="width: 80px;" id="endtime" name="endtime" class="form-control"  autocomplete="off" value="<?php echo $list['search']['endtime']; ?>">
                                                            <script language='JavaScript'>seltime("begintime","YYYY-MM-DD");seltime("endtime","YYYY-MM-DD");</script>

                                                            <input  name="subSearch" type="button" value="搜索"  class="oa_search-btn" onclick="javascript:shearch_check();"/>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="oa_subnav clearfix">
                        <div class="oa_subnav-tab clearfix">
                            <ul>
                                <li class="e_charts oa_on" onclick="get_echarts('follow',this)"><em>新关注人数</em></li>
                                <li class="e_charts" onclick="get_echarts('unfollow',this)"><em>取消关注人数</em></li>
                                <li class="e_charts" onclick="get_echarts('increase',this)"><em>净增关注人数</em></li>
                                <li class="e_charts" onclick="get_echarts('count',this)"><em>累积关注人数</em></li>
                                <li class="e_charts" onclick="get_echarts('visitor',this)"><em>新增游客人数</em></li>
                                <li class="e_charts" onclick="get_echarts('visit_count',this)"><em>累积游客人数</em></li>
                                <li class="e_charts" onclick="get_echarts('visit_to_follow',this)"><em>游客转关注人数</em></li>
                            </ul>
                        </div>
                    </div>

                    <div class="oa_text-list"  style="margin-bottom: 10px;border-left: 0;">
                        <div id="main" style="height: 500px; "></div>
                    </div>

                    <div class="oa_title clearfix">
                        <span class="oa_title-btn">
                          </span>
                        <span class="oa_ico-left"></span>
                        用户增减列表
                    </div>
                    <div class="oa_text-list">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
                            <tr class="oa_text-list-title">
                                <th><span class="oa_arr-text-list-title"></span>日期</th>
                                <th><span class="oa_arr-text-list-title"></span>新关注人数</th>
                                <th><span class="oa_arr-text-list-title"></span>取消关注人数</th>
                                <th><span class="oa_arr-text-list-title"></span>净增关注人数</th>
                                <th><span class="oa_arr-text-list-title"></span>累积关注人数</th>
                                <th><span class="oa_arr-text-list-title"></span>新增游客人数</th>
                                <th><span class="oa_arr-text-list-title"></span>累积游客人数</th>
                                <th><span class="oa_arr-text-list-title"></span>游客转关注人数</th>
                            </tr>
                            <?php if(empty($list)){?>
                            <tr>
                                <td colspan="7" style="height: 50px; text-align: center">当前没有访问记录</td>
                            </tr>
                            <?php }else{ if(is_array($list['list']) || $list['list'] instanceof \think\Collection || $list['list'] instanceof \think\Paginator): $i = 0; $__LIST__ = $list['list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <tr>
                                <?php if($key == '汇总'): ?>
                                <td colspan="3" style="text-align: center"><?php echo $key; ?></td>
                                <?php else: ?>
                                <td><?php echo $key; ?></td>
                                <td><a  href="javascript:CustomOpen('<?php echo url('report/getUserList','type=follow&start_time='.$key.'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $vo["follow"]; ?></a></td>
                                <td><a  href="javascript:CustomOpen('<?php echo url('report/getUserList','type=unfollow&start_time='.$key.'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $vo["unfollow"]; ?></a></td>
                                <?php endif; ?>
                                <td><?php echo $vo["increase"]; ?></td>
                                <td><a  href="javascript:CustomOpen('<?php echo url('report/getUserList','type=count&start_time='.$key.'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $vo["count"]; ?></a></td>
                                <td><a  href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visitor&start_time='.$key.'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $vo["visitor"]; ?></a></td>
                                <td><a  href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visit_count&start_time='.$key.'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $vo["visit_count"]; ?></a></td>
                                <td><a  href="javascript:CustomOpen('<?php echo url('report/getUserList','type=visit_to_follow&start_time='.$key.'&origin=member',''); ?>','activity','用户列表',1000,600)"><?php echo $vo["visit_to_follow"]; ?></a></td>
                            </tr>
                            <?php endforeach; endif; else: echo "" ;endif; } ?>
                        </table>
                    </div>
                    <div class="oa_content-main-bottom"></div>

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

    $(document).ready(function(){
        get_echarts('follow');
    });

    function empty() {
        window.location.reload();
    }


    function export_data(id,p) {
        var time_range = $("#time_range").val();
        var begintime = $("#begintime").val();
        var endtime = $("#endtime").val();
        var url = "/admin/report/member_report/time_range/"+time_range+"/begintime/"+begintime+'/endtime/'+endtime;
        window.open(url,"_blank");
    }

    /**
     * 获得数据的图表
     */
    function get_echarts(type,that) {
        //如果有传当前对象过来   那么就是点击事件   否则就是初始化
        if(that){
            //将所有的选中去掉,进行初始化
            $('.e_charts').removeClass('oa_on');
            // debugger;
            $(that).addClass('oa_on');
        }
        //将封装好的图表数据赋值
        var echart = <?php echo $list['echart']; ?>;
        //取点击传过来的类型值
        var arrData = echart[type];
        // debugger;
        var strtitle = '';
        var code = "main";
        showLine(code, arrData, strtitle, 130, 75, 0);
    }
    //搜索
    function shearch_check() {
        var st = $("#begintime").val();
        var et = $("#endtime").val();
        if (st != "" && et != "") {
            var start = new Date(st.replace("-", "/").replace("-", "/"));
            var end = new Date(et.replace("-", "/").replace("-", "/"));
            var now = new Date();
            var yesterday = now.setTime(now.getTime()-24*60*60*1000);
            // alert(now);
            if (end < start) {
                layer.alert("查询范围开始时间不能大于结束时间！")
                return;
            }
            if (start >= yesterday || end >= yesterday ) {
                layer.alert("查询范围时间不能大于昨天的时间！");
                return;
            }
        }
        if(!checkTime()){
            return false;
        };
        $("#form1").submit();
    }

    /**
     * 限制查询跨度在两个月之内
     * @returns {boolean}
     */
    function checkTime(){
        var begintime = $("#begintime").val();
        var endtime = $("#endtime").val();
        if(!endtime){
            return true;
        }

        var time1 = new Date(begintime).getTime();
        var time2 = new Date(endtime).getTime();
        if(begintime==''){
            layer.alert("开始时间不能为空！")
            return false;
        }
        if(endtime==''){
            layer.alert("结束时间不能为空");
            return false;
        }
        if(time1 > time2){
            layer.alert("开始时间不能大于结束时间");
            return false;
        }

        //判断时间跨度是否大于2个月
        var arr1 = begintime.split('-');
        var arr2 = endtime.split('-');
        arr1[1] = parseInt(arr1[1]);
        arr1[2] = parseInt(arr1[2]);
        arr2[1] = parseInt(arr2[1]);
        arr2[2] = parseInt(arr2[2]);
        var flag = true;
        //同年
        if(arr1[0] == arr2[0]){
            if(arr2[1]-arr1[1] > 2){ //月间隔超过2个月
                flag = false;
            }else if(arr2[1]-arr1[1] == 2){ //月相隔2个月，比较日
                if(arr2[2] > arr1[2]){ //结束日期的日大于开始日期的日
                    flag = false;
                }
            }
        }else{ //不同年
            if(arr2[0] - arr1[0] > 1){
                flag = false;
            }else if(arr2[0] - arr1[0] == 1){
                if(arr1[1] < 10){ //开始年的月份小于10时，不需要跨年
                    flag = false;
                }else if(arr1[1]+2-arr2[1] < 12){ //月相隔大于2个月
                    flag = false;
                }else if(arr1[1]+2-arr2[1] == 12){ //月相隔2个月，比较日
                    if(arr2[2] > arr1[2]){ //结束日期的日大于开始日期的日
                        flag = false;
                    }
                }
            }
        }
        if(!flag){
            layer.alert("时间跨度不得超过2个月！");
            return false;
        }
        return true;
    }
</script>
</body>
</html>