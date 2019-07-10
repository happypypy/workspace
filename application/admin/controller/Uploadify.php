<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: 当燃      
 * Date: 2015-09-22
 */
 
namespace app\admin\controller;
use think\Request;

class Uploadify extends Base{
   
    public function upload(){

        $func = Request::instance()->param('func');
        $path = Request::instance()->param('path');

        $info = array(
        	'num'=> Request::instance()->param('num'),
            'title' => '',       	
        	'upload' =>url('Admin/Ueditor/imageUp',array('savepath'=>$path,'pictitle'=>'banner','dir'=>'images')),
            'size' => '4M',
            'type' =>Request::instance()->param('type'),
            'input' => Request::instance()->param('input'),
            'func' => empty($func) ? 'undefined' : $func,
        );

        $this->assign('info',$info);

        return $this->fetch();
    }


    public function selimg(){

        $func = Request::instance()->param('func');
        $path = Request::instance()->param('path');

        $info = array(
            'num'=> Request::instance()->param('num'),
            'title' => '',
            'upload' =>url('Admin/Ueditor/imageUp',array('savepath'=>$path,'pictitle'=>'banner','dir'=>'images')),
            'size' => '4M',
            'type' =>Request::instance()->param('type'),
            'input' => Request::instance()->param('input'),
            'func' => empty($func) ? 'undefined' : $func,
        );

        $this->assign('info',$info);

        return $this->fetch();
    }

    /*
              删除上传的图片
     */
    public function delupload(){
        $action=isset($_GET['action']) ? $_GET['action'] : null;
        $filename= isset($_GET['filename']) ? $_GET['filename'] : null;
        $filename= str_replace('../','',$filename);
        $filename= trim($filename,'.');
        $filename= trim($filename,'/');
        if($action=='del' && !empty($filename)){
            $size = getimagesize($filename);
            $filetype = explode('/',$size['mime']);
            if($filetype[0]!='image'){
                return false;
                exit;
            }
            unlink($filename);
            exit;
        }
    }

    public function uploadimgcut1(){

        $path = Request::instance()->param('path');
        $imgType=Request::instance()->param('type');
        $imgType=trim(trim($imgType),';');
        $imgType=str_replace('*.','',$imgType);
        $imgType=json_encode(explode(';',$imgType));


        $filepath='';
        if (Request::instance()->isPost()) {
            $filekey="inputFile";
            $tmp_file_paht= $_FILES[$filekey]["tmp_name"];
            $newfilename =getNumber(). strrchr($_FILES[$filekey]["name"],'.');

            $filepath='public/uploads/'.session('idsite').'/'.$path.'/'.date('Y').'/'.date('m-d').'/';


            if(!is_dir($filepath)){
                mkdir($filepath, 0777,true);
            }

            $cropData = Request::instance()->param('cropData');
            $filepath=$filepath.$newfilename;
            if(empty($cropData))
            {
                move_uploaded_file($tmp_file_paht, $filepath);
            }
            else
            {
                $obj=json_decode($cropData);
                $this->image_cut($tmp_file_paht,$filepath,$obj->x,$obj->y,$obj->w,$obj->h);
            }

            $filepath="/".$filepath;
        }

        $_w=Request::instance()->param('w');
        $_h=Request::instance()->param('h');

        if(empty($_w) || $_w<1 || empty($_h) || $_h<1)
        {
            $_w=1;
            $_h=1;
        }

        $info = array(
            'title' => '',
            'upload' =>url('Admin/Ueditor/imageUp',array('savepath'=>$path,'pictitle'=>'banner','dir'=>'images')),
            'size' => '4M',
            'type' =>$imgType,
            'input' => Request::instance()->param('input'),
            'func' => empty($func) ? 'undefined' : $func,
            'filepath'=>$filepath,
            'w'=>$_w,
            'h'=>$_h,
        );

        $this->assign('info',$info);
        return $this->fetch();
    }

    /**
     * 居中裁剪图片
     * @param string $source [原图路径]
     * @param string $target [目标路径]
     * @param int $start_x [裁剪开到始X坐标]
     * @param int $start_y [裁剪开到始Y坐标]
     * @param int $img_w [裁剪宽度]
     * @param int $img_h [裁剪高度]
     * @param int $width [裁剪结果设置宽度]
     * @param int $height [裁剪结果设置高度]
     * @return bool [裁剪结果]
     */
    function image_cut($source,$target, $start_x,$start_y,$img_w,$img_h, $width=0, $height=0)
    {
        if (!file_exists($source)) return false;
        /* 根据类型载入图像 */
        switch (exif_imagetype($source)) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($source);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($source);
                break;
        }
        if (!isset($image)) return false;
        /* 获取图像尺寸信息 */
        $source_w = imagesx($image);
        $source_h = imagesy($image);

