<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/21
 * Time: 9:52
 */

namespace app\admin\controller;
use think\Controller;
use app\admin\module\Account;
use think\Request;
use think\Verify;
use think\Db;
class Index extends Base {

//    public function che()
//    {
//        $result = Db::query('SHOW TABLE STATUS');
//
//
//        foreach($result as $key=>$vo)
//        {
//            $tablename=$vo["Name"];
//
//                $result1 = Db::query('show full fields from '.$tablename);
//                foreach($result1 as $key1=>$vo1)
//                {
//                    $Field_type=$vo1['Type'];
//                    $Field_Name=$vo1['Field'];
//
//
//                    if(strpos($Field_type,'varchar')>-1 && (strpos($Field_Name,'tc_')>-1 || strpos($Field_Name,'en_')>-1) )
//                    {
//                        echo 'ALTER TABLE `'.$tablename.'` MODIFY  `'.$vo1['Field'].'`  varchar(50) ';
//                        Db::query('ALTER TABLE `'.$tablename.'` MODIFY  `'.$vo1['Field'].'`  varchar(50) ');
//                        echo '--OK'."<br>";
//                    }
//                }
//
//        }
//
//
//        foreach($result as $key=>$vo)
//        {
//            $tablename=$vo["Name"];
//            echo $tablename."<br>".'ALTER TABLE '.$tablename.' CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'."<br>";
//
//				Db::query('ALTER TABLE '.$tablename.' CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
//
//			$result1 = Db::query('show full fields from '.$tablename);
//
//
//            foreach($result1 as $key1=>$vo1)
//            {
//                $Field_type=$vo1['Type'];
//
//                if(strpos($Field_type,'varchar')>-1)
//                {
//                    echo 'ALTER TABLE `'.$tablename.'` MODIFY  `'.$vo1['Field'].'` '.$Field_type.' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'."<br>";
//                    Db::query('ALTER TABLE `'.$tablename.'` MODIFY  `'.$vo1['Field'].'`  '.$Field_type.' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
//                }
//
//			}
//        }
//        echo "OK";
//        exit();
//    }
//    public function copyfile()
//    {
//        $requset = Request::instance()->param();
//        $source=isset($requset['source'])?$requset['source']:"";
//        $dest=isset($requset['dest'])?$requset['dest']:"";
//        $chk1=isset($requset['chk1'])?$requset['chk1']:"";
//        $chk2=isset($requset['chk2'])?$requset['chk2']:"";
//        $chk3=isset($requset['chk3'])?$requset['chk3']:"";
//        $msg='';
//        if($source!="" && $dest!="")
//        {
//            if (!file_exists($dest))
//            {
//                $msg='目标目录不存在！';
//            }
//            if (!file_exists($source))
//            {
//                $msg='源文件目录不存在！';
//            }
//            if($msg=='')
//            {
//                $this->copydir($source, $dest);
//                $msg="复制完成！";
//            }
//
//        }
//
//        $this->assign('chk1',$chk1);
//        $this->assign('chk2',$chk2);
//        $this->assign('chk3',$chk3);
//        $this->assign('source',$source);
//        $this->assign('dest',$dest);
//        $this->assign('msg',$msg);
//
//        return $this->fetch();
//    }
//
//
//    /**
//     * 复制文件夹
//     * @param $source
//     * @param $dest
//     */
//    private function copydir($source, $dest)
//    {
//        if (!file_exists($dest)) mkdir($dest);
//        $handle = opendir($source);
//        while (($item = readdir($handle)) !== false) {
//            if ($item == '.' || $item == '..') continue;
//            $_source = $source . '/' . $item;
//            $_dest = $dest . '/' . $item;
//            if (is_file($_source)) copy($_source, $_dest);
//            if (is_dir($_source)) $this->copydir($_source, $_dest);
//        }
//        closedir($handle);
//    }

