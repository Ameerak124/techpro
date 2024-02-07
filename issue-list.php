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
$status= $data->status;
$department= $data->department;
$response = array();
try{
if(!empty($accesskey) && !empty($status)){
	$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
if(empty($department)){
	$stmt2=$pdoread->prepare("SELECT `sno`, `indent_no`, `issue_no`, `remarks`, `department`, `department_code`, `branch`, `branch_code`, `stock_point`, `store`, `status` FROM `issue_stock` WHERE `status`=:status");
$stmt2->bindParam(':status', $status, PDO::PARAM_STR);
}else{
	$stmt2=$pdoread->prepare("SELECT `sno`, `indent_no`, `issue_no`, `remarks`, `department`, `department_code`, `branch`, `branch_code`, `stock_point`, `store`, `status` FROM `issue_stock` WHERE `status`=:status AND `department`=:dept");
$stmt2->bindParam(':status', $status, PDO::PARAM_STR);
$stmt2->bindParam(':dept', $department, PDO::PARAM_STR);
}

$stmt2-> execute();
	if($stmt2 -> rowCount() > 0){
		 http_response_code(200);
          $data = $stmt2 -> fetchAll(PDO::FETCH_ASSOC);
		 $response['error']= false;
		 $response['message']="Data Found";
	    $response['data']= $data;
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
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e;
}
	echo json_encode($response);
   unset($pdoread);
?>
