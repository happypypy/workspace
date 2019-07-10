<?php


namespace app\admin\module;
use think\Model;
use think\Page;
use think\Db;
use think\Session;
use think\Log;
use think\Exception;

class Api extends Model{

	/**
	 * 从$index开始，执行$num个短信发送计划
	 * @author Hlt
	 * @DateTime 2019-04-20T16:37:54+0800
	 * @param    int                   $index 已执行的短信计划的最大id
	 * @param    int                   $num   本次应执行的短信计划数
	 * @return   int                          本次执行的短信计划最大id
	 */
	public function executeTextSchedule($index, $num)
	{
		$texts = db('sms_send_schedule')
            ->where(
                'send_time',
                'elt',
                date('Y-m-d H:i:s')
            )
            ->where(['status' => 0])
            ->where('sms_send_schedule_id', 'gt', $index)
            ->field([
                'sms_send_schedule_id',
                'mobile',
                'idsite',
                'content',
                'ssid'
            ])
            // ->fetchSql(true)
            ->order('send_time', 'asc')
            ->limit($num)
            ->select();

        foreach ($texts as $text)
        {
            Log::debug('本次短信发送参数为：' . print_r([$text['idsite'], $text['mobile'], $text['content'], $text['ssid']], true));
            $result = sendMsg($text['idsite'], $text['mobile'], $text['content'], $text['ssid']);
            // 发送成功
            if($result['status'] == 'success')
            {
                $status = 1;
            }else
            {
                Log::warning('短信发送失败。 $text = ' . print_r($text, true));
                $status = 2;
            }
            $updateArr = [
                'msg' => $result['msg'],
                'exec_time' => date('Y-m-d H:i:s'),
                'status' => $status,
            ];
            //Log::debug('短信返回信息为成功，返回值为：$result = ' . print_r($result, true));
            db('sms_send_schedule')->where(['sms_send_schedule_id' => $text['sms_send_schedule_id']])->update($updateArr);
            //usleep(1000);
        }

        return $texts ? $text['sms_send_schedule_id'] : $index;
    }
    /**
     * 执行解冻冻结积分
	 * @author Chenjie
	 * @param    int                   $starttime 开始时间戳
	 * @param    int                   $endtime   结束时间戳
	 * @return   bollean   true:解冻成功 false:解决失败
     */
    public function executeThawActivityIntegral($starttime,$endtime){
        if(!$starttime){
            $starttime = date('Y-m-d H:i:s',strtotime("yesterday"));
        }else{
            $starttime = date('Y-m-d H:i:s',$starttime);
        }
        if(!$endtime){
            $endtime = date('Y-m-d H:i:s',strtotime("today")-1);
        }else{
            $endtime = date('Y-m-d H:i:s',$endtime);
        }

        $activity = db('order')
            ->field("order.id,activity.dtsignetime")
            ->alias('order')
            ->join('activity','order.dataid = activity.idactivity')
            ->where([
                'activity.dtsignetime' => ['between',[$starttime,$endtime]],
            ])
            ->select();

        if($activity){
            // 循环解析活动积分
            foreach($activity as $value){
                $member_integral_record = db('member_integral_record')->where(['order_id'=>$value['id'],'is_freeze'=>1])->select();
                foreach($member_integral_record as $value2){
                    $member_id = $value2['member_id'];
                    $integral = $value2['integral'];
                    Db::startTrans();
                    try{
                        db('member_integral_record')->where('id',$value2['id'])->setField("is_freeze",2);
                        db('member')->where('idmember',$member_id)->setInc('integral',$integral);
                        Db::commit();
                    }catch(Exception $e){
                        Db::rollback();
                    }

                }
            }
        }

        return count($activity) ? true : false;
    }

}