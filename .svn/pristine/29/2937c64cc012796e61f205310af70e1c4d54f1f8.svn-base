<?php

namespace app\admin\module;
use think\Model;
use think\Page;
use think\Exception;

class Common extends Model {
	/**
	 * 解析订单中报名信息
	 * @author Hlt
	 * @DateTime 2019-04-16T18:12:02+0800
	 * @param    string                   $field 键名项
	 * @param    string                   $data  数据线
	 * @return   array                           对应二维数组
	 */
	function getMemberInfo($field, $data)
	{
	    $userData = $this->getOrderTable($field, $data);
	    $username = '';
	    $mobile = '';
	    foreach ($userData as $value)
	    {
            //类型1  用户名
	        if($value[0] == 9 && empty($username))
	        {
	            $username = $value[2];
	        }
            //类型10  手机号
	        if($value[0] == 10)
	        {
	            $mobile = $value[2];
	            break;
	        }
	    }

	    return ['username' => $username, 'mobile' => $mobile];
	}


	/**
     * 解析订单表中的用户输入数据
     * @author Hlt
     * @DateTime 2019-04-16T11:18:40+0800
     * @param    string                   $field 键名项
     * @param    string                   $value 数据项
     * @return   array                           [[$tmp_type,$tmp_field_name,$tmp_value],[],[],...]
     *                                           项目类别 1文本框(第一个文本框为姓名) 2数字框 3电话框 4邮件框 5下拉框 6多行文本框 7图片上传 8身份证 9 关联姓名框 10关联电话号码框 11关联邮箱框 12关联邮寄地址框 13关联身份证框
     */
    public function getOrderTable($field,$value)
    {
        $data=[];
        if(empty($field) || empty($value))
            return $data;

        $arr_field = explode("☆", $field);
        $arr_value = explode("☆", $value);

        $value_length=count($arr_value);
        foreach ($arr_field as $k=>$vo)
        {
            $tmp_value="";
            $tmp_field_name="";
            $tmp_type="";

            if( $k<$value_length)
            {
                $tmp_value=$arr_value[$k];
            }

            $pos = mb_strpos($vo, "∫");
            $pos = $pos === false ? count($vo) : $pos;

            $tmp_field_name = mb_substr($vo, 0, $pos);
            $tmp_type = mb_substr($vo, $pos + 1);

            $data[]=[$tmp_type,$tmp_field_name,$tmp_value];
        }

        return $data;
    }


    /**
     * 下载文件
     * @author Hlt
     * @DateTime 2019-05-05T14:06:57+0800
     * @param    string                   $file 文件路径
     * @return   binary                         下载文件|报错
     */
    public function downloadFile($file, $filename)
    {
        if(!is_file($file))
        {
            throw new Exception('文件不存在');
        }
        date_default_timezone_set('Asia/Shanghai');
        fopen ( $file, "r" );
        header("Content-type: text/html;charset=utf-8");
        header("Content-type:application/octet-stream");
        header("Content-Disposition:attachment;filename = ".$filename);
        header("Accept-ranges:bytes");
        header("Accept-length:".filesize($file));
        ob_clean();
        flush();
        readfile($file, filesize($file));
        fclose($file);
        exit();
    }
}