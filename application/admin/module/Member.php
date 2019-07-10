<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/5
 * Time: 14:43
 */

namespace app\admin\module;
use think\Exception;
use think\Model;
use think\Page;
use think\Db;
use think\Request;

class Member extends Model {

    //会员列表
    public function index($data){

        if(array_key_exists('p',$data) == false){
            $data['p'] = 1;
        }

        $search=[];
        $search['memberid']=empty($data['memberid'])?"":$data['memberid'];  //用户编号
        $search['username']=empty($data['username'])?"":$data['username'];  //用户姓名
        $search['nickname']=empty($data['nickname'])?"":$data['nickname'];  //昵称
        $search['categoryid']=empty($data['categoryid'])?"": implode(',',$data['categoryid']) ;  //用户类别
        $search['intstate']=empty($data['intstate'])?"":(is_array($data['intstate'])?implode(',',$data['intstate']):$data['intstate']) ;  //关注状态
        $search['istel']=empty($data['istel'])?"":$data['istel'];  //是否有手机号码
        $search['chrtel']=empty($data['chrtel'])?"":$data['chrtel'];  //手机号码
        $search['paynumif']=empty($data['paynumif'])?"":$data['paynumif'];  //订单条件
        $search['paynum']=empty($data['paynum'])?"":$data['paynum'];  //订单数
        $search['vflag']=empty($data['vflag'])?"":$data['vflag'];  //最近来访标记
        $search['vstime']=empty($data['vstime'])?"":$data['vstime'];  //最近来访 开始时间
        $search['vetime']=empty($data['vetime'])?"":$data['vetime'];  //最近来访 结束时间
        $search['rstime']=empty($data['rstime'])?"":$data['rstime'];  //注册时间范围 开始时间
        $search['retime']=empty($data['retime'])?"":$data['retime'];  //注册时间范围 结束时间
        $search['cstime']=empty($data['cstime'])?"":$data['cstime'];  //取消时间范围 开始时间
        $search['cetime']=empty($data['cetime'])?"":$data['cetime'];  //取消时间范围 结束时间
        $search['accountid']=empty($data['accountid'])?"":$data['accountid'];  //所属商务
        $search['vflag1']=empty($data['vflag1'])?"":$data['vflag1'];  //跟时时间标记
        $search['gstime']=empty($data['gstime'])?"":$data['gstime'];  //跟时 开始时间
        $search['getime']=empty($data['getime'])?"":$data['getime'];  //跟时 结束时间
        $search['qrcode_name']=empty($data['qrcode_name'])?"":$data['qrcode_name'];  // 二维码ids
        $search['qr_scene_str']=empty($data['qr_scene_str'])?"":$data['qr_scene_str'];  // 二维码ids

        if($search['qr_scene_str']){
            $map['qr_scene_str'] = $search['qr_scene_str'];
        }

        $map['idsite'] = session('idsite');
        if(!empty($search['memberid'])){$map['idmember'] = $search['memberid'];}
        if(!empty($search['username'])){$map['chrname'] = ['like','%'.$search['username'].'%'];}
        if(!empty($search['nickname'])){$map['nickname'] = ['like','%'.$search['nickname'].'%'];}
        if(!empty($search['categoryid'])){$map['categoryid'] = array('in',$search['categoryid']);}
        if(!empty($search['intstate'])){$map['intstate'] =  array('in',$search['intstate']);}
        if(!empty($search['istel'])){
            if($search['istel']==1)
            {
                $map['chrtel'] =array('neq','');
            }
            else if($search['istel']==2)
            {
                $map['chrtel']='';
            }
        }
        if(!empty($search['chrtel'])){$map['chrtel'] = $search['chrtel'];}
        if(!empty($search['paynumif']) && !empty($data['paynum'])){
            if($search['paynumif']==2)
            {
                $map['paynum']=$data['paynum'];
            }
            else if($search['paynumif']==3)
            {
                $map['paynum']=array('lt',$data['paynum']);
            }
            else
            {
                $map['paynum']=array('gt',$data['paynum']);
            }
        }
        if(!empty($search['vflag'])){
            $vstime='';
            $vetime='';
            if($search['vflag']==1)
            {
                $vstime=strtotime(date('Y-m-d',time()));
                $vetime=strtotime("1 day",$vstime);
            }
            else if($search['vflag']==2)
            {
                $vstime=strtotime(date('Y-m-d',time()));
                $vetime=strtotime("7 day",$vstime);
            }
            else if($search['vflag']==3)
            {
                $vstime=strtotime(date('Y-m-d',time()));
                $vetime=strtotime("30 day",$vstime);
            }
            else if($search['vflag']==4 && !empty($search['vstime']) && !empty($search['vetime']))
            {
                $vstime=strtotime($search['vstime']);
                $vetime=strtotime("1 day",strtotime($search['vetime']));
            }
            if($vstime!='' && $vetime!='')
            {
                $map['dtlastvisitteim']=array(array('gt',$vstime),array('lt',$vetime));
            }


        }
        if(!empty($search['rstime']) && !empty($search['retime'])){
            $map['dtsubscribetime']=array(array('gt',strtotime($search['rstime'])),array('lt',strtotime("1 day",strtotime($search['retime']))));
        }
        if(!empty($search['cstime']) && !empty($search['cetime'])){
            $map['dtunsubscribetime']=array(array('gt',strtotime($search['cstime'])),array('lt',strtotime("1 day",strtotime($search['cetime'])))) ;
        }
        if(!empty($search['vflag1'])){
            $gstime='';
            $getime='';
            if($search['vflag1']==1)
            {
                $gstime=strtotime(date('Y-m-d',time()));
                $getime=strtotime("1 day",$gstime);
            }
            else if($search['vflag1']==2)
            {
                $gstime=strtotime(date('Y-m-d',time()));
                $getime=strtotime("7 day",$gstime);
            }
            else if($search['vflag1']==3)
            {
                $gstime=strtotime(date('Y-m-d',time()));
                $getime=strtotime("30 day",$gstime);
            }
            else if($search['vflag1']==4 && !empty($search['gstime']) && !empty($search['getime']))
            {
                $gstime=strtotime($search['gstime']);
                $getime=strtotime("1 day",strtotime($search['getime']));
            }
            if($gstime!='' && $getime!='')
            {
                $map['followuptime']=array(array('gt',$gstime),array('lt',$getime));
            }


        }
        if(!empty($search['accountid'])){
            if($search['accountid']==-1)
            {
                //$map['_string'] = "(iduser='')  OR (iduser is null)";
                $map['iduser']= array([ 'eq' , '0'] , [ 'EXP',Db::raw('IS NULL')] ,  [ 'eq' , '' ] , 'or' );
                //$map['iduser'] =array('EXP',Db::raw('IS NULL'));
            }
            else
            {
                $map['iduser'] = $search['accountid'];
            }
        }

        $count = db('member')->where($map)->count();
        $page = new Page($count,PAGE_SIZE);

        $member_list = db('member')->where($map)->order('idmember desc')->limit($page->firstRow.','.$page->pageSize)->select();

        // 会员总数
        $member_total = db('member')->where('idsite',session('idsite'))->count();
        // 关注会员数
        $follow_memer = db('member')->where(['intstate'=>1,'idsite'=>session('idsite')])->count();
        // 取消关注会员数
        $nofollow_member = db('member')->where(['intstate'=>2,'idsite'=>session('idsite')])->count();
        // 游客会员数
        $guest_member = db('member')->where(['intstate'=>3,'idsite'=>session('idsite')])->count();

        // 带条件会员总数
        $map_member_total = db('member')->where($map)->count();
        // 带条件关注会员数
        $map_follow_memer = db('member')->where($map)->where('intstate',1)->count();
        // 带条件取消关注会员数
        $map_nofollow_member = db('member')->where($map)->where('intstate',2)->count();
        // 带条件游客会员数
        $map_guest_member = db('member')->where($map)->where('intstate',3)->count();

        // 无条件数据
        $statistical = [
            'map' => ['member_total'=>$map_member_total, 'follow_memer'=>$map_follow_memer, 'nofollow_member'=>$map_nofollow_member, 'guest_member'=>$map_guest_member],
            'nomap' => ['member_total'=>$member_total, 'follow_memer'=>$follow_memer, 'nofollow_member'=>$nofollow_member, 'guest_member'=>$guest_member]
        ];

        $result = [];
        $result['member_list'] = $member_list;
        $result['search'] = $search;
        $result['page'] = $page;
        $result['idsite'] = session('idsite');
        $result['statistical'] = $statistical;
        return $result;

    }

