<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:88:"C:\phpStudy\PHPTutorial\WWW\work\public/../application/admin\view\member\memberdeal.html";i:1561691687;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>内容管理</title>
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/tabscommon.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <link href="/static/js/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="/static/js/daterangepicker/moment.min.js"></script>
    <script type="text/javascript" src="/static/js/daterangepicker/daterangepicker.js"></script>
</head>
<body>
<script language="JavaScript">
    function getNow() {
        var myDate = new Date();
        var y=myDate.getYear();        //获取当前年份(2位)
        var Y=myDate.getFullYear();    //获取完整的年份(4位,1970-????)
        var m=myDate.getMonth()+1;       //获取当前月份(0-11,0代表1月)
        var d=myDate.getDate();        //获取当前日(1-31)
        var w=myDate.getDay();         //获取当前星期X(0-6,0代表星期天)
        var H=myDate.getHours();       //获取当前小时数(0-23)
        var M=myDate.getMinutes();     //获取当前分钟数(0-59)
        var s=myDate.getSeconds();     //获取当前秒数(0-59)
        return Y+"-"+m+"-"+d ;
    }
    function seltime(id,timeformat) {
        $('#'+id).daterangepicker(
            {
                format:timeformat,
                singleDatePicker: true,
                showDropdowns: true,
                minDate:'1900-01-01',
                maxDate:'2030-01-01',
                startDate: $('#'+id).val()==""?getNow():$('#'+id).val(),
                timePicker : false, //是否显示小时和分钟
                timePickerIncrement:1,//time选择递增数
                timePicker12Hour : false, //是否使用12小时制来显示时间

                locale : {
                    applyLabel : '确定',
                    cancelLabel : '取消',
                    fromLabel : '起始时间',
                    toLabel : '结束时间',
                    customRangeLabel : '自定义',
                    daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
                    monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月','七月', '八月', '九月', '十月', '十一月', '十二月' ],
                    firstDay : 1
                }
            }
        );}
