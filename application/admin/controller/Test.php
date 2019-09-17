<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/4
 * Time: 17:11
 */

namespace app\admin\controller;

use think\Db;
use think\db\Query;
use think\wx\Utils\HttpCurl;

class Test extends Base
{
    public function aaa()
    {
        echo "ffff";
        $abc=[];
        $abc[0]=[];
        $abc[0]['qqq']='qqq';
        dump($abc);

    }

    public function ver()
    {
        echo THINK_VERSION;

    }

    public function group()
    {


        $data = db('group_buy_order')->select();

        foreach ($data as $key => $value)
        {

            $pachage_name='';
            $userid=0;
            $username='';
            $buy_num=0;

            $data_p= db('package')->where(['package_id'=>$value['package_id']])->find();
            if(!empty($data_p))
            {
                $pachage_name=$data_p['keyword1'];
            }

            $data_o= db('order')->where(['group_buy_order_id' => $value['group_buy_order_id'],'state' => ['in', [4,5,6,7,8,12]]])->order('id asc')->find();
            if(!empty($data_o))
            {
                $userid=$data_o['fiduser'];
                $username=$data_o['chrusername'];
            }
            $data_c= db('order')->where(['group_buy_order_id' => $value['group_buy_order_id'],'state' => ['in', [4,5,6,7,8]]])->count();
            if(!empty($data_c))
            {
                $buy_num=$data_c;
            }

            db('group_buy_order')->where(['group_buy_order_id'=>$value['group_buy_order_id']])->update(['pachage_name'=>$pachage_name,'userid'=>$userid,'username'=>$username,'buy_num'=>$buy_num]);


        }
        echo "OK";
        exit();

    }

    public function testSmsSchedule()
    {
        $data = [
            [
                'idsite' => '8',
                'idaccount' => '0',
                'username' => '系统消息',
                'type' => '8',
                'create_time' => '2019-04-24 09:56:16',
                'send_time' => '2019-04-24 09:56:16',
                'ip' => '218.17.137.41',
                'ssid' => 'F80D755052FEB6BB85516CD08B24C9DE',
                'mobile' => '17722637391',
                'content' => '童享云体验，您好，您报名的免费2已审核通过，请提前安排好出行计划，准时参加。更多信息请到公众号的【会员中心】-【我的报名】中查看。',
            ],

            [
                'idsite' => '8',
                'idaccount' => '0',
                'username' => '系统消息',
                'type' => '8',
                'create_time' => '2019-04-24 09:56:16',
                'send_time' => '2019-04-24 09:56:16',
                'ip' => '218.17.137.41',
                'ssid' => 'F80D755052FEB6BB85516CD08B24C9DE',
                'mobile' => '18271678257',
                'content' => '童享云体验您好，报名的免费2已审核通过，请安排跟进。',
            ],
        ];

        $siteId = 8;
        $sendNum = 3;

        $res = smsSchedule($siteId, $sendNum, $data);
        print_r($res);
    }

//    public function testSendMsg()
//    {
//
//        $sitecode = 'tongxiang2';
//
//        $model = new \app\home\model\GroupBuyOrder;
//        $res = $model->sendGroupBuySuccessWechatMsg(33, 8);
//
//
//
//        var_dump($res);
//
//    }

    public function test()
    {

    }

    public function testGetSmsStatus()
    {
        $config = config('msg_config');
        $data = [
            'cid' => $config['data']['cid'], //客户端ID
            'pwd' => $config['data']['pwd'], //客户端密码
            // 'ssid' => 'BBBC6351FE882DD667C27BA115EEAB6F',
        ];

        // $url = $config['getreport_url'];
        $url = 'http://58.68.247.137:9053/communication/fetchReports.ashx';
        // $url = 'http://58.68.247.137:9053/communication/fetchDelivers.ashx';
        $url .= '?' . http_build_query($data);
        var_dump($url);die;
        $result = HttpCurl::get($url);
        file_put_contents('rs.log', $result);
        print_r($result);
    }

    public function testFreeOrderText()
    {
        $order = db('order')->where(['ordersn' => '20190424174831983126'])->find();
        $res = sysSendMsg(8, 7, $order, []);
    }

