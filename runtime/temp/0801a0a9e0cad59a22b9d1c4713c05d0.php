<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"D:\workspace\work\public/../application/admin\view\node\contentdeal.html";i:1561691688;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>内容管理</title>
<script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
<script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
<script type="text/javascript" src="/static/js/tabscommon.js"></script>
<script type="text/javascript" src="/static/js/del-checked.js"></script>
<script type="text/javascript" src="/static/js/layer/layer.js"></script>

    <link href="/static/js/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="/static/js/daterangepicker/moment.min.js"></script>
    <script type="text/javascript" src="/static/js/daterangepicker/daterangepicker.js"></script>


    <script type="text/javascript">
        window.UEDITOR_Admin_URL = "/public/plugins/Ueditor/";
        var URL_upload = "/index.php/Admin/Ueditor/imageUp/savepath/article";
        var URL_fileUp = "/index.php/Admin/Ueditor/fileUp/savepath/article";
        var URL_scrawlUp = "/index.php/Admin/Ueditor/scrawlUp/savepath/article";
        var URL_getRemoteImage = "/index.php/Admin/Ueditor/getRemoteImage/savepath/article";
        var URL_imageManager = "/index.php/Admin/Ueditor/imageManager/savepath/article";
        var URL_imageUp = "/index.php/Admin/Ueditor/imageUp/savepath/article";
        var URL_getMovie = "/index.php/Admin/Ueditor/getMovie/savepath/article";
        var URL_home = "";
    </script>
    <!--
    <script>
        KindEditor.ready(function(K) {
            window.editor = K.create('#editor_id');
        });
    </script>
-->
    <script language="javascript">
        //具体参数配置在  editor_config.js 中
        var options = {
            zIndex: 999,
            initialFrameWidth: 558, //初化宽度
            initialFrameHeight: 400, //初化高度
            focus: false, //初始化时，是否让编辑器获得焦点true或false
            maximumWords: 99999, removeFormatAttributes: 'class,style,lang,width,height,align,hspace,valign',//允许的最大字符数 'fullscreen',
            pasteplain:false, //是否默认为纯文本粘贴。false为不使用纯文本粘贴，true为使用纯文本粘贴
            autoHeightEnabled: false
            /*   autotypeset: {
             mergeEmptyline: true,        //合并空行
             removeClass: true,           //去掉冗余的class
             removeEmptyline: false,      //去掉空行
             textAlign: "left",           //段落的排版方式，可以是 left,right,center,justify 去掉这个属性表示不执行排版
             imageBlockLine: 'center',    //图片的浮动方式，独占一行剧中,左右浮动，默认: center,left,right,none 去掉这个属性表示不执行排版
             pasteFilter: false,          //根据规则过滤没事粘贴进来的内容
             clearFontSize: false,        //去掉所有的内嵌字号，使用编辑器默认的字号
             clearFontFamily: false,      //去掉所有的内嵌字体，使用编辑器默认的字体
             removeEmptyNode: false,      //去掉空节点
             //可以去掉的标签
             removeTagNames: {"font": 1},
             indent: false,               // 行首缩进
             indentValue: '0em'           //行首缩进的大小
             }*/
        };
    </script>
    <script type="text/javascript" src="/static/plugins/Ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="/static/plugins/Ueditor/ueditor.all.js"></script>

<!-- 模态框（Modal）JS加载 -->
 <link rel="stylesheet" href="/static/css/bootstrap.min.css">
<link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
<link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        img{max-width: 100%}
    </style>
</head>
<body>


