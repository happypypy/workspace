<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\Db;
use think\wx\Utils\HttpCurl;
use app\admin\module\Common;
use think\Log;
use think\db\Query;
/**

 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * I('id',0); 获取id参数 自动判断get或者post
 * I('post.name','','htmlspecialchars'); 获取$_POST['name']
 * I('get.'); 获取$_GET
 * </code>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @param mixed $datas 要获取的额外数据源
 * @return mixed
 */
function I($name,$default='',$filter=null,$datas=null) {

    $value = input($name,'',$filter);
    if($value !== null && $value !== ''){
        return $value;
    }
    if(strstr($name, '.'))
    {
        $name = explode('.', $name);
        $value = input(end($name),'',$filter);
        if($value !== null && $value !== '')
            return $value;
    }
    return $default;
}

/**
 * Think 系统函数库
 */

/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function C($name=null, $value=null,$default=null) {
    static $_config = array();
    // 无参数时获取所有
    if (empty($name)) {
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtoupper($name);
            if (is_null($value)) // 如果value 为空说明是设置值
                return isset($_config[$name]) ? $_config[$name] : $default;
            $_config[$name] = $value;
            return null;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);  //  这里可能是 类似于  DB.HOST 二位数组的  获取值 其实就是  $_config[DB][HOST]
        $name[0]   =  strtoupper($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
        $_config[$name[0]][$name[1]] = $value;
        return null;
    }
    // 批量设置
    if (is_array($name)){
        $_config = array_merge($_config, array_change_key_case($name,CASE_UPPER)); // array_change_key_case 将数组 所有键转换为大写之后 再来合并
        return null;
    }
    return null; // 避免非法参数
}

/**
 * 抛出异常处理
 * @param string $msg 异常消息
 * @param integer $code 异常代码 默认为0
 * @throws Think\Exception
 * @return void
 */
function E($msg, $code=0) {
    throw new Think\Exception($msg, $code);
}



//获得相关内容
function getContent(){
    return db('content')->select();
}
//获得相关栏目
function getNode(){
    return db('cms_node')->select();
}

//存取数据库转译问题
function HtmlEncode($fString)
{
    if($fString!="")
    {
        $fString = str_replace( '>', '&gt;',$fString);
        $fString = str_replace( '<', '&lt;',$fString);
        $fString = str_replace( chr(32), '&nbsp;',$fString);
        $fString = str_replace( chr(13), ' ',$fString);
        $fString = str_replace( chr(10) & chr(10), '<br>',$fString);
        $fString = str_replace( chr(10), '<BR>',$fString);
    }
    return $fString;
}
function EncodeHtml($fString)
{
    if($fString!="")
    {
        $fString = str_replace("&gt;" , ">", $fString);
        $fString = str_replace("&lt;", "<", $fString);
        $fString = str_replace("&nbsp;",chr(32),$fString);
        $fString = str_replace("",chr(13),$fString);
        $fString = str_replace("<br>",chr(10) & chr(10),$fString);
        $fString = str_replace("<BR>",chr(10),$fString);
    }
    return $fString;
}



