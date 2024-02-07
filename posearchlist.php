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
$po_number= $data->po_number;
$response = array();
try{
if(!empty($accesskey) &&!empty($po_number)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$emp = $check -> fetch(PDO::FETCH_ASSOC);
$stmt1 = $pdoread->prepare("SELECT `po_generate`. `po_number` , `vendormaster`.`legalname`,`po_generate`.`po_total`,`unit_master`.`unit`,`po_generate`.`po_status` FROM `po_generate`INNER JOIN `vendormaster` ON `po_generate`.`vendor_sno` = `vendormaster`.`sno` INNER JOIN `unit_master` ON `po_generate`.`invoice_unit`= `unit_master`.`sno` WHERE `po_number` LIKE :po_number");
$stmt1 -> bindParam(":po_number", $po_number, PDO::PARAM_STR);   
$stmt1 -> execute(); 
   $po_list = $stmt1->fetchAll(PDO::FETCH_ASSOC);
     if($stmt1 -> rowCount() > 0){
     http_response_code(200);
          $response['error']= false;
	     $response['message']="Data found";
		 $response['posearchlist']= $po_list;
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