<div class="oa_pop">
    <div class="oa_pop-main">
        <div class="oa_title clearfix">
            <span class="oa_ico-right"></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            内容管理
            <?php if(is_array($lang) || $lang instanceof \think\Collection || $lang instanceof \think\Paginator): if( count($lang)==0 ) : echo "" ;else: foreach($lang as $k=>$vo): ?>
            <a class="lang" lang="<?php echo $k; ?>"> <?php echo $vo; ?></a>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>

        <div class="oa_edition">
            <form id="handleposition" action="<?php echo url('node/contentpost'); ?>" method="post" enctype="multipart/form-data">
                <table id="table_content" width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition">
                   <style>
                       input{
                           border: 1px solid #DEDEDE;
                       }
                   </style>
                   <?php if(is_array($fieldlist) || $fieldlist instanceof \think\Collection || $fieldlist instanceof \think\Paginator): if( count($fieldlist)==0 ) : echo "" ;else: foreach($fieldlist as $k=>$vo): if(is_array($lang) || $lang instanceof \think\Collection || $lang instanceof \think\Paginator): if( count($lang)==0 ) : echo "" ;else: foreach($lang as $k1=>$v): $k1=='cn'?$prefix='':$prefix=$k1.'_'; if(($vo['fieldtype'] > 3 && $k1 == 'cn')): ?>
                    <tr class="tr <?php echo $k1; ?> public">
                        <td height="40px" width="150" class="oa_cell-left"><?php if($vo['enablenull'] != 1): ?><span style="color: red">*</span><?php endif; ?> <?php echo $vo['fieldalias']; if(in_array(($vo['fieldtype']), explode(',',"1,2,3"))): if($prefix == 'tc_'): ?>(繁体)<?php endif; if($prefix == 'en_'): ?>(英文)<?php endif; if($prefix == ''): endif; endif; ?>：</td>
                        <td height="40px" class="oa_cell-right">
                            <?php if($contentinfo != null): array_key_exists($prefix.$vo['fieldname'],$contentinfo)?$field=$prefix.$vo['fieldname']:$field=$vo['fieldname']; ?>
                            <?php echo getControl($vo,$contentinfo[strtolower($field)],$prefix); else: ?><?php echo getControl($vo,'',$prefix); endif; if($vo['enablenull'] != 1): ?>
                            <span style="color: red;display: none;" class="<?php echo $vo['fieldname']; ?>_null">(不能为空)</span>
                            <?php endif; if($vo['isonly'] == 1): ?>
                            <span style="color: red;display: none" class="<?php echo $vo['fieldname']; ?>">(内容重复)</span>
                            <?php endif; if($vo['tips'] != null): ?>
                            <br /><div style="color: blue;"><?php echo $vo['tips']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php elseif(($vo['fieldtype'] == 1) or ($vo['fieldtype'] == 2) or ($vo['fieldtype'] == 3)): ?>
                    <tr class="tr <?php echo $k1; ?>" style="margin-top: 10px">
                        <td height="40px" width="150" class="oa_cell-left"><?php if($vo['enablenull'] != 1): ?><span style="color: red">*</span><?php endif; ?> <?php echo $vo['fieldalias']; if(in_array(($vo['fieldtype']), explode(',',"1,2,3"))): if($prefix == 'tc_'): ?>(繁体)<?php endif; if($prefix == 'en_'): ?>(英文)<?php endif; if($prefix == ''): ?>(简体)<?php endif; endif; ?>：</td>
                        <td height="40px" class="oa_cell-right">
                            <?php if($contentinfo != null): array_key_exists($prefix.$vo['fieldname'],$contentinfo)?$field=$prefix.$vo['fieldname']:$field=$vo['fieldname']; ?>
                            <?php echo getControl($vo,$contentinfo[strtolower($field)],$prefix); else: ?><?php echo getControl($vo,'',$prefix); endif; if($vo['enablenull'] != 1): ?>
                            <span style="color: red;display: none;" class="<?php echo $vo['fieldname']; ?>_null">(不能为空)</span>
                            <?php endif; if($vo['isonly'] == 1): ?>
                            <span style="color: red;display: none" class="<?php echo $vo['fieldname']; ?>">(内容重复)</span>
                            <?php endif; if($vo['tips'] != null): ?>
                            <br /><div style="color: blue;"><?php echo $vo['tips']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; if($action != 'view'): ?>
                    <tr style=" border: solid 1px #ccc">

                        <td colspan="2">
                            <input style="margin:5px;height: 30px;width: 50px" type="button" onclick="javascript:window.location='<?php echo url('admin/node/contentlist',array('nodeid'=>$nodeid)); ?>'" value="返回">
                            <input style="margin:5px;height: 30px;width: 50px" type="button" onclick="adsubmit()" value="确定">
                        </td>
                    </tr>

                    <?php endif; ?>

                    <tr style="display: none;">
                        <?php if($action == 'edit'): ?>
                        <td><input type="hidden" name="contentid" value="<?php echo $contentinfo['contentid']; ?>"></td>
                        <?php endif; ?>
                        <td><input type="hidden" name="modelid" value="<?php echo $modelid; ?>"></td>
                        <td><input type="hidden" name="nodeid" value="<?php echo $nodeid; ?>"></td>
                        <td><input type="hidden" name="action" value="<?php echo $action; ?>"></td>
                    </tr>
                </table>
            </form>
        </div>
        <div style="height: 15px"></div>

    </div>
    <!-- 模态框（Modal） -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title" id="myModalLabel">
                        详情信息
                    </h4>
                </div>

                <div class="modal-body">
                    <form id="form1" action="" method="post">
                        栏目信息：
                        <select class="input-sm" name="nodeid" id="select">
                            <?php if(is_array($nodename) || $nodename instanceof \think\Collection || $nodename instanceof \think\Paginator): if( count($nodename)==0 ) : echo "" ;else: foreach($nodename as $k1=>$v): ?>
                            <option  value="<?php echo $v['nodeid']; ?>"><?php for($x=0;$x<=$v['level'];$x++){ echo "&nbsp;&nbsp;&nbsp;&nbsp;";} ?> <?php echo $v['nodename']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                        标题：<input type="text" name="title" value="" id="biaoti">
                        <!--<input type="submit" name="submit"  value="搜索">-->
                        <input type="button" name="button" id="chazhao" value="查找">
                    </form>
                </div>

                <hr>

                <div id="content" class="modal-body">
                    内容列表:
                    <form action="" method="post" >
                        <table id="table">
                            <tr>
                                <td  style="width: 30px;height: auto;border: darkgrey solid 1px;text-align: center;"><input type="checkbox" name="allChecked1"  onclick="DoCheck1();" ></td>
                                <td style="width: 60px;height: auto;border: darkgrey solid 1px;">ID</td>
                                <td style="width:390px;height: auto;border: darkgrey solid 1px;">标题</td>
                            </tr>
                        </table>
                        <input type="button" id="button" value="添加到相关内容">
                        <input type="button" id="buttonx" value="删除">
                    </form>
                </div>
                <div class="modal-body">
                    相关内容:
                    <table id="tablex">
                        <tr>
                            <td ><input type="hidden" name="allChecked" id="allChecked"></td>
                            <th style="width: 90px;height: auto;border: darkgrey solid 1px;">内容ID</th>
                            <th style="width: 340px;height: auto;border: darkgrey solid 1px;">标题</th>
                            <th style="width: 50px;height: auto;border: darkgrey solid 1px;text-align: center;">操作</th>
                        </tr>
                        <tr id="trx">
                        </tr>
                    </table>
                    <input type="text" value="" id="selectcomtentids" name="selectcomtentids" style="width:481px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                    </button>
                    <!--<button type="button" class="btn btn-primary" id="queren">
                        确认
                    </button>-->
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="queren">
                        确认
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    $(".lang").eq(0).css("color","#f00");
    var type = $(".lang").eq(0).attr("lang");
    $("table .tr").hide();
    $(".public").show();
    $("."+type).show();
    $(".lang").click(function () {
        var lang = $(this).attr("lang");
        $(this).css("color","#f00");
        $(this).siblings().css("color","#000");
        $(".tr").hide();
        $(".public").show();
        $("."+lang).show();
    });

