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
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
if(!empty($data)){   
}else{
$dataa = json_encode($_POST);
$data = json_decode($dataa);
}
$category = trim($data->category);
$accesskey = strtoupper($data->accesskey);
$response = array();
if(!empty($accesskey) && !empty($category)){
//Check access 
$check = $pdoread-> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
//Generate Admission number Start
$country = $pdoread -> prepare("SELECT UPPER(`country`) AS value FROM `nationality` WHERE `status` = 'Active' AND `category` = :category");
$country->bindParam(":category", $category, PDO::PARAM_STR);
$country -> execute();
if($country -> rowCount() > 0){
	http_response_code(200);
	$response['error']= false;
	$response['message']= "Data found";
while($result = $country->fetch(PDO::FETCH_ASSOC)){
$response['countrystate'][] = $result;
}
if($category == 'Foreigner'){
	$response['govtid'][0]['value'] = "Passport";
}else{
			$response['govtid'][0]['value'] = "Aadhar Card";
		$response['govtid'][1]['value'] = "Pancard";
		$response['govtid'][2]['value'] = "Voter ID";
		$response['govtid'][3]['value'] = "Passport";
}
$department = $pdoread-> prepare("SELECT DISTINCT UPPER(`f_getdept`) AS value FROM `doctor_master` WHERE `f_getdept` != '' ORDER BY `f_getdept` ASC");
$department -> execute();
if($department -> rowCount() > 0){
	while($departmentres = $department->fetch(PDO::FETCH_ASSOC)){
$response['department'][0]['value'] = "Select Department";
$response['department'][] = $departmentres;

}
}else{
		$response['department'][0]['value'] = "No Found";
}
$response['ward'][0]['value'] = "GENERAL WARD";
		$response['ward'][1]['value'] = "PRIVATE ROOM";
		$response['ward'][2]['value'] = "TWIN SHARING";
		$response['ward'][3]['value'] = "TRIPLE SHARING";
		$response['ward'][4]['value'] = "VIP";
		$response['ward'][5]['value'] = "DELUXE ROOM";
		$response['ward'][6]['value'] = "SUITE ROOM";
		$response['ward'][7]['value'] = "ICU";
		$response['ward'][8]['value'] = "PICU";
		$response['ward'][9]['value'] = "MICU";
		$response['ward'][10]['value'] = "NICU";
		$response['ward'][11]['value'] = "EMERGENCY";
		$response['ward'][12]['value'] = "DAY CARE";
		$response['ward'][13]['value'] = "RADIOTHERAPY DC";
		$response['ward'][14]['value'] = "OPD";
}else{
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
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>