    public function order($request)
    {
        $Search_arr=[];
        $Search_arr['idsite'] = session('idsite');
        $Search_arr['fiduser']=$request['userid'];
        if($request['state']==11)
        {
            $Search_arr['state']=1;
            $Search_arr['ischarge']=2;
        }
        else if($request['state']==6)
        {
            $Search_arr['state']=array('in','6,7');
        }
        else
        {
            $Search_arr['state']=$request['state'];
        }

        $count = db('order')->where($Search_arr)->count();
        $page = new Page($count, PAGE_SIZE);
        $data = db('order')->where($Search_arr)->order('id desc')->limit($page->firstRow . ',' . $page->pageSize)->select();

        $arr = array();
        $arr['pager'] = $page;
        $arr['data'] = $data;
        return $arr;


    }
    public function comment($request)
    {
        $Search_arr=[];
        $Search_arr['idsite'] = session('idsite');
        $Search_arr['iduser']=$request['userid'];
        $Search_arr['flag']=$request['flag'];

        if(!empty($request['show']) && $request['show']!=0)
        {
            $Search_arr['show']=$request['show'];
        }

        $count = db('comment')->where($Search_arr)->count();
        $page = new Page($count, PAGE_SIZE);
        $data = db('comment')->where($Search_arr)->order('id desc')->limit($page->firstRow . ',' . $page->pageSize)->select();

        $arr = array();
        $arr['pager'] = $page;
        $arr['data'] = $data;
        return $arr;
    }
    public function collection($request)
    {
        $Search_arr=[];
        $Search_arr['idsite'] = session('idsite');
        $Search_arr['userid']=$request['userid'];
        $Search_arr['flag']=$request['flag'];

        $count = db('collection')->where($Search_arr)->count();
        $page = new Page($count, PAGE_SIZE);
        $data = db('collection')->where($Search_arr)->order('id desc')->limit($page->firstRow . ',' . $page->pageSize)->select();

        $arr = array();
        $arr['pager'] = $page;
        $arr['data'] = $data;
        return $arr;
    }


