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
$orderno=$data->orderno;
try{
if(!empty($accesskey) &&!empty($orderno)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check-> rowCount() > 0){
$result = $check -> fetch(PDO::FETCH_ASSOC);
 $stmt = $pdoread->prepare ("SELECT `sno`, `umr_no`, `ip_no`, `order_no`, `drug_code`, `drug_name`, `drug_price`, `drug_generic`, `batch_no`, `hsn`, `created_by`, `created_on`, `quantity` FROM `pharmcy_indent` where `order_no` = :orderno AND `is_delete` != '1'");
 $stmt->bindParam(':orderno', $orderno , PDO::PARAM_STR);
 $stmt->execute();
 if($stmt->rowCount() > 0) {
 $res = $stmt-> fetchAll(PDO::FETCH_ASSOC);
	http_response_code(200);
	$response['error']= false;
	$response['message']= "Data found";
	$response['ordermedicineslist'] = $res;
	}
    else{
		http_response_code(503);
		$response['error'] = true;
          $response['message'] = "No data found";
        
    }
}else{
		http_response_code(400);
		$response['error'] = true;
          $response['message'] = "Access denied!";
        
    }

}
else{
	http_response_code(400);
	$response['error'] = true;
$response['message'] = "some details are missing";

}

}catch(PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " .$e;	
	}
echo json_encode($response);
$pdoread = null;
?>
