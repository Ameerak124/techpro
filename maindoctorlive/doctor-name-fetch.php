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

$accesskey=trim($data->accesskey);
$searchterm=$data->searchterm;
try {
	if(!empty($accesskey) ){

$check = $pdoread -> prepare("SELECT `userid`, `cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result=$check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$country = $pdoread -> prepare("SELECT CONCAT(`title`,' ',`doctor_name`) AS displayname, `doctor_name` AS searchname,`department` AS department, `doctor_uid` AS doctorcode ,`mobile`,`email`,`qualification`,`designation`,`registration_number`,`location`,`rating`,`description`, `specialisations`, `doctor_type`, `consulting_type`, `services`, `doctorurl`, `image_url`, `fees`, `pan`, `onlinestatus`,'' as `slotgap` FROM `doctor_master` WHERE (`doctor_uid` LIKE :searchterm AND  `status`='Active' AND `location`=:branch);");
	$country->bindParam(":searchterm", $searchterm, PDO::PARAM_STR);
	 $country->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$country -> execute();
if($country -> rowCount() > 0){
	http_response_code(200);
	$response['error']= false;
	$response['message']= "Data found";
$result = $country->fetch(PDO::FETCH_ASSOC);
$response['displayname']=$result['displayname'];
$response['searchname']=$result['searchname'];
$response['department']=$result['department'];
$response['doctorcode']=$result['doctorcode'];
$response['mobile']=$result['mobile'];
$response['email']=$result['email'];
$response['qualification']=$result['qualification'];
$response['designation']=$result['designation'];
$response['registration_number']=$result['registration_number'];
$response['location']=$result['location'];
$response['rating']=$result['rating'];
$response['description']=$result['description'];
$response['specialisations']=$result['specialisations'];
$response['doctor_type']=$result['doctor_type'];
$response['consulting_type']=$result['consulting_type'];
$response['services']=$result['services'];
$response['doctorurl']=$result['doctorurl'];
$response['image_url']=$result['image_url'];
$response['fees']=$result['fees'];
$response['pan']=$result['pan'];
$response['onlinestatus']=$result['onlinestatus'];
$response['slotgap']=$result['slotgap'];


}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="No data found";
	$response['displayname']="--";
$response['searchname']="--";
$response['department']="--";
$response['doctorcode']="--";
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
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>