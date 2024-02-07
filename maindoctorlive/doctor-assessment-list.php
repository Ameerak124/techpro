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
$doc_uid=trim($data->doc_uid);
$umrno=trim($data->umrno);
$ipno=trim($data->ipno);
$accesskey=trim($data->accesskey);

try{

if(!empty($accesskey)&& !empty($umrno)&& !empty($ipno)&& !empty($doc_uid)){
$check = $pdoread -> prepare("SELECT `userid`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$row=$check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$stmt = $pdoread->prepare("SELECT COUNT(`category`) AS allcount FROM `billing_history` WHERE `ipno` = :searchterm AND `credit_debit` LIKE 'CREDIT' AND `status` = 'Visible'");
$stmt->bindParam(':searchterm', $searchterm, PDO::PARAM_STR);
$stmt->execute();
$records = $stmt->fetch();
$totalRecords = $records['allcount'];
	
## Fetch records
$stmt1 = $pdoread->prepare("SELECT  `chief_complaint`, `present_illness`, `past_history`, `treatment_history`, `allergies`, `personal_history`, `family_history`, `obstetric_history`, `smoking_status`, `alcohol_drugs`, `general_examination`, `vitals`, `systemic_examination`, `adviced_investigations`, `emergency_diagnosis`, `assessment_plan`, `medication_reconciliation`, `review`, `pain_assessment`, `trans_shift_disc`, `pain_type`, `pain_rate`, `doc_name`, `pain_scores_table`.`pain`, `pain_loc`, `pain_char`, `acute_chornic`, `pain_duration`, `dec_pain`, `inc_pain`, `action_plan`, `intervention`, `interventiond`, `heparin_therapy`, `facial_expression`, `upper_limb_movements`, `compliance_mechanical_ventilation` FROM `doctor_assessment` left join `pain_scores_table` ON `pain_scores_table`.`page_id`=`doctor_assessment`.`pain_id` AND `pain_scores_table`.`admission_num`=`doctor_assessment`.`ipno` AND `pain_scores_table`.`doctor_uid`=`doctor_assessment`.`doctor_uid` WHERE `doctor_assessment`.`status`='Active' AND `cost_center`=:branch AND `ipno`=:ipno AND `doctor_assessment`.`umr_no`=:umrno AND `doctor_assessment`.`doctor_uid`=:doctor_uid");
$stmt1->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$stmt1->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$stmt1->bindParam(':doctor_uid', $doc_uid, PDO::PARAM_STR);
$stmt1->bindParam(':branch', $row['cost_center'], PDO::PARAM_STR);
$stmt1->execute();
if($stmt1->rowCount()>0){
$result=$stmt1->fetch(PDO::FETCH_ASSOC);
http_response_code(200);
	$response['error']= false;
	$response['message']= "Data found";
	$response['doctorassessmentlist'][]= $result;
	
}else{
		http_response_code(503);
	$response['error']=true;
	$response['message']="No data found";
	
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
}
}else{
	http_response_code(400);
	$response['error']=true;
	$response['message']="Sorry! some details are missing";
}


echo json_encode($response);
}
catch(PDOException $err){
     echo $err -> getMessage();
}
$pdoread = null;
?>