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

$stmt1 = $pdoread->prepare("SELECT  `chief_complaint`, `present_illness`, `past_history`, `treatment_history`, `allergies`, `personal_history`, `family_history`, `obstetric_history`, `smoking_status`, `alcohol_drugs`, `general_examination`, `vitals`, `systemic_examination`, `adviced_investigations`, `emergency_diagnosis`, `assessment_plan`, `medication_reconciliation`, `review`, `pain_assessment`, `trans_shift_disc`, `pain_type`, `pain_rate`, `doc_name`, `pain_scores_table`.`pain`, `pain_loc`, `pain_char`, `acute_chornic`, `pain_duration`, `dec_pain`, `inc_pain`, `action_plan`, `intervention`, `interventiond`, `heparin_therapy`, `facial_expression`, `upper_limb_movements`, `compliance_mechanical_ventilation` FROM `doctor_assessment` left join `pain_scores_table` ON `pain_scores_table`.`page_id`=`doctor_assessment`.`pain_id` AND `pain_scores_table`.`admission_num`=`doctor_assessment`.`ipno` AND `pain_scores_table`.`doctor_uid`=`doctor_assessment`.`doctor_uid` WHERE `doctor_assessment`.`status`='Active' AND `cost_center`=:branch AND `ipno`=:ipno AND `doctor_assessment`.`umr_no`=:umrno AND `doctor_assessment`.`doctor_uid`=:doctor_uid");
$stmt1->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$stmt1->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$stmt1->bindParam(':doctor_uid', $doc_uid, PDO::PARAM_STR);
$stmt1->bindParam(':branch', $row['cost_center'], PDO::PARAM_STR);
$stmt1->execute();
if($stmt1->rowCount()>0){
$result=$stmt1->fetch(PDO::FETCH_ASSOC);
$my_array = array("Chief Complaint","History Of Present Illness","Past History","Treatment History","Allergies","Personal History","Family History","Obstetric History","Smoking Status","Alcohol And Drug Abuse Status","General Examination","Vitals","Systemic Examination","Previous Investigations","Diagnosis","Assessment And Plan","Medication Reconciliation","Review","Pain Assessment","Transfer/Shiftout/Discharge Details");
$my_array1 = array($result['chief_complaint'],$result['present_illness'],$result['past_history'],$result['treatment_history'],$result['allergies'],$result['personal_history'],$result['family_history'],$result['obstetric_history'],$result['smoking_status'],$result['alcohol_drugs'],$result['general_examination'],$result['vitals'],$result['systemic_examination'],"Previous Investigations","Diagnosis",$result['assessment_plan'],$result['medication_reconciliation'],$result['review'],$result['assessment_plan'],"Transfer/Shiftout/Discharge Details");
/* $my_array2 = array($result['pain_type'],$result['doc_name'],$result['pain'],$result['pain_loc'],$result['pain_char'],$result['acute_chornic'],$result['pain_duration'],$result['dec_pain'],$result['inc_pain'],$result['action_plan'],$result['intervention'],$result['interventiond'],heparin_therapy,facial_expression,upper_limb_movements,compliance_mechanical_ventilation,"Medication Reconciliation","Review","Pain Assessment","Transfer/Shiftout/Discharge Details"); */

   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
 /*    $response['pain_type']= "behaviour pain"; */
    for($x = 0; $x < sizeof($my_array); $x++){	
	$response['doctorassessmenttitleslist'][$x]['title']=$my_array[$x];	
	$response['doctorassessmenttitleslist'][$x]['Value']=$my_array1[$x];	
	$response['doctorassessmenttitleslist'][$x]['view_btn']="Yes";	
	$response['doctorassessmenttitleslist'][$x]['save_btn']="Yes";	
	$response['doctorassessmenttitleslist'][$x]['history_btn']="No";		
	}
	$response['pain_score']['paintype']=$result['pain_type'];
	$response['pain_score']['doc_name']=$result['doc_name'];
	$response['pain_score']['pain']=$result['pain'];
	$response['pain_score']['pain_loc']=$result['pain_loc'];
	$response['pain_score']['pain_char']=$result['pain_char'];
	$response['pain_score']['acute_chornic']=$result['acute_chornic'];
	$response['pain_score']['pain_duration']=$result['pain_duration'];
	$response['pain_score']['dec_pain']=$result['dec_pain'];
	$response['pain_score']['inc_pain']=$result['inc_pain'];
	$response['pain_score']['action_plan']=$result['action_plan'];
	$response['pain_score']['intervention']=$result['intervention'];
	$response['pain_score']['interventiond']=$result['interventiond'];
	$response['pain_score']['heparin_therapy']=$result['heparin_therapy'];
	$response['pain_score']['facial_expression']=$result['facial_expression'];
	$response['pain_score']['upper_limb_movements']=$result['upper_limb_movements'];
	$response['pain_score']['compliance_mechanical_ventilation']=$result['compliance_mechanical_ventilation'];
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