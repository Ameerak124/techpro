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
$accesskey = $data->accesskey;
$consultantname = $data->consultantname;
$searchname = $data->searchname;
try {
if(!empty($accesskey) && !empty($consultantname) && !empty($searchname)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$stmt = $pdoread -> prepare("SELECT `patientname`,`patientage`,`patientgender`,`umrno`,`admissionno`,Date_format(`admittedon`,'%d-%b-%Y %H:%S') As admissiondate,'Cash' AS patientype,'CAS' As ptypeshort,`contactno`,Concat(`admissionno`,'/',Date_format(`admittedon`,'%d-%b-%Y')) As ipdate,Date_format(`dischargedon`,'%d-%b-%Y %H:%S') As dischargedate FROM `registration` WHERE `admissionstatus`='discharged' AND `consultantname`=:consultantname AND (`patientname` LIKE :searchname OR `admissionno` LIKE :searchname OR `umrno` LIKE :searchname)");
$stmt->bindParam(':consultantname', $consultantname , PDO::PARAM_STR);
$stmt->bindParam(':searchname', $searchname , PDO::PARAM_STR);
$stmt -> execute();
if($stmt -> rowCount()>0){
		 $result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
		 http_response_code(200);
           $response['error']= false;
		 $response['message']="Data found";
		 $response['discharged-patientslist']=$result1;
	   
     }else
     {
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
}catch(PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " ;
	$e;
	}
echo json_encode($response);
$pdoread = null;
?>