//取得控件
//$v内容字段的所有集合
function getControl($v,$value=null,$prefix=null)
{
    //选择语言
    $arr_lang = config("admin_lang_list");
    //前台调用配置语言
    if(empty($arr_lang)){
        $arr_lang = ['cn'=>'简体中文'];
    }
    $indexTmp=0;
    $langIndex=0;
    foreach ($arr_lang as $key=>$vo){
        if($key."_"==$prefix) {
            $langIndex=$indexTmp;
            break;
        }
        $indexTmp++;
    }

    //给字段加前缀，获取不同语言的字段
    if($prefix == "cn_"){
        $prefix = '';
    }

    //1单行文本,2多行文本,3编辑器,4多选列表框,5复选框,6单选按钮,7下拉列表,8数字型,9日期,10图片,11多图片,12文件,13多文件,15相关内容,16相关栏目,17相关栏目(多选),18只读文本,19弹窗分页(单选),20弹窗分页(多选),21相关产品,22日期和时间,23图片裁剪
    $ControlType=intval($v['fieldtype']);

    $style=intval($v['txtwidth'])>0?"width:".intval($v['txtwidth'])."px;":"";
    $style=$style.(intval($v['txtheight'])>0?"height:".intval($v['txtheight'])."px;":"");
    if(array_key_exists('enablenull',$v)){
        $is_null = $v['enablenull'];
    }else{
        $is_null = '';
    }
    if(array_key_exists('isonly',$v)){
        $is_only = $v['isonly'];
    }else{
        $is_only = '';
    }

    if($style!="")
        $style="style=\"".$style."\"";
    switch($ControlType)
    {
        case 1:
            $arr=getSettingData1($v['settings']);
            if($v['txttype']==2){
                return "<input sign=\"$arr[0]\" is_only=\"$is_only\" is_null=\"".$is_null."\"  reg=\"$arr[1]\" tip=\"$arr[2]\" type=\"password\" id=\"".$prefix.$v['fieldname']."\" name=\"".$prefix.$v['fieldname']."\" id=\"".$prefix.$v['fieldname']."\" class=\"form-control oa_input-200 input\" ".$style." value=\"".$value."\">";
            }else{
                return "<input reg=\"".$arr[1]."\" tip=\"".$arr[2]."\" sign=\"".$arr[0]."\" is_only=\"$is_only\" is_null=\"".$is_null."\" type=\"text\" id=\"".$prefix.$v['fieldname']."\" name=\"".$prefix.$v['fieldname']."\" id=\"".$prefix.$v['fieldname']."\" class=\"form-control oa_input-200 input\" ".$style." value=\"".$value."\">";
            }

        case 2:
            return "<textarea id=\"".$prefix.$v['fieldname']."\" name=\"".$prefix.$v['fieldname']."\" is_null=\"$is_null\" cols=\"50\" rows=\"3\"  class=\"form-control input oa_input-200\"  ".$style.">".$value."</textarea>";
        case 3:
            //print_r($value);
            /*  <textarea id="editor_id" name="content" style="width:700px;height:300px;">
               &lt;strong&gt;请输入内容&lt;/strong&gt;
               </textarea>*/
            //$strTmp="<script>var editor".$v['fieldname'].";$(function(){editor".$v['fieldname']." = new UE.ui.Editor(options);editor".$v['fieldname'].".render(\"post_".$v['fieldname']."\");});</script>";
            //return "<div style='width:100%'><textarea class=\"span12 ckeditor\" id=\"post_".$v['fieldname']."\" name=\"".$v['fieldname']."\">".$value."</textarea></div>\r\n".$strTmp;
            $strTmp="<script>var editor".$prefix.$v['fieldname'].";$(function(){editor".$prefix.$v['fieldname']." = new UE.ui.Editor(options);editor".$prefix.$v['fieldname'].".render(\"post_".$prefix.$v['fieldname']."\");});</script>";
            return "<div ><textarea  $style class=\"span12 ckeditor\" id=\"post_".$prefix.$v['fieldname']."\" is_null=\"$is_null\" name=\"".$prefix.$v['fieldname']."\">".$value."</textarea></div>\r\n".$strTmp;
        case 4://多选列表框
            $strTmp="<select is_null=\"$is_null\" class=\"input-sm input\" id=\"".$prefix.$v['fieldname']."\" name=\"".$prefix.$v['fieldname'].'[]'."\" multiple=\"multiple\">";
            $arr=getSettingData($v['settings']);
            foreach($arr as $vo) {
                if(count($vo)>1) {
                    if ((is_array($value) && in_array($vo[1], $value)) || (is_string($value) && $value == $vo[1])) {
                        if (is_array($vo[0])) {
                            $strTmp = $strTmp . "<option  value=\"" . $vo[1] . "\"  " . "selected" . ">" . getArrValue($vo[0], $langIndex) . "</option>";
                        } else {
                            $strTmp = $strTmp . "<option  value=\"" . $vo[1] . "\"  " . "selected" . ">" . $vo[0] . "</option>";
                        }
                    } else {
                        if (is_array($vo[0])) {
                            $strTmp = $strTmp . "<option  value=\"" . $vo[1] . "\"  " . ">" . getArrValue($vo[0], $langIndex) . "</option>";
                        } else {
                            $strTmp = $strTmp . "<option  value=\"" . $vo[1] . "\"  " . ">" . $vo[0] . "</option>";
                        }
                    }
                }
            }
            return $strTmp."</select>";
        case 5://复选框

            $strTmp = "";
            $arr=getSettingData($v['settings']);
                foreach ($arr as $key=>$vo){
                    if((is_array($value) && in_array($vo[1],$value)) || (is_string($value) && $value==$vo[1])) {
                        if (is_array($vo[0])) {
                            $strTmp = $strTmp . "<input class=\"input\" type=\"checkbox\" name=\"" . $prefix . $v['fieldname'] . '[]' . "\"  value=\"" . $vo[1] . "\" checked>" . getArrValue($vo[0], $langIndex) . "&nbsp;&nbsp;&nbsp;";
                        } else {
                            $strTmp = $strTmp . "<input class=\"input\" type=\"checkbox\" name=\"" . $prefix . $v['fieldname'] . '[]' . "\"  value=\"" . $vo[1] . "\" checked>" . $vo[0] . "&nbsp;&nbsp;&nbsp;";
                        }
                    }else{
                        if (is_array($vo[0])) {
                            $strTmp = $strTmp . "<input class=\"input\" type=\"checkbox\" name=\"" . $prefix . $v['fieldname'] . '[]' . "\"  value=\"" . $vo[1] . "\" >" . getArrValue($vo[0], $langIndex) . "&nbsp;&nbsp;&nbsp;";
                        } else {
                            $strTmp = $strTmp . "<input class=\"input\" type=\"checkbox\" name=\"" . $prefix . $v['fieldname'] . '[]' . "\"  value=\"" . $vo[1] . "\" >" . $vo[0] . "&nbsp;&nbsp;&nbsp;";
                        }
                    }
                }
            return $strTmp;

        case 6://单选按钮
            $strTmp="";
            $arr=getSettingData($v['settings']);
            foreach ($arr as $vo) {
                if(is_array($vo[0])) {
                    $strTmp=$strTmp."<input class=\"input\" is_null=\"$is_null\" type=\"radio\" name=\"".$prefix.$v['fieldname']."\" value=\"".$vo[1]."\" ".($value==$vo[1]?"checked=\"checked\"":"")."> " .getArrValue($vo[0], $langIndex)."&nbsp;&nbsp;&nbsp;&nbsp;" ;
                }else{
                    $strTmp=$strTmp."<input class=\"input\" is_null=\"$is_null\" type=\"radio\" name=\"".$prefix.$v['fieldname']."\" value=\"".$vo[1]."\" ".($value==$vo[1]?"checked=\"checked\"":"")."> " .$vo[0]."&nbsp;&nbsp;&nbsp;&nbsp;" ;
                }
            }
            return $strTmp;
        case 7://下拉列表
           if(array_key_exists('childsetting',$v)){
               $arr1 = $v['childsetting'];
           }
            if(!empty($arr1) && $arr1[0]==1){
                $strTmp="<select $style id=\"".$prefix.$v['fieldname']."\" onchange=\"".$arr1[3]."\" is_null=\"$is_null\" class=\"input-sm\" id=\"".$prefix.$v['fieldname']."\" name=\"".$prefix.$v['fieldname']."\" >";
            }else{
                $strTmp="<select $style id=\"".$prefix.$v['fieldname']."\" is_null=\"$is_null\" class=\"input-sm\"  name=\"".$prefix.$v['fieldname']."\" >";
            }
            $strTmp = $strTmp . "<option  value=\"\">请选择</option>";
            $arr=getSettingData($v['settings']);
            if(count($arr)>0) {
                foreach ($arr as $vo) {
                    if( count($vo)>1) {
                        if (is_array($vo[0])) {
                            $strTmp = $strTmp . "<option  value=\"" . $vo[1] . "\"  " . ($value == $vo[1] ? "selected=\"selected\"" : "") . ">" . getArrValue($vo[0], $langIndex) . "</option>";
                        } else {
                            $strTmp = $strTmp . "<option  value=\"" . $vo[1] . "\" " . ($value == $vo[1] ? "selected=\"selected\"" : "") . ">" . $vo[0] . "</option>";
                        }
                    }
                }
            }
            return $strTmp."</select>";
        case 8://数字型

            return "<input $style sign=\"6\" class=\"form-control oa_input-200 input\" is_null=\"$is_null\" type=\"number\" id=\"".$prefix.$v['fieldname']."\" name=\"".$v['fieldname']."\" value=\"".$value."\">";
        /*$strTmp="onkeyup=\"this.value=this.value.replace(/[^\d]/g,'')\" onpaste=\"this.value=this.value.replace(/[^\d]/g,'')\"";
        return "<input type=\"text\" name=\"".$v['fieldname']."\" ".$strTmp ." class=\"form-control\" ".$style." value=\"".$value."\">";*/
        case 9://日期
            return "<div style=\"width:250px\" class=\"input-prepend input-group\">"."<span class=\"add-on input-group-addon\">"."<i class=\"glyphicon glyphicon-calendar fa fa-calendar\">"."</i>"."</span>"
                ."<input type=\"text\" is_null=\"$is_null\" id=\"".$prefix.$v['fieldname']."\" name=\"".$v['fieldname']."\" class=\"form-control\" ".$style." value=\"".$value."\">". "</div>"
                ."<script language='JavaScript'>seltime(\"".$prefix.$v['fieldname']."\",\"YYYY-MM-DD\")</script>";

        case 22://时间日期
            $strTmp="<div  style=\"width:250px\" class=\"input-prepend input-group\">"."<span class=\"add-on input-group-addon\">"."<i class=\"glyphicon glyphicon-calendar fa fa-calendar\">"."</i>"."</span>";
            $strTmp=$strTmp."<input type=\"text\" is_null=\"$is_null\" id=\"".$prefix.$v['fieldname']."\" name=\"".$v['fieldname']."\" class=\"form-control\" ".$style." value=\"".$value."\">";
            $strTmp=$strTmp."</div>"
                ."<script language='JavaScript'>seltime(\"".$prefix.$v['fieldname']."\",\"YYYY-MM-DD HH:mm:ss\")</script>";
            return $strTmp;
        case 10://单图片
            //因为要支持图片截图功能，更换之前的单图片上传代码。
            return
                "<input onclick=\"GetUploadify(1,'".$prefix.$v['fieldname']."','admin','undefined','*.jpg;*.jpeg;*.png;*.gif;*.jpeg;');\" type=\"button\" value=\"上传单图片\"/>".
                "<input $style type=\"text\" is_null=\"$is_null\" value=\"".$value."\" id=\"".$prefix.$v['fieldname']."\" name=\"".$v['fieldname']."\" class=\"form-control large\" readonly=\"readonly\"  style=\"width:200px;display:initial;\"/>"
                ;
        case 11://多图片
            if(empty($value)){
                return "<div class=\"col-sm-10\">".
                    "<input onclick=\"GetUploadify(5,'".$v['fieldname']."','','undefined','*.jpg;*.jpeg;*.png;*.gif;*.jpeg;');\" type=\"button\" value=\"上传多图片\" name=\"".$prefix.$v['fieldname']."[]\"/>".
                    "<div id=".$prefix.$v['fieldname']." />".
                    "</div>";
            }else{
                $temp = '';
                if(is_array($value)){
                    for($i=0;$i<count($value);$i++){
                        $temp.= "<div class='div'>"."<input id=\"".$prefix.$v['fieldname']."\" name=\"".$v['fieldname']."[]\" type=text value=\"".$value[$i]."\">"."<a href=\"#none\">删除</a>"."</div>";
                    }
                }else{
                    $temp.= "<div class='div'>"."<input id=\"".$prefix.$v['fieldname']."\" name=\"".$v['fieldname']."[]\" type=text value=\"".$value."\">"."<a href=\"#none\" href=\"\">删除</a>"."</div>";
                }

                return "<div class=\"col-sm-10\">".
                    "<input onclick=\"GetUploadify(5,'".$v['fieldname']."','','','*.jpg;*.jpeg;*.png;*.gif;');\" type=\"button\" value=\"上传多图片\"/>".
                    "<div id=".$v['fieldname'].">".
                    $temp."</div>"."</div>";
            }
        case 12://单文件
            return "<div class=\"col-sm-10\">".
                "<input onclick=\"GetUploadify(1,'".$v['fieldname']."','','');\" type=\"button\" value=\"上传单文件\"/>".
                "<input type=\"text\" is_null=\"$is_null\" value=\"".$value."\"  name=\"".$v['fieldname']."\" id=\"".$v['fieldname']."\" class=\"form-control large\" readonly=\"readonly\"  style=\"width:500px;display:initial;\"/>".
                "</div>";
        case 13://多文件
            if(empty($value)){
                return "<div class=\"col-sm-10\">".
                    "<input onclick=\"GetUploadify(5,'".$v['fieldname']."','','','*.txt;*.exe;*.docx;*.jpg;');\" type=\"button\" value=\"上传多文件\"/>".
                    "<div id=\"".$v['fieldname']."\" />".
                    "</div>";
            }else{
                $temp = '';
                if(is_array($value)){
                    foreach($value as $key=>$val){
                        $temp.= "<div class='div'>"."<input name=\"".$v['fieldname']."[]\" type=text value=\"".$val."\">"."<a href=\"#none\">删除</a>"."</div>";
                    }
                }else{
                    $temp.= "<div class='div'>"."<input name=\"".$v['fieldname']."[]\" type=text value=\"".$value."\">"."<a href=\"#none\">删除</a>"."</div>";
                }

                return "<div>".
                    "<input onclick=\"GetUploadify(5,'".$v['fieldname']."','','','*.txt;*.exe;*.docx;*.jpg;');\" type=\"button\" value=\"上传多文件\"/>".
                    "<div id=\"".$v['fieldname']."\">".
                    $temp."</div>"."</div>";
            }
        case 14:
            return "<span>".$value."</span>";
        case 15://相关内容
            $strTmp="<select class=\"input-sm input\" name=\"".$v['fieldname']."\">";
            $arr=getContent();
            foreach ($arr as $key =>$vo){
                $strTmp = $strTmp."<option value=\"".$vo['content']."\" >".$vo['content']."</option>";
            }
            return $strTmp."</select>";
        case 16://相关栏目
            $strTmp="<select is_null=\"$is_null\" class=\"input-sm input\" name=\"".$v['fieldname']."\">";
            $arr=getNode();
            if(empty($value)){
                foreach ($arr AS $key => $vo)
                {
                    $strTmp=$strTmp."<option  value=\"".$vo['node_id']."\" >".$vo['nodename']."</option>";
                }
                return $strTmp."</select>";
            }else{
                foreach ($arr AS $key => $vo)
                {
                    if($vo['node_id']==$value){
                        $strTmp=$strTmp."<option  value=\"".$vo['node_id']."\" selected=\"selected\" >".$vo['nodename']."</option>";
                    }else{
                        $strTmp=$strTmp."<option  value=\"".$vo['node_id']."\" >".$vo['nodename']."</option>";
                    }
                }
                return $strTmp."</select>";
            }

        case 17://相关栏目多选
            $arr=getNode();
            $strTmp="<select is_null=\"$is_null\" class=\"input-sm input\" name=\"".$v['fieldname']."[]\" size=\"5\" multiple=\"multiple\" >";
            if (empty($value)){
                foreach ($arr AS $key => $vo)
                {
                    $strTmp=$strTmp."<option  value=\"".$vo['node_id']."\" >".$vo['nodename']."</option>";
                }
                return $strTmp."</select>";
            }else{
                foreach ($arr as $key=>$vo){
                    if(is_array($value) && in_array($vo['node_id'],$value)){
                        $strTmp=$strTmp."<option value=\"".$vo['node_id']."\" selected=\"selected\">".$vo['nodename']."</option>";
                    }elseif(is_string($value) && $value==$vo['node_id']){
                        $strTmp=$strTmp."<option value=\"".$vo['node_id']."\" selected=\"selected\">".$vo['nodename']."</option>";
                    }else{
                        $strTmp=$strTmp."<option value=\"".$vo['node_id']."\">".$vo['nodename']."</option>";
                    }
                }
                return $strTmp."</select>";
            }
        case 18://只读文本
            if($v['txttype']==1){    //普通框
                $strTmp = "<input type=\"text\"  value=\"您选择的栏目是：\" readonly=\"true\">";
            }else{
                $strTmp = "<input type=\"password\"  value=\"您选择的栏目是：\" readonly=\"true\">";    //密码框
            }

            return $strTmp;
        case 19://弹窗
            if(empty($value)){
                return "<input type=\"button\" name=\"".$v['fieldname']."\" class=\"btn btn-primary btn-lg\" data-toggle=\"modal\" data-target=\"#myModal\" value=\"浏览\" >".
                    "<table id='tab'>"."<tr>"."<td>"."标题"."</td>"."<td>"."操作"."</td>"."</tr>"."</table>";
            }else{
                $temp =  "<table id='tab'>"."<tr>"."<td>"."标题"."</td>"."<td>"."操作"."</td>"."</tr>";
                if(is_array($value)){
                    foreach ($value as $key=>$val){
                        $temp .= "<tr>"."<td>"."<input readonly='true' type='text' class='test' name=\"".$prefix.$v['fieldname']."[]\" value=\"".$val."\">"."</td>"."<td><a href=\"#none\">"."删除"."</a></td>"."</tr>";
                    }
                }else{
                    $temp .= "<tr>"."<td>"."<input readonly='true' type='text' class='test' name=\"".$prefix.$v['fieldname']."[]\" value=\"".$value."\">"."</td>"."<td><a href=\"#none\">"."删除"."</a></td>"."</tr>";
                }
                return "<input type=\"button\" name=\"".$v['fieldname']."\" class=\"btn btn-primary btn-lg\" data-toggle=\"modal\" data-target=\"#myModal\" value=\"浏览\" >".
                    $temp."</table>";
            }
        case 20:
            return "<span>".$value."</span>";
        case 21:
            return "<span>".$value."</span>";
        //单图片,支持截图
        case 23:
            //因为要支持图片截图功能，更换之前的单图片上传代码。
//            return
//                "<input onclick=\"GetUploadify(1,'".$prefix.$v['fieldname']."','admin','undefined','*.jpg;*.jpeg;*.png;*.gif;*.jpeg;');\" type=\"button\" value=\"上传单图片\"/>".
//                "<input $style type=\"text\" is_null=\"$is_null\" value=\"".$value."\" id=\"".$prefix.$v['fieldname']."\" name=\"".$v['fieldname']."\" class=\"form-control large\" readonly=\"readonly\"  style=\"width:200px;display:initial;\"/>"
//                ;
            $tmp=$v['settings'];
            if(!strstr($tmp,','))
                $tmp="100,100";
            return "<input name='".$prefix.$v['fieldname']."' id='".$prefix.$v['fieldname']."' type='text' value='".$value."'  class=\"form-control \"  style=\"width:800px;\" />
                    <input style=\"display: none;\" onclick=\"GetUploadify(1'".$prefix.$v['fieldname']."','admin','undefined','*.jpg;*.jpeg;*.png;*.gif;*.jpeg;');\" type=\"button\" value=\"上传单图片\"/>
                    <input onclick=\"uploadimgcut('".$prefix.$v['fieldname']."','admin',".$tmp.");\" type=\"button\" value=\"上传单图片\"/>
                    <input onclick=\"openimg('".$prefix.$v['fieldname']."')\" type=\"button\" value=\"查看图片\"/>";
    }
    return "";
}