    public function index(){

        return $this->fetch();

    }
    public function indexsite(){
        $requset = Request::instance()->param();
        $index_obj = new \app\admin\module\Index();
        $column_index = $index_obj->column_index1();
        $result = $index_obj->module_index1($requset);
        $columnData=[];
        $arrTmp=[];
        foreach ($result['data'] as $key=>$value){
            if($this->CMS->CheckModulePurview($value["chrcode"])) {
                $columnData[] = $value;
                if(!in_array($value["codecatalog"],$arrTmp))
                {
                    $arrTmp[]=$value["codecatalog"];
                }
            }
        }

        $columnCat=[];
        foreach ($column_index as $key=>$value){
            if(in_array($value["chrcode"],$arrTmp))
            {
                $columnCat[]=$value;
            }
        }

        //获取所有的节点
        $node_list = $this->getNode();
        $info_arr = [];
        $activity_arr = [];
        if($node_list){
            foreach ($node_list as $value){
                //如果是资讯
                if($value['nodetype'] == 1){
                    array_push($info_arr,$value);
                    //否则是产品
                }elseif ($value['nodetype'] == 2){
                    array_push($activity_arr,$value);
                }
            }
        }
        // var_dump($activity_arr);exit;
        $this->assign('info_arr',$info_arr);
        $this->assign('activity_arr',$activity_arr);
        $this->assign('idsite',$result['idsite']);
        $this->assign('modulist',$columnData);
        $this->assign('catalist',$columnCat);
        return $this->fetch();
    }
    // 切换主题
    public function toggleTheme(){
        $param = input('param.');
        $theme = isset($param['theme']) ? $param['theme'] : 'v1';
        if($theme == 'v1'){
            cache('AccountID_'.session("AccountID").'_theme','v1');
        }else if($theme == 'v2'){
            cache('AccountID_'.session("AccountID").'_theme','v2');
        }
        return json(['code'=>1, 'msg'=>'切换成功']);
    }
    public function bar(){
        return $this->fetch();
    }
    public function header(){
        $siteid=0;
        $siteflag=1;
        $conpayname="";
        $expiremsg='';
        if(!empty(session('idsite')))
        {
            $siteflag=2;
            $siteid=session('idsite');
            if($siteid>0)
            {
                $row=db('site_manage')->field('site_name,expiretime')->where(array('id'=>$siteid))->find();
                $conpayname=$row['site_name'];
                $expiretime=$row['expiretime'];
                if(empty($expiretime) ||$expiretime<100 )
                {
                    $expiremsg="<sapn style='color:red; font-weight: bold;'> 还没有设置过期时间，为了不影响服务，请联系客服！</sapn>";
                }
                else
                {
                    if(strtotime("+2  month")>$expiretime)
                    {
                        $expiremsg="<sapn style='color:red;font-weight: bold;'> 服务到期日期：".date('Y年m月d日',$expiretime)."，为了不影响服务，请及时续费！</sapn>";
                    }
                    else
                    {
                        $expiremsg="<sapn style='font-weight: bold;'> 服务到期日期：".date('Y年m月d日',$expiretime)."</sapn>";
                    }
                }

            }
        }


        $this->assign('expiremsg',$expiremsg);

        $this->assign('conpayname',$conpayname);
        $this->assign('username',session("UserName"));
        $this->assign('siteflag',$siteflag);

        return $this->fetch();
    }
    public function main(){

        $obj = new \app\admin\module\Index();
        $UserIndexInfo = $obj->getUserIndexInfo();
        $UserIndexConfig = $obj->getUserIndexConfig();
        $IndexConfig = $obj->getIndexConfig();
       for($i=0;$i<count($IndexConfig);$i++)
       {
           $code=$IndexConfig[$i]['chrcode'];
           $IndexConfig[$i]['checked']='';
           foreach ($UserIndexInfo as $vo)
           {
                if($vo['chrcode']==$code)
                {
                    $IndexConfig[$i]['checked']='checked';
                    break;
                }
           }
       }
        $this->assign('UserIndexInfo',$UserIndexInfo);
        $this->assign('UserIndexConfig',$UserIndexConfig);
        $this->assign('IndexConfig',$IndexConfig);
        return $this->fetch();
    }
    public function leftbar(){
        //清除idsite  session
        session('idsite',null);
        $index_obj = new \app\admin\module\Index();
        $column_index = $index_obj->column_index();
        $module_index = $index_obj->module_index();
        $this->assign('modulist',$module_index);
        $this->assign('catalist',$column_index);
        return $this->fetch();
    }

    //进入站点
    public function leftbar1(){ 
        $requset = Request::instance()->param();
        $index_obj = new \app\admin\module\Index();
        $column_index = $index_obj->column_index1();
        $result = $index_obj->module_index1($requset);
        $columnData=[];
        $arrTmp=[];
        foreach ($result['data'] as $key=>$value){
            if($this->CMS->CheckModulePurview($value["chrcode"])) {
                $columnData[] = $value;
                if(!in_array($value["codecatalog"],$arrTmp))
                {
                    $arrTmp[]=$value["codecatalog"];
                }
            }
        }

        $columnCat=[];
        foreach ($column_index as $key=>$value){
            if(in_array($value["chrcode"],$arrTmp))
            {
                $columnCat[]=$value;
            }
        }

        //获取所有的节点
        $node_list = $this->getNode();
        $info_arr = [];
        $activity_arr = [];
        if($node_list){
            foreach ($node_list as $value){
                //如果是资讯
                if($value['nodetype'] == 1){
                    array_push($info_arr,$value);
                    //否则是产品
                }elseif ($value['nodetype'] == 2){
                    array_push($activity_arr,$value);
                }
            }
        }
            // var_dump($activity_arr);exit;
        $this->assign('info_arr',$info_arr);
        $this->assign('activity_arr',$activity_arr);
        $this->assign('idsite',$result['idsite']);
        $this->assign('modulist',$columnData);
        $this->assign('catalist',$columnCat);
        return $this->fetch();
    }

