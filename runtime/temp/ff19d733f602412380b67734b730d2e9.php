<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:69:"D:\workspace\work\public/../application/admin\view\activity\modi.html";i:1563344465;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>活动管理</title>
    <link href="/static/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="/static/css/page.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <script type="text/javascript" src="/static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/static/js/del-checked.js"></script>
    <script type="text/javascript" src="/static/js/layer/layer.js"></script>
    <script type="text/javascript" src="/static/js/ContorlValidator.js"></script>
    <script type="text/javascript" src="/static/js/CostomLinkOpenSw.js"></script>
    <link href="/static/js/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="/static/js/daterangepicker/moment.min.js"></script>
    <script type="text/javascript" src="/static/js/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="/static/js/lhgdialog.js?skin=aero"></script>
    <style>
    .layui-layer.layui-layer-page.layui-layer-rim.layer-anim .layui-layer-content{
            padding: 10px;
        }
   .layui-layer.layui-layer-page.layui-layer-rim.layer-anim *{
        max-width : 100%!important;
        box-sizing: border-box;
    }
    .layui-layer.layui-layer-page.layui-layer-rim.layer-anim img{
        height: auto!important;
    }
    </style>


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
        function sel_address(){
            var lng=$("#chrmaplng").val();
            var lat=$("#chrmaplat").val();
            CustomOpen("/static/map/test.php?lng="+lng+"&lat="+lat,"sel_address","选择地址",950,680);
        }
    </script>
    <script type="text/javascript" src="/static/plugins/Ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="/static/plugins/Ueditor/ueditor.all.js"></script>
