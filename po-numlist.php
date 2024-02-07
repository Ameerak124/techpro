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
$vendor_sno= $data->vendor_sno;
$response = array();
try{
if(!empty($accesskey) &&!empty($vendor_sno)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$emp = $check -> fetch(PDO::FETCH_ASSOC);
$stmt1 = $pdoread->prepare("SELECT  `po_number` FROM `po_generate` WHERE `vendor_sno` =:vendor_sno");
$stmt1 -> bindParam(":vendor_sno", $vendor_sno, PDO::PARAM_STR);   
$stmt1 -> execute(); 
   $polist = $stmt1->fetchAll(PDO::FETCH_ASSOC);
     if($stmt1 -> rowCount() > 0){
	  http_response_code(200);
          $response['error']= false;
	     $response['message']="Data found";
		 $response['ponumlist']= $polist;
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
} 
catch(Exception $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e; 
}
echo json_encode($response);
$pdoread = null;
?>