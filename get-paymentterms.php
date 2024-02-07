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
try{
if(!empty($accesskey)){
	$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$empname = $result['userid'];
  $result = $check->fetch(PDO::FETCH_ASSOC);
     $query = "SELECT `sno`, `terms` FROM `payment_terms`";   
     $sbmt = $pdoread -> prepare($query);   
     $sbmt -> execute();
     if($sbmt -> rowCount() > 0){
          $data = $sbmt -> fetchAll(PDO::FETCH_ASSOC);
		  http_response_code(200);
          $response['error']= false;
	     $response['message']="Data Found";
          $response['paymenttermslist']= $data;
     }
     else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No Data Found!";
     }
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
	echo json_encode($response);
   unset($pdoread);
?>