</head>
<body>
<div class="oa_pop">
    <div class="oa_pop-main">
        <div style="height: 6px"></div>
        <div class="oa_title clearfix">
            <span class="oa_ico-right"></span>
            <span class="oa_title-btn"></span>
            <span class="oa_ico-left"></span>
            活动管理        </div>
        <div class="oa_edition">
            <form id="frm" action="" method="post">
                <table id="tabcontent" width="100%" border="0" cellspacing="1" cellpadding="0" class="oa_edition" style="border-bottom: #e0e0e0 solid 1px">
                    <tr>
                        <td width="150"class="oa_cell-left"><span style="color:#ff0000">*</span>活动标题：</td>
                        <td class="oa_cell-right"><input name="chrtitle" id="chrtitle" type="text" value="<?php echo $datainfo['chrtitle']; ?>" class="form-control "  style="width:800px;"   chname="活动标题"  /> </td>
                    </tr>
                    <tr>
                        <td width="150"class="oa_cell-left">活动简称：</td>
                        <td class="oa_cell-right"><input name="short_title" id="short_title" type="text" value="<?php echo $datainfo['short_title']; ?>" class="form-control "  style="width:800px;"   chname="活动标题"  /> </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">活动简介：</td>
                        <td class="oa_cell-right">
                            <textarea id="chrsummary" name="chrsummary" is_null="1" cols="50" rows="3"  class="form-control input oa_input-200"  style="width:800px;height:80px;"><?php echo $datainfo['chrsummary']; ?></textarea>
                            <div class="oa_info-source">活动简介内容展,示列表活动标题底部</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">外链URL：</td>
                        <td class="oa_cell-right"><input   name="chrurl" id="chrurl" type="text" value="<?php echo $datainfo['chrurl']; ?>"  class="form-control "  style="width:800px;"  chname="外链URL"  /></td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">关键字：</td>
                        <td class="oa_cell-right"><input   name="chrkeyword" id="chrkeyword" type="text" value="<?php echo $datainfo['chrkeyword']; ?>"  class="form-control "  style="width:800px;"  chname="关键字,多个以逗号分开"  /></td>
                    </tr>

                    <tr>
                        <td width="150" class="oa_cell-left">所属商务：</td>
                        <td class="oa_cell-right">
                            <select name="intselmarket" id="intselmarket" class="form-control" style="width: auto; height: 30px" >
                                <option value="0:">==请选择==</option>
                                <?php if(is_array($user) || $user instanceof \think\Collection || $user instanceof \think\Paginator): $i = 0; $__LIST__ = $user;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo $vo['idaccount']; ?>:<?php echo $vo['chrname']; ?>" <?php if ($vo['idaccount'] == $datainfo['intselmarket']) {echo "selected";}?> ><?php echo $vo['chrname']; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left"><span style="color:#ff0000">*</span>分类：</td>
                        <td class="oa_cell-right">
                            <select name="fidtype" id="fidtype" class="form-control" style="width: auto; height: 30px" >
                                <option  value="">请选择</option>
                                <?php if(is_array($hdfl) || $hdfl instanceof \think\Collection || $hdfl instanceof \think\Paginator): $i = 0; $__LIST__ = $hdfl;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo $vo['code']; ?>" <?php if ($vo['code'] == $datainfo['fidtype']) {echo "selected";}?> ><?php echo $vo['name']; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">省：</td>
                        <td class="oa_cell-right">
                            <select  id="fidprovince" onchange="getChild(this.value,0,'fidcity','/Admin/Api/getRegion');" is_null="1"  name="fidprovince"   class="form-control" style="width: auto; height: 30px" >
                                <option  value="">请选择</option>
                            </select>
                            <script language="javascript">
                                getChild(0,<?php echo $datainfo['fidprovince']; ?>,'fidprovince','/Admin/Api/getRegion');
                                <?php if (!empty($datainfo['fidcity']) && $datainfo['fidcity'] != 0) {?>
                                getChild(<?php echo $datainfo['fidprovince']; ?>,<?php echo $datainfo['fidcity']; ?>,'fidcity','/Admin/Api/getRegion');
                                 <?php }?>
                            </script>

                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">市：</td>
                        <td  class="oa_cell-right">
                            <select  id="fidcity" onchange="getChild(this.value,0,'fidarea','/Admin/Api/getRegion');" is_null="1"  name="fidcity"    class="form-control" style="width: auto; height: 30px">
                                <option  value="">请选择</option>
                            </select>
                            <?php if (!empty($datainfo['fidarea']) && $datainfo['fidarea'] != 0) {?>
                            <script language="javascript">
                                    getChild(<?php echo $datainfo['fidcity']; ?>,<?php echo $datainfo['fidarea']; ?>,'fidarea','/Admin/Api/getRegion');
                            </script>
                            <?php }?>
                        </td>
                    </tr>
                     <tr>
                        <td width="150" class="oa_cell-left">区：</td>
                        <td class="oa_cell-right">
                            <select  id="fidarea"  is_null="1" name="fidarea"   class="form-control" style="width: auto; height: 30px" >
                                <option  value="">请选择</option>
                            </select>
                        </td>
                    </tr>
                    <tr style="display: none">
                        <td width="150" class="oa_cell-left">商圈id（暂时选一个）：</td>
                        <td class="oa_cell-right">
                            <input name="fiddistrict" id="fiddistrict" type="text" value="<?php echo $datainfo['fiddistrict']; ?>" class="form-control "  style="width:800px;"  chname="商圈"  />
                        </td>
                    </tr>
                    </tr>
                     <tr>
                        <td width="150" class="oa_cell-left">活动小图片（140x140）：</td>
                        <td class="oa_cell-right">
                            <input name="chrimg_m" id="chrimg_m" type="text" value="<?php echo $datainfo['chrimg_m']; ?>"  imgtype="small"  chname="活动小图片（140x140）"   class="form-control "  style="width:800px;" />
                            <input style="display: none;" onclick="GetUploadify(1,'chrimg_m','admin','undefined','*.jpg;*.jpeg;*.png;*.gif;*.jpeg;');" type="button" value="上传单图片"/>
                            <input onclick="uploadimgcut('chrimg_m','admin',140,140);" type="button" value="上传单图片"/>
                            <input onclick="openimg('chrimg_m')" type="button" value="查看图片"/>
                        </td>
                    </tr>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">活动大图片（640x320）：</td>
                        <td class="oa_cell-right">
                        <input name="chrimg" id="chrimg" type="text" value="<?php echo $datainfo['chrimg']; ?>"   chname="活动大图片（640x320）"   class="form-control "  style="width:800px;" />
                        <input style="display: none;" onclick="GetUploadify(1,'chrimg','admin','undefined','*.jpg;*.jpeg;*.png;*.gif;*.jpeg;');" type="button" value="上传单图片"/>
                        <input onclick="uploadimgcut('chrimg','admin',640,320);" type="button" value="上传单图片"/>
                        <input onclick="openimg('chrimg')" type="button" value="查看图片"/>
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">活动开始时间：</td>
                        <td class="oa_cell-right">
                            <div style="width:250px" class="input-prepend input-group"><span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                            <input type="text" is_null="0" id="dtstart" name="dtstart" class="form-control"  value="<?php echo $datainfo['dtstart']; ?>"></div>
                            <script language='JavaScript'>seltime("dtstart","YYYY-MM-DD HH:mm:ss")</script>                               <!-- -->
                            <span style="color: red;display: none;" class="dtbirthday_null">(不能为空)</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">活动结束时间：</td>
                        <td class="oa_cell-right">
                            <div style="width:250px" class="input-prepend input-group"><span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                            <input type="text" is_null="0" id="dtend" name="dtend" class="form-control"  value="<?php echo $datainfo['dtend']; ?>"></div><script language='JavaScript'>seltime("dtend","YYYY-MM-DD HH:mm:ss")</script>                               <!-- -->
                            <span style="color: red;display: none;" class="dtbirthday_null">(不能为空)</span>
                        </td>
                    </tr>

                    <tr>
                        <td width="150" class="oa_cell-left">是否收费：</td>
                        <td class="oa_cell-right">
                            <select  id="ischarge"  is_null="1" name="ischarge"   class="form-control" style="width: auto; height: 30px" >
                                <option value="1" <?php if ($datainfo['ischarge'] != 2) {echo "selected";}?> >免费</option>
                                <option value="2" <?php if ($datainfo['ischarge'] == 2) {echo "selected";}?> >收费</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">适合对象：</td>
                        <td class="oa_cell-right">
                            <select  id="chrrange"  is_null="1" name="chrrange"   class="form-control" style="width: auto; height: 30px" >
                                <option value="0">==请选择==</option>
                                <option value="1" <?php if ($datainfo['chrrange'] == 1) {echo "selected";}?> >家长及儿童</option>
                                <option value="2" <?php if ($datainfo['chrrange'] == 2) {echo "selected";}?> >仅儿童</option>
                                <option value="3" <?php if ($datainfo['chrrange'] == 3) {echo "selected";}?> >仅家长</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">参与最小年龄：</td>
                        <td class="oa_cell-right">
                            <select  id="minage"  is_null="1" name="minage"   class="form-control" style="width: auto; height: 30px" >
                                <option value="0">==请选择==</option>
                                <?php for ($i = 1; $i < 100; $i++) {?>
                                <option value="<?php echo $i; ?>" <?php if ($datainfo['minage'] == $i) {echo "selected";}?> ><?php echo $i; ?></option>
                                <?php }?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">参与最大年龄：</td>
                        <td class="oa_cell-right">
                            <select  id="maxage"  is_null="1" name="maxage"   class="form-control" style="width: auto; height: 30px" >
                                <option value="0">==请选择==</option>
                                <?php for ($i = 1; $i < 100; $i++) {?>
                                <option value="<?php echo $i; ?>" <?php if ($datainfo['maxage'] == $i) {echo "selected";}?> ><?php echo $i; ?></option>
                                <?php }?>
                            </select>
                         </td>
                    </tr>

                    <tr style="display: none">
                        <td width="150" class="oa_cell-left">活动价值：</td>
                        <td class="oa_cell-right">
                            <textarea id="chrworth" name="chrworth" is_null="1" cols="50" rows="3"  class="form-control input oa_input-200"  style="width:800px;height:80px;"><?php echo $datainfo['chrworth']; ?></textarea>
                        </td>
                    </tr>
                    <tr style="display: none">
                        <td width="150" class="oa_cell-left">收费说明：</td>
                        <td class="oa_cell-right">
                            <textarea id="chrchargemark" name="chrchargemark" is_null="1" cols="50" rows="3"  class="form-control input oa_input-200"  style="width:800px;height:80px;"><?php echo $datainfo['chrchargemark']; ?></textarea>
                        </td>
                    </tr>
                    <tr style="display: none">
                        <td width="150" class="oa_cell-left">下单须知：</td>
                        <td class="oa_cell-right">
                            <textarea id="chrnotice" name="chrnotice" is_null="1" cols="50" rows="3"  class="form-control input oa_input-200"  style="width:800px;height:80px;"><?php echo $datainfo['chrnotice']; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">活动详细地点：</td>
                        <td class="oa_cell-right">
                            <input name="chraddressdetail" id="chraddressdetail" type="text" value="<?php echo $datainfo['chraddressdetail']; ?>" readonly="readonly" class="form-control "  style="width:800px;"   chname="活动详细地点"  />
                            <input name="chrmaplng" id="chrmaplng" type="hidden" value="<?php echo $datainfo['chrmaplng']; ?>" class="form-control "  style="width:800px;"   chname="活动详细地点经度"  />
                            <input name="chrmaplat" id="chrmaplat" type="hidden" value="<?php echo $datainfo['chrmaplat']; ?>" class="form-control "  style="width:800px;"   chname="活动详细地点纬度"  />
                            <input name="openmap" id="openmap" type="button" value="选择地址"  onclick="sel_address();" />
                        </td>
                    </tr>
                                        <tr>
                        <td width="150" class="oa_cell-left">交通指引：</td>
                        <td class="oa_cell-right">
                            <textarea id="chrmap" name="chrmap" is_null="1" cols="50" rows="3"  class="form-control input oa_input-200"  style="width:800px;height:80px;"><?php echo $datainfo['chrmap']; ?></textarea>
                        </td>
                    </tr>
                   <tr>
                        <td width="150" class="oa_cell-left">详细内容：</td>
                        <td class="oa_cell-right">
                            <div ><textarea  style="width:800px;height:600px;" class="span12 ckeditor" id="chrcontent" is_null="1" name="chrcontent"><?php echo $datainfo['chrcontent']; ?></textarea></div>
                            <script>var editorcontent;$(function(){editorcontent = new UE.ui.Editor(options);editorcontent.render("chrcontent");});</script>
                            <a href="javascript:previous();">预览</a>
                        </td>
                   </tr>
                   <tr>
                        <td width="150" class="oa_cell-left">发布时间：</td>
                        <td class="oa_cell-right">
                            <div style="width:250px" class="input-prepend input-group"><span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                <input type="text" is_null="0" id="dtpublishtime" name="dtpublishtime" class="form-control"  value="<?php echo $datainfo['dtpublishtime']; ?>"></div><script language='JavaScript'>seltime("dtpublishtime","YYYY-MM-DD HH:mm:ss")</script>
                            </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">用户权限：</td>
                        <td width="150" class="oa_cell-right">
                            <?php if(is_array($hyfl) || $hyfl instanceof \think\Collection || $hyfl instanceof \think\Paginator): if( count($hyfl)==0 ) : echo "" ;else: foreach($hyfl as $key=>$v): ?>
                            <input type="checkbox" name="usertype[]" value="<?php echo $v['id']; ?>" <?php if (in_array($v['code'], explode(",", trim($datainfo['usertype'], ',')))) {echo "checked";}?> /><?php echo $v['name']; endforeach; endif; else: echo "" ;endif; ?>
                        </td>
                    </tr>
                   <tr>
                        <td width="150" class="oa_cell-left">活动标签：</td>
                        <td class="oa_cell-right">
                            <?php if(is_array($hdbq) || $hdbq instanceof \think\Collection || $hdbq instanceof \think\Paginator): $i = 0; $__LIST__ = $hdbq;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <input type="checkbox" name="chrtags[]" value="<?php echo $vo['code']; ?>" <?php if (in_array($vo['code'], explode(",", trim($datainfo['chrtags'], ',')))) {echo "checked";}?> ><?php echo $vo['name']; ?></input>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </td>
                   </tr>
                   <tr style="display: none;">
                        <td width="150" class="oa_cell-left">活动分享海报模版图：</td>
                        <td class="oa_cell-right">
                            <input name="sharebackpic" id="sharebackpic" type="text" value="<?php echo $datainfo['sharebackpic']; ?>"   class="form-control "  style="width:800px;" />
                            <input onclick="GetUploadify(1,'sharebackpic','admin','undefined','*.jpg;*.jpeg;*.png;*.gif;*.jpeg;');" type="button" value="上传单图片"/>
                        </td>
                   </tr>

                    <tr style="display: none;">
                        <td width="150" class="oa_cell-left">是否自办：</td>
                        <td class="oa_cell-right">
                            <input type="checkbox" name="chktags" id="chktags" value="1" <?php if ($datainfo['chktags'] == 1) {echo "checked";}?>>自办</input>
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">是否置顶：</td>
                        <td class="oa_cell-right">
                            <input type="checkbox" name="chkcontentlev" id="chkcontentlev" value="1" <?php if ($datainfo['chkcontentlev'] == 1) {echo "checked";}?>>置顶</input>
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">推荐到首页：</td>
                        <td class="oa_cell-right">
                            <input type="checkbox" name="chkisindex" id="chkisindex" value="1" <?php if ($datainfo['chkisindex'] == 1) {echo "checked";}?>>推荐</input>
                        </td>
                    </tr>
                    <tr  style="display: none;">
                        <td width="150" class="oa_cell-left">是否下单：</td>
                        <td class="oa_cell-right">
                            <input type="checkbox" name="chkisthird" id="chkisthird" value="1" <?php if ($datainfo['chkisthird'] == 1) {echo "checked";}?>>自办</input>
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">是否下架：</td>
                        <td class="oa_cell-right">
                            <input type="checkbox" name="chkdown" id="chkdown" value="1" <?php if ($datainfo['chkdown'] == 1) {echo "checked";}?>>下架</input>
                        </td>
                    </tr>
                    <tr>
                        <td width="150" class="oa_cell-left">服务条款：</td>
                        <td class="oa_cell-right">
                            <div ><textarea  style="width:800px;height:600px;" class="span12 ckeditor" id="txtfwtk" is_null="1" name="txtfwtk"><?php echo $datainfo['txtfwtk']; ?></textarea></div>
                            <script>var editorcontent;$(function(){editorcontent = new UE.ui.Editor(options);editorcontent.render("txtfwtk");});</script>
                        </td>
                    </tr>
                    <tr >
                        <td width="150" class="oa_cell-left">开启下单：</td>
                        <td class="oa_cell-right">
                            <input  id="chkbm" onclick="javascript:isbm();"  type="checkbox" name="chksignup" value="1" <?php if ($datainfo['chksignup'] == 1) {echo "checked";}?>>在线下单</input>
                        </td>
                    </tr>
                    <tr id="bm">
                        <td colspan="2" style="padding: 0">
                            <table id="tabcontent1" border="0">

                            <tr>
                                <td width="150" class="oa_cell-left">下单开始时间：</td>
                                <td class="oa_cell-right">
                                    <div style="width:250px" class="input-prepend input-group"><span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input type="text" is_null="0" id="dtsignstime" name="dtsignstime" class="form-control"  value="<?php echo $datainfo['dtsignstime']; ?>"></div><script language='JavaScript'>seltime("dtsignstime","YYYY-MM-DD HH:mm:ss")</script>                               <!-- -->
                                    <span style="color: red;display: none;" class="dtbirthday_null">(不能为空)</span>
                                </td>
                            </tr>
                            <tr>
                                <td width="150" class="oa_cell-left">下单截止时间：</td>
                                <td class="oa_cell-right">
                                    <div style="width:250px" class="input-prepend input-group"><span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input type="text" is_null="0" id="dtsignetime" name="dtsignetime" class="form-control"  value="<?php echo $datainfo['dtsignetime']; ?>"></div><script language='JavaScript'>seltime("dtsignetime","YYYY-MM-DD HH:mm:ss")</script>                               <!-- -->
                                    <span style="color: red;display: none;" class="dtbirthday_null">(不能为空)</span>
                                </td>
                            </tr>
                                <?php if($is_cashed): ?>
                                <tr >
                                    <td width="150" class="oa_cell-left">是否可以使用现金券：</td>
                                    <td class="oa_cell-right">
                                        <input type="checkbox" onclick="javascript:show_chkpay()" name="is_use_cashed" id="chkvolume" value="1" <?php if ($datainfo['is_use_cashed'] == 1) {echo "checked";}?>></input>
                                    </td>
                                </tr>
                                <tr id="set_volume" <?php if ($datainfo['is_use_cashed'] != 1) {echo "style=\"display: none;\"";}?> >
                                    <td width="150" class="oa_cell-left"><span style="color:#ff0000">*</span>设置：</td>
                                    <td class="oa_cell-right">
                                        <div><span style="padding-left: 12px;color: #333">最大数量：</span><input name="max_use" type="text" value="<?php echo $datainfo['max_use']; ?>"  class="form-control "  style="width:100px;" />(张) <font style="color:red;">&nbsp;&nbsp; 0表示不限制</font></div>
                                        <div style="padding-top:5px;"><span style="padding-left: 12px;color: #333">最大金额：</span><input name="max_amount" type="text" value="<?php echo intval($datainfo['max_amount']) ?>"  class="form-control "  style="width:100px;"  />(元) <font style="color:red;">&nbsp;&nbsp; 0表示不限制</font> </div>
                                    </td>
                                </tr>
                                <tr id="is_can_receive" <?php if ($datainfo['is_use_cashed'] != 1) {echo "style=\"display: none;\"";}?>>
                                    <td width="150" class="oa_cell-left">是否可以领取现金券：</td>
                                    <td class="oa_cell-right">
                                        <input type="checkbox" onclick="javascript:show_cash()" name="is_receive_cashed" id="chkcash" value="1" <?php if ($datainfo['is_receive_cashed'] == 1) {echo "checked";}?>></input>
                                    </td>
                                </tr>
                                <tr  id="set_cash" <?php if ($datainfo['is_receive_cashed'] != 1) {echo "style=\"display: none;\"";}?> >
                                    <td width="150" class="oa_cell-left"><span style="color:#ff0000">*</span>设置：</td>
                                    <td class="oa_cell-right">
                                        <div style="padding-top:5px;"><span style="padding-left: 12px;color: #333">领券金额：</span><input name="activity_cashed_amount" type="text" value="<?php echo intval($datainfo['activity_cashed_amount']) ?>"  class="form-control "  style="width:100px;"  />(元)  </div>
                                        <div style="padding-top:5px;"><span style="padding-left: 24px;color: #333">有效期：</span><input name="activity_cashed_validity" type="text" value="<?php echo $datainfo['activity_cashed_validity']; ?>"  class="form-control "  style="width:100px;"  />(天)
                                        <div style="padding-top:5px;">可领券用户：
                                            <?php if ($hyfl) {?>
                                            <div>
                                                <span style="padding-left: 12px;color: #333">用户类别：</span>
                                                <?php if(is_array($hyfl) || $hyfl instanceof \think\Collection || $hyfl instanceof \think\Paginator): $i = 0; $__LIST__ = $hyfl;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                <input type="checkbox" name="receive_activity_cashed_categoryid[]" value="<?php echo $vo['code']; ?>" <?php if (in_array($vo['code'], explode(",", trim($datainfo['receive_activity_cashed_categoryid'], ',')))) {echo "checked";}?> ><?php echo $vo['name']; ?></input>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </div>
                                            <?php }?>
                                            <div>
                                                <span style="padding-left: 12px;color: #333">关注状态：</span>
                                            <input name="receive_activity_cashed_intstate_user[]" <?php if (in_array("1", explode(",", trim($datainfo['receive_activity_cashed_intstate_user'], ',')))) {echo "checked";}?> type="checkbox" value="1"/> 关注
                                            <input name="receive_activity_cashed_intstate_user[]" <?php if (in_array("2", explode(",", trim($datainfo['receive_activity_cashed_intstate_user'], ',')))) {echo "checked";}?> type="checkbox" value="2"/> 取消
                                            <input name="receive_activity_cashed_intstate_user[]" <?php if (in_array("3", explode(",", trim($datainfo['receive_activity_cashed_intstate_user'], ',')))) {echo "checked";}?> type="checkbox" value="3"/> 游客
                                            </div>
                                            <div>
                                                <span style="padding-left: 12px;color: red">注意：默认未设置可领券用户代表所有用户均可领！</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr >
                                    <td width="150" class="oa_cell-left">是否可以分享现金券：</td>
                                    <td class="oa_cell-right">
                                        <input type="checkbox" onclick="javascript:show_is_share_cashed()" name="is_share_cashed" id="is_share_cashed" value="1" <?php if ($datainfo['is_share_cashed'] == 1) {echo "checked";}?>></input>
                                    </td>
                                </tr>
                                <tr id="set_share_cashed" <?php if ($datainfo['is_share_cashed'] != 1) {echo "style=\"display: none;\"";}?> >
                                <td width="150" class="oa_cell-left"><span style="color:#ff0000">*</span>设置现金券计划：</td>
                                <td class="oa_cell-right">
                                    <div>
                                        <select name="cashed_plan_id" id="cashed_plan_id" >
                                            <option value="0">请选择现金券计划</option>
                                            <?php foreach ($cashed_plan as $k => $vo) {?>
                                            <option value="<?php echo $vo['id']; ?>" <?php if ($datainfo['cashed_plan_id'] == $vo['id']) {echo "selected";}?>><?php echo $vo['plan_name']; ?></option>
                                            <?php }?>
                                        </select>

                                        &nbsp;&nbsp;<a href="javascript:window.top.mainFrame.TabAdd('/admin/cashed/index/idsite/<?php echo $idsite; ?>','现金券管理','cashed');">添加现金券计划</a>
                                    </div>
                                </td>
                                </tr>
                                <?php endif; ?>
                            <tr>
                                <td width="150" class="oa_cell-left">是否需要关注：</td>
                                <td class="oa_cell-right">
                                    <input type="checkbox" name="chkissubscribe" value="1" <?php if ($datainfo['chkissubscribe'] == 1) {echo "checked";}?> >勾选此项则需要用户关注公众号后才能下单</input>
                                </td>
                            </tr>
                            <tr class="hidden">
                                <td width="150" class="oa_cell-left"><span style="color:#ff0000">*</span>活动总库存(0为不限)：</td>
                                <td class="oa_cell-right">
                                    <input name="intsignnum" disabled="disabled" id="intsignnum" type="text" value="<?php echo $datainfo['intsignnum']; ?>" class="form-control "  style="width:300px;"  chname="活动总库存(0为不限)"  />
                                </td>
                            </tr>
                            <tr id="allow-refund" style="<?=$datainfo['ischarge'] == 1 ? 'display: none' : '';?>">
                                <td width="150" class="oa_cell-left">是否可申请退款：</td>
                                <td class="oa_cell-right">
                                    <select name="isrefund" id="isrefund" class="form-control" style="width: auto; height: 30px" >
                                        <option value="1" <?php if (1 == $datainfo['isrefund']) {echo "selected";}?> >是</option>
                                        <option value="0" <?php if (0 == $datainfo['isrefund']) {echo "selected";}?> >否</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td width="150" class="oa_cell-left"><span style="color:#ff0000">*</span>报名模版：</td>
                                <td class="oa_cell-right">
                                    <select name="selsignfrom" id="selsignfrom" >
                                        <option value="0">请选报名模版</option>
                                        <?php foreach ($fromtemp as $k => $vo) {?>
                                        <option value="<?php echo $vo['id']; ?>" <?php if ($datainfo['selsignfrom'] == $vo['id']) {echo "selected";}?>><?php echo $vo['title']; ?></option>
                                        <?php }?>
                                    </select>
                                    <a href="javascript:CustomOpen('<?php echo url('/admin/signup/index'); ?>','signup','报名模版', 1000,500);">添加报名模版</a>
                                </td>
                            </tr>
                            <tr style="display: none;">
                                <td width="150" class="oa_cell-left">开启支付：</td>
                                <td class="oa_cell-right">
                                    <input type="checkbox" name="chkpay" value="1" <?php if ($datainfo['chkpay'] == 1) {echo "checked";}?>>开启支付</input>
                                </td>
                            </tr>
                            <tr style="display: none;">
                                <td width="150" class="oa_cell-left">支付方式：</td>
                                <td class="oa_cell-right">
                                    <select name="selpaytype1" id="selpaytype1">
                                        <option value="1" <?php if ($datainfo['selpaytype1'] != 2) {echo "checked";}?>>线上支付</option>
                                        <option value="2" <?php if ($datainfo['selpaytype1'] == 2) {echo "checked";}?>>线下支付</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td width="150" class="oa_cell-left">限制1：</td>
                                <td class="oa_cell-right">
                                    <input type="checkbox" name="chkismobile" value="1" <?php if ($datainfo['chkismobile'] == 1) {echo "checked";}?>>同一手机号只能下单一次（下单表单中必须有手机类型框）</input>
                                </td>
                            </tr>
                            <tr>
                                <td width="150" class="oa_cell-left">限制2：</td>
                                <td class="oa_cell-right">
                                    <input type="checkbox" name="chkisidcard" value="1" <?php if ($datainfo['chkisidcard'] == 1) {echo "checked";}?>>同一身份证号只能下单一次（下单表单中必须有身份证类型框）</input>
                                </td>
                            </tr>
                            <tr>
                                <td width="150" class="oa_cell-left">限制3：</td>
                                <td class="oa_cell-right">
                                    <input name="intmaxpaynum" id="intmaxpaynum" type="text" value="<?php echo $datainfo['intmaxpaynum']; ?>" class="form-control "  style="width:100px;"  chname="限制3">
                                    单次最大购买数量，0表示不限
                                </td>
                            </tr>
                            <tr>
                                <td width="150" class="oa_cell-left">限制4：</td>
                                <td class="oa_cell-right">
                                    <input name="intmaxmobilepaynum" id="intmaxmobilepaynum" type="text" value="<?php echo $datainfo['intmaxmobilepaynum']; ?>" class="form-control "  style="width:100px;"  chname="限制4"  />
                                    单个手机号累计购买上限，0表示不限（下单表单中必须有手机类型框）
                                </td>
                            </tr>
                            <tr>
                                <td width="150" class="oa_cell-left">限制5：</td>
                                <td class="oa_cell-right">
                                    <input name="intmaxidcardpaynum" id="intmaxidcardpaynum" type="text" value="<?php echo $datainfo['intmaxidcardpaynum']; ?>" class="form-control "  style="width:100px;"  chname="限制5"  />
                                    单个身份证累计购买上限，0表示不限（下单表单中必须有身份证类型框）
                                </td>
                            </tr>
                            <tr  style="display: none;">
                                <td width="150" class="oa_cell-left">参与分销：</td>
                                <td class="oa_cell-right">
                                    <input type="checkbox" name="chkisshare" value="1" <?php if ($datainfo['chkisshare'] == 1) {echo "checked";}?>>勾选后，该活动可参与分销功能，套餐中的分销佣金字段需要填写</input>
                                </td>
                            </tr>
                            <tr class="paytypecontent2">
                                <td width="150" class="oa_cell-left"> </td>
                                <td>
                                    <input style="border:none;text-align:center;" readonly="true" type="text" class="oa_input-100" value="关键字1(必填)" />
                                    <input style="border:none;text-align:center;" readonly="true" type="text" class="oa_input-100" value="关键字2" />
                                    <input style="border:none;text-align:center;" readonly="true" type="text" class="oa_input-100" value="市场价" />
                                    <input style="border:none;text-align:center;" readonly="true" type="text" class="oa_input-100" value="会员价(单位/元)" />
                                    <input style="border:none;text-align:center;" readonly="true" type="text" class="oa_input-100" value="成本价(单位/元)" />

                                    <input style="border:none;text-align:center;display:none;" readonly="true" type="text" class="oa_input-100 share" value="Ⅰ分销佣金" />
                                    <input style="border:none;text-align:center;display:none;" readonly="true" type="text" class="oa_input-100 share" value="Ⅱ分销佣金" />
                                    <input style="border:none;text-align:center;display:none;" readonly="true" type="text" class="oa_input-100 share1" value="Ⅲ分销佣金" />

                                    <input style="border:none;text-align:center;" readonly="true" type="text" class="oa_input-100" value="有效期(必填)" />
                                    <input style="border:none;text-align:center;width: 167px;" readonly="true" type="text" class="oa_input-100"  value="库存(0为不限，-1为已售罄)" />
                                </td>
                            </tr>
                            <?php
                            if (empty($datainfo['selcontent']) || !is_array($datainfo['selcontent'])) {
                                $datainfo['selcontent'] = [
                                    [
                                        'keyword1'               => '',
                                        'keyword2'               => '',
                                        'original_price'         => '',
                                        'member_price'           => '',
                                        'cost_price'             => '',
                                        'level1_commission_rate' => 0,
                                        'level2_commission_rate' => 0,
                                        'level3_commission_rate' => 0,
                                        'expire_at'              => '',
                                        'package_sum'            => 0,
                                    ],
                                ];
                            }
                            // 遍历套餐
                            foreach ($datainfo['selcontent'] as $selcontent_index => $value):
                            ?>
                                <tr class="paytypecontent2">
                                    <td width="150" class="oa_cell-left" style="vertical-align: top; padding-top:8px;">套餐<?php echo $selcontent_index + 1; ?>:</td>
                                    <td id="selcontent_<?php echo $selcontent_index + 1; ?>">
                                        <!-- <div> -->
                                            <input name="packages[<?php echo $selcontent_index; ?>][keyword1]" type="text" class="keyword1 oa_input-100" value="<?php echo $value['keyword1']; ?>" />
                                            <input name="packages[<?php echo $selcontent_index; ?>][keyword2]" type="text" class="keyword2 oa_input-100"  value="<?php echo $value['keyword2']; ?>" />
                                            <input name="packages[<?php echo $selcontent_index; ?>][original_price]" type="text" class="original_price oa_input-100 number" value="<?php echo $value['original_price']; ?>" />
                                            <input name="packages[<?php echo $selcontent_index; ?>][member_price]" type="text" class="member_price oa_input-100 number" value="<?php echo $value['member_price']; ?>" />
                                            <input name="packages[<?php echo $selcontent_index; ?>][cost_price]" type="text" class="cost_price oa_input-100 number" value="<?php echo $value['cost_price']; ?>" />

    <!--                                             <input name="packages[<?php echo $selcontent_index; ?>][level1_commission_rate]" type="text" class="level1_commission_rate oa_input-100 number hide share" value="<?php echo $value['level1_commission_rate']; ?>" />
                                            <input name="packages[<?php echo $selcontent_index; ?>][level2_commission_rate]" type="text" class="level2_commission_rate oa_input-100 number hide share" value="<?php echo $value['level2_commission_rate']; ?>" />
                                            <input name="packages[<?php echo $selcontent_index; ?>][level3_commission_rate]" type="text" class="level3_commission_rate oa_input-100 number hide share1" value="<?php echo $value['level3_commission_rate']; ?>" />
    -->
                                            <input name="packages[<?php echo $selcontent_index; ?>][expire_at]" type="text" class="expire_at oa_input-100" id="seldate<?php echo $selcontent_index; ?>" value="<?php echo $value['expire_at']; ?>"  />
                                            <input name="packages[<?php echo $selcontent_index; ?>][package_sum]" style="width: 165px;" type="text" class="package_sum oa_input-100" value="<?php echo $value['package_sum']; ?>"  />
                                            <?php if (isset($value['package_id'])): ?>
                                                <input type="hidden" class="package_id" name="packages[<?php echo $selcontent_index; ?>][package_id]" value="<?php echo $value['package_id']; ?>">
                                            <?php endif;?>
                                            <!-- 未发布状态的活动，允许删除 -->
                                            <input type="button" name="del" value="删除" onclick="delpaytype(<?php echo $selcontent_index + 1; ?>)" style="margin: 0; padding-left: 6px;padding-right:6px;">
                                            <?php if(checkedMarketingPackage($idsite, 'group_buy')): ?>
                                                <input type="button" name="addAssemble" index="<?php echo $selcontent_index; ?>" value="拼团" style="margin: 0; padding-left: 6px;padding-right:6px;" >
                                            <?php endif; 
                                            if (isset($value['group_buy'])) {
                                                foreach ($value['group_buy'] as $groupBuyIndex => $groupBuy): ?>
                                                    <table class="ass-table" style="margin-left:20px" index="<?php echo $groupBuyIndex; ?>">
                                                    <input type="hidden" class="group-buy-id" name="packages[<?php echo $selcontent_index; ?>][group_buy][<?php echo $groupBuyIndex; ?>][group_buy_id]" value="<?php echo $groupBuy['group_buy_id']; ?>">
                                                        <tr>
                                                            <td>
                                                                <span class="td-left">拼团方案</span>
                                                                <input type="checkbox" name="packages[<?php echo $selcontent_index; ?>][group_buy][<?php echo $groupBuyIndex; ?>][state]"  class="chooseBtn form-control" <?=$groupBuy['state'] == 1 ? 'checked' : '';?>>
                                                                <label  class="choose-label" style="margin: 0; vertical-align: middle;"></label>
                                                                <input class="delete-group-buy" type="button" value="删除" style="margin: 0; float: right; padding-left: 6px;padding-right:6px;">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <span class="td-left">成团商品数量</span>
                                                                <input class="td-right form-control" type="text" name="packages[<?php echo $selcontent_index; ?>][group_buy][<?php echo $groupBuyIndex; ?>][group_num]" value="<?php echo $groupBuy['group_num']; ?>" style="width: 100px;">
                                                                <span>必须大于等于2</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <span class="td-left">拼团活动开始时间</span>
                                                                <div class="oa_cell-right" style="display: inline-block; vertical-align: middle;">
                                                                    <div style="width:250px" class="input-prepend input-group">
                                                                        <span class="add-on input-group-addon">
                                                                            <i class="glyphicon glyphicon-calendar fa fa-calendar">
                                                                            </i>
                                                                        </span>
                                                                        <input type="text" is_null="0" id="dtsignstime" autocomplete="off" name="packages[<?php echo $selcontent_index; ?>][group_buy][<?php echo $groupBuyIndex; ?>][start_at]" class="form-control select-time" value="<?php echo $groupBuy['start_at']; ?>">
                                                                    </div>
                                                                    <span style="color: red;display: none;" class="dtbirthday_null">(不能为空)
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <span class="td-left">拼团活动结束时间</span>
                                                                <div class="oa_cell-right" style="display: inline-block; vertical-align: middle;">
                                                                    <div style="width:250px" class="input-prepend input-group">
                                                                        <span class="add-on input-group-addon">
                                                                            <i class="glyphicon glyphicon-calendar fa fa-calendar">
                                                                            </i>
                                                                        </span>
                                                                        <input type="text" autocomplete="off" id="dtsignstime" name="packages[<?php echo $selcontent_index; ?>][group_buy][<?php echo $groupBuyIndex; ?>][end_at]" class="form-control select-time" value="<?php echo $groupBuy['end_at']; ?>">

                                                                    </div>
                                                                    <span style="color: red;display: none;" class="dtbirthday_null">(不能为空)
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <span class="td-left">团购价格</span>
                                                                <input class="td-right form-control" type="text" name="packages[<?php echo $selcontent_index; ?>][group_buy][<?php echo $groupBuyIndex; ?>][group_buy_price]" value="<?php echo $groupBuy['group_buy_price']; ?>" style="width: 100px;">
                                                                <span>元</span>
                                                            </td>
                                                        </tr>
                                                        <tr class="hidden">
                                                            <td>
                                                                <span class="td-left">是否允许优惠券</span>
                                                                <select name="packages[<?php echo $selcontent_index; ?>][group_buy][<?php echo $groupBuyIndex; ?>][allow_coupon]"  class="form-control" disabled="disabled" style="width: auto; height: 34px">
                                                                    <option value="1" <?=$groupBuy['allow_coupon'] == 1 ? 'selected="selected"' : ''?> >是</option>
                                                                    <option value="0" <?=$groupBuy['allow_coupon'] == 0 ? 'selected="selected"' : ''?> >否</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <span class="td-left">时间限制类型</span>
                                                                <select name="packages[<?php echo $selcontent_index; ?>][group_buy][<?php echo $groupBuyIndex; ?>][time_limit_type]" class="form-control time-limit-type" style="width: auto; height: 34px">
                                                                    <option value="1" <?=$groupBuy['time_limit_type'] == 1 ? 'selected="selected"' : ''?> >开团固定时间后拼团结束</option>
                                                                    <option value="2" <?=$groupBuy['time_limit_type'] == 2 ? 'selected="selected"' : ''?> >拼团活动结束时间到，所有拼团活动立刻停止</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr class="time-limit">
                                                            <td>
                                                                <span class="td-left">组团时间限制</span>
                                                                <input class="td-right form-control" autocomplete="off" <?=$groupBuy['time_limit_type'] == 2 ? 'disabled' : '' ?>  name="packages[<?php echo $selcontent_index; ?>][group_buy][<?php echo $groupBuyIndex; ?>][time_limit]" type="text" value="<?php echo $groupBuy['time_limit']; ?>" style="width: 100px;">
                                                                <span>小时</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <span class="td-left">是否允许退款</span>
                                                                <select name="packages[<?php echo $selcontent_index; ?>][group_buy][<?php echo $groupBuyIndex; ?>][allow_refund]"  class="form-control" style="width: auto; height: 34px">
                                                                    <option value="1" <?=$groupBuy['allow_refund'] == 1 ? 'selected="selected"' : ''?> >是</option>
                                                                    <option value="0" <?=$groupBuy['allow_refund'] == 0 ? 'selected="selected"' : ''?> >否</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                            <tr>
                                                                <td>
                                                                    <span class="td-left">是否在首页显示</span>
                                                                    <select name="packages[<?php echo $selcontent_index; ?>][group_buy][<?php echo $groupBuyIndex; ?>][show_on_homepage]"  class="form-control" style="width: auto; height: 34px">
                                                                        <option value="1" <?=$groupBuy['show_on_homepage'] == 1 ? 'selected' : ''?>>是</option>
                                                                        <option value="0" <?=$groupBuy['show_on_homepage'] == 0 ? 'selected' : ''?>>否</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                    </table>
                                            <?php
                                                endforeach;
                                            }?>


                                    </td>
                                </tr>

                            <?php endforeach;?>

                            <tr id="tradd">
                                <td></td>
                                <td><input type="button" name="btnadd" value="增加" onclick="javascript:addSel()" /></td>
                            </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="padding:10px;">

                        <?php if(empty($datainfo['idactivity'])) {} else { } ?>
                            <input type="button" style="display: none" onclick="javascript:checkdata(0);" value="提交">

                            <input type="hidden" id="intflag" name="intflag" value="0" />
                            <?php  if(in_array($datainfo['intflag'],array(1,3)) && !empty($datainfo['idactivity'])){ ?>
                            <input type="button" onclick="javascript:checkdata(0);" value="保存">
                            <?php } if(in_array($datainfo['intflag'],array(2,3,6)) || empty($datainfo['idactivity'])){ ?>
                            <input type="button" onclick="javascript:checkdata(1);" value="提交审批">
                            <?php } 
                              if($cms->CheckPurview('contentmanage',"checkactivity")!=false){
                             if(in_array($datainfo['intflag'],array(1,2,3,6)) || empty($datainfo['idactivity'])){ ?>
                            <input type="button" onclick="javascript:checkdata(2);" value="直接发布">
                            <?php } if($datainfo['intflag']==1 && !empty($datainfo['idactivity'])) { ?>
                            <input type="button" onclick="javascript:checkdata(3);" value="审批不通过">
                            <?php }} if($datainfo['intflag']==6 || empty($datainfo['idactivity'])){ ?>
                            <input type="button" onclick="javascript:checkdata(6);" value="保存草稿">
                            <?php } ?>
                            <br/><br/>
                        </td>
                    </tr>
                </table>

            </form>
        </div>
        <div style="height: 30px"></div>
    </div>