    public function getNode(){
        $idsite = session('idsite');
        $node_list = db('node')->field('nodeid,nodetype,nodename,showonmenu')->where(['idsite'=>$idsite])->order('idorder asc,nodeid asc')->select();
        return $node_list;
    }
    public function tabs(){
        return $this->fetch();
    }

    public function logout()
    {
        if (!Request::instance()->isPost()) {
            $this->assign('msg', "");
            return $this->fetch('login');
        }
    }

    public function checktn()
    {
        require_once dirname(__FILE__).'/TnCode.php';
        $tn  = new TnCode();
        ob_end_clean();
        if($tn->check()){
            $_SESSION['tncode_check'] = 'ok';
            echo "ok";
        }else{
            $_SESSION['tncode_check'] = 'error';
            echo "error";
        }
        exit();
    }
    public function tncode()
    {
        ob_end_clean();
        error_reporting(0);
        require_once dirname(__FILE__).'/TnCode.php';
        $tn  = new TnCode();
        $tn->make();
    }

    public function loginsite()
    {
       if(!Request::instance()->isPost()) {
            $this -> clearLoginSession();
            $this->assign('msg', "");
            return $this->fetch();
        }

        $strSiteCode=I("txtSiteCode");
        $strAccount=I("txtAccount");
        $strPwd=I("txtPassword");
        $strMsg="";
        //setcookie("SiteCode",$strSiteCode);

        $verify = new Verify();
        if(empty($strSiteCode) || $strSiteCode=="")
        {
            $strMsg="站点代号不能为空！";
        }
        elseif(empty($strAccount) || $strAccount=="")
        {
            $strMsg="账号不能为空！";
        }
        else if(empty($strPwd) || $strPwd=="")
        {
            $strMsg="密码不能为空！";
        }
        else if(empty($_SESSION['tncode_check']) ||  $_SESSION['tncode_check'] != 'ok')
        {
            $strMsg="验证码不正确！";
        }
        $_SESSION['tncode_check']="";
        /*
        else if(empty($strSn) || $strSn=="")
        {
            $strMsg="验证码不能为空！";
        }
        else  if (!$verify->check($strSn, "loginsn"))
        {
              $strMsg="验证码不正确！";
        }*/

        $obj = new Account;
        $siteid=$obj->getSiteID($strSiteCode);

        if(empty($siteid) || $siteid==0)
        {
            $strMsg="站点代号不存在！";
        }
        session('idsite',$siteid);
        if( $strMsg!="")
        {
            $this->assign('msg',$strMsg);
            return $this->fetch();
            exit();
        }




        $dataInfo = $obj->login($strAccount,$siteid);
        if(!$dataInfo)
        {
            $strMsg="账号不存在！";
        }
        else if(md5($strPwd)!=$dataInfo['chrpassword'])
        {
            $strMsg="账号密码不匹配！";
        }
        else if($dataInfo['intflag']=="2")
        {
            $strMsg="账号已被锁定！";
        }

        if( $strMsg!="")
        {
            $this->assign('msg',$strMsg);
            return $this->fetch();
            exit();
            //$this->success($strMsg,url("admin/Index/loginsite"));
        }
        else
        {
            session("AccountID",$dataInfo['idaccount']);
            session("Account",$dataInfo['chraccount']);
            session("UserName",$dataInfo['chrname']);

            // 如果没有数据为空，不不显示系统更新
            $system_update_count = db('system_update')->where('is_open',1)->count();
            if($system_update_count > 0){
                $isUpdateRemind = $dataInfo['is_update_remind'];
            }else{
                $isUpdateRemind = 0;
            }
            session("isUpdateRemind",$isUpdateRemind);

            // 如果没有数据为空，不不显示新功能提醒
            $new_feature_recommend = db('new_feature_recommend')->where('is_open',1)->count();
            if($new_feature_recommend > 0){
                $newFeaturesRemind = $dataInfo['new_features_remind'];
            }else{
                $newFeaturesRemind = 0;
            }
            session("newFeaturesRemind",$newFeaturesRemind);
            session("login",1);

            db('account')->where("idaccount",$dataInfo['idaccount'])->setField("is_update_remind",0);
            db('account')->where("idaccount",$dataInfo['idaccount'])->setField("new_features_remind",0);

            if($dataInfo['repurview']==1)
                $obj->RefreshPurview($dataInfo['idaccount']);

            if(!empty(session("purview".$dataInfo["siteid"])))
            {
                session("purview".$dataInfo["siteid"],null);
            }

            $this->success('登陆成功',url("admin/Index/Indexsite"));
            //$this->assign('msg',"登成功");
        }

    }

