<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey=$data->accesskey;
$response = array();
$response1 = array();

if(!empty($accesskey)){
$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role,androidpermissions,androidsubmenu,androiddashboard FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$userid = $row['userid'];
	$mmsss=explode("," ,$row['androiddashboard']);

 foreach($mmsss as $key) { 
$percheck ="SELECT `menu`,`submenu` As name,`dashboardtitle` As title,`packagename` As packagename,`ioscontrollers` as webcontroller,`classname`, `classtype`,`storyboardname`,CONCAT(:baseurl,'appicons/',`d-icons`) as icon  FROM `android_permissions_new` WHERE `sno`=:key AND `source` ='dynamic'";
	$stmt2 = $pdoread->prepare($percheck);
	$stmt2->bindParam(":key", $key, PDO::PARAM_STR);
	$stmt2->bindParam(":baseurl", $baseurl, PDO::PARAM_STR);
	$stmt2->execute();
	$rows = $stmt2->fetch(PDO::FETCH_ASSOC);
	if($stmt2->rowCount() > 0){
	

$temp=[
"name"=>$rows['name'],
"displayname"=>$rows['title'],
"title"=>$rows['title'],
"packagename"=>$rows['packagename'],
"ioscontrollers"=>$rows['webcontroller'],
"storyboardname"=>$rows['storyboardname'],
"classname"=>$rows['classname'],
"classtype"=>$rows['classtype'],
"icon"=>$rows['icon'],
// "url"=>$url

];
array_push($response1,$temp);
}else{
http_response_code(503);
$response['error'] = true;
$response['message']="No Data Found";
} 
http_response_code(200);
$response['error'] = false;
$response['message']="Data found";
$response['gridcount']="3";
$response['mobilegridcount']="2";
$response['dashboardlist']=$response1;
 }
}else{
http_response_code(400);
$response['error']= true;
$response['message']="Access denied!";
}
}else{	
http_response_code(400);
$response['error']= true;
$response['message']="Sorry some details missing";
}
echo json_encode($response);
$pdoread = null;
?>