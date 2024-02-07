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
$keyword= $data->keyword;
$response = array();
try{
if(!empty($accesskey) && !empty($keyword)){
	$check = $pdoread -> prepare("SELECT `userid`,`department`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
	$stmt1 = $pdoread->prepare("SELECT `Admissionno`, `Umrno`, `admittedon` AS Dateofadmission, `Patientname`,`Consultantname` AS Consultant, `Department`,`Admittedward`,`Roomno`,attender_name FROM `registration` WHERE cost_center=:cost_center   AND `admissionstatus` != 'Discharged' AND (`admissionno` LIKE :keyword OR `umrno` LIKE :keyword OR `patientname` LIKE  :keyword)");
	
	$stmt1->bindValue(':keyword', "%{$keyword}%", PDO::PARAM_STR);
	$stmt1->bindParam(':cost_center',$emp['cost_center'], PDO::PARAM_STR);
	$stmt1 -> execute(); 
if($stmt1 -> rowCount() > 0){
	$data = $stmt1 -> fetchAll(PDO::FETCH_ASSOC);
	    http_response_code(200);
        $response['error']= false;
	    $response['message']="Data Found";
        $response['admissionsearch']= $data;
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
	$response['message']="Access denied!";
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
$pdoread = null;
?>