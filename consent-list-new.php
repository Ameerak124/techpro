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
$lang= $data->lang;
$consent_id= $data->consent_id;
$keyword= $data->keyword;
$response = array();
$response11 = array();
$response1 = array();
$response2 = array();
$response3 = array();

try{
if(!empty($accesskey)){
	$check = $pdoread -> prepare("SELECT `userid`,`department`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
	
	$stmt1 = $pdoread->prepare("SELECT `admissionno`,registration.`umrno`,`billno`,DATE_FORMAT(`admittedon`,'%d-%b-%Y %h:%i %p') AS admittedon,UPPER(`patientname`) AS patientname,DATE_FORMAT(`admittedon`,'%d-%b-%Y') AS admitteddate,DATE_FORMAT(`admittedon`,'%h:%i %p') AS admittedtime,DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`patientage`)), '%Y')+0 AS Age,`patientgender` AS gender,`contactno` AS mobile,`map_ward` AS ward,`roomno` AS bedno,`consultantname` AS consultant,`department` AS department,`procedure_surgery` AS surgery,'info@medicoverhospitals.in' AS emailid,`patient_category` AS category,`nursing_notes`,if(umr_registration.address='','-----',umr_registration.address) AS address,registration.cost_center,`tpa_name`,registration.organization_name,branch_master.display_name as branch  FROM `registration` INNER join umr_registration on umr_registration.umrno=registration.umrno  INNER JOIN branch_master on branch_master.cost_center=registration.cost_center WHERE `admissionno` LIKE :search AND `admissionstatus` != 'Discharged' AND registration.`status` = 'Visible' AND `patient_signature`='' AND registration.cost_center=:cost_center ORDER BY `admissionno` DESC");

	$stmt1->bindParam(':search', $keyword, PDO::PARAM_STR);
	$stmt1->bindParam(':cost_center', $emp['cost_center'], PDO::PARAM_STR);
	$stmt1 -> execute(); 
if($stmt1 -> rowCount() > 0){
	$data = $stmt1 -> fetch(PDO::FETCH_ASSOC);


	$stmt2=$pdoread->prepare("SELECT `title`, `consent_id`,case when :lang='English' then replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`content_eng`,'balarambhagavath',:name),'#@',:age),'MANAV',:gender),'etad',:date),'emit',:time),'retnectsoc',:costcenter),'elibom' ,:mobile),'sserdda',:address),'onpi',:ipno),'onmoor',:bedno) ,'rotcod',:doctor),'apt',:tpa),'ecnarusni',:insurance) when :lang='Telugu' then replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`content_tel`,'balarambhagavath',:name),'#@',:age),'MANAV',:gender),'etad',:date),'emit',:time),'retnectsoc',:costcenter),'elibom' ,:mobile),'sserdda',:address),'onpi',:ipno),'onmoor',:bedno) ,'rotcod',:doctor),'apt',:tpa),'ecnarusni',:insurance) when :lang='Hindi' then replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(`content_hin`,'balarambhagavath',:name),'#@',:age),'MANAV',:gender),'etad',:date),'emit',:time),'retnectsoc',:costcenter),'elibom' ,:mobile),'sserdda',:address),'onpi',:ipno),'onmoor',:bedno) ,'rotcod',:doctor),'apt',:tpa),'ecnarusni',:insurance) else ''end as contenttext, `patient_sign_one` as patient_sign,`patient_sign_two` as witness_sign,`emp_sign_one` as doctor_sign,`emp_sign_two` as interpreter_sign, `photo`, `questionnaires`, `questions_id`, `questions_type`, `created_by`, `created_on`, `status` FROM `consent_form_master` WHERE `status`='1' and consent_id=:consent_id");
	$stmt2->bindParam(':lang', $lang, PDO::PARAM_STR);
	$stmt2->bindParam(':consent_id', $consent_id, PDO::PARAM_STR);
	$stmt2->bindParam(':name', $data['patientname'], PDO::PARAM_STR);
	$stmt2->bindParam(':age', $data['Age'], PDO::PARAM_STR);
	$stmt2->bindParam(':gender', $data['gender'], PDO::PARAM_STR);
	$stmt2->bindParam(':date', $data['admitteddate'], PDO::PARAM_STR);
	$stmt2->bindParam(':time',$data['admittedtime'], PDO::PARAM_STR);
	$stmt2->bindParam(':costcenter',$data['branch'], PDO::PARAM_STR);
	$stmt2->bindParam(':ipno',$data['admissionno'], PDO::PARAM_STR);
	$stmt2->bindParam(':bedno',$data['bedno'], PDO::PARAM_STR);
	$stmt2->bindParam(':doctor',$data['consultant'], PDO::PARAM_STR);
	$stmt2->bindParam(':mobile',$data['mobile'], PDO::PARAM_STR);
	$stmt2->bindParam(':address',$data['address'], PDO::PARAM_STR);
	$stmt2->bindParam(':tpa',$data['tpa_name'], PDO::PARAM_STR);
	$stmt2->bindParam(':insurance',$data['organization_name'], PDO::PARAM_STR);
	$stmt2 -> execute(); 
	//,case when :lang='English' then `content_eng` when :lang='Telugu' then `content_tel` when :lang='Hindi' then `content_hin`else ''end as contenttext
	if($stmt2 -> rowCount() > 0){
	$data1 = $stmt2 -> fetch(PDO::FETCH_ASSOC);

	
	$temp=[
"title"=>$data1['title'],
"consent_id"=>$data1['consent_id'],
"contenttext"=>$data1['contenttext'],
"patient_sign"=>$data1['patient_sign'],
"witness_sign"=>$data1['witness_sign'],
"doctor_sign"=>$data1['doctor_sign'],
"interpreter_sign"=>$data1['interpreter_sign'],
"questionslist"=>$data1['questionnaires'],

];
array_push($response11,$temp);
	
	    http_response_code(200);
        $response['error']= false;
	    $response['message']="Data Found";
	    $response['consentlist']=$response11;
	} else
     {
	http_response_code(503);
    $response['error']= true;
  	$response['message']="No Data Found!";
     }
}else{
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