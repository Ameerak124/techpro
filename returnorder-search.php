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
$accesskey = $data->accesskey;
$searchname = $data->searchname;
$response = array();
try{
if(!empty($accesskey) && !empty($searchname) ){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$empdata = $check ->fetch(PDO::FETCH_ASSOC);
$sbmt = $pdoread->prepare("SELECT `sno` ,`order_no`,`itemname`,`itemcode`,`price`,`umrno`,`ipno`,`qty` FROM `pharmcy_returns` WHERE `itemname` LIKE :searchname");
$sbmt-> bindvalue(':searchname',"%{$searchname}%", PDO::PARAM_STR);
$sbmt -> execute();
if($sbmt -> rowCount() > 0){
		 http_response_code(200);
		 $result = $sbmt->fetchAll(PDO::FETCH_ASSOC);
		 $response['error']= false;
		 $response['message']="Data found";
		 $response['returnorderlist']=$result;
     }
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']=" No data found";
     }
	 
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
}

}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e;
}
	echo json_encode($response);
   unset($pdoread);
?>
