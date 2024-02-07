<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include 'pdo-db.php';
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$sup_sno = trim($data->sup_sno);
$patient_sno = trim($data->patient_sno);
try {

if(!empty($accesskey)&& !empty($sup_sno)  && !empty($patient_sno)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
	
	
	
	$stmt=$pdoread->prepare("SELECT `uniqueid` FROM `my_supporters` WHERE `sno`=:sup_sno");
	$stmt -> bindParam(':sup_sno', $sup_sno, PDO::PARAM_STR);
	$stmt -> execute();
	if($stmt -> rowCount() > 0){
	$result1 = $stmt->fetch(PDO::FETCH_ASSOC);
	
		$getitem = $pdo4 -> prepare("INSERT INTO `mysupporters_mapping`( `unique_id`, `billno`, `umrno`, `invoice_no`, `trans_id`, branch,`created_on`, `created_by`, `ch_on`, `ch_status`, `audit_on`, `audit_status`, `status`)SELECT :unique_id,`bill_no`,`umrno`,`invoice_no`,`transid`,location,CURRENT_TIMESTAMP,:userid,'0000-00-00 00:00:00','Pending','0000-00-00 00:00:00','Pending','Pending' FROM `patient_details` where sno=:sno");
		$getitem -> bindParam(':sno', $patient_sno, PDO::PARAM_STR);
		$getitem -> bindParam(':unique_id', $result1['unique_id'], PDO::PARAM_STR);
		$getitem -> bindParam(':userid', $result['userid'], PDO::PARAM_STR);
		$getitem -> execute();
		if($getitem -> rowCount() > 0){
            http_response_code(200);
			$response['error'] = false;
			$response['message'] = 'Data found';	
			
		}else{
			   http_response_code(503);
			   $response['error'] = true;
			   $response['message'] = 'No data found';             
			}
	}else{
		http_response_code(503);
		$response['error'] = true;
	    $response['message'] = 'Please select proper supporter'; 
		
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
}
catch(PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message']= $e->getMessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>