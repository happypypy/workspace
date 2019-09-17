
<?php
header('Content-Type:text/html;charset=UTF-8');
//error_reporting(0);
require_once 'AipFace.php';

// 你的 APPID AK SK
//const APP_ID = '11635999';
//const API_KEY = '7Ci11DT5VmibtTvsivvw5LG1';
//const SECRET_KEY = 'qHKu29rrtKLkIfnsL1oDsfyDvdO7vST6';

//const APP_ID = '17072319';
//const API_KEY = 'sEccFxMx8KZWUYr4HahHlXtG';
//const SECRET_KEY = 'HTHsz1fAbCI3GiIcI1uFYmNYmxyAnjiy';

const APP_ID = '17135991';
const API_KEY = 'bngz5OfYz0sXNqwzOWYLkdgW';
const SECRET_KEY = '3DKKyvGQbhXnrQT1I7KLpVBFBpc4Qs7I';



$client = new AipFace(APP_ID, API_KEY, SECRET_KEY);
//创建用户组
//$result= $client->groupAdd('test_group_2',array());
//组列表查询
//$result = $client->getGroupList();


//$image ="http://localhost:8899/img/face_recognizer.jpg";
//$imgPathRoot="C:\Users/Administrator/Desktop/aip-php-sdk-2.2.16/img/";
//$imgPath=$imgPathRoot."demo-card-2.jpg";
//图片的路径
//$imgPath="img/demo-card-2.jpg";//[face_token] => 6b31a23252305ff03fd33d261c6961b2  图片的token
//$imgPath="img/6b31a23252305ff03fd33d261c6961b2.jpg";//图片的token   66f6bc60eed881d309efb868638bcab2
//$imgPath="img/face_recognizer.jpg";//图片的token   40f9c3df5d14ae4846b6a503c0be3622
//$imgPath="img/Obama.png";//图片的token   3b4fbeaf0472c5ccc193c56e92e77bd2
//$imgPath="img/wx_cat1.png";//图片的token   074a45b5f03ff330573d74262d6b7361
//$imgPath="img/wx_cat2.png";//图片的token   5448c327af2e9161be18f5cd34e6829e


//$imgPath="img/den_1.jpg";//图片的token   b8c3fa3d4130a0651109efc8140ba766
//$imgPath="img/den_2.jpeg";//图片的token   928bd9bd1cb5d8328b2547858175de46
//$imgPath="img/den_3.jpeg";//图片的token   a474c673c9c5ad42b96843797194ce68
//$imgPath="img/den_4.jpeg";//图片的token   7e4df0e6c70c603dadf782451fd69e04
//$imgPath="img/den_5.jpg";//图片的token   0dee21c6521fd4d720f1ae7ad709c98d
//$imgPath="img/den_6.jpeg";//图片的token   915c0490052dc74e1fa12a073e9a91bd
//$imgPath="img/den_7.jpg";//图片的token   915c0490052dc74e1fa12a073e9a91bd
$imgPath="img/den_10.jpg";//图片的token   915c0490052dc74e1fa12a073e9a91bd
//将图片转换为base64的格式
$imgBase64=$client->imgToBase64($imgPath);
$imageType = 'BASE64';

//var_dump($img1);exit;222210超过人脸数量
//人脸注册
//$result = $client->addUser($imgBase64,$imageType,'test_group_2','user_id'.time());
//$result = $client->addUser($imgBase64,$imageType,'test_group_2','user_id1567152068',['user_info'=>json_encode(['img_url'=>$imgPath],JSON_UNESCAPED_UNICODE)]);
//var_dump($result);exit;
//人脸删除
//$result = $client->faceDelete('user_id1567067270','test_group_1','66f6bc60eed881d309efb868638bcab2');
$user_info = ['user_info'=>json_encode(['img_url'=>$imgPath],JSON_UNESCAPED_UNICODE)];
//var_dump($result);exit;
//人脸更新
//$result = $client->updateUser($imgBase64,$imageType,'test_group_2','user_id1567152068',$user_info);
//获取用户人脸列表      只有返回每个人脸的face_token和ctime
//$result = $client->faceGetlist('user_id1567152068','test_group_2');
//用户信息查询(user_info信息和用户所属的组)。
//$result = $client->getUser('user_id1567152068','test_group_2');
//var_dump($result['result']);exit;
//var_dump(json_decode($result['result']['user_list'][0]['user_info'],true));   //能获取到图片中的用户的信息

//获取用户列表(用于查询指定用户组中的用户列表)
//$result = $client->getGroupUsers('test_group_1');
//删除用户(同时把该用户下的人脸也全部删除了)
//$result = $client->deleteUser('test_group_2','user_id1567131159');


//$image = file_get_contents('1.jpg');
//图片类型
//BASE64:图片的base64值，base64编码后的图片数据，编码后的图片大小不超过2M；
//URL:图片的 URL地址( 可能由于网络等原因导致下载图片时间过长)；
//FACE_TOKEN: 人脸图片的唯一标识，调用人脸检测接口时，会为每个人脸图片赋予一个唯一的FACE_TOKEN，同一张图片多次检测得到的FACE_TOKEN是同一个。
//$imageType="URL";