    public function log()
    {
        $sql = 'select * from mysql.general_log order by event_time desc limit 100';

        $data = Db::query($sql);
        var_dump($data);
        die;
    }

    public function dpPull($num)
    {
        $cash = [1, 4, 7, 13];
        $arr = [[0, 0]];
        for ($i = 1; $i <= $num; $i++) {
            $count = $num;
            $curCash = 0;
            foreach ($cash as $v) {
                if ($i >= $v && ($arr[$i - $v][0] + 1) < $count) {
                    $count = $arr[$i - $v][0] + 1;
                    $curCash = $v;
                }
            }
            $arr[$i] = [$count, $curCash];
            if (count($arr) > 13) {
                unset($arr[$i - 13]);
            }

        }
        var_dump($arr[$num]);
    }

    public function dpPush($num)
    {
        $cash = [1, 4, 7, 13];
        $arr = [[0, 0]];

        for ($i = 0; $i <= $num; $i++) {
            if (isset($arr[$i])) {
                foreach ($cash as $v) {
                    if ($i + $v <= $num && (!isset($arr[$i + $v]) || $arr[$i][0] + 1 < $arr[$i + $v][0])) {
                        // if(is_array($arr[$i][0])){var_dump($arr, $i);die;}
                        $arr[$i + $v] = [$arr[$i][0] + 1, $v];
                    }
                }
            } else {
                $count = $num;
                $curCash = 0;
                foreach ($cash as $v) {
                    if ($i >= $v && ($arr[$i - $v][0] + 1) < $count) {
                        $count = $arr[$i - $v][0] + 1;
                        $curCash = $v;
                    }
                }
                $arr[$i] = [$count, $curCash];
            }
            if (count($arr) > 13) {
                unset($arr[$i - 13]);
            }

        }

        ksort($arr);
        var_dump($arr[$num]);

    }