</script>
<script>
    //单行文本框检测正则
    $(".input").blur(function(){
        var tip = $(this).attr('tip');
        var type = $(this).attr('sign');
        var val = $(this).val();
        var is_null = $(this).attr('is_null');
        var is_only = $(this).attr('is_only');
        var name = $(this).attr('name');

        if(is_null == 0){
            if(val.length == 0){
                $("."+name+"_null").show();
            }else {
                $("."+name+"_null").hide();
            }
        }
        if(is_only == 1 & val.length !== 0){  //惟一
            var modelid = <?php echo $modelid; ?>;
            var act = <?php echo $type; ?>;
            var field = $(this).attr("name");
            var content_id = <?php echo $contentid; ?>;
            $.ajax({
                data:"modelid="+modelid+"&value="+val+"&fieldname="+field+"&action="+act+"&contentid="+content_id,
                url:"<?php echo url('Admin/node/contenttest'); ?>",
                success:function(msg){
                    if(msg == 2){
                        $("."+field).show();
                    }else {
                        $("."+field).hide();
                    }
                }
            });
        }else{
            $("."+name).hide();
        }

        if(type == 0){  //无
            var reg = null;
        }
        if(type == 1){  //邮箱
            var reg = /^([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
        }
        if(type == 2){  //固定电话号码
            var reg = /^(0[0-9]{2,3}\/-)?([2-9][0-9]{6,7})+(\/-[0-9]{1,4})?$/;
        }
        if(type == 3){  //手机号码
            var reg = /(^1[3|4|5|7|8][0-9]{9}$)/;
        }
        if(type == 4){  //邮政编码
            var reg = /^[1-9]\\d{5}$/;
        }
        if(type == 5){  //纯数字
            var reg = /^[0-9]*$/;
        }
        if(type == 6){  //纯英文字母
            var reg = /^[a-zA-Z\d]+$/;
        }
        if(type == 7){  //纯中文
            var reg = /^[\u4e00-\u9fa5]+$/;
        }
        if(type == 8){  //自定义正则
            var reg = $(".input").attr("reg");
        }
        if(type == 9){  //调用函数
            var reg = $(this).attr('reg');

        }
        if(reg !== null & val.length !== 0 ){
            if(reg.test(val)){
            }else{
                alert(tip);
            }
        }
    });


    function adsubmit(){
        var input = $(".input");
        var str = '';
        for(var i=0;i<input.length;i++){
            var type = $(".input")[i].getAttribute("sign");
            var val = input[i].value;
            var tip = $(".input")[i].getAttribute("tip");
            var is_null = $(".input")[i].getAttribute("is_null");
            var is_only = $(".input")[i].getAttribute("is_only");
            var name = $(".input")[i].getAttribute("name");

            //判断空间是否为空
            if(is_null == 0){ //不能为空
                if(val.length == 0){
                    str += '2,';
                    $("."+name+"_null").show();
                }else {
                    $("."+name+"_null").hide();
                    str += '1,';
                }
            }

            if(is_only == 1 & val.length !== 0){  //如果惟一，并且值不能为空
                var modelid = <?php echo $modelid; ?>;
                var act = <?php echo $type; ?>;
                var field = name;
                var content_id = <?php echo $contentid; ?>;
                $.ajax({
                    data:"modelid="+modelid+"&value="+val+"&fieldname="+field+"&action="+act+"&contentid="+content_id,
                    url:"<?php echo url('Admin/node/contenttest'); ?>",
                    async: false,
                    success:function(msg){
                        if(msg == 2){
                            str += '2,';
                            $("."+field).show();
                        }else {
                            str += '1,';
                            $("."+field).hide();
                        }
                    }
                });
            }


            if(type == 0){  //无
                var reg = null;
            }
            if(type == 1){  //邮箱
                var reg = /^([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
            }
            if(type == 2){  //固定电话号码
                var reg = /^(0[0-9]{2,3}\/-)?([2-9][0-9]{6,7})+(\/-[0-9]{1,4})?$/;
            }
            if(type == 3){  //手机号码
                var reg = /(^1[3|4|5|7|8][0-9]{9}$)/;
            }
            if(type == 4){  //邮政编码
                var reg = /^[1-9]\\d{5}$/;
            }
            if(type == 5){  //纯数字
                var reg = /^[0-9]*$/;
            }
            if(type == 6){  //纯英文字母
                var reg = /^[a-zA-Z\d]+$/;
            }
            if(type == 7){  //纯中文
                var reg = /^[\u4e00-\u9fa5]+$/;
            }
            if(type == 8){  //自定义正则
                var reg = $(".input").attr("reg");
            }

            //正则检测单行文本框是否正确
            if(reg !== null & val.length !== 0 ){

                if(reg.test(val)){
                    str += '1,';
                }else{
                    str += '2,';
                }
            }else{
                str += '1,';
            }
        }

        if(str.indexOf('2,') == -1){
            $('#handleposition').submit();
        }else {
            alert("输入有误，请检查！");
        }
    }
</script>

<script type="text/javascript">

    //创建ajax对象 查找
    $("#chazhao").click(function(){
        //给id=button 添加点击时间
        $.ajax({
            url:"<?php echo url('node/ajaxclick'); ?>",
            data:$("#form1").serialize(),
            type:"post",
            dataType:"json",
            success:function(msg){
                $("#table .tr2 td[value!='']").remove();

                //style="width: 100px;height: auto;border: darkgrey solid 1px;"
                for(var i=0;i<msg[1].length;i++){
                    $("<tr class='tr2'>"+
                        "<td style='border: darkgrey solid 1px;text-align: center;'><input type='checkbox' class='content' name='content' value='"+msg[1][i].title+","+msg[1][i].contentid+"'>"+"</td>"+
                        "<td style='border: darkgrey solid 1px;'>"+msg[1][i].contentid+"</td>"+
                        "<td style='border: darkgrey solid 1px;'>"+msg[1][i].title+"</td>"+"</tr>")
                        .appendTo($("#table"))
                }
            },
            error:function(msg){
                alert("服务器没有正确的处理");
            }
        })
    });

    //添加到相关内容
    $("#button").click(function (){
        selIDs=$("#selectcomtentids").val();
        var obj = $(".content");
        var check_val =[];
        for (var i = 0; i < obj.length; i++){
            if(obj[i].checked == true){
                if(selIDs.indexOf(obj[i].value.split(",")[1]+",")==-1) {
                    check_val.push(obj[i].value);
                    selIDs+= obj[i].value.split(",")[1]+",";
                    $("#selectcomtentids").val(selIDs);
                }
            }
        }

        for (var j=0;j<check_val.length;j++){
            var check_valux = [];
            check_valx = check_val[j].split(",");
            var tablecount = document.getElementById("tablex");
            var tdcount = tablecount.getElementsByClassName("yz");

            $("<tr class='tr3'>"+
                "<td><input type='hidden' class='content1' title='"+check_valx[0]+"'   name='contentx'value='"+check_valx[1]+"'>"+"</td>"+
                "<td class='yz'style='border: darkgrey solid 1px;'>"+check_valx[1]+"</td>"+
                "<td style='border: darkgrey solid 1px;'>"+check_valx[0]+"</td>"+
                "<td class='td1'style='border: darkgrey solid 1px;text-align: center;'><input type='button' style='border: none;background: none;'  code='"+check_valx[1]+"' class='shanchu'  name='button1'value='"+"删除"+"'>"+"</td>"
                +"</tr>").appendTo($("#tablex"))
        }
    });


    //操作删除
    $("#tablex").on('click',".shanchu",function(){
        var contentid =$("#selectcomtentids").val();//拿到id的值
        contentid = contentid.replace($(this).attr("code")+",","");
        $("#selectcomtentids").val(contentid);
        $(this).parent().parent().remove();
    });

    $("#tab").on('click',"a",function(){
        $(this).parent().parent().remove();
    });

    $(".div").on('click',"a",function(){
        $(this).parent().remove();
    });


    function DoCheck1()
    {
        var ch1=document.getElementsByName("content");
        if(document.getElementsByName("allChecked1")[0].checked==true)
        {
            for(var i=0;i<ch1.length;i++)
            {
                ch1[i].checked=true;
            }
        }else{
            for(var i=0;i<ch1.length;i++)
            {
                ch1[i].checked=false;
            }
        }
    }

    //删除
    $("#buttonx").click(function() {
        var boxes = document.getElementsByName("contentx");//获得contentx所有的节点
        var contentid =$("#selectcomtentids").val();//拿到id的值
        for(var i=boxes.length-1;i>=0;i--){
            if(boxes[i].checked == true){
                contentid = contentid.replace(boxes[i].value+",","");
                $("#selectcomtentids").val(contentid);
            }
        }
        for(var i=boxes.length-1;i>=0;i--){
            if(boxes[i].checked == true){
                tr = boxes[i].parentNode.parentNode;
                tr.parentNode.removeChild(tr);
            }
        }
    });

    //确认
    $("#queren").click(function () {
        //创建节点
        var filed = $(".btn-primary").attr("name");
        var se = $(".content1");
        var text = document.getElementsByClassName("test");


        obj = {};
        var title_val = [];
        //添加前
        for (var i=0;i<se.length;i++){
            obj[se[i].value]=1;
        }

        //添加后
        for(var k=0;k<text.length;k++){
            if(obj[text[k].value]){
                obj[text[k].value]=2;
            }
        }

        for(var key in obj){
            if(obj[key] == 1){
                title_val.push(key);
            }
        }

        console.log(obj);
        console.log(title_val);


        for(var j=0;j<title_val.length;j++){

            $("<tr>"+"<td>"+"<input type='text' readonly= 'true' name='"+filed+"[]' class='test' value="+title_val[j]+">"+"</td>"+"<td>"+"<a href='#none'>"+"删除"+"</a>"+"</td>"+"</tr>").appendTo($("#tab"));

        }
    })
</script>
</html>