    public function statistical_order($userid)
    {
       // '订单状态:1.起草,待审核 2.已报名,审核不通过 3.已报名,已审批，4.已报名,已付款，5.已报名,退款中，6已报名，部分退款，7已报名，已退款，8.已报名,退款不通过，9.删除 10.已取消',
        $data=[];
        $data[1]=0;
        $data[2]=0;
        $data[3]=0;
        $data[4]=0;
        $data[5]=0;
        $data[6]=0;
        $data[7]=0;
        $data[8]=0;
        $data[9]=0;
        $data[10]=0;
        $data[11]=0;

        $result = db('order')->field("state,count(state) as vcount ")->where(array('fiduser'=>$userid))->group("state")->select();
        $data[11]=db("order")->where(array('fiduser'=>$userid,'ischarge'=>2,'state'=>1))->count();
        foreach ($result as $k=>$vo)
        {
            $data[$vo['state']]=$vo['vcount'];
        }
        $data[1]=$data[1]-$data[11];
        return $data;
    }
    public function statistical_comment($userid)
    {
        $data=[];
        $data[1]=0; //文章,显示
        $data[2]=0; //文章，屏蔽
        $data[3]=0; //活动，显示
        $data[4]=0; //活动，屏蔽
        $result = db('comment')->field("flag,`show`,count(flag) as vcount ")->where(array('iduser'=>$userid))->group("flag,`show`")->select();
        foreach ($result as $k=>$vo)
        {
            if($vo['flag']==2)
            {
                if($vo['show']==2)
                {
                    $data[4]=$data[4]+$vo['vcount'];
                }
                else
                {
                    $data[3]=$data[3]+$vo['vcount'];
                }
            }
            else
            {
                if($vo['show']==2)
                {
                    $data[2]=$data[2]+$vo['vcount'];
                }
                else
                {
                    $data[1]=$data[1]+$vo['vcount'];
                }
            }

        }

        return $data;

    }
    public function statistical_collection($userid)
    {
        $data=[];
        $data[1]=0; //文章
        $data[2]=0; //活动
        $result = db('collection')->field("flag,count(flag) as vcount ")->where(array('userid'=>$userid))->group("flag")->select();
        foreach ($result as $k=>$vo)
        {
            $data[$vo['flag']]=$vo['vcount'];
        }
        return $data;
    }
    public function login_log($userid,$top=10)
    {
        $result=db('login_log')->where(array('userid'=>$userid))->limit(0,$top)->order('createtime desc')->select();
        return $result;
    }



