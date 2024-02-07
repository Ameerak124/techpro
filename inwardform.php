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
$vendor_id= $data->vendor_id;
$invoice_no= $data->invoice_no;
$invoice_date= $data->invoice_date;
$invoice_amount= $data->invoice_amount;
$dc_no= $data->dc_no;
$dc_date= $data->dc_date;
$remarks=$data->remarks;
$response = array();
try{
if(!empty($accesskey) && !empty($vendor_id)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$emp = $check -> fetch(PDO::FETCH_ASSOC);

	$stmt2 = $pdo4->prepare("INSERT INTO `inward_security`( `inward_id`, `vendor_id`, `invoice_no`, `invoice_date`, `invoice_amount`, `dc_no`, `dc_date`, `inward_date`, `created_by`,`remarks`) SELECT (COALESCE((SELECT Concat('MCIN',LPAD((SUBSTRING_INDEX(`inward_id`,'MCIN',-1)+1),'7','0'))  FROM `inward_security` order by `id` desc limit 1),'MCIN0000001')) AS inward_id ,:vendor_id,:invoice_no,:invoice_date,:invoice_amount,:dc_no,:dc_date,CURRENT_TIMESTAMP,:userid,:remarks");
	 $stmt2->bindParam(':vendor_id',$vendor_id, PDO::PARAM_STR);
	 $stmt2->bindParam(':invoice_no',$invoice_no, PDO::PARAM_STR);
	 $stmt2->bindParam(':invoice_date',$invoice_date, PDO::PARAM_STR);
	 $stmt2->bindParam(':invoice_amount',$invoice_amount, PDO::PARAM_STR);
	 $stmt2->bindParam(':dc_no',$dc_no, PDO::PARAM_STR);
	 $stmt2->bindParam(':dc_date',$dc_date, PDO::PARAM_STR);
	 $stmt2->bindParam(':remarks',$remarks, PDO::PARAM_STR);
	 $stmt2->bindParam(':userid',$emp['userid'], PDO::PARAM_STR);
     $stmt2-> execute();
if($stmt2-> rowCount() > 0){
	http_response_code(200);
          $response['error']= false;
	     $response['message']="Successfully created";
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
$pdo4 = null;
$pdoread = null;
?>