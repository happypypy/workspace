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
                   // $memberinfo[$key] =  explode("|",$value);
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
        //是否开启分销结算
        if(isset($data['is_balance'])){
            $data['is_balance'] = intval($data['is_balance']);
            $data['spokesman_time'] = date('Y-m-d H:i:s',time());
        }else{
            $data['is_balance'] = 2;
        }
        if($data['spokesman_grade'] == 0){
            $data['is_balance'] = 2;
        }
        //如果是二级的话
        if($data['spokesman_grade'] == 2){
            //查询选择的一级代言人的信息
            $one_spokesman = db('member')->where(['idmember'=>$data['user_id'],'idsite'=>$data['idsite']])->find();
            //上级代言人
            $data['parent_user_id'] = $one_spokesman['idmember'];
            //上级姓名
            $data['parent_u_chrname'] = $one_spokesman['u_chrname'];
            //上级手机号
            $data['parent_u_chrtel'] = $one_spokesman['u_chrtel'];
            //上级的昵称
            $data['parent_nick_name'] = $one_spokesman['nickname'];
            //如果一级存在上级代言信息
            if($one_spokesman['parent_user_id']){
                //上上级代言人
                $data['top_parent_user_id'] = $one_spokesman['parent_user_id'];
                //上上级姓名
                $data['top_parent_u_chrname'] = $one_spokesman['parent_u_chrname'];
                //上上级手机号
                $data['top_parent_u_chrtel'] = $one_spokesman['parent_u_chrtel'];
                //上上级的昵称
                $data['top_parent_nick_name'] = $one_spokesman['parent_nick_name'];
            }
        }
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
        $member_visitor = $this->get_member_visitor_num($start_date, $end_date,'old');
        $member_count = $this->get_member_count($start_date, $end_date);
        $visit_member_count = $this->get_visitor_member_count($start_date, $end_date);
        $history_follow = $this->get_history_follow($start_date);   // 历史所有关注总数
        $history_visit = $this->get_history_visit($start_date);     // 历史游客总数
        $list = array();

        $history_follow_count = 0;
        $history_visit_count = 0;
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
            $history_visit_count += $list[$date]["visitor"];
            $history_follow_count += $list[$date]["follow"];
            $list[$date]["count"] = $history_follow + $history_follow_count;
            $list[$date]["visit_count"] = $history_visit + $history_visit_count;
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
                'siteid' => session('idsite'),
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
        $qr_scene_str = getSceneStr();

        // 获取配置
        $config = db('site_manage')->field('appid,appsecret')->where('id',$idsite)->find();
        $api = new \think\wx\Api([
            'appId' => trim($config['appid']),
            'appSecret'    => trim($config['appsecret']),
        ]);

        // 生成二维码
        $ticket  = $api->get_qrcode_url_by_str($qr_scene_str);

        $data = [
            'idsite' => $idsite,
            'qr_scene_str' => $qr_scene_str,
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
     * 获取历史用户
     *
     * @return void
     * @author Chenjie
     * @Date 2019-08-02 13:59:12
     */
    public function get_history_member($param){
        $idsite = session('idsite');
        $map = [
            'siteid'=>$idsite, 
            'status'=>$param['status']
        ];
        $totalRecord = db('history_member')->where($map)->count();
        $result = db('history_member')->where($map)->page($param['p'],PAGE_SIZE)->select();
        $page = new Page($totalRecord, PAGE_SIZE);
        return ['page' => $page, 'datalist'=> $result];
    }
    /**
     * 重新匹配
     *
     * @param int $id
     * @return void
     * @author Chenjie
     * @Date 2019-08-02 14:11:10
     */
    public function again_matching($id){
        $result = 0;
        Db::startTrans();
        try{
            $success_number = 0;
            // 全部重新匹配
            if($id == 0){
                $history_member_list = db('history_member')->where('status',0)->select();
                foreach($history_member_list as $member){
                    $member = db('member')->where('mobile',$member['mobile'])->find();
                    if($member){
                        $result = db('history_member')->where('id',$member['id'])->setField('status',1);
                        if($result){
                            $success_number++;
                        }
                    }
                }
            }
            // 重新匹配
            else{
                $history_member = db('history_member')->where('id',$id)->find();
                $member = db('member')->where('chrtel',$history_member['mobile'])->find();
                if($member){
                    $success_number = db('history_member')->where('id',$id)->setField('status',1);
                }
            }
            Db::commit();
            $result = $success_number;
        }catch(Exception $e){
            DB::rollback();
        }
        return $result;
    }
    /**
     * 根据id获取历史会员
     *
     * @return void
     * @author Chenjie
     * @Date 2019-08-02 14:52:54
     */
    public function get_history_member_by_id($id){
        $history_member = [];
        if($id){
            $history_member = db('history_member')->where('id',$id)->find();
        }else{
            $history_member = db('history_member')->where(['siteid' => session('idsite'),'status' => 0])->select();
        }
        return $history_member;
    }
    /**
     * 发送短信
     *
     * @return void
     * @author Chenjie
     * @Date 2019-08-02 14:46:26
     */
    public function send_message(){
        try
        {
            $params = Request::instance()->param();
            $mobile_list = !empty($params['mobile']) ? (strstr($params['mobile'],",") ? explode(",",$params['mobile']) : $params['mobile']) : [];
            $data = [];
            $msgConfig = config('msg_config');
            $sendNum = is_array($mobile_list) ? count($mobile_list) : 1;
            $create_time = date('Y-m-d H:i:s');
            $mode = [
                "idsite" => session('idsite'),
                "idaccount" => session('AccountID'),
                'username' => session('UserName'),
                "type" => 1,
                "create_time" => $create_time,
                "send_time" => isset($params['send_time']) ? $params['send_time'] : $create_time,
                "ip" => getip()
            ];

            // 循环生成短信任务
            if(is_array($mobile_list)){
                foreach($mobile_list as $mobile){
                    $mode['mobile'] = $mobile;
                    // 验证手机合法
                    if(!checkMobile($mode['mobile']))
                    {
                        throw new Exception('不是合法的手机号', 1);
                    }
                    $history_member = db('history_member')->where(['siteid'=>session('idsite'),'mobile'=>$mobile])->find();
                    $content = str_replace('{realname}', $history_member['real_name'], $params['content']);
                    $content = str_replace('{mobile}', $mobile, $content);
                    $content = str_replace('{idcard}', $history_member['id_card'], $content);
                    $mode['content'] = $content;
                    // 验证内容是否存在违禁词
                    if(isBadWord($mode['content']))
                    {
                        throw new Exception('含有违禁词', 1);
                    }
                    // 验证短信字数是否超限
                    if(mb_strlen($mode['content']) > $msgConfig['max_text_len'])
                    {
                        throw new Exception('短信内容超过' . $msgConfig['max_text_len'], 1);
                    }
                    $data[] = $mode;
                }
            }else{
                $mode['mobile'] = $mobile_list;
                $mobile = $mode['mobile'];
                // 验证手机合法
                if(!checkMobile($mode['mobile']))
                {
                    throw new Exception('不是合法的手机号', 1);
                }

                $history_member = db('history_member')->where(['siteid'=>session('idsite'),'mobile'=>$mobile])->find();
                $content = str_replace('{realname}', $history_member['real_name'], $params['content']);
                $content = str_replace('{mobile}', $mobile, $content);
                $content = str_replace('{idcard}', $history_member['id_card'], $content);
                $mode['content'] = $content;
                // 验证内容是否存在违禁词
                if(isBadWord($mode['content']))
                {
                    throw new Exception('含有违禁词', 1);
                }
                // 验证短信字数是否超限
                if(mb_strlen($mode['content']) > $msgConfig['max_text_len'])
                {
                    throw new Exception('短信内容超过' . $msgConfig['max_text_len'], 1);
                }
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
    private function get_member_visitor_num($start_date,$end_date,$type)
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
            //2019年8月21,如果是新的报表,就不减
            if($type == 'old'){
                foreach ($old_visitor as $member){
                    if(!isset($return[$member["d"]])){ $return[$member["d"]] = 0;}
                    $return[$member["d"]] -= $member["num"];
                }
            }
        } catch (Exception $ex) {
        }
        return $return;
    }

    /**
     * 获取历史关注用户总数
     *
     * @param int $start_date 开始时间
     * @return void
     * @author Chenjie
     * @Date 2019-07-09 17:52:52
     */
    private function get_history_follow($start_date){
        // 历史关注总数
        $history_follow = db('member_log')
                            ->where([
                                'idsite'=>session('idsite'), 
                                'intstate' => 1, 
                                'dtcreatetime'=>['<',strtotime($start_date)]
                            ])->group("idmember")
                            ->count();
        // 历史取消关注总数
        $history_unfollow = db('member_log')
                ->where([
                    'idsite'=>session('idsite'), 
                    'intstate' => 2, 
                    'dtcreatetime'=>['<',strtotime($start_date)]
                ])->group("idmember")
                ->count();

        return $history_follow - $history_unfollow;
    }
    /**
     * 获取历史游客总数
     *
     * @param int $start_date 开始时间
     * @return void
     * @author Chenjie
     * @Date 2019-07-09 17:52:27
     */
    private function get_history_visit($start_date){
        // 历史游客总数
        $history_visit = db('member_log')->where([
                            'idsite' => session('idsite'),
                            'intstate' => 3, 
                            'dtcreatetime'=>['<',strtotime($start_date)]
                        ])->count();

        // 历史游客转关注总数
        $history_visit_follow = db('member_log')->where([
            'idsite' => session('idsite'),
            'intstate' => 1, 
            'old_intstate' => 3, 
            'dtcreatetime'=>['<',strtotime($start_date)]
        ])->count();
        return $history_visit - $history_visit_follow;
    }

    /**
     * 获取用户游客转关注的数量
     */
    private function get_member_visit_to_follow_num($start_date,$end_date)
    {
        $idsite = session('idsite');
        $where = "idsite =$idsite and intstate =1 and old_intstate = 3 and dtsubscribetime>=".strtotime($start_date." 00:00:00")." and dtsubscribetime<=".strtotime($end_date." 23:59:59");
        $sql = "select FROM_UNIXTIME(dtsubscribetime,'%Y-%m-%d') as d,count(*) as num from cms_member_log where ".$where." group by d";
//        halt($sql);
        $member_follow = db("member_log")->query($sql);
        $return = array();

        foreach ($member_follow as $member){
            $return[$member["d"]] = $member["num"];
        }
        return $return;
    }

    /**
     * 用户增长报表
     */
    public function new_member_report_list($data)
    {
        //如果没有开始时间,那么默认就是一月前
        if(!isset($data["begintime"]) || empty($data["begintime"]) || (!\DateTime::createFromFormat('Y-m-d',$data['begintime']))){
            $start_date = date('Y-m-d',strtotime('-1 month -1 day'));
        }else{
            //搜索的结束时间
            $start_date = $data["begintime"];
        }
        //如果没有结束时间,那么默认就是到昨天
        if(!isset($data["endtime"]) || empty($data["endtime"]) || (!\DateTime::createFromFormat('Y-m-d',$data['endtime']))){
            $end_date = date('Y-m-d',strtotime('-1 day'));
        }else{
            //搜索的开始时间
            $end_date = $data["endtime"];
        }
        $result['search'] = ['begintime'=>$start_date,'endtime'=>$end_date];
        //昨天
        $result['search']['yesterday'] = date('Y-m-d',strtotime('yesterday'));
        //周
        $result['search']['yesterday_week'] =  date('Y-m-d',strtotime(" - 8 day"));
        //月
        $result['search']['yesterday_month'] =  date('Y-m-d',strtotime(" - 1 month - 1 day"));

//        halt($start_date.'--'.$end_date);
        $result['echart'] = [];
        //调用公共的获取用户的数据,用来作为图表的数据
        $result['list'] = $this->get_common_member_data($start_date,$end_date);
        // halt($result['list']);
//         //获取昨日的数据
        $result['yesterday_user_data'] = $result['list'][date('Y-m-d',strtotime('yesterday'))];
        // halt($result['yesterday_user_data']);
// //        halt($result);
        $result['yesterday_week_user_data'] = $result['list'][date('Y-m-d',strtotime(" - 8 day"))];
        $result['yesterday_month_user_data'] = $result['list'][date('Y-m-d',strtotime(" - 1 month - 1 day"))];
//        halt($result['list']);
        #region  如果有数据 那么处理封装成图表要的数据格式
        if($result['list']){
            //x轴
            $x_val = [];
            $follow = [];//新关注
            $unfollow = [];//取消关注
            $visitor = [];//新增游客
            $visit_to_follow = [];//游客转关注
            $increase = [];//净增关注
            $count = [];//累积关注
            $visit_count = [];//累积游客
            foreach ($result['list'] as $key=>$value){
                $x_val[] = $key;
                //每一个系列对应的x轴的值
                $follow[] = $value['follow'];
                $unfollow[] = $value['unfollow'];
                $visitor[] = $value['visitor'];
                $visit_to_follow[] = $value['visit_to_follow'];
                $increase[] = $value['increase'];
                $count[] = $value['count'];
                $visit_count[] = $value['visit_count'];
            }
            //因为要将图表要的数据转换为json数组的格式,每一个系列对应的x轴的值都是个数组
            $result['echart']['follow'] = [[''],$x_val,[$follow]];
            $result['echart']['unfollow'] = [[''],$x_val,[$unfollow]];
            $result['echart']['visitor'] = [[''],$x_val,[$visitor]];
            $result['echart']['visit_to_follow'] = [[''],$x_val,[$visit_to_follow]];
            $result['echart']['increase'] = [[''],$x_val,[$increase]];
            $result['echart']['count'] = [[''],$x_val,[$count]];
            $result['echart']['visit_count'] = [[''],$x_val,[$visit_count]];
        }
        #endregion
        $result['echart'] = json_encode($result['echart'],JSON_UNESCAPED_UNICODE);
//        halt($result);
        return $result;
    }

    /**
     * 通过日期的开始和结算时间获取公共的用户增长情况数据
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function get_common_member_data($start_date,$end_date){
        //获取关注的数量
        $member_follow = $this->get_member_follow_num($start_date, $end_date);
        //获取未关注的数量
        $member_unfollow = $this->get_member_unfollow_num($start_date, $end_date);
        //获取游客的数量
        $member_visitor = $this->get_member_visitor_num($start_date, $end_date,'new');
        $member_count = $this->get_member_count($start_date, $end_date);
        $visit_member_count = $this->get_visitor_member_count($start_date, $end_date);
        $history_follow = $this->get_history_follow($start_date);   // 在此之前历史所有关注总数
        $history_visit = $this->get_history_visit($start_date);     // 历史游客总数
        //获取游客转关注数量
        $member_visit_to_follow_num = $this->get_member_visit_to_follow_num($start_date, $end_date);
        $list = array();

        $history_follow_count = 0;
        $total_visit_count = 0;
        $total_visit_to_follow = 0;
        $lasting_day = round((strtotime($end_date.' 23:59:59') - strtotime($start_date)) / (3600 * 24));
        $totalfollow = $totalunfollow = $totalvisitor = $totalincrease = 0;
        for ($i = ($lasting_day - 1); $i >= 0; $i--) {
            $date = date("Y-m-d", (strtotime($end_date.' 23:59:59') - (3600 * 24 * $i)));//减去循环出来的天数
            $list[$date]["follow"] = isset($member_follow[$date]) ? $member_follow[$date] : 0;//新关注
            $list[$date]["unfollow"] = isset($member_unfollow[$date]) ? $member_unfollow[$date] : 0;//取消关注
            $list[$date]["visitor"] = isset($member_visitor[$date]) ? $member_visitor[$date] : 0;//新增的游客
            $list[$date]["visit_to_follow"] = isset($member_visit_to_follow_num[$date]) ? $member_visit_to_follow_num[$date] : 0;//游客转关注数量
            $list[$date]["increase"] = $list[$date]["follow"] - $list[$date]["unfollow"];//净增关注
            $member_count += $list[$date]["increase"];
            $visit_member_count += $list[$date]["visitor"];
            $total_visit_count += $list[$date]["visitor"];
            $total_visit_to_follow += $list[$date]["visit_to_follow"];
            $history_follow_count += $list[$date]["follow"];
            $totalunfollow += $list[$date]["unfollow"];
            $totalvisitor += $list[$date]["visitor"];
            $list[$date]["count"] = $history_follow + $history_follow_count - $totalunfollow;//关注用户总数
            $list[$date]["visit_count"] = $history_visit + $total_visit_count - $total_visit_to_follow;//累积游客总数
            $totalfollow += $list[$date]["follow"];
            $totalincrease += $list[$date]["increase"];
        }
        return $list;
    }

    /**
     * 通过点击数据的数字跳转用户列表页面
     */
    public function get_user_list($data)
    {
        //如果没有结束时间   那么结束时间就是等于开始时间
        if(!isset($data["end_time"]) || empty($data["end_time"])){
            $end_date = $data["start_time"];
        }else{
            //搜索的结束时间
            $end_date = $data["end_time"];
        }
        $start_date = $data["start_time"];
        $type = !isset($data["type"]) || empty($data["type"])?'':$data["type"];//用户增减报表中的状态类型
        $origin = !isset($data["origin"]) || empty($data["origin"])?'':$data["origin"];//来源
        $intsex = !isset($data["intsex"]) || empty($data["intsex"])?'':$data["intsex"];//性别
        $intprovince = !isset($data["intprovince"]) || empty($data["intprovince"])?'':$data["intprovince"];//省份
        $intcity = !isset($data["intcity"]) || empty($data["intcity"])?'':$data["intcity"];//城市
        $age_str = !isset($data["age_str"]) || empty($data["age_str"])?'':$data["age_str"];//年龄
        $intstate = !isset($data["intstate"]) || empty($data["intstate"])?'':$data["intstate"];//用户状态
        $focus_time_str = !isset($data["focus_time_str"]) || empty($data["focus_time_str"])?'':$data["focus_time_str"];//关注时长
        $count_str = !isset($data["count_str"]) || empty($data["count_str"])?'':$data["count_str"];//购买次数
        $order_type = !isset($data["order_type"]) || empty($data["order_type"])?'':$data["order_type"];//订单的类型  看是购买数量还是人数
        $product_id = !isset($data["product_id"]) || empty($data["product_id"])?'':$data["product_id"];//活动id
        //站点id
        $idsite = session('idsite');
        //默认的where条件
        $where = "ml.idsite =$idsite and m.idsite =$idsite and ml.dtsubscribetime>=".strtotime($start_date." 00:00:00")." and ml.dtsubscribetime<=".strtotime($end_date." 23:59:59");
//        halt($where);
        $user_where = [];
        //判断类型  如果是新关注人数
        if($type == 'follow'){
            $where .= " and ml.intstate =1";
            //如果是取消关注人数,取消关注时间
        }elseif ($type == 'unfollow'){
            $where = "ml.idsite =$idsite  and ml.intstate =2 and ml.dtunsubscribetime>=".strtotime($start_date." 00:00:00")." and ml.dtunsubscribetime<=".strtotime($end_date." 23:59:59");
            //如果是累积关注人数
        }elseif ($type == 'count'){
            $where = "ml.intstate =1 and m.intstate =1 and ml.idsite =$idsite and ml.dtsubscribetime<=".strtotime($end_date." 23:59:59");
            //如果是新增游客人数,创建时间
        }elseif ($type == 'visitor'){
            $where = "ml.idsite =$idsite and ml.intstate=3 and ml.dtcreatetime>=" . strtotime($start_date . " 00:00:00") . " and ml.dtcreatetime<=" . strtotime($end_date . " 23:59:59");
            //如果是累积游客人数
        }elseif ($type == 'visit_count'){
            $where = "ml.intstate =3 and ml.idsite =$idsite and ml.dtcreatetime<=".strtotime($end_date." 23:59:59");
            //如果是游客转关注人数
        }elseif ($type == 'visit_to_follow'){
            $where .= " and ml.intstate =1 and ml.old_intstate = 3";
            //如果是性别
        }elseif ($origin == 'attribute' && !empty($intsex)){
            //拼接条件
            if($intsex == 3){
                $user_where['intsex'] = [['=',0],['NULL',NULL],'or'];//"intsex = 0 or intsex is NULL"
            }else{
                $user_where['intsex'] = $intsex;
            }
            //如果是省份
        }elseif ($origin == 'attribute' && !empty($intprovince)){
            //如果省份有逗号,那么就是其他(省份),拼接条件
            if(strpos($intprovince,',')){
                $user_where['intprovince'] = [['not in',trim($intprovince,',')],['NULL',NULL],'or'];//"intprovince = not in() or intprovince is NULL"
                //有省份过来,条件就加上省份
            }else{
                $user_where['intprovince'] = $intprovince;
            }

            //如果市有逗号,那么就是其他(市),拼接条件
            if(strpos($intcity,',')){
                $user_where['intcity'] = [['not in',trim($intcity,',')],['NULL',NULL],'or'];//"intcity not in() or intcity is NULL"
                //否则如果不是其他城市的,那么就是加上城市的条件
            }elseif(!empty($intcity)){
                $user_where['intcity'] = $intcity;
            }
            //如果是年龄
        }elseif ($origin == 'attribute' && !empty($age_str)){
            $age_start = '';
            $age_end = '';
            //判断年龄段
            if($age_str == '3岁以下'){
                //年龄的开始时间
                $age_start = date('Y',strtotime('-2 year')).'-01-01';
                $age_end = date('Y',time()).'-12-31 23:59:59';
            }elseif ($age_str == '3-6岁'){
                //年龄的开始时间
                $age_start = date('Y',strtotime('-6 year')).'-01-01';
                $age_end = date('Y',strtotime('-3 year')).'-12-31 23:59:59';
            }elseif ($age_str == '7-9岁'){
                //年龄的开始时间
                $age_start = date('Y',strtotime('-9 year')).'-01-01';
                $age_end = date('Y',strtotime('-7 year')).'-12-31 23:59:59';
            }elseif ($age_str == '10-12岁'){
                //年龄的开始时间
                $age_start = date('Y',strtotime('-12 year')).'-01-01';
                $age_end = date('Y',strtotime('-10 year')).'-12-31 23:59:59';
            }elseif ($age_str == '13-18岁'){
                //年龄的开始时间
                $age_start = date('Y',strtotime('-18 year')).'-01-01';
                $age_end = date('Y',strtotime('-13 year')).'-12-31 23:59:59';
            }elseif ($age_str == '18岁以上'){
                //年龄的开始时间小于18岁
                $age_end = date('Y',strtotime('-18 year')).'-01-01';
            }
            //如果有开始时间和结算时间,那么就是在这两个时间范围内
            if($age_start && $age_end){
                //( ( `childage1` >= 1483200000 and `childage1` <= 1577807999 ) OR ( `childage2` >= 1483200000 and `childage2` <= 1577807999 ) OR ( `childage3` >= 1483200000 and `childage3` <= 1577807999 ) )
                $user_where['childage1|childage2|childage3'] = [['>=',strtotime($age_start)],['<=',strtotime($age_end)],'and'];
                //如果只有结算时间
            }elseif ($age_end && !$age_start){
                $user_where['childage1|childage2|childage3'] = [['>',0],['<=',strtotime($age_end)],'and'];
            }
            //如果是购买用户中的性别
        }elseif ($origin == 'buy_user' && !empty($intsex)){
            //拼接条件
            if($intsex == 3){
                $user_where['m.intsex'] = [['=',0],['NULL',NULL],'or'];//"intsex = 0 or intsex is NULL"
            }else{
                $user_where['m.intsex'] = $intsex;
            }
            //如果是购买用户中的关注状态
        }elseif ($origin == 'buy_user' && !empty($intstate)){
            $user_where['m.intstate'] = $intstate;
            //如果有关注时长
        }elseif ($origin == 'buy_user' && !empty($focus_time_str)) {
            $age_start = '';
            $age_end = '';
            //判断关注时长
            if ($focus_time_str == '3天以下') {
                //关注的开始时间
                $age_start = date('Y-m-d', strtotime('-3 day'));
                $age_end = date('Y-m-d', time());
            } elseif ($focus_time_str == '4-7天') {
                //关注的开始时间
                $age_start = date('Y-m-d', strtotime('-7 day'));
                $age_end = date('Y-m-d', strtotime('-4 day'));
            } elseif ($focus_time_str == '8-15天') {
                //关注的开始时间
                $age_start = date('Y-m-d', strtotime('-15 day'));
                $age_end = date('Y-m-d', strtotime('-8 day'));
            } elseif ($focus_time_str == '16-30天') {
                //关注的开始时间
                $age_start = date('Y-m-d', strtotime('-30 day'));
                $age_end = date('Y-m-d', strtotime('-16 day'));
            } elseif ($focus_time_str == '31-90天') {
                //关注的开始时间
                $age_start = date('Y-m-d', strtotime('-90 day'));
                $age_end = date('Y-m-d', strtotime('-31 day'));
            } elseif ($focus_time_str == '91-180天') {
                //关注的开始时间
                $age_start = date('Y-m-d', strtotime('-180 day'));
                $age_end = date('Y-m-d', strtotime('-91 day'));
            } elseif ($focus_time_str == '181-365天') {
                //关注的开始时间
                $age_start = date('Y-m-d', strtotime('-365 day'));
                $age_end = date('Y-m-d', strtotime('-181 day'));
            }elseif ($focus_time_str == '1年以上') {
                //关注的开始时间1年以上
                $age_end = date('Y-m-d', strtotime('-366 day'));
            }
//            halt($age_start.'end'.$age_end);
            //如果有开始时间和结束时间,那么就是在这两个时间范围内
            if ($age_start && $age_end) {
                //( ( `childage1` >= 1483200000 and `childage1` <= 1577807999 ) OR ( `childage2` >= 1483200000 and `childage2` <= 1577807999 ) OR ( `childage3` >= 1483200000 and `childage3` <= 1577807999 ) )
                $user_where['m.dtsubscribetime'] = [['>=', strtotime($age_start." 00:00:00")], ['<=', strtotime($age_end." 23:59:59")]];
                //如果只有结算时间
            } elseif ($age_end && !$age_start) {
                $user_where['m.dtsubscribetime'] = [['>', 0], ['<=', strtotime($age_end." 23:59:59")]];
            }
            $user_where['m.intstate'] = 1;
        }elseif ($origin == 'again_buy' && !empty($count_str)){
            $count_where = '';
            //判断关注时长
            if ($count_str == '5次以上') {
                //次数的条件
                $count_where = ' > 5';
            } elseif ($count_str == '2-5次') {
                //次数的条件
                $count_where = ' BETWEEN 2 AND 5';
            } elseif ($count_str == '1次') {
                //次数的条件
                $count_where = ' = 1';
            } elseif ($count_str == '0次') {
                //次数的条件
                $count_where = ' < 1';
            }
            //如果有状态条件
            if(!empty($intstate) && $intstate != 10){
                $user_where['m.intstate'] = $intstate;
            }
            //如果是成交数据,并且有活动id
        }elseif ($origin == 'deal_data' && !empty($product_id)){
            //如果活动id有逗号,那么就是其他(市),拼接条件
            if(strpos($product_id,',')){
                $user_where['o.dataid'] = ['not in',trim($product_id,',')];//"除去"
                //否则如果不是其他城市的,那么就是加上城市的条件
            }else{
                $user_where['o.dataid'] = $product_id;
            }
        }
        $fields = "m.idmember,m.userimg,m.openid,m.nickname,m.categoryid,m.intstate,m.dtsubscribetime,m.dtunsubscribetime,m.visitcount,m.integral,m.iduser,m.followuptime,m.dtlastvisitteim,m.ismanage";
//        halt($user_where);
        $result = [];
        $page = new Page(0,PAGE_SIZE);
        //如果是用户属性分析
        if($origin == 'attribute' ){
            if ($intstate){
                $user_where['intstate'] = $intstate;
            }
            $user_where['idsite'] = $idsite;
            $user_where['dtcreatetime'] = [['>=',strtotime($start_date." 00:00:00")],['<=',strtotime($end_date." 23:59:59")]];
            //查询条数
            $count = db('member')->where($user_where)->count();
            $page = new Page($count,PAGE_SIZE);
            //查询数据
            $result = db('member')->where($user_where)->order('idmember desc')->limit($page->firstRow.','.$page->pageSize)->select();
        }
        //如果是用户增减
        if($origin == 'member'){
            //查询条数
            $count = db('member')->alias('m')->join('member_log ml','m.idmember = ml.idmember')->where($where)->group('ml.idmember')->count();
            $page = new Page($count,PAGE_SIZE);
            //查询数据
            $result = db('member')->alias('m')->join('member_log ml','m.idmember = ml.idmember')->where($where)->order('m.idmember desc')->group('ml.idmember')->limit($page->firstRow.','.$page->pageSize)->select();
        }
        //如果是购买用户或者消费热力
        if($origin == 'buy_user' || ($origin == 'buy_power' && $order_type == 'num') || ($origin == 'deal_data' && !empty($product_id))){
            $user_where['m.idsite'] = $idsite;
            $user_where['o.dtcreatetime'] = [['>=',$start_date.' 00:00:00'],['<=',$end_date.' 23:59:59']];
            $user_where['o.idsite'] = $idsite;
            //查询条数
            $count = db('member')->alias('m')->join('order o','m.idmember=o.fiduser')->where($user_where)->group('o.fiduser')->count();
            $page = new Page($count,PAGE_SIZE);
            //查询数据
            $result = db('member')->alias('m')->join('order o','m.idmember=o.fiduser')->field($fields)->where($user_where)->group('o.fiduser')->order('o.dtcreatetime desc,m.idmember desc')->limit($page->firstRow.','.$page->pageSize)->select();
        }
        //如果是复购情况
        if($origin == 'again_buy' ){
            $user_where['m.idsite'] = $idsite;
//            $user_where['o.dtcreatetime'] = [['>=',$start_date.' 00:00:00'],['<=',$end_date.' 23:59:59']];
            $count_sql = "(SELECT count(o.id) FROM cms_order o WHERE o.fiduser = m.idmember AND ( `o`.`dtcreatetime` >= '$start_date 00:00:00' AND `o`.`dtcreatetime` <= '$end_date 23:59:59' ) and o.idsite = {$idsite})";
            $where_str = $count_sql.$count_where;
            //查询条数
            $count = db('member')->alias('m')->where($user_where)->where($where_str)->count();
            $page = new Page($count,PAGE_SIZE);
            //查询数据
            $result = db('member')->alias('m')->field("$fields,$count_sql  as num")->where($user_where)->where($where_str)->order('m.idmember desc')->limit($page->firstRow.','.$page->pageSize)->select();
        }
        $list['result'] = $result;
        $list['pager'] = $page;
//echo $result;exit;
//        halt($result);
//        echo $result;exit;
        $list['search'] = ['begintime'=>$start_date,'endtime'=>$end_date];

//        halt($result);
        return $list;
    }

    /**
     * 用户属性分析图
     */
    public function user_attribute($data)
    {
        //如果没有开始时间,那么默认就是一月前
        if(!isset($data["begintime"]) || empty($data["begintime"]) || (!\DateTime::createFromFormat('Y-m-d',$data['begintime']))){
            $start_date = date('Y-m-d',strtotime('-1 month -1 day'));
        }else{
            //搜索的结束时间
            $start_date = $data["begintime"];
        }
        //如果没有结束时间,那么默认就是到昨天
        if(!isset($data["endtime"]) || empty($data["endtime"]) || (!\DateTime::createFromFormat('Y-m-d',$data['endtime']))){
            $end_date = date('Y-m-d',strtotime('-1 day'));
        }else{
            //搜索的开始时间
            $end_date = $data["endtime"];
        }
        $intstate = isset($data["intstate"]) && !empty($data["intstate"])?$data["intstate"]:'';
        //搜索条件返回
        $result['search'] = ['begintime'=>$start_date,'endtime'=>$end_date,'intstate'=>$intstate];
//        halt($result);

        //站点id
        $idsite = session('idsite');
        //默认的where条件
        $where = "idsite =$idsite and dtcreatetime>=".strtotime($start_date." 00:00:00")." and dtcreatetime<=".strtotime($end_date." 23:59:59");
        if($intstate){
            $where .= " and intstate = {$intstate}";
        }
//        $where = "idsite =$idsite and dtcreatetime>=".strtotime($start_date." 00:00:00")." and dtcreatetime<=".strtotime($end_date." 23:59:59");
        $result['list'] = db('member')->field('ismanage',true)->where($where)->order('idmember desc')->select();
        //查询地区
        $result['region_list'] = db('member')->field('idmember,intstate,intsex,intprovince,count(idmember) as num')->where($where)->group('intprovince')->order('num desc')->select();
//        halt($result['region_list']);
        $result['echart'] = [];
        //需要出来搜索的省份
        $region_list = $result['region_list'];
        if($result['region_list']){
            //区域的总数
            $region_count = 0;
            //地区姓名
            $region_name_arr = [];
            //地区数量
            $region_count_arr = [];
            //地区id
            $region_id_arr = [];
            //其他的数量
            $other_count = 0;
            //3岁以下
            foreach ($result['region_list'] as $key=>$value){
                $region_count += $value['num'];
                //如果有省份的话,并且获取到的省份未到5个
                if($value['intprovince'] && count($region_name_arr) < 5){
                    //获取省份的名称
                    $region_info = db('region')->field('name')->where(['id'=>$value['intprovince']])->find();
                    $region_name_arr[] = $region_info['name'];
                    $region_count_arr[] = $value['num'];
                    $region_id_arr[] = $value['intprovince'];
                }else{
                    $other_count += $value['num'];//其他的数量让其累加
                }
                //如果有省份的话,就获取省份的名称
                if($value['intprovince']){
                    //获取省份的名称
                    $region_info = db('region')->field('name')->where(['id'=>$value['intprovince']])->find();
                    $region_list[$key]['name'] = $region_info['name'];
                }else{
                    unset($region_list[$key]);
                }
            }
            $region_name_arr[] = '其他';
            $region_count_arr[] = $other_count;
            $region_id_arr[] = implode(',',$region_id_arr).',';
            //因为要将图表要的数据转换为json数组的格式,每一个系列对应的x轴的值都是个数组
            $result['echart']['region'] = [[''],$region_name_arr,[$region_count_arr]];
            //地区的数量
            $result['region_count'] = $region_count;
            $result['region_id_arr'] = $region_id_arr;
        }
        #region  如果有数据 那么处理封装成图表要的数据格式
        if($result['list']){
            $count = count($result['list']);
            $man = 0;//男
            $woman = 0;//女
            $unknown = 0;//未知
            $three_under = 0;//3岁以下
            $three_six = 0;//3-6岁
            $seven_nine = 0;//7-9岁
            $ten_twelve = 0;//10-12岁
            $thirteen_eighteen = 0;//13-18岁
            $eighteen_top = 0;//18岁以上
            $age_count = 0;//孩子的总个数
            foreach ($result['list'] as $key=>$value){
                //如果是男
                if($value['intsex'] == 1){
                    $man +=1;
                    //女
                }elseif ($value['intsex'] == 2){
                    $woman += 1;
                }else{
                    $unknown += 1;
                }
                //判断用户数据的第一个孩子是否有值
                if($value['childage1'] > 0){
                    $age_count += 1;
                    //计算出孩子的年龄
                    $one_time = $this->diffDate(date('Y-m-d H:i:s',$value['childage1']),date('Y-m-d H:i:s',time()));
                    $one_age = intval($one_time['year']);
                    //小于三岁
                    if($one_age < 3){
                        $three_under +=1;
                        //3-6岁
                    }elseif ($one_age >= 3 && $one_age <= 6){
                        $three_six +=1;
                        //7-9岁
                    }elseif ($one_age >= 7 && $one_age <= 9){
                        $seven_nine +=1;
                        //10-12岁
                    }elseif ($one_age >= 10 && $one_age <= 12){
                        $ten_twelve +=1;
                        //13-18岁
                    }elseif ($one_age >= 13 && $one_age <= 18){
                        $thirteen_eighteen +=1;
                        //18岁以上
                    }elseif ($one_age > 18){
                        $eighteen_top +=1;
                    }
                }
                //判断用户数据的第二个孩子是否有值
                if($value['childage2'] > 0){
                    $age_count += 1;
                    //计算出孩子的年龄
                    $one_time = $this->diffDate(date('Y-m-d H:i:s',$value['childage2']),date('Y-m-d H:i:s',time()));
                    $one_age = intval($one_time['year']);
                    //3岁以下
                    if($one_age < 3){
                        $three_under +=1;
                        //3-6岁
                    }elseif ($one_age >= 3 && $one_age <= 6){
                        $three_six +=1;
                        //7-9岁
                    }elseif ($one_age >= 7 && $one_age <= 9){
                        $seven_nine +=1;
                        //10-12岁
                    }elseif ($one_age >= 10 && $one_age <= 12){
                        $ten_twelve +=1;
                        //13-18岁
                    }elseif ($one_age >= 13 && $one_age <= 18){
                        $thirteen_eighteen +=1;
                        //18岁以上
                    }elseif ($one_age > 18){
                        $eighteen_top +=1;
                    }
                }
                //判断用户数据的第三个孩子是否有值
                if($value['childage3'] > 0){
                    $age_count += 1;
                    //计算出孩子的年龄
                    $one_time = $this->diffDate(date('Y-m-d H:i:s',$value['childage3']),date('Y-m-d H:i:s',time()));
                    $one_age = intval($one_time['year']);
                    //小于三岁
                    if($one_age < 3){
                        $three_under +=1;
                        //3-6岁
                    }elseif ($one_age >= 3 && $one_age <= 6){
                        $three_six +=1;
                        //7-9岁
                    }elseif ($one_age >= 7 && $one_age <= 9){
                        $seven_nine +=1;
                        //10-12岁
                    }elseif ($one_age >= 10 && $one_age <= 12){
                        $ten_twelve +=1;
                        //13-18岁
                    }elseif ($one_age >= 13 && $one_age <= 18){
                        $thirteen_eighteen +=1;
                        //18岁以上
                    }elseif ($one_age > 18){
                        $eighteen_top +=1;
                    }
                }
            }
            //因为要将图表要的数据转换为json数组的格式,每一个系列对应的x轴的值都是个数组
            $result['echart']['sex'] = [[''],['男','女','未知'],[[$man,$woman,$unknown]]];
            if($count > 0){
                //性别的比例
                $result['sex_rate'] = [round($man/$count,4) * 100,round($woman/$count,4) * 100,round($unknown/$count,4) * 100];
            }else{
                $result['sex_rate'] = [0,0,0];
            }

            //孩子年龄的图表数据
            $result['echart']['age'] = [[''],['3岁以下','3-6岁','7-9岁','10-12岁','13-18岁','18岁以上'],[[$three_under,$three_six,$seven_nine,$ten_twelve,$thirteen_eighteen,$eighteen_top]]];
            //孩子的总数量
            $result['age_count'] = $age_count;
        }
        #endregion
        $result['echart_normal'] = $result['echart'];
        $result['echart'] = json_encode($result['echart'],JSON_UNESCAPED_UNICODE);
        //让数组键从 0 开始并以 1 递增
        $result['province'] = array_values($region_list);
//        halt($result['province']);
        $result['city_result'] = [];//初始化城市的数据
        $result['city_result']['echart'] = json_encode([]);//初始化城市的数据
        //如果省份有值的话,调用获取市区的数据
        if($result['province']){
            //默认就是第一个省的值
            $result['search']['province_id'] = $result['province'][0]['intprovince'];
            $city_result = $this->get_city_attribute($result['search']);
            $result['city_result'] = $city_result;
        }
//        halt($result['echart']);
        return $result;
    }

    /**
     * 通过省份获取市区的分析图
     */
    public function get_city_attribute($data)
    {
        //如果没有开始时间,那么默认就是一月前
        if(!isset($data["begintime"]) || empty($data["begintime"])){
            $start_date = date('Y-m-d',strtotime('-1 month -1 day'));
        }else{
            //搜索的结束时间
            $start_date = $data["begintime"];
        }
        //如果没有结束时间,那么默认就是到昨天
        if(!isset($data["endtime"]) || empty($data["endtime"])){
            $end_date = date('Y-m-d',strtotime('-1 day'));
        }else{
            //搜索的开始时间
            $end_date = $data["endtime"];
        }
        //省份
        $province_id = $data["province_id"];
        $intstate = $data["intstate"];
        //搜索条件返回
        $result['search'] = ['begintime'=>$start_date,'endtime'=>$end_date,'province_id'=>$province_id,'intstate'=>$intstate];

        //站点id
        $idsite = session('idsite');
        //默认的where条件
        $where = "intprovince = $province_id and idsite =$idsite and dtcreatetime>=".strtotime($start_date." 00:00:00")." and dtcreatetime<=".strtotime($end_date." 23:59:59");
//        $where = "idsite =$idsite and dtcreatetime>=".strtotime($start_date." 00:00:00")." and dtcreatetime<=".strtotime($end_date." 23:59:59");
        if($intstate){
            $where .= " and intstate = {$intstate}";
        }
        //查询市的地区
        $result['region_list'] = db('member')->field('idmember,intstate,intsex,intprovince,intcity,count(idmember) as num')->where($where)->group('intcity')->order('num desc')->select();
//        halt($result['region_list']);
        $result['echart'] = [];
        if($result['region_list']){
            //区域的总数
            $region_count = 0;
            //地区姓名
            $region_name_arr = [];
            //地区数量
            $region_count_arr = [];
            //地区id
            $region_id_arr = [];
            //其他的数量
            $other_count = 0;
            foreach ($result['region_list'] as $key=>$value){
                $region_count += $value['num'];
                //如果有市区的话,并且获取到的市区未到5个
                if($value['intcity'] && count($region_name_arr) < 5){
                    //获取省份的名称
                    $region_info = db('region')->field('name')->where(['id'=>$value['intcity']])->find();
                    $region_name_arr[] = $region_info['name'];
                    $region_count_arr[] = $value['num'];
                    $region_id_arr[] = $value['intcity'];
                }else{
                    $other_count += $value['num'];//其他的数量让其累加
                }
            }
            $region_name_arr[] = '其他';
            $region_count_arr[] = $other_count;
            $region_id_arr[] = implode(',',$region_id_arr).',';
            //因为要将图表要的数据转换为json数组的格式,每一个系列对应的x轴的值都是个数组
            $result['echart']['region'] = [[''],$region_name_arr,[$region_count_arr]];
            //地区的数量
            $result['region_count'] = $region_count;
            $result['region_id_arr'] = $region_id_arr;
        }
        #endregion
        $result['echart_normal'] = $result['echart'];
        $result['echart'] = json_encode($result['echart'],JSON_UNESCAPED_UNICODE);
//        halt($result);
        return $result;
    }

    /**
     * 计算两个时间之间相差的日时分秒
     * 时间格式需为2018-05-25 标准格式
     * @param $date1 开始时间
     * @param $date2 结束时间
     * @return array
     */
    function diffDate($date1, $date2) {
        $datetime1 = new \DateTime($date1);
        $datetime2 = new \DateTime($date2);
        $interval = $datetime1->diff($datetime2);
        $time['year'] = $interval->format('%Y');
        $time['month'] = $interval->format('%m');
        $time['day'] = $interval->format('%d');
        $time['hour'] = $interval->format('%H');
        $time['min'] = $interval->format('%i');
        $time['sec'] = $interval->format('%s');
        $time['days'] = $interval->format('%a'); // 两个时间相差总天数
        return $time;
    }

    /**
     * 购买用户分析图
     */
    public function buy_user($data)
    {
        if (!isset($data["time_range"])) {
            $data["time_range"] = 'last_week';
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
            case "this_year":
                $start_date = date('Y-m-d', strtotime(date('Y', time()) . '-01-01 00:00:00'));
                $end_date = date('Y-m-d', strtotime("today"));
                break;
            case "custom":
                $time = time();
                $start_date = $data["begintime"];
                $end_date = $data["endtime"];
                break;
        }
        //搜索条件返回
        $result['search'] = ['begintime'=>$start_date,'endtime'=>$end_date,'time_range'=>$data["time_range"]];
//        var_dump($result['search']);
//        halt($result);
        //站点id
        $idsite = session('idsite');
        //默认的where条件
        $where['idsite'] = $idsite;
//        $where['dtcreatetime'] = [['>=','2018-01-01 00:00:00'],['<=',$end_date.' 23:59:59']];
        $where['dtcreatetime'] = [['>=',$start_date.' 00:00:00'],['<=',$end_date.' 23:59:59']];
        //查询
        $result['list'] = db('order')->field('wechatid',true)->where($where)->group('fiduser')->order('dtcreatetime desc')->select();
//        halt($result['list']);
        $result['echart'] = [];
        #region  如果有数据 那么处理封装成图表要的数据格式
        if($result['list']){
            $count = count($result['list']);//数据的条数就是总数
            $man = 0;//男
            $woman = 0;//女
            $unknown = 0;//未知

            $one_year = 0;//1年以上
            $one_hundred_three = 0;//181-365天
            $ninety_one_hundred = 0;//91-180天
            $thirty_ninety = 0;//31-90天
            $sixteen_thirty = 0;//16-30天
            $eight_fifteen = 0;//8-15天
            $four_seven = 0;//4-7天
            $three_within = 0;//3天以内

            $follow = 0;//关注
            $unfollow = 0;//取消关注
            $visitor = 0;//游客
            foreach ($result['list'] as $key=>$value){
                //查询用户的信息
                $user_info = db('member')->field('ismanage',true)->where(['idmember'=>$value['fiduser'],'idsite'=>$idsite])->order('idmember desc')->find();
                if($user_info){
                    //如果是男
                    if($user_info['intsex'] == 1){
                        $man +=1;
                        //女
                    }elseif ($user_info['intsex'] == 2){
                        $woman += 1;
                    }else{
                        $unknown += 1;
                    }
                    //如果是关注
                    if($user_info['intstate'] == 1){
                        $follow +=1;

                        //获取关注时间离今天的天数
                        $one_time = $this->diffBetweenTwoDays(date('Y-m-d H:i:s',$user_info['dtsubscribetime']),date('Y-m-d',time()).'23:59:59');
                        $one_age = $one_time;
                        //1年以上
                        if($one_age > 365){
                            $one_year +=1;
                            //181-365天
                        }elseif ($one_age >= 181 && $one_age <= 365){
                            $one_hundred_three +=1;
                            //91-180天
                        }elseif ($one_age >= 91 && $one_age <= 180){
                            $ninety_one_hundred +=1;
                            //31-90天
                        }elseif ($one_age >= 31 && $one_age <= 90){
                            $thirty_ninety +=1;
                            //16-30天
                        }elseif ($one_age >= 16 && $one_age <= 30){
                            $sixteen_thirty +=1;
                            //8-15天
                        }elseif ($one_age >= 8 && $one_age <= 15){
//                            echo $user_info['dtsubscribetime'].'iD'.$user_info['idmember'].'<br>';
                            $eight_fifteen +=1;
                            //4-7天
                        }elseif ($one_age >= 4 && $one_age <= 7){
                            $four_seven +=1;
                            //3天以内
                        }elseif ($one_age >= 0 && $one_age <= 3){
                            $three_within +=1;
                        }
                        //取消关注
                    }elseif ($user_info['intstate'] == 2){
                        $unfollow += 1;
                    }else{
                        //游客
                        $visitor += 1;
                    }
                }
            }
            //因为要将图表要的数据转换为json数组的格式,每一个系列对应的x轴的值都是个数组
            $result['echart']['sex'] = [[''],['男','女','未知'],[[$man,$woman,$unknown]]];
            //关注状态
            $result['echart']['state'] = [[''],['关注','取消关注','游客'],[[$follow,$unfollow,$visitor]]];
            if($count > 0){
                //性别的比例
                $result['sex_rate'] = [round($man/$count,4) * 100,round($woman/$count,4) * 100,round($unknown/$count,4) * 100];
                //状态的比例
                $result['state_rate'] = [round($follow/$count,4) * 100,round($unfollow/$count,4) * 100,round($visitor/$count,4) * 100];
            }else{
                $result['sex_rate'] = [0,0,0];
                $result['state_rate'] = [0,0,0];
            }

            //孩子年龄的图表数据
            $result['echart']['time'] = [[''],['1年以上','181-365天','91-180天','31-90天','16-30天','8-15天','4-7天','3天以内'],[[$one_year,$one_hundred_three,$ninety_one_hundred,$thirty_ninety,$sixteen_thirty,$eight_fifteen,$four_seven,$three_within]]];
            //孩子的总数量
            $result['time_count'] = $count;
        }
        #endregion
        $result['echart_normal'] = $result['echart'];
        $result['echart'] = json_encode($result['echart'],JSON_UNESCAPED_UNICODE);
//        halt($result['echart']);
        return $result;
    }

    /**
     * 求两个日期之间相差的天数
     * (针对1970年1月1日之后，求之前可以采用泰勒公式)
     * @param string $day1
     * @param string $day2
     * @return number
     */
    function diffBetweenTwoDays ($day1, $day2)
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return round(($second1 - $second2) / 86400);
    }

    /**
     * 复购情况的购买次数分析图
     */
    public function again_condition($data)
    {
        //如果没有开始时间,那么默认就是一月前
        if(!isset($data["begintime"]) || empty($data["begintime"])){
            $start_date = date('Y-m-d',strtotime('-1 month'));
        }else{
            //搜索的结束时间
            $start_date = $data["begintime"];
        }
        //如果没有结束时间,那么默认就是到昨天
        if(!isset($data["endtime"]) || empty($data["endtime"])){
            $end_date = date('Y-m-d',strtotime('today'));
        }else{
            //搜索的开始时间
            $end_date = $data["endtime"];
        }
        $data["intstate"] = isset($data['intstate']) && !empty($data['intstate'])?$data['intstate']:10;
        //搜索条件返回
        $result['search'] = ['again_begintime'=>$start_date,'again_endtime'=>$end_date,'intstate'=>$data["intstate"]];
//        halt($result['search']);
//        halt($result);
        //站点id
        $idsite = session('idsite');
        //默认的where条件
        $where['idsite'] = $idsite;
        //如果有会员状态
        if(isset($data["intstate"]) && $data["intstate"] != 10){
            $where['intstate'] = $data["intstate"];
        }
        //查询用户数据
        $result['list'] = db('member')->field('idmember')->where($where)->order('idmember desc')->select();
//        halt($result['list']);
        $result['echart'] = [];
        #region  如果有数据 那么处理封装成图表要的数据格式
        if($result['list']){
            $count = count($result['list']);//数据的条数就是人数的总数
            $five_top = 0;//5次以上
            $two_five = 0;//2-5次
            $one = 0;//1次
            $zero = 0;//0次
            foreach ($result['list'] as $key=>$value){
                //查询用户的订单条数
                $order_count = db('order')->where(['fiduser'=>$value['idmember'],'idsite'=>$idsite,'dtcreatetime'=>[['>=',$start_date.' 00:00:00'],['<=',$end_date.' 23:59:59']]])->count();
                //如果是5次以上
                if($order_count > 5){
                    $five_top +=1;
                    //2-5次
                }elseif ($order_count >= 2 && $order_count <= 5){
                    $two_five += 1;
                }elseif ($order_count == 1 ){
                    $one += 1;
                }elseif ($order_count == 0 ){
                    $zero += 1;
                }

            }
            //孩子年龄的图表数据
            $result['echart']['again_condition'] = [[''],['5次以上','2-5次','1次','0次'],[[$five_top,$two_five,$one,$zero]]];
            //孩子的总数量
            $result['again_condition_count'] = $count;
        }
        #endregion
        $result['echart_normal'] = $result['echart'];
        $result['echart'] = json_encode($result['echart'],JSON_UNESCAPED_UNICODE);
//        halt($result['echart']);
        return $result;
    }

    /**
     * 消费能力列表
     */
    public function buy_power_list($data)
    {
        if (!isset($data["time_range"])) {
            $data["time_range"] = 'last_week';
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
            case "this_year":
                $start_date = date('Y-m-d', strtotime(date('Y', time()) . '-01-01 00:00:00'));
                $end_date = date('Y-m-d', strtotime("today"));
                break;
            case "custom":
                $time = time();
                $start_date = $data["begintime"];
                $end_date = $data["endtime"];
                break;
        }
        $data["state"] = isset($data['state']) && !empty($data['state'])?$data['state']:'';
        //搜索条件返回
        $result['search'] = ['begintime'=>$start_date,'endtime'=>$end_date,'time_range'=>$data["time_range"],'p'=>isset($data['p'])?$data['p']:'','state'=>$data["state"]];
        //排序方式
        $data["order_by"] = isset($data['order_by']) && !empty($data['order_by'])?$data['order_by']:'total';
//        var_dump($result['search']);
//        halt($result);
        //站点id
        $idsite = session('idsite');
        //默认的where条件
        $where['idsite'] = $idsite;
        //如果有会员状态
        if(isset($data["state"]) && !empty($data["state"])){
            $where['state'] = $data["state"];
        }
        //订单时间
        $where['dtcreatetime'] = [['>=',$start_date.' 00:00:00'],['<=',$end_date.' 23:59:59']];
        $where['fiduser'] = ['>',0];//得要有用户信息的
        $count = db('order')->where($where)->group('fiduser')->count();
        $page = new Page($count, PAGE_SIZE);
        //查询用户数据
        $result['list'] = db('order')->field('fiduser,sum(price) as total, count(fiduser) as num, sum(paynum) as pay_num,max(price) as max_price')->where($where)->group('fiduser')->order("{$data["order_by"]} desc")->limit($page->firstRow . ',' . $page->pageSize)->select();
//        halt($result['list']);
        #region  如果有数据 那么就获取用户的昵称
        if($result['list']){
            $ranking = $page->firstRow;//排名的开始值为每页的第一条数据的位置
            foreach ($result['list'] as $key=>&$value) {
                //查询用户的信息
                $user_info = db('member')->field('nickname,ismanage,openid')->where(['idmember' => $value['fiduser'], 'idsite' => $idsite])->find();
                if($user_info){
                    $ranking += 1;
                    $value['nickname'] = $user_info['nickname'];
                    $value['ismanage'] = $user_info['ismanage'];
                    $value['openid'] = $user_info['openid'];
                    $value['idmember'] = $value['fiduser'];
                    $value['ranking'] = $ranking;
                }else{
                    unset($result['list'][$key]);
                }
            }
        }
        #endregion
        $result['pager'] = $page;
//        halt($result);
        return $result;
    }

    /**
     * 消费热力图
     */
    public function img_buy_power($data)
    {
        //获取去年的数据
        $last_year = date('Y',time())-1;
        $last_start_date = $last_year.'-01-01';//去年的开始时间
        $last_end_date = $last_year.'-12-31';//去年的结束时间
//        halt($last_start_date.'end'.$last_end_date);
        $result['last_year'] = $this->get_img_buy_power($last_start_date,$last_end_date);
        //获取今年的数据
        $year = date('Y',time());
        $year_start_date = $year.'-01-01';
        $year_end_date = date('Y-m-d',time());//到今天
        $result['year'] = $this->get_img_buy_power($year_start_date,$year_end_date);
//        halt($result);
        $k_val = [];
        //循环去年
        for ($i = 11; $i >= 0; $i--) {
            //  减去月份
            $date = date("Y-m", strtotime("-".$i." month",strtotime($last_year.'-12-01')));
            //判断去年的每一个月份的数据是否有
            $result['last_year_num'][0][] = isset($result['last_year'][$date]['num'])?$result['last_year'][$date]['num']:0;
            $result['last_year_num'][1][] = $date.'-01';
            $result['last_year_num'][2][] = $date.'-'.date('t',strtotime($date));
            $result['last_year_pay_num'][0][] = isset($result['last_year'][$date]['pay_num'])?$result['last_year'][$date]['pay_num']:0;

            //  减去今年的月份
            $year_date = date("Y-m", strtotime("-".$i." month",strtotime($year.'-12-01')));
            //判断今年的每一个月份的数据是否有
            $result['year_num'][0][] = isset($result['year'][$year_date]['num'])?$result['year'][$year_date]['num']:0;
            $result['year_num'][1][] = $year_date.'-01';
            $result['year_num'][2][] = $year_date.'-'.date('t',strtotime($year_date));
            $result['year_pay_num'][0][] = isset($result['year'][$year_date]['pay_num'])?$result['year'][$year_date]['pay_num']:0;
            $k_val[] = intval(date('m',strtotime($date))).'月';
//            echo $date;
        }
//        halt($result['last_year_num']);
        $result['echart'] = [];
        #region  如果有数据 那么处理封装成图表要的数据格式
        $result['echart']['img_buy_power'] = [["{$last_year}购买人数","{$last_year}购买数量","{$year}购买人数","{$year}购买数量"],$k_val,[$result['last_year_num'][0],$result['last_year_pay_num'][0],$result['year_num'][0],$result['year_pay_num'][0]]];
        #endregion
        $result['echart_normal'] = $result['echart'];
        $result['echart'] = json_encode($result['echart'],JSON_UNESCAPED_UNICODE);
//        halt($result['echart_normal']);
        return $result;
    }

    //获取消费热力图的数据
    public function get_img_buy_power($start_date,$end_date){
        $idsite = session('idsite');
        //因为会存在订单里面的用户数据  在用户里面找不到了,所以需要关联
        $where = "o.idsite =$idsite and m.idsite = $idsite and o.dtcreatetime>= '{$start_date} 00:00:00'"." and o.dtcreatetime<= '{$end_date} 23:59:59'";
        $sql = "select DATE_FORMAT(o.dtcreatetime,'%Y-%m') as d,COUNT( DISTINCT o.fiduser)  as num , sum(o.paynum) as pay_num from cms_order o JOIN cms_member m on o.fiduser=m.idmember where ".$where." group by d";
//        echo $sql;exit;
        $list = db("order")->query($sql);
        $return = array();

        foreach ($list as $member){
            //人数
            $return[$member["d"]]['num'] = $member["num"];
            //购买数
            $return[$member["d"]]['pay_num'] = $member["pay_num"];
        }
        return $return;
    }

    /**
     * 成交数据中每个产品类别购买人数图
     */
    public function deal_data($data)
    {
        //如果没有开始时间,那么默认就是一月前
        if(!isset($data["begintime"]) || empty($data["begintime"])){
            $start_date = date('Y-m-d',strtotime('-1 month'));
        }else{
            //搜索的结束时间
            $start_date = $data["begintime"];
        }
        //如果没有结束时间,那么默认就是到昨天
        if(!isset($data["endtime"]) || empty($data["endtime"])){
            $end_date = date('Y-m-d',strtotime('today'));
        }else{
            //搜索的开始时间
            $end_date = $data["endtime"];
        }
        //看是通过够买量pay_num还是购买人数user_count排序(看是要获取前五位的购买人数还是数量)
        $order_by = isset($data["order_by"]) && !empty($data["order_by"])?$data["order_by"]:'user_count';
        //搜索条件返回
        $result['search'] = ['begintime'=>$start_date,'endtime'=>$end_date];
//        var_dump($result['search']);
//        halt($result);
        //站点id
        $idsite = session('idsite');
        //默认的where条件
        $where['idsite'] = $idsite;
        $where['dtcreatetime'] = [['>=',$start_date.' 00:00:00'],['<=',$end_date.' 23:59:59']];
        //查询用户数据
        $result['list'] = db('order')->field('dataid,chrtitle,sum(paynum) as pay_num,count(DISTINCT fiduser) as user_count,fiduser')->where($where)->order("{$order_by} desc,dataid desc")->group('dataid')->select();
//        halt($result['list']);
        $result['echart'] = [];
        #region  如果有数据 那么处理封装成图表要的数据格式
        if($result['list']){
            #region
            //产品购买人数的总数
            $total_user_count_activity = 0;
            //产品其他购买人数的数量
            $other_user_count_activity = 0;
            //装产品购买人数的值的图表中值的数组
            $user_count_echart_val_activity = [];
            //装产品购买人数的活动id的数组
            $user_count_id_activity = [];
            //其他活动用户id数组
            $fiduser_activity = [];
            #endregion


            //产品其他购买人数的数量
            $other_user_count_class = 0;
            //装产品购买人数的值的图表中值的数组
            $user_count_echart_val_class = [];
            //装分类id的数组
            $user_count_id_class = [];

            //装分类id和数量
            $class_arr = [];
            //装标签id和数量
            $final_chrtags_arr = [];

            //其他标签数
            $other_user_count_chrtags = 0;
            //装标签值图表中值的数组
            $user_count_echart_val_chrtags = [];
            //装标签id的数组
            $user_count_id_chrtags = [];
            //标签的总数
            $total_user_count_chrtags = 0;
            //分类的总数
            $total_user_count_class = 0;
            foreach ($result['list'] as $key=>$value){
                //查询出该活动的分类id和标签id
                $activity_info = db('activity')->field('fidtype,chrtags')->where(['idactivity'=>$value['dataid'],'siteid'=>$idsite])->find();
                //查询用户的信息,此时查找用户数据  是防止订单里面的用户id在用户表中查不到
                $user_info = db('member')->field('nickname,ismanage,openid')->where(['idmember' => $value['fiduser'], 'idsite' => $idsite])->find();
                if($activity_info && $user_info) {
                    if($order_by == 'pay_num'){
                        $total_user_count_activity += $value[$order_by];//累加购买数量
                    }
//                $total_pay_num_activity += $value['pay_num'];//累加购买量
                    //如果有活动id的话,并且获取到的购买人数的数据未到5个
                    if ($value['dataid'] && count($user_count_echart_val_activity) < 5) {
                        $user_count_echart_name_activity[] = $value['chrtitle'];//购买人数的活动名称
                        $user_count_echart_val_activity[] = $value[$order_by];
                        $user_count_id_activity[] = $value['dataid'];
                        if($order_by == 'user_count'){
                            $total_user_count_activity += $value[$order_by];//累加购买数量
                        }
                    } else {
                        //如果是购买数量
                        if($order_by == 'pay_num'){
                            $other_user_count_activity += $value[$order_by];//其他购买数的数量累加
                        }
                    }
                    //将分类id作为键  购买人数作为值   组合成数组,用来统计分类的购买人数
                    if (array_key_exists("{$activity_info['fidtype']}", $class_arr)) {
                        $class_arr[$activity_info['fidtype']] = $class_arr[$activity_info['fidtype']] + $value[$order_by];
                    } else {
                        $class_arr[$activity_info['fidtype']] = $value[$order_by];
                    }
                    //将分类数组,通过值降序排序
                    arsort($class_arr);

                    //处理活动标签
                    if($activity_info['chrtags']){
                        //将标签转换为数组
                        $chrtags_arr = explode(',',trim($activity_info['chrtags'],','));
//                        var_dump($chrtags_arr);
                        //循环标签
                        foreach ($chrtags_arr as $val){
                            //将标签d作为键  购买人数作为值   组合成数组,用来统计分类的购买人数
                            if (array_key_exists("$val", $final_chrtags_arr)) {
                                $final_chrtags_arr[$val] = $final_chrtags_arr[$val] + $value[$order_by];
                            } else {
                                $final_chrtags_arr[$val] = $value[$order_by];
                            }
                        }
                        //将分类数组,通过值降序排序
                        arsort($final_chrtags_arr);
                    }

                }
            }
//            halt($final_chrtags_arr);
            //如果有分类
            if($class_arr){
                foreach ($class_arr as $key=>$val){
                    //查询出书签表中分类的数据
                    $book_info = db('work_content')->field('name')->where(['code'=>$key,'idsite'=>$idsite])->find();
                    if($book_info) {
                        $total_user_count_class += $val;//累加购买数量
                        //如果有活动分类id的话,并且获取到的购买人数的数据未到5个
                        if(count($user_count_echart_val_class) < 5){
                            $user_count_echart_name_class[] = $book_info['name'];//分类名称
                            $user_count_echart_val_class[] = $val;
                            $user_count_id_class[] = $key;
                        }else{
                            $other_user_count_class += $val;//其他购买数的数量累加
                        }
                    }
                }
            }
            //如果有标签
            if($final_chrtags_arr){
                foreach ($final_chrtags_arr as $key=>$val){
                    //查询出书签表中分类的数据
                    $book_info = db('work_content')->field('name')->where(['code'=>$key,'idsite'=>$idsite])->find();
                    if($book_info) {
                        $total_user_count_chrtags += $val;//累加购买数量
                        //如果活动标签id的购买人数的数据未到5个
                        if(count($user_count_echart_val_chrtags) < 5){
                            $user_count_echart_name_chrtags[] = $book_info['name'] ;//标签名称
                            $user_count_echart_val_chrtags[] = $val;
                            $user_count_id_chrtags[] = $key;
                        }else{
                            $other_user_count_chrtags += $val;//其他购买数的数量累加
                        }
                    }
                }
            }
//            halt($class_arr);
            #region   活动购买人数
            $user_count_echart_name_activity[] = '其他';
            //其他活动id
            $user_count_id_activity_str = implode(',',$user_count_id_activity).',';
            //如果是购买人数
            if($order_by == 'user_count'){
                $user_where['m.idsite'] = $idsite;
                $user_where['o.dtcreatetime'] = [['>=',$start_date.' 00:00:00'],['<=',$end_date.' 23:59:59']];
                $user_where['o.idsite'] = $idsite;
                $user_where['o.dataid'] = ['not in',trim($user_count_id_activity_str,',')];//"除去"
                $other_user_count_activity = db('member')->alias('m')->join('order o','m.idmember=o.fiduser')->where($user_where)->group('o.fiduser')->count();
                //总数等于前五加其他
                $total_user_count_activity += $other_user_count_activity;
            }
            $user_count_echart_val_activity[] = $other_user_count_activity;
            $user_count_id_activity[] = $user_count_id_activity_str;
            #endregion

            $user_count_echart_name_class[] = '其他';
            $user_count_echart_val_class[] = $other_user_count_class;
            $user_count_id_class[] = 0;

            $user_count_echart_name_chrtags[] = '其他';
            $user_count_echart_val_chrtags[] = $other_user_count_chrtags;
            $user_count_id_chrtags[] = 0;
            //因为要将图表要的数据转换为json数组的格式,每一个系列对应的x轴的值都是个数组
            $result['echart']['user_count_activity'] = [[''],$user_count_echart_name_activity,[$user_count_echart_val_activity]];
            //分类购买人数的图表数据
            $result['echart']['user_count_class'] = [[''],$user_count_echart_name_class,[$user_count_echart_val_class]];
            //标签的图表数据
            $result['echart']['user_count_chrtags'] = [[''],$user_count_echart_name_chrtags,[$user_count_echart_val_chrtags]];
            //活动购买总人数数量
            $result['total_user_count_activity'] = $total_user_count_activity;
            //活动标签的总数
            $result['total_user_count_chrtags'] = $total_user_count_chrtags;
            //活动分类的总数
            $result['total_user_count_class'] = $total_user_count_class;
            //活动购买人数前五的活动id
            $result['user_count_id_activity'] = $user_count_id_activity;
            //分类购买人数前五的分类id
            $result['user_count_id_class'] = $user_count_id_class;
            //分类购买人数前五的标签id
            $result['user_count_id_chrtags'] = $user_count_id_chrtags;
        }
        #endregion
        $result['echart_normal'] = $result['echart'];
        $result['echart'] = json_encode($result['echart'],JSON_UNESCAPED_UNICODE);
//        echo $result['echart'];exit;
        return $result;
    }

    /**
     * 浏览(浏览产品和转发)数据
     */
    public function browse_data($data)
    {
        //如果没有开始时间,那么默认就是一月前
        if(!isset($data["begintime"]) || empty($data["begintime"])){
            $start_date = date('Y-m-d',strtotime('-1 month'));
        }else{
            //搜索的结束时间
            $start_date = $data["begintime"];
        }
        //如果没有结束时间,那么默认就是到昨天
        if(!isset($data["endtime"]) || empty($data["endtime"])){
            $end_date = date('Y-m-d',strtotime('today'));
        }else{
            //搜索的开始时间
            $end_date = $data["endtime"];
        }
        //看是通过浏览网站还是转发次数
        $type = isset($data["type"]) && !empty($data["type"])?$data["type"]:'browse';
        //搜索条件返回
        $result['search'] = ['begintime'=>$start_date,'endtime'=>$end_date];
//        var_dump($result['search']);
//        halt($result);
        //站点id
        $idsite = session('idsite');
        //如果是浏览类型
        if($type == 'browse'){
            //查询浏览数据
            $result['list'] = db('visit_record')->field('aid as dataid,count(aid) as count,atitle as chrtitle')->where(['idsite'=>$idsite,'userid'=>['<>',0]])->order("count desc,id desc")->group('aid')->select();
        }else{
            //查询转发数据
            $result['list'] = db('forwarded_log')->field('dataid,count(dataid) as count,chrtitle')->where(['idsite'=>$idsite])->order("count desc,id desc")->group('dataid')->select();
        }
//        halt($result['list']);
        $result['echart'] = [];
        #region  如果有数据 那么处理封装成图表要的数据格式
        if($result['list']){
            #region
            //产品购买人数的总数
            $total_user_count_activity = 0;
            //产品其他购买人数的数量
            $other_user_count_activity = 0;
            //装产品购买人数的值的图表中值的数组
            $user_count_echart_val_activity = [];
            //装产品购买人数的活动id的数组
            $user_count_id_activity = [];
            #endregion


            //产品其他购买人数的数量
            $other_user_count_class = 0;
            //装产品购买人数的值的图表中值的数组
            $user_count_echart_val_class = [];
            //装分类id的数组
            $user_count_id_class = [];

            //装分类id和数量
            $class_arr = [];
            //装标签id和数量
            $final_chrtags_arr = [];

            //其他标签数
            $other_user_count_chrtags = 0;
            //装标签值图表中值的数组
            $user_count_echart_val_chrtags = [];
            //装标签id的数组
            $user_count_id_chrtags = [];
            //标签的总数
            $total_user_count_chrtags = 0;
            //分类的总数
            $total_user_count_class = 0;
            foreach ($result['list'] as $key=>$value){
                //查询出该活动的分类id和标签id
                $activity_info = db('activity')->field('fidtype,chrtags')->where(['idactivity'=>$value['dataid'],'siteid'=>$idsite,'dtpublishtime'=>[['>=',$start_date.' 00:00:00'],['<=',$end_date.' 23:59:59']]])->find();
                if($activity_info) {
                    $total_user_count_activity += $value['count'];//累加购买人数
//                $total_pay_num_activity += $value['pay_num'];//累加购买量
                    //如果有活动id的话,并且获取到的购买人数的数据未到5个
                    if ($value['dataid'] && count($user_count_echart_val_activity) < 5) {
                        $user_count_echart_name_activity[] = $value['chrtitle'];//购买人数的活动名称
                        $user_count_echart_val_activity[] = $value['count'];
                        $user_count_id_activity[] = $value['dataid'];
                    } else {
                        $other_user_count_activity += $value['count'];//其他购买人数的数量累加
                    }
                    //将分类id作为键  购买人数作为值   组合成数组,用来统计分类的购买人数
                    if (array_key_exists("{$activity_info['fidtype']}", $class_arr)) {
                        $class_arr[$activity_info['fidtype']] = $class_arr[$activity_info['fidtype']] + $value['count'];
                    } else {
                        $class_arr[$activity_info['fidtype']] = $value['count'];
                    }
                    //将分类数组,通过值降序排序
                    arsort($class_arr);

                    //处理活动标签
                    if($activity_info['chrtags']){
                        //将标签转换为数组
                        $chrtags_arr = explode(',',trim($activity_info['chrtags'],','));
//                        var_dump($chrtags_arr);
                        //循环标签
                        foreach ($chrtags_arr as $val){
                            //将标签d作为键  购买人数作为值   组合成数组,用来统计分类的购买人数
                            if (array_key_exists("$val", $final_chrtags_arr)) {
                                $final_chrtags_arr[$val] = $final_chrtags_arr[$val] + $value['count'];
                            } else {
                                $final_chrtags_arr[$val] = $value['count'];
                            }
                        }
                        //将分类数组,通过值降序排序
                        arsort($final_chrtags_arr);
                    }

                }
            }
//            halt($final_chrtags_arr);
            //如果有分类
            if($class_arr){
                foreach ($class_arr as $key=>$val){
                    //查询出书签表中分类的数据
                    $book_info = db('work_content')->field('name')->where(['code'=>$key,'idsite'=>$idsite])->find();
                    if($book_info) {
                        $total_user_count_class += $val;//累加购买数量
                        //如果有活动分类id的话,并且获取到的购买人数的数据未到5个
                        if(count($user_count_echart_val_class) < 5){
                            $user_count_echart_name_class[] = $book_info['name'];//分类名称
                            $user_count_echart_val_class[] = $val;
                            $user_count_id_class[] = $key;
                        }else{
                            $other_user_count_class += $val;//其他购买数的数量累加
                        }
                    }
                }
            }
            //如果有标签
            if($final_chrtags_arr){
                foreach ($final_chrtags_arr as $key=>$val){
                    //查询出书签表中分类的数据
                    $book_info = db('work_content')->field('name')->where(['code'=>$key,'idsite'=>$idsite])->find();
                    if($book_info) {
                        $total_user_count_chrtags += $val;//累加购买数量
                        //如果活动标签id的购买人数的数据未到5个
                        if(count($user_count_echart_val_chrtags) < 5){
                            $user_count_echart_name_chrtags[] = $book_info['name'] ;//标签名称
                            $user_count_echart_val_chrtags[] = $val;
                            $user_count_id_chrtags[] = $key;
                        }else{
                            $other_user_count_chrtags += $val;//其他购买数的数量累加
                        }
                    }
                }
            }
//            halt($class_arr);
            #region   活动购买人数
            $user_count_echart_name_activity[] = '其他';
            $user_count_echart_val_activity[] = $other_user_count_activity;
            $user_count_id_activity[] = implode(',',$user_count_id_activity).',';
            #endregion

            $user_count_echart_name_class[] = '其他';
            $user_count_echart_val_class[] = $other_user_count_class;
            $user_count_id_class[] = 0;

            $user_count_echart_name_chrtags[] = '其他';
            $user_count_echart_val_chrtags[] = $other_user_count_chrtags;
            $user_count_id_chrtags[] = 0;
            //因为要将图表要的数据转换为json数组的格式,每一个系列对应的x轴的值都是个数组
            $result['echart']['user_count_activity'] = [[''],$user_count_echart_name_activity,[$user_count_echart_val_activity]];
            //分类购买人数的图表数据
            $result['echart']['user_count_class'] = [[''],$user_count_echart_name_class,[$user_count_echart_val_class]];
            //标签的图表数据
            $result['echart']['user_count_chrtags'] = [[''],$user_count_echart_name_chrtags,[$user_count_echart_val_chrtags]];
            //活动购买总人数数量
            $result['total_user_count_activity'] = $total_user_count_activity;
            //活动标签的总数
            $result['total_user_count_chrtags'] = $total_user_count_chrtags;
            //活动分类的总数
            $result['total_user_count_class'] = $total_user_count_class;
            //活动购买人数前五的活动id
            $result['user_count_id_activity'] = $user_count_id_activity;
            //分类购买人数前五的分类id
            $result['user_count_id_class'] = $user_count_id_class;
            //分类购买人数前五的标签id
            $result['user_count_id_chrtags'] = $user_count_id_chrtags;
        }
        #endregion
        $result['echart_normal'] = $result['echart'];
        $result['echart'] = json_encode($result['echart'],JSON_UNESCAPED_UNICODE);
//        echo $result['echart'];exit;
        return $result;
    }

}