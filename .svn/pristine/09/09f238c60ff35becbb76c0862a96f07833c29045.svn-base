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
 * Author: JY
 * Date: 2015-09-23
 */

namespace app\home\controller;
use Home\Logic\UsersLogic;
use think\Controller;
use think\Request;
class Api extends Controller {

    public  $send_scene;

    /*
     * 获取地区
     */
    public function getRegion(){
        $request = Request::instance()->param();
        $parent_id = $request['parent_id'];
        if(array_key_exists('selected',$request)){
            $selected = $request['selected'];
        }else{
            $selected = 0;
        }

        /*$parent_id = I('get.parent_id');
        $selected = I('get.selected',0); */
        $data = db('region')->where("parent_id=".$parent_id)->select();
        $html = '';
        if($data){
            foreach($data as $h){
                if($h['id'] == $selected){
                    $html .= "<option value='{$h['id']}' selected>{$h['name']}</option>";
                }
                else {
                    $html .= "<option value='{$h['id']}'>{$h['name']}</option>";
                }
            }
        }

        echo $html;
    }

    public function getTwon(){
        $parent_id = I('get.parent_id');
        $data = db('region')->where("parent_id=$parent_id")->select();
        $html = '';
        if($data){
            foreach($data as $h){
                $html .= "<option value='{$h['id']}'>{$h['name']}</option>";
            }
        }
        if(empty($html)){
            echo '0';
        }else{
            echo $html;
        }
    }

    public function getProvince()
    {
        $province = db('region')->field('id,name')->where(['level'=>1])->select();
        foreach($province as $key=>$val){
            $province[$key]['city'] = M('region')->field('id,name')->where(['parent_id'=> $province[$key]['id']])->select();
        }
        $res = ['status'=>1,'msg'=>'获取成功','result'=>$province];
        //$this->AjaxReturn($res);
        return $res;
    }

    public function getArea()
    {
        $id = I('id');
        if($id){
            $area = M('region')->field('id,name,parent_id as pid')->where(['parent_id'=>$id])->select();
            $res = ['status'=>1,'msg'=>'获取成功','result'=>$area];
        }else{
            $res = ['status'=>0,'msg'=>'获取失败,参数有误','result'=>''];
        }
        //$this->AjaxReturn($res);
        return $res;
    }

    public function adshow()
    {
        $request = Request::instance()->param();
        $idsite=$request['idsite'];
        $id=$request['id'];

        $resule = db('ad')->where(['pid'=>$id,'idsite'=>$idsite])->select();

        echo json_encode($resule);

        exit();
    }


    public function test(){
//        die("---------");
        $arr = db("member")->select();
        foreach ($arr as $r){
            $tmp = $r;
            if($tmp["intstate"]==3){
                $tmp["dtsubscribetime"] = "";
                $tmp["dtunsubscribetime"] = "";
                $tmp["dtcreatetime"] = $tmp["dtsubscribetime"]?:$tmp["dtcreatetime"];
            }elseif($tmp["intstate"]==2){
                if($tmp["dtsubscribetime"]){
                    $tmp1 = $tmp;
                    $tmp1["intstate"] = 1;
                    $tmp1["dtunsubscribetime"] = "";
                    $rs = member_log($tmp1);
                    print_r($rs);
                    echo "<br/>";
                    $tmp1 = array();
                }
                $tmp["dtsubscribetime"] = "";
            }else{
                $tmp["dtunsubscribetime"] = "";
            }
            $rs = member_log($tmp);
            print_r($rs);
            echo "<br/>";
        }

    }

}