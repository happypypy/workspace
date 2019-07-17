<?php
/*
 * @Descripttion: 预约对象
 * @version: v1.0
 * @Author: ChenJie
 * @Date: 2019-07-08 14:08:58
 * @LastEditors: ChenJie
 * @LastEditTime: 2019-07-09 15:06:00
 */
namespace app\admin\controller;
use app\admin\module\Subscribe as subscribeModel;
use think\Exception;
use think\Request;
class Subscribe extends Basesite{
    /**
     * 预约对象列表
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-08 14:11:10
     */
    public function index(){
        $param = input('param.');
        $param['object_name'] = isset($param['object_name']) ? rtrim($param['object_name']) : '';
        $param['category_id'] = isset($param['category_id']) ? $param['category_id'] : '';
        $param['start_time'] = isset($param['start_time']) ? $param['start_time'] : '';
        $param['end_time'] = isset($param['end_time']) ? $param['end_time'] : '';
        $param['p'] = isset($param['p']) ? intval($param['p']) : 0;

        $subscribeModel = new subscribeModel;
        // 获取分类
        $subscribe_object_category = $subscribeModel->get_work_content();
        // 获取数据列表
        $result = $subscribeModel->index($param);


        $this->assign("param", $param);
        $this->assign("subscribe_object_category", $subscribe_object_category);
        $this->assign("page", $result['page']);
        $this->assign("datalist", $result['datalist']);
        return $this->fetch();
    }

    /**
     * 计算多个集合的笛卡尔积
     * @param  Array $sets 集合数组
     * @return Array
     */
    function CartesianProduct($sets){

        // 保存结果
        $result = array();
        // 循环遍历集合数据

        $count=count($sets);
        if($count==1)
        {
            foreach ($sets as $vo)
            {
                foreach ($vo as $v)
                {
                    $result[][]=$v;
                }

            }
            return $result;
        }
        for($i=0; $i<$count-1; $i++){
            // 初始化
            if($i==0){
                $result = $sets[$i];
            }

            // 保存临时数据
            $tmp = array();

            // 结果与下一个集合计算笛卡尔积
            foreach($result as $res){
                $tmp1=[];
                if(is_array($res))
                    $tmp1=$res;
                else
                    $tmp1[]=$res;

                foreach($sets[$i+1] as $set){
                    $tmp2=$tmp1;
                    $tmp2[]=$set;
                    $tmp[] =$tmp2;
                   // $tmp[] = $res.','.$set;
                }
            }

            // 将笛卡尔积写入结果
            $result = $tmp;

        }

        return $result;

    }