    //用户增，改跳转页面
    public function member_deal($data){

        $nodename = $this->node_list(); //获得节点列表
        if($data['action'] == 'add'){
            $memberinfo = '';
            $type = 1;
            $memberid = 0;
        }
        if($data['action'] == 'edit' || $data['action'] == 'view'){
            $memberid = $data['idmember'];
            $memberinfo = db('member')->where(array('idmember'=>$memberid))->find();//拿到一条内容的信息
            foreach ($memberinfo as $key=>$value){
                if(strstr($value,'|')){
                    $memberinfo[$key] =  explode("|",$value);
                }
                else if($key=='childage1' || $key=='childage2' || $key=='childage3')
                {
                    $memberinfo[$key] =$value<100?"":date('Y-m-d',$value);
                }
                else{
                    $memberinfo[$key] =$value;
                }
            }
            $type = 2;
        }

        $result['memberinfo'] = $memberinfo;
        $result['nodename'] = $nodename;
        $result['memberinfo'] = $memberinfo;
        $result['memberinfo'] = $memberinfo;
        $result['type'] = $type;
        $result['memberid'] = $memberid;
        return $result;
    }

    //用户增，改提交地址
    public function member_post($data){

        foreach ($data as $key=>$value){
            if(is_array($value) && !empty($value)){
                $va[$key] = implode('|',$value);
            }
            else if($key=='childage1' || $key=='childage2' || $key=='childage3')
            {
                $va[$key] =$value==""?0:strtotime($value);
            }
            else{
                $va[$key] = $value;
            }
        }

        if ($va['action'] == 'add') {
            $bool = db('member')->strict(false)->insert($va);
        } else{
            if(isset($data["iduser"]) && (int)$data["iduser"] >0){
                $member = db('member')->where(array('idmember'=>$va['idmember']))->find();
                $old_iduser = $member["iduser"];
                if($old_iduser != (int)$data["iduser"]){
                    $this -> sendmsg(array("username"=>$member["nickname"],"iduser"=>(int)$data["iduser"],"idmember"=>$va['idmember']));
                }
            }
            $bool = db('member')->where(array('idmember'=>$va['idmember']))->strict(false)->update($va);

        }

        $result['bool'] = $bool;
        $result['idsite'] = session('idsite');
        return $result;
    }


    public function sendmsg($data)
    {
        $account = db('account')->where("idaccount",$data["iduser"])->find();
        $touser = $account["openid"];
        $chrname = $account["chrname"]?:"";
        if(empty($touser)){
            return false;
        }
        $arrData=[];
        $arrData['first']=array("value"=>"你好，".$chrname."，".session("UserName")."给你分配了一个用户，请及时跟进！");
        $arrData['keyword1']=array("value"=>$data['username']);
        $arrData['keyword2']=array("value"=>date("Y年m月d日 H:i"));
        $arrData['remark']=array("value"=>"更多用户信息可点击查看!");

        $key=getNumber();
        $arr=[];
        $template_key = getWxTemplateId("OPENTM206165551",session('idsite'));
        $arr['Template_key']=$template_key;//"vb7FWt57VW9LAmkitWP4Bwtp4bw2gxdcOHXNgyBQ4aY";
        $arr['dataid']= 0;
        $arr['data']=json_encode($arrData);
        $arr['url']= ROOTURL."/admin/member/deal/idmember/".$data["idmember"]."/action/view";
        $arr['touser']= $touser;
        $arr['inttype']=2;
        $arr['inttype1']=1;
        $arr['username']=$data['username'];
        $arr['userid']= 0;
        $arr['state']=1;
        $arr['createtime']=time();
        $arr['key']= $key;
        $arr['idsite']= session('idsite');
        $arr['ip']=getip();
        $bl=db("sendmsg")->insert($arr);

        if($bl){
            send_msg($key,getSiteCode(session('idsite')));
        }
        return $bl;

    }

    //用户删除
    public function member_del($data){
        $bool = db('member')->where('idmember=:idmember',['idmember'=>$data['id']])->delete();
        return $bool;
    }