    public function format_bytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 5; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . $delimiter . " " . $units[$i];
    }

    public function run($num, $action)
    {
        $start = microtime(true);
        echo "内存初始状态：" . $this->format_bytes(memory_get_usage());
        echo "<hr/>";

        echo $this->$action($num);

        echo '<hr>';
        $end = microtime(true);
        $time = $end - $start;
        echo '消耗时间:' . $time;
        echo '<hr>';
        echo "最终内存状态：" . $this->format_bytes(memory_get_usage());
        echo "<hr/>";
        echo "内存峰值状态：" . $this->format_bytes(memory_get_peak_usage());
    }

    public function lis()
    {
        $list = [1, 5, 2, 3, 6, 9, 2, 3, 5, 7, 8];

        $result = [];
        foreach ($list as $k => $v) {
            $result[$k] = 1;
            for ($i = 0; $i < $k; $i++) {
                if ($list[$i] <= $v) {
                    $result[$k] = max($result[$k], $result[$i] + 1);
                }
            }
        }

        var_dump($result);
    }

    public function cutDp($size)
    {
        $price = [
            0 => 0,
            1 => 1,
            2 => 5,
            3 => 8,
            4 => 9,
            5 => 10,
            6 => 17,
            7 => 20,
            8 => 24,
            9 => 30,
        ];

        $idealPrice = $price;
        for ($i = 1; $i <= $size; $i++) {
            for ($j = 1; $j <= $i && $j < count($price); $j++) {
                $idealPrice[$i] = max($idealPrice[$i], $idealPrice[$i - $j] + $idealPrice[$j]);
            }
        }
        var_dump($idealPrice);
        return $idealPrice[$size];
    }

    public function cutMemo($size, &$idealPrice = [])
    {
        $price = [
            0 => 0,
            1 => 1,
            2 => 5,
            3 => 8,
            4 => 9,
            5 => 10,
            6 => 17,
            7 => 20,
            8 => 24,
            9 => 30,
        ];

        if (isset($idealPrice[$size])) {
            return $idealPrice[$size];
        }

        $idealPrice[$size] = $price[$size];
        for ($i = 1; $i <= $size; $i++) {
            $idealPrice[$size] = max($idealPrice[$size], $this->cutMemo($size - $i, $idealPrice) + $price[$i]);
        }
        var_dump($idealPrice);
        return $idealPrice[$size];
    }

    public function cut($size)
    {
        $price = [
            0 => 0,
            1 => 1,
            2 => 5,
            3 => 8,
            4 => 9,
            5 => 10,
            6 => 17,
            7 => 20,
            8 => 24,
            9 => 30,
        ];

        $result = $price[$size];
        for ($i = 1; $i <= $size; $i++) {
            $result = max($result, $this->cut($size - $i) + $price[$i]);
        }
        return $result;
    }

    public function fibonacci($value)
    {
        $fibonacciArray = [0, 1];
        for ($i = 2; $i <= $value; $i++) {
            $fibonacciArray[$i] = $fibonacciArray[$i - 1] + $fibonacciArray[$i - 2];
            unset($fibonacciArray[$i - 2]);
        }

        var_dump($fibonacciArray);
    }

    public function transferItemToTable()
    {
        $key1 = [
            'keyword1',
            'keyword2',
            'original_price',
            'member_price',
            'cost_price',
            'level1_commission_rate',
            'level2_commission_rate',
            'level3_commission_rate',
            'expire_at',
            'package_sum',
        ];

        $activities = db('activity')->select();
        $data = [];
        foreach ($activities as $id => $activity) {
            $str = $activity['selcontent'];
            if (!$str) {
                continue;
            }

            $arr = explode('☆', $str);
            $tmp = [];
            foreach ($arr as $key => $value) {
                $value = explode('∮', $value);
                foreach ($value as $k => $v) {
                    $tmp[$k][$key] = $v ?: 0;
                }
            }
            foreach ($tmp as $key => $value) {
                if (count($value) < count($key1)) {
                    $len = count($value);
                    while ($len != count($key1)) {
                        $value[] = 0;
                        $len++;
                    }
                }
                $temp = array_combine($key1, $value);
                $temp['activity_id'] = $activity['idactivity'];
                $temp['sold'] = 0;

                // $tmp['sold'] = 0;
                if ($temp['expire_at']) {
                    $temp['expire_at'] = strtotime($temp['expire_at']);
                }
                $data[] = $temp;
            }
        }
        // var_dump($data);die;

        db('package')->insertAll($data);
        var_dump($data);

    }

    public function testNotify()
    {
        $xml = <<<XML
        <xml><appid><![CDATA[wxa22b0f1a96c793e9]]></appid>
<bank_type><![CDATA[CFT]]></bank_type>
<cash_fee><![CDATA[2]]></cash_fee>
<fee_type><![CDATA[CNY]]></fee_type>
<is_subscribe><![CDATA[Y]]></is_subscribe>
<mch_id><![CDATA[1511368881]]></mch_id>
<nonce_str><![CDATA[gX878H0YFCEKgA1cMzC83oNVmLoU0NFa]]></nonce_str>
<openid><![CDATA[oO09h1WpHiWr-UkDHfeBAZ6c8lnE]]></openid>
<out_trade_no><![CDATA[20190628114843585559]]></out_trade_no>
<result_code><![CDATA[SUCCESS]]></result_code>
<return_code><![CDATA[SUCCESS]]></return_code>
<sign><![CDATA[213DC0A8D5ECBA35397B8B0411754918]]></sign>
<time_end><![CDATA[20190628114850]]></time_end>
<total_fee>2</total_fee>
<trade_type><![CDATA[JSAPI]]></trade_type>
<transaction_id><![CDATA[4200000325201906287235438665]]></transaction_id>
</xml>
XML;
        $url = 'https://test2.tongxiang123.com/tongxiangtest/signup_post1/13'; //接收xml数据的文件
//         $url = 'http://www.txy.com/tongxiangtest/signup_post1/13'; //接收xml数据的文件
        $ch = curl_init();  // 初始一个curl会话
        $timeout = 30;  // php运行超时时间，单位秒
        curl_setopt($ch, CURLOPT_URL, $url);    // 设置url
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);  // post 请求
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:text/xml; charset=utf-8"));    // 一定要定义content-type为xml，要不然默认是text/html！
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);//post提交的数据包
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); // PHP脚本在成功连接服务器前等待多久，单位秒
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);   // 抓取URL并把它传递给浏览器
        // 是否报错
        if(curl_errno($ch))
        {
            print curl_error($ch);
        }
        curl_close($ch);    // //关闭cURL资源，并且释放系统资源
         
        var_dump($result);
    }

    public function downloadLog()
    {
        $dir = date('Ym');
        $name = date('d') . '.log';
        $file = RUNTIME_PATH . 'log' . DS . $dir . DS . $name;

        if (file_exists($file)) {
            header("Content-type:application/octet-stream");
            $filename = basename($file);
            header("Content-Disposition:attachment;filename = " . $filename);
            header("Accept-ranges:bytes");
            header("Accept-length:" . filesize($file));
            readfile($file);
        } else {
            echo "<script>alert('文件不存在')</script>";
        }
    }
    /**
     * 关注日志重置
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-10 10:45:32
     * 1.清空会员日志
     * 2.查询会员信息
     * 3.分页会员信息
     * 4.判断关注状态
     * 4.分批写入到会员日志
     */
    public function memberLogReset(){
        $param = input('param.');
        $page = isset($param['page']) ? $param['page'] : 1;
        $page_size = 100;
        // 清空会员日志
        $is_reset = cache('is_reset');  // 是否重置
        if(!$is_reset){
            db('member_log')->where('1=1')->delete();
            cache("is_reset",true);
        }
        $total_record = db('member')->count();
        $member_list = db('member')->page($page,$page_size)->select();
        $total_page = ceil($total_record / $page_size);
        if($page <= $total_page){
            $data = [];
            // 循环写入日志
            foreach($member_list as $member){
                $intstate = $member['intstate'];
                $dtcreatetime = 0;
                $remark = '';
                // 判断关注类型
                if($intstate == 1){
                    $dtcreatetime = $member['dtsubscribetime'];
                    $remark = '关注';
                }elseif($intstate == 2){
                    $dtcreatetime = $member['dtunsubscribetime'];
                    $remark = '取消关注';
                }elseif($intstate == 3){
                    $dtcreatetime = $member['dtcreatetime'];
                    $remark = '游客';
                }
                // 如果存在，则不写入
                $member_log = db('member_log')->where('idmember',$member['idmember'])->find();
                if(!$member_log){
                    if($intstate == 2){
                        $data[] = [
                            'idmember' => $member['idmember'],
                            'openid' => $member['openid'],
                            'userimg' => $member['userimg'],
                            'intstate' => 1,
                            'old_intstate' => 0,
                            'dtsubscribetime' => $member['dtsubscribetime'],
                            'dtunsubscribetime' => null,
                            'dtcreatetime' => $dtcreatetime,
                            'idsite' => $member['idsite'],
                            'type' => 1,
                            'remark' => $remark
                        ];
                    }
                    $data[] = [
                        'idmember' => $member['idmember'],
                        'openid' => $member['openid'],
                        'userimg' => $member['userimg'],
                        'intstate' => $intstate,
                        'old_intstate' => $intstate==1 || $intstate==3  ? 0 : 1,
                        'dtsubscribetime' => $intstate==1 ? $member['dtsubscribetime'] : null,
                        'dtunsubscribetime' => $intstate==2 ? $member['dtunsubscribetime'] : null,
                        'dtcreatetime' => $dtcreatetime,
                        'idsite' => $member['idsite'],
                        'type' => 1,
                        'remark' => $remark
                    ];
                }
            }
            // 批量写入到数据库
            db('member_log')->insertAll($data);
            $page++;
            $href = url('test/memberLogReset',['page'=>$page]);
            $current_page = $page - 1;
            echo "一共".$total_page."页数据,已更新".$current_page."页数据<script>setTimeout(function(){ window.location.href=\"".$href."\"; }, 1000);</script>";
        }else{
            echo '数据更新完成!';
            cache("is_reset",false);
            exit;
        }
    }
}
