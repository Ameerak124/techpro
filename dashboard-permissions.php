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
$accesskey= $data->accesskey;
$response = array();
$response1 = array();
try{
 if(!empty($accesskey)){
$check=$pdoread->prepare("SELECT `userid`,`username`,`role`,`cost_center`, `branch`,`androidpermissions`, `androidsubmenu`, `androiddashboard` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` ='Active'");
$check->bindParam(':accesskey',$accesskey,PDO::PARAM_STR);
$check -> execute();
if($check->rowCount()>0){
$row=$check->fetch(PDO::FETCH_ASSOC);
$userid = $row['userid'];
$mmsss=explode("," ,$row['androiddashboard']);
foreach($mmsss as $key) { 
     $percheck ="SELECT `menu`,`submenu` as name,`dashboardtitle` as title,`packagename` as packagename, `classname`, `classtype`,`ioscontrollers` as controller,`storyboardname`,CONCAT(:baseurl1,'icons/',`d-icons`) AS icons FROM `android_permissions_new` WHERE `sno`=:key and source='Operations'";
          $stmt2=$pdoread->prepare($percheck);
          $stmt2->bindParam(":key", $key, PDO::PARAM_STR);
          $stmt2->bindParam(":baseurl1", $baseurl1, PDO::PARAM_STR);
          $stmt2->execute(); 
     while($rows = $stmt2->fetch(PDO::FETCH_ASSOC)){ 
     $temp=[
     "name"=>$rows['name'],
     "dashboardname"=>$rows['title'],
     "packagename"=>$rows['packagename'],
     "classname"=>$rows['classname'],
     "classtype"=>$rows['classtype'],
     "ioscontrollers"=>$rows['controller'],
     "icon"=>$rows['icons'],
     ];
     array_push($response1,$temp);
	 }
	 if(!empty($response1)){
		http_response_code(200);
     $response['error'] = false;
     $response['message']="Data found";
     $response['dashboardlist']=$response1; 
		 
	 }else{
		  http_response_code(503);
     $response['error'] = true;
     $response['message']="No Data Found";		 
	 }
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
}catch(PDOException $e) {
    http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
     echo json_encode($response);
     $pdoread = null;   
     ?>