    function object_array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        } if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }
    /**
     * 获取 规格的 笛卡尔积
     * @param $goods_id 商品 id
     * @param $spec_arr 笛卡尔积
     * @return string 返回表格字符串
     */
    public function getspecinput()
    {
        $param = Request::instance()->param();

        $spec_arr=$param['spec_arr'];
        $dataid=$param['dataid'];
        $spec_arr=json_decode($spec_arr);
        //$goods_id, $spec_arr, $store_id = 0
        // <input name="item[2_4_7][price]" value="100" /><input name="item[2_4_7][name]" value="蓝色_S_长袖" />
        /*$spec_arr = array(
            20 => array('7','8','9'),
            10=>array('1','2'),
            1 => array('3','4'),

        );
        */
        //$spec_arr=$this->object_array($spec_arr);


//        $spec_arr_sort=[];
//        $spec_arr2=[];
//        // 排序
//        foreach ($spec_arr as $k => $v)
//        {
//            $spec_arr_sort[$k] = count($v);
//        }
//        asort($spec_arr_sort);
//        foreach ($spec_arr_sort as $key =>$val)
//        {
//            $spec_arr2[$key] = $spec_arr[$key];
//        }

        $arr_spec=[];
        $arr_spec_item=[];
        foreach ($spec_arr as $k => $v)
        {
            $arr_spec[]=$k;
            $arr_spec_item[]=$v;
        }
        //dump($arr_spec_item);
        $arr_spec_item= $this->CartesianProduct($arr_spec_item);
        //dump($arr_spec_item);

        $spec_tmp = db('spec')->field('id,name')->select(); // 规格表
        $spec=[];
        foreach ($spec_tmp as $vo)
        {
            $spec[$vo['id']]=$vo['name'];
        }
        //$specItem_tmp = db('spec_item')->where("subscribe_object_id = $dataid")->field('id,name,specid')->select();//规格项
        $specItem_tmp = db('spec_item')->field('id,name,specid')->select();//规格项
        $specItem=[];
        foreach ($specItem_tmp as $vo)
        {
            $specItem[$vo['id']]=$vo['name'];
        }
        //$keySpecGoodsPrice = M('SpecGoodsPrice')->where("store_id = $store_id and goods_id = $goods_id")->getField('key,key_name,price,price1,store_count,bookbaseprice,bookprice,deposit_price,advance_price,sku');//规格项
        $keySpecGoodsPrice =[];
        $str = "<table class='table table-bordered' id='spec_input_tab'>";
        $str .="<tr>";
        // 显示第一行的数据
        foreach ($arr_spec as $k => $v)
        {
            $str .=" <td><b>{$spec[$v]}</b></td>";
        }
        $str .="<td><b>现货成本价</b></td>
                <td><b>现货价</b></td>
               <td><b>库存</b></td>
               <td><b>订货成本价</b></td>
                <td><b>订货价</b></td>
               <td><b>定金</b></td>
               <td><b>预付款</b></td>
               <td><b>SKU</b></td>
             </tr>";
        // 显示第二行开始


        foreach ($arr_spec_item as $k => $vo)
        {
                $str .="<tr>";
                foreach($vo as $k1 => $v)
                {

                  // $str .="<td>[".$v."]</td>";
                    $str .="<td>".$specItem[$v]."</td>";
                    // $item_key_name[$v2] = $spec[$specItem[$v2]['spec_id']].':'.$specItem[$v2]['name'];
                }
                /*
                                ksort($item_key_name);
                                $item_key = implode('_', array_keys($item_key_name));
                                $item_name = implode(' ', $item_key_name);

                                $keySpecGoodsPrice[$item_key][price1] ? false : $keySpecGoodsPrice[$item_key][price1] = 0; // 现货成本价价格默认为0
                                $keySpecGoodsPrice[$item_key][price] ? false : $keySpecGoodsPrice[$item_key][price] = 0; // 现货价价格默认为0
                                $keySpecGoodsPrice[$item_key][store_count] ? false : $keySpecGoodsPrice[$item_key][store_count] = 0; //库存默认为0
                                $keySpecGoodsPrice[$item_key][bookbaseprice] ? false : $keySpecGoodsPrice[$item_key][bookbaseprice] = 0; // 订货成本价格默认为0
                                $keySpecGoodsPrice[$item_key][bookprice] ? false : $keySpecGoodsPrice[$item_key][bookprice] = 0; // 订货价价格默认为0
                                $keySpecGoodsPrice[$item_key][deposit_price] ? false : $keySpecGoodsPrice[$item_key][deposit_price] = 0; // 定金价格默认为0
                                $keySpecGoodsPrice[$item_key][advance_price] ? false : $keySpecGoodsPrice[$item_key][advance_price] = 0; // 预付款价格默认为0
                                $str .="<td><input onblur='setprice(this,\"$item_key\",1)' style='width: 100px;' name='item[$item_key][price1]' value='{$keySpecGoodsPrice[$item_key][price1]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
                                $str .="<td><input id='".$item_key."' readonly='readonly' style='width: 100px;' name='item[$item_key][price]' value='{$keySpecGoodsPrice[$item_key][price]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
                                $str .="<td><input name='item[$item_key][store_count]' style='width: 100px;' value='{$keySpecGoodsPrice[$item_key][store_count]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")'/></td>";
                                $str .="<td><input onblur='setprice(this,\"book$item_key\",2)' style='width: 100px;' name='item[$item_key][bookbaseprice]' value='{$keySpecGoodsPrice[$item_key][bookbaseprice]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
                                $str .="<td><input id='book".$item_key."' readonly='readonly' style='width: 100px;' name='item[$item_key][bookprice]' value='{$keySpecGoodsPrice[$item_key][bookprice]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
                                $str .="<td><input id='deposit".$item_key."'  name='item[$item_key][deposit_price]' style='width: 100px;' value='{$keySpecGoodsPrice[$item_key][deposit_price]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
                                $str .="<td><input id='advance".$item_key."'  name='item[$item_key][advance_price]' style='width: 100px;' value='{$keySpecGoodsPrice[$item_key][advance_price]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
                                $str .="<td><input name='item[$item_key][sku]' style='width: 100px;' value='{$keySpecGoodsPrice[$item_key][sku]}' />
                                <input type='hidden' name='item[$item_key][key_name]' value='$item_name' /></td>";
                */

            $str .="<td><input  /></td>";
            $str .="<td><input  /></td>";
            $str .="<td><input  /></td>";
            $str .="<td><input  /></td>";
            $str .="<td><input  /></td>";
            $str .="<td><input  /></td>";
            $str .="<td><input  /></td>";
            $str .="<td><input  /></td>";


               $str .="</tr>";

        }
        $str .= "</table>";
        return $str;
    }

    /**
     * 预约对象新增/编辑
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-08 14:11:21
     */
    public function object_edit(){
        $idsite = session('idsite');
        $param = input('param.');
        $param['category_id'] = isset($param['category_id']) ? $param['category_id'] : '';
        $id = isset($param['id']) ? intval($param['id']) : 0;

        $subscribeModel = new subscribeModel;
        
        if(request()->isPost()){
            try {
                $subscribeModel->subscribe_object_edit($param);
                $this->success('操作成功',PUBLIC_URL.'postsuccess.html');
            } catch (Exception $e) {
                $this->error('操作失败');
            }
        }

        $Spec=$subscribeModel->getSpec();
        $SpecItem=$subscribeModel->getSpecItem(8);

        // 获取分类
        $subscribe_object_category = $subscribeModel->get_work_content();

        // 获取对象详情
        $datainfo = $subscribeModel->get_subscribe_object_by_id($id);

        $this->assign("siteid", $idsite);
        $this->assign("Spec", $Spec);
        $this->assign("SpecItem", $SpecItem);
        $this->assign("param", $param);
        $this->assign("subscribe_object_category", $subscribe_object_category);
        $this->assign("datainfo", $datainfo);
        return $this->fetch();
    }
    /**
     * 预约对象删除
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-08 14:11:33
     */
    public function object_delete(){
        $param = input('param.');
        $subscribeModel = new subscribeModel;
        $bool = $subscribeModel->subscribe_object_delete($param);
        if($bool){
            return 1;
        }else{
            return 2;
        }
    }
    /**
     * 预约会员卡列表
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-09 15:04:16
     */
    public function member_cart(){
        return $this->fetch();
    }
    /**
     * 预约会员卡新增/编辑
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-09 15:04:52
     */
    public function member_cart_edit(){
        return $this->fetch();
    }
    /**
     * 预约会员卡删除
     *
     * @return void
     * @author Chenjie
     * @Date 2019-07-09 15:05:38
     */
    public function member_cart_delete(){
        return $this->fetch();
    }
}