    /**
     * 清楚登录session
     */
    private function clearLoginSession(){

        session("AccountID",null);
        session("Account",null);
        session("UserName",null);
        session("login",1);
        session('idsite',null);
    }


    public function login()
    {
        if(!Request::instance()->isPost()) {
            $this -> clearLoginSession();
            $this->assign('msg', "");
            return $this->fetch();
        }

        $strAccount=I("txtAccount");
        $strPwd=I("txtPassword");
        $strSn=I("txtICode");
        $strMsg="";

        $verify = new Verify();
        if(empty($strAccount) || $strAccount=="")
        {
            $strMsg="账号不能为空！";
        }
        else if(empty($strPwd) || $strPwd=="")
        {
            $strMsg="密码不能为空！";
        }
        else if(empty($_SESSION['tncode_check']) ||  $_SESSION['tncode_check'] != 'ok')
        {
            $strMsg="验证码不正确！";
        }
        $_SESSION['tncode_check']="";
        /*
        else if(empty($strSn) || $strSn=="")
        {
            $strMsg="验证码不能为空！";
        }
        else  if (!$verify->check($strSn, "loginsn"))
        {
            $strMsg="验证码不正确！";
        }
        */
        if( $strMsg!="")
        {
            $this->assign('msg',$strMsg);
            return $this->fetch();
        }

        $obj = new Account;

        $dataInfo = $obj->login($strAccount);
        if(!$dataInfo)
        {
            $strMsg="账号不存在！";
        }
        else if(md5($strPwd)!=$dataInfo['chrpassword'])
        {
            $strMsg="账号密码不匹配！";
        }
        else if($dataInfo['intflag']=="2")
        {
            $strMsg="账号已被锁定！";
        }

        if( $strMsg!="")
        {
            $this->assign('msg',$strMsg);
            return $this->fetch();
        }
        else
        {
            session("AccountID",$dataInfo['idaccount']);
            session("Account",$dataInfo['chraccount']);
            session("UserName",$dataInfo['chrname']);
            session("login",1);

            if($dataInfo['repurview']==1)
                $obj->RefreshPurview($dataInfo['idaccount']);

            $this->success('登陆成功',url("admin/Index/Index"));
            //$this->assign('msg',"登成功");
        }

    }

    public function setuserconfig()
    {
        $data = Request::instance()->param();

        $obj = new \app\admin\module\Index();
        $bool = $obj->setUserConfig($data);
        if($bool){
            exit(1) ;
        }else{
            exit(0);
        }
    }

    public function addbox()
    {
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Index();
        $bool = $obj->AddBox($data);
        if($bool){
            exit('1') ;
        }else{
            exit('0');
        }
    }

    public function  removebox()
    {
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Index();
        $bool = $obj->RemoveBox($data);
        if($bool){
            exit('1') ;
        }else{
            exit('0');
        }
    }

    public function sortorder()
    {
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Index();
        $obj->SortOrder($data);
        exit();
    }

    public function setboxconfig()
    {
        $data = Request::instance()->param();
        $obj = new \app\admin\module\Index();
        $bool = $obj->SetBoxConfig($data);
        if($bool !== false){
            exit('1') ;
        }else{
            exit('0');
        }
    }


    /**
     * 验证码获取
     */
    public function vertify()
    {
        $config = array(
            'fontSize' => 20,
            'length' => 4,
            'useCurve' => false,
            'useNoise' => false,
            'reset' => false
        );
        ob_end_clean();
        $Verify = new Verify($config);
        $Verify->entry("loginsn");
        exit();
    }

    //多语言
    public function lang(){
        switch ($_GET['lang']) {
            case 'cn':
                cookie('think_var', 'zh-cn');
                break;
            case 'en':
                cookie('think_var', 'en-us');
                break;
            case 'co':
                cookie('think_var', 'zh-co');
                break;
            //其它语言
        }
    }

    //多语言
   /* public function lang_type(){
        switch ($_GET['lang_type']) {
            case 'cn':
                cookie('lang_type', 'cn');
                break;
            case 'en':
                cookie('lang_type', 'en');
                break;
            case 'tc':
                cookie('lang_type', 'tc');
                break;
            //其它语言
        }
    }*/
    public function _empty($name){
        $this->error("没有".$name.'操作');
    }
}