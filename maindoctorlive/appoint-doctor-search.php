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
$searchterm = $data->searchname;
$accesskey = $data->accesskey;	
$response = array();
try{
if(!empty($searchterm) && !empty($accesskey)){
$check = $pdoread->prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$stmt = $pdoread->prepare("SELECT `sno`AS doctorid, `doctor_code`, `doctor_uid` AS doc_id, `doctor_name` AS fullname, `mobile`, `email`, `department`, `qualification` AS Qualification, `designation` AS consultanttype, `registration_number`, `location` AS branch,CONCAT(:domain,'/images/doctor.png') AS image_url, ROUND(`rating`,1) AS rating, `description`, `specialisations`, `services`, `doctorurl`,'available now' AS availability,CONCAT(`fees`,' + ','100 (New pt)') AS disfee,`fees`,if(`onlinestatus`='0','unavailable','available')AS onlineconsultation FROM `doctor_master` WHERE (`doctor_name` LIKE :searchterm AND `status`='Active') OR (`doc_id` LIKE :searchterm AND `status`='Active')");
	$stmt -> bindValue(":searchterm", "%{$searchterm}%", PDO::PARAM_STR);
	$stmt -> bindParam(":domain", $baseurl, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
		 http_response_code(200);
		$response['error'] = false;
		$response['message'] = "Data Found";
	while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
		$response['list'][] = $result; 
	}	
	}else{
		 http_response_code(503);
		$response['error'] = true;
		$response['message'] = "No Data Found";
	}
	}else{
		 http_response_code(400);
		$response['error'] = true;
		$response['message']="Access denied!";
	}
	}else{
		 http_response_code(400);
		$response['error'] = true;
		$response['message'] = "Sorry! some details are missing";
	}
} 
catch(PDOException $e)
{
    die("ERROR: Could not connect. " . $e->getMessage());
}
echo json_encode($response);
$pdoread = null;
?>