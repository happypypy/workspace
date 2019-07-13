<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:73:"D:\workspace\work\public/../application/admin\view\pattern\fielddeal.html";i:1561691683;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
<link href="/static/css/page.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
<script src="/static/js/jquery-3.1.1.js" type="text/javascript"></script>

</head>
<body>
<script>
    function selectSettings(index)
    {
        switch(index)
        {
            case '2':
                break;
            case '3':
                break;
            default:
                index=1;
        }
        $("#GetType"+index).attr("checked","checked");
        showSettings(index);
    }
    function selectFileType()
    {
        var index=$("#FieldType").val();
        showJL(index);
        switch(index)
        {
            case '1':
                $(".tr").hide();
                $("#fieldtype").show();
                $("#divGetType").hide();
                break;
            case '7':
                $("#fieldtype").hide();
                $("#divGetType").show();
                break;
            case '4':
            case '5':
            case '6':
            case '19':
            case '20':
                $(".tr").hide();
                $("#fieldtype").hide();
                $("#divGetType").show();
                break;
            default:
                $(".tr").hide();
                $("#fieldtype").hide();
                $("#divGetType").hide();
        }
    }
    function showSettings(index)
    {
        if(index==1)
        {
            $("#settings3").hide();
            $("#settings2").hide();
            $("#settings1").show();
        }
        else if(index==2)
        {
            $("#settings1").hide();
            $("#settings2").show();
            $("#settings3").hide();
        }
        else if(index==3)
        {
            $("#settings1").hide();
            $("#settings2").hide();
            $("#settings3").show();
        }
        else
        {
            $("#settings1").hide();
            $("#settings2").hide();
            $("#settings3").hide();
        }
    }

    function checkForm1() {
        $("#submit").submit();
    }
