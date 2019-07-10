<?php

namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Verify;
class Qrcode  extends Controller{
    /*
     * 站点二维码
     */
    public function siteurl()
    {
        ob_end_clean();
        include_once('../thinkphp/library/think/Phpqrcode/phpqrcode.php');
        $code= I("code");
        $url="http://".$_SERVER['HTTP_HOST']."/".$code;
        $size=6;
        Vendor('Phpqrcode.phpqrcode');
        $obj=new \QRcode();
        $obj::png($url,false,QR_ECLEVEL_L,$size,2,false,0xFFFFFF,0x000000);

        exit();
    }
    /*
     * 活动二维码
     */
    public function activityurl()
    {
        $sitecode= I("sitecode");
        $id= I("id");
        ob_end_clean();
        include_once('../thinkphp/library/think/Phpqrcode/phpqrcode.php');
        $code= I("code");
        $url=ROOTURL."/".$sitecode."/detail/".$id."?type=test";
        //$url="http://".$_SERVER['HTTP_HOST']."/".$sitecode."/detail/".$id."?type=test";
        $size=6;
        Vendor('Phpqrcode.phpqrcode');
        $obj=new \QRcode();
        $obj::png($url,false,QR_ECLEVEL_L,$size,2,false,0xFFFFFF,0x000000);
        exit();
    }
    /*
     * 内容二维码
     */
    public function contenturl()
    {
        $sitecode= I("sitecode");
        $id= I("id");
        ob_end_clean();
        include_once('../thinkphp/library/think/Phpqrcode/phpqrcode.php');
        $code= I("code");
        $url="http://".$_SERVER['HTTP_HOST']."/".$sitecode."/content/".$id;
        $size=6;
        Vendor('Phpqrcode.phpqrcode');
        $obj=new \QRcode();
        $obj::png($url,false,QR_ECLEVEL_L,$size,2,false,0xFFFFFF,0x000000);
        exit();
    }
    /*
     * 签到二维码
     */
    public function signin()
    {
        $sitecode= I("code");
        $id= I("id");
        ob_end_clean();
        include_once('../thinkphp/library/think/Phpqrcode/phpqrcode.php');
        $url=config('ROOTURL')."/".$sitecode."/signin/".$id;
        $size=6;
        Vendor('Phpqrcode.phpqrcode');
        $obj=new \QRcode();
        $obj::png($url,false,QR_ECLEVEL_L,$size,2,false,0xFFFFFF,0x000000);
        exit();
    }

    /*
     * 管理员分享现金券二维码
     */
    public function share_cashed()
    {
        $sitecode= I("sitecode");
        $id= I("id");
        ob_end_clean();
        include_once('../thinkphp/library/think/Phpqrcode/phpqrcode.php');
        //分享链接
        $url = ROOTURL."/".$sitecode."/share/0/".$id;
        $size=6;
        Vendor('Phpqrcode.phpqrcode');
        $obj=new \QRcode();
        $obj::png($url,false,QR_ECLEVEL_L,$size,2,false,0xFFFFFF,0x000000);
        exit();
    }
}

