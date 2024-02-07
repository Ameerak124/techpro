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
$keyword= $data->keyword;
$response = array();
try{
if(!empty($accesskey) &&!empty($keyword)){
	$check = $pdoread -> prepare("SELECT `userid`,`department` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
	
	$stmt1 = $pdoread->prepare("SELECT `sno`, `itemname`, `itemcode`, `qty`,`mrp`, `batch_no`, `expiry_date`, `hsn`, `uom`, `department`,   `branch`,  `status`  FROM `department_inventory` WHERE (`itemname` LIKE :keyword OR `itemcode` LIKE :keyword) ");
	$stmt1->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$stmt1->bindParam(':keyword', $keyword, PDO::PARAM_STR);
	$stmt1 -> execute();
	if($stmt1 -> rowCount() > 0){
	$data = $stmt1 -> fetch(PDO::FETCH_ASSOC);
	http_response_code(200);
          $response['error']= false;
	     $response['message']="Data Found";
          $response['consumptionsearch']= $data;
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