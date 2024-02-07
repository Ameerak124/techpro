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
$accesskey= $data-> accesskey;
$department= $data-> department;
$keyword= $data-> keyword;
$response = array();
$zero = 0;
try{
if(!empty($accesskey) && !empty($department) && !empty($keyword)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
     $empdata = $check -> fetch(PDO::FETCH_ASSOC);
     $empname = $empdata['userid'];
      $stmt = $pdoread -> prepare("SELECT
	sno AS itemsno,
	cen_sno,
	itemname,
	itemcode,
	qty,
	org_qty,
	mrp,
	batch_no,
	expiry_date,
	hsn,
	uom,
	department
FROM
	ms_billing.department_inventory
WHERE
	department_inventory.qty > :zero
	AND department = :department AND (itemname LIKE :keyword OR itemcode LIKE :keyword)");
      $stmt -> bindParam(":zero", $zero, PDO::PARAM_STR);
      $stmt -> bindParam(":department", $department, PDO::PARAM_STR);
      $stmt -> bindValue(":keyword", "%{$keyword}%", PDO::PARAM_STR);
      $stmt -> execute(); 
     if($stmt -> rowCount() > 0){
          $data = $stmt -> fetchAll(PDO::FETCH_ASSOC);
		http_response_code(200);
          $response['error']= false;
	     $response['message']="Data Found";
          $response['conlist']= $data;
     }
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No Data Found!";
     }
}
else{
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