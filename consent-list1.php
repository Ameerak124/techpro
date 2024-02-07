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

try{
if(!empty($accesskey)){
	$check = $pdoread -> prepare("SELECT `userid`,`department`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
	
	$stmt1 = $pdoread->prepare("SELECT `admissionno`,registration.`umrno`,`billno`,DATE_FORMAT(`admittedon`,'%d-%b-%Y %h:%i %p') AS admittedon,UPPER(`patientname`) AS patientname,DATE_FORMAT(`admittedon`,'%d-%b-%Y') AS admitteddate,DATE_FORMAT(`admittedon`,'%h:%i %p') AS admittedtime,DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`patientage`)), '%Y')+0 AS Age,`patientgender` AS gender,`contactno` AS elibom,`map_ward` AS ward,`roomno` AS bedno,`consultantname` AS consultant,`department` AS department,`procedure_surgery` AS surgery,'info@medicoverhospitals.in' AS emailid,`patient_category` AS category,`nursing_notes`,if(umr_registration.address='','-----',umr_registration.address) AS address,registration.cost_center FROM `registration` INNER join umr_registration on umr_registration.umrno=registration.umrno WHERE `admissionno` LIKE :search AND `admissionstatus` != 'Discharged' AND registration.`status` = 'Visible' AND `patient_signature`='' AND registration.cost_center=:cost_center ORDER BY `admissionno` DESC");

	$stmt1->bindParam(':search', $keyword, PDO::PARAM_STR);
	$stmt1->bindParam(':cost_center', $emp['cost_center'], PDO::PARAM_STR);
	$stmt1 -> execute(); 
if($stmt1 -> rowCount() > 0){
	$data = $stmt1 -> fetch(PDO::FETCH_ASSOC);


	$stmt2=$con->prepare("SELECT `title`, `consent_id`,case when :lang='English' then replace(replace(replace(replace(replace(replace(replace(replace(`content_eng`,'balarambhagavath',:name),'#@',:age),'MANAV',:gender),'etad',:date),'emit',:time),'retnectsoc',:costcenter),'elibom' ,:mobile),'sserdda',:address)  when :lang='Telugu' then replace(replace(replace(replace(replace(replace(replace(replace(`content_tel`,'balarambhagavath',:name),'#@',:age),'MANAV',:gender),'etad',:date),'emit',:time),'retnectsoc',:costcenter),'elibom' ,:mobile),'sserdda',:address) when :lang='Hindi' then replace(replace(replace(replace(replace(replace(replace(replace(`content_hin`,'balarambhagavath',:name),'#@',:age),'MANAV',:gender),'etad',:date),'emit',:time),'retnectsoc',:costcenter),'elibom' ,:mobile),'sserdda',:address) else ''end as contenttext, `patient_sign_one` as patient_sign,`patient_sign_two` as witness_sign,`emp_sign_one` as doctor_sign,`emp_sign_two` as interpreter_sign, `photo`, `questionnaires`, `questions_id`, `questions_type`, `created_by`, `created_on`, `status` FROM `consent_form_master` WHERE `status`='1' and consent_id=:consent_id");
	$stmt2->bindParam(':lang', $lang, PDO::PARAM_STR);
	$stmt2->bindParam(':consent_id', $consent_id, PDO::PARAM_STR);
	$stmt2->bindParam(':name', $data['patientname'], PDO::PARAM_STR);
	$stmt2->bindParam(':age', $data['Age'], PDO::PARAM_STR);
	$stmt2->bindParam(':gender', $data['gender'], PDO::PARAM_STR);
	$stmt2->bindParam(':date', $data['admitteddate'], PDO::PARAM_STR);
	$stmt2->bindParam(':time',$data['admittedtime'], PDO::PARAM_STR);
	$stmt2->bindParam(':costcenter',$data['cost_center'], PDO::PARAM_STR);
	$stmt2->bindParam(':mobile',$data['mobile'], PDO::PARAM_STR);
	$stmt2->bindParam(':address',$data['address'], PDO::PARAM_STR);
	$stmt2 -> execute(); 
	//,case when :lang='English' then `content_eng` when :lang='Telugu' then `content_tel` when :lang='Hindi' then `content_hin`else ''end as contenttext
	if($stmt2 -> rowCount() > 0){
	$data1 = $stmt2 -> fetchAll(PDO::FETCH_ASSOC);
	
	    http_response_code(200);
        $response['error']= false;
	    $response['message']="Data Found";
	    $response['consentlist']=$data1;
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