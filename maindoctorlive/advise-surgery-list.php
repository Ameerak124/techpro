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
$accesskey = trim($data->accesskey);
$req_no = trim($data->req_no);
//$ipaddress = $_SERVER['REMOTE_ADDR'];
try {

if(!empty($accesskey) && !empty($req_no)){

//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Create Registration
		$checkq = $pdoread->prepare("SELECT @a:=@a+1 serial_number,`surgery_advise`.`sno` AS cpno,`umr_registration`.`umrno`,CONCAT(`umr_registration`.`title`,'. ',`umr_registration`.`patient_name`,' ',`umr_registration`.`middle_name`,' ',`umr_registration`.`last_name`) AS patientname,`umr_registration`.`patient_gender`,`surgery_advise`.`transfer_status`,`surgery_advise`.`ward`,`surgery_advise`.`doctor_name`,`surgery_advise`.`procedure_surgery`,`surgery_advise`.`adm_date`,`surgery_advise`.`remarks`,DATE_FORMAT(`surgery_advise`.`createdon`,'%d-%b-%Y %h:%i %p') AS createdon,`surgery_advise`.`createdby`  FROM (SELECT @a:= 0) AS a,`surgery_advise` LEFT JOIN `umr_registration` ON `surgery_advise`.`umrno` = `umr_registration`.`umrno` WHERE `req_no` = :req_no AND `estatus` LIKE 'A';");
		$checkq->bindParam(':req_no', $req_no, PDO::PARAM_STR);
		$checkq -> execute();
		if($checkq -> rowCount() > 0){
			while($checkres = $checkq->fetch(PDO::FETCH_ASSOC)){
				$response['advicesurgerylist'][] = $checkres;
			}
		http_response_code(200);
		$response['error']= false;
	$response['message']= "Data found";
	}else{
		http_response_code(503);
	$response['error']= true;
	$response['message']="No data found";
	}
//
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
}
	//
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
$pdoread = null;
?>