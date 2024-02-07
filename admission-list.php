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
$fdate= date_format(date_create($data->fdate),"Y-m-d");
$todate = date_format(date_create($data->todate),"Y-m-d");
$response = array();
try {
if(!empty($accesskey)&& !empty($fdate)&& !empty($todate)){

$check = $pdoread-> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$empname = $result['userid'];
if($check -> rowCount() > 0){
$getadm_list =$pdoread->prepare("SELECT `Admissionno`, `Umrno`, `admittedon` AS Dateofadmission, `Patientname`,`Consultantname` AS Consultant, `Department`,`Admittedward`,`Roomno` FROM `registration` WHERE date(`createdon`) BETWEEN :fdate AND :todate");   
   $getadm_list -> bindParam(":fdate", $fdate, PDO::PARAM_STR); 
   $getadm_list -> bindParam(":todate", $todate, PDO::PARAM_STR);
   $getadm_list -> execute(); 
   $admlist = $getadm_list->fetchAll(PDO::FETCH_ASSOC);
     if($getadm_list -> rowCount() > 0){
     http_response_code(200);
          $response['error']= false;
	     $response['message']="Data found";
         $response['admissionlist'] = $admlist;
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
$pdoread= null;
?>