// 如果有可选参数
//$options = array();
////$options["face_field"] = "beauty,glasses,landmark,gender,expression,age";
//$options["max_face_num"] = 5;//最多处理人脸的数目
//$options["face_type"] = "LIVE";
//// 调用人脸检测(检测图片中的人脸并标记出位置信息;)
//$result = $client->detect($imgBase64, $imageType ,$options);
//print_r($result['result']);exit;


// 带参数调用人脸检测
//print_r($client->detect($image, $imageType, $options));
//$SSS=$client->detect($image, $imageType, $options);
//$SSS=$client->detect($imgBase64, 'BASE64', $options);

//用户分组，以逗号形式隔开，最多20个
$groupIdList = "test_group_2";

$imgPath="img/132.jpg";//图片的token   915c0490052dc74e1fa12a073e9a91bd
//将图片转换为base64的格式
$imgBase64=$client->imgToBase64($imgPath);
$imageType = 'BASE64';
// 如果有可选参数
$options = array();
$options["max_face_num"] = 1;//最多处理人脸的数目
$options["match_threshold"] = 70;//此阈值设置得越高，检索速度将会越快，推荐使用默认阈值80
//$options["quality_control"] = "NORMAL";
//$options["liveness_control"] = "LOW";
//$options["user_id"] = "user_id1567152068";
$options["max_user_num"] = 20;

// 带参数调用1：N人脸搜索 识别
//$result =  $client->search('a474c673c9c5ad42b96843797194ce68', 'FACE_TOKEN', $groupIdList, $options);
$result =  $client->search($imgBase64, $imageType, $groupIdList, $options);
print_r($result);exit;
//带参数调用M：N人脸搜索 识别
//$result =  $client->multiSearch($imgBase64, $imageType, $groupIdList, $options);
//print_r($result);exit;

//print("<pre>"); // 格式化输出数组
// print_r($SSS);
// print("</pre>");
//echo $SSS['result']['face_list'][0]['landmark'][0]['x'];
//exit();
/*
$str="";
for($x=0; $x<=71; $x++)
{
$str=$str."\r\n[".$x."]";
$str=$str."\r\nx=".floor($SSS['result']['face_list'][0]['landmark72'][$x]['x']);
$str=$str."\r\ny=".floor($SSS['result']['face_list'][0]['landmark72'][$x]['y']);
 
}
//写出ini文件
file_put_contents("test.ini",$str);
*/

if($SSS['error_code']!=0)
{
    echo "Error!";
}

for ($i=0;$i<$SSS['result']['face_num'];$i++)
{
    $tmpPath='img/'.$SSS['result']['face_list'][$i]['face_token'].".jpg";
    $x=$SSS['result']['face_list'][$i]['location']['left'];
    $y=$SSS['result']['face_list'][$i]['location']['top'];
    $w=$SSS['result']['face_list'][$i]['location']['width'];
    $h=$SSS['result']['face_list'][$i]['location']['height'];
    $client->image_cut($imgPath,$tmpPath,$x,$y,$w,$h);
}




//绘制信息面板male男female女
$text1=$SSS['result']['face_list'][0]['gender']['type'];
//none无表情smile 微笑 laugh 大笑
$text2=$SSS['result']['face_list'][0]['expression']['type'];
$text3=$SSS['result']['face_list'][0]['beauty'];
$text4=$SSS['result']['face_list'][0]['age'];
$text5=$SSS['result']['face_list'][0]['glasses']['type'];

if($text1=='male')
{
//imagestring($im,16,10,50,"Gender:".$text1,$lan);
//    imagefttext($im, 13, 0, 10, 50, $lan, './a.ttf',iconv('GB2312','UTF-8','性别:♂男'));
    echo "<br>性别:♂男";

}
else{//imagestring($im,16,10,50,"Gender:".$text1,$red);
    //imagefttext($im, 13, 0, 10, 50, $red, './a.ttf',iconv('GB2312','UTF-8','性别:♀女'));
    echo "<br>性别:♂女";
}

//imagestring($im,16,10,70,"表情:".$text2,$red);
//imagefttext($im, 13, 0, 10, 70, $red, './a.ttf',iconv('GB2312','UTF-8','表情:'.$text2));
echo "<br>表情:".$text2;
// imagestring($im,16,10,90,"颜值:".$text3,$red);
//imagefttext($im, 13, 0, 10, 90, $red, './a.ttf',iconv('GB2312','UTF-8','颜值:'.$text3));
echo "<br>颜值:".$text3;

// imagestring($im,16,10,110,"年龄".$text4,$red);
//imagefttext($im, 13, 0, 10, 110, $red, './a.ttf',iconv('GB2312','UTF-8','年龄:'.$text4));
echo "<br>年龄:".$text4;

//imagestring($im,16,10,130,"眼镜:".$text5,$red);
//imagefttext($im, 13, 0, 10, 130, $red, './a.ttf',iconv('GB2312','UTF-8','眼镜:'.$text5));
echo "<br>眼镜:".$text5;

//imagefttext($im, 13, 0, 10, 55, $red, './a.ttf',iconv('GB2312','UTF-8','中文水印'));
//图像,size,角度,x,y,颜色,字体

//header("content-type:image/png");
//imagepng($im,"C:\\Users\\Administrator\\Desktop\\aip-php-sdk-2.2.16\\img\\aaa.png",100);
//imagedestory($im);
echo "OK";

?>
