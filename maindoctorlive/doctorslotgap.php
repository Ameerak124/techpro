<?php
header("Content-Type: application/json; charset=UTF-8");
try {
//data credentials
include 'pdo-db.php';
//data credential
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$doctor_code = strtoupper($data->doctor_code);
$docslotgap = (int) $data->docslotgap;



if(!empty($accesskey) && !empty($doctor_code) && !empty($docslotgap)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
	$empname = $result['userid'];
	$cost_center = $result['cost_center'];  
	$update = $pdo4->prepare("UPDATE `doctor_timings` SET `slotgap`= :docslotgap,`modifiedon` = CURRENT_TIMESTAMP,`modifiedby` = :empname  WHERE `location_cd` = :cost_center AND `doctor_code` = :doctor_code AND `status` = 'Active'");
	$update->bindParam(':empname', $empname, PDO::PARAM_STR);
	$update->bindParam(':doctor_code', $doctor_code, PDO::PARAM_STR);
	$update->bindParam(':docslotgap', $docslotgap, PDO::PARAM_STR);
	$update->bindParam(':cost_center', $cost_center, PDO::PARAM_STR);
	$update -> execute();
	if($update -> rowCount() > 0){
		http_response_code(200);
		$response['error'] = false;
		$response['message'] = "Data saved";
	}else{
		http_response_code(503);
		$response['error'] = true;
		$response['message'] = "No data saved";
	}
}
else
{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
}
}
else{
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