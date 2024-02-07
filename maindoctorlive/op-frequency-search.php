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
$data=json_decode(file_get_contents("php://input"));
$accesskey =$data->accesskey;
$searchterm=$data->searchterm;

$response =array();
try {
      if(!empty($accesskey) && !empty($searchterm)){
   
$check = $pdoread->prepare("SELECT `userid` ,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
$checkresult=$check->fetch(PDO::FETCH_ASSOC);
if($check->rowCount() > 0){
$query=$pdoread->prepare("SELECT `frequency` FROM `doctor_mediciation` WHERE `frequency` LIKE :search AND `vstatus`='Active' GROUP BY `frequency`");
	
$query -> bindValue(":search", "%{$searchterm}%", PDO::PARAM_STR);
$query->execute();

if($query->rowCount()>0){
	http_response_code(200);
 $response['error']=false;
 $response['message']="Data found";
 while($res=$query->fetch(PDO::FETCH_ASSOC)){
    	$response['searchfrequency'][] = $res;
           
 }
}else{
	http_response_code(503);
$response['error']=true;
$response['message']='No Data Found';
}
}else {
	http_response_code(400);
$response['error']=true;
$response['message']='Access denied!';
}
}else {
	http_response_code(400);
$response['error']=true;
$response['message']='Sorry! Some Details Are Missing';
}
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread=null;
?>