    //选中删除
    public function del_checked($data){
            if(strstr($data['id'],',')){
                $id = explode(',',$data['id']);
                for ($i=0;$i<count($id);$i++){
                    $bool = db('member')->where('idmember',$id[$i])->delete();
                }
            }else{
                $bool = db('member')->where('idmember',$data['id'])->delete();
            }
            return $bool;
    }
    //选中删除
    public function ismanage($data){
        $id=$data['id'];
        $flag=$data['flag'];
        $bool = db('member')->where('idmember',$id)->update(array("ismanage"=>$flag));
        return $bool;
    }
    //判断是否惟一
    public function member_test($data){
        $value = $data['value'];
        $modelid = $data['modelid'];
        $field = $data['fieldname'];
        $action = $data['action'];
        $map[$field] = ['eq',$value];
        $map['idmodel'] = ['eq',$modelid];
        //获取该模型下所有的内容
        if($action == 2){  //编辑
            $contentid = $data['contentid'];
            $map['idmember'] = ['neq',$contentid];
        }
        $count = db('member')->where($map)->count();
        return $count;
    }
    public function followup($data)
    {
        $search=[];
        $search['userid']=empty($data['userid'])?"":$data['userid'];  //最近来访标记
        $search['vflag']=empty($data['vflag'])?"":$data['vflag'];  //最近来访标记
        $search['vstime']=empty($data['vstime'])?"":$data['vstime'];  //最近来访 开始时间
        $search['vetime']=empty($data['vetime'])?"":$data['vetime'];  //最近来访 结束时间

        $memberid = isset($data["memberid"])?$data["memberid"]:0;

        $map=[];
        $map['idsite'] = session('idsite');
        if(!empty($search['vflag'])){
            $vstime='';
            $vetime='';
            if($search['vflag']==1)
            {
                $vstime=strtotime(date('Y-m-d',time()));
                $vetime=strtotime("1 day",$vstime);
            }
            else if($search['vflag']==2)
            {
                $vstime=strtotime(date('Y-m-d',time()));
                $vetime=strtotime("7 day",$vstime);
            }
            else if($search['vflag']==3)
            {
                $vstime=strtotime(date('Y-m-d',time()));
                $vetime=strtotime("30 day",$vstime);
            }
            else if($search['vflag']==4 && !empty($search['vstime']) && !empty($search['vetime']))
            {
                $vstime=strtotime($search['vstime']);
                $vetime=strtotime("1 day",strtotime($search['vetime']));
            }
            if($vstime!='' && $vetime!='')
            {
                $map['uptime']=array(array('gt',$vstime),array('lt',$vetime));
            }
        }
        if(!empty($search['userid'])){$map['userid'] = $search['userid'];} //所属商务

        if(!empty($memberid)){$map['memberid'] = $memberid;}

        $count = db('followup')->where($map)->count();
        $page = new Page($count, PAGE_SIZE);
        $data = db('followup')->where($map)->order('id desc')->limit($page->firstRow . ',' . $page->pageSize)->select();

        $arr = array();
        $arr['pager'] = $page;
        $arr['data'] = $data;
        $arr['search'] = $search;

        return $arr;

    }
    public function followuppost($data)
    {
        if(empty($data['uptime']))
        {$data['uptime']=0;}
        else
        {
            $data['uptime']=strtotime($data['uptime']);
        }
        $data['createtime']=time();
        foreach ($data as $key=>$value){
            if(is_array($value) && !empty($value)){
                $va[$key] = implode('|',$value);
            }
            else{
                $va[$key] = $value;
            }
        }
        if ($va['action'] == 'add') {
            $va['idsite'] = session('idsite');
            $bool = db('followup')->strict(false)->insert($va);
        } else{
            $bool = db('followup')->where(array('id'=>$va['id']))->strict(false)->update($va);
        }
        db('member')->where(array('idmember'=>$va['memberid']))->strict(false)->update(array('followuptime'=>$va['uptime']));


        $result['bool'] = $bool;
        $result['idsite'] = session('idsite');
        return $bool;
    }
    /**
     * 获得指定分类下的子分类的数组
     * @access  public
     * @param   int     $no_node_id     要排除的栏目ID

     * @return  mix
     */
    public function node_list($no_node_id=0)
    {
        global $cms_node_g, $cms_node2_g;
        $sql = "SELECT * FROM  cms_node ORDER BY idorder ASC ";
        $cms_node_g = db('node')->query($sql);

        $cms_node_g = convert_arr_key($cms_node_g, 'nodeid');

        foreach ($cms_node_g AS $key => $value)
        {
            if($no_node_id==$value['nodeid'])
                continue;
            if($value['level'] == 0)
            {
                $this->get_cat_tree($no_node_id,$value['nodeid']);
            }
        }

        return $cms_node2_g;
        // $this->display();
    }

