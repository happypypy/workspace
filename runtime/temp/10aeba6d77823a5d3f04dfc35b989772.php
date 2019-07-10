<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:66:"D:\workspace\work\public/../application/admin\view\index\main.html";i:1561971916;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>

</title>
    <style type="text/css">
        .spanDiv{
            position:relative;
            width:5px;
            height:5px;
        }

        .list-box,.nodragDiv{
            position:relative;
            filter:alpha(opacity=100);
            opacity:1;
            margin-bottom:6px;
            background-color:#FFFFFF;
        }
    </style>

    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/home.css" rel="stylesheet" type="text/css" />

    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/tabledrag.js"></script>
    <script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>

        <script language="javascript" type="text/javascript">

            /////////////////////////////////////////////////////////////////////////////////////////////////////////
            //公共处理
            //
            ////////////////////////////////////////////////////////////////////////////////////////////////////////

            var StatusFlag=0;
            var ClickBtnObj=null;

            function showDiary(early,flag,senderobj)
            {
                StatusFlag=flag;
                ClickBtnObj=senderobj;
                if(early)
                {
                    document.getElementById("divDis").style.display="block";
                    return false;
                }else
                {
                    document.getElementById("divDis").style.display="none";
                    return true;
                }
            }

            function ClickBtn()
            {
                ClickBtnObj.disabled=true;
                if(StatusFlag==0)
                {$("#btnCheckOut").click();}
                if(StatusFlag==1)
                {$("#btnCheckIn").click();}
                document.getElementById('divDis').style.display='none';
            }
            var thistime ='';
            function ConvertDate(DateTime)
            {
                var r=/([\d]+)(\-)([\d]+)(\-)([\d]+)/;
                DateTime=DateTime.replace(r,"$1/$3/$5");
                return new Date(DateTime);
            }

            function showInfo(url)
            {
                win=window.open(url,'','toolbar=yes,location=yes,directories=yes,status=yes,scrollbars=yes,resizable=yes,menubar=yes');
                win.focus();
            }
            function showPage(url)
            {
                win=window.open(url,'','');
                win.focus();
                //parent.parent.IntegrateLinkTargetToLinktitle(url, $(getEvent()).text());
            }
            function showSonPage(url)
            {
                win=window.open(url,"","toolbar=yes,location=yes,width=600,height=600,directories=yes,status=yes,scrollbars=yes,resizable=yes,menubar=yes,top=300,left=300");
                win.focus();
            }

            function checkColumnWidth(v)
            {
                if (v == null) return false;
                v = v.trim();
                var reg = /^(?:[+-])?\d+$/;
                var arr = reg.exec(v);
                if (arr==null || arr.length <= 0) return false;
                v = Number(v);
                if (isNaN(v)) return false;
                return (v >= 0 && v <= 100);
            }
            function checkBoxRecCount(v)
            {
                if (v == null) return false;
                v = v.trim();
                var reg = /^(?:[+-])?\d+$/;
                var arr = reg.exec(v);
                if (arr==null || arr.length <= 0) return false;
                v = Number(v);
                if (isNaN(v)) return false;
                return (v > 0 && v <= 50);
            }
            function UrlEncode(s)
            {
                return s.replace(/\+/ig,"%2B");
            }

            /////////////////////////////////////////////////////////////////////////////////////////////////////////
            //提醒消息
            //
            ////////////////////////////////////////////////////////////////////////////////////////////////////////

            var remindmsgcount=0;
            //刷新工作状态
            function ReflashRemindBox()
            {
                return;
                try{
                    //
                    $("#userRemindbox").html("<li id=\"objloadingli\" style=\"margin:0 0 0 0;line-height:22px;padding-left:10px;\" ><img src=\"/static/images/box/loading.gif\" />loading</li>");
                    var i=1;
                    $.get("NewMain.aspx?Action=GetRemindListXML&t=" + (new Date()).getTime(),null,function(data){
                        var itemarrobj=$($.parseXML(data)).find("root").find("item");
                        remindmsgcount=itemarrobj.size();
                        if(remindmsgcount==0)
                        {
                            $("#userRemindbox").html("");
                        }

                        itemarrobj.each(function (index, domEle) {
                                var vurl=$(domEle).attr("viUrl");
                                if(vurl.length>0)
                                {
                                    AddRemindBox(vurl);

                                }else
                                {
                                    remindmsgcount=remindmsgcount-1;
                                    if(remindmsgcount==0)
                                    {
                                        $("#objloadingli").remove();
                                        if($("#userRemindbox").children().size()<=0)
                                        {
                                            //  $("#userRemindbox").html("<li id=\"objlinomsg\" style=\"margin:0 0 0 0;line-height:22px;padding-left:10px;\" ><a href=\"javascript:void(0);\">当前无待办事项</a></li>");
                                        }
                                    }
                                }
                            }

                        );
                    });


                }catch(e){
                    $("#userRemindbox").html("<li id=\"objlinomsg\" style=\"margin:0 0 0 0;line-height:22px;padding-left:10px;\" ><a href=\"javascript:void(0);\">当前无待办事项</a></li>");
                }
            }
            //添加提醒
            function AddRemindBox(url)
            {
                try{
                    $.get(url,null,function(data){
                        $($.parseXML(data)).find("root").find("item").each(function (index, domEle) {
                                var Title=$(domEle).attr("Title");
                                var innerUrl=$(domEle).attr("Url");
                                $("#userRemindbox").append($("<li style=\"margin:0 0 0 0;line-height:22px;padding-left:10px;\" />").html("<a href=\"javascript:void(0);\" onclick=\"parent.parent.IntegrateLinkTargetToLinktitle('"+innerUrl+"','提醒消息');return false;\">"+Title+"</a>"));


                                remindmsgcount=remindmsgcount-1;

                                if(remindmsgcount==0)
                                {

                                    $("#objloadingli").remove();
                                    if($("#userRemindbox").children().size()<=0)
                                    {
                                        $("#userRemindbox").html("<li id=\"objlinomsg\" style=\"margin:0 0 0 0;line-height:22px;padding-left:10px;\" ><a href=\"javascript:void(0);\">当前无待办事项</a></li>");
                                    }
                                }

                            }
                        );



                    });
                }catch(e){}
                remindmsgcount=remindmsgcount-1;

                if(remindmsgcount==0)
                {

                    $("#objloadingli").remove();
                    if($("#userRemindbox").children().size()<=0)
                    {
                        //$("#userRemindbox").html("<li id=\"objlinomsg\" style=\"margin:0 0 0 0;line-height:22px;padding-left:10px;\" ><a href=\"javascript:void(0);\">当前无待办事项</a></li>");
                    }
                }
            }
            function empty() {
                window.location.reload();
            }

            /////////////////////////////////////////////////////////////////////////////////////////////////////////
            //列表信息
            //
            ////////////////////////////////////////////////////////////////////////////////////////////////////////
            function rdl_doRemove(obj){
                var Code =$(obj).attr("value");
                $("#newmaindragtable").find("#DrogChild_"+Code).remove();
            }


            function rdl_doAdd(obj)
            {
                var Code =$(obj).attr("value");
                var Url= $(obj).attr("Vurl");
                var title= $(obj).attr("title");
                var Ico = $(obj).attr("ICO");
                var TopN = $(obj).attr("TopN");
                var NewOpen = $(obj).attr("NewOpen");
                var _wd=($("#newmaindragtable td").first().width()-16);
                var AppendHtml ="<div class=\"list-box\" code=\""+Code+"\" id=\"DrogChild_"+Code+"\">"+
                    "<table  class=\"list-box-title\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">"+
                    "<tr>"+
                    "<td    id=\"DrogChild_Title_"+Code+"\"  style=\"padding:2px;\">"+
                    "<span class=\"ico-listbox\"><img height=\"25\" border=\"0\"  src=\""+Ico+"\" alt=\"\" /></span><span  style=\"padding-left: 10px;\">"+title+"</span></div>"+
                    "</td>"+
                    "<td width=\"70\" >"+
                    "<span class=\"oa_btn-op\" onmouseover=\"$(this).children().show();\" onmouseout=\"$(this).children().hide();\">"+
                    "	<a href=\"javascript:void(0);\"  onclick=\"LessOrExpansionListBoxEditAre('"+Code+"');return false;\"><img src=\"/static/images/box/edit.gif\" alt=\"\" /></a>"+
                    "	<a  href=\"javascript:void(0);\"  onclick=\"FillThisContent('BoxConentPar_"+Code+"','"+Url+",{'TopN':"+TopN+",'NewOpen':"+NewOpen+"});return false;\"><img src=\"/static/images/box/refresh.gif\" alt=\"\" /></a>"+
                    "	<a href=\"javascript:void(0);\"  onclick=\"ListBoxRemover('"+Code+"');return false;\"><img src=\"/static/images/box/closetab.gif\" alt=\"\" /></a>"+
                    "	<a href=\"javascript:void(0);\"  IsOpen=\"true\" onclick=\"LessOrExpansionObj(this,'"+Code+"');return false;\"><img src=\"/static/images/box/open.gif\" alt=\"\" /></a>"+
                    "</span>"+
                    "</td>"+
                    "</tr>"+
                    "</table>"+
                    "<DIV class=\"boxExpandEdit\" id=\"boxExpandEdit_"+Code+"\" IsOpen=\"true\"  Vurl=\""+Url+"\">"+
                    "<TABLE>"+
                    "<TR>"+
                    "<TD>元素标题：</TD>"+
                    "<TD><INPUT style=\"WIDTH: 120px\" id=txtBoxTitle value=\""+title+"\" maxLength=50></TD>"+
                    "</TR>"+
                    "<TR>"+
                    "<TD>显示条数：</TD>"+
                    "<TD><INPUT style=\"WIDTH: 20px\" id=txtRecCount value=\""+TopN+"\" maxLength=2></TD>"+
                    "</TR>"+
                    "<TR>"+
                    "<TD>新窗体打开：</TD>"+
                    "<TD><INPUT id=chkNewOpen type=checkbox "+(NewOpen=="1"?"checked":"")+"></TD>"+
                    "</TR>"+
                    "<TR>"+
                    "<TD></TD>"+
                    "<TD>"+
                    "<input type=\"button\" value=\"确定\" onclik=\"SubmitListBoxEditConfig('"+Code+"')\" />"+
                    "<input type=\"button\" value=\"取消\" onclick=\"LessOrExpansionListBoxEditAre('"+Code+"');\"/>"+
                    "</TD>"+
                    "</TR>"+
                    "</TABLE>"+
                    "</DIV>"+
                    "<dl id=\"BoxConentPar_"+Code+"\" style=\"width:"+_wd+"px \" >"+
                    "</dl>"+
                    "<span class=\"list-box-bottom\"><em></em></span>"+
                    "</div>";
                $("#newmaindragtable").find("td").eq(0).append(AppendHtml);


                FillThisContent("BoxConentPar_"+Code,Url,{'TopN':TopN,'NewOpen':NewOpen});
                new Drag('DrogChild_Title_'+Code, 'DrogChild_'+Code,{DragEnd:"saveOrder",DragContainer:"newmaindragtable",itemSelectorClass:"list-box"});


            }

            //勾选列表，工作状态
            function ConfigCheck(obj)
            {

                var isChecked = obj.checked;
                obj.disabled=true;
                var Code =$(obj).attr("value");
                var Url= $(obj).attr("Vurl");
                var boxType = $(obj).attr("boxtype");

                if (!isChecked)//移除
                {
                    $.post({
                        url:"<?php echo url('admin/index/removebox'); ?>?t=" + (new Date()).getTime(),
                        data:{"Code":Code},
                        success: function(result)
                        {
                            if (result == 1)
                            {
                                if(boxType==1)
                                {rdl_doRemove(obj);}
                                else if(boxType==2)
                                {}
                                else if(boxType==3)
                                {DisplayBoxRemove(Code)}
                            }
                            else
                            {
                                alert("系统项，不能取消！");
                                obj.checked=true;
                            }
                        }
                    });
                }
                else//添加
                {

                    if( $("#DrogChild_"+ Code).html()!=undefined)  //已存在;
                    {
                        obj.disabled=false;
                        return ;
                    }
                    if (boxType == "2")//工作提醒
                    {
                        var removecheckbox="NewMain.aspx?Action=AddBox&Code=" + Code + "&t=" + (new Date()).getTime();
                        $.get(removecheckbox,null,function(data){
                            //var code=$(data).find('root').find('status').attr("Code");
                            //var msg = $(data).find('root').find('status').attr("Msg");
                            var code=$(data).find('status').attr("Code");
                            var msg = $(data).find('status').attr("Msg");
                            if (code != "OK")
                            {
                                ReflashRemindBox();
                            }
                        });

                    }
                    else if(boxType=="1")//列表
                    {
                        $.post({
                            url:"<?php echo url('admin/index/addbox'); ?>?t=" + (new Date()).getTime(),
                            data:{"Code":Code},
                            success: function(result)
                            {
                                if (result == 1)
                                {
                                    rdl_doAdd(obj);
                                }
                            }
                        });
                    }
                    else if(boxType=="3")//简介
                    {
                        $.post({
                            url:"<?php echo url('admin/index/addbox'); ?>?t=" + (new Date()).getTime(),
                            data:{"Code":Code},
                            success: function(result)
                            {
                                if (result == 1)
                                {
                                    addDisplayBox(obj);
                                }
                            }
                        });
                    }

                }
                obj.disabled=false;

            }

            ///增加简介
            function addDisplayBox(obj)
            {
                var Code =$(obj).attr("value");
                var Url= $(obj).attr("Vurl");
                var title= $(obj).attr("title");
                var Ico = $(obj).attr("ICO");
                var TopN = $(obj).attr("TopN");
                var NewOpen = $(obj).attr("NewOpen");

                var AppendHtml ="<div id=\"divDisplayBox_"+Code+"\" class=\"fixbox_line\" Vurl=\""+Url+"\">"+
                    "<div class=\"list-box-fixed\">"+
                    "<div class=\"list-box-fixed-title\">"+
                    "<span class=\"oa_btn-op\"> </span>"+
                    "<span class=\"ico-listboxfixed\"></span>"+title+
                    "</div>"+
                    "<ul id=\"DisplayBox_"+Code+"\">"+
                    "</ul>"+
                    "<span class=\"list-box-fixed-bottom\"><em></em></span>"+
                    "</div>"+
                    "</div>"
                $("#divDisplayBox").append(AppendHtml);

                ReflashDisplayBox(Code);

            }
            ///刷新简介
            function ReflashDisplayBox(code)
            {
                var Url= $("#divDisplayBox_"+code).attr("Vurl");
                $("#DisplayBox_"+code).html("");
                $.get(Url,null,function(data){
                    $($.parseXML(data)).find("root").find("item").each(function (index, domEle) {
                            var Title=$(domEle).attr("Title");
                            var datevar=$(domEle).attr("DateTime");
                            if(typeof($(domEle).attr("Url"))!="undefined"  && $(domEle).attr("Url")!=""){
                                // 文章时间戳
                                var dateTime = (new Date(datevar)).getTime()/1000;
                                var fiveTime = new Date();
                                // 五天前的时间戳
                                fiveTime.setDate(fiveTime.getDate() - 5);
                                fiveTime = fiveTime.getTime()/1000;
                                
                                if(code == 'newfeaturerecommend' && dateTime > fiveTime || code=='systemUpdate' && dateTime > fiveTime){
                                    Title = "<a href=\""+$(domEle).attr("Url")+"\" target='_blank' style='color:red;'>"+Title+"</a><em style=\"float:right;font-style:normal;margin-right:5px;color:red\">"+datevar+"</em>";
                                }else{
                                    Title = "<a href=\""+$(domEle).attr("Url")+"\" target='_blank'>"+Title+"</a><em style=\"float:right;font-style:normal;margin-right:5px;\">"+datevar+"</em>";
                                }
                            }
                            $("#DisplayBox_"+code).append($("<li style=\"margin:0 0 0 0;line-height:22px;padding-left:4px;padding-right:8px;\" />").html(Title));
                        }
                    );
                });
            }
            // 获取字符串
            function getDataStr(){

            }
            function DisplayBoxRemove(code){
                $("#divDisplayBox_"+code).remove();
            }

            //收起列表的拖拉表格
            function LessBox(imgobj,code)
            {
                $(imgobj).attr("IsOpen","false");
                $("#BoxConentPar_"+code).children().hide();
                $(imgobj).children().attr("src","/static/images/box/close.gif");


            }
            //展开列表的拖拉表格
            function ExpansionBox(imgobj,code)
            {
                $(imgobj).attr("IsOpen","true");
                $("#BoxConentPar_"+code).children().show();
                $(imgobj).children().attr("src","/static/images/box/open.gif");
            }
            function LessOrExpansionObj(imgobj,code)
            {

                var isOpen=$(imgobj).attr("IsOpen");

                if(isOpen=="true")
                {
                    LessBox(imgobj,code);
                }else
                {
                    ExpansionBox(imgobj,code);
                }
            }

            function ListBoxRemover(Code)
            {
                $.post({
                    url:"<?php echo url('admin/index/removebox'); ?>?t=" + (new Date()).getTime(),
                    data:{"Code":Code},
                    success: function(result)
                    {
                        if (result == 1)
                        {
                            $("#newmaindragtable").find("#DrogChild_"+Code).remove();
                            $("#fastboxCheckbox_"+Code).attr("checked",false);
                        }
                        else
                        {
                            alert("系统项，不能取消！");
                        }
                    }
                });

                //$("#newmaindragtable > tr > td > #DrogChild_"+Code).remove();

            }


            // 打开或者显示编辑区域
            function LessOrExpansionListBoxEditAre(code)
            {
                var isOpen=$("#boxExpandEdit_"+code).attr("IsOpen");
                if(isOpen=="true")
                {
                    ExpansionListBoxEditAre(code);

                }else
                {
                    LessListBoxEditAre(code);
                }
            }

            //收起编辑区域
            function LessListBoxEditAre(code)
            {
                $("#boxExpandEdit_"+code).attr("IsOpen","true");
                $("#boxExpandEdit_"+code).hide();
            }

            //展开编辑区域
            function ExpansionListBoxEditAre(code)
            {
                $("#boxExpandEdit_"+code).attr("IsOpen","false");
                $("#boxExpandEdit_"+code).show();
            }


            function SubmitListBoxEditConfig(code)
            {

                var topn=$("#boxExpandEdit_"+code).find("#txtRecCount").val();
                var customTitle=$("#boxExpandEdit_"+code).find("#txtBoxTitle").val();
                var NewOpen=$("#boxExpandEdit_"+code).find("#chkNewOpen").get(0).checked;
                $.post({
                    url:"<?php echo url('admin/index/setboxconfig'); ?>",
                    data:{"Code":code,'TopN':topn,'customTitle':customTitle,'NewOpen':(NewOpen?"1":"0")},
                    success: function(result)
                    {
                    }
                });
                LessListBoxEditAre(code);

                var VUrl= $("#boxExpandEdit_"+code).attr("Vurl");
                FillThisContent("BoxConentPar_"+code,VUrl,{"TopN":topn,"NewOpen":(NewOpen?"1":"0")});
            }

            function LessOrExpansionCheckBoxListObj(obj)
            {
                var isOpen=$(obj).attr("IsOpen");
                if(isOpen=="true")
                {
                    ExpansionCheckBox(obj);
                }else
                {
                    LessCheckBox(obj);
                }
            }



            function ExpansionCheckBox(obj)
            {
                $(obj).attr("IsOpen","false");
                $("#checklist1").show();
                $("#checklist1_ColumnConfig").show();
                $(obj).children().attr("src","/static/images/box/close.gif");
            }
            function LessCheckBox(obj)
            {
                $(obj).attr("IsOpen","true");
                $("#checklist1").hide();
                $("#checklist1_ColumnConfig").hide();
                $(obj).children().attr("src","/static/images/box/open.gif");
            }

            function LessOrExpansionCheckBoxListObj1(obj)
            {
                var isOpen=$(obj).attr("IsOpen");
                if(isOpen=="true")
                {
                    ExpansionCheckBox1(obj);
                }else
                {
                    LessCheckBox1(obj);
                }
            }



            function ExpansionCheckBox1(obj)
            {
                $(obj).attr("IsOpen","false");
                $("#checklist1_1").show();move
                $(obj).children().attr("src","/static/images/box/close.gif");
            }
            function LessCheckBox1(obj)
            {
                $(obj).attr("IsOpen","true");
                $("#checklist1_1").hide();
                $(obj).children().attr("src","/static/images/box/open.gif");
            }
            function changeTitle(obj){
                if(obj.offsetWidth>obj.parentElement.offsetWidth){
                    obj.title=obj.innerText;
                }else{
                    obj.title="";
                }
            }

            /////////////////////////////////////////////////////////////////////////////////////////////////////////
            //列表信息
            //
            ////////////////////////////////////////////////////////////////////////////////////////////////////////
            var indexxx=0;
            function FillThisContent(objid,url,postdata)
            {

                $("#"+objid).html("<dd ><em></em><a href='javascript:void(0);'><img src=\"/static/images/box/loading.gif\" />loading</a></dd>");
                $.post({
                    url:url+"?t=" + (new Date()).getTime(),
                    data:postdata,
                    success: function(data)
                    {
                        var AppendHtml="";
                        $($.parseXML(data)).find('root').find('item').each(function(index1, ele) {
                            var title = $(ele).attr("Title");
                            var url=$(ele).attr("Url");
                            var tempurl="";

                            if(url.toLowerCase().indexOf("showinfo(")>=0||url.toLowerCase().indexOf("showwin(")>=0||url.toLowerCase().indexOf("showpage(")>=0||url.toLowerCase().indexOf("customopen(")>=0)
                            {
                                tempurl=url+";";
                            }
                            else
                            {
                                indexxx=indexxx+1;
                                tempurl="javascript:parent.parent.IntegrateLinkTargetToLinktitle('"+url+"','桌面信息');"
                                //tempurl=" window.parent.parent.mainFrame.TabAdd('"+url+"','"+title+"','code"+indexxx+"')";
                            }

                            var datevar=$(ele).attr("DateTime");
                            AppendHtml=AppendHtml+"<dd  title='"+title+"' ><em >["+datevar+"]</em><a  href='javascript:void(0);' onclick=\""+tempurl+"\">"+title+"</a></dd>";

                        });
                        $("#"+objid).html("");
                        if($.trim(AppendHtml)=="")
                        {
                            AppendHtml="<dd ><em></em><a href='javascript:void(0);'  >没有数据</a></dd>";
                        }

                        $("#"+objid).html(AppendHtml);
                    }
                });
            }


            function SwListBoxUrl(url,type)
            {

            }

            function setColumnConfig(flag)
            {
                if(flag==1)
                {
                    $("#trColumn2Width").hide();
                    $("#trColumn3Width").hide();
                    $("#rdoColumn1").attr("checked",true);
                    $("#rdoColumn2").attr("checked",false);
                    $("#rdoColumn3").attr("checked",false);
                }else if(flag==2)
                {
                    $("#trColumn2Width").show();
                    $("#trColumn3Width").hide();
                    $("#rdoColumn1").attr("checked",false);
                    $("#rdoColumn2").attr("checked",true);
                    $("#rdoColumn3").attr("checked",false);
                }else if(flag==3){
                    $("#trColumn2Width").show();
                    $("#trColumn3Width").show();
                    $("#rdoColumn1").attr("checked",false);
                    $("#rdoColumn2").attr("checked",false);
                    $("#rdoColumn3").attr("checked",true);
                }
            }

            function checkColumnWidth(v)
            {
                if (v == null) return false;
                v = v.trim();
                var reg = /^(?:[+-])?\d+$/;
                var arr = reg.exec(v);
                if (arr==null || arr.length <= 0) return false;
                v = Number(v);
                if (isNaN(v)) return false;
                return (v >= 0 && v <= 100);
            }

            function SaveColumnConfig()
            {
                var oriColumnCount = 1;

                var newColumnCount = 0;
                if ($("#rdoColumn1").attr("checked")=="checked")
                {
                    newColumnCount = 1;
                }else  if ($("#rdoColumn2").attr("checked")=="checked")
                {
                    newColumnCount = 2;
                }
                else
                {
                    newColumnCount = 3;
                }
                var iColumn1Width = 0;//parseInt(GetDocument("txtColumn1Width").value);
                var iColumn2Width = 0;//parseInt(GetDocument("txtColumn2Width").value);
                var iColumn3Width = 0;//parseInt(GetDocument("txtColumn3Width").value);
                if (newColumnCount >= 1)
                {
                    if (!checkColumnWidth($("#txtColumn1Width").val()))
                    {
                        //alert("请输入正确的列宽。列宽必需为整数，并不能小于0或大于100。");
                        // document.getElementById("txtColumn1Width").focus();
                        $("#txtColumn1Width").focus();

                        return;
                    }
                    else
                    {
                        iColumn1Width = Number($("#txtColumn1Width").val());
                    }
                }
                if (newColumnCount >= 2)
                {
                    if (!checkColumnWidth($("#txtColumn2Width").val()))
                    {
                        //alert("请输入正确的列宽。列宽必需为整数，并不能小于0或大于100。");
                        //document.getElementById("txtColumn2Width").focus();
                        $("#txtColumn2Width").focus();
                        return;
                    }
                    else
                    {
                        iColumn2Width = Number($("#txtColumn2Width").val());
                    }
                }
                else
                {
                    iColumn2Width = 0;
                }

                if (newColumnCount >= 3)
                {
                    if (!checkColumnWidth($("#txtColumn3Width").val()))
                    {
                        //alert("请输入正确的列宽。列宽必需为整数，并不能小于0或大于100。");
                        $("#txtColumn3Width").focus();
                        return;
                    }
                    else
                    {
                        iColumn3Width = Number($("#txtColumn3Width").val());
                    }
                }
                else
                {
                    iColumn3Width = 0;
                }

                if (newColumnCount < oriColumnCount)
                {
                    var b = window.confirm("修改的列数比现在少，被删除的元素会转到前面列！确定此操作吗？");
                    if (!b) return;
                }

                if((iColumn1Width+iColumn2Width+iColumn3Width)>100)
                {
                    alert("设定列宽的总和不能操作100!");
                    return;
                }
                $.post({
                    url:"<?php echo url('admin/index/setuserconfig'); ?>?t=" + (new Date()).getTime(),
                    data:{"Column":newColumnCount,"Column1Width":iColumn1Width,"Column2Width":iColumn2Width,"Column3Width":iColumn3Width,"Action":"SetColumnConfig"},
                    success: function()
                    {
                        //  $("#btnReflashListBox").click();

                        window.location.reload();
                    }
                });
            }

            function showOA_btn_Op(flag,code,senderobj)
            {
                if(flag==0)
                {
                    $(senderobj).find("div").each(function(index, domEle){alert(index);$(domEle).show()});
                }else
                {
                    $(senderobj).find("a").each(function(index, domEle){$(domEle).hide()});

                }
            }

            function ShowWin(id,codepage,dataid,nodestateid)
            {
                var flowtransactstate=24;

                CustomOpen("webpage/index.aspx?code="+codepage+"&dataid="+dataid+"&nodestateid="+nodestateid+"&flowtransactstate="+flowtransactstate,"webpage_EditOper","公文办理","450","500");

            }
        </script>

    </head>
