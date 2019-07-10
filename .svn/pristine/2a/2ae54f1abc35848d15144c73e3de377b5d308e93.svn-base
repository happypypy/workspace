<?php


namespace app\admin\module;

use think\Exception;
use think\Model;
use think\Page;
use think\Db;
use think\Log;
use think\Request;

class Sms extends Model{

    public function uploadExcel($siteId)
    {
        try
        {
            //判断是否有上传文件
            if(empty($_FILES) || !isset($_FILES['mobile-sheet']) || $_FILES['mobile-sheet']['error'] > 0)
            {
                throw new Exception('请先上传文件', 1);
            }
            // 判断上传文件扩展名
            if(!in_array(pathinfo($_FILES['mobile-sheet']['name'], PATHINFO_EXTENSION), ['xls', 'xlsx']))
            {
                throw new Exception('只能上传excel文件', 2);
            }

            //根据日期和站点id建立上传文件的的文件夹
            $dir = ROOT_PATH . DS . 'uploads' . DS . date('Ymd');
            if(!is_dir($dir))
            {
                mkdir($dir);
            }
            $dir = $dir . DS . $siteId;
            if(!is_dir($dir))
            {
                mkdir($dir);
            }

            //根据当前时间戳产生文件名
            $filename = $dir . DS . time() . '_' . $_FILES['mobile-sheet']['name'];
            if(!move_uploaded_file($_FILES['mobile-sheet']['tmp_name'], $filename))
            {
                throw new Exception('文件上传失败', 3);
            }

            return ['status' => 'success', 'filename' => $filename];
        } catch (Exception $e)
        {
            Log::error(date('Y-m-d H:i:s') . 'excel文件上传失败，$_FILES = ' . print_r($_FILES, true));
            return ['status' => 'fail', 'msg' => $e->getMessage()];
        }
    }


    /**
     * 读取上传的excel文件并根据文件中数据生成短信发送计划
     * @author Hlt
     * @DateTime 2019-05-05T10:04:19+0800
     * @return   array                   ['status', 'msg']
     */
    public function sendTextMsgWithExcel()
    {
        try
        {
            $params = Request::instance()->param();
            //读取excel文件
            require_once ROOT_PATH .'extend' . DS .'PHPExcel' . DS .'PHPExcel' . DS .'IOFactory.php';
            // $objPHPExcel = \PHPExcel_IOFactory::load($uploadResult['filename']);
            $objPHPExcel = \PHPExcel_IOFactory::load($_FILES['mobile-sheet']['tmp_name']);
            $excelData = $objPHPExcel->getsheet(0)->toArray();
            $variableName = array_shift($excelData);
            if($variableName[0] !== '手机号')
            {
                throw new Exception('excel文件格式错误，第一列必须以“手机号”为标题', 1);
            }
            unset($variableName[0]);

            $sendNum = 0;
            $ip = getip();
            $time= date('Y-m-d H:i:s');
            $sendTime = isset($params['send_time']) ? $params['send_time'] : $time;
            $length = count($variableName);
            $data = [];
            $mode = [
                "idsite" => session('idsite'),
                "idaccount" => session('AccountID'),
                'username' => session('UserName'),
                "type" => 1,
                "create_time" => $time,
                "send_time" => $sendTime,
                "ip" => $ip,
            ];


            $msgConfig = config('msg_config');
            foreach ($excelData as $line => $singleData)
            {
                $mode['mobile'] = $singleData[0];
                unset($singleData[0]);
                if(!checkMobile($mode['mobile']))
                {
                    throw new Exception('不是合法的手机号，错误行：' . ($line + 2), 1);
                }

                //处理excel数据
                $currentDataLength = count($singleData);
                if($length >= $currentDataLength)       //列长大于等于数据长
                {
                    $search = array_slice($variableName, 0, $currentDataLength);
                    $replace = $singleData;
                }elseif($currentDataLength > $length)   //列长小于数据长
                {
                    $replace = array_slice($singleData, 0, $length);
                    $search = $variableName;
                }
                //替换变量
                $mode['content'] = str_replace($search, $replace, $params['content']);
                if(isBadWord($mode['content']))
                {
                    throw new Exception('含有违禁词，错误行：' . ($line + 2), 1);
                }


                if(mb_strlen($mode['content']) > $msgConfig['max_text_len'])
                {
                    throw new Exception('短信内容超过' . $msgConfig['max_text_len'] . '，错误行：' . ($line + 2), 1);
                }

                //计算发送短信总个数
                $sendNum += ceil(mb_strlen($mode['content']) / $msgConfig['text_len']);
                $data[] = $mode;
            }

            return smsSchedule(session('idsite'), $sendNum, $data);
        } catch (Exception $e)
        {
            // throw $e;
            Log::warning(date('Y-m-d H:i:s') . ' ' . $e->getMessage());
            return ['status' => 'fail', 'msg' => $e->getMessage()];
        }
        
    }
}