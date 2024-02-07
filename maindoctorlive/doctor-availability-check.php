<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
try {
//data credentials
include 'pdo-db.php';
//data credential
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$doctorcode = $data->doctorcode;

if(!empty($accesskey) && !empty($doctorcode)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);
$doctor = $pdoread->prepare("SELECT CONCAT(`title`,' ',`doctor_name`) AS doctorname,`qualification`,`designation`,`location`,`registration_number` FROM `doctor_master` WHERE `doctor_code` = :doctorcode AND `status` = 'Active'");
$doctor->bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
$doctor -> execute();
if($doctor -> rowCount() > 0){
	$docres = $doctor->fetch(PDO::FETCH_ASSOC);
	$response['error'] = false;
	$response['message'] = "Data found";
	$response['doctorname'] = $docres['doctorname'];
	$response['qualification'] = $docres['qualification'];
	$response['designation'] = $docres['designation'];
	$response['registration_number'] = $docres['registration_number'];
	$response['location'] = $docres['location'];
	$location = explode(",",$docres['location']);
	$i = 0;
	foreach($location AS $value){
		$response['branchlist'][$i]['branchname'] = $value;
		$response['branchlist'][$i]['branchcode'] = $value;
		// if($getlist -> rowCount() > 0){
		// 	while($getres = $getlist->fetch(PDO::FETCH_ASSOC)){
		// // $response['list'][$i]['value'][] = $getres;
		// 	}
		// }else{
		// 	$response['error'] = true;
		// 	$response['message'] = "No Data found";
		// }
		$i++;
	}

}else{

}
}	
else
{
	$response['error']= true;
	$response['message']="Access Denied!";
}
}
else{
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} 
catch(PDOException $e) {
	$response['error'] = true;
	$response['message']= $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>