<body >
<span id="Span1"></span>
<span id="wwww"></span>
<form name="form1" method="post" action="" id="form1">
    <input type="hidden" id="CheckClassID" name="CheckClassID" value="" class="noneDisplay" />
    <input type="hidden" id="CheckClassDate" name="CheckClassDate" value="" class="noneDisplay" />
    <div class="oa_wrapper">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr class="oa_wrapper-display">
                <td class="oa_wrapper-top-arr-left">&nbsp;

                </td>
                <td class="oa_wrapper-top-arr-middle">
                </td>
                <td class="oa_wrapper-top-arr-right">&nbsp;

                </td>
            </tr>
            <tr>
                <td class="oa_wrapper-middle-arr-left oa_wrapper-display">
                </td>
                <td class="oa_wrapper-middle-arr-middle">
                    <div class="oa_location clearfix">
                        <span class="oa_ico-left"></span>location<span class="oa_ico-right"></span></div>
                    <div class="oa_main clearfix">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top">
                                    <input type="submit" name="btnReflashListBox" value="" id="btnReflashListBox" class="" style="display:none;" />
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" id="newmaindragtable">
                                        <tr>

                                           <?php if($UserIndexConfig['intcolumn1width']>0) {?>
                                            <td width="<?php echo $UserIndexConfig['intcolumn1width']; ?>%" valign="top" celltarget="cell1" nowrap >
                                                <?php if(is_array($UserIndexInfo) || $UserIndexInfo instanceof \think\Collection || $UserIndexInfo instanceof \think\Paginator): $i = 0; $__LIST__ = $UserIndexInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['sellsnum']==1 and $vo['inttype']==1): ?>
                                                <div class="list-box" code="<?php echo $vo['chrcode']; ?>" id="DrogChild_<?php echo $vo['chrcode']; ?>">
                                                    <table class="list-box-title" width="100%" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            <td id="DrogChild_Title_<?php echo $vo['chrcode']; ?>" style="padding:2px;">
                                                                <?php if($vo['chrico']!=''): ?>
                                                                <span class="ico-listbox">
                                                                    <img height="25" border="0" src="<?php echo $vo['chrico']; ?>" alt="" />
                                                                </span>
                                                                <?php endif; ?>
                                                                <span style="padding-left: 10px;"> <?php echo $vo['chrname']; ?></span>
                                                            </td>
                                                            <td width="70">
                                                                 <span class="oa_btn-op" onMouseOver="$(this).children().show();" onMouseOut="$(this).children().hide();">
                                                                      <a href="javascript:void(0);" onClick="LessOrExpansionListBoxEditAre('<?php echo $vo['chrcode']; ?>');return false;"><img src="/static/images/box/edit.gif" alt="" /></a>
                                                                      <a href="javascript:void(0);" onClick="FillThisContent('BoxConentPar_<?php echo $vo['chrcode']; ?>','<?php echo $vo['chrsrc']; ?>',{'TopN':<?php echo $vo['inttopn']; ?>,'NewOpen':<?php echo $vo['intopentype']; ?>});return false;"><img src="/static/images/box/refresh.gif" alt="" /></a>
                                                                      <a href="javascript:void(0);" onClick="ListBoxRemover('<?php echo $vo['chrcode']; ?>');return false;"><img src="/static/images/box/closetab.gif" alt="" /></a>
                                                                      <a href="javascript:void(0);" isopen="true" onclick="LessOrExpansionObj(this,'<?php echo $vo['chrcode']; ?>'); return false;"><img src="/static/images/box/open.gif" alt="" /></a>
                                                                 </span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <div class="boxExpandEdit" id="boxExpandEdit_<?php echo $vo['chrcode']; ?>" isopen="true" Vurl="<?php echo $vo['chrsrc']; ?>">
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    元素标题：</td>
                                                                <td>
                                                                    <input style="width: 120px" id="txtBoxTitle" value="<?php echo $vo['chrname']; ?>" maxlength="50"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    显示条数：</td>
                                                                <td>
                                                                    <input style="width: 20px" id="txtRecCount" value="<?php echo $vo['inttopn']; ?>" maxlength="2"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    新窗体打开：</td>
                                                                <td>
                                                                    <input id="chkNewOpen" type="checkbox" <?php echo $vo['intopentype']=='1'?'checked':''; ?>></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                </td>
                                                                <td>
                                                                    <input type="button" value="确定" onClick="SubmitListBoxEditConfig('<?php echo $vo['chrcode']; ?>');" />
                                                                    <input type="button" value="取消" onClick="LessOrExpansionListBoxEditAre('<?php echo $vo['chrcode']; ?>');" />
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <dl id="BoxConentPar_<?php echo $vo['chrcode']; ?>">
                                                    </dl>

                                                    <script type="text/javascript" language="javascript">
                                                        $(document).ready(function(){
                                                            FillThisContent('BoxConentPar_<?php echo $vo['chrcode']; ?>','<?php echo $vo['chrsrc']; ?>',{'TopN':<?php echo $vo['inttopn']; ?>,"NewOpen":<?php echo $vo['intopentype']; ?>});
                                                            new Drag('DrogChild_Title_<?php echo $vo['chrcode']; ?>', 'DrogChild_<?php echo $vo['chrcode']; ?>',{DragEnd:"saveOrder",DragContainer:"newmaindragtable",itemSelectorClass:"list-box"});
                                                        })
                                                    </script>

                                                    <span class="list-box-bottom"><em></em></span>
                                                </div>
                                                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                            </td>
                                            <?php
                                      }
                                      if($UserIndexConfig['intcolumn2width']>0) { ?>
                                            <td width="<?php echo $UserIndexConfig['intcolumn2width']; ?>%" valign="top" celltarget="cell2" nowrap >
                                                <?php if(is_array($UserIndexInfo) || $UserIndexInfo instanceof \think\Collection || $UserIndexInfo instanceof \think\Paginator): $i = 0; $__LIST__ = $UserIndexInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['sellsnum']==2 and $vo['inttype']==1): ?>
                                                <div class="list-box" code="<?php echo $vo['chrcode']; ?>" id="DrogChild_<?php echo $vo['chrcode']; ?>">
                                                    <table class="list-box-title" width="100%" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            <td id="DrogChild_Title_<?php echo $vo['chrcode']; ?>" style="padding:2px;">
                                                                <?php if($vo['chrico']!=''): ?>
                                                                <span class="ico-listbox">
                                                                    <img height="25" border="0" src="<?php echo $vo['chrico']; ?>" alt="" />
                                                                </span>
                                                                <?php endif; ?>
                                                                <span style="padding-left: 10px;"> <?php echo $vo['chrname']; ?></span>
                                                            </td>
                                                            <td width="70">
                                                                 <span class="oa_btn-op" onMouseOver="$(this).children().show();" onMouseOut="$(this).children().hide();">
                                                                      <a href="javascript:void(0);" onClick="LessOrExpansionListBoxEditAre('<?php echo $vo['chrcode']; ?>');return false;"><img src="/static/images/box/edit.gif" alt="" /></a>
                                                                      <a href="javascript:void(0);" onClick="FillThisContent('BoxConentPar_<?php echo $vo['chrcode']; ?>','<?php echo $vo['chrsrc']; ?>',{'TopN':<?php echo $vo['inttopn']; ?>,'NewOpen':<?php echo $vo['intopentype']; ?>});return false;"><img src="/static/images/box/refresh.gif" alt="" /></a>
                                                                      <a href="javascript:void(0);" onClick="ListBoxRemover('<?php echo $vo['chrcode']; ?>');return false;"><img src="/static/images/box/closetab.gif" alt="" /></a>
                                                                      <a href="javascript:void(0);" isopen="true" onclick="LessOrExpansionObj(this,'<?php echo $vo['chrcode']; ?>'); return false;"><img src="/static/images/box/open.gif" alt="" /></a>
                                                                 </span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <div class="boxExpandEdit" id="boxExpandEdit_<?php echo $vo['chrcode']; ?>" isopen="true" Vurl="<?php echo $vo['chrsrc']; ?>">
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    元素标题：</td>
                                                                <td>
                                                                    <input style="width: 120px" id="txtBoxTitle" value="<?php echo $vo['chrname']; ?>" maxlength="50"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    显示条数：</td>
                                                                <td>
                                                                    <input style="width: 20px" id="txtRecCount" value="<?php echo $vo['inttopn']; ?>" maxlength="2"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    新窗体打开：</td>
                                                                <td>
                                                                    <input id="chkNewOpen" type="checkbox" <?php echo $vo['intopentype']=='1'?'checked':''; ?>></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                </td>
                                                                <td>
                                                                    <input type="button" value="确定" onClick="SubmitListBoxEditConfig('<?php echo $vo['chrcode']; ?>');" />
                                                                    <input type="button" value="取消" onClick="LessOrExpansionListBoxEditAre('<?php echo $vo['chrcode']; ?>');" />
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <dl id="BoxConentPar_<?php echo $vo['chrcode']; ?>">
                                                    </dl>

                                                    <script type="text/javascript" language="javascript">
                                                        $(document).ready(function(){
                                                            FillThisContent('BoxConentPar_<?php echo $vo['chrcode']; ?>','<?php echo $vo['chrsrc']; ?>',{'TopN':<?php echo $vo['inttopn']; ?>,"NewOpen":<?php echo $vo['intopentype']; ?>});
                                                            new Drag('DrogChild_Title_<?php echo $vo['chrcode']; ?>', 'DrogChild_<?php echo $vo['chrcode']; ?>',{DragEnd:"saveOrder",DragContainer:"newmaindragtable",itemSelectorClass:"list-box"});
                                                        })

                                                    </script>

                                                    <span class="list-box-bottom"><em></em></span>
                                                </div>
                                                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                            </td>
                                            <?php
                                      }
                                      if($UserIndexConfig['intcolumn3width']>0) { ?>
                                            <td width="<?php echo $UserIndexConfig['intcolumn3width']; ?>%" valign="top" celltarget="cell3" nowrap >
                                                <?php if(is_array($UserIndexInfo) || $UserIndexInfo instanceof \think\Collection || $UserIndexInfo instanceof \think\Paginator): $i = 0; $__LIST__ = $UserIndexInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['sellsnum']==3  and $vo['inttype']==1): ?>
                                                <div class="list-box" code="<?php echo $vo['chrcode']; ?>" id="DrogChild_<?php echo $vo['chrcode']; ?>">
                                                    <table class="list-box-title" width="100%" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            <td id="DrogChild_Title_<?php echo $vo['chrcode']; ?>" style="padding:2px;">
                                                                <?php if($vo['chrico']!=''): ?>
                                                                <span class="ico-listbox">
                                                                    <img height="25" border="0" src="<?php echo $vo['chrico']; ?>" alt="" />
                                                                </span>
                                                                <?php endif; ?>
                                                                <span style="padding-left: 10px;"> <?php echo $vo['chrname']; ?></span>
                                                            </td>
                                                            <td width="70">
                                                                 <span class="oa_btn-op" onMouseOver="$(this).children().show();" onMouseOut="$(this).children().hide();">
                                                                      <a href="javascript:void(0);" onClick="LessOrExpansionListBoxEditAre('<?php echo $vo['chrcode']; ?>');return false;"><img src="/static/images/box/edit.gif" alt="" /></a>
                                                                      <a href="javascript:void(0);" onClick="FillThisContent('BoxConentPar_<?php echo $vo['chrcode']; ?>','<?php echo $vo['chrsrc']; ?>',{'TopN':<?php echo $vo['inttopn']; ?>,'NewOpen':<?php echo $vo['intopentype']; ?>});return false;"><img src="/static/images/box/refresh.gif" alt="" /></a>
                                                                      <a href="javascript:void(0);" onClick="ListBoxRemover('<?php echo $vo['chrcode']; ?>');return false;"><img src="/static/images/box/closetab.gif" alt="" /></a>
                                                                      <a href="javascript:void(0);" isopen="true" onclick="LessOrExpansionObj(this,'<?php echo $vo['chrcode']; ?>'); return false;"><img src="/static/images/box/open.gif" alt="" /></a>
                                                                 </span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <div class="boxExpandEdit" id="boxExpandEdit_<?php echo $vo['chrcode']; ?>" isopen="true" Vurl="<?php echo $vo['chrsrc']; ?>">
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    元素标题：</td>
                                                                <td>
                                                                    <input style="width: 120px" id="txtBoxTitle" value="<?php echo $vo['chrname']; ?>" maxlength="50"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    显示条数：</td>
                                                                <td>
                                                                    <input style="width: 20px" id="txtRecCount" value="<?php echo $vo['inttopn']; ?>" maxlength="2"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    新窗体打开：</td>
                                                                <td>
                                                                    <input id="chkNewOpen" type="checkbox" <?php echo $vo['intopentype']=='1'?'checked':''; ?>></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                </td>
                                                                <td>
                                                                    <input type="button" value="确定" onClick="SubmitListBoxEditConfig('<?php echo $vo['chrcode']; ?>');" />
                                                                    <input type="button" value="取消" onClick="LessOrExpansionListBoxEditAre('<?php echo $vo['chrcode']; ?>');" />
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <dl id="BoxConentPar_<?php echo $vo['chrcode']; ?>">
                                                    </dl>

                                                    <script type="text/javascript" language="javascript">
                                                        $(document).ready(function(){
                                                            FillThisContent('BoxConentPar_<?php echo $vo['chrcode']; ?>','<?php echo $vo['chrsrc']; ?>',{'TopN':<?php echo $vo['inttopn']; ?>,"NewOpen":<?php echo $vo['intopentype']; ?>});
                                                            new Drag('DrogChild_Title_<?php echo $vo['chrcode']; ?>', 'DrogChild_<?php echo $vo['chrcode']; ?>',{DragEnd:"saveOrder",DragContainer:"newmaindragtable",itemSelectorClass:"list-box"});
                                                        })

                                                    </script>

                                                    <span class="list-box-bottom"><em></em></span>
                                                </div>
                                                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                            </td>
                                            <?php } ?>

                                        </tr>
                                    </table>

                                </td>
                                <td width="240" valign="top">
                                    <div id="MessageBoxUPdatePanel">

                                        <input type="submit" name="btnCheckIn" value="" id="btnCheckIn" class="noneDisplay" class="noneDisplay" style="display: none" />
                                        <input type="submit" name="btnCheckOut" value="" id="btnCheckOut" class="noneDisplay" class="noneDisplay" style="display: none" />

                                        <div class="list-box-fixed" style=" display:none">
                                            <div class="list-box-fixed-title">
                                                <span class="ico-listboxfixed"></span>中班</div>
                                            <dl class="clearfix">
                                                <dd>
                                                    <button onclick="$('#CheckClassID').val(170);$('#CheckClassDate').val('2017-07-26');if(!showDiary(true,1,this))return false;$('#btnCheckIn').click();"  class='oa_input-submit'><span style='color:#FF0000;cursor:hand;'>上班签到</span></button>
                                                    <button onclick="$('#CheckClassID').val(170);$('#CheckClassDate').val('2017-07-26');if(!showDiary(true,0,this))return false;$('#btnCheckOut').click();"   class='oa_input-submit'><span style='color:#FF0000;cursor:hand;'>下班签到</span></button>
                                                </dd>
                                            </dl>
                                            <span class="list-box-fixed-bottom"><em></em></span>
                                        </div>

                                    </div>

                                    <div class="fixbox_line">
                                        <div class="list-box-fixed">
                                            <div class="list-box-fixed-title">
                                                <span class="oa_btn-op"><a href="javascript:void(0);" isopen="true" onClick="LessOrExpansionCheckBoxListObj(this); return false;">
                                                        <img src="/static/images/box/open.gif" alt="" /></a> </span>
                                                <span class="ico-listboxfixed"></span>
                                                个性首页设置

                                            </div>
                                            <ul id="checklist1" style="display: none;">
                                                <?php if(is_array($IndexConfig) || $IndexConfig instanceof \think\Collection || $IndexConfig instanceof \think\Paginator): $i = 0; $__LIST__ = $IndexConfig;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <li>
                                                    <input  id="fastboxCheckbox_<?php echo $vo['chrcode']; ?>" TopN="5" NewOpen="0" name="FastBoxCheckBox"  <?php echo $vo['distype']>2?'disabled':''; ?>
                                                           type="checkbox" value="<?php echo $vo['chrcode']; ?>" vurl="<?php echo $vo['chrsrc']; ?>" boxtype="<?php echo $vo['inttype']; ?>"  onclick="ConfigCheck(this);"
                                                           title="<?php echo $vo['chrname']; ?>" ico="<?php echo $vo['chrico']; ?>"  <?php echo $vo['checked']; ?> />
                                                    <?php echo $vo['chrname']; ?>
                                                </li>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>

                                            </ul>

                                            <table class="b_line"  width="98%" cellpadding="1" cellspacing="1" border="0" id="checklist1_ColumnConfig"  style="display: none; border-left:#c0c0c0 1px solid; border-top:#dedede solid 1px;"  >
                                                <tr>
                                                    <td style=" padding-top:7px;">&nbsp;首页排版：</td>
                                                    <td>
                                                        <input id="rdoColumn1" <?php echo $UserIndexConfig['intcolumn']==1?"checked='true'":""; ?> type="radio" name="rdoColumn" onClick="javascript:setColumnConfig(1);">1列
                                                        <input id="rdoColumn2" <?php echo $UserIndexConfig['intcolumn']==2?"checked='true'":""; ?> type="radio" name="rdoColumn" onClick="javascript:setColumnConfig(2);">2列
                                                        <input id="rdoColumn3" <?php echo $UserIndexConfig['intcolumn']==3?"checked='true'":""; ?> type="radio" name="rdoColumn" onClick="javascript:setColumnConfig(3);">3列
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td >&nbsp;列1宽度：</td>
                                                    <td><INPUT style="WIDTH: 40px" id="txtColumn1Width" value='<?php echo $UserIndexConfig['intcolumn1width']; ?>' maxLength="3">%</td>
                                                </tr>
                                                <tr <?php echo $UserIndexConfig['intcolumn']<2?"style='display:none;'":""; ?>  id="trColumn2Width">
                                                    <td>&nbsp;列2宽度：</td>
                                                    <td><INPUT style="WIDTH: 40px"  id="txtColumn2Width" value='<?php echo $UserIndexConfig['intcolumn2width']; ?>' maxLength="3">%</td>
                                                </tr>
                                                <tr <?php echo $UserIndexConfig['intcolumn']<3?"style='display:none;'":""; ?>  id="trColumn3Width">
                                                    <td>&nbsp;列3宽度：</td>
                                                    <td><INPUT style="WIDTH: 40px" id="txtColumn3Width" value='<?php echo $UserIndexConfig['intcolumn3width']; ?>' maxLength="3">%</td>
                                                </TR>
                                                <tr>
                                                    <td></td>
                                                    <td>

                                                        <button   class='oa_input-submit' onClick="javascript:SaveColumnConfig();return false;">保存</button></td>
                                                </tr>
                                            </table>
                                            <span class="list-box-fixed-bottom"><em></em></span>
                                        </div>
                                    </div>
                                    <input type="submit" name="btnClick" value="" id="btnClick" style="display: none;" />

                                    <div id="divDisplayBox">
                                    <?php if(is_array($UserIndexInfo) || $UserIndexInfo instanceof \think\Collection || $UserIndexInfo instanceof \think\Paginator): $i = 0; $__LIST__ = $UserIndexInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['inttype']==3): ?>
                                        <div id="divDisplayBox_<?php echo $vo['chrcode']; ?>" class="fixbox_line" Vurl="<?php echo $vo['chrsrc']; ?>">
                                            <div class="list-box-fixed">
                                                <div class="list-box-fixed-title">
                                                    <span class="oa_btn-op"> </span>
                                                    <span class="ico-listboxfixed"></span><?php echo $vo['chrname']; ?>
                                                </div>
                                                <ul id="DisplayBox_<?php echo $vo['chrcode']; ?>">
                                                </ul>
                                                <span class="list-box-fixed-bottom"><em></em></span>
                                            </div>
                                            <script type="text/javascript" language="javascript">
                                                $(document).ready(function(){
                                                    ReflashDisplayBox("<?php echo $vo['chrcode']; ?>");
                                                })
                                            </script>
                                        </div>
                                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                    </div>
                                    <!--
                                    <div class="fixbox_line">
                                        <div class="list-box-fixed">
                                            <div class="list-box-fixed-title">
                                                <span class="oa_btn-op"><a href="javascript:void(0);"
                                                                           onclick="ReflashRemindBox(); return false;">
                                                        <img src="/static/images/box/refresh.gif" alt="" /></a> </span>

                                                <span class="ico-listboxfixed"></span>提醒信息
                                            </div>
                                            <ul id="userRemindbox">

                                            </ul>

                                            <span class="list-box-fixed-bottom"><em></em></span>
                                        </div>
                                    </div>-->
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td class="oa_wrapper-middle-arr-right oa_wrapper-display">
                </td>
            </tr>
            <tr class="oa_wrapper-display">
                <td class="oa_wrapper-bottom-arr-left">&nbsp;

                </td>
                <td class="oa_wrapper-bottom-arr-middle">
                </td>
                <td class="oa_wrapper-bottom-arr-right">&nbsp;

                </td>
            </tr>
        </table>
    </div>

    <div style="position:absolute; top:25%; left:40%; width:250px; border:#D8D8D8 solid 3px;  background: url(/static/Images/err_01.gif); height:120px;display:none;" id="divDis">
        <table width="100%" height="120" border="0" cellspacing="0" cellpadding="0" align="center">
            <tr>
                <td width="37%" height="35" align="right"><img src="/static/images/err_08.gif" alt="" /></td>
                <td style="font-size:14px; font-weight:bold; padding:2px 0 0 10px;">提示信息</td>
            </tr>
            <tr><td width="100%" align="center" valign="middle" colspan="2"><font color="#FF0000">现在不是正常签到时间，你确定打卡吗？</font></td>
            </tr><tr>
            <td colspan="2" align="center">
                <input name="btnOK" value="确定" type="button" style="background:url(/static/images/err_06.gif); border:none; color:#67633F; letter-spacing:3px;width:53px; height:19px; padding:2px 0 0 0;" onClick="ClickBtn()"/>
                &nbsp; &nbsp;&nbsp;&nbsp;<input name="btnCancel" value="取消" type="button" style="background:url(/static/images/err_06.gif); border:none; color:#67633F; letter-spacing:3px;width:53px; height:19px; padding:2px 0 0 0;" onClick="document.getElementById('divDis').style.display='none';ClickBtnObj.disabled=false;" />
            </td>
        </tr>

        </table>
    </div>

