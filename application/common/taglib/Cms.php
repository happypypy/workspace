<?php
namespace app\taglib;
use think\template\TagLib;

/**
 * 自定义标签
 */

class Cms extends TagLib {
	protected $tags = array(
		'node' => array('attr'=>'ipage,limit,order,where,item,pid','close'=>1),
	    'page' => array('attr'=>'url,pagecount,ipage,pagesize','close'=>1),
	    'connect' => array('attr'=>'item,pid','close'=>1),
	    'content' => array('attr'=>'limit,where,item,pid','close'=>1),
	    // 'page' => array('attr'=>'item,count','close'=>1),
	    'pagecontent' => array('attr'=>'item,pagesize,ipage','close'=>1),
	    'details' => array('attr'=>'item,key,pid','close'=>1),
	    'page111' => array('attr'=>'item,pagesize,order,','close'=>1),
	    'table'   =>array('attr'=>'item,pid,key','close'=>1),
	    
	);
	public function _node($tag,$content){
     	$order = $tag['order']; //排序
        $ipage = !empty($tag['ipage ']) ? $tag['ipage'] : '1'; 
        $limit = !empty($tag['limit']) ? $tag['limit'] : '1'; 
        $item  = !empty($tag['item']) ? $tag['item'] : 'item';// 返回的变量item	
        $key  =  !empty($tag['key']) ? $tag['key'] : 'key';// 返回的变量key
        $pid  =  $tag['pid']!= null? $tag['pid'] : '-1';
        $where=!empty($tag['where']) ?$tag['where'] : ''; 
        $str = '<?php ';
        $str .= '$pid ='.$pid.';';
        $str .= '$where ="'.$where.'";';
        $str .= '$result =getMenu($pid,$where,$limit,$order,$ipage);';
        $str .= 'foreach($result as $'.$key.'=>$'.$item.'):';
        $str .= '?>';
        $str .=  $this->tpl->parse($content);
        $str .= '<?php endforeach; ?>';
        return $str;
	}

	public  function _connect($tag,$content){
	    $pid  =  !empty($tag['pid']) ? $tag['pid'] : '-1';
	    $item=!empty($tag['item']) ? $tag['item'] : 'item';// 返回的变量item
	    $key= !empty($tag['key']) ? $tag['key'] : 'key';// 返回的变量key
	    $str = '<?php ';
	    $str .= '$pid ='.$pid.';';
	  //  $str .= '$result = D("cms_node")->where("parentid=$pid")->limit("'.$limit.'")->select();';
	    $str .= '$result =getConnect($pid);';
	    $str .= 'foreach($result as $'.$key.'=>$'.$item.'):';
	    $str .= '?>';
	    $str .=  $this->tpl->parse($content);
	    $str .= '<?php endforeach; ?>';
	    return $str;    
	}
	
  	public  function _content($tag,$content){
	    $pid  =  !empty($tag['pid']) ? $tag['pid'] : '';
	    $item=!empty($tag['item']) ? $tag['item'] : 'item';// 返回的变量item
	    $key= !empty($tag['key']) ? $tag['key'] : 'key';// 返回的变量key
 	    $order=!empty($tag['order']) ?$tag['order'] : '';
	    $limit = !empty($tag['limit']) ? $tag['limit'] : '1';	 
	    $str = '<?php ';
	    if(empty($pid)){
	      $str .= '';
	    }else {
	        $str .= '$pid ='.$pid.';';
	    } 
	    $str .= '$limit ='.$limit.';';
		   // $str .= '$order ='.$order.';';
	    $str .= '$result =getList($pid,$limit,$order);';
	    $str .= 'foreach($result as $'.$key.'=>$'.$item.'):';
	    $str .= '?>';
	    $str .=  $this->tpl->parse($content);
	    $str .= '<?php endforeach; ?>';
	    return $str;
	}
	
	public  function _pagecontent($tag,$content){
	    $pagesize  =  !empty($tag['pagesize']) ? $tag['pagesize'] : '1';
	    $ipage  =  !empty($tag['ipage']) ? $tag['ipage'] : '1';
	    $item=!empty($tag['item']) ? $tag['item'] : 'item';// 返回的变量item
	    $key= !empty($tag['key']) ? $tag['key'] : 'key';// 返回的变量key
	    $str = '<?php ';
        echo $pagesize;
     	$str .= '$pagesize ='.$pagesize.';';
	    $str .= '$result =getPage($pagesize);';
	    $str .= 'foreach($result as $'.$key.'=>$'.$item.'): ';
	    //$str .= '$result1 = M("cms_content")->where("nodeid=1")->count();';
	    $str .= '$pagecount =M("cms_content")->where("nodeid=1")->count();';
        $str .= '?>';
	    $str .=  $this->tpl->parse($content);
	    $str .= '<?php endforeach; ?>';
	    return $str;
	}
	
	public function  _details($tag,$content){
	    $pid=!empty($tag['pid']) ? $tag['pid'] : '1';
	    $item=!empty($tag['item']) ? $tag['item'] : 'item';// 返回的变量item
	    $key= !empty($tag['key']) ? $tag['key'] : 'key';// 返回的变量key
	   //  echo $pid;	 
        $str = '<?php ';
	    $str .= '$pid ='.$pid.';';
	    $str .= '$result =getDetails($pid);';
	    $str .= 'foreach($result as $'.$key.'=>$'.$item.'):';
	    $str .= '?>';
	    $str .=  $this->tpl->parse($content);
	    $str .= '<?php endforeach; ?>';
	    return $str;    
	    
	}
	



	
	}
	
		
	
	
	




 