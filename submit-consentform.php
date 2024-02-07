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
$umrno= $data->ipno;
$consent_id= $data->consent_id;
$patient_sign_one= $data->patient_sign_one;
$patient_name= $data->patient_name;
$witness_sign=$data->witness_sign;
$witness_name=$data->witness_name;
$doctor_sign=$data->doctor_sign;
$doctor_name=$data->doctor_name;
$content=$data->consent;
$interpreter_sign=$data->interpreter_sign;
$interpreter_name=$data->interpreter_name;
$response = array();
try{
if(!empty($accesskey) && !empty($umrno) && !empty($patient_sign_one)  && !empty($doctor_sign)){
	
	$check = $pdoread -> prepare("SELECT `userid`,`department`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
	
	$check1 = $pdoread -> prepare("SELECT * FROM `consent_patient_data` where `ipno`=:ipno and `consent_id`=:consent_id");
$check1->bindParam(':consent_id', $consent_id, PDO::PARAM_STR);
$check1->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$check1 -> execute();
if($check1 -> rowCount() > 0){
	http_response_code(503);
    $response['error']= true;
  	$response['message']="Already submitted";
	
}else{
	$stmt2 = $pdoread->prepare("SELECT `admissionno`,registration.`umrno`,`billno`,DATE_FORMAT(`admittedon`,'%d-%b-%Y %h:%i %p') AS admittedon,UPPER(`patientname`) AS patientname,DATE_FORMAT(`admittedon`,'%d-%b-%Y') AS admitteddate,DATE_FORMAT(`admittedon`,'%h:%i %p') AS admittedtime,DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`patientage`)), '%Y')+0 AS Age,`patientgender` AS gender,`contactno` AS mobile,`map_ward` AS ward,`roomno` AS bedno,`consultantname` AS consultant,`department` AS department,`procedure_surgery` AS surgery,'info@medicoverhospitals.in' AS emailid,`patient_category` AS category,`nursing_notes`,if(umr_registration.address='','-----',umr_registration.address) AS address,registration.cost_center,`tpa_name`,registration.organization_name  FROM `registration` INNER join umr_registration on umr_registration.umrno=registration.umrno WHERE `admissionno` LIKE :search AND `admissionstatus` != 'Discharged' AND registration.`status` = 'Visible' AND `patient_signature`='' AND registration.cost_center=:cost_center ORDER BY `admissionno` DESC");

	$stmt2->bindParam(':search', $umrno, PDO::PARAM_STR);
	$stmt2->bindParam(':cost_center', $emp['cost_center'], PDO::PARAM_STR);
	$stmt2 -> execute(); 
	$data = $stmt2 -> fetch(PDO::FETCH_ASSOC);
	$stmt1 = $pdo4->prepare("INSERT INTO `consent_patient_data`( ipno,`umrno`,billno, `consent_id`, `patient_sign_one`, `patient_name`, `patient_on`, `witness_sign`, `witness_on`, `doctor_sign`, `doctor_name`, `doctor_on`, `interpreter_sign`, `interpreter_on`, `status`,content) VALUES (:ipno,:umrno,:billno,:consent_id,:patient_sign_one,:patientname,CURRENT_TIMESTAMP,:witness_sign,CURRENT_TIMESTAMP,:doctor_sign,:doctor_name,CURRENT_TIMESTAMP,:interpreter_sign,CURRENT_TIMESTAMP,'Active',:content)");
	
	$stmt1->bindParam(':ipno', $umrno, PDO::PARAM_STR);
	$stmt1->bindParam(':umrno', $data['umrno'], PDO::PARAM_STR);
	$stmt1->bindParam(':content', $content, PDO::PARAM_STR);
	$stmt1->bindParam(':billno', $data['billno'], PDO::PARAM_STR);
	$stmt1->bindParam(':consent_id', $consent_id, PDO::PARAM_STR);
	$stmt1->bindParam(':patient_sign_one', $patient_sign_one, PDO::PARAM_STR);
	$stmt1->bindParam(':patientname',  $data['patientname'], PDO::PARAM_STR);
	$stmt1->bindParam(':witness_sign', $witness_sign, PDO::PARAM_STR);
	//$stmt1->bindParam(':witness_name', $witness_name, PDO::PARAM_STR);
	$stmt1->bindParam(':doctor_sign', $doctor_sign, PDO::PARAM_STR);
	$stmt1->bindParam(':doctor_name', $data['consultant'], PDO::PARAM_STR);
	//$stmt1->bindParam(':interpreter_name', $interpreter_name, PDO::PARAM_STR);
	$stmt1->bindParam(':interpreter_sign', $interpreter_sign, PDO::PARAM_STR);
	$stmt1 -> execute(); 
if($stmt1 -> rowCount() > 0){
         http_response_code(200);
        $response['error']= false;
	    $response['message']="Sucessfully updated";
      }
	 else
     {
	http_response_code(503);
    $response['error']= true;
  	$response['message']="Already submitted";
     }
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
$pdo4 = null;
$pdoread = null;
?>