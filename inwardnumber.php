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
$response = array();
$accesskey = $data->accesskey;
$inwardnumber = $data->inwardnumber;
try {
if(!empty($accesskey)&&!empty($inwardnumber)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$stmt = $pdoread -> prepare("SELECT inward_security.`inward_id` AS inwardnumber,vendormaster.`legalname` AS vendorname,vendormaster.gstno,vendormaster.payment_terms,inward_security.`vendor_id`, inward_security.`invoice_no`, inward_security.`invoice_date`, inward_security.`dc_no`, inward_security.`dc_date`, inward_security.`ponumber` FROM inward_security INNER JOIN vendormaster ON inward_security.`vendor_id`=vendormaster.sno WHERE `inward_id` LIKE :inward_id");
$stmt->bindParam(':inward_id', $inward_id , PDO::PARAM_STR);
$inwardno='%'.$inwardno;
$stmt -> execute();
if($stmt -> rowCount()>0){
		$result1 = $stmt->fetch(PDO::FETCH_ASSOC);
		 http_response_code(200);
         $response['error']= false;
		 $response['message']="Data found";
		 $response['inwardnumberlist'][]=$result1;
	   
     }else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No data found";
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
	$response['message']= "Connection failed: " ;
	$e;
	}
echo json_encode($response);
$pdoread = null;
?>