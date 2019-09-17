<?php
namespace app\admin\controller;
use think\Controller;
use think\Image;
use think\Request;
use Uploader;
use think\Session;
 
class Ueditorr {
	/**
     * 析构函数
     */
    function __construct(){
	    Session::start();       
    }  
	public function index(){
        //header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
        //header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
        date_default_timezone_set("Asia/chongqing");
        error_reporting(E_ERROR);
		header("Content-Type: text/html; charset=utf-8");
		$idsite = session('idsite') ? session('idsite') : 0;   //站点ID
 
		// 读取json文件内容
		$filename = ROOT_PATH."public"."/static/plugins/Ueditor/php/config.json";
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize ($filename));
		fclose($handle);

		// 格式化JSON配置文件
		$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", $contents), true);
		// $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("./static/plugins/ueditor/php/config.json")), true);
	
		$CONFIG['imagePathFormat'] = '/uploads/'.$idsite.'/{yyyy}/{mm}-{dd}/{time}{rand:6}';
		$CONFIG['scrawlPathFormat'] = '/uploads/'.$idsite.'/{yyyy}/{mm}-{dd}/{time}{rand:6}';
		$CONFIG['snapscreenPathFormat'] = '/uploads/'.$idsite.'/{yyyy}/{mm}-{dd}/{time}{rand:6}';
		$CONFIG['catcherPathFormat'] = '/uploads/'.$idsite.'/{yyyy}/{mm}-{dd}/{time}{rand:6}';
		$CONFIG['videoPathFormat'] = '/uploads/'.$idsite.'/{yyyy}/{mm}-{dd}/{time}{rand:6}';
		$CONFIG['filePathFormat'] = '/uploads/'.$idsite.'/{yyyy}/{mm}-{dd}/{time}{rand:6}';

        $action = $_GET['action'];
		$base64 = "upload";
        switch ($action) {
            case 'config':
                $result =  json_encode($CONFIG);
                break;
            /* 上传图片 */
			case 'uploadimage':
				$config = array(
					"pathFormat" => $CONFIG['imagePathFormat'],
					"maxSize" => $CONFIG['imageMaxSize'],
					"allowFiles" => $CONFIG['imageAllowFiles']
				);
				$fieldName = $CONFIG['imageFieldName'];
				$uploader = new \Uploader($fieldName, $config, $base64);
				$result = json_encode($uploader->getFileInfo());
		        break;
            /* 上传涂鸦 */
            case 'uploadscrawl':
				$config = array(
					"pathFormat" => $CONFIG['scrawlPathFormat'],
					"maxSize" => $CONFIG['scrawlMaxSize'],
					"allowFiles" => $CONFIG['scrawlAllowFiles'],
					"oriName" => "scrawl.png"
				);
				$fieldName = $CONFIG['scrawlFieldName'];
				$base64 = "base64";
				$uploader = new \Uploader($fieldName, $config, $base64);
				$result = json_encode($uploader->getFileInfo());
		        break;
            /* 上传视频 */
            case 'uploadvideo':
				$config = array(
					"pathFormat" => $CONFIG['videoPathFormat'],
					"maxSize" => $CONFIG['videoMaxSize'],
					"allowFiles" => $CONFIG['videoAllowFiles']
				);
				$fieldName = $CONFIG['videoFieldName'];
				$uploader = new \Uploader($fieldName, $config, $base64);
				$result = json_encode($uploader->getFileInfo());
		        break;
            /* 上传文件 */
			case 'uploadfile':
				$config = array(
					"pathFormat" => $CONFIG['filePathFormat'],
					"maxSize" => $CONFIG['fileMaxSize'],
					"allowFiles" => $CONFIG['fileAllowFiles']
				);	
		        $fieldName = $CONFIG['fileFieldName'];
				$uploader = new \Uploader($fieldName, $config, $base64);
				$result = json_encode($uploader->getFileInfo());
                break;
            /* 列出图片 */
            case 'listimage':
			    $allowFiles = $CONFIG['imageManagerAllowFiles'];
			    $listSize = $CONFIG['imageManagerListSize'];
				$path = $CONFIG['imageManagerListPath'];
				$listPath = './public/uploads/'.$idsite;
			    $get =$_GET;
			    $result =$this->fileList($allowFiles,$listSize,$get,$listPath);
                break;
            /* 列出文件 */
            case 'listfile':
			    $allowFiles = $CONFIG['fileManagerAllowFiles'];
			    $listSize = $CONFIG['fileManagerListSize'];
				$path = $CONFIG['fileManagerListPath'];
				$listPath = './public/uploads/'.$idsite;
			    $get = $_GET;
			    $result = $this->fileList($allowFiles,$listSize,$get,$listPath);
                break;
            /* 抓取远程文件 */
            case 'catchimage':
		    	$config = array(
			        "pathFormat" => $CONFIG['catcherPathFormat'],
			        "maxSize" => $CONFIG['catcherMaxSize'],
			        "allowFiles" => $CONFIG['catcherAllowFiles'],
			        "oriName" => "remote.png"
			    );
			    $fieldName = $CONFIG['catcherFieldName'];
			    /* 抓取远程图片 */
			    $list = array();
			    isset($_POST[$fieldName]) ? $source = $_POST[$fieldName] : $source = $_GET[$fieldName];
				
				$no_down_domain = config('no_down_domain');  // 不下载图片域名
				
			    foreach($source as $imgUrl){
					$domain = parse_url($imgUrl)['host'];  // 网址域名
					if(in_array($domain, $no_down_domain)){
						$item = new \Uploader($imgUrl, $config, "remote");
						$info = $item->getFileInfo();
						array_push($list, array(
							"state" => $info["state"],
							"url" => $info["url"],
							"size" => $info["size"],
							"title" => htmlspecialchars($info["title"]),
							"original" => htmlspecialchars($info["original"]),
							"source" => htmlspecialchars($imgUrl)
						));
					}else{
						$size = $this->getPicSize($imgUrl);
						$file_name = basename($imgUrl);
						array_push($list, array(
							"state" => "SUCCESS",
							"url" => $imgUrl,
							"size" => $size,
							"title" => htmlspecialchars($file_name),
							"original" => htmlspecialchars($file_name),
							"source" => htmlspecialchars($imgUrl)
						));
					}
			    }
 
			    $result = json_encode(array(
			        'state' => count($list) ? 'SUCCESS':'ERROR',
			        'list' => $list
			    ));
                break;
            default:
                $result = json_encode(array(
                    'state' => '请求地址出错'
                ));
                break;
        }
 
