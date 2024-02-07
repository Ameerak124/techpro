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
$accesskey = strtoupper($data->accesskey);
$searchterm = strtoupper($data->searchterm);
try {
$pdoread = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
// set the PDO error mode to exception
$pdoread->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//echo "Connected successfully";
if(!empty($accesskey) && !empty($searchterm)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
//Generate Admission number Start
$country = $pdoread -> prepare("SELECT `doctorname` AS name,`doctorcd` AS code FROM `doctor_master` WHERE `f_getdept` = :category");
$country->bindParam(':category', $searchterm, PDO::PARAM_STR);
$country -> execute();
if($country -> rowCount() > 0){
	http_response_code(200);
	$response['error']= false;
	$response['message']= "Data found";
while($result = $country->fetch(PDO::FETCH_ASSOC)){
$response['consultant'][0]['name'] = "-Select Doctor-";
$response['consultant'][0]['code'] = "";
$response['consultant'][] = $result;

}
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