function formatListData($value,$Mdata)
{
    //1单行文本,2多行文本,3编辑器,4多选列表框,5复选框,6单选按钮,7下拉列表,8数字型,9日期,22日期和时间,10图片,11多图片,12文件,
    //13多文件,15相关内容,16相关栏目,17相关栏目(多选),18只读文本,19弹窗分页(单选),20弹窗分页(多选),21相关产品
    if(empty($value) && $value=="")
        return "";
    switch ($Mdata['fieldtype'])
    {
        case 4:
        case 5:
        case 6:
        case 7:
                $arr=getSettingData($Mdata['settings']);
                foreach ($arr as $k=>$vo)
                {
                    if($vo[1]==$value)
                    {
                        return $vo[0];
                    }
                }
                return "";
        case 9:
            return date('Y-m-d',strtotime($value));
        case 22:
            return date('Y-m-d H:i',strtotime($value));
        default:
            return $value;
    }

}

function getSettingData1($s){
    if(strstr($s,'∮')){
        $arr=explode('∮',$s);
        $arrTmp[0]=$arr[0];//1手动设置列表值,2从表中自动获取,3从数据字典中读取
        $arr1 = explode('☆',$arr[1]);
        $arrTmp[1]=$arr1[0];//数据 格式如：红色|1,白色｜2
        $arrTmp[2]=$arr1[1];//数据表名
        return $arrTmp;
    }
}

// 省市区的三级联动
function getChildData($c){
    if(strstr($c,'∮')){
        $arr=explode('∮',$c);//1有连级,2没有
        return $arr;
    }
}

function getSettingData($s)
{
    if(strstr($s,'∮')) {
        $arr = explode('∮', $s);

        $arrTmp[0] = $arr[0];//1手动设置列表值,2从表中自动获取,3从数据字典中读取
        $arr1 = explode('☆', $arr[1]);
        $arrTmp[1] = $arr1[0];//数据 格式如：红色|1,白色｜2
        $arrTmp[2] = $arr1[1];//数据表名
        $arrTmp[3] = $arr1[2];//条件表达式
        $arrTmp[4] = $arr1[3];//获取条数
        $arrTmp[5] = $arr1[4];//文本字段
        $arrTmp[6] = $arr1[5];//值字段

        $Data = array();
        // var_dump(htmlspecialchars_decode($arrTmp[3]));//手动
        if (intval($arrTmp[0]) == 1 && $arrTmp[0] != "") {
            $arr1 = explode(',', $arrTmp[1]);

            for ($i = 0; $i < count($arr1); $i++) {
                $Data[] = explode('|', $arr1[$i]);
            }

            for ($j = 0;$j < count($Data); $j++){
                if(strstr($Data[$j][0],'#')){

                    $arr2 = explode('#', $Data[$j][0]);
                    $Data[$j][0] = $arr2;
                }
            }

        } elseif (intval($arrTmp[0]) == 2) {
            //$ModelData =  M($arrTmp[2])->where($arrTmp[3])->setField($arrTmp[5],$arrTmp[6])->limit(intval($arrTmp[4])==0?20:intval($arrTmp[4]))->select();
            $ModelData = db($arrTmp[2])->where(htmlspecialchars_decode($arrTmp[3]))->limit(0, intval($arrTmp[4]) == 0 ? 50 : intval($arrTmp[4]))->cache(true,60)->select();

            foreach ($ModelData AS $key => $value) {
                $Data[] = array($value[strtolower($arrTmp[5])], $value[strtolower($arrTmp[6])]);
            }
        } elseif (intval($arrTmp[0]) == 1) {

        }
        elseif (intval($arrTmp[0] == 3)){
            $book_list = db('work_content')->where(['bookcode'=>$arr1[6]])->cache(true,60)-> select();
            foreach ($book_list as $key=>$value){
                $Data[] = array($value['name'],$value['code']);
            }
        }
        return $Data;
    }
}

//商品分类处理
function getTree($data, $pId){
    $tree = '';
    foreach($data as $k => $v)
    {
        if($v['parent_id'] == $pId)
        {        //父亲找到儿子
            $v['parent_id'] = getTree($data, $v['id']);
            $tree[] = $v;
            //unset($data[$k]);
        }
    }
    return $tree;
}

function getArrValue($arr,$LangIndex)
{
    if(empty($arr))
        return "";
    if(count($arr)>$LangIndex)
        return $arr[$LangIndex];
    else
        return $arr[count($arr)-1];
}


/**
*
*  商品缩略图 给于标签调用 拿出商品表的 original_img 原始图来裁切出来的
* @param type $goods_id  商品id
* @param type $width     生成缩略图的宽度
* @param type $height    生成缩略图的高度
*/
function goods_thum_images($goods_id,$width,$height){

    if(empty($goods_id)) return '';
    //判断缩略图是否存在
    $path = "public/upload/goods/thumb/$goods_id/";
    $goods_thumb_name ="goods_thumb_{$goods_id}_{$width}_{$height}";

    // 这个商品 已经生成过这个比例的图片就直接返回了
    if(file_exists($path.$goods_thumb_name.'.jpg'))  return '/'.$path.$goods_thumb_name.'.jpg';
    if(file_exists($path.$goods_thumb_name.'.jpeg')) return '/'.$path.$goods_thumb_name.'.jpeg';
    if(file_exists($path.$goods_thumb_name.'.gif'))  return '/'.$path.$goods_thumb_name.'.gif';
    if(file_exists($path.$goods_thumb_name.'.png'))  return '/'.$path.$goods_thumb_name.'.png';

    $original_img = db('goods')->where("goods_id = $goods_id")->field('original_img')->find();
    if(empty($original_img)) return '';

    $original_img = '.'.$original_img; // 相对路径
    if(!file_exists($original_img)) return '';

    try{
        $image = \think\Image::open($original_img);
        $goods_thumb_name = $goods_thumb_name. '.'.$image->type();
        // 生成缩略图
        if(!is_dir($path)) mkdir($path,0777,true);
        // 参考文章 http://www.mb5u.com/biancheng/php/php_84533.html  改动参考 http://www.thinkphp.cn/topic/13542.html
        $image->thumb($width, $height,2)->save($path.$goods_thumb_name,NULL,100); //按照原图的比例生成一个最大为$width*$height的缩略图并保存
        //图片水印处理
        $water = tpCache('water');
        if($water['is_mark']==1){
            $imgresource = './'.$path.$goods_thumb_name;
            if($width>$water['mark_width'] && $height>$water['mark_height']){
                if($water['mark_type'] == 'img'){
                    $image->open($imgresource)->water(".".$water['mark_img'],$water['sel'],$water['mark_degree'])->save($imgresource);
                }else{
                    //检查字体文件是否存在,注意是否有字体文件
                    if(file_exists('./zhjt.ttf')){
                        $image->open($imgresource)->text($water['mark_txt'],'./zhjt.ttf',20,'#000000',$water['sel'])->save($imgresource);
                    }
                }
            }
        }
        return '/'.$path.$goods_thumb_name;
    }catch (Think\Exception $e){
        return $original_img;
    }



}


function convert_arr_key($arr, $key_name)
{
    $arr2 = array();
    foreach($arr as $key => $val){
        $arr2[$val[$key_name]] = $val;
    }
    return $arr2;
}


function getMenu($pid=-1,$where="",$limit=100,$order=""){
    $arr=array();
    $wh='';
    $db_menu = db('node');
    if($pid>-1) {
        $wh.='parentid ='.$pid;
    }
    if($where!="") {
        // $wh.= " and model_name='".深圳市."'";
        $wh.=" and ".$where;
    }
    $tmpOrder="nodeid asc";
    if($order!="")
    {
        $tmpOrder=$order;
    }
    $menu=$db_menu->where($wh)->limit($limit)->order($tmpOrder)->select();
    if($menu)
    {
        foreach ($menu as $row)
        {
            if($pid>-1)
            {
                $row["nodelist"]=getMenu($row['nodeid'],$where,$limit,$order);
            }
            $arr[]=$row;
        }
    }
    return $arr;
}

//过滤字段
function field_filter($content){
    $siteid='';
    $where="isusing=1";
    if(empty(session('idsite')))
    {
        $where=$where." and idsite=0";
    }
    else
    {
        $siteid=session('idsite');
        $where=$where." and (idsite=0 or idsite=".session('idsite').")";
    }

    if(!empty(session('filter_'.$siteid))){
        $filter_field = session('filter_'.$siteid);
    }else{
        $filter_field = db('filter')->where($where)->order('idsite asc,idorder asc')->field('content,replace')->select();
        session('filter_'.$siteid,$filter_field);
    }
    if(is_string($content)){
        foreach ($filter_field as $key=>$value){
            if(strstr($content,$value['content'])){
                $content = str_replace($value['content'],$value['replace'],$content);
            }
        }
    }
    return $content;
}

//取字符串的长度，超过设为
function str_cut($str, $length = 0,$ext = "..."){

    if($length < 1){
        return $str;
    }

    //计算字符串长度
    $strlen = (strlen($str) + mb_strlen($str,"UTF-8")) / 2;
    //$strlen = mb_strlen($str,'gb2312');
    if($strlen < $length){
        return $str;
    }

    if(mb_check_encoding($str,"UTF-8")){
        $str = mb_strcut(mb_convert_encoding($str, "gb2312","UTF-8"), 0, $length, "gb2312");
        $str = mb_convert_encoding($str, "UTF-8", "gb2312");

    }else{

        return "不支持的文档编码";
    }

    //$str = rtrim($str);
    return $str.$ext;
}