</script>
<div class="oa_pop">
    <div style="height: 6px"></div>
  <div class="oa_pop-main">
  	<div class="oa_title clearfix">
      <span class="oa_ico-right"></span>
      <span class="oa_title-btn"></span>
      <span class="oa_ico-left"></span>
        <?php echo $request['action']=='add'?"添加配置项":"修改字段信息"; ?>
    </div>
  	<td class="oa_edition">
        <form action="<?php echo url('pattern/fieldpost'); ?>" method="post" enctype="multipart/form-data">
            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border: #e0e0e0 solid 1px;border-top: 0;">
                <tr>
                    <td class="oa_cell-left">字段名称：</td>
                    <td class="oa_cell-right">
                        <input type="text" class="form-control" readonly="true " name="FieldName" value="<?php echo $fieldinfo['fieldname']; ?>" ><br />
                        <div style="color: blue;">注：字段名由字母、数字、下划线组成，并且仅能字母开头，不以下划线结尾</div>
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left"><span style="color: red;">*</span>&nbsp;字段别名：</td>
                    <td class="oa_cell-right">
                        <input type="text" class="form-control" name="FieldAlias" value="<?php echo $fieldinfo['fieldalias']; ?>" ><br />
                        <div style="color: blue;">例如：新闻标题</div>
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">提示信息：</td>
                    <td class="oa_cell-right"><input name="Tips" type="text" class="form-control" value="<?php echo $fieldinfo['tips']; ?>" size="50" />
                        <br />
                        <div style="color: blue;">显示在字段别名下方作为重要提示的文字</div>
                    </td>
                </tr>
                <tr>
                    <td height="47" class="oa_cell-left">字段描述：</td>
                    <td class="oa_cell-right"><textarea name="Description" cols="50" rows="3" class="form-control"><?php echo $fieldinfo['remark']; ?></textarea>
                        <br />
                        <div style="color: blue;">对字段的描述或者说明</div>
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">是否在列表中显示：</td>
                    <td class="oa_cell-right">
                        <input type="checkbox" name="IsDisplayOnList" id="1" value=1 <?php echo $fieldinfo['isdisplayonlist']==1?"checked":""; ?> />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">是否为查询条件：</td>
                    <td class="oa_cell-right">
                        <input type="checkbox" name="IsSearch" id="1" value=1 <?php echo $fieldinfo['issearch']==1?"checked":""; ?> />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">是否允许为空：</td>
                    <td class="oa_cell-right">
                        <input type="checkbox" name="EnableNull" id="1" value=1 <?php echo $fieldinfo['enablenull']==1?"checked":""; ?> />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">是否唯一：</td>
                    <td class="oa_cell-right">
                        <input type="checkbox" name="isOnly" id="1" value=1 <?php echo $fieldinfo['isonly']==1?"checked":""; ?> />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">是否启用：</td>
                    <td class="oa_cell-right">
                        <input type="checkbox" name="isusing" id="1" value=1 <?php echo $fieldinfo['isusing']==1?"checked":""; ?> />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left"><span style="color: red;">*</span>&nbsp;字段类型：</td>
                    <td class="oa_cell-right">
                        <select onchange="javascript:selectFileType();" class="input-sm must" id="FieldType" name="fieldtype">
                            <option value="">请选择类型</option>
                            <option value="1" <?php echo $fieldinfo['fieldtype']==1?"selected":""; ?>>单行文本</option>
                            <option value="18"<?php echo $fieldinfo['fieldtype']==18?"selected":""; ?>>只读文本</option>
                            <option value="2" <?php echo $fieldinfo['fieldtype']==2?"selected":""; ?>>多行文本</option>
                            <option value="3" <?php echo $fieldinfo['fieldtype']==3?"selected":""; ?>>编辑器</option>
                            <option value="4" <?php echo $fieldinfo['fieldtype']==4?"selected":""; ?>>多选列表框</option>
                            <option value="5" <?php echo $fieldinfo['fieldtype']==5?"selected":""; ?>>复选框</option>
                            <option value="6" <?php echo $fieldinfo['fieldtype']==6?"selected":""; ?>>单选按钮</option>
                            <option value="7" <?php echo $fieldinfo['fieldtype']==7?"selected":""; ?>>下拉列表</option>
                            <option value="8" <?php echo $fieldinfo['fieldtype']==8?"selected":""; ?>>数字型</option>
                            <option value="9" <?php echo $fieldinfo['fieldtype']==9?"selected":""; ?>>日期</option>
                            <option value="22"<?php echo $fieldinfo['fieldtype']==22?"selected":""; ?>>日期和时间</option>
                            <option value="10" <?php echo $fieldinfo['fieldtype']==10?"selected":""; ?>>图片</option>
                            <option value="23" <?php echo $fieldinfo['fieldtype']==23?"selected":""; ?>>图片裁剪</option>
                            <option value="11" <?php echo $fieldinfo['fieldtype']==11?"selected":""; ?>>多图片</option>
                            <option value="12" <?php echo $fieldinfo['fieldtype']==12?"selected":""; ?>>文件</option>
                            <option value="13" <?php echo $fieldinfo['fieldtype']==13?"selected":""; ?>>多文件</option>
                            <option value="15" <?php echo $fieldinfo['fieldtype']==15?"selected":""; ?>>相关内容</option>
                            <option value="16" <?php echo $fieldinfo['fieldtype']==16?"selected":""; ?>>相关栏目</option>
                            <option value="17" <?php echo $fieldinfo['fieldtype']==17?"selected":""; ?>>相关栏目(多选)</option>
                            <option value="19" <?php echo $fieldinfo['fieldtype']==19?"selected":""; ?>>弹窗分页(单选)</option>
                            <option value="20" <?php echo $fieldinfo['fieldtype']==20?"selected":""; ?>>弹窗分页(多选)</option>
                            <option value="21" <?php echo $fieldinfo['fieldtype']==21?"selected":""; ?>>相关产品</option>
                        </select>
                    </td>
                </tr>
                <tr class="tr" id="is_next">
                    <td class="oa_cell-left">是否级联：</td>
                    <td class="oa_cell-right">
                        <input type="radio" name="child" value="1" <?php if($fieldinfo['childsetting'][0] == 1): ?>checked<?php endif; ?>>是
                        <input type="radio" name="child" value="2" <?php if($fieldinfo['childsetting'][0] != 1): ?>checked<?php endif; ?>>否
                    </td>
                </tr>
                <tr class="tr">
                    <td class="oa_cell-left">下级名称：</td>
                    <td class="oa_cell-right"><input type="text" name="childname" value="<?php echo $fieldinfo['childsetting'][1]; ?>" class="oa_input-300" ></td>
                </tr>
                <tr class="tr">
                    <td class="oa_cell-left">调用函数地址：</td>
                    <td class="oa_cell-right">
                        <input type="text" name="childurl" value="<?php echo $fieldinfo['childsetting'][2]; ?>" class="oa_input-300" >
                    </td>
                </tr>
                <tr class="tr">
                    <td class="oa_cell-left">调用函数：</td>
                    <td class="oa_cell-right">
                        <input type="text" name="func" value="<?php echo $fieldinfo['childsetting'][3]; ?>" class="oa_input-300" >
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">默认值：</td>
                    <td class="oa_cell-right">
                        <input name="defaultval" type="text" value="<?php echo $fieldinfo['defaultvalue']; ?>" class="oa_input-200" />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">文本框类型：</td>
                    <td class="oa_cell-right">
                        <input type="radio" name="txttype" value="1" <?php echo $fieldinfo['txttype']==1?"checked":""; ?> />普通 &nbsp;&nbsp;
                        <input type="radio" name="txttype" value="2" <?php echo $fieldinfo['txttype']==2?"checked":""; ?> />密码
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">文本框长度：</td>
                    <td class="oa_cell-right">
                        <input name="txtwidth" type="text" value="<?php echo $fieldinfo['txtwidth']; ?>" class="oa_input-200" />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">文本框高度：</td>
                    <td class="oa_cell-right">
                        <input name="txtheight" type="text" value="<?php echo $fieldinfo['txtheight']; ?>" class="oa_input-200" />
                    </td>
                </tr>
                <tr id="fieldtype" <?php if($fieldinfo['fieldtype'] != 1): ?>style="display:none"<?php endif; ?> >
                <td class="oa_cell-left"><span style="color: red;">*</span>&nbsp;验证规则：</td>

                <td class="oa_cell-right">
                    <select id="dropRegPattern" name="dropRegPattern" class="input-sm">
                        <option <?php if($fieldinfo['settings'][0] == 0): ?> selected="selected"<?php endif; ?> value="0">无</option>
                        <option <?php if($fieldinfo['settings'][0] == 1): ?> selected="selected"<?php endif; ?> value="1">Email地址</option>
                        <option <?php if($fieldinfo['settings'][0] == 2): ?> selected="selected"<?php endif; ?> value="2">固定电话号码</option>
                        <option <?php if($fieldinfo['settings'][0] == 3): ?> selected="selected"<?php endif; ?> value="3">手机号码</option>
                        <option <?php if($fieldinfo['settings'][0] == 4): ?> selected="selected"<?php endif; ?> value="4">邮政编码</option>
                        <option <?php if($fieldinfo['settings'][0] == 5): ?> selected="selected"<?php endif; ?> value="5">纯数字</option>
                        <option <?php if($fieldinfo['settings'][0] == 6): ?> selected="selected"<?php endif; ?> value="6">纯英文字母</option>
                        <option <?php if($fieldinfo['settings'][0] == 7): ?> selected="selected"<?php endif; ?> value="7">纯中文</option>
                        <option <?php if($fieldinfo['settings'][0] == 8): ?> selected="selected"<?php endif; ?> value="8">自定义正则</option>
                        <option <?php if($fieldinfo['settings'][0] == 9): ?> selected="selected"<?php endif; ?> value="9">自定义函数</option>
                    </select>

                    <br />
                    正则或函数名：
                    <input style="margin:10px 0"  name="txtRegPattern" class="form-control-auto" value="<?php echo $fieldinfo['settings'][1]; ?>"/>
                    <br />
                    错误提示信息：
                    <input name="txtErrorMessage" class="form-control-auto" value="<?php echo $fieldinfo['settings'][2]; ?>" />
                </td>

                </tr>
                <tr id="cj" <?php if($fieldinfo['fieldtype'] != 23): ?>style="display:none"<?php endif; ?>>
                    <td class="oa_cell-left">裁剪尺寸：</td>

                    <td class="oa_cell-right">
                        <input name="txtcjzc" class="form-control-auto" value="<?php echo $fieldinfo['txtcjzc']; ?>" />
                        <br /><div style="color: blue;">格式如:宽,高</div>
                    </td>
                </tr>
                <tr id="divGetType" <?php if(in_array($fieldinfo['fieldtype'],$fieldtype)): ?>style="display:none"<?php endif; ?>>
                <td class="oa_cell-left">多项选择属性列表&nbsp;<span style="color: red;">*</span>&nbsp;：</td>
                <td class="oa_cell-right">
                    <table border="0">
                        <tr>
                            <td><input id="GetType1" onclick="javascript:showSettings(1)" type="radio" value="1" name="GetType" <?php if($fieldinfo['settings'][0] == 1): ?>checked="checked"<?php endif; ?> />手动设置列表值&nbsp;&nbsp;</td>
                            <td><input id="GetType2" onclick="javascript:showSettings(2)" type="radio" value="2" name="GetType" <?php if($fieldinfo['settings'][0] == 2): ?>checked="checked"<?php endif; ?> />从表中自动获取&nbsp;&nbsp;</td>
                            <td><input id="GetType3" onclick="javascript:showSettings(3)" type="radio" value="3" name="GetType" <?php if($fieldinfo['settings'][0] == 3): ?>checked="checked"<?php endif; ?> />从数据字典中读取&nbsp;&nbsp;</td>
                        </tr>
                    </table>
                    <div id="settings1">
                        <textarea name="ModelName" cols="30" rows="4" class="form-control"><?php echo $fieldinfo['settings'][1]; ?></textarea>
                        <br /><div style="color: blue;">格式如:红色|1,白色|2</div>
                    </div>

                    <div id="settings2">
                        <br/>
                        <table border="0" class="table" style="margin-bottom:0px">
                            <tr>
                                <td width="100">数据表名：</td><td><input class="form-control-auto"  name="txtTableName" value="<?php echo $fieldinfo['settings'][2]; ?>" /></td>
                            </tr>
                            <tr>
                                <td>条件表达式：</td><td><input class="form-control-auto"  name="txtExpression" value="<?php echo $fieldinfo['settings'][3]; ?>"  /><br/><div style="color: blue;">例如：chrName like '%深圳%' and idUser&gt;5 </div></td>
                                </td>
                            </tr>
                            <tr>
                                <td>获取条数：</td><td><input  class="form-control-auto"   name="txtGetNum" value="<?php echo $fieldinfo['settings'][4]; ?>"  /></td>
                            </tr>
                            <tr>
                                <td>文本字段：</td><td><input  class="form-control-auto"  name="txtTextColumnName" value="<?php echo $fieldinfo['settings'][5]; ?>"  /></td>
                            </tr>
                            <tr>
                                <td>值字段：</td><td><input class="form-control-auto"  name="txtValueColumnName" value="<?php echo $fieldinfo['settings'][6]; ?>"  /></td>
                            </tr>
                        </table>
                    </div>
                    <div id="settings3">
                        <table  class="table" style="margin-bottom:0px">
                            <tr>
                                <td width="50">&nbsp;&nbsp;字典：</td>
                                <td>
                                    <select name="selDictionary">
                                        <?php if(is_array($dic) || $dic instanceof \think\Collection || $dic instanceof \think\Paginator): $i = 0; $__LIST__ = $dic;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                        <option value="<?php echo $vo['code']; ?>"><?php echo $vo['name']; ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>


                </td>
                </tr>

                <tr>
                    <td class="oa_cell-left">排序：</td>
                    <td class="oa_cell-right">
                        <input name="idorder" type="text" value="<?php echo $fieldinfo['idorder']; ?>" class="oa_input-200" />
                    </td>
                </tr>
                <tr>
                    <td class="oa_cell-left">最大字符数：</td>
                    <td class="oa_cell-right">
                        <input name="maxlength" type="text" value="<?php echo $fieldinfo['maxlength']; ?>" class="oa_input-200" />
                    </td>
                </tr>
                <tr><td style="padding:10px;"><input type="submit" id="submit" value="确定"/></td></tr>
                <tr>
                    <td>
                        <input type="hidden" name="idmodel" value="<?php echo $request['idmodel']; ?>">
                        <input type="hidden" name="idfield" value="<?php echo $request['idfield']; ?>">
                        <input type="hidden" name="action" value="<?php echo $request['action']; ?>">
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div style="height: 6px"></div>
  </div>
</div>
</body>
<script type="text/javascript">

    showSettings(<?php echo $fieldinfo['settings'][0]; ?>);
    function showJL(indexI)
    {
        indexI=parseInt(indexI);
        if(indexI==7)
        { $(".tr").show();}
        else
        { $(".tr").hide();}

        if(indexI==23)
        { $("#cj").show();}
        else
        { $("#cj").hide();}


        switch(indexI)
        {

            case 1:
                $("#fieldtype").show();
                $("#divGetType").hide();
                break;
            case 7:
                $("#fieldtype").hide();
                $("#divGetType").show();

                break;
            case 4:
            case 5:
            case 6:
            case 19:
            case 20:
                $("#fieldtype").hide();
                $("#divGetType").show();
                break;
            default:
                $("#fieldtype").hide();
                $("#divGetType").hide();
        }
    }

    var filed_val = '<?php echo $fieldinfo['fieldtype']; ?>';
    showJL(filed_val);


</script>
</html>