        /* 输出结果 */
        if(isset($_GET["callback"])){
            if(preg_match("/^[\w_]+$/", $_GET["callback"])){
                echo htmlspecialchars($_GET["callback"]).'('.$result.')';
            }else{
                echo json_encode(array(
                    'state' => 'callback参数不合法'
                ));
            }
        }else{
            echo $result;
        }
	}
	
    //列出图片
	private function fileList($allowFiles,$listSize,$get,$listPath){
		$dirname = $listPath;
		$allowFiles = substr(str_replace(".","|",join("",$allowFiles)),1);
 
		/* 获取参数 */
		$size = isset($get['size']) ? htmlspecialchars($get['size']) : $listSize;
		$start = isset($get['start']) ? htmlspecialchars($get['start']) : 0;
		$end = $start + $size;
 
		/* 获取文件列表 */
		$path = $dirname;
		$files = $this->getFiles($path,$allowFiles);
		if(!count($files)){
		    return json_encode(array(
		        "state" => "no match file",
		        "list" => array(),
		        "start" => $start,
		        "total" => count($files)
		    ));
		}
 
		/* 获取指定范围的列表 */
		$len = count($files);
		for($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
		    $list[] = $files[$i];
		}
 
		/* 返回数据 */
		$result = json_encode(array(
		    "state" => "SUCCESS",
		    "list" => $list,
		    "start" => $start,
		    "total" => count($files)
		));
 
		return $result;
	}
 
   	/*
	 * 遍历获取目录下的指定类型的文件
	 * @param $path
	 * @param array $files
	 * @return array
	*/
    private function getFiles($path,$allowFiles,&$files = array()){
	    if(!is_dir($path)) return null;
	    if(substr($path,strlen($path)-1) != '/') $path .= '/';
	    $handle = opendir($path);
			
	    while(false !== ($file = readdir($handle))){
	        if($file != '.' && $file != '..'){
	            $path2 = $path.$file;
	            if(is_dir($path2)){
	                $this->getFiles($path2,$allowFiles,$files);
	            }else{
		            if(preg_match("/\.(".$allowFiles.")$/i",$file)){
		                $files[] = array(
		                    'url' => substr($path2,1),
		                    'mtime' => filemtime($path2)
		                );
		            }
	            }
	        }
	    }
		
	    return $files;
	}
	
	// 获取远程图片大小
	private function getPicSize($url){
		if($url){
			$header_array = get_headers($url, true);
			$size = $header_array['Content-Length'];
		}else{
			$size = 0;
		}
		return $size;
	}
}