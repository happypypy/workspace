<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/2/23
 * Time: 17:31
 */

namespace app\admin\controller;


use think\Request;

class Report extends Base{
    /**
     * 之前的会员增减报表
     * @return mixed
     */
    public function index_former(){

        if($this->CMS->CheckPurview('manage') == false){
            $this->NoPurview();
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member;

        $list = $obj->member_report_list($request);

        $this->assign('list',$list);
        return $this->fetch();
    }

    //用户增长报表
    public function index(){

        if($this->CMS->CheckPurview('manage') == false){
            $this->NoPurview();
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member;
        //新的用户增长报表方法
        $list = $obj->new_member_report_list($request);

        $this->assign('list',$list);
        return $this->fetch();
    }

    //通过点击数据的数字跳转用户列表页面
    public function getUserList(){
        $request = Request::instance()->param();
//        halt($request);
        $obj = new \app\admin\module\Member;
        //新的用户增长报表方法
        $list = $obj->get_user_list($request);
        $obj1 = new \app\admin\module\activity(session('idsite'));

        $_account=$obj1->getUser();
        $account=[];
        if($_account)
        {
            foreach ($_account as $k=>$vo)
            {
                $account[$vo['idaccount']]=$vo['chrname'];
            }
        }

        $hyfl=$obj1->getDic("hyfl");
        $usertype=[];
        foreach ($hyfl as $vo)
        {
            $usertype[$vo['code']]=$vo['name'];
        }
//        halt($list);
        $this->assign('usertype',$usertype);
        $this->assign('hyfl',$hyfl);
        $this->assign('account',$account);
        $this->assign('member_list',$list['result']);
        $this->assign('page',$list['pager']);
        $this->assign('idsite',session('idsite'));
        $this->assign('is_cashed',checkedMarketingPackage(session('idsite'),'cashed'));
        return $this->fetch();
    }

    /**
     * 订单列表
     */
    public function order(){
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Order;

        $list = $obj->order_report_list($request);

        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 订单列表
     */
    public function activity(){
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Order;

        $list = $obj->activity_order_report_list($request);

        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 会员报表导出
     */
    public function member_report()
    {
        if($this->CMS->CheckPurview('manage') == false){
            $this->NoPurview();
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member;

        $list = $obj->member_report_list($request);

        ob_clean();

        //2.加载PHPExcle类库
        require_once dirname(__FILE__) . '/../../../extend/PHPExcel/PHPExcel.php';
        //3.实例化PHPExcel类
        $objPHPExcel = new \PHPExcel();
        $PHPSheet = $objPHPExcel->getActiveSheet();
        //5.设置表格头（即excel表格的第一行）
        $PHPSheet
            ->setCellValue('A1', '日期	')
            ->setCellValue('B1', '关注用户总数	')
            ->setCellValue('C1', '游客总数	')
            ->setCellValue('D1', '新关注')
            ->setCellValue('E1', '取消关注	')
            ->setCellValue('F1', '净增关注	')
            ->setCellValue('G1', '新游客	');


        //设置单元格宽度
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(25);


        $i = 0;
        foreach ($list as $key=>$value){
            $PHPSheet->setCellValue('A' . ($i + 2), $key);//添加订单号
            $PHPSheet->setCellValue('B' . ($i + 2), $value['count']);//添加产品名称
            $PHPSheet->setCellValue('C' . ($i + 2), $value['visit_count']);//添加产品名称
            $PHPSheet->setCellValue('D' . ($i + 2), $value['follow']);//添加报名人
            $PHPSheet->setCellValue('E' . ($i + 2), $value['unfollow']);//添加状态
            $PHPSheet->setCellValue('F' . ($i + 2), $value['increase']);//是否签到
            $PHPSheet->setCellValue('G' . ($i + 2), $value['visitor']);//是否签到
            if ($key =="汇总") {
                $objPHPExcel->getActiveSheet()->mergeCells('A'.($i + 2) . ':' .'C'.($i + 2));
                $objPHPExcel->getActiveSheet()->getStyle(('A'.($i + 2)))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            $i ++;
        }


        //7.设置保存的Excel表格名称
        $filename =  iconv('utf-8', 'gb2312', "会员动态") . date('ymdHis', time()).rand(10,99) . '.xls';
        //8.设置当前激活的sheet表格名称；
        $objPHPExcel->getActiveSheet()->setTitle( iconv('utf-8', 'gb2312', "会员动态"));

        //4.激活当前的sheet表
        $objPHPExcel->setActiveSheetIndex(0);
        //9.设置浏览器窗口下载表格
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        header("Content-Disposition: attachment;filename=$filename");

        //生成excel文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //下载文件在浏览器窗口
        $objWriter->save('php://output');
        exit;
    }

    /**
     * 会员报表导出
     */
    public function order_report()
    {
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Order;

        $list = $obj->order_report_list($request);

        ob_clean();

        //2.加载PHPExcle类库
        require_once dirname(__FILE__) . '/../../../extend/PHPExcel/PHPExcel.php';
        //3.实例化PHPExcel类
        $objPHPExcel = new \PHPExcel();
        $PHPSheet = $objPHPExcel->getActiveSheet();
        //5.设置表格头（即excel表格的第一行）
        $PHPSheet
            ->setCellValue('A1', '产品标题	')
            ->setCellValue('B1', '套餐名')
            ->setCellValue('C1', '订单数量	')
            ->setCellValue('D1', '销售数量	')
            ->setCellValue('E1', '销售金额	')
            ->setCellValue('F1', '利润	')
            ->setCellValue('G1', '剩余库存');


        //设置单元格宽度
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(25);


        $i = 0;
        foreach ($list as $key=>$value){
            $PHPSheet->setCellValue('A' . ($i + 2), $value['chrtitle']);//添加订单号
            $PHPSheet->setCellValue('B' . ($i + 2), $value['payname']);//添加产品名称
            $PHPSheet->setCellValue('C' . ($i + 2), $value['order_num']);//添加报名人
            $PHPSheet->setCellValue('D' . ($i + 2), $value['pay_num']);//添加状态
            $PHPSheet->setCellValue('E' . ($i + 2), $value['pay_price']);//是否签到
            $PHPSheet->setCellValue('F' . ($i + 2), $value['profit']);//是否签到
            $PHPSheet->setCellValue('G' . ($i + 2), $value['storage']);//是否签到
            if ($key =="汇总数据") {
                $objPHPExcel->getActiveSheet()->mergeCells('A'.($i + 2) . ':' .'B'.($i + 2));
                $objPHPExcel->getActiveSheet()->getStyle(('A'.($i + 2)))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            $i ++;
        }


        //7.设置保存的Excel表格名称
        $filename =  iconv('utf-8', 'gb2312', "订单") . date('ymdHis', time()).rand(10,99) . '.xls';
        //8.设置当前激活的sheet表格名称；
        $objPHPExcel->getActiveSheet()->setTitle( iconv('utf-8', 'gb2312', "订单"));

        //4.激活当前的sheet表
        $objPHPExcel->setActiveSheetIndex(0);
        //9.设置浏览器窗口下载表格
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        header("Content-Disposition: attachment;filename=$filename");

        //生成excel文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //下载文件在浏览器窗口
        $objWriter->save('php://output');
        exit;
    }

    /**
     *  产品导出
     */
    public function activity_report(){
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Order;

        $list = $obj->activity_order_report_list($request);

        ob_clean();

        //2.加载PHPExcle类库
        require_once dirname(__FILE__) . '/../../../extend/PHPExcel/PHPExcel.php';
        //3.实例化PHPExcel类
        $objPHPExcel = new \PHPExcel();
        $PHPSheet = $objPHPExcel->getActiveSheet();
        //5.设置表格头（即excel表格的第一行）
        $PHPSheet
            ->setCellValue('A1', '产品标题	')
            ->setCellValue('B1', '套餐名')
            ->setCellValue('C1', '产品发布时间	')
            ->setCellValue('D1', '报名截止日期	')
            ->setCellValue('E1', '产品开始日期	')
            ->setCellValue('F1', '产品结束日期')
            ->setCellValue('G1', '订单数量	')
            ->setCellValue('H1', '销售数量	')
            ->setCellValue('I1', '销售金额	')
            ->setCellValue('J1', '利润	')
            ->setCellValue('K1', '剩余库存');


        //设置单元格宽度
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(25);


        $i = 0;
        foreach ($list as $key=>$value){
            $PHPSheet->setCellValue('A' . ($i + 2), $value['chrtitle']);//添加订单号
            $PHPSheet->setCellValue('B' . ($i + 2), $value['payname']);//添加产品名称
            $PHPSheet->setCellValue('C' . ($i + 2), $value['dtpublishtime']);//添加产品名称
            $PHPSheet->setCellValue('D' . ($i + 2), $value['dtsignetime']);//添加产品名称
            $PHPSheet->setCellValue('E' . ($i + 2), $value['dtstart']);//添加产品名称
            $PHPSheet->setCellValue('F' . ($i + 2), $value['dtend']);//添加产品名称
            $PHPSheet->setCellValue('G' . ($i + 2), $value['order_num']);//添加报名人
            $PHPSheet->setCellValue('H' . ($i + 2), $value['pay_num']);//添加状态
            $PHPSheet->setCellValue('I' . ($i + 2), $value['pay_price']);//是否签到
            $PHPSheet->setCellValue('J' . ($i + 2), $value['profit']);//是否签到
            $PHPSheet->setCellValue('K' . ($i + 2), $value['storage']);//是否签到
            if ($key =="汇总数据") {
                $objPHPExcel->getActiveSheet()->mergeCells('A'.($i + 2) . ':' .'B'.($i + 2));
                $objPHPExcel->getActiveSheet()->getStyle(('A'.($i + 2)))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            $i ++;
        }


        //7.设置保存的Excel表格名称
        $filename =  iconv('utf-8', 'gb2312', "产品订单") . date('ymdHis', time()).rand(10,99) . '.xls';
        //8.设置当前激活的sheet表格名称；
        $objPHPExcel->getActiveSheet()->setTitle( iconv('utf-8', 'gb2312', "产品订单"));

        //4.激活当前的sheet表
        $objPHPExcel->setActiveSheetIndex(0);
        //9.设置浏览器窗口下载表格
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        header("Content-Disposition: attachment;filename=$filename");

        //生成excel文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //下载文件在浏览器窗口
        $objWriter->save('php://output');
        exit;
    }

    /**
     * 用户属性分析图
     */
    public function user_attribute(){
        if($this->CMS->CheckPurview('manage','attribute') == false){
            $this->NoPurview();
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member;
        //新的用户增长报表方法
        $list = $obj->user_attribute($request);

        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 通过省份获取市区的分析图
     */
    public function get_city_attribute(){
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member;
        //新的用户增长报表方法
        $list = $obj->get_city_attribute($request);

//        $this->assign('list',$list);
        return json($list);
    }

    /**
     * 购买用户分析图
     */
    public function buy_user(){
        if($this->CMS->CheckPurview('manage','buy_user') == false){
            $this->NoPurview();
        }

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member;
        //新的用户增长报表方法
        $list = $obj->buy_user($request);

        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 复购情况的购买次数分析图
     */
    public function again_condition(){

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member;
        //新的用户增长报表方法
        $list = $obj->again_condition($request);

        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 消费能力列表
     */
    public function buy_power_list(){

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member;
        //获取复购情况的数据
        $list = $obj->buy_power_list($request);

        $this->assign('list',$list);
        $this->assign('page',$list['pager']);
        $this->assign('is_cashed',checkedMarketingPackage(session('idsite'),'cashed'));
        return $this->fetch();
    }

    /**
     * 消费热力图
     */
    public function img_buy_power(){

        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member;
        //新的用户增长报表方法
        $list = $obj->img_buy_power($request);

        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 成交数据中每个产品类别购买人数图
     */
    public function deal_data(){
        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member;
        $list = $obj->deal_data($request);

        $this->assign('list',$list);
        //如果是用户数量
        if($request['order_by'] == 'user_count'){
            return $this->fetch();
        }else{
            return $this->fetch('deal_data_pay_num');
        }
    }

    /**
     * 浏览(浏览产品和转发)数据
     */
    public function browse_data(){


        $request = Request::instance()->param();
        $obj = new \app\admin\module\Member;
        $list = $obj->browse_data($request);

        $this->assign('list',$list);
        //如果是浏览次数
        if($request['type'] == 'browse'){
            return $this->fetch();
        }else{
            return $this->fetch('browse_data_share');
        }
    }

}