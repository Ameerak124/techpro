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
$response = array();
try{
if(!empty($accesskey)){
	$check = $pdoread -> prepare("SELECT `userid`,`department`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
	
	$stmt2=$pdoread->prepare("SELECT `title`, `consent_id`, `patient_sign_one` as patient_sign,`patient_name_one`as patient_name,`patient_relation_one` as patient_relation, `patient_sign_two` as witness_sign,`patient_name_two` as witness_name, `patient_relation_two` as witness_relation, if(`emp_sign_one`='Yes',emp_sign_one,billing_head_sign) as doctor_sign,if(`emp_name_one`='Yes',emp_name_one,billing_head_name) as doctor_name, if(`emp_sign_two`='Yes',emp_sign_two,mod_sign) as interpreter_sign,if(`emp_name_two`='Yes',emp_name_two,mod_name) as interpreter_name, `photo`, `questionnaires`, `questions_id`, `questions_type`, `created_by`, `created_on`, `status`,if(consent_id='CONS00061','IP Admission Cell / MOD Name',IF((consent_id ='CONS00060' || consent_id ='CONS00062'),'Patient/Legal Guardian Name','Patient/Consenter Name'))  AS patientname,if(emp_name_one = 'Yes' ,'Doctor Name','Billing Head Name') as doctorname,'Relationship' AS patientrelation,'Witness Name' as witnessname,if(consent_id = 'CONS00036',if(emp_name_two ='Yes','Interpreter Name','Finance / MOD Name'),if(emp_name_two ='Yes','Interpreter Name','MOD Name')) as interpretername,'Relationship' as witnessrelation,if(consent_id='CONS00061','Signature of  IP Admission Cell / MOD',if((consent_id ='CONS00060' || consent_id ='CONS00062'),'Signature of Patient/Legal Guardian','Signature of Patient/Consenter')) as patientsignature,'Signature of Witness' as witnesssignature ,if(emp_sign_one ='Yes','Signature of Doctor','Signature of Billing Head') as doctorsignature,if(consent_id = 'CONS00036',if(emp_sign_two='Yes','Signature of Interpreter','Signature of Finance / MOD'),if(emp_sign_two='Yes','Signature of Interpreter','Signature of MOD')) as interpretersignature FROM `consent_form_master` WHERE `status`='1'");
	
	$stmt2 -> execute(); 
	if($stmt2 -> rowCount() > 0){
	$data1 = $stmt2 -> fetchAll(PDO::FETCH_ASSOC);
	
	    http_response_code(200);
        $response['error']= false;
	    $response['message']="Data Found";
	    $response['consentformlist']=$data1;
	} else
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