</form>
<input name="list1SortOrder" id="list1SortOrder" type="hidden" />

<script language="javascript" type="text/javascript">


    function aa()
    {

        $("table td dl").each(
            function(index, domEle){
                $(domEle).width($(domEle).parent().width());
            }
        );
    }
    aa();
    $(window).resize(function(){
        $("table td dl").each(
            function(index, domEle){
                $(domEle).width(10);
            }
        );
        setTimeout(aa,100);
    });
    function saveOrder() {
        var data = $("div.list-box").map(
            function() { return $(this).parent().attr("celltarget")+":"+$(this).attr("code"); }

        ).get();

        $("input[name=list1SortOrder]").val(data.join("|"));
        //alert(	$("input[name=list1SortOrder]").val());

        $.ajax({
            url:"<?php echo url('admin/index/sortorder',''); ?>?t=" + (new Date()).getTime(),
            data:{"value":$('#list1SortOrder').val(),"action":"UserSort"},
            success: function()
            {
            }
        });

    };
    <?php if(session('idsite') != 0 && session('isUpdateRemind') != 0): ?>
    CustomOpen('<?php echo url('systemupdate/tip'); ?>', 'node','系统更新', 500,50);
    <?php endif; if(session('idsite') != 0 && session('newFeaturesRemind') != 0): ?>
    CustomOpen('<?php echo url('newfeaturerecommend/tip'); ?>', 'node','新功能推荐', 500,50);
    <?php endif; ?>
</script>

</body>
</html>