function cut_str($sourcestr, $cutlength,$text) {
    $returnstr = '';
    $i = 0;
    $n = 0;
    $str_length = strlen ( $sourcestr ); //字符串的字节数
    while ( ($n < $cutlength) and ($i <= $str_length) ) {
        $temp_str = substr ( $sourcestr, $i, 1 );
        $ascnum = Ord ( $temp_str ); //得到字符串中第$i位字符的ascii码
        if ($ascnum >= 224) //如果ASCII位高与224，
        {
            $returnstr = $returnstr . substr ( $sourcestr, $i, 3 ); //根据UTF-8编码规范，将3个连续的字符计为单个字符
            $i = $i + 3; //实际Byte计为3
            $n ++; //字串长度计1
        } elseif ($ascnum >= 192) //如果ASCII位高与192，
        {
            $returnstr = $returnstr . substr ( $sourcestr, $i, 2 ); //根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i = $i + 2; //实际Byte计为2
            $n ++; //字串长度计1
        } elseif ($ascnum >= 65 && $ascnum <= 90) //如果是大写字母，
        {
            $returnstr = $returnstr . substr ( $sourcestr, $i, 1 );
            $i = $i + 1; //实际的Byte数仍计1个
            $n ++; //但考虑整体美观，大写字母计成一个高位字符
        } else //其他情况下，包括小写字母和半角标点符号，
        {
            $returnstr = $returnstr . substr ( $sourcestr, $i, 1 );
            $i = $i + 1; //实际的Byte数计1个
            $n = $n + 0.5; //小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($str_length > $cutlength) {
        $returnstr = $returnstr . $text; //超过长度时在尾处加上省略号
    }
    return $returnstr;
}


/**
 * @param $menucode
 * @param $fieldname
 * @param $idsite
 * @return string
 */
function GetConfigVal($menucode,$fieldname,$idsite){
    $arr = [];
    if(cache('config'.$idsite)){
        $arr = cache('config'.$idsite);
    }else{
        $menu_list = db('config_menu')->where(['idsite' => ['in', [0, $idsite]]])->select();
        $rule_list = db('config_rule')->where(['idsite' => $idsite])->select();
        foreach ($menu_list as $key=>$value){
            foreach ($rule_list as $ke=>$val){
                if($value['chrcode'] == $val['menucode']){
                    $arr[$value['chrcode']][$val['fieldname']] = $val['defaultval'];
                }
            }
        }
        //存入缓存 所有菜单和配置项的值
        cache('config'.$idsite,$arr);
    }

    $data = '';

    foreach ($arr as $key=>$value){
        if($key == $menucode){
            if(array_key_exists($fieldname,$value) ) {
                $data = $value[$fieldname];
            }
        }
    }
    return $data;
}

/**
 * 获取订单 order_sn
 */
function getOrderSn()
{
    // 保证不会有重复订单号存在
    while (true) {
        $ordersn = date('YmdHis') . rand(100000, 999999); // 订单编号
        $ordersn_count = db('order')->where("ordersn = '$ordersn'")->count();
        if ($ordersn_count == 0)
            break;
    }
    return $ordersn;
}

/**
 * 获取积分订单 order_sn
 */
function getIntegralOrderSn()
{
    // 保证不会有重复订单号存在
    while (true) {
        $order_no = date('YmdHis') . rand(100000, 999999); // 订单编号
        $ordersn_count = db('integral_mall_exchange_record')->where("order_no = '$order_no'")->count();
        if ($ordersn_count == 0)
            break;
    }
    return $order_no;
}

/**
 * 获取场景字符串
 */
function getSceneStr()
{
    // 保证不会有重复订单号存在
    while (true) {
        $scene_str = date('YmdHis') . rand(100000, 999999); // 订单编号
        $ordersn_count = db('qrcode_manage')->where("scene_str = '$scene_str'")->count();
        if ($ordersn_count == 0)
            break;
    }
    return $scene_str;
}

function getOrderCode($s=8)
{
    //$v='0123456789abcdefghijklmnopqrstuvwsyzABCDEFGHIJKLMNOPQRSTUVWSYZ';
    $v='0123456789abcdefghijklmnopqrstuvwsyz';
    //$v='0123456789';

    $strV='';


    while (true) {
        $strV='';
        for($i=0;$i<$s;$i++)
        {
            $index=rand(0,strlen($v)-1);
            $strV.=$v[$index];
        }
        $ordersn_count = db('order')->where("checkcode = '$strV'")->count();
        if ($ordersn_count == 0)
            break;
    }

    return $strV;

}

/**
 * 获取唯一编号
 */

function getNumber(){

    if(cache('my_static_i')){
        $my_static_i = cache('my_static_i');
        $my_static_i=$my_static_i+1;
        if ($my_static_i >= 1000){
            $my_static_i=1;
        }

    }else{
        $my_static_i=1;
    }
    cache('my_static_i',$my_static_i);

    $a = substr(date('YmdHis'), -12,12);

    $b = sprintf ("%04d", $my_static_i);
    //return $a .str_pad($b,4,'0',STR_PAD_LEFT );
    return $a .$b;
}
function getRegionIDs($province='',$city='',$area='')
{
    $arr=[];
    $arr[0]=0;
    $arr[1]=0;
    $arr[2]=0;
    $parent_id=0;
    if($province!='')
    {
        $row= db('region')->where(['level'=>1,'parent_id'=>0,'name'=>array('like', $province.'%')])->find();
        if($row)
        {
            $arr[0]=$row['id'];
            $parent_id=$row['id'];

        }
    }
    if($city!='')
    {
        if($parent_id>0)
            $row= db('region')->where(['level'=>2,'parent_id'=>$parent_id,'name'=>array('like',$city.'%')])->find();
        else
            $row= db('region')->where(['level'=>2,'name'=>array('like',$city.'%')])->find();

        if($row)
        {
            $arr[1]=$row['id'];
            $parent_id=$row['id'];

        }
    }
    if($area!='')
    {
        if($parent_id>0)
            $row= db('region')->where(['level'=>3,'parent_id'=>$parent_id,'name'=>array('like',$area.'%')])->find();
        else
            $row= db('region')->where(['level'=>3,'name'=>array('like',$area.'%')])->find();

        if($row)
        {
            $arr[2]=$row['id'];
        }
    }
    return $arr;
}

function getSiteCode($id)
{
    $data=db('site_manage')->field('site_code')->where(['id'=>$id])->find();
    if($data)
    {
        return $data['site_code'];
    }
    return "";
}
//读取配配信息
function getWeiXinConfig($code)
{
    $config=cache("WeiXinConfig");
    if(empty($config))
    {
        $config=[];
    }

    if(array_key_exists($code,$config)==false)
    {
        $data=db('site_manage')->field('id,site_name,appid,appsecret,token,encodingaeskey,mchid,paykey,cainfo,sslcertpath,sslkeypath')->where(['site_code'=>$code])->find();
        if($data)
        {
            $config[$code]=$data;
            cache("WeiXinConfig",$config);
        }
    }

    return $config[$code];
}

function getip() {
    //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    if('::1'==$ip)
    {
        $ip='127.0.0.1';
    }
    $res =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    return $res;
    //dump(phpinfo());//所有PHP配置信息
}

//信息发送
function send_msg($key,$sitecode)
{
    Log::debug('时间：' . date('Y-m-d H:i:s') . ' 微信消息发送：' . print_r(['key' => $key, 'sitecode' => $sitecode], true));
    $config=getWeiXinConfig(strtolower($sitecode));
    $idsite=$config['id'];

    $datainfo=db("sendmsg")->where(array("key"=>$key,"idsite"=>$idsite,"state"=>1))->find();
    if(empty($datainfo))
    {
        return array("state"=>0,"msg"=>"没有要发送的信息");
    }
    $template_id=$datainfo["Template_key"];
    $data= $datainfo["data"];
    $arr=explode(",",$datainfo["touser"]) ;
    // api模块 - 包含各种系统主动发起的功能
    $api = new \think\wx\Api(
        array(
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret'])
        )
    );
    foreach ($arr as $k=>$vo)
    {
        if(!empty($vo) && strlen($vo)>20 && strlen($vo)<33 )
        {
            $info=$api->template_send($vo,$template_id,$datainfo["url"],$data);
        }
    }
    Log::debug('时间：' . date('Y-m-d H:i:s') . ' 公众号消息发送结果：' . print_r($info, true));
    if(empty($info))
    {
        return array("state"=>0,"msg"=>"信息发送失败");
    }
    if($info->errcode!=0)
    {
        return array("state"=>0,"msg"=>$info->errmsg);
    }

    $arrData=[];
    $arrData['state']=2;
    db("sendmsg")->where(array("id"=>$datainfo['id']))->update($arrData);
    return array("state"=>1,"msg"=>"信息发送成功");
}




//退款模板
function template_tg($id)
{
    $datainfo= db('order')->where(array('id'=>$id))->find();
    $sitecode=getSiteCode($datainfo['idsite']);
    /*
    {曹先生}您好，您在{夏歌琴行}退款的课程详细信息：
    订单编号：2017122066665486
    退款商品：初级钢琴课程
    退款金额：6000.00
    退款时间：2017年12月20日 18:20
    已退款成功
    */

    Log::debug('退款微信消息，退款订单：' . print_r($datainfo, true));
    $template = 'OPENTM414474089';
    $msgToUserRemark = "可点击查看详情，如有疑问，请联系客服人员。";
    $msgToUserFirst = $msgToManagerFirst = "已退款成功";
    if(empty($datainfo['refundprice2']) || $datainfo['refundprice2']==0)
    {
        $price=$datainfo['refundprice'];
        $refundReason = $datainfo['refundremark'];
    }else
    {
        $refundReason = $datainfo['refundmsg2'];
        $price=$datainfo['refundprice2'];
    }


    if($datainfo['state']==10){
        $msgToManagerFirst = $msgToUserFirst="订单已取消，".$msgToUserFirst;
    }elseif($datainfo['state']==8)
    {
        //拒绝退款改用OPENTM412357964模板
        $template = 'OPENTM412357964';
        $msgToUserFirst="您申请的退款失败，如有异议请联系客服，谢谢！";
        $msgToManagerFirst =$datainfo["chrusername"]. "申请的退款不通过";
        // 本模板所用变量与本函数其他模板不一样，单独设置，后期优化
        // 申请原因
        $msgKeyword1 = $datainfo['refundmsg2'] ? : $datainfo['refundremark'];
        // 处理结果
        $msgKeyword2 = '拒绝退款';
        // 处理理由
        $msgKeyword3 = $datainfo['refundremark2'] ? : $datainfo['refundremark1'];


        $msgToUserRemark = "订单和退款详情可点击查看详情";
    }elseif($datainfo['state']==11)
    {
        $msgToUserFirst="您申请的退款已全额退款，款项已原路退回，请注意查收。";
        $msgToManagerFirst =$datainfo["chrusername"]. "申请的退款已全额退款，不再参加活动。";
    }elseif($datainfo['state']==7)
    {
        $msgToUserFirst="您申请的退款已完成退款，还请准时参加活动。";
        $msgToManagerFirst =$datainfo["chrusername"]. "申请的退款已完成退款，可参加活动。";
    }elseif($datainfo['state']==6)
    {
        $msgToUserFirst="您申请的退款已完成部分退款，请准时参加活动";
        $msgToManagerFirst =$datainfo["chrusername"]. "申请的退款已完成部分退款，需继续服务。";
    }elseif($datainfo['state']==13)
    {
        $msgToUserFirst="您申请的退款已完成部分退款，款项已原路退回";
        $msgToManagerFirst =$datainfo["chrusername"]. "申请的退款已完成部分退款，终止服务。";
    }elseif($datainfo['state']==5)
    {
        $template = 'OPENTM411136263';
        $msgToUserFirst="您已申请退款，我们将在1-3个工作日处理，请耐心等待，谢谢！";
        $msgToManagerFirst =$datainfo["chrusername"]. "已申请退款，请跟进处理退款。";
        //申请人
        $obj = new Common();
        $userData = $obj->getMemberInfo($datainfo['txtfield'], $datainfo['txtdata']);
        $msgKeyword1 = $userData['username'];
        //手机号
        $msgKeyword2 = $userData['mobile'];
        //申请日期
        $msgKeyword3 = date('Y年m月d日');

    }else
    {
        return false;
    }

    


    $arr1=[];
    $template_key = getWxTemplateId($template, $datainfo['idsite']);
    $arr1['Template_key']= $template_key;//"o-2ZSrWdeManCOWHb7UNUQmcVfu0OtQIaGUVGz0fKfQ";//"bTjllUlEt9-H8771xJfqR2HnSqC7D-5_FVqaCgITGSM";
    $arr1['dataid']=$datainfo['id'];
    $arr1['url']=ROOTURL."/".$sitecode."/orderdetail/".$datainfo['id'];
//    $arr1['touser']=$datainfo['wechatid'];
    $arr1['inttype']=3;
    $arr1['inttype1']=2;
    $arr1['username']="系统";
    $arr1['userid']=0;
    $arr1['state']=1;
    $arr1['createtime']=time();
//    $arr1['key']=getNumber();
    $arr1['idsite']=$datainfo['idsite'];
    $arr1['ip']=getip();

    $idaccount_array = $openIdArray = array();

//    $openIdArray[] = $datainfo['wechatid'];

    //根据订单活动id获取活动所属商务信息
    $activity = db('activity')->where(array("idactivity"=>$datainfo["dataid"]))->field("intselmarket")->find();
    //根据openid获取跟进用户的商务的信息
    $member = db('member')->where(array("openid"=>$datainfo['wechatid']))->field("iduser")->find();

    if($activity["intselmarket"]) {
        $idaccount_array[] = $activity["intselmarket"];
    }

    if($member["iduser"]){

        $idaccount_array[] = $member["iduser"];
    }

    //获取活动商务和跟进用户的商务对应的openid
    if(!empty($idaccount_array) && is_array($idaccount_array)) {
        $account_array = db('account')->where("idaccount","in",$idaccount_array)->field("openid")->select();
        foreach ($account_array as $account){
            if(!empty( $account["openid"])) {
                $openIdArray[] = $account["openid"];
            }
        }
    }

    //管理员列表
    $adminList = db("member")->where(array("idsite"=>$datainfo['idsite'],"ismanage"=>1))->field("openid")->select();

    foreach ($adminList as $admin){
        if(!empty( $admin["openid"])) {
            $openIdArray[] = $admin["openid"];
        }
    }

    $openIdArray = array_unique($openIdArray);
    //保证用户的openid位于$openIdArray的第0位
    array_unshift($openIdArray,$datainfo['wechatid']);

    //循环发送微信消息给指定微信用户
    $result=[];
    foreach ($openIdArray as $key=> $openId){

        $arrData=[];
        
        if($key == 0){      //发给用户的信息
            $arrData['first']=array("value"=>$msgToUserFirst,"color"=>"#ff7902");
//            $arrData['keyword1']=array("value"=>$datainfo['ordersn'],"color"=>"#8d8d8d");
            $arrData['keyword1']=array("value"=> $refundReason ,"color"=>"#8d8d8d");
            $arrData['keyword2']=array("value"=>$price,"color"=>"#8d8d8d");
//            $arrData['keyword4']=array("value"=>$datainfo['dtrefundtime'],"color"=>"#8d8d8d");
            $arrData['remark']=array("value"=>$msgToUserRemark,"color"=>"#4a93e4");
        }else{              //发给商务及管理员的信息
            $arrData['first']=array("value"=>$msgToManagerFirst,"color"=>"#ff7902");
//            $arrData['keyword1']=array("value"=>$datainfo['ordersn'],"color"=>"#8d8d8d");
            $arrData['keyword1']=array("value"=> $refundReason ,"color"=>"#8d8d8d");
            $arrData['keyword2']=array("value"=>$price,"color"=>"#8d8d8d");
//            $arrData['keyword4']=array("value"=>$datainfo['dtrefundtime'],"color"=>"#8d8d8d");
            $arrData['remark']=array("value"=>"可点击查看详情。","color"=>"#4a93e4");
        }

        //替换 退款不通过|申请退款 时的模板变量
        if(in_array($datainfo['state'], [8, 5]))
        {
            $arrData['keyword1'] = ['value' => $msgKeyword1, "color"=>"#8d8d8d"];
            $arrData['keyword2'] = ['value' => $msgKeyword2, "color"=>"#8d8d8d"];
            $arrData['keyword3'] = ['value' => $msgKeyword3, "color"=>"#8d8d8d"];
        }

        Log::debug('微信消息数据：' . print_r(['openid' => $openId, 'msg' => $arrData], true));
        $arr1['data']=json_encode($arrData, JSON_UNESCAPED_UNICODE);

        if(empty($openId)){
            continue;
        }
        $arr1['touser'] = $openId;
        $arr1['key']=getNumber();
        $bl=db("sendmsg")->insert($arr1);
        Log::debug('sendmsg表插入结果：' . print_r($bl, true));
        if($bl) {
            $result = send_msg($arr1['key'], $sitecode);
            Log::debug('微信消息发送结果：' . print_r($result, true));
        }
    }

    return $result;
}

//报名模板
function template_bm($id,$ordersn="")
{
    if($ordersn!="")
        $info=db('order')->where(array('ordersn'=>$ordersn))->find();
    else
        $info=db('order')->where(array('id'=>$id))->find();

    $str1 = "";

    $mark = "可点击查看详情，如有疑问，请联系客服人员。";
    $mark1 = "可点击查看详情";

    $order_state = config('order_state');
    $str2 = $order_state[$info["state"]];


    $first_str1 = $info["chrusername"].$str2."，请及时处理。";


    if($info['state']==4)
    {
        $str1="您的报名已完成支付，请您关注订单";
    }elseif($info['state']==3)
    {
        $str1="您的报名符合要求，审核已通过";
    }elseif($info['state']==2)
    {
        $str1="您的报名由于信息不符合要求，审核不通过";
    }elseif($info['state']==12)
    {
        $str1 = "您的报名还未支付，请尽早完成支付，超过30分钟再支付有可能因为名额已满无法正常购买，请您谅解。";
    }elseif($info['state']==1)
    {
        if(db("activity")->where(array("idactivity"=>$info["dataid"],"ischarge"=>1))->count()>0)
        {
            $str1="您的报名已提交审核，审核结果消息将通过本公众号下发，请关注消息。";
        }
    }else
    {
        return [];
    }
    $sitecode=getSiteCode($info['idsite']);


    $openIdArray = array();           //要发送的微信用户openid列表

    $arr=[];
    $template_key = getWxTemplateId("OPENTM411026400",$info['idsite']);
    $arr['Template_key']= $template_key;//"9HbG5-gamO983mRtXX-5kxZUr4kYUDw2xjl2zk0a4W0";
    $arr['dataid']=$info['id'];
    $arr['url']=ROOTURL."/".$sitecode."/orderdetail/".$info['id'];
    $arr['inttype']=3;
    $arr['inttype1']=2;
    $arr['username']="系统";
    $arr['userid']=0;
    $arr['state']=1;
    $arr['createtime']=time();
    $arr['idsite']=$info['idsite'];
    $arr['ip']=getip();

//    $openIdArray[] = $info['wechatid'];


    $idaccount_array = array();
    //获取活动所属商务的管理员信息
    $activity = db('activity')->where(array("idactivity"=>$info["dataid"]))->field("intselmarket")->find();
    //获取用户所属商户的管理员信息
    $member = db('member')->where(array("openid"=>$info['wechatid']))->field("iduser")->find();

    if($activity["intselmarket"]) {
        $idaccount_array[] = $activity["intselmarket"];
    }

    if($member["iduser"]){

        $idaccount_array[] = $member["iduser"];
    }

    //获取管理员对应的openid
    if(!empty($idaccount_array) && is_array($idaccount_array)) {
        $account_array = db('account')->where("idaccount","in",$idaccount_array)->field("openid")->select();
        foreach ($account_array as $account){
            if(!empty( $account["openid"])) {
                $openIdArray[] = $account["openid"];
            }
        }
    }

    //管理员列表
    $adminList = db("member")->where(array("idsite"=>$info['idsite'],"ismanage"=>1))->field("openid")->select();

    foreach ($adminList as $admin){
        if(!empty( $admin["openid"])) {
            $openIdArray[] = $admin["openid"];
        }
    }

    $openIdArray = array_unique($openIdArray);
    array_unshift($openIdArray,$info['wechatid']);

    //循环发送微信消息给指定微信用户
    Log::debug('时间：' . date('Y-m-d H:i:s') . ' 发送微信消息，$openid = ' . print_r($openIdArray, true));
    $result=[];
    foreach ($openIdArray as $key => $openId){
        if(empty($openId)) {
            continue;
        }

        $arrData = [];
        if($key==0) {
            $arrData['first'] = array("value" => $str1, "color" => "#ff7902");
            $arrData['keyword1'] = array("value" => "【" . $info['chrtitle'] . "】 " . $info['payname'] . " X " . $info['paynum'], "color" => "#8d8d8d");
            $arrData['keyword2'] = array("value" => $str2, "color" => "#8d8d8d");
            $arrData['remark'] = array("value" => $mark, "color" => "#4a93e4");
        }else{
            $arrData['first'] = array("value" => $first_str1, "color" => "#ff7902");
            $arrData['keyword1'] = array("value" => "【" . $info['chrtitle'] . "】 " . $info['payname'] . " X " . $info['paynum'], "color" => "#8d8d8d");
            $arrData['keyword2'] = array("value" => $str2, "color" => "#8d8d8d");
            $arrData['remark'] = array("value" => $mark1, "color" => "#4a93e4");
        }


        $arr['data']=json_encode($arrData);
        $arr['touser'] = $openId;
        $arr['key']=getNumber();

        $bl=db("sendmsg")->insert($arr);
        Log::debug('时间：' . date('Y-m-d H:i:s') . ' 插入sendmsg的记录 ' . print_r($arr, true) . '\\r\\n 插入结果：' . print_r($bl, true));
        if($bl) {
            $result = send_msg($arr['key'], $sitecode);
        }
    }
    return $result;
}

/**
 * 积分兑换模板
 *
 * @param int $goods_id 商品ID
 * @param int $order_id 订单ID
 * @param integer $status 兑换状态 1:兑换订单 2:取消订单
 * @return void
 * @author Chenjie
 * @Date 2019-06-25 16:08:37
 */
function template_integral_exchange($goods_id,$order_id,$status = 1){
    // 积分商城商品
    $goods = db('integral_mall_goods')->where('id',$goods_id)->find();
    $siteid = $goods['siteid'];
    $sitecode = getSiteCode($siteid);

    // 兑换订单信息
    $exchange_record = db('integral_mall_exchange_record')->where('id',$order_id)->find();
    $template_key = getWxTemplateId("OPENTM401078533",$siteid);

    // 模板消息信息
    if($status == 1){
        $first = '您好，您已成功兑换礼品!';
        $keyword1 = $exchange_record['order_no'];
        $keyword2 = $goods['goods_name'];
        $keyword3 = '兑换成功';
        $keyword4 = date("Y年m月d日");
        $remark = '感谢您的使用,'.$exchange_record['integral'].'积分已扣减!';
    }else{
        $first = '你好，你积分兑换的礼品已成功取消。';
        $keyword1 = $exchange_record['order_no'];
        $keyword2 = $goods['goods_name'];
        $keyword3 = '成功取消兑换';
        $keyword4 = date("Y年m月d日");
        $remark = '感谢您的使用,'.$exchange_record['integral'].'积分已成功退回，兑换详情请单击查询。';
    }

    $template_data = json_encode([
        'first' => ["value" => $first, "color" => "#ff7902"],
        'keyword1' => ["value" => $keyword1, "color" => "#8d8d8d"],
        'keyword2' => ["value" => $keyword2, "color" => "#8d8d8d"],
        'keyword3' => ["value" => $keyword3, "color" => "#8d8d8d"],
        'keyword4' => ["value" => $keyword4, "color" => "#8d8d8d"],
        'remark' => ["value" => $remark, "color" => "#4a93e4"],
    ]);

    // 待发送信息
    $data = [
        'dataid' => $goods['id'],
        'Template_key' => $template_key,
        'data' => $template_data,
        'url' => ROOTURL."/".$sitecode."/integralmall?tabType=2",
        'inttype' => 3,
        'inttype1' => 4,
        'state' => 1,
        'userid' => 0,
        'username' => '系统',
        'ip' => getip(),
        'createtime' => time(),
        'idsite' => $siteid
    ];
    

    // 取得商品管理员openid
    $account_openid = db('account')->where('idaccount',$goods['account_id'])->column("openid");

    // 取得机构管理openid
    $site_openids = db('member')->where(['idsite'=>$siteid, 'ismanage'=>1])->column("openid");

    // 取得当前用户openid
    $member_openid = db('member')->where('idmember',$exchange_record['member_id'])->column("openid");

    // openid合并
    $openids = array_merge($account_openid, $site_openids, $member_openid);

    foreach($openids as $openid){
        $data['touser'] = $openid;
        $data['key'] = getNumber();

        $send_result = [];
        $result = db('sendmsg')->insert($data);
        if($result){
            $send_result[] = send_msg($data['key'], $sitecode);
        }
    }

    return $send_result;
}

// 发送客服消息
function send_ordinary_msg($sitecode, $openid, $msg){
    $config = getWeiXinConfig(strtolower($sitecode));
    // api模块 - 包含各种系统主动发起的功能
    $api = new \think\wx\Api(
        array(
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret'])
        )
    );
    $result = $api->send($openid,$msg);
    return $result[0] ? true : false;
}

function replearurl($str)
{
    $str = html_entity_decode($str);
    $str=str_ireplace("https://mmbiz.qpic.cn","http://img01.store.sogou.com/net/a/04/link?appid=100520029&url=https://mmbiz.qpic.cn",$str);
    $str=str_ireplace("https://img.xiumi.us","http://img01.store.sogou.com/net/a/04/link?appid=100520029&url=https://img.xiumi.us",$str);
    return $str;
}


//套餐库存还剩多少
function getGoodsCount1($dataid,$GoodsName)
{
    $datainfo=db("activity")->where(array("idactivity"=>$dataid))->find();
    if(empty($datainfo))
        return 0;

    $intsignnum=$datainfo["intsignnum"];
    if(empty($intsignnum))
        $intsignnum=0;

    if(!empty($datainfo['selcontent']))
    {
        $selcontent=[];
        $arr = explode("☆", $datainfo['selcontent']);
        $index=0;
        foreach ($arr as $k=>$vo)
        {
            $index++;
            $selcontent[]=explode("∮", $vo);
        }
        for($i = $index;$i <=10;$i++)
        {
            if($i==10)
                $selcontent[]=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
            else
                $selcontent[]=array("","","","","","","","","","","","","","","","","","","","");
        }

        $selcontent_index=count($selcontent[0]);
        for($i=0;$i<$selcontent_index;$i++) {
            $tmp=$selcontent[0][$i]." ".$selcontent[1][$i]."(".$selcontent[3][$i].")";
            if($tmp==$GoodsName)
            {
                if(!empty($selcontent[9][$i]) && $selcontent[9][$i]!=0)
                {
                    $intsignnum=$selcontent[9][$i];
                }
                break;
            }
        }
    }

    if($intsignnum==0)
        return 999999;

    $iCount=getGoodsCount($dataid,$GoodsName);
    return $intsignnum-$iCount;
}
//已买数量
function getGoodsCount($dataid,$GoodsName)
{
    $where = "dataid=".$dataid;
    if(empty($GoodsName)==false)
        $where .= " and payname='".$GoodsName."'";

    $where .= " and (state in (3,4,5,6,7,8,10,11,13) or (dtcreatetime>'".date("Y-m-d H:i:s",time()-1800)."' and state in (1,12)) or (checktime>'".date("Y-m-d H:i:s",time()-1800)."' and state in (2)))";
    $iCount= db("order")->where($where)->sum("paynum");

    if(empty($iCount))
        $iCount=0;

    return $iCount;
}

function SetUserPayCount($userid)
{
    if(empty($userid) || $userid<1)
        return;
    // // '订单状态:1.起草,待审核 2.已报名,审核不通过 3.已报名,已审批，4.已报名,已付款，5.已报名,退款中，6已报名，部分退款，7已报名，已退款，8.已报名,退款不通过，9.删除 10.已取消',
    $PayCount = db('order')->where(array('fiduser'=>$userid,'state'=>array('in','3,4')))->count();
    db("member")->where(array('idmember'=>$userid))->update(array('paynum'=>$PayCount));

}

function fromatetime($i)
{
    if(empty($i))
        return "";
    if($i<60)
        return round($i,2)."秒";

    $i=$i/60;
    if($i<60)
        return  round($i,2)."分钟";

    $i=$i/60;
    return round(round($i,2),2)."小时";

}


/**
 * 会员日志
 * @param $data
 * @param int $type
 * @param string $remark
 * @return bool
 */
function member_log($data,$type=1,$remark="")
{
    if(!isset($data["openid"]) || empty($data["openid"])){
        return false;
    }

    if(!isset($data["idmember"]) || empty($data["idmember"])){
        $member = db("member")->where(array("openid"=>$data["openid"]))->field("idmember,idsite")->find();
        if(empty($member)){
            return false;
        }
        $data["idmember"] = $member["idmember"];
        if(!isset($data["idsite"]) && isset($member["idsite"])){
            $data["idsite"]  = $member["idsite"];
        }
    }

    $log = array();
    $field_list = array("idmember", "openid", "userimg", "intstate","old_intstate", "dtsubscribetime", "dtunsubscribetime", "idsite");
    foreach ($data as $key => $value) {
        if (!in_array($key, $field_list)) {
            continue;
        }
        $log[$key] = $value;
    }
    $log["type"] = $type;
    $log["remark"] = $remark;
    if(!isset($data["dtcreatetime"])) {
        $log["dtcreatetime"] = time();
    }else{
        $log["dtcreatetime"]  = $data["dtcreatetime"];
    }
    $rs = db("member_log")->insert($log);
    return $rs;
}


/**
 * 发送短信
 * @param $siteid      用户站点id，必填
 * @param $mobile      手机号码 传单个以字符串传递，传多个以数组传递
 * @param $content     短信内容，不能包含敏感词
 * @return array
 */
function sendMsg($siteid,$mobile,$content,$ssid=0)
{
    try {

        //判断站点是否为空
        if (empty($siteid) || (int)$siteid <= 0 || (int)$siteid != $siteid) {
            return array("status" => "fail", "msg" => "站点id不为空");
        }

        //判断手机号码是否为空
        if (empty($mobile)) {
            return array("status" => "fail", "msg" => "用户手机号码不为空");
        }

        //判断用户内容是否为空
        if (empty($content)) {
            return array("status" => "fail", "msg" => "用户内容不为空");
        }

        $sendNum = 1;

        if (is_array($mobile)) {
            $sendNum = count($mobile);
            $mobile = implode(",", $mobile);
        }

        if ($sendNum > 200) {
            return array("status" => "fail", "msg" => "短信每次最多发送200条");
        }

        $flag = isBadWord($content);
        if ($flag) {
            return array("status" => "fail", "msg" => "发送的内容包含敏感词");
        }

        $c_data = config("msg_config");
        $data = $c_data["data"];
        $data['mobile'] = base64_encode(urlencode($mobile));//$mobile;
        $data['content'] = base64_encode(urlencode($content));//$content;
        $data['ssid'] = $ssid;

        //发送短信
        $url = $c_data["send_url"] . "?" . http_build_query($data);
        Log::debug('时间：' . date('Y-m-d H:i:s') . ' 本次短信发送接口地址为 $url = ' . $url);
        $rs = HttpCurl::get($url, "json");

        if ($rs == false) {
            return array("status" => "fail", "msg" => "短信请求接口返回错误。");
        }
        Log::debug('时间：' . date('Y-m-d H:i:s') . ' 短信发送接口返回数据：$rs = ' . print_r($rs, true));
        if ($rs->status == 0) {
            //这里处理短信发送成功后的代码逻辑
            return array("status" => "success", "msg" => "短信发送成功。");
        } else {
            return array("status" => "fail", "msg" => "短信发送错误，错误码:" . $rs["status"]);
        }
    } catch (Exception $ex) {
        return array("status" => "fail", "msg" => "短信发送失败000" . print_r($ex, true));
    }
}


/**
 * 短信发送任务
 * @param int $siteid      用户站点id，必填
 * @param array $mobileArray      手机号码 传单个以字符串传递，传多个以数组传递
 * @param $content     短信内容，不能包含敏感词
 * @param  int $idaccount   操作人id
 * @param int $type         操作类型  0-系统消息，1-个人消息
 * @param string $exec_time         发送时间  当前时间戳-默认
 * @return array
 */
function smsSendSchedule($siteid,$mobileArray,$content,$idaccount, $username,$type=0,$send_time="")
{
    $msgConfig = config('msg_config');
    if(empty($content) || mb_strlen($content) > $msgConfig['max_text_len'])
    {
        return ['status' => 'fail', 'msg' => '短信内容不能为空，且最长' . $msgConfig['max_text_len'] . '个字'];
    }

    if (empty($send_time)) {
        $send_time = date("Y-m-d H:i:s");
    }

    //作为保障，防止只传入idaccount且idaccount!=0而不传入username
    if($idaccount != 0)
    {
        $username = session('Username');
    }

    //判断站点是否为空
    if (empty($siteid) || (int)$siteid <= 0 || (int)$siteid != $siteid) {
        return array("status" => "fail", "msg" => "站点id不为空");
    }

    //判断手机号码是否为空
    if (empty($mobileArray)) {
        return array("status" => "fail", "msg" => "用户手机号码不为空");
    }

    //判断用户内容是否为空
    if (empty($content)) {
        return array("status" => "fail", "msg" => "用户内容不为空");
    }

    //将号码转换数组
    if (!is_array($mobileArray)) {
        $mobileArray = array($mobileArray);
    }

    //验证手机号码
    foreach ($mobileArray as $mobile) {
        if (!checkMobile($mobile)) {
            return array("status" => "fail", "msg" => $mobile . "不是有效的手机号码");
        }
    }
    $siteManage = db('site_manage')->field('sms_sign')->where(['id' => $siteid])->find();
    $content = '【' . $siteManage['sms_sign'] . '】' . $content;
    $sendNum = ceil(mb_strlen($content) / $msgConfig['text_len']) * count($mobileArray);

    $create_time = date('Y-m-d H:i:s');
    $ip = getip();
    $data = [];
    foreach ($mobileArray as $mobile) {
        $data[] = [
            "idsite" => $siteid,
            "mobile" => $mobile,
            "content" => $content,
            "idaccount" => $idaccount,
            'username' => $username,
            "type" => $type,
            "create_time" => $create_time,
            "send_time" => $send_time,
            "ip" => $ip,
            "ssid" => create_ssid(),
        ];
    }

    return smsSchedule($siteid, $sendNum, $data);

    //     $smsNum = getSiteMsgCount($siteid);

    //     if ($sendNum > $smsNum) {
    //         return array("status" => "fail", "msg" => "短信可发送数量不足");
    //     }

    //     Db::startTrans();
    //     //先扣除短信数量操作，防止并发导致超额发送短信。
    //     $sql = "update cms_site_manage set sms_num=" . (int)($smsNum - $sendNum) . " where id=" . $siteid . " and (sms_num-" . $sendNum . ") >= 0";
    //     $rs = Db::execute($sql);
    //     if (!$rs) {
    //         Db::rollback();
    //         return array("status" => "fail", "msg" => "扣除短信可发送数量失败");
    //     }

    //     $ssid = create_ssid();
    //     $smsScheduleArr = array();
    //     $create_time = time();
    //     $ip = getip();
    //     foreach ($mobileArray as $mobile) {
    //         $smsScheduleArr[] = [
    //             "idsite" => $siteid,
    //             "mobile" => $mobile,
    //             "content" => $content,
    //             "idaccount" => $idaccount,
    //             'username' => $username,
    //             "type" => $type,
    //             "create_time" => $create_time,
    //             "exec_time" => $exec_time,
    //             "ip" => $ip,
    //             "ssid" => $ssid,
    //         ];
    //     }
    //     $rs = db("sms_send_schedule")->insertAll($smsScheduleArr);
    //     if(!$rs){
    //         Db::rollback();
    //         return array("status" => "fail", "msg" => "生成短信计划失败。", "num" => $sendNum);
    //     }
    //     Db::commit();
    //     return array("status" => "success", "msg" => "生成短信计划成功。", "num" => $sendNum);
    // } catch (Exception $ex) {
    //     return array("status" => "fail", "msg" => "短信发送失败000" . print_r($ex, true));
    // }
}


//发送报告
function fetchReports(){
    $c_data = config("msg_config");
    $data = $c_data["report_data"];
    $url = $c_data["getreport_url"] . "?" . http_build_query($data);
//      echo $url;exit;
    $rs = HttpCurl::get($url, "json");
    if ($rs == false) {
        return array("status" => "fail", "msg" => "短信请求接口返回错误。");
    }
    if ($rs->status == 0) {
        return array("status" => "success", "data" => $rs->data);
    }else{
        return array("status" => "fail", "msg" => "获取发送报告失败");
    }
}



//取得企业可发短信数量
function getSiteMsgCount($siteid)
{
    $smsNum = db("site_manage")->where(array('id' => $siteid, 'sms_enable' => 1))->value("sms_num");
    return $smsNum?:0;
}



/**
 * 是否是敏感词
 * @param string $str
 * @return bool
 */
function isBadWord($str = '')
{
    $flag_arr = array('？', '！', '￥', '（', '）', '：', '‘', '’', '“', '”', '《', '》', '，', '…', '。', '、', 'nbsp', '】', '【', '～');
    $content_filter = preg_replace('/\s/', '', preg_replace("/[[:punct:]]/", '', strip_tags(html_entity_decode(str_replace($flag_arr, '', $str), ENT_QUOTES, 'UTF-8'))));

    $rs = false;
    $badWord = cache("bad_word");
    if (empty($badWord)) {

        $badWord = getBadWord();
        $rs = cache("bad_word", $badWord);
        if (!$rs) {
            return true;
        }
    }

    if (!$badWord) {
        $rs = true;
    }
    foreach ($badWord as $word) {
        if (substr_count($content_filter, $word)) {
            $rs = true;
            break;
        }
    }

    return $rs;
}

/**
 * 获取敏感词
 * @return array|bool
 */
function getBadWord()
{
    $filePath = __ROOT__ . '/public/dat/bad_word.dat';
    if (!is_file($filePath)) {
        return false;
    }
    $handle = @fopen($filePath, "r");
    $file_read = fread($handle, filesize($filePath));
    fclose($handle);
    $badword = explode(',', $file_read);
    return $badword;
}

/**
 * 获取微信模板id
 * @param $template_id_short
 * @param $idsite
 * @return mixed|string
 */
function getWxTemplateId($template_id_short,$idsite)
{
    try {
        $template_id_list = cache("wx_template_id");
        if (empty($template_id_list)) {
            $wx_template_list = db("wx_template")->where("idsite=" . $idsite)->select();
            $template_id_list = array();
            if (!empty($wx_template_list)) {
                foreach ($wx_template_list as $wx_template) {
                    $template_id_list[$wx_template["template_id_short"]] = $wx_template["template_id"];
                }
                cache("wx_template_id", $template_id_list);
            }
        }

        return isset($template_id_list[$template_id_short]) ? $template_id_list[$template_id_short] : "";
    } catch (Exception $ex) {
        return "";
    }
}


function create_ssid()
{
    // mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
    return substr(microtime(true) * 10000, 1)  . str_pad(mt_rand(0, 99999), 5, 0, STR_PAD_LEFT);
    $str = date('YmdHis') . substr(microtime(true) * 10000, 10) . str_pad(mt_rand(0, 999999), 6, STR_PAD_LEFT);
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = "";
    chr(45);
    $uuid = substr($charid, 0, 8) . $hyphen
        . substr($charid, 8, 4) . $hyphen
        . substr($charid, 12, 4) . $hyphen
        . substr($charid, 16, 4) . $hyphen
        . substr($charid, 20, 12);

    return $uuid;
}

//验证手机号码格式的有效性
function checkMobile($mobile)
{
    if (preg_match("/^1[34578]\d{9}$/", $mobile)) {
        return true;
    } else {
        return false;
    }
}


/**
 * 系统按照模板自动发送短信
 * @author Hlt
 * @DateTime 2019-04-16T16:20:31+0800
 * @param    int                      $siteId      站点id
 * @param    int                      $type        信息类型
 * @param    array                    $order       涉及的订单信息
 * @param    array                    $replace     自定义的变量替换信息
 * @param    int                      $marketId    商务id
 * @return   bool|array                            发送成功与否/错误信息
 */
function sysSendMsg($siteId, $type, $order, $replace = [], $userMobile = '', $marketMobile = '', $managerMobile = '')
{
    //判断站点是否为空
    Log::debug('时间：' . date('Y-m-d H:i:s') . ' 调用成功，参数为:' . print_r(func_get_args(), true));
    if (empty($siteId) || (int)$siteId <= 0 || (int)$siteId != $siteId) {
        return array("status" => "fail", "msg" => "站点id不为空");
    }

    // 查询开关
    $config = allow_sms_send($siteId, $type);
    Log::debug('时间：' . date('Y-m-d H:i:s') . ' 短信开关设置为:' . print_r($config, true));
    if(!$config)
    {
        return false;
    }
    //获取用户报名时填写的信息
    if(empty($order))
    {
        //公共变量替换
        $commonReplace = [
            '{time}' => date('Y-m-d H:i:s'),
        ];
    }else
    {
        $obj = new Common();
        $userData = $obj->getMemberInfo($order['txtfield'], $order['txtdata']);
        $userMobile = $userMobile ? : $userData['mobile'];
        if(!$marketMobile)
        {
            $market = db('account')->field(['mobile'])->where(['idaccount' => $order['marketid']])->find();
            $marketMobile = $market['mobile'];
        }
        // 查询活动
        $activity = db('activity')->where(['idactivity' => $order['dataid']])->find();

        //公共变量替换
        $commonReplace = [
            '{title}' => $activity['short_title'],
            '{pay_num}' => $order['paynum'],
            '{order_price}' => $order['price'],
            '{refund_price}' => $order['refundprice2'] > 0 ? $order['refundprice2'] : $order['refundprice'],
            '{time}' => date('Y-m-d H:i:s'),
            '{name}' => $userData['username'],
        ];
    }

    $replace = array_merge($commonReplace, $replace);
    Log::debug('短信替换变量：' . print_r($replace, true));

    //初始化发送短信个数
    $sendNum = 0;
    $ip = getip();
    $data = [];
    $time = date('Y-m-d H:i:s');
    $mode = [
        "idsite" => $siteId,
        "idaccount" => 0,
        'username' => '系统消息',
        "type" => $type,
        "create_time" => $time,
        "send_time" => $time,
        "ip" => $ip,
    ];

    //获取短信签名
    $siteManage = db('site_manage')->where(['id' => $siteId])->find();

    $msgConfig = config('msg_config');
    Log::debug('时间：' . date('Y-m-d H:i:s') . ' 站点配置为:' . print_r($msgConfig, true));

    //如果用户开关打开
    if($config[0])
    {
        if(checkMobile($userMobile))
        {
            //查询相应的用户短信模板
            $template = db('sms_system_template')->where(['inttype' => $type . '_' . '0'])->find();
            $personalText = '【' . $siteManage['sms_sign'] . '】' . str_replace(array_keys($replace), array_values($replace), $template['content']);
            Log::debug('时间：' . date('Y-m-d H:i:s') . ' 用户短信内容：' . $personalText);
            if(mb_strlen($personalText) > $msgConfig['max_text_len'])
                return false;

            //计算发送短信总个数
            $sendNum += ceil(mb_strlen($personalText) / $msgConfig['text_len']);
            //短信信息
            $tmp = $mode;
            $tmp['mobile'] = $userMobile;
            $tmp['content'] = $personalText;
            $tmp['ssid'] = create_ssid();
            $data[] = $tmp;
        }
    }

    //如果商务开关打开
    if($config[1] && checkMobile($marketMobile))
    {
        //查询相应的商务短信模板
        $template = db('sms_system_template')->where(['inttype' => $type . '_' . '1'])->find();
        $personalText = '【' . $siteManage['sms_sign'] . '】' . str_replace(array_keys($replace), array_values($replace), $template['content']);
        Log::debug('时间：' . date('Y-m-d H:i:s') . ' 商务短信内容：' . $personalText);

        if(mb_strlen($personalText) > $msgConfig['max_text_len'])
            return false;

        //计算发送短信总个数
        $sendNum += ceil(mb_strlen($personalText) / $msgConfig['text_len']);
        //短信信息
        $tmp = $mode;
        $tmp['mobile'] = $marketMobile;
        $tmp['content'] = $personalText;
        $tmp['ssid'] = create_ssid();
        $data[] = $tmp;
    }
    // var_dump($data);die;
    Log::debug('时间：' . date('Y-m-d H:i:s') . ' 本次短信内容为$data = ' . print_r($data, true));
    
    if(!$data)
    {
        return '发送内容为空';
    }
    return smsSchedule($siteId, $sendNum, $data);
}


/**
 * 修改用户可发送短信数并将短信发送列入计划
 * @author Hlt
 * @DateTime 2019-04-15T17:46:10+0800
 * @param    int                   $siteId    站点id
 * @param    int                   $sendNum   发送短信总数
 * @param    array                 $data      发送短信数据
 * @return   array                            ['status' => '执行状态', 'msg' => '正确/错误信息']
 */
function smsSchedule($siteId, $sendNum, $data)
{
    try{
        Db::startTrans();
        $smsNum = getSiteMsgCount($siteId);
        if ($sendNum > $smsNum) {
            throw new Exception("短信可发送数量不足", 1);
            // return array("status" => "fail", "msg" => "短信可发送数量不足");
        }
        //先扣除短信数量操作，防止并发导致超额发送短信。
        $sql = 'UPDATE cms_site_manage SET sms_num = (sms_num - ?) WHERE id = ? AND sms_num >= ?';
        // $sql = db()->fetch(true)->execute($sql, [$sendNum, $siteId, $sendNum]);

        $rs = Db::execute($sql, [$sendNum, $siteId, $sendNum]);
        if (!$rs) {
            throw new Exception("扣除短信可发送数量失败", 1);
        }

        $rs = Db::name("sms_send_schedule")->insertAll($data);
        if(!$rs){
            throw new Exception("生成短信计划失败", 1);
        }
        Db::commit();
        return array("status" => "success", "msg" => "生成短信计划成功。", "num" => $sendNum);
    } catch (Exception $ex) {
        Db::rollback();
        $args = func_get_args();
        Log::error($ex->getMessage() . " 参数信息为： " . print_r(['siteId' => $args[0], 'sendNum' =>$args[1], 'data' => $args[2]], true));
        return array("status" => "fail", "msg" => $ex->getMessage() );
    }
}


/**
 * 检验该站点是否有权限发送该类型的短信
 * @param $idsite           站点id
 * @param $index            系统短信类型
 * @param $tab_index        0-客户/1-商务
 * @return string           该场景下的设置，（0000|1100|...）
 */
function allow_sms_send($idsite, $index)
{
    if($index<1) $index=1;

    $result= db('sms_open_config')->field('config')->where(['idsite'=>$idsite])->find();
    if(!$result)
        return false;
    $tmp=$result['config'];
    $tmp_arr=explode(",",$tmp);
    if($tmp_arr[0] != '1')
        return false;
    if($index > count($tmp_arr))
        return false;

    return $tmp_arr[$index];

}


/**
 * 增减库存，用于有乐观锁的表增减库存或其他操作
 * @author Hlt
 * @DateTime 2019-05-17T11:30:04+0800
 * @param    string                   $tableName   表名，无前缀
 * @param    array                    $where       条件，必须有主键条件
 * @param    integer                  $num         增减数量
 * @param    boolean                  $sell        是否为卖出商品
 * @param    string                   $sold        已卖出字段
 * @param    string                   $versionName 版本字段名
 * @return   void
 */
function changeStock($packageId, $num, $sell = true, $sold = 'sold', $versionName = 'version')
{
    if($num <= 0)
    {
        return false;
    }
    $tableName = 'package';
    $oldData = db('package')->where(['package_id' => $packageId])->find();
    if(!$oldData)
    {
        return false;
    }

    $where = ['package_id' => $packageId];
    // 如果套餐共享活动库存
    if($oldData['package_sum'] == 0)
    {
        $tableName = 'activity';
        $where = ['idactivity' => $oldData['activity_id']];
        $oldData = db('activity')->where($where)->find();
        // 如果活动库存无限
        if($oldData['intsignnum'] == 0)
        {
            // 直接返回
            return true;
        }
    }
    //版本自增
    $data = [$versionName => $oldData[$versionName] + 1];
    //版本作为条件
    $where[$versionName] = $oldData[$versionName];

    if($sell)
    {
        // 锁定库存
        $data[$sold] = (new Query)->raw($sold . ' + ' . (int)$num);
        if($tableName == 'package')
        {
            $where['package_sum'] = ['egt', (new Query)->raw($sold . ' + ' . (int)$num)];
        }else
        {
            $where['intsignnum'] = ['egt', (new Query)->raw($sold . ' + ' . (int)$num)];
        }
    }else
    {
        //释放库存
        $data[$sold] = (new Query)->raw($sold . ' - ' . (int)$num);
        // 已卖出数量大于等于退货数量
        $where[$sold] = ['egt', $num];
    }

    // $sql = db($tableName)->fetchSql(true)->where($where)->update($data);
    // var_dump($sql);die;
    return db($tableName)->where($where)->update($data);
}

/**
 * 修改使用状态
 * @param $result
 * @throws \think\Exception
 * @throws \think\exception\PDOException
 */
function update_used_status($result){
    //进行循环未使用,已过期的数据
    if($result){
        foreach ($result as $value){
            db('cashed_card_receive')->where(['id'=>$value['id']])->update(['used_status'=>15]);
        }
    }
}


function isDate($datetime, $format = 'Y-m-d H:i:s')
{
    $timestamp = strtotime($datetime);
    if(!is_numeric($timestamp))
    {
        return false;
    }

    return strtotime(date($format, $timestamp)) == $timestamp;
}
/**
 * 退款方法
 *
 * @param string $sitecode 机构代号
 * @param string $out_trade_no 商户订单号
 * @param int $refund_fee 商户退款单号
 * @param int $total_fee 退款金额
 * @param string $refund_desc 总金额
 * @param string $notify_url 异步接收微信支付退款结果通知的回调地址
 * @return boolean|string 成功返回true,错误返回错误消息
 * @author Chenjie
 */
function refund($sitecode, $out_trade_no, $refund_fee, $total_fee, $refund_desc, $notify_url){
    $out_refund_no = date('YmdHis').rand(100000,999999);
    $data = [
        'out_trade_no' => $out_trade_no,   // 商户订单号
        'out_refund_no' => $out_refund_no,  // 商户退款单号
        'refund_fee' => $refund_fee,      // 退款金额
        'total_fee' => $total_fee,        // 总金额
        'refund_desc' => $refund_desc,    // 退款原因
        'notify_url' => $notify_url,      // 异步接收微信支付退款结果通知的回调地址

    ];
    $config = getWeiXinConfig($sitecode);
    $api = new \think\wx\Api(
        array(
            'appId' => trim($config['appid']),
            'appSecret'      => trim($config['appsecret']),
            'mchId'          => trim($config['mchid']),
            'key'            => trim($config['paykey']) ,
            'sslKeyPath'     => __ROOT__."/cart/".trim($config['sslkeypath']) ,
            'sslCertPath'    => __ROOT__."/cart/".trim($config['sslcertpath']),
            'sslrootcatPath' => __ROOT__."/cart/".trim($config['cainfo']),
        )
    );

    $result = $api->refund($data);
    if($result['state'] == 1){
        $info = $result['info'];
        if($info['return_code'] == 'SUCCESS'){
            return true;
        }else{
            $return_msg = $info['return_msg'];
            switch($return_msg){
                case 'SYSTEMERROR':
                    $error_msg = '接口返回错误';
                case 'BIZERR_NEED_RETRY':
                    $error_msg = '退款业务流程错误，需要商户触发重试来解决';
                case 'TRADE_OVERDUE':
                    $error_msg = '订单已经超过退款期限';
                case 'ERROR':
                    $error_msg = '业务错误';
                case 'USER_ACCOUNT_ABNORMAL':
                    $error_msg = '退款请求失败';
                case 'INVALID_REQ_TOO_MUCH':
                    $error_msg = '无效请求过多';
                case 'NOTENOUGH':
                    $error_msg = '余额不足';
                case 'INVALID_TRANSACTIONID':
                    $error_msg = '无效transaction_id';
                case 'PARAM_ERROR':
                    $error_msg = '参数错误';
                case 'APPID_NOT_EXIST':
                    $error_msg = 'APPID不存在';
                case 'MCHID_NOT_EXIST':
                    $error_msg = 'MCHID不存在';
                case 'ORDERNOTEXIST':
                    $error_msg = '订单号不存在';
                case 'REQUIRE_POST_METHOD':
                    $error_msg = '请使用post方法';
                case 'SIGNERROR':
                    $error_msg = '签名错误';
                case 'XML_FORMAT_ERROR':
                    $error_msg = 'XML格式错误';
                case 'FREQUENCY_LIMITED':
                    $error_msg = '频率限制';
            }
            return $error_msg;
        }
    }
}

/**
 * 检查机构是否购买营销包
 *
 * @param int $siteid 机构ID
 * @param int $marketing_package_code 营销包代号
 * @return bool 是否购买, true：购买 false: 没有购买
 * @author Chenjie
 */
function checkedMarketingPackage($siteid,$marketing_package_code){
    $result = db('marketing_package_record')->where(['siteid'=>$siteid,'marketing_package_code'=>$marketing_package_code])->find();
    return $result ? true : false;
}

/**
 * 写入机构操作日志
 *
 * @param string $operate_name 操作名称
 * @param string $operate_type 操作类型
 * @param string $explain   说明
 * @param int $node_id 节点ID
 * @return void
 * @author Chenjie
 * @Date 2019-07-02 16:05:31
 */
function write_site_operate_log($operate_name = '',  $operate_type = '', $explain = '', $node_id = 0){
    $siteid = session('idsite');
    $module_name = strtolower(CONTROLLER_NAME);

    // 过滤不在配置文件的类型
    if(!in_array($operate_type,config("use_operate_type"))) return false;

    // 缓存模块列表
    $module_list = cache("module_list");
    if(!$module_list){
        $module_list = db('module')->field("idmodule, chrname, codecatalog, chrcode")->whereIn('idsite',[0,$siteid])->select();
        cache("module_list", $module_list);
    }

    // 从模块列表，获取当前模块
    $module = [];
    foreach($module_list as $value){
        if($value['chrcode'] == $module_name){
            $module = $value;
        }   
    }

    // 缓存CMS栏目
    $catalog_list =cache("catalog_list");
    if(!$catalog_list){
        $catalog_list = db('catalog')->field('idcatalog,chrname,chrcode')->select();
        cache("catalog_list", $catalog_list);
    }

    // 从缓存CMS栏目中，获取当前栏目
    $catalog = [];
    foreach($catalog_list as $value){
        if($module['codecatalog'] == $value['chrcode']){
            $catalog = $value;
        }
    }

    // 写入日志到数据库
    $data = [
        'siteid' => $siteid,
        'column_id' => $catalog['idcatalog'],
        'column_name' => $catalog['chrname'],
        'module_id' => $module['idmodule'],
        'module_name' => $module['chrname'],
        'node_id' => $node_id,
        'operate_name' => $operate_name,
        'explain' => $explain,
        'operate_type' => $operate_type,
        'account_id' => session("AccountID"),
        'chrname' => session("UserName"),
        'ip' => getip(),
        'operate_time' => time()
    ];
    $result = db('site_operate_log')->insert($data);
    if(!$result){
        return false;
    }
    return true;
}