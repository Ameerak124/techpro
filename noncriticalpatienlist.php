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
$accesskey = trim($data->accesskey);
$response = array();
try {
if(!empty($accesskey)){

$check = $pdoread-> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$empname = $result['userid'];
if($check -> rowCount() > 0){
$ctr_pnts =$pdoread->prepare("SELECT DISTINCT `admittedward`,'p' AS type ,`patientname`,(DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`patientage`)), '%Y')+0) AS `patientage`,`patientgender`,`umrno`,`admissionno`,`roomno` FROM `registration`WHERE NOT (`admittedward` LIKE('%er%') OR `admittedward` LIKE('%icu%'))"); 
$ctr_pnts -> execute(); 
   $ctrpntslist = $ctr_pnts->fetchAll(PDO::FETCH_ASSOC);
     if($ctr_pnts -> rowCount() > 0){
     http_response_code(200);
     $response['error']= false;
	 $response['message']="Data found";
     $response['criticalpatientslist'] = $ctrpntslist;
     }
     else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="Data not found";
     }
}
else
{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access Denied! Pleasae try after some time";
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
$pdoread = null;
?>