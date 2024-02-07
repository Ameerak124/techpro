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
$response1 = array();
$response2 = array();
$response3 = array();
$response4 = array();
$response5 = array();
try{
if(!empty($accesskey)){
	$check = $pdoread -> prepare("SELECT `userid`,`department`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
	
	$stmt2=$pdoread->prepare("SELECT `title`, `consent_id`, `patient_sign_one` as patient_sign,`patient_name_one`as patient_name,`patient_relation_one` as patient_relation, `patient_sign_two` as witness_sign,`patient_name_two` as witness_name, `patient_relation_two` as witness_relation, `emp_sign_one` as doctor_sign,`emp_name_one` as doctor_name, `emp_sign_two` as interpreter_sign,`emp_name_two` as interpreter_name, `photo`, `questionnaires`, `questions_id`, `questions_type`, `created_by`, `created_on`, `status`,'Patient/Consenter Name'  AS patientname,'Doctor Name' as doctorname,'Relationship' AS patientrelation,'Witness Name' as witnessname,'Interpreter Name' as interpretername,'Relationship' as witnessrelation FROM `consent_form_master` WHERE `status`='1'");
	$stmt2->bindParam(':lang', $lang, PDO::PARAM_STR);
	$stmt2 -> execute(); 
	if($stmt2 -> rowCount() > 0){
	while($data1 = $stmt2 -> fetch(PDO::FETCH_ASSOC)){
	
	   
$response5[]=[
"interpreter_sign"=>$data1['interpreter_sign'],
"interpreter_name"=>$data1['interpreter_name'],
"interpretername"=>$data1['interpretername'],
];
	$response4[]=[
"doctor_sign"=>$data1['doctor_sign'],
"doctor_name"=>$data1['doctor_name'],
"doctorname"=>$data1['doctorname'],
];
$response3[]=[
"witness_sign"=>$data1['witness_sign'],
"witness_name"=>$data1['witness_name'],
"witness_relation"=>$data1['witness_relation'],
"witnessname"=>$data1['witnessname'],
"witnessrelation"=>$data1['witnessrelation'],
];
$response2[]=[
"patient_sign"=>$data1['patient_sign'],
"patient_name"=>$data1['patient_name'],
"patient_relation"=>$data1['patient_relation'],
"patientname"=>$data1['patientname'],
"patientrelation"=>$data1['patientrelation'],
];
 $temp=[
"title"=>$data1['title'],
"consent_id"=>$data1['consent_id'],
"questionnaires"=>$data1['questionnaires'],
"questions_id"=>$data1['questions_id'],
"questions_type"=>$data1['questions_type'],
"created_by"=>$data1['created_by'],
"created_on"=>$data1['created_on'],
"photo"=>$data1['photo'],
"patientlist"=>$response2,
"witnesslist"=>$response3,
"doctorlist"=>$response4,
"interpreterlist"=>$response5
];
array_push($response1,$temp);
$response5=array();
$response4=array();
$response3=array();
$response2=array();
	}
    http_response_code(200);
        $response['error']= false;
	    $response['message']="Data Found";
	    $response['consentformlist']=$response1;
		
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