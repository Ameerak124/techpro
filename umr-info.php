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
$umrno = trim($data->umrno);
if($accesskey == '' && $umrno == ''){
$accesskey = trim($_POST['accesskey']);
$umrno = trim($_POST['umrno']);
}
try {
$pdoread = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
// set the PDO error mode to exception
$pdoread->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//echo "Connected successfully";
if(!empty($accesskey) && !empty($umrno)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
//$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Generate Registration List
$reglist = $pdoread -> prepare("SELECT `umr_registration`.`umrno`,`umr_registration`.`patient_name` AS patientname,`umr_registration`.`patient_age` AS dob,DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`umr_registration`.`patient_age`)), '%Y')+0 AS Age,`patient_gender` AS gender,`umr_registration`.`mobile_no` AS contactno,`umr_registration`.`email_id` AS emailid,`umr_registration`.`state`,`umr_registration`.`address`,`umr_registration`.`city` AS city,`payment_history`.`receiptno`,`payment_history`.`amount`,`payment_history`.`paymentmode` FROM `umr_registration` INNER JOIN `payment_history` ON `umr_registration`.`umrno` = `payment_history`.`admissionon` WHERE `umr_registration`.`umrno` LIKE :umrno AND `umr_registration`.`status` = 'Visible'");
$reglist -> bindValue(":umrno", "%{$umrno}%", PDO::PARAM_STR);
$reglist -> execute();
if($reglist -> rowCount() > 0){
	http_response_code(200);
		$response['error']= false;
	$response['message']= "Data found";
	while($regres = $reglist->fetch(PDO::FETCH_ASSOC)){
		$response['umrinfolist'][] = $regres;
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