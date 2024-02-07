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
$lang= $data->lang;
$consent_id
= $data->consent_id;

$response = array();
try{
if(!empty($accesskey) &&!empty($keyword)){
	$check = $pdoread -> prepare("SELECT `userid`,`department`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
	$stmt1 = $pdoread->prepare("SELECT if(attender_name='','-----',attender_name) AS attender_name,`admissionno`,registration.`umrno`,`billno`,DATE_FORMAT(`admittedon`,'%d-%b-%Y %h:%i %p') AS admittedon,UPPER(`patientname`) AS patientname,DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`patientage`)), '%Y')+0 AS Age,`patientgender` AS gender,`contactno` AS mobile,`map_ward` AS ward,`roomno` AS bedno,`consultantname` AS consultant,`department` AS department,`procedure_surgery` AS surgery,'info@medicoverhospitals.in' AS emailid,`patient_category` AS category,`nursing_notes`,if(umr_registration.address='','-----',umr_registration.address) AS address FROM `registration` INNER join umr_registration on umr_registration.umrno=registration.umrno WHERE `admissionno` LIKE :search AND `admissionstatus` != 'Discharged' AND registration.`status` = 'Visible' AND `patient_signature`='' AND registration.cost_center=:cost_center ORDER BY `admissionno` DESC");

	$stmt1->bindParam(':search', $keyword, PDO::PARAM_STR);
	$stmt1->bindParam(':cost_center', $emp['cost_center'], PDO::PARAM_STR);
	$stmt1 -> execute(); 
if($stmt1 -> rowCount() > 0){
	$data = $stmt1 -> fetch(PDO::FETCH_ASSOC);
	
	$stmt2=$pdoread->prepare("SELECT `consent_id`, `content_eng`, `content_tel`, `content_hin` FROM `consent_form_master` where `consent_id`=:consent_id");
	stmt2->bindParam(':consent_id', $consent_id, PDO::PARAM_STR);
	$stmt2 -> execute(); 
	
	$data1 = $stmt2 -> fetch(PDO::FETCH_ASSOC);
	    http_response_code(200);
        $response['error']= false;
	    $response['message']="Data Found";
		if ($lang=='Hindi'){
	    $response['message1']=$data1['content_hin'];	
}else if ($lang=='Telugu'){
	
		    $response['message1']=$data1['content_tel'];	
}else if ($lang=='English'){
	
	 $response['message1']=$data1['content_eng'];
}
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