</script>
<div class="oa_pop">
    <div class="oa_pop-main">
        <div style="height: 6px"></div>
        <div class="oa_title clearfix">
            <span class="oa_ico-right"></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            用户管理
        </div>
        <div class="oa_edition">
            <form id="handleposition" action="<?php echo url('member/memberpost'); ?>" method="post" >
                <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                   <style>
                       input{
                           border: 1px solid #DEDEDE;
                       }
                   </style>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">昵称：</td>
                        <td class="oa_cell-right">
                            <input type="text" name="nickname" value="<?php echo $memberinfo['nickname']; ?>" class="oa_input-200" />
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">姓名(报名表单)：</td>
                        <td class="oa_cell-right">
                            <input type="text" name="chrname" value="<?php echo $memberinfo['chrname']; ?>" readonly="readonly" class="oa_input-200" />
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">电话(报名表单)：</td>
                        <td class="oa_cell-right">
                            <input type="text" name="chrtel" value="<?php echo $memberinfo['chrtel']; ?>" readonly="readonly" class="oa_input-200" />
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">邮箱(报名表单)：</td>
                        <td class="oa_cell-right">
                            <input type="text" name="chrmail" value="<?php echo $memberinfo['chrmail']; ?>" readonly="readonly" class="oa_input-200" />
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">邮寄地址(报名表单)：</td>
                        <td class="oa_cell-right">
                            <textarea is_null="1" cols="50" rows="3" class="oa_input-200" readonly="readonly" name="postal_address"><?php echo $memberinfo['postal_address']; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">身份证(报名表单)：</td>
                        <td class="oa_cell-right">
                            <input type="text" is_null="1"  class="oa_input-200" readonly="readonly" name="identity_card_num" value="<?php echo $memberinfo['identity_card_num']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">姓名：</td>
                        <td class="oa_cell-right">
                            <input type="text" name="u_chrname" value="<?php echo $memberinfo['u_chrname']; ?>"  class="oa_input-200" />
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">电话：</td>
                        <td class="oa_cell-right">
                            <input type="text" name="u_chrtel" value="<?php echo $memberinfo['u_chrtel']; ?>" class="oa_input-200" />
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">邮箱：</td>
                        <td class="oa_cell-right">
                            <input type="text" name="u_chrmail" value="<?php echo $memberinfo['u_chrmail']; ?>" class="oa_input-200" />
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">邮寄地址：</td>
                        <td class="oa_cell-right">
                            <textarea is_null="1" cols="50" rows="3" class="oa_input-200" name="u_postal_address"><?php echo $memberinfo['u_postal_address']; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">身份证：</td>
                        <td class="oa_cell-right">
                            <input type="text" is_null="1" class="oa_input-200" name="u_identity_card_num" value="<?php echo $memberinfo['u_identity_card_num']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">所属分类：</td>
                        <td class="oa_cell-right">

                            <select name="categoryid" >
                                <option value="">请选择分类</option>
                                <?php if(is_array($hyfl) || $hyfl instanceof \think\Collection || $hyfl instanceof \think\Paginator): $i = 0; $__LIST__ = $hyfl;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo $vo['code']; ?>" <?php if($memberinfo['categoryid']==$vo['code']) { echo "selected"; } ?>><?php echo $vo['name']; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr id="business">
                        <td height="40px" width="150" class="oa_cell-left">所属商务：</td>
                        <td class="oa_cell-right">
                            <select name="iduser" >
                                <option value="">请选择商务</option>
                                <?php if(is_array($account) || $account instanceof \think\Collection || $account instanceof \think\Paginator): $i = 0; $__LIST__ = $account;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo $vo['idaccount']; ?>" <?php if($memberinfo['iduser']==$vo['idaccount']) { echo "selected"; } ?>><?php echo $vo['chrname']; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">小孩姓名1：</td>
                        <td class="oa_cell-right">
                            <input type="text" name="childname1" value="<?php echo $memberinfo['childname1']; ?>" class="oa_input-200" />
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">小孩性别1：</td>
                        <td class="oa_cell-right">
                            <input type="radio" name="childsex1" value="1"  <?php echo $memberinfo['childsex1']=="1"?"checked":""; ?> />男 &nbsp;&nbsp;&nbsp;
                            <input type="radio" name="childsex1" value="2"  <?php echo $memberinfo['childsex1']=="2"?"checked":""; ?> />女
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">小孩年龄1：</td>
                        <td class="oa_cell-right">
                            <input type="text" is_null="0" id="childage1" name="childage1" class="form-control"  value="<?php echo $memberinfo['childage1']; ?>">
                            <script language='JavaScript'>seltime("childage1","YYYY-MM-DD HH:mm:ss")</script>
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">小孩姓名2：</td>
                        <td class="oa_cell-right">
                            <input type="text" name="childname2" value="<?php echo $memberinfo['childname2']; ?>" class="oa_input-200" />
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">小孩性别2：</td>
                        <td class="oa_cell-right">
                            <input type="radio" name="childsex2" value="1"  <?php echo $memberinfo['childsex2']=="1"?"checked":""; ?> />男 &nbsp;&nbsp;&nbsp;
                            <input type="radio" name="childsex2" value="2"  <?php echo $memberinfo['childsex2']=="2"?"checked":""; ?> />女
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">小孩年龄2：</td>
                        <td class="oa_cell-right">
                            <input type="text" is_null="0" id="childage2" name="childage2" class="form-control"  value="<?php echo $memberinfo['childage2']; ?>">
                            <script language='JavaScript'>seltime("childage2","YYYY-MM-DD")</script>

                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">小孩姓名3：</td>
                        <td class="oa_cell-right">
                            <input type="text" name="childname3" value="<?php echo $memberinfo['childname3']; ?>" class="oa_input-200" />
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">小孩性别3：</td>
                        <td class="oa_cell-right">
                            <input type="radio" name="childsex3" value="1"  <?php echo $memberinfo['childsex3']=="1"?"checked":""; ?> />男 &nbsp;&nbsp;&nbsp;
                            <input type="radio" name="childsex3" value="2"  <?php echo $memberinfo['childsex3']=="2"?"checked":""; ?> />女
                        </td>
                    </tr>
                    <tr>
                        <td height="40px" width="150" class="oa_cell-left">小孩年龄3：</td>
                        <td class="oa_cell-right">
                            <input type="text" is_null="0" id="childage3" name="childage3" class="form-control"  value="<?php echo $memberinfo['childage3']; ?>">
                            <script language='JavaScript'>seltime("childage3","YYYY-MM-DD")</script>

                        </td>
                    </tr>
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <input type="hidden" name="idsite" value="<?php echo $idsite; ?>">
                    <?php if($action == 'edit'): ?>
                    <input type="hidden" name="idmember" value="<?php echo $memberinfo['idmember']; ?>">
                    <?php endif; ?>
                    <tr id="require1">
                        <td><input style="margin:10px;height: 30px;width: 50px" type="button" onclick="adsubmit()" value="确定"></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </form>
        </div>
        <div style="height: 30px"></div>
    </div>

</div>
</body>

<?php if($assign_business==1){ ?>
<script type="text/javascript">
    $(document).ready(function () {
        $("body tr").hide();
        $("#business").show();
        $("#require1").show();
        $("#require2").show();
    })
</script>
<?php } ?>

<script>

        function adsubmit(){
             $('#handleposition').submit();
        }


</script>

</html>