</div>


<script>


    function return_function(name,value)
    { 
        var flag=true;
        var testValue = $("#selsignfrom option");
        testValue.each(function() {

            if(value==$(this).val())
            {
                flag=false;
            }

        });
        if(flag)
        {
            $("#selsignfrom").append("<option value='"+value+"'>"+name+"</option>");  //添加一项option
        }
    }

    //单行文本框检测正则
    $(".input").blur(function(){
        var tip = $(this).attr('tip');
        var type = $(this).attr('sign');
        var val = $(this).val();
        var is_null = $(this).attr('is_null');
        var name = $(this).attr('name');

        if(is_null == 0){
            if(val.length == 0){
                $("."+name+"_null").show();
            }else {
                $("."+name+"_null").hide();
            }
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


    function isbm() {
        if($("#chkbm").is(":checked"))
        {
            $("#bm").show();
        }
        else
        {
            $("#bm").hide();
        }
    }
    isbm();

    function  checkdata(flag) {
        if(eval($("#maxage").val()) < eval($("#minage").val())){
            alert("参与最大年龄不能小于最小年龄");
            return false;
        }

        if($("#chrtitle").val()=="")
        {
            alert("请输入活动标题");
            return false;
        }
        if($("#fidtype").val()=="")
        {
            alert("请选择分类");
            return false;
        }
        var  wntx_sync_status = <?php echo $datainfo['wntx_sync_status']; ?>;
        // 已上架和待审核和审核失败
        if((flag == 2 || flag == 1) && (wntx_sync_status  == 4 || wntx_sync_status  == 1 || wntx_sync_status  == 3)){
            if(!confirm("由于您修改了产品内容，同步到蜗牛童行的产品已下架，请确认产品内容定稿后再次提交同步请求。")){
                return false;
            }
            // alert();

        }

        if($("#chkbm").is(":checked")) {
            try {
                //如果是直接发布的话
                if(flag == 2 || flag == 1){
                    var price = /^(([1-9]\d{0,6})|0)([.]{1}[0-9]{1,2})?$/; //金额
                    var int =  /^[0-9]*$/;//整数
                    //如果选中可以使用现金券
                    if($('#chkvolume').is(':checked')){
                        //最大数量
                        if(!int.test($('input[name="max_use"]').val()) || $('input[name="max_use"]').val() == ''){
                            alert("请设置以整数为最大数量");
                            throw 'error';
                        }
                        //最大金额
                        if(!int.test($('input[name="max_amount"]').val())){
                            alert("请设置以整数为最大金额");
                            throw 'error';
                        }
                    }
                    //如果选中可以领取现金券
                    if($('#chkcash').is(':checked')){
                        //领券金额
                        if(!int.test($('input[name="activity_cashed_amount"]').val())){
                            alert("请设置以整数为领券金额");
                            throw 'error';
                        }
                        var int_not_zero = /^[1-9]\d{0,8}$/;
                        //有效期
                        if(!int_not_zero.test($('input[name="activity_cashed_validity"]').val()) || $('input[name="activity_cashed_validity"]').val() == ''){
                            alert("请设置以整数为有效期");
                            throw 'error';
                        }
                    }
                    //如果可以分享现金券
                    if($('#is_share_cashed').is(':checked')){
                        //设置现金券计划
                        if($('#cashed_plan_id').val() <= 0){
                            alert("请选择现金券计划");
                            throw 'error';
                        }
                    }
                }

                // if($("#intsignnum").val() == '')
                // {
                //     alert("请填写活动库存");
                //     throw 'error';
                // }


                if($("#selsignfrom").val() == 0)
                {
                    alert("请选择报名模板");
                    throw 'error';
                }



                $('.paytypecontent2').each(function () {
                    var id = $(this).attr('id');
                    var index = id === undefined ? 1 : id.replace('selcontent_', '') + 1;

                    //关键字非空
                    if($(this).find('.keyword1').eq(0).val() == '')
                    {
                        alert("套餐" + index + "的关键字1不能为空！");
                        throw 'error';
                    }

                    //会员价非空
                    if($(this).find('.member_price').eq(0).val() == '')
                    {
                        alert("套餐" + index + "的会员价不能为空！");
                        throw 'error';
                    }

                    //有效期非空
                    if($(this).find('.expire_at').eq(0).val() == '')
                    {
                        alert("套餐" + index + "的有效期不能为空！");
                        throw 'error';
                    }

                    //库存非空
                    if($(this).find('.package_sum').eq(0).val() == '')
                    {
                        alert("套餐" + index + "的库存不能为空！");
                        throw 'error';
                    }
                });
            } catch (e) {
                return false;
            }

        }

       // var i=0;
       //  if($("#chkbm").is(":checked")) {
       //      $(".keyword1").each(function () {
       //          i++;
       //          if ($(this).val() == "") {
       //              alert("套餐" + i + "的关键字1不能为空！");
       //              exit();
       //          }
       //      })
       //      i = 0;
       //      $("input[name='txtpayvalue4[]']").each(function () {
       //          i++;
       //          if ($(this).val() == "") {
       //              alert("套餐" + i + "的会员价不能为空！");
       //              exit();
       //          }
       //      })
       //      i = 0;
       //      $("input[name='txtpayvalue9[]']").each(function () {
       //          i++;
       //          if ($(this).val() == "") {
       //              alert("套餐" + i + "的有效期不能为空！");
       //              exit();
       //          }
       //      })
       //  }

        if(flag>0)
        {
            $("#intflag").val(flag);
        }

        $("#frm").submit();
    }

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
                var modelid = 35;
                var act = 1;
                var field = name;
                var content_id = 0;
                $.ajax({
                    data:"modelid="+modelid+"&value="+val+"&fieldname="+field+"&action="+act+"&contentid="+content_id,
                    url:"/index.php/Admin/node/contenttest.html",
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
    var index_sel=<?php echo $selcontent_index + 2; ?>;
    function addSel(){
        // console.log(index_sel);
        var tr = $('<tr>').addClass('paytypecontent2');
        var tdLeft = $('<td>').addClass('oa_cell-left').html('套餐' + index_sel).appendTo(tr);
        var tdRight = $('<td>').addClass('oa_cell-right').attr('id', 'selcontent_' + index_sel).appendTo(tr);
        var input = $("#selcontent_1>input").clone();
        input.each(function () {
            $(this).val('').attr('name', $(this).attr('name').replace('[0]', '[' + index_sel + ']')).css({'margin-right': '3px'});
        });

        // $('<input>').attr('type', 'button').attr('onclick', 'delpaytype(' + index_sel + ');').val('删除');
        tdRight.append(input);
        //库存默认为0
        tdRight.children('.package_sum').eq(0).val(0);
        // 删除按钮绑定特定参数和方法
        tdRight.children('input[type=button][name=del]').val('删除').attr('onclick', 'delpaytype(' + index_sel + ');');
        // 拼团按钮绑定特定参数和方法
        // 不要问index_sel怎么算的，问就自杀
        tdRight.children('input[type=button][name=addAssemble]').val('拼团').attr('index', index_sel);
        //新增套餐删除套餐id字段
        tdRight.children('.package_id').remove();

        $("#tradd").before(tr);

        //为有效期输入框添加选择日期事件
        selectTime1(tdRight.children('.expire_at').eq(0), "YYYY-MM-DD");
        index_sel++;
    }



    function delpaytype(index){
        var obj = $('#selcontent_'+index+'');
        var packageId = obj.find('.package_id');
        if(packageId.length === 1)
        {
            $.ajax({
                url: "<?php echo url('activity/deletePackage'); ?>",
                data: {
                    package_id: packageId.eq(0).val(),
                    activity_id: "<?php echo $datainfo['idactivity']; ?>"
                },
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    if(result.status === 'success')
                    {
                        obj.parent().remove();
                        alert(result.msg);
                    }else
                    {
                        alert(result.msg);
                    }
                },
                error: function () {
                    alert('删除失败');
                }
            });
        }else if($('.paytypecontent2').length > 2)
        {
            obj.parent().remove();
        }
    }
    //是否可以使用现金券
    $(function () {

        // 停用启用
        $(document).on('click', '.choose-label', function () {
            var checkbox = $(this).siblings('input[type=checkbox]').eq(0);
            
            var groupBuyId = $(this).parents("table .ass-table").find('.group-buy-id');
            if(groupBuyId.length == 1 && groupBuyId.val() != 0)
            {
                $.ajax({
                    data: {
                        'group_buy_id': groupBuyId.val(),
                        'state': !(checkbox.prop('checked'))
                    },
                    url: "<?php echo url('group_buy/switchState'); ?>",
                    async: false,
                    success:function(res)
                    {
                        if(res.status === 'success')
                        {
                            checkbox.prop('checked', !(checkbox.prop('checked')));
                            alert(res.msg);
                        }else {
                            alert(res.msg);
                        }
                    }
                });
            }else
            {
                checkbox.prop('checked', !(checkbox.prop('checked')));
            }
        });

        $(".select-time").each(function () {
            selectTime($(this), "YYYY-MM-DD HH:mm:ss");
        });

        $(".expire_at").each(function () {
            selectTime1($(this), "YYYY-MM-DD");
        })

        // 添加拼团
        $(document).on("click", "input[name=addAssemble]",function () {
            var package_index = $(this).attr('index');
            var group_buy_index = 0;
            if($(this).siblings('table').length > 0)
            {
                group_buy_index = Number($(this).siblings('table').last().attr('index')) + 1;
            }
            var assContent = $("#ass-table").clone().css('display', 'block').attr('index', group_buy_index).removeAttr('id');

            assContent.find('.form-control').each(function () {
                var name = $(this).attr('name').replace('[package_index]', '[' + package_index + ']').replace('[group_buy_index]', '[' + group_buy_index + ']')
                $(this).attr('name', name);
                if($(this).hasClass('select-time'))
                {
                    selectTime($(this), "YYYY-MM-DD HH:mm:ss");
                }
            });

            $(this).parent("td").append(assContent);
        });

        // 拼团时间限制类型
        $(document).on('change', '.time-limit-type', function () {
            if($(this).val() == 1)
            {
                $(this).parents('tr').siblings('.time-limit').eq(0).find('input').removeAttr('disabled');
            }else
            {
                $(this).parents('tr').siblings('.time-limit').eq(0).find('input').attr('disabled', 'disabled');
            }
        });

        // 删除拼团
        $(document).on("click",".delete-group-buy",function () {
            var groupBuyId = $(this).parents("table .ass-table").find('.group-buy-id');
            var belongTable = $(this).parents(".ass-table");
            if(groupBuyId.length == 1 && groupBuyId.val() != 0)
            {
                $.ajax({
                    data: {
                        'group_buy_id': groupBuyId.val()
                    },
                    url: "<?php echo url('group_buy/deleteGroupBuy'); ?>",
                    async: false,
                    success:function(res)
                    {
                        if(res.status === 'success')
                        {
                            belongTable.remove();
                            alert(res.msg);
                        }else {
                            alert(res.msg);
                        }
                    }
                });

            }else
            {
                belongTable.remove();
            }
        });

    });


    function show_chkpay() {
        if($('#chkvolume').is(":checked"))
        {
            $('#set_volume').show();
            $('#is_can_receive').show();
        }
        else
        {
            $('input[name="is_receive_cashed"]').prop('checked',false);
            $('#set_volume').hide();
            $('#is_can_receive').hide();
            $('#set_cash').hide();

        }
    }
    //是否可以领取现金券
    function show_cash() {
        if($('#chkcash').is(":checked"))
        {
            $('#set_cash').show();
        }
        else
        {
            $('#set_cash').hide();
        }
    }
    //是否可以分享现金券
    function show_is_share_cashed() {
        if($('#is_share_cashed').is(":checked"))
        {
            $('#set_share_cashed').show();
        }
        else
        {
            $('#set_share_cashed').hide();
        }
    }


    function previous() {
        var content = UE.getEditor('chrcontent').getContent();
        layer.open({
            title:"内容预览",
            type: 1,
            skin: 'layui-layer-rim', //加上边框
            area: ['375px', '600px'], //宽高
            content: content
        });
    }




    $('#ischarge').change(function () {
        if($(this).val() == 1)
        {
            // $('#isrefund').val(0);
            $('#allow-refund').hide();
        }else
        {
            $('#allow-refund').show();
        }
    });


</script>

<table class="ass-table" id="ass-table" style="margin-left:20px; display: none; ">
    <tr>
        <td style="width: 766px">
            <span class="td-left">拼团方案</span>
            <input type="checkbox" value="true" name="packages[package_index][group_buy][group_buy_index][state]" class="chooseBtn form-control">
            <label  class="choose-label" style="margin: 0; vertical-align: middle;"></label>
            <input class="delete-group-buy" type="button" value="删除" style="margin: 0; float: right; padding-left: 6px;padding-right:6px;">
        </td>
    </tr>
    <tr>
        <td>
            <span class="td-left">成团商品数量</span>
            <input class="td-right form-control" type="text" name="packages[package_index][group_buy][group_buy_index][group_num]" value="" style="width: 100px;">
            <span>必须大于等于2</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="td-left">拼团活动开始时间</span>
            <div class="oa_cell-right" style="display: inline-block; vertical-align: middle;">
                <div style="width:250px" class="input-prepend input-group">
                    <span class="add-on input-group-addon">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar">
                        </i>
                    </span>
                    <input type="text" is_null="0" autocomplete="off" name="packages[package_index][group_buy][group_buy_index][start_at]" class="form-control select-time" value="">
                </div>
                <span style="color: red;display: none;" class="dtbirthday_null">(不能为空)
                </span>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <span class="td-left">拼团活动结束时间</span>
            <div class="oa_cell-right" style="display: inline-block; vertical-align: middle;">
                <div style="width:250px" class="input-prepend input-group">
                    <span class="add-on input-group-addon">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar">
                        </i>
                    </span>
                    <input type="text" autocomplete="off" name="packages[package_index][group_buy][group_buy_index][end_at]" class="form-control select-time" value="">
                </div>
                <span style="color: red;display: none;" class="dtbirthday_null">(不能为空)
                </span>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <span class="td-left">团购价格</span>
            <input class="td-right form-control" type="text" name="packages[package_index][group_buy][group_buy_index][group_buy_price]" value="" style="width: 100px;">
            <span>元</span>
        </td>
    </tr>
    <tr class="hidden">
        <td>
            <span class="td-left">是否允许优惠券</span>
            <select name="packages[package_index][group_buy][group_buy_index][allow_coupon]"  class="form-control" disabled="disabled" style="width: auto; height: 34px">
                <option value="1" >是</option>
                <option value="0" >否</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <span class="td-left">时间限制类型</span>
            <select name="packages[package_index][group_buy][group_buy_index][time_limit_type]" class="form-control time-limit-type" style="width: auto; height: 34px">
                <option value="1" >开团固定时间后拼团结束</option>
                <option value="2" >拼团活动结束时间到，所有拼团活动立刻停止</option>
            </select>
        </td>
    </tr>
    <tr class="time-limit">
        <td>
            <span class="td-left">组团时间限制</span>
            <input class="td-right form-control" autocomplete="off" name="packages[package_index][group_buy][group_buy_index][time_limit]" type="text" value="" style="width: 100px;">
            <span>小时</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="td-left">是否允许退款</span>
            <select name="packages[package_index][group_buy][group_buy_index][allow_refund]"  class="form-control" style="width: auto; height: 34px">
                <option value="1" >是</option>
                <option value="0" selected>否</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <span class="td-left">是否在首页显示</span>
            <select name="packages[package_index][group_buy][group_buy_index][show_on_homepage]"  class="form-control" style="width: auto; height: 34px">
                <option value="1" >是</option>
                <option value="0" selected>否</option>
            </select>
        </td>
    </tr>
</table>

</body>
</html>