    /**
     * 获取指定id下的 所有分类
     * @global type $cms_node_g 所有商品分类
     * @param type $id 当前显示的 菜单id
     * @return 返回数组 Description
     */
    public function get_cat_tree($no_node_id,$id)
    {
        global $cms_node_g, $cms_node2_g;
        $cms_node2_g[$id] = $cms_node_g[$id];
        foreach ($cms_node_g AS $key => $value){
            if($no_node_id==$value['nodeid'])
                continue;
            if($value['parentid'] == $id)
            {
                $this->get_cat_tree($no_node_id,$value['nodeid']);
                $cms_node2_g[$id]['have_son'] = 1; // 还有下级
            }
        }
    }


    /**
 * 用户报表列表
 */
    public function member_report_list($data)
    {
        if (!isset($data["time_range"])) {
            return false;
        }
        $start_date = $end_date = "";
        switch ($data["time_range"]) {
            case "last_week":

                $time = time();
                $last_monday = date('Y-m-d', strtotime('-1 monday', $time));
                $last_sunday = date('Y-m-d', strtotime('-1 sunday', $time));
                if ($last_monday > $last_sunday) {
                    $last_monday = date('Y-m-d', strtotime('-2 monday', $time));
                }
                $start_date = $last_monday;
                $end_date = $last_sunday;
                break;
            case "this_week":
                $start_date = date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600));
                $end_date = date('Y-m-d', strtotime("today"));
                break;
            case "last_month":
                $start_date = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m', time()) . '-01 00:00:00')));
                $end_date = date('Y-m-d', strtotime(date('Y-m', time()) . '-01 00:00:00') - 86400);
                break;
            case "this_month":
                $start_date = date('Y-m-d', strtotime(date('Y-m', time()) . '-01 00:00:00'));
                $end_date = date('Y-m-d', strtotime("today"));
                break;
            case "custom":
                $start_date = $data["begintime"];
                $end_date = $data["endtime"];
                break;
        }
        if (empty($start_date) || empty($end_date)) {
            return false;
        }
        $member_follow = $this->get_member_follow_num($start_date, $end_date);
        $member_unfollow = $this->get_member_unfollow_num($start_date, $end_date);
        $member_visitor = $this->get_member_visitor_num($start_date, $end_date);
        $member_count = $this->get_member_count($start_date, $end_date);
        $visit_member_count = $this->get_visitor_member_count($start_date, $end_date);
        $list = array();

        $lasting_day = round((strtotime($end_date . " 23:59:59") - strtotime($start_date)) / (3600 * 24));
        $totalfollow = $totalunfollow = $totalvisitor = $totalincrease = 0;
        for ($i = ($lasting_day - 1); $i >= 0; $i--) {
            $date = date("Y-m-d", (strtotime($end_date . " 23:59:59") - (3600 * 24 * $i)));
            $list[$date]["follow"] = isset($member_follow[$date]) ? $member_follow[$date] : 0;
            $list[$date]["unfollow"] = isset($member_unfollow[$date]) ? $member_unfollow[$date] : 0;
            $list[$date]["visitor"] = isset($member_visitor[$date]) ? $member_visitor[$date] : 0;
            $list[$date]["increase"] = $list[$date]["follow"] - $list[$date]["unfollow"];
            $member_count += $list[$date]["increase"];
            $visit_member_count += $list[$date]["visitor"];
            $list[$date]["count"] = $member_count;
            $list[$date]["visit_count"] = $visit_member_count;
            $totalfollow += $list[$date]["follow"];
            $totalunfollow += $list[$date]["unfollow"];
            $totalvisitor += $list[$date]["visitor"];
            $totalincrease += $list[$date]["increase"];
        }
        if($list) {
            $list["汇总"] = array("count"=>$member_count,"visit_count"=>$visit_member_count,"follow" => $totalfollow,"unfollow" => $totalunfollow,"visitor" => $totalvisitor,"increase" => $totalincrease);
        }
        return $list;
    }

    // 获取用户信息
    public function get_member($idmember){
        return db('member')->where('idmember',$idmember)->find();
    }
    // 赠送积分
    public function membergiving($data){
        Db::startTrans();
        try{
            $member =  Db::name('member')->where('idmember',$data['idmember'])->find();
            Db::name('member')->where('idmember',$data['idmember'])->setInc('integral', $data['integral']);
            Db::name('member_integral_record')->where('idmember',$data['idmember'])->insert([
                'member_id' => $data['idmember'],
                'userimg' => !empty($member['userimg']) ? $member['userimg'] : '',
                'chrname' => !empty($member['chrname']) ? $member['chrname'] : '',
                'nickname' => !empty($member['nickname']) ? $member['nickname'] : '',
                'category_id' => 6,
                'integral' => !empty($data['integral']) ? intval($data['integral']) : 0,
                'integral_rmark' => !empty($data['remark']) ? $data['remark'] : '',
                'create_time' => time()
            ]);
            $bool = true;
            Db::commit();
        }catch(Exception $e){
            $bool = false;
            Db::rollback();
        }
        return $bool;
    }
    // 获取二维码列表
    public function get_qrcode_list($data){
        $idsite = session("idsite");

        $map = ['idsite' => $idsite];
        if($data['qrcode_name']){
            $map['qrcode_name'] = ['like','%'.$data['qrcode_name'].'%'];
        }

        $totalRecord = db('qrcode_manage')->where($map)->count();
        $result = db('qrcode_manage')->where($map)->page($data['p'],PAGE_SIZE)->order('id desc')->select();        
        $page = new Page($totalRecord, PAGE_SIZE);
        
        return ['page' => $page, 'data'=> $result];
    }
    // 获取二维码详情
    public function get_qrcode_info($id){
        return db('qrcode_manage')->where('id',$id)->find();
    }
    // 二维码增加或者修改
    public function qrcode_modi($data){
        $id = isset($data['id']) ? $data['id'] : 0;
        $action = isset($data['action']) ? $data['action'] : 0;
        $idsite = session('idsite');
        $scene_str = getSceneStr();

        // 获取配置
        $config = db('site_manage')->field('appid,appsecret')->where('id',$idsite)->find();
        $api = new \think\wx\Api([
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ]);

        // 生成二维码
        $ticket  = $api->get_qrcode_url_by_str($scene_str);

        $data = [
            'idsite' => $idsite,
            'scene_str' => $scene_str,
            'ticket' => $ticket,
            'qrcode_name' => isset($data['qrcode_name']) ? $data['qrcode_name'] : '',
            'qrcode_desc' => isset($data['qrcode_desc']) ? $data['qrcode_desc'] : '',
            'create_time' => time()
        ];

        if($action == 'add'){
            $result = db('qrcode_manage')->insert($data);
        }else{
            $result = db('qrcode_manage')->where('id',$id)->update($data);
        }

        return $result;
    }
    // 二维码删除
    public function qrcode_del($data){
        $id = isset($data['id']) ? $data['id'] : 0;

        $result = false;
        if(strstr( $data['id'],',')){
            $result = db('qrcode_manage')->whereIn('id',explode(",",$id))->delete();
        }else{
            $result = db('qrcode_manage')->where('id',$id)->delete();
        }

        return $result;
    }
    // 二维码选择
    public function qrcode_select($data){
        $idsite = session('idsite');
        $totalRecord = db('qrcode_manage')->where('idsite',$idsite)->count();
        $result = db('qrcode_manage')->where('idsite',$idsite)->page($data['p'],PAGE_SIZE)->select();
        $page = new Page($totalRecord, PAGE_SIZE);
        
        return ['page' => $page, 'data'=> $result];
    }
    /**
     * 通过活动日期来获取用户数量
     * @param $start_date
     * @return int|string
     */
    private function get_member_count($start_date, $end_date)
    {
        $idsite = session('idsite');
        //获取指定日期前关注的用户量
        $where_arr = array();
        $where_arr["idsite"] = $idsite;
        $where_arr["intstate"] = 1;
        $where_arr["dtsubscribetime"] = ['between',[strtotime($start_date),strtotime($end_date." 23:59:59")]];
        $dtsubscribe_num = db("member_log")->where($where_arr)->count();
        //获取指定日期前取消关注的用户量
        $where_arr = array();
        $where_arr["idsite"] = $idsite;
        $where_arr["intstate"] = 2;
        $where_arr["dtunsubscribetime"] = ['between',[strtotime($start_date),strtotime($end_date." 23:59:59")]];
        $dtunsubscribe_num = db("member_log")->where($where_arr)->count();
        return ($dtsubscribe_num-$dtunsubscribe_num);
    }

    /**
     * 通过活动日期来获取用户数量
     * @param $start_date
     * @return int|string
     */
    private function get_visitor_member_count($start_date, $end_date)
    {
        $idsite = session('idsite');
        //获取指定日期前的游客
        $where_arr = array();
        $where_arr["idsite"] = $idsite;
        $where_arr["intstate"] = 3;
        $where_arr["dtcreatetime"] = ['between',[strtotime($start_date),strtotime($end_date." 23:59:59")]];
        $visitor_num = db("member_log")->where($where_arr)->count();
        //获取指定日期前变成关注/未关注的游客数量
        $where_arr = array();
        $where_arr["idsite"] = $idsite;
        $where_arr["old_intstate"] = 3;
        $where_arr["dtcreatetime"] = ['between',[strtotime($start_date),strtotime($end_date." 23:59:59")]];
        $old_visitor_num = db("member_log")->where($where_arr)->count();
        return ($visitor_num-$old_visitor_num);
    }

    /**
     * 获取用户关注数
     */
    private function get_member_follow_num($start_date,$end_date)
    {
        $idsite = session('idsite');
        $where = "idsite =$idsite and intstate =1 and dtsubscribetime>=".strtotime($start_date." 00:00:00")." and dtsubscribetime<=".strtotime($end_date." 23:59:59");
        $sql = "select FROM_UNIXTIME(dtsubscribetime,'%Y-%m-%d') as d,count(*) as num from cms_member_log where ".$where." group by d";
        $member_follow = db("member_log")->query($sql);
        $return = array();

        foreach ($member_follow as $member){
            $return[$member["d"]] = $member["num"];
        }
        return $return;
    }

    /**
     * 获取用户未关注数
     */
    private function get_member_unfollow_num($start_date,$end_date)
    {
        $idsite = session('idsite');
        $where = "idsite =$idsite and intstate = 2 and dtunsubscribetime>=".strtotime($start_date." 00:00:00")." and dtunsubscribetime<=".strtotime($end_date." 23:59:59");
        $sql = "select FROM_UNIXTIME(dtunsubscribetime,'%Y-%m-%d') as d,count(*) as num from cms_member_log where ".$where." group by d";
        $member_follow = db("member_log")->query($sql);
        $return = array();

        foreach ($member_follow as $member){
            $return[$member["d"]] = $member["num"];
        }
        return $return;
    }

    /**
     * 获取新增游客数量
     * @param $start_date
     * @param $end_date
     * @return array
     */
    private function get_member_visitor_num($start_date,$end_date)
    {
        $return = array();
        try {
            $idsite = session('idsite');
            $where = "idsite =$idsite and intstate=3 and dtcreatetime>=" . strtotime($start_date . " 00:00:00") . " and dtcreatetime<=" . strtotime($end_date . " 23:59:59");
            $sql = "select FROM_UNIXTIME(dtcreatetime,'%Y-%m-%d') as d,count(*) as num from cms_member_log where " . $where . " group by d";
            $member_follow = db("member_log")->query($sql);

            $idsite = session('idsite');
            $where = "idsite =$idsite and old_intstate=3 and dtcreatetime>=" . strtotime($start_date . " 00:00:00") . " and dtcreatetime<=" . strtotime($end_date . " 23:59:59");
            $sql = "select FROM_UNIXTIME(dtcreatetime,'%Y-%m-%d') as d,count(*) as num from cms_member_log where " . $where . " group by d";
            $old_visitor = db("member_log")->query($sql);

            foreach ($member_follow as $member) {
                $return[$member["d"]] = $member["num"];
            }

            foreach ($old_visitor as $member){
                if(!isset($return[$member["d"]])){ $return[$member["d"]] = 0;}
                $return[$member["d"]] -= $member["num"];
            }
        } catch (Exception $ex) {
        }
        return $return;
    }
}