        /* 裁剪图像 */
        $target_img = imagecreatetruecolor($img_w, $img_h);
        imagecopy($target_img, $image, 0, 0, $start_x, $start_y, $img_w, $img_h);

        /* 将图片保存至文件 */
        if (!file_exists(dirname($target))) mkdir(dirname($target), 0777, true);
        switch (exif_imagetype($source)) {
            case IMAGETYPE_JPEG:
                imagejpeg($target_img, $target);
                break;
            case IMAGETYPE_PNG:
                imagepng($target_img, $target);
                break;
            case IMAGETYPE_GIF:
                imagegif($target_img, $target);
                break;
        }
        return boolval(file_exists($target));
    }



    public function uploadimgcut(){

        $path = Request::instance()->param('path');
        $imgType=Request::instance()->param('type');
        $imgType=trim(trim($imgType),';');
        $imgType=str_replace('*.','',$imgType);
        $imgType=json_encode(explode(';',$imgType));


        $filepath_small = $filepath_big = $filepath1='';
        if (Request::instance()->isPost()) {
            $filekey="inputFile";
            $tmp_file_paht= $_FILES[$filekey]["tmp_name"];
            $tmp_num = getNumber();
            $newfilename =$tmp_num. strrchr($_FILES[$filekey]["name"],'.');
            $newfilename_small =$tmp_num."_small". strrchr($_FILES[$filekey]["name"],'.');
            $newfilename_big =$tmp_num."_big". strrchr($_FILES[$filekey]["name"],'.');

            $filepath='public/uploads/'.session('idsite').'/'.$path.'/'.date('Y').'/'.date('m-d').'/';


            if(!is_dir($filepath)){
                mkdir($filepath, 0777,true);
            }

            $cropData = Request::instance()->param('cropData');
            $filepath1=$filepath.$newfilename;
            $filepath_small=$filepath.$newfilename_small;
            $filepath_big=$filepath.$newfilename_big;
            if(empty($cropData))
            {
                move_uploaded_file($tmp_file_paht, $filepath);
            }
            else
            {
                $obj=json_decode($cropData);
                $this->image_cut($tmp_file_paht,$filepath1,$obj->x,$obj->y,$obj->w,$obj->h);
                //小图
                $this ->resize_image($filepath1,$filepath_small,200,(200*$obj->h/$obj->w));
                //大图
                $this ->resize_image($filepath1,$filepath_big,800,(800*$obj->h/$obj->w));
            }

            $filepath1 = "/".$filepath1;
            $filepath_big = "/".$filepath_big;
            $filepath_small = "/".$filepath_small;
        }

        $_w=Request::instance()->param('w');
        $_h=Request::instance()->param('h');

        if(empty($_w) || $_w<1 || empty($_h) || $_h<1)
        {
            $_w=1;
            $_h=1;
        }
        $info = array(
            'title' => '',
            'upload' =>url('Admin/Ueditor/imageUp',array('savepath'=>$path,'pictitle'=>'banner','dir'=>'images')),
            'size' => '4M',
            'type' =>$imgType,
            'input' => Request::instance()->param('input'),
            'func' => empty($func) ? 'undefined' : $func,
            'filepath'=>$filepath1,
            'filepath_big'=>$filepath_big,
            'filepath_small' => $filepath_small,
            'w'=>$_w,
            'h'=>$_h,
        );

        $this->assign('info',$info);
        return $this->fetch();
    }

    /**
     * 重定义图片大小
     * @param $source
     * @param $target
     * @param $img_w
     * @param $img_h
     * @return bool
     */
    private function resize_image($source,$target, $img_w,$img_h)
    {
        if (!file_exists($source)) return false;
        /* 根据类型载入图像 */
        switch (exif_imagetype($source)) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($source);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($source);
                break;
        }
        if (!isset($image)) return false;
        /* 获取图像尺寸信息 */
        $source_w = imagesx($image);
        $source_h = imagesy($image);

        /* 裁剪图像 */
        $target_img = imagecreatetruecolor($img_w, $img_h);

        imagecopyresampled($target_img, $image, 0, 0, 0, 0, $img_w, $img_h, $source_w, $source_h);

        /* 将图片保存至文件 */
        if (!file_exists(dirname($target))) mkdir(dirname($target), 0777, true);
        switch (exif_imagetype($source)) {
            case IMAGETYPE_JPEG:
                imagejpeg($target_img, $target,100);
                break;
            case IMAGETYPE_PNG:
                imagepng($target_img, $target,9);
                break;
            case IMAGETYPE_GIF:
                imagegif($target_img, $target);
                break;
        }
        return boolval(file_exists($target));
    }

}